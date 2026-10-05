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

function reporteEncabezadoSucursal($pdf, $nombreSucursal, $periodo)
{
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(0);
    $pdf->Cell(0, 7, textoPdf('Sucursal: ' . $nombreSucursal), 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, textoPdf('Periodo: ' . $periodo . '   -   Solo ventas consolidadas'), 0, 1);
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
        $pdf->Cell(40, 6, dinero($fila['total']), 'B', 1, 'R');
    }
    $pdf->Ln(4);
}
