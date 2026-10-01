<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

$con = new conexion();

// el formulario de login envia los datos por POST: se validan, se guarda la sesion y se recarga sin datos en la URL
if (isset($_POST['usuario'], $_POST['password'])) {
    $searchUser = $con->getUser($_POST['usuario'], $_POST['password']);

    if (empty($searchUser)) {
        header("Location: ../index.php?error=login");
        exit;
    }

    guardarSesion($searchUser[0]['id_usu']);
    header("Location: AccessUsers.php");
    exit;
}

$usuarioSesion = requerirSesion();

$tipo = $usuarioSesion['tipo'];
$id_usuario = $usuarioSesion['id_usu'];
$nombres = $usuarioSesion['nombre'];
$foto = $usuarioSesion['foto'];
$usuario = $usuarioSesion['login'];

// los vendedores trabajan directamente en la pantalla de ventas
if ($tipo != 'ADMINISTRADOR') {
    header("Location: Ventas.php");
    exit;
}

$urlViews = URL_VIEWS;

$imageUser = $foto;

$userLogueado = $nombres;


$menuMain = $con->getMenuMain();

$tipoDeMoneda = $con->getTipoMoneda();
foreach ($tipoDeMoneda as $moneda){
    $tipoMonedaElegida = $moneda['tipoMoneda'];
}

// misma zona horaria con la que RegistrarVenta guarda la fecha de la venta
$ventasDelDia = $con->getVentasDelDia(date('Y-m-d'));

require('../Views/Wellcome.php');
