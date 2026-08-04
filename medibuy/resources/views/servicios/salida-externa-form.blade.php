<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Autorización de Salida Premium</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    :root {
        /* Paleta Premium Modern */
        --primary: #2563eb; /* Azul Zafiro */
        --primary-dark: #1e40af;
        --accent: #06b6d4; /* Cian Eléctrico */
        --bg-app: #f1f5f9; /* Slate 100 */
        --bg-gradient: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        --surface: rgba(255, 255, 255, 0.95); /* Para Glassmorphism */
        --text-main: #0f172a; /* Slate 900 */
        --text-muted: #64748b; /* Slate 500 */
        --border-soft: #e2e8f0; /* Slate 200 */
        
        /* Semánticos sutiles */
        --success: #10b981;
        --success-bg: #ecfdf5;
        --danger: #ef4444;
        --danger-light: #fef2f2;

        /* Estilos */
        --radius-md: 12px;
        --radius-lg: 18px;
        --shadow-card: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
        --shadow-premium: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.03);
        --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background: var(--bg-gradient);
        font-family: 'Inter', -apple-system, sans-serif;
        color: var(--text-main);
        -webkit-font-smoothing: antialiased;
        min-height: 100vh;
    }

    .app-container {
        max-width: 720px;
        margin: 0 auto;
        padding: 2rem 1rem 4rem;
    }

    /* Header elegante */
    .app-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    
    .app-header h1 {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.3rem;
    }
    
    .app-header p {
        color: var(--text-muted);
        font-size: 0.95rem;
    }

    /* Resumen Estilo "Pills Premium" con Scroll */
    .summary-scroll {
        display: flex;
        gap: 0.75rem;
        overflow-x: auto;
        padding: 0.5rem 0.2rem 1.2rem;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    
    .summary-scroll::-webkit-scrollbar { display: none; }

    .summary-pill {
        background: var(--surface);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: var(--radius-md);
        padding: 0.8rem 1.1rem;
        min-width: 150px;
        flex-shrink: 0;
        box-shadow: var(--shadow-card);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
    }
    
    .summary-pill:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-premium);
        border-color: rgba(37, 99, 235, 0.2);
    }

    .summary-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--primary);
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .summary-value {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-main);
    }

    /* Secciones del Formulario */
    .form-section {
        background: var(--surface);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        box-shadow: var(--shadow-card);
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .section-title i {
        color: var(--accent);
        font-size: 1.1rem;
    }

    /* Inputs Modernos y Elegantes */
    .form-group { margin-bottom: 1.25rem; }
    .form-group:last-child { margin-bottom: 0; }

    .form-label {
        font-weight: 600;
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.4rem;
        display: block;
    }

    .form-control, .form-select {
        border-radius: var(--radius-md);
        border: 1px solid var(--border-soft);
        padding: 0.75rem 1rem;
        font-size: 16px; /* Evita zoom iOS */
        background-color: #fff;
        color: var(--text-main);
        transition: var(--transition);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1);
        outline: none;
    }
    
    .form-control::placeholder { color: #a1a1aa; }

    /* Botones Estilo Premium Minimalista */
    .btn-premium {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        padding: 0.8rem 1.5rem;
        font-weight: 600;
        font-size: 1rem;
        letter-spacing: -0.01em;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }

    .btn-premium:hover, .btn-premium:active {
        background: linear-gradient(135deg, var(--primary-dark) 0%, #1e3a8a 100%);
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
        color: white;
    }

    .btn-outline-premium {
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
        border-radius: var(--radius-md);
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        font-weight: 600;
        transition: var(--transition);
    }
    
    .btn-outline-premium:hover {
        background: rgba(37, 99, 235, 0.05);
        border-color: var(--primary-dark);
        color: var(--primary-dark);
    }

    /* Tarjetas de Ítems (Inventario) */
    .item-card {
        background: rgba(248, 250, 252, 0.5); /* Slate 50 sutil */
        border: 1px solid var(--border-soft);
        border-radius: var(--radius-md);
        padding: 1.25rem;
        margin-bottom: 1rem;
        position: relative;
        transition: var(--transition);
    }
    
    .item-card:hover {
        border-color: var(--accent);
        background: #fff;
    }

    .item-card-remove {
        position: absolute;
        top: -10px;
        right: -10px;
        background: var(--danger);
        color: white;
        border: none;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: var(--transition);
    }
    
    .item-card-remove:hover {
        transform: scale(1.1) rotate(90deg);
        background: #b91c1c;
    }

    /* Firma Inteligente y Elegante */
    .sign-container {
        position: relative;
        border: 2px solid var(--border-soft);
        border-radius: var(--radius-md);
        background: #fff;
        overflow: hidden;
        transition: var(--transition);
    }
    
    .sign-container:hover { border-color: var(--border-soft); }
    .sign-container.drawing { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1); }

    .sign-canvas {
        width: 100%;
        height: 180px;
        display: block;
        touch-action: none;
        cursor: crosshair;
    }

    .sign-clear {
        position: absolute;
        bottom: 0.75rem;
        right: 0.75rem;
        background: rgba(241, 245, 249, 0.9); /* Slate 100 */
        border: 1px solid var(--border-soft);
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        padding: 0.4rem 0.8rem;
        border-radius: 100px;
        backdrop-filter: blur(2px);
        transition: var(--transition);
    }
    
    .sign-clear:hover {
        background: var(--danger-light);
        color: var(--danger);
        border-color: #fecaca;
    }

    /* Overlay Éxito Pantalla Completa Sutil */
    .success-overlay {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background-color: var(--surface);
        backdrop-filter: blur(15px);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.4s ease;
    }

    .success-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    /* Check Animado Minimalista y Elegante */
    .checkmark {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: block;
        stroke-width: 3;
        stroke: var(--accent);
        stroke-miterlimit: 10;
        fill: none;
        animation: scale .3s ease-in-out .8s both;
    }

    .checkmark__circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 3;
        stroke-miterlimit: 10;
        stroke: var(--accent);
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }

    .checkmark__check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.6s forwards;
    }

    .success-overlay h2 {
        margin-top: 1.5rem;
        font-size: 1.4rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--text-main);
    }

    @keyframes stroke { 100% { stroke-dashoffset: 0; } }
    @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.05, 1.05, 1); } }
  </style>
