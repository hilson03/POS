<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');

$con = new conexion();

// limpia lo escrito y lo corta al largo de la columna
function textoCuenta($campo, $maximo)
{
    $texto = isset($_POST[$campo]) ? trim(preg_replace('/\s+/u', ' ', (string) $_POST[$campo])) : '';
    return mb_substr($texto, 0, $maximo, 'UTF-8');
}

if (isset($_POST['nueva_cuenta']) || isset($_POST['update_cuenta'])) {
    $banco = textoCuenta('banco', 60);
    $numero = textoCuenta('numero', 40);
    $titular = textoCuenta('titular', 100);

    if ($banco == '' || $numero == '') {
        $con->updateMensajeAlert("No se guardó la cuenta: el banco y el número de cuenta son obligatorios.", "alert alert-danger");
    } elseif (isset($_POST['nueva_cuenta'])) {
        $con->registrarCuentaBancaria($banco, $numero, $titular);
        $con->updateMensajeAlert("Se registró la cuenta de " . $banco . " correctamente !!!", "alert alert-success");
    } else {
        $idCuenta = isset($_POST['idCuenta']) ? (int) $_POST['idCuenta'] : 0;
        $estado = (isset($_POST['estado']) && $_POST['estado'] == 'Inactivo') ? 'Inactivo' : 'Activo';
        if ($con->getCuentaBancaria($idCuenta) === null) {
            $con->updateMensajeAlert("No se encontró la cuenta que querías editar.", "alert alert-danger");
        } else {
            $con->updateCuentaBancaria($idCuenta, $banco, $numero, $titular, $estado);
            $con->updateMensajeAlert("Se actualizó la cuenta de " . $banco . " correctamente !!!", "alert alert-info");
        }
    }
}

header("Location: CuentasBancarias.php?estado='Activo'");
