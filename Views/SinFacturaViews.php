<!DOCTYPE html>
<html lang="en">
<?PHP
include('LiteralMoney.php');
?>


<table align="left" bgcolor="#ffffff" class="breadcrumb">

    <tbody>


    <tr>

        <td colspan="6" align="center">
            <span style="color: #2b2b2b;">    -----------------------------------------------------------------------------------</span>
        </td>
    </tr>

<tr>
    <td colspan="6" align="center">
        <span style="color: #2b2b2b;"><p style="font-size:25px">  FICHA NRO : <strong> <?PHP echo  $ficha; ?> </strong> </span>
    </td>
</tr>


    <tr>

        <td colspan="6"><span style="color: #2b2b2b;"><b>Fecha de Venta : </b> <?PHP echo $fecha; ?></span></td>
    </tr>
    <tr>
        <td colspan="6"><span style="color: #2b2b2b;"><b>RTN/DNI : </b> <?PHP echo $ci; ?></span></td>
    </tr>
    <tr>
        <td colspan="6"><span style="color: #2b2b2b;"><b>Se&ntilde;or(es): </b> <?PHP echo $nombreCliente; ?></span>
        </td>
    </tr>

    <tr>

        <td colspan="6" align="center">
            <span style="color: #2b2b2b;">    -----------------------------------------------------------------------------------</span>
        </td>
    </tr>
    <tr>
        <td width="10"></td>
        <td width="40"><span style="color: #2b2b2b;"><b>Cant.</b></span></td>
        <td width="100"><span style="color: #2b2b2b;"><b>Descripcion</b></span></td>
        <td width="70"><span style="color: #2b2b2b;"><b>Precio</b></span></td>
        <td width="70"><span style="color: #2b2b2b;"><b>Total</b></span></td>
        <td width="70"><span style="color: #2b2b2b;"></span></td>
    </tr>
    <?PHP
    for ($i = 0; $i < $pedido; $i++) {
        echo '<tr>';
        $detallePedido = mysqli_fetch_array($pedidoTotalPreventa);
        echo '<td>' . '</td>';
        echo '<td width="40"><span style="color: #2b2b2b;">' . $detallePedido['cantidad'] . '</span></td>';
        echo '<td width="100"><span style="color: #2b2b2b;">' . $detallePedido['producto'] . '</span></td>';
        echo '<td width="70"><span style="color: #2b2b2b;">' . dinero($detallePedido['precio']) . '</span></td>';
        echo '<td width="70"><span style="color: #2b2b2b;">' . dinero($detallePedido['precio'] * $detallePedido['cantidad']) . '</span></td>';
        echo '<td width="70"><span style="color: #2b2b2b;"></span></td>';
        echo '</tr>';
    }


    ?>

    <tr>

        <td colspan="6" align="center">
            <span style="color: #2b2b2b;">    -----------------------------------------------------------------------------------</span>
        </td>
    </tr>

    <tr>

        <td colspan="6"><span
                    style="color: #2b2b2b;"><b>Total a Pagar : <?PHP echo $tipoMoneda; ?>  </b> <?PHP echo dinero($totalAPagar); ?></span>
        </td>
    </tr>
<?PHP if (isset($formaPago) && $formaPago == 'Tarjeta') { ?>
                            <tr>
                                <td colspan="6"><span style="color: #2b2b2b;"><b>Forma de pago : </b> Tarjeta</span></td>
                            </tr>
                            <tr>
                                <td colspan="6"><span style="color: #2b2b2b;"><b>Autorizaci&oacute;n : </b> <?PHP echo htmlspecialchars($referenciaPago); ?><?PHP if ($ultimos4 != '') { echo ' &nbsp; <b>Tarjeta :</b> **** ' . htmlspecialchars($ultimos4); } ?></span></td>
                            </tr>
<?PHP } elseif (isset($formaPago) && $formaPago == 'Transferencia') { ?>
                            <tr>
                                <td colspan="6"><span style="color: #2b2b2b;"><b>Forma de pago : </b> Transferencia</span></td>
                            </tr>
                            <tr>
                                <td colspan="6"><span style="color: #2b2b2b;"><b>Referencia : </b> <?PHP echo htmlspecialchars($referenciaPago); ?></span></td>
                            </tr>
                            <tr>
                                <td colspan="6"><span style="color: #2b2b2b;"><b>Cuenta destino : </b> <?PHP echo htmlspecialchars($bancoDestino); ?></span></td>
                            </tr>
<?PHP if ($nombreTransfirio != '') { ?>
                            <tr>
                                <td colspan="6"><span style="color: #2b2b2b;"><b>Transfiri&oacute; : </b> <?PHP echo htmlspecialchars($nombreTransfirio); ?></span></td>
                            </tr>
<?PHP } ?>
<?PHP } else { ?>
                            <tr>
                                <td colspan="6"><span style="color: #2b2b2b;"><b>Forma de pago : </b> Efectivo</span></td>
                            </tr>
    <tr>
        <td colspan="6"><span
                    style="color: #2b2b2b;"><b>Efectivo : <?PHP echo $tipoMoneda; ?> </b> <?PHP echo dinero($efectivo); ?></span>
        </td>
    </tr>
    <tr>
        <td colspan="6"><span style="color: #2b2b2b;"><b>Cambio : <?PHP echo $tipoMoneda; ?> </b> <?PHP echo dinero($cambio); ?></span>
        </td>
    </tr>
<?PHP } ?>
    <tr>
        <td colspan="6">&nbsp;<span style="color: #2b2b2b;"> <b>Son : </b>
                                <?PHP
                                $literalMoney = new  EnLetras();
                                $shoLiteralMoney = mb_strtoupper($literalMoney->valorEnletras($totalAPagar, $contextMoneda), "UTF-8");
                                echo "<b>" . $shoLiteralMoney . "</b>";
                                ?>
                                      </span>
        </td>
    </tr>

    <tr>

        <td colspan="6" align="center">
            <span style="color: #2b2b2b;">    -----------------------------------------------------------------------------------</span>
        </td>
    </tr>


    <tr>
        <td colspan="4">
            <span style="color: #2b2b2b;">  Atendido por : <strong><?PHP echo $userLogueado; ?> </strong></span>
        </td>
    </tr>
    </tbody>
</table>


</div>


</div>
</div>
</div>
<div class="col-md-4"></div>


</body>
</html>