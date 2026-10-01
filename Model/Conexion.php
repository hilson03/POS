<?php
class conexion{

    public $con;
    
    public function __construct(){
        $user = 'root';
        $password = '';
        $server = 'localhost';
        $database = 'icontpos';
        $this-> con = new mysqli($server, $user, $password, $database); 
        $this->con->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))"); 
    }

    public function getUser($usuario, $password){
    $query = $this->con->query("SELECT * FROM usuarios WHERE login='" . $usuario . "' AND password = '" . $password . "'");

        $retorno = array();
        $i = 0;
        while($fila=$query->fetch_assoc()){

            $retorno[$i] =$fila;
            $i++;
        }

    return $retorno;

    }

   public function getMenuMain(){

        $query = $this->con->query("SELECT * FROM `menu`");

        $retorno=[];

        $i = 0;
        while ($fila = $query->fetch_assoc()){

            $retorno[$i] = $fila;
            $i++;

        }
        return $retorno;

    }

//***************************esta funcion atraves de una consulta trae toda la informacion de los usuarios.******************************
    public function getAllUserData(){
        
        $query = $this->con->query("SELECT * FROM `usuarios`");

       
         return $query;

    }


//***********************esta funcion sirve para registrar nuevos usuarios con imagen******************
//recordar ver como resolver lo de la imagen de perfil de usuario
    public function getRegisterNewUser($nombre, $tipo, $usuario, $password, $imagenUsuario){

    $query = $this->con->query("INSERT INTO `usuarios`(`id_usu`,`login`,`tipo`,`nombre`,`password`,`foto`)
                            VALUES(NULL, '$usuario', '$tipo','$nombre','$password','$imagenUsuario')");

    return $query;

    }


    public function deleteUsuario($idUsuario)
    {

        $query = $this->con->query("DELETE FROM usuarios Where id_usu=$idUsuario ");

        return $query;
    }

    //*********************esta consulta permite actualizar la informacion del usuario********************************
    public function updateUsuario($login, $tipo, $nombre, $password, $foto, $idUsuario)
    {

        $query = $this->con->query("UPDATE `usuarios`
        SET `login` = '$login',
             `tipo` = '$tipo',
              `nombre` = '$nombre',
               `password` = '$password',
               `foto` = '$foto' WHERE `usuarios`.`id_usu` = $idUsuario");

        return $query;
    }


    public function getMensajeAlerta()
    {

        $query = $this->con->query("SELECT * FROM `alerta`");

        $retorno = [];

        $i = 0;
        while ($fila = $query->fetch_assoc()) {
            $retorno[$i] = $fila;
            $i++;
        }
        return $retorno;

    }
//*************esta funcion sirve para ver un mensaje de alerta ya se por que se creo, actualizo o elimino un usuario.**********
    public function updateMensajeAlert($mensaje, $alerta)
    {
        $query = $this->con->query("UPDATE `alerta` SET `tipoAlerta` = '$alerta',
                                                `mensaje` = '$mensaje'  WHERE `alerta`.`alertaId` = 1");
        return $query;
    }

//******************esta funciones sirven para obtener datos de facturas y actualizarlos al mismo tiempo*******************
    public function getDataFactura(){

        $query = $this->con->query("SELECT * FROM `datos`");
        return $query;

    }


    public function updateDataFactura($iddatos,$propietario, $razon, $direccion, $nro, $telefono){

        $query = $this->con->query("UPDATE `datos` SET `propietario` = '$propietario',
        `razon` = '$razon',
        `direccion` = '$direccion',
         `nro` = '$nro',
         `telefono` = '$telefono'
          WHERE `datos`.`iddatos` = $iddatos");

            return $query;


    }


//******************************metodo para que puedan funcionar el cambio de moneda****************************

    public function getMoneda()
        {
            $query = $this->con->query("SELECT * FROM `moneda`");
            return $query;
        }

    public function updateDataMoneda($idMoneda, $pais, $tipoMoneda, $contexto){

        $query = $this->con->query("UPDATE `moneda` SET
        `pais` = '$pais',
        `tipoMoneda` = '$tipoMoneda',
        `contexto` = '$contexto' WHERE `moneda`.`idMoneda` = $idMoneda ");
        
        return $query;

    }

//*************************************Metodos para cambiar idiomas*********************************************
    public function getIdioma()
    {
        $query = $this->con->query("SELECT * FROM `idioma`");
        return $query;
    }

    public function updateDataIdioma($idioma, $idIdioma)
    {

        $query = $this->con->query("UPDATE `idioma`
                                          SET `idioma` = '$idioma'
                                          WHERE `idioma`.`idIdioma` = $idIdioma");
        return $query;
    }

    public function updateIdiomaSistem($opcionMenu, $idIdioma)
    {
        $query = $this->con->query("UPDATE `menu`
                                          SET `opcion` = '$opcionMenu'
                                          WHERE `menu`.`idmenu` = $idIdioma ");
        return $query;
    }
/**Funciones de proveedores que nos permiten obtener, crear, actualizar y borrar proveedores de la base de datos
fueron borradas ya que el cliente no necesitaba ese modulo en esta version del POS************************/

//funcion sql para obtener una lista de todos los clientes tambien para crear, modificar y borrar
    public function getAllCliente()
    {

        $query = $this->con->query("SELECT * FROM cliente ");

        return $query;
    }


    public function registerNewCliente($imagen, $nombre, $apellido, $direccion, $telefonoFijo, $telefonoCelular, $email, $fechaRegistro, $ci)
    {

        $query = $this->con->query("INSERT INTO `cliente` (`idcliente`, `foto`, `nombre`, `apellido`, `direccion`, `telefonoFijo`, `telefonoCelular`, `email`, `contactoReferencia`, `telefonoReferencia`, `observaciones`, `fechaRegistro`, `ci`)
                                     VALUES (NULL, '$imagen', '$nombre', '$apellido', '$direccion', '$telefonoFijo', '$telefonoCelular', '$email', '', '', '', '$fechaRegistro', '$ci')");

        return $query;
    }

    public function updateClient($idcliente, $imagen, $nombre, $apellido, $direccion, $telefonoFijo, $telefonoCelular, $email, $fechaRegistro, $ci)
    {

        $query = $this->con->query("UPDATE `cliente` SET
                                                `foto` = '$imagen',
                                                `nombre` = '$nombre',
                                                `apellido` = '$apellido',
                                                `direccion` = '$direccion',
                                                `telefonoFijo` = '$telefonoFijo',
                                                `telefonoCelular` = '$telefonoCelular',
                                                `email` = '$email',
                                                `fechaRegistro` = '$fechaRegistro',
                                                `ci` = '$ci' WHERE `cliente`.`idcliente` = $idcliente");

        return $query;
    }


     public function deleteClient($idClient)
    {
        $query = $this->con->query("Delete from cliente where idcliente=$idClient ");

        return $query;
    }

//******************************funcion SQL permite traer todos los productos*******************************************
    public function getAllProducto()
    {

        $query = $this->con->query("SELECT * FROM producto");

        return $query;
    }


    public function getAllTipoProducto()
    {
        $query = $this->con->query("SELECT * FROM tipoproducto");

        return $query;
    }


    public function registerNewProducto($imagen, $codigo, $nombreProducto, $cantidad, $fechaRegistro, $precioVenta, $tipo, $proveedor, $precioCompra)
    {

        $query = $this->con->query("INSERT INTO `producto` (`idproducto`, `imagen`, `codigo`, `nombreProducto`, `cantidad`, `fechaRegistro`, `precioVenta`, `tipo`, `proveedor`, `precioCompra`)
                                          VALUES (NULL, '$imagen', '$codigo', '$nombreProducto', '$cantidad', '$fechaRegistro', '$precioVenta', '$tipo', '$proveedor', '$precioCompra')");

        return $query;
    }

    public function deleteProduct($idproducto)
    {
        $query = $this->con->query("Delete from producto where idproducto=$idproducto");

        return $query;
    }

     public function updateProduct($imagen, $codigo, $nombreProducto, $cantidad, $fechaRegistro, $precioVenta, $tipo, $proveedor, $precioCompra, $idproducto)
    {

        $query = $this->con->query("UPDATE `producto` SET `imagen` = '$imagen',
                                                     `codigo` = '$codigo',
                                                     `nombreProducto` = '$nombreProducto',
                                                     `cantidad` = '$cantidad',
                                                     `fechaRegistro` = '$fechaRegistro',
                                                     `precioVenta` = '$precioVenta',
                                                     `tipo` = '$tipo',
                                                      `proveedor` = '$proveedor',
                                                      `precioCompra` = '$precioCompra' WHERE `producto`.`idproducto` = $idproducto");

        return $query;
    }

//*****************************************Funcion SQL para registrar nuevos tipos de producto******************************************
    public function registerNewTipoProduct($tipoProducto)
    {
        $query = $this->con->query("INSERT INTO `tipoproducto` (`idtipoproducto`, `tipoproducto`)
                                          VALUES (NULL, '$tipoProducto')");

        return $query;

    }

    public function deleteTipoProduct($tipoProductoId)
    {
        $query = $this->con->query("Delete from tipoproducto where idtipoproducto=$tipoProductoId");

        return $query;
    }


    public function updateTipoProducto($tipoProductoId, $tipoproducto)
    {
        $query = $this->con->query("UPDATE `tipoproducto` SET `tipoproducto` = '$tipoproducto'
                                          WHERE `tipoproducto`.`idtipoproducto` = $tipoProductoId");

        return $query;
    }

    //************************funcion SQL para traer los inventarios, crearlos, Modificarlos y borrarlos*******************************
    

    
//*************************************metodo para obtener todos los tipos de monedas**************************************
    public function getTipoMoneda()
    {

        $query = $this->con->query("SELECT * FROM `moneda`");

        $retorno = [];

        $i = 0;
        while ($fila = $query->fetch_assoc()) {
            $retorno[$i] = $fila;
            $i++;
        }
        return $retorno;

    }
//consulta para hacer una preVenta

    public function getPreventa($idUser)
    {
        $idUser = (int) $idUser;
        $query = $this->con->query("SELECT MIN(idPreventa) as idPreventa,imagen,producto,COUNT(producto) as cantidad, SUM(precio) as totalPrecio,idProducto,pventa,idUser,precio,tipo
                                            FROM `preventa`
                                            WHERE idUser = $idUser
                                            GROUP BY producto,idProducto,tipo
                                            ORDER BY idPreventa ASC");
        return $query;
    }

     public function getTotalPreventa($idUser)
    {
        $idUser = (int) $idUser;
        $query = $this->con->query("SELECT Sum(precio) as total , idUser FROM `preventa` WHERE idUser = $idUser HAVING COUNT(*) > 0");
        return $query;
    }

    /******************Funcion SQL para traer datos de usuario relacionados con el pedido modificar o eliminar*****************************/ 

    public function getOnlyUserData($idUser)
    { $query = $this->con->query("SELECT * FROM usuarios where id_usu=$idUser");
        $retorno = [];
        $i = 0;
        while ($fila = $query->fetch_assoc()) {
            $retorno[$i] = $fila;
            $i++;
        }
        return $retorno;
    }


    public function getProductoElegido($idproducto)
    {

        $query = $this->con->query("SELECT * FROM `producto` where idproducto='$idproducto'");

        $retorno = [];

        $i = 0;
        while ($fila = $query->fetch_assoc()) {
            $retorno[$i] = $fila;
            $i++;
        }
        return $retorno;

    }

    public function insertarPreventaProducto($imagen, $producto, $precio, $idProducto, $pventa, $idUser, $tipo)
    {
        $query = $this->con->query("INSERT INTO `preventa` (`idPreventa`, `imagen`, `producto`, `precio`, `idProducto`, `pventa`, `idUser`, `tipo`)
                                          VALUES (NULL, '$imagen', '$producto', '$precio', '$idProducto', '$pventa', '$idUser', '$tipo')");

        return $query;
    }

    public function deleteOnlyPreventa($idProducto, $tipo, $idUser)
    {
        $idUser = (int) $idUser;
        $query = $this->con->query("Delete from preventa where idproducto='$idProducto'  and  tipo='$tipo' and idUser = $idUser");
        return $query;
    }

    public function deleteAllPreventa($idUser)
    {
        $idUser = (int) $idUser;
        $query = $this->con->query("DELETE FROM `preventa` WHERE idUser = $idUser");
        return $query;
    }


    public function getDataProductoChoose($idProducto, $tipo, $idUser)
    {
        $idUser = (int) $idUser;
        $query = $this->con->query("SELECT * FROM `preventa` where idproducto='$idProducto' and tipo='$tipo' and idUser = $idUser");

        $retorno = [];

        $i = 0;
        while ($fila = $query->fetch_assoc()) {
            $retorno[$i] = $fila;
            $i++;
        }
        return $retorno;

    }

    public function getCantidadProductoChoose($idProducto, $tipo, $idUser)
    {
        $idUser = (int) $idUser;
        $query = $this->con->query("SELECT count(idproducto) as cantidadTotal FROM `preventa` where idproducto='$idProducto' and tipo='$tipo' and idUser = $idUser");

        $retorno = [];

        $i = 0;
        while ($fila = $query->fetch_assoc()) {
            $retorno[$i] = $fila;
            $i++;
        }
        return $retorno;

    }

    /******************Funcion SQL para saber cuantas unidades de un producto estan apartadas en los pedidos*****************************/
    // cuenta los pedidos de TODOS los usuarios, asi dos vendedores no pueden vender la misma unidad a la vez;
    // con $idUserExcluido y $tipoExcluido se descuentan las lineas que el usuario esta editando
    public function getCantidadEnPreventa($idProducto, $idUserExcluido = 0, $tipoExcluido = '')
    {
        $idProducto = (int) $idProducto;
        $idUserExcluido = (int) $idUserExcluido;
        $query = $this->con->query("SELECT count(idproducto) as cantidadTotal FROM `preventa`
                                          where idproducto=$idProducto and NOT (idUser = $idUserExcluido and tipo = '$tipoExcluido')");
        $fila = $query->fetch_assoc();
        return (int) $fila['cantidadTotal'];
    }

     public function getContact($nitClient)
    {
        $query = $this->con->query("SELECT * FROM `cliente`  where  ci='$nitClient'");
        return $query;
    }

    public function getClienteDatos($nitClient)
    {
        $query = $this->con->query("select * from cliente where ci = $nitClient ");
        $retorno = [];

        $i = 0;
        while ($fila = $query->fetch_assoc()) {
            $retorno[$i] = $fila;
            $i++;
        }
        return $retorno;
    }

      public function getDatosFactura()
    {
        $query = $this->con->query("SELECT * FROM `datos`");
        return $query;
    }

    public function getDatosDosificacion()
    {
        $query = $this->con->query("SELECT * FROM `dosificacion`");
        return $query;
    }

     public function registrarDatosPreventa($ci, $nombre, $totalAPagar, $efectivo, $cambio, $fechaVenta, $idcliente, $idUser)
    {
        $idUser = (int) $idUser;
        $query = $this->con->query("INSERT INTO `clientedato` (`idCliente`, `nombre`, `ci`, `fecha`, `totalApagar`, `efectivo`, `cambio`, `idClientei`, `tipoVenta`, `idUser`)
                                            VALUES (NULL , '$nombre', '$ci', '$fechaVenta', '$totalAPagar', '$efectivo', '$cambio', '$idcliente', 'Local', $idUser);");
        return $query;
    }


    public function getDataCliente($idUser)
    {
        $idUser = (int) $idUser;
        $query = $this->con->query("SELECT * FROM `clientedato` WHERE idUser = $idUser order by idcliente DESC  limit 1");
        return $query;
    }

    public function getPedidoTotalForFactura($idUser)
    {
        $idUser = (int) $idUser;
        $query = $this->con->query("SELECT MIN(idpreventa) as idpreventa,imagen,producto,precio, count( idproducto ) AS cantidad, precio*count( idproducto ) as totalPrecio, idproducto, pventa ,tipo
                                          FROM `preventa` WHERE idUser = $idUser GROUP BY idproducto");
        return $query;
    }

    public function getNumFicha($dateInicial, $dateFinal)
    {
        $query = $this->con->query("SELECT (COUNT(*) +1 ) as numficha FROM `ventatotal` WHERE fecha >= '$dateInicial 00:00:00' and fecha <= '$dateFinal 23:59:00'");
        return $query;
    }

/***************************************************Funciones SQL para registrar una venta ************************************************/

    // devuelve el id de la venta recien creada (0 si fallo)
    public function registrarVenta($nombre, $ci, $totalAPagar, $efectivo, $cambio, $idClientei, $codigoControl, $fechaVenta, $idUsuario)
    {
        $idUsuario = (int) $idUsuario;
        $query = $this->con->query("INSERT INTO `ventatotal` (`idVentas`, `nombre`, `ci`, `fecha`, `totalApagar`, `efectivo`, `cambio`, `idClientei`, `codigoControl`, `idUsuario`)
                                            VALUES (NULL, '$nombre', '$ci', '$fechaVenta', '$totalAPagar', '$efectivo', '$cambio', '$idClientei', '$codigoControl', $idUsuario)");
        return $query ? $this->con->insert_id : 0;
    }

    public function getDatosVenta($idVentas)
    {
        $idVentas = (int) $idVentas;
        $query = $this->con->query("SELECT * FROM `ventatotal` WHERE idVentas = $idVentas");
        return $query;
    }


     public function registrarDatosVenta($cantidad, $descripcion, $precio, $total, $tipo, $fechaVenta, $codigoControl, $idVentas, $estado)
    {
        $query = $this->con->query("INSERT INTO `datosventa` (`idDatosVentas`, `cantidad`, `descripcion`, `precio`, `total`, `tipo`, `fechaVenta`, `codigoControl`, `idVentas`, `estado`)
                                      VALUES (NULL, '$cantidad', '$descripcion', '$precio', '$total', '$tipo', '$fechaVenta', '$codigoControl', '$idVentas', '$estado')");
        return $query;
    }

    /******************Funcion SQL para traer lo vendido en un dia, agrupado por producto*****************************/
    public function getVentasDelDia($fecha)
    {
        $query = $this->con->query("SELECT descripcion, precio, SUM(cantidad) as cantidad, SUM(total) as totalVendido, MAX(DATE(fechaVenta)) as fecha
                                          FROM `datosventa`
                                          WHERE fechaVenta >= '$fecha 00:00:00' and fechaVenta <= '$fecha 23:59:59'
                                          GROUP BY descripcion, precio
                                          ORDER BY totalVendido DESC");
        return $query;
    }

    /******************Funcion SQL para descontar del stock la cantidad vendida de un producto*****************************/
    public function descontarStockProducto($idProducto, $cantidadVendida)
    {
        $idProducto = (int) $idProducto;
        $cantidadVendida = (int) $cantidadVendida;
        $query = $this->con->query("UPDATE `producto` SET `cantidad` = CAST(`cantidad` AS SIGNED) - $cantidadVendida
                                          WHERE `idproducto` = $idProducto");
        return $query;
    }

    public function registrarDatosVentaTotal($cliente, $cantidad, $precio, $total, $codigoControl, $fechaVenta, $estado,$comentario)
    {
        $query = $this->con->query("INSERT INTO `datosventatotal` (`idVentas`, `cliente`, `cantidad`, `precio`, `total`, `codigoControl`, `fechaVenta`, `estado`, `comentario`)
                                       VALUES (NULL, '$cliente', '$cantidad', '$precio', '$total', '$codigoControl', '$fechaVenta', '$estado','$comentario')");
        return $query;
    }

     public function registrarDatosClienteVenta($fechaVenta, $nitci, $cliente, $codigoControl, $idVentas, $estado)
    {
        $query = $this->con->query("INSERT INTO `datosclienteventa` (`idClienteVenta`, `fechaVenta`, `nitCliente`, `cliente`, `codigoControl`, `idVentas`, `estado`)
                                             VALUES (NULL, '$fechaVenta', '$nitci', '$cliente', '$codigoControl', '$idVentas', '$estado')");
        return $query;
    }

    public function registrarDatosFacturaVenta($nit, $factura, $numeroAutorizacion, $codigoControl, $idVentas, $estado)
    {
        $query = $this->con->query("INSERT INTO `datosfacturaventa` (`idDatosFactura`, `nit`, `factura`, `numeroAutorizacion`, `codigoControl`, `idVentas`, `estado`)
                                              VALUES (NULL, '$nit', '$factura', '$numeroAutorizacion', '$codigoControl', '$idVentas', '$estado')");
        return $query;
    }

    public function cleanClientData($idUser)
    {
        $idUser = (int) $idUser;
        $query = $this->con->query("DELETE FROM `clientedato` WHERE idUser = $idUser");
        return $query;
    }

     public function cleanRegistroPreventa($idUser)
    {
        $idUser = (int) $idUser;
        $query = $this->con->query("DELETE FROM `preventa` WHERE idUser = $idUser");
        return $query;
    }


    public function updateOpcionElegida($colorElegido,$idMenu)
    {

        $query = $this->con->query("UPDATE `menu` SET `color` = '$colorElegido' WHERE `idmenu` = $idMenu ");

        return $query;
    }

    public function updateOpcionDefecto($colorDefecto,$idMenu)
    {
        $query = $this->con->query("UPDATE `menu` SET `color` = '$colorDefecto' WHERE `idmenu` != $idMenu ");

        return $query;
    }

  


/*******En esta parte se borro todas las consultas sql que eran referentes a el 
modulo pedido dicho modulo no se necesitaba ya que el cliente no lo requeria*********/
    
    


    public function getAllVentas()
    {
        $query = $this->con->query('SELECT * FROM datosventatotal where estado=\'NoConsolidado\' order by idVentas ASC ');
        return $query;
    }

    public function updateDatosclienteventa($codigoControl)
    {
        $query = $this->con->query("UPDATE `datosclienteventa` SET `estado` = 'Consolidado' WHERE `codigoControl` = '$codigoControl'");
        return $query;
    }


    public function updateDatosfacturaventa($codigoControl)
    {
        $query = $this->con->query("UPDATE `datosfacturaventa` SET `estado` = 'Consolidado' WHERE `codigoControl` = '$codigoControl'");
        return $query;
    }

    public function updateDatosventa($codigoControl)
    {
        $query = $this->con->query("UPDATE `datosventa` SET `estado` = 'Consolidado' WHERE `codigoControl` = '$codigoControl'");
        return $query;
    }

    public function updateDatosventatotal($codigoControl)
    {
        $query = $this->con->query("UPDATE `datosventatotal` SET `estado` = 'Consolidado' WHERE `codigoControl` = '$codigoControl'");
        return $query;
    }

    /************************Funciones SQL para sacar los reportes de ventas por dia, semana, mes y anio*******************************/

    public function getVentasDia($fechaInicial,$fechaFinal)
    {
        $query = $this->con->query("SELECT * FROM `datosventatotal` WHERE fechaVenta >= '$fechaInicial' and fechaVenta < '$fechaFinal' and estado='Consolidado'");
        return $query;
    }


    public function getVentasTotalesDia($fechaInicial,$fechaFinal)
    {
        $query = $this->con->query("SELECT SUM(total) as totalVentas FROM `datosventatotal` WHERE fechaVenta >= '$fechaInicial' and fechaVenta < '$fechaFinal' and estado='Consolidado'");
        return $query;
    }

    public function getVentasProductoByDia($fechaInicial,$fechaFinal)
    {
        $query = $this->con->query("SELECT * FROM `datosventa` WHERE fechaVenta >= '$fechaInicial' and fechaVenta < '$fechaFinal' and estado='Consolidado'");
        return $query;
    }

    public function getVentasProductoTotalesDia($fechaInicial,$fechaFinal)
    {
        $query = $this->con->query("SELECT SUM(total) as totalVentas FROM `datosventa` WHERE fechaVenta >= '$fechaInicial' and fechaVenta < '$fechaFinal' and estado='Consolidado'");
        return $query;
    }

    public function getVentasMensuales()
    {

        $query = $this->con->query("SELECT MonthName(fechaVenta) as mes FROM datosventatotal GROUP BY MONTH(fechaVenta) ORDER BY MONTH(fechaVenta) ASC");
        return $query;
    }

    public function getSumaTotalVentasByMes($mes, $anio)
    {
        $query = $this->con->query("SELECT SUM(cantidad * precio) as totalVentas FROM datosventatotal WHERE MONTH(fechaVenta) = '$mes' AND YEAR(fechaVenta) = '$anio'");
        return $query;
    }


    public function getTotalVentasByMes($mes, $anio)
    {
        $query = $this->con->query("SELECT SUM(cantidad * precio) as total, DAY(fechaVenta) as dia FROM datosventatotal WHERE MONTH(fechaVenta) = '$mes' AND YEAR(fechaVenta) = '$anio' GROUP BY DAY(fechaVenta) ORDER BY DAY(fechaVenta) ASC");
        return $query;
    }


    public function getTotalVentasByYear($anio)
    {
        $query = $this->con->query("SELECT SUM(cantidad * precio) as totalVentas FROM datosventatotal WHERE  YEAR(fechaVenta) = '$anio'");
        return $query;
    }

    public function getTotalVentasByAnio($anio)
    {
        $query = $this->con->query("SELECT SUM(cantidad * precio) as total, MonthName(fechaVenta) as mes FROM datosventatotal  WHERE  YEAR(fechaVenta) = '$anio'   GROUP BY MONTH(fechaVenta) ORDER BY MONTH(fechaVenta) ASC");
        return $query;
    }

    public function getTotalVentas6Meses()
    {
       $query = $this->con->query("SELECT SUM(cantidad * precio) as total, MonthName(fechaVenta) as mes FROM datosventatotal  WHERE fechaVenta BETWEEN date_sub(now(), interval 6 month) AND NOW() GROUP BY MONTH(fechaVenta) ORDER BY MONTH(fechaVenta) ASC");
        return $query;
    }

    public function getGrandTotalVentas6Meses()
    {
        $query = $this->con->query("SELECT SUM(cantidad * precio) as totalVentas FROM datosventatotal WHERE fechaVenta BETWEEN date_sub(now(), interval 6 month) AND NOW()");
        return $query;
    }




}


