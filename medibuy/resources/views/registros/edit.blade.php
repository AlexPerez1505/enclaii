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
 
.badge-soft{ background:#e9fbf7; color:var(--brand-ink); border-radius:999px; padding:.25rem .55rem; font-weight:700; font-size:.75rem }
.comp-card{ border:1px solid var(--line); border-radius:12px; background:#fff; padding:.7rem }
.comp-name{ font-weight:700; color:var(--ink) }
.comp-note{ font-size:.85rem; color:var(--muted) }
 
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
                <select class="form-select" name="Tipo_de_Equipo" id="eq_tipo"
                  x-model="tipoSel" @change="onTipoChange(true)" required>
                  <option value="" hidden></option>
                  <template x-for="t in tipos" :key="'t-'+t">
                    <option :value="t" x-text="titleCase(t)"></option>
                  </template>
                </select>
                <label for="eq_tipo">Tipo de equipo *</label>
              </div>
            </div>
 
            {{-- Subtipo --}}
            <div class="col-md-6">
              <div class="ux-float" :class="{'has-value': subtipoSel}">
                <select class="form-select" name="Subtipo_de_Equipo" id="eq_sub"
                  x-model="subtipoSel" @change="onSubtipoChange(true)" :disabled="!tipoSel" required>
                  <option value="" hidden></option>
                  <template x-for="s in subtipos" :key="'s-'+s">
                    <option :value="s" x-text="s"></option>
                  </template>
                </select>
                <label for="eq_sub">Subtipo *</label>
              </div>
            </div>
 
            {{-- Marca --}}
            <div class="col-md-6">
              <div class="ux-float" :class="{'has-value': marcaSel}">
                <select class="form-select" name="Marca" id="eq_marca"
                  x-model="marcaSel" @change="onMarcaChange" :disabled="!tipoSel || !subtipoSel" required>
                  <option value="" hidden></option>
                  <template x-for="m in marcas" :key="'m-'+m">
                    <option :value="m" x-text="m"></option>
                  </template>
                </select>
                <label for="eq_marca">Marca *</label>
              </div>
              <small class="text-muted-ux d-block mt-1"
                     x-show="tipoSel && subtipoSel && !marcas.length && !marcaSel">
                No hay marcas configuradas para este subtipo.
              </small>
            </div>
 
            {{-- Modelo --}}
            <div class="col-md-6">
              <div class="ux-float" :class="{'has-value': modeloSel}">
                <select class="form-select" name="Modelo" id="eq_modelo"
                  x-model="modeloSel" :disabled="!marcaSel" required>
                  <option value="" hidden></option>
                  <template x-for="mo in modelos" :key="'mo-'+mo">
                    <option :value="mo" x-text="mo"></option>
                  </template>
                </select>
                <label for="eq_modelo">Modelo *</label>
              </div>
              <small class="text-muted-ux d-block mt-1"
                     x-show="marcaSel && !modelos.length && !modeloSel">
                No hay modelos configurados para esta marca.
              </small>
            </div>
 
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
            <small class="text-muted-ux">Se generan por <b>Subtipo</b> y ya incluyen lo guardado. Desmarca si "no viene".</small>
 
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
      <p class="text-muted-ux mb-2">Desmarca si "no viene".</p>
 
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

// ==================== DATOS COMPLETOS (COPIADOS DEL SEGUNDO CÓDIGO) ====================
const tiposEquipos = {
  'endoscopia': [
    "Adaptador","Argón Plasma","Balance de Blancos","Bomba de Irrigación","Bomba de Secreción","Bomba de CO2","Broncoscopio","Cable","Cable Bipolar","Cable Monopolar","Capturador de Video","Capuchón Distal","Carro","Caja de almacenamiento","Cepillo de Limpieza","Cepillos de Limpieza","Colonoscopio","Conjunto de Irrigación","Contenedor de líquidos","Convertidor de Video","Duodenoscopio","Eliminador","Focos","Fuente de Luz","Gastroscopio","Grabador","Interfaz","Instrumento endoscópico","Interfaz Monopolar para Erbe","Kit de Limpieza","Línea de Irrigación","Manguera Yugo","Monitor","Mouse","Multicontacto","PC SIIMED Análogo","PC SIIMED HD","Pedal","Pilas","Pigtail","Pinzas de Endoscopia","Probador de Fuga","Procesador","Protector Bucal de Endoscopio","Protector de Punta de Endoscopio","Regulador de Argón de Endoscopia","Sonda Para Argon","Sistema Endoscopia","Tapon de Biopsia","Tapon-ETO","Tanque de Argón","Teclado","Toallitas humedas","Transductor para USG-400","Remoto para Endoscopios","Solucion desinfectante","Solucion detergente","Valvúlas desechables","Valvúlas Reusables","Yugo Para Argón"
  ],
  'refacciones de endoscopia': [
    "Tubo de inserción","Tubo de guía de luz","Camisa para líneas ensamblado","Camisa para lineas Ensamblada M","Canal de biopsia","Canales de biopsia M","C-Cover M","Fibras de luz P","Fibras de luz M","Perillas de control U/D R/L","Conector eléctrico","Sección flexible ensamblada","Pipeta Aire/Agua P","Adhesivos epóxico","Tornillería","Rubber M","Sección de flexión Ensamblado M","Líneas de angulación M","Tubos de inserción M","Cable guía de luz M","Stopper del cable de angulación M","Pipeta aire/agua M","Body Grip ME","Camisa para lineas Ensamblado ME","Canal de biopsia ME","Rubber ME", "Rubber GM","Sección flexible Ensamblada ME","Drums (Poleas) ME","Canal de aire/agua ME","Tarjeta Electrica ME","Membrana Flexible CCD OEM ME","Tornillería ME","Tubos de inserción ME","Tubo Universal ME","Stopper del cable de control ME","Poleas de control U/D R/L ME","Oring ME","Perillas de control U/D R/L E","Freno de perillas U/D R/L E","Membrana Flexible CCD OEM E","Conector Electrico E","Botonera E","Cubierta de Perillas de control E","Tuerca E","Oring E","Fibra de luz ME"
  ],
  'laparoscopia': [
    "Adaptador","Cabezal","Cable Interfaz 1688","Cable Interfaz USB 1588","Cable Bipolar","Cámara","Case de Transporte","Charola de Esterilización","Clarity","Clips para Monitor","Funda para Cámara","Eliminador","Fibra de Luz","Fuente de Luz","Grabador","Instrumental de Laparoscopia","Insuflador","Lente","Manguera de Insuflación","Manguera para Bomba de Agua","Monitor Grado Médico","Parche para Electrocauterio","Pedestal","Pieza de Mano","Pinza","Porta tanque","Transmisor","Trocar","Receptor","Video Carro","Video Grabador","Remotos"
  ],
  'quirófano': [
    "Adaptador","Adaptador para Ligasure","Adaptador para Armonico","Armónico Gen11","Bipap","Brazalete Pani","Bomba de Infusion","Cable Para Pinza Bipolar","Cable Trocal ECG","Cable Interfaz","Carro para Electrocauterio","Carro Rojo Emergencias","CharoLa de Esterilizacion","Circuito de Paciente","Desfribilador","Electrocauterio","Eliminador","Evacuador de Humo","Lámpara de Quirófano","Lapíz para Electrocauterio","Ligasure LS8","Línea de Muestreo de CO2","Laringoscopio","Máquina de Anestesia","Mesa de Cirugía","Monitor Signos Vitales","Oximetro","Pedal Bipolar","Pedal Ligasure","Pedal Monopolar","Pieza de Mano Para Gen11","Placa para Electrocauterio","Sensor de ECG","Sensor de SPO2","Sensor PANI","Sensor de Temperatura","UPS","Vaporizador"
  ],
  'hospitalización': [
    "Aspirador","Cama Hospitalaria Eléctrica","Camilla","Cuna Térmica","Incubadora","Mesa de Exploración","Ventilador"
  ],
  'material': [
    "Limpiador y Desengrasante","Playon"
  ],
  'otorrinolaringologia': [
    "Microdebrilador","Pedal Microdebrilador","Pieza de Mano","Electrocirugia","Pedal"
  ],
  'radiología': ["Arco en C","Batería","Chasis","Flat Panel","Rayos X Rodable","Rayos X Portatil"],
  'urología': ["Cistoscopio","Histeroscopio","Resectoscopio","Ureteroscopio Flexible", "Ureteroscopio Rigido"],
  'artroscopia': [
    "Artroscopio","Bomba de Irrigación","Camisa","Opturador","Cable para pedal","Cable para pieza de mano","Charola de Esterilización","Endogia","Hoja de Sierra Sagital","Pieza de Mano","Pedal","Puntas de Radio Frecuencia","Puntas Serfas de radiofrecuencia","Rasurador Shaver","Radio Frecuencia Serfas","Set de Taladros de Artroscopia System 4","Set de Taladros de Artroscopia System 7","Set de Taladros de Artroscopia System 8","Set de Taladros Electrico Core Azul","Set de Taladros Electrico Core Negro","Transmisores","Set de Cirugia Para Tobillo y Muñeca","Set de Cirugía de Rodilla","Meditronic","Línea de Irrigación"
  ],
  'ceye': ["Autoclave de cámara 95 L ","Monitor"],
  'ginecología': ["Camilla Ginecologíca","Mesa de Exploración","Ultrasonido","Impresora"],
  'Endoscopia Veterinaria':["Gastroscopio Veterinaria","Colonoscopio Veterinaria","Procesador Veterinaria","Monitor de Imagen","Coledoscopio Veterinaria","Cabezal Veterinaria","Tapon de Biopsia","Valvula de Succion","Valvula de aire/agua","Tapon de Inmersion","Probador de Fuga","Kit de Limpieza","Adaptador de Limpieza de Succion","Adaptador de limpieza del canal de aire/agua","Tapon de Canal","Cepillo de Limpieza de la apertura del canal","Cepillo de Limpieza del Canal"]
};

const marcasModelosPorSubtipo = {
  laparoscopia: {
    'Camara': {
      'Stryker': ['1188','1288','Precision','1488','1588','1688','1788'],
      'Karl Storz': ['IMAGE1 S','IMAGE1 HUB','Spies']
    },
    'Cable Interfaz 1688': { 'Stryker': ['1688'] },
    'Insuflador': {
      'Stryker': ['High Flow 40L','PneumoSure 45L','PneumoClear 50L'],
      'Karl Storz': ['Endoflator 50','Endoflator 264320 20']
    },
    'Fuente de luz': {
      'Stryker': ['X8000','L9000','L10','L11'],
      'Karl Storz': ['Xenon 300','Power LED 300']
    },
    'Monitor grado medico': {
      'Stryker': ['Vision Elect HDTV','VisionPro LED 26"','VisionPro SYNK LED 26"','4K LED 32"','4K 32" OLED','Wise HD 26"']
    },
    'Cabezal': {
      'Stryker': ['1188','1288','Precision','1488','1588','1688 AIM 4K','1788 Platform']
    },
    'Cable Bipolar': { 'Olympus': ['WA00014A para ESG-400'] },
    'Clarificador de video': { 'Stryker': ['clarity'] },
    'Grabador': { 'Stryker': ['SDC Ultra','SDC3','Connected OR HUB'] },
    'Lente': {
      'Stryker': ['30-5mm Azul','30-5mm AIM','30-5mm Precision','30-10mm Precision','30-10mm AIM','30-10mm Azul','0°-10 Precision','30° 10mm Ideal Eyes','30° 5mm Ideal Eyes','30° 5.5mm Precision','30° 5.4mm AIM'],
      'Novadac': ['30°-5mm']
    },
    'funda para Cámara': { 'Stema': ['Funda para Cabezales'] },
    'Fibra de luz': {
      'Stryker': ['X8000 Gris','L9000 Blanca','L10 y L11 Verde','L12 Verde','Kit Ureteral IRIS'],
      'Karl Storz': ['Xenon 8000']
    },
    'Video carro': { 'Stryker': ['Standar','Connected OR'] },
    'Transmisor': {
      'Stryker': ['4K SYNK Wireless','4K SYNK Wireless Receiver','VisionPro SYNK Wireless','Wise HDTV Wireless']
    },
    'Trocar': {
      'Ethicon': ['11mm X 100mm','12mm X 100mm 2D12-T','12mm X 100mm 2CB12LT'],
      'GM': ['KIT Trocares GYTR L KIT A','KIT TROCARES GYTR-LLL KIT A']
    },
    'Receptor': { 'stryker': ['4k'] },
    'Pedestal': { 'Stryker': ['Pedestal'] },
    'Porta tanque': { 'GM': ['Porta tanque'] },
    'Eliminador': {
      'Stryker': ['Para Monitor VisionPRO-WISE HD-4K'],
      'GM': ['Para Monitor VisionPRO-WISEHD-4K']
    },
    'Instrumental de laparoscopia': {
      'AMRCN': ['Clips Hemolok Morado','Clips Hemolok Verde'],
      'Ethicon': ['100mm x 12mm','Clips Titanio LT300'],
      'GM': ['Aguja de Veress','Baja Nudos','Cable Bipolar','Cable monopolar','Clips Hemolok Dorado','Clips Hemolok Morado','Clips Hemolok Verde','Clips Titanio OC300','Clips Titanio OC400','Conjunto de Irrigacion y Succion desechable','Engrapadora Articulada','Engrapadora Hemolok Amarillo','Engrapadora Hemolok Dorado','Engrapadora Hemolok Morado','Engrapadora Hemolok Verde','Engrapadora Titanio LT300','Engrapadora Titanio LT400','Espatula','Gancho En L','Pinza Sectorial','Pinza Alligator','Pinza Babcock','Pinza Babcock Grasper 5mm 330mm','Pinza Babcock Grasper 10mm 330mm','Pinza Cobra','Pinza Colecistectomia','Pinza De Curva','Pinza De Disección','Pinza De Tijera Recta','Pinza Disectora','Pinza Extractora De Litos','Pinza Fenestrada','Pinza Grasper','Pinza Maryland Curva','Porta agujas 5mm 300mm','Mango Aislado con Cremallera','Mango Aislado Sin Cremallera','Retractor','Tijera Metzenbaum Doble Acción Curva 5mm* 330mm','Tubo de Irrigacion y Succion Reusable'],
      'Covidien': ['Engrapadora Endogia Articulada 45mm Morado','Engrapadora Endogia Articulada 60mm Morado','Engrapadora Endogia Articulada 45mm Vascular Dorado','Engrapadora Endogia Articulada 60mm Vascular Dorado','Engrapadora Endogia ultra 12mm','Engrapadora Endoclip 10mm M/L','Engrapadora Tri-Staple Extra 60mm Negro'],
      'Storz': ['Pinza Grasper']
    },
    'Manguera de insuflacion': {
      'stryker': ['manguera','yugo CO2','Linea de Insuflacion con Adaptador Desechables','Linea de Insuflacion con Filtro Desechables']
    },
    'Pinza': {
      'Covidien': ['Blunt Tip 5mm-37cm','Impact 36mm-18cm','Maryland 5mm-37cm','Maryland 5mm-23cm','Small Jam 16.5mm-19cm','Exact Dissector 20.6mm-21cm'],
      'Ethicon': ['Pinza Har23','Pinza Har36','Engrapadora Circular Curva y Recta 33mm'],
      'STRYKER': ['Blunt Tip 5mm-37cm']
    },
    'Adaptador': {
      'stryker': ['Adaptador cople de lente','Adaptador frontal de Insuflador','Adaptador Trasero de Insuflador'],
      'GM': ['Cable HDMI macho a 2 hebras']
    },
    'Case de transporte': {
      'GM': ['Camara y Fuente L9000','Camara 1688 y Fuente L11','Grabador e Insuflador','Monitor Vision Pro led','Monitor 4K Stryker','Monitor 4K SONY']
    },
    'Remotos': { 'GM': ['Para Grabador SDC3 y Connected'] },
    'Charola de esterilizacion': {
      'Stryker': ['Charola para Camara IAM','Charola para Lente de Laparoscopia'],
      'Storz': ['Charola para Lente de Laparoscopia'],
      'Artrhex': ['Charola para Lente de Laparoscopia'],
      'Olympus': ['Charola para Lente de Laparoscopia'],
      'GM': ['Charola para Instrumental'],
      'Richard Wolf': ['Charola para Lente de Laparoscopia']
    },
    'Clips para monitor': { 'GM': ['Porta Monitor'] }
  },
  endoscopia: {
    'procesador': {
      'Olympus': ['CV-160','CV-170','CV-180','CV-190','EVIS X1','EU-ME1'],
      'Fujifilm': ['VP-4400','VP-4440HD','EP-6000','EP-7000'],
      'Pentax': ['EPK-i','EPK-i7010']
    },
    'fuente de luz': {
      'Olympus': ['CLV-160','CLV-180','CLV-190'],
      'Fujifilm': ['XL-4400','XL-4450','BL-7000'],
      'Pentax': ['Prueba']
    },
    'broncoscopio': { 'Olympus': ['BF-XP160F','BF-1T190'] },
    'colonoscopio': {
      'Olympus': ['CF-Q160L','CF-H180AL','CF-Q180AL','CF-HQ190L','CF-EZ1500','CF-Q160S'],
      'Fujinon': ['EC-250HL5','EC-600HL','EC-760R-V/L','EC-530FL','EC-530WL'],
      'Pentax': ['EC-3890LI']
    },
    'duodenoscopio': {
      'Olympus': ['JF-140F','TJF-160F','TJF-160VF','TJF-Q180V','TJF-Q180','TJF-Q90V'],
      'Fujinon': ['ED-530XT'],
      'Pentax': ['ED-34-I10T2']
    },
    'gastroscopio': {
      'Olympus': ['GIF-Q160','GIF-XP160','GIF-1TQ160','GIF-2T160','GIF-180','GIF-H180','GIF-H180J','GIF-H170','GIF-HQ190','GIF-EZ1500','GF-UCT180'],
      'Fujinon': ['EG-530N','EG-530WR','EG-600WR','EG-6400N','EG-760R'],
      'Pentax': ['EG-2990i']
    },
    'argon plasma': { 'Erbe': ['ICC200','ICC300','VIO 300D','APC300','APC'] },
    'bomba de co2': {
      'Fujinon': ['GW-100','Linea de CO2 para GW-100'],
      'Olympus': ['UCR','Linea de CO2 para UCR']
    },
    'bomba de irrigacion': {
      'Olympus': ['OFP','OFP2'],
      'Medivators': ['Endogator EGP-100','Stratus EGA-500'],
      'Erbe': ['EIP 2']
    },
    'Sonda Para Argon': {
      'Erbe': ['FiaPC 2.3mm x L2.2mm Frontal','FiaPC 2.3mm x L2.2mm Circular','Sonda Jet Flexible 1.3mm x 2.2m']
    },
    'bomba de secrecion': { 'Infusomat': ['Braun Sumalfit'] },
    'capturador de video': { 'Ugreen': ['HDMI'], 'Steren': ['RCA/S-VIDEO'] },
    'convertidor de video': { 'GM': ['X003'] },
    'monitor': {
      'Fujinon': ['LCD 19"'],
      'Olympus': ['OEV 262H','OEV 191H'],
      'Storz': ['4k 32"','Led 26"'],
      'Sony': ['HD 19"','4k 32 Pulgadas']
    },
    'Adaptador': {
      'Valleylab': ['Adapatador Bipolar Azul Active Only'],
      'Erbe': ['Adaptador Bipolar ICC 200','Adaptador para Sonda ICC200 ICC300 VIO 300D','Sonda Circular'],
      'GM': ['Adaptador para el canal de Biopsia'],
      'Cerofrict': ['Conector de Agua Auxiliar para Serie fujinon 700']
    },
    'grabador': { 'KingMa': ['KM-YK980'] },
    'interfaz monopolar para erbe': { 'Erbe': ['Cable interfaz'] },
    'interfaz': {
      'Olympus': ['MAJ-1411 Serie 180','MAJ-1933 Serie 190'],
      'Fujinon': ['Serie 6000 y 7000']
    },
    'Balance de Blancos': { 'Olympus': ['MH-155 W/B','MAJ-960 Porta W/B'] },
    'eliminador': {
      'Storz': ['4k 32"','Led 26"','XP POWER'],
      'Sony': ['HD 19"','4k 32 Pulgadas'],
      'GM': ['Image Stream str']
    },
    'focos': {
      'Excelitas': ['PE300BFA para 180-160-4400-4450-Xenon 300','PE150AF para Fujinon-2200','Y1882 para EPK-i','PE300C-10FS para EPK-i','Y1964 para EPK-5010 y EPKI-7010','Y1911 para EPK-5010 y EPKI-7010'],
      'Olympus': ['MAJ-1817 para 180-160-4400-4450-Xenon 300']
    },
    'Manguera Yugo': {
      'Olympus': ['UCR']
    },
    'Carro': {
      'Olympus': ['Para sistema 160 o 180','Para sistema 190'],
      'Fujinon': ['Carro Original'],
      'GM': ['Carro GM']
    },
    'Caja de almacenamiento': {
      'Sterilite': ['32 cuartos']
    },
    'Solucion desinfectante': {
      'Gafidex': ['NF0']
    },
    'Solucion detergente': {
      'Cidezyme': ['Enzimatico 5L']
    },
    'kit de limpieza': {
      'Olympus': ['MH-946 para 160 180 y 190','MAJ-885 Linea de Irrigacion Jet Auxiliar','MAJ-MH-856 Adaptador de Limpieza por Aspiracion','MAJ-222 Adaptador de Limpieza para Broncoscopio','MH-948 Valvula de Limpieza','MH-944 Conector de canal y valvulas'],
      'Fujinon': ['WA-007 para 760','SA-503 para 530-600'],
      'Pentax': ['Serie K-I'],
      'Medivators': ['Scope Buddy 1 via','Scope Buddy 2 vias']
    },
    'linea de irrigacion': {
      'GM': ['Genericas'],
      'Medivators': ['100611 OFP-OFP 2-Stratus','200230 EGP-100, Olimpus OFP y ERBE'],
      'Endo Smartcap': ['100145CO2EXT Olympus 140,140,180 y 190 con Co2','100145CO2 Olympus 140,140,180 y 190','100160 Adaptador para Pentax','100551 Tubo de CO2'],
      'Boston Scientific': ['SIT-576 para Olympus'],
      'Olympus': ['Para Olympus']
    },
    'contenedor de liquidos': {
      'Olympus': ['Serie 100','160','180','190 Para UCR MAJ-902','190 MAJ-901'],
      'Fujinon': ['Serie 500 y 600','760 WT 04','760 para Insuflador'],
      'Pentax': ['Serie 7010']
    },
    'Pedal': {
      'Medivators': ['Endogator'],
      'Olympus': ['OFP'],
      'Erbe': ['OFP2']
    },
    'Pilas': { 'Olympus': ['Pilas para Sistema Serie 160-180-190'] },
    'Pinzas de Endoscopia': {
      'Olympus': ['Pinza de biopsia 2.0mm x 1150mm','pinza de Polipectomia hot 2.8mm x 2300mm','pinza de Extraccion 2.0mm','pinza de Extraccion 2.8mm','pinza de biopsia 3.2mm x 2300mm'],
      'GM': ['Pinza de biopsia 2.8mm','Pinza de biopsia 2.0mm','Pinza de canasta de 4 Hilos 2.8/3.2 mm x 1600mm','Pinza de canasta de 4 Hilos 2.0 mm x 1800mm','Pinza de extraccion Mixta 2.0 x 1800mm','Pinza de extraccion Mixta 2.8 x 1600mm','Pinza de polipectomia 2.4 x 1600mm'],
      'Micro-Tech': ['Pinza de Caiman 2.8mm x 1800mm','Pinza de Caiman 2.8mm x 2300mm','Pinza de extraccion Mixta 2.8mm x 2300mm','Pinza de extraccion Mixta 2.0mm x 1800mm','Pinza de Red 2.8mm x 2300mm'],
      'Cook': ['Inyector de varices Desechable 2.8mm x 2400mm'],
      'Boston Scientific': ['pinza de biopsia 2.8mm x 1600mm']
    },
    'Probador de fuga': {
      'Olympus': ['Serie 160 180 190'],
      'Fujinon': ['Serie 500 y 600','Serie 760'],
      'Pentax': ['Serie 90i']
    },
    'transductor para usg-400': { 'Olympus': ['Thunderbeat TD-TB400','Sonicbeat'] },
    'Protector bucal de endoscopio': { 'Olympus': ['MB-142 Olympus'] },
    'Protector de punta de endoscopio': { 'GM': ['Protector Azul'] },
    'Remoto para Endoscopios': {
      'GM': ['Para Olympus 160','Para Olympus 180 y 190','Para Pentax','Para Fujinon']
    },
    'Tapon de biopsia': { 'GM': ['GM'] },
    'Tapon-eto': {
      'Olympus': ['MH-553','ETO CAP MB-156'],
      'Pentax': ['Serie 90i']
    },
    'Tanque de Argón': { 'GM': ['Tanque de Argón'] },
    'Valvulas desechables': { 'Olympus': ['ScopeValet'] },
    'Valvulas reusables': {
      'Fujinon': ['Serie 760','Serie 530 y 600'],
      'Olympus': ['Serie 160-180-190'],
      'Pentax': ['Serie 90i','Serie 90k']
    },
    'Yugo para Argon': { 'Erbe': ['ICC200','ICC300','VIO 300D','APC300','APC'] },
    'Teclado': {
      'Olympus': ['Serie 100','160','180','190'],
      'Fujinon': ['Serie 500 y 600','760'],
      'Pentax': ['Serie 7010','Serie EPK-i']
    },
    'Toallitas humedas': {
      'Compa wipes': ['XL']
    },
    'Mouse': { 'GM': ['GM'] },
    'Multicontacto': { 'Adir': ['Para carros GM'] },
    'Manguera de insuflacion': { 'Olympus': ['manguera de CO2','yugo CO2'] },
    'pc siimed analogo': { 'Dell': ['PC SIIMED para 4400-160-180'] },
    'pc siimed hd': { 'Dell': ['PC SIIMED para 4450-190-EPKi-EPKi-7010'] },
    'Pigtail': { 'Olympus': ['Sistema 160-Maj-843','Sistema 180-190-Maj-1430'] },
    'Cable': { 'GM': ['Cable de Video HDMI','Cable de Video Coaxial','Cable de Video SDI'] },
    'Cable bipolar': { 'Olympus': ['Punta verde MH-969'] },
    'Cepillo de limpieza': {
      'Olympus': ['2mm-4.2','950mm de largo, 2mm-3.2mm'],
      'Storz': ['Cepillo 1.2mm-1.8mm'],
      'GM': ['Cepillo 2.8mm-1600mm']
    },
    'Capuchon distal': { 'Olympus': ['Protector Distal para Duodenoscopio MAJ-2315'] }
  },
  quirofano: {
    'adaptador': { 'Valleylab': ['Adapatador Bipolar Azul Active Only'] },
    'adaptador para ligasure': { 'Cad': ['LS8','Force FX','Force 2','Adaptador Bipolar LS8'] },
    'adaptador para armonico': {
      'Ethicon': ['Enseal HGA11','Harmonic HGA11','Adaptador de Conmutacion HSA07','Adaptador para cambio rapido de Pinzas','Adaptador para Prueba de Pieza de Mano']
    },
    'ligasure ls8': { 'Valleylab': ['LS8'] },
    'electrocauterio': {
      'Valleylab': ['Force 2','Force FX','ForceTriad','FT10'],
      'Ellman': ['Surgitron 4.0 Dual'],
      'Erbe': ['ICC 200','ICC 300','VIO 300D'],
      'Olympus': ['ESG-400'],
      'GM': ['CITADEL 300'],
      'Conmed': ['Sabre Genesis']
    },
    'brazalete pani': {
      'Datex-Ohmeda': ['Cardiocap5'],
      'Drager': ['Delta Infinity'],
      'Phillips': ['MP50 Intellivue','MP70 Intellivue','Heartstart MRX'],
      'Mindray': ['V12'],
      'Datascope': ['Accutor Plus'],
      'GM': ['Adultos','Pediatrico']
    },
    'Bomba de Infusion': { 'Dre Med': ['NTx3 Plus'] },
    'Cable Interfaz': { 'Covidien o Valleylab': ['Evacuador de Humo'] },
    'Evacuador de Humo': { 'Covidien o Valleylab': ['RapidVac'] },
    'maquina de anestesia': {
      'Datex-Ohmeda': ['Aestiva','Avance','Aisys','Aespire'],
      'Narkomed': ['GS'],
      'Dräger': ['Fabius MRI']
    },
    'mesa de cirugia': {
      'Amsco': ['2080 Semielectrica y SemiTraslucida','3080 Electrica y Traslucida'],
      'Maquet': ['Alphamaxx']
    },
    'lampara de quirofano': { 'Stryker': ['Vision 2'], 'Skytron': ['Aurora'] },
    'laringoscopio': {
      'Aswad': ['Mango con 4 hojas'],
      'Surgical Appliances': ['Mango con 4 hojas']
    },
    'monitor signos vitales': {
      'Datex-Ohmeda': ['Cardiocap5'],
      'Drager': ['Delta Infinity'],
      'Phillips': ['MP50 Intellivue sin capnografia','MP50 Intellivue con capnografia','MP70 Intellivue'],
      'Mindray': ['V12'],
      'DataScope': ['Accutor Plus']
    },
    'desfribilador': { 'Phillips': ['Heartstart MRX'], 'Zoll': ['AED plus'] },
    'bipap': { 'Phillips': ['Ventilador Respironics Nuevo'] },
    'vaporizador': { 'Datex-Ohmeda': ['Tec 7 Aestiva-Aespire','Casette Aisys'] },
    'CharoLa de esterilizacion': { 'Ethicon': ['Endo Surgery para Pieza de Mano'] },
    'oximetro': { 'Masimo': ['Radical 7'] },
    'sensor de ecg': {
      'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
      'Drager': ['Delta Infinity'],
      'Datex Ohmeda': ['Cardiocap5'],
      'Mindray': ['V12']
    },
    'sensor de spo2': {
      'GM': ['Contec CMS 9200 Plus'],
      'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
      'Drager': ['Delta Infinity'],
      'Datex Ohmeda': ['Cardiocap5'],
      'Mindray': ['V12'],
      'DataScope': ['Accutor Plus'],
      'Masimo': ['Radical 7']
    },
    'sensor pani': {
      'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
      'Drager': ['Delta Infinity'],
      'Datex Ohmeda': ['Cardiocap5'],
      'Mindray': ['V12'],
      'Datascope': ['Accutor Plus']
    },
    'sensor de temperatura': {
      'Phillips': ['Heartstart MRX','MP70 Intellivue','MP50 Intellivue'],
      'Drager': ['Delta Infinity'],
      'Datex Ohmeda': ['Cardiocap5'],
      'Mindray': ['V12'],
      'Orantech': ['Cardiocap5']
    },
    'pedal bipolar': {
      'Valleylab': ['Force 2','Force FX','ForceTriad','FT10'],
      'Conmed': ['Sabre Genesis']
    },
    'pedal monopolar': {
      'Valleylab': ['Force 2','Force FX','ForceTriad','FT10'],
      'Conmed': ['Sabre Genesis'],
      'Olympus': ['ESG-400'],
      'Ellman': ['Electrocauterio Ellman']
    },
    'pedal ligasure': { 'Covidien': ['Pedal Bipolar Morado','Pedal Bipolar Anaranjado','Pedal Bipolar Doble'] },
    'placa para electrocauterio': { 'OBS': ['Placa desechable'] },
    'lapiz para electrocauterio': {
      'Avante': ['Placa desechable'],
      'OBS': ['Placa desechable'],
      'Covidien': ['Placa desechable'],
      'Conmed': ['Placa desechable'],
      'Smith&Nephew': ['Placa desechable']
    },
    'Línea de Muestreo de CO2': {
      'Datex Ohmeda': ['Aisys','Avance','Cardiocap5'],
      'Phillips': ['Heartstart MRX']
    },
    'cable para pinza bipolar': { 'Covidien': ['Pinza Bipolar'] },
    'cable trocal ecg': { 'Drager': ['Delta Infinity'] },
    'carro para electrocauterio': {
      'Erbe': ['Para ERBE'],
      'Covidien': ['Force 2','Force FX','ForceTriad','FT10']
    },
    'carro rojo Emergencias': {
      'Lifeline': ['Carro de Emergencias'],
      'GM': ['Carro de Emergencias NUEVO']
    },
    'eliminador': {
      'Phillips': ['Fuente de poder Desfibrilador MRX'],
      'Drager': ['Infinity Delta']
    },
    'pieza de mano para gen11': {
      'Ethicon': ['Pieza Gris con 4 usos HP054','Pieza Gris con 70 usos HP054','Pieza Gris con 87 usos HP054','Pieza Gris con 92 usos HP054','Pieza Azul HPBLUE','HP054 usos ilimitados']
    },
    'armonico gen11': {
      'Ethicon': ['Armonico GEN11','Pedal Gen11']
    },
    'UPS': {
      'ONLINE': ['LP3KOLT']
    }
  },
  hospitalizacion: {
    'aspirador': { 'Hergon': ['7E-A NUEVO'] },
    'cama hospitalaria electrica': {
      'Hill Roon': ['Versacare'],
      'stryker': ['MPS Secure II','S3','MPS']
    },
    'camilla': {
      'Hill Roon': ['P8000'],
      'Stryker': ['Prime Series','1015 Stretcher']
    },
    'cuna termica': { 'GE Healthcare': ['Panda Warmer'] },
    'incubadora': { 'GE': ['Giraffe'] },
    'mesa de exploracion': {},
    'ventilador': { 'Nellcor': ['Puritan Benett 840'] }
  },
  radiologia: {
    'arco en c': {
      'Phillips': ['BV Pulsera 2008','Veradius 2016']
    },
    'bateria': {},
    'chasis': {},
    'flat panel': {},
    'rayos x rodable': {},
    'rayos x portatil': {}
  },
  urologia: {
    'cistoscopio': {},
    'histeroscopio': {
      'Storz': ['Lente 2.9mm-30°-30cm largo 26120BA','Pinza Grasper Semirrigida 227-029-533','pinza de biopsia Semirrigida 227-029-560','tijeras de cirugia Semirrigida 227-029-514','vaina de histeroscopio 5mm 26153EA','fibra de luz Gris AA-496']
    },
    'resectoscopio': {
      'stryker': ['Lente Precision 4mm-30°-30cm 0502-990-030','Elemento de Trabajo Pasivo tipo Inglesas 502-880-401','Obturador Timberlake 502-880-003','Obturador Estandar 502-880-001','Obturador Visual de 24/26 fr 502-880-002','Vaina Exterior de Flujo Continuo 502-880-426','Adaptador Interno para evacuador Ellik 502-880-602','Adapatador de Jeringa 502-880-006','Cable Monopolar GM','Fibra de Luz Gris GM','Electrodo de Bucle de corte recto 0504-880-500','Electrodo de bola Vaporizadora con Hoyuelos 0504-880-600','Electrodo de bola Rodante de 3mm 0504-880-200','Electrodo de Cuchillo 0540-880-800']
    },
    'ureteroscopio flexible': { 'Richard Wolf': ['7305.006'] },
    'ureteroscopio rigido': {}
  },
  artroscopia: {
    'rasurador shaver': { 'Stryker': ['Core','Core2','Crossfire','Crossfire2'] },
    'radio frecuencia serfas': { 'Stryker': ['Serfas Energy 90S 4.0mm X 135mm'] },
    'puntas de radio frecuencia': {
      'Stryker': ['Cortadora Agresiva Plus 3.5mm x 80mm Amarillo','Cortadora Agresiva Plus 5.0mm x 125mm Azul','Cortadora Angular 4.0mm x 125mm Rojo','Cortadora Angular 5.0mm x 125mm Azul','Cortadora Resector 3.5mm x 125mm Amarillo','Cortadora XL Agresiva 4.0mm x 180mm Rojo','Fresa 5mm x 125mm Azul','Fresa de Abrasion 2.0mm x 80mm Morado','Fresa Redonda de 12 filos 5.5mm x 125mm Café','Fresa de Barril de 12 hilos 5.5mm x 125mm Cafe']
    },
    'puntas serfas de radiofrecuencia': { 'Stryker': ['90-S 4.0mm x 135mm Rojo','50-S 3.5mm x 135mm Amarillo'] },
    'bomba de irrigacion': { 'Stryker': ['Flow Control'] },
    'Hoja de Sierra Sagital': { 'Stryker': ['90191.2','90221.2'], 'GM': ['Prueba'] },
    'artroscopio': {
      'Stryker': ['4mm-30°Ideal eyes','4mm-30°Precision','4mm-30° de rosca Precision','4mm-0 Precision°','2.7mm-30°'],
      'Artrhex': ['2.7mm-30°'],
      'Storz': ['1.9mm-30°']
    },
    'transmisores': {},
    'pedal': { 'Arthocare': ['Coblator II'], 'Stryker': ['serfas'] },
    'set de taladros de artroscopia System 4': {
      'Stryker': ['System 4 Mandril con llave 1/4','System 4 Llave Larga','System 4 Llave Corta','System 4 Taladro Con Mandril 5/32','System 4 Taladro Con Mandril 1/4','System 4 Taladro Pequeño','System 4 Destornillador CD4 Gatillo Doble','System 4 Pinza de Alambre','System 4 Pinza de Sujecion','System 4 Sierra Sagital Sabo 2','System 4 Baterias','System 4 Cargador de Baterias']
    },
    'set de taladros de artroscopia System 7': {
      'Stryker': ['System 7 Mandril con llave 1/4','System 7 Mandril con llave 5/32','System 7 Llave Larga','System 7 Llave Corta','System 7 Taladro pequeño','System 7 Pinza de Pasador de Gatillo Doble','System 7 Porta Pines de Doble Gatillo','System 7 Hudsom','System 7 Hudsom Modificada Trinkle','System 7 Escoreador Largo','System 7 Trinkle','System 7 Pinza de Alambre','System 7 Mandril de Bloqueo Sin Llave 1/4','System 7 Taladro Rotatoria de Doble Gatillo','System 7 Sierra Reciproca','System 7 Sierra Ocilatoria','System 7 Baterias','System 7 Sierra Sagital','Cargador de Baterias']
    },
    'set de taladros de artroscopia System 8': {
      'Stryker': ['System 8 Mandril llave 1/4','System 8 Llave Larga','System 8 Mandril con llave 5/32','System 8 Taladro pequeño','System 8 Llave Corta','System 8 Pinza de Pasador de Gatillo Doble','System 8 Porta Pines de Doble Gatillo','System 8 Hudsom','System 8 Hudsom Modificada Trinkle','System 8 Escoreador Largo','System 8 Trinkle','System 8 Pinza de Alambre','System 8 Mandril de Bloqueo Sin Llave 1/4','System 8 Pieza de Mano Rotatoria de Doble Gatillo','System 8 Sierra Reciproca','System 8 Sierra Ocilatoria','System 8 Baterias','System 8 Sierra Sagital','Cargador de Baterias']
    },
    'Set de Taladros Electrico Core Azul': {
      'Stryker': ['Micro Taladro Electrico 6400-015-000','Sierra Sagital Electrica 6400-034-000','Sierra Ocilatoria Electrica 6400-031-000','Sierra Reciproca Electrica 6400-037-000','Taladro de Mandrin 5/32 4100-132-000','Taladro de Mandril 1/4 4100-131-000','Hudsom/Taladro Trinkle Modificado 4100-135-000','Taladro Pequeño AO 4100-110-000','Micro Taladro Recto 5100-015-250','Pinza de Alfiler 4100-125-000','Pinza de Alambre 4100-062-000','Controlador Universal 6400-099-000','Destornillador de Cable Elecrtico 6400-062-000','Cable para TPS-CORE 5100-004-000','Interuptor Manual 6400-9','Llave Corta','Llave Larga']
    },
    'Set de Taladros Electrico Core Negro': {
      'Stryker': ['Micro Taladro Electrico 5400-15','Sierra Sagital Electrica 5400-34','Escoreador Largo 4100-210-000','Sierra Ocilatoria Electrica','Sierra Reciproca Electrica','Taladro de Mandril con Llave 5/32 4100-132','Taladro de Mandril con llave 1/4 4100-131','Hudsom/Taladro Trinkle Modificado','Taladro Pequeño AO-4100-110','Micro Taladro Recto 5100-15-250','Pinza de Alfiler 4100-125','Pinza de Alambre 4100-62','Controlador Universal 6400-099-000','Destornillador de Cable Elecrtico','Cable para TPS-CORE 5100-004-000','Interuptor Manual 6400-9']
    },
    'camisa': {
      'Artrhex': ['Camisa 2.7mm-4mm'],
      'Stryker': ['Camisa 2.7mm-4mm']
    },
    'opturador': {
      'Artrhex': ['Opturador 2.7mm'],
      'Stryker': ['Opturador 2.7mm']
    },
    'pieza de mano': { 'Stryker': ['Formula Core Negra','Formula 180 Azul'] },
    'cable para pieza de mano': {},
    'charola de esterilizacion': {
      'Stryker': ['Set de Taladro System 4','Set de Taladro System 7','Set de Taladro System 8','Set de Taladros Electrico Core Azul','Set de Taladros Electrico Core Negro','Set de cirugia para hombro y tobillo','Set de cirugia de rodilla'],
      'Arthrex': ['AR-3100'],
      'GM': ['GM']
    },
    'Linea de irrigacion': { 'Stryker': ['Flow Control'] },
    'meditronic': {},
    'set de cirugia para tobillo y muñeca': {
      'Stryker': ['Nariz Roma hacia Arriba de 2.7mm 242-100-013','Nariz Recta 242-100-012','Pinza Grasper hacia Arriba 2.1mm 242-100-006','Pinza Grasper de 2.5mm 242-100-008','Nariz Recta Punzon de Articulacion Pequeña 242-100-002','Grasper Nariz hacia Abajo 242-100-005','Nariz derecha Punzon en Articulacion Pequeña 242-100-003','Nariz Izquierda Pequeño Golpe en la Articulacion 2.1mm 242-100-004','Tijeras para Juntas Pequeñas 242-100-007','Sonda de Articulacion Pequeña Recta 242-100-014','Sonda para Articulaciones Pequeñas de 90° 242-100-015','Sonda para Articulaciones pequeñas de 30° 242-100-016']
    },
    'set de cirugia de rodilla': {
      'Stryker': ['Pinza de Mordida Grande hacia Arriba 3.4mm 15°','Morida Grande del Eje Ascendente Recto','Punzon de Mordida Grande de 3.4mm Recto','Pinzas para Tejidos Blandos de 3.4mm X 120mm','Micro Punzon de Tijera Recto 3.4mm','Mordedor de Punta Derecha 3.4mm x 45°','Mordedor de Punta Izquierda de 3.4mm 45°','Punzon Rotatorio de 3.4 mm y 90° a la Derecha','Sonda con Mango en Forma de Anillo','Opturador','Sonda','Aguja de Negra','Opturador de Punta Roma para Canula de Entrada / Salida 5.8mm','Palpador Switching Stick Pequeño','Canula de 100mm','Mango de Bisturi'],
      'V.MUELLER': ['Tijeras de Diseccion Inoxidables','Porta agujas','Pinzas para la Arteria de Kelly','Pinza ADSON 1X2 Dientes'],
      'KONIG': ['Pinza Quirurgica/Tolla','Pinza para Arteria de Crile- 1/2" de Largo']
    }
  },
  ceye: { 'autoclave de camara 95 l': {}, 'monitor': {} },
  ginecologia: {
    'Camilla Ginecologíca': { 'Stryker': ['Geynnie'] },
    'Ultrasonido': { 'GE': ['Logic P3'] },
    'mesa de exploracion': { 'Midmark': ['Modelo 404','Ritte 622'] },
    'Impresora': { 'Sony': ['UP-D897'] }
  },
  otorrinolaringologia: {
    'Microdebrilador': { 'Meditronic': ['XPS 3000'] },
    'Pedal Microdebrilador': { 'Meditronic': ['Xomed para XPS 3000'] },
    'Pieza de mano': { 'Meditronic': ['Magnum ii'] },
    'Electrocirugia': { 'Arthrocare': ['Coblator II'] },
    'Pedal': { 'Arthrocare': ['Pedal para Coblator Aquiline'] }
  },
  material: {
    'Limpiador y Desengrasante': { 'Steren': ['Desengrasante Y Limpiador'] },
    'Playon': { 'GM': ['GM'] }
  },
  'endoscopia veterinaria': {
    'Gastroscopio Veterinaria': { 'Grupo Medibuy': ['EG-GP13N Video Gastroscopio','EG-GP13T Video Gastroscopio'] },
    'Colonoscopio Veterinaria': { 'Grupo Medibuy': ['EC-GP13L Video Colonoscopio'] },
    'Procesador Veterinaria': { 'Grupo Medibuy': ['HV-3101 Procesador'] },
    'Monitor de Imagen': { 'Grupo Medibuy': ['HP-1315 Monitor de imagen'] },
    'Coledoscopio Veterinaria': { 'Grupo Medibuy': ['CHD-1 Coledocoscopio Flexible de Video de un Solo Uso'] },
    'Cabezal Veterinaria': { 'Grupo Medibuy': ['HP1315 Cabezal'] },
    'Tapon de Biopsia': { 'Grupo Medibuy': ['MB-358'] },  
    'Valvula de Succion': { 'Grupo Medibuy': ['MH-443'] },
    'Valvula de aire/agua': { 'Grupo Medibuy': ['MH-438'] },
    'Tapon de Inmersion': { 'Grupo Medibuy': ['EDC-038'] },
    'Probador de Fuga': { 'Grupo Medibuy': ['EDC-002'] },
    'Kit de Limpieza': { 'Grupo Medibuy': ['MH-946'] },
    'Adaptador de Limpieza de Succion': { 'Grupo Medibuy': ['MH-856'] },
    'Adaptador de Limpieza del canal de aire/agua': { 'Grupo Medibuy': ['MH-948'] },
    'Tapon de Canal': { 'Grupo Medibuy': ['MH-944'] },
    'Cepillo de Limpieza de la Apertura del canal': { 'Grupo Medibuy': ['MH-507'] },
    'Cepillo de Limpieza del Canal': { 'Grupo Medibuy': ['BW-20T'] }
  },
  'refacciones de endoscopia': {
    "tubo de insercion": {
      "Olympus": [
        "FITO103 - Tubo de inserción 12.9 × 1622 mm para CF-H180AL, CF-Q180AL, CF-Q160AL - Lote: 261372 / 20260526",
        "FITO101 - Tubo de inserción 9.2 × longitud 1030 mm para GIF-Q180, GIF-Q165 - Lote: 261365 / 20260501",
        "FITO105 - Tubo de inserción 9.5 × longitud 1040 mm para GIF-H190 - Lote: 251229 / 20260702",
        "FITO116 - Tubo de inserción 10.2 × longitud 1025 mm para GIF-H180J - Lote: 251323 / 20260104",
        "FITO113 - Tubo de inserción 10.15 × 1015 mm - Lote: 251311 / 20251216"
      ]
    },
    "tubo de guia de luz": {
      "Olympus": [
        "FLGT101 - Tubo de guía de luz 13 mm × longitud 1416 mm para 160/180/190 - Lote: 261388 / 20260526"
      ]
    },
    "camisa para lineas ensamblado": {
      "Olympus": [
        "FCPA105 - Camisa para líneas ensamblado Coil Pipe para CF-H180AL - Lote: 261386 / 20260528",
        "FCPA106 - Camisa para líneas ensamblado Coil Pipe para CF-Q180AL - Lote: 261343 / 20260304",
        "FCPA121 - Camisa para líneas ensamblado Coil Pipe para CF-H190L/i - Lote: 241194 / 20241116",
        "FCPA121-F - Camisa para líneas ensamblado Coil Pipe para CF-H190L/i - Lote: 261367 / 20260501",
        "FCPA111 - Camisa para líneas ensamblado Coil Pipe para GIF-Q180 / GIF-Q165 - Lote: 261376 / 20260510",
        "FCPA101 - Camisa para líneas ensamblado Coil Pipe para GIF-H190 - Lote: 261389 / 20260605",
        "FCPA103 - Camisa para líneas ensamblado Coil Pipe para GIF-HQ190 - Lote: 261386 / 20260528",
        "FCPA132 - Camisa para líneas ensamblado Coil Pipe para TJF-Q180V"
      ]
    }
  }
};

// ==================== FUNCIONES DE UTILIDAD ====================
function norm(s){
  return (s||'').toString().trim().toLowerCase()
    .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
    .replace(/\s+/g,' ');
}

function getMarcas(tipo, subtipo){
  const tipoSlug = norm(tipo);
  const subSlug  = norm(subtipo);
  const tipoNode = marcasModelosPorSubtipo[tipoSlug];
  if(!tipoNode) return [];
  
  let node = null;
  for (const k in tipoNode){
    if (norm(k) === subSlug){ node = tipoNode[k]; break; }
  }
  if(!node) return [];
  return Object.keys(node);
}

function getModelos(tipo, subtipo, marca){
  const tipoSlug = norm(tipo);
  const subSlug  = norm(subtipo);
  const tipoNode = marcasModelosPorSubtipo[tipoSlug];
  if(!tipoNode) return [];
  
  let node = null;
  for (const k in tipoNode){
    if (norm(k) === subSlug){ node = tipoNode[k]; break; }
  }
  if(!node) return [];
  const arr = node[marca] || [];
  return Array.isArray(arr) ? arr : [];
}

// ==================== DEFINICIÓN DEL COMPONENTE ALPINE.JS ====================
document.addEventListener('alpine:init', () => {
  Alpine.data('EditarUI', (initial, updateUrl, csrfToken) => {
    return {
      // Estado del formulario
      tipoSel: initial.tipo || '',
      subtipoSel: initial.subtipo || '',
      marcaSel: initial.marca || '',
      modeloSel: initial.modelo || '',
      
      form: {
        serie: initial.serie || '',
        anio: initial.anio || '',
        descripcion: initial.descripcion || '',
        fecha: initial.fecha || '',
        observaciones: initial.observaciones || ''
      },
      
      // Datos para selects
      tipos: [],
      subtipos: [],
      marcas: [],
      modelos: [],
      
      // Evidencias
      previews: [initial.evid1 || '', initial.evid2 || '', initial.evid3 || ''],
      videoUrl: initial.video || '',
      
      // Firma
      firmaExistente: initial.firma || '',
      firmaData: '',
      reFirmar: false,
      isDrawing: false,
      
      // Componentes
      sheet: {
        lista: []
      },
      seleccion: [],
      
      // Utilidades
      updateUrl: updateUrl,
      csrfToken: csrfToken,
      sheetHeight: '50vh',
      
      // Métodos
      boot() {
        // Cargar tipos desde los datos locales
        this.tipos = Object.keys(tiposEquipos);
        
        // Inicializar firma
        setTimeout(() => this.inicializarFirma(), 100);
        
        // Inicializar componentes
        this.inicializarComponentes();
        
        // Cargar valores guardados
        this.cargarValoresGuardados();
      },
      
      titleCase(str) {
        if (!str) return '';
        return str.split(' ').map(word => 
          word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
        ).join(' ');
      },
      
      cargarValoresGuardados() {
        // Si hay un tipo guardado, cargar sus subtipos
        if (this.tipoSel) {
          this.subtipos = tiposEquipos[this.tipoSel] || [];
          
          // Si hay un subtipo guardado, cargar sus marcas
          if (this.subtipoSel) {
            this.marcas = getMarcas(this.tipoSel, this.subtipoSel);
            
            // Si hay una marca guardada, cargar sus modelos
            if (this.marcaSel) {
              this.modelos = getModelos(this.tipoSel, this.subtipoSel, this.marcaSel);
            }
          }
        }
      },
      
      onTipoChange(cargarDependencias = true) {
        if (cargarDependencias && this.tipoSel) {
          this.subtipoSel = '';
          this.marcaSel = '';
          this.modeloSel = '';
          this.subtipos = tiposEquipos[this.tipoSel] || [];
          this.marcas = [];
          this.modelos = [];
        }
      },
      
      onSubtipoChange(cargarDependencias = true) {
        if (cargarDependencias && this.tipoSel && this.subtipoSel) {
          this.marcaSel = '';
          this.modeloSel = '';
          this.marcas = getMarcas(this.tipoSel, this.subtipoSel);
          this.modelos = [];
          
          // Cargar componentes del subtipo
          this.cargarComponentesSubtipo(this.tipoSel, this.subtipoSel);
        }
      },
      
      onMarcaChange() {
        if (this.tipoSel && this.subtipoSel && this.marcaSel) {
          this.modeloSel = '';
          this.modelos = getModelos(this.tipoSel, this.subtipoSel, this.marcaSel);
        }
      },
      
      // Componentes
      inicializarComponentes() {
        if (initial.componentes && initial.componentes.length) {
          this.seleccion = JSON.parse(JSON.stringify(initial.componentes));
          this.sheet.lista = JSON.parse(JSON.stringify(initial.componentes));
        }
        
        if (this.tipoSel && this.subtipoSel) {
          this.cargarComponentesSubtipo(this.tipoSel, this.subtipoSel);
        }
      },
      
      cargarComponentesSubtipo(tipo, subtipo) {
        // Intentar obtener componentes de los datos locales (si existen)
        const tipoSlug = norm(tipo);
        const subSlug = norm(subtipo);
        const tipoNode = marcasModelosPorSubtipo[tipoSlug];
        
        let componentes = [];
        if (tipoNode) {
          for (const k in tipoNode) {
            if (norm(k) === subSlug) {
              // Si el subtipo tiene componentes definidos, usarlos
              const node = tipoNode[k];
              if (node && typeof node === 'object') {
                // Extraer nombres de componentes de las marcas
                const marcas = Object.keys(node);
                marcas.forEach(marca => {
                  if (node[marca] && Array.isArray(node[marca])) {
                    node[marca].forEach(modelo => {
                      // Si el modelo parece un componente
                      if (modelo.includes('componente') || modelo.includes('kit') || modelo.includes('set')) {
                        componentes.push({
                          nombre: modelo,
                          cantidad: 1,
                          incluido: 1
                        });
                      }
                    });
                  }
                });
              }
              break;
            }
          }
        }
        
        // Si no se encontraron componentes, crear algunos básicos
        if (!componentes.length) {
          componentes = [
            { nombre: 'Componente 1', cantidad: 1, incluido: 1 },
            { nombre: 'Componente 2', cantidad: 1, incluido: 1 }
          ];
        }
        
        this.fusionarComponentes(componentes);
      },
      
      fusionarComponentes(nuevosComponentes) {
        if (!this.seleccion || !this.seleccion.length) {
          this.seleccion = JSON.parse(JSON.stringify(nuevosComponentes));
          this.sheet.lista = JSON.parse(JSON.stringify(nuevosComponentes));
          this.syncSeleccion();
          return;
        }
        
        const existentesNombres = this.seleccion.map(c => c.nombre);
        const nuevosParaAgregar = nuevosComponentes.filter(c => !existentesNombres.includes(c.nombre));
        
        if (nuevosParaAgregar.length) {
          this.seleccion = [...this.seleccion, ...nuevosParaAgregar];
          this.sheet.lista = JSON.parse(JSON.stringify(this.seleccion));
          this.syncSeleccion();
        }
      },
      
      syncSeleccion() {
        this.seleccion = JSON.parse(JSON.stringify(this.sheet.lista));
      },
      
      dec(idx) {
        if (this.sheet.lista[idx] && this.sheet.lista[idx].cantidad > 0) {
          this.sheet.lista[idx].cantidad--;
          this.syncSeleccion();
        }
      },
      
      inc(idx) {
        if (this.sheet.lista[idx]) {
          this.sheet.lista[idx].cantidad++;
          this.syncSeleccion();
        }
      },
      
      restaurarPlantilla() {
        if (this.tipoSel && this.subtipoSel) {
          this.cargarComponentesSubtipo(this.tipoSel, this.subtipoSel);
          this.mostrarMensaje('Componentes restaurados a la plantilla del subtipo', 'success');
        }
      },
      
      // Evidencias
      previewImg(event, index) {
        const file = event.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = (e) => {
          this.previews[index] = e.target.result;
        };
        reader.readAsDataURL(file);
      },
      
      previewVideo(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = (e) => {
          this.videoUrl = e.target.result;
        };
        reader.readAsDataURL(file);
      },
      
      // Firma
      inicializarFirma() {
        const canvas = this.$refs.canvas;
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        let drawing = false;
        
        canvas.width = canvas.offsetWidth || 400;
        canvas.height = canvas.offsetHeight || 170;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#0f172a';
        
        const startDraw = (e) => {
          drawing = true;
          this.isDrawing = true;
          const pos = this.getCanvasPos(e, canvas);
          ctx.beginPath();
          ctx.moveTo(pos.x, pos.y);
        };
        
        const draw = (e) => {
          if (!drawing) return;
          const pos = this.getCanvasPos(e, canvas);
          ctx.lineTo(pos.x, pos.y);
          ctx.stroke();
        };
        
        const endDraw = () => {
          if (drawing) {
            drawing = false;
            this.isDrawing = false;
            this.firmaData = canvas.toDataURL('image/png');
          }
        };
        
        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', endDraw);
        canvas.addEventListener('mouseleave', endDraw);
        
        canvas.addEventListener('touchstart', (e) => {
          e.preventDefault();
          const touch = e.touches[0];
          const mouseEvent = new MouseEvent('mousedown', {
            clientX: touch.clientX,
            clientY: touch.clientY,
          });
          canvas.dispatchEvent(mouseEvent);
        });
        canvas.addEventListener('touchmove', (e) => {
          e.preventDefault();
          const touch = e.touches[0];
          const mouseEvent = new MouseEvent('mousemove', {
            clientX: touch.clientX,
            clientY: touch.clientY,
          });
          canvas.dispatchEvent(mouseEvent);
        });
        canvas.addEventListener('touchend', (e) => {
          e.preventDefault();
          const mouseEvent = new MouseEvent('mouseup', {});
          canvas.dispatchEvent(mouseEvent);
        });
      },
      
      getCanvasPos(e, canvas) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        return {
          x: (e.clientX - rect.left) * scaleX,
          y: (e.clientY - rect.top) * scaleY
        };
      },
      
      clearSig() {
        const canvas = this.$refs.canvas;
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        this.firmaData = '';
      },
      
      toggleFirma() {
        this.reFirmar = !this.reFirmar;
        if (this.reFirmar) {
          setTimeout(() => this.inicializarFirma(), 100);
        }
      },
      
      // Envío del formulario
      async submit() {
        const form = document.getElementById('frmEdit');
        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }
        
        if (!this.firmaData && !this.firmaExistente) {
          this.mostrarMensaje('Debes proporcionar una firma digital', 'error');
          return;
        }
        
        if (this.firmaData) {
          this.firmaExistente = this.firmaData;
        }
        
        const formData = new FormData(form);
        
        if (this.firmaData) {
          formData.set('firmaDigital', this.firmaData);
        } else if (this.firmaExistente) {
          formData.set('firmaDigital', this.firmaExistente);
        }
        
        this.seleccion.forEach((c, i) => {
          formData.append(`componentes[${i}][nombre]`, c.nombre);
          formData.append(`componentes[${i}][cantidad]`, c.cantidad);
          formData.append(`componentes[${i}][incluido]`, c.incluido ? 1 : 0);
        });
        
        try {
          const response = await fetch(this.updateUrl, {
            method: 'POST',
            body: formData,
            headers: {
              'X-CSRF-TOKEN': this.csrfToken
            }
          });
          
          const data = await response.json();
          
          if (response.ok) {
            this.mostrarMensaje('Producto actualizado correctamente', 'success');
            setTimeout(() => {
              window.location.href = '/registros';
            }, 1500);
          } else {
            this.mostrarMensaje(data.message || 'Error al actualizar el producto', 'error');
          }
        } catch (error) {
          console.error('Error al enviar el formulario:', error);
          this.mostrarMensaje('Error de conexión. Intenta nuevamente.', 'error');
        }
      },
      
      mostrarMensaje(mensaje, tipo = 'info') {
        const container = document.getElementById('toastPlace');
        if (!container) return;
        
        const colores = {
          success: '#cfeee7',
          error: '#f8d7da',
          info: '#d1ecf1'
        };
        
        const iconos = {
          success: 'bi-check-circle-fill',
          error: 'bi-exclamation-circle-fill',
          info: 'bi-info-circle-fill'
        };
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-dark border-0 mb-2`;
        toast.style.backgroundColor = colores[tipo] || colores.info;
        toast.innerHTML = `
          <div class="d-flex">
            <div class="toast-body">
              <i class="bi ${iconos[tipo] || iconos.info} me-2"></i>
              ${mensaje}
            </div>
            <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
        `;
        container.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
          toast.remove();
        });
      }
    };
  });
});
</script>
@endsection