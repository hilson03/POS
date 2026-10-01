<?php
require("../Model/Conexion.php");
require("Constans.php");
require_once("SubirFoto.php");

if (!isset($_SESSION)){
    session_start();
}

$usuarioLogin = $_POST['usuarioLogin'];
$passwordLogin = $_POST['passwordLogin'];

$con = new conexion();

$allUsuarios = $con->getAllUserData();
$menuMain = $con->getMenuMain();


if(isset($_POST['nuevo_usuario'])){

    $usuario = $_POST['login'];
    $tipo = $_POST['tipo'];
    $nombre = $_POST['nombre'];
    $password = $_POST['password'];
    
    $imagenUsuario = subirFoto('userfile', "img/user.png", $errorFoto);

    $mensaje = "Se Añadio un nuevo Usuario";
    $alerta = "alert alert-success";
    if ($errorFoto != "") {
        $mensaje = "Se añadio el usuario, pero la foto no se guardo: " . $errorFoto;
        $alerta = "alert alert-warning";
    }

    $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);




    $registerNewUser = $con->getRegisterNewUser($nombre, $tipo, $usuario, $password, $imagenUsuario);

}

//esta parte permite borrar sin mas la informacion de un usuario

if (isset($_GET['idborrar'])) {

        $idUsuario = $_GET['idborrar'];
        $usuarioLogin = $_GET['usuarioLogin'];
        $passwordLogin = $_GET['passwordLogin'];

        $mensaje = "Se Elimino un usuario";
        $alerta = "alert alert-danger";

        $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

        $deleteUser = $con->deleteUsuario($idUsuario);



    }

//aqui viene a dar la informacion cuando se actualizan los datos de usuario 

if (isset($_POST['update_usuario'])) {

        $idUsuarioData = $_POST['idUsuario'];
        $login = $_POST['login'];
        $tipo = $_POST['tipo'];
        $nombre = $_POST['nombre'];
        $password = $_POST['password'];
        $imagen = $_POST['imagen'];

        $usuarioLogin = $_POST['usuarioLogin'];
        $passwordLogin = $_POST['passwordLogin'];

        $imagenUsuario = subirFoto('userfileEdit', $imagen, $errorFoto);

        $mensaje = "Se Edito los datos de  un usuario";
        $alerta = "alert alert-info";
        if ($errorFoto != "") {
            $mensaje = "Se editaron los datos del usuario, pero la foto no se guardo: " . $errorFoto;
            $alerta = "alert alert-warning";
        }

        $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

        $updateUser = $con->updateUsuario($login, $tipo, $nombre, $password, $imagenUsuario, $idUsuarioData);

        


    }


header("Location: Usuario.php?usuario=$usuarioLogin&password=$passwordLogin&estado='Activo'");

