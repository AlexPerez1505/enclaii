@extends('layouts.app')
@section('title','Agregar producto')
@section('titulo','Registro')

@section('content')
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

/* Zona de carga con previews clicables */
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

/* Badges y cards */
.badge-soft{
  background:#e9fbf7;
  color:var(--brand-ink);
  border-radius:999px;
  padding:.25rem .55rem;
  font-weight:700;
  font-size:.75rem
}
.comp-card{
  border:1px solid var(--line);
  border-radius:12px;
  background:#fff;
  padding:.7rem
}
.comp-name{
  font-weight:700;
  color:var(--ink)
}
.comp-note{
  font-size:.85rem;
  color:var(--muted)
}

/* Switch card */
.switch-card{
  border:1px solid var(--line);
  border-radius:14px;
  background:#fff;
  padding:1rem;
}
.switch-title{
  font-weight:800;
  color:var(--ink);
  line-height:1.1;
}
.switch-sub{
  color:var(--muted);
  font-size:.9rem;
  margin-top:.2rem;
}
.switch-badge{
  display:inline-flex;
  align-items:center;
  gap:.4rem;
  border-radius:999px;
  padding:.25rem .55rem;
  font-weight:800;
  font-size:.75rem;
  border:1px solid var(--line);
  background:#fafcff;
  color:var(--brand-ink);
}
.form-check-input{
  cursor:pointer;
}
.form-check-input:focus{
  box-shadow:none;
  border-color:var(--brand-ink);
}
.form-check-input:checked{
  background-color:var(--brand-ink);
  border-color:var(--brand-ink);
}

/* Toasts y sheet */
#toastPlace{
  position:fixed;
  top:1rem;
  right:1rem;
  z-index:1080
}
.offcanvas-bottom{
  border-top-left-radius:16px;
  border-top-right-radius:16px
}
.offcanvas .offcanvas-header h5{
  color:var(--brand-ink);
  font-weight:800
}
#componentesSheet{
  padding-bottom: env(safe-area-inset-bottom);
  transition: height .2s ease;
  max-height: 90dvh;
}

/* ===== Loader multi-step (CSS only) ===== */
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
  transform:translateY(0);
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
  transform:translateY(-1px);
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
@keyframes spin{
  to{ transform:rotate(360deg); }
}
</style>

