<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <form class="form-validate form-horizontal" name="calculadora" action="RegistrarPreventa.php" method="post">
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
                                RTN / DNI :
                            </label>
                        </td>
                        <td colspan="7">
                            <div class="col-lg-10">
                                <?PHP // en efectivo el cliente es opcional: sin RTN/DNI ni nombre la venta sale a "Consumidor Final" ?>
                                <input class="form-control input-lg m-bot15" type="text"<?PHP echo $modoCobro == 'Efectivo' ? ' placeholder="Opcional"' : ' required'; ?> name="ci" autocomplete="off"  onkeyup="myFunctionSearch()" id="searchNit"  >
                                <?PHP if ($modoCobro == 'Efectivo') { ?>
                                    <small class="help-block" style="margin-top:-10px;">Si no escribe RTN/DNI ni nombre, la venta sale a <b>Consumidor Final</b>.</small>
                                <?PHP } ?>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label>
                                NOMBRE :
                            </label>
                        </td>
                        <td colspan="7">
                            <div class="col-lg-10">
                                <div  class="textbox2" size="25" readonly name="nombreCliente" id="myDiv"></div>
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
                    <!-- pago mixto: tarjeta y/o transferencia con su monto; el efectivo es lo que falta para el total -->
                    <tr>
                        <td colspan="6">
                            <style>
                                .mixto-parte { margin: 0 15px 10px; padding: 10px 12px; border: 1px solid #dde1e7; border-radius: 6px; background: #fafbfc; }
                                .mixto-parte.activa { background: #fff; border-color: #9aa0a8; }
                                .mixto-parte > label.mixto-titulo { font-weight: 700; font-size: 15px; cursor: pointer; margin: 0; }
                                .mixto-parte input[type=checkbox] { width: 18px; height: 18px; vertical-align: middle; margin: 0 6px 0 0; }
                                .mixto-campos { display: none; margin-top: 8px; }
                                .mixto-parte.activa .mixto-campos { display: block; }
                                .mixto-fila { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 6px; }
                                .mixto-fila > div { flex: 1 1 140px; }
                                .mixto-fila small { color: #5a6068; font-weight: 600; }
                                .mixto-resumen { margin: 4px 15px 10px; padding: 8px 12px; border-radius: 6px; font-weight: 700; font-size: 15px; }
                                .mixto-resumen.falta { background: #fdecea; color: #a12a1f; border: 1px solid #f3b8b1; }
                                .mixto-resumen.listo { background: #e3f4e9; color: #1f6b3a; border: 1px solid #a9dbb9; }
                            </style>

                            <div class="mixto-parte" id="parteTarjeta">
                                <label class="mixto-titulo" style="color:#3b6fd8;">
                                    <input type="checkbox" name="usaTarjeta" id="usaTarjeta" value="1" onchange="validarMixto()">
                                    <i class="fa fa-credit-card"></i> Tarjeta
                                </label>
                                <div class="mixto-campos">
                                    <div class="mixto-fila">
                                        <div><small>Monto</small>
                                            <input class="form-control input-lg" type="text" inputmode="decimal" name="montoTarjeta" id="montoTarjeta" autocomplete="off" placeholder="0.00" oninput="validarMixto()"></div>
                                        <div><small>N° autorización (voucher)</small>
                                            <input class="form-control input-lg" type="text" name="autorizacionMixto" id="autorizacionMixto" maxlength="20" autocomplete="off" placeholder="Ej: 482913" oninput="validarMixto()"></div>
                                        <div style="flex-basis:100px;"><small>Últimos 4</small>
                                            <input class="form-control input-lg" type="text" name="ultimos4Mixto" id="ultimos4Mixto" maxlength="4" inputmode="numeric" autocomplete="off" placeholder="Opcional" oninput="validarMixto()"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mixto-parte" id="parteTransferencia">
                                <label class="mixto-titulo" style="color:#8a4fd0;">
                                    <input type="checkbox" name="usaTransferencia" id="usaTransferencia" value="1" onchange="validarMixto()"<?PHP echo empty($cuentasTransferencia) ? ' disabled' : ''; ?>>
                                    <i class="fa fa-university"></i> Transferencia
                                </label>
                                <?PHP if (empty($cuentasTransferencia)) { ?>
                                    <small class="text-muted"> — no hay cuentas bancarias registradas (Configuración → Cuentas bancarias)</small>
                                <?PHP } else { ?>
                                <div class="mixto-campos">
                                    <div class="mixto-fila">
                                        <div><small>Monto</small>
                                            <input class="form-control input-lg" type="text" inputmode="decimal" name="montoTransferencia" id="montoTransferencia" autocomplete="off" placeholder="0.00" oninput="validarMixto()"></div>
                                        <div><small>N° referencia</small>
                                            <input class="form-control input-lg" type="text" name="referenciaMixto" id="referenciaMixto" maxlength="30" autocomplete="off" placeholder="Ej: 123456789" oninput="validarMixto()"></div>
                                    </div>
                                    <div class="mixto-fila">
                                        <div><small>Cuenta destino</small>
                                            <select class="form-control input-lg" name="idCuentaMixto" id="idCuentaMixto" onchange="validarMixto()">
                                                <?PHP if (count($cuentasTransferencia) > 1) { ?>
                                                    <option value="">-- ¿A cuál cuenta llegó? --</option>
                                                <?PHP } ?>
                                                <?PHP foreach ($cuentasTransferencia as $cuenta) { ?>
                                                    <option value="<?PHP echo $cuenta['idCuenta']; ?>"><?PHP echo htmlspecialchars($cuenta['banco'] . ' - ' . $cuenta['numero']); ?></option>
                                                <?PHP } ?>
                                            </select></div>
                                        <div><small>Transfirió (opcional)</small>
                                            <input class="form-control input-lg" type="text" name="nombreTransfirioMixto" maxlength="100" autocomplete="off" placeholder="Nombre"></div>
                                    </div>
                                    <label style="font-weight:600; color:#4a2a7a; cursor:pointer; margin:2px 0 0;">
                                        <input type="checkbox" name="verificadoMixto" id="verificadoMixto" value="1" onchange="validarMixto()">
                                        Verifiqué que la transferencia llegó a la cuenta
                                    </label>
                                </div>
                                <?PHP } ?>
                            </div>

                            <div class="mixto-parte activa" id="parteEfectivo">
                                <label class="mixto-titulo" style="color:#2e9d5b; cursor:default;"><i class="fa fa-money"></i> Efectivo (lo que falta)</label>
                                <div class="mixto-campos">
                                    <div class="mixto-fila">
                                        <div><small>Efectivo a cobrar</small>
                                            <input class="form-control input-lg" type="text" id="restoEfectivo" readonly value="<?PHP echo dinero($preventa); ?>"></div>
                                        <div><small>Recibido</small>
                                            <input class="form-control input-lg" type="text" inputmode="decimal" name="recibidoMixto" id="recibidoMixto" autocomplete="off" placeholder="0.00" oninput="validarMixto()"></div>
                                        <div><small>Cambio</small>
                                            <input class="form-control input-lg" type="text" id="cambioMixto" readonly value="0.00"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mixto-resumen falta" id="resumenMixto">Marca al menos una forma además del efectivo.</div>
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
                                        <option value="<?PHP echo $cuenta['idCuenta']; ?>">
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

    document.calculadora.registrar.disabled=true;

    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: true
        });
    });

    function myFunctionSearch() {

        var clientSearch = document.getElementById('searchNit').value;

        if (clientSearch == '') {

            document.getElementById("myDiv").innerHTML = "";
            document.getElementById("myDiv").style.border = "0px";
            return;
        }

        loadDoc("nitClient=" + clientSearch, "SearchContact.php", function () {

            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

                document.getElementById("myDiv").innerHTML = xmlhttp.responseText;
                document.getElementById("myDiv").style.border = "1px solid #A5ACB2";

            }

        });
    }


    // pago mixto: el efectivo es lo que falta para el total; ACEPTAR se activa cuando todo cuadra y cada parte tiene sus datos
    function centavos(texto) {
        var n = parseFloat(String(texto).replace(/,/g, ''));
        return isNaN(n) ? 0 : Math.round(n * 100);
    }
    function validarMixto() {
        var total = centavos(document.calculadora.ingreso1.value);
        var usaTarjeta = document.getElementById('usaTarjeta').checked;
        var cajaTransf = document.getElementById('usaTransferencia');
        var usaTransf = cajaTransf.checked && !cajaTransf.disabled;
        document.getElementById('parteTarjeta').classList.toggle('activa', usaTarjeta);
        document.getElementById('parteTransferencia').classList.toggle('activa', usaTransf);

        var ultimos4 = document.getElementById('ultimos4Mixto');
        ultimos4.value = ultimos4.value.replace(/[^0-9]/g, '');

        var tarjeta = usaTarjeta ? centavos(document.getElementById('montoTarjeta').value) : 0;
        var transf = usaTransf ? centavos(document.getElementById('montoTransferencia').value) : 0;
        var resto = total - tarjeta - transf;
        var recibido = centavos(document.getElementById('recibidoMixto').value);
        document.getElementById('restoEfectivo').value = (Math.max(resto, 0) / 100).toFixed(2);
        document.getElementById('cambioMixto').value = (resto > 0 && recibido >= resto ? (recibido - resto) / 100 : 0).toFixed(2);
        document.getElementById('recibidoMixto').disabled = resto <= 0;

        var problema = '';
        var formas = (usaTarjeta ? 1 : 0) + (usaTransf ? 1 : 0) + (resto > 0 ? 1 : 0);
        if (!usaTarjeta && !usaTransf) {
            problema = 'Marca al menos una forma además del efectivo.';
        } else if (usaTarjeta && tarjeta <= 0) {
            problema = 'Escribe el monto de la tarjeta.';
        } else if (usaTarjeta && document.getElementById('autorizacionMixto').value.trim() === '') {
            problema = 'Escribe el número de autorización de la tarjeta.';
        } else if (usaTarjeta && ultimos4.value !== '' && ultimos4.value.length !== 4) {
            problema = 'Los últimos dígitos de la tarjeta deben ser 4.';
        } else if (usaTransf && transf <= 0) {
            problema = 'Escribe el monto de la transferencia.';
        } else if (usaTransf && document.getElementById('referenciaMixto').value.trim() === '') {
            problema = 'Escribe la referencia de la transferencia.';
        } else if (usaTransf && document.getElementById('idCuentaMixto').value === '') {
            problema = 'Elige la cuenta a la que llegó la transferencia.';
        } else if (usaTransf && !document.getElementById('verificadoMixto').checked) {
            problema = 'Marca que verificaste que la transferencia llegó.';
        } else if (resto < 0) {
            problema = 'Tarjeta y transferencia suman ' + ((-resto) / 100).toFixed(2) + ' más que el total.';
        } else if (resto > 0 && recibido < resto) {
            problema = 'Falta cobrar en efectivo: ' + ((resto - recibido) / 100).toFixed(2);
        } else if (formas < 2) {
            problema = 'Con una sola forma de pago usa Efectivo, Tarjeta o Transferencia directamente.';
        }

        var resumen = document.getElementById('resumenMixto');
        resumen.textContent = problema !== '' ? problema : 'Todo cuadra: listo para ACEPTAR.';
        resumen.className = 'mixto-resumen ' + (problema !== '' ? 'falta' : 'listo');
        document.calculadora.registrar.disabled = problema !== '';
    }

    // transferencia: ACEPTAR se activa con la referencia, la cuenta destino y la casilla de verificacion marcada
    function validarTransferencia() {
        var referencia = document.getElementById('referenciaPago').value.trim();
        var cuenta = document.getElementById('idCuentaDestino').value;
        var verificado = document.getElementById('verificado').checked;
        document.calculadora.registrar.disabled = !(referencia !== '' && cuenta !== '' && verificado);
    }

    // tarjeta: ACEPTAR se activa con el numero de autorizacion (y, si se escriben, 4 digitos exactos)
    function validarTarjeta() {
        var autorizacion = document.getElementById('referenciaPago').value.trim();
        var ultimos4 = document.getElementById('ultimos4');
        ultimos4.value = ultimos4.value.replace(/[^0-9]/g, '');
        var ultimosOk = ultimos4.value === '' || ultimos4.value.length === 4;
        document.calculadora.registrar.disabled = !(autorizacion !== '' && ultimosOk);
    }

    function Suma() {

        var ingreso1 = document.calculadora.ingreso1.value;
        var ingreso2 = document.calculadora.ingreso2.value;

        try {
            ingreso1 = (isNaN(parseFloat(ingreso1))) ? 0 : parseFloat(ingreso1);
            ingreso2 = (isNaN(parseFloat(ingreso2))) ? 0 : parseFloat(ingreso2);
            document.calculadora.resultado.value = (ingreso2 - ingreso1).toFixed(2);

            if ((ingreso2 - ingreso1) >= 0) {
                document.calculadora.registrar.disabled = false;
            }

            if((ingreso2 == "") || (document.calculadora.resultado.value < 0) ){
                document.calculadora.registrar.disabled=true;
            }
        } catch (e) {

        }

    }

</script>
