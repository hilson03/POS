<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
require_once('PagoMixto.php');
require_once('Validar.php');
require_once('Codigo_control.class.php');

iniciarSesionSegura();
$con = new conexion();

$usuarioSesion = requerirSesion();

$usuario = $usuarioSesion['login'];
$password = '';

$ci = textoPost('ci', 100);
$fechaVenta = date("Y-m-d H:i:s");

// forma de pago: con tarjeta o transferencia se cobra el total exacto, sin cambio, y se guardan los datos del comprobante
$formaPago = (isset($_POST['formaPago']) && in_array($_POST['formaPago'], array('Tarjeta', 'Transferencia', 'Mixto'), true)) ? $_POST['formaPago'] : 'Efectivo';

// el total siempre se calcula con los productos del pedido guardado, nunca con el que manda el formulario
$totalAPagar = 0;
foreach ($con->getTotalPreventa($usuarioSesion['id_usu']) as $preVentaTotal) {
    $totalAPagar = round((float) $preVentaTotal['total'], 2);
}
if ($totalAPagar <= 0) {
    $con->updateMensajeAlert("No hay productos en el pedido para cobrar.", "alert alert-danger");
    header("Location: Ventas.php?estado='Activo'");
    exit;
}
$efectivo = $totalAPagar;
$cambio = 0;
if ($formaPago == 'Efectivo') {
    // lo que entrega el cliente debe alcanzar para el total; el cambio lo calcula el servidor
    $efectivo = str_replace(array(',', ' '), '', textoPost('ingreso2', 20));
    if (!numeroValido($efectivo, $totalAPagar)) {
        $con->updateMensajeAlert("El efectivo recibido debe ser un número igual o mayor al total (" . montoHn($totalAPagar) . ").", "alert alert-danger");
        header("Location: Ventas.php?estado='Activo'");
        exit;
    }
    $efectivo = round((float) $efectivo, 2);
    $cambio = round($efectivo - $totalAPagar, 2);
}
$referenciaPago = '';
$ultimos4 = '';
$bancoDestino = '';
$nombreTransfirio = '';
$pagosDetalle = '';
if ($formaPago == 'Mixto') {
    // el total ya se calculo arriba con el pedido guardado: las partes tienen que cuadrar con lo que realmente se vende
    $cobroMixto = armarPagoMixto($con, $_POST, $totalAPagar, $faltante);
    if ($cobroMixto === null) {
        $con->updateMensajeAlert("Para cobrar con pago mixto " . $faltante . ".", "alert alert-danger");
        header("Location: Ventas.php?estado='Activo'");
        exit;
    }
    $pagosDetalle = json_encode($cobroMixto['partes']);
    // efectivo = todo lo que entrego el cliente; asi efectivo - cambio sigue siendo el total, como en las otras formas
    $efectivo = round($totalAPagar + $cobroMixto['cambio'], 2);
    $cambio = $cobroMixto['cambio'];
}
if ($formaPago == 'Transferencia') {
    $referenciaPago = substr(preg_replace('/[^A-Za-z0-9\-]/', '', isset($_POST['referenciaPago']) ? $_POST['referenciaPago'] : ''), 0, 30);
    $cuenta = $con->getCuentaBancaria(isset($_POST['idCuentaDestino']) ? (int) $_POST['idCuentaDestino'] : 0);
    $nombreTransfirio = isset($_POST['nombreTransfirio']) ? mb_substr(trim(preg_replace('/\s+/u', ' ', (string) $_POST['nombreTransfirio'])), 0, 100, 'UTF-8') : '';

    $faltante = '';
    if ($referenciaPago == '') {
        $faltante = "escribe el número de referencia de la transferencia";
    } elseif ($cuenta === null || $cuenta['estado'] != 'Activo') {
        $faltante = "elige la cuenta de la tienda a la que llegó el dinero";
    } elseif (empty($_POST['verificado'])) {
        $faltante = "confirma que verificaste que el dinero llegó a la cuenta";
    }
    if ($faltante != '') {
        $con->updateMensajeAlert("Para cobrar por transferencia " . $faltante . ".", "alert alert-danger");
        header("Location: Ventas.php?estado='Activo'");
        exit;
    }
    // se guarda como texto para que el historial no cambie si despues se edita la cuenta
    $bancoDestino = mb_substr($cuenta['banco'] . ' - ' . $cuenta['numero'], 0, 110, 'UTF-8');
    $efectivo = $totalAPagar;
    $cambio = 0;
}
if ($formaPago == 'Tarjeta') {
    $referenciaPago = substr(preg_replace('/[^A-Za-z0-9\-]/', '', isset($_POST['referenciaPago']) ? $_POST['referenciaPago'] : ''), 0, 20);
    $ultimos4 = (isset($_POST['ultimos4']) && preg_match('/^[0-9]{4}$/', $_POST['ultimos4'])) ? $_POST['ultimos4'] : '';
    if ($referenciaPago == '') {
        $con->updateMensajeAlert("Para cobrar con tarjeta escribe el número de autorización del comprobante.", "alert alert-danger");
        header("Location: Ventas.php?estado='Activo'");
        exit;
    }
    $efectivo = $totalAPagar;
    $cambio = 0;
}

/*
 * Cliente: se elige de la lista de Clientes (buscador del cobro) o se registra ahi mismo con DNI, nombre y apellido.
 * En efectivo es opcional: sin cliente la venta sale a "Consumidor Final". Tarjeta, transferencia y mixto piden el DNI.
 */
