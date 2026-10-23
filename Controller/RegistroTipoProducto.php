<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
require_once('Validar.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');
$usuario = $usuarioSesion['login'];

$con = new conexion();

if (isset($_POST['nuevo_Tipo'])) {
    $tipoproducto = textoPost('tipoProducto', 150);

    if ($tipoproducto == '') {
        $con->updateMensajeAlert("No se registro el tipo de producto: el nombre es obligatorio.", "alert alert-danger");
    } elseif ($con->tipoProductoExiste($tipoproducto)) {
        $con->updateMensajeAlert("No se registro: ya existe el tipo de producto \"" . $tipoproducto . "\".", "alert alert-danger");
    } else {
        $con->registerNewTipoProduct($tipoproducto);
        $con->updateMensajeAlert("Se registro un nuevo tipo producto  correctamente !!!", "alert alert-success");
    }

}

if (isset($_GET['idborrar'])) {
    $idborrar = $_GET['idborrar'];

    $mensaje = "Se elimino  los datos del tipo producto correctamente !!!";
    $alerta = "alert alert-danger";
    $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

    $deleteTipoProducto = $con->deleteTipoProduct($idborrar);


}

if (isset($_POST['update_tipo'])) {
    $idproducto = enteroPost('idtipoproducto');
    $tipoproducto = textoPost('tipoProducto', 150);

    if ($tipoproducto == '') {
        $con->updateMensajeAlert("No se actualizo el tipo de producto: el nombre es obligatorio.", "alert alert-danger");
    } elseif ($con->tipoProductoExiste($tipoproducto, $idproducto)) {
        $con->updateMensajeAlert("No se actualizo: ya existe el tipo de producto \"" . $tipoproducto . "\".", "alert alert-danger");
    } else {
        $con->updateTipoProducto($idproducto, $tipoproducto);
        $con->updateMensajeAlert("Se Actualizo  los datos del tipo Producto correctamente !!!", "alert alert-info");
    }
}



$menuMain = $con->getMenuMain();
header("Location: TipoProducto.php?estado='Activo'");
