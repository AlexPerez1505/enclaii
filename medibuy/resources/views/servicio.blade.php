@extends('layouts.app')
@section('title', 'Servicio')
@section('titulo', 'Registro de servicio')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
:root{
  --bg:#f6f8fb;
  --ink:#0f172a;
  --muted:#6b7280;
  --line:#e7ebf0;
  --brand:#cfeee7;
  --brand-ink:#145b56;
  --warn:#fff7ed;
  --warn-ink:#9a3412;
}
body{
  background:var(--bg);
  font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif
}
.card-soft{
  background:#fff;
  border:1px solid var(--line);
  border-radius:16px;
  box-shadow:0 12px 40px rgba(17,24,39,.06)
}
.heading-xl{
  font-weight:800;
  font-size:clamp(22px,3vw,30px);
  color:var(--ink)
}
.text-muted-ux{ color:var(--muted) }
.btn-pastel{
  border:none;
  border-radius:12px;
  padding:.7rem 1.1rem;
  font-weight:700;
  color:#0b3634;
  background:var(--brand)
}
.btn-ghost{
  border-radius:12px;
  border:1px solid var(--line);
  background:#fff;
  color:var(--ink);
  font-weight:700
}

.form-control,.form-select{
  border-radius:12px;
  border:1px solid var(--line);
  padding:.9rem 1rem
}
.form-control:focus,.form-select:focus{
  border-color:var(--brand-ink);
  box-shadow:none
}

.ux-float{ position:relative }
.ux-float .form-control,.ux-float .form-select{
  padding:1.25rem 1rem .55rem
}
.ux-float label{
  position:absolute;
  left:12px;
  top:50%;
  transform:translateY(-50%);
  font-weight:600;
  color:#93a1b2;
  pointer-events:none;
  transition:all .16s ease
}
.ux-float:focus-within label,
.ux-float.has-value label,
.ux-float .form-control:not(:placeholder-shown)+label{
  top:.45rem;
  transform:none;
  font-size:.75rem;
  color:var(--brand-ink)
}
.ux-float textarea.form-control{
  min-height:110px;
  padding-top:1.6rem
}

/* Switch */
.switch-card{
  border:1px solid var(--line);
  border-radius:14px;
  padding:12px 14px;
  background:#fff;
}
.switch-row{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
}
.switch-title{ font-weight:800; color:var(--ink); }
.switch-sub{ font-size:.88rem; color:var(--muted); margin-top:2px; }
.form-switch .form-check-input{
  width:3.2rem; height:1.55rem;
  cursor:pointer;
}
.note-warn{
  margin-top:10px;
  background:var(--warn);
  color:var(--warn-ink);
  border:1px solid #fed7aa;
  border-radius:14px;
  padding:10px 12px;
  font-weight:700;
  font-size:.9rem;
  display:flex;
  gap:10px;
  align-items:flex-start;
}
.note-warn i{ font-size:1.05rem; margin-top:2px; }

/* Upload zone */
.upload-zone{
  border:1px dashed var(--line);
  border-radius:14px;
  padding:1rem;
  background:#fff
}
.tile{
  position:relative;
  width:100%;
  aspect-ratio:1/1;
  border:1px solid var(--line);
  border-radius:12px;
  background:#fafcff;
  display:grid;
  place-items:center;
  overflow:hidden;
  cursor:pointer;
  transition:transform .08s ease, box-shadow .08s ease;
}
.tile:active{ transform:scale(.995) }
.tile:hover{ box-shadow:0 8px 24px rgba(17,24,39,.06) }
.tile img,.tile video{
  width:100%;
  height:100%;
  object-fit:cover;
  pointer-events:none
}
.tile-hint{
  position:absolute;
  inset:auto 8px 8px auto;
  background:#ffffffd9;
  border:1px solid var(--line);
  border-radius:999px;
  padding:.2rem .6rem;
  font-size:.75rem;
  font-weight:700;
  color:var(--brand-ink)
}
.tile-empty{
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:.35rem;
  color:#94a3b8;
  font-weight:600;
  font-size:.95rem
}
.tile-empty i{ font-size:1.25rem }

