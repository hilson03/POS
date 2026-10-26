<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
require_once('Validar.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');

$con = new conexion();

// bancos del catalogo: el nombre no se repite (sin distinguir mayusculas ni tildes)
if (isset($_POST['nuevo_banco']) || isset($_POST['update_banco'])) {
    $nombre = textoPost('nombreBanco', 60);
    $idBanco = enteroPost('idBanco');
    $estado = (isset($_POST['estado']) && $_POST['estado'] == 'Inactivo') ? 'Inactivo' : 'Activo';

    if ($nombre == '') {
        $con->updateMensajeAlert("No se guardó el banco: el nombre es obligatorio.", "alert alert-danger");
    } elseif ($con->bancoExiste($nombre, isset($_POST['update_banco']) ? $idBanco : 0)) {
        $con->updateMensajeAlert("No se guardó: el banco \"" . $nombre . "\" ya está en la lista.", "alert alert-danger");
    } elseif (isset($_POST['nuevo_banco'])) {
        $con->registrarBanco($nombre);
        $con->updateMensajeAlert("Se agregó el banco " . $nombre . " a la lista.", "alert alert-success");
    } elseif ($con->getBanco($idBanco) === null) {
        $con->updateMensajeAlert("No se encontró el banco que querías editar.", "alert alert-danger");
    } else {
        $con->updateBanco($idBanco, $nombre, $estado);
        $con->updateMensajeAlert("Se actualizó el banco " . $nombre . ($estado == 'Inactivo' ? ": ya no aparece al registrar cuentas y sus cuentas no se pueden elegir al cobrar." : "."), "alert alert-info");
    }
}

// cuentas de la tienda: el banco se elige del catalogo y el numero lleva solo digitos
if (isset($_POST['nueva_cuenta']) || isset($_POST['update_cuenta'])) {
    $idBanco = enteroPost('idBanco');
    $numero = textoPost('numero', 30);
    $titular = textoPost('titular', 100);
    $idCuenta = enteroPost('idCuenta');
    $banco = $con->getBanco($idBanco);
    $cuentaActual = isset($_POST['update_cuenta']) ? $con->getCuentaBancaria($idCuenta) : null;

    if (isset($_POST['update_cuenta']) && $cuentaActual === null) {
        $con->updateMensajeAlert("No se encontró la cuenta que querías editar.", "alert alert-danger");
    } elseif ($banco === null || ($banco['estado'] != 'Activo' && !($cuentaActual !== null && (int) $cuentaActual['idBanco'] === $idBanco))) {
        // un banco inactivo no se puede elegir (salvo que la cuenta ya fuera de ese banco)
        $con->updateMensajeAlert("No se guardó la cuenta: elige el banco de la lista.", "alert alert-danger");
    } elseif (!preg_match('/^[0-9]{4,30}$/', $numero)) {
        $con->updateMensajeAlert("No se guardó la cuenta: el número de cuenta lleva solo números, sin guiones ni espacios (de 4 a 30 dígitos).", "alert alert-danger");
    } elseif ($con->cuentaRepetida($idBanco, $numero, $idCuenta)) {
        $con->updateMensajeAlert("No se guardó: la cuenta " . $numero . " de " . $banco['nombre'] . " ya está registrada.", "alert alert-danger");
    } elseif (isset($_POST['nueva_cuenta'])) {
        $con->registrarCuentaBancaria($idBanco, $numero, $titular);
        $con->updateMensajeAlert("Se registró la cuenta de " . $banco['nombre'] . " correctamente !!!", "alert alert-success");
    } else {
        $estado = (isset($_POST['estado']) && $_POST['estado'] == 'Inactivo') ? 'Inactivo' : 'Activo';
        $con->updateCuentaBancaria($idCuenta, $idBanco, $numero, $titular, $estado);
        $con->updateMensajeAlert("Se actualizó la cuenta de " . $banco['nombre'] . " correctamente !!!", "alert alert-info");
    }
}

header("Location: CuentasBancarias.php?estado='Activo'");