$ci = trim((string) $ci);
if ($ci == '') {
    $ci = textoPost('dniNuevo', 100);
}
$nombreNuevo = textoPost('nombreNuevo', 200);
$apellidoNuevo = textoPost('apellidoNuevo', 200);
if ($ci == '' && $formaPago != 'Efectivo') {
    $con->updateMensajeAlert("Elige al cliente o regístralo con su DNI (solo en pagos en efectivo puede quedar sin cliente).", "alert alert-danger");
    header("Location: Ventas.php?estado='Activo'");
    exit;
}

$nombreClienteDato = CONSUMIDOR_FINAL;
if ($ci != '') {
    $searchClient = $con->getClienteDatos($ci);

    // cliente nuevo: se registra con nombre y apellido (como en Clientes); sin ellos, solo se permite en efectivo
    if (empty($searchClient)) {
        if ($nombreNuevo != '' && $apellidoNuevo != '') {
            $con->registerNewCliente("img/user.png", $nombreNuevo, $apellidoNuevo, "", "", "", "", date('Y-m-d'), $ci);
            $searchClient = $con->getClienteDatos($ci);
        } elseif ($formaPago != 'Efectivo') {
            $con->updateMensajeAlert("El DNI " . $ci . " no está en Clientes: escribe el nombre y el apellido para registrarlo.", "alert alert-danger");
            header("Location: Ventas.php?estado='Activo'");
            exit;
        }
    }

    foreach ($searchClient as $cliente) {
        $nombreClienteDato = nombreCliente($cliente);
    }
}






// usuario que esta cobrando: sus datos de cobro se guardan aparte de los de otros vendedores
$usuarioVenta = $con->getOnlyUserData($usuarioSesion['id_usu']);
$idUsuarioVenta = empty($usuarioVenta) ? 0 : $usuarioVenta[0]['id_usu'];

$registrarDatoscliente = $con->registrarDatosPreventa($ci,$nombreClienteDato ,$totalAPagar,$efectivo,$cambio,$fechaVenta,'1',$idUsuarioVenta,
    $formaPago, $referenciaPago, $ultimos4, $bancoDestino, $nombreTransfirio, $pagosDetalle);


$searchUser = $con->getOnlyUserData($usuarioSesion['id_usu']);

foreach ($searchUser as $user) {
    $tipo = $user['tipo'];
    $id_usuario = $user['id_usu'];
    $nombres = $user['nombre'];
    $foto = $user['foto'];
}
$urlViews = URL_VIEWS;
$userLogueado = $nombres;
$imageUser = $foto;

$datosFactura = $con-> getDatosFactura();
 foreach ($datosFactura as $facturaPropieario){
     $propietario = $facturaPropieario['propietario'];
     $razon = $facturaPropieario['razon'];
     $direccion = $facturaPropieario['direccion'];
     $nro = $facturaPropieario['nro'];
     $telefono = $facturaPropieario['telefono'];
 }

 $datosDosificacion = $con->getDatosDosificacion();
 foreach ($datosDosificacion as $dosificacion){
   $autorizacion = $dosificacion['autorizacion'];
   $factura =$dosificacion['factura'];
   $llave =$dosificacion['llave'];
   $nit =$dosificacion['nit'];
   $fechaLimite=$dosificacion['fechaL'];
 }

 $obtenerDatosCliente =$con->getDataCliente($idUsuarioVenta);
 foreach ( $obtenerDatosCliente as $datosCliente){
     $nombreCliente = $datosCliente['nombre'];
     $ci = $datosCliente['ci'];
     $fecha = $datosCliente['fecha'];
     $totalApagar = $datosCliente['totalApagar'];
     $efectivo = $datosCliente['efectivo'];
     $cambio = $datosCliente['cambio'];
     $formaPago = $datosCliente['formaPago'];
     $referenciaPago = $datosCliente['referenciaPago'];
     $ultimos4 = $datosCliente['ultimos4'];
     $bancoDestino = $datosCliente['bancoDestino'];
     $nombreTransfirio = $datosCliente['nombreTransfirio'];
     $pagosDetalle = $datosCliente['pagosDetalle'];
 }

 $pedidoTotalPreventa = $con->getPedidoTotalForFactura($idUsuarioVenta);
 $pedido = mysqli_num_rows($pedidoTotalPreventa);

$dataMoneda = $con -> getMoneda();

while ($dataMonedaValues = mysqli_fetch_array($dataMoneda)){
    $contextMoneda = $dataMonedaValues['contexto'];
    $tipoMoneda = $dataMonedaValues['tipoMoneda'];
}

$fechaCodigoControl = date("Ymd");

$codigoControl = new CodigoControl($autorizacion, $factura, $ci, $fechaCodigoControl, $totalApagar, $llave);
$getCodigoControl = $codigoControl->generar();

$getDatosFecha = explode("-",$fechaLimite);
$fechaLimiteAnio=$getDatosFecha[0];
$fechaLimiteMes=$getDatosFecha[1];
$fechaLimiteDia=$getDatosFecha[2];

$fechaLimiteEmision = $fechaLimiteDia.' / '.$fechaLimiteMes.' / '.$fechaLimiteAnio;






$menuMain = $con->getMenuMain();
require('../Views/ShowFacturaViews.php');
?>