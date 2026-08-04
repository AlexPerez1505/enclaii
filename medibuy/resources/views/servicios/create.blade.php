@extends('layouts.app')
@section('title', 'Servicio')
@section('titulo', 'Registro de servicio')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
:root {
  --bg-app: #f8fafc;
  --surface: #ffffff;
  --ink-dark: #0f172a;
  --ink-base: #334155;
  --ink-muted: #64748b;
  --border-subtle: #e2e8f0;
  --border-focus: #94a3b8;
  --brand-primary: #0f172a;
  --brand-surface: #f1f5f9;
  --brand-accent: #2563eb;
  --warn-bg: #fffbeb;
  --warn-border: #fef08a;
  --warn-text: #854d0e;

  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --shadow-premium: 0 10px 30px -5px rgba(15, 23, 42, 0.08);
}

body {
  background: var(--bg-app);
  font-family: 'Inter', system-ui, sans-serif;
  color: var(--ink-base);
}

.page-header { margin-bottom: 2rem; }
.heading-xl {
  font-weight: 800;
  font-size: clamp(1.5rem, 3vw, 2rem);
  color: var(--ink-dark);
  letter-spacing: -0.02em;
}
.text-muted-ux {
  color: var(--ink-muted);
  font-size: 0.95rem;
}

.card-premium {
  background: var(--surface);
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-premium);
  margin-bottom: 1.5rem;
  overflow: hidden;
}
.card-header-ux {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--border-subtle);
  background: #fafaf9;
  font-weight: 700;
  color: var(--ink-dark);
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.card-body-ux { padding: 1.5rem; }

.btn-premium {
  background: var(--brand-primary);
  color: #fff;
  border: none;
  border-radius: var(--radius-sm);
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.2s ease;
  box-shadow: var(--shadow-sm);
}
.btn-premium:hover {
  background: #1e293b;
  color: #fff;
  transform: translateY(-1px);
}
.btn-ghost-ux {
  background: var(--surface);
  border: 1px solid var(--border-subtle);
  color: var(--ink-dark);
  border-radius: var(--radius-sm);
  padding: 0.75rem 1.5rem;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.2s ease;
}
.btn-ghost-ux:hover {
  background: var(--brand-surface);
  color: var(--ink-dark);
}

.form-control, .form-select {
  border-radius: var(--radius-sm);
  border: 1px solid var(--border-subtle);
  background-color: var(--surface);
  color: var(--ink-dark);
  font-size: 0.95rem;
  box-shadow: var(--shadow-sm);
  transition: border-color 0.2s, box-shadow 0.2s;
}
.form-control:focus, .form-select:focus {
  border-color: var(--brand-accent);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.ux-float { position: relative; }
.ux-float .form-control, .ux-float .form-select {
  padding: 1.5rem 1rem 0.5rem;
  height: auto;
}
.ux-float label {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  font-weight: 500;
  color: var(--ink-muted);
  pointer-events: none;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  margin: 0;
  font-size: 0.95rem;
}
.ux-float:focus-within label,
.ux-float.has-value label,
.ux-float .form-control:not(:placeholder-shown)+label {
  top: 0.8rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--brand-accent);
}
.ux-float textarea.form-control {
  min-height: 120px;
  padding-top: 1.75rem;
}

.note-warn {
  background: var(--warn-bg);
  border: 1px solid var(--warn-border);
  color: var(--warn-text);
  border-radius: var(--radius-md);
  padding: 1rem 1.25rem;
  font-size: 0.95rem;
  display: flex;
  gap: 0.75rem;
  align-items: flex-start;
  box-shadow: var(--shadow-sm);
}
.note-warn i { font-size: 1.2rem; margin-top: -2px; }

.segmented {
  display: flex;
  gap: 0.5rem;
  background: var(--brand-surface);
  padding: 0.35rem;
  border-radius: var(--radius-sm);
  border: 1px solid var(--border-subtle);
}
.segmented input { display: none; }
.segmented label {
  flex: 1;
  text-align: center;
  padding: 0.6rem 1rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.9rem;
  color: var(--ink-muted);
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}
.segmented input:checked + label {
  background: var(--surface);
  color: var(--ink-dark);
  box-shadow: var(--shadow-sm);
}

