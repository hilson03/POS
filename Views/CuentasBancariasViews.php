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
                <h3 class="page-header"><i class="fa fa-university"></i> CUENTAS BANCARIAS</h3>
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
                    <header class="panel-heading">Registrar nueva cuenta</header>
                    <div class="panel-body">
                        <p class="help-block" style="margin-top:0;">
                            Cuentas de la tienda donde los clientes pueden transferir. Al cobrar por transferencia el vendedor elige
                            en cuál llegó el dinero, para poder cuadrar después con el estado de cuenta de cada banco.
                        </p>
                        <form class="form-inline" action="RegistroCuentaBancaria.php" method="post">
                            <div class="form-group">
                                <input class="form-control input-lg" name="banco" placeholder="Banco (ej: BAC Credomatic)" maxlength="60" required>
                            </div>
                            <div class="form-group">
                                <input class="form-control input-lg" name="numero" placeholder="Número de cuenta" maxlength="40" required>
                            </div>
                            <div class="form-group">
                                <input class="form-control input-lg" name="titular" placeholder="A nombre de (opcional)" maxlength="100">
                            </div>
                            <button name="nueva_cuenta" type="submit" class="btn btn-primary btn-lg">
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
                    <header class="panel-heading">Cuentas registradas</header>
                    <div class="panel-body">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><i class="fa fa-university"></i> BANCO</th>
                                <th><i class="icon_creditcard"></i> NÚMERO DE CUENTA</th>
                                <th><i class="icon_profile"></i> A NOMBRE DE</th>
                                <th><i class="icon_check_alt2"></i> ESTADO</th>
                                <th><i class="icon_cog"></i> ACCIONES</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?PHP if (empty($cuentas)) { ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Todavía no hay cuentas. Mientras no registres al menos una, no se podrá cobrar por transferencia.
                                    </td>
                                </tr>
                            <?PHP } ?>
                            <?PHP foreach ($cuentas as $cuenta) { ?>
                                <tr>
                                    <td><?PHP echo htmlspecialchars($cuenta['banco']); ?></td>
                                    <td><?PHP echo htmlspecialchars($cuenta['numero']); ?></td>
                                    <td><?PHP echo htmlspecialchars($cuenta['titular']); ?></td>
                                    <td>
                                        <span class="label <?PHP echo $cuenta['estado'] == 'Activo' ? 'label-success' : 'label-default'; ?>">
                                            <?PHP echo $cuenta['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#c<?PHP echo $cuenta['idCuenta']; ?>" role="button" class="btn btn-success" data-toggle="modal">
                                            <i class="fa fa-pencil" title="Editar"></i></a>
                                    </td>
                                </tr>

                                <div id="c<?PHP echo $cuenta['idCuenta']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                    <form class="form-validate form-horizontal" action="RegistroCuentaBancaria.php" method="post">
                                        <input type="hidden" name="idCuenta" value="<?PHP echo $cuenta['idCuenta']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h3 align="center">Editar cuenta</h3>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label class="control-label col-lg-3">Banco:</label>
                                                        <div class="col-lg-9">
                                                            <input class="form-control input-lg" name="banco" maxlength="60" required
                                                                   value="<?PHP echo htmlspecialchars($cuenta['banco']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-lg-3">Número:</label>
                                                        <div class="col-lg-9">
                                                            <input class="form-control input-lg" name="numero" maxlength="40" required
                                                                   value="<?PHP echo htmlspecialchars($cuenta['numero']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-lg-3">A nombre de:</label>
                                                        <div class="col-lg-9">
                                                            <input class="form-control input-lg" name="titular" maxlength="100"
                                                                   value="<?PHP echo htmlspecialchars($cuenta['titular']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-lg-3">Estado:</label>
                                                        <div class="col-lg-9">
                                                            <select class="form-control input-lg" name="estado">
                                                                <option value="Activo"<?PHP echo $cuenta['estado'] == 'Activo' ? ' selected' : ''; ?>>Activo</option>
                                                                <option value="Inactivo"<?PHP echo $cuenta['estado'] == 'Inactivo' ? ' selected' : ''; ?>>Inactivo</option>
                                                            </select>
                                                            <small class="help-block">Una cuenta inactiva ya no aparece al cobrar; las ventas anteriores la siguen mostrando.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><strong>Cerrar</strong></button>
                                                    <button name="update_cuenta" type="submit" class="btn btn-primary"><strong>Guardar</strong></button>
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
