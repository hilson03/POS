<?php
require_once('../Model/Conexion.php');
require('Constans.php');

if (!isset($_SESSION)) {
    session_start();
}

$usuario = $_GET['usuarioLogin'];
$password = $_GET['passwordLogin'];
$con = new Conexion();


if (isset($_GET['reporte_dia'])) {
    $fechaVentas = $_GET['fechaVentas'];
    $fechaVentasInicial = $fechaVentas . ' ' . '00:01:00';

    date_default_timezone_set("America/Caracas");
    $tiempo = getdate(time());
    $fecha = date_create($fechaVentas);
    date_add($fecha, date_interval_create_from_date_string('1 days'));
    $fechaVentasU = date_format($fecha, 'Y-m-d');

    $fechaVentasFinal = $fechaVentasU . ' ' . '23:59:00';

    $ventasByDia = $con->getVentasDia($fechaVentasInicial, $fechaVentasFinal);
    $ventasTotalByDia = $con->getVentasTotalesDia($fechaVentasInicial, $fechaVentasFinal);


    require('../Views/ReporteVentasPorDia.php');
}





if (isset($_GET['rango_fecha'])) {

    $fechaVentas = $_GET['fechaInicialVentas'];
    $fechaVentasInicial = $fechaVentas . ' ' . '00:01:00';

    $fechaFinalVentas = $_GET['fechaFinalVentas'];

    date_default_timezone_set("America/Caracas");
    $tiempo = getdate(time());
    $fecha = date_create($fechaFinalVentas);
    date_add($fecha, date_interval_create_from_date_string('1 days'));
    $fechaVentasU = date_format($fecha, 'Y-m-d');

    $fechaVentasFinal = $fechaVentasU . ' ' . '23:59:00';

    $ventasByDia = $con->getVentasDia($fechaVentasInicial, $fechaVentasFinal);
    $ventasTotalByDia = $con->getVentasTotalesDia($fechaVentasInicial, $fechaVentasFinal);


    require('../Views/ReporteVentasPorDia.php');

}


if (isset($_GET['reporte_producto'])) {

    $fechaVentas = $_GET['fechaInicialVentas'];
    $fechaVentasInicial = $fechaVentas . ' ' . '00:01:00';

    $fechaFinalVentas = $_GET['fechaFinalVentas'];

    date_default_timezone_set("America/Caracas");
    $tiempo = getdate(time());
    $fecha = date_create($fechaFinalVentas);
    date_add($fecha, date_interval_create_from_date_string('1 days'));
    $fechaVentasU = date_format($fecha, 'Y-m-d');

    $fechaVentasFinal = $fechaVentasU . ' ' . '23:59:00';


    $ventasProductoByDia = $con->getVentasProductoByDia($fechaVentasInicial, $fechaVentasFinal);
    $ventasTotalProductoByDia = $con->getVentasProductoTotalesDia($fechaVentasInicial, $fechaVentasFinal);

    require('../Views/ReporteProductosVentasPorDia.php');
}


if (isset($_GET['reporte_mes'])) {

    $anio = $_GET['anio'];
    $mes = $_GET['mes'];

   $ventasMensuales = $con->getVentasMensuales();
   $sumVentasByMes = $con->getSumaTotalVentasByMes($mes,$anio);
   $totalVentasMensual = $con->getTotalVentasByMes($mes,$anio);

   require('../Views/ReporteVentasPorMes.php');
}



if (isset($_GET['reporte_anual'])) {

    $anio = $_GET['anio'];

    $ventasMensuales = $con->getVentasMensuales();
    $sumVentasByMes = $con->getTotalVentasByYear($anio);
    $totalVentasMensual = $con->getTotalVentasByAnio($anio);

    require('../Views/ReporteVentasPorAnio.php');

}


if (isset($_GET['reporte_6meses'])) {


    $ventasMensuales = $con->getVentasMensuales();
    $VentasByMes = $con->getTotalVentas6Meses();
    $totalVentasMensual = $con->getGrandTotalVentas6Meses();

    require('../Views/ReporteVentasPor6Meses.php');

}


