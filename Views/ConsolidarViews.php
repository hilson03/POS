
<!DOCTYPE html>
<html lang="es">
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
                <h3 class="page-header"><i class="icon_documents_alt"></i> CIERRE DE VENTAS</h3>
                <div class="<?PHP echo $alerta; ?>" role="alert">
                    <strong><?PHP echo $mensaje; ?></strong>
                </div>

                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="AccessUsers.php">Inicio</a></li>
                    <li><i class="icon_documents_alt"></i> Cierre de ventas</li>

                </ol>
            </div>
        </div>

        <header class="panel-heading"> Ventas pendientes de cierre</header>
        <?php
        // se leen todas las ventas antes de la tabla para avisar arriba cuantas transferencias hay que revisar
        $listaVentas = $allVentas->fetch_all(MYSQLI_BOTH);
        $transferencias = 0;
        $totalTransferencias = 0;
        foreach ($listaVentas as $venta) {
            if (strpos($venta['formaPago'], 'Transferencia') !== false) {
                $transferencias++;
                $totalTransferencias += $venta['montoTransferencia'];   // en un pago mixto, solo la parte transferida
            }
        }
        ?>
        <style>
            /* transferencias resaltadas: el administrador las revisa contra el banco antes de consolidar */
            #dataTables-example tr.fila-transferencia > td { background-color: #f3ecfb !important; }
            .label-transferencia { background-color: #8a4fd0; }
            .label-mixto { background-color: #e08a1e; }
        </style>
        <?php if ($transferencias > 0) { ?>
            <div class="alert" style="background:#f3ecfb; border:1px solid #d9c6f1; color:#4a2a7a; margin:10px 15px 0;">
                <i class="fa fa-university"></i>
                <strong><?php echo $transferencias == 1 ? 'Hay 1 venta' : 'Hay ' . $transferencias . ' ventas'; ?> por transferencia (<?php echo montoHn($totalTransferencias); ?>).</strong>
                Antes de cerrarlas, revisa en la app o el estado de cuenta del banco que cada referencia haya llegado.
                Pasa el mouse sobre la etiqueta <span class="label label-transferencia">Transferencia</span> para ver la referencia y la cuenta.
            </div>
        <?php } ?>


        <div class="panel-body">
            <div class="dataTable_wrapper">
                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                    <tr>
                        <th><i class="icon_profile"></i> N° VENTA</th>
                        <th><i class="icon_profile"></i> FECHA</th>
                        <th><i class="icon_briefcase_alt"></i> CLIENTE</th>
                        <th><i class="icon_profile"></i> TOTAL L.</th>
                        <th><i class="icon_profile"></i> VENDEDOR</th>
                        <th><i class="fa fa-credit-card"></i> PAGO</th>
                        <th><i class="icon_contacts_alt"></i> ESTADO</th>
                        <th><i class="icon_contacts_alt"></i> COMENTARIO</th>
                        <th><i class="icon_cog"></i> ACCIONES</th>
                    </tr>
                    </thead>
                    <?php

                    foreach ($listaVentas as $ventas) {
                        $esTransferencia = strpos($ventas['formaPago'], 'Transferencia') !== false;
                        ?>

                        <tr<?php echo $esTransferencia ? ' class="fila-transferencia"' : ''; ?>>
                            <td align="center"><?php echo htmlspecialchars((string) $ventas['idVentas']); ?></td>
                            <td data-order="<?php echo htmlspecialchars((string) $ventas['fechaVenta']); ?>"><?php echo htmlspecialchars(fechaHn($ventas['fechaVenta'])); ?></td>
                            <td><?php echo htmlspecialchars((string) $ventas['cliente']); ?></td>
                            <td align="center" data-order="<?php echo (float) $ventas['total']; ?>"><?php echo montoHn($ventas['total']); ?></td>
                            <td><?php echo htmlspecialchars($ventas['vendedor']); ?></td>
                            <td><?php
                                $esTarjeta = strpos($ventas['formaPago'], 'Tarjeta') !== false;
                                // varios pagos de la misma forma (ej. dos tarjetas) tambien es pago mixto: se muestra cuantos fueron
                                $esMixto = $ventas['partesPago'] > 1;
                                $textoPago = $ventas['formaPago'] . ($ventas['partesPago'] > substr_count($ventas['formaPago'], ' + ') + 1 ? ' (' . $ventas['partesPago'] . ' pagos)' : '');
                                if ($esTransferencia) {
                                    $claseEtiqueta = 'label-transferencia';
                                    $detallePago = $ventas['refsPago'] . ' | ' . $ventas['bancoDestino'] . ($ventas['nombreTransfirio'] != '' ? ' | ' . $ventas['nombreTransfirio'] : '');
                                } else {
                                    // pago mixto sin transferencia (ej. "Tarjeta + Efectivo") en naranja
                                    $claseEtiqueta = $esMixto ? 'label-mixto' : ($esTarjeta ? 'label-primary' : 'label-success');
                                    $detallePago = $ventas['refsPago'];
                                }
                                ?><span class="label <?php echo $claseEtiqueta; ?>"<?php echo $detallePago != '' ? ' title="' . htmlspecialchars($detallePago) . '"' : ''; ?>><?php echo htmlspecialchars($textoPago); ?></span>
                                <?php if ($ventas['refsPago'] != '') { ?><br><small style="color:<?php echo $esTransferencia ? '#5b3a8c' : '#2c4f9e'; ?>;"><?php echo htmlspecialchars($ventas['refsPago']); ?></small><?php } ?></td>
                            <td><?php echo $ventas['estado'] == 'Consolidado' ? 'Cerrada' : 'Pendiente'; ?></td>
                            <td><?php echo htmlspecialchars((string) $ventas['comentario']); ?></td>


                            <td>
                                <a href="#a<?php echo $ventas[0]; ?>" role="button" class="btn btn-warning"
                                   data-toggle="modal"><i class="icon_pencil-edit_alt"></i></a>
                                <a href="ConsolidarVenta.php?idConsolidar=<?php echo $ventas[0]; ?>"
                                   data-confirmar-tipo="consolidar"
                                   data-confirmar="la venta #<?php echo htmlspecialchars((string) $ventas['idVentas']); ?> de <?php echo htmlspecialchars($ventas['cliente']); ?> por <?php echo montoHn($ventas['total']); ?>"
                                   class="btn btn-success" title="Cerrar venta"><i class="icon_box-checked"></i></a>
                            </td>
                        </tr>

                        <div id="a<?php echo $ventas[0]; ?>" class="modal fade" tabindex="-1" role="dialog"
                             aria-labelledby="myModalLabel" aria-hidden="true">
                            <form class="form-validate form-horizontal" name="form2" action="ConsolidarVenta.php"
                                  method="post">
                                <input type="hidden" name="idVentas"
                                       value="<?php echo htmlspecialchars((string) $ventas['idVentas']); ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                    aria-hidden="true">×
                                            </button>
                                            <h3 id="myModalLabel" align="center">Escribir Comentario</h3>
                                        </div>

                                        <div class="modal-body">
                                            <div class="form-group ">
                                                <label for="responsable"
                                                       class="control-label col-lg-2">Detalle de la venta:</label>
                                                <div class="col-lg-10">
                                                    <input class="form-control input-lg m-bot15" type="text"  name="comentario" >
                                                </div>
                                            </div>
                                        </div>

                                        <br>
                                        <div class="modal-footer">
                                            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                                                <strong>Cerrar</strong></button>
                                            <button name="insertarComentario" type="submit" class="btn btn-primary"><strong>Editar</strong>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    <?php } ?>
                </table>
            </div>
        </div>
    </section>
</section>
<!--main content end-->

<?PHP include("LibraryJs.php"); ?>


</body>
</html>
