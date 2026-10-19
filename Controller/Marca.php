<?php
/*
 * Logos e imagenes del negocio que el administrador puede cambiar (Configuracion > Apariencia).
 * Cada imagen tiene una original en Views/img; la personalizada se guarda en Views/img/personalizado
 * y su ruta en la tabla apariencia. Si no hay personalizada (o el archivo ya no existe) se usa la original,
 * asi el sistema nunca se queda sin logo.
 */

if (!defined('CARPETA_MARCA')) define('CARPETA_MARCA', 'img/personalizado/');

/*
 * Imagenes que se pueden cambiar, en el orden en que salen en la pantalla.
 * medida: la recomendada; minimo: ancho y alto por debajo de los cuales se ve borrosa;
 * forma: 'cuadrada' (ancho = alto) u 'horizontal' (unas 3 veces mas ancha que alta).
 * Las imagenes nunca se estiran (se ajustan sin deformarse), pero si la forma no es la indicada quedan chicas o con espacios.
 */
function imagenesMarca()
{
    return array(
        'logo_login'  => array('titulo' => 'Logo del inicio de sesión', 'original' => 'img/logo-login.png',
                               'ayuda'  => 'Va dentro del círculo del login (se muestra a 120 × 120 px).',
                               'medida' => '500 × 500 px', 'minimo' => array(240, 240), 'forma' => 'cuadrada',
                               'fondo'  => 'Transparente (PNG) o blanco. El logo en color oscuro.'),
        'fondo_login' => array('titulo' => 'Imagen de fondo del login', 'original' => 'img/logo-fondo.png',
                               'ayuda'  => 'La imagen grande y tenue de la derecha. Se genera sola al cambiar el logo del login; súbela aquí solo si quieres otra.',
                               'medida' => '1000 × 1000 px', 'minimo' => array(600, 600), 'forma' => 'cuadrada',
                               'fondo'  => 'Transparente (PNG), con el dibujo en gris muy claro. Una foto o un fondo de color se verá como un bloque.'),
        'logo_barra'  => array('titulo' => 'Logo de la barra de arriba', 'original' => 'img/logoI.png',
                               'ayuda'  => 'Sale arriba a la izquierda en todas las pantallas (se muestra a 34 px de alto y 200 px de ancho como máximo).',
                               'medida' => '400 × 120 px', 'minimo' => array(150, 45), 'forma' => 'horizontal',
                               'fondo'  => 'Transparente (PNG), con el logo en blanco o claro porque la barra es azul.'),
        'favicon'     => array('titulo' => 'Icono de la pestaña', 'original' => 'img/logo-login.png',
                               'ayuda'  => 'El iconito de la pestaña del navegador. Se ve muy chico: mejor solo el símbolo, sin letras pequeñas.',
                               'medida' => '128 × 128 px', 'minimo' => array(32, 32), 'forma' => 'cuadrada',
                               'fondo'  => 'Transparente (PNG o ICO).'),
    );
}

/*
 * Datos del negocio que escribe el administrador en Configuracion > Datos del negocio
 * (nombre, propietario, RTN, direccion, ciudad, telefono, correo, mensaje al pie). Se leen una vez por pagina.
 */
function datosNegocio()
{
    static $datos = null;
    if ($datos === null) {
        $datos = array('razon' => '', 'propietario' => '', 'nro' => '', 'direccion' => '', 'ciudad' => '', 'telefono' => '', 'correo' => '', 'mensajePie' => '');
        try {
            if (!class_exists('conexion')) {
                require_once(__DIR__ . '/../Model/Conexion.php');
            }
            $con = new conexion();
            $fila = $con->getDatosNegocio();
            if ($fila !== null) {
                foreach ($datos as $campo => $valor) {
                    $datos[$campo] = isset($fila[$campo]) ? trim((string) $fila[$campo]) : '';
                }
            }
        } catch (Throwable $e) {
            // sin base de datos o sin la migracion 011: quedan vacios
        }
    }
    return $datos;
}

// titulo de la pestana del navegador: "<nombre del negocio> - Punto de venta" (sin nombre, "Punto de venta")
function tituloPagina()
{
    $nombre = datosNegocio()['razon'];
    return ($nombre != '' ? $nombre . ' - ' : '') . 'Punto de venta';
}

