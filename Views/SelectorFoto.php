<?PHP
/*
 * Selector de foto con vista previa (productos, clientes y usuarios). Se incluye con $campoFoto = nombre del campo.
 * Sirve aunque se repita en cada fila de una lista: no usa ids. Al elegir una foto, si en la ventana hay una foto
 * actual (antes del selector) se reemplaza por la nueva; si no, la vista previa sale debajo del boton.
 */
?>
<div class="selector-foto">
    <input type="file" name="<?PHP echo htmlspecialchars($campoFoto); ?>" accept="image/jpeg,image/png,image/gif,image/webp"
           onchange="verFotoElegida(this)">
    <div class="foto-aviso help-block" style="display:none; color:#a94442; font-weight:bold;"></div>
    <img class="foto-elegida" alt="Vista previa" style="display:none; width:100%; margin-top:8px;">
</div>
<?PHP if (!defined('SELECTOR_FOTO_JS')) { define('SELECTOR_FOTO_JS', true); ?>
<script>
    // muestra la foto elegida antes de guardar (el servidor vuelve a revisar el tipo y el peso al subirla)
    function verFotoElegida(input) {
        var caja = input.parentNode;
        var propia = caja.querySelector('.foto-elegida');
        var aviso = caja.querySelector('.foto-aviso');

        // foto actual: la primera imagen de la ventana que esta antes del selector
        var actual = null;
        var ventana = input.closest ? input.closest('.modal-body') : null;
        if (ventana) {
            var imagen = ventana.querySelector('img:not(.foto-elegida)');
            if (imagen && (imagen.compareDocumentPosition(input) & Node.DOCUMENT_POSITION_FOLLOWING)) {
                actual = imagen;
                if (!actual.getAttribute('data-original')) {
                    actual.setAttribute('data-original', actual.getAttribute('src'));
                }
            }
        }

        function volver(mensaje) {
            input.value = '';
            if (actual) { actual.src = actual.getAttribute('data-original'); }
            propia.style.display = 'none';
            aviso.textContent = mensaje || '';
            aviso.style.display = mensaje ? 'block' : 'none';
        }

        var foto = input.files && input.files[0];
        if (!foto) { volver(''); return; }
        if (!/^image\/(jpeg|png|gif|webp)$/.test(foto.type)) { volver('Ese archivo no es una foto. Elige una .jpg, .png, .gif o .webp.'); return; }
        if (foto.size > 5000000) { volver('La foto pesa más de 5 MB. Elige una más liviana.'); return; }

        aviso.style.display = 'none';
        var url = URL.createObjectURL(foto);
        if (actual) {
            actual.src = url;
        } else {
            propia.src = url;
            propia.style.display = 'block';
        }
    }
</script>
<?PHP } ?>
