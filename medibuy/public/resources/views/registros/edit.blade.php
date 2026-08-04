@extends('layouts.app')
@section('title','Editar producto')
@section('titulo','Editar')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

@php
  $router = app('router');

  $updateUrl = $router->has('registros.update')
    ? route('registros.update', $registro->id)
    : url('/registros/'.$registro->id);

  if (!isset($componentes)) {
    $componentes = \Illuminate\Support\Facades\DB::table('inv_registro_componentes')
      ->where('registro_id', $registro->id)
      ->select('nombre_cache as nombre', 'cantidad', 'incluido')
      ->orderBy('nombre_cache')->get();
  }

  $initial = [
    'tipo'    => $registro->tipo_equipo,
    'subtipo' => $registro->subtipo_equipo,
    'marca'   => $registro->marca,
    'modelo'  => $registro->modelo,
    'serie'   => $registro->numero_serie,
    'anio'    => $registro->anio,
    'descripcion' => $registro->descripcion,
    'fecha'   => optional($registro->fecha_adquisicion)->format('Y-m-d'),
    'observaciones' => $registro->observaciones,

    'evid1' => $registro->evidencia1,
    'evid2' => $registro->evidencia2,
    'evid3' => $registro->evidencia3,
    'video' => $registro->video,
    'firma' => $registro->firma_digital,

    'componentes' => $componentes->map(fn($c)=>[
      'nombre'=>$c->nombre, 'cantidad'=>(int)$c->cantidad, 'incluido'=>(int)$c->incluido
    ])->values(),
  ];
@endphp

