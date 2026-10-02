<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion();
$usuario = $usuarioSesion['login'];
$con = new Conexion();

/*
 * Sucursal del reporte: un vendedor solo puede ver la suya; un administrador elige una o todas (0).
 */
if ($usuarioSesion['tipo'] == 'ADMINISTRADOR') {
    $idSucursalReporte = isset($_GET['idSucursal']) ? (int) $_GET['idSucursal'] : (int) $usuarioSesion['idSucursalActiva'];
} else {
    $idSucursalReporte = (int) $usuarioSesion['idSucursalActiva'];
}

if ($idSucursalReporte === 0) {
    $nombreSucursalReporte = 'Todas las sucursales';
} else {
    $sucursalReporte = $con->getSucursal($idSucursalReporte);
    if ($sucursalReporte === null) {
        $idSucursalReporte = (int) $usuarioSesion['idSucursalActiva'];
        $sucursalReporte = $con->getSucursal($idSucursalReporte);
    }
    $nombreSucursalReporte = $sucursalReporte['nombre'];
}

// desglose por sucursal (solo cuando el reporte es de todas)
$desgloseSucursales = array();

// devuelve 'Y-m-d' si la fecha recibida es valida; si no, la fecha de hoy
function fechaValida($fecha)
{
    $d = DateTime::createFromFormat('Y-m-d', (string) $fecha);
    return ($d && $d->format('Y-m-d') === $fecha) ? $fecha : date('Y-m-d');
}

// rango [desde, hasta) que cubre completos los dias desde $fechaInicio hasta $fechaFin
function rangoDias($fechaInicio, $fechaFin)
{
    $desde = fechaValida($fechaInicio) . ' 00:00:00';
    $hasta = date('Y-m-d', strtotime(fechaValida($fechaFin) . ' +1 day')) . ' 00:00:00';
    return array($desde, $hasta);
}

function fechaLegible($fecha)
{
    return date('d/m/Y', strtotime($fecha));
}

$mesesNombre = array(1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');


if (isset($_GET['reporte_dia'])) {
    // antes tomaba desde las 00:01 hasta las 23:59 del dia SIGUIENTE (mezclaba dos dias)
    $fechaVentas = fechaValida($_GET['fechaVentas']);
    list($fechaVentasInicial, $fechaVentasFinal) = rangoDias($fechaVentas, $fechaVentas);
    $periodoReporte = 'Dia ' . fechaLegible($fechaVentas);

    $ventasByDia = $con->getVentasDia($fechaVentasInicial, $fechaVentasFinal, $idSucursalReporte);
    $ventasTotalByDia = $con->getVentasTotalesDia($fechaVentasInicial, $fechaVentasFinal, $idSucursalReporte);
    if ($idSucursalReporte === 0) {
        $desgloseSucursales = $con->getVentasPorSucursal($fechaVentasInicial, $fechaVentasFinal);
    }

    require('../Views/ReporteVentasPorDia.php');
}


if (isset($_GET['rango_fecha'])) {

    $fechaInicio = fechaValida($_GET['fechaInicialVentas']);
    $fechaFin = fechaValida($_GET['fechaFinalVentas']);
    list($fechaVentasInicial, $fechaVentasFinal) = rangoDias($fechaInicio, $fechaFin);
    $periodoReporte = 'Del ' . fechaLegible($fechaInicio) . ' al ' . fechaLegible($fechaFin);

    $ventasByDia = $con->getVentasDia($fechaVentasInicial, $fechaVentasFinal, $idSucursalReporte);
    $ventasTotalByDia = $con->getVentasTotalesDia($fechaVentasInicial, $fechaVentasFinal, $idSucursalReporte);
    if ($idSucursalReporte === 0) {
        $desgloseSucursales = $con->getVentasPorSucursal($fechaVentasInicial, $fechaVentasFinal);
    }

    require('../Views/ReporteVentasPorDia.php');

}


if (isset($_GET['reporte_producto'])) {

    $fechaInicio = fechaValida($_GET['fechaInicialVentas']);
    $fechaFin = fechaValida($_GET['fechaFinalVentas']);
    list($fechaVentasInicial, $fechaVentasFinal) = rangoDias($fechaInicio, $fechaFin);
    $periodoReporte = 'Del ' . fechaLegible($fechaInicio) . ' al ' . fechaLegible($fechaFin);

    $ventasProductoByDia = $con->getVentasProductoByDia($fechaVentasInicial, $fechaVentasFinal, $idSucursalReporte);
    $ventasTotalProductoByDia = $con->getVentasProductoTotalesDia($fechaVentasInicial, $fechaVentasFinal, $idSucursalReporte);
    if ($idSucursalReporte === 0) {
        $desgloseSucursales = $con->getVentasPorSucursal($fechaVentasInicial, $fechaVentasFinal, true);
    }

    require('../Views/ReporteProductosVentasPorDia.php');
}


if (isset($_GET['reporte_mes'])) {

    $anio = (int) $_GET['anio'];
    $mes = max(1, min(12, (int) $_GET['mes']));
    $periodoReporte = $mesesNombre[$mes] . ' ' . $anio;

   $ventasMensuales = $con->getVentasMensuales();
   $sumVentasByMes = $con->getSumaTotalVentasByMes($mes, $anio, $idSucursalReporte);
   $totalVentasMensual = $con->getTotalVentasByMes($mes, $anio, $idSucursalReporte);
    if ($idSucursalReporte === 0) {
        $inicioMes = sprintf('%04d-%02d-01', $anio, $mes);
        $desgloseSucursales = $con->getVentasPorSucursal($inicioMes . ' 00:00:00', date('Y-m-d', strtotime($inicioMes . ' +1 month')) . ' 00:00:00');
    }

   require('../Views/ReporteVentasPorMes.php');
}



if (isset($_GET['reporte_anual'])) {

    $anio = (int) $_GET['anio'];
    $periodoReporte = 'Ano ' . $anio;

    $ventasMensuales = $con->getVentasMensuales();
    $sumVentasByMes = $con->getTotalVentasByYear($anio, $idSucursalReporte);
    $totalVentasMensual = $con->getTotalVentasByAnio($anio, $idSucursalReporte);
    if ($idSucursalReporte === 0) {
        $desgloseSucursales = $con->getVentasPorSucursal($anio . '-01-01 00:00:00', ($anio + 1) . '-01-01 00:00:00');
    }

    require('../Views/ReporteVentasPorAnio.php');

}


if (isset($_GET['reporte_6meses'])) {

    $periodoReporte = 'Ultimos 6 meses (hasta ' . date('d/m/Y') . ')';

    $ventasMensuales = $con->getVentasMensuales();
    $VentasByMes = $con->getTotalVentas6Meses($idSucursalReporte);
    $totalVentasMensual = $con->getGrandTotalVentas6Meses($idSucursalReporte);
    if ($idSucursalReporte === 0) {
        $desgloseSucursales = $con->getVentasPorSucursal(date('Y-m-d H:i:s', strtotime('-6 months')), date('Y-m-d H:i:s', strtotime('+1 second')));
    }

    require('../Views/ReporteVentasPor6Meses.php');

}
