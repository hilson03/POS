<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

$con = new conexion();

// el formulario de login envia los datos por POST: se validan, se guarda la sesion y se recarga sin datos en la URL
if (isset($_POST['usuario'], $_POST['password']) && is_string($_POST['usuario']) && is_string($_POST['password'])) {
    $loginEnviado = mb_substr(trim($_POST['usuario']), 0, 50, 'UTF-8');
    $ipCliente = isset($_SERVER['REMOTE_ADDR']) ? substr($_SERVER['REMOTE_ADDR'], 0, 45) : '';

    // demasiados intentos fallidos: ni siquiera se revisa la contrasena hasta que pase el bloqueo
    $minutosBloqueo = $con->minutosBloqueoLogin($loginEnviado, $ipCliente);
    if ($minutosBloqueo > 0) {
        header("Location: ../index.php?error=bloqueado&min=" . $minutosBloqueo);
        exit;
    }

    $searchUser = $con->getUser($loginEnviado, $_POST['password']);

    if (empty($searchUser)) {
        $con->registrarIntentoFallido($loginEnviado, $ipCliente);
        $minutosBloqueo = $con->minutosBloqueoLogin($loginEnviado, $ipCliente);
        if ($minutosBloqueo > 0) {
            header("Location: ../index.php?error=bloqueado&min=" . $minutosBloqueo);
            exit;
        }
        $quedan = $con->intentosLoginRestantes($loginEnviado);
        header("Location: ../index.php?error=login" . ($quedan <= 2 ? "&quedan=" . $quedan : ""));
        exit;
    }
    $con->limpiarIntentosLogin($loginEnviado);
    // usuario desactivado: sus datos siguen en el historial, pero ya no puede entrar
    if (isset($searchUser[0]['estado']) && $searchUser[0]['estado'] == 'Inactivo') {
        header("Location: ../index.php?error=inactivo");
        exit;
    }

    guardarSesion($searchUser[0]['id_usu'], $searchUser[0]['idSucursal']);
    header("Location: AccessUsers.php");
    exit;
}

$usuarioSesion = requerirSesion();

$tipo = $usuarioSesion['tipo'];
$id_usuario = $usuarioSesion['id_usu'];
$nombres = $usuarioSesion['nombre'];
$foto = $usuarioSesion['foto'];
$usuario = $usuarioSesion['login'];

// los vendedores tambien ven Principal, con los indicadores de lo que les corresponde
$esAdministrador = $tipo == 'ADMINISTRADOR';

$urlViews = URL_VIEWS;

$imageUser = $foto;

$userLogueado = $nombres;


$menuMain = $con->getMenuMain();

$tipoDeMoneda = $con->getTipoMoneda();
foreach ($tipoDeMoneda as $moneda){
    $tipoMonedaElegida = $moneda['tipoMoneda'];
}

// misma zona horaria con la que RegistrarVenta guarda la fecha de la venta
$ventasDelDia = $con->getVentasDelDia(date('Y-m-d'), $usuarioSesion['idSucursalActiva']);

// indicadores de la sucursal activa para los cuadros de arriba
$indicadorVendidoHoy = $con->getTotalVendidoDia(date('Y-m-d'), $usuarioSesion['idSucursalActiva']);
$indicadorPorConsolidar = mysqli_num_rows($con->getAllVentas($usuarioSesion['idSucursalActiva'], $esAdministrador ? 0 : $id_usuario));
$indicadorSinStock = $con->contarProductosSinStock($usuarioSesion['idSucursalActiva']);
$indicadorTraslados = $con->contarTrasladosPorAtender($usuarioSesion['idSucursalActiva'], true);   // todos gestionan traslados

require('../Views/Wellcome.php');
