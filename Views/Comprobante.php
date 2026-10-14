<?PHP
/*
 * Comprobante de venta para impresora termica (rollo de 80 o 58 mm, se elige en Configuracion > Apariencia).
 * Lo usan la impresion (ConFacturaViews) y la vista previa (ShowFacturaViews).
 * Los datos de la venta llegan en las variables de siempre ($fecha, $ci, $nombreCliente, $pedidoTotalPreventa, ...).
 */
include_once(__DIR__ . '/../Controller/LiteralMoney.php');

$anchoPapel = anchoPapel();
// area que imprime cada rollo: 72 mm en el de 80, 48 mm en el de 58
$anchoImpresion = $anchoPapel == 58 ? 48 : 72;
$letraComprobante = $anchoPapel == 58 ? 10.5 : 12;
$moneda = htmlspecialchars($tipoMoneda);

if (!function_exists('filaComprobante')) {
    // una fila "texto ........ valor"
    function filaComprobante($etiqueta, $valor, $clase = '')
    {
        echo '<div class="c-fila ' . $clase . '"><span>' . $etiqueta . '</span><span class="c-valor">' . $valor . '</span></div>';
    }
}
?>
<style>
    .comprobante {
        width: <?PHP echo $anchoImpresion; ?>mm;
        margin: 0 auto;
        padding: 3mm 0;
        font-family: Arial, Helvetica, sans-serif;
        font-size: <?PHP echo $letraComprobante; ?>px;
        line-height: 1.35;
        color: #000;
        background: #fff;
        word-wrap: break-word;
    }
    .comprobante .c-centro { text-align: center; }
    .comprobante .c-negocio { font-size: 1.6em; font-weight: bold; }
    .comprobante .c-titulo { font-size: 1.15em; font-weight: bold; margin-top: 2px; }
    .comprobante .c-linea { border-top: 1px dashed #000; margin: 5px 0; }
    .comprobante .c-fila { display: flex; justify-content: space-between; gap: 6px; }
    .comprobante .c-fila .c-valor { text-align: right; white-space: nowrap; }
    .comprobante .c-fila.c-total { font-size: 1.25em; font-weight: bold; }
    .comprobante .c-producto { font-weight: bold; }
    .comprobante .c-detalle { font-size: 0.9em; padding-left: 8px; }
    .comprobante .c-letras { font-size: 0.9em; font-weight: bold; margin-top: 4px; }
    .comprobante b { font-weight: bold; }
</style>

<div class="comprobante">
        <div class="c-centro">
            <div class="c-negocio"><?PHP echo htmlspecialchars($razon); ?></div>
            <div>De: <?PHP echo htmlspecialchars($propietario); ?></div>
            <div>Casa Matriz: <?PHP echo htmlspecialchars($direccion); ?></div>
            <div>N&ordm; <?PHP echo htmlspecialchars($nro); ?> - Tel: <?PHP echo htmlspecialchars($telefono); ?></div>
            <div>TEUPASENTI</div>
            <div class="c-titulo">COMPROBANTE DE VENTA</div>
        </div>

    <div class="c-linea"></div>
    <div><b>Fecha:</b> <?PHP echo htmlspecialchars($fecha); ?></div>
    <?PHP if (trim($ci) != '') { ?>
        <div><b>RTN/DNI:</b> <?PHP echo htmlspecialchars($ci); ?></div>
    <?PHP } ?>
    <div><b>Cliente:</b> <?PHP echo htmlspecialchars($nombreCliente); ?></div>

    <div class="c-linea"></div>
    <?PHP
    // cada producto en su linea y debajo "cantidad x precio ..... total": entra bien en los dos anchos de rollo
    for ($i = 0; $i < $pedido; $i++) {
        $detallePedido = mysqli_fetch_array($pedidoTotalPreventa);
        echo '<div class="c-producto">' . htmlspecialchars($detallePedido['producto']) . '</div>';
        filaComprobante($detallePedido['cantidad'] . ' x ' . dinero($detallePedido['precio']), dinero($detallePedido['precio'] * $detallePedido['cantidad']), 'c-detalle');
    }
    ?>

    <div class="c-linea"></div>
    <?PHP filaComprobante('TOTAL', $moneda . ' ' . dinero($totalAPagar), 'c-total'); ?>

    <?PHP if (isset($formaPago) && $formaPago == 'Tarjeta') { ?>
        <?PHP filaComprobante('Forma de pago', 'Tarjeta'); ?>
        <?PHP filaComprobante('Autorizaci&oacute;n', htmlspecialchars($referenciaPago)); ?>
        <?PHP if ($ultimos4 != '') { filaComprobante('Tarjeta', '**** ' . htmlspecialchars($ultimos4)); } ?>
    <?PHP } elseif (isset($formaPago) && $formaPago == 'Transferencia') { ?>
        <?PHP filaComprobante('Forma de pago', 'Transferencia'); ?>
        <?PHP filaComprobante('Referencia', htmlspecialchars($referenciaPago)); ?>
        <div><b>Cuenta:</b> <?PHP echo htmlspecialchars($bancoDestino); ?></div>
        <?PHP if ($nombreTransfirio != '') { ?>
            <div><b>Transfiri&oacute;:</b> <?PHP echo htmlspecialchars($nombreTransfirio); ?></div>
        <?PHP } ?>
    <?PHP } elseif (isset($formaPago) && $formaPago == 'Mixto') { ?>
        <?PHP filaComprobante('Forma de pago', 'Mixto'); ?>
        <?PHP foreach (partesPagoMixto($pagosDetalle) as $parte) {
            filaComprobante(htmlspecialchars($parte['forma']), $moneda . ' ' . dinero($parte['monto']));
            if ($parte['forma'] == 'Tarjeta') {
                $detalleParte = 'Aut. ' . $parte['referencia'] . ($parte['ultimos4'] != '' ? '  **** ' . $parte['ultimos4'] : '');
            } elseif ($parte['forma'] == 'Transferencia') {
                $detalleParte = 'Ref. ' . $parte['referencia'] . ' - ' . $parte['bancoDestino'] . ($parte['nombreTransfirio'] != '' ? ' (' . $parte['nombreTransfirio'] . ')' : '');
            } else {
                $detalleParte = 'Recibido ' . dinero($parte['monto'] + $cambio) . '  Cambio ' . dinero($cambio);
            }
            echo '<div class="c-detalle">' . htmlspecialchars($detalleParte) . '</div>';
        } ?>
    <?PHP } else { ?>
        <?PHP filaComprobante('Forma de pago', 'Efectivo'); ?>
        <?PHP filaComprobante('Efectivo', $moneda . ' ' . dinero($efectivo)); ?>
        <?PHP filaComprobante('Cambio', $moneda . ' ' . dinero($cambio)); ?>
    <?PHP } ?>

    <div class="c-letras">
        Son: <?PHP
        $literalMoney = new EnLetras();
        echo htmlspecialchars(mb_strtoupper($literalMoney->valorEnletras($totalAPagar, $contextMoneda), 'UTF-8'));
        ?>
    </div>

    <div class="c-linea"></div>
    <div>Atendido por: <b><?PHP echo htmlspecialchars($userLogueado); ?></b></div>
</div>