<div class="container py-4" x-data="RegistroUI()">
  <div class="card-soft p-3 p-md-4">
    <div class="d-flex align-items-center justify-content-between pb-3 border-bottom">
      <div>
        <div class="heading-xl">Agregar producto</div>
        <div class="text-muted-ux">
          Completa los datos, adjunta evidencias y firma.
          <span class="badge-soft ms-2">
            Tip: si capturas varias series se creará un lote para moverlos juntos en los procesos.
          </span>
        </div>
      </div>
      <a href="{{ url()->previous() }}" class="btn btn-ghost d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <form id="frmRegistro" class="mt-4" enctype="multipart/form-data" @submit.prevent="submit">
      @csrf
      <input type="hidden" name="firmaDigital" x-model="firmaData">

      {{-- ✅ SWITCH: Saltar procesos / Directo a STOCK --}}
      <input type="hidden" name="estado_proceso" x-model="estadoProceso">
      <div class="switch-card mt-3">
        <div class="d-flex align-items-start justify-content-between gap-3">
          <div>
            <div class="switch-title">
              Saltar procesos y mandar directo a <span style="color:var(--brand-ink)">Stock</span>
            </div>
            <div class="switch-sub">
              Si lo activas, el equipo se guardará de una vez con <b>estado_proceso = stock</b>.
              Si lo desactivas, inicia en <b>registro</b> para hacer procesos uno por uno.
            </div>
            <div class="mt-2">
              <span class="switch-badge" x-show="directoStock">
                <i class="bi bi-lightning-charge-fill"></i> Directo a STOCK
              </span>
              <span class="switch-badge" x-show="!directoStock">
                <i class="bi bi-diagram-3"></i> Flujo normal (registro → hojalatería → mantenimiento → stock)
              </span>
            </div>
          </div>

          <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" id="swDirectoStock"
                   x-model="directoStock" @change="onToggleStock()">
          </div>
        </div>
      </div>

      <div class="row g-4 mt-1">
        {{-- IZQUIERDA: Datos + evidencias --}}
        <div class="col-lg-8">
          <div class="row g-3">
            {{-- Tipo --}}
            <div class="col-md-6">
              <div class="ux-float" :class="{'has-value': tipoSel}">
                <select class="form-select" name="Tipo_de_Equipo" id="eq_tipo" x-model="tipoSel" @change="onTipoChange" required>
                  <option value="" hidden></option>
                  <template x-for="t in tiposKeys" :key="'t-'+t">
                    <option :value="t" x-text="titleCase(t)"></option>
                  </template>
                </select>
                <label for="eq_tipo">Tipo de equipo *</label>
              </div>
            </div>

            {{-- Subtipo dependiente --}}
            <div class="col-md-6">
              <div class="ux-float" :class="{'has-value': subtipoSel}">
                <select class="form-select" name="Subtipo_de_Equipo" id="eq_sub" x-model="subtipoSel" @change="onSubtipoChange" :disabled="!tipoSel" required>
                  <option value="" hidden></option>
                  <template x-for="s in subtiposArr" :key="'s-'+s">
                    <option :value="s" x-text="s"></option>
                  </template>
                </select>
                <label for="eq_sub">Subtipo *</label>
              </div>
            </div>

            {{-- Marca dependiente --}}
            <div class="col-md-6">
              <div class="ux-float" :class="{'has-value': marcaSel}">
                <select
                  class="form-select"
                  name="Marca"
                  id="eq_marca"
                  x-model="marcaSel"
                  @change="onMarcaChange"
                  :disabled="!tipoSel || !subtipoSel || !marcasArr.length"
                  required
                >
                  <option value="" hidden></option>
                  <template x-for="m in marcasArr" :key="'m-'+m">
                    <option :value="m" x-text="m"></option>
                  </template>
                </select>
                <label for="eq_marca">Marca *</label>
              </div>
              <small class="text-muted-ux d-block mt-1" x-show="tipoSel && subtipoSel && !marcasArr.length">
                No hay marcas configuradas para este subtipo.
              </small>
            </div>

            {{-- Modelo dependiente --}}
            <div class="col-md-6">
              <div class="ux-float" :class="{'has-value': modeloSel}">
                <select
                  class="form-select"
                  name="Modelo"
                  id="eq_modelo"
                  x-model="modeloSel"
                  :disabled="!marcaSel || !modelosArr.length"
                  required
                >
                  <option value="" hidden></option>
                  <template x-for="mo in modelosArr" :key="'mo-'+mo">
                    <option :value="mo" x-text="mo"></option>
                  </template>
                </select>
                <label for="eq_modelo">Modelo *</label>
              </div>
              <small class="text-muted-ux d-block mt-1" x-show="marcaSel && !modelosArr.length">
                No hay modelos configurados para esta marca.
              </small>
            </div>

            {{-- Número de serie(s) multilinea --}}
            <div class="col-md-6">
              <div class="ux-float">
                <textarea
                  class="form-control"
                  name="Numero_de_Serie"
                  id="eq_serie"
                  x-ref="serieField"
                  placeholder=" "
                  rows="3"
                  required
                  @blur="checkSeriesNow()"
                ></textarea>
                <label for="eq_serie">Número de serie(s) *</label>
                <small class="text-muted-ux d-block mt-1">
                  Puedes capturar <b>varios números de serie</b>, uno por línea. Se creará un <b>lote</b> para moverlos juntos en el flujo.
                </small>
                <small class="text-muted-ux d-block mt-1" x-show="dupLocalList.length">
                  <span class="badge text-bg-danger">Duplicados en tu captura</span>
                  <span x-text="dupLocalList.join(', ')"></span>
                </small>
                <small class="text-muted-ux d-block mt-1" x-show="dupServerList.length">
                  <span class="badge text-bg-danger">Ya registradas</span>
                  <span x-text="dupServerList.join(', ')"></span>
                </small>
              </div>
            </div>

            {{-- Generador: serie base + cantidad --}}
            <div class="col-12 mt-2">
              <div class="d-flex flex-wrap gap-2 align-items-end">
                <div style="flex:1 1 230px">
                  <div class="ux-float" :class="{'has-value': baseSerie}">
                    <input class="form-control" type="text" x-model="baseSerie" placeholder=" ">
                    <label>Serie base (ej. ABC001)</label>
                  </div>
                </div>

                <div style="width:120px">
                  <div class="ux-float" :class="{'has-value': cantidadSerie}">
                    <input class="form-control" type="number" min="1" max="500" x-model.number="cantidadSerie" placeholder=" ">
                    <label>Cantidad</label>
                  </div>
                </div>

                <button type="button" class="btn btn-ghost" @click="genSeries">
                  Generar series
                </button>

                <button type="button" class="btn btn-ghost" @click="checkSeriesNow()">
                  Verificar series
                </button>
              </div>
              <small class="text-muted-ux d-block mt-1">
                Esto llenará automáticamente el campo de números de serie con la secuencia generada
                (<code>ABC001, ABC002, ABC003…</code>).
              </small>
            </div>

            <div class="col-md-6">
              <div class="ux-float">
                <input class="form-control" type="text" name="Año" id="eq_anio" placeholder=" " inputmode="numeric" pattern="[0-9]{4}">
                <label for="eq_anio">Año</label>
              </div>
            </div>

            <div class="col-12">
              <div class="ux-float">
                <textarea class="form-control" name="descripcion" id="eq_desc" placeholder=" " required></textarea>
                <label for="eq_desc">Descripción *</label>
              </div>
            </div>

            <div class="col-md-6">
              <div class="ux-float">
                <input class="form-control" type="date" name="fecha_inicial" id="eq_fecha" placeholder=" " required>
                <label for="eq_fecha">Fecha de adquisición *</label>
              </div>
            </div>

            <div class="col-12">
              <div class="ux-float">
                <input class="form-control" type="text" name="observaciones" id="eq_obs" placeholder=" ">
                <label for="eq_obs">Observaciones</label>
              </div>
            </div>
          </div>

          {{-- Evidencias --}}
          <div class="upload-zone mt-4">
            <div class="row g-3">
              {{-- Imagen 1 --}}
              <div class="col-6 col-md-3">
                <div class="tile"
                     role="button" tabindex="0"
                     @click="$refs.img0.click()"
                     @keydown.enter.prevent="$refs.img0.click()"
                     @keydown.space.prevent="$refs.img0.click()">
                  <input type="file" class="d-none" accept="image/*" name="evidencia1" x-ref="img0" @change="previewImg($event,0)">
                  <template x-if="previews[0]"><img :src="previews[0]"></template>
                  <template x-if="!previews[0]">
                    <div class="tile-empty">
                      <i class="bi bi-image"></i>
                      <span>Imagen 1</span>
                      <small class="text-muted">Toca para subir</small>
                    </div>
                  </template>
                  <span class="tile-hint" x-show="previews[0]">Cambiar</span>
                </div>
              </div>

              {{-- Imagen 2 --}}
              <div class="col-6 col-md-3">
                <div class="tile"
                     role="button" tabindex="0"
                     @click="$refs.img1.click()"
                     @keydown.enter.prevent="$refs.img1.click()"
                     @keydown.space.prevent="$refs.img1.click()">
                  <input type="file" class="d-none" accept="image/*" name="evidencia2" x-ref="img1" @change="previewImg($event,1)">
                  <template x-if="previews[1]"><img :src="previews[1]"></template>
                  <template x-if="!previews[1]">
                    <div class="tile-empty">
                      <i class="bi bi-image"></i>
                      <span>Imagen 2</span>
                      <small class="text-muted">Toca para subir</small>
                    </div>
                  </template>
                  <span class="tile-hint" x-show="previews[1]">Cambiar</span>
                </div>
              </div>

              {{-- Imagen 3 --}}
              <div class="col-6 col-md-3">
                <div class="tile"
                     role="button" tabindex="0"
                     @click="$refs.img2.click()"
                     @keydown.enter.prevent="$refs.img2.click()"
                     @keydown.space.prevent="$refs.img2.click()">
                  <input type="file" class="d-none" accept="image/*" name="evidencia3" x-ref="img2" @change="previewImg($event,2)">
                  <template x-if="previews[2]"><img :src="previews[2]"></template>
                  <template x-if="!previews[2]">
                    <div class="tile-empty">
                      <i class="bi bi-image"></i>
                      <span>Imagen 3</span>
                      <small class="text-muted">Toca para subir</small>
                    </div>
                  </template>
                  <span class="tile-hint" x-show="previews[2]">Cambiar</span>
                </div>
              </div>

              {{-- Video --}}
              <div class="col-6 col-md-3">
                <div class="tile"
                     role="button" tabindex="0"
                     @click="$refs.vid.click()"
                     @keydown.enter.prevent="$refs.vid.click()"
                     @keydown.space.prevent="$refs.vid.click()">
                  <input type="file" class="d-none" accept="video/*" name="video-evidencia" x-ref="vid" @change="previewVideo($event)">
                  <template x-if="videoUrl"><video :src="videoUrl" muted autoplay loop playsinline></video></template>
                  <template x-if="!videoUrl">
                    <div class="tile-empty">
                      <i class="bi bi-play-btn"></i>
                      <span>Video</span>
                      <small class="text-muted">Toca para subir</small>
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
            <div class="border rounded-3 p-2">
              <canvas x-ref="canvas" style="width:100%; height:170px; display:block"></canvas>
            </div>
            <div class="mt-2 d-flex gap-2">
              <button type="button" class="btn btn-ghost" @click="clearSig()">Limpiar</button>
            </div>
          </div>

          {{-- Móvil: ver componentes --}}
          <div class="d-md-none mt-4">
            <button type="button" class="btn btn-pastel w-100" data-bs-toggle="offcanvas" data-bs-target="#componentesSheet">
              Ver componentes del subtipo
            </button>
          </div>
        </div>

        {{-- DERECHA: Componentes (escritorio) --}}
        <div class="col-lg-4 d-none d-md-block">
          <div class="card-soft p-3">
            <div class="fw-bold" style="color:var(--brand-ink)">Componentes del subtipo</div>
            <small class="text-muted-ux">Se generan automáticamente según el <b>Subtipo</b>. Desmarca si “no viene”.</small>

            <div class="mt-3" x-show="!sheet.lista.length">
              <div class="text-muted-ux">Selecciona un <b>Subtipo</b> para ver los componentes esperados.</div>
            </div>

            <div class="mt-3" x-show="sheet.lista.length">
              <template x-for="(c, idx) in sheet.lista" :key="'desk-'+idx">
                <div class="comp-card mb-2">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <div class="comp-name" x-text="c.nombre"></div>
                      <div class="comp-note">Esperado</div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                      <div class="input-group input-group-sm" style="width:110px">
                        <button class="btn btn-ghost" type="button" @click="dec(idx)">−</button>
                        <input type="number" class="form-control text-center" min="0" x-model.number="c.cantidad" @input="syncSeleccion">
                        <button class="btn btn-ghost" type="button" @click="inc(idx)">+</button>
                      </div>
                      <div class="form-check form-switch m-0">
                        <input class="form-check-input" type="checkbox" x-model="c.incluido" @change="syncSeleccion">
                      </div>
                    </div>
                  </div>
                </div>
              </template>

              <div class="d-flex justify-content-end gap-2 mt-2">
                <button type="button" class="btn btn-ghost" @click="restaurarPlantilla">Restaurar plantilla</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Hidden inputs --}}
      <template x-for="(row, i) in seleccion" :key="'hid-'+i">
        <div>
          <input type="hidden" :name="`componentes[${i}][nombre]`"   :value="row.nombre">
          <input type="hidden" :name="`componentes[${i}][cantidad]`" :value="row.cantidad">
          <input type="hidden" :name="`componentes[${i}][incluido]`" :value="row.incluido ? 1 : 0">
        </div>
      </template>

      <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
        <a href="{{ url()->previous() }}" class="btn btn-ghost">Cancelar</a>
        <button type="submit" class="btn btn-pastel">Guardar</button>
      </div>
    </form>
  </div>

  <div id="toastPlace" class="toast-container position-fixed"></div>

  {{-- Bottom sheet (móvil) --}}
  <div
    id="componentesSheet"
    class="offcanvas offcanvas-bottom"
    tabindex="-1"
    aria-labelledby="componentesSheetLabel"
    :style="{ height: sheetHeight }"
  >
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="componentesSheetLabel">Componentes del subtipo</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body small">
      <p class="text-muted-ux mb-2">Se generan automáticamente según el <b>Subtipo</b>. Desmarca si “no viene”.</p>

      <template x-for="(c, idx) in sheet.lista" :key="'m-'+idx">
        <div class="comp-card mb-2">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="comp-name" x-text="c.nombre"></div>
              <div class="comp-note">Esperado</div>
            </div>
            <div class="d-flex align-items-center gap-2">
              <div class="input-group input-group-sm" style="width:110px">
                <button class="btn btn-ghost" type="button" @click="dec(idx)">−</button>
                <input type="number" class="form-control text-center" min="0" x-model.number="c.cantidad" @input="syncSeleccion">
                <button class="btn btn-ghost" type="button" @click="inc(idx)">+</button>
              </div>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" x-model="c.incluido" @change="syncSeleccion">
              </div>
            </div>
          </div>
        </div>
      </template>

      <div class="d-flex justify-content-end gap-2 mt-2">
        <button type="button" class="btn btn-ghost" @click="restaurarPlantilla">Restaurar plantilla</button>
        <button type="button" class="btn btn-pastel" data-bs-dismiss="offcanvas">Listo</button>
      </div>
    </div>
  </div>

  {{-- Loader overlay --}}
  <div
    class="loader-backdrop"
    x-show="loading"
    x-cloak
    x-transition.opacity
  >
    <div class="loader-box">
      <div class="d-flex align-items-center gap-2 mb-2">
        <div class="spinner-soft"></div>
        <div class="loader-title">Guardando registro…</div>
      </div>

      <div class="loader-steps">
        <template x-for="(txt, idx) in loadingTexts" :key="'step-'+idx">
          <div class="loader-step" :class="{ 'active': idx === loadingStep, 'done': idx < loadingStep }">
            <div class="loader-step-icon">
              <template x-if="idx < loadingStep"><i class="bi bi-check-lg"></i></template>
              <template x-if="idx === loadingStep"><i class="bi bi-dot"></i></template>
              <template x-if="idx > loadingStep"><i class="bi bi-circle"></i></template>
            </div>
            <div x-text="txt"></div>
          </div>
        </template>
        <div class="loader-note">
          No cierres la ventana mientras guardamos la información y preparamos el lote.
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Mantener estado flotante --}}
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
function RegistroUI(){
  return {
    /* =========================
       ===== DUPLICADOS UI =====
       ========================= */
    dupLocalList: [],
    dupServerList: [],
    isChecking: false,
    checkDebounce: null,

    /* Switch directo a stock */
    directoStock: false,
    estadoProceso: 'registro',

    parseSeries(raw){
      return String(raw||'')
        .split(/\r\n|\r|\n/)
        .map(v => v.trim())
        .filter(v => v !== '');
    },

    findLocalDuplicates(list){
      const count = new Map();
      const dups = new Set();
      list.forEach(s => {
        const key = s.toLowerCase().trim();
        count.set(key, (count.get(key) || 0) + 1);
        if (count.get(key) > 1) dups.add(s);
      });
      return Array.from(dups);
    },

    onToggleStock(){
      this.estadoProceso = this.directoStock ? 'stock' : 'registro';
      if(this.directoStock){
        this.toast('Se guardará directo en STOCK (saltando procesos).', true);
      }else{
        this.toast('Flujo normal: iniciará en REGISTRO para hacer procesos uno por uno.', true);
      }
    },

    async checkSeriesOnServer(seriesList){
      const url = @json(url('/registros/check-series'));
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': @json(csrf_token()),
        },
        body: JSON.stringify({ series: seriesList.join("\n") })
      });

      const json = await res.json().catch(()=> ({}));
      if(!res.ok) throw new Error(json?.message || 'No se pudo verificar series');

      return json;
    },

    async checkSeriesNow(showToast=true){
      const raw = this.$refs.serieField?.value || '';
      const list = this.parseSeries(raw);

      this.dupLocalList = [];
      this.dupServerList = [];

      if (!list.length) return;

      const localDups = this.findLocalDuplicates(list);
      this.dupLocalList = localDups;

      if (localDups.length && showToast){
        this.toast(`Hay números de serie repetidos en tu captura: ${localDups.join(', ')}`, false);
      }

      try{
        this.isChecking = true;
        const unique = Array.from(new Set(list.map(x => x.trim()).filter(Boolean)));
        const out = await this.checkSeriesOnServer(unique);

        const exists = Array.isArray(out?.duplicates) ? out.duplicates : [];
        this.dupServerList = exists;

        if (exists.length && showToast){
          this.toast(`Ya están registrados: ${exists.join(', ')}`, false);
        }
      }catch(e){
        if(showToast){
          this.toast('No se pudo verificar si las series ya existen (servidor).', false);
        }
      }finally{
        this.isChecking = false;
      }
    },

    /* TIPOS → SUBTIPOS */
    tiposEquipos: {
      ENDOSCOPIA: [
        "Adaptador","Argón Plasma","Bomba de Irrigación","Bomba de Secreción","Bomba de CO2","Boquillas","Broncoscopio","Cable","Cable Bipolar","Cable Monopolar","Capturador de Video","Capuchón Distal","Carro","Cepillo de Limpieza","Colonoscopio","Conjunto de Irrigación","Contenedor de líquidos","Convertidor de Video","Duodenoscopio","Eliminador","Focos excelitas","Fuente de Luz","Gastroscopio","Grabador","Interfaz Monopolar para Erbe","Kit de Limpieza","Línea de Irrigación","Monitor","Mouse","Multicontacto","PC SIIMED Análogo","PC SIIMED HD","Pigtail","Pinzas de Endoscopia","Probador de Fuga","Procesador","Protector Bucal de Endoscopio","Protector de Punta de Endoscopio","Regulador de Argón de Endoscopia","Sistema Endoscopia","Tapon de Biopsia","Tapon-ETO","Teclado","Valvúlas desechables","Valvúlas Reusables","Yugo Para Argón"
      ],
      LAPAROSCOPIA: [
        "Adaptador","Cabezal","Cable Interfaz 1688","Cable Interfaz USB 1588","Cámara","Case de Transporte","Charola de Esterilización","Clarity","Clips para Monitor","Fibra de Luz","Fuente de Luz","Grabador","Instrumental de Laparoscopia","Insuflador","Lente","Manguera de Insuflación","Manguera para Bomba de Agua","Monitor Grado Médico","Parche para Electrocauterio","Pedestal","Pieza de Mano","Pinza","Transmisor","Trocar","Video Carro","Video Grabador"
      ],
      QUIRÓFANO: [
        "Adaptador para Ligasure","Armónico Gen11","Bipap","Brazalete Pani","Bomba de Infusion","Cable Para Pinza Bipolar","Cable Trocal ECG","Carro para Electrocauterio","Carro Rojo Emergencias","Desfribilador","Electrocauterio","Eliminador","Fuente de Poder para Desfribilador","Lámpara de Quirófano","Lapíz para Electrocauterio","Ligasure LS8","Línea de Muestreo de CO2","Máquina de Anestesia","Mesa de Cirugía","Monitor Signos Vitales","Pedal Bipolar","Pedal Ligasure","Pedal Monopolar","Pieza de Mano Para Gen11","Placa para Electrocauterio","Sensor de ECG","Sensor de SPO2","Sensor PANI","Sensor de Temperatura","Vaporizador"
      ],
      HOSPITALIZACIÓN: [
        "Aspirador","Cama Hospitalaria Eléctrica","Camilla","Cuna Térmica","Incubadora","Mesa de Exploración","Ventilador"
      ],
      MATERIAL: [
        "Limpiador y Desengrasante"
      ],
      RADIOLOGÍA: ["Arco en C","Batería","Chasis","Flat Panel","Rayos X Rodable","Rayos X Portatil"],
      UROLOGÍA: ["Cistoscopio","Histeroscopio","Resectoscopio","Ureteroscopio Flexible", "Ureteroscopio Rigido"],
      ARTROSCOPIA: [
        "Batería","Cargador de Baterias","Camisa con Opturador","Cable para pedal","Cable para pieza de mano","Charola de Esterilización","Puntas de Radio Frecuencia","Endogia","Bomba de Irrigación","Pedal",
        "Lente",
        "Serfas de radiofrecuencia","Serfas Energy","Shaver","Rasurador", "Radio Frecuencia",
        "Set de Taladros de Artroscopia",
        "Transmisores",
        "Set de Cirugia Para Hombro y Tobillo", "Set de Cirugía de Rodilla",
        "Meditronic","linea de irrigacion"
      ],
      CEYE: ["Autoclave de cámara 95 L ","Monitor"],
      GINECOLOGÍA: ["Camilla Ginecologíca","Mesa de Exploración","Ultrasonido"]
    },

    /* =========================
       MARCAS → MODELOS (por Tipo/Subtipo)
       ========================= */
    marcasModelosPorSubtipo: {
      laparoscopia: {
        'camara': {
          'Stryker': ['1188','1288','Precision','1488','1588','1688','1788'],
          'Karl Storz': ['IMAGE1 S', 'IMAGE1 HUB', 'Spies']
        },
        'insuflador': {
          'Stryker': ['High Flow 40L','PneumoSure 45L','PneumoClear 50L'],
          'Karl Storz': ['Endoflator 50', 'Endoflator 264320 20'],
        },
        'fuente de luz': {
          'Stryker': ['X8000', 'L9000', 'L10', 'L11'],
          'Karl Storz': ['Xenon 300', 'Power LED 300']
        },
        'monitor grado medico': {
          'Stryker': ['Vision Elect HDTV', 'VisionPro LED 26"', 'VisionPro SYNK LED 26"', '4K LED 32"', '4K 32" OLED', 'Wise HD 26"'],
        },
        'cabezal': {
          'Stryker': ['1188', '1288', 'Precision', '1488', '1588','1688', 'prueba']
        },
        'clarity': { 'Stryker': ['clarity'] },
        'grabador': { 'Stryker': ['SDC Ultra','SDC3','Connected OR HUB'] },
        'lente': {
          'Stryker': ['30-5mm Azul','30-5mm AIM','30-5mm Precision','30-10mm Precision','30-10mm AIM','30-10mm Azul']
        },
        'fibra de luz': {
          'Stryker' : ['X8000 Gris','L9000 Blanca','L10 y L11 Verde','Kit Ureteral IRIS']
        },
        'video carro': {
          'Stryker': ['Standar','Connected OR'],
        },
        'transmisor': {
          'Stryker': ['4K SYNK Wireless','4K SYNK Wireless Receiver','VisionPro SYNK Wireless','Wise HDTV Wireless']
        },
        'trocar': {
            'Ethicon': ['11mm X 100mm','12mm X 100mm 2D12-T'],
            'GM': ['KIT Trocares GYTR L KIT A','KIT TROCARES GYTR-LLL KIT A']
        },
        'pedestal': { 'Stryker': ['Pedestal'] },
        'instrumental de laparoscopia': { 
          'Ethicon': ['100mm x 12mm'],
          'GM': ['Aguja de Veress','Baja Nudos','Cable Bipolar','Cable monopolar','Clips Hemolok Dorado','Clips Hemolok Morado','Clips Hemolok Verde','Clips Titanio OC300','Clips Titanio OC400','Conjunto de Irrigacion y Succion desechable','Engrapadora Articulada','Engrapadora Hemolok Amarillo','Engrapadora Hemolok Dorado','Engrapadora Hemolok Morado','Engrapadora Hemolok Verde','Engrapadora Titanio LT300','Engrapadora Titanio LT400','Espatula','Gancho En L','Pinza Alligator','Pinza Babcock','Pinza Babcock Grasper 5mm 330mm','Pinza Babcock Grasper 10mm 330mm','Pinza Cobra','Pinza Colecistectomia','Pinza De Curva','Pinza De Disección','Pinza De Tijera Recta','Pinza Disectora','Pinza Extractora De Litos','Pinza Fenestrada','Pinza Grasper','Pinza Har23','Pinza Har26','Pinza Maryland Curva','Porta agujas 5mm 300mm','Retractor','Tijera Metzenbaum Doble Acción Curva 5mm* 330mm','Tubo de Irrigacion y Succion Reusable' ],
          'Covidien': ['Engrapadora Endogia Articulada 45mm Morado','Engrapadora Endogia Articulada 60mm Morado','Engrapadora Endogia Articulada 45mm Vascular Dorado','Engrapadora Endogia Articulada 60mm Vascular Dorado','Engrapadora Endogia ultra 12mm','Engrapadora Endoclip 10mm M/L','Engrapadora Tri-Staple Extra 60mm Negro'],
          'Storz': ['Pinza Grasper'],
        },
        'manguera de insuflacion': { 
            'stryker': ['manguera','yugo CO2']
        },
        'pinza': {
          'Covidien': ['Blunt Tip 5mm-37cm','Maryland 5mm-37cm','Maryland 5mm-23cm','Small Jam 16.5mm-19cm','Exact Dissector 20.6mm-21cm']
        },
        'adaptador': { 
            'stryker': ['Adaptador cople de lente','Adaptador frontal de Insuflador','Adaptador Trasero de Insuflador'],
        },
        'case de transporte': {
            'GM': [ 'Camara y Fuente L9000','Camara 1688 y Fuente L11','Grabador e Insuflador','Monitor Vision Pro led','Monitor 4K Stryker','Monitor 4K SONY']
        },
        'charola de esterilizacion': {
          'Stryker': ['Camara IAM','Lente de Laparoscopia'],
          'Storz': ['Lente de Laparoscopia'],
          'Artrhex': ['Lente de Laparoscopia'],
          'Olympus': ['Lente de Laparoscopia'],
        },
        'clips para monitor': {
            'GM':[ 'Porta Monitor']},
      },

      endoscopia: {
        'procesador': {
          'Olympus': ['CV-160','CV-170','CV-180','CV-190','EVIS X1'],
          'Fujifilm': ['VP-4400','VP-4440HD','EP-6000','EP-7000'],
          'Pentax': ['EPK-i','EPK-i7010'],
        },
        'fuente de luz': {
          'Olympus': ['CLV-160','CLV-180','CLV-190'],
          'Fujifilm': ['XL-4400','XL-4450','BL-7000'],
          'Pentax': ['Prueba']
        },
        'broncoscopio': { 'Olympus': ['BF-XP160F'] },
        'colonoscopio': {
          'Olympus': ['CF-Q160L','CF-H180AL','CF-HQ190L'],
          'Fujinon': ['EC-250HL5','EC-600HL','EC-760R-V/L'],
          'Pentax': ['EC-3890LI'],
        },
        'duodenoscopio': { 
          'Olympus': ['JF-140F','TJF-160F','TJF-160VF','TJF-Q180V','TJF-Q180','TJF-Q90V'],
          'Fujinon': ['ED-530XT'],
          'Pentax': ['ED-34-I10T2'],
        },
        'gastroscopio': {
          'Olympus': ['GIF-Q160','GIF-XP160','GIF-1TQ160','GIF-2T160','GIF-180','GIF-H180','GIF-H180J','GIF-HQ190'],
          'Fujinon': ['EG-530N','EG-530WR','EG-600WR','EG-6400N','EG-760R'],
          'Pentax': ['EG-2990i'],
        },
        'argon plasma': { 'Erbe': ['ICC200','ICC300','VIO 300D','APC300', 'APC'] },
        'bomba de co2': { 'Fujinon': ['GW-100'] },
        'bomba de irrigacion': {
          'Olympus': ['UCR','OFP','OFP2'],
          'Medivators': ['Endogator EGP-100','Stratus EGA-500'],
          'Erbe': ['EIP 2'],
        },
        'bomba de secrecion': { 'Infusomat': ['Braun Sumalfit'] },
        'capturador de video': { 'Ugreen': ['HDMI'] },
        'convertidor de video': { 'GM': ['X003'] },
        'monitor': {
          'Olympus': ['OEV 262H','OEV 191H'],
          'Storz': ['4k 32"','Led 26"'],
          'Sony': ['HD 19"','4k 55"'],
        },
        'Adaptador': {
        'Valleylab': ['Adapatador Bipolar Azul Active Only'],
        'Erbe': ['Adaptador Bipolar ICC 200 ','Adaptador para Sonda ICC200 ICC300 VIO 300D','Sonda Circular'],
        'Generico': ['Adaptador para el canal de Biopsia']
        },
        'grabador': { 'KingMa': ['KM-YK980'] },
        'interfaz monopolar para erbe': { 'Erbe': ['Cable interfaz']
        },
            
        'eliminador': { 
            'Storz': ['4k 32"','Led 26"'],
            'Sony': ['HD 19"','4k 55"'],
        },
        'focos excelitas': {
          'PE300BFA': ['180-160-4400-4450-Xenon300'],
          'PE150AF': ['Fujinon-2200'],
          'Y1911': ['EPK-5010','EPKI-7010'],
          'Y1882': ['EPK-i'],
          'Y1964': ['EPK-5010','EPKI-7010'],
        },
        'Carro': { 
            'Olympus': ['Para sistema 160 o 180','Para sistema 190'],
            'Fujinon': ['Carro Original'],
            'GM':['Carro GM'],
        },
        'kit de limpieza': { 
            'Olympus': ['MH-946 para 160 180 y 190'],
            'Fujinon': ['WA-007 para 760'],
        },
        'linea de irrigacion': {
            'GM': ['Genericas'],
            'Medivators': ['OFP','OFP 2','Stratus'],
            },
        'contenedor de liquidos': {
            'Olympus': ['Serie 100','160','180','190'],
            'Fujinon': [ 'Serie 500 y 600','760','760 para Insuflador'],
            'Pentax': ['Serie 7010'],
        },
        'Pinzas de Endoscopia': {
        'Olympus': ['pinza de biopsia','pinza de biopsia hot','pinza de canasta','pinza de 4 hilos','pinza de extraccion','pinza de polipectomia'],
        'GM': ['Prueba']
        },
        'probador de fuga': { 
            'Olympus': ['Serie 160 180 190'],
            'Fujinon': [ 'Serie 500 y 600','Serie 760'],
            'Pentax': ['Serie 90i'],
        },
        'protector bucal de endoscopio': {'Olympus': ['MB-142 Olympus'],
            },
        'protector de punta de endoscopio': {
            'GM': ['Protector Azul']},
        'tapon de biopsia': {'GM': ['GM'],
            },
        'tapon-eto': {
            'Olympus': ['MH-553'],
        },
        'valvulas desechables': { },
        'valvulas reusables': {
            'Fujinon': [ 'Serie 760']},
        'yugo para argon': {'Erbe': ['ICC200','ICC300','VIO 300D','APC300', 'APC'],
            },
        'teclado': { 
           'Olympus': ['Serie 100','160','180','190'],
            'Fujinon': [ 'Serie 500 y 600','760'],
            'Pentax': ['Serie 7010'],
        },
        'mouse': {'GM': ['GM'],
            
            },
        'multicontacto': { },
        'pc siimed analogo': { },
        'pc siimed hd': { },
        'pigtail': { 
            'Olympus': ['Maj-1430']
        },
        'cable': { },
        'cable bipolar': { },
        'cable monopolar': { },
        'boquillas': { },
        'cepillo de limpieza': { },
        'capuchon distal': { },
      },

      quirofano: {
        'adaptador para ligasure': {
            'Cad':['LS8','Force FX','Force 2','Adaptador Bipolar LS8']
        },
        'ligasure ls8': { 'Medtronic': ['LS8'] },
        'electrocauterio': {
          'Valleylab': ['Force 2','Force FX','ForceTriad','FT10'],
          'Erbe': ['ICC 200','ICC 300','VIO 300D'],
          'Olympus': ['ESG-400',],
          'GM': ['CITADEL 300'],
          'Conmed': ['Sabre Genesis'],
        },
        'brazalete pani': { 
           'Datex-Ohmeda': ['Cardiocap5'],
            'Drager': ['Delta Infinity'],
            'Phillips': ['MP50 Intellivue','MP70 Intellivue'],
            'Mindray': ['V12'],
        },
        'Bomba de Infusion': {
            'Dre Med':[ 'NTx3 Plus'],
        },
        'maquina de anestesia': {
            'Datex-Ohmeda': ['Aestiva','Avance','Aisys','Aespire'],
        },
        'mesa de cirugia': {
            'Amsco': ['2080 Semielectrica y SemiTraslucida' ,'3080 Electrica y Traslucida'],
            'Maquet':['AlphaStart']
      },
        'lampara de quirofano': {
            'Stryker': ['Vision 2'],
            'Skytron': ['Aurora'],
      },
        'monitor signos vitales': {
            'Datex-Ohmeda': ['Cardiocap5'],
            'Drager': ['Delta Infinity'],
            'Phillips': ['MP50 Intellivue','MP70 Intellivue'],
            'Mindray': ['V12'],
        },
        'desfribilador': {
            'Phillips': ['Heartstart MRX'],
            'Zoll': ['AED plus'],
            },
        'bipap': {
            'Phillips':['Ventilador Respironics Nuevo']},
        'vaporizador': { 
            'Datex-Ohmeda': ['Tec 7 Aestiva-Aespire','Casette Aisys'],
        },
        'sensor de ecg': {
            'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
            'Drager': ['Delta Infinity'],
            'Datex Ohmeda':['Cardiocap5'],
            'Mindray': ['V12']
        },
        'sensor de spo2': {
            'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
            'Drager': ['Delta Infinity'],
            'Datex Ohmeda':['Cardiocap5'],
            'Mindray': ['V12']},
        'sensor pani': {
            'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
            'Drager': ['Delta Infinity'],
            'Datex Ohmeda':['Cardiocap5'],
            'Mindray': ['V12']
        },
        'sensor de temperatura': {
            'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
            'Drager': ['Delta Infinity'],
            'Datex Ohmeda':['Cardiocap5'],
            'Mindray': ['V12']
        },
        'pedal bipolar': {
            'Valleylab': ['Force 2','Force FX','ForceTriad','FT10'],
            'Conmed': ['Sabre Genesis'],
        },
        'pedal monopolar': { 
            'Valleylab': ['Force 2','Force FX','ForceTriad','FT10'],
            'Conmed': ['Sabre Genesis'],
            'Olympus': ['ESG-400']
        },
        'pedal ligasure': { 
            'Covidien':[ 'Pedal Bipolar Morado','Pedal Bipolar Anaranjado']
        },
        'placa para electrocauterio': {
            'OBS':['Placa desechable']},
        'lapiz para electrocauterio': {
            'Avante':['Placa desechable'],
            'OBS':['Placa desechable'],
            'Covidien': [ 'Placa desechable'],
            'Conmed':['Placa desechable'],
            'Smith&Nephew':['Placa desechable']
        },
        'Línea de Muestreo de CO2': {
            'Datex Ohmeda':['Aisys','Avance' ,'Cardiocap5'],
            'Phillips': ['Heartstart MRX'],
        },
        'cable para pinza bipolar': {
            'Covidien': [ 'Pinza Bipolar']},
        'cable trocal ecg': { 
            'Drager': ['Delta Infinity'],
        },
        'carro para electrocauterio': { 
            'Erbe': [ 'Para ERBE'],
            'Covidien': ['Force 2','Force FX','ForceTriad','FT10'],
        },
        'carro rojo Emergencias': {
            'Lifeline': [ 'Carro de Emergencias'],
            'GM': [ 'Carro de Emergencias NUEVO'],
        },
        'eliminador': {
            'Phillips': ['Fuente de poder Desfibrilador MRX']
            },
        'pieza de mano para gen11': {
            'Ethicon':[ 'Pieza con 4 usos','Pieza con 70 usos','Pieza con 87 usos']},
        'armonico gen11': { 
            'Ethicon':[ 'Armonico GEN11']
        },
      },

      hospitalizacion: {
        'aspirador': {
            'Hergon': ['7E-A NUEVO']
        },
        'cama hospitalaria electrica': {
            'Hill Roon':['Versacare',],
            'stryker':['MPS Secure II']
        },
        'camilla': { 
            'Hill Roon':['P8000'],
            'Stryker':['Prime Series','1015 Stretcher'],
        },
        'cuna termica': {
            'GE Healthcare':[' Panda Warmer']},
        'incubadora': {
            'GE': [' Giraffe'],
        },
        'mesa de exploracion': { },
        'ventilador': {
            'Nellcor': ['Puritan Benett 840']
        },
      },

      radiologia: {
        'arco en c': { },
        'bateria': { },
        'chasis': { },
        'flat panel': { },
        'rayos x rodable': { },
        'rayos x portatil': { },
      },

      urologia: {
        'cistoscopio': { },
        'histeroscopio': { },
        'resectoscopio': { },
        'ureteroscopio flexible': { },
        'ureteroscopio rigido': { },
      },

      artroscopia: {
        'shaver': { },
        'rasurador': { },
        'radio frecuencia': { },
        'puntas de radio frecuencia': {
          'Stryker': ['Cortadora Agresiva Plus 3.5mm x 80mm Amarillo','Cortadora Agresiva Plus 5.0mm x 125mm Azul','Cortadora Angular 4.0mm x 125mm Rojo','Cortadora Angular 5.0mm x 125mm Azul','Cortadora Resector 3.5mm x 125mm Amarillo','Cortadora XL Agresiva 4.0mm x 180mm Rojo','Fresa 5mm x 125mm Azul','Fresa de Abrasion 2.0mm x 80mm Morado','Fresa Redonda de 12 filos 5.5mm x 125mm Café','Fresa de Barril de 12 hilos 5.5mm x 125mm Cafe'],
        },
        'serfas de radiofrecuencia': { },
        'serfas energy': { },
        'bomba de irrigacion': { },
        'lente': { 'Stryker': ['30-4mm'] },
        'transmisores': { },
        'pedal': {
            'Arthocare': ['Coblator II']},
        'set de taladros de artroscopia': {
            'Stryker': [ 'System 7 Mandril llave']},
        'camisa con opturador': { },
        'cable para pedal': { },
        'cable para pieza de mano': { },
        'charola de esterilizacion': {
            'Stryker': ['Art-Stryker']},
        'bateria': { },
        'cargador de baterias': { 
            'Stryker': ['Taladros'],
        },
        'meditronic': { },
        'set de cirugia para hombro y tobillo': { },
        'set de cirugia de rodilla': { },
      },

      ceye: {
        'autoclave de camara 95 l': { },
        'monitor': { },
      },

      ginecologia: {
        'Camilla Ginecologíca': { 
            'Stryker': ['Geynnie'],
        },
        'Ultrasonido': {
                'GE': [ 'Logic P3'],
        },
        'mesa de exploracion': { 
            'Midmark': ['Modelo 404',' Ritte 622']
        },
      },

      material: {
          'Limpiador y Desengrasante': {
            'Steren': ['Desengrasante Y Limpiador']
      },
    },
    },
    

    /* PLANTILLAS por SUBTIPO (keys tal cual, las buscamos por slug) */
    plantillasPorSubtipo: {
      'Camara': ["Cable de alimentación","Cable de video","Coupler","Cabezal","Soporte de cabezal", "Interfaz"],
      'Camara con cabezal': ["Cabezal","Coupler","Cable de video","Cable de alimentación","Soporte de cabezal", "Interfaz"],
      'Fuente de Luz': ["Cable de alimentación","Fibra de luz","Adaptador para fibra","Filtro/portafiltro"],
      'Fuente de Luz L9000': ["Consola","Fibra de luz blanca","Fuente de luz gris","Cable de poder"],
      'Fuente de Luz l10': ["Consola","Fibra de luz verde","Adaptador para fibra","Cable interfaz USB–CCU (azul)","Cable de poder"],
      'Fuente de luz l11': ["Consola","Fibra de luz verde","Adaptador para fibra","Cable interfaz USB–CCU (azul)","Cable de poder"],
      'Insuflador': ["Consola","Manguera","Yugo","Adaptador trasero de CO2","Adaptador frontal","Cable de poder","Linea de insuflacion con filtro"],
      'Insuflador pnemosure 45 lts': ["Consola","Manguera","Yugo","Adaptador trasero de CO2","Adaptador frontal","Cable de poder","Linea de insuflacion con filtro"],
      'Insuflador core 40 lts': ["Consola","Manguera","Yugo","Adaptador trasero de CO2","Cable de poder","Linea de insuflacion con filtro"],
      'Grabador': ["Cable de alimentación","Cable de video/HDMI","Control remoto","SSD/USB (opcional)"],
      'Monitor': ["Cable de alimentación","Eliminador","HDMI/SDI","Base/pedestal (si aplica)"],
      'Lente': ["Barril","Tapa frontal/trasera","Caja de cartón","Charola de esterilización"],
      'Lente de Laparoscopia': ["Barril","Barril de clip","Caja de cartón","Charola para esterilización","Tapa frontal/trasera"],
      'Lente de Artroscopia': ["Barril de cuerda","Barril de clip","Caja de cartón","Charola para esterilización","Camisa o cánula","Punzón"],
      'Broncoscopio': ["Unidad flexible","Válvulas de succión/biopsia","Estuche","Tapas"],
      'Colonoscopio': ["Unidad flexible","Válvulas","Estuche","Tapas"],
      'Duodenoscopio': ["Unidad flexible","Válvulas","Estuche","Tapas","Guía protectora (opcional)"],
      'Gastroscopio': ["Unidad flexible","Válvulas","Estuche","Tapas"],
      'Clarity': ["Cable de alimentación","Cable de video","Módulo clarity","Soporte"],
      'Transmisores': ["Cable de alimentación","Cable de video","Llave azul/pareado","Base de carga (si aplica)"],
      'Crossfire2': ["Pieza de mano de pedal", "Cable de poder","Kits de irrigación (opc.)"],
      'Core': ["Cable de alimentación fórmula Core","Charola de sierras y taladros (opcional)"],
      'Video Carro': ["Brazo","Soporte","2 puertas de acrílico","4 charolas","Cajón","Llave para puerta","Multicontacto","Cable de poder","Ruedas/frenos"],
      'Regulador de co2 Endoscopia': ["Regulador","Manguera CO2","Manómetros","Yugo"],
      'Argón plasma': ["Consola","Cable de poder","Sonda argón (consumible)"],
      'Procesador': ["Consola/procesador","Cable CCU","Cable de poder"],
      'Sistema': ["Consola principal","Cables de interconexión","Cable de poder"],
      'Sistema Endoscopia': ["Procesador","Fuente de luz","Cámara","Cables","Video grabador (opc.)","Carro"],
      'Tapon-Eto': ["Tapón","Anillos/oring"],
      'Teclado': ["Teclado","Cable USB/dongle"],
      'Mouse': ["Mouse","Dongle o cable USB"],
      'Pigtail': ["Conector pigtail","Ajustes/oring"],
      'Probador': ["Unidad principal","Manguera","Adaptadores"],
      'Probador de fuga': ["Unidad principal","Manguera","Adaptadores","Aceite/sello (opc.)"],
      'Carrito': ["Estructura","Ruedas","Frenos","Multicontacto"],
      'Kit de limpieza': ["Cepillos","Jeringas","Tapones","Bandeja"],
      'Línea de irrigación': ["Línea de irrigación","Conectores","Clamp"],
      'Boquillas': ["Boquillas surtidas","Estuche"],
      'Pinza de Biopsia': ["Pinza","Estuche"],
      'Pinza de Biopsia hot': ["Pinza Hot","Conector RF"],
      'Pinza de Extraccion': ["Pinza/Canastilla","Estuche"],
      'Pinza de Polipectomia': ["Lazo polipectomía","Conector","Estuche"],
      'Contenedor de Liquidos': ["Contenedor","Tapa","Mangueras"],
      'Eliminador': ["Eliminador","Cable AC"],
      'Erbe': ["Pedal","Eliminador"],
      'Cable': ["Cable","Capuchones"],
      'Cable bipolar': ["Cable bipolar","Conectores"],
      'Adaptador usb': ["Adaptador","Cable USB"],
      'Adaptador para sonda': ["Adaptador","Anillos/oring"],
      'Adaptador': ["Cuerpo adaptador","Anillos/oring"],
      'Adaptador para ligasure': ["Adaptador","Cable interfaz","Manual (opc.)"],
      'Armonico Gen11': ["Consola armónica","Cable pieza de mano","Pedal (si aplica)"],
      'Cabezal': ["Cabezal","Coupler","Cable"],
      'Cable usb': ["Cable USB-A/C","Fijadores"],
      'Camilla': ["Colchón","Barandales","Frenos","Portasueros (opc.)"],
      'Charolas de esterilización': ["Charolas","Tapas","Separadores"],
      'Clips para Monitor': ["Clips","Tornillería","Llave Allen"],
      'Fibra de Luz': ["Fibra","Protectores de punta"],
      'Lampara Xenon': ["Consola","Lámpara/bulbo","Cable de poder"],
      'Maletin/case': ["Case","Espuma interna"],
      'Manguera de insuflación': ["Manguera","Conectores","Filtro"],
      'Manguera para Bomba de Agua': ["Manguera","Conectores"],
      'Manguera y Yugo': ["Manguera","Yugo","Conectores"],
      'Pedestal': ["Base","Poste","Abrazaderas"],
      'Pieza de mano': ["Pieza de mano","Cable","Electrodos (opc.)"],
      'Pinza': ["Pinza","Inserto","Mango"],
      'Rasurador y radio frecuencia': ["Consola","Pieza de mano","Cables RF","Pedal"],
      'Transmisor': ["Transmisor","Base/clip","Cable"],
      'Trocar': ["Trocar","Vaina","Obturador"],
      'Video Grabador': ["Grabador","Cable de poder","HDMI/SDI"],
      'Yugo': ["Yugo","Tornillería"],
      'Arco en C': ["Arco en C","Disparador","Gabinete de pantallas","Impresora","Cable de poder","Baterías","Frenos","Protecciones de transporte"],
      'Desfribilador': ["Consola","Sensor SpO2","Sensor de temperatura","Sensor de CO2","Trampa de agua CO2","Cable de poder","Baterías","Palas para desfibrilar"],
      'Maquina de Anestesia': ["Vaporizador","Circuito","Absorbedor CO2","Frenos","Control","Batería","Cable de poder"],
      'Mesa de Cirugía': ["Colchón","Frenos","Pierneras","Portapierneras","Control","Batería","Cable de poder"],
      'Mesa de Cirugía 3080': ["Colchón","Frenos","Pierneras","Portapierneras","Control","Batería","Cable de poder"],
      'Lampara de Cirugía': ["Cúpulas","Brazos","Control de intensidad","Fuente"],
      'Lampara de Quirófano': ["Cúpulas","Brazos","Control","Fuente"],
      'Consola Quirurgica': ["Consola","Cables","Pedal (si aplica)"],
      'Monitor Signos Vitales': ["Cables ECG","Manguito NIBP","Sensor SpO2","Temperatura","CO2 (si aplica)","Cable de poder"],
      'electrocauterios': ["Consola","Cable de alimentación","Pedal monopolar","Pedal bipolar (si aplica)","Lápiz","Placa"],
      'electrocauterio': ["Consola","Cable de alimentación","Pedal monopolar","Lápiz","Placa"],
      'aspirador': ["Frasco colector","Tapa","Manguera","Filtro","Cable de poder"],
      'cama hospitalaria electrica': ["Colchón","Control paciente","Barandales","Ruedas/frenos","Portasueros (opc.)","Cable de poder"],
      'cama': ["Cable de poder","Colchón","Control para paciente"],
      'camillas': ["Colchón","Frenos","Llantas","Portasueros (opc.)"],
      'cuna termica': ["Colchón","Sensor de temperatura","Acrílicos","Calefactor","Ruedas"],
      'cunas termicas': ["Colchón","Sensor de temperatura","Llantas","Acrílicos"],
      'incubadora': ["Cúpula","Bandeja","Humidificador","Sensores","Ruedas"],
      'mesa de exploracion': ["Colchón","Cajones","Papelera","Descansa-brazos"],
      'ventilador': ["Cámara","Celda de oxígeno","Compresor","Cable de poder","Circuito","Humidificador (opc.)"],
      'ligasura s8': ["Consola","Cable de alimentación","Adaptador para pinzas","Pedal (si aplica)"],
      'force triad': ["Consola","Cable de alimentación","Pedal monopolar","Lápiz","Placa"],
      'gen11': ["Consola","Cable de alimentación","Adaptador Harmónico","Pieza de mano gris","Pedal (si aplica)"],
      'cama de ginecologia': ["Colchón","Pierneras","Frenos","Control"],
      'autoclave de camara 95 l': ["Cámara","Charolas","Juntas","Cable de poder","Mangueras de drenaje"],
      'shaver': ["Cable de poder"],
    },

    /* GETTERS */
    get tiposKeys(){ return Object.keys(this.tiposEquipos) },
    get subtiposArr(){ return this.tipoSel ? this.tiposEquipos[this.tipoSel] : [] },

    get marcasArr(){
      if(!this.tipoSel || !this.subtipoSel) return [];
      const tipoSlug = this.slug(this.tipoSel);
      const subSlug  = this.slug(this.subtipoSel);
      const tipoNode = this.marcasModelosPorSubtipo?.[tipoSlug];
      if (!tipoNode) return [];

      let node = tipoNode[subSlug];
      if (!node) {
        // fallback por si hay diferencias pequeñas en el nombre
        for (const [k, val] of Object.entries(tipoNode)) {
          if (this.slug(k) === subSlug) {
            node = val;
            break;
          }
        }
      }
      return node ? Object.keys(node) : [];
    },

    get modelosArr(){
      if(!this.tipoSel || !this.subtipoSel || !this.marcaSel) return [];
      const tipoSlug = this.slug(this.tipoSel);
      const subSlug  = this.slug(this.subtipoSel);
      const tipoNode = this.marcasModelosPorSubtipo?.[tipoSlug];
      if (!tipoNode) return [];

      let node = tipoNode[subSlug];
      if (!node) {
        for (const [k, val] of Object.entries(tipoNode)) {
          if (this.slug(k) === subSlug) {
            node = val;
            break;
          }
        }
      }

      const arr = node?.[this.marcaSel] || [];
      return Array.isArray(arr) ? arr : [];
    },

    onMarcaChange(){
      this.modeloSel = '';
    },

    get sheetHeight(){
      const n = this.sheet?.lista?.length || 0;
      if (n <= 1) return '25dvh';
      if (n <= 3) return '50dvh';
      return '75dvh';
    },

    tipoSel:'', subtipoSel:'',
    marcaSel:'', modeloSel:'',

    previews:[null,null,null], videoUrl:null,
    firmaData:'', isDrawing:false,

    sheet:{ lista:[] },
    seleccion:[],

    baseSerie: '',
    cantidadSerie: 1,

    /* Loader multi-step */
    loading:false,
    loadingStep:0,
    loadingTexts:[
      'Validando información capturada',
      'Guardando registro en el inventario',
      'Generando lote de equipos por serie',
      'Registrando componentes del subtipo',
      'Subiendo evidencias e imagen de firma',
      'Preparando regreso a la lista de equipos'
    ],
    loadingTimer:null,

    init(){
      this.directoStock = false;
      this.estadoProceso = 'registro';

      this.$nextTick(()=>{ this.initCanvas(); });
      this.$watch('tipoSel', ()=> this.queueCheck());
      this.$watch('subtipoSel', ()=> this.queueCheck());
    },

    queueCheck(){
      clearTimeout(this.checkDebounce);
      this.checkDebounce = setTimeout(()=> this.checkSeriesNow(false), 550);
    },

    titleCase(s){
      return (s||'').replace(/_/g,' ').replace(/\w\S*/g,t=>t.charAt(0).toUpperCase()+t.slice(1))
    },

    slug(s){
      return (s||'').toString().toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
        .replace(/\s+/g,' ').trim();
    },

    genSeries(){
      const base = (this.baseSerie || '').trim();
      const n = Number(this.cantidadSerie || 0);

      if (!base || !n || n <= 0) {
        this.toast('Captura la serie base y una cantidad válida', false);
        return;
      }

      const max = 500;
      const count = Math.min(n, max);

      const m = base.match(/^(.*?)(\d+)(\s*)$/);
      const list = [];

      if (m) {
        const prefix = m[1];
        const numStr = m[2];
        const suffix = m[3] ?? '';
        let current = parseInt(numStr, 10);
        const width = numStr.length;

        for (let i = 0; i < count; i++) {
          const num = String(current + i).padStart(width, '0');
          list.push(prefix + num + suffix);
        }
      } else {
        for (let i = 0; i < count; i++) {
          list.push(`${base}-${i + 1}`);
        }
      }

      this.$nextTick(() => {
        if (this.$refs.serieField) {
          this.$refs.serieField.value = list.join('\n');
          this.$refs.serieField.dispatchEvent(new Event('input'));
          this.checkSeriesNow(true);
        }
      });
    },

    onTipoChange(){
      this.subtipoSel='';
      this.marcaSel='';
      this.modeloSel='';
      this.sheet.lista=[];
      this.syncSeleccion();
    },

    onSubtipoChange(){
      this.marcaSel = '';
      this.modeloSel = '';

      const slugSel = this.slug(this.subtipoSel);
      let base = [];

      // Buscar plantilla por slug del nombre de subtipo
      for (const [k, arr] of Object.entries(this.plantillasPorSubtipo)) {
        if (this.slug(k) === slugSel) {
          base = arr;
          break;
        }
      }

      this.sheet.lista = (base || []).map(n => ({
        nombre:n,
        cantidad:1,
        incluido:true
      }));
      this.syncSeleccion();
    },

    syncSeleccion(){
      this.seleccion = this.sheet.lista
        .filter(x => x.cantidad > 0)
        .map(x => ({ nombre:x.nombre, cantidad:x.cantidad, incluido:!!x.incluido }));
    },
    restaurarPlantilla(){ this.onSubtipoChange(); },

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
        draw=false;
        this.isDrawing=false;
        this.firmaData=canvas.toDataURL('image/png');
      };

      canvas.addEventListener('mousedown',start);
      canvas.addEventListener('mousemove',move);
      window.addEventListener('mouseup',end);
      canvas.addEventListener('touchstart',start,{passive:false});
      canvas.addEventListener('touchmove',move,{passive:false});
      canvas.addEventListener('touchend',end);

      const ctxc=canvas.getContext('2d');
      ctxc.fillStyle='#fff';
      ctxc.fillRect(0,0,canvas.width,canvas.height);
      this.firmaData=canvas.toDataURL('image/png');

      window.addEventListener('resize', ()=>this.initCanvas(), {once:true});
    },

    clearSig(){
      const c=this.$refs.canvas, ctx=c.getContext('2d');
      ctx.fillStyle='#fff';
      ctx.fillRect(0,0,c.width,c.height);
      this.firmaData=c.toDataURL('image/png');
    },

    inc(i){ this.sheet.lista[i].cantidad=(this.sheet.lista[i].cantidad||0)+1; this.syncSeleccion(); },
    dec(i){ this.sheet.lista[i].cantidad=Math.max(0,(this.sheet.lista[i].cantidad||0)-1); this.syncSeleccion(); },

    startLoader(){
      this.loading = true;
      this.loadingStep = 0;
      if(this.loadingTimer) clearInterval(this.loadingTimer);
      const total = this.loadingTexts.length;
      this.loadingTimer = setInterval(() => {
        if(!this.loading){
          clearInterval(this.loadingTimer);
          this.loadingTimer = null;
          return;
        }
        if(this.loadingStep < total - 1){
          this.loadingStep++;
        }
      }, 1800);
    },
    stopLoader(){
      this.loading = false;
      if(this.loadingTimer){
        clearInterval(this.loadingTimer);
        this.loadingTimer = null;
      }
    },

    async submit(){
      const raw = this.$refs.serieField?.value || '';
      const list = this.parseSeries(raw);
      const localDups = this.findLocalDuplicates(list);
      this.dupLocalList = localDups;

      if(localDups.length){
        this.toast(`Hay números de serie repetidos en tu captura: ${localDups.join(', ')}`, false);
        return;
      }

      try{
        const unique = Array.from(new Set(list.map(x => x.trim()).filter(Boolean)));
        const out = await this.checkSeriesOnServer(unique);
        const exists = Array.isArray(out?.duplicates) ? out.duplicates : [];
        this.dupServerList = exists;

        if(exists.length){
          this.toast(`No se puede guardar. Ya existe(n): ${exists.join(', ')}`, false);
          this.$refs.serieField?.focus();
          return;
        }
      }catch(e){
        this.toast('No se pudo verificar si las series ya existen. Intenta de nuevo.', false);
        return;
      }

      const fd = new FormData(document.getElementById('frmRegistro'));
      fd.set('estado_proceso', this.estadoProceso || (this.directoStock ? 'stock' : 'registro'));

      this.syncSeleccion();
      this.seleccion.forEach((r,i)=>{
        fd.append(`componentes[${i}][nombre]`, r.nombre);
        fd.append(`componentes[${i}][cantidad]`, r.cantidad);
        fd.append(`componentes[${i}][incluido]`, r.incluido ? 1 : 0);
      });

      this.startLoader();

      try{
        const res = await fetch(@json(route('registros.guardar')), {
          method : 'POST',
          headers: { Accept:'application/json' },
          body   : fd
        });

        const json = await res.json().catch(()=> ({}));

        if(!res.ok || !json.success){
          this.stopLoader();

          const dup = (json?.duplicates || json?.duplicated || json?.exists);
          if(Array.isArray(dup) && dup.length){
            this.toast(`No se puede guardar. Ya existe(n): ${dup.join(', ')}`, false);
            return;
          }

          this.toast(json?.error || 'Error al guardar', false);
          return;
        }

        try{
          if (Array.isArray(json.registros_ids) && json.registros_ids.length) {
            localStorage.setItem('lote_registros', JSON.stringify(json.registros_ids));
            this.toast(`Lote preparado para procesos masivos (${json.registros_ids.length} equipo(s)).`, true);
          }
        }catch(e){}

        this.stopLoader();
        this.toast(json.message || 'Registro guardado correctamente', true);

        if(json.imprimir_barcode_url){
          this.toast(
            `<a href="${json.imprimir_barcode_url}" target="_blank" class="text-decoration-none">Imprimir etiqueta</a>`,
            true,
            true
          );
        }

        setTimeout(()=>{
          window.location.href=@json(url()->previous());
        }, 1200);

      }catch(e){
        this.stopLoader();
        this.toast('Error de red/servidor', false);
      }
    },

    toast(msg, ok=true, html=false){
      const id='t'+Date.now();
      const el=document.createElement('div');
      el.className='toast align-items-center border-0 shadow-sm';
      el.id=id;
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
