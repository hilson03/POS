<div class="top-nav notification-row">
    <ul class="nav pull-right top-menu">
        <?PHP if (isset($usuarioSesion)) { ?>
        <li style="padding: 12px 10px 0 0;">
            <?PHP if ($usuarioSesion['tipo'] == 'ADMINISTRADOR') {
                // el administrador puede trabajar en cualquier sucursal activa
                $conSucursales = new conexion();
                $listaSucursales = $conSucursales->getSucursales(true);
                ?>
                <form action="CambiarSucursal.php" method="post" style="margin: 0;">
                    <i class="icon_building" style="color: #fff;"></i>
                    <select name="idSucursal" onchange="this.form.submit()" title="Sucursal en la que estás trabajando"
                            style="height: 30px; border-radius: 4px; border: none; padding: 0 6px; color: #333;">
                        <?PHP foreach ($listaSucursales as $sucursalMenu) { ?>
                            <option value="<?PHP echo $sucursalMenu['idSucursal']; ?>"<?PHP echo $sucursalMenu['idSucursal'] == $usuarioSesion['idSucursalActiva'] ? ' selected' : ''; ?>>
                                <?PHP echo htmlspecialchars($sucursalMenu['nombre']); ?>
                            </option>
                        <?PHP } ?>
                    </select>
                </form>
            <?PHP } else { ?>
                <span style="color: #fff; font-size: 14px;">
                    <i class="icon_building"></i> <?PHP echo htmlspecialchars($usuarioSesion['nombreSucursal']); ?>
                </span>
            <?PHP } ?>
        </li>
        <?PHP } ?>
        <li class="dropdown">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <span class="profile-ava">

                <img src="<?PHP echo $urlViews .$imageUser;?>" alt="Usuario" height="35" width="35">

                </span>
                <span class="username"> <?PHP echo $userLogueado; ?> </span>
                <b class="caret"></b>
            </a>
            <?PHP include ("MenuOpciones.php"); ?>
        </li>

    </ul>
</div>
