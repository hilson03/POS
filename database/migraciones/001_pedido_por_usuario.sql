-- Fase 0, paso 1: cada usuario tiene su propio pedido (preventa) y sus propios datos de cobro (clientedato).
-- Ejecutar una sola vez en la base de datos (local y en el hosting).

ALTER TABLE `clientedato` ADD COLUMN `idUser` INT NOT NULL DEFAULT 0;
ALTER TABLE `clientedato` ADD INDEX `idx_clientedato_idUser` (`idUser`);

ALTER TABLE `preventa` ADD INDEX `idx_preventa_idUser` (`idUser`);

-- usuario que realizo cada venta
ALTER TABLE `ventatotal` ADD COLUMN `idUsuario` INT NOT NULL DEFAULT 0;
