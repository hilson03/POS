<!--
    Ventana para elegir la forma de pago antes del cobro (se abre con el boton ACEPTAR del pedido).
    Efectivo sigue al cobro de siempre (Factura.php). Tarjeta y Transferencia se muestran pero todavia no funcionan.
    Va fija en la pantalla de Ventas porque el pedido se recarga por ajax y ahi no se ejecutan scripts.
-->
<style>
    .pago-fondo {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        z-index: 1040;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .pago-fondo[hidden] {
        display: none;
    }

    .pago-caja {
        background: #fff;
        border-radius: 8px;
        width: 100%;
        max-width: 620px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
        overflow: hidden;
        font-family: Arial, sans-serif;
    }

    .pago-cabecera {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid #e5e5e5;
    }

    .pago-cabecera h3 {
        margin: 0;
        font-size: 20px;
        font-weight: bold;
        color: #232a4d;
    }

    .pago-cerrar {
        background: none;
        border: none;
        font-size: 26px;
        line-height: 1;
        color: #888;
        cursor: pointer;
    }

    .pago-total {
        padding: 14px 20px 0;
        color: #555;
        font-size: 15px;
    }

    .pago-total strong {
        color: #232a4d;
        font-size: 22px;
    }

    .pago-opciones {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        padding: 18px 20px 22px;
    }

    @media (max-width: 560px) {
        .pago-opciones {
            grid-template-columns: 1fr;
        }
    }

    .pago-opcion {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 22px 12px 18px;
        border: 2px solid #d9dee8;
        border-radius: 8px;
        background: #f7f8fb;
        color: #232a4d;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
    }

    .pago-opcion svg {
        width: 46px;
        height: 46px;
    }

    .pago-opcion small {
        font-size: 12px;
        font-weight: normal;
        color: #666;
    }

    .pago-opcion:not([disabled]):hover,
    .pago-opcion:not([disabled]):focus {
        border-color: #2e9d5b;
        background: #eef8f2;
        transform: translateY(-3px);
        box-shadow: 0 6px 14px rgba(46, 157, 91, 0.2);
        outline: none;
    }

    .pago-opcion[disabled] {
        cursor: not-allowed;
        opacity: 0.55;
    }

    .pago-proximamente {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #f0ad4e;
        color: #fff;
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 2px 6px;
        border-radius: 3px;
    }
</style>

<div class="pago-fondo" id="ventanaFormaPago" hidden>
    <div class="pago-caja" role="dialog" aria-modal="true" aria-labelledby="tituloFormaPago">
        <div class="pago-cabecera">
            <h3 id="tituloFormaPago">¿Cómo va a pagar el cliente?</h3>
            <button type="button" class="pago-cerrar" id="cerrarFormaPago" aria-label="Cerrar">&times;</button>
        </div>
        <div class="pago-total">Total a pagar: <strong id="totalFormaPago"></strong></div>
        <div class="pago-opciones">
            <button type="button" class="pago-opcion" id="pagoEfectivo">
                <svg viewBox="0 0 24 24" fill="none" stroke="#2e9d5b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2.8"/><path d="M6 9.5v5M18 9.5v5"/>
                </svg>
                Efectivo
                <small>Continuar al cobro</small>
            </button>
            <button type="button" class="pago-opcion" disabled>
                <span class="pago-proximamente">Próximamente</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="#3b6fd8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>
                </svg>
                Tarjeta
                <small>Débito o crédito</small>
            </button>
            <button type="button" class="pago-opcion" disabled>
                <span class="pago-proximamente">Próximamente</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="#8a4fd0" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 10l9-6 9 6"/><path d="M5 10v8M9.5 10v8M14.5 10v8M19 10v8"/><path d="M3 20h18"/>
                </svg>
                Transferencia
                <small>Depósito bancario</small>
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        var ventana = document.getElementById('ventanaFormaPago');

        // la llama el boton ACEPTAR del pedido
        window.abrirFormaPago = function (total) {
            document.getElementById('totalFormaPago').textContent = total;
            ventana.hidden = false;
            document.getElementById('pagoEfectivo').focus();
        };

        function cerrar() {
            ventana.hidden = true;
        }

        // efectivo: abre la ventana de cobro de siempre (enlace oculto del pedido que carga Factura.php)
        document.getElementById('pagoEfectivo').addEventListener('click', function () {
            cerrar();
            var enlaceCobro = document.getElementById('abrirCobroEfectivo');
            if (enlaceCobro) {
                enlaceCobro.click();
            }
        });

        document.getElementById('cerrarFormaPago').addEventListener('click', cerrar);
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