.tile {
  position: relative;
  width: 100%;
  aspect-ratio: 1/1;
  border: 1px dashed var(--border-focus);
  border-radius: var(--radius-sm);
  background: #fafaf9;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s ease;
}
.tile:hover {
  background: var(--brand-surface);
  border-color: var(--brand-accent);
}
.tile img, .tile video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  position: absolute;
  inset: 0;
}
.tile-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  color: var(--ink-muted);
}
.tile-empty i { font-size: 1.5rem; color: var(--border-focus); }
.tile-empty span { font-weight: 600; font-size: 0.9rem; color: var(--ink-base); }
.tile-empty small { font-size: 0.75rem; }
.tile-hint {
  position: absolute;
  bottom: 8px;
  right: 8px;
  background: rgba(255,255,255,0.9);
  backdrop-filter: blur(4px);
  border: 1px solid var(--border-subtle);
  border-radius: 4px;
  padding: 0.2rem 0.5rem;
  font-size: 0.7rem;
  font-weight: 600;
  color: var(--ink-dark);
  z-index: 10;
}

.canvas-wrapper {
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-sm);
  background: var(--surface);
  position: relative;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
}
.canvas-baseline {
  position: absolute;
  bottom: 25%;
  left: 10%;
  right: 10%;
  border-bottom: 2px dotted var(--border-subtle);
  pointer-events: none;
}
.canvas-watermark {
  position: absolute;
  bottom: 15%;
  left: 10%;
  color: var(--border-focus);
  font-size: 0.8rem;
  font-weight: 600;
  pointer-events: none;
}

.badge-soft {
  background: var(--brand-surface);
  color: var(--ink-base);
  border: 1px solid var(--border-subtle);
  border-radius: 6px;
  padding: 0.25rem 0.6rem;
  font-weight: 600;
  font-size: 0.75rem;
}

