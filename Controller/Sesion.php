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

    // todos los formularios POST de la pagina llevan el codigo de proteccion (ver insertarCsrfEnFormularios)
    ob_start('insertarCsrfEnFormularios');
}

/*
 * Proteccion contra "enlaces trampa" (CSRF): cada sesion tiene un codigo secreto que va oculto en los formularios.
 * Una pagina o un enlace de afuera no conoce ese codigo, asi que no puede hacer que el navegador del usuario
 * guarde, borre o desactive algo sin que el usuario lo haya pedido desde el sistema.
 */
function tokenCsrf()
{
    iniciarSesionSegura();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

// agrega el campo oculto con el codigo a cada <form method="post"> del html que se envia al navegador
function insertarCsrfEnFormularios($html)
{
    foreach (headers_list() as $cabecera) {
        // los PDF y las respuestas JSON se envian tal cual
        if (stripos($cabecera, 'Content-Type:') === 0 && stripos($cabecera, 'text/html') === false) {
            return $html;
        }
    }
    if (stripos($html, '<form') === false || empty($_SESSION['csrf']) && session_status() !== PHP_SESSION_ACTIVE) {
        return $html;
    }
    $campo = '<input type="hidden" name="csrf" value="' . htmlspecialchars(tokenCsrf()) . '">';
    return preg_replace('/<form\b[^>]*\bmethod\s*=\s*["\']?post\b[^>]*>/i', '$0' . $campo, $html);
}

/*
 * Para los controladores que guardan, borran o desactivan: la accion solo se hace si llega por POST con el codigo
 * correcto. Si no, no se hace nada y se vuelve a $volverA con un aviso.
 */
function exigirCsrf($volverA)
{
    $enviado = isset($_POST['csrf']) && is_string($_POST['csrf']) ? $_POST['csrf'] : '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $enviado)) {
        return;
    }
    // si el formulario traia un archivo mas pesado que lo que acepta el servidor, PHP descarta todo el formulario
    $maximo = ini_get('post_max_size');
    $mensaje = "No se realizó la acción: la página estaba vencida o la orden no salió del sistema. Vuelve a intentarlo desde la pantalla.";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && !empty($_SERVER['CONTENT_LENGTH'])) {
        $mensaje = "No se guardó: lo que enviaste es demasiado pesado (el servidor acepta hasta " . $maximo . "). Usa una foto más liviana.";
    }
    $con = new conexion();
    $con->updateMensajeAlert($mensaje, "alert alert-danger");
    header("Location: " . $volverA . "?estado='Activo'");
    exit;
}

// guarda al usuario en la sesion despues de validar su contrasena; empieza trabajando en su propia sucursal
function guardarSesion($idUsuario, $idSucursal)
{
    iniciarSesionSegura();
    session_regenerate_id(true); // evita que alguien reutilice un identificador de sesion anterior
    unset($_SESSION['csrf']);    // codigo de proteccion nuevo para esta sesion
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

    // si lo desactivaron mientras tenia la sesion abierta, se le cierra en su siguiente clic
    $motivoSalida = 'sesion';
    if ($usuarioSesion !== null && isset($usuarioSesion['estado']) && $usuarioSesion['estado'] == 'Inactivo') {
        $usuarioSesion = null;
        $motivoSalida = 'inactivo';
        $_SESSION = array();
    }

    if ($usuarioSesion === null) {
        if ($esAjax) {
            http_response_code(401);
            echo "<div class='alert alert-danger'>Tu sesion termino. <a href='../index.php'>Inicia sesion de nuevo</a>.</div>";
        } else {
            header("Location: ../index.php?error=" . $motivoSalida);
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
