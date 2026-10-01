<?php
/*
 * Manejo de la sesion del usuario.
 * Antes el usuario y la contrasena viajaban en la URL de cada pantalla; ahora el servidor recuerda
 * quien inicio sesion y cada controlador lo consulta con requerirSesion().
 */
require_once(__DIR__ . '/../Model/Conexion.php');

function iniciarSesionSegura()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $esHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params(array(
        'lifetime' => 0,          // la sesion termina al cerrar el navegador
        'path' => '/',
        'secure' => $esHttps,     // en el hosting con https la cookie solo viaja cifrada
        'httponly' => true,       // el javascript de la pagina no puede leer la cookie
        'samesite' => 'Lax',
    ));
    session_name('VENTASPOS');
    session_start();
}

// guarda al usuario en la sesion despues de validar su contrasena; empieza trabajando en su propia sucursal
function guardarSesion($idUsuario, $idSucursal)
{
    iniciarSesionSegura();
    session_regenerate_id(true); // evita que alguien reutilice un identificador de sesion anterior
    $_SESSION['id_usu'] = (int) $idUsuario;
    $_SESSION['idSucursal'] = (int) $idSucursal;
}

// solo los administradores pueden trabajar en otra sucursal distinta a la suya
function cambiarSucursalActiva($idSucursal)
{
    iniciarSesionSegura();
    $_SESSION['idSucursal'] = (int) $idSucursal;
}

function cerrarSesion()
{
    iniciarSesionSegura();
    $_SESSION = array();
    $parametros = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $parametros['path'], '', $parametros['secure'], $parametros['httponly']);
    session_destroy();
}

/*
 * Devuelve los datos del usuario conectado. Si no hay sesion, manda al login.
 * Con $tipoRequerido = 'ADMINISTRADOR' tambien bloquea a los usuarios que no lo son.
 * $esAjax = true para los controladores que responden un pedazo de pagina (no pueden redirigir).
 */
function requerirSesion($tipoRequerido = '', $esAjax = false)
{
    iniciarSesionSegura();

    $usuarioSesion = null;
    if (!empty($_SESSION['id_usu'])) {
        $con = new conexion();
        $datos = $con->getOnlyUserData((int) $_SESSION['id_usu']);
        if (!empty($datos)) {
            $usuarioSesion = $datos[0];
        }
    }

    if ($usuarioSesion === null) {
        if ($esAjax) {
            http_response_code(401);
            echo "<div class='alert alert-danger'>Tu sesion termino. <a href='../index.php'>Inicia sesion de nuevo</a>.</div>";
        } else {
            header("Location: ../index.php?error=sesion");
        }
        exit;
    }

    /*
     * Sucursal en la que se esta trabajando: un vendedor siempre en la suya; un administrador en la que eligio
     * (si la elegida ya no existe o esta inactiva, vuelve a la suya).
     */
    $idSucursalActiva = (int) $usuarioSesion['idSucursal'];
    if ($usuarioSesion['tipo'] == 'ADMINISTRADOR' && !empty($_SESSION['idSucursal'])) {
        $idSucursalActiva = (int) $_SESSION['idSucursal'];
    }
    $sucursal = $con->getSucursal($idSucursalActiva);
    if ($sucursal === null || $sucursal['estado'] != 'Activo') {
        $idSucursalActiva = (int) $usuarioSesion['idSucursal'];
        $sucursal = $con->getSucursal($idSucursalActiva);
    }
    $_SESSION['idSucursal'] = $idSucursalActiva;
    $usuarioSesion['idSucursalActiva'] = $idSucursalActiva;
    $usuarioSesion['nombreSucursal'] = $sucursal === null ? '(sin sucursal)' : $sucursal['nombre'];

    if ($tipoRequerido != '' && $usuarioSesion['tipo'] != $tipoRequerido) {
        http_response_code(403);
        echo "<div style='font-family:Arial;padding:30px'><h3>No tienes permiso para entrar a esta pantalla.</h3>"
            . "<a href='Ventas.php'>Volver a Ventas</a></div>";
        exit;
    }

    return $usuarioSesion;
}
?>
