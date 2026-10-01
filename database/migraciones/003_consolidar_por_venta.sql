-- Consolidar cada venta por su numero (antes se usaba el codigo de control, que se repite entre ventas)
-- y registrar que vendedor hizo cada venta, para que cada uno consolide solo las suyas.
-- No modifica los totales de las ventas antiguas.
-- Ejecutar una sola vez en la base de datos (local y en el hosting).

ALTER TABLE `datosventatotal` ADD COLUMN `idVentaTotal` INT NOT NULL DEFAULT 0;
ALTER TABLE `datosventatotal` ADD COLUMN `idUsuario` INT NOT NULL DEFAULT 0;
ALTER TABLE `datosventatotal` ADD INDEX `idx_datosventatotal_idUsuario` (`idUsuario`);

-- vincula las ventas existentes: todas las tablas de una venta se guardan con la misma fecha y codigo de control
UPDATE `datosventatotal` d
    JOIN `ventatotal` v ON v.fecha = d.fechaVenta AND v.codigoControl = d.codigoControl
SET d.idVentaTotal = v.idVentas, d.idUsuario = v.idUsuario;

-- Consolidar disponible para vendedores (cada uno ve solo sus ventas)
UPDATE `menu` SET `acceso` = 'A' WHERE `location` = 'Consolidar.php';
