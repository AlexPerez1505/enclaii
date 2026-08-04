@extends('layouts.app')
@section('title', 'Editar servicio')
@section('titulo', 'Editar servicio')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

@php
use Illuminate\Support\Str;

$img1Url = $servicio->evidencia1
    ? (Str::startsWith($servicio->evidencia1, ['http://','https://'])
        ? $servicio->evidencia1
        : asset('storage/' . ltrim($servicio->evidencia1, '/')))
    : null;

$img2Url = $servicio->evidencia2
    ? (Str::startsWith($servicio->evidencia2, ['http://','https://'])
        ? $servicio->evidencia2
        : asset('storage/' . ltrim($servicio->evidencia2, '/')))
    : null;

$img3Url = $servicio->evidencia3
    ? (Str::startsWith($servicio->evidencia3, ['http://','https://'])
        ? $servicio->evidencia3
        : asset('storage/' . ltrim($servicio->evidencia3, '/')))
    : null;

$videoUrl = $servicio->video
    ? (Str::startsWith($servicio->video, ['http://','https://'])
        ? $servicio->video
        : asset('storage/' . ltrim($servicio->video, '/')))
    : null;

$firmaUrl = $servicio->firma_digital
    ? (Str::startsWith($servicio->firma_digital, ['http://','https://'])
        ? $servicio->firma_digital
        : asset('storage/' . ltrim($servicio->firma_digital, '/')))
    : null;

$fechaInicial = old('fecha_inicial');
if (!$fechaInicial && !empty($servicio->fecha_adquisicion)) {
    try {
        $fechaInicial = \Illuminate\Support\Carbon::parse($servicio->fecha_adquisicion)->format('Y-m-d');
    } catch (\Throwable $e) {
        $fechaInicial = $servicio->fecha_adquisicion;
    }
}
@endphp

<style>
:root {
  /* PALETA CORPORATIVA ESTRICTA */
  --bg: #f9fafb; 
  --card: #ffffff; 
  --title: #111111;
  --ink: #333333; 
  --muted: #888888; 
  --line: #ebebeb; 
  --blue: #007aff; 
  --blue-soft: #e6f0ff; 
  --success: #15803d; 
  --success-soft: #e6ffe6;  
  --danger: #ff4a4a; 
  --danger-soft: #ffebeb;
  --warn: #b45309;
  --warn-soft: #fffbeb;

  /* VARIABLES DE DISEÑO MÍNIMO */
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --shadow-base: 0 4px 12px rgba(0,0,0,0.02);
  --shadow-hover: 0 8px 24px rgba(0,0,0,0.06);
  --shadow-focus: 0 0 0 3px var(--blue-soft);
  --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  --font-family: 'Quicksand', sans-serif;
}

/* RESET & BASE */
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  background-color: var(--bg);
  font-family: var(--font-family);
  color: var(--ink);
  -webkit-font-smoothing: antialiased;
}
h1, h2, h3, h4, h5, h6 { color: var(--title); font-weight: 700; margin-bottom: 0.5rem; }
a { text-decoration: none; }
ul { list-style-position: inside; }

/* LAYOUT PRINCIPAL */
.premium-wrapper {
  max-width: 1200px;
  margin: 0 auto;
  padding: 48px 24px;
}
.page-layout {
  display: grid;
  grid-template-columns: 1fr;
  gap: 32px;
  align-items: start;
}
@media (min-width: 992px) {
  .page-layout { grid-template-columns: 2fr 1fr; }
}

/* ANIMACIONES */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-enter { animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }

/* CABECERA */
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 40px;
  flex-wrap: wrap;
  gap: 24px;
}
.page-title-block h1 { font-size: 2rem; letter-spacing: -0.02em; margin-bottom: 8px; }
.page-subtitle { color: var(--muted); font-size: 0.95rem; font-weight: 600; line-height: 1.5; }

/* BADGES */
.badge-soft {
  background: var(--bg);
  color: var(--muted);
  border: 1px solid var(--line);
  border-radius: 6px;
  padding: 4px 10px;
  font-weight: 700;
  font-size: 0.75rem;
  display: inline-flex;
  align-items: center;
  margin-left: 8px;
}

