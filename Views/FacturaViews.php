<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <form class="form-validate form-horizontal" name="calculadora" action="RegistrarPreventa.php" method="post">
                <table id="dataTables-example">

                    <?PHP
                    // el mismo formulario sirve para cada forma de pago; solo cambian el encabezado y los campos del dinero
                    $esTarjeta = isset($formaPago) && $formaPago == 'Tarjeta';
                    $esTransferencia = isset($formaPago) && $formaPago == 'Transferencia';
                    $sinEfectivo = $esTarjeta || $esTransferencia;   // se cobra el total exacto, sin efectivo ni cambio
                    $encabezados = array(
                        'Efectivo'      => array('#2e9d5b', '<i class="fa fa-money"></i> COBRO EN EFECTIVO'),
                        'Tarjeta'       => array('#3b6fd8', '<i class="fa fa-credit-card"></i> COBRO CON TARJETA'),
                        'Transferencia' => array('#8a4fd0', '<i class="fa fa-university"></i> COBRO POR TRANSFERENCIA'),
                    );
                    $modoCobro = $esTarjeta ? 'Tarjeta' : ($esTransferencia ? 'Transferencia' : 'Efectivo');
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
                                <input class="form-control input-lg m-bot15" type="text" required name="ci" autocomplete="off"  onkeyup="myFunctionSearch()" id="searchNit"  >
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
                        <?PHP if ($esTransferencia) { ?>
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
            document.getElementById("pers").innerHTML = "";
            return;
        }

        loadDoc("nitClient=" + clientSearch, "SearchContact.php", function () {

            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

                document.getElementById("myDiv").innerHTML = xmlhttp.responseText;
                document.getElementById("myDiv").style.border = "1px solid #A5ACB2";

            }

        });
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
