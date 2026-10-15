<?php
require_once(__DIR__ . '/ReporteSucursalPdf.php');

require('fpdf/fpdf.php');

class PDF extends FPDF
{
    var $widths;
    var $aligns;

    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border

            $this->Rect($x, $y, $w, $h);

            $this->MultiCell($w, 5, $data[$i], 0, $a, 'true');
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw =& $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    function Header()
    {

        $this->SetFont('Arial', '', 10);
        $this->Text(20, 14, 'Historial de Ventas por dia', 0, 'C', 0);
        $this->Ln(10);
    }

    function Footer()
    {
        reportePie($this);

    }

}

$pdf = new PDF('L', 'mm', 'Letter');
$pdf->AliasNbPages();   // total de paginas para el pie
//$pdf->Open();
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20);
$pdf->Ln(10);
reporteEncabezadoSucursal($pdf, $nombreSucursalReporte, $periodoReporte);


/*
 * Una seccion por forma de pago (efectivo, tarjeta, transferencia), cada una con sus ventas y su total.
 * Una venta mixta sale en cada forma que uso, con el monto que se pago de esa forma.
 */
$ventasPorForma = array('Efectivo' => array(), 'Tarjeta' => array(), 'Transferencia' => array());
foreach ($ventasFormaPago as $venta) {
    $ventasPorForma[$venta['forma']][] = $venta;
}

$pdf->SetWidths(array(12, 45, 20, 28, 28, 71, 35));   // 239 mm: el ancho util de la hoja carta horizontal
foreach ($ventasPorForma as $forma => $ventasForma) {
    $pdf->CheckPageBreak(25);
    list($r, $g, $b) = colorFormaPago($forma);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor($r, $g, $b);
    $pdf->Cell(0, 8, textoPdf('VENTAS EN ' . mb_strtoupper($forma, 'UTF-8')), 0, 1);

    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetFillColor($r, $g, $b);
    $pdf->SetTextColor(255);
    $pdf->Row(array('NRO', 'CLIENTE', 'CANTIDAD', 'TOTAL VENTA', 'PAGADO',
                    $forma == 'Efectivo' ? 'NOTA' : ($forma == 'Tarjeta' ? textoPdf('AUTORIZACIÓN') : 'REFERENCIA / CUENTA'), 'FECHA'));

    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0);
    $totalForma = 0;
    if (empty($ventasForma)) {
        $pdf->SetFillColor(251, 251, 251);
        $pdf->Cell(239, 6, textoPdf('Sin ventas en ' . strtolower($forma) . ' en este periodo.'), 1, 1, 'C', true);
    }
    foreach ($ventasForma as $i => $venta) {
        if ($forma == 'Tarjeta') {
            $detalle = 'Aut. ' . $venta['referencia'] . ($venta['ultimos4'] != '' ? '  **** ' . $venta['ultimos4'] : '');
        } elseif ($forma == 'Transferencia') {
            $detalle = 'Ref. ' . $venta['referencia'] . ($venta['bancoDestino'] != '' ? ' - ' . $venta['bancoDestino'] : '');
        } else {
            $detalle = '';
        }
        if ($venta['partes'] > 1) {
            $detalle = 'Pago mixto' . ($detalle != '' ? ' - ' . $detalle : '');
        }
        $pdf->SetFillColor($i % 2 == 1 ? 251 : 226, $i % 2 == 1 ? 251 : 226, $i % 2 == 1 ? 251 : 226);
        $pdf->Row(array($i + 1, textoPdf($venta['cliente']), $venta['cantidad'], dinero($venta['total']), dinero($venta['monto']),
                        textoPdf($detalle), $venta['fechaVenta']));
        $totalForma += $venta['monto'];
    }

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor($r, $g, $b);
    $pdf->Cell(0, 7, textoPdf('Total ' . strtolower($forma) . ': ' . dinero($totalForma)), 0, 1, 'R');
    $pdf->SetTextColor(0);
    $pdf->Ln(4);
}

// la suma de las tres formas es el total de ventas del periodo
reporteResumenFormasPago($pdf, $totalesFormaPago, 'TOTAL DE VENTAS');

$pdf->Ln(10);


reporteDesgloseSucursales($pdf, $desgloseSucursales);

$pdf->Output();
?>