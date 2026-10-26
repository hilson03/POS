<?php
/*
 * Buscador de clientes del formulario de cobro: recibe lo que escribe el vendedor (DNI o nombre, 3 letras o mas)
 * y devuelve en JSON los clientes que coinciden. Los textos se arman en el navegador con textContent (sin html).
 */
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
require_once('Validar.php');
$con = new conexion();

iniciarSesionSegura();
$usuarioSesion = requerirSesion('', true);

$texto = textoPost('texto', 60);
$clientes = array();
if (mb_strlen($texto, 'UTF-8') >= 3) {
    foreach ($con->buscarClientes($texto) as $fila) {
        $clientes[] = array('ci' => trim((string) $fila['ci']), 'nombre' => nombreCliente($fila));
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($clientes);
