@extends('layouts.app')

@section('title','Hojalatería')
@section('titulo','Proceso: Hojalatería')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@php
  $equipoId = $id ?? ($registro->id ?? null);

  $saveUrl = \Illuminate\Support\Facades\Route::has('procesos.guardar')
    ? route('procesos.guardar', $equipoId)
    : url('/procesos/'.$equipoId.'/guardar');

  $inventarioUrl = \Illuminate\Support\Facades\Route::has('registros.index')
    ? route('registros.index')
    : ( \Illuminate\Support\Facades\Route::has('inventario') ? route('inventario') : url('/inventario') );
@endphp

<style>
:root{
  --ink:#0f172a; --muted:#6b7280; --line:#e5e7eb; --bg:#f7f9fc; --panel:#ffffff;
  --pri:#2563eb; --pri-ink:#0b2a4a; --ok:#16a34a; --radius:16px; --shadow:0 14px 40px rgba(2,6,23,.08);
}
*{ box-sizing:border-box }
body{ background:var(--bg); font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; color:var(--ink) }

/* Layout */
.wrap{ max-width:980px; margin:0 auto; padding:0 16px }
.headcard{ margin-top:92px; background:var(--panel); border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow); padding:16px 18px }
.headcard .title{ font-weight:800; letter-spacing:-.02em; margin:0 }
.subtle{ color:var(--muted) }

/* Section card */
.card-soft{ background:var(--panel); border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow) }
.card-soft .hd{ padding:14px 16px; border-bottom:1px solid var(--line); display:flex; align-items:center; gap:10px }
.card-soft .bd{ padding:16px }

