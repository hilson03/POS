<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');

$con = new conexion();

$idSucursal = isset($_POST['idSucursal']) ? (int) $_POST['idSucursal'] : 0;
$sucursal = $con->getSucursal($idSucursal);

if ($sucursal === null || $sucursal['estado'] != 'Activo') {
    $con->updateMensajeAlert("Esa sucursal no existe o esta inactiva.", "alert alert-danger");
} elseif ($idSucursal != $usuarioSesion['idSucursalActiva'] && $con->contarPreventaUsuario($usuarioSesion['id_usu']) > 0) {
    // el pedido en curso aparta stock de la sucursal actual; no se puede llevar a otra
    $con->updateMensajeAlert("Termina o cancela tu pedido en Ventas antes de cambiar de sucursal.", "alert alert-warning");
} else {
    cambiarSucursalActiva($idSucursal);
    $con->updateMensajeAlert("Ahora estas trabajando en la sucursal " . $sucursal['nombre'] . ".", "alert alert-info");
}

// vuelve a la pantalla donde estaba (solo dentro del sistema)
$volver = 'AccessUsers.php';
if (!empty($_SERVER['HTTP_REFERER'])) {
    $pagina = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
    if (preg_match('/^[A-Za-z]+\.php$/', $pagina) && file_exists(__DIR__ . '/' . $pagina)) {
        $volver = $pagina;
    }
}
header("Location: " . $volver . "?estado='Activo'");
