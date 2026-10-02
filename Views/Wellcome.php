<!DOCTYPE html>
<html lang="en">
    <?php 
    include('Head.php')
    ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
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
                             <input class="form-control" placeholder="Search" type="text">
                    </form>
                </li>
            </ul>
            <!--  search form end -->
        </div>
        <?PHP include ("DropDown.php"); ?> 
    </header>
    <?PHP include ("Menu.php"); ?>
</section>
    
    <section id = "main-content">
        <section class="wrapper">

        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-laptop"></i> PRINCIPAL</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="AccessUsers.php">Inicio</a></li>
                    <li><i class="fa fa-laptop"></i> Principal</li>
                </ol>
            </div>
        </div>

        <div class="row">

            <style>
                /* cuadros de indicadores: toda la caja es un enlace al modulo correspondiente */
                a.indicador,
                a.indicador:hover,
                a.indicador:focus {
                    display: block;
                    color: #fff;
                    text-decoration: none;
                }

                a.indicador .info-box {
                    transition: transform 0.2s, box-shadow 0.2s;
                }

                a.indicador:hover .info-box {
                    transform: translateY(-4px);
                    box-shadow: 0 8px 18px rgba(0, 0, 0, 0.18);
                }

                a.indicador .info-box .count {
                    font-size: 30px;
                    font-weight: bold;
                }

                a.indicador .info-box .detalle {
                    font-size: 12px;
                    opacity: 0.9;
                    margin-top: 2px;
                }
            </style>
            <?PHP
            // indicadores de la sucursal activa: [enlace, color, icono, numero, titulo, detalle]
            $indicadores = array(
                array('ReportesVentas.php', 'blue-bg', 'fa fa-money', $tipoMonedaElegida . ' ' . number_format($indicadorVendidoHoy, 2),
                    'Vendido hoy', 'Ver reportes de ventas'),
                array('Consolidar.php', 'brown-bg', 'icon_documents_alt', $indicadorPorConsolidar,
                    'Por consolidar', $indicadorPorConsolidar == 1 ? 'venta pendiente' : 'ventas pendientes'),
                array('Producto.php', $indicadorSinStock > 0 ? 'dark-bg' : 'green-bg', 'fa fa-cubes', $indicadorSinStock,
                    'Sin stock', $indicadorSinStock == 1 ? 'producto agotado aquí' : 'productos agotados aquí'),
                array('Traslados.php', 'green-bg', 'arrow_left-right_alt', $indicadorTraslados,
                    'Traslados', $indicadorTraslados == 1 ? 'solicitud por atender' : 'solicitudes por atender'),
            );
            foreach ($indicadores as $indicador) { ?>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <a href="<?PHP echo $indicador[0]; ?>" class="indicador">
                    <div class="info-box <?PHP echo $indicador[1]; ?>">
                        <i class="<?PHP echo $indicador[2]; ?>"></i>
                        <div class="count"><?PHP echo $indicador[3]; ?></div>
                        <div class="title"><?PHP echo $indicador[4]; ?></div>
                        <div class="detalle"><?PHP echo $indicador[5]; ?></div>
                    </div><!--/.info-box-->
                </a>
            </div><!--/.col-->
            <?PHP } ?>


        </div>

        <div class="row">

            <div class="col-lg-9 col-md-12">

                <div class="panel panel-default">

                    <div class="panel-heading">
                        <h2><i class="fa fa-flag-o red"></i><strong>Venta Total del Día - <?php echo htmlspecialchars($usuarioSesion['nombreSucursal']); ?></strong></h2>

                    </div>
                    <div class="panel-body">
                        <div class="dataTable_wrapper">
                            <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                            <thead>
                            <tr>

                                    <th>PRODUCTO</th>
                                    <th>CANTIDAD</th>
                                    <th>PRECIO</th>
                                    <th>TOTAL VENDIDO</th>
                                    <th>FECHA</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?PHP
                            $totalDelDia = 0;
                            while ($venta = mysqli_fetch_array($ventasDelDia)) {
                                $totalDelDia += $venta['totalVendido'];
                                ?>
                                <tr>
                                    <td><?PHP echo htmlspecialchars($venta['descripcion']); ?></td>
                                    <td><?PHP echo $venta['cantidad']; ?></td>
                                    <td><?PHP echo $venta['precio'] . ' ' . $tipoMonedaElegida; ?></td>
                                    <td><?PHP echo $venta['totalVendido'] . ' ' . $tipoMonedaElegida; ?></td>
                                    <td><?PHP echo date('d/m/Y', strtotime($venta['fecha'])); ?></td>
                                </tr>
                            <?PHP } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th colspan="3" style="text-align: right;">TOTAL DEL DIA :</th>
                                <th colspan="2"><?PHP echo $totalDelDia . ' ' . $tipoMonedaElegida; ?></th>
                            </tr>
                            </tfoot>


                            </table>


                        </div>


                    </div>

                    
                </div>



            </div>


        </div>


        <div class="col-md-3">
        <div style="text-align: center;"><h3>Calendario</h3></div>

        <div id="calendar" class="mb">
                    <div class="panel green-panel no-margin">
                        <div class="panel-body">
                            <div id="date-popover" class="popover top"
                                 style="cursor: pointer; disadding: block; margin-left: 33%; margin-top: -50px; width: 175px;">
                                <div class="arrow"></div>
                                <h3 class="popover-title" style="disadding: none;"></h3>
                                <div id="date-popover-content" class="popover-content"></div>
                            </div>
                            <div id="my-calendar"></div>
                        </div>
                    </div>
                </div>


        </div>

        <div class="col1of2">
                    <div class="datepicker-placeholder"></div>
        </div>


        </div>  

        </section>
    </section>

<?PHP include ("LibraryJs.php"); ?>

</body>
</html>