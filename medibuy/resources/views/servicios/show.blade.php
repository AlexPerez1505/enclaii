{{-- resources/views/mantenimiento/detalle.blade.php --}}
@extends('layouts.app')

@section('title','Mantenimiento - Detalle')
@section('titulo','Mantenimiento')

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

  /* Curva de animación suave (Apple style) */
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

/* --- Utilidades Generales --- */
.page-wrap { max-width: 1200px; margin: 0 auto; padding: 32px 20px 60px; }
.flex-row { display: flex; align-items: center; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.gap-4 { gap: 16px; }
.mt-1 { margin-top: 4px; }
.mt-2 { margin-top: 8px; }
.mt-3 { margin-top: 16px; }
.mb-1 { margin-bottom: 4px; }
.mb-2 { margin-bottom: 8px; }
.mb-3 { margin-bottom: 16px; }
.mb-4 { margin-bottom: 24px; }
.text-muted { color: var(--muted); }
.text-bold { font-weight: 700; }
.w-full { width: 100%; }

/* --- Animaciones --- */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-entry { animation: fadeInUp 0.8s var(--ease) both; }
.delay-1 { animation-delay: 0.15s; }
.delay-2 { animation-delay: 0.3s; }

/* --- Tarjetas (Cards) --- */
.card-ui {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.01);
  padding: 24px;
  transition: all 0.4s var(--ease);
}
.card-ui:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.04);
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
  transition: all 0.3s var(--ease);
  line-height: 1;
}
.btn-ui:active { transform: scale(0.96); }

.btn-primary { background: var(--blue); color: #fff; }
.btn-primary:hover { background: #006ce4; box-shadow: 0 8px 20px rgba(0, 122, 255, 0.25); transform: translateY(-1px); }

.btn-ghost { background: transparent; color: var(--gray); }
.btn-ghost:hover { background: var(--bg); color: #111; }

.btn-outline { background: var(--card); border: 1px solid var(--line); color: var(--ink); }
.btn-outline:hover { background: var(--bg); border-color: #d1d5db; }

.btn-outline-blue { background: var(--blue-soft); border: 1px solid transparent; color: var(--blue); }
.btn-outline-blue:hover { background: #d0e3ff; }

/* --- Badges --- */
.badge-ui {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 14px; border-radius: 999px;
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
}
.hero-main { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; flex: 1; }
.hero-icon {
  width: 64px; height: 64px;
  border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
  background: var(--blue-soft);
  color: var(--blue);
  font-size: 28px;
  transition: transform 0.5s var(--ease);
}
.card-ui.hero:hover .hero-icon { transform: scale(1.05) rotate(5deg); }
.hero h1 { font-size: 24px; letter-spacing: -0.02em; }
.hero-meta { font-size: 13px; color: var(--muted); margin-top: 6px; line-height: 1.5; }
.hero-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

/* --- Avisos (Notices) --- */
.notice-ui {
  border-radius: 12px;
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  border: 1px solid transparent;
}
.notice-warn { background: var(--warn-soft); border-color: #fde68a; color: var(--warn); }
.notice-ok { background: var(--success-soft); border-color: #bbf7d0; color: var(--success); }
.notice-ui .n-title { font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 8px; }
.notice-ui .n-desc { font-size: 13px; opacity: 0.9; margin-left: 22px; }

/* --- Layout Principal --- */
.main-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.1fr);
  gap: 24px;
}
@media (max-width: 992px) { .main-grid { grid-template-columns: 1fr; } }

/* --- Info Grid --- */
.section-title { font-size: 16px; font-weight: 700; color: #111; margin-bottom: 4px; }
.section-sub { font-size: 13px; color: var(--muted); margin-bottom: 20px; }

.info-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 20px;
}
@media (max-width: 576px) { .info-grid { grid-template-columns: 1fr; } }

.info-item { display: flex; flex-direction: column; gap: 4px; }
.info-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); font-weight: 700; }
.info-value { font-size: 14px; font-weight: 600; color: var(--ink); }

/* --- Cajas de Texto --- */
.detail-box {
  border-radius: 12px;
  border: 1px solid var(--line);
  background: var(--bg);
  padding: 16px;
  font-size: 14px;
  line-height: 1.6;
  color: var(--ink);
  min-height: 80px;
  white-space: pre-wrap;
}

/* --- Evidencias y Multimedia --- */
.evidencias-grid { display: flex; flex-wrap: wrap; gap: 12px; }
.evidencia-thumb {
  width: 80px; height: 80px;
  border-radius: 12px; overflow: hidden;
  border: 1px solid var(--line);
  cursor: pointer;
  transition: all 0.3s var(--ease);
}
.evidencia-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s var(--ease); }
.evidencia-thumb:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.08); border-color: transparent; }
.evidencia-thumb:hover img { transform: scale(1.1); }
.empty-state { font-size: 13px; color: var(--muted); font-weight: 500; padding: 12px 0; }

