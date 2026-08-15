<?php
require('../Model/Conexion.php');
require('Constans.php');
$con = new Conexion();

if(isset($_GET['productos'])){
    $allProducto =$con->getAllProducto();
    require('../Views/ReporteProductosPdf.php');
}

if(isset($_GET['inventario'])){
    $allactivo =$con->getAllActivos();
    require('../Views/ReporteInventarioPdf.php');
}




?>