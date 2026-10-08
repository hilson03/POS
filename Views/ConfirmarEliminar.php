<!--
    Ventana de confirmacion. Cualquier enlace con el atributo data-confirmar="texto" muestra esta ventana;
    solo si se confirma se sigue el enlace.
    data-confirmar-tipo elige el modo: "eliminar" (por defecto, roja) o "consolidar" (verde).
-->
<style>
    .confirmar-fondo {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .confirmar-fondo[hidden] {
        display: none;
    }

    .confirmar-caja {
        background: #fff;
        border-radius: 6px;
        max-width: 420px;
        width: 100%;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        font-family: Arial, sans-serif;
    }

    .confirmar-cabecera.verde {
        background: #2e9d5b;
    }

    .confirmar-cabecera {
        background: #d9534f;
        color: #fff;
        padding: 14px 18px;
        font-size: 18px;
        font-weight: bold;
    }

    .confirmar-cuerpo {
        padding: 20px 18px;
        font-size: 15px;
        color: #333;
        line-height: 1.5;
    }

    .confirmar-cuerpo strong {
        color: #000;
    }

    .confirmar-aviso {
        margin-top: 8px;
        font-size: 13px;
        color: #a94442;
    }

    .confirmar-botones {
        padding: 12px 18px;
        background: #f5f5f5;
        text-align: right;
    }

    .confirmar-botones button {
        margin-left: 8px;
    }
</style>

<div class="confirmar-fondo" id="confirmarEliminar" hidden>
    <div class="confirmar-caja" role="dialog" aria-modal="true" aria-labelledby="confirmarTitulo">
        <div class="confirmar-cabecera" id="confirmarTitulo"><i class="icon_error-triangle_alt"></i> <span id="confirmarTituloTexto">Confirmar eliminación</span></div>
        <div class="confirmar-cuerpo">
            <span id="confirmarPregunta">¿Seguro que deseas eliminar</span> <strong id="confirmarNombre"></strong>?
            <div class="confirmar-aviso" id="confirmarAviso">Esta acción no se puede deshacer.</div>
        </div>
        <div class="confirmar-botones">
            <button type="button" class="btn btn-default" id="confirmarCancelar">Cancelar</button>
            <button type="button" class="btn btn-danger" id="confirmarAceptar">Sí, eliminar</button>
        </div>
    </div>
</div>

<script>
    (function () {
        var ventana = document.getElementById('confirmarEliminar');
        var destino = null;

        // textos y color de cada modo
        var modos = {
            eliminar: {
                titulo: 'Confirmar eliminación', pregunta: '¿Seguro que deseas eliminar',
                aviso: 'Esta acción no se puede deshacer.', boton: 'Sí, eliminar', claseBoton: 'btn btn-danger', verde: false
            },
            consolidar: {
                titulo: 'Confirmar consolidación', pregunta: '¿Deseas consolidar',
                aviso: 'Una vez consolidada no se puede deshacer y la venta pasará a los reportes.',
                boton: 'Aceptar', claseBoton: 'btn btn-success', verde: true
            }
        };

        function cerrar() {
            ventana.hidden = true;
            destino = null;
        }

        document.addEventListener('click', function (evento) {
            var enlace = evento.target.closest ? evento.target.closest('a[data-confirmar]') : null;
            if (!enlace) {
                return;
            }
            evento.preventDefault();
            destino = enlace.getAttribute('href');
            var modo = modos[enlace.getAttribute('data-confirmar-tipo')] || modos.eliminar;
            document.getElementById('confirmarTituloTexto').textContent = modo.titulo;
            document.getElementById('confirmarTitulo').className = 'confirmar-cabecera' + (modo.verde ? ' verde' : '');
            document.getElementById('confirmarPregunta').textContent = modo.pregunta;
            document.getElementById('confirmarAviso').textContent = modo.aviso;
            document.getElementById('confirmarAceptar').textContent = modo.boton;
            document.getElementById('confirmarAceptar').className = modo.claseBoton;
            document.getElementById('confirmarNombre').textContent = enlace.getAttribute('data-confirmar');
            ventana.hidden = false;
            document.getElementById('confirmarCancelar').focus();
        });

        document.getElementById('confirmarAceptar').addEventListener('click', function () {
            if (destino) {
                window.location.href = destino;
            }
        });
        document.getElementById('confirmarCancelar').addEventListener('click', cerrar);

        // cerrar con la tecla Escape o haciendo clic fuera de la caja
        ventana.addEventListener('click', function (evento) {
            if (evento.target === ventana) {
                cerrar();
            }
        });
        document.addEventListener('keydown', function (evento) {
            if (evento.key === 'Escape' && !ventana.hidden) {
                cerrar();
            }
        });
    })();
</script>
