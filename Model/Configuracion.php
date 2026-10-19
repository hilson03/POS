<?php
/*
 * Lee la configuracion de la instalacion (config.php en la carpeta del proyecto, ver config.ejemplo.php)
 * y aplica el manejo de errores: en el hosting no se muestran en pantalla, se guardan en registros/errores.log.
 */
function configuracion()
{
    static $config = null;
    if ($config === null) {
        // valores de Laragon por si no hay config.php (computadora de desarrollo)
        $config = array('db_servidor' => 'localhost', 'db_usuario' => 'root', 'db_clave' => '', 'db_nombre' => 'icontpos',
                        'mostrar_errores' => true);
        $archivo = dirname(__DIR__) . '/config.php';
        if (is_file($archivo)) {
            $propia = include $archivo;
            if (is_array($propia)) {
                $config = array_merge($config, $propia);
            }
        }

        if (empty($config['mostrar_errores'])) {
            $carpeta = dirname(__DIR__) . '/registros';
            if (!is_dir($carpeta)) {
                @mkdir($carpeta, 0755, true);
            }
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
            ini_set('error_log', $carpeta . '/errores.log');
        }
    }
    return $config;
}

configuracion();
