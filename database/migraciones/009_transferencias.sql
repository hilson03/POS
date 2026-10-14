-- Pago por transferencia.
-- cuenta_bancaria: cuentas de la tienda donde los clientes transfieren (Configuracion > Cuentas bancarias).
-- clientedato y pago guardan el banco destino como texto ("BAC - 730123456"), asi el historial no cambia
-- si despues se edita o se desactiva la cuenta.
-- Ejecutar una sola vez en la base de datos (local y en el hosting).

CREATE TABLE `cuenta_bancaria` (
  `idCuenta` INT NOT NULL AUTO_INCREMENT,
  `banco` VARCHAR(60) NOT NULL,
  `numero` VARCHAR(40) NOT NULL,
  `titular` VARCHAR(100) NOT NULL DEFAULT '',
  `estado` VARCHAR(10) NOT NULL DEFAULT 'Activo',   -- Activo / Inactivo (inactiva no sale al cobrar)
  PRIMARY KEY (`idCuenta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `clientedato` ADD COLUMN `bancoDestino` VARCHAR(110) NOT NULL DEFAULT '';
ALTER TABLE `clientedato` ADD COLUMN `nombreTransfirio` VARCHAR(100) NOT NULL DEFAULT '';

ALTER TABLE `pago` ADD COLUMN `bancoDestino` VARCHAR(110) NOT NULL DEFAULT '';      -- transferencia: cuenta de la tienda que recibio
ALTER TABLE `pago` ADD COLUMN `nombreTransfirio` VARCHAR(100) NOT NULL DEFAULT '';  -- transferencia: titular de la cuenta que envio (opcional)
