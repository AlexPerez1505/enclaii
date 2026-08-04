<style>
/* ==================== TEMA & TOKENS ==================== */
:root{
  --bank-bg: #ffffff;
  --bank-surface: #fbfdff;
  --bank-border: #e7eef6;
  --bank-accent: #64b5ff;      /* pastel primario */
  --bank-accent-strong:#2f86e5;
  --bank-ink: #0b2a47;
  --bank-ink-soft:#3b5b71;
  --bank-muted:#6b7b83;
  --bank-warn:#b26a00;
  --bank-warn-bg:#fff7e3;
  --bank-warn-br:#ffe2a6;
  --radius-xl: 20px;
  --radius-lg: 14px;
  --shadow-lg: 0 22px 60px rgba(9,30,66,.12);
  --shadow-md: 0 10px 30px rgba(9,30,66,.08);
}

/* ==================== CONTENEDOR MODAL ==================== */
#pagoModal .modal-dialog{ max-width: 620px }
#pagoModal .modal-content{
  border-radius: var(--radius-xl);
  border:1px solid var(--bank-border);
  background: var(--bank-bg);
  box-shadow: var(--shadow-lg);
  overflow: hidden;
  animation: modal-enter .45s cubic-bezier(.2,.8,.2,1) both;
}
@keyframes modal-enter{
  from{ transform: translateY(12px) scale(.98); opacity: 0 }
  to  { transform: translateY(0)    scale(1);    opacity: 1 }
}

/* ==================== HEADER ==================== */
#pagoModal .modal-header{
  border:0; padding:22px 24px;
  background: radial-gradient(120% 140% at 0% 0%, #eaf4ff 0%, #f7fbff 40%, #ffffff 100%);
}
#pagoModal .modal-title{
  color: var(--bank-ink);
  font-weight: 800; letter-spacing:.2px;
  display: grid; grid-template-columns:auto 1fr; align-items:center; gap:12px;
}
.badge-chip{
  font-size:.72rem; padding:.2rem .6rem; border-radius:99px;
  background:#e6f2ff; color: var(--bank-accent-strong); border:1px solid #d2e6ff;
  transform-origin:left center; animation: chip-pop .5s .2s both ease;
}
@keyframes chip-pop{ from{transform:scale(.8); opacity:0} to{transform:scale(1); opacity:1} }

/* ==================== BODY ==================== */
#pagoModal .modal-body{ padding:18px 24px 8px 24px }

/* Tarjeta de resumen (cuota) */
.bank-card{
  background: var(--bank-surface);
  border:1px solid var(--bank-border);
  border-radius: var(--radius-lg);
  padding: 16px 16px 14px;
  box-shadow: var(--shadow-md);
  margin-bottom: 14px;
  position: relative;
  overflow: hidden;
}
.bank-card::after{
  content:"";
  position:absolute; inset:0;
  background: radial-gradient(80% 70% at 120% -10%, rgba(111,181,255,.18), transparent 60%);
  pointer-events:none;
}
.bank-k{ font-size:.78rem; color:var(--bank-muted); letter-spacing:.2px }
.bank-v{ font-weight:700; color:var(--bank-ink) }
.bank-amount{
  font-variant-numeric: tabular-nums;
  font-weight:800; letter-spacing:.3px; color:var(--bank-ink);
  font-size:1.7rem;
  transform-origin:right center; animation: amount-rise .5s .05s ease both;
}
@keyframes amount-rise{ from{transform: translateY(6px); opacity:0} to{transform:none; opacity:1} }

