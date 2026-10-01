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
                <h3 class="page-header"><i class="icon_building"></i> SUCURSALES</h3>
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
                    <header class="panel-heading">Registrar nueva sucursal</header>
                    <div class="panel-body">
                        <form class="form-inline" action="RegistroSucursal.php" method="post">
                            <div class="form-group">
                                <input class="form-control input-lg" name="nombre" placeholder="Nombre de la sucursal" maxlength="100" required>
                            </div>
                            <div class="form-group">
                                <input class="form-control input-lg" name="direccion" placeholder="Dirección" maxlength="200">
                            </div>
                            <div class="form-group">
                                <input class="form-control input-lg" name="telefono" placeholder="Teléfono" maxlength="50">
                            </div>
                            <button name="nueva_sucursal" type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-plus"></i> Registrar
                            </button>
                        </form>
                    </div>
                </section>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">Sucursales registradas</header>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><i class="icon_building"></i> NOMBRE</th>
                                <th><i class="icon_pin_alt"></i> DIRECCIÓN</th>
                                <th><i class="icon_phone"></i> TELÉFONO</th>
                                <th><i class="icon_check_alt2"></i> ESTADO</th>
                                <th><i class="icon_cog"></i> ACCIONES</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?PHP foreach ($sucursales as $sucursal) { ?>
                                <tr>
                                    <td><?PHP echo htmlspecialchars($sucursal['nombre']); ?></td>
                                    <td><?PHP echo htmlspecialchars($sucursal['direccion']); ?></td>
                                    <td><?PHP echo htmlspecialchars($sucursal['telefono']); ?></td>
                                    <td>
                                        <span class="label <?PHP echo $sucursal['estado'] == 'Activo' ? 'label-success' : 'label-default'; ?>">
                                            <?PHP echo $sucursal['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#s<?PHP echo $sucursal['idSucursal']; ?>" role="button" class="btn btn-success" data-toggle="modal">
                                            <i class="icon_pencil-edit"></i></a>
                                    </td>
                                </tr>

                                <div id="s<?PHP echo $sucursal['idSucursal']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                    <form class="form-validate form-horizontal" action="RegistroSucursal.php" method="post">
                                        <input type="hidden" name="idSucursal" value="<?PHP echo $sucursal['idSucursal']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h3 align="center">Editar sucursal</h3>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label class="control-label col-lg-3">Nombre:</label>
                                                        <div class="col-lg-9">
                                                            <input class="form-control input-lg" name="nombre" maxlength="100" required
                                                                   value="<?PHP echo htmlspecialchars($sucursal['nombre']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-lg-3">Dirección:</label>
                                                        <div class="col-lg-9">
                                                            <input class="form-control input-lg" name="direccion" maxlength="200"
                                                                   value="<?PHP echo htmlspecialchars($sucursal['direccion']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-lg-3">Teléfono:</label>
                                                        <div class="col-lg-9">
                                                            <input class="form-control input-lg" name="telefono" maxlength="50"
                                                                   value="<?PHP echo htmlspecialchars($sucursal['telefono']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-lg-3">Estado:</label>
                                                        <div class="col-lg-9">
                                                            <select class="form-control input-lg" name="estado">
                                                                <option value="Activo"<?PHP echo $sucursal['estado'] == 'Activo' ? ' selected' : ''; ?>>Activo</option>
                                                                <option value="Inactivo"<?PHP echo $sucursal['estado'] == 'Inactivo' ? ' selected' : ''; ?>>Inactivo</option>
                                                            </select>
                                                            <small class="help-block">Una sucursal inactiva no aparece para vender ni para asignar usuarios.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><strong>Cerrar</strong></button>
                                                    <button name="update_sucursal" type="submit" class="btn btn-primary"><strong>Guardar</strong></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?PHP } ?>
                            </tbody>
                        </table>
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
