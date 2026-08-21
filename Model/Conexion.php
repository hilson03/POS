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
//Funciones de proveedores que nos permiten obtener, crear, actualizar y borrar proveedores de la base de datos************************
     public function getAllProveedor()
    {

        $query = $this->con->query("SELECT * FROM proveedor ");

        return $query;
    }

    public function registerNewProveedor($proveedor, $responsable, $direccion, $telefono, $fechaRegistro)
    {

        $query = $this->con->query("INSERT INTO `proveedor` (`idproveedor`, `proveedor`, `responsable`, `fechaRegistro`, `direccion`, `telefono`, `estado`, `fechaAviso`, `valor`, `valorCobrado`, `saldo`)
        VALUES (NULL, '$proveedor', '$responsable', '$fechaRegistro', '$direccion', '$telefono', '', '$fechaRegistro', 0, 0, '') ");

        return $query;
    }


    public function deleteProveedor($idProveedor)
    {
        $query = $this->con->query("Delete from proveedor where idproveedor=$idProveedor ");

        return $query;
    }

    public function updateProveedor($idProveedor, $proveedor, $responsable, $direccion, $telefono, $fechaRegistro)
    {

        $query = $this->con->query("UPDATE `proveedor` SET `proveedor` = '$proveedor',
                                            `responsable` = '$responsable',
                                            `fechaRegistro` = '$fechaRegistro',
                                            `direccion` = '$direccion',
                                             `telefono` = '$telefono' WHERE `proveedor`.`idproveedor` = $idProveedor");

        return $query;
    }

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
     public function getAllActivos()
    {
        $query = $this->con->query("SELECT * FROM activos ");

        return $query;
    }

    public function registerNewActivo($imagen, $codigo, $nombreProducto, $cantidad, $fechaRegistro)
    {

        $query = $this->con->query("INSERT INTO `activos` (`idactivo`, `imagen`, `codigo`, `nombreProducto`, `cantidad`, `fechaRegistro`)
                                          VALUES (NULL, '$imagen', '$codigo', '$nombreProducto', '$cantidad', '$fechaRegistro')");

        return $query;
    }

    public function deleteActivo($idproducto)
    {
        $query = $this->con->query("Delete from activos where idactivo=$idproducto");

        return $query;
    }

    public function updateActivo($imagen, $codigo, $nombreProducto, $cantidad, $fechaRegistro, $idproducto)
    {

        $query = $this->con->query("UPDATE `activos` SET `imagen` = '$imagen',
                                                     `codigo` = '$codigo',
                                                     `nombreProducto` = '$nombreProducto',
                                                     `cantidad` = '$cantidad',
                                                     `fechaRegistro` = '$fechaRegistro'
                                                      WHERE `activos`.`idactivo` = $idproducto");

        return $query;
    }
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

    public function getPreventa()
    {
        $query = $this->con->query("SELECT idPreventa,imagen,producto,COUNT(producto) as cantidad, SUM(precio) as totalPrecio,idProducto,pventa,idUser,precio,tipo
                                            FROM `preventa`
                                            GROUP BY producto,idProducto,tipo
                                            ORDER BY idPreventa ASC");
        return $query;
    }

     public function getTotalPreventa()
    {
        $query = $this->con->query("SELECT Sum(precio) as total , idUser FROM `preventa`");
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

    public function deleteOnlyPreventa($idProducto, $tipo)
    {
        $query = $this->con->query("Delete from preventa where idproducto='$idProducto'  and  tipo='$tipo'");
        return $query;
    }

    public function deleteAllPreventa()
    {
        $query = $this->con->query("TRUNCATE `preventa`");
        return $query;
    }


    public function getDataProductoChoose($idProducto, $tipo)
    {

        $query = $this->con->query("SELECT * FROM `preventa` where idproducto='$idProducto' and tipo='$tipo'");

        $retorno = [];

        $i = 0;
        while ($fila = $query->fetch_assoc()) {
            $retorno[$i] = $fila;
            $i++;
        }
        return $retorno;

    }

    public function getCantidadProductoChoose($idProducto, $tipo)
    {
        $query = $this->con->query("SELECT count(idproducto) as cantidadTotal FROM `preventa` where idproducto='$idProducto' and tipo='$tipo'");

        $retorno = [];

        $i = 0;
        while ($fila = $query->fetch_assoc()) {
            $retorno[$i] = $fila;
            $i++;
        }
        return $retorno;

    }

     public function getContact($nitClient)
    {
        $query = $this->con->query("SELECT * FROM `cliente`  where  ci='$nitClient'");
        return $query;
    }





}


