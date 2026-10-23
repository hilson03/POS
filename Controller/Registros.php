<?php
require("../Model/Conexion.php");
require("Constans.php");
require_once('Sesion.php');
require_once("SubirFoto.php");
require_once("Validar.php");

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');
$usuario = $usuarioSesion['login'];

$con = new conexion();

// la sucursal enviada en el formulario si existe y esta activa; si no, la Sucursal Principal (1)
function sucursalValida($con, $idSucursal)
{
    $sucursal = $con->getSucursal($idSucursal);
    return ($sucursal !== null && $sucursal['estado'] == 'Activo') ? (int) $idSucursal : 1;
}

// tipos de usuario que existen en el sistema
$tiposUsuario = array('ADMINISTRADOR', 'VENTAS');

// motivo por el que los datos del usuario no son validos, o vacio si estan bien ($password vacio = no se cambia)
function validarDatosUsuario($login, $nombre, $tipo, $password, $passwordObligatorio, $tiposUsuario)
{
    if ($nombre == '') {
        return "el nombre es obligatorio.";
    }
    if (!loginValido($login)) {
        return "el login debe tener de 3 a 50 letras o numeros, sin espacios (se permiten . - _).";
    }
    if (!in_array($tipo, $tiposUsuario, true)) {
        return "elige el tipo de usuario: ADMINISTRADOR o VENTAS.";
    }
    if (($passwordObligatorio || $password !== '') && !passwordValido($password)) {
        return "la contrasena debe tener al menos 6 caracteres.";
    }
    return "";
}

$allUsuarios = $con->getAllUserData();
$menuMain = $con->getMenuMain();


if(isset($_POST['nuevo_usuario'])){

    $usuario = textoPost('login', 50);
    $tipo = textoPost('tipo', 50);
    $nombre = textoPost('nombre', 50);
    // la contrasena no se recorta ni se limpia: se guarda cifrada tal como la escribieron
    $password = (isset($_POST['password']) && is_string($_POST['password'])) ? $_POST['password'] : '';
    $sucursalElegida = $con->getSucursal(enteroPost('idSucursal'));
    $errorDatos = validarDatosUsuario($usuario, $nombre, $tipo, $password, true, $tiposUsuario);

    if ($errorDatos != '') {
        $updateMensaje = $con->updateMensajeAlert("No se registro el usuario: " . $errorDatos, "alert alert-danger");
    } elseif ($con->loginEnUso($usuario)) {
        // el login identifica a una sola persona (tambien a los usuarios desactivados, que siguen en el historial)
        $updateMensaje = $con->updateMensajeAlert("No se registro el usuario: el login \"" . $usuario . "\" ya lo usa otro usuario (puede estar desactivado). Elige otro.", "alert alert-danger");
    } elseif ($sucursalElegida === null || $sucursalElegida['estado'] != 'Activo') {
        // sin sucursal el usuario no tendria donde trabajar: no se registra
        $updateMensaje = $con->updateMensajeAlert("No se registro el usuario: elige la sucursal donde va a trabajar.", "alert alert-danger");
    } else {
        $imagenUsuario = subirFoto('userfile', "img/user.png", $errorFoto);

        $mensaje = "Se Añadio un nuevo Usuario en la sucursal " . $sucursalElegida['nombre'];
        $alerta = "alert alert-success";
        if ($errorFoto != "") {
            $mensaje = "Se añadio el usuario, pero la foto no se guardo: " . $errorFoto;
            $alerta = "alert alert-warning";
        }

        $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

        $registerNewUser = $con->getRegisterNewUser($nombre, $tipo, $usuario, $password, $imagenUsuario, $sucursalElegida['idSucursal']);
    }

}

//esta parte permite borrar sin mas la informacion de un usuario

if (isset($_GET['idborrar'])) {

        $idUsuario = (int) $_GET['idborrar'];
        $usuarioBorrar = $con->getOnlyUserData($idUsuario);

        if (empty($usuarioBorrar)) {
            $updateMensaje = $con->updateMensajeAlert("Ese usuario ya no existe.", "alert alert-warning");
        } elseif ($idUsuario === (int) $usuarioSesion['id_usu']) {
            $updateMensaje = $con->updateMensajeAlert("No puedes eliminar tu propio usuario mientras estas conectado.", "alert alert-danger");
        } elseif ($usuarioBorrar[0]['tipo'] == 'ADMINISTRADOR' && $con->contarAdministradores() <= 1) {
            // sin ningun administrador nadie podria entrar a Configuracion ni aprobar traslados
            $updateMensaje = $con->updateMensajeAlert("No se puede eliminar al ultimo administrador del sistema.", "alert alert-danger");
        } elseif ($con->usuarioTieneHistorial($idUsuario)) {
            // con ventas o traslados no se borra: se desactiva, para no perder quien hizo cada movimiento
            $updateMensaje = $con->updateMensajeAlert("No se puede eliminar a " . $usuarioBorrar[0]['nombre'] . " porque tiene ventas o traslados registrados. Desactívalo para que ya no pueda entrar.", "alert alert-warning");
        } else {
            $deleteUser = $con->deleteUsuario($idUsuario);
            $updateMensaje = $con->updateMensajeAlert("Se elimino al usuario " . $usuarioBorrar[0]['nombre'] . ".", "alert alert-danger");
        }



    }

