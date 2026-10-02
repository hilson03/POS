<aside>
<div id="sidebar" class="nav-collapse">
    <ul class="sidebar-menu" style="background-color: #4e4e4e; ">
        <?php
        $menuEsAdministrador = isset($usuarioSesion) && $usuarioSesion['tipo'] == 'ADMINISTRADOR';
        foreach ($menuMain as $menu){
            // acceso 'D' = opcion solo para administradores (Configuracion)
            if ($menu['acceso'] == 'D' && !$menuEsAdministrador) {
                continue;
            }

            // traslados que esperan una accion de esta sucursal (aprobar, enviar o confirmar llegada)
            $contador = '';
            if ($menu['location'] == 'Traslados.php' && isset($usuarioSesion)) {
                $conMenu = new conexion();
                $porAtender = $conMenu->contarTrasladosPorAtender($usuarioSesion['idSucursalActiva'], $menuEsAdministrador);
                if ($porAtender > 0) {
                    $contador = " <span class='badge' style='background-color: #d9534f;'>" . $porAtender . "</span>";
                }
            }

            echo "<li class ='' style = 'backgroud-color:".$menu['color'].";'>";
            echo "<a class='' href=". $menu['location'].">";
            echo "<i class=" . $menu['icon'] . "></i>";
            echo "<span><span #fff style=\"color: ; \"'>".$menu['opcion']."</span>" . $contador . "</span>";
            echo "</a>";
            echo "</li>";
        }
        ?>
    </ul>
</div>
</aside>
