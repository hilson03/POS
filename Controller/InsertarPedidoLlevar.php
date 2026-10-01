<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();

$idProducto = $_GET['idproducto'];
$usuarioSesion = requerirSesion('', true);
$idUsuario = $usuarioSesion['id_usu'];

$con = new conexion();

$onlyUserSession = $con->getOnlyUserData($idUsuario);

foreach ($onlyUserSession as $user) {
    $usuario = $user['login'];
}

$productElegido = $con->getProductoElegido($idProducto, $usuarioSesion['idSucursalActiva']);

foreach ($productElegido as $product) {
    $idProducto = $product['idproducto'];
    $imagen = $product['imagen'];
    $codigo = $product['codigo'];
    $nombreProducto = $product['nombreProducto'];
    $cantidad = $product['cantidad'];
    $fechaRegistro = date('Y-m-d H:i:s');
    $precioVenta = $product['precioVenta'];
    $tipo = $product['tipo'];
}
$tipoPedido = 'Llevar';
$urlViews = URL_VIEWS;
$cantidadEnPedido = $con->getCantidadEnPreventa($idProducto, $usuarioSesion['idSucursalActiva']);

if (!is_numeric($precioVenta) || $precioVenta <= 0) {
    $mensajeStock = "El producto $nombreProducto no tiene precio de venta. Asignale un precio en el modulo Productos.";
} elseif ($cantidadEnPedido < (int) $cantidad) {
    $regiterPreventa = $con->insertarPreventaProducto($imagen, $nombreProducto, $precioVenta, $idProducto, $precioVenta, $idUsuario, $tipoPedido, $usuarioSesion['idSucursalActiva']);
} else {
    $mensajeStock = "Sin stock: no quedan unidades disponibles de $nombreProducto (stock: " . (int) $cantidad . ").";
}

require('../Views/RefreshPedido.php');
?>
