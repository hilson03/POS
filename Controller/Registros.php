<?php
require("../Model/Conexion.php");
require("Constans.php");
require_once('Sesion.php');
require_once("SubirFoto.php");

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');
$usuario = $usuarioSesion['login'];

$con = new conexion();

// la sucursal enviada en el formulario si existe y esta activa; si no, la Sucursal Principal (1)
function sucursalValida($con, $idSucursal)
{
    $sucursal = $con->getSucursal($idSucursal);
    return ($sucursal !== null && $sucursal['estado'] == 'Activo') ? (int) $idSucursal : 1;
}

$allUsuarios = $con->getAllUserData();
$menuMain = $con->getMenuMain();


if(isset($_POST['nuevo_usuario'])){

    $usuario = $_POST['login'];
    $tipo = $_POST['tipo'];
    $nombre = $_POST['nombre'];
    $password = $_POST['password'];
    $sucursalElegida = isset($_POST['idSucursal']) ? $con->getSucursal($_POST['idSucursal']) : null;

    if ($sucursalElegida === null || $sucursalElegida['estado'] != 'Activo') {
        // sin sucursal el usuario no tendria donde trabajar: no se registra
        $updateMensaje = $con->updateMensajeAlert("No se registro el usuario: elige la sucursal donde va a trabajar.", "alert alert-danger");
    } else {
        $imagenUsuario = subirFoto('userfile', "img/user.png", $errorFoto);

        $mensaje = "Se Añadio un nuevo Usuario en la sucursal " . $sucursalElegida['nombre'];
        $alerta = "alert alert-success";
        if ($errorFoto != "") {
            $mensaje = "Se añadio el usuario, pero la foto no se guardo: " . $errorFoto;
            $alerta = "alert alert-warning";
        }

        $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

        $registerNewUser = $con->getRegisterNewUser($nombre, $tipo, $usuario, $password, $imagenUsuario, $sucursalElegida['idSucursal']);
    }

}

//esta parte permite borrar sin mas la informacion de un usuario

if (isset($_GET['idborrar'])) {

        $idUsuario = $_GET['idborrar'];

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

        $imagenUsuario = subirFoto('userfileEdit', $imagen, $errorFoto);

        $mensaje = "Se Edito los datos de  un usuario";
        $alerta = "alert alert-info";
        if ($errorFoto != "") {
            $mensaje = "Se editaron los datos del usuario, pero la foto no se guardo: " . $errorFoto;
            $alerta = "alert alert-warning";
        }

        $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

        $updateUser = $con->updateUsuario($login, $tipo, $nombre, $password, $imagenUsuario, $idUsuarioData, sucursalValida($con, $_POST['idSucursal']));

        


    }


header("Location: Usuario.php?estado='Activo'");