[x-cloak] { display: none !important; }
.loader-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.4);
  backdrop-filter: blur(8px);
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
}
.loader-box {
  background: var(--surface);
  border-radius: var(--radius-md);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  padding: 2rem;
  width: 100%;
  max-width: 420px;
  border: 1px solid var(--border-subtle);
}
.loader-title {
  font-weight: 700;
  color: var(--ink-dark);
  font-size: 1.1rem;
}
.loader-step {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-top: 0.75rem;
  font-size: 0.9rem;
  color: var(--ink-muted);
  font-weight: 500;
}
.loader-step i { font-size: 1.1rem; }
.loader-step.active { color: var(--brand-accent); font-weight: 600; }
.loader-step.done { color: #16a34a; }
.spinner-border-sm { width: 1.2rem; height: 1.2rem; border-width: 0.15em; }
</style>

<div class="container py-5" x-data="ServicioUI()" x-init="init()">

  <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
      <h1 class="heading-xl mb-1">Registrar Servicio</h1>
      <p class="text-muted-ux mb-0">
        Captura la información del equipo y adjunta las evidencias correspondientes.
        <span class="badge-soft ms-md-2 mt-2 mt-md-0 d-inline-block">Flujo obligatorio por proceso</span>
      </p>
    </div>
    <a href="{{ url('/inventario/servicio') }}" class="btn-ghost-ux d-inline-flex align-items-center gap-2 text-decoration-none">
      <i class="bi bi-arrow-left"></i> Volver
    </a>
  </div>

  <form id="servicioForm" enctype="multipart/form-data" @submit.prevent="submit" method="POST" action="{{ route('servicio.store') }}">
    @csrf
    <input type="hidden" name="firmaDigital" x-model="firmaData">

    <div class="note-warn mb-4">
      <i class="bi bi-info-circle-fill"></i>
      <div>
        <strong>Flujo obligatorio:</strong>
        al registrar el servicio, si el mantenimiento es <b>interno</b> el siguiente paso será <b>Validar OS → Salida a entrega</b>.
        Si es <b>externo</b>, el flujo será <b>Salida a mantenimiento foráneo → Regreso → Validar OS → Salida para cliente</b>.
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-8">

        <div class="card-premium">
          <div class="card-header-ux">
            <i class="bi bi-card-text text-muted"></i> Información del Equipo
          </div>
          <div class="card-body-ux">
            <div class="row g-4">

              <div class="col-12">
                <label class="form-label fw-semibold text-muted-ux mb-2 d-block">Modalidad del servicio <span class="text-danger">*</span></label>
                <div class="segmented">
                  <input type="radio" name="mantenimiento_tipo" id="tipo_interno" value="interno" {{ old('mantenimiento_tipo', 'interno') === 'interno' ? 'checked' : '' }}>
                  <label for="tipo_interno"><i class="bi bi-building"></i> Interno</label>

                  <input type="radio" name="mantenimiento_tipo" id="tipo_externo" value="externo" {{ old('mantenimiento_tipo') === 'externo' ? 'checked' : '' }}>
                  <label for="tipo_externo"><i class="bi bi-box-arrow-up-right"></i> Externo</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="ux-float">
                  <input class="form-control" type="text" name="tipo_equipo" id="tipo_equipo" placeholder=" " required value="{{ old('tipo_equipo') }}">
                  <label for="tipo_equipo">Tipo de equipo *</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="ux-float">
                  <input class="form-control" type="text" name="subtipo_equipo" id="subtipo_equipo" placeholder=" " required value="{{ old('subtipo_equipo') }}">
                  <label for="subtipo_equipo">Subtipo de equipo *</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="ux-float">
                  <input class="form-control" type="text" name="marca" id="eq_marca" placeholder=" " value="{{ old('marca') }}">
                  <label for="eq_marca">Marca</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="ux-float">
                  <input class="form-control" type="text" name="modelo" id="eq_modelo" placeholder=" " value="{{ old('modelo') }}">
                  <label for="eq_modelo">Modelo</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="ux-float">
                  <input class="form-control" type="text" name="numero_serie" id="eq_serie" placeholder=" " value="{{ old('numero_serie') }}">
                  <label for="eq_serie">Número de serie</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="ux-float">
                  <input class="form-control" type="text" name="año" id="eq_anio" placeholder=" " inputmode="numeric" pattern="[0-9]{4}" maxlength="4" value="{{ old('año') }}">
                  <label for="eq_anio">Año</label>
                </div>
              </div>

              <div class="col-12">
                <div class="ux-float">
                  <textarea class="form-control" name="descripcion" id="eq_desc" placeholder=" ">{{ old('descripcion') }}</textarea>
                  <label for="eq_desc">Descripción</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="ux-float">
                  <input class="form-control" type="date" name="fecha_inicial" id="eq_fecha" placeholder=" " required value="{{ old('fecha_inicial') }}">
                  <label for="eq_fecha">Fecha de adquisición *</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="ux-float">
                  <input class="form-control" type="text" name="nombre_doctor" id="nombre_doctor" placeholder=" " value="{{ old('nombre_doctor') }}">
                  <label for="nombre_doctor">Médico / Solicitante</label>
                </div>
              </div>

              <div class="col-12">
                <div class="ux-float">
                  <textarea class="form-control" name="observaciones" id="eq_obs" placeholder=" " rows="2">{{ old('observaciones') }}</textarea>
                  <label for="eq_obs">Observaciones</label>
                </div>
              </div>

              @auth
              <div class="col-12">
                <div class="ux-float has-value">
                  <input class="form-control bg-light" type="text" id="user_name" name="user_name" value="{{ Auth::user()->name }}" readonly placeholder=" ">
                  <label for="user_name">Registrado por</label>
                </div>
              </div>
              @endauth

            </div>
          </div>
        </div>

        <div class="card-premium">
          <div class="card-header-ux d-flex justify-content-between">
            <div><i class="bi bi-images text-muted"></i> Evidencias Multimedia</div>
            <span class="badge-soft fw-normal">Max. 3 Fotos / 1 Video</span>
          </div>
          <div class="card-body-ux">
            <div class="row g-3">

              <div class="col-6 col-sm-3">
                <div class="tile" role="button" tabindex="0" @click="$refs.img0.click()">
                  <input type="file" class="d-none" accept="image/*" name="evidencia1" x-ref="img0" @change="previewImg($event, 0)">
                  <template x-if="previews[0]"><img :src="previews[0]"></template>
                  <template x-if="!previews[0]">
                    <div class="tile-empty">
                      <i class="bi bi-camera"></i>
                      <span>Foto 1</span>
                      <small>Toca para subir</small>
                    </div>
                  </template>
                  <span class="tile-hint" x-show="previews[0]"><i class="bi bi-pencil-square"></i></span>
                </div>
              </div>

              <div class="col-6 col-sm-3">
                <div class="tile" role="button" tabindex="0" @click="$refs.img1.click()">
                  <input type="file" class="d-none" accept="image/*" name="evidencia2" x-ref="img1" @change="previewImg($event, 1)">
                  <template x-if="previews[1]"><img :src="previews[1]"></template>
                  <template x-if="!previews[1]">
                    <div class="tile-empty">
                      <i class="bi bi-camera"></i>
                      <span>Foto 2</span>
                      <small>Toca para subir</small>
                    </div>
                  </template>
                  <span class="tile-hint" x-show="previews[1]"><i class="bi bi-pencil-square"></i></span>
                </div>
              </div>

              <div class="col-6 col-sm-3">
                <div class="tile" role="button" tabindex="0" @click="$refs.img2.click()">
                  <input type="file" class="d-none" accept="image/*" name="evidencia3" x-ref="img2" @change="previewImg($event, 2)">
                  <template x-if="previews[2]"><img :src="previews[2]"></template>
                  <template x-if="!previews[2]">
                    <div class="tile-empty">
                      <i class="bi bi-camera"></i>
                      <span>Foto 3</span>
                      <small>Toca para subir</small>
                    </div>
                  </template>
                  <span class="tile-hint" x-show="previews[2]"><i class="bi bi-pencil-square"></i></span>
                </div>
              </div>

              <div class="col-6 col-sm-3">
                <div class="tile" role="button" tabindex="0" @click="$refs.vid.click()">
                  <input type="file" class="d-none" accept="video/mp4,video/avi,video/mpeg,video/webm,video/quicktime,video/*" name="video" x-ref="vid" @change="previewVideo($event)">
                  <template x-if="videoUrl"><video :src="videoUrl" muted autoplay loop playsinline></video></template>
                  <template x-if="!videoUrl">
                    <div class="tile-empty">
                      <i class="bi bi-camera-video"></i>
                      <span>Clip</span>
                      <small>Toca para subir</small>
                    </div>
                  </template>
                  <span class="tile-hint" x-show="videoUrl"><i class="bi bi-pencil-square"></i></span>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="card-premium">
          <div class="card-header-ux d-flex justify-content-between align-items-center">
            <div><i class="bi bi-pen text-muted"></i> Autenticación</div>
            <span class="badge-soft text-primary border-primary bg-transparent" x-show="isDrawing">Capturando...</span>
          </div>
          <div class="card-body-ux">
            <label class="form-label fw-semibold text-muted-ux mb-3">Firma Digital del Responsable <span class="text-danger">*</span></label>
            <div class="canvas-wrapper">
              <div class="canvas-baseline"></div>
              <div class="canvas-watermark">Firme aquí</div>
              <canvas x-ref="canvas" style="width:100%; height:180px; display:block; position:relative; z-index:2; cursor:crosshair;"></canvas>
            </div>
            <div class="mt-3 text-end">
              <button type="button" class="btn-ghost-ux py-1 px-3" style="font-size:0.85rem;" @click="clearSig()">
                <i class="bi bi-eraser"></i> Limpiar lienzo
              </button>
            </div>
          </div>
        </div>

      </div>

      <div class="col-lg-4 d-none d-lg-block">
        <div class="card-premium position-sticky" style="top: 2rem;">
          <div class="card-header-ux"><i class="bi bi-lightbulb text-warning"></i> Mejores Prácticas</div>
          <div class="card-body-ux">
            <ul class="text-muted-ux small mb-0 ps-3" style="line-height: 1.8;">
              <li>Tipo de equipo y subtipo de equipo son campos de texto libre.</li>
              <li>Clasifica correctamente entre servicio <b>interno</b> o <b>externo</b>.</li>
              <li>La firma digital es obligatoria.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-3 mt-2 mb-5">
      <a href="{{ url('/inventario/servicio') }}" class="btn-ghost-ux text-decoration-none">Cancelar</a>
      <button type="submit" class="btn-premium">
        <i class="bi bi-check2-circle me-1"></i> Confirmar y Guardar
      </button>
    </div>
  </form>

  <div id="toastPlace" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080"></div>

  <div class="loader-backdrop" x-show="loading" x-cloak x-transition.opacity>
    <div class="loader-box">
      <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
        <div class="loader-title mb-0">Procesando Registro</div>
      </div>
      <div>
        <template x-for="(txt, idx) in loadingTexts" :key="'step-'+idx">
          <div class="loader-step" :class="{'active': idx === loadingStep, 'done': idx < loadingStep}">
            <template x-if="idx < loadingStep"><i class="bi bi-check-circle-fill"></i></template>
            <template x-if="idx === loadingStep"><i class="bi bi-arrow-right-circle text-primary"></i></template>
            <template x-if="idx > loadingStep"><i class="bi bi-circle"></i></template>
            <span x-text="txt"></span>
          </div>
        </template>
      </div>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const setState = el => el.closest('.ux-float')?.classList.toggle('has-value', !!el.value);
  document.querySelectorAll('.ux-float .form-control, .ux-float .form-select').forEach(el => {
    setState(el);
    el.addEventListener('input', () => setState(el));
    el.addEventListener('change', () => setState(el));
    setTimeout(() => setState(el), 100);
  });
});
</script>

