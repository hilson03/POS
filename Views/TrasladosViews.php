<!DOCTYPE html>
<html lang="en">
<?php
include('Head.php');

$etiquetasEstado = array(
    'Pendiente' => array('label-warning', 'Pendiente'),
    'Aprobada' => array('label-info', 'Aprobada'),
    'Rechazada' => array('label-danger', 'Rechazada'),
    'EnCamino' => array('label-primary', 'En camino'),
    'Recibida' => array('label-success', 'Recibida'),
    'Cancelada' => array('label-default', 'Cancelada'),
);

function fechaCorta($fecha)
{
    return $fecha ? date('d/m/Y H:i', strtotime($fecha)) : '';
}

// quien hizo cada paso del traslado
function historialTraslado($t)
{
    $pasos = array('Pedido por ' . $t['usuarioSolicita'] . ' el ' . fechaCorta($t['fechaSolicitud']));
    if ($t['fechaAprueba']) {
        $pasos[] = ($t['estado'] == 'Rechazada' ? 'Rechazado' : 'Aprobado') . ' por ' . $t['usuarioAprueba'] . ' el ' . fechaCorta($t['fechaAprueba']);
    }
    if ($t['fechaEnvio']) {
        $pasos[] = 'Enviado por ' . $t['usuarioEnvia'] . ' el ' . fechaCorta($t['fechaEnvio']);
    }
    if ($t['fechaRecibe']) {
        $pasos[] = 'Recibido por ' . $t['usuarioRecibe'] . ' el ' . fechaCorta($t['fechaRecibe']);
    }
    return $pasos;
}

