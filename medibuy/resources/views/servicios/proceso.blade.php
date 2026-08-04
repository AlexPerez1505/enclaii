{{-- resources/views/mantenimiento/proceso.blade.php --}}
@extends('layouts.app')

@section('title', 'Proceso de servicio')
@section('titulo', 'Proceso de servicio')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
  --bg: #f9fafb; 
  --card: #ffffff; 
  --ink: #333333; 
  --muted: #888888; 
  --line: #ebebeb; 
  
  --blue: #007aff; 
  --blue-soft: #e6f0ff; 
  
  --success: #15803d; 
  --success-soft: #e6ffe6; 
  
  --danger: #ff4a4a; 
  --danger-soft: #ffebeb;

  --warn: #d97706;
  --warn-soft: #fef3c7;

  --gray: #4b5563;
  --gray-soft: #f3f4f6;

  /* Curva de animación Apple */
  --ease: cubic-bezier(0.2, 0.8, 0.2, 1);
}

*, *::before, *::after { box-sizing: border-box; }

body {
  background: var(--bg);
  color: var(--ink);
  font-family: 'Quicksand', system-ui, -apple-system, sans-serif;
  margin: 0;
  padding: 0;
  -webkit-font-smoothing: antialiased;
}

h1, h2, h3, h4, h5, h6 { color: #111111; margin: 0; font-weight: 700; }
a { text-decoration: none; }

.page-wrap { max-width: 1240px; margin: 0 auto; padding: 32px 20px 60px; overflow-x: hidden; }

/* --- Animaciones --- */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-entry { animation: fadeInUp 0.8s var(--ease) both; }
.delay-1 { animation-delay: 0.15s; }
.delay-2 { animation-delay: 0.3s; }

/* --- Tarjetas --- */
.card-ui {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.02);
  padding: 24px;
  transition: transform 0.3s var(--ease), box-shadow 0.3s var(--ease);
}
.card-ui:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 24px rgba(0,0,0,0.05);
}

/* --- Botones --- */
.btn-ui {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  border-radius: 8px;
  font-family: 'Quicksand', sans-serif;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  border: none;
  transition: all 0.2s var(--ease);
  line-height: 1;
}
.btn-ui:active { transform: scale(0.96); }

