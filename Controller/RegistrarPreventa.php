<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
require_once('Codigo_control.class.php');

iniciarSesionSegura();
$con = new conexion();

$usuarioSesion = requerirSesion();

$usuario = $usuarioSesion['login'];
$password = '';

$ci =$_POST['ci'];

$totalAPagar =$_POST['ingreso1'];
$efectivo =$_POST['ingreso2'];
$cambio=$_POST['resultado'];
$fechaVenta = date("Y-m-d H:i:s");

// forma de pago: con tarjeta se cobra el total exacto, sin cambio, y se guarda el numero de autorizacion
$formaPago = (isset($_POST['formaPago']) && $_POST['formaPago'] == 'Tarjeta') ? 'Tarjeta' : 'Efectivo';
$referenciaPago = '';
$ultimos4 = '';
if ($formaPago == 'Tarjeta') {
    $referenciaPago = substr(preg_replace('/[^A-Za-z0-9\-]/', '', isset($_POST['referenciaPago']) ? $_POST['referenciaPago'] : ''), 0, 20);
    $ultimos4 = (isset($_POST['ultimos4']) && preg_match('/^[0-9]{4}$/', $_POST['ultimos4'])) ? $_POST['ultimos4'] : '';
    if ($referenciaPago == '') {
        $con->updateMensajeAlert("Para cobrar con tarjeta escribe el numero de autorizacion del comprobante.", "alert alert-danger");
        header("Location: Ventas.php?estado='Activo'");
        exit;
    }
    $efectivo = $totalAPagar;
    $cambio = 0;
}

$searchClient = $con->getClienteDatos($ci);

if(empty($searchClient)){

    $nombreNewCliente = $_POST['nombreNewCliente'];

    $registrarNewProveedor = $con->registerNewCliente("img/user.png","",$nombreNewCliente,"","","","",$fechaVenta,$ci);
    $searchClient = $con->getClienteDatos($ci);
    foreach ($searchClient as $cliente) {
        $nombreClienteDato = $cliente['apellido'];
    }
}

if(!empty($searchClient)){
    foreach ($searchClient as $cliente) {
        $nombreClienteDato = $cliente['apellido'];
    }
}






// usuario que esta cobrando: sus datos de cobro se guardan aparte de los de otros vendedores
$usuarioVenta = $con->getOnlyUserData($usuarioSesion['id_usu']);
$idUsuarioVenta = empty($usuarioVenta) ? 0 : $usuarioVenta[0]['id_usu'];

$registrarDatoscliente = $con->registrarDatosPreventa($ci,$nombreClienteDato ,$totalAPagar,$efectivo,$cambio,$fechaVenta,'1',$idUsuarioVenta,
    $formaPago, $referenciaPago, $ultimos4);


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


date_default_timezone_set("America/Caracas" ) ;
$dateInicial= date('Y-m-d');
$dateFinal= date('Y-m-d');
$getNumeroFicha = $con->getNumFicha($dateInicial,$dateFinal);
foreach ( $getNumeroFicha as $numFicha){
    $ficha = $numFicha['numficha'];
}




$menuMain = $con->getMenuMain();
require('../Views/ShowFacturaViews.php');
?>