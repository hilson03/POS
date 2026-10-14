<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();

$usuarioSesion = requerirSesion('ADMINISTRADOR');

$con = new Conexion();

$tipo = $usuarioSesion['tipo'];
$nombres = $usuarioSesion['nombre'];
$foto = $usuarioSesion['foto'];

$tipoDeAlerta = $con->getMensajeAlerta();
foreach ($tipoDeAlerta as $tipoAlerta) {
    $alerta = $tipoAlerta['tipoAlerta'];
    $mensaje = $tipoAlerta['mensaje'];
}

if (!isset($_GET['estado'])) {
    $mensaje = "";
    $alerta = "";

    $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);
}

$urlViews = URL_VIEWS;
$userLogueado = $nombres;
$imageUser = $foto;


$menuMain = $con->getMenuMain();
require("../Views/ImpresoraViews.php");
