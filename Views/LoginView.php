<?php
// logos personalizados (Configuracion > Apariencia)
require_once(__DIR__ . '/../Controller/Marca.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Creative - Bootstrap 3 Responsive Admin Template">
    <meta name="author" content="GeeksLabs">
    <meta name="keyword" content="Creative, Dashboard, Admin, Template, Theme, Bootstrap, Responsive, Retina, Minimal">
    <link rel="shortcut icon" href="Views/<?php echo imagenMarca('favicon'); ?>">

    <title>iCONT - Sistema Profesional Para Empresas</title>
    <!-- Bootstrap CSS -->
    <link href="Views/css/bootstrap.min.css" rel="stylesheet">
    <!-- bootstrap theme -->
    <link href="Views/css/bootstrap-theme.css" rel="stylesheet">
    <!--external css-->
    <!-- font icon -->
    <link href="Views/css/elegant-icons-style.css" rel="stylesheet" />
    <link href="Views/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom styles -->
    <link href="Views/css/style.css" rel="stylesheet">
    <link href="Views/css/style-responsive.css" rel="stylesheet" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 -->
    <!--[if lt IE 9]>
    <script src="Views/js/html5shiv.js"></script>
    <script src="Views/js/respond.min.js"></script>
    
    <![endif]-->
    <link href="Views/css/legibilidad.css?v=<?php echo filemtime(__DIR__ . '/css/legibilidad.css'); ?>" rel="stylesheet">
    <style>
        /* fondo claro sin la imagen de la plantilla; la "RC" grande y tenue va a la derecha */
        body.login-img3-body {
            background: #f7f7f8 url('Views/<?php echo imagenMarca('fondo_login'); ?>') no-repeat right -120px center !important;
            background-size: auto 115vh !important;
            min-height: 100vh;
            margin: 0;
        }
        .login-marca {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 30px 16px 30px 9%;
        }
        .login-columna {
            width: 100%;
            max-width: 380px;
            text-align: center;
        }
        .login-circulo {
            width: 170px;
            height: 170px;
            margin: 0 auto 18px;
            border: 3px solid #1a1a1a;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        }
        .login-circulo img { width: 120px; height: 120px; object-fit: contain; }
        .login-nombre {
            font-family: Georgia, 'Times New Roman', serif;
            font-size: 40px;
            letter-spacing: 12px;
            margin: 0 0 26px 12px; /* compensa el espacio de la ultima letra */
            color: #1a1a1a;
        }
        /* el formulario de la plantilla quedaba centrado con fondo gris; aqui va bajo la marca */
        .login-img3-body .login-marca .login-form {
            max-width: none;
            margin: 0;
            background: #fff;
            border: 1px solid #d5d8dd;
            border-radius: 6px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.07);
        }
        /* la plantilla quitaba el borde de los campos; se dibuja la casilla completa (icono + campo) */
        .login-marca .login-form .input-group-addon,
        .login-marca .login-form input[type="text"],
        .login-marca .login-form input[type="password"] {
            border: 1px solid #9aa0a8;
            background: #fff;
        }
        .login-marca .login-form .input-group-addon {
            border-right: none;
            border-radius: 4px 0 0 4px;
        }
        .login-marca .login-form input[type="text"],
        .login-marca .login-form input[type="password"] {
            border-left: none;
            border-radius: 0 4px 4px 0;
        }
        /* al escribir se pinta de azul toda la casilla, no solo el campo */
        .login-marca .login-form .input-group:focus-within .input-group-addon,
        .login-marca .login-form .input-group:focus-within .form-control {
            border-color: #007aff !important;
        }
        .login-marca .login-form .input-group:focus-within .form-control { box-shadow: none; }
        .login-marca .login-form .btn { border-radius: 4px; }
        /* celular: sin "RC" de fondo y todo centrado */
        @media (max-width: 767px) {
            body.login-img3-body { background-image: none !important; }
            .login-marca { justify-content: center; padding: 24px 16px; }
            .login-nombre { font-size: 32px; letter-spacing: 8px; margin-left: 8px; }
        }
    </style>
</head>

<body class="login-img3-body">

<div class="login-marca">
  <div class="login-columna">
    <div class="login-circulo">
        <img src="Views/<?php echo imagenMarca('logo_login'); ?>" alt="Logo">
    </div>
        <h1 class="login-nombre">REYCELL</h1>

    <form class="login-form" action="Controller/AccessUsers.php" method="post">
        <div class="login-wrap">
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger" style="text-align: center;">
                    <?php
                    if ($_GET['error'] == 'login') {
                        echo 'Usuario o contraseña incorrectos, intenta de nuevo.';
                    } else {
                        echo 'Tu sesión terminó, vuelve a iniciar sesión.';
                    }
                    ?>
                </div>
            <?php } elseif (isset($_GET['salir'])) { ?>
                <div class="alert alert-info" style="text-align: center;">Cerraste sesión correctamente.</div>
            <?php } ?>
            <div class="input-group">
                <span class="input-group-addon"><i class="icon_profile"></i></span>
                <input type="text" name="usuario"   class="form-control" placeholder="Usuario" autofocus required>
            </div>
            <div class="input-group">
                <span class="input-group-addon"><i class="icon_key_alt"></i></span>
                <input type="password"  name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <button class="btn btn-primary btn-lg btn-block" type="submit">Ingresar</button>
        </div>
    </form>
  </div>
</div>
</body>
</html>