<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
require_once('SubirFoto.php');
require_once('Validar.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion('ADMINISTRADOR');
$usuario = $usuarioSesion['login'];

$con = new conexion();

// devuelve el motivo por el que los datos no son validos, o vacio si estan bien
function validarDatosProducto($con, $nombreProducto, $codigo, $tipoproducto, $cantidad, $precioVenta, $precioCompra)
{
    if ($nombreProducto == '') {
        return "el nombre del producto es obligatorio.";
    }
    if ($codigo == '') {
        return "el codigo (IMEI o codigo de barras) es obligatorio.";
    }
    if ($tipoproducto == '' || !$con->tipoProductoExiste($tipoproducto)) {
        return "elige un tipo de producto de la lista.";
    }
    if (!numeroValido($precioVenta, 0.01)) {
        return "el precio de venta debe ser un numero mayor a 0.";
    }
    if (!numeroValido($precioCompra, 0)) {
        return "el precio de compra debe ser un numero.";
    }
    if ((float) $precioCompra > (float) $precioVenta) {
        // se venderia con perdida: casi siempre es un error al escribir los precios
        return "el precio de compra (" . dinero($precioCompra) . ") no puede ser mayor que el precio de venta (" . dinero($precioVenta) . ").";
    }
    if (!numeroValido($cantidad, 0, 999999) || floor($cantidad) != $cantidad) {
        return "la cantidad debe ser un numero entero igual o mayor a 0.";
    }
    return "";
}

if (isset($_POST['nuevo_Producto'])) {
    $tipoproducto = textoPost('tipoproducto', 150);
    $codigo = textoPost('codigo', 150);
    $nombreProducto = textoPost('descripcion', 150);
    $cantidad = textoPost('cantidad', 20);
    $precioVenta = textoPost('pventa', 20);
    $precioCompra = textoPost('pcompra', 20);
    // la fecha de registro es la del dia (en el formulario es solo de lectura)
    $fechaRegistro = date('Y-m-d');
    $proveedor =null;

    $errorDatos = validarDatosProducto($con, $nombreProducto, $codigo, $tipoproducto, $cantidad, $precioVenta, $precioCompra);

    if ($errorDatos != "") {
        $updateMensaje = $con->updateMensajeAlert("No se registro el producto: " . $errorDatos, "alert alert-danger");
    } else {
        $destino = subirFoto('userfile', "img/user.png", $errorFoto);

        $mensaje = "Se registro un nuevo producto  correctamente !!!";
        $alerta = "alert alert-success";
        if ($errorFoto != "") {
            $mensaje = "Se registro el producto, pero la foto no se guardo: " . $errorFoto;
            $alerta = "alert alert-warning";
        }

        $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);
        $registerNewProducto = $con->registerNewProducto($destino,$codigo,$nombreProducto,$cantidad,$fechaRegistro,$precioVenta,$tipoproducto,$proveedor,$precioCompra,$usuarioSesion['idSucursalActiva']);
    }
}


if (isset($_GET['idborrar'])) {
    $idborrar = (int) $_GET['idborrar'];
    $trasladosActivos = $con->contarTrasladosActivosProducto($idborrar);

    if ($trasladosActivos > 0) {
        // si se borrara, esas unidades quedarian pendientes o en camino sin un producto al que sumarse
        $updateMensaje = $con->updateMensajeAlert("No se puede eliminar el producto: tiene $trasladosActivos traslado(s) en curso entre tiendas. Terminalos o cancelalos primero.", "alert alert-danger");
    } else {
        $mensaje = "Se elimino  los datos del producto correctamente !!!";
        $alerta = "alert alert-danger";
        $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

        $deleteProducto = $con->deleteProduct($idborrar);
    }


}


if (isset($_POST['update_producto'])) {
    $idproducto = enteroPost('idproducto');
    $tipoproducto = textoPost('tipoproducto', 150);
    $codigo = textoPost('codigo', 150);
    $nombreProducto = textoPost('descripcion', 150);
    $cantidad = textoPost('cantidad', 20);
    $precioVenta = textoPost('pventa', 20);
    $precioCompra = textoPost('pcompra', 20);
    $fechaRegistro = date("Y-m-d");
    $proveedor =null;

    // la foto actual se lee de la base (no del formulario, que se puede alterar)
    $productoActual = $con->getProductoElegido($idproducto, $usuarioSesion['idSucursalActiva']);
    $imagen = empty($productoActual) ? '' : $productoActual[0]['imagen'];

    $errorDatos = empty($productoActual) ? "no se encontro el producto."
        : validarDatosProducto($con, $nombreProducto, $codigo, $tipoproducto, $cantidad, $precioVenta, $precioCompra);

    if ($errorDatos != "") {
        $updateMensaje = $con->updateMensajeAlert("No se actualizo el producto: " . $errorDatos, "alert alert-danger");
    } else {
        $destino = subirFoto('userfileEdit', $imagen, $errorFoto);


        $mensaje = "Se Actualizo  los datos del Producto correctamente !!!";
        $alerta = "alert alert-info";
        if ($errorFoto != "") {
            $mensaje = "Se actualizo el producto, pero la foto no se guardo: " . $errorFoto;
            $alerta = "alert alert-warning";
        }

        $updateMensaje = $con->updateMensajeAlert($mensaje, $alerta);

        $updateProductoData = $con->updateProduct($destino,$codigo,$nombreProducto,$cantidad,$fechaRegistro,$precioVenta,$tipoproducto,$proveedor,$precioCompra,$idproducto,$usuarioSesion['idSucursalActiva']);
    }

}

$searchUser = $con->getOnlyUserData($usuarioSesion['id_usu']);
$allUsuarios = $con->getAllUserData();

foreach ($searchUser as $user) {
    $tipo = $user['tipo'];
    $id_usuario = $user['id_usu'];
    $nombres = $user['nombre'];
    $foto = $user['foto'];
}


$menuMain = $con->getMenuMain();
header("Location: Producto.php?estado='Activo'");


?>

