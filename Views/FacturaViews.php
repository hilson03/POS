<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <form class="form-validate form-horizontal" name="calculadora" action="RegistrarPreventa.php" method="post">
                <table id="dataTables-example">

                    <?PHP
                    // el mismo formulario sirve para cada forma de pago; solo cambian el encabezado y los campos del dinero
                    $esTarjeta = isset($formaPago) && $formaPago == 'Tarjeta';
                    ?>
                    <input type="hidden" name="formaPago" value="<?PHP echo $esTarjeta ? 'Tarjeta' : 'Efectivo'; ?>">
                    <tr>
                        <th colspan="5" align="Center">
                            <span style="display:inline-block; padding:6px 14px; border-radius:4px; color:#fff; font-size:16px; white-space:nowrap;
                                         background: <?PHP echo $esTarjeta ? '#3b6fd8' : '#2e9d5b'; ?>;">
                                <?PHP echo $esTarjeta ? '<i class="fa fa-credit-card"></i> COBRO CON TARJETA' : '<i class="fa fa-money"></i> COBRO EN EFECTIVO'; ?>
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
                        <td<?PHP echo $esTarjeta ? ' colspan="5"' : ''; ?>>
                            <div class="<?PHP echo $esTarjeta ? 'col-lg-6' : 'col-lg-20'; ?>">
                                <input class="form-control input-lg m-bot15" type="text" required name="ingreso1"
                                       readonly value="<?PHP echo dinero($preventa); ?>" onKeyUp="Suma()">

                            </div>
                        </td>


                        <?PHP if ($esTarjeta) { ?>
                        <!-- tarjeta: se cobra el total exacto (sin efectivo ni cambio) y se anotan los datos del comprobante de la terminal -->
                        <input type="hidden" name="ingreso2" value="<?PHP echo dinero($preventa); ?>">
                        <input type="hidden" name="resultado" value="0.00">

                    </tr>
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
                        <?PHP } ?>
                    </tr>
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
