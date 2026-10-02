<?php
// administrador: elige una sucursal o todas; vendedor: siempre la suya (el servidor tambien lo valida)
if ($usuarioSesion['tipo'] == 'ADMINISTRADOR') {
    $selectorSucursal = '<div class="form-group" style="margin: 0 15px 10px;"><label class="control-label">Sucursal:</label>'
        . '<select class="form-control input-lg" name="idSucursal"><option value="0">Todas las sucursales</option>';
    foreach ($sucursalesReporte as $sucursalOpcion) {
        $selectorSucursal .= '<option value="' . (int) $sucursalOpcion['idSucursal'] . '"'
            . ($sucursalOpcion['idSucursal'] == $usuarioSesion['idSucursalActiva'] ? ' selected' : '') . '>'
            . htmlspecialchars($sucursalOpcion['nombre']) . '</option>';
    }
    $selectorSucursal .= '</select></div>';
} else {
    $selectorSucursal = '<p style="margin: 0 15px 10px;"><i class="icon_building"></i> Sucursal: <strong>'
        . htmlspecialchars($usuarioSesion['nombreSucursal']) . '</strong></p>';
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
include('Head.php');
?>
<body>
<section id="container" class="">
    <header class="header dark-bg">
        <div class="toggle-nav">
            <div class="icon-reorder tooltips" data-original-title="Toggle Navigation" data-placement="bottom"><i
                        class="icon_menu"></i></div>
        </div>
        <?PHP include("Logo.php") ?>
        <div class="nav search-row" id="top_menu">
            <!--  search form start -->
            <ul class="nav top-menu">
                <li>
                    <form class="navbar-form">
                        <!--                              <input class="form-control" placeholder="Search" type="text">-->
                    </form>
                </li>
            </ul>
            <!--  search form end -->
        </div>
        <?PHP include("DropDown.php"); ?>
    </header>
    <?PHP include("Menu.php") ?>

</section>

<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--overview start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-laptop"></i> PRINCIPAL</h3>
                <div class="<?PHP echo $alerta; ?>" role="alert">
                    <strong><?PHP echo $mensaje; ?></strong>
                </div>

                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="AccessUsers.php">Inicio</a></li>
                    <li><i class="fa fa-truck"></i>Reporte de Ventas</li>

                </ol>
            </div>
        </div>
        <header class="panel-heading">
            <div class="panel-body">
                <div align="center">

                    <button href="#reporteDia" title="" data-toggle="modal" class="btn btn-danger tooltips"
                            type="button"
                            data-original-title="Buscar Reporte por dia" data-placement="left">
                        <span class="icon_datareport"></span> REPORTE DEL DIA
                    </button>

                    <button href="#rangoFecha" title="" data-placement="left" data-toggle="modal"
                            class="btn btn-danger tooltips" type="button"
                            data-original-title="Reporte por rango de fechas"><span
                                class="icon_datareport"></span> RANGO DE FECHAS
                    </button>

                    <button href="#ReportePorProducto" title="" data-placement="left" data-toggle="modal"
                            class="btn btn-danger tooltips" type="button"
                            data-original-title="Buscar Reporte por producto"><span
                                class="icon_datareport"></span> REPORTE POR PRODUCTO
                    </button>

                </div>

                <br>
                <div align="center">
                    <button href="#ReporteByMes" title="" data-placement="left" data-toggle="modal"
                            class="btn btn-danger tooltips" type="button"
                            data-original-title="Buscar Reporte por mes"><span
                                class="icon_datareport"></span> REPORTE POR MES
                    </button>
                </div>

                <br>


                <div align="center">
                    <button href="#ReporteAnual" title="" data-placement="left" data-toggle="modal"
                            class="btn btn-danger tooltips" type="button"
                            data-original-title="Buscar anual"><span
                                class="icon_datareport"></span> REPORTE ANUAL
                    </button>
                    <button href="#6meses" title="" data-placement="left" data-toggle="modal"
                            class="btn btn-danger tooltips" type="button"
                            data-original-title="Reporte por  los ultimos 6 meses"><span
                                class="icon_datareport"></span> REPORTE DE LOS ULTIMOS 6 MESES
                    </button>
                </div>


                <div id="reporteDia" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                     aria-hidden="true">
                    <form class="form-validate form-horizontal" target="_blank" name="form2" action="Reportes.php"
                          method="GET">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 id="myModalLabel" align="center">Mostrar Reporte del Dia</h3>
                                </div>

                                <div class="modal-body">
                                    <!-- antes mostraba la fecha de hoy pero enviaba la de ayer -->
                                    <label class="control-label col-lg-2">Fecha:</label>
                                    <div class="col-lg-10">
                                        <input class="form-control input-lg m-bot15" type="date" name="fechaVentas" required
                                               value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <br><br><br>

                                </div>
                                <?php echo $selectorSucursal; ?>
                                <div class="modal-footer">
                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                                        <strong>Cerrar</strong>
                                    </button>
                                    <button name="reporte_dia" type="submit" data-target="_blank"
                                            class="btn btn-primary">
                                        <strong>Buscar</strong></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div id="rangoFecha" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                     aria-hidden="true">
                    <form class="form-validate form-horizontal" target="_blank" name="form2" action="Reportes.php"
                          method="GET">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 id="myModalLabel" align="center">Mostrar Reporte Por Fecha</h3>
                                </div>

                                <div class="modal-body">
                                    <label for="responsable" class="control-label col-lg-2">Fecha de Inicio:</label>
                                    <div class="col-lg-10">
                                        <input class="form-control input-lg m-bot15" type="date"
                                               name="fechaInicialVentas" autocomplete="off" required
                                               value="<?php echo date('Y-m-d'); ?>"></div>
                                    <br><br><br>
                                    <br>

                                    <label for="responsable" class="control-label col-lg-2">Fecha de Fin:</label>
                                    <div class="col-lg-10">
                                        <input class="form-control input-lg m-bot15" type="date" name="fechaFinalVentas"
                                               autocomplete="off" required value="<?php echo date('Y-m-d'); ?>"></div>
                                    <br><br><br>
                                    <br>

                                </div>
                                <?php echo $selectorSucursal; ?>
                                <div class="modal-footer">
                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                                        <strong>Cerrar</strong>
                                    </button>
                                    <button name="rango_fecha" type="submit" data-target="_blank"
                                            class="btn btn-primary">
                                        <strong>Buscar</strong></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                <div id="ReportePorProducto" class="modal fade" tabindex="-1" role="dialog"
                     aria-labelledby="myModalLabel"
                     aria-hidden="true">
                    <form class="form-validate form-horizontal" target="_blank" name="form2" action="Reportes.php"
                          method="GET">
                        <div class="modal-dialog">


                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 id="myModalLabel" align="center">Mostrar Reporte Por Producto</h3>
                                </div>

                                <div class="modal-body">
                                    <label for="responsable" class="control-label col-lg-2">Fecha de Inicio:</label>
                                    <div class="col-lg-10">
                                        <input class="form-control input-lg m-bot15" type="date"
                                               name="fechaInicialVentas" autocomplete="off" required
                                               value="<?php echo date('Y-m-d'); ?>"></div>
                                    <br><br><br>
                                    <br>

                                    <label for="responsable" class="control-label col-lg-2">Fecha de Fin:</label>
                                    <div class="col-lg-10">
                                        <input class="form-control input-lg m-bot15" type="date" name="fechaFinalVentas"
                                               autocomplete="off" required value="<?php echo date('Y-m-d'); ?>"></div>
                                    <br><br><br>
                                    <br>

                                </div>
                                <?php echo $selectorSucursal; ?>
                                <div class="modal-footer">
                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                                        <strong>Cerrar</strong>
                                    </button>
                                    <button name="reporte_producto" type="submit" data-target="_blank"
                                            class="btn btn-primary">
                                        <strong>Buscar</strong></button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>


                <div id="ReporteByMes" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                     aria-hidden="true">
                    <form class="form-validate form-horizontal" target="_blank" name="form2" action="Reportes.php"
                          method="GET">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 id="myModalLabel" align="center">Buscar Reporte por Mes</h3>
                                </div>

                                <div class="modal-body">
                                    <label class="col-sm-2 control-label"> Buscar Mes/Anio </label>
                                    <div class="col-sm-4">

                                        <select class="form-control input-lg m-bot15"
                                                name="mes">
                                            <option value="01"<?php echo '01' == date('m') ? ' selected' : ''; ?>>ENERO</option>
                                            <option value="02"<?php echo '02' == date('m') ? ' selected' : ''; ?>>FEBRERO</option>
                                            <option value="03"<?php echo '03' == date('m') ? ' selected' : ''; ?>>MARZO</option>
                                            <option value="04"<?php echo '04' == date('m') ? ' selected' : ''; ?>>ABRIL</option>
                                            <option value="05"<?php echo '05' == date('m') ? ' selected' : ''; ?>>MAYO</option>
                                            <option value="06"<?php echo '06' == date('m') ? ' selected' : ''; ?>>JUNIO</option>
                                            <option value="07"<?php echo '07' == date('m') ? ' selected' : ''; ?>>JULIO</option>
                                            <option value="08"<?php echo '08' == date('m') ? ' selected' : ''; ?>>AGOSTO</option>
                                            <option value="09"<?php echo '09' == date('m') ? ' selected' : ''; ?>>SEPTIEMBRE</option>
                                            <option value="10"<?php echo '10' == date('m') ? ' selected' : ''; ?>>OCTUBRE</option>
                                            <option value="11"<?php echo '11' == date('m') ? ' selected' : ''; ?>>NOVIEMBRE</option>
                                            <option value="12"<?php echo '12' == date('m') ? ' selected' : ''; ?>>DICIEMBRE</option>
                                        </select>

                                    </div>

                                    <div class="col-sm-4">

                                        <select class="form-control input-lg m-bot15" name="anio">
                                            <?php for ($a = 2018; $a <= (int) date('Y') + 1; $a++) { ?>
                                                <option value="<?php echo $a; ?>"<?php echo $a == (int) date('Y') ? ' selected' : ''; ?>><?php echo $a; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <br><br><br><br>
                                </div>
                                <?php echo $selectorSucursal; ?>
                                <div class="modal-footer">
                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                                        <strong>Cerrar</strong>
                                    </button>
                                    <button name="reporte_mes" type="submit" class="btn btn-primary">
                                        <strong>Buscar</strong></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                <div id="ReporteAnual" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                     aria-hidden="true">
                    <form class="form-validate form-horizontal" target="_blank" name="form2" action="Reportes.php"
                          method="GET">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 id="myModalLabel" align="center">Buscar Reporte por Anio</h3>
                                </div>

                                <div class="modal-body">
                                    <label class="col-sm-2 control-label"> Buscar Anio </label>

                                    <div class="col-sm-8">

                                        <select class="form-control input-lg m-bot15" name="anio">
                                            <?php for ($a = 2018; $a <= (int) date('Y') + 1; $a++) { ?>
                                                <option value="<?php echo $a; ?>"<?php echo $a == (int) date('Y') ? ' selected' : ''; ?>><?php echo $a; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <br><br><br><br>
                                </div>
                                <?php echo $selectorSucursal; ?>
                                <div class="modal-footer">
                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                                        <strong>Cerrar</strong>
                                    </button>
                                    <button name="reporte_anual" type="submit" class="btn btn-primary">
                                        <strong>Buscar</strong></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>



                <div id="6meses" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                     aria-hidden="true">
                    <form class="form-validate form-horizontal"  target="_blank" name="form2" action="Reportes.php"
                          method="GET">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                    <h3 id="myModalLabel" align="center">Mostrar Reporte de los ultimos 6 meses</h3>
                                </div>

                                <div class="modal-body">

                                </div>
                                <?php echo $selectorSucursal; ?>
                                <div class="modal-footer">
                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                                        <strong>Cerrar</strong>
                                    </button>
                                    <button name="reporte_6meses" type="submit" data-target="_blank" class="btn btn-primary">
                                        <strong>Buscar</strong></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>



        </header>

        <div class="row">

            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">
                        <img src="<?php echo $urlViews; ?>/img/descargar.jpg" width="204" height="202">
                    </header>
                </section>
            </div>
        </div>


    </section>
</section>
<!--main content end-->

<?PHP include("LibraryJs.php"); ?>


</body>
</html>
