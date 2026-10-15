<!DOCTYPE html>
<html lang="en">
<?php
include('Head.php');

// campos del formulario: nombre => [etiqueta, ayuda, maximo, obligatorio]
$camposNegocio = array(
    'razon'       => array('Nombre del negocio', 'Sale grande en el comprobante y en la pestaña del navegador.', 100, true),
    'propietario' => array('Propietario o razón social', 'Sale como "De: ..." en el comprobante.', 100, false),
    'nro'         => array('RTN del negocio', 'Opcional. Si lo escribes, sale en el comprobante.', 100, false),
    'direccion'   => array('Dirección', 'Barrio, calle o referencia.', 100, false),
    'ciudad'      => array('Ciudad', 'Ej: Tegucigalpa, Teupasenti.', 100, false),
    'telefono'    => array('Teléfono', '', 100, false),
    'correo'      => array('Correo', 'Opcional.', 100, false),
    'mensajePie'  => array('Mensaje al pie del comprobante', 'Opcional. Ej: ¡Gracias por su compra!', 200, false),
);
?>
<style>
    /* vista previa del encabezado del comprobante (misma letra y orden que Comprobante.php) */
    .vista-negocio { width: 72mm; max-width: 100%; margin: 0 auto; padding: 12px 10px; background: #fff; color: #000;
                     font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 1.35; text-align: center;
                     box-shadow: 0 2px 10px rgba(0,0,0,0.15); }
    .vista-negocio .v-nombre { font-size: 1.6em; font-weight: bold; }
    .vista-negocio .v-titulo { font-size: 1.15em; font-weight: bold; margin-top: 2px; }
    .vista-negocio .v-linea { border-top: 1px dashed #000; margin: 6px 0; }
    .vista-negocio .v-gris { color: #888; }
    .vista-negocio .v-pie { font-weight: bold; margin-top: 4px; }
</style>
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
                <h3 class="page-header"><i class="fa fa-building-o"></i> DATOS DEL NEGOCIO</h3>
                <div class="<?PHP echo $alerta; ?>" role="alert">
                    <strong><?PHP echo htmlspecialchars($mensaje); ?></strong>
                </div>

                <ol class="breadcrumb">
                    <?PHP include("MenuOpcionesConfiguracion.php"); ?>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <section class="panel">
                    <header class="panel-heading"><i class="fa fa-building-o"></i> Datos que salen en el comprobante de venta</header>
                    <div class="panel-body">
                        <form class="form-horizontal" action="GuardarDatosNegocio.php" method="post">
                            <?PHP foreach ($camposNegocio as $campo => $info) { ?>
                                <div class="form-group">
                                    <label class="control-label col-lg-4" for="neg_<?PHP echo $campo; ?>">
                                        <?PHP echo $info[0]; ?><?PHP echo $info[3] ? ' <span style="color:#a12a1f;">*</span>' : ''; ?>
                                    </label>
                                    <div class="col-lg-8">
                                        <input class="form-control input-lg" id="neg_<?PHP echo $campo; ?>" name="<?PHP echo $campo; ?>"
                                               type="<?PHP echo $campo == 'correo' ? 'email' : 'text'; ?>" maxlength="<?PHP echo $info[2]; ?>"
                                               value="<?PHP echo htmlspecialchars($negocio[$campo]); ?>"<?PHP echo $info[3] ? ' required' : ''; ?>
                                               oninput="verNegocio()">
                                        <?PHP if ($info[1] != '') { ?>
                                            <small class="help-block" style="margin-bottom:0;"><?PHP echo $info[1]; ?></small>
                                        <?PHP } ?>
                                    </div>
                                </div>
                            <?PHP } ?>
                            <div class="form-group" style="margin-bottom:0;">
                                <div class="col-lg-8 col-lg-offset-4">
                                    <button name="guardar_negocio" type="submit" class="btn btn-primary btn-lg"><i class="fa fa-save"></i> Guardar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

            <div class="col-lg-5">
                <section class="panel">
                    <header class="panel-heading"><i class="fa fa-eye"></i> Así se ve en el comprobante</header>
                    <div class="panel-body" style="background:#eee;">
                        <div class="vista-negocio">
                            <div class="v-nombre" id="v_razon"></div>
                            <div id="v_propietario"></div>
                            <div id="v_nro"></div>
                            <div id="v_direccion"></div>
                            <div id="v_ciudad"></div>
                            <div id="v_telefono"></div>
                            <div id="v_correo"></div>
                            <div class="v-titulo">COMPROBANTE DE VENTA</div>
                            <div class="v-linea"></div>
                            <div class="v-gris">... fecha, cliente, productos y pagos ...</div>
                            <div class="v-linea"></div>
                            <div class="v-pie" id="v_mensajePie"></div>
                        </div>
                        <p class="help-block" style="margin:10px 0 0; text-align:center;">
                            La vista cambia mientras escribes; se aplica a los comprobantes al presionar <b>Guardar</b>.
                        </p>
                    </div>
                </section>
            </div>
        </div>

    </section>
</section>
<!--main content end-->

<?PHP include("LibraryJs.php"); ?>

<script>
    // vista previa del encabezado: mismo texto y orden que el comprobante (las lineas vacias no salen)
    function verNegocio() {
        function valor(campo) { return document.getElementById('neg_' + campo).value.trim(); }
        var formatos = {
            razon: function (v) { return v || 'Nombre del negocio'; },
            propietario: function (v) { return v ? 'De: ' + v : ''; },
            nro: function (v) { return v ? 'RTN: ' + v : ''; },
            direccion: function (v) { return v; },
            ciudad: function (v) { return v; },
            telefono: function (v) { return v ? 'Tel: ' + v : ''; },
            correo: function (v) { return v; },
            mensajePie: function (v) { return v; }
        };
        Object.keys(formatos).forEach(function (campo) {
            var linea = document.getElementById('v_' + campo);
            var texto = formatos[campo](valor(campo));
            linea.textContent = texto;
            linea.style.display = texto ? 'block' : 'none';
        });
    }
    verNegocio();
</script>
</body>
</html>
