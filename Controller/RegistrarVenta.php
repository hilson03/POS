<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
require_once('Codigo_control.class.php');
iniciarSesionSegura();

if (isset($_GET['RegistarVenta'])) {
    $con = new conexion();

    $usuarioSesion = requerirSesion();

    $usuario = $usuarioSesion['login'];
    $password = '';
    $ci = $_GET['ci'];
    $totalAPagar = $_GET['ingreso1'];
    $efectivo = $_GET['ingreso2'];
    $cambio = $_GET['resultado'];
    $fechaVenta = date("Y-m-d H:i:s");
    $estado = "NoConsolidado";
    $comentario = "";
    $fechaCodigoControl = date("Ymd");

    $searchClient = $con->getClienteDatos($ci);

    foreach ($searchClient as $cliente) {
        $nombreClienteDato = $cliente['apellido'];
        $idClientei = $cliente['idcliente'];
    }

    $searchUser = $con->getOnlyUserData($usuarioSesion['id_usu']);

    foreach ($searchUser as $user) {
        $tipo = $user['tipo'];
        $id_usuario = $user['id_usu'];
        $nombres = $user['nombre'];
        $login =$user['login'];
    }
    $urlViews = URL_VIEWS;
    $userLogueado = $nombres;


    $datosFactura = $con->getDatosFactura();
    foreach ($datosFactura as $facturaPropieario) {
        $propietario = $facturaPropieario['propietario'];
        $razon = $facturaPropieario['razon'];
        $direccion = $facturaPropieario['direccion'];
        $nro = $facturaPropieario['nro'];
        $telefono = $facturaPropieario['telefono'];
    }


    $dataMoneda = $con->getMoneda();

    while ($dataMonedaValues = mysqli_fetch_array($dataMoneda)) {
        $contextMoneda = $dataMonedaValues['contexto'];
        $tipoMoneda = $dataMonedaValues['tipoMoneda'];
    }

    date_default_timezone_set("America/Caracas" ) ;
    $dateInicial= date('Y-m-d');
    $dateFinal= date('Y-m-d');
    $getNumeroFicha = $con-> getNumFicha($dateInicial,$dateFinal);
    foreach ( $getNumeroFicha as $numFicha){
        $ficha = $numFicha['numficha'];
    }

    $menuMain = $con->getMenuMain();


    $datosDosificacion = $con->getDatosDosificacion();
    foreach ($datosDosificacion as $dosificacion) {
        $autorizacion = $dosificacion['autorizacion'];
        $factura = $dosificacion['factura'];
        $llave = $dosificacion['llave'];
        $nit = $dosificacion['nit'];
        $fechaLimite = $dosificacion['fechaL'];
    }


    $obtenerDatosCliente = $con->getDataCliente($id_usuario);
    foreach ($obtenerDatosCliente as $datosCliente) {
        $nombreCliente = $datosCliente['nombre'];
        $ci = $datosCliente['ci'];
        $fecha = $datosCliente['fecha'];
        $totalApagar = $datosCliente['totalApagar'];
        $efectivo = $datosCliente['efectivo'];
        $cambio = $datosCliente['cambio'];
    }

    $getDatosFecha = explode("-", $fechaLimite);
    $fechaLimiteAnio = $getDatosFecha[0];
    $fechaLimiteMes = $getDatosFecha[1];
    $fechaLimiteDia = $getDatosFecha[2];

    $fechaLimiteEmision = $fechaLimiteDia . ' / ' . $fechaLimiteMes . ' / ' . $fechaLimiteAnio;


    $getCodigoControl = new CodigoControl($autorizacion, $factura, $ci, $fechaCodigoControl, $totalApagar, $llave);
    $codigoControl = $getCodigoControl->generar();


    // se usa el id de la venta recien creada (antes se tomaba la ultima de la tabla y podia ser la de otro vendedor)
    $idVentaNueva = $con->registrarVenta($nombreClienteDato, $ci, $totalAPagar, $efectivo, $cambio, $idClientei, $codigoControl, $fechaVenta, $id_usuario, $usuarioSesion['idSucursalActiva']);

    $getDataClienteVenta = $con->getDatosVenta($idVentaNueva);

    foreach ($getDataClienteVenta as $dataVenta) {
        $idVentas = $dataVenta['idVentas'];
        $nombreCliente = $dataVenta['nombre'];
        $ci = $dataVenta['ci'];
        $fecha = $dataVenta['fecha'];
        $totalApagar = $dataVenta['totalApagar'];
        $efectivo = $dataVenta['efectivo'];
        $cambio = $dataVenta['cambio'];
        $codigoControl = $dataVenta['codigoControl'];
    }


    $pedidoTotalPreventa = $con->getPedidoTotalForFactura($id_usuario);
    $pedido = mysqli_num_rows($pedidoTotalPreventa);

    $unidadesVenta = 0;
    $totalVenta = 0;
    for ($i = 0; $i < $pedido; $i++) {
        $detallePedido = mysqli_fetch_array($pedidoTotalPreventa);

        $descripcion = $detallePedido['producto'];
        $precio = $detallePedido['precio'];
        $cantidad = $detallePedido['cantidad'];
        $total = $detallePedido['precio'] * $detallePedido['cantidad'];
        $tipo = $detallePedido['tipo'];
        $idProductoVendido = $detallePedido['idproducto'];

        $registrarPreventaTotalFinal = $con->registrarDatosVenta($cantidad, $descripcion, $precio, $total, $tipo, $fechaVenta, $codigoControl, $idVentas, $estado, $usuarioSesion['idSucursalActiva']);

        $descontarStock = $con->descontarStockProducto($idProductoVendido, $cantidad, $usuarioSesion['idSucursalActiva']);

        $unidadesVenta += $cantidad;
        $totalVenta += $total;
    }

    // antes se guardaban la cantidad, el precio y el total del ultimo producto del bucle, no los de la venta completa
    $precioPromedio = $unidadesVenta > 0 ? round($totalVenta / $unidadesVenta, 2) : 0;
    $registrarDatosVentaTotal = $con->registrarDatosVentaTotal($nombreClienteDato, $unidadesVenta, $precioPromedio, $totalVenta, $codigoControl, $fechaVenta, $estado, $comentario, $idVentas, $id_usuario, $usuarioSesion['idSucursalActiva']);

    $registrarClienteDatosFinal = $con->registrarDatosClienteVenta($fechaVenta, $ci, $nombreClienteDato, $codigoControl, $idVentas, $estado);

    $registrarFacturaDatosFinal = $con->registrarDatosFacturaVenta($nit, $factura, $autorizacion, $codigoControl, $idVentas, $estado);

    /// Clean for new client

    $cleanDataCliente = $con->cleanClientData($id_usuario);
    $cleanDataPreventa = $con->cleanRegistroPreventa($id_usuario);

 header("Location: Ventas.php");

}

?>
