<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion();
exigirCsrf('Traslados.php');   // solo acciones enviadas desde el sistema (proteccion contra enlaces trampa)

$con = new conexion();

// desde 2026-10-19 los vendedores gestionan traslados igual que el administrador (pedir, aprobar, rechazar, enviar, recibir
// y cancelar), siempre como usuarios de su sucursal: aprueba y envia la tienda que tiene el producto, recibe la que lo pidio
$gestionaTraslados = true;
$idSucursalActiva = (int) $usuarioSesion['idSucursalActiva'];
$idUsuario = (int) $usuarioSesion['id_usu'];
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

function avisar($con, $mensaje, $alerta)
{
    $con->updateMensajeAlert($mensaje, $alerta);
}

if ($accion == 'crear') {
    // se piden productos para la sucursal en la que se esta trabajando
    $idProducto = (int) $_POST['idProducto'];
    $idOrigen = (int) $_POST['idSucursalOrigen'];
    $cantidad = (int) $_POST['cantidad'];
    $motivo = trim($_POST['motivo']);
    $origen = $con->getSucursal($idOrigen);
    $producto = $con->getProductoElegido($idProducto, $idOrigen);

    if (!$gestionaTraslados) {
        avisar($con, "No tienes permiso para pedir productos a otra tienda.", "alert alert-danger");
    } elseif ($origen === null || $origen['estado'] != 'Activo' || $idOrigen == $idSucursalActiva) {
        avisar($con, "Elige una tienda distinta a la tuya para pedir el producto.", "alert alert-danger");
    } elseif (empty($producto)) {
        avisar($con, "El producto no existe.", "alert alert-danger");
    } elseif ($cantidad < 1) {
        avisar($con, "La cantidad debe ser al menos 1.", "alert alert-danger");
    } elseif ($cantidad > (int) $producto[0]['cantidad']) {
        avisar($con, $origen['nombre'] . " solo tiene " . (int) $producto[0]['cantidad'] . " unidades de " . $producto[0]['nombreProducto'] . ".", "alert alert-danger");
    } else {
        $id = $con->crearTraslado($idProducto, $cantidad, $idOrigen, $idSucursalActiva, $motivo, $idUsuario);
        avisar($con, "Solicitud #$id enviada a " . $origen['nombre'] . ": $cantidad x " . $producto[0]['nombreProducto'] . ". Queda pendiente de aprobacion.", "alert alert-success");
    }
} elseif (in_array($accion, array('aprobar', 'rechazar', 'enviar', 'recibir', 'cancelar'))) {
    $traslado = $con->getTraslado(isset($_POST['idTraslado']) ? $_POST['idTraslado'] : 0);

    if ($traslado === null) {
        avisar($con, "Ese traslado no existe.", "alert alert-danger");
    } else {
        $id = $traslado['idTraslado'];
        $esOrigen = (int) $traslado['idSucursalOrigen'] === $idSucursalActiva;
        $esDestino = (int) $traslado['idSucursalDestino'] === $idSucursalActiva;
        $detalle = $traslado['cantidad'] . " x " . $traslado['nombreProducto'];

        if ($accion == 'aprobar' || $accion == 'rechazar') {
            // aprueba o rechaza un usuario de la tienda que tiene el producto
            if (!$gestionaTraslados || !$esOrigen) {
                avisar($con, "Solo la tienda " . $traslado['sucursalOrigen'] . " puede aprobar o rechazar esta solicitud.", "alert alert-danger");
            } elseif ($accion == 'aprobar') {
                $hecho = $con->aprobarTraslado($id, $idUsuario);
                avisar($con, $hecho ? "Solicitud #$id aprobada ($detalle). Ya se puede enviar." : "La solicitud #$id ya no esta pendiente.",
                    $hecho ? "alert alert-success" : "alert alert-warning");
            } else {
                $hecho = $con->rechazarTraslado($id, $idUsuario, trim($_POST['motivoRechazo']));
                avisar($con, $hecho ? "Solicitud #$id rechazada." : "La solicitud #$id ya no esta pendiente.",
                    $hecho ? "alert alert-info" : "alert alert-warning");
            }
        } elseif ($accion == 'enviar') {
            // envia cualquier usuario de la tienda que tiene el producto
            if (!$esOrigen) {
                avisar($con, "Solo la tienda " . $traslado['sucursalOrigen'] . " puede enviar este traslado.", "alert alert-danger");
            } else {
                $error = $con->enviarTraslado($id, $idUsuario);
                avisar($con, $error == '' ? "Traslado #$id enviado a " . $traslado['sucursalDestino'] . " ($detalle). Se desconto de tu stock." : $error,
                    $error == '' ? "alert alert-success" : "alert alert-danger");
            }
        } elseif ($accion == 'recibir') {
            // confirma la llegada cualquier usuario de la tienda que lo pidio
            if (!$esDestino) {
                avisar($con, "Solo la tienda " . $traslado['sucursalDestino'] . " puede confirmar que recibio este traslado.", "alert alert-danger");
            } else {
                $error = $con->recibirTraslado($id, $idUsuario);
                avisar($con, $error == '' ? "Traslado #$id recibido ($detalle). Se sumo a tu stock." : $error,
                    $error == '' ? "alert alert-success" : "alert alert-danger");
            }
        } elseif ($accion == 'cancelar') {
            // la tienda que pidio puede anular su solicitud mientras no se haya enviado
            if (!$gestionaTraslados || !$esDestino) {
                avisar($con, "Solo la tienda " . $traslado['sucursalDestino'] . " puede cancelar esta solicitud.", "alert alert-danger");
            } else {
                $hecho = $con->cancelarTraslado($id);
                avisar($con, $hecho ? "Solicitud #$id cancelada." : "La solicitud #$id ya fue enviada o cerrada y no se puede cancelar.",
                    $hecho ? "alert alert-info" : "alert alert-warning");
            }
        }
    }
}

header("Location: Traslados.php?estado='Activo'");
