-- Datos del negocio (Configuracion > Datos del negocio): campos nuevos para que cada negocio personalice su comprobante.
-- La ciudad antes estaba escrita fija en el comprobante ("TEUPASENTI"); se copia a la fila existente para que no cambie nada.
-- Ejecutar una sola vez en la base de datos (local y en el hosting).

ALTER TABLE `datos` ADD COLUMN `ciudad` VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE `datos` ADD COLUMN `correo` VARCHAR(100) NOT NULL DEFAULT '';
ALTER TABLE `datos` ADD COLUMN `mensajePie` VARCHAR(200) NOT NULL DEFAULT '';   -- ej. "¡Gracias por su compra!"

UPDATE `datos` SET `ciudad` = 'TEUPASENTI' WHERE `ciudad` = '';
