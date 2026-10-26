<?php
require_once('../Model/Conexion.php');
require('Constans.php');
require_once('Sesion.php');
$con = new conexion();

iniciarSesionSegura();
$usuarioSesion = requerirSesion('', true);
$nitCliente = (isset($_POST['nitClient']) && is_string($_POST['nitClient'])) ? trim($_POST['nitClient']) : '';
$showContact = $con->getContact($nitCliente);

if (mysqli_num_rows($showContact) == 0) {
    echo '<input class="form-control input-lg m-bot15"   size="25"  type="text"   name="nombreNewCliente"  />';
} else {
    while ($fila = mysqli_fetch_array($showContact)) {
        echo '<div onclick="myFunction2(' . (int) $fila["idcliente"] . ')">' . htmlspecialchars(nombreCliente($fila)) . '</div>';
    }
}

?>