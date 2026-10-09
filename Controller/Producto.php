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

$tipo = '';
$id_usuario = '';
$nombres = '';
$foto = '';

foreach ($searchUser as $user) {
    $tipo = $user['tipo'];
    $id_usuario = $user['id_usu'];
    $nombres = $user['nombre'];
    $foto = $user['foto'];
}
$colorElegido="#4e4e4e";
$colorDefecto="#0061c2";
$idMenu="5";

/*$updateMenuColorElegido=$con->updateOpcionElegida($colorElegido,$idMenu);
$updateMenuColorDefecto=$con->updateOpcionDefecto($colorDefecto,$idMenu);*/

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

$allProducto =$con->getAllProducto($usuarioSesion['idSucursalActiva']);
$tipoProductos = $con->getAllTipoProducto();

$menuMain = $con->getMenuMain();
require("../Views/ProductoViews.php");


?>