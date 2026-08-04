{{-- resources/views/transactions/qr-ack.blade.php --}}
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Aceptar entrega</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- CSRF para el POST --}}
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root{
      --bg:#f5f7fb; --panel:#fff; --text:#0f172a; --muted:#667085; --border:#e7eaf0;
      --pblue:#cfe6ff; --pblue-strong:#5aa9ff; --pgreen:#c9f7df; --pgreen-strong:#34d399;
    }
    body{ background:var(--bg); color:var(--text); }
    .card{ border:1px solid var(--border); border-radius:16px; box-shadow:0 10px 40px rgba(2,6,23,.08); }
    .sig-wrap{ border:1px dashed #d5e4f8; border-radius:14px; padding:12px; background:#fff; }
    .sig-canvas{ width:100%; height:200px; background:#fff; border-radius:10px; display:block; touch-action:none; }
    .badge-soft{ background:#eaf4ff; color:#1f7ae6; border:1px solid #d7e8ff; }

    .btn-pastel-blue{
      color:#0b2a4a; background:var(--pblue); border:1px solid #b6d8ff; border-radius:14px; font-weight:800;
      box-shadow:0 10px 22px rgba(90,169,255,.25);
    }
    .btn-pastel-blue:hover{ filter:brightness(1.03); box-shadow:0 12px 26px rgba(90,169,255,.32) }

    /* Vista de éxito */
    .success-hero{ background:#f1fff7; border:1px solid #c8f3de; border-radius:16px; }
    .check-wrap{ width:160px; height:160px; margin:8px auto 14px; position:relative }
    .check-circle{ fill:none; stroke:#34d399; stroke-width:10; stroke-linecap:round; opacity:.95;
      stroke-dasharray:520; stroke-dashoffset:520; animation:drawCircle 1s ease forwards }
    @keyframes drawCircle{ to{ stroke-dashoffset:0 } }
    .check-mark{ fill:none; stroke:#34d399; stroke-width:10; stroke-linecap:round; stroke-linejoin:round;
      stroke-dasharray:120; stroke-dashoffset:120; animation:drawCheck .7s .6s ease forwards }
    @keyframes drawCheck{ to{ stroke-dashoffset:0 } }
    .scale-in{ animation:scaleIn .35s ease both }
    @keyframes scaleIn{ from{ transform:scale(.96); opacity:.0 } to{ transform:none; opacity:1 } }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="mx-auto" style="max-width:720px">
    {{-- Encabezado --}}
    <div class="text-center mb-3">
      <h1 class="h4 mb-1">Confirmar y firmar recepción</h1>
      <p class="text-muted mb-0">Escaneaste un QR para aceptar una entrega de efectivo.</p>
    </div>

    {{-- === Vista principal (formulario) === --}}
    <div id="view-form" class="card scale-in">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
              <span class="text-muted">Monto</span>
              <span class="fs-4 fw-bold">${{ number_format((float)$trx->amount,2) }}</span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
              <span class="text-muted">Fecha / hora</span>
              <span>{{ $trx->created_at->format('Y-m-d H:i') }}</span>
            </div>
            <div class="mt-2">
              <span class="badge badge-soft">Token válido hasta {{ \Carbon\Carbon::parse($trx->qr_expires_at)->format('H:i') }}</span>
            </div>
          </div>

          <div class="col-12">
            <label class="form-label">¿Para qué es el dinero? (concepto)</label>
            <input type="text" id="purpose" class="form-control" maxlength="255" placeholder="préstamo / gasto / compra…" required>
          </div>

          <div class="col-12">
            <label class="form-label">Tu firma</label>
            <div class="sig-wrap">
              <canvas id="sig" class="sig-canvas"></canvas>
              <div class="text-end mt-2">
                <button class="btn btn-sm btn-outline-secondary" id="clearSig" type="button">Limpiar</button>
              </div>
            </div>
            <div class="form-text">Firma con tu dedo. Debe ser claramente visible.</div>
          </div>

          <div class="col-12 text-end">
            <button id="submitBtn" class="btn btn-pastel-blue">
              <span class="btn-text"><i class="bi bi-pencil-square me-1"></i> Aceptar y firmar</span>
              <span class="btn-spinner d-none spinner-border spinner-border-sm"></span>
            </button>
          </div>
        </div>
      </div>
    </div>

    {{-- === Vista de éxito (se muestra al guardar) === --}}
    <div id="view-success" class="card success-hero d-none">
      <div class="card-body text-center">
        <div class="check-wrap">
          <svg viewBox="0 0 220 220" width="160" height="160">
            <circle class="check-circle" cx="110" cy="110" r="80"></circle>
            <path class="check-mark" d="M75 115 L105 145 L155 90"></path>
          </svg>
        </div>
        <h3 class="mb-1">¡Entrega aceptada!</h3>
        <p class="text-muted mb-3">Tu motivo y firma fueron registrados correctamente.</p>
        <a href="javascript:window.close();" class="btn btn-pastel-blue">Cerrar</a>
      </div>
    </div>

    <p class="text-center text-muted mt-3 mb-0 small">
      Si cierras esta página, el QR podría expirar y tendrás que solicitar uno nuevo.
    </p>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
<script>
(function(){
  // --- SignaturePad con ajuste correcto (iOS/Android)
  const canvas = document.getElementById('sig');
  const pad = new SignaturePad(canvas, {backgroundColor:'#fff', penColor:'#0f172a'});

  function fitCanvas(){
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    const rect = canvas.getBoundingClientRect();
    const w = rect.width > 0 ? rect.width : (canvas.parentElement?.clientWidth || 600);
    const h = rect.height > 0 ? rect.height : 200;
    canvas.width = w * ratio; canvas.height = h * ratio;
    canvas.getContext('2d').scale(ratio, ratio);
    pad.clear();
  }
  window.addEventListener('resize', fitCanvas);
  fitCanvas();

  document.getElementById('clearSig').addEventListener('click', ()=>pad.clear());

  // --- Helpers UI
  function setLoading(on){
    const btn = document.getElementById('submitBtn');
    btn.disabled = on;
    btn.querySelector('.btn-text').classList.toggle('d-none', on);
    btn.querySelector('.btn-spinner').classList.toggle('d-none', !on);
  }
  function showSuccess(){
    // Cambia de vista
    document.getElementById('view-form').classList.add('d-none');
    const sView = document.getElementById('view-success');
    sView.classList.remove('d-none');
    sView.classList.add('scale-in');

    // Confetti 🎉
    setTimeout(()=>{
      confetti({particleCount:100, spread:70, origin:{y:0.6}});
      setTimeout(()=>confetti({particleCount:80, spread:60, origin:{y:0.6}}), 250);
    }, 200);
  }
  function showError(msg){
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger mt-3';
    alert.innerText = msg || 'Ocurrió un error. Intenta de nuevo.';
    document.querySelector('.card-body').prepend(alert);
    setTimeout(()=>alert.remove(), 4000);
  }

  // --- Envío AJAX (sin ver JSON)
  const action = "{{ route('transactions.qr.ack', ['token'=> request()->route('token')], false) }}";
  const csrf   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  document.getElementById('submitBtn').addEventListener('click', function(){
    const p = document.getElementById('purpose').value.trim();
    if (!p) return showError('Escribe el concepto.');
    if (pad.isEmpty()) return showError('La firma es obligatoria.');

    const fd = new FormData();
    fd.append('purpose', p);
    fd.append('signature', pad.toDataURL('image/png'));

    setLoading(true);
    fetch(action, {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': csrf},
      body: fd
    }).then(async res=>{
      setLoading(false);
      if(res.ok){ showSuccess(); }
      else if(res.status===410){ showError('El QR ha expirado. Solicita uno nuevo.'); }
      else{
        let j = null; try{ j = await res.json(); }catch(_){}
        showError(j?.message || 'No se pudo guardar. Revisa tu conexión.');
      }
    }).catch(()=>{
      setLoading(false);
      showError('No se pudo comunicar con el servidor.');
    });
  });
})();
</script>
</body>
</html>
