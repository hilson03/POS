<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();

$usuarioSesion = requerirSesion();
$idUsuario = $usuarioSesion['id_usu'];

$con = new conexion();

$onlyUserSession = $con->getOnlyUserData($idUsuario);

foreach ($onlyUserSession as $user) {
    $usuario = $user['login'];
    $tipoUsuserio = $user['tipo'];
}
$urlViews = URL_VIEWS;

$idProducto = $_GET['idProducto'];
$imagen = $_GET['imagen'];
$precio = $_GET['precio'];
$precioVenta = $_GET['pventa'];
$tipoUser = $_GET['tipo'];
$tipoPedido = $_GET['tipoPedido'];
$idpreventa = $_GET['idpreventa'];
$nombreProducto = $_GET['producto'];
$cantidadUpdated = $_GET['cantidadUpdated'];
$nuevoPrecio = $_GET['nuevoPrecio'];

$parametroStock = '';
$productElegido = $con->getProductoElegido($idProducto, $usuarioSesion['idSucursalActiva']);
foreach ($productElegido as $product) {
    $stockProducto = (int) $product['cantidad'];
}
// unidades disponibles = stock - lo apartado en los pedidos de otros usuarios y en el otro tipo (Mesa/Llevar) de este usuario
$cantidadDisponible = $stockProducto - $con->getCantidadEnPreventa($idProducto, $usuarioSesion['idSucursalActiva'], $idUsuario, $tipoPedido);

if ((int) $cantidadUpdated > $cantidadDisponible) {
    $cantidadUpdated = $cantidadDisponible;
    $parametroStock = "&sinStock=" . urlencode($nombreProducto) . "&stock=" . $stockProducto;
}

$getCantidadActual = $con->getCantidadProductoChoose($idProducto, $tipoPedido, $idUsuario);

foreach ($getCantidadActual as $cantidadPedidoActual) {
    $cantidadActual = $cantidadPedidoActual['cantidadTotal'];
}

if ($cantidadUpdated > $cantidadActual) {
    $cantidadNuevaActualizada = $cantidadUpdated - $cantidadActual;
    for ($i=0; $i <$cantidadNuevaActualizada; $i++ ){
        $isertarCantidadActualizada = $con->insertarPreventaProducto($imagen, $nombreProducto, $precioVenta, $idProducto, $precioVenta, $idUsuario, $tipoPedido, $usuarioSesion['idSucursalActiva']);
    }
}


if (($cantidadUpdated < $cantidadActual) and ($cantidadUpdated != 1)) {
    $cantidadNuevaActualizada = $cantidadActual - ($cantidadActual - $cantidadUpdated);
    $deleteCantidadActual = $con->deleteOnlyPreventa($idProducto, $tipoPedido, $idUsuario);
    for ($i=0; $i <$cantidadNuevaActualizada; $i++ ){
        $isertarCantidadActualizada = $con->insertarPreventaProducto($imagen, $nombreProducto, $precioVenta, $idProducto, $precioVenta, $idUsuario, $tipoPedido, $usuarioSesion['idSucursalActiva']);
    }
}


if ($cantidadUpdated == 1) {
    $deleteCantidadActual = $con->deleteOnlyPreventa($idProducto, $tipoPedido, $idUsuario);
    for ($i=0; $i <$cantidadUpdated; $i++ ){
        $isertarCantidadActualizada = $con->insertarPreventaProducto($imagen, $nombreProducto, $precioVenta, $idProducto, $precioVenta, $idUsuario, $tipoPedido, $usuarioSesion['idSucursalActiva']);
    }
}

header("Location: Ventas.php?" . ltrim($parametroStock, '&'));

?>
