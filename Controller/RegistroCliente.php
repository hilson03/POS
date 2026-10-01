<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('SubirFoto.php');

if (!isset($_SESSION)) {
    session_start();
}

$usuarioLogin = $_POST['usuarioLogin'];
$passwordLogin = $_POST['passwordLogin'];

$con = new conexion();

if (isset($_POST['nuevo_cliente'])) {


    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $direccion = $_POST['direccion'];
    $telefonoFijo = $_POST['telefonoFijo'];
    $telefonoCelular = $_POST['telefonoCelular'];
    $email = $_POST['email'];
    $fechaRegistro = $_POST['fechaRegistro'];
    $ci = $_POST['ci'];


    $destino = subirFoto('userfile', "img/user.png", $errorFoto);


    $mensaje = "Se registro un nuevo cliente  correctamente !!!";
    $alerta = "alert alert-success";
    if ($errorFoto != "") {
        $mensaje = "Se registro el cliente, pero la foto no se guardo: " . $errorFoto;
        $alerta = "alert alert-warning";
    }

    $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

    $registrarNewProveedor = $con->registerNewCliente($destino,$nombre,$apellido,$direccion,$telefonoFijo,$telefonoCelular,$email,$fechaRegistro,$ci);


}

if (isset($_GET['idborrar'])) {
    $usuarioLogin = $_GET['usuarioLogin'];
    $passwordLogin = $_GET['passwordLogin'];
    $idborrar = $_GET['idborrar'];

    $mensaje = "Se elimino  los datos del cliente correctamente !!!";
    $alerta = "alert alert-danger";
    $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

    $deleteClient = $con->deleteClient($idborrar);


}


if (isset($_POST['update_cliente'])) {

        $idcliente = $_POST['idcliente'];
        $imagen = $_POST['imagen'];
        $usuarioLogin = $_POST['usuarioLogin'];
        $passwordLogin = $_POST['passwordLogin'];
    
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $direccion = $_POST['direccion'];
        $telefonoFijo = $_POST['telefonoFijo'];
        $telefonoCelular = $_POST['telefonoCelular'];
        $email = $_POST['email'];
        $fechaRegistro = date('Y-m-d');
        $ci = $_POST['ci'];

        $destino = subirFoto('userfileEdit', $imagen, $errorFoto);

        $mensaje = "Se Actualizo  los datos del cliente correctamente !!!";
        $alerta = "alert alert-info";
        if ($errorFoto != "") {
            $mensaje = "Se actualizo el cliente, pero la foto no se guardo: " . $errorFoto;
            $alerta = "alert alert-warning";
        }
    
        $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);
    
        $registrarNewCliente= $con->updateClient($idcliente,$destino,$nombre,$apellido,$direccion,$telefonoFijo,$telefonoCelular,$email,$fechaRegistro,$ci);


    }

    $menuMain = $con->getMenuMain();

header("Location: Cliente.php?usuario=$usuarioLogin&password=$passwordLogin&estado='Activo'");

?>
