<?php
/*
 * Pago mixto: una venta pagada con varias formas o varias tarjetas/transferencias (hasta 5) mas efectivo.
 * El vendedor escribe el monto de cada tarjeta y transferencia; el efectivo es lo que falta para el total
 * (asi la suma siempre cuadra) y de ese efectivo se calcula el cambio.
 * Los montos se manejan en centavos para que la suma no tenga errores de decimales.
 */

if (!defined('MAXIMO_PAGOS_MIXTO')) define('MAXIMO_PAGOS_MIXTO', 5);

function centavosMixto($valor)
{
    $valor = str_replace(',', '', trim((string) $valor));
    return is_numeric($valor) ? (int) round(((float) $valor) * 100) : 0;
}

function textoMixto($post, $campo, $maximo)
{
    $texto = isset($post[$campo]) ? trim(preg_replace('/\s+/u', ' ', (string) $post[$campo])) : '';
    return mb_substr($texto, 0, $maximo, 'UTF-8');
}

/*
 * Arma las partes del cobro mixto a partir del formulario y las valida.
 * Devuelve array('partes' => [...], 'recibido' => efectivo entregado, 'cambio' => cambio) o null si algo falta
 * (el motivo queda en $error, listo para mostrarse despues de "Para cobrar con pago mixto ...").
 */
function armarPagoMixto($con, $post, $totalAPagar, &$error)
{
    $error = '';
    $total = centavosMixto($totalAPagar);
    $partes = array();
    $cubierto = 0;

    // hasta 5 pagos con tarjeta o transferencia (ej. dos tarjetas, o transferencias desde varios bancos);
    // llegan como pagos[n][tipo|monto|referencia|ultimos4|cuenta|nombre|verificado]
    $lineas = (isset($post['pagos']) && is_array($post['pagos'])) ? array_values($post['pagos']) : array();
    if (count($lineas) > MAXIMO_PAGOS_MIXTO) {
        $error = 'se permiten hasta ' . MAXIMO_PAGOS_MIXTO . ' pagos con tarjeta o transferencia en una venta';
        return null;
    }
    foreach ($lineas as $i => $linea) {
        $numero = $i + 1;
        $esTarjeta = isset($linea['tipo']) && $linea['tipo'] == 'tarjeta';
        $nombre = $esTarjeta ? 'tarjeta' : 'transferencia';
        $monto = centavosMixto(isset($linea['monto']) ? $linea['monto'] : 0);
        $referencia = substr(preg_replace('/[^A-Za-z0-9\-]/', '', isset($linea['referencia']) ? (string) $linea['referencia'] : ''), 0, $esTarjeta ? 20 : 30);
        if ($monto <= 0) {
            $error = 'escribe el monto del pago ' . $numero . ' (' . $nombre . ')';
            return null;
        }
        if ($referencia == '') {
            $error = 'escribe ' . ($esTarjeta ? 'el número de autorización' : 'la referencia') . ' del pago ' . $numero . ' (' . $nombre . ')';
            return null;
        }

        if ($esTarjeta) {
            $ultimos4 = (isset($linea['ultimos4']) && preg_match('/^[0-9]{4}$/', (string) $linea['ultimos4'])) ? $linea['ultimos4'] : '';
            $partes[] = array('forma' => 'Tarjeta', 'monto' => $monto / 100, 'referencia' => $referencia, 'ultimos4' => $ultimos4,
                              'bancoDestino' => '', 'nombreTransfirio' => '');
        } else {
            $cuenta = $con->getCuentaBancaria(isset($linea['cuenta']) ? (int) $linea['cuenta'] : 0);
            if ($cuenta === null || $cuenta['estado'] != 'Activo') {
                $error = 'elige la cuenta de la tienda a la que llegó el pago ' . $numero . ' (transferencia)';
                return null;
            }
            if (empty($linea['verificado'])) {
                $error = 'confirma que verificaste que llegó el pago ' . $numero . ' (transferencia)';
                return null;
            }
            $partes[] = array('forma' => 'Transferencia', 'monto' => $monto / 100, 'referencia' => $referencia, 'ultimos4' => '',
                              'bancoDestino' => mb_substr($cuenta['banco'] . ' - ' . $cuenta['numero'], 0, 110, 'UTF-8'),
                              'nombreTransfirio' => textoMixto($linea, 'nombre', 100));
        }
        $cubierto += $monto;
    }
    if ($cubierto > $total) {
        $error = 'los pagos con tarjeta y transferencia suman más que el total';
        return null;
    }

    // el efectivo es lo que falta para el total
    $restoEfectivo = $total - $cubierto;
    $recibido = 0;
    if ($restoEfectivo > 0) {
        $recibido = centavosMixto(isset($post['recibidoMixto']) ? $post['recibidoMixto'] : 0);
        if ($recibido < $restoEfectivo) {
            $error = 'el efectivo recibido no alcanza para completar el total (faltan ' . montoHn(($restoEfectivo - $recibido) / 100) . ')';
            return null;
        }
        $partes[] = array('forma' => 'Efectivo', 'monto' => $restoEfectivo / 100, 'referencia' => '', 'ultimos4' => '',
                          'bancoDestino' => '', 'nombreTransfirio' => '');
    }

    if (count($partes) < 2) {
        $error = 'usa al menos dos pagos (si es uno solo, elige Efectivo, Tarjeta o Transferencia directamente)';
        return null;
    }

    return array('partes' => $partes, 'recibido' => $recibido / 100, 'cambio' => ($recibido - $restoEfectivo) / 100);
}

// partes guardadas de un cobro mixto (texto JSON de clientedato.pagosDetalle)
function partesPagoMixto($pagosDetalle)
{
    $partes = json_decode((string) $pagosDetalle, true);
    return is_array($partes) ? $partes : array();
}
?>