/*
 * Desactivar / activar: el usuario inactivo no puede entrar al sistema, pero sigue apareciendo en las ventas,
 * Consolidar y traslados que hizo. No se puede desactivar uno mismo ni al ultimo administrador activo.
 */
if (isset($_GET['iddesactivar']) || isset($_GET['idactivar'])) {
    $desactivar = isset($_GET['iddesactivar']);
    $idUsuario = (int) ($desactivar ? $_GET['iddesactivar'] : $_GET['idactivar']);
    $usuarioCambiar = $con->getOnlyUserData($idUsuario);

    if (empty($usuarioCambiar)) {
        $con->updateMensajeAlert("Ese usuario ya no existe.", "alert alert-warning");
    } elseif ($desactivar && $idUsuario === (int) $usuarioSesion['id_usu']) {
        $con->updateMensajeAlert("No puedes desactivar tu propio usuario mientras estas conectado.", "alert alert-danger");
    } elseif ($desactivar && $usuarioCambiar[0]['tipo'] == 'ADMINISTRADOR' && $usuarioCambiar[0]['estado'] == 'Activo' && $con->contarAdministradores() <= 1) {
        $con->updateMensajeAlert("No se puede desactivar al ultimo administrador activo del sistema.", "alert alert-danger");
    } else {
        $con->setEstadoUsuario($idUsuario, $desactivar ? 'Inactivo' : 'Activo');
        $con->updateMensajeAlert($desactivar
            ? "Se desactivo a " . $usuarioCambiar[0]['nombre'] . ": ya no puede entrar al sistema, pero sus ventas y traslados siguen registrados con su nombre."
            : "Se activo a " . $usuarioCambiar[0]['nombre'] . ": ya puede volver a entrar.", $desactivar ? "alert alert-warning" : "alert alert-success");
    }
}

//aqui viene a dar la informacion cuando se actualizan los datos de usuario 

if (isset($_POST['update_usuario'])) {

        $idUsuarioData = enteroPost('idUsuario');
        $login = textoPost('login', 50);
        $tipo = textoPost('tipo', 50);
        $nombre = textoPost('nombre', 50);
        $password = (isset($_POST['password']) && is_string($_POST['password'])) ? $_POST['password'] : '';

        // la foto actual se lee de la base (no del formulario, que se puede alterar)
        $usuarioActual = $con->getOnlyUserData($idUsuarioData);
        $imagen = empty($usuarioActual) ? 'img/user.png' : $usuarioActual[0]['foto'];
        $errorDatos = empty($usuarioActual) ? "ese usuario ya no existe." : validarDatosUsuario($login, $nombre, $tipo, $password, false, $tiposUsuario);

        if ($errorDatos != '') {
            $con->updateMensajeAlert("No se guardaron los cambios: " . $errorDatos, "alert alert-danger");
            header("Location: Usuario.php?estado='Activo'");
            exit;
        }

        if ($con->loginEnUso($login, (int) $idUsuarioData)) {
            $con->updateMensajeAlert("No se guardaron los cambios: el login \"" . $login . "\" ya lo usa otro usuario (puede estar desactivado).", "alert alert-danger");
            header("Location: Usuario.php?estado='Activo'");
            exit;
        }

        // si se le quita el tipo administrador al ultimo administrador activo, nadie podria entrar a Configuracion
        $usuarioEditar = $con->getOnlyUserData((int) $idUsuarioData);
        if (!empty($usuarioEditar) && $usuarioEditar[0]['tipo'] == 'ADMINISTRADOR' && $tipo != 'ADMINISTRADOR'
            && $usuarioEditar[0]['estado'] == 'Activo' && $con->contarAdministradores() <= 1) {
            $con->updateMensajeAlert("No se puede quitar el tipo administrador al ultimo administrador activo del sistema.", "alert alert-danger");
            header("Location: Usuario.php?estado='Activo'");
            exit;
        }

        $imagenUsuario = subirFoto('userfileEdit', $imagen, $errorFoto);

        $mensaje = "Se Edito los datos de  un usuario";
        $alerta = "alert alert-info";
        if ($errorFoto != "") {
            $mensaje = "Se editaron los datos del usuario, pero la foto no se guardo: " . $errorFoto;
            $alerta = "alert alert-warning";
        }

        $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

        $updateUser = $con->updateUsuario($login, $tipo, $nombre, $password, $imagenUsuario, $idUsuarioData, sucursalValida($con, enteroPost('idSucursal')));

        


    }


header("Location: Usuario.php?estado='Activo'");