/* Badge */
.badge-soft{
  background:#e9fbf7;
  color:var(--brand-ink);
  border-radius:999px;
  padding:.25rem .55rem;
  font-weight:700;
  font-size:.75rem
}

/* Toasts */
#toastPlace{
  position:fixed;
  top:1rem;
  right:1rem;
  z-index:1080
}

/* Loader */
[x-cloak]{ display:none !important; }
.loader-backdrop{
  position:fixed;
  inset:0;
  background:rgba(15,23,42,.55);
  backdrop-filter:blur(18px);
  z-index:2000;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:1.5rem;
}
.loader-box{
  position:relative;
  width:100%;
  max-width:460px;
  background:#ffffffee;
  border-radius:20px;
  border:1px solid rgba(248,250,252,.7);
  box-shadow:0 24px 80px rgba(15,23,42,.35);
  padding:1.5rem 1.75rem;
}
.loader-title{
  font-weight:800;
  color:var(--brand-ink);
  font-size:1.05rem;
}
.loader-steps{ margin-top:.75rem; }
.loader-step{
  display:flex;
  align-items:flex-start;
  gap:.55rem;
  margin-bottom:.35rem;
  font-size:.9rem;
  color:var(--muted);
  opacity:.35;
  transition:opacity .3s ease, transform .3s ease;
}
.loader-step-icon{
  width:1.25rem;
  height:1.25rem;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  border-radius:999px;
  border:1px solid rgba(148,163,184,.7);
  font-size:.7rem;
  background:#fff;
}
.loader-step.active{
  opacity:1;
  color:var(--brand-ink);
}
.loader-step.active .loader-step-icon{
  background:var(--brand);
  border-color:var(--brand-ink);
  color:var(--brand-ink);
}
.loader-step.done{
  opacity:.85;
  color:#15803d;
}
.loader-step.done .loader-step-icon{
  background:#22c55e1a;
  border-color:#22c55e;
  color:#22c55e;
}
.loader-note{
  font-size:.78rem;
  color:var(--muted);
  margin-top:.25rem;
}
.spinner-soft{
  width:20px;
  height:20px;
  border-radius:999px;
  border:2px solid rgba(148,163,184,.4);
  border-top-color:var(--brand-ink);
  animation:spin .8s linear infinite;
}
@keyframes spin{ to{ transform:rotate(360deg); } }
</style>

