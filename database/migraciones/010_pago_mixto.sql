-- Pago mixto: una venta pagada con varias formas (ej. tarjeta + efectivo).
-- Mientras se confirma la factura, el detalle del cobro viaja en clientedato.pagosDetalle (JSON con cada parte:
-- forma, monto, referencia, ultimos4, bancoDestino, nombreTransfirio). Al registrar la venta, cada parte
-- se guarda como una fila en la tabla `pago` (que ya admite varias filas por venta).
-- Ejecutar una sola vez en la base de datos (local y en el hosting).

ALTER TABLE `clientedato` ADD COLUMN `pagosDetalle` TEXT NULL;
