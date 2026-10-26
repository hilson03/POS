<!DOCTYPE html>
<html lang="es">
<?php
include('Head.php');

// opciones del banco para una cuenta: los activos, y ademas el suyo si esta inactivo (para no perderlo al editar)
function opcionesBanco($bancos, $idBancoActual = 0)
{
    $html = '<option value="">Elige el banco...</option>';
    foreach ($bancos as $banco) {
        if ($banco['estado'] != 'Activo' && (int) $banco['idBanco'] !== (int) $idBancoActual) {
            continue;
        }
        $html .= '<option value="' . (int) $banco['idBanco'] . '"' . ((int) $banco['idBanco'] === (int) $idBancoActual ? ' selected' : '') . '>'
            . htmlspecialchars($banco['nombre']) . ($banco['estado'] != 'Activo' ? ' (inactivo)' : '') . '</option>';
    }
    return $html;
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
                            Se pueden registrar varias cuentas del mismo banco; lo que no se puede es repetir la misma cuenta.
                        </p>
                        <form class="form-inline" action="RegistroCuentaBancaria.php" method="post">
                            <div class="form-group">
                                <select class="form-control input-lg" name="idBanco" required>
                                    <?PHP echo opcionesBanco($bancos); ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input class="form-control input-lg campo-cuenta" name="numero" placeholder="Número de cuenta (solo números)"
                                       maxlength="30" inputmode="numeric" pattern="[0-9]{4,30}" required
                                       title="Solo números, sin guiones ni espacios (de 4 a 30 dígitos).">
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
                                            <?PHP echo htmlspecialchars((string) $cuenta['estado']); ?>
                                        </span>
                                        <?PHP if ($cuenta['estadoBanco'] != 'Activo') { ?>
                                            <small class="text-muted">(banco inactivo: no aparece al cobrar)</small>
                                        <?PHP } ?>
                                    </td>
                                    <td>
                                        <a href="#c<?PHP echo (int) $cuenta['idCuenta']; ?>" role="button" class="btn btn-success" data-toggle="modal">
                                            <i class="fa fa-pencil" title="Editar"></i></a>
                                    </td>
                                </tr>

                                <div id="c<?PHP echo (int) $cuenta['idCuenta']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                    <form class="form-validate form-horizontal" action="RegistroCuentaBancaria.php" method="post">
                                        <input type="hidden" name="idCuenta" value="<?PHP echo (int) $cuenta['idCuenta']; ?>">
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
                                                            <select class="form-control input-lg" name="idBanco" required>
                                                                <?PHP echo opcionesBanco($bancos, $cuenta['idBanco']); ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-lg-3">Número:</label>
                                                        <div class="col-lg-9">
                                                            <input class="form-control input-lg campo-cuenta" name="numero" maxlength="30" inputmode="numeric"
                                                                   pattern="[0-9]{4,30}" required title="Solo números, sin guiones ni espacios (de 4 a 30 dígitos)."
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

        <div class="row">
            <div class="col-lg-12">
                <section class="panel">
                    <header class="panel-heading">Bancos</header>
                    <div class="panel-body">
                        <p class="help-block" style="margin-top:0;">
                            Lista de bancos que se eligen al registrar una cuenta. Cada banco aparece una sola vez.
                            Si un banco no se usa, desactívalo y deja de salir en la lista.
                        </p>
                        <form class="form-inline" action="RegistroCuentaBancaria.php" method="post" style="margin-bottom:15px;">
                            <div class="form-group">
                                <input class="form-control input-lg" name="nombreBanco" placeholder="Nombre del banco" maxlength="60" required>
                            </div>
                            <button name="nuevo_banco" type="submit" class="btn btn-primary btn-lg">
                                <i class="fa fa-plus"></i> Agregar banco
                            </button>
                        </form>
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th><i class="fa fa-university"></i> BANCO</th>
                                <th><i class="icon_creditcard"></i> CUENTAS</th>
                                <th><i class="icon_check_alt2"></i> ESTADO</th>
                                <th><i class="icon_cog"></i> ACCIONES</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?PHP foreach ($bancos as $banco) { ?>
                                <tr>
                                    <td><?PHP echo htmlspecialchars($banco['nombre']); ?></td>
                                    <td><?PHP echo (int) $banco['cuentas']; ?></td>
                                    <td>
                                        <span class="label <?PHP echo $banco['estado'] == 'Activo' ? 'label-success' : 'label-default'; ?>">
                                            <?PHP echo $banco['estado'] == 'Activo' ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="#b<?PHP echo (int) $banco['idBanco']; ?>" role="button" class="btn btn-success" data-toggle="modal">
                                            <i class="fa fa-pencil" title="Editar"></i></a>
                                    </td>
                                </tr>

                                <div id="b<?PHP echo (int) $banco['idBanco']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                    <form class="form-validate form-horizontal" action="RegistroCuentaBancaria.php" method="post">
                                        <input type="hidden" name="idBanco" value="<?PHP echo (int) $banco['idBanco']; ?>">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                    <h3 align="center">Editar banco</h3>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label class="control-label col-lg-3">Nombre:</label>
                                                        <div class="col-lg-9">
                                                            <input class="form-control input-lg" name="nombreBanco" maxlength="60" required
                                                                   value="<?PHP echo htmlspecialchars($banco['nombre']); ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-lg-3">Estado:</label>
                                                        <div class="col-lg-9">
                                                            <select class="form-control input-lg" name="estado">
                                                                <option value="Activo"<?PHP echo $banco['estado'] == 'Activo' ? ' selected' : ''; ?>>Activo</option>
                                                                <option value="Inactivo"<?PHP echo $banco['estado'] != 'Activo' ? ' selected' : ''; ?>>Inactivo</option>
                                                            </select>
                                                            <small class="help-block">Un banco inactivo no sale al registrar cuentas, y sus cuentas no se pueden elegir al cobrar.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><strong>Cerrar</strong></button>
                                                    <button name="update_banco" type="submit" class="btn btn-primary"><strong>Guardar</strong></button>
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

<script>
    // numero de cuenta: no deja escribir letras, guiones ni espacios (el servidor lo vuelve a revisar)
    document.querySelectorAll('.campo-cuenta').forEach(function (campo) {
        campo.addEventListener('input', function () {
            var limpio = campo.value.replace(/[^0-9]/g, '');
            if (limpio !== campo.value) { campo.value = limpio; }
        });
    });
</script>

</body>
</html>
