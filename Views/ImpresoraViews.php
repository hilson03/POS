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
            <ul class="nav top-menu">
                <li>
                    <form class="navbar-form"></form>
                </li>
            </ul>
        </div>
        <?PHP include("DropDown.php"); ?>
    </header>
    <?PHP include("Menu.php") ?>

</section>

<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-print"></i> IMPRESORA</h3>
                <div class="<?PHP echo $alerta; ?>" role="alert">
                    <strong><?PHP echo htmlspecialchars($mensaje); ?></strong>
                </div>

                <ol class="breadcrumb">
                    <?PHP include("MenuOpcionesConfiguracion.php"); ?>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <section class="panel">
                    <header class="panel-heading"><i class="fa fa-print"></i> Impresora de comprobantes</header>
                    <div class="panel-body">
                        <p class="help-block" style="margin-top:0;">
                            Los comprobantes de venta se imprimen en impresora térmica. Elige el ancho del rollo de papel que usa la impresora
                            (viene escrito en la caja del rollo o en el manual de la impresora).
                        </p>
                        <form action="GuardarImpresora.php" method="post">
                            <?PHP foreach (array(80 => array('80 mm', 'El más común en tiendas y cajas'), 58 => array('58 mm', 'Impresoras pequeñas o portátiles')) as $ancho => $texto) { ?>
                                <label style="display:block; font-weight:normal; cursor:pointer; padding:8px 10px; margin-bottom:6px; border:1px solid #d5d8dd; border-radius:6px;">
                                    <input type="radio" name="ancho_papel" value="<?PHP echo $ancho; ?>"<?PHP echo anchoPapel() == $ancho ? ' checked' : ''; ?>
                                           style="width:18px; height:18px; vertical-align:middle; margin:0 8px 0 0;">
                                    <b><?PHP echo $texto[0]; ?></b> — <?PHP echo $texto[1]; ?>
                                </label>
                            <?PHP } ?>
                            <button name="guardar_papel" type="submit" class="btn btn-primary" style="margin-top:6px;"><i class="fa fa-save"></i> Guardar</button>
                        </form>
                    </div>
                </section>
            </div>

            <div class="col-lg-6">
                <section class="panel">
                    <header class="panel-heading"><i class="fa fa-info-circle"></i> Primera vez que se imprime en una computadora</header>
                    <div class="panel-body">
                        <ol style="padding-left:18px; margin-bottom:8px;">
                            <li>Instala el driver de la impresora térmica y elige en él el papel del mismo ancho (80 o 58 mm).</li>
                            <li>Haz una venta y presiona <b>Imprimir comprobante</b>.</li>
                            <li>En la ventana de imprimir de Chrome: elige la impresora térmica, márgenes <b>"Ninguno"</b>,
                                escala <b>100%</b> y desmarca <b>"Encabezados y pies de página"</b>.</li>
                            <li>Imprime. Chrome recuerda estos ajustes para las siguientes veces.</li>
                        </ol>
                        <p class="help-block" style="margin-bottom:0;">
                            Sin impresora todavía: en la ventana de imprimir elige <b>"Guardar como PDF"</b> y verás el comprobante
                            tal como saldrá en el rollo.
                        </p>
                    </div>
                </section>
            </div>
        </div>

    </section>
</section>
<!--main content end-->

<?PHP include("LibraryJs.php"); ?>

</body>
</html>