/* Campos */
.form-label{ font-weight:700; color:#244863 }
.bank-input, .bank-select{
  border-radius:12px; border:1px solid #d8e3ec; background:#f9fbfd;
  padding:.8rem .95rem; transition:.2s border-color, .2s box-shadow, .2s transform;
}
.bank-input:focus, .bank-select:focus{
  border-color: var(--bank-accent);
  box-shadow:0 0 0 .2rem rgba(100,181,255,.25);
  transform: translateY(-1px);
}
.input-group-text{
  background:#f1f6fb; border-color:#d8e3ec; font-weight:700; color:var(--bank-ink-soft)
}

/* Barra de coincidencia (monto ingresado vs esperado) */
.match-wrap{ margin-top:8px }
.match-bar{
  height:10px; border-radius:99px; background:#eef3f8; position:relative; overflow:hidden;
  border:1px solid #dde8f3;
}
.match-fill{
  position:absolute; inset:0 100% 0 0; background: linear-gradient(90deg,#9dd1ff,#64b5ff);
  border-radius:99px; transition: inset .35s cubic-bezier(.2,.8,.2,1);
}
.match-label{ font-size:.78rem; color:var(--bank-muted); margin-top:6px }

/* Warning inline */
.inline-warning{
  display:none; margin-top:10px; border:1px solid var(--bank-warn-br);
  background: var(--bank-warn-bg); color:var(--bank-warn);
  padding:10px 12px; border-radius:12px; font-size:.92rem;
  animation: warn-in .3s ease both;
}
@keyframes warn-in{ from{transform:translateY(-4px); opacity:0} to{transform:none; opacity:1} }

/* ==================== FOOTER & BOTONES ==================== */
#pagoModal .modal-footer{
  border:0; padding:16px 24px 22px 24px;
  background: linear-gradient(180deg,#ffffff 0%, #f9fbff 100%);
  display:flex; justify-content:space-between; gap:12px;
}
.btn-bank{
  position:relative; overflow:hidden; border-radius:12px; padding:.72rem 1.1rem; font-weight:800;
  transition: transform .12s ease, filter .2s ease;
}
.btn-ghost{
  background:#eaf3ff; color:var(--bank-ink); border:1px solid #d2e6ff;
}
.btn-ghost:hover{ filter:brightness(.98) }
.btn-primary-bank{
  background: var(--bank-accent); color:#07223a; border:1px solid #8cc8ff;
  box-shadow: 0 8px 20px rgba(100,181,255,.35);
}
.btn-primary-bank:hover{ transform: translateY(-1px) }
.btn-bank:active{ transform: translateY(0) scale(.99) }

/* Ripple */
.btn-bank .ripple{
  position:absolute; border-radius:50%; transform: scale(0); animation:ripple .6s linear;
  background: rgba(255,255,255,.6); pointer-events:none;
}
@keyframes ripple{ to{ transform: scale(3); opacity:0 } }

/* Accesibilidad */
@media (prefers-reduced-motion: reduce){
  *{ animation: none !important; transition: none !important }
}
</style>

<!-- ============== MODAL REGISTRAR PAGO (BANCO) ============== -->
<div class="modal fade" id="pagoModal" tabindex="-1" aria-labelledby="pagoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="formPagoModal" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <div class="w-100 d-flex justify-content-between align-items-center">
            <h5 class="modal-title" id="pagoModalLabel">Registrar pago</h5>
            <span class="badge-chip" id="pm-chip">Cuota programada</span>
          </div>
        </div>

        <div class="modal-body">
          <!-- Resumen estilo banca -->
          <div class="bank-card">
            <div>
              <div class="bank-k">Fecha programada</div>
              <div class="bank-v" id="pm-fecha-text">—</div>
              <div class="bank-k mt-1" id="pm-desc-text"></div>
            </div>
            <div class="text-end">
              <div class="bank-k">Monto esperado</div>
              <div class="bank-amount" id="pm-esperado-text">$0.00</div>
            </div>
          </div>

          <!-- Hidden para validación/envío -->
          <input type="hidden" id="pm-expected-amount" value="0">
          <input type="hidden" name="fecha_programada" id="pm-fecha-hidden">
          <input type="hidden" name="descripcion_programada" id="pm-desc-hidden">

          <!-- Campos del formulario -->
          <div class="mb-3">
            <label class="form-label">Monto recibido</label>
            <div class="input-group">
              <span class="input-group-text">$</span>
              <input type="number" step="0.01" min="0" class="form-control bank-input" name="monto" id="modalMonto" required>
            </div>

            <!-- Barra de coincidencia -->
            <div class="match-wrap">
              <div class="match-bar"><div class="match-fill" id="matchFill"></div></div>
              <div class="match-label"><span id="matchText">0% coincide con el esperado</span></div>
            </div>

            <!-- Warning inline -->
            <div class="inline-warning" id="pm-mismatch">
              El monto ingresado no coincide con el monto programado.  
              Para cambiarlo, edita primero el plan en <b>Pagos de financiamiento</b>.
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha de pago</label>
            <input type="date" class="form-control bank-input" name="fecha_pago" id="modalFecha" required>
          </div>

          <div class="mb-2">
            <label class="form-label">Método de pago</label>
            <select class="form-select bank-select" name="metodo_pago" id="modalMetodo" required>
              <option value="">Selecciona…</option>
              <option value="Efectivo">Efectivo</option>
              <option value="Transferencia bancaria">Transferencia bancaria</option>
              <option value="Tarjeta">Tarjeta</option>
              <option value="Cheque">Cheque</option>
            </select>
          </div>

          <div class="mb-2">
            <label class="form-label">Adjuntar recibo (opcional)</label>
            <input type="file" class="form-control bank-input" name="recibo">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-bank btn-ghost" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-bank btn-primary-bank" id="pm-submit">Guardar pago</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- SweetAlert2 si no lo tienes en el layout -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const pagoModal   = document.getElementById('pagoModal');
  const form        = document.getElementById('formPagoModal');
  const montoInput  = document.getElementById('modalMonto');
  const fechaInput  = document.getElementById('modalFecha');
  const metodoInput = document.getElementById('modalMetodo');
  const mismatch    = document.getElementById('pm-mismatch');
  const matchFill   = document.getElementById('matchFill');
  const matchText   = document.getElementById('matchText');
  const submitBtn   = document.getElementById('pm-submit');

  // Ripple en botones .btn-bank
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.btn-bank');
    if(!btn) return;
    const r = document.createElement('span');
    const rect = btn.getBoundingClientRect();
    r.className = 'ripple';
    r.style.left = (e.clientX - rect.left) + 'px';
    r.style.top  = (e.clientY - rect.top)  + 'px';
    btn.appendChild(r);
    setTimeout(()=> r.remove(), 600);
  });

  // util: moneda para UI
  const fmt = (n)=> Number(n||0).toLocaleString('es-MX',{minimumFractionDigits:2, maximumFractionDigits:2});

  // Conteo animado del monto esperado
  function animateAmount(el, to){
    const duration = 600;
    const start = performance.now();
    const from = 0;
    function tick(now){
      const p = Math.min(1, (now - start)/duration);
      const val = from + (to - from)*p;
      el.textContent = '$' + fmt(val);
      if(p<1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  }

  // Actualiza barra de coincidencia
  function updateMatch(){
    const esperado = parseFloat(document.getElementById('pm-expected-amount').value || 0);
    const ingresado = parseFloat(montoInput.value || 0);
    let pct = 0;
    if(esperado>0){
      pct = Math.max(0, Math.min(100, (ingresado/esperado)*100));
    }
    // llenar de izquierda a derecha (controlando borde derecho con inset)
    const rightInset = Math.max(0, 100 - pct);
    matchFill.style.inset = `0 ${rightInset}% 0 0`;
    matchText.textContent = `${Math.round(pct)}% coincide con el esperado`;

    const iguales = Math.abs(esperado - ingresado) < 0.005;
    mismatch.style.display = iguales ? 'none' : 'block';
    return iguales;
  }

  // Abrir modal con datos desde el botón "Registrar"
  pagoModal.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const ventaId     = btn.getAttribute('data-venta-id');
    const fecha       = btn.getAttribute('data-fecha') || '';
    const esperado    = parseFloat(btn.getAttribute('data-monto') || 0);
    const descripcion = btn.getAttribute('data-descripcion') || '';

    // UI resumen
    document.getElementById('pm-fecha-text').textContent    = fecha ? new Date(fecha).toLocaleDateString('es-MX') : '—';
    document.getElementById('pm-desc-text').textContent     = descripcion;
    document.getElementById('pm-fecha-hidden').value        = fecha;
    document.getElementById('pm-desc-hidden').value         = descripcion;
    document.getElementById('pm-expected-amount').value     = esperado.toFixed(2);

    // Conteo animado del monto esperado
    animateAmount(document.getElementById('pm-esperado-text'), esperado);

    // Prefill campos
    fechaInput.value = fecha;
    montoInput.value = esperado.toFixed(2);
    metodoInput.value = "";
    mismatch.style.display = 'none';
    updateMatch();

    // Endpoint Laravel (ajústalo si usas route() en Blade)
    form.action = `/ventas/${ventaId}/pagos`;
    form.method = 'POST';
  });

  // Validaciones en vivo
  montoInput.addEventListener('input', updateMatch);
  montoInput.addEventListener('blur', ()=>{ if(montoInput.value){ montoInput.value = (parseFloat(montoInput.value)||0).toFixed(2); } updateMatch(); });

  // Envío con validación dura + tutorial
  form.addEventListener('submit', async function(e){
    const ok = updateMatch();
    if(!ok){
      e.preventDefault();

      const modalInst = bootstrap.Modal.getOrCreateInstance(pagoModal);
      const { isConfirmed } = await Swal.fire({
        icon:'info',
        title:'Monto distinto al programado',
        html: `
          <div class="text-start">
            <p>Para registrar un monto diferente, primero debes ajustar el plan en <b>Pagos de financiamiento</b>.</p>
            <ol class="mb-2">
              <li>Cierra este modal.</li>
              <li>Haz clic en <b>“Editar pagos de financiamiento”</b>.</li>
              <li>Edita el <b>monto</b> de la(s) cuota(s) correspondiente(s).</li>
              <li>Comprueba que el <b>Total de pagos</b> sea igual al <b>Total a programar</b>.</li>
              <li>Guarda cambios y vuelve a registrar el pago.</li>
            </ol>
          </div>
        `,
        confirmButtonText:'Abrir editor de financiamiento',
        showCancelButton:true,
        cancelButtonText:'Cancelar'
      });

      if(isConfirmed){
        modalInst.hide();
        const editorEl = document.getElementById('modalEditarPagos');
        if(editorEl){
          const editorInst = bootstrap.Modal.getOrCreateInstance(editorEl);
          setTimeout(()=> editorInst.show(), 200);
        }
      }
    }
  });
});
</script>