<script>
function ServicioUI(){
  return {
    previews:[null,null,null],
    videoUrl:null,
    firmaData:'',
    isDrawing:false,
    loading:false,
    loadingStep:0,
    loadingTexts:[
      'Validando esquemas de datos',
      'Registrando servicio en base de datos',
      'Procesando evidencias multimedia',
      'Cifrando firma digital',
      'Redirigiendo al proceso obligatorio'
    ],
    loadingTimer:null,

    init(){
      this.$nextTick(()=>{ this.initCanvas(); });
    },

    previewImg(e, i){
      const f = e.target.files?.[0];
      if(!f){ this.previews[i]=null; return; }
      const rd = new FileReader();
      rd.onload = ev => this.previews[i] = ev.target.result;
      rd.readAsDataURL(f);
    },

    previewVideo(e){
      const f = e.target.files?.[0];
      if(this.videoUrl) URL.revokeObjectURL(this.videoUrl);
      this.videoUrl = f ? URL.createObjectURL(f) : null;
    },

    initCanvas(){
      const canvas=this.$refs.canvas, dpr=window.devicePixelRatio||1;
      const rect=canvas.getBoundingClientRect();
      canvas.width=rect.width*dpr;
      canvas.height=180*dpr;

      const ctx=canvas.getContext('2d');
      ctx.scale(dpr,dpr);
      ctx.lineWidth=2.5;
      ctx.lineCap='round';
      ctx.lineJoin='round';
      ctx.strokeStyle='#0f172a';

      ctx.clearRect(0,0, canvas.width, canvas.height);

      let draw=false, last=null;
      const pos=e=>{
        const b=canvas.getBoundingClientRect();
        const x=(e.touches?e.touches[0].clientX:e.clientX)-b.left;
        const y=(e.touches?e.touches[0].clientY:e.clientY)-b.top;
        return {x,y};
      };

      const start=e=>{
        draw=true;
        last=pos(e);
        this.isDrawing=true;
        e.preventDefault();
      };

      const move=e=>{
        if(!draw) return;
        const p=pos(e);
        ctx.beginPath();
        ctx.moveTo(last.x,last.y);
        ctx.lineTo(p.x,p.y);
        ctx.stroke();
        last=p;
        e.preventDefault();
      };

      const end=()=>{
        if(draw){
          draw=false;
          this.isDrawing=false;
          this.firmaData=canvas.toDataURL('image/png');
        }
      };

      canvas.addEventListener('mousedown',start);
      canvas.addEventListener('mousemove',move);
      window.addEventListener('mouseup',end);
      canvas.addEventListener('touchstart',start,{passive:false});
      canvas.addEventListener('touchmove',move,{passive:false});
      canvas.addEventListener('touchend',end);

      window.addEventListener('resize', ()=>this.initCanvas(), {once:true});
    },

    clearSig(){
      const c=this.$refs.canvas, ctx=c.getContext('2d');
      ctx.clearRect(0,0,c.width,c.height);
      this.firmaData='';
    },

    startLoader(){
      this.loading = true;
      this.loadingStep = 0;
      if(this.loadingTimer) clearInterval(this.loadingTimer);
      const total = this.loadingTexts.length;
      this.loadingTimer = setInterval(() => {
        if(this.loadingStep < total - 1) this.loadingStep++;
      }, 1200);
    },

    stopLoader(){
      this.loading = false;
      if(this.loadingTimer){
        clearInterval(this.loadingTimer);
        this.loadingTimer = null;
      }
    },

    async submit(){
      if (!this.firmaData) {
        this.toast('Es obligatorio estampar la firma digital para proceder.', false);
        return;
      }

      const tipoMantenimiento = document.querySelector('input[name="mantenimiento_tipo"]:checked')?.value;
      if (!tipoMantenimiento) {
        this.toast('Debe especificar la modalidad del servicio (Interno/Externo).', false);
        return;
      }

      const tipo = document.getElementById('tipo_equipo')?.value?.trim();
      const subtipo = document.getElementById('subtipo_equipo')?.value?.trim();

      if (!tipo) {
        this.toast('Debe capturar el tipo de equipo.', false);
        return;
      }

      if (!subtipo) {
        this.toast('Debe capturar el subtipo de equipo.', false);
        return;
      }

      const fd = new FormData(document.getElementById('servicioForm'));
      const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      this.startLoader();

      try{
        const res = await fetch('{{ route('servicio.store') }}', {
          method : 'POST',
          headers: {
            'Accept':'application/json',
            'X-CSRF-TOKEN': csrf
          },
          body : fd,
          credentials: 'same-origin'
        });

        const raw = await res.text();
        let json = null;
        try { json = JSON.parse(raw); } catch(e) {}

        if(res.status === 419){
          this.stopLoader();
          this.toast('Sesión de seguridad caducada. Recargue la página.', false);
          return;
        }

        if(!res.ok || !json || !json.success){
          this.stopLoader();
          let msg = json?.error || (json?.errors ? Object.values(json.errors).flat().join(' | ') : 'Error al procesar el registro.');
          this.toast(msg, false);
          return;
        }

        this.stopLoader();
        this.toast(json.message || 'Servicio registrado con éxito.', true);

        setTimeout(()=>{
          window.location.href = json.next_url || "{{ url('/inventario/servicio') }}";
        }, 1000);

      } catch(e) {
        this.stopLoader();
        this.toast('Error de conexión con el servidor.', false);
      }
    },

    toast(msg, ok=true){
      const el=document.createElement('div');
      el.className=`toast align-items-center text-bg-${ok?'success':'danger'} border-0 shadow-lg`;
      el.setAttribute('role','alert');
      el.setAttribute('aria-live','assertive');
      el.setAttribute('aria-atomic','true');
      el.innerHTML=`
        <div class="d-flex px-1 py-1">
          <div class="toast-body fw-medium">${this.escape(msg)}</div>
          <button type="button" class="btn-close btn-close-white ms-auto m-2" data-bs-dismiss="toast"></button>
        </div>`;
      document.getElementById('toastPlace').appendChild(el);
      const t=new bootstrap.Toast(el,{delay:4000});
      t.show();
      el.addEventListener('hidden.bs.toast',()=>el.remove());
    },

    escape(s){
      return (s||'').replace(/[&<>"']/g, m=>({
        '&':'&amp;',
        '<':'&lt;',
        '>':'&gt;',
        '"':'&quot;',
        "'":'&#39;'
      }[m]))
    }
  }
}
</script>
@endsection