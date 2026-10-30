<?php
/*
 * Validaciones del lado del servidor para los formularios.
 * Lo que valida el navegador (required, maxlength, type="email") se puede quitar desde las herramientas
 * del navegador o enviando el formulario a mano, asi que aqui se revisa todo de nuevo antes de guardar.
 */

// texto enviado en el formulario: sin espacios de mas ni caracteres de control, cortado al largo de la columna.
// Si el campo no viene (o viene como lista en vez de texto) devuelve vacio.
function textoPost($campo, $maximo)
{
    if (!isset($_POST[$campo]) || is_array($_POST[$campo])) {
        return '';
    }
    $texto = preg_replace('/[\x00-\x1F\x7F]/u', ' ', (string) $_POST[$campo]);
    $texto = trim(preg_replace('/\s+/u', ' ', (string) $texto));
    return mb_substr($texto, 0, $maximo, 'UTF-8');
}

// numero entero enviado en el formulario (ids); 0 si no viene o no es un numero
function enteroPost($campo)
{
    return (isset($_POST[$campo]) && !is_array($_POST[$campo]) && ctype_digit(trim((string) $_POST[$campo]))) ? (int) $_POST[$campo] : 0;
}

// numero con decimales entre un minimo y un maximo (precios, cantidades)
function numeroValido($valor, $minimo, $maximo = 99999999)
{
    return !is_array($valor) && is_numeric($valor) && $valor >= $minimo && $valor <= $maximo;
}

// el correo es opcional, pero si se escribe tiene que tener forma de correo
function correoValido($correo)
{
    return $correo === '' || filter_var($correo, FILTER_VALIDATE_EMAIL) !== false;
}

// fecha con formato AAAA-MM-DD que existe en el calendario
function fechaValida($fecha)
{
    return is_string($fecha) && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $fecha, $partes) && checkdate((int) $partes[2], (int) $partes[3], (int) $partes[1]);
}

// login: de 3 a 50 letras, numeros, punto, guion o guion bajo, sin espacios
function loginValido($login)
{
    return (bool) preg_match('/^[A-Za-z0-9._-]{3,50}$/', $login);
}

// contrasena: de 6 a 72 caracteres (72 es lo maximo que usa password_hash)
function passwordValido($password)
{
    return is_string($password) && strlen($password) >= 6 && strlen($password) <= 72;
}

// IMEI de un telefono: 15 digitos y el ultimo es el digito de control (formula de Luhn, como las tarjetas);
// asi se detecta casi cualquier error al escribirlo
function imeiValido($imei)
{
    if (!is_string($imei) || !preg_match('/^[0-9]{15}$/', $imei)) {
        return false;
    }
    $suma = 0;
    for ($i = 0; $i < 15; $i++) {
        $digito = (int) $imei[14 - $i];
        if ($i % 2 == 1) {
            $digito *= 2;
            if ($digito > 9) {
                $digito -= 9;
            }
        }
        $suma += $digito;
    }
    return $suma % 10 == 0;
}
