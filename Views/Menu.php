<aside>
<div id="sidebar" class="nav-collapse">
    <ul class="sidebar-menu" style="background-color: #4e4e4e; ">
        <?php
        foreach ($menuMain as $menu){
            echo "<li class ='' style = 'backgroud-color:".$menu['color'].";'>";
            echo "<a class='' href=". $menu['location']."?usuario=$usuario&password=$password".">";
            echo "<i class=" . $menu['icon'] . "></i>";
            echo "<span><span #fff style=\"color: ; \"'>".$menu['opcion']."</span></span>";
            echo "</a>";
            echo "</li>";
        }
        ?>
    </ul>
</div>
</aside>