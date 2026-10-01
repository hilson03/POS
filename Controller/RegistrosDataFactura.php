1<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');


iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');
$usuario = $usuarioSesion['login'];


$con = new conexion();

$allUsuarios = $con->getAllUserData();
$menuMain = $con->getMenuMain();

if (isset($_POST['update_data_factura'])) {

    $iddatos = $_POST['iddatos'];
    $iddatos = $_POST['iddatos'];
    $propietario = $_POST['propietario'];
    $razon = $_POST['razon'];
    $direccion = $_POST['direccion'];
    $nro = $_POST['nro'];
    $telefono = $_POST['telefono'];

    $mensaje = "Se Actualizo  los datos de la factura correctamente !!!";
    $alerta = "alert alert-info";

    $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

    $updateDatosFactura = $con->updateDataFactura($iddatos, $propietario, $razon, $direccion, $nro, $telefono);



}


    header("Location: DatosFactura.php?estado='Activo'");



?>