/* ALERTAS Y NOTAS */
.alert-box {
  padding: 16px 24px;
  border-radius: var(--radius-md);
  margin-bottom: 24px;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  font-weight: 600;
  font-size: 0.95rem;
  line-height: 1.5;
  box-shadow: var(--shadow-base);
}
.alert-box i { font-size: 1.25rem; }
.alert-box.success { background: var(--success-soft); color: var(--success); border: 1px solid rgba(21, 128, 61, 0.2); }
.alert-box.danger { background: var(--danger-soft); color: var(--danger); border: 1px solid rgba(255, 74, 74, 0.2); }
.alert-box.info { background: var(--blue-soft); color: var(--blue); border: 1px solid rgba(0, 122, 255, 0.2); }
.alert-box.warn { background: var(--warn-soft); color: var(--warn); border: 1px solid rgba(180, 83, 9, 0.2); }

/* TARJETAS */
.card-premium {
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-base);
  margin-bottom: 24px;
  overflow: hidden;
  transition: var(--transition);
}
.card-premium:hover {
  transform: translateY(-2px);
  box-shadow: var(--shadow-hover);
}
.card-header-ux {
  padding: 20px 24px;
  border-bottom: 1px solid var(--line);
  font-weight: 700;
  color: var(--title);
  font-size: 1.05rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.card-header-ux i { color: var(--muted); margin-right: 8px; }
.card-body-ux { padding: 24px; }

/* GRID DE FORMULARIO */
.form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}
@media (min-width: 768px) {
  .form-grid { grid-template-columns: 1fr 1fr; }
  .col-span-2 { grid-column: span 2; }
}

/* INPUTS ESTILO FLOTANTE */
.ux-float {
  position: relative;
  width: 100%;
}
.premium-input {
  width: 100%;
  padding: 22px 16px 8px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-sm);
  font-family: var(--font-family);
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--ink);
  transition: var(--transition);
}
textarea.premium-input { min-height: 110px; resize: vertical; }
.premium-input:focus {
  outline: none;
  border-color: var(--blue);
  box-shadow: var(--shadow-focus);
}
.ux-float label {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted);
  font-size: 0.95rem;
  font-weight: 600;
  pointer-events: none;
  transition: var(--transition);
  margin: 0;
}
textarea ~ label { top: 24px; }
.ux-float.has-value label,
.premium-input:focus ~ label,
.premium-input:not(:placeholder-shown) ~ label {
  top: 14px;
  font-size: 0.75rem;
  color: var(--blue);
}
.premium-input[readonly] { background: var(--bg); cursor: not-allowed; color: var(--muted); }

/* CONTROLES SEGMENTADOS (APPLE STYLE) */
.segmented-wrapper { margin-bottom: 8px; }
.segmented-label { font-size: 0.85rem; font-weight: 700; color: var(--title); margin-bottom: 8px; display: block; }
.segmented-label .required { color: var(--danger); }
.segmented {
  display: flex;
  background: var(--bg);
  padding: 6px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--line);
  gap: 6px;
}
.segmented input { display: none; }
.segmented label {
  flex: 1;
  text-align: center;
  padding: 10px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 0.9rem;
  color: var(--muted);
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.segmented input:checked + label {
  background: var(--card);
  color: var(--blue);
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

/* GRID MULTIMEDIA */
.media-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}
@media (min-width: 576px) { .media-grid { grid-template-columns: repeat(4, 1fr); } }

/* TILES (EVIDENCIAS) */
.tile {
  position: relative;
  width: 100%;
  aspect-ratio: 1/1;
  border: 1px dashed var(--line);
  border-radius: var(--radius-sm);
  background: var(--bg);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  cursor: pointer;
  transition: var(--transition);
}
.tile:hover { background: var(--card); border-color: var(--blue); }
.tile img, .tile video { width: 100%; height: 100%; object-fit: cover; position: absolute; inset: 0; z-index: 1; }
.tile-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  color: var(--muted);
  z-index: 0;
}
.tile-empty i { font-size: 1.5rem; }
.tile-empty span { font-weight: 700; font-size: 0.9rem; color: var(--title); }
.tile-empty small { font-size: 0.75rem; }

