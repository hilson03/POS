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

    /*
     * Ejecuta una consulta preparada: los datos van aparte del SQL (marcados con ?), asi lo que escribe
     * el usuario nunca se interpreta como parte de la consulta (protege contra inyeccion SQL).
     * Devuelve un mysqli_result en los SELECT y true/false en INSERT, UPDATE y DELETE.
     */
    private function ejecutar($sql, $parametros = array())
    {
        $stmt = $this->con->prepare($sql);

        if (!empty($parametros)) {
            $valores = array();
            foreach ($parametros as $parametro) {
                $valores[] = $parametro === null ? null : (string) $parametro;
            }
            $stmt->bind_param(str_repeat('s', count($valores)), ...$valores);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();

        return $resultado === false ? true : $resultado;
    }

    // convierte un resultado en un arreglo de filas (lo que devolvian las funciones que hacian el while a mano)
    private function filas($resultado)
    {
        $retorno = array();
        while ($fila = $resultado->fetch_assoc()) {
            $retorno[] = $fila;
        }
        return $retorno;
    }

    /*
     * Busca al usuario por login y valida la contrasena.
     * Las contrasenas se guardan cifradas con password_hash; si una todavia esta en texto plano
     * (usuarios creados antes de este cambio) se valida igual y se cifra en ese momento.
     */
    public function getUser($usuario, $password){
        $usuarios = $this->filas($this->ejecutar("SELECT * FROM usuarios WHERE login = ?", array($usuario)));

        foreach ($usuarios as $fila) {
            $guardada = $fila['password'];
            $esHash = !empty(password_get_info($guardada)['algo']);

            if ($esHash && password_verify($password, $guardada)) {
                return array($fila);
            }

            if (!$esHash && $guardada !== '' && hash_equals($guardada, (string) $password)) {
                $this->ejecutar("UPDATE usuarios SET password = ? WHERE id_usu = ?",
                    array(password_hash($password, PASSWORD_DEFAULT), $fila['id_usu']));
                return array($fila);
            }
        }

        return array();
    }

   public function getMenuMain(){

        return $this->filas($this->ejecutar("SELECT * FROM `menu`"));

    }

//***************************esta funcion atraves de una consulta trae toda la informacion de los usuarios.******************************
    public function getAllUserData(){

        return $this->ejecutar("SELECT u.*, COALESCE(s.nombre, '(sin sucursal)') AS nombreSucursal FROM `usuarios` u LEFT JOIN sucursal s ON s.idSucursal = u.idSucursal");

    }


//***********************esta funcion sirve para registrar nuevos usuarios con imagen******************
    public function getRegisterNewUser($nombre, $tipo, $usuario, $password, $imagenUsuario, $idSucursal){

        return $this->ejecutar("INSERT INTO `usuarios`(`id_usu`,`login`,`tipo`,`nombre`,`password`,`foto`,`idSucursal`) VALUES(NULL, ?, ?, ?, ?, ?, ?)",
            array($usuario, $tipo, $nombre, password_hash($password, PASSWORD_DEFAULT), $imagenUsuario, (int) $idSucursal));

    }


    public function deleteUsuario($idUsuario)
    {

        return $this->ejecutar("DELETE FROM usuarios Where id_usu = ?", array((int) $idUsuario));
    }

    //*********************esta consulta permite actualizar la informacion del usuario********************************
    // si $password viene vacio se conserva la contrasena actual
    public function updateUsuario($login, $tipo, $nombre, $password, $foto, $idUsuario, $idSucursal)
    {
        if ($password === null || $password === '') {
            return $this->ejecutar("UPDATE `usuarios` SET `login` = ?, `tipo` = ?, `nombre` = ?, `foto` = ?, `idSucursal` = ? WHERE `id_usu` = ?",
                array($login, $tipo, $nombre, $foto, (int) $idSucursal, (int) $idUsuario));
        }

        return $this->ejecutar("UPDATE `usuarios` SET `login` = ?, `tipo` = ?, `nombre` = ?, `password` = ?, `foto` = ?, `idSucursal` = ? WHERE `id_usu` = ?",
            array($login, $tipo, $nombre, password_hash($password, PASSWORD_DEFAULT), $foto, (int) $idSucursal, (int) $idUsuario));
    }


    public function getMensajeAlerta()
    {

        return $this->filas($this->ejecutar("SELECT * FROM `alerta`"));

    }
//*************esta funcion sirve para ver un mensaje de alerta ya se por que se creo, actualizo o elimino un usuario.**********
    public function updateMensajeAlert($mensaje, $alerta)
    {
        return $this->ejecutar("UPDATE `alerta` SET `tipoAlerta` = ?, `mensaje` = ? WHERE `alerta`.`alertaId` = 1", array($alerta, $mensaje));
    }

//******************esta funciones sirven para obtener datos de facturas y actualizarlos al mismo tiempo*******************
    public function getDataFactura(){

        return $this->ejecutar("SELECT * FROM `datos`");

    }


    public function updateDataFactura($iddatos,$propietario, $razon, $direccion, $nro, $telefono){

        return $this->ejecutar("UPDATE `datos` SET `propietario` = ?, `razon` = ?, `direccion` = ?, `nro` = ?, `telefono` = ? WHERE `datos`.`iddatos` = ?",
            array($propietario, $razon, $direccion, $nro, $telefono, (int) $iddatos));

    }


//******************************metodo para que puedan funcionar el cambio de moneda****************************

    public function getMoneda()
        {
            return $this->ejecutar("SELECT * FROM `moneda`");
        }

    public function updateDataMoneda($idMoneda, $pais, $tipoMoneda, $contexto){

        return $this->ejecutar("UPDATE `moneda` SET `pais` = ?, `tipoMoneda` = ?, `contexto` = ? WHERE `moneda`.`idMoneda` = ?",
            array($pais, $tipoMoneda, $contexto, (int) $idMoneda));

    }

//*************************************Metodos para cambiar idiomas*********************************************
    public function getIdioma()
    {
        return $this->ejecutar("SELECT * FROM `idioma`");
    }

    public function updateDataIdioma($idioma, $idIdioma)
    {

        return $this->ejecutar("UPDATE `idioma` SET `idioma` = ? WHERE `idioma`.`idIdioma` = ?", array($idioma, (int) $idIdioma));
    }

    public function updateIdiomaSistem($opcionMenu, $idIdioma)
    {
        return $this->ejecutar("UPDATE `menu` SET `opcion` = ? WHERE `menu`.`idmenu` = ?", array($opcionMenu, (int) $idIdioma));
    }
/**Funciones de proveedores que nos permiten obtener, crear, actualizar y borrar proveedores de la base de datos
fueron borradas ya que el cliente no necesitaba ese modulo en esta version del POS************************/

//funcion sql para obtener una lista de todos los clientes tambien para crear, modificar y borrar
    public function getAllCliente()
    {

        return $this->ejecutar("SELECT * FROM cliente ");
    }


    public function registerNewCliente($imagen, $nombre, $apellido, $direccion, $telefonoFijo, $telefonoCelular, $email, $fechaRegistro, $ci)
    {

        return $this->ejecutar("INSERT INTO `cliente` (`idcliente`, `foto`, `nombre`, `apellido`, `direccion`, `telefonoFijo`, `telefonoCelular`, `email`, `contactoReferencia`, `telefonoReferencia`, `observaciones`, `fechaRegistro`, `ci`)
                                     VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, '', '', '', ?, ?)",
            array($imagen, $nombre, $apellido, $direccion, $telefonoFijo, $telefonoCelular, $email, $fechaRegistro, $ci));
    }

    public function updateClient($idcliente, $imagen, $nombre, $apellido, $direccion, $telefonoFijo, $telefonoCelular, $email, $fechaRegistro, $ci)
    {

        return $this->ejecutar("UPDATE `cliente` SET `foto` = ?, `nombre` = ?, `apellido` = ?, `direccion` = ?, `telefonoFijo` = ?,
                                                `telefonoCelular` = ?, `email` = ?, `fechaRegistro` = ?, `ci` = ? WHERE `cliente`.`idcliente` = ?",
            array($imagen, $nombre, $apellido, $direccion, $telefonoFijo, $telefonoCelular, $email, $fechaRegistro, $ci, (int) $idcliente));
    }


     public function deleteClient($idClient)
    {
        return $this->ejecutar("Delete from cliente where idcliente = ?", array((int) $idClient));
    }

