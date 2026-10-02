<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
require_once('SubirFoto.php');

iniciarSesionSegura();
$usuarioSesion = requerirSesion();
$usuario = $usuarioSesion['login'];

$con = new conexion();

// devuelve el motivo por el que los datos no son validos, o vacio si estan bien
function validarDatosProducto($cantidad, $precioVenta, $precioCompra)
{
    if (!is_numeric($precioVenta) || $precioVenta <= 0) {
        return "el precio de venta debe ser un numero mayor a 0.";
    }
    if (!is_numeric($precioCompra) || $precioCompra < 0) {
        return "el precio de compra debe ser un numero.";
    }
    if (!is_numeric($cantidad) || $cantidad < 0) {
        return "la cantidad debe ser un numero igual o mayor a 0.";
    }
    return "";
}

if (isset($_POST['nuevo_Producto'])) {
    $tipoproducto = $_POST['tipoproducto'];
    $codigo= $_POST['codigo'];
    $nombreProducto= $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $precioVenta = $_POST['pventa'];
    $precioCompra = $_POST['pcompra'];
    $fechaRegistro = $_POST['fechaRegistro'];
    $proveedor =null;

    $errorDatos = validarDatosProducto($cantidad, $precioVenta, $precioCompra);

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
    $idproducto = $_POST['idproducto'];
    $imagen = $_POST['imagen'];
    $tipoproducto = $_POST['tipoproducto'];
    $codigo= $_POST['codigo'];
    $nombreProducto= $_POST['descripcion'];
    $cantidad = $_POST['cantidad'];
    $precioVenta = $_POST['pventa'];
    $precioCompra = $_POST['pcompra'];
    $fechaRegistro = date("Y-m-d");
    $proveedor =null;

    $errorDatos = validarDatosProducto($cantidad, $precioVenta, $precioCompra);

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

