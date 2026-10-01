<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');
$usuario = $usuarioSesion['login'];

$con = new conexion();


if (isset($_GET['idConsolidar'])) {

    $idVenta = $_GET['idConsolidar'];
    $codigoControl = $_GET['codigoControl'];

    $updateDatosclienteventa = $con->updateDatosclienteventa($codigoControl);
    $updateDatosfacturaventa = $con->updateDatosfacturaventa($codigoControl);
    $updateDatosventa = $con->updateDatosventa($codigoControl);
    $updateDatosventatotal = $con->updateDatosventatotal($codigoControl);


    $mensaje = "Se Consolido la venta  correctamente !!!";
    $alerta = "alert alert-success";
    $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

}


if (isset($_POST['insertarComentario'])) {
    $idVentas = $_POST['idVentas'];
    $comentario = $_POST['comentario'];


   $updateComentario = $con ->insertarComentarioFicha($idVentas, $comentario);

    $mensaje = "Se Inserto un comentario correctamente !!!";
    $alerta = "alert alert-info";

    $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

}

$searchUser = $con->getOnlyUserData($usuarioSesion['id_usu']);
$allUsuarios = $con->getAllUserData();

foreach ($searchUser as $user) {
    $tipo = $user['tipo'];
    $id_usuario = $user['id_usu'];
    $nombres = $user['nombre'];
    $password = $user['password'];
    $foto = $user['foto'];
}


$menuMain = $con->getMenuMain();

header("Location: Consolidar.php?estado='Activo'");


?>
