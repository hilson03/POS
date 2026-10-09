-- Los vendedores solo ven Principal, Ventas, Traslados, Consolidar y Reporte.
-- Clientes y Productos pasan a ser solo para administradores (igual que Configuracion).
-- Ejecutar una sola vez en la base de datos (local y en el hosting).

UPDATE `menu` SET `acceso` = 'D' WHERE `location` IN ('Cliente.php', 'Producto.php');