.tile-remove {
  position: absolute;
  top: 8px; right: 8px;
  z-index: 12;
  background: var(--card);
  color: var(--danger);
  border: 1px solid var(--danger-soft);
  width: 28px; height: 28px;
  border-radius: 6px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  transition: var(--transition);
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.tile-remove:hover { background: var(--danger-soft); transform: scale(1.05); }

.tile-check {
  position: absolute; inset: 0;
  background: rgba(255, 74, 74, 0.85);
  display: flex; align-items: center; justify-content: center; text-align: center;
  z-index: 11;
  color: white; font-size: 0.85rem; font-weight: 700; padding: 16px;
  backdrop-filter: blur(2px);
}
.tile-hint {
  position: absolute; bottom: 8px; right: 8px;
  background: rgba(255,255,255,0.9); border: 1px solid var(--line); border-radius: 6px;
  padding: 4px 8px; font-size: 0.75rem; font-weight: 700; color: var(--title); z-index: 10;
}

/* FIRMA Y EXTRAS */
.media-frame {
  border: 1px solid var(--line);
  background: var(--bg);
  border-radius: var(--radius-sm);
  padding: 16px;
  text-align: center;
  margin-bottom: 16px;
}
.media-frame img { max-width: 100%; max-height: 180px; }

/* CHECKBOX PERSONALIZADO */
.custom-checkbox {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.95rem;
  color: var(--ink);
}
.custom-checkbox input {
  width: 18px; height: 18px;
  accent-color: var(--danger);
  cursor: pointer;
}

/* BOTONES */
.btn-actions {
  display: flex;
  justify-content: flex-end;
  gap: 16px;
  margin-top: 32px;
}
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: var(--radius-sm);
  font-family: var(--font-family);
  font-weight: 700;
  font-size: 0.95rem;
  transition: var(--transition);
  cursor: pointer;
  border: none;
}
.btn-primary { background: var(--blue); color: var(--card); }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0, 122, 255, 0.2); }
.btn-primary:active { transform: scale(0.98); }
.btn-ghost { background: transparent; color: var(--muted); border: 1px solid transparent; }
.btn-ghost:hover { background: var(--card); color: var(--title); border-color: var(--line); }

[x-cloak] { display: none !important; }

/* TIPS SIDEBAR */
.tips-list { padding-left: 16px; margin: 0; color: var(--muted); font-size: 0.9rem; line-height: 1.8; font-weight: 500; }
.tips-list li { margin-bottom: 8px; }
.tips-list b { color: var(--title); }
.sticky-sidebar { position: sticky; top: 32px; }

@media (max-width: 768px) {
  .btn-actions { flex-direction: column-reverse; }
  .btn { width: 100%; }
}
</style>

