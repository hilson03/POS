# Instalación en el hosting

Pasos para subir el sistema a un hosting (por ejemplo Hostinger) sin perder datos ni dejar partes sin funcionar.

## 1. Base de datos

Hay dos caminos. **Usa solo uno.**

### Camino A (recomendado): exportar la base local completa

La base local ya tiene aplicadas todas las migraciones de esta carpeta (001 a 010).

1. En la computadora local, abrir phpMyAdmin (Laragon → Database) y exportar la base `icontpos` completa (formato SQL).
2. En el hosting, crear una base de datos nueva y su usuario desde el panel.
3. En el phpMyAdmin del hosting, importar el archivo exportado.
4. **No hay que ejecutar ninguna migración**: ya vienen incluidas.

Antes de exportar conviene borrar las ventas y clientes de prueba, si el cliente quiere arrancar en limpio.

### Camino B: base que ya existe en el hosting

Si en el hosting ya hay una base con datos reales, ejecutar en orden **solo las migraciones que esa base no tenga**:

| Migración | Qué agrega |
|---|---|
| 001_pedido_por_usuario.sql | Cada vendedor con su propio pedido |
| 002_password_cifrado.sql | Contraseñas cifradas |
| 003_consolidar_por_venta.sql | Consolidar venta por venta |
| 004_sucursales.sql | Sucursales y stock por sucursal |
| 005_traslados.sql | Traslados entre sucursales |
| 006_menu_vendedores.sql | Permisos de los vendedores en el menú |
| 007_pagos.sql | Tabla de pagos y pago con tarjeta |
| 008_apariencia.sql | Logos personalizados y ancho del rollo de la impresora |
| 009_transferencias.sql | Cuentas bancarias y pago por transferencia |
| 010_pago_mixto.sql | Pago mixto |

Cada archivo se ejecuta **una sola vez**. Si al ejecutar uno sale "Duplicate column" o "Table already exists", esa migración ya estaba aplicada: se salta y se sigue con la siguiente.

## 2. Archivos

1. Subir todos los archivos del proyecto a la carpeta pública del dominio (`public_html`).
2. Las fotos de productos (`Views/fotoproducto/`) y los logos personalizados (`Views/img/personalizado/`) **no están en GitHub**: copiarlos desde la computadora local si se quieren conservar.

## 3. Permisos de carpetas

El sistema guarda archivos en estas carpetas, así que deben tener permiso de escritura (755 en Hostinger suele bastar; si al subir una foto o un logo sale error de permisos, probar 775):

- `Views/fotoproducto/` (fotos de productos y usuarios)
- `Views/img/personalizado/` (logos de Configuración → Apariencia)

## 4. Conexión a la base

Poner en `Model/Conexion.php` los datos de la base del hosting (servidor, usuario, contraseña y nombre de la base).
**No subir esos datos a GitHub**: el repositorio es público.

## 5. Comprobar después de instalar

- [ ] Entrar con un usuario administrador.
- [ ] Hacer una venta de prueba en efectivo, con tarjeta, por transferencia y mixta, y revisar que salgan en Consolidar.
- [ ] Generar un reporte PDF.
- [ ] Subir la foto de un producto y un logo en Apariencia (confirma los permisos de las carpetas).
- [ ] Revisar que la hora de las ventas sea la de Honduras (el sistema la fija solo, no depende del servidor).
- [ ] Borrar la venta de prueba.
