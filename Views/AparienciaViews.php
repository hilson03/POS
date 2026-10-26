<!DOCTYPE html>
<html lang="es">
<?php
include('Head.php');
?>
<style>
    /* tarjetas del mismo alto aunque tengan textos de distinto largo */
    .marca-fila { display: flex; flex-wrap: wrap; }
    /* colores de la barra superior: cuadritos para elegir */
    .colores-barra { display: flex; flex-wrap: wrap; gap: 14px; margin: 6px 0 16px; }
    .color-opcion { display: flex; flex-direction: column; align-items: center; gap: 6px; cursor: pointer; font-weight: normal; margin: 0; }
    .color-opcion input { position: absolute; opacity: 0; pointer-events: none; }
    .color-muestra { width: 64px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center;
                     color: #fff; font-size: 20px; border: 3px solid transparent; box-shadow: 0 2px 6px rgba(0,0,0,0.2); }
    .color-muestra i { visibility: hidden; }
    .color-opcion input:checked + .color-muestra { border-color: #1a1a1a; box-shadow: 0 0 0 3px #fff, 0 0 0 5px #1a1a1a; }
    .color-opcion input:checked + .color-muestra i { visibility: visible; }
    .color-opcion input:focus-visible + .color-muestra { outline: 2px solid #007aff; outline-offset: 3px; }
    .color-nombre { font-size: 12px; color: #3a3f45; }
    .marca-fila > div { display: flex; }
    .marca-tarjeta { width: 100%; display: flex; flex-direction: column; }
    .marca-tarjeta .panel-body { flex: 1; display: flex; flex-direction: column; border: 1px solid #e3e6ea; border-top: none; }
    .marca-tarjeta .marca-botones { margin-top: auto; }
    .marca-requisitos { padding-left: 18px; margin-bottom: 12px; color: #3a3f45; font-size: 13px; }
    .marca-requisitos li { margin-bottom: 3px; }
    .marca-aviso { display: none; margin-top: 8px; padding: 6px 10px; border-radius: 4px; font-size: 13px; }
    .marca-aviso.alerta { background: #fcf3dc; color: #7a5a00; border: 1px solid #f0d58a; }
    .marca-aviso.bien { background: #e3f4e9; color: #1f6b3a; border: 1px solid #a9dbb9; }
    .marca-vista {
        height: 170px;
        border: 1px dashed #b9bec5;
        border-radius: 6px;
        background: #f7f7f8;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        overflow: hidden;
    }
    .marca-vista img { max-width: 90%; max-height: 150px; object-fit: contain; }
    /* el logo de la barra se ve sobre el mismo azul de la barra */
    .marca-vista.oscura { background: #007fff; }
    .marca-vista.oscura img { max-height: 40px; }
    .marca-vista.pestana img { width: 64px; height: 64px; }
    .marca-estado { margin-bottom: 8px; }
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
                <h3 class="page-header"><i class="icon_images"></i> APARIENCIA</h3>
                <div class="<?PHP echo $alerta; ?>" role="alert">
                    <strong><?PHP echo htmlspecialchars($mensaje); ?></strong>
                </div>

                <ol class="breadcrumb">
                    <?PHP include("MenuOpcionesConfiguracion.php"); ?>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading"><i class="fa fa-paint-brush"></i> Color de la barra superior</header>
                    <div class="panel-body">
                        <p class="help-block" style="margin-top:0;">
                            Al tocar un color, la barra de arriba cambia para que veas cómo queda; se aplica en todo el sistema al presionar <b>Guardar color</b>.
                            Son colores oscuros para que el logo y los textos en blanco se lean bien.
                        </p>
                        <form action="GuardarApariencia.php" method="post">
                            <div class="colores-barra">
                                <?PHP foreach (coloresBarra() as $hex => $nombreColor) { ?>
                                    <label class="color-opcion" title="<?PHP echo $nombreColor; ?>">
                                        <input type="radio" name="color_barra" value="<?PHP echo $hex; ?>"<?PHP echo colorBarra() == $hex ? ' checked' : ''; ?>
                                               onchange="verColorBarra('<?PHP echo $hex; ?>')">
                                        <span class="color-muestra" style="background: <?PHP echo $hex; ?>;"><i class="fa fa-check"></i></span>
                                        <span class="color-nombre"><?PHP echo $nombreColor; ?></span>
                                    </label>
                                <?PHP } ?>
                            </div>
                            <button name="guardar_color" type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar color</button>
                        </form>
                    </div>
                </section>
            </div>
        </div>

        <div class="row marca-fila">
            <?PHP foreach ($imagenes as $clave => $imagen) {
                $personalizada = esImagenPersonalizada($clave);
                $claseVista = $clave == 'logo_barra' ? ' oscura' : ($clave == 'favicon' ? ' pestana' : '');
                ?>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <section class="panel marca-tarjeta">
                        <header class="panel-heading"><?PHP echo $imagen['titulo']; ?></header>
                        <div class="panel-body">
                            <div class="marca-vista<?PHP echo $claseVista; ?>">
                                <img id="vista_<?PHP echo $clave; ?>" src="<?PHP echo $urlViews . imagenMarca($clave); ?>" alt="<?PHP echo $imagen['titulo']; ?>">
                            </div>
                            <div class="marca-estado">
                                <?PHP if ($clave == 'fondo_login' && esFondoAutomatico()) { ?>
                                    <span class="label label-info">Generada del logo</span>
                                <?PHP } elseif ($personalizada) { ?>
                                    <span class="label label-success">Personalizada</span>
                                <?PHP } else { ?>
                                    <span class="label label-default">Original</span>
                                <?PHP } ?>
                            </div>
                            <p class="help-block"><?PHP echo $imagen['ayuda']; ?></p>
                            <ul class="marca-requisitos">
                                <li><b>Medida:</b> <?PHP echo $imagen['medida']; ?> (mínimo <?PHP echo $imagen['minimo'][0] . ' × ' . $imagen['minimo'][1]; ?>)</li>
                                <li><b>Forma:</b> <?PHP echo $imagen['forma'] == 'cuadrada' ? 'cuadrada (igual de ancho que de alto)' : 'horizontal (unas 3 veces más ancha que alta)'; ?></li>
                                <li><b>Fondo:</b> <?PHP echo $imagen['fondo']; ?></li>
                                <li><b>Peso:</b> 2 MB máximo</li>
                            </ul>

                            <div class="marca-botones">
                            <form action="GuardarApariencia.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="clave" value="<?PHP echo $clave; ?>">
                                <input type="file" name="imagen" class="form-control" required
                                       accept=".png,.jpg,.jpeg,.webp<?PHP echo $clave == 'favicon' ? ',.ico' : ''; ?>"
                                       data-ancho="<?PHP echo $imagen['minimo'][0]; ?>" data-alto="<?PHP echo $imagen['minimo'][1]; ?>"
                                       data-forma="<?PHP echo $imagen['forma']; ?>" data-medida="<?PHP echo $imagen['medida']; ?>"
                                       onchange="verAntes(this, '<?PHP echo $clave; ?>')">
                                <div class="marca-aviso" id="aviso_<?PHP echo $clave; ?>"></div>
                                <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">
                                    <i class="fa fa-upload"></i> Cambiar imagen
                                </button>
                            </form>
                            <?PHP if ($personalizada) { ?>
                                <form action="GuardarApariencia.php" method="post" style="margin-top:6px;">
                                    <input type="hidden" name="clave" value="<?PHP echo $clave; ?>">
                                    <button type="submit" name="restaurar" class="btn btn-default btn-block">
                                        <i class="fa fa-undo"></i> Volver a la original
                                    </button>
                                </form>
                            <?PHP } ?>
                            </div>
                        </div>
                    </section>
                </div>
            <?PHP } ?>
        </div>

    </section>
</section>
<!--main content end-->

<?PHP include("LibraryJs.php"); ?>

<script>
    // vista previa del color: pinta la barra de arriba de esta misma pagina (se guarda con "Guardar color")
    function verColorBarra(color) {
        var barra = document.querySelector('.header.dark-bg');
        if (barra) {
            barra.style.setProperty('background', color, 'important');
            barra.style.setProperty('background-color', color, 'important');
        }
    }

    // muestra la imagen elegida antes de guardarla y avisa si su medida o forma no es la recomendada
    function verAntes(input, clave) {
        var aviso = document.getElementById('aviso_' + clave);
        aviso.style.display = 'none';
        if (!input.files || !input.files[0]) {
            return;
        }
        var lector = new FileReader();
        lector.onload = function (e) {
            var vista = document.getElementById('vista_' + clave);
            vista.onload = function () {
                var ancho = vista.naturalWidth, alto = vista.naturalHeight, proporcion = ancho / Math.max(1, alto);
                var d = input.dataset, medidas = ancho + ' × ' + alto + ' px', texto = '';
                if (ancho < d.ancho || alto < d.alto) {
                    texto = 'Mide ' + medidas + ': puede verse borrosa. Lo recomendado es ' + d.medida + '.';
                } else if (d.forma == 'cuadrada' && (proporcion < 0.8 || proporcion > 1.25)) {
                    texto = 'Mide ' + medidas + ' y no es cuadrada: se verá más chica y con espacios. Lo recomendado es ' + d.medida + '.';
                } else if (d.forma == 'horizontal' && proporcion < 2) {
                    texto = 'Mide ' + medidas + ' y no es lo bastante horizontal: se verá muy chica. Lo recomendado es ' + d.medida + '.';
                }
                aviso.innerHTML = texto !== '' ? '<i class="fa fa-exclamation-triangle"></i> ' + texto + ' Puedes guardarla igual.'
                                               : '<i class="fa fa-check"></i> Mide ' + medidas + ': buena medida.';
                aviso.className = 'marca-aviso ' + (texto !== '' ? 'alerta' : 'bien');
                aviso.style.display = 'block';
                vista.onload = null;
            };
            vista.src = e.target.result;
        };
        lector.readAsDataURL(input.files[0]);
    }
</script>
</body>
</html>
