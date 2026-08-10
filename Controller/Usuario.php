<?php
require('../Model/Conexion.php');
require('Constans.php');



if(!isset($_SESSION)){
    
    session_start();

}

$usuario = $_GET['usuario'];
$password = $_GET['password'];

$con = new conexion();


$allUsuarios = $con->getAllUserData();

$searchUser = $con->getUser($usuario, $password);

$tipo = '';
$id_usuario = '';
$nombres = '';
$foto = '';

foreach ($searchUser as $user) {

    $tipo =$user['tipo'];
    $id_usuario =$user['id_usu'];
    $nombres = $user['nombre'];
    $password =$user['password'];
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