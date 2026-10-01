<?php
require_once('Sesion.php');

cerrarSesion();
header("Location: ../index.php?salir=1");
exit;
?>