<div class="premium-wrapper" x-data="ServicioEditUI()" x-init="init()">

  <div class="page-header animate-enter">
    <div class="page-title-block">
      <h1>Editar Servicio #{{ $servicio->id }}</h1>
      <p class="page-subtitle mb-0">
        Actualiza la información del equipo y administra las evidencias del registro.
        <span class="badge-soft">Edición de registro</span>
      </p>
    </div>
    <a href="{{ route('servicios.show', $servicio->id) }}" class="btn btn-ghost">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  @if ($errors->any())
    <div class="alert-box danger animate-enter delay-1">
      <i class="bi bi-exclamation-triangle-fill"></i>
      <div>
        <strong>Corrige los campos marcados:</strong>
        <ul style="margin-top: 8px; margin-bottom: 0;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  @if (session('error'))
    <div class="alert-box danger animate-enter delay-1">
      <i class="bi bi-x-octagon-fill"></i>
      <div>{{ session('error') }}</div>
    </div>
  @endif

  @if (session('ok'))
    <div class="alert-box success animate-enter delay-1">
      <i class="bi bi-check-circle-fill"></i>
      <div>{{ session('ok') }}</div>
    </div>
  @endif

  <form id="servicioEditForm" enctype="multipart/form-data" method="POST" action="{{ route('servicio.update', $servicio->id) }}">
    @csrf
    @method('PUT')

    <input type="hidden" name="eliminar_evidencia1" x-model="removeE1">
    <input type="hidden" name="eliminar_evidencia2" x-model="removeE2">
    <input type="hidden" name="eliminar_evidencia3" x-model="removeE3">
    <input type="hidden" name="eliminar_video" x-model="removeVideo">
    <input type="hidden" name="eliminar_firma" x-model="removeFirma">

    <div class="alert-box warn animate-enter delay-1">
      <i class="bi bi-info-circle-fill"></i>
      <div>
        <strong>Importante:</strong> Puedes editar libremente <b>tipo de equipo</b> y <b>subtipo de equipo</b>. 
        También puedes <b>reemplazar</b> o <b>eliminar</b> evidencias existentes. La firma actual puede eliminarse si así lo necesitas.
      </div>
    </div>

    <div class="page-layout animate-enter delay-2">
      <div>
        <div class="card-premium">
          <div class="card-header-ux">
            <span><i class="bi bi-card-text"></i> Información del Equipo</span>
          </div>
          <div class="card-body-ux">
            
            <div class="segmented-wrapper col-span-2 form-grid" style="grid-template-columns: 1fr; margin-bottom: 20px;">
              <div>
                <label class="segmented-label">Modalidad del servicio <span class="required">*</span></label>
                <div class="segmented">
                  <input type="radio" name="mantenimiento_tipo" id="tipo_interno" value="interno"
                    {{ old('mantenimiento_tipo', $servicio->mantenimiento_tipo ?? 'interno') === 'interno' ? 'checked' : '' }}>
                  <label for="tipo_interno"><i class="bi bi-building"></i> Interno</label>

                  <input type="radio" name="mantenimiento_tipo" id="tipo_externo" value="externo"
                    {{ old('mantenimiento_tipo', $servicio->mantenimiento_tipo) === 'externo' ? 'checked' : '' }}>
                  <label for="tipo_externo"><i class="bi bi-box-arrow-up-right"></i> Externo</label>
                </div>
              </div>
            </div>

            <div class="form-grid">
              <div class="ux-float">
                <input class="premium-input" type="text" name="tipo_equipo" id="tipo_equipo" placeholder=" " required
                  value="{{ old('tipo_equipo', $servicio->tipo_equipo) }}">
                <label for="tipo_equipo">Tipo de equipo *</label>
              </div>

              <div class="ux-float">
                <input class="premium-input" type="text" name="subtipo_equipo" id="subtipo_equipo" placeholder=" " required
                  value="{{ old('subtipo_equipo', $servicio->subtipo_equipo) }}">
                <label for="subtipo_equipo">Subtipo de equipo *</label>
              </div>

              <div class="ux-float">
                <input class="premium-input" type="text" name="marca" id="eq_marca" placeholder=" "
                  value="{{ old('marca', $servicio->marca) }}">
                <label for="eq_marca">Marca</label>
              </div>

              <div class="ux-float">
                <input class="premium-input" type="text" name="modelo" id="eq_modelo" placeholder=" "
                  value="{{ old('modelo', $servicio->modelo) }}">
                <label for="eq_modelo">Modelo</label>
              </div>

              <div class="ux-float">
                <input class="premium-input" type="text" name="numero_serie" id="eq_serie" placeholder=" "
                  value="{{ old('numero_serie', $servicio->numero_serie) }}">
                <label for="eq_serie">Número de serie</label>
              </div>

              <div class="ux-float">
                <input class="premium-input" type="text" name="año" id="eq_anio" placeholder=" " inputmode="numeric" pattern="[0-9]{4}" maxlength="4"
                  value="{{ old('año', $servicio->año) }}">
                <label for="eq_anio">Año</label>
              </div>

              <div class="ux-float col-span-2">
                <textarea class="premium-input" name="descripcion" id="eq_desc" placeholder=" ">{{ old('descripcion', $servicio->descripcion) }}</textarea>
                <label for="eq_desc">Descripción</label>
              </div>

              <div class="ux-float">
                <input class="premium-input" type="date" name="fecha_inicial" id="eq_fecha" placeholder=" " required
                  value="{{ $fechaInicial }}">
                <label for="eq_fecha">Fecha de adquisición *</label>
              </div>

              <div class="ux-float">
                <input class="premium-input" type="text" name="nombre_doctor" id="nombre_doctor" placeholder=" "
                  value="{{ old('nombre_doctor', $servicio->nombre_doctor) }}">
                <label for="nombre_doctor">Médico / Solicitante</label>
              </div>

              <div class="ux-float col-span-2">
                <textarea class="premium-input" name="observaciones" id="eq_obs" placeholder=" ">{{ old('observaciones', $servicio->observaciones) }}</textarea>
                <label for="eq_obs">Observaciones</label>
              </div>

              <div class="ux-float col-span-2 has-value">
                <input class="premium-input" type="text" id="user_name" name="user_name" readonly placeholder=" "
                  value="{{ old('user_name', Auth::user()->name ?? $servicio->user_name) }}">
                <label for="user_name">Registrado por</label>
              </div>
            </div>

          </div>
        </div>

        <div class="card-premium">
          <div class="card-header-ux">
            <span><i class="bi bi-images"></i> Evidencias Multimedia</span>
          </div>
          <div class="card-body-ux">
            <div class="media-grid">

              <div class="tile" role="button" tabindex="0" @click="$refs.img0.click()">
                <input type="file" class="d-none" accept="image/*" name="evidencia1" x-ref="img0" @change="previewImg($event, 0)" style="display:none;">
                <template x-if="previews[0]"><img :src="previews[0]"></template>
                <template x-if="!previews[0]">
                  <div class="tile-empty">
                    <i class="bi bi-camera"></i>
                    <span>Foto 1</span>
                    <small>Toca para subir</small>
                  </div>
                </template>

                <button type="button" class="tile-remove" title="Eliminar" x-show="previews[0]" @click.stop="removeImage(0)">
                  <i class="bi bi-trash"></i>
                </button>

                <div class="tile-check" x-show="removeE1 === '1'">
                  Marcada para eliminar
                </div>

                <span class="tile-hint" x-show="previews[0]"><i class="bi bi-pencil-square"></i> Editar</span>
              </div>

              <div class="tile" role="button" tabindex="0" @click="$refs.img1.click()">
                <input type="file" class="d-none" accept="image/*" name="evidencia2" x-ref="img1" @change="previewImg($event, 1)" style="display:none;">
                <template x-if="previews[1]"><img :src="previews[1]"></template>
                <template x-if="!previews[1]">
                  <div class="tile-empty">
                    <i class="bi bi-camera"></i>
                    <span>Foto 2</span>
                    <small>Toca para subir</small>
                  </div>
                </template>

                <button type="button" class="tile-remove" title="Eliminar" x-show="previews[1]" @click.stop="removeImage(1)">
                  <i class="bi bi-trash"></i>
                </button>

                <div class="tile-check" x-show="removeE2 === '1'">
                  Marcada para eliminar
                </div>

                <span class="tile-hint" x-show="previews[1]"><i class="bi bi-pencil-square"></i> Editar</span>
              </div>

              <div class="tile" role="button" tabindex="0" @click="$refs.img2.click()">
                <input type="file" class="d-none" accept="image/*" name="evidencia3" x-ref="img2" @change="previewImg($event, 2)" style="display:none;">
                <template x-if="previews[2]"><img :src="previews[2]"></template>
                <template x-if="!previews[2]">
                  <div class="tile-empty">
                    <i class="bi bi-camera"></i>
                    <span>Foto 3</span>
                    <small>Toca para subir</small>
                  </div>
                </template>

                <button type="button" class="tile-remove" title="Eliminar" x-show="previews[2]" @click.stop="removeImage(2)">
                  <i class="bi bi-trash"></i>
                </button>

                <div class="tile-check" x-show="removeE3 === '1'">
                  Marcada para eliminar
                </div>

                <span class="tile-hint" x-show="previews[2]"><i class="bi bi-pencil-square"></i> Editar</span>
              </div>

              <div class="tile" role="button" tabindex="0" @click="$refs.vid.click()">
                <input type="file" class="d-none" accept="video/mp4,video/avi,video/mpeg,video/webm,video/quicktime,video/*" name="video" x-ref="vid" @change="previewVideo($event)" style="display:none;">
                <template x-if="videoUrl"><video :src="videoUrl" muted autoplay loop playsinline></video></template>
                <template x-if="!videoUrl">
                  <div class="tile-empty">
                    <i class="bi bi-camera-video"></i>
                    <span>Clip</span>
                    <small>Toca para subir</small>
                  </div>
                </template>

                <button type="button" class="tile-remove" title="Eliminar" x-show="videoUrl" @click.stop="removeVideoFile()">
                  <i class="bi bi-trash"></i>
                </button>

                <div class="tile-check" x-show="removeVideo === '1'">
                  Marcado para eliminar
                </div>

                <span class="tile-hint" x-show="videoUrl"><i class="bi bi-pencil-square"></i> Editar</span>
              </div>

            </div>
          </div>
        </div>

        <div class="card-premium">
          <div class="card-header-ux">
            <span><i class="bi bi-pen"></i> Firma Digital Actual</span>
            <span class="badge-soft" style="color: var(--danger); border-color: var(--danger-soft); background: var(--danger-soft);" x-show="removeFirma === '1'">Marcada para eliminar</span>
          </div>
          <div class="card-body-ux">
            @if($firmaUrl)
              <div class="media-frame" x-show="removeFirma !== '1'">
                <img src="{{ $firmaUrl }}" alt="Firma actual">
              </div>
            @else
              <div style="color: var(--muted); font-size: 0.95rem; margin-bottom: 16px;">No hay firma digital registrada en este servicio.</div>
            @endif

            <label class="custom-checkbox">
              <input type="checkbox" id="eliminar_firma_chk" @change="removeFirma = $event.target.checked ? '1' : '0'">
              Eliminar firma actual
            </label>

            <div style="margin-top: 16px; font-size: 0.85rem; color: var(--muted); line-height: 1.5;">
              <i class="bi bi-info-circle"></i> Esta vista respeta tu backend actual: aquí puedes eliminar la firma existente. Si luego quieres volver a capturar una nueva firma en edición, se adaptará el canvas correspondiente.
            </div>
          </div>
        </div>

      </div>

      <div class="d-none d-lg-block sticky-sidebar">
        <div class="card-premium">
          <div class="card-header-ux" style="color: var(--warn); background: var(--warn-soft); border-bottom-color: rgba(180, 83, 9, 0.2);">
            <span><i class="bi bi-lightbulb-fill"></i> Mejores Prácticas</span>
          </div>
          <div class="card-body-ux">
            <ul class="tips-list">
              <li><b>Tipo de equipo</b> y <b>subtipo de equipo</b> son campos libres.</li>
              <li>Puedes actualizar datos generales del equipo sin afectar el flujo del proceso.</li>
              <li>Si reemplazas evidencias, se subirán las nuevas automáticamente al guardar.</li>
              <li>Si eliminas evidencias, se enviarán con bandera de borrado al backend.</li>
              <li>La firma actual también puede eliminarse permanentemente desde esta vista.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="btn-actions">
      <a href="{{ route('servicios.show', $servicio->id) }}" class="btn btn-ghost">Cancelar</a>
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-save2"></i> Guardar cambios
      </button>
    </div>
  </form>
