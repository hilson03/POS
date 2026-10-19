-- Usuarios activos e inactivos: en lugar de borrar a un usuario se le desactiva, asi su nombre sigue apareciendo
-- en las ventas, Consolidar y traslados que hizo (auditoria). Un usuario inactivo no puede iniciar sesion.
-- Solo se pueden eliminar usuarios sin historial (por ejemplo, creados por error).
-- Ejecutar una sola vez en la base de datos (local y en el hosting).

ALTER TABLE `usuarios` ADD COLUMN `estado` VARCHAR(10) NOT NULL DEFAULT 'Activo';
