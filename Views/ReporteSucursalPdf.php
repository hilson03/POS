<?php
/*
 * Piezas compartidas por los PDF de reportes de ventas: encabezado con la sucursal y el periodo,
 * y desglose del total por sucursal cuando el reporte es de "Todas las sucursales".
 */

// FPDF trabaja con ISO-8859-1: convierte el texto para que se vean bien las tildes y la ñ
function textoPdf($texto)
{
    $convertido = @iconv('UTF-8', 'windows-1252//TRANSLIT', (string) $texto);
    return $convertido === false ? (string) $texto : $convertido;
}

/*
 * Pie de todos los reportes PDF: quien lo genero y cuando (a la izquierda) y "Pagina X de Y" (a la derecha).
 * Para el total de paginas, despues de crear el PDF hay que llamar $pdf->AliasNbPages().
 */
function reportePie($pdf)
{
    global $usuarioSesion;
    $quien = isset($usuarioSesion['nombre']) ? $usuarioSesion['nombre'] : '';
    $pdf->SetY(-15);
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(90);
    $pdf->Cell(0, 10, textoPdf('Generado por: ' . $quien . ' - ' . date('d/m/Y H:i')), 0, 0, 'L');
    $pdf->SetX($pdf->GetX() - 60);
    $pdf->Cell(60, 10, textoPdf('Página ' . $pdf->PageNo() . ' de {nb}'), 0, 0, 'R');
    $pdf->SetTextColor(0);
}

function reporteEncabezadoSucursal($pdf, $nombreSucursal, $periodo)
{
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(0);
    $pdf->Cell(0, 7, textoPdf('Sucursal: ' . $nombreSucursal), 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, textoPdf('Periodo: ' . $periodo . '   -   Solo ventas cerradas'), 0, 1);
    $pdf->Ln(4);
}

// colores de cada forma de pago (los mismos del cobro: verde, azul y morado)
function colorFormaPago($forma)
{
    $colores = array('Efectivo' => array(46, 157, 91), 'Tarjeta' => array(59, 111, 216), 'Transferencia' => array(138, 79, 208));
    return isset($colores[$forma]) ? $colores[$forma] : array(85, 107, 47);
}

/*
 * Totales por forma de pago al final del reporte. En una venta mixta cada parte suma a su forma,
 * asi la suma de las tres formas da el total de ventas.
 */
function reporteResumenFormasPago($pdf, $totales, $etiquetaTotal = 'TOTAL')
{
    // el bloque va entero en una pagina (si no cabe, el total quedaria solo en la siguiente)
    if (method_exists($pdf, 'CheckPageBreak')) {
        $pdf->CheckPageBreak(7 + 6 * count($totales) + 11);
    }
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor(0);
    $pdf->Cell(0, 7, textoPdf('TOTAL POR FORMA DE PAGO'), 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $suma = 0;
    foreach ($totales as $forma => $total) {
        list($r, $g, $b) = colorFormaPago($forma);
        $pdf->SetTextColor($r, $g, $b);
        $pdf->Cell(80, 6, textoPdf($forma), 'B', 0);
        $pdf->SetTextColor(0);
        $pdf->Cell(40, 6, montoHn($total), 'B', 1, 'R');
        $suma += $total;
    }
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(80, 7, textoPdf($etiquetaTotal), 0, 0);
    $pdf->Cell(40, 7, montoHn($suma), 0, 1, 'R');
    $pdf->Ln(4);
}

function reporteDesgloseSucursales($pdf, $desglose)
{
    if (empty($desglose)) {
        return;
    }
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor(0);
    $pdf->Cell(0, 7, textoPdf('TOTAL POR SUCURSAL'), 0, 1);
    $pdf->SetFont('Arial', '', 10);
    foreach ($desglose as $fila) {
        $pdf->Cell(80, 6, textoPdf($fila['nombre']), 'B', 0);
        $pdf->Cell(40, 6, montoHn($fila['total']), 'B', 1, 'R');
    }
    $pdf->Ln(4);
}
