<?php
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
