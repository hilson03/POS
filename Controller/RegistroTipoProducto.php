<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
require_once('Validar.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');
exigirCsrf('TipoProducto.php');   // solo acciones enviadas desde el sistema (proteccion contra enlaces trampa)
$usuario = $usuarioSesion['login'];

$con = new conexion();

// casilla "cada producto es un equipo unico (IMEI)"
$codigoUnico = !empty($_POST['codigoUnico']) ? 1 : 0;

if (isset($_POST['nuevo_Tipo'])) {
    $tipoproducto = textoPost('tipoProducto', 150);

    if ($tipoproducto == '') {
        $con->updateMensajeAlert("No se registro el tipo de producto: el nombre es obligatorio.", "alert alert-danger");
    } elseif ($con->tipoProductoExiste($tipoproducto)) {
        $con->updateMensajeAlert("No se registro: ya existe el tipo de producto \"" . $tipoproducto . "\".", "alert alert-danger");
    } else {
        $con->registerNewTipoProduct($tipoproducto, $codigoUnico);
        $con->updateMensajeAlert("Se registro un nuevo tipo producto  correctamente !!!", "alert alert-success");
    }

}

if (isset($_GET['idborrar'])) {
    $idborrar = (int) $_GET['idborrar'];
    $tipoBorrar = $con->getTipoProducto($idborrar);
    $usado = $tipoBorrar === null ? 0 : $con->contarProductosDeTipo($tipoBorrar['tipoproducto']);

    if ($tipoBorrar === null) {
        $con->updateMensajeAlert("Ese tipo de producto ya no existe.", "alert alert-warning");
    } elseif ($usado > 0) {
        // los productos quedarian sin tipo (y sin las reglas de su tipo, como el IMEI unico)
        $con->updateMensajeAlert("No se puede eliminar \"" . $tipoBorrar['tipoproducto'] . "\": lo usan " . $usado . " producto(s). Cámbiales el tipo primero.", "alert alert-danger");
    } else {
        $con->deleteTipoProduct($idborrar);
        $con->updateMensajeAlert("Se elimino  los datos del tipo producto correctamente !!!", "alert alert-danger");
    }


}

if (isset($_POST['update_tipo'])) {
    $idproducto = enteroPost('idtipoproducto');
    $tipoproducto = textoPost('tipoProducto', 150);

    if ($tipoproducto == '') {
        $con->updateMensajeAlert("No se actualizo el tipo de producto: el nombre es obligatorio.", "alert alert-danger");
    } elseif ($con->tipoProductoExiste($tipoproducto, $idproducto)) {
        $con->updateMensajeAlert("No se actualizo: ya existe el tipo de producto \"" . $tipoproducto . "\".", "alert alert-danger");
    } else {
        $con->updateTipoProducto($idproducto, $tipoproducto, $codigoUnico);
        $con->updateMensajeAlert("Se Actualizo  los datos del tipo Producto correctamente !!!", "alert alert-info");
    }
}



$menuMain = $con->getMenuMain();
header("Location: TipoProducto.php?estado='Activo'");
