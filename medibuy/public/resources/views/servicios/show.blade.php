@extends('layouts.app')
@section('title','Mantenimiento interno - Detalle')
@section('titulo','Mantenimiento interno')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root{
  --bg:#eaebec; --panel:#ffffff; --text:#0f172a; --muted:#667085; --border:#e7eaf0;
  --pblue:#dbeafe; --pblue-700:#1d4ed8; --pgreen:#dcfce7; --pgreen-700:#046c4e;
  --danger-soft:#fee2e2; --danger:#b91c1c;
  --shadow:0 10px 30px rgba(2,6,23,.06); --radius:22px;
}
*,*::before,*::after{ box-sizing:border-box; }
body{
  background:var(--bg);
  color:var(--text);
  font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
}
.page-wrap{
  max-width:1160px;
  margin:0 auto;
  padding:0 16px 40px;
}

/* HERO */
.hero{
  background:
    radial-gradient(1200px 150px at 0% 0%, rgba(96,165,250,.18), transparent 40%),
    radial-gradient(1200px 150px at 100% 0%, rgba(14,165,233,.14), transparent 40%),
    #fff;
  border:1px solid var(--border);
  border-radius:18px;
  padding:14px 18px;
  box-shadow:var(--shadow);
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  flex-wrap:wrap;
  margin:18px 0 20px;
}
.hero-main{
  display:flex;
  align-items:center;
  gap:14px;
  flex-wrap:wrap;
}
.hero .chip{
  width:56px; height:56px;
  border-radius:16px;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  background:#fff;
  border:1px solid #dce7ff;
}
.hero h1{ margin:0; font-weight:800; letter-spacing:-.02em; }
.subtle{ color:var(--muted); font-size:13px; }

