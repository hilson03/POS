<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
require_once('PagoMixto.php');
require_once('Codigo_control.class.php');
iniciarSesionSegura();
$con = new conexion();

$usuarioSesion = requerirSesion();

$usuario = $usuarioSesion['login'];
$password = '';

$ci =$_GET['ci'];

$totalAPagar = isset($_GET['ingreso1']) ? (float) $_GET['ingreso1'] : 0;   // montos siempre numericos aunque alteren el enlace
$efectivo = isset($_GET['ingreso2']) ? (float) $_GET['ingreso2'] : 0;
$cambio = isset($_GET['resultado']) ? (float) $_GET['resultado'] : 0;
$fechaVenta = date("Y-m-d H:i:s");

// sin RTN/DNI (o sin cliente registrado con ese numero) la venta es de Consumidor Final
$nombreClienteDato = CONSUMIDOR_FINAL;
$searchClient = trim($ci) == '' ? array() : $con->getClienteDatos($ci);

foreach ($searchClient as $cliente) {
    $nombreClienteDato = nombreCliente($cliente);
}

// usuario que esta cobrando: sus datos de cobro se guardan aparte de los de otros vendedores
$usuarioVenta = $con->getOnlyUserData($usuarioSesion['id_usu']);
$idUsuarioVenta = empty($usuarioVenta) ? 0 : $usuarioVenta[0]['id_usu'];

// esta pantalla vuelve a guardar los datos del cobro: se conserva la forma de pago elegida en el cobro
$pagoEnCurso = $con->getPagoEnCurso($idUsuarioVenta);
$registrarDatoscliente = $con->registrarDatosPreventa($ci,$nombreClienteDato ,$totalAPagar,$efectivo,$cambio,$fechaVenta,'1',$idUsuarioVenta,
    $pagoEnCurso['formaPago'], $pagoEnCurso['referenciaPago'], $pagoEnCurso['ultimos4'], $pagoEnCurso['bancoDestino'], $pagoEnCurso['nombreTransfirio'],
    $pagoEnCurso['pagosDetalle']);


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

$codigoControl = new CodigoControl($autorizacion, $factura, $nit, $fechaCodigoControl, $totalApagar, $llave);
$getCodigoControl = $codigoControl->generar();

$getDatosFecha = explode("-",$fechaLimite);
$fechaLimiteAnio=$getDatosFecha[0];
$fechaLimiteMes=$getDatosFecha[1];
$fechaLimiteDia=$getDatosFecha[2];

$fechaLimiteEmision = $fechaLimiteDia.' / '.$fechaLimiteMes.' / '.$fechaLimiteAnio;


$menuMain = $con->getMenuMain();
require('../Views/ConFacturaViews.php');
?>
