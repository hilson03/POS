<!-- cambiar la cantidad de un producto del pedido: el nombre y el precio salen del catalogo de Productos y no se pueden
     cambiar aqui (el servidor solo usa el producto y la cantidad; si un nombre esta mal se corrige en Productos) -->
<form class="form-validate form-horizontal" name="form" action="UpdatePreventa.php" method="GET">

    <input type="hidden" id="idProducto" name="idProducto"  value="<?php echo htmlspecialchars((string) $idProducto); ?>" >
    <table bgcolor="#ccc">
        <tr>
            <td>Producto :</td>
            <td> <input id="producto" class="textbox2" type="text" readonly tabindex="-1"
                        style="background:#eee; cursor:not-allowed;" title="El nombre se cambia en Productos"
                        value="<?php echo htmlspecialchars((string) $producto); ?>" /></td>
        </tr>
        <tr>
            <td>Nueva cantidad :</td>
            <td> <input name="cantidadUpdated" id="cantidadUpdated"  class="textbox2" autocomplete="off" type="number" min="1" step="1" required
                        value="<?php echo htmlspecialchars((string) $cantidadActual); ?>" /></td>
        </tr>
        <tr>
            <td>Precio :  </td>
            <td><input id="nuevoPrecio" class="textbox2" readonly tabindex="-1" type="text"
                       style="background:#eee; cursor:not-allowed;" value="<?php echo montoHn($precio); ?>" /></td>
        </tr>
        <tr>
            <td height="10"></td>
        </tr>
        <tr>
            <td colspan="2" align="center">
                <input name="a_update"  type="submit" role="button" class="btn btn-success"   value="Actualizar Pedido" />
            </td>
        <tr>
            <td height="20"></td>
        </tr>
    </table>
</form>