.hero-meta{
  font-size:12px;
  color:var(--muted);
}
.badge-estado{
  display:inline-flex;
  align-items:center;
  gap:.35rem;
  padding:6px 10px;
  border-radius:999px;
  font-size:12px;
  font-weight:700;
}
.badge-registro{ background:#e0f2fe; color:#1d4ed8; }
.badge-salida{ background:#fef3c7; color:#92400e; }
.badge-regreso{ background:#dcfce7; color:#166534; }
.badge-entregado{ background:#e0e7ff; color:#3730a3; }
.badge-defectuoso{ background:#fee2e2; color:#b91c1c; }
.badge-otro{ background:#e5e7eb; color:#374151; }

.btn{
  display:inline-flex;
  align-items:center;
  gap:.45rem;
  padding:9px 13px;
  border-radius:14px;
  border:1px solid var(--border);
  background:#fff;
  color:#334155;
  font-weight:700;
  text-decoration:none;
  cursor:pointer;
  transition:transform .04s ease, box-shadow .18s ease, background .18s ease;
}
.btn:active{ transform:translateY(1px); }
.btn-utility{ box-shadow:0 4px 10px rgba(2,6,23,.04); }
.btn-soft{
  background:#f9fafb;
}
.btn-primary-soft{
  background:var(--pblue);
  color:#0b2a4a;
  border-color:rgba(96,165,250,.45);
}

/* MAIN GRID */
.main-grid{
  display:grid;
  grid-template-columns: minmax(0, 1.4fr) minmax(0, 1.2fr);
  gap:18px;
}
@media (max-width:992px){
  .main-grid{ grid-template-columns:1fr; }
}

/* CARD */
.card-soft{
  background:#fff;
  border-radius:var(--radius);
  border:1px solid var(--border);
  box-shadow:var(--shadow);
  padding:16px 18px 18px;
}

/* INFO GRID */
.info-grid{
  display:grid;
  grid-template-columns: repeat(2, minmax(0,1fr));
  gap:10px 18px;
  font-size:13px;
}
@media (max-width:576px){
  .info-grid{ grid-template-columns:1fr; }
}
.info-label{
  font-size:11px;
  text-transform:uppercase;
  letter-spacing:.05em;
  color:#9ca3af;
  font-weight:700;
}
.info-value{
  font-size:13px;
  font-weight:600;
}

/* SECTIONS */
.section-title{
  font-size:13px;
  font-weight:700;
  margin-bottom:6px;
  color:#0f172a;
}
.section-sub{
  font-size:11px;
  color:#9ca3af;
}

/* TEXTAREAS */
.detail-box{
  border-radius:14px;
  border:1px solid #e2e8f0;
  background:#f9fafb;
  padding:10px 11px;
  font-size:13px;
  min-height:70px;
  white-space:pre-wrap;
}

/* Evidencias */
.evidencias-grid{
  display:flex;
  flex-wrap:wrap;
  gap:8px;
}
.evidencia-thumb{
  width:78px;
  height:78px;
  border-radius:12px;
  overflow:hidden;
  border:1px solid var(--border);
  background:#f1f5f9;
  cursor:pointer;
}
.evidencia-thumb img{
  width:100%; height:100%; object-fit:cover;
}
.evidencias-empty{
  font-size:12px;
  color:var(--muted);
}

/* Video */
.detail-video{
  width:100%;
  max-height:260px;
  border-radius:16px;
  border:1px solid var(--border);
  background:#000;
}
.video-empty{
  font-size:12px;
  color:var(--muted);
}

/* Firma */
.firma-box{
  border-radius:14px;
  border:1px dashed #cbd5e1;
  padding:10px 12px;
  background:#f9fafb;
  text-align:center;
}
.firma-box img{
  max-width:100%;
  max-height:140px;
}
.firma-placeholder{
  font-size:12px;
  color:#9ca3af;
}

/* Timeline */
.timeline{
  display:flex;
  flex-direction:column;
  gap:10px;
  max-height:280px;
  overflow-y:auto;
}
.timeline-item{
  border-radius:14px;
  border:1px solid #e2e8f0;
  padding:9px 11px;
  background:#f9fafb;
  font-size:12px;
}
.timeline-label{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:2px;
}
.timeline-chip{
  display:inline-flex;
  align-items:center;
  gap:4px;
  padding:3px 8px;
  border-radius:999px;
  font-size:11px;
}
.timeline-chip.salida{
  background:#fee2e2; color:#b91c1c;
}
.timeline-chip.entrada{
  background:#dcfce7; color:#166534;
}
.timeline-chip.dueno{
  background:#e0f2fe; color:#1d4ed8;
}
.timeline-date{
  font-size:11px;
  color:#6b7280;
}
.timeline-desc{
  margin-top:4px;
  white-space:pre-wrap;
}
.timeline-empty{
  font-size:12px;
  color:#9ca3af;
}
</style>

@php
  $estado = $servicio->estado_proceso ?? 'registro';
  $estadoLabel = ucfirst(str_replace('_',' ',$estado));
  $badgeClass = match($estado){
      'registro' => 'badge-registro',
      'salida','salida_mantenimiento','btn-salida-mantenimiento' => 'badge-salida',
      'regreso','entrada_mantenimiento' => 'badge-regreso',
      'entregado','salida_dueno','btn-salida-dueno' => 'badge-entregado',
      'defectuoso' => 'badge-defectuoso',
      default => 'badge-otro'
  };

  // 🔹 Evitar error de variable indefinida
  /** @var \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $movimientos */
  $movimientos = $movimientos ?? collect();
@endphp

<div class="page-wrap">
  {{-- HERO --}}
  <div class="hero" style="margin-top:90px;">
    <div class="hero-main">
      <button type="button" class="btn btn-soft btn-utility" onclick="window.history.back()">
        <i class="bi bi-arrow-left"></i> Volver
      </button>

      <div class="chip">
        <i class="bi bi-gear-wide-connected" style="font-size:1.4rem;color:#1d4ed8"></i>
      </div>

      <div>
        <h1 class="h5 mb-1">
          {{ $servicio->tipo_equipo ?? 'Equipo' }}
          @if($servicio->subtipo_equipo)
            <span class="text-muted">·</span> {{ $servicio->subtipo_equipo }}
          @endif
        </h1>
        <div class="hero-meta">
          Serie: <strong>{{ $servicio->numero_serie ?? '—' }}</strong>
          · Doctor: <strong>{{ $servicio->nombre_doctor ?? '—' }}</strong><br>
          Registrado por: <strong>{{ $servicio->user_name ?? '—' }}</strong>
        </div>
      </div>
    </div>

    <div class="d-flex flex-wrap gap-2 align-items-center">
      <span class="badge-estado {{ $badgeClass }}">
        <i class="bi bi-circle-fill" style="font-size:.55rem"></i>
        {{ $estadoLabel }}
      </span>
      <a href="{{ url('/movimientos/salida-mantenimiento/'.$servicio->id) }}" class="btn btn-soft btn-utility">
        <i class="bi bi-box-arrow-right"></i> Salida mantto.
      </a>
      <a href="{{ url('/movimientos/entrada-mantenimiento/'.$servicio->id) }}" class="btn btn-soft btn-utility">
        <i class="bi bi-arrow-bar-left"></i> Regreso
      </a>
      <a href="{{ url('/movimientos/salida-dueno/'.$servicio->id) }}" class="btn btn-soft btn-utility">
        <i class="bi bi-person-check"></i> Entrega
      </a>
    </div>
  </div>

  {{-- MAIN GRID --}}
  <div class="main-grid">
    {{-- Columna izquierda: info --}}
    <div class="card-soft">
      <div class="mb-3">
        <div class="section-title">Información del equipo</div>
        <div class="section-sub">Datos clave del activo y del responsable.</div>
      </div>

      <div class="info-grid mb-3">
        <div>
          <div class="info-label">Tipo</div>
          <div class="info-value">{{ $servicio->tipo_equipo ?? '—' }}</div>
        </div>
        <div>
          <div class="info-label">Subtipo</div>
          <div class="info-value">{{ $servicio->subtipo_equipo ?? '—' }}</div>
        </div>
        <div>
          <div class="info-label">Serie</div>
          <div class="info-value">{{ $servicio->numero_serie ?? '—' }}</div>
        </div>
        <div>
          <div class="info-label">Marca / Modelo</div>
          <div class="info-value">
            {{ trim(($servicio->marca ?? '').' '.($servicio->modelo ?? '')) ?: '—' }}
          </div>
        </div>
        <div>
          <div class="info-label">Año</div>
          <div class="info-value">{{ $servicio->anio ?? '—' }}</div>
        </div>
        <div>
          <div class="info-label">Fecha de adquisición</div>
          <div class="info-value">
            {{ optional($servicio->fecha_adquisicion)->format('Y-m-d') ?? '—' }}
          </div>
        </div>
        <div>
          <div class="info-label">Doctor responsable</div>
          <div class="info-value">{{ $servicio->nombre_doctor ?? '—' }}</div>
        </div>
        <div>
          <div class="info-label">Estado actual</div>
          <div class="info-value">{{ $estadoLabel }}</div>
        </div>
      </div>

      <div class="mb-3">
        <div class="section-title">Descripción del equipo</div>
        <div class="detail-box">
          {{ $servicio->descripcion ?? 'Sin descripción registrada.' }}
        </div>
      </div>

      <div>
        <div class="section-title">Observaciones generales</div>
        <div class="detail-box">
          {{ $servicio->observaciones ?? 'Sin observaciones registradas.' }}
        </div>
      </div>
    </div>

    {{-- Columna derecha: evidencias / video / firma / movimientos --}}
    <div class="card-soft">
      {{-- Evidencias --}}
      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <div class="section-title mb-0">Evidencias fotográficas</div>
          @php
            $ev = array_filter([$servicio->evidencia1, $servicio->evidencia2, $servicio->evidencia3]);
            $countEv = count($ev);
          @endphp
          <div class="section-sub">
            @if($countEv)
              {{ $countEv }} archivo{{ $countEv > 1 ? 's' : '' }}
            @else
              Sin fotos
            @endif
          </div>
        </div>

        @if($countEv)
          <div class="evidencias-grid">
            @foreach($ev as $url)
              @php $finalUrl = $url; @endphp
              <div class="evidencia-thumb" data-img="{{ $finalUrl }}">
                <img src="{{ $finalUrl }}" alt="Evidencia">
              </div>
            @endforeach
          </div>
        @else
          <div class="evidencias-empty">No hay evidencias fotográficas adjuntas.</div>
        @endif
      </div>

      {{-- Video --}}
      <div class="mb-3">
        <div class="section-title">Video del equipo</div>
        @if($servicio->video)
          <video class="detail-video" controls>
            <source src="{{ $servicio->video }}" type="video/mp4">
            Tu navegador no soporta video HTML5.
          </video>
        @else
          <div class="video-empty">No hay video adjunto para este equipo.</div>
        @endif
      </div>

      {{-- Firma --}}
      <div class="mb-3">
        <div class="section-title">Firma digital</div>
        <div class="firma-box">
          @if($servicio->firma_digital)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($servicio->firma_digital) }}" alt="Firma digital">
            <div class="mt-1" style="font-size:12px;">
              Firmado por <strong>{{ $servicio->user_name ?? '—' }}</strong>
            </div>
          @else
            <div class="firma-placeholder">Sin firma registrada.</div>
          @endif
        </div>
      </div>

      {{-- Movimientos --}}
      <div>
        <div class="section-title mb-1">Reportes y movimientos</div>
        <div class="section-sub mb-2">Histórico de salidas, regresos y entregas de este equipo.</div>

        @if($movimientos->count())
          <div class="timeline">
            @foreach($movimientos as $mov)
              @php
                $tipo = $mov->tipo_movimiento;
                $chipClass = 'otro';
                $chipText  = 'Movimiento';

                if ($tipo === 'salida_mantenimiento') { $chipClass = 'salida'; $chipText = 'Salida a mantenimiento'; }
                elseif ($tipo === 'entrada_mantenimiento') { $chipClass = 'entrada'; $chipText = 'Regreso de mantenimiento'; }
                elseif ($tipo === 'salida_dueno') { $chipClass = 'dueno'; $chipText = 'Entrega a destinatario'; }

                $evMov = array_filter([$mov->evidencia1, $mov->evidencia2, $mov->evidencia3]);
                $hasVideoMov = !empty($mov->video);
                $checklistItems = [];
                if($mov->checklist){
                  try{
                    $checklistItems = is_string($mov->checklist) ? json_decode($mov->checklist,true) : $mov->checklist;
                  }catch(\Throwable $e){ $checklistItems = []; }
                }
              @endphp

              <div class="timeline-item">
                <div class="timeline-label">
                  <span class="timeline-chip {{ $chipClass }}">
                    <i class="bi bi-arrow-repeat"></i> {{ $chipText }}
                  </span>
                  <span class="timeline-date">
                    {{ $mov->created_at?->format('Y-m-d H:i') }}
                  </span>
                </div>
                <div class="timeline-desc">
                  {{ $mov->descripcion ?? 'Sin descripción.' }}
                </div>

                @if(count($evMov))
                  <div class="mt-2">
                    <span class="section-sub d-block mb-1">Evidencias:</span>
                    <div class="evidencias-grid">
                      @foreach($evMov as $u)
                        <div class="evidencia-thumb" data-img="{{ $u }}">
                          <img src="{{ $u }}" alt="Evidencia movimiento">
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endif

                @if($hasVideoMov)
                  <div class="mt-2">
                    <span class="section-sub d-block mb-1">Video del movimiento:</span>
                    <video controls style="width:100%;max-height:180px;border-radius:10px;border:1px solid #e2e8f0;">
                      <source src="{{ $mov->video }}" type="video/mp4">
                    </video>
                  </div>
                @endif

                @if(is_array($checklistItems) && count($checklistItems))
                  <div class="mt-2">
                    <span class="section-sub d-block mb-1">Checklist:</span>
                    <ul class="mb-0" style="padding-left:18px;">
                      @foreach($checklistItems as $item)
                        <li>{{ $item }}</li>
                      @endforeach
                    </ul>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @else
          <div class="timeline-empty">Aún no hay movimientos registrados para este equipo.</div>
        @endif
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('click', function(e){
  const thumb = e.target.closest('.evidencia-thumb');
  if(!thumb) return;
  const url = thumb.getAttribute('data-img');
  if(!url) return;

  Swal.fire({
    imageUrl: url,
    imageAlt: 'Evidencia',
    showConfirmButton:false,
    showCloseButton:true,
    width: 'auto',
    background:'#0b1120',
  });
});
</script>
@endsection
