<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');

$con = new conexion();

$imagenes = imagenesMarca();
$clave = isset($_POST['clave']) ? $_POST['clave'] : '';

if (!isset($imagenes[$clave])) {
    // tambien pasa si la imagen supera el limite del servidor (post_max_size): PHP descarta todo el formulario
    $con->updateMensajeAlert("No se guardó el cambio. Si la imagen es muy pesada, prueba con una de menos de 2 MB.", "alert alert-danger");
    header("Location: Apariencia.php?estado=1");
    exit;
}

$titulo = $imagenes[$clave]['titulo'];
$anterior = valoresMarca();
$anterior = isset($anterior[$clave]) ? $anterior[$clave] : '';

if (isset($_POST['restaurar'])) {
    $con->setApariencia($clave, '');
    borrarImagenMarca($anterior);
    $con->updateMensajeAlert("Se volvió a la imagen original: " . $titulo . ".", "alert alert-info");

    // si el fondo venia del logo, tambien vuelve al original junto con el logo
    if ($clave == 'logo_login' && esFondoAutomatico()) {
        borrarImagenMarca(valoresMarca()['fondo_login']);
        $con->setApariencia('fondo_login', '');
    }
} else {
    $ruta = subirImagenMarca('imagen', $clave, $error);
    if ($ruta === '') {
        $con->updateMensajeAlert("No se cambió " . $titulo . ": " . $error, "alert alert-danger");
    } else {
        $con->setApariencia($clave, $ruta);
        borrarImagenMarca($anterior);
        $texto = "Se cambió " . $titulo . " correctamente.";

        // logo nuevo: el fondo tenue se arma solo, salvo que el administrador haya subido un fondo propio
        if ($clave == 'logo_login' && (!esImagenPersonalizada('fondo_login') || esFondoAutomatico())) {
            $fondo = generarFondoDesdeLogo($ruta);
            if ($fondo !== '') {
                if (esImagenPersonalizada('fondo_login')) {
                    borrarImagenMarca(valoresMarca()['fondo_login']);
                }
                $con->setApariencia('fondo_login', $fondo);
                $texto .= " También se actualizó la imagen de fondo del login.";
            }
        }
        // se guarda igual, pero si la medida no es la recomendada se avisa como advertencia
        $consejo = consejoMedidasMarca($clave, $ruta);
        $con->updateMensajeAlert($consejo === '' ? $texto : $texto . ' Ojo: ' . $consejo,
            $consejo === '' ? "alert alert-success" : "alert alert-warning");
    }
}

header("Location: Apariencia.php?estado=1");
