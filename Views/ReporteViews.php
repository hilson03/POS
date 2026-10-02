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
                <h3 class="page-header"><i class="icon_datareport"></i> REPORTES DE VENTAS</h3>
                <div class="<?PHP echo $alerta; ?>" role="alert">
                    <strong><?PHP echo $mensaje; ?></strong>
                </div>

                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="AccessUsers.php">Inicio</a></li>
                    <li><i class="icon_datareport"></i> Reporte de Ventas</li>

                </ol>
            </div>
        </div>
        <header class="panel-heading">
            <div class="panel-body">
                <style>
                    /* tarjetas de reportes: fondo claro, formas pastel en las esquinas y barras azul marino */
                    .reportes-intro {
                        color: #5a5f73;
                        font-size: 15px;
                        margin: 0 0 22px;
                    }

                    .grid-reportes {
                        display: grid;
                        grid-template-columns: repeat(3, 1fr);
                        gap: 26px;
                        max-width: 1000px;
                    }

                    @media (max-width: 991px) {
                        .grid-reportes { grid-template-columns: repeat(2, 1fr); }
                    }

                    @media (max-width: 600px) {
                        .grid-reportes { grid-template-columns: 1fr; }
                    }

                    .tarjeta-reporte {
                        --pastel: #f3c4c4;
                        position: relative;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                        min-height: 320px;
                        padding: 64px 24px 56px;
                        background: #f4f2ef;
                        border-radius: 6px;
                        overflow: hidden;
                        box-shadow: 0 6px 18px rgba(35, 42, 77, 0.12);
                        color: #232a4d;
                        text-decoration: none;
                        transition: transform 0.2s, box-shadow 0.2s;
                    }

                    .tarjeta-reporte:hover,
                    .tarjeta-reporte:focus {
                        transform: translateY(-6px);
                        box-shadow: 0 14px 28px rgba(35, 42, 77, 0.2);
                        color: #232a4d;
                        text-decoration: none;
                    }

                    /* formas pastel: arriba a la izquierda y abajo a la derecha */
                    .tarjeta-reporte::before,
                    .tarjeta-reporte::after {
                        content: '';
                        position: absolute;
                        width: 190px;
                        height: 190px;
                        background: var(--pastel);
                        border-radius: 46px;
                        transform: rotate(45deg);
                        opacity: 0.8;
                    }

                    .tarjeta-reporte::before {
                        top: -120px;
                        left: -70px;
                    }

                    .tarjeta-reporte::after {
                        bottom: -130px;
                        right: -60px;
                    }

                    /* barras azul marino */
                    .tarjeta-reporte .barra {
                        position: absolute;
                        height: 7px;
                        background: #232a4d;
                        z-index: 1;
                    }

                    .tarjeta-reporte .barra-arriba {
                        top: 20px;
                        right: 0;
                        width: 55%;
                    }

                    .tarjeta-reporte .barra-abajo {
                        bottom: 20px;
                        left: 0;
                        width: 45%;
                    }

                    .tarjeta-reporte > * {
                        position: relative;
                        z-index: 2;
                    }

                    /* icono dentro de un rombo redondeado, como el logo del ejemplo */
                    .tarjeta-reporte .rombo {
                        width: 74px;
                        height: 74px;
                        border: 6px solid #232a4d;
                        border-radius: 20px;
                        transform: rotate(45deg);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        background: #f4f2ef;
                        margin-bottom: 26px;
                    }

                    .tarjeta-reporte .rombo svg {
                        transform: rotate(-45deg);
                        width: 34px;
                        height: 34px;
                    }

                    .tarjeta-reporte h4 {
                        font-size: 21px;
                        font-weight: 800;
                        letter-spacing: -0.3px;
                        margin: 0 0 8px;
                        color: #232a4d;
                    }

                    .tarjeta-reporte p {
                        font-size: 13.5px;
                        letter-spacing: 0.4px;
                        color: #4d5268;
                        margin: 0 0 16px;
                        line-height: 1.45;
                    }

                    .tarjeta-reporte .abrir {
                        font-size: 12px;
                        font-weight: bold;
                        letter-spacing: 1px;
                        text-transform: uppercase;
                        color: #232a4d;
                        border-bottom: 2px solid #232a4d;
                        padding-bottom: 2px;
                    }
                </style>

                <p class="reportes-intro">
                    Elige un reporte. Se abre en una pestaña nueva, listo para imprimir o guardar.
                    Solo se incluyen las ventas consolidadas.
                </p>

                <?php
                // titulo, descripcion, ventana que abre, color pastel e icono (trazos SVG) de cada reporte
                $tarjetasReporte = array(
                    array('Reporte del día', 'Todas las ventas de un día específico.', '#reporteDia', '#f3c4c4',
                        '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/><rect x="11" y="13" width="4" height="4" rx="1"/>'),
                    array('Rango de fechas', 'Las ventas entre dos fechas que tú eliges.', '#rangoFecha', '#c9dcf2',
                        '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4M7 15h10M14 13l3 2-3 2"/>'),
                    array('Por producto', 'Qué productos se vendieron y cuántos, en un período.', '#ReportePorProducto', '#f6d9b8',
                        '<path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/><path d="M12 12l8-4.5M12 12v9M12 12L4 7.5"/>'),
                    array('Por mes', 'Cuánto se vendió cada día del mes elegido.', '#ReporteByMes', '#d9cdee',
                        '<path d="M4 20V10M9 20V6M14 20v-8M19 20V4"/><path d="M3 20h18"/>'),
                    array('Anual', 'El total vendido en cada mes del año.', '#ReporteAnual', '#c6e6d6',
                        '<circle cx="12" cy="12" r="8.5"/><path d="M12 3.5V12l6 6"/>'),
                    array('Últimos 6 meses', 'Cómo van las ventas en el último semestre.', '#6meses', '#f2e3a9',
                        '<path d="M3 17l5-5 4 4 8-8"/><path d="M14 8h6v6"/>'),
                );
                ?>
                <div class="grid-reportes">
                    <?php foreach ($tarjetasReporte as $t) { ?>
                        <a href="<?php echo $t[2]; ?>" data-toggle="modal" class="tarjeta-reporte" style="--pastel: <?php echo $t[3]; ?>;">
                            <span class="barra barra-arriba"></span>
                            <span class="barra barra-abajo"></span>
                            <span class="rombo">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#232a4d" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $t[4]; ?></svg>
                            </span>
                            <h4><?php echo $t[0]; ?></h4>
                            <p><?php echo $t[1]; ?></p>
                            <span class="abrir">Generar reporte</span>
                        </a>
                    <?php } ?>
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



    </section>
</section>
<!--main content end-->

<?PHP include("LibraryJs.php"); ?>


</body>
</html>