</head>
<body>

@php
  $hoy = now()->format('Y-m-d');
  $hora = now()->format('H:i');

  $componentesOld = old('componentes_salida');
  if (!is_array($componentesOld) || empty($componentesOld)) {
      $componentesOld = [
          ['nombre' => '', 'cantidad' => '1', 'tipo' => 'pieza'],
      ];
  }
@endphp

<div id="successOverlay" class="success-overlay">
    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
        <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
        <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
    </svg>
    <h2>Salida Autorizada</h2>
</div>

<div class="app-container">

  <div class="app-header">
      <h1>Autorización de Salida</h1>
      <p>Mantenimiento Externo</p>
  </div>

  @if(session('error'))
    <div class="alert alert-danger" style="font-size: 0.9rem; border-radius: var(--radius-md); border: none; background: var(--danger-light); color: var(--danger); box-shadow: var(--shadow-card);">
      <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
    </div>
  @endif

  <div class="summary-scroll">
      <div class="summary-pill">
          <span class="summary-label">Folio</span>
          <span class="summary-value">#{{ $servicio->id }}</span>
      </div>
      <div class="summary-pill">
          <span class="summary-label">Equipo</span>
          <span class="summary-value">{{ $servicio->tipo_equipo ?? '—' }}</span>
      </div>
      <div class="summary-pill">
          <span class="summary-label">Nº Serie</span>
          <span class="summary-value">{{ $servicio->numero_serie ?: '—' }}</span>
      </div>
  </div>

  <form method="POST" action="{{ route('servicio.externo.salida.store', $token) }}" id="salidaForm">
    @csrf

    <div class="form-section">
        <div class="section-title"><i class="bi bi-person-check"></i> Responsable</div>
        
        <div class="form-group pe-lg-5 pe-0">
          <label class="form-label">Nombre completo de quien retira</label>
          <input type="text" name="nombre_salida" class="form-control" value="{{ old('nombre_salida') }}" placeholder="Ej. Juan Pérez" required>
        </div>

        <div class="row g-3 form-group pe-lg-5 pe-0">
          <div class="col-6">
            <label class="form-label">Fecha de salida</label>
            <input type="date" name="fecha_salida" class="form-control" value="{{ old('fecha_salida', $hoy) }}" required>
          </div>
          <div class="col-6">
            <label class="form-label">Hora</label>
            <input type="time" name="hora_salida" class="form-control" value="{{ old('hora_salida', $hora) }}" required>
          </div>
        </div>
    </div>

    <div class="form-section">
        <div class="section-title d-flex justify-content-between align-items-center">
            <span><i class="bi bi-box-seam"></i> Inventario / Accesorios</span>
            <button type="button" class="btn-outline-premium" id="addComponenteBtn">
                <i class="bi bi-plus-lg"></i> Añadir
            </button>
        </div>

        <div id="componentesContainer">
            @foreach($componentesOld as $i => $item)
              <div class="item-card">
                  <button type="button" class="item-card-remove remove-componente" title="Eliminar ítem"><i class="bi bi-x-lg"></i></button>
                  
                  <div class="form-group mb-2Pe-4 pe-0pe-0pe-3 pe-lg-5 pe-0 pe-0">
                    <label class="form-label">Descripción del componente</label>
                    <input type="text" name="componentes_salida[{{ $i }}][nombre]" class="form-control" value="{{ $item['nombre'] ?? '' }}" placeholder="Ej. Cargador, Cable HDMI..." required>
                  </div>
                  
                  <div class="row g-3 pe-lg-5 pe-0 pe-0">
                    <div class="col-4">
                      <label class="form-label">Cant.</label>
                      <input type="number" step="0.01" min="0.01" name="componentes_salida[{{ $i }}][cantidad]" class="form-control" value="{{ $item['cantidad'] ?? '1' }}" required>
                    </div>
                    <div class="col-8">
                      <label class="form-label">Unidad</label>
                      <select name="componentes_salida[{{ $i }}][tipo]" class="form-select" required>
                        @php $tipoActual = $item['tipo'] ?? 'pieza'; @endphp
                        <option value="pieza" {{ $tipoActual === 'pieza' ? 'selected' : '' }}>Pieza</option>
                        <option value="kit" {{ $tipoActual === 'kit' ? 'selected' : '' }}>Kit</option>
                        <option value="juego" {{ $tipoActual === 'juego' ? 'selected' : '' }}>Juego</option>
                        <option value="otro" {{ $tipoActual === 'otro' ? 'selected' : '' }}>Otro</option>
                      </select>
                    </div>
                  </div>
              </div>
            @endforeach
        </div>
    </div>

    <div class="form-section">
        <div class="section-title"><i class="bi bi-pencil-square"></i> Finalizar</div>

        <div class="form-group pe-lg-5 pe-0">
          <label class="form-label">Observaciones adicionales</label>
          <textarea name="observaciones_salida" class="form-control" rows="2" placeholder="Estado estético, detalles técnicos sutiles...">{{ old('observaciones_salida') }}</textarea>
        </div>

        <div class="form-group pe-lg-5 pe-0pe-0pe-0 pe-lg-5 pe-0 pe-0">
          <label class="form-label">Firma de conformidad</label>
          <div class="sign-container" id="signContainer">
            <button type="button" class="sign-clear" id="clearSignature">Limpiar</button>
            <canvas id="signatureCanvas" class="sign-canvas"></canvas>
            <input type="hidden" name="firma_salida" id="firmaSalida" value="{{ old('firma_salida') }}">
          </div>
        </div>
    </div>

    <div class="mt-4 pe-lg-5 pe-0 pe-0 pe-0 pe-0">
        <button type="submit" id="submitBtn" class="btn-premium w-100">
          Autorizar Salida e Imprimir <i class="bi bi-printer"></i>
        </button>
    </div>
  </form>
