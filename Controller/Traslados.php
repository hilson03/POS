<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();

$usuarioSesion = requerirSesion();

$usuario = $usuarioSesion['login'];

$con = new Conexion();

$tipo = $usuarioSesion['tipo'];
$id_usuario = $usuarioSesion['id_usu'];
$nombres = $usuarioSesion['nombre'];
$foto = $usuarioSesion['foto'];
$esAdministrador = $tipo == 'ADMINISTRADOR';
// desde 2026-10-19 los vendedores gestionan traslados igual que el administrador (pedir, aprobar, rechazar, enviar, recibir
// y cancelar), siempre como usuarios de su sucursal: aprueba y envia la tienda que tiene el producto, recibe la que lo pidio
$gestionaTraslados = true;
$idSucursalActiva = (int) $usuarioSesion['idSucursalActiva'];

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

// lo que otras tiendas le piden a esta sucursal y lo que esta sucursal pidio
$trasladosRecibidos = $con->getTrasladosSucursal($idSucursalActiva, 'recibidas');
$trasladosEnviados = $con->getTrasladosSucursal($idSucursalActiva, 'enviadas');

// formulario para pedir: productos y stock disponible en las otras tiendas
$productosPedir = array();
$stockOtras = array();
$idProductoPedir = isset($_GET['pedir']) ? (int) $_GET['pedir'] : 0;
if ($gestionaTraslados) {
    $stockOtras = $con->getStockOtrasSucursales($idSucursalActiva);
    $resultado = $con->getAllProducto($idSucursalActiva);
    while ($producto = mysqli_fetch_array($resultado)) {
        $productosPedir[] = $producto;
    }
}

$menuMain = $con->getMenuMain();
require("../Views/TrasladosViews.php");
