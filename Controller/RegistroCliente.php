<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
require_once('SubirFoto.php');
require_once('Validar.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');
$usuario = $usuarioSesion['login'];

$con = new conexion();

// datos del formulario de cliente, limpios y cortados al largo de cada columna
function datosClientePost()
{
    return array(
        'nombre' => textoPost('nombre', 200),
        'apellido' => textoPost('apellido', 200),
        'direccion' => textoPost('direccion', 200),
        'telefonoFijo' => textoPost('telefonoFijo', 30),
        'telefonoCelular' => textoPost('telefonoCelular', 30),
        'email' => textoPost('email', 200),
        'ci' => textoPost('ci', 100),
    );
}

// motivo por el que los datos del cliente no son validos, o vacio si estan bien
function validarDatosCliente($con, $cliente, $idCliente = 0)
{
    if ($cliente['nombre'] == '') {
        return "el nombre es obligatorio.";
    }
    if ($cliente['apellido'] == '') {
        return "el apellido es obligatorio.";
    }
    if (!correoValido($cliente['email'])) {
        return "el correo no es valido (ejemplo: nombre@gmail.com).";
    }
    // el DNI es opcional, pero si se escribe no se puede repetir: con el se busca al cliente al cobrar en Ventas
    if ($cliente['ci'] != '') {
        foreach ($con->getClienteDatos($cliente['ci']) as $otro) {
            if ((int) $otro['idcliente'] !== (int) $idCliente) {
                return "ya hay un cliente con el DNI " . $cliente['ci'] . " (" . nombreCliente($otro) . ").";
            }
        }
    }
    return "";
}

if (isset($_POST['nuevo_cliente'])) {
    $cliente = datosClientePost();
    $errorDatos = validarDatosCliente($con, $cliente);
    // la fecha de registro es la del dia (en el formulario es solo de lectura)
    $fechaRegistro = date('Y-m-d');

    if ($errorDatos != "") {
        $con->updateMensajeAlert("No se registro el cliente: " . $errorDatos, "alert alert-danger");
    } else {
        $destino = subirFoto('userfile', "img/user.png", $errorFoto);

        $mensaje = "Se registro un nuevo cliente  correctamente !!!";
        $alerta = "alert alert-success";
        if ($errorFoto != "") {
            $mensaje = "Se registro el cliente, pero la foto no se guardo: " . $errorFoto;
            $alerta = "alert alert-warning";
        }

        try {
            $con->registerNewCliente($destino, $cliente['nombre'], $cliente['apellido'], $cliente['direccion'], $cliente['telefonoFijo'], $cliente['telefonoCelular'], $cliente['email'], $fechaRegistro, $cliente['ci']);
            $con->updateMensajeAlert($mensaje, $alerta);
        } catch (Throwable $e) {
            // el mensaje de exito solo sale si de verdad se guardo
            error_log('No se registro el cliente: ' . $e->getMessage());
            $con->updateMensajeAlert("No se pudo registrar el cliente. Revisa los datos e intenta de nuevo.", "alert alert-danger");
        }
    }
}

if (isset($_GET['idborrar'])) {
    $idborrar = $_GET['idborrar'];

    $mensaje = "Se elimino  los datos del cliente correctamente !!!";
    $alerta = "alert alert-danger";
    $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

    $deleteClient = $con->deleteClient($idborrar);


}


if (isset($_POST['update_cliente'])) {

        $idcliente = enteroPost('idcliente');
        $cliente = datosClientePost();
        // la foto actual se lee de la base (no del formulario, que se puede alterar)
        $clienteActual = $con->getCliente($idcliente);
        // la fecha de registro no cambia al editar: es cuando se registro el cliente
        $fechaRegistro = $clienteActual === null ? date('Y-m-d') : $clienteActual['fechaRegistro'];
        $errorDatos = $clienteActual === null ? "no se encontro el cliente." : validarDatosCliente($con, $cliente, $idcliente);
        if ($errorDatos != "") {
            $con->updateMensajeAlert("No se actualizo el cliente: " . $errorDatos, "alert alert-danger");
            header("Location: Cliente.php?estado='Activo'");
            exit;
        }
        $imagen = $clienteActual['foto'];
        $nombre = $cliente['nombre'];
        $apellido = $cliente['apellido'];
        $direccion = $cliente['direccion'];
        $telefonoFijo = $cliente['telefonoFijo'];
        $telefonoCelular = $cliente['telefonoCelular'];
        $email = $cliente['email'];
        $ci = $cliente['ci'];

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

header("Location: Cliente.php?estado='Activo'");

?>
