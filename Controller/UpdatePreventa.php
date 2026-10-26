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

$idProducto = isset($_GET['idProducto']) ? (int) $_GET['idProducto'] : 0;
// tienda de celulares: un solo tipo de venta
$tipoPedido = 'Venta';
$cantidadUpdated = (isset($_GET['cantidadUpdated']) && ctype_digit(trim((string) $_GET['cantidadUpdated']))) ? (int) $_GET['cantidadUpdated'] : 0;

$parametroStock = '';
$productElegido = $con->getProductoElegido($idProducto, $usuarioSesion['idSucursalActiva']);
if (empty($productElegido) || $cantidadUpdated < 1) {
    header("Location: Ventas.php");
    exit;
}
// nombre, foto y precio salen siempre del producto guardado, nunca del enlace (se podria alterar el precio)
foreach ($productElegido as $product) {
    $stockProducto = (int) $product['cantidad'];
    $imagen = $product['imagen'];
    $nombreProducto = $product['nombreProducto'];
    $precioVenta = $product['precioVenta'];
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
