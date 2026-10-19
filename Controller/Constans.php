<?php
// configuracion de la instalacion (config.php): base de datos y errores ocultos en el hosting
require_once(__DIR__ . '/../Model/Configuracion.php');

// hora de Honduras para todo el sistema: sin esto PHP usa UTC (6 horas adelantado) y una venta de la noche
// quedaba con fecha del dia siguiente en los reportes. En el hosting tambien evita depender de la hora del servidor.
date_default_timezone_set('America/Tegucigalpa');

// nombre del cliente cuando se vende sin RTN/DNI ni nombre (solo en pagos en efectivo)
if (!defined('CONSUMIDOR_FINAL')) define('CONSUMIDOR_FINAL', 'Consumidor Final');

// formato de los montos de dinero (dinero()), disponible en todas las pantallas
require_once(__DIR__ . '/Formato.php');
// logos e imagenes personalizadas del negocio (imagenMarca())
require_once(__DIR__ . '/Marca.php');

/*
 * URL_VIEWS: direccion web de la carpeta Views (estilos, iconos, fotos).
 * Se arma sola segun como se entro al sistema, asi funciona igual:
 *   - en esta computadora:            http://ventaspos.test/Views/
 *   - desde otro equipo de la red:    http://192.168.x.x/ventaspos/Views/
 *   - en el hosting:                  https://dominio.com/Views/
 */
if (!defined('URL_VIEWS')) {
    $esHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    // solo caracteres validos de un nombre de servidor o IP (con puerto opcional)
    if (!preg_match('/^[A-Za-z0-9.\-]+(:\d+)?$/', $host)) {
        $host = 'localhost';
    }

    // carpeta del proyecto dentro de la raiz del servidor ('' si el sitio apunta directo al proyecto)
    $raizServidor = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])) : '';
    $raizProyecto = str_replace('\\', '/', dirname(__DIR__));
    $carpeta = '';
    if ($raizServidor !== '' && stripos($raizProyecto, $raizServidor) === 0) {
        $carpeta = substr($raizProyecto, strlen($raizServidor));
    }

    define('URL_VIEWS', ($esHttps ? 'https' : 'http') . '://' . $host . rtrim($carpeta, '/') . '/Views/');
}
if(!defined('ADDRESS')) define('ADDRESS', dirname(__DIR__) . '/Views/fotoproducto/');
?>
