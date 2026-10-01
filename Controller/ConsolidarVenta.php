<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion();
$usuario = $usuarioSesion['login'];

$con = new conexion();

// el administrador puede trabajar con cualquier venta; un vendedor solo con las suyas
function puedeModificarVenta($venta, $usuarioSesion)
{
    return $venta !== null
        && ($usuarioSesion['tipo'] == 'ADMINISTRADOR' || (int) $venta['idUsuario'] === (int) $usuarioSesion['id_usu']);
}


if (isset($_GET['idConsolidar'])) {

    $idVenta = $_GET['idConsolidar'];

    if (puedeModificarVenta($con->getVentaConsolidar($idVenta), $usuarioSesion)) {
        $con->consolidarVenta($idVenta);
        $updateMensaje = $con->updateMensajeAlert("Se Consolido la venta  correctamente !!!", "alert alert-success");
    } else {
        $updateMensaje = $con->updateMensajeAlert("No puedes consolidar esa venta: no existe o la hizo otro vendedor.", "alert alert-danger");
    }

}


if (isset($_POST['insertarComentario'])) {
    $idVentas = $_POST['idVentas'];
    $comentario = $_POST['comentario'];

    if (puedeModificarVenta($con->getVentaConsolidar($idVentas), $usuarioSesion)) {
        $updateComentario = $con->insertarComentarioFicha($idVentas, $comentario);
        $updateMensaje = $con->updateMensajeAlert("Se Inserto un comentario correctamente !!!", "alert alert-info");
    } else {
        $updateMensaje = $con->updateMensajeAlert("No puedes comentar esa venta: no existe o la hizo otro vendedor.", "alert alert-danger");
    }

}

$searchUser = $con->getOnlyUserData($usuarioSesion['id_usu']);
$allUsuarios = $con->getAllUserData();

foreach ($searchUser as $user) {
    $tipo = $user['tipo'];
    $id_usuario = $user['id_usu'];
    $nombres = $user['nombre'];
    $foto = $user['foto'];
}


$menuMain = $con->getMenuMain();

header("Location: Consolidar.php?estado='Activo'");


?>