<div class="container py-4" x-data="ServicioUI()" x-init="init()">
  <div class="card-soft p-3 p-md-4">
    <div class="d-flex align-items-center justify-content-between pb-3 border-bottom">
      <div>
        <div class="heading-xl">Registrar servicio</div>
        <div class="text-muted-ux">
          Captura la información del equipo, adjunta evidencias y firma el registro.
          <span class="badge-soft ms-2">Tip: usa fotos claras para agilizar la revisión.</span>
        </div>
      </div>
      <a href="{{ url('/inventario/servicio') }}" class="btn btn-ghost d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i> Volver al inventario
      </a>
    </div>

    <form id="servicioForm" class="mt-4" enctype="multipart/form-data" @submit.prevent="submit" method="POST" action="{{ route('servicio.store') }}">
      @csrf

      <input type="hidden" name="firmaDigital" x-model="firmaData">
      <input type="hidden" name="mantenimiento_tipo" :value="mantenimientoTipo">

      {{-- Switch Interno/Externo --}}
      <div class="switch-card mb-4">
        <div class="switch-row">
          <div>
            <div class="switch-title">
              Tipo de mantenimiento:
              <span class="badge-soft ms-1" x-text="mantenimientoTipo === 'interno' ? 'INTERNO' : 'EXTERNO'"></span>
            </div>
            <div class="switch-sub">
              Externo = flujo normal con movimientos. Interno = requiere validar Orden de Servicio (OS) obligatoria.
            </div>
          </div>

          <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="swInterno"
                   x-model="isInterno"
                   @change="mantenimientoTipo = isInterno ? 'interno' : 'externo'">
          </div>
        </div>

        <div class="note-warn" x-show="mantenimientoTipo === 'interno'">
          <i class="bi bi-exclamation-triangle"></i>
          <div>
            Este servicio es <b>INTERNO</b>. Al guardar, el sistema te obligará a <b>validar el número de OS</b>.
            Sin OS validada, el servicio queda en “REQUIERE OS”.
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-8">
          <div class="row g-3">
            {{-- Tipo --}}
            <div class="col-md-6">
              <div class="ux-float" :class="{'has-value': tipoSel}">
                <select class="form-select"
                        name="tipo_equipo"
                        id="tipo_equipo"
                        x-model="tipoSel"
                        @change="onTipoChange"
                        required>
                  <option value="" hidden></option>
                  <template x-for="t in tiposKeys" :key="'t-'+t">
                    <option :value="t" x-text="titleCase(t)"></option>
                  </template>
                </select>
                <label for="tipo_equipo">Tipo de equipo *</label>
              </div>
            </div>

            {{-- Subtipo (select) ✅ usando x-if para NO duplicar name --}}
            <template x-if="tipoSel && tipoSel !== 'otros'">
              <div class="col-md-6">
                <div class="ux-float" :class="{'has-value': subtipoSel}">
                  <select class="form-select"
                          name="subtipo_equipo"
                          id="subtipo_equipo"
                          x-model="subtipoSel"
                          required>
                    <option value="" hidden></option>
                    <template x-for="s in subtiposArr" :key="'s-'+s">
                      <option :value="s.toLowerCase().replace(/\s+/g,'_')" x-text="s"></option>
                    </template>
                  </select>
                  <label for="subtipo_equipo">Subtipo de equipo *</label>
                </div>
              </div>
            </template>

            {{-- Subtipo (otros) ✅ manda subtip_equipo directo --}}
            <template x-if="tipoSel === 'otros'">
              <div class="col-md-6">
                <div class="ux-float" :class="{'has-value': subtipoOtro}">
                  <input class="form-control"
                         type="text"
                         id="subtipoEquipoOtro"
                         name="subtipo_equipo"
                         placeholder=" "
                         x-model="subtipoOtro"
                         required>
                  <label for="subtipoEquipoOtro">Especifica el subtipo *</label>
                </div>
              </div>
            </template>

            {{-- Marca --}}
            <div class="col-md-6">
              <div class="ux-float">
                <input class="form-control"
                       type="text"
                       name="marca"
                       id="eq_marca"
                       placeholder=" "
                       required
                       value="{{ old('marca') }}">
                <label for="eq_marca">Marca *</label>
              </div>
            </div>

            {{-- Modelo --}}
            <div class="col-md-6">
              <div class="ux-float">
                <input class="form-control"
                       type="text"
                       name="modelo"
                       id="eq_modelo"
                       placeholder=" "
                       required
                       value="{{ old('modelo') }}">
                <label for="eq_modelo">Modelo *</label>
              </div>
            </div>

            {{-- Número de serie --}}
            <div class="col-md-6">
              <div class="ux-float">
                <input class="form-control"
                       type="text"
                       name="numero_serie"
                       id="eq_serie"
                       placeholder=" "
                       required
                       value="{{ old('numero_serie') }}">
                <label for="eq_serie">Número de serie *</label>
              </div>
            </div>

            {{-- Año --}}
            <div class="col-md-6">
              <div class="ux-float">
                <input class="form-control"
                       type="text"
                       name="año"
                       id="eq_anio"
                       placeholder=" "
                       inputmode="numeric"
                       pattern="[0-9]{4}"
                       maxlength="4"
                       title="El año debe ser un número de 4 dígitos"
                       value="{{ old('año') }}">
                <label for="eq_anio">Año</label>
              </div>
            </div>

            {{-- Descripción --}}
            <div class="col-12">
              <div class="ux-float">
                <textarea class="form-control"
                          name="descripcion"
                          id="eq_desc"
                          placeholder=" "
                          required>{{ old('descripcion') }}</textarea>
                <label for="eq_desc">Descripción del equipo *</label>
              </div>
            </div>

            {{-- Fecha adquisición --}}
            <div class="col-md-6">
              <div class="ux-float">
                <input class="form-control"
                       type="date"
                       name="fecha_inicial"
                       id="eq_fecha"
                       placeholder=" "
                       required
                       value="{{ old('fecha_inicial') }}">
                <label for="eq_fecha">Fecha de adquisición *</label>
              </div>
            </div>

            {{-- Observaciones --}}
            <div class="col-12">
              <div class="ux-float">
                <textarea class="form-control"
                          name="observaciones"
                          id="eq_obs"
                          placeholder=" "
                          rows="3"
                          required>{{ old('observaciones') }}</textarea>
                <label for="eq_obs">Observaciones / notas adicionales *</label>
              </div>
            </div>

            {{-- Nombre del doctor --}}
            <div class="col-md-6">
              <div class="ux-float">
                <input class="form-control"
                       type="text"
                       name="nombre_doctor"
                       id="nombre_doctor"
                       placeholder=" "
                       value="{{ old('nombre_doctor') }}">
                <label for="nombre_doctor">Nombre del doctor</label>
              </div>
            </div>

            {{-- Registrado por --}}
            @auth
            <div class="col-md-6">
              <div class="ux-float has-value">
                <input class="form-control"
                       type="text"
                       id="user_name"
                       name="user_name"
                       value="{{ Auth::user()->name }}"
                       readonly
                       placeholder=" ">
                <label for="user_name">Registrado por</label>
              </div>
            </div>
            @endauth
          </div>

          {{-- Evidencias --}}
          <div class="upload-zone mt-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <div class="fw-semibold" style="color:var(--brand-ink)">Evidencias del equipo</div>
              <span class="text-muted-ux" style="font-size:.82rem;">Hasta 3 fotos y 1 video.</span>
            </div>

            <div class="row g-3">
              {{-- Img1 --}}
              <div class="col-6 col-md-3">
                <div class="tile" role="button" tabindex="0"
                     @click="$refs.img0.click()"
                     @keydown.enter.prevent="$refs.img0.click()"
                     @keydown.space.prevent="$refs.img0.click()">
                  <input type="file" class="d-none" accept="image/*" name="evidencia1" x-ref="img0" @change="previewImg($event,0)">
                  <template x-if="previews[0]"><img :src="previews[0]"></template>
                  <template x-if="!previews[0]">
                    <div class="tile-empty">
                      <i class="bi bi-image"></i><span>Imagen 1</span><small class="text-muted">Toca para subir</small>
                    </div>
                  </template>
                  <span class="tile-hint" x-show="previews[0]">Cambiar</span>
                </div>
              </div>

              {{-- Img2 --}}
              <div class="col-6 col-md-3">
                <div class="tile" role="button" tabindex="0"
                     @click="$refs.img1.click()"
                     @keydown.enter.prevent="$refs.img1.click()"
                     @keydown.space.prevent="$refs.img1.click()">
                  <input type="file" class="d-none" accept="image/*" name="evidencia2" x-ref="img1" @change="previewImg($event,1)">
                  <template x-if="previews[1]"><img :src="previews[1]"></template>
                  <template x-if="!previews[1]">
                    <div class="tile-empty">
                      <i class="bi bi-image"></i><span>Imagen 2</span><small class="text-muted">Toca para subir</small>
                    </div>
                  </template>
                  <span class="tile-hint" x-show="previews[1]">Cambiar</span>
                </div>
              </div>

              {{-- Img3 --}}
              <div class="col-6 col-md-3">
                <div class="tile" role="button" tabindex="0"
                     @click="$refs.img2.click()"
                     @keydown.enter.prevent="$refs.img2.click()"
                     @keydown.space.prevent="$refs.img2.click()">
                  <input type="file" class="d-none" accept="image/*" name="evidencia3" x-ref="img2" @change="previewImg($event,2)">
                  <template x-if="previews[2]"><img :src="previews[2]"></template>
                  <template x-if="!previews[2]">
                    <div class="tile-empty">
                      <i class="bi bi-image"></i><span>Imagen 3</span><small class="text-muted">Toca para subir</small>
                    </div>
                  </template>
                  <span class="tile-hint" x-show="previews[2]">Cambiar</span>
                </div>
              </div>

              {{-- Video --}}
              <div class="col-6 col-md-3">
                <div class="tile" role="button" tabindex="0"
                     @click="$refs.vid.click()"
                     @keydown.enter.prevent="$refs.vid.click()"
                     @keydown.space.prevent="$refs.vid.click()">
                  <input type="file" class="d-none"
                         accept="video/mp4,video/avi,video/mpeg,video/webm,video/quicktime"
                         name="video" x-ref="vid" @change="previewVideo($event)">
                  <template x-if="videoUrl"><video :src="videoUrl" muted autoplay loop playsinline></video></template>
                  <template x-if="!videoUrl">
                    <div class="tile-empty">
                      <i class="bi bi-play-btn"></i><span>Video</span><small class="text-muted">Toca para subir</small>
                    </div>
                  </template>
                  <span class="tile-hint" x-show="videoUrl">Cambiar</span>
                </div>
              </div>
            </div>
          </div>

          {{-- Firma --}}
          <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center">
              <label class="form-label fw-semibold mb-2">Firma digital *</label>
              <span class="badge-soft" x-show="isDrawing">dibujando…</span>
            </div>
            <div class="border rounded-3 p-2 bg-white">
              <canvas x-ref="canvas" style="width:100%; height:170px; display:block"></canvas>
            </div>
            <div class="mt-2 d-flex gap-2">
              <button type="button" class="btn btn-ghost" @click="clearSig()">Limpiar firma</button>
            </div>
          </div>
        </div>

        {{-- Tips --}}
        <div class="col-lg-4 d-none d-lg-block">
          <div class="card-soft p-3">
            <div class="fw-bold mb-1" style="color:var(--brand-ink)">Consejos de captura</div>
            <ul class="text-muted-ux small mb-0 ps-3">
              <li>Verifica que el <b>tipo</b> y <b>subtipo</b> correspondan al equipo.</li>
              <li>Incluye el <b>modelo completo</b> tal cual aparece en la placa.</li>
              <li>Sube al menos una foto de la placa de fabricante.</li>
              <li>Escribe el <b>nombre del doctor</b> responsable o solicitante.</li>
            </ul>
          </div>
        </div>
      </div>

      {{-- Botones --}}
      <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
        <a href="{{ url('/inventario/servicio') }}" class="btn btn-ghost">Cancelar</a>
        <button type="submit" class="btn btn-pastel">Guardar servicio</button>
      </div>
    </form>
  </div>

  <div id="toastPlace" class="toast-container position-fixed"></div>

  {{-- Loader --}}
  <div class="loader-backdrop" x-show="loading" x-cloak x-transition.opacity>
    <div class="loader-box">
      <div class="d-flex align-items-center gap-2 mb-2">
        <div class="spinner-soft"></div>
        <div class="loader-title">Guardando servicio…</div>
      </div>
      <div class="loader-steps">
        <template x-for="(txt, idx) in loadingTexts" :key="'step-'+idx">
          <div class="loader-step" :class="{'active': idx === loadingStep,'done': idx < loadingStep}">
            <div class="loader-step-icon">
              <template x-if="idx < loadingStep"><i class="bi bi-check-lg"></i></template>
              <template x-if="idx === loadingStep"><i class="bi bi-dot"></i></template>
              <template x-if="idx > loadingStep"><i class="bi bi-circle"></i></template>
            </div>
            <div x-text="txt"></div>
          </div>
        </template>
        <div class="loader-note">No cierres la ventana mientras subimos evidencias y registramos el servicio.</div>
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
    setTimeout(() => setState(el), 250);
  });
});
</script>