//******************************Sucursales*******************************************
    public function getSucursales($soloActivas = false)
    {
        if ($soloActivas) {
            return $this->filas($this->ejecutar("SELECT * FROM sucursal WHERE estado = 'Activo' ORDER BY nombre"));
        }
        return $this->filas($this->ejecutar("SELECT * FROM sucursal ORDER BY nombre"));
    }

    public function getSucursal($idSucursal)
    {
        $filas = $this->filas($this->ejecutar("SELECT * FROM sucursal WHERE idSucursal = ?", array((int) $idSucursal)));
        return empty($filas) ? null : $filas[0];
    }

    public function registrarSucursal($nombre, $direccion, $telefono)
    {
        return $this->ejecutar("INSERT INTO sucursal (nombre, direccion, telefono, estado) VALUES (?, ?, ?, 'Activo')",
            array($nombre, $direccion, $telefono));
    }

    public function updateSucursal($idSucursal, $nombre, $direccion, $telefono, $estado)
    {
        return $this->ejecutar("UPDATE sucursal SET nombre = ?, direccion = ?, telefono = ?, estado = ? WHERE idSucursal = ?",
            array($nombre, $direccion, $telefono, $estado, (int) $idSucursal));
    }

    // cuantos usuarios tienen asignada la sucursal (no se puede desactivar si todavia tiene usuarios)
    public function contarUsuariosSucursal($idSucursal)
    {
        return (int) $this->ejecutar("SELECT count(*) c FROM usuarios WHERE idSucursal = ?", array((int) $idSucursal))->fetch_assoc()['c'];
    }

