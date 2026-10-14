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
$formasCobro = array('tarjeta' => 'Tarjeta', 'transferencia' => 'Transferencia', 'mixto' => 'Mixto');
$formaPago = (isset($_GET['forma']) && isset($formasCobro[$_GET['forma']])) ? $formasCobro[$_GET['forma']] : 'Efectivo';

// transferencia (sola o en pago mixto): el vendedor elige en cual cuenta de la tienda llego el dinero
$cuentasTransferencia = in_array($formaPago, array('Transferencia', 'Mixto')) ? $con->getCuentasBancarias(true) : array();

require('../Views/FacturaViews.php');
?>
