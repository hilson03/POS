<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Comprobante de venta</title>
    <style>
        html, body { margin: 0; padding: 0; background: #fff; }
        /* el navegador no agrega margenes; el ancho del papel lo da el comprobante */
        @page { margin: 0; }
    </style>
</head>
<body>
<?PHP include(__DIR__ . '/Comprobante.php'); ?>
<script>
    // la hoja mide lo mismo que el comprobante (ancho del rollo x alto del contenido),
    // asi la impresora termica corta justo al terminar y "Guardar como PDF" muestra como queda en el rollo
    (function () {
        var comprobante = document.querySelector('.comprobante');
        var altoMm = Math.ceil(comprobante.getBoundingClientRect().height * 25.4 / 96) + 6;
        var estilo = document.createElement('style');
        estilo.textContent = '@page { size: <?PHP echo anchoPapel(); ?>mm ' + altoMm + 'mm; margin: 0; }';
        document.head.appendChild(estilo);
    })();
</script>
</body>
</html>