/*
 * Colores para la barra superior (Configuracion > Apariencia). Todos son oscuros a proposito: en la barra van
 * el logo y los textos en blanco, y con colores claros dejarian de leerse. El primero es el original.
 */
function coloresBarra()
{
    return array(
        '#007FFF' => 'Azul (original)',
        '#060147' => 'Azul marino',
        '#4C2882' => 'Morado',
        '#008000' => 'Verde',
        '#9B1B30' => 'Vino',
        '#2F3640' => 'Gris carbón',
    );
}

// color elegido para la barra superior (si no hay uno valido, el azul original)
function colorBarra()
{
    $valores = valoresMarca();
    $colores = coloresBarra();
    $elegido = isset($valores['color_barra']) ? strtoupper($valores['color_barra']) : '';
    return isset($colores[$elegido]) ? $elegido : '#007FFF';
}

// ancho del rollo de la impresora termica en mm (80 o 58), se elige en Configuracion > Apariencia
function anchoPapel()
{
    $valores = valoresMarca();
    return (isset($valores['ancho_papel']) && $valores['ancho_papel'] == '58') ? 58 : 80;
}

// revisa la imagen subida contra lo recomendado; devuelve un consejo o '' si esta bien (no impide guardarla)
function consejoMedidasMarca($clave, $ruta)
{
    $imagenes = imagenesMarca();
    $datos = @getimagesize(dirname(__DIR__) . '/Views/' . $ruta);
    if ($datos === false || !isset($imagenes[$clave])) {
        return '';
    }
    $imagen = $imagenes[$clave];
    list($ancho, $alto) = $datos;
    $proporcion = $ancho / max(1, $alto);
    $medidas = $ancho . ' × ' . $alto . ' px';

    if ($ancho < $imagen['minimo'][0] || $alto < $imagen['minimo'][1]) {
        return 'La imagen mide ' . $medidas . ' y puede verse borrosa; lo recomendado es ' . $imagen['medida'] . '.';
    }
    if ($imagen['forma'] == 'cuadrada' && ($proporcion < 0.8 || $proporcion > 1.25)) {
        return 'La imagen mide ' . $medidas . ' y no es cuadrada: se verá más chica y con espacios a los lados. Lo recomendado es ' . $imagen['medida'] . '.';
    }
    if ($imagen['forma'] == 'horizontal' && $proporcion < 2) {
        return 'La imagen mide ' . $medidas . ' y no es lo bastante horizontal: en la barra se verá muy chica. Lo recomendado es ' . $imagen['medida'] . '.';
    }
    return '';
}

// rutas personalizadas guardadas (se leen una sola vez por pagina)
function valoresMarca()
{
    static $valores = null;
    if ($valores === null) {
        $valores = array();
        try {
            if (!class_exists('conexion')) {
                require_once(__DIR__ . '/../Model/Conexion.php');
            }
            $con = new conexion();
            $valores = $con->getApariencia();
        } catch (Throwable $e) {
            // sin base de datos o sin la tabla (migracion 008 sin aplicar): se usan las imagenes originales
        }
    }
    return $valores;
}

function esImagenPersonalizada($clave)
{
    $valores = valoresMarca();
    return !empty($valores[$clave]) && strpos($valores[$clave], CARPETA_MARCA) === 0
        && is_file(dirname(__DIR__) . '/Views/' . $valores[$clave]);
}

// ruta de la imagen a mostrar, relativa a Views/, con ?v= para que el navegador no muestre una vieja guardada
function imagenMarca($clave)
{
    $imagenes = imagenesMarca();
    $ruta = esImagenPersonalizada($clave) ? valoresMarca()[$clave] : $imagenes[$clave]['original'];
    return $ruta . '?v=' . @filemtime(dirname(__DIR__) . '/Views/' . $ruta);
}

// el fondo que se genera solo a partir del logo se reconoce por el nombre del archivo
function esFondoAutomatico()
{
    return esImagenPersonalizada('fondo_login') && strpos(basename(valoresMarca()['fondo_login']), 'fondo_login_auto_') === 0;
}

/*
 * Guarda la imagen enviada en el input $campo dentro de Views/img/personalizado.
 * Devuelve la ruta relativa a Views/ o '' si hubo un error (el motivo queda en $error).
 */
