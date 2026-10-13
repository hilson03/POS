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
    $tipoUsuserio = $user['tipo'];
    $id_usuario = $user['id_usu'];
}
$urlViews = URL_VIEWS;

$preventa = 0;
$getTotalPreventa = $con->getTotalPreventa($id_usuario);

foreach ($getTotalPreventa as $preVentaTotal){
    $preventa = $preVentaTotal['total'];
}

// forma de pago elegida en la ventana "¿Como va a pagar el cliente?" (el formulario de cobro cambia segun ella)
$formaPago = (isset($_GET['forma']) && $_GET['forma'] == 'tarjeta') ? 'Tarjeta' : 'Efectivo';

require('../Views/FacturaViews.php');
?>
