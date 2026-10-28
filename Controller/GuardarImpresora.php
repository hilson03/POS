<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');
exigirCsrf('Impresora.php');   // solo acciones enviadas desde el sistema (proteccion contra enlaces trampa)

$con = new conexion();

// ancho del rollo de la impresora termica de los comprobantes (se guarda con las demas preferencias, tabla apariencia)
if (isset($_POST['guardar_papel'])) {
    $ancho = (isset($_POST['ancho_papel']) && $_POST['ancho_papel'] == '58') ? '58' : '80';
    $con->setApariencia('ancho_papel', $ancho);
    $con->updateMensajeAlert("Los comprobantes se imprimirán en rollo de " . $ancho . " mm.", "alert alert-success");
}

header("Location: Impresora.php?estado=1");
