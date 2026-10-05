<?PHP
// Ventas.php define $id_usuario; los controladores que refrescan el pedido por ajax definen $idUsuario
$idUsuarioPedido = isset($idUsuario) ? $idUsuario : $id_usuario;
?>
<section class="panel">


            <header align="center" class="alert alert-info">
                    <strong>
                        PRODUCTOS SOLICITADOS
                    </strong>
            </header>

            <?PHP if (!empty($mensajeStock)) { ?>
                <div class="alert alert-danger" align="center">
                    <strong><?PHP echo htmlspecialchars($mensajeStock); ?></strong>
                </div>
            <?PHP } ?>

            <div id="formularioEdit" style="display: none;"></div>

            <table class="table table-striped">
                <thead>

                <tr>
                <td width="20"><b>Imagen</b></td>
                <td><b>Productos</b></td>
                <td><b>Cant.</b></td>
                <td><b>Precio</b></td>
                <td><b>Total</b></td>
                <td><b>Opcion</b></td>
                </tr>

                <?PHP
        $showPreventa = $con->getPreventa($idUsuarioPedido);
        while ($preventa = mysqli_fetch_array($showPreventa)) {
            ?>

                <tr>
                <td><img src="<?php echo $urlViews . $preventa['imagen'] ?>" height="60" width="60"></td>
                <td><b> <?PHP echo $preventa['producto']; ?></b></td>
                <td><?PHP echo $preventa['cantidad']; ?></td>
                <td><?PHP echo dinero($preventa['precio']); ?></td>
                <td><?PHP echo dinero($preventa['totalPrecio']); ?></td>
                <td>


                <?PHP
                    echo "<a style=\"cursor:pointer;\"  class='btn btn-success'   
                               onclick=\"editarPreventa('" . $preventa['idProducto'] . "','" . $preventa['tipo'] . "','" . $preventa['idUser'] . "')\">
                               <i class='icon_pencil-edit'></i></a>";

                    echo "<a style=\"cursor:pointer;\"  class='btn btn-danger'
                         onclick=\"deleteOnlyProducto('" . $preventa['idProducto'] . "','" . $preventa['tipo'] . "','" . $preventa['idUser'] . "')\">
                <i class='icon_minus-box'></i></a>"; ?>

                </td>
            </tr>

        <?PHP } ?>


        <tr>
            
            
            
            
            <td colspan="3"></td>
            <td><strong> Total :</strong></td>
            <td>
                <h2>
                    <strong>
                        <?PHP
                        $totalPreventaConsulta = $con->getTotalPreventa($idUsuarioPedido);
                        while ($totalVenta = mysqli_fetch_array($totalPreventaConsulta)) {
                            $userId = $totalVenta['idUser'];
                            echo dinero($totalVenta['total']);
                        }
                        ?>
                    </strong>
                </h2>
            </td>
        </tr>

        <tr>

        <td colspan="3" align="center">
                <?PHP
                if (isset($userId)) {

                    echo " <a  data-toggle='modal'  class='btn btn-primary enabled'
                              href='Factura.php'
                              data-target='#myModal'>
                    <i class='icon_check'></i><strong> ACEPTAR</strong> </a>
                    <div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>      
                       <div class='modal-dialog'>
                           <div class='modal-content'>
                            </div>
                        </div>
                    </div>
                    
                    
                     ";
                } else {
                    echo " <a style=\"cursor:pointer;\"  class='btn btn-primary disabled '
                              onclick=\"\">
                    <i class='icon_check'></i><strong> ACEPTAR</strong> </a> ";
                }
                ?>

            </td>

            <td colspan="3" align="center">
                <?PHP
                if (isset($userId)) {
                    echo " <a style=\"cursor:pointer;\"  class='btn btn-danger'
                              onclick=\"deleteAllPreventa('" . $userId . "')\">
                    <i class='icon_minus-box'></i><strong> CANCELAR</strong> </a> ";
                } else {
                    echo " <a style=\"cursor:pointer;\"  class='btn btn-danger disabled '
                              onclick=\"\">
                    <i class='icon_minus-box'></i><strong> CANCELAR</strong> </a> ";
                }
                ?>
            </td>

            </tr>

            </thead>

        </table>

</section>