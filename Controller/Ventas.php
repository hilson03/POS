<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();

$usuarioSesion = requerirSesion();

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
$idMenu="7";

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


$tipoDeMoneda = $con->getTipoMoneda();
foreach ($tipoDeMoneda as $moneda){
    $tipoMonedaElegida =$moneda['tipoMoneda'];
}

if (isset($_GET['sinStock'])) {
    $mensajeStock = "Sin stock suficiente de " . $_GET['sinStock'] . " (stock: " . (int) $_GET['stock'] . "). Se ajusto la cantidad al maximo disponible.";
}

$allProducto =$con->getAllProducto($usuarioSesion['idSucursalActiva']);
// stock en las otras tiendas, para saber a cual pedir un producto agotado
$stockOtras = $con->getStockOtrasSucursales($usuarioSesion['idSucursalActiva']);
$esAdministrador = $usuarioSesion['tipo'] == 'ADMINISTRADOR';
$menuMain = $con->getMenuMain();
require("../Views/VentasViews.php");


?>