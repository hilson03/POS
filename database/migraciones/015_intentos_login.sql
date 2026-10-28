-- Limite de intentos de inicio de sesion: cada intento fallido se anota aqui. Con 5 fallos seguidos de un mismo
-- usuario (o 20 desde una misma conexion) en 15 minutos, se bloquea el inicio de sesion por 15 minutos, asi
-- nadie puede probar contrasenas sin fin. Al entrar bien se borran los fallos de ese usuario.
-- Ejecutar una sola vez en la base de datos (local y en el hosting), ANTES de subir los archivos nuevos.

CREATE TABLE IF NOT EXISTS `intento_login` (
  `idIntento` INT NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(50) NOT NULL,
  `ip` VARCHAR(45) NOT NULL,
  `fecha` DATETIME NOT NULL,
  PRIMARY KEY (`idIntento`),
  KEY `intento_login_usuario` (`login`, `fecha`),
  KEY `intento_login_ip` (`ip`, `fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