<script>
function ServicioUI(){
  return {
    tiposEquipos: {
      endoscopia: [
        "Adaptador USB","Adaptador para Sonda","Bomba de Irrigación","Bomba de Secreción","Boquillas","Broncoscopio",
        "Cable","Cable Bipolar","Cable USB","Cámara con Cabezal","Carrito","Cepillo de Limpieza","Colonoscopio",
        "Contenedor de Liquidos","Duodenoscopio","Eliminador","Fuente de Luz","Gastroscopio","Kit de Limpieza",
        "Lineas de Irrigación","Mause","Pigtail","Pigtel","Pinza de Biopsia","Pinza de Biopsia Hot",
        "Pinza de Extracción","Pinza de Polipectomia","Probador","Probador de Fuga","Procesador",
        "Proctector","Sistema","Sistema Endoscopia","Tapon-ETO","Teclado","Video Carro","Regulador de CO2 Endoscopia","Argon Plasma","Electrocauterio"
      ],
      laparoscopia: [
        "Adaptador","Adaptador Para Ligasure","Armonico","Cabezal","Camara","Cable USB","Camilla","Charolas de Esterilización",
        "Clips para Monitor","Eliminador","Fibra de Luz","Forcetriad","Fuente de Luz","Insuflador","Lampara XENON",
        "Lente","Maletin/Case","Manguera de Insuflación","Manguera para Bomba de Agua","Manguera y Yugo",
        "Monitor","Pedestal","Pieza de Mano","Pinza","Rasurador y Radio Frecuencia","Set de Artroscopia",
        "Trasmisor","Trocar","Video Carro","Video Grabador","Yugo","Carro FT10","FT10","Carro Forcetriad"
      ],
      quirofano: [
        "Desfibrador","Electrocauterio","Eliminador","Lámpara de Cirugía","Lámpara de Quirofano",
        "Máquina de Anestesia","Mesa de Cirugía","Consola Quirurjica","Monitor Signos Vitales"
      ],
      hospitalizacion: [
        "Aspirador","Cama Hospitalaria Eléctrica","Camilla","Incubadora","Mesa de Exploración"
      ],
      cirujia: [
        "Lapíz para Electrocauterio","Placa para Electrocauterio","Brazalete"
      ],
      artroscopia: [
        "Set de Taladros de Artroscopia"
      ],
      ginecologia: [
        "Mesa de Exploración","Cama de Ginecología"
      ],
      otros: []
    },

    get tiposKeys(){ return Object.keys(this.tiposEquipos) },
    get subtiposArr(){
      if(!this.tipoSel || this.tipoSel === 'otros') return [];
      return this.tiposEquipos[this.tipoSel] || [];
    },

    mantenimientoTipo: 'externo',
    isInterno: false,

    tipoSel:'',
    subtipoSel:'',
    subtipoOtro:'',

    previews:[null,null,null],
    videoUrl:null,
    firmaData:'',
    isDrawing:false,

    loading:false,
    loadingStep:0,
    loadingTexts:[
      'Validando información capturada',
      'Guardando servicio en el sistema',
      'Subiendo evidencias (fotos y video)',
      'Registrando firma digital',
      'Preparando siguiente paso'
    ],
    loadingTimer:null,

    init(){
      this.$nextTick(()=>{ this.initCanvas(); });
    },

    titleCase(s){
      return (s||'').replace(/_/g,' ').replace(/\w\S*/g,t=>t.charAt(0).toUpperCase()+t.slice(1))
    },

    onTipoChange(){
      this.subtipoSel='';
      this.subtipoOtro='';
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
      canvas.height=170*dpr;
      canvas.style.width=rect.width+'px';
      canvas.style.height='170px';
      const ctx=canvas.getContext('2d');
      ctx.scale(dpr,dpr);
      ctx.lineWidth=2;
      ctx.lineCap='round';
      ctx.strokeStyle='#1d4d4f';

      // fondo blanco
      const fillWhite = () => {
        const c2 = canvas.getContext('2d');
        c2.save();
        c2.setTransform(1,0,0,1,0,0);
        c2.fillStyle='#fff';
        c2.fillRect(0,0,canvas.width,canvas.height);
        c2.restore();
      };
      fillWhite();

      let draw=false, last=null;
      const pos=e=>{
        const b=canvas.getBoundingClientRect();
        const x=(e.touches?e.touches[0].clientX:e.clientX)-b.left;
        const y=(e.touches?e.touches[0].clientY:e.clientY)-b.top;
        return {x,y};
      };
      const start=e=>{
        draw=true; last=pos(e); this.isDrawing=true; e.preventDefault();
      };
      const move=e=>{
        if(!draw) return;
        const p=pos(e);
        ctx.beginPath();
        ctx.moveTo(last.x,last.y);
        ctx.lineTo(p.x,p.y);
        ctx.stroke();
        last=p; e.preventDefault();
      };
      const end=()=>{
        draw=false; this.isDrawing=false;
        this.firmaData=canvas.toDataURL('image/png');
      };
      canvas.addEventListener('mousedown',start);
      canvas.addEventListener('mousemove',move);
      window.addEventListener('mouseup',end);
      canvas.addEventListener('touchstart',start,{passive:false});
      canvas.addEventListener('touchmove',move,{passive:false});
      canvas.addEventListener('touchend',end);

      this.firmaData=canvas.toDataURL('image/png');
      window.addEventListener('resize', ()=>this.initCanvas(), {once:true});
    },
    clearSig(){
      const c=this.$refs.canvas, ctx=c.getContext('2d');
      ctx.save();
      ctx.setTransform(1,0,0,1,0,0);
      ctx.fillStyle='#fff';
      ctx.fillRect(0,0,c.width,c.height);
      ctx.restore();
      this.firmaData=c.toDataURL('image/png');
    },

    startLoader(){
      this.loading = true;
      this.loadingStep = 0;
      if(this.loadingTimer) clearInterval(this.loadingTimer);
      const total = this.loadingTexts.length;
      this.loadingTimer = setInterval(() => {
        if(!this.loading){ clearInterval(this.loadingTimer); this.loadingTimer=null; return; }
        if(this.loadingStep < total - 1){ this.loadingStep++; }
      }, 1500);
    },
    stopLoader(){
      this.loading = false;
      if(this.loadingTimer){ clearInterval(this.loadingTimer); this.loadingTimer=null; }
    },

    async submit(){
      // Validación rápida
      if (!this.firmaData) {
        this.toast('Por favor firma antes de guardar el servicio', false);
        return;
      }
      if (!this.tipoSel) {
        this.toast('Selecciona el tipo de equipo', false);
        return;
      }
      if (this.tipoSel === 'otros' && !this.subtipoOtro) {
        this.toast('Especifica el subtipo cuando el tipo es "otros".', false);
        return;
      }
      if (this.tipoSel !== 'otros' && !this.subtipoSel) {
        this.toast('Selecciona el subtipo de equipo', false);
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
          body   : fd,
          credentials: 'same-origin'
        });

        // Leemos como texto para capturar 419/HTML y aún así dar error claro
        const raw = await res.text();
        let json = null;
        try { json = JSON.parse(raw); } catch(e){ json = null; }

        if(res.status === 419){
          this.stopLoader();
          this.toast('Sesión expirada (419). Recarga la página e intenta de nuevo.', false);
          return;
        }

        if(!res.ok || !json || !json.success){
          this.stopLoader();
          let msg = 'Error al guardar el servicio';
          if (json && json.error) msg = json.error;
          else if (json && json.errors) msg = Object.values(json.errors).flat().join(' | ');
          else if (!json && raw) msg = 'Respuesta del servidor: ' + raw.substring(0, 160);
          this.toast(msg, false);
          return;
        }

        this.stopLoader();
        this.toast(json.message || 'Servicio registrado correctamente', true);

        setTimeout(()=>{
          window.location.href = json.next_url || "{{ url('/inventario/servicio') }}";
        }, 900);

      }catch(e){
        console.error(e);
        this.stopLoader();
        this.toast('Error de red/servidor', false);
      }
    },

    toast(msg, ok=true, html=false){
      const el=document.createElement('div');
      el.className='toast align-items-center border-0 shadow-sm';
      el.setAttribute('role','alert');
      el.setAttribute('aria-live','assertive');
      el.setAttribute('aria-atomic','true');
      el.innerHTML=`
        <div class="d-flex ${ok?'bg-success-subtle text-success-emphasis':'bg-danger-subtle text-danger-emphasis'} rounded-3 px-3 py-2">
          <div class="toast-body">${html?msg:this.escape(msg)}</div>
          <button type="button" class="btn-close ms-auto m-1" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>`;
      document.getElementById('toastPlace').appendChild(el);
      const t=new bootstrap.Toast(el,{delay:3200}); t.show();
      el.addEventListener('hidden.bs.toast',()=>el.remove());
    },
    escape(s){
      return (s||'').replace(/[&<>"']/g, m=>({
        '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
      }[m]))
    },
  }
}
</script>
@endsection