<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();

$usuarioSesion = requerirSesion('ADMINISTRADOR');

$usuario = $usuarioSesion['login'];
$password = '';

$con = new Conexion();


$searchUser = $con->getOnlyUserData($usuarioSesion['id_usu']);
$allUsuarios = $con->getAllUserData();

foreach ($searchUser as $user) {
    $tipo = $user['tipo'];
    $id_usuario = $user['id_usu'];
    $nombres = $user['nombre'];
    $password = $user['password'];
    $foto = $user['foto'];
}

$colorElegido="#4e4e4e";
$colorDefecto="#0061c2";
$idMenu="10";

$updateMenuColorElegido=$con->updateOpcionElegida($colorElegido,$idMenu);
$updateMenuColorDefecto=$con->updateOpcionDefecto($colorDefecto,$idMenu);

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

$allVentas =$con->getAllVentas();

$menuMain = $con->getMenuMain();


require("../Views/ConsolidarViews.php");


?>