<style>
:root{ --bg:#f6f8fb; --ink:#0f172a; --muted:#6b7280; --line:#e7ebf0; --brand:#cfeee7; --brand-ink:#145b56; }
body{ background:var(--bg); font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif }
.card-soft{ background:#fff; border:1px solid var(--line); border-radius:16px; box-shadow:0 12px 40px rgba(17,24,39,.06) }
.heading-xl{ font-weight:800; font-size:clamp(22px,3vw,30px); color:var(--ink) }
.text-muted-ux{ color:var(--muted) }
.btn-pastel{ border:none; border-radius:12px; padding:.7rem 1.1rem; font-weight:700; color:#0b3634; background:var(--brand) }
.btn-ghost{ border-radius:12px; border:1px solid var(--line); background:#fff; color:var(--ink); font-weight:700 }

.form-control,.form-select{ border-radius:12px; border:1px solid var(--line); padding:.9rem 1rem }
.form-control:focus,.form-select:focus{ border-color:var(--brand-ink); box-shadow:none }

.ux-float{ position:relative }
.ux-float .form-control,.ux-float .form-select{ padding:1.25rem 1rem .55rem }
.ux-float label{
  position:absolute; left:12px; top:50%; transform:translateY(-50%);
  font-weight:600; color:#93a1b2; pointer-events:none; transition:all .16s ease
}
.ux-float:focus-within label, .ux-float.has-value label,
.ux-float .form-control:not(:placeholder-shown)+label{
  top:.45rem; transform:none; font-size:.75rem; color:var(--brand-ink)
}
.ux-float textarea.form-control{ min-height:110px; padding-top:1.6rem }

/* Zona evidencias */
.upload-zone{ border:1px dashed var(--line); border-radius:14px; padding:1rem; background:#fff }
.tile{
  position:relative; width:100%; aspect-ratio:1/1; border:1px solid var(--line);
  border-radius:12px; background:#fafcff; display:grid; place-items:center; overflow:hidden;
  cursor:pointer; transition:transform .08s ease, box-shadow .08s ease;
}
.tile:active{ transform:scale(.995) }
.tile:hover{ box-shadow:0 8px 24px rgba(17,24,39,.06) }
.tile img,.tile video{ width:100%; height:100%; object-fit:cover; pointer-events:none }
.tile-hint{
  position:absolute; inset:auto 8px 8px auto; background:#ffffffd9; border:1px solid var(--line);
  border-radius:999px; padding:.2rem .6rem; font-size:.75rem; font-weight:700; color:var(--brand-ink)
}
.tile-empty{ display:flex; flex-direction:column; align-items:center; gap:.35rem; color:#94a3b8; font-weight:600; font-size:.95rem }
.tile-empty i{ font-size:1.25rem }

/* Componentes */
.badge-soft{ background:#e9fbf7; color:var(--brand-ink); border-radius:999px; padding:.25rem .55rem; font-weight:700; font-size:.75rem }
.comp-card{ border:1px solid var(--line); border-radius:12px; background:#fff; padding:.7rem }
.comp-name{ font-weight:700; color:var(--ink) }
.comp-note{ font-size:.85rem; color:var(--muted) }

/* Toasts + bottom sheet */
#toastPlace{ position:fixed; top:1rem; right:1rem; z-index:1080 }
.offcanvas-bottom{ border-top-left-radius:16px; border-top-right-radius:16px }
.offcanvas .offcanvas-header h5{ color:var(--brand-ink); font-weight:800 }
#componentesSheet{ padding-bottom: env(safe-area-inset-bottom); transition: height .2s ease; max-height: 90dvh; }
</style>

<div
  class="container py-4"
  x-data="EditarUI({{ json_encode($initial, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) }}, '{{ $updateUrl }}', '{{ csrf_token() }}')"
  x-init="boot()"
>
  <div class="card-soft p-3 p-md-4">
    <div class="d-flex align-items-center justify-content-between pb-3 border-bottom">
      <div>
        <div class="heading-xl">Editar producto</div>
        <div class="text-muted-ux">Actualiza los datos, evidencias, componentes y firma.</div>
      </div>
      <a href="{{ url()->previous() }}" class="btn btn-ghost d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <form id="frmEdit" class="mt-4" enctype="multipart/form-data" @submit.prevent="submit">
      @csrf
      @method('PUT')
      <input type="hidden" name="firmaDigital" x-model="firmaData">

      <div class="row g-4">
        {{-- IZQUIERDA --}}
        <div class="col-lg-8">
          <div class="row g-3">
            {{-- Tipo --}}
            <div class="col-md-6">
              <div class="ux-float" :class="{'has-value': tipoSel}">
                <select
                  class="form-select"
                  name="Tipo_de_Equipo"
                  id="eq_tipo"
                  x-model="tipoSel"
                  @change="onTipoChange(true)"
                  required
                >
                  <option value="" hidden></option>
                  <template x-for="t in tiposKeys" :key="'t-'+t">
                    <option :value="t" x-text="titleCase(t)"></option>
                  </template>
                </select>
                <label for="eq_tipo">Tipo de equipo *</label>
              </div>
            </div>

            {{-- Subtipo --}}
            <div class="col-md-6">
              <div class="ux-float" :class="{'has-value': subtipoSel}">
                <select
                  class="form-select"
                  name="Subtipo_de_Equipo"
                  id="eq_sub"
                  x-model="subtipoSel"
                  @change="onSubtipoChange(true)"
                  :disabled="!tipoSel"
                  required
                >
                  <option value="" hidden></option>
                  <template x-for="s in subtiposArr" :key="'s-'+s">
                    <option :value="s" x-text="s"></option>
                  </template>
                </select>
                <label for="eq_sub">Subtipo *</label>
              </div>
            </div>

            {{-- Marca --}}
            <div class="col-md-6">
              <div class="ux-float" :class="{'has-value': marcaSel}">
                <select
                  class="form-select"
                  name="Marca"
                  id="eq_marca"
                  x-model="marcaSel"
                  @change="onMarcaChange"
                  :disabled="!tipoSel || !subtipoSel"
                  required
                >
                  <option value="" hidden></option>
                  <template x-for="m in marcasArr" :key="'m-'+m">
                    <option :value="m" x-text="m"></option>
                  </template>
                </select>
                <label for="eq_marca">Marca *</label>
              </div>
              <small class="text-muted-ux d-block mt-1"
                     x-show="tipoSel && subtipoSel && !marcasArr.length && !marcaSel">
                No hay marcas configuradas para este subtipo.
              </small>
            </div>

            {{-- Modelo --}}
            <div class="col-md-6">
              <div class="ux-float" :class="{'has-value': modeloSel}">
                <select
                  class="form-select"
                  name="Modelo"
                  id="eq_modelo"
                  x-model="modeloSel"
                  :disabled="!marcaSel"
                  required
                >
                  <option value="" hidden></option>
                  <template x-for="mo in modelosArr" :key="'mo-'+mo">
                    <option :value="mo" x-text="mo"></option>
                  </template>
                </select>
                <label for="eq_modelo">Modelo *</label>
              </div>
              <small class="text-muted-ux d-block mt-1"
                     x-show="marcaSel && !modelosArr.length && !modeloSel">
                No hay modelos configurados para esta marca.
              </small>
            </div>

            {{-- Serie, año, descripción, fecha, observaciones --}}
            <div class="col-md-6">
              <div class="ux-float">
                <input class="form-control" type="text" name="Numero_de_Serie" id="eq_serie" placeholder=" " x-model="form.serie" required>
                <label for="eq_serie">Número de serie *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="ux-float">
                <input class="form-control" type="text" name="Año" id="eq_anio" placeholder=" " x-model="form.anio" inputmode="numeric" pattern="[0-9]{4}">
                <label for="eq_anio">Año</label>
              </div>
            </div>

            <div class="col-12">
              <div class="ux-float">
                <textarea class="form-control" name="descripcion" id="eq_desc" placeholder=" " x-model="form.descripcion" required></textarea>
                <label for="eq_desc">Descripción *</label>
              </div>
            </div>

            <div class="col-md-6">
              <div class="ux-float">
                <input class="form-control" type="date" name="fecha_inicial" id="eq_fecha" placeholder=" " x-model="form.fecha" required>
                <label for="eq_fecha">Fecha de adquisición *</label>
              </div>
            </div>

            <div class="col-12">
              <div class="ux-float">
                <input class="form-control" type="text" name="observaciones" id="eq_obs" placeholder=" " x-model="form.observaciones">
                <label for="eq_obs">Observaciones</label>
              </div>
            </div>
          </div>

          {{-- Evidencias --}}
          <div class="upload-zone mt-4">
            <div class="row g-3">
              <div class="col-6 col-md-3">
                <div class="tile" role="button" tabindex="0"
                     @click="$refs.img0.click()" @keydown.enter.prevent="$refs.img0.click()" @keydown.space.prevent="$refs.img0.click()">
                  <input type="file" class="d-none" accept="image/*" name="evidencia1" x-ref="img0" @change="previewImg($event,0)">
                  <template x-if="previews[0]"><img :src="previews[0]"></template>
                  <template x-if="!previews[0]">
                    <div class="tile-empty"><i class="bi bi-image"></i><span>Imagen 1</span><small class="text-muted">Toca para subir</small></div>
                  </template>
                  <span class="tile-hint" x-show="previews[0]">Cambiar</span>
                </div>
              </div>

              <div class="col-6 col-md-3">
                <div class="tile" role="button" tabindex="0"
                     @click="$refs.img1.click()" @keydown.enter.prevent="$refs.img1.click()" @keydown.space.prevent="$refs.img1.click()">
                  <input type="file" class="d-none" accept="image/*" name="evidencia2" x-ref="img1" @change="previewImg($event,1)">
                  <template x-if="previews[1]"><img :src="previews[1]"></template>
                  <template x-if="!previews[1]">
                    <div class="tile-empty"><i class="bi bi-image"></i><span>Imagen 2</span><small class="text-muted">Toca para subir</small></div>
                  </template>
                  <span class="tile-hint" x-show="previews[1]">Cambiar</span>
                </div>
              </div>

              <div class="col-6 col-md-3">
                <div class="tile" role="button" tabindex="0"
                     @click="$refs.img2.click()" @keydown.enter.prevent="$refs.img2.click()" @keydown.space.prevent="$refs.img2.click()">
                  <input type="file" class="d-none" accept="image/*" name="evidencia3" x-ref="img2" @change="previewImg($event,2)">
                  <template x-if="previews[2]"><img :src="previews[2]"></template>
                  <template x-if="!previews[2]">
                    <div class="tile-empty"><i class="bi bi-image"></i><span>Imagen 3</span><small class="text-muted">Toca para subir</small></div>
                  </template>
                  <span class="tile-hint" x-show="previews[2]">Cambiar</span>
                </div>
              </div>

              <div class="col-6 col-md-3">
                <div class="tile" role="button" tabindex="0"
                     @click="$refs.vid.click()" @keydown.enter.prevent="$refs.vid.click()" @keydown.space.prevent="$refs.vid.click()">
                  <input type="file" class="d-none" accept="video/*" name="video-evidencia" x-ref="vid" @change="previewVideo($event)">
                  <template x-if="videoUrl"><video :src="videoUrl" muted autoplay loop playsinline></video></template>
                  <template x-if="!videoUrl">
                    <div class="tile-empty"><i class="bi bi-play-btn"></i><span>Video</span><small class="text-muted">Toca para subir</small></div>
                  </template>
                  <span class="tile-hint" x-show="videoUrl">Cambiar</span>
                </div>
              </div>
            </div>
          </div>

          {{-- Firma --}}
          <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <label class="form-label fw-semibold mb-0">Firma digital *</label>
              <span class="badge-soft" x-show="isDrawing">dibujando…</span>
            </div>

            <template x-if="!reFirmar && firmaExistente">
              <div class="border rounded-3 p-2">
                <img :src="firmaExistente" alt="Firma actual" style="max-height:170px; width:100%; object-fit:contain">
              </div>
            </template>

            <template x-if="reFirmar || !firmaExistente">
              <div>
                <div class="border rounded-3 p-2">
                  <canvas x-ref="canvas" style="width:100%; height:170px; display:block"></canvas>
                </div>
                <div class="mt-2 d-flex gap-2">
                  <button type="button" class="btn btn-ghost" @click="clearSig()">Limpiar</button>
                </div>
              </div>
            </template>

            <div class="mt-2">
              <button type="button" class="btn btn-pastel" @click="toggleFirma()"
                x-text="reFirmar ? 'Conservar firma actual' : 'Reemplazar firma'"></button>
              <small class="text-muted-ux ms-2">Si no reemplazas, se conservará la firma actual.</small>
            </div>
          </div>

          {{-- Móvil: ver componentes --}}
          <div class="d-md-none mt-4">
            <button type="button" class="btn btn-pastel w-100" data-bs-toggle="offcanvas" data-bs-target="#componentesSheet">
              Ver componentes del subtipo
            </button>
          </div>
        </div>

        {{-- DERECHA: Componentes --}}
        <div class="col-lg-4 d-none d-md-block">
          <div class="card-soft p-3">
            <div class="fw-bold" style="color:var(--brand-ink)">Componentes del subtipo</div>
            <small class="text-muted-ux">Se generan por <b>Subtipo</b> y ya incluyen lo guardado. Desmarca si “no viene”.</small>

            <div class="mt-3" x-show="!sheet.lista.length">
              <div class="text-muted-ux">Selecciona un <b>Subtipo</b> para ver los componentes.</div>
            </div>

            <div class="mt-3" x-show="sheet.lista.length">
              <template x-for="(c, idx) in sheet.lista" :key="'desk-'+idx">
                <div class="comp-card mb-2">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <div class="comp-name" x-text="c.nombre"></div>
                      <div class="comp-note">Esperado/Guardado</div>
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

      {{-- Hidden componentes --}}
      <template x-for="(row, i) in seleccion" :key="'hid-'+i">
        <div>
          <input type="hidden" :name="`componentes[${i}][nombre]`"   :value="row.nombre">
          <input type="hidden" :name="`componentes[${i}][cantidad]`" :value="row.cantidad">
          <input type="hidden" :name="`componentes[${i}][incluido]`" :value="row.incluido ? 1 : 0">
        </div>
      </template>

      <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
        <a href="{{ url()->previous() }}" class="btn btn-ghost">Cancelar</a>
        <button type="submit" class="btn btn-pastel">Actualizar</button>
      </div>
    </form>
  </div>

  <div id="toastPlace" class="toast-container position-fixed"></div>

  {{-- Bottom sheet móvil --}}
  <div id="componentesSheet" class="offcanvas offcanvas-bottom" tabindex="-1" :style="{ height: sheetHeight }">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Componentes del subtipo</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body small">
      <p class="text-muted-ux mb-2">Desmarca si “no viene”.</p>

      <template x-for="(c, idx) in sheet.lista" :key="'m-'+idx">
        <div class="comp-card mb-2">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <div class="comp-name" x-text="c.nombre"></div>
              <div class="comp-note">Esperado/Guardado</div>
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
function EditarUI(initial, updateUrl, csrf){
  return {
   /* TIPOS → SUBTIPOS */
    tiposEquipos: {
      ENDOSCOPIA: [
        "Adaptador","Argón Plasma","Bomba de Irrigación","Bomba de Secreción","Bomba de CO2","Boquillas","Broncoscopio","Cable","Cable Bipolar","Cable Monopolar","Capturador de Video","Capuchón Distal","Carro","Cepillo de Limpieza","Colonoscopio","Conjunto de Irrigación","Contenedor de líquidos","Convertidor de Video","Duodenoscopio","Eliminador","Focos excelitas","Fuente de Luz","Gastroscopio","Grabador","Interfaz Monopolar para Erbe","Kit de Limpieza","Línea de Irrigación","Monitor","Mouse","Multicontacto","PC SIIMED Análogo","PC SIIMED HD","Pigtail","Pinzas de Endoscopia","Probador de Fuga","Procesador","Protector Bucal de Endoscopio","Protector de Punta de Endoscopio","Regulador de Argón de Endoscopia","Sistema Endoscopia","Tapon de Biopsia","Tapon-ETO","Teclado","Valvúlas desechables","Valvúlas Reusables","Yugo Para Argón"
      ],
      LAPAROSCOPIA: [
        "Adaptador","Cabezal","Cable Interfaz 1688","Cable Interfaz USB 1588","Cámara","Case de Transporte","Charola de Esterilización","Clarity","Clips para Monitor","Fibra de Luz","Fuente de Luz","Grabador","Instrumental de Laparoscopia","Insuflador","Lente","Manguera de Insuflación","Manguera para Bomba de Agua","Monitor Grado Médico","Parche para Electrocauterio","Pedestal","Pieza de Mano","Pinza","Transmisor","Trocar","Video Carro","Video Grabador"
      ],
      QUIROFANO: [
        "Adaptador para Ligasure","Armónico Gen11","Bipap","Brazalete Pani","Bomba de Infusion","Cable Para Pinza Bipolar","Cable Trocal ECG","Carro para Electrocauterio","Carro Rojo Emergencias","Desfribilador","Electrocauterio","Eliminador","Fuente de Poder para Desfribilador","Lámpara de Quirófano","Lapíz para Electrocauterio","Ligasure LS8","Línea de Muestreo de CO2","Máquina de Anestesia","Mesa de Cirugía","Monitor Signos Vitales","Pedal Bipolar","Pedal Ligasure","Pedal Monopolar","Pieza de Mano Para Gen11","Placa para Electrocauterio","Sensor de CO2","Sensor de ECG","Sensor de SPO2","Sensor PANI","Sensor de Temperatura","Vaporizador"
      ],
      HOSPITALIZACION: [
        "Aspirador","Cama Hospitalaria Eléctrica","Camilla","Cuna Térmica","Incubadora","Mesa de Exploración","Ventilador"
      ],
      MATERIAL: [
        "Limpiador y Desengrasante"
      ],
      RADIOLOGIA: ["Arco en C","Batería","Chasis","Flat Panel","Rayos X Rodable","Rayos X Portatil"],
      UROLOGIA: ["Cistoscopio","Histeroscopio","Resectoscopio","Ureteroscopio Flexible", "Ureteroscopio Rigido"],
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
      GINECOLOGIA: ["Camilla Ginecologíca","Mesa de Exploración","Ultrasonido"]
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
                'GE': [ 'Logic P3']
        },
        'mesa de exploracion': { 
            'Midmark': ['Modelo 404',' Ritte 622']
        },
      },

      material:{
          'Steren': ['desengrasante']},
    },

    /* ===== PLANTILLAS COMPONENTES ===== */
    plantillasPorSubtipo: {
      'camara': ["Cable de alimentación","Cable de video", "couple", "cabezal"],
      'fuente de luz': ["Cable de alimentación","Fibra de luz"],
      'insuflador pnemosure 45 lts': ["consola","Manguera","Yugo","Adaptador trasero de CO2", "adaptador frontal", "cable de poder"],
      'insuflador core 40 lts': ["consola","Manguera","Yugo","Adaptador trasero de CO2","cable de poder"],
      'grabador': ["Cable de alimentación","Cable de video","Remotos"],
      'monitor': ["Cable de alimentación","Eliminador"],
      'lente de laparoscopia': ["Barril", "barril de clip", "caja de carton", "charola para esterilizacion"],
      'lente de artroscopia': ["Barril de cuerda", "barril de clip", "caja de carton", "charola para esterilizacion", "camisa o canula", "punzon"],
      'clarity': ["Cable de alimentación","Cable de video"],
      'transmisores': ["Cable de alimentación","Cable de video", "llave azul"],
      'crossfire2': ["Cable delinea de agua","cable de poder"],
      'videocarro': ["brazo","soporte","2 puertas de acrilico", "4 charolas", "cajon", "llave para puerta", "multicontacto", "cable de poder"],
      'sistema 7 pequeños fragmentos': ["taladro","sierra sagital","pinza de pasador", "taladro pequeño", "mandril 1/4", "mandril 5/32", "llave corta", "llave larga", "cargador 2 puertos", "2 baterias"],
      'core': ["Cable de alimentación fórmula Core","Charola de sierras y taladros (opcional)"],
      'ligasura s8': ["Cable de alimentación","Adaptador para pinzas"],
      'force triad': ["Cable de alimentación","Pedal monopolar","Lápiz","Placa"],
      'gen11': ["Cable de alimentación","Adaptador Harmónico","Pieza de mano gris"],
      'cama': ["Cable de poder","colchon","Control para paciente"],
      'camillas': ["Colchon","Frenos","Llantas"],
      'mesa de cirugia': ["Colchon","Frenos","Pierneras", "Portapierneras"],
      'ventilador': ["camara","celda de oxigeno","compresor", "cable de poder", "circuito"],
      'fuente de luz l9000': ["consola","fibra de luz blanca","fuente de luz gris"],
      'fuente de luz l10': ["consola","fibra de luz verde", "adaptador para fibra", "cable de interfaz usb a ccu azul", "cable de poder "],
      'fuente de luz l11': ["consola","fibra de luz verde", "adaptador para fibra", "cable de interfaz usb a ccu azul", "cable de poder "],
      'arco en c': ["equipo en c","disparador","gabineta de pantallas", "impresora", "cable de poder", "baterias", "frenos"],
      'desfibrilador': ["consola","sensor spo2","sensor de temperatura", "sensor de co2", "trampa de agua de co2", "cable de poder", "baterias","palas para desfibrilar"],
      'maquina de anestesia': ["vaporizador","Frenos","Pierneras", "Portapierneras", "control","bateria", "cable de poder"],
      'mesa de cirugia 3080': ["Colchon","Frenos","Pierneras", "Portapierneras", "control","bateria", "cable de poder"],
      'cunas termicas': ["Colchon","sensor de temperatura","Llantas", "acrilicos"],
      'electrocauterios': []
    },

    get tiposKeys(){ return Object.keys(this.tiposEquipos); },
    get subtiposArr(){ return this.tipoSel ? (this.tiposEquipos[this.tipoSel] || []) : []; },

    get marcasArr(){
      if (!this.tipoSel || !this.subtipoSel) return [];
      const tipoKey = this.slug(this.tipoSel);
      const subKey  = this.slug(this.subtipoSel);
      const node    = this.marcasModelosPorSubtipo?.[tipoKey]?.[subKey] || {};
      let list      = Object.keys(node);
      if (this.marcaSel && !list.includes(this.marcaSel)) list.unshift(this.marcaSel);
      return list;
    },

    get modelosArr(){
      if (!this.tipoSel || !this.subtipoSel || !this.marcaSel) return [];
      const tipoKey = this.slug(this.tipoSel);
      const subKey  = this.slug(this.subtipoSel);
      const node    = this.marcasModelosPorSubtipo?.[tipoKey]?.[subKey] || {};
      let arr = [];

      if (node) {
        if (node[this.marcaSel]) {
          arr = node[this.marcaSel];
        } else {
          const brandKey = Object.keys(node).find(
            b => b.toLowerCase() === String(this.marcaSel).toLowerCase()
          );
          if (brandKey) arr = node[brandKey];
        }
      }

      if (this.modeloSel && !arr.includes(this.modeloSel)) arr.unshift(this.modeloSel);
      return arr;
    },

    get sheetHeight(){
      const n = this.sheet?.lista?.length || 0;
      if (n <= 1) return '25dvh';
      if (n <= 3) return '50dvh';
      return '75dvh';
    },

    // Estado
    tipoSel:'', subtipoSel:'',
    marcaSel:'', modeloSel:'',
    form:{ serie:'', anio:'', descripcion:'', fecha:'', observaciones:'' },

    previews:[null,null,null], videoUrl:null,
    firmaExistente:'', reFirmar:false, firmaData:'', isDrawing:false,

    sheet:{ lista:[] }, seleccion:[],

    _initial: initial, _updateUrl: updateUrl, _csrf: csrf,

    titleCase(s){
      return (s||'').toString().toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
    },

    slug(s){
      return (s||'').toString().toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
        .replace(/\s+/g,' ').trim();
    },

    boot(){
      console.log('DEBUG EDIT - initial desde Blade:', this._initial);

      // === Tipo ===
      const tipoSlug  = this.slug(this._initial.tipo);
      const tipoMatch = this.tiposKeys.find(k => this.slug(k) === tipoSlug);
      this.tipoSel    = tipoMatch || (this._initial.tipo || '').trim();

      // === Subtipo ===
      const subtipoSlug = this.slug(this._initial.subtipo);
      const subArr      = this.subtiposArr || [];
      const subMatch    = subArr.find(s => this.slug(s) === subtipoSlug);
      this.subtipoSel   = subMatch || (this._initial.subtipo || '').trim();

      // === Marca ===
      const marcaSlug = this.slug(this._initial.marca);
      const tipoKey   = this.slug(this.tipoSel);
      const subKey    = this.slug(this.subtipoSel);
      const nodeMarca = this.marcasModelosPorSubtipo?.[tipoKey]?.[subKey] || {};
      const brandsArr = Object.keys(nodeMarca);
      let marcaMatch  = brandsArr.find(m => this.slug(m) === marcaSlug);
      this.marcaSel   = marcaMatch || (this._initial.marca || '').trim();

      // === Modelo ===
      const nodeModel = this.marcasModelosPorSubtipo?.[tipoKey]?.[subKey] || {};
      let modelos     = [];
      if (nodeModel) {
        if (nodeModel[this.marcaSel]) {
          modelos = nodeModel[this.marcaSel];
        } else {
          const brandKey = Object.keys(nodeModel).find(
            b => b.toLowerCase() === String(this.marcaSel).toLowerCase()
          );
          if (brandKey) modelos = nodeModel[brandKey];
        }
      }
      const modeloSlug = this.slug(this._initial.modelo);
      let modeloMatch  = modelos.find(mo => this.slug(mo) === modeloSlug);
      this.modeloSel   = modeloMatch || (this._initial.modelo || '').trim();

      console.log('DEBUG boot() después de normalizar:', {
        tipoSel: this.tipoSel,
        subtipoSel: this.subtipoSel,
        marcaSel: this.marcaSel,
        modeloSel: this.modeloSel,
        subtiposArr: this.subtiposArr,
        marcasArr: this.marcasArr,
        modelosArr: this.modelosArr,
      });

      // Campos de texto
      this.form.serie         = this._initial.serie || '';
      this.form.anio          = this._initial.anio || '';
      this.form.descripcion   = this._initial.descripcion || '';
      this.form.fecha         = this._initial.fecha || '';
      this.form.observaciones = this._initial.observaciones || '';

      // Evidencias y firma
      this.previews = [
        this._initial.evid1 || null,
        this._initial.evid2 || null,
        this._initial.evid3 || null
      ];
      this.videoUrl       = this._initial.video || null;
      this.firmaExistente = this._initial.firma || '';
      this.firmaData      = '';

      // Componentes según subtipo (sin resetear marca/modelo)
      if (this.subtipoSel) {
        this.onSubtipoChange(false);
      }

      if (!this.firmaExistente) {
        this.$nextTick(() => this.initCanvas());
      }

      // Fallback MANUAL: asegurar que los selects DOM tengan el valor correcto
      this.$nextTick(() => {
        console.log('DEBUG boot() – fallback manual a selects DOM');

        const ensureSelect = (id, value, text) => {
          if (!value) return;
          const el = document.getElementById(id);
          if (!el) return;

          let opt = Array.from(el.options).find(o => o.value === value);
          if (!opt) {
            opt = new Option(text || value, value, true, true);
            el.add(opt);
          }
          el.value = value;
        };

        ensureSelect('eq_tipo',   this.tipoSel,    this.tipoSel);
        ensureSelect('eq_sub',    this.subtipoSel, this.subtipoSel);
        ensureSelect('eq_marca',  this.marcaSel,   this.marcaSel);
        ensureSelect('eq_modelo', this.modeloSel,  this.modeloSel);

        // Para que las labels floten visualmente
        ['eq_tipo','eq_sub','eq_marca','eq_modelo'].forEach(id => {
          const el = document.getElementById(id);
          if (el) el.dispatchEvent(new Event('change', {bubbles:true}));
        });
      });
    },

    onTipoChange(resetSub=true){
      if (resetSub) {
        this.subtipoSel='';
        this.marcaSel='';
        this.modeloSel='';
      }
      this.sheet.lista=[]; this.syncSeleccion();
    },

    onSubtipoChange(merge = true){
      if (merge) {
        this.marcaSel='';
        this.modeloSel='';
      }
      const key = this.slug(this.subtipoSel);
      const base = this.plantillasPorSubtipo[key] || [];
      this.sheet.lista = base.map(n => ({ nombre:n, cantidad:1, incluido:true }));
      if (merge) this.mergeExistentes();
      this.syncSeleccion();
    },

    onMarcaChange(){ this.modeloSel = ''; },

    mergeExistentes(){
      const exMap = new Map((this._initial.componentes || []).map(c => [this.slug(c.nombre), c]));
      this.sheet.lista = this.sheet.lista.map(it => {
        const ex = exMap.get(this.slug(it.nombre));
        return ex ? { nombre: it.nombre, cantidad: Number(ex.cantidad)||1, incluido: !!(+ex.incluido) } : it;
      });
      exMap.forEach((ex, key) => {
        if (!this.sheet.lista.some(it => this.slug(it.nombre) === key)) {
          this.sheet.lista.push({ nombre: ex.nombre, cantidad: Number(ex.cantidad)||1, incluido: !!(+ex.incluido) });
        }
      });
    },

    syncSeleccion(){
      this.seleccion = this.sheet.lista
        .filter(x => x.cantidad > 0)
        .map(x => ({ nombre:x.nombre, cantidad:x.cantidad, incluido:!!x.incluido }));
    },
    restaurarPlantilla(){ this.onSubtipoChange(false); this.syncSeleccion(); },

    previewImg(e, i){
      const f = e.target.files?.[0]; if(!f){ return; }
      const rd = new FileReader(); rd.onload = ev => this.previews.splice(i,1,ev.target.result); rd.readAsDataURL(f);
    },
    previewVideo(e){
      const f = e.target.files?.[0];
      if(this.videoUrl && this.videoUrl.startsWith('blob:')) URL.revokeObjectURL(this.videoUrl);
      this.videoUrl = f ? URL.createObjectURL(f) : this._initial.video || null;
    },

    toggleFirma(){
      this.reFirmar = !this.reFirmar;
      if (this.reFirmar) this.$nextTick(()=>this.initCanvas());
      if (!this.reFirmar) this.firmaData = '';
    },
    initCanvas(){
      const canvas=this.$refs.canvas; if(!canvas) return;
      const dpr=window.devicePixelRatio||1, rect=canvas.getBoundingClientRect();
      canvas.width=rect.width*dpr; canvas.height=170*dpr; canvas.style.width=rect.width+'px'; canvas.style.height='170px';
      const ctx=canvas.getContext('2d'); ctx.scale(dpr,dpr); ctx.lineWidth=2; ctx.lineCap='round'; ctx.strokeStyle='#1d4d4f';
      let draw=false, last=null;
      const pos=e=>{ const b=canvas.getBoundingClientRect(); const x=(e.touches?e.touches[0].clientX:e.clientX)-b.left; const y=(e.touches?e.touches[0].clientY:e.clientY)-b.top; return {x,y}; };
      const start=e=>{ draw=true; last=pos(e); this.isDrawing=true; e.preventDefault(); };
      const move=e=>{ if(!draw) return; const p=pos(e); ctx.beginPath(); ctx.moveTo(last.x,last.y); ctx.lineTo(p.x,p.y); ctx.stroke(); last=p; e.preventDefault(); };
      const end=()=>{ draw=false; this.isDrawing=false; this.firmaData=canvas.toDataURL('image/png'); };
      ctx.fillStyle='#fff'; ctx.fillRect(0,0,canvas.width,canvas.height);
      canvas.addEventListener('mousedown',start); canvas.addEventListener('mousemove',move); window.addEventListener('mouseup',end);
      canvas.addEventListener('touchstart',start,{passive:false}); canvas.addEventListener('touchmove',move,{passive:false}); canvas.addEventListener('touchend',end);
    },
    clearSig(){ const c=this.$refs.canvas; if(!c) return; const ctx=c.getContext('2d'); ctx.fillStyle='#fff'; ctx.fillRect(0,0,c.width,c.height); this.firmaData=c.toDataURL('image/png'); },

    inc(i){ this.sheet.lista[i].cantidad=(this.sheet.lista[i].cantidad||0)+1; this.syncSeleccion(); },
    dec(i){ this.sheet.lista[i].cantidad=Math.max(0,(this.sheet.lista[i].cantidad||0)-1); this.syncSeleccion(); },

    async submit(){
      const fd = new FormData(document.getElementById('frmEdit'));

      fd.set('Tipo_de_Equipo', this.tipoSel || '');
      fd.set('Subtipo_de_Equipo', this.subtipoSel || '');

      this.syncSeleccion();
      this.seleccion.forEach((r,i)=>{
        fd.set(`componentes[${i}][nombre]`, r.nombre);
        fd.set(`componentes[${i}][cantidad]`, r.cantidad);
        fd.set(`componentes[${i}][incluido]`, r.incluido ? 1 : 0);
      });

      if (!this.reFirmar) { fd.set('firmaDigital', ''); }

      try{
        const res = await fetch(this._updateUrl, {
          method: 'POST',
          headers: {
            'X-Requested-With':'XMLHttpRequest',
            'X-CSRF-TOKEN': this._csrf,
            'X-HTTP-Method-Override':'PUT',
            'Accept':'application/json'
          },
          body: fd
        });
        const json = await res.json().catch(()=>({}));
        if(!res.ok || json.success === false){
          this.toast(json?.error || 'No se pudo actualizar', false);
          return;
        }
        this.toast('Actualizado correctamente', true);
        setTimeout(()=>{ window.location = "{{ url()->previous() }}"; }, 900);
      }catch(e){
        this.toast('Error de red/servidor', false);
      }
    },

    toast(msg, ok=true){
      const id='t'+Date.now();
      const el=document.createElement('div');
      el.className='toast align-items-center border-0 shadow-sm'; el.id=id;
      el.setAttribute('role','alert'); el.setAttribute('aria-live','assertive'); el.setAttribute('aria-atomic','true');
      el.innerHTML=`
        <div class="d-flex ${ok?'bg-success-subtle text-success-emphasis':'bg-danger-subtle text-danger-emphasis'} rounded-3 px-3 py-2">
          <div class="toast-body">${this.escape(msg)}</div>
          <button type="button" class="btn-close ms-auto m-1" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>`;
      document.getElementById('toastPlace').appendChild(el);
      const t=new bootstrap.Toast(el,{delay:2200}); t.show();
      el.addEventListener('hidden.bs.toast',()=>el.remove());
    },
    escape(s){ return (s||'').replace(/[&<>"']/g, m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
  }
}
</script>
@endsection
