<!DOCTYPE html>
<html lang="en">
    <?php 
    include('Head.php')
    ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
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
                             <input class="form-control" placeholder="Search" type="text">
                    </form>
                </li>
            </ul>
            <!--  search form end -->
        </div>
        <?PHP include ("DropDown.php"); ?> 
    </header>
    <?PHP include ("Menu.php"); ?>
</section>
    
<?PHP include ("LibraryJs.php"); ?>

</body>
</html>