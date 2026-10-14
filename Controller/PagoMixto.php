<?php
/*
 * Pago mixto: una venta pagada con varias formas (tarjeta, transferencia y efectivo).
 * El vendedor escribe los montos de tarjeta y transferencia; el efectivo es lo que falta para el total
 * (asi la suma siempre cuadra) y de ese efectivo se calcula el cambio.
 * Los montos se manejan en centavos para que la suma no tenga errores de decimales.
 */

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

    if (!empty($post['usaTarjeta'])) {
        $monto = centavosMixto(isset($post['montoTarjeta']) ? $post['montoTarjeta'] : 0);
        $autorizacion = substr(preg_replace('/[^A-Za-z0-9\-]/', '', isset($post['autorizacionMixto']) ? $post['autorizacionMixto'] : ''), 0, 20);
        $ultimos4 = (isset($post['ultimos4Mixto']) && preg_match('/^[0-9]{4}$/', $post['ultimos4Mixto'])) ? $post['ultimos4Mixto'] : '';
        if ($monto <= 0) {
            $error = 'escribe el monto que se cobra con tarjeta';
            return null;
        }
        if ($autorizacion == '') {
            $error = 'escribe el número de autorización del voucher de la tarjeta';
            return null;
        }
        $partes[] = array('forma' => 'Tarjeta', 'monto' => $monto / 100, 'referencia' => $autorizacion, 'ultimos4' => $ultimos4,
                          'bancoDestino' => '', 'nombreTransfirio' => '');
        $cubierto += $monto;
    }

    if (!empty($post['usaTransferencia'])) {
        $monto = centavosMixto(isset($post['montoTransferencia']) ? $post['montoTransferencia'] : 0);
        $referencia = substr(preg_replace('/[^A-Za-z0-9\-]/', '', isset($post['referenciaMixto']) ? $post['referenciaMixto'] : ''), 0, 30);
        $cuenta = $con->getCuentaBancaria(isset($post['idCuentaMixto']) ? (int) $post['idCuentaMixto'] : 0);
        if ($monto <= 0) {
            $error = 'escribe el monto que se pagó por transferencia';
            return null;
        }
        if ($referencia == '') {
            $error = 'escribe el número de referencia de la transferencia';
            return null;
        }
        if ($cuenta === null || $cuenta['estado'] != 'Activo') {
            $error = 'elige la cuenta de la tienda a la que llegó la transferencia';
            return null;
        }
        if (empty($post['verificadoMixto'])) {
            $error = 'confirma que verificaste que la transferencia llegó a la cuenta';
            return null;
        }
        $partes[] = array('forma' => 'Transferencia', 'monto' => $monto / 100, 'referencia' => $referencia, 'ultimos4' => '',
                          'bancoDestino' => mb_substr($cuenta['banco'] . ' - ' . $cuenta['numero'], 0, 110, 'UTF-8'),
                          'nombreTransfirio' => textoMixto($post, 'nombreTransfirioMixto', 100));
        $cubierto += $monto;
    }

    if ($cubierto > $total) {
        $error = 'los montos de tarjeta y transferencia suman más que el total';
        return null;
    }

    // el efectivo es lo que falta para el total
    $restoEfectivo = $total - $cubierto;
    $recibido = 0;
    if ($restoEfectivo > 0) {
        $recibido = centavosMixto(isset($post['recibidoMixto']) ? $post['recibidoMixto'] : 0);
        if ($recibido < $restoEfectivo) {
            $error = 'el efectivo recibido no alcanza para completar el total (faltan ' . dinero(($restoEfectivo - $recibido) / 100) . ')';
            return null;
        }
        $partes[] = array('forma' => 'Efectivo', 'monto' => $restoEfectivo / 100, 'referencia' => '', 'ultimos4' => '',
                          'bancoDestino' => '', 'nombreTransfirio' => '');
    }

    if (count($partes) < 2) {
        $error = 'usa al menos dos formas de pago (si es una sola, elige Efectivo, Tarjeta o Transferencia directamente)';
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
