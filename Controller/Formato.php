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

/*
 * Nombre completo de un cliente: "Nombre Apellido". Los clientes registrados desde Ventas tienen todo el nombre
 * en el apellido (y el nombre vacio); asi salen bien los dos casos en el recibo, la busqueda y el cierre de ventas.
 */
if (!function_exists('nombreCliente')) {
    function nombreCliente($cliente)
    {
        return trim(preg_replace('/\s+/u', ' ', $cliente['nombre'] . ' ' . $cliente['apellido']));
    }
}

/*
 * Fecha como se usa en Honduras: dia/mes/ano ("26/10/2026"); si trae hora, tambien la hora ("26/10/2026 14:05").
 * En la base se siguen guardando como AAAA-MM-DD (asi se ordenan y se buscan bien).
 */
if (!function_exists('fechaHn')) {
    function fechaHn($fecha)
    {
        $fecha = trim((string) $fecha);
        $tiempo = $fecha === '' ? false : strtotime($fecha);
        if ($tiempo === false) {
            return $fecha;
        }
        return date(strlen($fecha) > 10 ? 'd/m/Y H:i' : 'd/m/Y', $tiempo);
    }
}

/*
 * Montos para MOSTRAR como se usan en Honduras: coma para los miles y punto para los centavos.
 *   miles(25000)   -> 25,000.00
 *   montoHn(25000) -> L 25,000.00   (con el simbolo de la moneda del sistema)
 * Los campos donde se escriben o calculan montos siguen usando dinero() (sin comas), porque JavaScript
 * y el servidor no leerian "25,000.00" como numero.
 */
if (!function_exists('miles')) {
    function miles($monto)
    {
        return number_format((float) $monto, 2, '.', ',');
    }
}

// simbolo de la moneda del sistema (tabla moneda: "L" en Honduras); se lee una sola vez por pagina
if (!function_exists('simboloMoneda')) {
    function simboloMoneda()
    {
        static $simbolo = null;
        if ($simbolo === null) {
            $simbolo = 'L';
            try {
                if (class_exists('conexion')) {
                    $con = new conexion();
                    foreach ($con->getTipoMoneda() as $moneda) {
                        if (trim((string) $moneda['tipoMoneda']) !== '') {
                            $simbolo = trim($moneda['tipoMoneda']);
                        }
                    }
                }
            } catch (Throwable $e) {
                // sin base de datos queda la L
            }
        }
        return $simbolo;
    }
}

if (!function_exists('montoHn')) {
    function montoHn($monto)
    {
        return simboloMoneda() . ' ' . miles($monto);
    }
}
