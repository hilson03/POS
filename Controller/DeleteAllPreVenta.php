<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();

$usuarioSesion = requerirSesion('', true);
$idUsuario = $usuarioSesion['id_usu'];

$con = new conexion();

$onlyUserSession = $con->getOnlyUserData($idUsuario);

foreach ($onlyUserSession as $user) {
    $usuario = $user['login'];
}

$deleteAllPreventa = $con->deleteAllPreventa($idUsuario);

$urlViews = URL_VIEWS;

require('../Views/RefreshPedido.php');
?>
