-- Catalogo de bancos: cada banco existe una sola vez (tabla banco) y las cuentas de la tienda lo eligen de la lista,
-- en vez de escribirlo a mano ("bac", "BAC", "Bac Credomatic" eran bancos distintos).
-- Una cuenta no se puede repetir (mismo banco y mismo numero) y el numero de cuenta lleva solo digitos.
-- Las ventas ya hechas no cambian: el pago guarda el banco y la cuenta como texto.
-- Ejecutar una sola vez en la base de datos (local y en el hosting), ANTES de subir los archivos nuevos.

CREATE TABLE IF NOT EXISTS `banco` (
  `idBanco` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(60) NOT NULL,
  `estado` VARCHAR(10) NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`idBanco`),
  UNIQUE KEY `banco_nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- bancos de Honduras (los que no se usen se pueden desactivar en Configuracion > Cuentas bancarias)
INSERT IGNORE INTO `banco` (`nombre`) VALUES
  ('Banco Atlántida'), ('BAC Credomatic'), ('Banpaís'), ('Ficohsa'), ('Banco de Occidente'), ('Davivienda'),
  ('Banco Popular'), ('Lafise'), ('Banrural'), ('Promerica'), ('Banco Azteca'), ('Cuscatlán');

-- nombres escritos a mano que corresponden a un banco de la lista
UPDATE `cuenta_bancaria` SET `banco` = 'BAC Credomatic' WHERE LOWER(TRIM(`banco`)) IN ('bac', 'credomatic', 'bac honduras', 'bac credomatic');
UPDATE `cuenta_bancaria` SET `banco` = 'Banco Atlántida' WHERE LOWER(TRIM(`banco`)) IN ('atlantida', 'atlántida', 'banco atlantida');
UPDATE `cuenta_bancaria` SET `banco` = 'Banco de Occidente' WHERE LOWER(TRIM(`banco`)) IN ('occidente', 'banco occidente');
UPDATE `cuenta_bancaria` SET `banco` = 'Banco Popular' WHERE LOWER(TRIM(`banco`)) IN ('popular');
UPDATE `cuenta_bancaria` SET `banco` = 'Banco Azteca' WHERE LOWER(TRIM(`banco`)) IN ('azteca');
UPDATE `cuenta_bancaria` SET `banco` = 'Banpaís' WHERE LOWER(TRIM(`banco`)) IN ('banpais', 'banpaís', 'banco del pais', 'banco del país');

-- cualquier otro banco que ya estuviera escrito en una cuenta se agrega al catalogo
-- (la comparacion no distingue mayusculas ni tildes: "ficohsa" y "Ficohsa" son el mismo)
INSERT IGNORE INTO `banco` (`nombre`)
  SELECT DISTINCT TRIM(`banco`) FROM `cuenta_bancaria` WHERE TRIM(`banco`) <> '';

-- cada cuenta apunta a su banco del catalogo
ALTER TABLE `cuenta_bancaria` ADD COLUMN `idBanco` INT NOT NULL DEFAULT 0 AFTER `idCuenta`;
UPDATE `cuenta_bancaria` c
  JOIN `banco` b ON b.`nombre` = CONVERT(TRIM(c.`banco`) USING utf8mb4) COLLATE utf8mb4_unicode_ci
  SET c.`idBanco` = b.`idBanco`;

-- numero de cuenta solo con digitos (quita guiones y espacios que se hubieran escrito)
UPDATE `cuenta_bancaria` SET `numero` = REGEXP_REPLACE(`numero`, '[^0-9]', '');

-- una cuenta no se puede registrar dos veces en el mismo banco
ALTER TABLE `cuenta_bancaria` ADD UNIQUE KEY `cuenta_unica` (`idBanco`, `numero`);

-- el nombre del banco ya vive en el catalogo
ALTER TABLE `cuenta_bancaria` DROP COLUMN `banco`;
