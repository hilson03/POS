<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();

$usuarioSesion = requerirSesion();

$usuario = $usuarioSesion['login'];
$password = '';


$con = new conexion();

$onlyUserSession = $con->getOnlyUserData($usuarioSesion['id_usu']);

foreach ($onlyUserSession as $user) {
    $usuario = $user['login'];
    $password = $user['password'];
    $tipoUsuserio = $user['tipo'];
    $id_usuario = $user['id_usu'];
}
$urlViews = URL_VIEWS;

$preventa = 0;
$getTotalPreventa = $con->getTotalPreventa($id_usuario);

foreach ($getTotalPreventa as $preVentaTotal){
    $preventa = $preVentaTotal['total'];
}

require('../Views/FacturaViews.php');
?>
