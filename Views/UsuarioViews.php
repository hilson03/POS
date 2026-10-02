<!DOCTYPE html>
<html lang="en">
<?php
include('Head.php');
?>

<body>

    <section id="container" class="">
    <header class="header dark-bg">
        <div class="toggle-nav">
            <div class="icon-reorder tooltips" data-original-title="Toggle Navigation" data-placement="bottom"><i
                        class="icon_menu"></i></div>
        </div>
        <?PHP include("Logo.php") ?>
        <div class="nav search-row" id="top_menu">
            <!--  search form start -->
            <ul class="nav top-menu">
                <li>
                    <form class="navbar-form">
                        <!--                              <input class="form-control" placeholder="Search" type="text">-->
                    </form>
                </li>
            </ul>
            <!--  search form end -->
        </div>
        <?PHP include("DropDown.php"); ?>
    </header>
    <?PHP include("Menu.php") ?>

</section>


<section id="main-content">
    <section class="wrapper">

        <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header"><i class="fa fa-laptop"></i> PRINCIPAL</h3>
                    <div class="<?PHP echo $alerta; ?>" role="alert">
                    <strong><?PHP echo $mensaje; ?></strong>
                    </div>

                    <ol class="breadcrumb">
                        <?PHP include ("MenuOpcionesConfiguracion.php");?>
                    </ol>
                </div>
            </div>
        
        <div class='row'>
            <div div class="col-lg-12">
                <div class='row'>
                    <section class="panel">
                        <header class="panel-heading"> Lista de usuarios del sistema</header>
                        <header class="panel-heading">
                            <div class="panel-body">
                                <div align="right">
                                        <button href="#addUser" title="" data-placement="left" data-toggle="modal"
                                                class="btn btn-primary tooltips" type="button"
                                                data-original-title="Nuevo Usuario">
                                            <span class="fa fa-plus"> </span>
                                            AGREGAR NUEVO USUARIO
                                        </button>
                                    </div>

                                    <div id="addUser" class="modal fade" tabindex="-1" role="dialog"
                                    aria-labelledby="myModalLabel" aria-hidden="true">

                                     <form class="form-validate form-horizontal" name="form2" action="Registros.php"
                                          method="POST" enctype="multipart/form-data">

                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                                aria-hidden="true"> x
                                                        </button>
                                                        <h3 id=myModalLabel align="center"> Registrar Nuevo Usuario </h3>
                                                </div>

                                                <div class="modal-body">

                                                    <section class="panel" class="col-lg-6">
                                                        <div>
                                                            <strong>
                                                                Agregar Imagen del Usuario
                                                            </strong>
                                                        </div>
                                                        <?php
                                                           include("UploadViewImageCreate.php");
                                                        ?>
                                                    </section>


                                                    <label for="nombre" class="control-label col-lg-2">
                                                        Nombre :
                                                    </label>

                                                    <div class="col-lg-10">
                                                        <input class="form-control input-lg m-bot15" id="nombre"
                                                               name="nombre" minlength="5" type="text" required>
                                                    </div>
                                                    <br><br>


                                                    <label for="tipo" class="control-label col-lg-2">
                                                        Tipo :
                                                    </label>
                                                    <div class="col-lg-10">
                                                        <select class="form-control input-lg m-bot15" name="tipo">
                                                            <option value="ADMINISTRADOR"> ADMINISTRADOR</option>
                                                            <option value="VENTAS"> VENTAS</option>
                                                        </select>

                                                    </div>
                                                    <br><br>

                                                    <label class="control-label col-lg-2">
                                                        Sucursal :
                                                    </label>
                                                    <div class="col-lg-10">
                                                        <select class="form-control input-lg m-bot15" name="idSucursal" required>
                                                            <option value="" selected disabled>— Elige una sucursal —</option>
                                                            <?php foreach ($sucursales as $sucursal) { ?>
                                                                <option value="<?php echo $sucursal['idSucursal']; ?>"><?php echo htmlspecialchars($sucursal['nombre']); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <br><br>


                                                    <label for="login" class="control-label col-lg-2">
                                                        Login :
                                                    </label>
                                                    <div class="col-lg-10">
                                                        <input class="form-control input-lg m-bot15" id="login"
                                                               name="login" minlength="5" type="text" required>
                                                    </div>
                                                    <br><br>


                                                    <label for="password" class="control-label col-lg-2">
                                                        Password:
                                                    </label>
                                                    <div class="col-lg-10">
                                                        <input class="form-control input-lg m-bot15" id="password"
                                                               name="password" minlength="5" type="password" autocomplete="new-password" required>
                                                    </div>
                                                    <br><br>




                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-danger" data-dismiss="modal"
                                                            aria-hidden="true"><strong> Cerrar</strong>
                                                    </button>
                                                    <button name="nuevo_usuario" type="submit" class="btn btn-primary">
                                                        <strong> Registrar</strong>
                                                    </button>
                                                </div>

                                            </div>



                                        </div>
                                
                                    </form>

                                    </div>
                            </div>
                        </header>

                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                    <th><i class="icon_images"></i> IMAGEN</th>
                                        <th><i class="icon_contacts"></i> NOMBRE</th>
                                        <th><i class="icon_folder"></i> TIPO</th>
                                        <th><i class="icon_contacts_alt"></i> LOGIN</th>
                                        <th><i class="icon_building"></i> SUCURSAL</th>
                                        <th><i class="icon_cog"></i> ACCIONES</th>
                                    </tr>
                                    </thead>
                                    <?php 

                                        while ($datosUsuarios = mysqli_fetch_array($allUsuarios)) {

                                    ?>

                                    <tr>

                                        <td><img src="<?php echo $urlViews . $datosUsuarios['foto'] ?>" height="50" width="50"></td>

                                        <td> <?PHP echo $datosUsuarios['nombre']; ?></td>
                                                <td> <?PHP echo $datosUsuarios['tipo']; ?></td>
                                                <td> <?PHP echo $datosUsuarios['login']; ?></td>
                                                <td> <?PHP echo htmlspecialchars($datosUsuarios['nombreSucursal']); ?></td>
                                                <td>
                                                <a href="#a<?php echo $datosUsuarios[0]; ?>" role="button" class="btn btn-success" data-toggle="modal">
                                                    <i class="icon_check_alt2"></i> </a>
                                                <a href="Registros.php?idborrar=<?PHP echo $datosUsuarios[0]; ?>" data-confirmar="al usuario <?php echo htmlspecialchars($datosUsuarios['nombre'] . ' (' . $datosUsuarios['login'] . ')'); ?>"
                                                   role="button" class="btn btn-danger"> <i class="icon_close_alt2"></i>
                                                </a>
                                            </td>

                                    </tr>

                                    <div id="a<?php echo $datosUsuarios[0]; ?>" class="modal fade" tabindex="-1"
                                        role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

                                        <form class="form-validate form-horizontal" name="form2"
                                        action="Registros.php" method="post" enctype="multipart/form-data">

                                            <input type="hidden" name="idUsuario"
                                                       value="<?php echo $datosUsuarios['id_usu']; ?>">
                                            <input type="hidden" name="imagen"
                                                       value="<?php echo $datosUsuarios['foto']; ?>">

                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">

                                                        <button type="button" class="close" data-dismiss="modal"
                                                                    aria-hidden="true">×
                                                    </button>
                                                    <h3 id="myModalLabel" align="center">Cambiar Informacion del Usuario</h3>

                                                    </div>

                                                    <div class="modal-body">
                                                        <img src="<?PHP echo $urlViews;
                                                            echo $datosUsuarios['foto']; ?>" width="250" height="250">
                                                            <br><br>
                                                            <section class="panel" class="col-lg-6">
                                                                <div><strong>Cambiar Imagen de usuario</strong></div>
                                                                <?php 
                                                                include("UploadViewImageEdit.php");
                                                                ?>
                                                            </section>

                                                    <div class="form-group ">
                                                                <label for="proveedor"
                                                                       class="control-label col-lg-2">Nombre:</label>
                                                                <div class="col-lg-10">
                                                                    <input class="form-control input-lg m-bot15"
                                                                           type="text" name="nombre"
                                                                           value="<?php echo $datosUsuarios['nombre']; ?>">
                                                                    <input type="hidden" name="idUsuario"
                                                                           value="<?php echo $datosUsuarios['id_usu']; ?>">
                                                                </div>
                                                        </div>
                                                    
                                                    <div class="form-group ">
                                                                <label for="responsable"
                                                                       class="control-label col-lg-2">Tipo:</label>
                                                                <div class="col-lg-10">
                                                                    <select class="form-control input-lg m-bot15"
                                                                            name="tipo">
                                                                        <option value="<?php echo $datosUsuarios['tipo']; ?>"><?php echo $datosUsuarios['tipo']; ?></option>
                                                                        <option value="ADMINISTRADOR">
                                                                            ADMINISTRADOR
                                                                        </option>
                                                                        <option value="VENTAS">VENTAS</option>
                                                                    </select>
                                                                </div>
                                                        </div>

                                                    <div class="form-group ">
                                                                <label class="control-label col-lg-2">Sucursal:</label>
                                                                <div class="col-lg-10">
                                                                    <select class="form-control input-lg m-bot15" name="idSucursal" required>
                                                                        <?php foreach ($sucursales as $sucursal) { ?>
                                                                            <option value="<?php echo $sucursal['idSucursal']; ?>"<?php echo $sucursal['idSucursal'] == $datosUsuarios['idSucursal'] ? ' selected' : ''; ?>><?php echo htmlspecialchars($sucursal['nombre']); ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                        </div>

                                                    <div class="form-group ">
                                                                <label for="direccion"
                                                                       class="control-label col-lg-2">Login:</label>
                                                                <div class="col-lg-10">
                                                                    <input class="form-control input-lg m-bot15"
                                                                           type="text" name="login"
                                                                           value="<?php echo $datosUsuarios['login']; ?>">
                                                                </div>
                                                        </div>

                                                    <div class="form-group ">
                                                                <label for="telefono"
                                                                       class="control-label col-lg-2">Password:</label>
                                                                <div class="col-lg-10">
                                                                    <input class="form-control input-lg m-bot15"
                                                                           type="password" name="password" minlength="5"
                                                                           autocomplete="new-password"
                                                                           placeholder="Déjalo vacío para no cambiarla">
                                                                    <!-- la contrasena se guarda cifrada y ya no se puede mostrar -->
                                                                    <small class="help-block">Escribe una contraseña nueva solo si quieres cambiarla.</small>
                                                                </div>
                                                        </div>

                                                    <div class="modal-footer">
                                                                <button class="btn btn-default" data-dismiss="modal"
                                                                        aria-hidden="true"><strong>Cerrar</strong>
                                                                </button>
                                                                <button name="update_usuario" type="submit"
                                                                        class="btn btn-primary"><strong>Actualizar  Datos</strong></button>
                                                    </div>


                                                    </div>

                                                </div>
                                                        
                                            </div>
                                    
                                        </form>


                                    </div>
                                    <?PHP } ?>
                                </table>

                            </div>
                        </div>

                    </section>

                </div>

            </div>

        </div>

    </section>

    <?PHP include ("LibraryJs.php"); ?>
</section>

</body>