.detail-video { width: 100%; max-height: 240px; border-radius: 12px; border: 1px solid var(--line); background: #000; outline: none; }

.firma-box {
  border-radius: 12px; border: 1px dashed #cbd5e1;
  padding: 20px; background: var(--bg);
  text-align: center;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.firma-box img { max-width: 100%; max-height: 120px; }

/* --- Timeline --- */
.timeline { display: flex; flex-direction: column; gap: 16px; max-height: 500px; overflow-y: auto; padding-right: 8px; }
.timeline::-webkit-scrollbar { width: 6px; }
.timeline::-webkit-scrollbar-track { background: transparent; }
.timeline::-webkit-scrollbar-thumb { background: var(--line); border-radius: 10px; }

.timeline-item {
  border-radius: 12px;
  border: 1px solid var(--line);
  padding: 16px;
  background: var(--bg);
  transition: background 0.3s ease;
}
.timeline-item:hover { background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }

.timeline-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.timeline-date { font-size: 12px; color: var(--muted); font-weight: 600; }
.timeline-desc { font-size: 13px; line-height: 1.5; color: var(--ink); white-space: pre-wrap; }

/* Checklist QR Timeline */
.timeline-checklist {
  margin-top: 12px;
  border: 1px solid var(--line);
  border-radius: 12px;
  padding: 14px;
  background: #fff;
}
.timeline-checklist .c-title { font-size: 12px; color: var(--muted); font-weight: 700; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
.timeline-checklist .c-row { font-size: 13px; margin-bottom: 6px; display: flex; flex-direction: column;}
.timeline-checklist .c-row strong { color: #111; font-weight: 600; }

/* SweetAlert Custom UI */
.swal2-popup.sw-wow-popup {
  border-radius: 20px !important;
  background: var(--card) !important;
  padding: 8px !important;
  box-shadow: 0 20px 60px rgba(0,0,0,0.15) !important;
}
.swal2-image { border-radius: 12px !important; margin: 0 !important; }
.swal2-close { border-radius: 50% !important; background: var(--bg) !important; color: var(--ink) !important; }
</style>

@php
  $cleanUrl = function($path){
    if(!$path) return null;
    if(preg_match('/^https?:\/\//', $path)) return $path;

    $p = ltrim((string)$path, '/');
    $p = preg_replace('#^storage/#', '', $p);
    $p = preg_replace('#^public/#', '', $p);
    $p = ltrim($p, '/');

    return \Illuminate\Support\Facades\Storage::disk('public')->url($p);
  };

  $mtoTipo = strtolower(trim($servicio->mantenimiento_tipo ?? 'interno'));
  $isInterno = $mtoTipo === 'interno';
  $isExterno = $mtoTipo === 'externo';

  $osId = $servicio->orden_id ?? null;
  $osValidadaAt = $servicio->orden_validada_at ?? null;
  $osPendiente = empty($osValidadaAt) || empty($osId);

  $estado = $servicio->estado_proceso ?? 'requiere_os';

  $estadoLabel = match($estado){
      'requiere_os'               => 'Requiere OS',
      'pendiente_entrega'         => 'Pendiente entrega',
      'pendiente_salida_foraneo'  => 'Pendiente salida foráneo',
      'pendiente_regreso_foraneo' => 'Pendiente regreso foráneo',
      'pendiente_salida_cliente'  => 'Pendiente salida cliente',
      'completado'                => 'Completado',
      'defectuoso'                => 'Defectuoso',
      default                     => ucfirst(str_replace('_',' ',$estado)),
  };

  // Convertido a las nuevas clases de badges minimalistas
  $badgeClass = match($estado){
      'requiere_os'               => 'badge-warning',
      'pendiente_entrega'         => 'badge-info',
      'pendiente_salida_foraneo'  => 'badge-info',
      'pendiente_regreso_foraneo' => 'badge-info',
      'pendiente_salida_cliente'  => 'badge-info',
      'completado'                => 'badge-success',
      'defectuoso'                => 'badge-danger',
      default                     => 'badge-gray'
  };

  $movimientos = $servicio->movimientos ?? collect();

  $fechaAdq = null;
  try {
    if(!empty($servicio->fecha_adquisicion)) {
      $fechaAdq = \Carbon\Carbon::parse($servicio->fecha_adquisicion)->format('d/m/Y');
    }
  } catch (\Throwable $e) {}

  $anioVal = $servicio->{'año'} ?? null;
  $firmaUrl = $cleanUrl($servicio->firma_digital ?? null);

  $ordenShowUrl = null;
  $ordenPdfUrl = null;
  $ordenRemisionPdfUrl = null;

  if($osId && \Illuminate\Support\Facades\Route::has('ordenes.show')) {
      try {
          $ordenShowUrl = route('ordenes.show', ['orden' => $osId]);
      } catch (\Throwable $e) {
          $ordenShowUrl = null;
      }
  }

  if($osId && \Illuminate\Support\Facades\Route::has('ordenes.pdf')) {
      try {
          $ordenPdfUrl = route('ordenes.pdf', ['orden' => $osId]);
      } catch (\Throwable $e) {
          $ordenPdfUrl = null;
      }
  }

  if($osId && \Illuminate\Support\Facades\Route::has('ordenes.remision.pdf')) {
      try {
          $ordenRemisionPdfUrl = route('ordenes.remision.pdf', ['orden' => $osId]);
      } catch (\Throwable $e) {
          $ordenRemisionPdfUrl = null;
      }
  }

  $puedeGenerarQr = ($isExterno && $estado === 'pendiente_salida_foraneo');
@endphp

<div class="page-wrap">
  
  {{-- HERO --}}
  <div class="card-ui hero mb-4 animate-entry">
    <div class="hero-main">
      <button type="button" class="btn-ui btn-ghost" onclick="window.history.back()" style="padding: 10px;">
        <i class="bi bi-arrow-left" style="font-size: 18px;"></i>
      </button>

   

      <div>
        <h1>
          {{ $servicio->tipo_equipo ?? 'Equipo' }}
          @if($servicio->subtipo_equipo)
            <span class="text-muted" style="font-weight: 500;">· {{ $servicio->subtipo_equipo }}</span>
          @endif
        </h1>

        <div class="hero-meta">
          Mantenimiento: <strong style="color: #111;">{{ strtoupper($mtoTipo) }}</strong> · 
          Serie: <strong style="color: #111;">{{ $servicio->numero_serie ?? '—' }}</strong><br>
          Doctor: <strong style="color: #111;">{{ $servicio->nombre_doctor ?? '—' }}</strong> · 
          Registrado por: <strong style="color: #111;">{{ $servicio->user_name ?? '—' }}</strong>
        </div>
      </div>
    </div>

    <div class="hero-actions">
      <span class="badge-ui {{ $badgeClass }}">
        <i class="bi bi-circle-fill" style="font-size: 8px;"></i> {{ $estadoLabel }}
      </span>

      <span class="badge-ui {{ $osPendiente ? 'badge-warning' : 'badge-success' }}">
        <i class="bi {{ $osPendiente ? 'bi-shield-exclamation' : 'bi-shield-check' }}"></i>
        OS {{ $osPendiente ? 'PENDIENTE' : 'VALIDADA' }}
      </span>

      <a href="{{ route('servicio.proceso', $servicio->id) }}" class="btn-ui btn-primary">
        <i class="bi bi-diagram-3"></i> Abrir proceso
      </a>

      @if($puedeGenerarQr)
        <a href="{{ route('servicio.externo.salida.qr', $servicio->id) }}" class="btn-ui btn-outline">
          <i class="bi bi-qr-code"></i> QR Salida
        </a>
      @endif

      @if($osPendiente && \Illuminate\Support\Facades\Route::has('servicio.os.form'))
        <a href="{{ route('servicio.os.form', $servicio->id) }}" class="btn-ui btn-outline">
          <i class="bi bi-clipboard-check"></i> Validar OS
        </a>
      @endif

      @if(!$osPendiente && $ordenShowUrl)
        <a target="_blank" href="{{ $ordenShowUrl }}" class="btn-ui btn-ghost">
          <i class="bi bi-eye"></i> OS
        </a>
      @endif

      @if(!$osPendiente && $ordenPdfUrl)
        <a target="_blank" href="{{ $ordenPdfUrl }}" class="btn-ui btn-outline-blue">
          <i class="bi bi-file-earmark-pdf"></i> PDF OS
        </a>
      @endif

      @if(!$osPendiente && $ordenRemisionPdfUrl)
        <a target="_blank" href="{{ $ordenRemisionPdfUrl }}" class="btn-ui btn-ghost">
          <i class="bi bi-receipt"></i> Remisión
        </a>
      @endif
    </div>
  </div>

  {{-- NOTICES --}}
  <div class="animate-entry delay-1">
    @if($isInterno)
      @if($osPendiente)
        <div class="notice-ui notice-warn mb-4">
          <div class="n-title"><i class="bi bi-exclamation-triangle-fill"></i> Requiere Orden de Servicio</div>
          <div class="n-desc">Este servicio es <strong>INTERNO</strong>. El flujo correcto es: <strong>registro → validar OS → salida a entrega</strong>.</div>
        </div>
      @else
        <div class="notice-ui notice-ok mb-4">
          <div class="n-title"><i class="bi bi-check-circle-fill"></i> OS Validada</div>
          <div class="n-desc">OS vinculada: <strong>#{{ $osId }}</strong> · Validada el <strong>{{ $osValidadaAt ? \Carbon\Carbon::parse($osValidadaAt)->format('d/m/Y H:i') : '—' }}</strong></div>
        </div>
      @endif
    @else
      @if($osPendiente)
        <div class="notice-ui notice-info mb-4" style="background: var(--blue-soft); color: var(--blue);">
          <div class="n-title"><i class="bi bi-arrow-repeat"></i> Flujo externo activo</div>
          <div class="n-desc" style="color: #0b2a4a;">Flujo correcto: <strong>registro → salida a mantenimiento foráneo → regreso → validar OS → salida para cliente</strong>.</div>
        </div>
      @else
        <div class="notice-ui notice-ok mb-4">
          <div class="n-title"><i class="bi bi-check-circle-fill"></i> OS Validada</div>
          <div class="n-desc">OS vinculada: <strong>#{{ $osId }}</strong> · Validada el <strong>{{ $osValidadaAt ? \Carbon\Carbon::parse($osValidadaAt)->format('d/m/Y H:i') : '—' }}</strong></div>
        </div>
      @endif
    @endif
  </div>

  {{-- MAIN GRID --}}
  <div class="main-grid animate-entry delay-2">
    
    {{-- COLUMNA IZQUIERDA --}}
    <div class="card-ui">
      <div class="mb-4">
        <div class="section-title">Información del equipo</div>
        <div class="section-sub">Datos clave del activo y del responsable asignado.</div>
        
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Tipo</span>
            <span class="info-value">{{ $servicio->tipo_equipo ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Subtipo</span>
            <span class="info-value">{{ $servicio->subtipo_equipo ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Tipo de mantenimiento</span>
            <span class="info-value">{{ strtoupper($mtoTipo) }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Estado actual</span>
            <span class="info-value">{{ $estadoLabel }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Número de Serie</span>
            <span class="info-value">{{ $servicio->numero_serie ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Marca / Modelo</span>
            <span class="info-value">{{ trim(($servicio->marca ?? '').' '.($servicio->modelo ?? '')) ?: '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Año</span>
            <span class="info-value">{{ $anioVal ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Fecha de adquisición</span>
            <span class="info-value">{{ $fechaAdq ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Doctor responsable</span>
            <span class="info-value">{{ $servicio->nombre_doctor ?? '—' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Orden de Servicio</span>
            <span class="info-value">
              @if($osId)
                #{{ $osId }} · <span style="color: {{ $osPendiente ? 'var(--warn)' : 'var(--success)' }}">{{ $osPendiente ? 'Pendiente' : 'Validada' }}</span>
              @else
                —
              @endif
            </span>
          </div>
        </div>
      </div>

      <div class="mb-4">
        <div class="section-title">Descripción del equipo</div>
        <div class="detail-box">{{ $servicio->descripcion ?? 'Sin descripción registrada.' }}</div>
      </div>

      <div>
        <div class="section-title">Observaciones generales</div>
        <div class="detail-box">{{ $servicio->observaciones ?? 'Sin observaciones registradas.' }}</div>
      </div>
    </div>

    {{-- COLUMNA DERECHA --}}
    <div class="card-ui">
      
      {{-- Evidencias Fotográficas --}}
      <div class="mb-4">
        <div style="display:flex; justify-content:space-between; align-items:flex-end;">
          <div>
            <div class="section-title">Evidencias fotográficas</div>
            @php
              $ev = array_filter([
                $cleanUrl($servicio->evidencia1 ?? null),
                $cleanUrl($servicio->evidencia2 ?? null),
                $cleanUrl($servicio->evidencia3 ?? null),
              ]);
              $countEv = count($ev);
            @endphp
            <div class="section-sub mb-2">Imágenes adjuntas al crear el registro.</div>
          </div>
        </div>

        @if($countEv)
          <div class="evidencias-grid">
            @foreach($ev as $url)
              <div class="evidencia-thumb" data-img="{{ $url }}">
                <img src="{{ $url }}" alt="Evidencia">
              </div>
            @endforeach
          </div>
        @else
          <div class="empty-state">No hay fotografías adjuntas.</div>
        @endif
      </div>

      {{-- Video --}}
      <div class="mb-4">
        <div class="section-title mb-2">Video del equipo</div>
        @php $videoUrl = $cleanUrl($servicio->video ?? null); @endphp
        @if($videoUrl)
          <video class="detail-video" controls preload="metadata">
            <source src="{{ $videoUrl }}" type="video/mp4">
            Tu navegador no soporta video HTML5.
          </video>
        @else
          <div class="empty-state">No hay video adjunto.</div>
        @endif
      </div>

      {{-- Firma Digital --}}
      <div class="mb-4">
        <div class="section-title mb-2">Firma digital</div>
        <div class="firma-box">
          @if($firmaUrl)
            <img src="{{ $firmaUrl }}" alt="Firma digital">
            <div class="mt-2 text-muted" style="font-size:12px;">
              Firmado por <strong style="color: var(--ink);">{{ $servicio->user_name ?? '—' }}</strong>
            </div>
          @else
            <div class="empty-state">Sin firma registrada.</div>
          @endif
        </div>
      </div>

      {{-- Historial Timeline --}}
      <div>
        <div class="section-title">Historial del proceso</div>
        <div class="section-sub mb-3">Trazabilidad de pasos y movimientos.</div>

        @if(collect($movimientos)->count())
          <div class="timeline">
            @foreach($movimientos as $mov)
              @php
                $tipo = $mov->tipo_movimiento ?? 'otro';
                $chipClass = match($tipo) {
                  'salida_foraneo' => 'badge-warning',
                  'regreso_foraneo' => 'badge-success',
                  'entrega', 'salida_cliente' => 'badge-info',
                  default => 'badge-gray',
                };

                $chipText = match($tipo) {
                  'salida_foraneo' => 'Salida foráneo',
                  'regreso_foraneo' => 'Regreso foráneo',
                  'entrega' => 'Salida a entrega',
                  'salida_cliente' => 'Salida cliente',
                  default => ucfirst(str_replace('_',' ', $tipo)),
                };

                $evMov = array_filter([
                  $cleanUrl($mov->evidencia1 ?? null),
                  $cleanUrl($mov->evidencia2 ?? null),
                  $cleanUrl($mov->evidencia3 ?? null),
                ]);
                $movVideo = $cleanUrl($mov->video ?? null);

                $check = is_array($mov->checklist)
                  ? $mov->checklist
                  : (json_decode($mov->checklist ?? '', true) ?: null);
              @endphp

              <div class="timeline-item">
                <div class="timeline-header">
                  <span class="badge-ui {{ $chipClass }}">
                    <i class="bi bi-arrow-repeat"></i> {{ $chipText }}
                  </span>
                  <span class="timeline-date">{{ $mov->created_at?->format('d/m/Y H:i') }}</span>
                </div>

                <div class="timeline-desc">{{ $mov->descripcion ?? 'Sin descripción.' }}</div>

                {{-- Checklist Validation QR --}}
                @if($tipo === 'salida_foraneo' && is_array($check) && ($check['origen'] ?? null) === 'qr_salida_externa')
                  <div class="timeline-checklist">
                    <div class="c-title">Validación por QR</div>
                    
                    <div class="c-row"><span>Responsable:</span> <strong>{{ $check['nombre_salida'] ?? '—' }}</strong></div>
                    <div class="c-row"><span>Fecha/Hora de salida:</span> <strong>{{ $check['fecha_salida'] ?? '—' }} {{ $check['hora_salida'] ?? '' }}</strong></div>
                    <div class="c-row"><span>Componentes:</span> <strong>{{ $check['componentes_salida'] ?? '—' }}</strong></div>

                    @if(!empty($check['observaciones_salida']))
                      <div class="c-row mt-1"><span>Observaciones:</span> <strong>{{ $check['observaciones_salida'] }}</strong></div>
                    @endif

                    @if(!empty($check['fecha_regreso_estimada']) || !empty($check['hora_regreso_estimada']))
                      <div class="c-row mt-1"><span>Regreso estimado:</span> <strong>{{ $check['fecha_regreso_estimada'] ?? '—' }} {{ $check['hora_regreso_estimada'] ?? '' }}</strong></div>
                    @endif

                    @if(!empty($check['firma_salida']))
                      <div class="mt-2">
                        <div class="c-title" style="margin-bottom: 4px;">Firma de Recibido</div>
                        <img src="{{ $check['firma_salida'] }}" alt="Firma de salida" style="max-width:100px; border-radius:8px; border:1px solid var(--line); background:#fff;">
                      </div>
                    @endif
                  </div>
                @endif

                {{-- Evidencias del movimiento --}}
                @if(count($evMov))
                  <div class="mt-3">
                    <div class="text-muted mb-2" style="font-size: 12px; font-weight: 700; text-transform: uppercase;">Fotografías:</div>
                    <div class="evidencias-grid">
                      @foreach($evMov as $u)
                        <div class="evidencia-thumb" data-img="{{ $u }}" style="width: 60px; height: 60px;">
                          <img src="{{ $u }}" alt="Evidencia">
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endif

                {{-- Video del movimiento --}}
                @if($movVideo)
                  <div class="mt-3">
                    <div class="text-muted mb-2" style="font-size: 12px; font-weight: 700; text-transform: uppercase;">Video adjunto:</div>
                    <video controls style="width:100%; border-radius:8px; background:#000; outline:none;">
                      <source src="{{ $movVideo }}" type="video/mp4">
                    </video>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @else
          <div class="empty-state">Aún no hay movimientos en el historial.</div>
        @endif
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Manteniendo la lógica intacta del visor de imágenes con el estilo UI ajustado
document.addEventListener('click', function(e){
  const thumb = e.target.closest('.evidencia-thumb');
  if(!thumb) return;
  
  const url = thumb.getAttribute('data-img');
  if(!url) return;

  Swal.fire({
    imageUrl: url,
    imageAlt: 'Evidencia',
    showConfirmButton: false,
    showCloseButton: true,
    width: 'auto',
    background: 'transparent',
    backdrop: `rgba(0,0,0,0.85)`,
    customClass: {
      popup: 'sw-wow-popup',
      image: 'swal2-image'
    }
  });
});
</script>
@endsection