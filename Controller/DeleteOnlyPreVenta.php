<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();

$idProducto = $_GET['idProducto'];
$tipo = $_GET['tipo'];
$usuarioSesion = requerirSesion('', true);
$idUsuario = $usuarioSesion['id_usu'];

$con = new conexion();

$onlyUserSession = $con->getOnlyUserData($idUsuario);

foreach ($onlyUserSession as $user) {
    $usuario = $user['login'];
}

$deleteOnlyPreventaProducto = $con->deleteOnlyPreventa($idProducto, $tipo, $idUsuario);

$urlViews = URL_VIEWS;

require('../Views/RefreshPedido.php');
?>