// boton que envia una accion sobre un traslado
function botonAccion($idTraslado, $accion, $texto, $clase, $confirmacion)
{
    echo '<form action="AccionTraslado.php" method="post" style="display:inline-block; margin:2px;" onsubmit="return confirm(\'' . $confirmacion . '\');">'
        . '<input type="hidden" name="idTraslado" value="' . (int) $idTraslado . '">'
        . '<input type="hidden" name="accion" value="' . $accion . '">'
        . '<button type="submit" class="btn btn-sm ' . $clase . '">' . $texto . '</button></form>';
}
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
                <h3 class="page-header"><i class="arrow_left-right_alt"></i> TRASLADOS - <?PHP echo htmlspecialchars($usuarioSesion['nombreSucursal']); ?></h3>
                <div class="<?PHP echo $alerta; ?>" role="alert">
                    <strong><?PHP echo htmlspecialchars($mensaje); ?></strong>
                </div>
                <ol class="breadcrumb">
                    <li><i class="fa fa-home"></i><a href="AccessUsers.php">Inicio</a></li>
                    <li><i class="arrow_left-right_alt"></i> Traslados</li>
                </ol>
            </div>
        </div>

        <?PHP if ($esAdministrador) { ?>
        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">Pedir un producto a otra tienda</header>
                    <div class="panel-body">
                        <?PHP if (empty($stockOtras)) { ?>
                            <p class="text-muted">Ninguna otra tienda tiene stock disponible en este momento.</p>
                        <?PHP } else { ?>
                        <form class="form-horizontal" action="AccionTraslado.php" method="post">
                            <input type="hidden" name="accion" value="crear">
                            <div class="form-group">
                                <label class="control-label col-lg-2">Producto:</label>
                                <div class="col-lg-4">
                                    <select class="form-control input-lg" name="idProducto" id="trasladoProducto" required onchange="actualizarOrigenes()">
                                        <option value="">— Elige un producto —</option>
                                        <?PHP foreach ($productosPedir as $producto) {
                                            if (empty($stockOtras[$producto['idproducto']])) continue; ?>
                                            <option value="<?PHP echo $producto['idproducto']; ?>"<?PHP echo $producto['idproducto'] == $idProductoPedir ? ' selected' : ''; ?>>
                                                <?PHP echo htmlspecialchars($producto['nombreProducto']); ?> (aquí: <?PHP echo (int) $producto['cantidad']; ?>)
                                            </option>
                                        <?PHP } ?>
                                    </select>
                                </div>
                                <label class="control-label col-lg-2">Pedirlo a:</label>
                                <div class="col-lg-4">
                                    <select class="form-control input-lg" name="idSucursalOrigen" id="trasladoOrigen" required onchange="actualizarMaximo()">
                                        <option value="">— Primero elige un producto —</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-lg-2">Cantidad:</label>
                                <div class="col-lg-2">
                                    <input class="form-control input-lg" type="number" name="cantidad" id="trasladoCantidad" min="1" value="1" required>
                                </div>
                                <label class="control-label col-lg-2">Motivo:</label>
                                <div class="col-lg-6">
                                    <input class="form-control input-lg" name="motivo" maxlength="255" placeholder="Ej: cliente esperando, se agotó aquí...">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    <button type="submit" class="btn btn-primary btn-lg"><i class="arrow_left-right_alt"></i> Enviar solicitud</button>
                                </div>
                            </div>
                        </form>
                        <script>
                            // stock de cada producto en las otras tiendas: {idProducto: [{idSucursal, nombre, cantidad}]}
                            var stockOtrasTiendas = <?PHP echo json_encode($stockOtras, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

                            function actualizarOrigenes() {
                                var producto = document.getElementById('trasladoProducto').value;
                                var origen = document.getElementById('trasladoOrigen');
                                origen.innerHTML = '';
                                var tiendas = stockOtrasTiendas[producto] || [];
                                if (tiendas.length === 0) {
                                    origen.innerHTML = '<option value="">— Primero elige un producto —</option>';
                                }
                                tiendas.forEach(function (tienda) {
                                    var opcion = document.createElement('option');
                                    opcion.value = tienda.idSucursal;
                                    opcion.textContent = tienda.nombre + ' (tiene ' + tienda.cantidad + ')';
                                    opcion.setAttribute('data-cantidad', tienda.cantidad);
                                    origen.appendChild(opcion);
                                });
                                actualizarMaximo();
                            }

                            function actualizarMaximo() {
                                var origen = document.getElementById('trasladoOrigen');
                                var elegida = origen.options[origen.selectedIndex];
                                var cantidad = document.getElementById('trasladoCantidad');
                                if (elegida && elegida.getAttribute('data-cantidad')) {
                                    cantidad.max = elegida.getAttribute('data-cantidad');
                                } else {
                                    cantidad.removeAttribute('max');
                                }
                            }

                            actualizarOrigenes();
                        </script>
                        <?PHP } ?>
                    </div>
                </section>
            </div>
        </div>
        <?PHP } ?>

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading tab-bg-primary">
                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab" href="#recibidas">Me piden (otras tiendas)</a></li>
                            <li><a data-toggle="tab" href="#enviadas">Pedí (mi tienda)</a></li>
                        </ul>
                    </header>
                    <div class="panel-body">
                        <div class="tab-content">
                            <?PHP foreach (array('recibidas' => $trasladosRecibidos, 'enviadas' => $trasladosEnviados) as $pestana => $traslados) { ?>
                            <div id="<?PHP echo $pestana; ?>" class="tab-pane<?PHP echo $pestana == 'recibidas' ? ' active' : ''; ?>">
                                <?PHP if (empty($traslados)) { ?>
                                    <p class="text-muted" style="padding: 15px;">No hay traslados todavía.</p>
                                <?PHP } else { ?>
                                <div style="overflow-x: auto;">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Producto</th>
                                        <th>Cant.</th>
                                        <th><?PHP echo $pestana == 'recibidas' ? 'Para' : 'Pedido a'; ?></th>
                                        <th>Estado</th>
                                        <th>Historial</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?PHP foreach ($traslados as $t) {
                                        $etiqueta = isset($etiquetasEstado[$t['estado']]) ? $etiquetasEstado[$t['estado']] : array('label-default', $t['estado']); ?>
                                        <tr>
                                            <td><?PHP echo $t['idTraslado']; ?></td>
                                            <td>
                                                <img src="<?PHP echo $urlViews . $t['imagen']; ?>" width="40" height="40" style="border-radius: 4px;">
                                                <?PHP echo htmlspecialchars($t['nombreProducto']); ?>
                                                <?PHP if ($t['motivo'] != '') { ?><br><small class="text-muted">Motivo: <?PHP echo htmlspecialchars($t['motivo']); ?></small><?PHP } ?>
                                            </td>
                                            <td><strong><?PHP echo (int) $t['cantidad']; ?></strong></td>
                                            <td><?PHP echo htmlspecialchars($pestana == 'recibidas' ? $t['sucursalDestino'] : $t['sucursalOrigen']); ?></td>
                                            <td>
                                                <span class="label <?PHP echo $etiqueta[0]; ?>"><?PHP echo $etiqueta[1]; ?></span>
                                                <?PHP if ($t['motivoRechazo'] != '') { ?><br><small class="text-danger"><?PHP echo htmlspecialchars($t['motivoRechazo']); ?></small><?PHP } ?>
                                            </td>
                                            <td><small><?PHP echo implode('<br>', array_map('htmlspecialchars', historialTraslado($t))); ?></small></td>
                                            <td>
                                                <?PHP
                                                if ($pestana == 'recibidas') {
                                                    if ($t['estado'] == 'Pendiente' && $esAdministrador) {
                                                        botonAccion($t['idTraslado'], 'aprobar', '<i class="icon_check"></i> Aprobar', 'btn-success', '¿Aprobar el traslado de ' . (int) $t['cantidad'] . ' unidades?');
                                                        ?>
                                                        <form action="AccionTraslado.php" method="post" style="display:inline-block; margin:2px;" onsubmit="return confirm('¿Rechazar esta solicitud?');">
                                                            <input type="hidden" name="idTraslado" value="<?PHP echo $t['idTraslado']; ?>">
                                                            <input type="hidden" name="accion" value="rechazar">
                                                            <input type="text" name="motivoRechazo" class="form-control input-sm" placeholder="Motivo (opcional)" maxlength="255" style="display:inline-block; width:150px;">
                                                            <button type="submit" class="btn btn-sm btn-danger"><i class="icon_close"></i> Rechazar</button>
                                                        </form>
                                                        <?PHP
                                                    } elseif ($t['estado'] == 'Pendiente') {
                                                        echo '<small class="text-muted">Esperando aprobación de un administrador</small>';
                                                    } elseif ($t['estado'] == 'Aprobada') {
                                                        botonAccion($t['idTraslado'], 'enviar', '<i class="arrow_right"></i> Marcar como enviado', 'btn-primary', '¿Ya enviaste ' . (int) $t['cantidad'] . ' unidades? Se descontarán de tu stock.');
                                                    }
                                                } else {
                                                    if ($t['estado'] == 'EnCamino') {
                                                        botonAccion($t['idTraslado'], 'recibir', '<i class="icon_check_alt2"></i> Confirmar que llegó', 'btn-success', '¿Llegaron las ' . (int) $t['cantidad'] . ' unidades? Se sumarán a tu stock.');
                                                    } elseif (in_array($t['estado'], array('Pendiente', 'Aprobada')) && $esAdministrador) {
                                                        botonAccion($t['idTraslado'], 'cancelar', '<i class="icon_close_alt2"></i> Cancelar', 'btn-default', '¿Cancelar esta solicitud?');
                                                    }
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?PHP } ?>
                                    </tbody>
                                </table>
                                </div>
                                <?PHP } ?>
                            </div>
                            <?PHP } ?>
                        </div>
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
