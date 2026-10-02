<style>
    .buscador-productos {
        position: relative;
        margin-bottom: 12px;
    }

    .buscador-productos i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
        color: #888;
    }

    .buscador-productos input {
        height: 42px;
        padding-left: 40px;
        font-size: 16px;
    }

    .grid-productos {
        display: flex;
        flex-wrap: wrap;
    }

    .tarjeta-producto {
        width: 25%;
        min-width: 130px;
        padding: 10px 4px;
        text-align: center;
        border: 1px solid #a9d3a9;
        background-size: 200px 200px;
        background-repeat: repeat;
    }

    .tarjeta-producto .single-product {
        width: 112px;
        margin: 0 auto;
    }

    /* boton "Agregar" (antes eran dos botones cortos: Mesa y Llevar) */
    .tarjeta-producto .product-hover a.add-to-cart-link {
        font-size: 12px;
        padding: 8px 4px;
        left: 5%;
        width: 90%;
        letter-spacing: 0.5px;
    }

    .tarjeta-producto .single-product:hover .product-hover a.add-to-cart-link {
        top: 30%;
    }

    .stock-otras {
        margin-top: 4px;
        font-size: 11px;
        line-height: 1.3;
        color: #d9edf7;
    }

    .sin-resultados {
        width: 100%;
        padding: 20px;
        text-align: center;
        font-size: 16px;
        color: #888;
    }
</style>

<div class="buscador-productos">
    <i class="icon_search"></i>
    <input type="text" id="buscarProducto" class="form-control" placeholder="Buscar producto por nombre..."
           autocomplete="off" oninput="filtrarProductos()">
</div>

<div class="grid-productos" id="gridProductos">
<?php
while ($product = mysqli_fetch_array($allProducto)) {
    $stock = (int) $product['cantidad'];
    $tienePrecio = is_numeric($product['precioVenta']) && $product['precioVenta'] > 0;
    ?>
    <div class="tarjeta-producto" data-buscar="<?PHP echo htmlspecialchars($product['nombreProducto']); ?>"
         style="background-image: url('<?PHP echo $urlViews; ?>img/fondoCelulares.svg');">
        <div class="single-product">
            <div class="product-f-image">
                <img src="<?PHP echo $urlViews . $product['imagen']; ?>" width="90" height="90" class="imgRedonda">
                <?PHP if ($stock > 0 && $tienePrecio) { ?>
                <div class="product-hover">
                    <a onclick="insertarPedido('<?PHP echo $product['idproducto'];?>')" style="text-decoration: none; cursor: pointer;"
                       class="add-to-cart-link"><i class="icon_cart_alt"></i> Agregar</a>
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
                <?PHP if (!$tienePrecio) { ?>
                    <span class="label label-warning">SIN PRECIO</span>
                <?PHP } elseif ($stock > 0) { ?>
                    <span class="label label-success">Stock: <?PHP echo $stock; ?></span>
                <?PHP } else { ?>
                    <span class="label label-danger">SIN STOCK</span>
                <?PHP } ?>
                <?PHP if (!empty($stockOtras[$product['idproducto']])) {
                    $otrasTiendas = array();
                    foreach ($stockOtras[$product['idproducto']] as $otra) {
                        $otrasTiendas[] = htmlspecialchars($otra['nombre']) . ': ' . $otra['cantidad'];
                    } ?>
                    <div class="stock-otras" title="Stock en otras tiendas"><?PHP echo implode('<br>', $otrasTiendas); ?></div>
                    <?PHP if ($esAdministrador && $stock <= 0) { ?>
                        <a href="Traslados.php?pedir=<?PHP echo $product['idproducto']; ?>" class="btn btn-xs btn-info" style="margin-top: 3px;">
                            <i class="arrow_left-right_alt"></i> Pedir
                        </a>
                    <?PHP } ?>
                <?PHP } ?>
            </div>
        </div>
    </div>
    <?php
}
?>
    <div class="sin-resultados" id="sinResultados" style="display: none;">No se encontraron productos con ese nombre.</div>
</div>

<script>
    // quita tildes y mayusculas para que "pollo" encuentre "Pollo" y "almuerzo" encuentre "Almuérzo"
    function normalizarTexto(texto) {
        return texto.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();
    }

    // filtra por el atributo data-buscar; para buscar por otro dato en el futuro basta con agregarlo a ese atributo
    function filtrarProductos() {
        var busqueda = normalizarTexto(document.getElementById('buscarProducto').value);
        var tarjetas = document.querySelectorAll('#gridProductos .tarjeta-producto');
        var visibles = 0;

        for (var i = 0; i < tarjetas.length; i++) {
            var coincide = normalizarTexto(tarjetas[i].getAttribute('data-buscar')).indexOf(busqueda) !== -1;
            tarjetas[i].style.display = coincide ? '' : 'none';
            if (coincide) {
                visibles++;
            }
        }

        document.getElementById('sinResultados').style.display = visibles === 0 ? '' : 'none';
    }
</script>
