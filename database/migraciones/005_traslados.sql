-- Fase 2: solicitudes de traslado de productos entre sucursales.
-- Recorrido: Pendiente -> Aprobada (o Rechazada) -> EnCamino (se descuenta del origen) -> Recibida (se suma al destino).
-- La tienda que pidio puede Cancelar mientras este Pendiente o Aprobada.
-- Ejecutar una sola vez en la base de datos (local y en el hosting).

CREATE TABLE `traslado` (
  `idTraslado` INT NOT NULL AUTO_INCREMENT,
  `idProducto` INT NOT NULL,
  `cantidad` INT NOT NULL,
  `idSucursalOrigen` INT NOT NULL,          -- tienda que tiene el producto y lo envia
  `idSucursalDestino` INT NOT NULL,         -- tienda que lo pidio y lo recibe
  `estado` VARCHAR(20) NOT NULL DEFAULT 'Pendiente',
  `motivo` VARCHAR(255) NOT NULL DEFAULT '',
  `motivoRechazo` VARCHAR(255) NOT NULL DEFAULT '',
  `idUsuarioSolicita` INT NOT NULL,
  `fechaSolicitud` DATETIME NOT NULL,
  `idUsuarioAprueba` INT NULL,
  `fechaAprueba` DATETIME NULL,
  `idUsuarioEnvia` INT NULL,
  `fechaEnvio` DATETIME NULL,
  `idUsuarioRecibe` INT NULL,
  `fechaRecibe` DATETIME NULL,
  PRIMARY KEY (`idTraslado`),
  INDEX `idx_traslado_origen` (`idSucursalOrigen`, `estado`),
  INDEX `idx_traslado_destino` (`idSucursalDestino`, `estado`)
) ENGINE=InnoDB;

-- opcion del menu (despues de Ventas); disponible para todos, cada pantalla valida lo que puede hacer cada usuario
INSERT INTO `menu` (`idmenu`, `opcion`, `estado`, `icon`, `location`, `color`, `acceso`)
VALUES (8, 'Traslados', 'NoActivo', 'arrow_left-right_alt', 'Traslados.php', '#0061c2', 'A');
