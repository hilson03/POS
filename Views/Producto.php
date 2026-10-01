<?php
$row = 1;
while ($product = mysqli_fetch_array($allProducto)) {
    if ($row > 4) {
        echo "</tr><tr class='success'>";
        $row = 1;
    }
    ?>
    <td background="<?PHP echo $urlViews; ?>img/menuPOS.jpg" align="center">
        <div style="width: 112px">
            <div class="single-product">
                <div class="product-f-image">
                    <img src="<?PHP echo $urlViews . $product['imagen']; ?>" width="90" height="90" class="imgRedonda">
                    <?PHP $stock = (int) $product['cantidad']; ?>
                    <?PHP if ($stock > 0) { ?>
                    <div class="product-hover">
                        <a onclick="insertarPedidoMesa('<?PHP echo $product['idproducto'];?>','<?PHP echo $id_usuario;?>')" data-name="Mouse" style="text-decoration: none; cursor: pointer;"
                           class="add-to-cart-link">Mesa</a>
                        <a onclick="insertarPedidoLlevar('<?PHP echo $product['idproducto'];?>','<?PHP echo $id_usuario;?>')" data-name="Mouse" style="text-decoration: none; cursor: pointer;"
                           class="view-details-link">Llevar</a>
                    </div>
                    <?PHP } ?>

                    <span style="color: #FFFFFF">
                    <b>
                        <?PHP echo $product['nombreProducto'];
                        echo '<br>';
                        echo $product['precioVenta'];
                        echo '&nbsp;';
                        echo $tipoMonedaElegida; ?>   .
                    </b>
                        </span>
                    <br>
                    <?PHP if ($stock > 0) { ?>
                        <span class="label label-success">Stock: <?PHP echo $stock; ?></span>
                    <?PHP } else { ?>
                        <span class="label label-danger">SIN STOCK</span>
                    <?PHP } ?>
                </div>
            </div>
        </div>
    </td>
    <?php
    $row++;
}

echo '</tr>';


?>