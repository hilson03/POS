<?php
class conexion{

    public $con;
    
    public function __construct(){
        $user = 'root';
        $password = '';
        $server = 'localhost';
        $database = 'icontpos';
        $this-> con = new mysqli($server, $user, $password, $database); 
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






}