/* Inputs */
.form-control, .form-select{ border-radius:12px; border:1px solid var(--line); padding:.8rem .9rem }
.form-control:focus, .form-select:focus{ border-color:#c7d2fe; box-shadow:none }

/* Upload tiles */
.grid-tiles{ display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:12px }
.tile{
  position:relative; border:1px dashed #d7ddee; background:#fbfcff; border-radius:14px; min-height:140px;
  display:grid; place-items:center; cursor:pointer; transition:transform .06s ease, box-shadow .2s ease;
}
.tile:hover{ transform:translateY(-1px); box-shadow:0 10px 24px rgba(2,6,23,.06) }
.tile input{ display:none }
.tile .hint{ color:var(--muted); font-weight:600; font-size:.9rem; text-align:center }
.tile .preview{ position:absolute; inset:0; border-radius:14px; overflow:hidden }
.tile .preview img, .tile .preview video{ width:100%; height:100%; object-fit:cover }
.tile .remove{
  position:absolute; right:8px; top:8px; background:#fff; border:1px solid var(--line); border-radius:999px;
  width:30px; height:30px; display:grid; place-items:center; font-weight:800; cursor:pointer;
}

/* Checklist */
.ck-group{ border:1px solid var(--line); border-radius:14px; padding:10px 12px; background:#fff; }
.ck-hd{ display:flex; align-items:center; justify-content:space-between; gap:12px; padding:6px 2px 10px }
.ck-hd .gtitle{ font-weight:800; }
.ck-items{ display:flex; flex-direction:column; gap:10px }
.ck{
  display:grid; grid-template-columns:24px 1fr; gap:10px; align-items:start;
  border:1px solid var(--line); background:#fcfdff; border-radius:12px; padding:10px 12px;
}
.ck input[type="checkbox"]{ width:18px; height:18px; margin-top:3px }
.ck .name{ font-weight:700 }
.ck small{ color:var(--muted) }
.ck .note{ margin-top:6px }
.ck .note input{ width:100%; border:1px solid var(--line); border-radius:10px; padding:.55rem .7rem; font-size:.9rem }

/* Buttons */
.btn-ghost{ border:1px solid var(--line); background:#fff; color:var(--ink); border-radius:12px; padding:.7rem 1rem; font-weight:800 }
.btn-primary-soft{
  border:1px solid #cfe0ff; background:linear-gradient(180deg,#eef4ff,#e7efff); color:#0b2a4a; border-radius:12px;
  padding:.8rem 1.1rem; font-weight:800;
}

/* Footer actions */
.form-actions{ display:flex; justify-content:flex-end; gap:10px; padding:12px 0 }

/* Badge */
.badge-dot{ display:inline-flex; align-items:center; gap:.45rem; background:#eef2ff; color:#1d4ed8; border:1px solid #dbe4ff; padding:6px 10px; border-radius:999px; font-weight:800; font-size:.8rem }
.badge-dot .dot{ width:8px; height:8px; border-radius:999px; background:#1d4ed8 }

/* ───── SweetAlert2 minimal / elegante ───── */
.swal2-popup.swal2-elegant{
  border-radius:20px;
  padding:18px 20px 20px;
  box-shadow:0 18px 45px rgba(15,23,42,0.16);
  border:1px solid #e5e7eb;
  font-family:"Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}
.swal2-title.swal2-elegant-title{
  font-size:1rem;
  font-weight:700;
  letter-spacing:-.02em;
  color:var(--ink);
  margin-bottom:.4rem;
}
.swal2-html-container.swal2-elegant-html{
  margin:0;
  font-size:.85rem;
  color:var(--muted);
}
.swal2-confirm.btn-primary-soft{
  font-size:.85rem;
  padding:.6rem 1.2rem;
  border-radius:999px;
}
.swal2-confirm.btn-ghost{
  font-size:.85rem;
  padding:.6rem 1.2rem;
  border-radius:999px;
}
.swal2-loader{
  display:none !important; /* ocultar spinner default */
}
.swal2-icon{
  box-shadow:none !important;
  border:none !important;
}

/* Barra de progreso dentro del modal */
.sw-progress-wrap{
  margin-top:.35rem;
}
.sw-progress-bg{
  width:100%;
  height:10px;
  border-radius:999px;
  background:#eef1f6;
  overflow:hidden;
}
.sw-progress-bar{
  width:0%;
  height:100%;
  border-radius:999px;
  background:linear-gradient(90deg,#4f46e5,#22c55e);
  transition:width .2s ease-out;
}
.sw-progress-text{
  margin-top:.5rem;
  font-size:.8rem;
  color:var(--muted);
}

/* Responsive */
@media (max-width:640px){
  .grid-tiles{ grid-template-columns:1fr 1fr }
}
</style>

<div class="wrap" x-data="HojUI()" x-init="initLote()">
  <div class="headcard d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
      <div class="title h4 mb-1">Hojalatería — Equipo #{{ $equipoId }}</div>
      <div class="subtle">Registra reparación estética/estructural con evidencia y checklist.</div>
    </div>
    <span class="badge-dot"><span class="dot"></span> Proceso activo</span>
  </div>

  <form id="frmHoj" class="mt-3" action="{{ $saveUrl }}" method="POST" enctype="multipart/form-data" @submit.prevent="submit">
    @csrf
    <input type="hidden" name="tipo_proceso" value="hojalateria">
    <input type="hidden" name="checklist_json" x-model="checklistJson">
    {{-- 🔹 lote: ids del lote en JSON para clonar procesos --}}
    <input type="hidden" name="lote_ids" x-model="loteIdsJson">

    {{-- Descripción --}}
    <div class="card-soft mt-3">
      <div class="hd">
        <strong>Descripción del trabajo</strong>
      </div>
      <div class="bd">
        <textarea
          class="form-control"
          name="descripcion_proceso"
          rows="4"
          placeholder="Detalle de daños, piezas intervenidas, técnicas (enderezado, soldadura, reemplazos), tiempos y observaciones."
          required
        ></textarea>

        {{-- Checkbox aplicar a todo el lote (solo si este equipo es el maestro del lote) --}}
        <div class="form-check mt-3" x-show="isLoteMaster">
          <input
            class="form-check-input"
            type="checkbox"
            id="aplicar_lote"
            name="aplicar_lote"
            value="1"
            x-model="aplicarLote"
          >
          <label class="form-check-label" for="aplicar_lote">
            Aplicar este proceso a todos los equipos del lote
          </label>
          <small class="text-muted d-block mt-1">
            Usará la misma descripción, evidencias y checklist para todos los equipos creados en el mismo registro masivo.
          </small>
        </div>
      </div>
    </div>

    {{-- Evidencias --}}
    <div class="card-soft mt-3">
      <div class="hd"><strong>Evidencias (3 fotos + 1 video)</strong></div>
      <div class="bd">
        <div class="grid-tiles">
          {{-- Foto 1 --}}
          <label class="tile">
            <input type="file" accept="image/*" name="evidencia1" @change="pickImage($event,0)">
            <div class="hint" x-show="!images[0]">
              <div class="mb-1">Daño inicial</div>
              <small>PNG/JPG</small>
            </div>
            <div class="preview" x-show="images[0]">
              <img :src="images[0]">
              <button class="remove" type="button" @click="removeImage(0)">×</button>
            </div>
          </label>

          {{-- Foto 2 --}}
          <label class="tile">
            <input type="file" accept="image/*" name="evidencia2" @change="pickImage($event,1)">
            <div class="hint" x-show="!images[1]">
              <div class="mb-1">Proceso (enderezado/soldado)</div>
              <small>PNG/JPG</small>
            </div>
            <div class="preview" x-show="images[1]">
              <img :src="images[1]">
              <button class="remove" type="button" @click="removeImage(1)">×</button>
            </div>
          </label>

          {{-- Foto 3 --}}
          <label class="tile">
            <input type="file" accept="image/*" name="evidencia3" @change="pickImage($event,2)">
            <div class="hint" x-show="!images[2]">
              <div class="mb-1">Resultado final</div>
              <small>PNG/JPG</small>
            </div>
            <div class="preview" x-show="images[2]">
              <img :src="images[2]">
              <button class="remove" type="button" @click="removeImage(2)">×</button>
            </div>
          </label>
        </div>

        <div class="grid-tiles mt-3" style="grid-template-columns:1fr;">
          {{-- Video --}}
          <label class="tile">
            <input type="file" accept="video/mp4,video/quicktime,video/webm,video/mpeg" name="video" @change="pickVideo($event)">
            <div class="hint" x-show="!videoUrl">
              <div class="mb-1">Recorrido visual / pruebas</div>
              <small>MP4/MOV/WEBM</small>
            </div>
            <div class="preview" x-show="videoUrl">
              <video :src="videoUrl" controls muted playsinline></video>
              <button class="remove" type="button" @click="removeVideo()">×</button>
            </div>
          </label>
        </div>
      </div>
    </div>

    {{-- Checklist profesional de hojalatería --}}
    <div class="card-soft mt-3">
      <div class="hd"><strong>Checklist de hojalatería</strong></div>
      <div class="bd" x-init="initChecklist()">
        <template x-for="(grp, gi) in groups" :key="'g-'+gi">
          <div class="ck-group mb-3">
            <div class="ck-hd">
              <div class="gtitle" x-text="grp.name"></div>
              <div class="d-flex gap-2">
                <button class="btn-ghost btn-sm" type="button" @click="toggleGroup(gi,true)">Marcar todo</button>
                <button class="btn-ghost btn-sm" type="button" @click="toggleGroup(gi,false)">Desmarcar</button>
              </div>
            </div>
            <div class="ck-items">
              <template x-for="(item, ii) in grp.items" :key="'i-'+gi+'-'+ii">
                <div class="ck">
                  <input type="checkbox" x-model="item.done" @change="syncChecklist()">
                  <div>
                    <div class="name" x-text="item.label"></div>
                    <small x-text="item.hint"></small>
                    <div class="note" x-show="item.note!==undefined">
                      <input
                        type="text"
                        class="form-control form-control-sm"
                        placeholder="Nota (opcional)"
                        x-model="item.note"
                        @input="syncChecklist()"
                      >
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </template>
      </div>
    </div>

    <div class="form-actions">
      <a href="{{ $inventarioUrl }}" class="btn-ghost">Cancelar</a>
      <button type="submit" class="btn-primary-soft">Guardar proceso</button>
    </div>
  </form>
</div>

<script>
function HojUI(){
  return {
    images:[null,null,null],
    videoUrl:null,
    groups:[],
    checklistJson:'',

    // 🔹 Lote
    loteIdsJson:'[]',
    aplicarLote:false,
    isLoteMaster:false,

    initLote(){
      try{
        const raw = localStorage.getItem('lote_registros');
        if(!raw) return;

        const ids = JSON.parse(raw);
        const actual = {{ $equipoId ?? 'null' }};

        if(!Array.isArray(ids) || !actual) return;

        // Solo el primer ID del lote puede aplicar el proceso a todos
        if(ids.length && ids[0] === actual){
          this.isLoteMaster = true;
          this.loteIdsJson  = JSON.stringify(ids);
        } else {
          this.isLoteMaster = false;
          this.loteIdsJson  = '[]';
        }
      }catch(e){
        // silencioso
      }
    },

    initChecklist(){
      this.groups = [
        {
          name:'Diagnóstico y preparación',
          items:[
            {label:'Inspección visual completa', hint:'Golpes, abolladuras, fisuras, óxido', done:false, note:''},
            {label:'Registro de piezas afectadas', hint:'Cubiertas, paneles, bastidor, manerales, puertas', done:false, note:''},
            {label:'Desmontaje/aislamiento de electrónica', hint:'Protección de módulos y cableado', done:false, note:''},
          ]
        },
        {
          name:'Estructura y alineación',
          items:[
            {label:'Enderezado/ajuste de bastidor', hint:'Sin deformaciones, escuadras correctas', done:false, note:''},
            {label:'Soldaduras/epóxicos aplicados', hint:'Acabado limpio, sin poros', done:false, note:''},
            {label:'Alineación de puertas/tapas', hint:'Cierran sin fricción, bisagras OK', done:false, note:''},
          ]
        },
        {
          name:'Piezas y herrajes',
          items:[
            {label:'Reemplazo de tornillería/herrajes', hint:'Tornillos, remaches, topes, jaladeras', done:false, note:''},
            {label:'Revisión de ruedas/frenos', hint:'Giro suave, freno efectivo', done:false, note:''},
            {label:'Restitución de protectores y esquineros', hint:'Guardas, bumpers, cantoneras', done:false, note:''},
          ]
        },
        {
          name:'Acabado y pintura',
          items:[
            {label:'Lijado y preparación de superficie', hint:'Desengrase, nivelado de masilla', done:false, note:''},
            {label:'Pintura/recubrimiento aplicado', hint:'Color y textura uniformes', done:false, note:''},
            {label:'Curado y pulido final', hint:'Sin chorreos, sin piel de naranja', done:false, note:''},
          ]
        },
        {
          name:'Ensamble y calidad',
          items:[
            {label:'Re-ensamble de paneles/acrílicos', hint:'Ajuste preciso, sin vibraciones', done:false, note:''},
            {label:'Etiquetas/leyendas reinstaladas', hint:'Indicadores, logotipos, advertencias', done:false, note:''},
            {label:'Prueba de funcionamiento mecánico', hint:'Cajones, brazos, elevación (si aplica)', done:false, note:''},
          ]
        },
        {
          name:'Entrega segura',
          items:[
            {label:'Bordes sin aristas vivas', hint:'Lima y protección en cantos', done:false, note:''},
            {label:'Sin corrosión expuesta', hint:'Sellos en uniones y perforaciones', done:false, note:''},
            {label:'Evidencias cargadas', hint:'Fotos y/o video del resultado final', done:false, note:''},
          ]
        }
      ];
      this.syncChecklist();
    },

    toggleGroup(gi, state){
      this.groups[gi].items.forEach(it => it.done = !!state);
      this.syncChecklist();
    },

    syncChecklist(){
      const data = this.groups.map(g => ({
        name: g.name,
        items: g.items.map(i => ({ label:i.label, done:!!i.done, note:i.note ?? '' }))
      }));
      this.checklistJson = JSON.stringify(data);
    },

    /* Upload handlers */
    pickImage(ev, idx){
      const f = ev.target.files?.[0];
      if(!f){ this.images[idx]=null; return; }
      const rd = new FileReader();
      rd.onload = e => this.images[idx] = e.target.result;
      rd.readAsDataURL(f);
    },
    removeImage(idx){
      const input = document.querySelectorAll('input[type=file][name^="evidencia"]')[idx];
      if(input) input.value = '';
      this.images[idx]=null;
    },
    pickVideo(ev){
      const f = ev.target.files?.[0];
      if(!f){ this.videoUrl=null; return; }
      if(this.videoUrl) URL.revokeObjectURL(this.videoUrl);
      this.videoUrl = URL.createObjectURL(f);
    },
    removeVideo(){
      const input = document.querySelector('input[type=file][name="video"]');
      if(input) input.value = '';
      if(this.videoUrl) URL.revokeObjectURL(this.videoUrl);
      this.videoUrl = null;
    },

    /* Submit con barra de progreso */
    submit(){
      this.syncChecklist();

      // Si no está marcada la opción de aplicar al lote, enviamos lote_ids vacío
      if(!this.aplicarLote){
        this.loteIdsJson = '[]';
      }

      const form = document.getElementById('frmHoj');
      const fd   = new FormData(form);

      Swal.fire({
        title:'Guardando proceso',
        html:`
          <div class="sw-progress-wrap">
            <div class="sw-progress-bg">
              <div id="pb" class="sw-progress-bar"></div>
            </div>
            <div class="sw-progress-text">
              <b id="pct">0%</b> • Subiendo evidencias y checklist…
            </div>
          </div>
        `,
        allowOutsideClick:false,
        showConfirmButton:false,
        showCancelButton:false,
        width:'420px',
        background:'#ffffff',
        customClass:{
          popup:'swal2-elegant',
          title:'swal2-elegant-title',
          htmlContainer:'swal2-elegant-html'
        },
        didOpen: ()=>{
          const xhr=new XMLHttpRequest();
          xhr.open('POST', form.getAttribute('action'), true);
          xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

          xhr.upload.addEventListener('progress', (e)=>{
            if(e.lengthComputable){
              const p = Math.round((e.loaded/e.total)*100);
              const bar = document.getElementById('pb');
              const txt = document.getElementById('pct');
              if(bar) bar.style.width = p+'%';
              if(txt) txt.textContent = p+'%';
            }
          });

          xhr.onload=()=>{
            if(xhr.status===201 || xhr.status===200){
              Swal.fire({
                icon:'success',
                title:'Proceso guardado',
                text:'Proceso de hojalatería guardado correctamente.',
                width:'420px',
                background:'#ffffff',
                customClass:{
                  popup:'swal2-elegant',
                  title:'swal2-elegant-title',
                  htmlContainer:'swal2-elegant-html',
                  confirmButton:'btn-primary-soft'
                },
                confirmButtonText:'Volver al inventario'
              }).then(()=>{ window.location.href = @json($inventarioUrl); });
            }else{
              let msg='Ocurrió un error al guardar.';
              try{
                const r=JSON.parse(xhr.responseText);
                msg = r.message || (r.errors ? Object.values(r.errors).flat().join(' • ') : msg);
              }catch(_){}
              Swal.fire({
                icon:'error',
                title:'Error',
                text:msg,
                width:'420px',
                background:'#ffffff',
                customClass:{
                  popup:'swal2-elegant',
                  title:'swal2-elegant-title',
                  htmlContainer:'swal2-elegant-html',
                  confirmButton:'btn-ghost'
                },
                confirmButtonText:'Cerrar'
              });
            }
          };

          xhr.onerror=()=> {
            Swal.fire({
              icon:'error',
              title:'Error de conexión',
              text:'No se pudo conectar con el servidor.',
              width:'420px',
              background:'#ffffff',
              customClass:{
                popup:'swal2-elegant',
                title:'swal2-elegant-title',
                htmlContainer:'swal2-elegant-html',
                confirmButton:'btn-ghost'
              },
              confirmButtonText:'Cerrar'
            });
          };

          xhr.send(fd);
        }
      });
    }
  };
}
</script>
@endsection
