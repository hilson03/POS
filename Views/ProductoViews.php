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

<!--sidebar end-->
<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        <!--overview start-->
        <div class="row">
            <div class="col-lg-12">
                <h3 class="page-header"><i class="fa fa-laptop"></i> PRINCIPAL</h3>
                <div class="<?PHP echo $alerta; ?>" role="alert">
                    <strong><?PHP echo $mensaje; ?></strong>
                </div>

                <ol class="breadcrumb">
                    <li>
                        <i class="fa fa-home"></i><a href="AccessUsers.php">Inicio</a>
                    </li>
                    <li>
                        <i class="fa fa-inbox"></i><a href="Producto.php">Producto</a>
                    </li>
                    <li>
                        <i class="fa fa-plus"></i><a href="TipoProducto.php">Registrar Tipo Producto</a>
                    </li>
                </ol>
            </div>
        </div>

        <header class="panel-heading"> Lista de Productos del sistema</header>
        <header class="panel-heading">
            <div class="panel-body">
                <div align="right">

                    <a href="ReporteProductosPdf.php?productos=productos" target="_blank"
                       class="btn btn-danger tooltips"><i
                                class="fa fa-rotate-right"></i> EXPORTAR PDF </a>

                    <button href="#add" title="" data-placement="top" data-toggle="modal"
                            class="btn btn-primary tooltips" type="button" data-original-title="Nuevo Producto">
                        <span class="icon_bag_alt"></span>AGREGAR NUEVO PRODUCTO
                    </button>
                </div>
                <div id="add" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                     aria-hidden="true">
                    <form action="RegistroProducto.php" method="post" enctype="multipart/form-data">
                        <div class="modal-dialog" id="mdialTamanio">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x
                                    </button>
                                    <h3 id="myModalLabel" align="center">Registrar Informacion del Producto</h3>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <section class="panel">
                                                <div><strong>Agregar Imagen</strong></div>
                                                <br>
                                                <?php
                                                include("UploadViewImageCreate.php");
                                                ?>
                                            </section>
                                        </div>
                                        <div class="col-lg-8">
                                            <section class="panel">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Tipo Producto:</label>
                                                    <div class="col-sm-4">
                                                        <select class="form-control input-lg m-bot15"
                                                                name="tipoproducto">
                                                            <?php
                                                            while ($tipoProducto = mysqli_fetch_array($tipoProductos)) {
                                                                echo '<option value="' . $tipoProducto['tipoproducto'] . '">' . $tipoProducto['tipoproducto'] . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <label class="col-sm-2 control-label">Codigo:</label>
                                                    <div class="col-sm-4">
                                                        <input class="form-control input-lg m-bot15" id="codigo"
                                                               name="codigo" type="text" required/>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Descripcion:</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control input-lg m-bot15"
                                                               id="descripcion" name="descripcion" type="text"
                                                               required/>
                                                    </div>

                                                </div>
                                                <div class="form-group">
                                                    <label for="cantidad" class="control-label col-lg-2">Stock en <?php echo htmlspecialchars($usuarioSesion['nombreSucursal']); ?> :</label>
                                                    <div class="col-lg-4">
                                                        <input class="form-control input-lg m-bot15"
                                                               id="cantidad" name="cantidad"
                                                               placeholder="0" type="number" step="1" min="0" required/>
                                                    </div>
                                                    <label for="pVenta" class="control-label col-lg-2">Precio
                                                        de Venta:</label>
                                                    <div class="col-lg-4">
                                                        <input class="form-control input-lg m-bot15"
                                                               id="pventa" name="pventa"
                                                               placeholder="0.00" type="number" step="0.01" min="0.01" required/>
                                                    </div>

                                                </div>
                                                <div class="form-group">
                                                    <label for="pCompra" class="control-label col-lg-2">Precio
                                                        Compra:</label>
                                                    <div class="col-lg-4">
                                                        <input class="form-control input-lg m-bot15" id="pcompra"
                                                               name="pcompra" placeholder="0.00" type="number" step="0.01" min="0"
                                                               required/>
                                                    </div>
                                                    <label for="fechaRegistr"
                                                           class="control-label col-lg-2">Fecha:</label>
                                                    <div class="col-lg-4">
                                                        <input class="form-control input-lg m-bot15" type="date"
                                                               readonly name="fechaRegistro" autocomplete="off"
                                                               value="<?php echo date('Y-m-d'); ?>">
                                                    </div>

                                                </div>

                                            </section>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-default" data-dismiss="modal" aria-hidden="true"><strong>Cerrar</strong>
                                    </button>
                                    <button name="nuevo_Producto" type="submit" class="btn btn-primary">
                                        <strong>Registrar Nuevo Producto</strong></button>
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
                        <th>IMAGEN</th>
                        <th> CODIGO</th>
                        <th> PRODUCTO</th>
                        <th> DESCRIPCION</th>
                        <th>STOCK (<?php echo htmlspecialchars($usuarioSesion['nombreSucursal']); ?>)</th>
                        <th> PRECIO COMPRA</th>
                        <th> PRECIO VENTA</th>
                        <th> TIPO</th>
                        <th> FECHA REGISTRO</th>
                        <th> ACCIONES</th>
                    </tr>
                    </thead>
                    <?PHP
                    while ($product = mysqli_fetch_array($allProducto)) {
                        ?>

                        <tr>
                            <td><img src="<?php echo $urlViews . $product['imagen'] ?>" height="50"
                                     width="50"></td>
                            <td> <?PHP echo $product['codigo']; ?></td>
                            <td> <?PHP echo $product['nombreProducto']; ?></td>
                            <td> <?PHP echo $product['nombreProducto']; ?></td>
                            <td> <?PHP echo $product['cantidad']; ?></td>
                            <td> <?PHP echo dinero($product['precioCompra']); ?></td>
                            <td> <?PHP echo dinero($product['precioVenta']); ?></td>
                            <td> <?PHP echo $product['tipo']; ?></td>
                            <td> <?PHP echo $product['fechaRegistro']; ?></td>
                            <td>
                                <a href="#a<?php echo $product[0]; ?>" role="button"
                                   class="btn btn-success" data-toggle="modal">
                                    <i class="fa fa-pencil" title="Editar"></i> </a>
                                <a href="RegistroProducto.php?idborrar=<?PHP echo $product[0]; ?>" data-confirmar="el producto <?php echo htmlspecialchars($product['nombreProducto']); ?>"
                                   role="button" class="btn btn-danger"> <i class="icon_close_alt2"></i>
                                </a>
                            </td>
                        </tr>

                        <div id="a<?php echo $product[0]; ?>" class="modal fade" tabindex="-1" role="dialog"
                             aria-labelledby="myModalLabel" aria-hidden="true">
                            <form class="form-validate form-horizontal" name="form2" enctype="multipart/form-data"
                                  action="RegistroProducto.php"
                                  method="post">
                                <input type="hidden" id="idproducto" name="idproducto"
                                       value="<?php echo $product['idproducto']; ?>">
                                <input type="hidden" name="imagen" value="<?php echo $product['imagen']; ?>">

                                <div class="modal-dialog" id="mdialTamanio">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                    aria-hidden="true">x
                                            </button>
                                            <h3 id="myModalLabel" align="center">Cambiar Informacion del
                                                Producto</h3>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <section class="panel">
                                                        <img src="<?PHP echo $urlViews . $product['imagen']; ?>" width="250"
                                                             height="250">
                                                        <br><br>
                                                        <div><strong>Cambiar Imagen</strong></div>
                                                        <?php include("UploadViewImageEdit.php"); ?>
                                                    </section>
                                                </div>
                                                <div class="col-lg-8">
                                                    <section class="panel">
                                                        <div class="form-group">
                                                            <label class="col-sm-2 control-label">Tipo
                                                                Producto:</label>
                                                            <div class="col-sm-4">
                                                                <select class="form-control input-lg m-bot15"
                                                                        name="tipoproducto">
                                                                    <?php
                                                                    // MenuTipoProducto.php nunca existio; se usa la misma lista de tipos del formulario de registro
                                                                    mysqli_data_seek($tipoProductos, 0);
                                                                    $opcionesTipo = '';
                                                                    $tipoEncontrado = false;
                                                                    while ($tipoProducto = mysqli_fetch_array($tipoProductos)) {
                                                                        $seleccionado = $tipoProducto['tipoproducto'] == $product['tipo'] ? ' selected' : '';
                                                                        $tipoEncontrado = $tipoEncontrado || $seleccionado != '';
                                                                        $opcionesTipo .= '<option value="' . htmlspecialchars($tipoProducto['tipoproducto']) . '"' . $seleccionado . '>' . htmlspecialchars($tipoProducto['tipoproducto']) . '</option>';
                                                                    }
                                                                    // si el tipo del producto ya se borro de la lista, se muestra igual para no cambiarlo sin querer
                                                                    if (!$tipoEncontrado && $product['tipo'] != '') {
                                                                        echo '<option value="' . htmlspecialchars($product['tipo']) . '" selected>' . htmlspecialchars($product['tipo']) . ' (tipo eliminado)</option>';
                                                                    }
                                                                    echo $opcionesTipo;
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <label class="col-sm-2 control-label">Codigo:</label>
                                                            <div class="col-sm-4">
                                                                <input class="form-control input-lg m-bot15"
                                                                       id="codigo"
                                                                       name="codigo" type="text"
                                                                       value="<?php echo $product['codigo']; ?>"/>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-sm-2 control-label">Descripcion:</label>
                                                            <div class="col-sm-10">
                                                                <input class="form-control input-lg m-bot15"
                                                                       id="descripcion" name="descripcion"
                                                                       type="text"
                                                                       value="<?php echo $product['nombreProducto']; ?>"/>
                                                            </div>

                                                        </div>
                                                        <div class="form-group">
                                                            <label for="pdistribuidor"
                                                                   class="control-label col-lg-2">Stock en <?php echo htmlspecialchars($usuarioSesion['nombreSucursal']); ?> :</label>
                                                            <div class="col-lg-4">
                                                                <input class="form-control input-lg m-bot15"
                                                                       id="cantidad" name="cantidad"
                                                                       placeholder="0" type="number" step="1" min="0" required
                                                                       value="<?php echo $product['cantidad']; ?>"/>
                                                            </div>
                                                            <label for="pprofesional"
                                                                   class="control-label col-lg-2">Precio de
                                                                Compra:</label>
                                                            <div class="col-lg-4">
                                                                <input class="form-control input-lg m-bot15"
                                                                       id="pcompra" name="pcompra"
                                                                       placeholder="0.00" type="number" step="0.01" min="0" required
                                                                       value="<?php echo dinero($product['precioCompra']); ?>"/>
                                                            </div>

                                                        </div>


                                                        <div class="form-group">
                                                            <label for="ppublico" class="control-label col-lg-2">Precio
                                                                de
                                                                Venta:</label>
                                                            <div class="col-lg-4">
                                                                <input class="form-control input-lg m-bot15"
                                                                       id="pventa"
                                                                       name="pventa" placeholder="0.00"
                                                                       type="number" step="0.01" min="0.01" required
                                                                       value="<?php echo dinero($product['precioVenta']); ?>"/>
                                                            </div>
                                                            <label for="pventa"
                                                                   class="control-label col-lg-2">Fecha:</label>
                                                            <div class="col-lg-4">
                                                                <input class="form-control input-lg m-bot15"
                                                                       type="date"
                                                                       readonly name="fechaRegistro"
                                                                       autocomplete="off"
                                                                       value="<?php echo date('Y-m-d'); ?>">
                                                            </div>
                                                        </div>
                                                    </section>

                                                </div>
                                            </div>


                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
                                                <strong>Cerrar</strong></button>
                                            <button name="update_producto" type="submit" class="btn btn-primary">
                                                <strong>Editar</strong>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </form>
                        </div>

                    <?php } ?>
                </table>
            </div>
        </div>
    </section>
</section>
<!--main content end-->

<?PHP include("LibraryJs.php"); ?>


</body>
</html>