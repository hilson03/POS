<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
$usuarioSesion = requerirSesion();
$con = new Conexion();

if(isset($_GET['productos'])){
    $allProducto =$con->getAllProducto($usuarioSesion['idSucursalActiva']);
    require('../Views/ReporteProductosPdf.php');
}


?>