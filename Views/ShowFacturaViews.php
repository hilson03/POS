<!DOCTYPE html>
<html lang="es">
<?php
include('Head.php');
include('LiteralMoney.php');
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
            <div class="col-lg-12 ">
                <h3 class="page-header"><i class="fa fa-print"></i> PRINCIPAL</h3>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a
                            href="AccessUsers.php">Inicio</a>
                    </li>
                    <li>
                        <i class="fa fa-print"></i><a
                            href="#">Vista previa del comprobante</a>
                    </li>
                    <li>
                        <i class="fa fa-save"></i><a
                                href="RegistrarVenta.php?RegistarVenta=1&<?PHP echo 'ci=' . urlencode($ci) . '&ingreso1=' . urlencode($totalApagar) . '&ingreso2=' . urlencode($efectivo) . '&resultado=' . urlencode($cambio); ?>">
                             Guardar venta</a>
                    </li>
                </ol>
            </div>
        </div>

        <?PHP $datosCobro = 'ci=' . urlencode($ci) . '&ingreso1=' . urlencode($totalApagar) . '&ingreso2=' . urlencode($efectivo) . '&resultado=' . urlencode($cambio); ?>
        <style>
            /* acciones del comprobante: iconos de la fuente del sistema (las imagenes del original nunca existieron) */
            .acciones-venta { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
            .accion-venta {
                display: flex; align-items: center; gap: 12px; padding: 12px 18px; border-radius: 8px;
                color: #fff !important; text-decoration: none !important; min-width: 230px;
                box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15); transition: transform 0.15s, box-shadow 0.15s;
            }
            .accion-venta:hover { transform: translateY(-2px); box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2); }
            .accion-venta i { font-size: 30px; width: 34px; text-align: center; }
            .accion-venta strong { display: block; font-size: 15px; letter-spacing: 0.3px; }
            .accion-venta small { display: block; font-size: 12px; opacity: 0.9; }
            .accion-venta.imprimir { background: #3b6fd8; }
            .accion-venta.guardar { background: #2e9d5b; }
        </style>
        <div class="acciones-venta">
            <a class="btnPrint accion-venta imprimir" href="ConFactura.php?<?PHP echo $datosCobro; ?>">
                <i class="fa fa-print"></i>
                <span><strong>IMPRIMIR COMPROBANTE</strong><small>Con los datos de la tienda</small></span>
            </a>
            <!-- la venta se registra aqui (descuenta stock y pasa a Cierre de ventas); imprimir no la guarda -->
            <a class="accion-venta guardar" href="RegistrarVenta.php?RegistarVenta=1&<?PHP echo $datosCobro; ?>">
                <i class="fa fa-check-circle"></i>
                <span><strong>GUARDAR VENTA</strong><small>Registra la venta y empieza otro pedido</small></span>
            </a>
        </div>
        <br>

        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6"></div>
                    <div class="col-md-6">

                        <!-- vista previa: el mismo comprobante que se imprime, con el ancho del rollo de la impresora termica -->
                        <div style="display:inline-block; background:#fff; padding:6px 10px; box-shadow:0 2px 10px rgba(0,0,0,0.15);">
                            <?PHP include(__DIR__ . '/Comprobante.php'); ?>
                        </div>
                        <p style="margin-top:8px; font-size:12px; color:#555;">
                            Vista previa en rollo de <?PHP echo anchoPapel(); ?> mm (se cambia en Configuración → Impresora).
                        </p>


                    </div>


                </div>
            </div>
        </div>
        <div class="col-md-4"></div>


    </section>
</section>

<?PHP include("LibraryJs.php"); ?>


</body>
</html>