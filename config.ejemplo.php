<?php
/*
 * Configuracion de la instalacion (base de datos y errores).
 *
 * Copia este archivo como "config.php" (en esta misma carpeta) y pon los datos de cada instalacion.
 * config.php NO se sube a GitHub (el repositorio es publico): ahi va la contrasena real de la base.
 * Si no existe config.php se usan los valores de Laragon (root sin contrasena, base icontpos).
 */
return array(
    // datos de la base de datos (en Hostinger: panel > Bases de datos > MySQL)
    'db_servidor' => 'localhost',
    'db_usuario'  => 'root',
    'db_clave'    => '',
    'db_nombre'   => 'icontpos',

    // true en la computadora de desarrollo; false en el hosting (los errores se guardan en registros/errores.log
    // en vez de mostrarse en pantalla, donde revelarian rutas del servidor)
    'mostrar_errores' => true,
);