//******************************Stock por sucursal*******************************************
    // fija el stock de un producto en una sucursal (lo crea si no existia)
    public function setStockSucursal($idProducto, $idSucursal, $cantidad)
    {
        return $this->ejecutar("INSERT INTO stock_sucursal (idProducto, idSucursal, cantidad) VALUES (?, ?, ?)
                                       ON DUPLICATE KEY UPDATE cantidad = VALUES(cantidad)",
            array((int) $idProducto, (int) $idSucursal, (int) $cantidad));
    }

    // stock de un producto en cada sucursal activa (para ver donde hay unidades disponibles)
    public function getStockPorSucursal($idProducto)
    {
        return $this->filas($this->ejecutar("SELECT s.idSucursal, s.nombre, COALESCE(st.cantidad, 0) AS cantidad
                                                    FROM sucursal s
                                                    LEFT JOIN stock_sucursal st ON st.idSucursal = s.idSucursal AND st.idProducto = ?
                                                    WHERE s.estado = 'Activo' ORDER BY s.nombre", array((int) $idProducto)));
    }

//******************************funcion SQL permite traer todos los productos*******************************************
    /*
     * Productos con el stock de la sucursal indicada en la columna `cantidad` (misma posicion y nombre que en la
     * tabla producto, asi las pantallas que ya leian $product['cantidad'] muestran el stock de la sucursal).
     */
    private function sqlProductoConStock()
    {
        return "SELECT p.idproducto, p.imagen, p.codigo, p.nombreProducto, COALESCE(st.cantidad, 0) AS cantidad, p.fechaRegistro,
                       p.precioVenta, p.tipo, p.proveedor, p.precioCompra
                FROM producto p
                LEFT JOIN stock_sucursal st ON st.idProducto = p.idproducto AND st.idSucursal = ?";
    }

    public function getAllProducto($idSucursal)
    {

        return $this->ejecutar($this->sqlProductoConStock() . " ORDER BY p.idproducto", array((int) $idSucursal));
    }


    public function getAllTipoProducto()
    {
        return $this->ejecutar("SELECT * FROM tipoproducto");
    }


    // el producto es del catalogo compartido; $cantidad es el stock inicial de la sucursal donde se registra
    // (producto.cantidad ya no se usa: el stock vive en stock_sucursal)
    public function registerNewProducto($imagen, $codigo, $nombreProducto, $cantidad, $fechaRegistro, $precioVenta, $tipo, $proveedor, $precioCompra, $idSucursal)
    {

        $this->ejecutar("INSERT INTO `producto` (`idproducto`, `imagen`, `codigo`, `nombreProducto`, `cantidad`, `fechaRegistro`, `precioVenta`, `tipo`, `proveedor`, `precioCompra`)
                                          VALUES (NULL, ?, ?, ?, '0', ?, ?, ?, ?, ?)",
            array($imagen, $codigo, $nombreProducto, $fechaRegistro, $precioVenta, $tipo, (string) $proveedor, $precioCompra));
        $idProducto = $this->con->insert_id;

        return $this->setStockSucursal($idProducto, $idSucursal, $cantidad);
    }

    public function deleteProduct($idproducto)
    {
        $this->ejecutar("DELETE FROM stock_sucursal WHERE idProducto = ?", array((int) $idproducto));
        return $this->ejecutar("Delete from producto where idproducto = ?", array((int) $idproducto));
    }

    // actualiza los datos del catalogo y el stock del producto en la sucursal indicada
     public function updateProduct($imagen, $codigo, $nombreProducto, $cantidad, $fechaRegistro, $precioVenta, $tipo, $proveedor, $precioCompra, $idproducto, $idSucursal)
    {

        $this->ejecutar("UPDATE `producto` SET `imagen` = ?, `codigo` = ?, `nombreProducto` = ?, `fechaRegistro` = ?,
                                                     `precioVenta` = ?, `tipo` = ?, `proveedor` = ?, `precioCompra` = ? WHERE `producto`.`idproducto` = ?",
            array($imagen, $codigo, $nombreProducto, $fechaRegistro, $precioVenta, $tipo, (string) $proveedor, $precioCompra, (int) $idproducto));

        return $this->setStockSucursal($idproducto, $idSucursal, $cantidad);
    }

//*****************************************Funcion SQL para registrar nuevos tipos de producto******************************************
    public function registerNewTipoProduct($tipoProducto)
    {
        return $this->ejecutar("INSERT INTO `tipoproducto` (`idtipoproducto`, `tipoproducto`) VALUES (NULL, ?)", array($tipoProducto));

    }

    public function deleteTipoProduct($tipoProductoId)
    {
        return $this->ejecutar("Delete from tipoproducto where idtipoproducto = ?", array((int) $tipoProductoId));
    }


    public function updateTipoProducto($tipoProductoId, $tipoproducto)
    {
        return $this->ejecutar("UPDATE `tipoproducto` SET `tipoproducto` = ? WHERE `tipoproducto`.`idtipoproducto` = ?",
            array($tipoproducto, (int) $tipoProductoId));
    }

//*************************************metodo para obtener todos los tipos de monedas**************************************
    public function getTipoMoneda()
    {

        return $this->filas($this->ejecutar("SELECT * FROM `moneda`"));

    }
//consulta para hacer una preVenta

    public function getPreventa($idUser)
    {
        return $this->ejecutar("SELECT MIN(idPreventa) as idPreventa,imagen,producto,COUNT(producto) as cantidad, SUM(precio) as totalPrecio,idProducto,pventa,idUser,precio,tipo
                                            FROM `preventa`
                                            WHERE idUser = ?
                                            GROUP BY producto,idProducto,tipo
                                            ORDER BY idPreventa ASC", array((int) $idUser));
    }

     public function getTotalPreventa($idUser)
    {
        return $this->ejecutar("SELECT Sum(precio) as total , idUser FROM `preventa` WHERE idUser = ? HAVING COUNT(*) > 0", array((int) $idUser));
    }

    /******************Funcion SQL para traer datos de usuario relacionados con el pedido modificar o eliminar*****************************/

    public function getOnlyUserData($idUser)
    {
        return $this->filas($this->ejecutar("SELECT * FROM usuarios where id_usu = ?", array((int) $idUser)));
    }


    public function getProductoElegido($idproducto, $idSucursal)
    {

        return $this->filas($this->ejecutar($this->sqlProductoConStock() . " WHERE p.idproducto = ?", array((int) $idSucursal, (int) $idproducto)));

    }

    public function insertarPreventaProducto($imagen, $producto, $precio, $idProducto, $pventa, $idUser, $tipo, $idSucursal)
    {
        return $this->ejecutar("INSERT INTO `preventa` (`idPreventa`, `imagen`, `producto`, `precio`, `idProducto`, `pventa`, `idUser`, `tipo`, `idSucursal`)
                                          VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)",
            array($imagen, $producto, $precio, $idProducto, $pventa, $idUser, $tipo, (int) $idSucursal));
    }

    public function contarPreventaUsuario($idUser)
    {
        return (int) $this->ejecutar("SELECT count(*) c FROM preventa WHERE idUser = ?", array((int) $idUser))->fetch_assoc()['c'];
    }

    public function deleteOnlyPreventa($idProducto, $tipo, $idUser)
    {
        return $this->ejecutar("Delete from preventa where idproducto = ? and tipo = ? and idUser = ?", array($idProducto, $tipo, (int) $idUser));
    }

    public function deleteAllPreventa($idUser)
    {
        return $this->ejecutar("DELETE FROM `preventa` WHERE idUser = ?", array((int) $idUser));
    }


    public function getDataProductoChoose($idProducto, $tipo, $idUser)
    {
        return $this->filas($this->ejecutar("SELECT * FROM `preventa` where idproducto = ? and tipo = ? and idUser = ?",
            array($idProducto, $tipo, (int) $idUser)));

    }

    public function getCantidadProductoChoose($idProducto, $tipo, $idUser)
    {
        return $this->filas($this->ejecutar("SELECT count(idproducto) as cantidadTotal FROM `preventa` where idproducto = ? and tipo = ? and idUser = ?",
            array($idProducto, $tipo, (int) $idUser)));

    }

    /******************Funcion SQL para saber cuantas unidades de un producto estan apartadas en los pedidos*****************************/
    // cuenta los pedidos de TODOS los usuarios de la sucursal, asi dos vendedores no pueden vender la misma unidad a la vez;
    // con $idUserExcluido y $tipoExcluido se descuentan las lineas que el usuario esta editando
    public function getCantidadEnPreventa($idProducto, $idSucursal, $idUserExcluido = 0, $tipoExcluido = '')
    {
        $fila = $this->ejecutar("SELECT count(idproducto) as cantidadTotal FROM `preventa`
                                          where idproducto = ? and idSucursal = ? and NOT (idUser = ? and tipo = ?)",
            array((int) $idProducto, (int) $idSucursal, (int) $idUserExcluido, $tipoExcluido))->fetch_assoc();
        return (int) $fila['cantidadTotal'];
    }

     public function getContact($nitClient)
    {
        return $this->ejecutar("SELECT * FROM `cliente`  where  ci = ?", array($nitClient));
    }

    public function getClienteDatos($nitClient)
    {
        return $this->filas($this->ejecutar("select * from cliente where ci = ?", array($nitClient)));
    }

      public function getDatosFactura()
    {
        return $this->ejecutar("SELECT * FROM `datos`");
    }

    public function getDatosDosificacion()
    {
        return $this->ejecutar("SELECT * FROM `dosificacion`");
    }

     public function registrarDatosPreventa($ci, $nombre, $totalAPagar, $efectivo, $cambio, $fechaVenta, $idcliente, $idUser)
    {
        return $this->ejecutar("INSERT INTO `clientedato` (`idCliente`, `nombre`, `ci`, `fecha`, `totalApagar`, `efectivo`, `cambio`, `idClientei`, `tipoVenta`, `idUser`)
                                            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, 'Local', ?)",
            array($nombre, $ci, $fechaVenta, $totalAPagar, $efectivo, $cambio, $idcliente, $idUser));
    }


    public function getDataCliente($idUser)
    {
        return $this->ejecutar("SELECT * FROM `clientedato` WHERE idUser = ? order by idcliente DESC  limit 1", array((int) $idUser));
    }

    public function getPedidoTotalForFactura($idUser)
    {
        return $this->ejecutar("SELECT MIN(idpreventa) as idpreventa,imagen,producto,precio, count( idproducto ) AS cantidad, precio*count( idproducto ) as totalPrecio, idproducto, pventa ,tipo
                                          FROM `preventa` WHERE idUser = ? GROUP BY idproducto", array((int) $idUser));
    }

    public function getNumFicha($dateInicial, $dateFinal)
    {
        return $this->ejecutar("SELECT (COUNT(*) +1 ) as numficha FROM `ventatotal` WHERE fecha >= ? and fecha <= ?",
            array($dateInicial . ' 00:00:00', $dateFinal . ' 23:59:00'));
    }

/***************************************************Funciones SQL para registrar una venta ************************************************/

    // devuelve el id de la venta recien creada (0 si fallo)
    public function registrarVenta($nombre, $ci, $totalAPagar, $efectivo, $cambio, $idClientei, $codigoControl, $fechaVenta, $idUsuario, $idSucursal)
    {
        $query = $this->ejecutar("INSERT INTO `ventatotal` (`idVentas`, `nombre`, `ci`, `fecha`, `totalApagar`, `efectivo`, `cambio`, `idClientei`, `codigoControl`, `idUsuario`, `idSucursal`)
                                            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array($nombre, $ci, $fechaVenta, $totalAPagar, $efectivo, $cambio, $idClientei, $codigoControl, $idUsuario, (int) $idSucursal));
        return $query ? $this->con->insert_id : 0;
    }

    public function getDatosVenta($idVentas)
    {
        return $this->ejecutar("SELECT * FROM `ventatotal` WHERE idVentas = ?", array((int) $idVentas));
    }


     public function registrarDatosVenta($cantidad, $descripcion, $precio, $total, $tipo, $fechaVenta, $codigoControl, $idVentas, $estado, $idSucursal)
    {
        return $this->ejecutar("INSERT INTO `datosventa` (`idDatosVentas`, `cantidad`, `descripcion`, `precio`, `total`, `tipo`, `fechaVenta`, `codigoControl`, `idVentas`, `estado`, `idSucursal`)
                                      VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array($cantidad, $descripcion, $precio, $total, $tipo, $fechaVenta, $codigoControl, $idVentas, $estado, (int) $idSucursal));
    }

    /******************Funcion SQL para traer lo vendido en un dia, agrupado por producto*****************************/
    public function getVentasDelDia($fecha, $idSucursal)
    {
        return $this->ejecutar("SELECT descripcion, precio, SUM(cantidad) as cantidad, SUM(total) as totalVendido, MAX(DATE(fechaVenta)) as fecha
                                          FROM `datosventa`
                                          WHERE fechaVenta >= ? and fechaVenta <= ? and idSucursal = ?
                                          GROUP BY descripcion, precio
                                          ORDER BY totalVendido DESC", array($fecha . ' 00:00:00', $fecha . ' 23:59:59', (int) $idSucursal));
    }

    /******************Funcion SQL para descontar del stock de la sucursal la cantidad vendida de un producto*****************************/
    public function descontarStockProducto($idProducto, $cantidadVendida, $idSucursal)
    {
        return $this->ejecutar("INSERT INTO stock_sucursal (idProducto, idSucursal, cantidad) VALUES (?, ?, ?)
                                       ON DUPLICATE KEY UPDATE cantidad = cantidad - ?",
            array((int) $idProducto, (int) $idSucursal, -(int) $cantidadVendida, (int) $cantidadVendida));
    }

    // una fila por venta para Consolidar y los reportes: $total es el total de la venta y $cantidad las unidades vendidas
    public function registrarDatosVentaTotal($cliente, $cantidad, $precio, $total, $codigoControl, $fechaVenta, $estado, $comentario, $idVentaTotal, $idUsuario, $idSucursal)
    {
        return $this->ejecutar("INSERT INTO `datosventatotal` (`idVentas`, `cliente`, `cantidad`, `precio`, `total`, `codigoControl`, `fechaVenta`, `estado`, `comentario`, `idVentaTotal`, `idUsuario`, `idSucursal`)
                                       VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            array($cliente, $cantidad, $precio, $total, $codigoControl, $fechaVenta, $estado, $comentario, (int) $idVentaTotal, (int) $idUsuario, (int) $idSucursal));
    }

     public function registrarDatosClienteVenta($fechaVenta, $nitci, $cliente, $codigoControl, $idVentas, $estado)
    {
        return $this->ejecutar("INSERT INTO `datosclienteventa` (`idClienteVenta`, `fechaVenta`, `nitCliente`, `cliente`, `codigoControl`, `idVentas`, `estado`)
                                             VALUES (NULL, ?, ?, ?, ?, ?, ?)",
            array($fechaVenta, $nitci, $cliente, $codigoControl, $idVentas, $estado));
    }

    public function registrarDatosFacturaVenta($nit, $factura, $numeroAutorizacion, $codigoControl, $idVentas, $estado)
    {
        return $this->ejecutar("INSERT INTO `datosfacturaventa` (`idDatosFactura`, `nit`, `factura`, `numeroAutorizacion`, `codigoControl`, `idVentas`, `estado`)
                                              VALUES (NULL, ?, ?, ?, ?, ?, ?)",
            array($nit, $factura, $numeroAutorizacion, $codigoControl, $idVentas, $estado));
    }

    public function cleanClientData($idUser)
    {
        return $this->ejecutar("DELETE FROM `clientedato` WHERE idUser = ?", array((int) $idUser));
    }

     public function cleanRegistroPreventa($idUser)
    {
        return $this->ejecutar("DELETE FROM `preventa` WHERE idUser = ?", array((int) $idUser));
    }


    public function updateOpcionElegida($colorElegido,$idMenu)
    {

        return $this->ejecutar("UPDATE `menu` SET `color` = ? WHERE `idmenu` = ?", array($colorElegido, $idMenu));
    }

    public function updateOpcionDefecto($colorDefecto,$idMenu)
    {
        return $this->ejecutar("UPDATE `menu` SET `color` = ? WHERE `idmenu` != ?", array($colorDefecto, $idMenu));
    }

/*******En esta parte se borro todas las consultas sql que eran referentes a el
modulo pedido dicho modulo no se necesitaba ya que el cliente no lo requeria*********/


    // ventas pendientes de consolidar de una sucursal; con $idUsuario solo las de ese vendedor (0 = todas, para el administrador)
    public function getAllVentas($idSucursal, $idUsuario = 0)
    {
        if ((int) $idUsuario > 0) {
            return $this->ejecutar("SELECT * FROM datosventatotal where estado = 'NoConsolidado' and idSucursal = ? and idUsuario = ? order by idVentas ASC",
                array((int) $idSucursal, (int) $idUsuario));
        }
        return $this->ejecutar("SELECT * FROM datosventatotal where estado = 'NoConsolidado' and idSucursal = ? order by idVentas ASC", array((int) $idSucursal));
    }

    // fila de la lista de Consolidar (datosventatotal.idVentas)
    public function getVentaConsolidar($idFila)
    {
        $filas = $this->filas($this->ejecutar("SELECT * FROM datosventatotal WHERE idVentas = ?", array((int) $idFila)));
        return empty($filas) ? null : $filas[0];
    }

    /*
     * Consolida UNA venta. Antes se consolidaba por codigo de control, pero ese codigo se repite entre ventas
     * y se consolidaban ventas ajenas. Ahora se usa el numero de venta; las ventas antiguas que no quedaron
     * vinculadas usan su fecha exacta + codigo de control (todas las tablas de una venta comparten la misma fecha).
     */
    public function consolidarVenta($idFila)
    {
        $venta = $this->getVentaConsolidar($idFila);
        if ($venta === null) {
            return false;
        }

        if ((int) $venta['idVentaTotal'] > 0) {
            $idVenta = (int) $venta['idVentaTotal'];
            $this->ejecutar("UPDATE `datosventa` SET `estado` = 'Consolidado' WHERE `idVentas` = ?", array($idVenta));
            $this->ejecutar("UPDATE `datosclienteventa` SET `estado` = 'Consolidado' WHERE `idVentas` = ?", array($idVenta));
            $this->ejecutar("UPDATE `datosfacturaventa` SET `estado` = 'Consolidado' WHERE `idVentas` = ?", array($idVenta));
        } else {
            $datos = array($venta['codigoControl'], $venta['fechaVenta']);
            $this->ejecutar("UPDATE `datosventa` SET `estado` = 'Consolidado' WHERE `codigoControl` = ? AND `fechaVenta` = ?", $datos);
            $this->ejecutar("UPDATE `datosclienteventa` SET `estado` = 'Consolidado' WHERE `codigoControl` = ? AND `fechaVenta` = ?", $datos);
        }

        return $this->ejecutar("UPDATE `datosventatotal` SET `estado` = 'Consolidado' WHERE `idVentas` = ?", array((int) $idFila));
    }

    public function insertarComentarioFicha($idFila, $comentario)
    {
        return $this->ejecutar("UPDATE `datosventatotal` SET `comentario` = ? WHERE `idVentas` = ?", array($comentario, (int) $idFila));
    }

    /************************Funciones SQL para sacar los reportes de ventas por dia, semana, mes y anio*******************************/

    public function getVentasDia($fechaInicial,$fechaFinal)
    {
        return $this->ejecutar("SELECT * FROM `datosventatotal` WHERE fechaVenta >= ? and fechaVenta < ? and estado = 'Consolidado'",
            array($fechaInicial, $fechaFinal));
    }


    public function getVentasTotalesDia($fechaInicial,$fechaFinal)
    {
        return $this->ejecutar("SELECT SUM(total) as totalVentas FROM `datosventatotal` WHERE fechaVenta >= ? and fechaVenta < ? and estado = 'Consolidado'",
            array($fechaInicial, $fechaFinal));
    }

    public function getVentasProductoByDia($fechaInicial,$fechaFinal)
    {
        return $this->ejecutar("SELECT * FROM `datosventa` WHERE fechaVenta >= ? and fechaVenta < ? and estado = 'Consolidado'",
            array($fechaInicial, $fechaFinal));
    }

    public function getVentasProductoTotalesDia($fechaInicial,$fechaFinal)
    {
        return $this->ejecutar("SELECT SUM(total) as totalVentas FROM `datosventa` WHERE fechaVenta >= ? and fechaVenta < ? and estado = 'Consolidado'",
            array($fechaInicial, $fechaFinal));
    }

    public function getVentasMensuales()
    {

        return $this->ejecutar("SELECT MonthName(fechaVenta) as mes FROM datosventatotal GROUP BY MONTH(fechaVenta) ORDER BY MONTH(fechaVenta) ASC");
    }

    public function getSumaTotalVentasByMes($mes, $anio)
    {
        return $this->ejecutar("SELECT SUM(total) as totalVentas FROM datosventatotal WHERE MONTH(fechaVenta) = ? AND YEAR(fechaVenta) = ?",
            array($mes, $anio));
    }


    public function getTotalVentasByMes($mes, $anio)
    {
        return $this->ejecutar("SELECT SUM(total) as total, DAY(fechaVenta) as dia FROM datosventatotal WHERE MONTH(fechaVenta) = ? AND YEAR(fechaVenta) = ?
                                          GROUP BY DAY(fechaVenta) ORDER BY DAY(fechaVenta) ASC", array($mes, $anio));
    }


    public function getTotalVentasByYear($anio)
    {
        return $this->ejecutar("SELECT SUM(total) as totalVentas FROM datosventatotal WHERE  YEAR(fechaVenta) = ?", array($anio));
    }

    public function getTotalVentasByAnio($anio)
    {
        return $this->ejecutar("SELECT SUM(total) as total, MonthName(fechaVenta) as mes FROM datosventatotal  WHERE  YEAR(fechaVenta) = ?
                                          GROUP BY MONTH(fechaVenta) ORDER BY MONTH(fechaVenta) ASC", array($anio));
    }

    public function getTotalVentas6Meses()
    {
       return $this->ejecutar("SELECT SUM(total) as total, MonthName(fechaVenta) as mes FROM datosventatotal  WHERE fechaVenta BETWEEN date_sub(now(), interval 6 month) AND NOW() GROUP BY MONTH(fechaVenta) ORDER BY MONTH(fechaVenta) ASC");
    }

    public function getGrandTotalVentas6Meses()
    {
        return $this->ejecutar("SELECT SUM(total) as totalVentas FROM datosventatotal WHERE fechaVenta BETWEEN date_sub(now(), interval 6 month) AND NOW()");
    }

}
