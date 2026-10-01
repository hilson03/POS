<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();

$idProducto = $_POST['idProducto'];
$tipo = $_POST['tipo'];
$usuarioSesion = requerirSesion('', true);
$idUsuario = $usuarioSesion['id_usu'];

$con = new conexion();

$onlyUserSession = $con->getOnlyUserData($idUsuario);

foreach ($onlyUserSession as $user) {
    $usuario = $user['login'];
    $password = $user['password'];
    $tipoUsuserio = $user['tipo'];
}
$urlViews = URL_VIEWS;

$editPreVentaData = $con->getDataProductoChoose($idProducto, $tipo, $idUsuario);

foreach ($editPreVentaData as $preVenta) {
    $idPreventa = $preVenta['idPreventa'];
    $imagen = $preVenta['imagen'];
    $producto = $preVenta['producto'];
    $precio = $preVenta['precio'];
    $idProducto = $preVenta['idProducto'];
    $pventa = $preVenta['pventa'];
    $userId = $preVenta['idUser'];
    $tipoPedido = $preVenta['tipo'];
}

$getCantidad = $con->getCantidadProductoChoose($idProducto, $tipo, $idUsuario);

foreach ($getCantidad as $getCantidadTotal) {
    $cantidadActual = $getCantidadTotal['cantidadTotal'];
}


require('../Views/EditPreVentaForm.php');
?>
