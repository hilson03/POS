<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');

$con = new conexion();

if (isset($_POST['nueva_sucursal'])) {
    $nombre = trim($_POST['nombre']);

    if ($nombre == '') {
        $con->updateMensajeAlert("No se registro la sucursal: el nombre es obligatorio.", "alert alert-danger");
    } else {
        $con->registrarSucursal($nombre, trim($_POST['direccion']), trim($_POST['telefono']));
        $con->updateMensajeAlert("Se registro la sucursal " . $nombre . " correctamente !!!", "alert alert-success");
    }
}

if (isset($_POST['update_sucursal'])) {
    $idSucursal = (int) $_POST['idSucursal'];
    $nombre = trim($_POST['nombre']);
    $estado = $_POST['estado'] == 'Inactivo' ? 'Inactivo' : 'Activo';

    if ($nombre == '' || $con->getSucursal($idSucursal) === null) {
        $con->updateMensajeAlert("No se actualizo la sucursal: el nombre es obligatorio.", "alert alert-danger");
    } elseif ($estado == 'Inactivo' && $con->contarUsuariosSucursal($idSucursal) > 0) {
        // si se desactivara, sus usuarios quedarian sin una sucursal donde trabajar
        $con->updateMensajeAlert("No se puede desactivar " . $nombre . ": todavia tiene usuarios asignados. Cambialos de sucursal primero.", "alert alert-danger");
    } else {
        $con->updateSucursal($idSucursal, $nombre, trim($_POST['direccion']), trim($_POST['telefono']), $estado);
        $con->updateMensajeAlert("Se actualizo la sucursal " . $nombre . " correctamente !!!", "alert alert-info");
    }
}

header("Location: Sucursales.php?estado='Activo'");
