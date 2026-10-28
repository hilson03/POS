<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');
exigirCsrf('DatosNegocio.php');   // solo acciones enviadas desde el sistema (proteccion contra enlaces trampa)

$con = new conexion();

// limpia lo escrito y lo corta al largo de la columna
function textoNegocio($campo, $maximo)
{
    $texto = isset($_POST[$campo]) ? trim(preg_replace('/\s+/u', ' ', (string) $_POST[$campo])) : '';
    return mb_substr($texto, 0, $maximo, 'UTF-8');
}

if (isset($_POST['guardar_negocio'])) {
    $datos = array(
        'razon'       => textoNegocio('razon', 100),
        'propietario' => textoNegocio('propietario', 100),
        'nro'         => textoNegocio('nro', 100),
        'direccion'   => textoNegocio('direccion', 100),
        'ciudad'      => textoNegocio('ciudad', 100),
        'telefono'    => textoNegocio('telefono', 100),
        'correo'      => textoNegocio('correo', 100),
        'mensajePie'  => textoNegocio('mensajePie', 200),
    );

    if ($datos['razon'] == '') {
        $con->updateMensajeAlert("No se guardó: el nombre del negocio es obligatorio.", "alert alert-danger");
    } elseif ($datos['correo'] != '' && !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
        $con->updateMensajeAlert("No se guardó: el correo no es válido.", "alert alert-danger");
    } else {
        $con->guardarDatosNegocio($datos);
        $con->updateMensajeAlert("Se guardaron los datos del negocio.", "alert alert-success");
    }
}

header("Location: DatosNegocio.php?estado=1");
