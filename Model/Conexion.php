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


    public function getAllUserData(){
        
        $query = $this->con->query("SELECT * FROM `usuarios`");

       
         return $query;

    }

}
