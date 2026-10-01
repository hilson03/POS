<?php
/*
 * Sube una foto enviada desde un formulario a la carpeta Views/fotoproducto.
 *
 * $campo         nombre del input file (ej. 'userfile' o 'userfileEdit')
 * $imagenDefecto ruta que se devuelve si no se envio foto o si hubo un error
 * $error         recibe el mensaje de error (vacio si todo salio bien)
 *
 * Devuelve la ruta relativa a Views/ que se guarda en la base de datos (ej. 'fotoproducto/foto_123.jpg').
 */
function subirFoto($campo, $imagenDefecto, &$error)
{
    $error = '';

    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] == UPLOAD_ERR_NO_FILE || $_FILES[$campo]['name'] == '') {
        return $imagenDefecto;
    }

    $archivo = $_FILES[$campo];

    if ($archivo['error'] == UPLOAD_ERR_INI_SIZE || $archivo['error'] == UPLOAD_ERR_FORM_SIZE) {
        $error = "La foto es demasiado pesada. El servidor permite maximo " . ini_get('upload_max_filesize') . ".";
        return $imagenDefecto;
    }

    if ($archivo['error'] != UPLOAD_ERR_OK) {
        $error = "Ocurrio un error al subir la foto (codigo " . $archivo['error'] . ").";
        return $imagenDefecto;
    }

    $extensionesPermitidas = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

    // getimagesize confirma que realmente es una imagen y no otro archivo renombrado
    if (!in_array($extension, $extensionesPermitidas) || @getimagesize($archivo['tmp_name']) === false) {
        $error = "El archivo no es una imagen valida. Se permiten fotos .jpg, .png, .gif o .webp.";
        return $imagenDefecto;
    }

    if ($archivo['size'] > 5000000) {
        $error = "La foto es demasiado pesada. Se permiten fotos de 5 Mb maximo.";
        return $imagenDefecto;
    }

    $carpetaDestino = rtrim(ADDRESS, '/\\') . '/';
    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0777, true);
    }

    // nombre unico para que una foto nueva no reemplace a otra con el mismo nombre
    $nombreBase = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($archivo['name'], PATHINFO_FILENAME));
    $nombreArchivo = $nombreBase . '_' . time() . '.' . $extension;

    if (!move_uploaded_file($archivo['tmp_name'], $carpetaDestino . $nombreArchivo)) {
        $error = "No se pudo guardar la foto en el servidor.";
        return $imagenDefecto;
    }

    return 'fotoproducto/' . $nombreArchivo;
}
?>
