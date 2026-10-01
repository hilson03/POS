<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');



iniciarSesionSegura();

$usuarioSesion = requerirSesion('ADMINISTRADOR');

$usuario = $usuarioSesion['login'];
$password = '';

$con = new conexion();


$allUsuarios = $con->getAllUserData();
$sucursales = $con->getSucursales(true);

$searchUser = $con->getOnlyUserData($usuarioSesion['id_usu']);

$tipo = '';
$id_usuario = '';
$nombres = '';
$foto = '';

foreach ($searchUser as $user) {

    $tipo =$user['tipo'];
    $id_usuario =$user['id_usu'];
    $nombres = $user['nombre'];
    $foto = $user['foto'];

    # code...
}

$tipoDeAlerta = $con->getMensajeAlerta();

foreach ($tipoDeAlerta as $tipoAlerta) {
    $alerta = $tipoAlerta['tipoAlerta'];
    $mensaje = $tipoAlerta['mensaje'];
}





$urlViews = URL_VIEWS;
$imageUser = $foto;
$userLogueado = $nombres;

$menuMain = $con->getMenuMain();

require("../Views/UsuarioViews.php");