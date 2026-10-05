<?php
/*
 * Formato de los montos de dinero en todo el sistema: siempre con 2 decimales.
 *   dinero(100)    -> 100.00
 *   dinero(7500)   -> 7500.00
 *   dinero(11.25)  -> 11.25
 * Sin separador de miles a proposito: los campos del cobro hacen calculos con estos valores en JavaScript
 * y "7,500.00" no se podria leer como numero.
 */
if (!function_exists('dinero')) {
    function dinero($monto)
    {
        return number_format((float) $monto, 2, '.', '');
    }
}
