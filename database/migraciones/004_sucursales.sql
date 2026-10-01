-- Fase 1: sucursales y stock por sucursal.
-- El catalogo de productos y los clientes son compartidos; el stock, los pedidos y las ventas son de cada sucursal.
-- Decision del cliente: todas las sucursales empiezan con stock 0 (producto.cantidad se conserva pero ya no se usa).
-- Ejecutar una sola vez en la base de datos (local y en el hosting).

CREATE TABLE `sucursal` (
  `idSucursal` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `direccion` VARCHAR(200) NOT NULL DEFAULT '',
  `telefono` VARCHAR(50) NOT NULL DEFAULT '',
  `estado` VARCHAR(20) NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`idSucursal`)
);

-- sucursal inicial a la que quedan asignados los usuarios existentes (se puede renombrar en Configuracion > Sucursales)
INSERT INTO `sucursal` (`idSucursal`, `nombre`) VALUES (1, 'Sucursal Principal');

CREATE TABLE `stock_sucursal` (
  `idProducto` INT NOT NULL,
  `idSucursal` INT NOT NULL,
  `cantidad` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`idProducto`, `idSucursal`)
);

ALTER TABLE `usuarios` ADD COLUMN `idSucursal` INT NOT NULL DEFAULT 1;

-- el pedido en curso, cada venta y su detalle quedan registrados en la sucursal donde se hicieron
ALTER TABLE `preventa` ADD COLUMN `idSucursal` INT NOT NULL DEFAULT 1;
ALTER TABLE `preventa` ADD INDEX `idx_preventa_sucursal` (`idSucursal`, `idProducto`);
ALTER TABLE `ventatotal` ADD COLUMN `idSucursal` INT NOT NULL DEFAULT 1;
ALTER TABLE `datosventa` ADD COLUMN `idSucursal` INT NOT NULL DEFAULT 1;
ALTER TABLE `datosventatotal` ADD COLUMN `idSucursal` INT NOT NULL DEFAULT 1;
ALTER TABLE `datosventatotal` ADD INDEX `idx_datosventatotal_sucursal` (`idSucursal`);