</div>

<script>
// Lógica para Inputs Flotantes
document.addEventListener('DOMContentLoaded', () => {
  const setState = el => {
    const wrapper = el.closest('.ux-float');
    if(wrapper) {
      wrapper.classList.toggle('has-value', !!el.value.trim());
    }
  };
  document.querySelectorAll('.premium-input').forEach(el => {
    setState(el);
    el.addEventListener('input', () => setState(el));
    el.addEventListener('change', () => setState(el));
    // Check inicial diferido por autofill
    setTimeout(() => setState(el), 150);
  });
});

// Lógica Alpine para Multimedia
function ServicioEditUI(){
  return {
    previews: [
      @json($img1Url),
      @json($img2Url),
      @json($img3Url)
    ],
    videoUrl: @json($videoUrl),

    removeE1: '{{ old('eliminar_evidencia1', '0') }}',
    removeE2: '{{ old('eliminar_evidencia2', '0') }}',
    removeE3: '{{ old('eliminar_evidencia3', '0') }}',
    removeVideo: '{{ old('eliminar_video', '0') }}',
    removeFirma: '{{ old('eliminar_firma', '0') }}',

    init(){
      if (this.removeE1 === '1') this.previews[0] = null;
      if (this.removeE2 === '1') this.previews[1] = null;
      if (this.removeE3 === '1') this.previews[2] = null;
      if (this.removeVideo === '1') this.videoUrl = null;
    },

    previewImg(e, i){
      const f = e.target.files?.[0];
      if(!f){ return; }

      const rd = new FileReader();
      rd.onload = ev => {
        this.previews[i] = ev.target.result;

        if(i === 0) this.removeE1 = '0';
        if(i === 1) this.removeE2 = '0';
        if(i === 2) this.removeE3 = '0';
      };
      rd.readAsDataURL(f);
    },

    previewVideo(e){
      const f = e.target.files?.[0];
      if(this.videoUrl && String(this.videoUrl).startsWith('blob:')) {
        URL.revokeObjectURL(this.videoUrl);
      }
      this.videoUrl = f ? URL.createObjectURL(f) : null;
      if (f) this.removeVideo = '0';
    },

    removeImage(i){
      this.previews[i] = null;

      if(i === 0){
        this.removeE1 = '1';
        this.$refs.img0.value = '';
      }
      if(i === 1){
        this.removeE2 = '1';
        this.$refs.img1.value = '';
      }
      if(i === 2){
        this.removeE3 = '1';
        this.$refs.img2.value = '';
      }
    },

    removeVideoFile(){
      if(this.videoUrl && String(this.videoUrl).startsWith('blob:')) {
        URL.revokeObjectURL(this.videoUrl);
      }
      this.videoUrl = null;
      this.removeVideo = '1';
      this.$refs.vid.value = '';
    }
  }
}
</script>
@endsection