.btn-primary { background: var(--blue); color: #fff; }
.btn-primary:hover { background: #006ce4; box-shadow: 0 6px 16px rgba(0, 122, 255, 0.25); transform: translateY(-1px); }

.btn-ghost { background: transparent; color: var(--gray); border: 1px solid transparent; }
.btn-ghost:hover { background: var(--bg); color: #111; }

.btn-outline { background: var(--card); border: 1px solid var(--line); color: var(--ink); }
.btn-outline:hover { background: var(--bg); border-color: #d1d5db; }

/* --- Badges --- */
.badge-ui {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 14px; border-radius: 8px;
  font-size: 12px; font-weight: 700;
}
.badge-success { background: var(--success-soft); color: var(--success); }
.badge-danger { background: var(--danger-soft); color: var(--danger); }
.badge-warning { background: var(--warn-soft); color: var(--warn); }
.badge-info { background: var(--blue-soft); color: var(--blue); }
.badge-gray { background: var(--gray-soft); color: var(--gray); }

/* --- HERO --- */
.hero {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 24px;
  flex-wrap: wrap;
  padding: 24px 32px;
  margin-bottom: 24px;
}
.hero-main { flex: 1; }
.hero h1 { font-size: 24px; letter-spacing: -0.02em; margin-bottom: 6px; }
.hero-sub { color: var(--muted); font-size: 14px; font-weight: 500; margin-bottom: 16px; }
.hero-badges { display: flex; gap: 10px; flex-wrap: wrap; }
.hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }

/* --- Avisos --- */
.notice-ui {
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  border: 1px solid transparent;
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 24px;
}
.notice-warn { background: var(--warn-soft); border-color: #fde68a; color: var(--warn); }
.notice-ok { background: var(--success-soft); border-color: #bbf7d0; color: var(--success); }
.notice-info { background: var(--blue-soft); border-color: #bfdbfe; color: var(--blue); }

/* --- Formularios --- */
.form-label { display: block; font-weight: 700; color: #111; font-size: 13px; margin-bottom: 6px; }
.form-control, .form-select {
  width: 100%;
  border-radius: 8px;
  border: 1px solid var(--line);
  padding: 12px 14px;
  font-size: 14px;
  font-family: 'Quicksand', sans-serif;
  font-weight: 500;
  background-color: var(--card);
  color: var(--ink);
  transition: all 0.2s var(--ease);
  outline: none;
}
.form-control:focus, .form-select:focus {
  border-color: var(--blue);
  box-shadow: 0 0 0 3px var(--blue-soft);
}
input[type="file"].form-control { padding: 9px 12px; font-size: 13px; }

.file-box {
  border: 1px dashed var(--line);
  border-radius: 12px;
  padding: 20px;
  background: var(--bg);
  margin-bottom: 24px;
}
.file-box-title { font-weight: 700; color: #111; font-size: 14px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.grid-files { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 576px) { .grid-files { grid-template-columns: 1fr; } }

/* --- Grid Principal --- */
.main-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
}
@media (max-width: 992px) { .main-grid { grid-template-columns: 1fr; } }
.section-title { font-weight: 700; font-size: 16px; color: #111; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }

/* --- Pasos (Ruta) --- */
.steps-list { display: flex; flex-direction: column; gap: 12px; }
.step-item {
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 16px;
  display: flex;
  align-items: center;
  gap: 16px;
  background: var(--card);
  transition: all 0.3s var(--ease);
}
.step-item.done { border-color: transparent; background: var(--success-soft); }
.step-item.current { border-color: var(--blue); background: #ffffff; box-shadow: 0 4px 16px rgba(0, 122, 255, 0.08); transform: translateX(4px); }
.step-item.pending { opacity: 0.6; background: var(--bg); }

.step-ico {
  width: 44px; height: 44px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 20px;
  background: var(--bg);
  color: var(--muted);
}
.step-item.done .step-ico { background: #fff; color: var(--success); }
.step-item.current .step-ico { background: var(--blue-soft); color: var(--blue); }

.step-info .step-title { font-weight: 700; color: #111; font-size: 14px; margin-bottom: 2px; }
.step-info .step-status { font-size: 12px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.step-item.done .step-status { color: var(--success); }
.step-item.current .step-status { color: var(--blue); }

/* --- Key-Values (Ficha Técnica) --- */
.kv-list { display: flex; flex-direction: column; gap: 16px; }
.kv-item { display: flex; justify-content: space-between; border-bottom: 1px solid var(--line); padding-bottom: 12px; align-items: center;}
.kv-item:last-child { border-bottom: none; padding-bottom: 0; }
.kv-label { color: var(--muted); font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
.kv-val { color: #111; font-size: 14px; font-weight: 600; text-align: right;}

/* --- Historial / Tabla --- */
.history-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--line); }
.history-count { font-size: 13px; font-weight: 700; color: var(--muted); background: var(--bg); padding: 6px 12px; border-radius: 8px; }

.table-wrap { overflow-x: auto; border-radius: 12px; border: 1px solid var(--line); }
.table-pro { width: 100%; border-collapse: collapse; min-width: 800px; }
.table-pro th { padding: 16px; font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--muted); background: var(--bg); border-bottom: 1px solid var(--line); text-align: left; }
.table-pro td { padding: 16px; border-bottom: 1px solid var(--line); vertical-align: middle; font-size: 13px; color: var(--ink); }
.table-pro tr:last-child td { border-bottom: none; }
.table-pro tr:hover td { background: var(--bg); }

.row-index { width: 28px; height: 28px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; background: var(--line); font-weight: 700; color: var(--muted); font-size: 12px; }
.mov-title { font-weight: 700; color: #111; display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
.mov-desc { color: var(--muted); font-size: 13px; line-height: 1.5; white-space: pre-wrap; }
.text-main { font-weight: 700; color: #111; }
.text-sub { color: var(--muted); font-size: 12px; margin-top: 2px; }

/* --- Detalles Foráneos (Expedientes) --- */
.expediente-wrap { margin-top: 32px; border-top: 2px solid var(--line); padding-top: 24px; }
.exp-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
.exp-title { font-size: 16px; font-weight: 700; color: #111; display: flex; align-items: center; gap: 8px; }

.detail-box { border: 1px solid var(--line); border-radius: 12px; padding: 24px; margin-bottom: 20px; background: #fff; }
.detail-top { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px dashed var(--line); padding-bottom: 16px; margin-bottom: 20px; flex-wrap: wrap; gap: 16px; }

.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
@media(max-width: 768px) { .info-grid { grid-template-columns: 1fr; } }
.info-panel { background: var(--bg); border-radius: 12px; padding: 20px; }
.info-panel-title { font-weight: 700; font-size: 13px; color: #111; text-transform: uppercase; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.info-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 13px; align-items: flex-start; gap: 16px;}
.info-row:last-child { margin-bottom: 0; }
.info-key { font-weight: 700; color: var(--muted); }
.info-val { font-weight: 600; color: #111; text-align: right; }

.obs-box { background: #fff; border: 1px solid var(--line); border-radius: 8px; padding: 12px; color: var(--muted); font-size: 13px; line-height: 1.5; margin-top: 8px; }

.signature-box { border: 1px dashed var(--line); border-radius: 12px; background: #fff; padding: 16px; display: flex; align-items: center; justify-content: center; min-height: 140px; }
.signature-box img { max-width: 100%; max-height: 120px; }
.component-chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; background: #fff; border: 1px solid var(--line); font-size: 12px; font-weight: 700; color: var(--ink); margin: 0 4px 8px 0; }

.empty-state { text-align: center; color: var(--muted); font-size: 14px; font-weight: 600; padding: 32px 0; }
</style>

@php
  $tipoClase = ($servicio->mantenimiento_tipo ?? 'interno') === 'externo' ? 'badge-info' : 'badge-gray';
  $tipoTexto = ($servicio->mantenimiento_tipo ?? 'interno') === 'externo' ? 'Mantenimiento externo' : 'Mantenimiento interno';

  $estadoTexto = match($servicio->estado_proceso){
    'pendiente_entrega'         => 'Pendiente de salida a entrega',
    'pendiente_salida_foraneo'  => 'Pendiente de salida foránea',
    'pendiente_regreso_foraneo' => 'Pendiente de regreso foráneo',
    'pendiente_salida_cliente'  => 'Pendiente de salida para cliente',
    'requiere_os'               => 'Pendiente de validación OS',
    'completado'                => 'Proceso completado',
    default => ucfirst(str_replace('_',' ', $servicio->estado_proceso ?? 'pendiente')),
  };

  $esQrSalidaExterna = ($servicio->mantenimiento_tipo ?? 'interno') === 'externo'
    && (($proceso['siguiente']['key'] ?? null) === 'salida_foraneo');
@endphp

<div class="page-wrap">
  
  {{-- HERO --}}
  <div class="card-ui hero animate-entry">
    <div class="hero-main">
      <h1>Gestión de Proceso de Servicio</h1>
      <div class="hero-sub">Panel de control unificado. Supervisa el flujo obligatorio del mantenimiento.</div>

      <div class="hero-badges">
        <span class="badge-ui {{ $tipoClase }}">
          <i class="bi {{ ($servicio->mantenimiento_tipo ?? 'interno') === 'externo' ? 'bi-box-arrow-up-right' : 'bi-building' }}"></i>
          {{ $tipoTexto }}
        </span>

        <span class="badge-ui badge-warning">
          <i class="bi bi-diagram-3"></i>
          {{ $estadoTexto }}
        </span>
      </div>
    </div>

    <div class="hero-actions">
      <a href="{{ url('/inventario/servicio') }}" class="btn-ui btn-ghost">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
      <a href="{{ route('servicios.show', $servicio->id) }}" class="btn-ui btn-outline">
        <i class="bi bi-file-earmark-text"></i> Detalles del servicio
      </a>
    </div>
  </div>

  {{-- ALERTAS --}}
  <div class="animate-entry delay-1">
    @if(session('ok'))
      <div class="notice-ui notice-ok">
        <i class="bi bi-check-circle-fill fs-5"></i> {{ session('ok') }}
      </div>
    @endif

    @if(session('error'))
      <div class="notice-ui notice-warn">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i> {{ session('error') }}
      </div>
    @endif

    @if($errors->any())
      <div class="notice-ui notice-warn">
        <i class="bi bi-exclamation-circle-fill fs-5"></i> {{ implode(' | ', $errors->all()) }}
      </div>
    @endif
  </div>

  <div class="main-grid animate-entry delay-2">
    
    {{-- COLUMNA IZQUIERDA --}}
    <div style="display:flex; flex-direction:column; gap:24px;">
      
      {{-- Acción Requerida --}}
      <div class="card-ui">
        <div class="section-title"><i class="bi bi-lightning-charge"></i> Acción Requerida</div>

        @if($proceso['completado'])
          <div class="notice-ui notice-ok mb-0">
            <i class="bi bi-check-all fs-4"></i>
            Este servicio ha completado satisfactoriamente todo su ciclo de proceso.
          </div>
        @elseif(($proceso['siguiente']['key'] ?? null) === 'validar_os')
          <div class="notice-ui notice-warn mb-4">
            <i class="bi bi-shield-exclamation fs-5"></i>
            Se requiere la validación de la Orden de Servicio como paso obligatorio para continuar.
          </div>
          <a href="{{ route('servicio.os.form', $servicio->id) }}" class="btn-ui btn-primary w-100">
            <i class="bi bi-patch-check"></i> Validar Orden de Servicio
          </a>
        @elseif($esQrSalidaExterna)
          <div style="background:var(--bg); border:1px solid var(--line); border-radius:12px; padding:20px;">
            <div class="notice-ui notice-info mb-4" style="margin-bottom:20px; border:none;">
              <i class="bi bi-shield-lock-fill fs-4"></i>
              <div>Registro protegido. Requiere captura vía <strong>formulario QR</strong> para asegurar identidad y firmas.</div>
            </div>

            <div style="display:flex; gap:12px; margin-bottom:20px;">
              <a href="{{ route('servicio.externo.salida.qr', $servicio->id) }}" class="btn-ui btn-primary" style="flex:1;">
                <i class="bi bi-qr-code-scan"></i> Generar QR
              </a>
              <a href="{{ route('servicio.externo.salida.qr', $servicio->id) }}" target="_blank" class="btn-ui btn-outline" style="flex:1;">
                <i class="bi bi-box-arrow-up-right"></i> Abrir Enlace
              </a>
            </div>

            <ul style="margin:0; padding-left:20px; color:var(--muted); font-size:13px; line-height:1.6; font-weight:500;">
              <li>Aplica exclusivamente a mantenimientos externos.</li>
              <li>Genera un acceso controlado mediante token temporal.</li>
              <li>Sincroniza automáticamente el movimiento de salida foránea.</li>
            </ul>
          </div>
        @else
          <div style="font-size:14px; font-weight:600; color:var(--muted); margin-bottom:16px; border-bottom:1px solid var(--line); padding-bottom:16px;">
            Paso en curso: <strong style="color:#111;">{{ $proceso['siguiente']['label'] ?? 'Continuar proceso' }}</strong>
          </div>

          <form action="{{ route('servicio.proceso.avanzar', $servicio->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="paso" value="{{ $proceso['siguiente']['key'] ?? '' }}">

            <div style="margin-bottom: 20px;">
              <label class="form-label">Notas operativas (Opcional)</label>
              <textarea name="descripcion" class="form-control" rows="3" placeholder="Registre cualquier observación relevante...">{{ old('descripcion') }}</textarea>
            </div>

            <div class="file-box">
              <div class="file-box-title"><i class="bi bi-paperclip"></i> Evidencias adjuntas</div>
              <div class="grid-files">
                <div>
                  <label class="form-label">Fotografía 1</label>
                  <input type="file" name="evidencia1" class="form-control" accept="image/*">
                </div>
                <div>
                  <label class="form-label">Fotografía 2</label>
                  <input type="file" name="evidencia2" class="form-control" accept="image/*">
                </div>
                <div>
                  <label class="form-label">Fotografía 3</label>
                  <input type="file" name="evidencia3" class="form-control" accept="image/*">
                </div>
                <div>
                  <label class="form-label">Video corto</label>
                  <input type="file" name="video" class="form-control" accept="video/*">
                </div>
              </div>
            </div>

            <button type="submit" class="btn-ui btn-primary w-100">
              <i class="bi {{ $proceso['siguiente']['icon'] ?? 'bi-check2-circle' }}"></i>
              {{ $proceso['siguiente']['button'] ?? 'Confirmar y avanzar' }}
            </button>
          </form>
        @endif
      </div>

      {{-- Ruta de Trabajo --}}
      <div class="card-ui">
        <div class="section-title"><i class="bi bi-signpost-split"></i> Ruta de Trabajo</div>

        <div class="steps-list">
          @foreach($proceso['pasos'] as $index => $paso)
            @php
              $icono = match($paso['key']) {
                'validar_os' => 'bi-patch-check',
                'entrega' => 'bi-box-arrow-up-right',
                'salida_foraneo' => 'bi-truck',
                'regreso_foraneo' => 'bi-arrow-repeat',
                'salida_cliente' => 'bi-person-badge',
                default => 'bi-check2-circle',
              };

              $statusText = match($paso['status']) {
                'done' => 'Completado',
                'current' => 'En Proceso',
                default => 'Pendiente',
              };
            @endphp

            <div class="step-item {{ $paso['status'] }}">
              <div class="step-ico"><i class="bi {{ $icono }}"></i></div>
              <div class="step-info">
                <div class="step-title">Paso {{ $index + 1 }}: {{ $paso['label'] }}</div>
                <div class="step-status">{{ $statusText }}</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- COLUMNA DERECHA --}}
    <div style="display:flex; flex-direction:column; gap:24px;">
      
      {{-- Ficha Técnica --}}
      <div class="card-ui">
        <div class="section-title"><i class="bi bi-info-circle"></i> Ficha Técnica del Servicio</div>

        <div class="kv-list">
          <div class="kv-item">
            <span class="kv-label">Identificación</span>
            <span class="kv-val">
              {{ $servicio->tipo_equipo ?? 'No especificado' }}
              @if($servicio->subtipo_equipo)
                <span style="color:var(--muted); font-weight:500;">| {{ $servicio->subtipo_equipo }}</span>
              @endif
            </span>
          </div>
          <div class="kv-item">
            <span class="kv-label">No. de Serie</span>
            <span class="kv-val">{{ $servicio->numero_serie ?: 'N/A' }}</span>
          </div>
          <div class="kv-item">
            <span class="kv-label">Marca / Modelo</span>
            <span class="kv-val">{{ trim(($servicio->marca ?? '').' '.($servicio->modelo ?? '')) ?: 'N/A' }}</span>
          </div>
          <div class="kv-item">
            <span class="kv-label">Médico / Titular</span>
            <span class="kv-val">{{ $servicio->nombre_doctor ?: 'No asignado' }}</span>
          </div>
          <div class="kv-item">
            <span class="kv-label">Responsable</span>
            <span class="kv-val">{{ $servicio->user_name ?: 'Sistema' }}</span>
          </div>
          <div class="kv-item" style="align-items:flex-start;">
            <span class="kv-label">Validación OS</span>
            <span class="kv-val" style="text-align:right;">
              @if($servicio->orden_id)
                <span style="color:var(--success);"><i class="bi bi-check-circle-fill"></i> #{{ $servicio->orden_id }}</span><br>
                <span style="color:var(--muted); font-size:12px; font-weight:500;">Autorizada: {{ optional($servicio->orden_validada_at)->format('d/m/Y H:i') }}</span>
              @else
                <span style="color:var(--warn);"><i class="bi bi-hourglass-split"></i> Pendiente</span>
              @endif
            </span>
          </div>
        </div>
      </div>

      {{-- Historial --}}
      <div class="card-ui">
        @php
          $movimientos = $servicio->movimientos ?? collect();
          $salidasForaneas = [];
        @endphp

        <div class="history-head">
          <div class="section-title" style="margin:0;"><i class="bi bi-clock-history"></i> Auditoría de Movimientos</div>
          <div class="history-count">{{ $movimientos->count() }} {{ $movimientos->count() === 1 ? 'Evento' : 'Eventos' }}</div>
        </div>

        @if($movimientos->count())
          <div class="table-wrap">
            <table class="table-pro">
              <thead>
                <tr>
                  <th style="width:50px; text-align:center;">ID</th>
                  <th>Evento</th>
                  <th>Fecha/Hora</th>
                  <th>Operador</th>
                  <th>Origen</th>
                  <th>Detalles</th>
                </tr>
              </thead>
              <tbody>
                @foreach($movimientos as $index => $mov)
                  @php
                    $check = is_array($mov->checklist)
                      ? $mov->checklist
                      : (json_decode($mov->checklist ?? '', true) ?: []);

                    $esSalidaForaneo = $mov->tipo_movimiento === 'salida_foraneo';
                    $esQr = ($check['origen'] ?? null) === 'qr_salida_externa';

                    $movTitulo = ucfirst(str_replace('_', ' ', $mov->tipo_movimiento));
                    $responsable = $check['nombre_salida'] ?? $check['responsable'] ?? $check['nombre'] ?? $servicio->user_name ?? 'Sistema';

                    $canalTexto = $esQr ? 'Token QR' : 'Consola';
                    $canalBadge = $esQr ? 'badge-info' : 'badge-gray';
                    $movBadge = $esSalidaForaneo ? 'badge-warning' : 'badge-success';

                    if ($esSalidaForaneo && $esQr) {
                      $salidasForaneas[] = [
                        'mov' => $mov,
                        'check' => $check,
                        'responsable' => $responsable,
                      ];
                    }
                  @endphp

                  <tr>
                    <td style="text-align:center;">
                      <span class="row-index">{{ $index + 1 }}</span>
                    </td>
                    <td>
                      <div class="mov-title">
                        <span class="badge-ui {{ $movBadge }}" style="padding:4px 8px; font-size:11px;">
                          <i class="bi {{ $esSalidaForaneo ? 'bi-truck' : 'bi-check2-circle' }}"></i> {{ $movTitulo }}
                        </span>
                      </div>
                    </td>
                    <td>
                      <div class="text-main">{{ optional($mov->created_at)->format('d/m/Y') ?: '—' }}</div>
                      <div class="text-sub">{{ optional($mov->created_at)->format('H:i') ?: '—' }} hrs</div>
                    </td>
                    <td>
                      <div class="text-main">{{ $responsable }}</div>
                      <div class="text-sub">{{ $esSalidaForaneo ? 'Autorizó traslado' : 'Operación interna' }}</div>
                    </td>
                    <td>
                      <span class="badge-ui {{ $canalBadge }}" style="padding:4px 8px; font-size:11px;">
                        <i class="bi {{ $esQr ? 'bi-qr-code-scan' : 'bi-keyboard' }}"></i> {{ $canalTexto }}
                      </span>
                    </td>
                    <td>
                      <div class="mov-desc">{{ $mov->descripcion ?: 'Sin notas adicionales.' }}</div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- Expedientes Foráneos --}}
          @if(count($salidasForaneas))
            <div class="expediente-wrap">
              <div class="exp-header">
                <div class="exp-title"><i class="bi bi-folder-check"></i> Expedientes de Traslado Foráneo</div>
                <div class="history-count">{{ count($salidasForaneas) }} Doc(s)</div>
              </div>

              @foreach($salidasForaneas as $i => $item)
                @php
                  $mov = $item['mov'];
                  $check = $item['check'];
                  $responsable = $item['responsable'];

                  $firmaRaw = $check['firma'] ?? $check['firma_salida'] ?? $check['firma_digital'] ?? $check['signature'] ?? $mov->firma_digital ?? null;
                  $firmaSrc = null;

                  if ($firmaRaw) {
                    if (\Illuminate\Support\Str::startsWith($firmaRaw, ['data:image', 'http://', 'https://'])) {
                      $firmaSrc = $firmaRaw;
                    } elseif (\Illuminate\Support\Str::startsWith($firmaRaw, ['/storage/'])) {
                      $firmaSrc = asset(ltrim($firmaRaw, '/'));
                    } elseif (\Illuminate\Support\Str::startsWith($firmaRaw, ['storage/'])) {
                      $firmaSrc = asset($firmaRaw);
                    } else {
                      $firmaSrc = \Illuminate\Support\Facades\Storage::url($firmaRaw);
                    }
                  }

                  $componentesRaw = $check['componentes_detalle'] ?? $check['componentes'] ?? $check['componentes_salida'] ?? [];
                  $componentesArray = is_array($componentesRaw) ? $componentesRaw : null;

                  $observaciones = $check['observaciones_salida'] ?? $check['observaciones'] ?? $mov->descripcion ?? null;

                  $fechaSalida = $check['fecha_salida'] ?? optional($mov->created_at)->format('d/m/Y');
                  $horaSalida = $check['hora_salida'] ?? optional($mov->created_at)->format('H:i');
                  $fechaRegreso = $check['fecha_regreso_estimada'] ?? null;
                  $horaRegreso = $check['hora_regreso_estimada'] ?? null;

                  $folioSalida = $check['folio'] ?? $check['folio_salida'] ?? ('MOV-'.$mov->id);
                  $empresaDestino = $check['empresa_destino'] ?? $check['destino'] ?? $check['proveedor_externo'] ?? 'Mantenimiento externo';
                @endphp

                <div class="detail-box">
                  <div class="detail-top">
                    <div>
                      <div class="mov-title mb-2">
                        <span class="badge-ui badge-warning"><i class="bi bi-truck"></i> Manifiesto Salida</span>
                        <span class="badge-ui badge-info"><i class="bi bi-shield-lock-fill"></i> QR</span>
                      </div>
                      <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:8px;">
                        <span class="badge-ui badge-gray"><i class="bi bi-hash"></i> Folio: {{ $folioSalida }}</span>
                        <span class="badge-ui badge-success"><i class="bi bi-calendar3"></i> {{ $fechaSalida }} {{ $horaSalida }}</span>
                      </div>
                    </div>
                    <div style="text-align:right;">
                      <div style="font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase;">Doc ID</div>
                      <div class="history-count" style="background:#fff; border:1px solid var(--line); margin-top:4px;">#EXP-{{ str_pad($i + 1, 3, '0', STR_PAD_LEFT) }}</div>
                    </div>
                  </div>

                  <div class="info-grid">
                    <div class="info-panel">
                      <div class="info-panel-title"><i class="bi bi-clipboard-data"></i> Datos del Traslado</div>
                      <div class="info-row"><span class="info-key">Autorizó Traslado</span><span class="info-val">{{ $responsable ?: '—' }}</span></div>
                      <div class="info-row"><span class="info-key">Destino / Empresa</span><span class="info-val">{{ $empresaDestino ?: '—' }}</span></div>
                      <div class="info-row"><span class="info-key">Retorno Programado</span><span class="info-val">{{ $fechaRegreso ? $fechaRegreso.' '.$horaRegreso : 'No definido' }}</span></div>
                      
                      <div class="info-row" style="flex-direction:column; gap:4px; margin-top:16px;">
                        <span class="info-key">Notas Técnicas</span>
                        <div class="obs-box w-100">{{ $observaciones ?: 'Sin observaciones adicionales registradas.' }}</div>
                      </div>
                    </div>

                    <div class="info-panel">
                      <div class="info-panel-title"><i class="bi bi-box-seam"></i> Inventario Remitido</div>
                      
                      @if(is_array($componentesArray) && count($componentesArray))
                        <div style="margin-bottom:16px;">
                          @foreach($componentesArray as $comp)
                            @php
                              if (is_array($comp)) {
                                $compNombre = $comp['nombre'] ?? $comp['componente'] ?? $comp['tipo'] ?? 'Componente';
                                $compCantidad = $comp['cantidad'] ?? null;
                                $compUnidad = $comp['unidad'] ?? $comp['tipo_unidad'] ?? $comp['medida'] ?? null;
                                $compTexto = trim($compNombre.' '.($compCantidad ? '('.$compCantidad.($compUnidad ? ' '.$compUnidad : '').')' : ''));
                              } else {
                                $compTexto = $comp;
                              }
                            @endphp
                            <span class="component-chip"><i class="bi bi-check-circle-fill text-success" style="font-size:10px;"></i> {{ $compTexto }}</span>
                          @endforeach
                        </div>
                      @elseif(!empty($componentesRaw) && is_string($componentesRaw))
                        <div class="obs-box mb-3">{{ $componentesRaw }}</div>
                      @else
                        <div class="obs-box mb-3 text-center">No se documentaron componentes.</div>
                      @endif

                      <div class="info-panel-title" style="border-top:1px solid var(--line); padding-top:16px;"><i class="bi bi-pen"></i> Rúbrica de Conformidad</div>
                      <div class="signature-box">
                        @if($firmaSrc)
                          <img src="{{ $firmaSrc }}" alt="Firma Electrónica Autorizada">
                        @else
                          <div class="empty-state" style="padding:0;"><i class="bi bi-vector-pen fs-3 d-block mb-2"></i> Firma no disponible</div>
                        @endif
                      </div>
                      <div style="text-align:center; margin-top:8px;">
                        <div style="font-weight:700; color:#111; font-size:13px;">{{ $responsable ?: 'Usuario no autenticado' }}</div>
                        <div style="color:var(--muted); font-size:11px;">Aval legal del traspaso de custodia.</div>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif

        @else
          <div class="empty-state">
            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
            Aún no se ha iniciado la bitácora de eventos para esta orden.
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection