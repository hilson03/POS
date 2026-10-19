-- "Consolidar" pasa a llamarse "Cierre de ventas", que es como le dicen en las tiendas de Honduras.
-- Solo cambia el nombre que se ve en el menu lateral; la pantalla (Consolidar.php) y los estados guardados
-- de las ventas (NoConsolidado / Consolidado) siguen igual por dentro.
-- Ejecutar una sola vez en la base de datos (local y en el hosting).

UPDATE `menu` SET `opcion` = 'Cierre de ventas' WHERE `location` = 'Consolidar.php';
