<div class="modal-dialog" style="width: 720px; max-width: 96%;"><?PHP /* mas ancho que el normal: total, efectivo y cambio caben completos */ ?>
    <div class="modal-content">
        <div class="modal-header">
            <form class="form-validate form-horizontal" name="calculadora<?PHP echo isset($formaPago) ? $formaPago : 'Efectivo'; ?>" id="formCobro<?PHP echo isset($formaPago) ? $formaPago : 'Efectivo'; ?>" action="RegistrarPreventa.php" method="post">
                <table id="dataTables-example">

                    <?PHP
                    // el mismo formulario sirve para cada forma de pago; solo cambian el encabezado y los campos del dinero
                    $esTarjeta = isset($formaPago) && $formaPago == 'Tarjeta';
                    $esTransferencia = isset($formaPago) && $formaPago == 'Transferencia';
                    $esMixto = isset($formaPago) && $formaPago == 'Mixto';
                    $sinEfectivo = $esTarjeta || $esTransferencia || $esMixto;   // sin los campos de efectivo/cambio de siempre
                    $encabezados = array(
                        'Efectivo'      => array('#2e9d5b', '<i class="fa fa-money"></i> COBRO EN EFECTIVO'),
                        'Tarjeta'       => array('#3b6fd8', '<i class="fa fa-credit-card"></i> COBRO CON TARJETA'),
                        'Transferencia' => array('#8a4fd0', '<i class="fa fa-university"></i> COBRO POR TRANSFERENCIA'),
                        'Mixto'         => array('#e08a1e', '<i class="fa fa-random"></i> PAGO MIXTO'),
                    );
                    $modoCobro = $esMixto ? 'Mixto' : ($esTarjeta ? 'Tarjeta' : ($esTransferencia ? 'Transferencia' : 'Efectivo'));
                    ?>
                    <input type="hidden" name="formaPago" value="<?PHP echo $modoCobro; ?>">
                    <tr>
                        <th colspan="5" align="Center">
                            <span style="display:inline-block; padding:6px 14px; border-radius:4px; color:#fff; font-size:16px; white-space:nowrap;
                                         background: <?PHP echo $encabezados[$modoCobro][0]; ?>;">
                                <?PHP echo $encabezados[$modoCobro][1]; ?>
                            </span>
                        </th>
                        <th>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <label>
                                CLIENTE :
                            </label>
                        </td>
                        <td colspan="7">
                            <?PHP
                            // cliente de la lista de Clientes (buscador por DNI o nombre) o nuevo con DNI, nombre y apellido;
                            // en efectivo es opcional (sin cliente la venta sale a "Consumidor Final")
                            ?>
                            <style>
                                .cliente-cobro { margin-bottom: 12px; }
                                .cliente-lista { border: 1px solid #ccd; border-top: 0; border-radius: 0 0 4px 4px; max-height: 220px; overflow-y: auto; background: #fff; }
                                .cliente-lista:empty { display: none; }
                                .cliente-opcion { padding: 8px 12px; cursor: pointer; border-top: 1px solid #eef; font-size: 14px; }
                                .cliente-opcion:hover { background: #eef4ff; }
                                .cliente-opcion.sin-dni { cursor: not-allowed; color: #999; background: #fafafa; }
                                .cliente-opcion.nuevo { color: #1f6b3a; font-weight: bold; }
                                .cliente-elegido { padding: 10px 12px; border-radius: 6px; background: #e3f4e9; border: 1px solid #a9dbb9; font-size: 15px; }
                                .cliente-nuevo { padding: 10px 12px; border-radius: 6px; background: #f6f8fc; border: 1px solid #ccd; }
                                .cliente-nuevo .form-control { margin-bottom: 6px; }
                                .cliente-nuevo-titulo { font-weight: bold; margin-bottom: 6px; }
                                /* total, efectivo y cambio: antes median 62px y "25000.00" se veia cortado como "2500" */
                                .modal [name=ingreso1], .modal [name=ingreso2], .modal [name=resultado] { min-width: 115px; padding-left: 8px; padding-right: 8px; }
                            </style>
                            <div class="col-lg-10 cliente-cobro" data-obligatorio="<?PHP echo $modoCobro == 'Efectivo' ? '0' : '1'; ?>">
                                <input type="hidden" name="ci" class="cliente-ci" value="">
                                <div class="cliente-buscar">
                                    <input class="form-control input-lg cliente-texto" type="text" autocomplete="off"
                                           placeholder="Buscar por DNI o nombre<?PHP echo $modoCobro == 'Efectivo' ? ' (opcional)' : ''; ?>"
                                           oninput="buscarClienteCobro(this)">
                                    <div class="cliente-lista"></div>
                                </div>
                                <div class="cliente-elegido" hidden>
                                    <i class="fa fa-user"></i> <b class="cliente-nombre"></b> &middot; DNI <span class="cliente-dni"></span>
                                    <button type="button" class="btn btn-default btn-xs" onclick="quitarClienteCobro(this)">&times; Cambiar</button>
                                </div>
                                <div class="cliente-nuevo" hidden>
                                    <div class="cliente-nuevo-titulo"><i class="fa fa-user-plus"></i> Cliente nuevo (se guarda en Clientes)</div>
                                    <input class="form-control" type="text" name="dniNuevo" maxlength="100" placeholder="DNI / RTN" disabled>
                                    <input class="form-control" type="text" name="nombreNuevo" maxlength="200" placeholder="Nombre" disabled>
                                    <input class="form-control" type="text" name="apellidoNuevo" maxlength="200" placeholder="Apellido" disabled>
                                    <button type="button" class="btn btn-default btn-xs" onclick="quitarClienteCobro(this)">Cancelar</button>
                                </div>
                                <?PHP if ($modoCobro == 'Efectivo') { ?>
                                    <small class="help-block">Si no elige cliente, la venta sale a <b>Consumidor Final</b>.</small>
                                <?PHP } ?>
                            </div>
                        </td>
                    </tr>


                    <tr>
                        <td><label>TOTAL A PAGAR :</label></td>
                        <td<?PHP echo $sinEfectivo ? ' colspan="5"' : ''; ?>>
                            <div class="<?PHP echo $sinEfectivo ? 'col-lg-6' : 'col-lg-20'; ?>">
                                <input class="form-control input-lg m-bot15" type="text" required name="ingreso1"
                                       readonly value="<?PHP echo dinero($preventa); ?>" onKeyUp="Suma()">

                            </div>
                        </td>


                        <?PHP if ($sinEfectivo) { ?>
                        <!-- tarjeta y transferencia: se cobra el total exacto (sin efectivo ni cambio) y se anotan los datos del comprobante -->
                        <input type="hidden" name="ingreso2" value="<?PHP echo dinero($preventa); ?>">
                        <input type="hidden" name="resultado" value="0.00">

                    </tr>
                        <?PHP } ?>
                        <?PHP if ($esMixto) { ?>
                    <!-- pago mixto: hasta 5 pagos con tarjeta o transferencia (cada uno con su monto y sus datos); el efectivo es lo que falta -->
                    <tr>
                        <td colspan="6">
                            <style>
                                .mixto-lista { margin: 0 15px; }
                                .mixto-pago { position: relative; margin-bottom: 8px; padding: 8px 36px 6px 12px; border: 1px solid #9aa0a8; border-radius: 6px; background: #fff; }
                                .mixto-pago.tarjeta { border-left: 5px solid #3b6fd8; }
                                .mixto-pago.transferencia { border-left: 5px solid #8a4fd0; }
                                .mixto-pago .mixto-titulo { font-weight: 700; font-size: 14px; margin-bottom: 4px; }
                                .mixto-pago.tarjeta .mixto-titulo { color: #3b6fd8; }
                                .mixto-pago.transferencia .mixto-titulo { color: #8a4fd0; }
                                .mixto-quitar { position: absolute; top: 6px; right: 8px; border: none; background: none; font-size: 20px; line-height: 1; color: #a12a1f; cursor: pointer; }
                                .mixto-fila { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 6px; }
                                .mixto-fila > div { flex: 1 1 130px; }
                                .mixto-fila small { color: #5a6068; font-weight: 600; }
                                .mixto-pago input[type=checkbox] { width: 18px; height: 18px; vertical-align: middle; margin: 0 6px 0 0; }
                                .mixto-agregar { margin: 2px 15px 10px; display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
                                .mixto-efectivo { margin: 0 15px 10px; padding: 8px 12px; border: 1px solid #9aa0a8; border-left: 5px solid #2e9d5b; border-radius: 6px; }
                                .mixto-efectivo .mixto-titulo { font-weight: 700; color: #2e9d5b; margin-bottom: 4px; }
                                .mixto-resumen, .mixto-aviso { margin: 4px 15px 10px; padding: 8px 12px; border-radius: 6px; font-weight: 700; font-size: 14px; }
                                .mixto-resumen.falta { background: #fdecea; color: #a12a1f; border: 1px solid #f3b8b1; }
                                .mixto-resumen.listo { background: #e3f4e9; color: #1f6b3a; border: 1px solid #a9dbb9; }
                                .mixto-aviso { display: none; background: #fcf3dc; color: #7a5a00; border: 1px solid #f0d58a; font-weight: 600; }
                            </style>

                            <div class="mixto-lista" id="listaPagos"></div>

                            <div class="mixto-agregar">
                                <button type="button" class="btn btn-primary btn-sm" id="agregarTarjeta" onclick="agregarPago('tarjeta')">
                                    <i class="fa fa-credit-card"></i> + Tarjeta</button>
                                <button type="button" class="btn btn-sm" id="agregarTransferencia" style="background:#8a4fd0; color:#fff;" onclick="agregarPago('transferencia')"<?PHP echo empty($cuentasTransferencia) ? ' disabled title="No hay cuentas bancarias registradas"' : ''; ?>>
                                    <i class="fa fa-university"></i> + Transferencia</button>
                                <small class="text-muted" id="cupoPagos">Hasta 5 pagos con tarjeta o transferencia.</small>
                                <?PHP if (empty($cuentasTransferencia)) { ?>
                                    <small class="text-muted">Para transferencias, registra las cuentas en Configuración → Cuentas bancarias.</small>
                                <?PHP } ?>
                            </div>

                            <div class="mixto-efectivo">
                                <div class="mixto-titulo"><i class="fa fa-money"></i> Efectivo (lo que falta)</div>
                                <div class="mixto-fila">
                                    <div><small>Efectivo a cobrar</small>
                                        <input class="form-control input-lg" type="text" id="restoEfectivo" readonly value="<?PHP echo dinero($preventa); ?>"></div>
                                    <div><small>Recibido</small>
                                        <input class="form-control input-lg" type="text" inputmode="decimal" name="recibidoMixto" id="recibidoMixto" autocomplete="off" placeholder="0.00" oninput="validarMixto()"></div>
                                    <div><small>Cambio</small>
                                        <input class="form-control input-lg" type="text" id="cambioMixto" readonly value="0.00"></div>
                                </div>
                            </div>

                            <div class="mixto-aviso" id="avisoRepetido"></div>
                            <div class="mixto-resumen falta" id="resumenMixto">Agrega al menos un pago con tarjeta o transferencia.</div>

                            <!-- opciones de la cuenta destino, para copiarlas en cada transferencia que se agregue -->
                            <template id="opcionesCuentas">
                                <?PHP if (count($cuentasTransferencia) > 1) { ?>
                                    <option value="">-- ¿A cuál cuenta llegó? --</option>
                                <?PHP } ?>
                                <?PHP foreach ($cuentasTransferencia as $cuenta) { ?>
                                    <option value="<?PHP echo htmlspecialchars((string) $cuenta['idCuenta']); ?>"><?PHP echo htmlspecialchars($cuenta['banco'] . ' - ' . $cuenta['numero']); ?></option>
                                <?PHP } ?>
                            </template>
                        </td>
                    </tr>
                        <?PHP } elseif ($esTransferencia) { ?>
                    <?PHP if (empty($cuentasTransferencia)) { ?>
                    <tr>
                        <td colspan="6">
                            <div class="alert alert-warning" style="margin:6px 0;">
                                <i class="fa fa-exclamation-triangle"></i> No hay cuentas bancarias registradas. El administrador debe agregarlas en
                                <b>Configuración → Cuentas bancarias</b> antes de cobrar por transferencia.
                            </div>
                        </td>
                    </tr>
                    <?PHP } else { ?>
                    <tr>
                        <td><label>N° REFERENCIA :</label></td>
                        <td colspan="5">
                            <div class="col-lg-10">
                                <input class="form-control input-lg m-bot15" type="text" required name="referenciaPago" id="referenciaPago"
                                       maxlength="30" autocomplete="off" placeholder="Ej: 123456789" oninput="validarTransferencia()">
                                <small class="help-block" style="margin-top:-10px;">Número de referencia o de comprobante que aparece en la transferencia del cliente.</small>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label>CUENTA DESTINO :</label></td>
                        <td colspan="5">
                            <div class="col-lg-10">
                                <select class="form-control input-lg m-bot15" name="idCuentaDestino" id="idCuentaDestino" required onchange="validarTransferencia()">
                                    <?PHP if (count($cuentasTransferencia) > 1) { ?>
                                        <option value="">-- ¿A cuál cuenta llegó? --</option>
                                    <?PHP } ?>
                                    <?PHP foreach ($cuentasTransferencia as $cuenta) { ?>
                                        <option value="<?PHP echo htmlspecialchars((string) $cuenta['idCuenta']); ?>">
                                            <?PHP echo htmlspecialchars($cuenta['banco'] . ' - ' . $cuenta['numero'] . ($cuenta['titular'] != '' ? ' (' . $cuenta['titular'] . ')' : '')); ?>
                                        </option>
                                    <?PHP } ?>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label>TRANSFIRIÓ :</label></td>
                        <td colspan="5">
                            <div class="col-lg-10">
                                <input class="form-control input-lg m-bot15" type="text" name="nombreTransfirio" maxlength="100" autocomplete="off"
                                       placeholder="Opcional: nombre de quien hizo la transferencia">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div style="margin:0 15px 10px; padding:10px 12px; background:#f3ecfb; border:1px solid #d9c6f1; border-radius:4px;">
                                <label style="font-weight:600; color:#4a2a7a; cursor:pointer; margin:0;">
                                    <input type="checkbox" name="verificado" id="verificado" value="1" required onchange="validarTransferencia()"
                                           style="width:18px; height:18px; vertical-align:middle; margin:0 6px 0 0;">
                                    Verifiqué que el dinero llegó a la cuenta (no solo vi la captura del cliente)
                                </label>
                            </div>
                        </td>
                    </tr>
                    <?PHP } ?>
                        <?PHP } elseif ($esTarjeta) { ?>
                    <!-- datos del comprobante en filas propias para que se lean completos -->
                    <tr>
                        <td><label>N° AUTORIZACIÓN :</label></td>
                        <td colspan="5">
                            <div class="col-lg-10">
                                <input class="form-control input-lg m-bot15" type="text" required name="referenciaPago" id="referenciaPago"
                                       maxlength="20" autocomplete="off" placeholder="Ej: 482913" oninput="validarTarjeta()">
                                <small class="help-block" style="margin-top:-10px;">
                                    Código <b>"AUT"</b> que imprime el voucher del POS del banco al aprobar el cobro.
                                    <b>No es el número de la tarjeta.</b>
                                </small>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label>ÚLTIMOS 4 DÍG. :</label></td>
                        <td colspan="5">
                            <div class="col-lg-4">
                                <input class="form-control input-lg m-bot15" type="text" name="ultimos4" id="ultimos4"
                                       maxlength="4" inputmode="numeric" pattern="[0-9]{4}" autocomplete="off" placeholder="Opcional"
                                       oninput="validarTarjeta()">
                                <small class="help-block" style="margin-top:-10px;">Los 4 que se ven en el voucher: ************4521</small>
                            </div>
                        </td>
                    </tr>
                        <?PHP } else { ?>
                        <td><label>EFECTIVO :</label></td>
                        <td>
                            <div class="col-lg-20">
                                <input class="form-control input-lg m-bot15" type="text" required name="ingreso2"
                                       value="" onKeyUp="Suma()">

                            </div>
                        </td>


                        <td><label>CAMBIO :</label></td>
                        <td>
                            <div class="col-lg-20">
                                <input class="form-control input-lg m-bot15" type="text" required name="resultado"
                                       readonly onKeyUp="Suma()">

                            </div>
                        </td>
                    </tr>
                        <?PHP } ?>
                    <tr>
                        <td colspan="5"></td>
                        <td colspan="1">
                            <button name="registrar" type="submit" class="btn btn-success"> ACEPTAR</button>
                        </td>
                    </tr>


                </table>
            </form>

        </div>
    </div>
</div>


<script>

    // cada cobro trabaja solo con su formulario (pueden quedar varios cargados en la pantalla de Ventas)
    function formCobro(modo) {
        return document.getElementById('formCobro' + modo);
    }

    formCobro('<?PHP echo $modoCobro; ?>').registrar.disabled = true;
    // (antes aqui se aplicaba DataTables a la tabla del formulario: no es una lista de datos y fallaba al abrir
    // el cobro desde Ventas, cortando este script; por eso el pago mixto mandaba los pagos sin numero)

    /*
     * Cliente del cobro: buscador en la lista de Clientes (DNI o nombre, 3 letras o mas). Al elegir uno queda su DNI
     * en el campo oculto "ci"; si no esta, se registra ahi mismo con DNI, nombre y apellido.
     */
    var esperaBusquedaCliente = null;
    function buscarClienteCobro(campo) {
        var caja = campo.closest('.cliente-cobro');
        var lista = caja.querySelector('.cliente-lista');
        var texto = campo.value.trim();
        clearTimeout(esperaBusquedaCliente);
        if (texto.length < 3) {
            lista.innerHTML = '';
            return;
        }
        esperaBusquedaCliente = setTimeout(function () {
            fetch('SearchContact.php', {
                method: 'POST', credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'texto=' + encodeURIComponent(texto)
            }).then(function (r) { return r.json(); }).then(function (clientes) {
                if (campo.value.trim() !== texto) { return; }   // ya escribio otra cosa
                lista.innerHTML = '';
                clientes.forEach(function (cliente) {
                    var fila = document.createElement('div');
                    fila.className = 'cliente-opcion' + (cliente.ci === '' ? ' sin-dni' : '');
                    var nombre = document.createElement('b');
                    nombre.textContent = cliente.nombre || '(sin nombre)';
                    fila.appendChild(nombre);
                    fila.appendChild(document.createTextNode(cliente.ci === '' ? ' · sin DNI (agrégalo en Clientes para elegirlo)' : ' · DNI ' + cliente.ci));
                    if (cliente.ci !== '') {
                        fila.addEventListener('click', function () { elegirClienteCobro(caja, cliente); });
                    }
                    lista.appendChild(fila);
                });
                var nuevo = document.createElement('div');
                nuevo.className = 'cliente-opcion nuevo';
                nuevo.textContent = clientes.length ? '+ No es ninguno: registrar cliente nuevo' : 'No está en Clientes. + Registrar cliente nuevo';
                nuevo.addEventListener('click', function () { clienteNuevoCobro(caja, texto); });
                lista.appendChild(nuevo);
            }).catch(function () {
                lista.innerHTML = '';
            });
        }, 250);
    }

    function mostrarParteCliente(caja, parte) {
        caja.querySelector('.cliente-buscar').hidden = parte !== 'buscar';
        caja.querySelector('.cliente-elegido').hidden = parte !== 'elegido';
        caja.querySelector('.cliente-nuevo').hidden = parte !== 'nuevo';
        // los campos del cliente nuevo solo se envian (y son obligatorios) cuando se esta registrando uno
        caja.querySelectorAll('.cliente-nuevo input').forEach(function (campo) {
            campo.disabled = parte !== 'nuevo';
            campo.required = parte === 'nuevo';
        });
    }

    function elegirClienteCobro(caja, cliente) {
        caja.querySelector('.cliente-ci').value = cliente.ci;
        caja.querySelector('.cliente-nombre').textContent = cliente.nombre;
        caja.querySelector('.cliente-dni').textContent = cliente.ci;
        caja.querySelector('.cliente-lista').innerHTML = '';
        mostrarParteCliente(caja, 'elegido');
    }

    function clienteNuevoCobro(caja, texto) {
        caja.querySelector('.cliente-ci').value = '';
        caja.querySelector('.cliente-lista').innerHTML = '';
        mostrarParteCliente(caja, 'nuevo');
        // si lo que buscaba parece un DNI, se pasa al campo DNI; si no, al nombre
        var pareceDni = /^[0-9\-\s]+$/.test(texto);
        caja.querySelector('[name=dniNuevo]').value = pareceDni ? texto.replace(/\s/g, '') : '';
        caja.querySelector('[name=nombreNuevo]').value = pareceDni ? '' : texto;
        caja.querySelector(pareceDni ? '[name=nombreNuevo]' : '[name=dniNuevo]').focus();
    }

    function quitarClienteCobro(boton) {
        var caja = boton.closest('.cliente-cobro');
        caja.querySelector('.cliente-ci').value = '';
        caja.querySelector('.cliente-texto').value = '';
        caja.querySelector('.cliente-lista').innerHTML = '';
        mostrarParteCliente(caja, 'buscar');
        caja.querySelector('.cliente-texto').focus();
    }

    // al enviar: con tarjeta, transferencia o mixto el cliente es obligatorio (el servidor lo vuelve a revisar)
    (function () {
        var form = formCobro('<?PHP echo $modoCobro; ?>');
        if (!form || form.dataset.clienteListo) { return; }
        form.dataset.clienteListo = '1';
        form.addEventListener('submit', function (evento) {
            var caja = form.querySelector('.cliente-cobro');
            var texto = caja.querySelector('.cliente-texto');
            var hayCliente = caja.querySelector('.cliente-ci').value !== '' || !caja.querySelector('.cliente-nuevo').hidden;
            if (caja.dataset.obligatorio === '1' && !hayCliente) {
                evento.preventDefault();
                texto.setCustomValidity('Elige al cliente o regístralo (con tarjeta, transferencia o mixto el cliente es obligatorio).');
                texto.reportValidity();
                texto.setCustomValidity('');
            }
        });
    })();


    // pago mixto: hasta 5 pagos con tarjeta o transferencia; el efectivo es lo que falta para el total.
    // ACEPTAR se activa cuando todo cuadra y cada pago tiene sus datos.
    var MAXIMO_PAGOS = 5;
    var siguientePago = 0;   // indice de los nombres pagos[n][...]; no se reutiliza al quitar uno

    function centavos(texto) {
        var n = parseFloat(String(texto).replace(/,/g, ''));
        return isNaN(n) ? 0 : Math.round(n * 100);
    }

    // monto para mostrar en los avisos, como se usa en Honduras: L 25,000.00
    function montoHn(valor) {
        return <?PHP echo json_encode(simboloMoneda() . ' '); ?> + Number(valor).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function agregarPago(tipo) {
        var lista = document.getElementById('listaPagos');
        if (lista.children.length >= MAXIMO_PAGOS) {
            return;
        }
        // indice unico aunque el contador no se haya inicializado (por ejemplo si el script se corto antes)
        if (typeof siguientePago !== 'number' || isNaN(siguientePago)) {
            siguientePago = lista.querySelectorAll('.mixto-pago').length + Date.now() % 100000;
        }
        var n = siguientePago++;
        var p = 'pagos[' + n + ']';
        var caja = document.createElement('div');
        caja.className = 'mixto-pago ' + tipo;
        caja.dataset.tipo = tipo;
        var campos = '<input type="hidden" name="' + p + '[tipo]" value="' + tipo + '">'
            + '<button type="button" class="mixto-quitar" title="Quitar este pago" onclick="quitarPago(this)">&times;</button>'
            + '<div class="mixto-titulo"></div>'
            + '<div class="mixto-fila">'
            + '<div><small>Monto</small><input class="form-control input-lg c-monto" type="text" inputmode="decimal" name="' + p + '[monto]" autocomplete="off" placeholder="0.00" oninput="validarMixto()"></div>';
        if (tipo == 'tarjeta') {
            campos += '<div><small>N° autorización (voucher)</small><input class="form-control input-lg c-ref" type="text" name="' + p + '[referencia]" maxlength="20" autocomplete="off" placeholder="Ej: 482913" oninput="validarMixto()"></div>'
                + '<div style="flex-basis:90px;"><small>Últimos 4</small><input class="form-control input-lg c-ult4" type="text" name="' + p + '[ultimos4]" maxlength="4" inputmode="numeric" autocomplete="off" placeholder="Opcional" oninput="validarMixto()"></div>'
                + '</div>';
        } else {
            campos += '<div><small>N° referencia</small><input class="form-control input-lg c-ref" type="text" name="' + p + '[referencia]" maxlength="30" autocomplete="off" placeholder="Ej: 123456789" oninput="validarMixto()"></div>'
                + '</div><div class="mixto-fila">'
                + '<div><small>Cuenta destino</small><select class="form-control input-lg c-cuenta" name="' + p + '[cuenta]" onchange="validarMixto()">'
                + document.getElementById('opcionesCuentas').innerHTML + '</select></div>'
                + '<div><small>Transfirió (opcional)</small><input class="form-control input-lg" type="text" name="' + p + '[nombre]" maxlength="100" autocomplete="off" placeholder="Nombre"></div>'
                + '</div>'
                + '<label style="font-weight:600; color:#4a2a7a; cursor:pointer; margin:0 0 4px;"><input type="checkbox" class="c-verif" name="' + p + '[verificado]" value="1" onchange="validarMixto()"> Verifiqué que esta transferencia llegó a la cuenta</label>';
        }
        caja.innerHTML = campos;
        lista.appendChild(caja);
        caja.querySelector('.c-monto').focus();
        validarMixto();
    }

    function quitarPago(boton) {
        boton.parentNode.remove();
        validarMixto();
    }

    function validarMixto() {
        var form = formCobro('Mixto');
        var total = centavos(form.ingreso1.value);
        var pagos = document.querySelectorAll('#listaPagos .mixto-pago');
        var cubierto = 0;
        var problema = '';
        var vistos = {};
        var repetidos = [];

        pagos.forEach(function (caja, i) {
            var numero = i + 1;
            var esTarjeta = caja.dataset.tipo == 'tarjeta';
            var nombre = esTarjeta ? 'tarjeta' : 'transferencia';
            caja.querySelector('.mixto-titulo').innerHTML = (esTarjeta ? '<i class="fa fa-credit-card"></i> ' : '<i class="fa fa-university"></i> ')
                + 'Pago ' + numero + ': ' + (esTarjeta ? 'Tarjeta' : 'Transferencia');

            var monto = centavos(caja.querySelector('.c-monto').value);
            var ref = caja.querySelector('.c-ref').value.trim();
            cubierto += monto;
            if (esTarjeta) {
                var ult4 = caja.querySelector('.c-ult4');
                ult4.value = ult4.value.replace(/[^0-9]/g, '');
            }

            if (problema === '') {
                if (monto <= 0) {
                    problema = 'Pago ' + numero + ': escribe el monto de la ' + nombre + '.';
                } else if (ref === '') {
                    problema = 'Pago ' + numero + ': escribe ' + (esTarjeta ? 'el número de autorización.' : 'la referencia de la transferencia.');
                } else if (esTarjeta && ult4.value !== '' && ult4.value.length !== 4) {
                    problema = 'Pago ' + numero + ': los últimos dígitos de la tarjeta deben ser 4.';
                } else if (!esTarjeta && caja.querySelector('.c-cuenta').value === '') {
                    problema = 'Pago ' + numero + ': elige la cuenta a la que llegó la transferencia.';
                } else if (!esTarjeta && !caja.querySelector('.c-verif').checked) {
                    problema = 'Pago ' + numero + ': marca que verificaste que la transferencia llegó.';
                }
            }

            // misma autorizacion/referencia en dos pagos del mismo tipo: casi siempre es un error al copiar (solo se avisa)
            if (ref !== '') {
                var clave = nombre + '|' + ref.toUpperCase();
                if (vistos[clave]) {
                    repetidos.push('los pagos ' + vistos[clave] + ' y ' + numero + ' tienen ' + (esTarjeta ? 'la misma autorización' : 'la misma referencia') + ' (' + ref.replace(/[<>&"]/g, '') + ')');
                } else {
                    vistos[clave] = numero;
                }
            }
        });

        var resto = total - cubierto;
        var recibido = centavos(document.getElementById('recibidoMixto').value);
        document.getElementById('restoEfectivo').value = (Math.max(resto, 0) / 100).toFixed(2);
        document.getElementById('cambioMixto').value = (resto > 0 && recibido >= resto ? (recibido - resto) / 100 : 0).toFixed(2);
        document.getElementById('recibidoMixto').disabled = resto <= 0;

        var formas = pagos.length + (resto > 0 ? 1 : 0);
        if (pagos.length === 0) {
            problema = 'Agrega al menos un pago con tarjeta o transferencia.';
        } else if (problema === '' && resto < 0) {
            problema = 'Los pagos suman ' + montoHn(-resto / 100) + ' más que el total.';
        } else if (problema === '' && resto > 0 && recibido < resto) {
            problema = 'Falta cobrar en efectivo: ' + montoHn((resto - recibido) / 100);
        } else if (problema === '' && formas < 2) {
            problema = 'Con un solo pago usa Tarjeta o Transferencia directamente.';
        }

        var lleno = pagos.length >= MAXIMO_PAGOS;
        document.getElementById('agregarTarjeta').disabled = lleno;
        var botonTransf = document.getElementById('agregarTransferencia');
        if (!botonTransf.title) {
            botonTransf.disabled = lleno;
        }
        document.getElementById('cupoPagos').textContent = lleno ? 'Llegaste al máximo de 5 pagos.' : 'Hasta 5 pagos con tarjeta o transferencia (' + pagos.length + ' de 5).';

        var aviso = document.getElementById('avisoRepetido');
        aviso.innerHTML = repetidos.length ? '<i class="fa fa-exclamation-triangle"></i> Revisa: ' + repetidos.join('; ') + '. Si es correcto, puedes continuar.' : '';
        aviso.style.display = repetidos.length ? 'block' : 'none';

        var resumen = document.getElementById('resumenMixto');
        resumen.textContent = problema !== '' ? problema : 'Todo cuadra: listo para ACEPTAR.';
        resumen.className = 'mixto-resumen ' + (problema !== '' ? 'falta' : 'listo');
        form.registrar.disabled = problema !== '';
    }
    // transferencia: ACEPTAR se activa con la referencia, la cuenta destino y la casilla de verificacion marcada
    function validarTransferencia() {
        var form = formCobro('Transferencia');
        var referencia = form.querySelector('[name=referenciaPago]').value.trim();
        var cuenta = form.querySelector('[name=idCuentaDestino]').value;
        var verificado = form.querySelector('[name=verificado]').checked;
        form.registrar.disabled = !(referencia !== '' && cuenta !== '' && verificado);
    }

    // tarjeta: ACEPTAR se activa con el numero de autorizacion (y, si se escriben, 4 digitos exactos)
    function validarTarjeta() {
        var form = formCobro('Tarjeta');
        var autorizacion = form.querySelector('[name=referenciaPago]').value.trim();
        var ultimos4 = form.querySelector('[name=ultimos4]');
        ultimos4.value = ultimos4.value.replace(/[^0-9]/g, '');
        var ultimosOk = ultimos4.value === '' || ultimos4.value.length === 4;
        form.registrar.disabled = !(autorizacion !== '' && ultimosOk);
    }

    function Suma() {
        var form = formCobro('Efectivo');

        var ingreso1 = form.ingreso1.value;
        var ingreso2 = form.ingreso2.value;

        try {
            ingreso1 = (isNaN(parseFloat(ingreso1))) ? 0 : parseFloat(ingreso1);
            ingreso2 = (isNaN(parseFloat(ingreso2))) ? 0 : parseFloat(ingreso2);
            form.resultado.value = (ingreso2 - ingreso1).toFixed(2);

            if ((ingreso2 - ingreso1) >= 0) {
                form.registrar.disabled = false;
            }

            if((ingreso2 == "") || (form.resultado.value < 0) ){
                form.registrar.disabled=true;
            }
        } catch (e) {

        }

    }

</script>
