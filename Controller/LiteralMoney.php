<?php
/*
 * Convierte un monto a letras para la linea "Son: ..." de la factura.
 *   valorEnletras(37000, 'Lempiras')   -> "Treinta y Siete Mil Lempiras 00/100 "
 *   valorEnletras(1234.56, 'Lempiras') -> "Mil Doscientos Treinta y Cuatro Lempiras 56/100 "
 * La version anterior perdia los miles desde 10,000 en adelante (37000 salia vacio).
 */
class EnLetras
{
    private $unidades = array('', 'Un', 'Dos', 'Tres', 'Cuatro', 'Cinco', 'Seis', 'Siete', 'Ocho', 'Nueve',
        'Diez', 'Once', 'Doce', 'Trece', 'Catorce', 'Quince', 'Dieciséis', 'Diecisiete', 'Dieciocho', 'Diecinueve',
        'Veinte', 'Veintiún', 'Veintidós', 'Veintitrés', 'Veinticuatro', 'Veinticinco', 'Veintiséis', 'Veintisiete',
        'Veintiocho', 'Veintinueve');
    private $decenas = array('', '', '', 'Treinta', 'Cuarenta', 'Cincuenta', 'Sesenta', 'Setenta', 'Ochenta', 'Noventa');
    private $centenas = array('', 'Ciento', 'Doscientos', 'Trescientos', 'Cuatrocientos', 'Quinientos', 'Seiscientos',
        'Setecientos', 'Ochocientos', 'Novecientos');

    function ValorEnLetras($x, $Moneda)
    {
        $monto = round(abs((float) $x), 2);
        $entero = (int) floor($monto);
        $centavos = (int) round(($monto - $entero) * 100);
        if ($centavos == 100) {
            $entero++;
            $centavos = 0;
        }

        $letras = $entero == 0 ? 'Cero' : $this->numeroEnLetras($entero);
        // "Un Millón de Lempiras", "Dos Millones de Lempiras" (solo cuando no sigue nada despues de los millones)
        if ($entero >= 1000000 && $entero % 1000000 == 0) {
            $letras .= ' de';
        }

        $signo = (float) $x < 0 ? 'Menos ' : '';
        return $signo . $letras . ' ' . $Moneda . ' ' . str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100 ';
    }

    // numeros enteros de 1 a 999,999,999
    private function numeroEnLetras($n)
    {
        $partes = array();

        $millones = (int) floor($n / 1000000);
        $miles = (int) floor(($n % 1000000) / 1000);
        $resto = $n % 1000;

        if ($millones > 0) {
            $partes[] = $millones == 1 ? 'Un Millón' : $this->centenasEnLetras($millones) . ' Millones';
        }
        if ($miles > 0) {
            $partes[] = $miles == 1 ? 'Mil' : $this->centenasEnLetras($miles) . ' Mil';
        }
        if ($resto > 0) {
            $partes[] = $this->centenasEnLetras($resto);
        }

        return implode(' ', $partes);
    }

    // numeros de 1 a 999
    private function centenasEnLetras($n)
    {
        if ($n == 100) {
            return 'Cien';
        }

        $partes = array();
        $c = (int) floor($n / 100);
        $resto = $n % 100;

        if ($c > 0) {
            $partes[] = $this->centenas[$c];
        }
        if ($resto > 0) {
            if ($resto < 30) {
                $partes[] = $this->unidades[$resto];
            } else {
                $d = (int) floor($resto / 10);
                $u = $resto % 10;
                $partes[] = $this->decenas[$d] . ($u > 0 ? ' y ' . $this->unidades[$u] : '');
            }
        }

        return implode(' ', $partes);
    }
}
?>