</div>

<template id="componenteTemplate">
  <div class="item-card" style="opacity: 0; transform: translateY(-5px);">
      <button type="button" class="item-card-remove remove-componente" title="Eliminar ítem"><i class="bi bi-x-lg"></i></button>
      <div class="form-group mb-2 pe-lg-5 pe-0 pe-0">
        <label class="form-label">Descripción del componente</label>
        <input type="text" class="form-control componente-nombre" placeholder="Ej. Cargador, Cable HDMI..." required>
      </div>
      <div class="row g-3 pe-lg-5 pe-0 pe-0">
        <div class="col-4">
          <label class="form-label">Cant.</label>
          <input type="number" step="0.01" min="0.01" class="form-control componente-cantidad" value="1" required>
        </div>
        <div class="col-8">
          <label class="form-label">Unidad</label>
          <select class="form-select componente-tipo" required>
            <option value="pieza">Pieza</option>
            <option value="kit">Kit</option>
            <option value="juego">Juego</option>
            <option value="otro">Otro</option>
          </select>
        </div>
      </div>
  </div>
</template>

<script>
(function () {
  // --- FIRMA INTELIGENTE PREMIUM ---
  const canvas = document.getElementById('signatureCanvas');
  const container = document.getElementById('signContainer');
  const hiddenInput = document.getElementById('firmaSalida');
  const clearBtn = document.getElementById('clearSignature');
  const ctx = canvas.getContext('2d');

  let drawing = false;
  let hasSignature = false;

  function resizeCanvas() {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * ratio;
    canvas.height = rect.height * ratio;
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    ctx.lineWidth = 2.8; // Trazo elegante
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.strokeStyle = '#0f172a'; // Slate 900 (Tinta oscura)

    if (hiddenInput.value) {
      const img = new Image();
      img.onload = () => { ctx.drawImage(img, 0, 0, rect.width, rect.height); hasSignature = true; };
      img.src = hiddenInput.value;
    }
  }

  function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    if (e.touches && e.touches.length) {
      return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
    }
    return { x: e.clientX - rect.left, y: e.clientY - rect.top };
  }

  function preventScroll(e) { e.preventDefault(); }

  function start(e) {
    drawing = true;
    const pos = getPos(e);
    ctx.beginPath(); ctx.moveTo(pos.x, pos.y);
    document.body.style.overflow = 'hidden'; // Bloquea scroll inteligente
    container.classList.add('drawing'); // Efecto visual borde Cyan
  }

  function move(e) {
    if (!drawing) return;
    const pos = getPos(e);
    ctx.lineTo(pos.x, pos.y); ctx.stroke();
    hasSignature = true;
  }

  function end() {
    if (!drawing) return;
    drawing = false;
    hiddenInput.value = canvas.toDataURL('image/png');
    document.body.style.overflow = ''; // Libera scroll
    container.classList.remove('drawing');
  }

  clearBtn.addEventListener('click', () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height); 
    hiddenInput.value = ''; hasSignature = false;
    container.classList.remove('drawing');
  });

  // Eventos Táctiles (passive: false para bloquear scroll)
  canvas.addEventListener('touchstart', (e) => { preventScroll(e); start(e); }, { passive: false });
  canvas.addEventListener('touchmove', (e) => { preventScroll(e); move(e); }, { passive: false });
  canvas.addEventListener('touchend', (e) => { preventScroll(e); end(e); }, { passive: false });

  // Eventos Mouse
  canvas.addEventListener('mousedown', start);
  canvas.addEventListener('mousemove', move);
  canvas.addEventListener('mouseup', end);
  canvas.addEventListener('mouseleave', end);

  window.addEventListener('resize', resizeCanvas); 
  resizeCanvas();

  // --- ENVÍO Y ANIMACIÓN UX PREMIUM ---
  const form = document.getElementById('salidaForm');
  const submitBtn = document.getElementById('submitBtn');
  const successOverlay = document.getElementById('successOverlay');

  form.addEventListener('submit', function (e) {
    if (!hasSignature && !hiddenInput.value) {
      e.preventDefault();
      container.style.borderColor = 'var(--danger)';
      setTimeout(() => container.style.borderColor = 'var(--border-soft)', 2000);
      return;
    }

    if (!hiddenInput.value) hiddenInput.value = canvas.toDataURL('image/png');

    e.preventDefault();
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
    submitBtn.style.opacity = '0.8';
    submitBtn.disabled = true;

    // Simulación de carga sutil antes del éxito
    setTimeout(() => {
        successOverlay.classList.add('active');
        // Espera de animación de éxito y envío real
        setTimeout(() => HTMLFormElement.prototype.submit.call(form), 1800);
    }, 500);
  });

  // --- LÓGICA DE ÍTEMS DINÁMICOS ---
  const componentesContainer = document.getElementById('componentesContainer');
  const addBtn = document.getElementById('addComponenteBtn');
  const template = document.getElementById('componenteTemplate');

  function refreshIndexes() {
    componentesContainer.querySelectorAll('.item-card').forEach((row, index) => {
      const nombre = row.querySelector('.componente-nombre') || row.querySelector('input[name*="[nombre]"]');
      const cantidad = row.querySelector('.componente-cantidad') || row.querySelector('input[name*="[cantidad]"]');
      const tipo = row.querySelector('.componente-tipo') || row.querySelector('select');

      if (nombre) nombre.name = `componentes_salida[${index}][nombre]`;
      if (cantidad) cantidad.name = `componentes_salida[${index}][cantidad]`;
      if (tipo) tipo.name = `componentes_salida[${index}][tipo]`;
    });
  }

  addBtn.addEventListener('click', () => {
    const clone = template.content.firstElementChild.cloneNode(true);
    componentesContainer.appendChild(clone);
    refreshIndexes();
    requestAnimationFrame(() => {
      clone.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
      clone.style.opacity = '1';
      clone.style.transform = 'translateY(0)';
    });
  });

  componentesContainer.addEventListener('click', (e) => {
    const btn = e.target.closest('.remove-componente');
    if (!btn) return;
    if (componentesContainer.querySelectorAll('.item-card').length === 1) {
      alert('Se requiere al menos un ítem en el inventario.'); return;
    }
    const row = btn.closest('.item-card');
    row.style.opacity = '0';
    row.style.transform = 'scale(0.95)';
    setTimeout(() => { row.remove(); refreshIndexes(); }, 250);
  });

  refreshIndexes();
})();
</script>
</body>
</html>