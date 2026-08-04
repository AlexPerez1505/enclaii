@extends('layouts.app')  
@section('title', 'Guías')
@section('titulo', 'Guías')
@section('content')

<style>
:root{ --mint:#48cfad; --mint-dark:#34c29e; --ink:#2a2e35; --muted:#7a7f87; --line:#e9ecef; --card:#ffffff; }
*{box-sizing:border-box}
body{font-family:"Open Sans",sans-serif;background:#eaebec}

/* Shell */
.wrap{ max-width:1180px; margin:110px auto 40px; padding:0 16px; }
.header{ display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:14px; }
.header h2{ margin:0; font-weight:800; color:var(--ink); }
.header p{ margin:4px 0 0; color:var(--muted); font-size:14px }
.back-link{ display:inline-flex; align-items:center; gap:8px; color:var(--muted); text-decoration:none;
  padding:8px 12px; border-radius:10px; border:1px solid var(--line); background:#fff; }
.back-link:hover{ color:var(--ink); border-color:#dfe3e8 }

/* 2 paneles (desktop) / apilado (mobile) */
.grid-panels{ display:grid; gap:18px; grid-template-columns:1fr; }
@media(min-width:980px){ .grid-panels{ grid-template-columns: 0.9fr 1.1fr; } }

.panel{ background:var(--card); border-radius:16px; box-shadow:0 16px 40px rgba(18,38,63,.12); border:1px solid var(--line); overflow:hidden; }
.panel-head{
  padding:20px 22px; border-bottom:1px solid var(--line);
  display:flex; justify-content:space-between; align-items:center; gap:12px;
}
.panel-head h3{ margin:0; color:var(--ink); display:flex; align-items:center; gap:10px; }
.section{ padding:22px; }

/* Badge total */
.badge{ background:#e8fff9; color:#0f5c49; border:1px solid #b8f0e1; padding:6px 10px; border-radius:999px; font-weight:800; font-size:.85rem; }

/* Right sticky list (solo desktop) */
@media(min-width:980px){ .panel--sticky .section{ position:sticky; top:92px; } }

/* Inputs + labels flotantes */
.field{ position:relative; background:#fff; border:1px solid var(--line); border-radius:12px; padding:16px 14px 10px; transition:box-shadow .2s, border-color .2s; }
.field:focus-within{ border-color:#d8dee6; box-shadow:0 8px 24px rgba(18,38,63,.08) }
.field input{ width:100%; border:0; outline:0; background:transparent; font-size:15px; color:var(--ink); padding-top:10px; }
.field label{ position:absolute; left:14px; top:14px; color:var(--muted); font-size:13px; pointer-events:none; transition:transform .15s, color .15s, font-size .15s, top .15s; }
.field input::placeholder{ color:transparent; }
.field input:focus + label, .field input:not(:placeholder-shown) + label{ top:8px; transform:translateY(-10px); font-size:11px; color:var(--mint-dark); }

/* Grid form */
.grid{ display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
@media(max-width:900px){ .grid{ grid-template-columns:1fr } }

/* List */
.list{ display:grid; gap:10px; max-height:520px; overflow:auto; }
.item{ display:flex; align-items:center; justify-content:space-between; gap:12px; background:#fff; border:1px solid var(--line); border-radius:12px; padding:10px 12px; }
.item:hover{ border-color:#dfe3e8; background:#f9fffd }
.id{ font-weight:800; color:#11695a; letter-spacing:.2px }
.meta{ color:var(--muted); font-size:.92rem }

/* Toolbar (desktop base) */
.toolbar{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-left:auto; }
.search{ position:relative; flex:1 1 220px; min-width:220px; }
.search input{ width:100%; border:1px solid var(--line); border-radius:12px; padding:12px 42px 12px 38px; }
.search .icon{ position:absolute; left:12px; top:50%; transform:translateY(-50%); opacity:.6; color:var(--muted); }
.search .icon-right{ position:absolute; right:12px; top:50%; transform:translateY(-50%); opacity:.45; color:var(--muted); cursor:pointer; }

/* Buttons */
.btn{ border:0; border-radius:12px; padding:12px 18px; font-weight:700; cursor:pointer; transition:transform .05s, box-shadow .2s, background .2s, color .2s; }
.btn:active{ transform:translateY(1px) }
.btn-primary{ background:var(--mint); color:#fff; box-shadow:0 12px 22px rgba(72,207,173,.26) }
.btn-primary:hover{ background:var(--mint-dark) }
.btn-ghost{ background:#fff; color:#2a2e35; border:1px solid var(--line) }
.btn-ghost:hover{ border-color:#dfe3e8 }
.btn-outline{ background:#fff; color:#11695a; border:1px dashed rgba(72,207,173,.6) }

/* Toast + skeleton */
.toast{ position:fixed; top:18px; right:18px; z-index:99; padding:12px 14px; border-radius:12px; font-weight:700; background:#f0fffb; border:1px solid #def7ee; color:#145447; box-shadow:0 10px 22px rgba(18,38,63,.08); display:none; }
.skeleton{ display:grid; gap:10px }
.shimmer{ height:54px; border-radius:12px; background:linear-gradient(90deg,#eef3f1 25%,#f7fbfa 37%,#eef3f1 63%); background-size:400% 100%; animation:shimmer 1.2s infinite linear; border:1px solid var(--line); }
@keyframes shimmer{ 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Resumen por kilo */
.pills{ display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px; }
.pill{ background:#f5fffc; border:1px dashed rgba(72,207,173,.45); color:#0b3b26; padding:6px 10px; border-radius:999px; font-weight:800; font-size:.85rem; }

/* ======== FIX MÓVIL: nada se corta, input y botón perfectos ======== */
@media (max-width: 720px){
  /* fuerza layout vertical del encabezado */
  .panel-head{
    display:grid !important;
    grid-template-columns: 1fr !important;
    grid-auto-rows: auto;
    row-gap: 10px;
    align-items: stretch !important;
    justify-content: stretch !important;
  }
  .panel-head *{ min-width:0; } /* evita recortes por width implícitas */

  .panel-head h3{
    width: 100% !important;
    display:flex; flex-wrap:wrap; gap:8px;
    line-height:1.2;
    order:1;
  }

  /* barra: input ocupa todo, botón a la derecha */
  .panel-head .toolbar{
    order:2;
    margin-left:0 !important;
    width:100% !important;
    display:grid !important;
    grid-template-columns: 1fr 128px !important; /* botón ancho fijo legible */
    gap:10px;
  }

  .search{ min-width:0 !important; }
  .search input{
    width:100%;
    height:44px;
    padding:10px 40px;   /* espacio para íconos */
    font-size:15px;
  }
  .search .icon, .search .icon-right{ width:18px; height:18px; }
  #btnRefresh{ height:44px; white-space:nowrap; }
  .badge{ font-size:.8rem; padding:4px 8px; }
}

/* Teléfonos muy angostos */
@media (max-width: 420px){
  .panel-head .toolbar{
    grid-template-columns: 1fr !important; /* botón debajo */
  }
  #btnRefresh{ width:100%; }
}

/* Laravel errors */
.is-invalid{ border-color:#f9c0c0 !important } .error{ color:#cc4b4b; font-size:12px; margin-top:6px }
</style>

<div class="wrap" style="margin-top:-5px;">
  <div class="header">
    <div>
      <h2>Guías y Entregas</h2>
      <p>Crea guías y entrégalas en tiempo real. Diseño mint, limpio y rápido.</p>
    </div>
    <a href="{{ url()->previous() }}" class="back-link">⟵ Volver</a>
  </div>

  <div class="grid-panels">
    {{-- ===== Panel izquierdo: Crear guía ===== --}}
    <div class="panel">
      <div class="panel-head"><h3>Nueva guía</h3></div>
      <div class="section">
        <div class="grid">
          <div>
            <div class="field @error('numero_rastreo') is-invalid @enderror">
              <input type="text" id="f-rastreo" placeholder=" " inputmode="numeric" autocomplete="off">
              <label for="f-rastreo">Número de rastreo</label>
            </div>
            @error('numero_rastreo')<div class="error">{{ $message }}</div>@enderror
          </div>
          <div>
            <div class="field @error('peso') is-invalid @enderror">
              <input type="number" step="0.01" id="f-peso" placeholder=" ">
              <label for="f-peso">Peso (kg)</label>
            </div>
            @error('peso')<div class="error">{{ $message }}</div>@enderror
          </div>
          <div>
            <div class="field @error('fecha_recepcion') is-invalid @enderror">
              <input type="date" id="f-fecha" placeholder=" ">
              <label for="f-fecha">Fecha de recepción</label>
            </div>
            @error('fecha_recepcion')<div class="error">{{ $message }}</div>@enderror
          </div>
          <div style="display:flex; align-items:end; gap:12px">
            <button class="btn btn-primary" id="btnCrearGuia">
              <span id="spinCrear" class="spinner" style="display:none"></span>
              Guardar guía
            </button>
            <button class="btn btn-ghost" type="button" id="btnReset">Limpiar</button>
          </div>
        </div>
      </div>
    </div>

    {{-- ===== Panel derecho: Guías sin entregar ===== --}}
    <div class="panel panel--sticky">
      <div class="panel-head">
        <h3>
          Guías sin entregar
          <span id="totalBadge" class="badge" aria-label="Total pendientes">0 pendientes</span>
        </h3>
        <div class="toolbar">
          <div class="search">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true">
              <circle cx="11" cy="11" r="8"></circle>
              <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input id="searchTxt" placeholder="Buscar por rastreo…">
            <svg xmlns="http://www.w3.org/2000/svg" id="clearSearch" class="icon-right" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="display:none" aria-hidden="true">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
          </div>
          <button class="btn btn-ghost" id="btnRefresh">Actualizar</button>
        </div>
      </div>
      <div class="section">
        {{-- Resumen por kilo --}}
        <div id="resumeKilos" class="pills"></div>

        <div id="skeleton" class="skeleton" style="display:none">
          <div class="shimmer"></div><div class="shimmer"></div><div class="shimmer"></div>
        </div>

        <div class="list" id="listGuias"></div>
        <div id="emptyState" class="meta" style="text-align:center; display:none">No hay guías pendientes.</div>

        <div style="display:flex; justify-content:center; margin-top:12px;">
          <button id="btnMore" class="btn btn-outline">Cargar más</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="toast" class="toast"></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const SwalMint = Swal.mixin({ customClass:{ popup:'swal2-rounded', confirmButton:'swal2-confirm', cancelButton:'swal2-cancel' }, buttonsStyling:false });

  const csrf='{{ csrf_token() }}';
  const routes={
    crearGuia:'{{ route('guias.store') }}',
    search:'{{ route('guias.search') }}',
    disponibles:'{{ route('guias.disponibles') }}',
    resumen:'{{ route('guias.resumen') }}'
  };
  const entregaBase='{{ route('entrega.create') }}';

  const $=(q,r=document)=>r.querySelector(q);
  const list=$('#listGuias'), toast=$('#toast'), skel=$('#skeleton'), empty=$('#emptyState'), totalBadge=$('#totalBadge');

  function showToast(msg, ok=true){
    toast.textContent=msg; toast.style.display='block';
    toast.style.background= ok ? '#f0fffb' : '#fff5f5';
    toast.style.borderColor= ok ? '#def7ee' : '#ffd7d7';
    toast.style.color= ok ? '#145447' : '#7a2e2e';
    clearTimeout(showToast._t); showToast._t=setTimeout(()=>toast.style.display='none',2400);
  }
  function setLoading(on){ skel.style.display=on?'grid':'none'; }

  const MAX_DIGITS = 12;
  function formatDateDMY(dateStr){ if(!dateStr) return '—'; const [y,m,d]=String(dateStr).split('-'); return (y&&m&&d)?`${d.padStart(2,'0')}/${m.padStart(2,'0')}/${y}`:'—'; }
  function prettyRastreo(n){ return String(n).replace(/(\d{4})(?=\d)/g,'$1 '); }

  // Máscara rastreo
  const rastreoEl=document.getElementById('f-rastreo');
  const maskRastreo=v=>v.replace(/\D/g,'').slice(0,MAX_DIGITS).replace(/(.{4})/g,'$1 ').trim();
  rastreoEl.maxLength = MAX_DIGITS + Math.floor((MAX_DIGITS - 1)/4);
  rastreoEl.addEventListener('input', e=> e.target.value=maskRastreo(e.target.value));
  rastreoEl.addEventListener('keydown', e=>{
    const sel=e.target.selectionEnd - e.target.selectionStart > 0;
    const len=e.target.value.replace(/\D/g,'').length;
    if(!sel && len>=MAX_DIGITS && /[0-9]/.test(e.key)) e.preventDefault();
  });
  rastreoEl.addEventListener('paste', e=>{
    e.preventDefault(); const txt=(e.clipboardData||window.clipboardData).getData('text'); rastreoEl.value=maskRastreo(txt);
  });

  // Lista paginada
  let page=1, hasNext=true, lastSearch='';
  async function fetchGuias({reset=false}={}){
    try{
      if(reset){ list.innerHTML=''; page=1; hasNext=true; empty.style.display='none'; }
      if(!hasNext) return;
      setLoading(true);

      const url=new URL(routes.search, window.location.origin);
      url.searchParams.set('page',page);
      if(lastSearch.trim()) url.searchParams.set('search',lastSearch.trim());

      const res=await fetch(url,{headers:{'X-Requested-With':'XMLHttpRequest'}});
      const data=await res.json();

      if(reset && (!data.data || data.data.length===0)){ empty.style.display='block'; }

      (data.data||[]).forEach(g=>{
        const rec = g.fecha_recepcion ? formatDateDMY(g.fecha_recepcion)
                  : (g.created_at ? formatDateDMY(String(g.created_at).slice(0,10)) : '—');
        const peso = (g.peso!=null && g.peso!=='') ? Number(g.peso).toFixed(2) : '0.00';

        const el=document.createElement('div');
        el.className='item';
        el.innerHTML=`
          <div>
            <div class="id"># ${prettyRastreo(g.numero_rastreo)}</div>
            <div class="meta">Peso: ${peso} kg · Recibida: ${rec}</div>
          </div>
          <a class="btn btn-outline"
             href="${entregaBase}?guia_id=${g.id}&rastreo=${encodeURIComponent(g.numero_rastreo)}&peso=${encodeURIComponent(g.peso ?? '')}">
            Entregar
          </a>`;
        list.appendChild(el);
      });

      if(data.next_page_url){ page++; hasNext=true; } else { hasNext=false; }
      document.getElementById('btnMore').disabled = !hasNext;
    }catch(e){ console.error(e); showToast('Error al cargar guías',false); }
    finally{ setLoading(false); }
  }

  // Resumen (TOTAL + categorías)
  async function fetchResumen(){
    const cont = document.getElementById('resumeKilos');
    try{
      const res = await fetch(routes.resumen, { headers:{'X-Requested-With':'XMLHttpRequest'} });
      const { total=0, byWeight=[] } = await res.json();

      totalBadge.textContent = `${total} pendiente${total===1?'':'s'}`;

      if(!Array.isArray(byWeight) || byWeight.length===0){
        cont.style.display='none';
        return;
      }

      cont.innerHTML = byWeight.map(({kg, c})=>{
        const val = parseFloat(kg);
        const label = Number.isInteger(val) ? `${val} kg` : `${val.toFixed(2)} kg`;
        return `<span class="pill">${c} guía${c>1?'s':''} sin entregar de ${label}</span>`;
      }).join('');
    }catch(e){
      console.error(e);
      cont.style.display='none';
    }
  }

  // Buscar con debounce + clear
  let t=null;
  const searchInput = document.getElementById('searchTxt');
  const clearBtn = document.getElementById('clearSearch');

  searchInput.addEventListener('input', e=>{
    clearBtn.style.display = e.target.value ? 'block':'none';
    clearTimeout(t);
    t=setTimeout(()=>{ lastSearch=e.target.value; fetchGuias({reset:true}); },320);
  });
  clearBtn.addEventListener('click', ()=>{
    searchInput.value=''; lastSearch=''; clearBtn.style.display='none'; fetchGuias({reset:true});
  });

  // Crear guía (AJAX)
  const btnCrear=document.getElementById('btnCrearGuia');
  const spinCrear=document.getElementById('spinCrear');
  document.getElementById('btnReset').addEventListener('click',()=>{ document.getElementById('f-rastreo').value=''; document.getElementById('f-peso').value=''; document.getElementById('f-fecha').value=''; });
  const savingCrear = on=>{ btnCrear.disabled=on; spinCrear.style.display=on?'inline-block':'none'; };

  btnCrear.addEventListener('click',async ()=>{
    const rawRastreo = document.getElementById('f-rastreo').value.replace(/\s+/g,'');
    if(rawRastreo.length !== MAX_DIGITS){
      SwalMint.fire({icon:'warning', title:'Número de rastreo', text:`Debe tener exactamente ${MAX_DIGITS} dígitos.`});
      return;
    }
    const fd=new FormData();
    fd.append('numero_rastreo', rawRastreo);
    fd.append('peso',document.getElementById('f-peso').value);
    fd.append('fecha_recepcion',document.getElementById('f-fecha').value);
    try{
      savingCrear(true);
      const res=await fetch(routes.crearGuia,{method:'POST',headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'},body:fd});
      const data=await res.json();
      if(data.ok){
        document.getElementById('btnReset').click();
        showToast(data.message||'Guía creada');
        fetchGuias({reset:true});
        fetchResumen();
      }else{
        SwalMint.fire({icon:'warning', title:'Revisa los datos', text:data.message||'Campos inválidos'});
      }
    }catch(e){ SwalMint.fire({icon:'error', title:'Error', text:'No se pudo guardar la guía'}); }
    finally{ savingCrear(false); }
  });

  // Refresh / More
  document.getElementById('btnRefresh').addEventListener('click', ()=> { fetchGuias({reset:true}); fetchResumen(); });
  document.getElementById('btnMore').addEventListener('click', ()=> fetchGuias());

  // Init
  fetchGuias({reset:true});
  fetchResumen();
</script>
@endsection
