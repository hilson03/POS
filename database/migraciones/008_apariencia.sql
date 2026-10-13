-- Logos e imagenes que el administrador puede cambiar desde Configuracion > Apariencia.
-- Cada fila guarda la ruta (relativa a Views/) de la imagen personalizada; si no hay fila se usa la original.
-- Claves: logo_login, fondo_login, logo_barra, favicon.
-- Ejecutar una sola vez en la base de datos (local y en el hosting).
-- En el hosting, la carpeta Views/img/personalizado debe tener permiso de escritura (igual que Views/fotoproducto).

CREATE TABLE IF NOT EXISTS `apariencia` (
  `clave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