function subirImagenMarca($campo, $clave, &$error)
{
    $error = '';
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] == UPLOAD_ERR_NO_FILE || $_FILES[$campo]['name'] == '') {
        $error = 'No elegiste ninguna imagen.';
        return '';
    }
    $archivo = $_FILES[$campo];
    if ($archivo['error'] == UPLOAD_ERR_INI_SIZE || $archivo['error'] == UPLOAD_ERR_FORM_SIZE || $archivo['size'] > 2000000) {
        $error = 'La imagen es demasiado pesada. Se permiten imágenes de 2 MB máximo.';
        return '';
    }
    if ($archivo['error'] != UPLOAD_ERR_OK) {
        $error = 'Ocurrió un error al subir la imagen (código ' . $archivo['error'] . ').';
        return '';
    }

    $permitidas = array('png', 'jpg', 'jpeg', 'webp');
    if ($clave == 'favicon') {
        $permitidas[] = 'ico';
    }
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    // getimagesize confirma que realmente es una imagen y no otro archivo renombrado
    if (!in_array($extension, $permitidas) || @getimagesize($archivo['tmp_name']) === false) {
        $error = 'El archivo no es una imagen válida. Se permiten .' . implode(', .', $permitidas) . '.';
        return '';
    }

    $carpeta = dirname(__DIR__) . '/Views/' . CARPETA_MARCA;
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }
    // el nombre lo arma el sistema (no se usa el del archivo subido)
    $nombre = $clave . '_' . time() . '.' . $extension;
    if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombre)) {
        $error = 'No se pudo guardar la imagen en el servidor. Revisa que la carpeta Views/img/personalizado tenga permiso de escritura.';
        return '';
    }
    return CARPETA_MARCA . $nombre;
}

/*
 * Arma la imagen tenue del fondo del login a partir del logo: lo que es tinta (oscuro u opaco) queda gris muy claro,
 * lo blanco o transparente desaparece. Devuelve la ruta relativa a Views/ o '' si no se pudo.
 */
function generarFondoDesdeLogo($rutaLogo)
{
    if (!function_exists('imagecreatefromstring')) {
        return '';
    }
    $origen = @imagecreatefromstring(@file_get_contents(dirname(__DIR__) . '/Views/' . $rutaLogo));
    if ($origen === false) {
        return '';
    }
    $ancho = imagesx($origen);
    $alto = imagesy($origen);
    $escala = 800 / max($ancho, $alto);
    $w = max(1, (int) round($ancho * $escala));
    $h = max(1, (int) round($alto * $escala));

    $chico = imagecreatetruecolor($w, $h);
    imagealphablending($chico, false);
    imagesavealpha($chico, true);
    imagefill($chico, 0, 0, imagecolorallocatealpha($chico, 255, 255, 255, 127));
    imagecopyresampled($chico, $origen, 0, 0, 0, 0, $w, $h, $ancho, $alto);

    $fondo = imagecreatetruecolor($w, $h);
    imagealphablending($fondo, false);
    imagesavealpha($fondo, true);
    for ($y = 0; $y < $h; $y++) {
        for ($x = 0; $x < $w; $x++) {
            $c = imagecolorsforindex($chico, imagecolorat($chico, $x, $y));
            $tinta = (1 - (($c['red'] + $c['green'] + $c['blue']) / 3) / 255) * (1 - $c['alpha'] / 127);
            $alfa = (int) round(127 - $tinta * 0.13 * 127);   // GD: 0 opaco, 127 transparente
            imagesetpixel($fondo, $x, $y, imagecolorallocatealpha($fondo, 120, 120, 120, $alfa));
        }
    }

    $ruta = CARPETA_MARCA . 'fondo_login_auto_' . time() . '.png';
    $ok = imagepng($fondo, dirname(__DIR__) . '/Views/' . $ruta, 9);
    return $ok ? $ruta : '';
}

// borra un archivo personalizado que ya no se usa (nunca toca las imagenes originales)
function borrarImagenMarca($ruta)
{
    if ($ruta !== '' && strpos($ruta, CARPETA_MARCA) === 0 && basename($ruta) === substr($ruta, strlen(CARPETA_MARCA))) {
        @unlink(dirname(__DIR__) . '/Views/' . $ruta);
    }
}
?>
