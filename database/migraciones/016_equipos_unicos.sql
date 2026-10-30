-- Equipos unicos: los tipos de producto marcados (celulares) identifican cada equipo por su IMEI.
-- Para esos productos el IMEI lleva 15 digitos, no se repite en ninguna tienda y el stock es 0 o 1
-- (un telefono esta en una sola tienda; para moverlo se usa Traslados). Los accesorios no cambian:
-- su codigo se puede repetir y pueden tener cualquier cantidad.
-- Ejecutar una sola vez en la base de datos (local y en el hosting), ANTES de subir los archivos nuevos.

ALTER TABLE `tipoproducto` ADD COLUMN `codigoUnico` TINYINT(1) NOT NULL DEFAULT 0;

-- los tipos que ya son de telefonos quedan marcados
UPDATE `tipoproducto` SET `codigoUnico` = 1
 WHERE LOWER(`tipoproducto`) LIKE '%celular%' OR LOWER(`tipoproducto`) LIKE '%telefono%'
    OR LOWER(`tipoproducto`) LIKE '%teléfono%' OR LOWER(`tipoproducto`) LIKE '%smartphone%';
