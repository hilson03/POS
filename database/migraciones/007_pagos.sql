-- Formas de pago: cada venta guarda su(s) pago(s) en la tabla `pago` (hoy uno por venta; preparada para pago mixto).
-- clientedato (los datos del cobro en curso de cada vendedor) lleva la forma de pago hasta que se registra la venta.
-- Las ventas anteriores no tienen fila en `pago` y se consideran pagadas en efectivo.
-- Ejecutar una sola vez en la base de datos (local y en el hosting).

ALTER TABLE `clientedato` ADD COLUMN `formaPago` VARCHAR(20) NOT NULL DEFAULT 'Efectivo';
ALTER TABLE `clientedato` ADD COLUMN `referenciaPago` VARCHAR(50) NOT NULL DEFAULT '';
ALTER TABLE `clientedato` ADD COLUMN `ultimos4` VARCHAR(4) NOT NULL DEFAULT '';

CREATE TABLE `pago` (
  `idPago` INT NOT NULL AUTO_INCREMENT,
  `idVenta` INT NOT NULL,                     -- ventatotal.idVentas
  `formaPago` VARCHAR(20) NOT NULL,           -- Efectivo, Tarjeta, Transferencia
  `monto` DECIMAL(12,2) NOT NULL,
  `referencia` VARCHAR(50) NOT NULL DEFAULT '',  -- tarjeta: numero de autorizacion
  `ultimos4` VARCHAR(4) NOT NULL DEFAULT '',     -- tarjeta: ultimos 4 digitos (nunca el numero completo)
  `fecha` DATETIME NOT NULL,
  `idSucursal` INT NOT NULL,
  `idUsuario` INT NOT NULL,
  PRIMARY KEY (`idPago`),
  INDEX `idx_pago_venta` (`idVenta`),
  INDEX `idx_pago_sucursal_fecha` (`idSucursal`, `fecha`)
) ENGINE=InnoDB;
