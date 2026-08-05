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

//esta funcion atraves de una consulta trae toda la informacion de los usuarios.
    public function getAllUserData(){
        
        $query = $this->con->query("SELECT * FROM `usuarios`");

       
         return $query;

    }


//esta funcion sirve para registrar nuevos usuarios con imagen
//recordar ver como resolver lo de la imagen de perfil de usuario
    public function getRegisterNewUser($nombre, $tipo, $usuario, $password, $imagenUsuario){

    $query = $this->con->query("INSERT INTO `usuarios`(`id_usu`,`login`,`tipo`,`nombre`,`password`,`foto`)
                            VALUES(NULL, '$usuario', '$tipo','$nombre','$password','$imagenUsuario')");

    return $query;

    }


    public function updateMensajeAlert($mensaje, $alerta)
    {
        $query = $this->con->query("UPDATE `alerta` SET `tipoAlerta` = '$alerta',
                                                `mensaje` = '$mensaje'  WHERE `alerta`.`alertaId` = 1");
        return $query;
    }


    public function deleteUsuario($idUsuario)
    {

        $query = $this->con->query("DELETE FROM usuarios Where id_usu=$idUsuario ");

        return $query;
    }

}


