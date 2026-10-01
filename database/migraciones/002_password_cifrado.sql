-- Fase 0, paso 3: las contrasenas se guardan cifradas con password_hash (60 caracteres o mas).
-- Las contrasenas actuales se convierten solas la proxima vez que cada usuario inicia sesion.
-- Ejecutar una sola vez en la base de datos (local y en el hosting).

ALTER TABLE `usuarios` MODIFY `password` VARCHAR(255) NOT NULL;
