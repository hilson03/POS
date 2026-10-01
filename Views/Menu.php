<aside>
<div id="sidebar" class="nav-collapse">
    <ul class="sidebar-menu" style="background-color: #4e4e4e; ">
        <?php
        $esAdministrador = isset($usuarioSesion) && $usuarioSesion['tipo'] == 'ADMINISTRADOR';
        foreach ($menuMain as $menu){
            // acceso 'D' = opcion solo para administradores (Configuracion, Consolidar)
            if ($menu['acceso'] == 'D' && !$esAdministrador) {
                continue;
            }
            echo "<li class ='' style = 'backgroud-color:".$menu['color'].";'>";
            echo "<a class='' href=". $menu['location'].">";
            echo "<i class=" . $menu['icon'] . "></i>";
            echo "<span><span #fff style=\"color: ; \"'>".$menu['opcion']."</span></span>";
            echo "</a>";
            echo "</li>";
        }
        ?>
    </ul>
</div>
</aside>