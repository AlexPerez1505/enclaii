@extends('layouts.app')
@section('title','Paqueterías')
@section('titulo','Paqueterías')

@section('content')
<style>
  :root{
    --bg:#f3f8ff;
    --surface:#ffffff;
    --ink:#0f3b67;
    --muted:#5e7085;
    --stroke:#d9e8ff;
    --ring:#77b6ff;
    --brand:#cfe7ff;
    --brand-ink:#0b4a8f;
    --radius:16px;
    --shadow:0 10px 28px rgba(12, 44, 94, .09);

    /* TAMAÑO XL DE CAMPOS */
    --control-h: 64px;     /* alto del input */
    --control-fs: 18px;    /* tamaño de fuente */
  }

  body{ background:var(--bg); }
  .page{ max-width:1100px; margin:20px auto; padding:0 14px; }

  /* HERO */
  .hero{
    background:linear-gradient(135deg, rgba(207,231,255,.85), rgba(183,218,255,.65));
    border:1px solid var(--stroke); border-radius:var(--radius); box-shadow:var(--shadow);
    padding:18px; margin:14px 0 12px; display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap;
  }
  .hero h2{ margin:0; color:var(--ink); font-weight:800; font-size:clamp(18px,2.2vw,22px); }
  .hero p{ margin:0; color:var(--muted); font-size:13px; }

  .btnx{
    appearance:none; border:1px solid var(--stroke); border-radius:12px; padding:12px 16px;
    background:var(--brand); color:var(--brand-ink); font-weight:800; cursor:pointer;
    box-shadow:var(--shadow); text-decoration:none; display:inline-flex; align-items:center; gap:8px;
    transition:transform .06s ease, filter .2s ease;
  }
  .btnx:active{ transform:translateY(1px); }
  .btnx.ghost{ background:#fff; color:var(--ink); }

  /* LAYOUT */
  .grid{ display:grid; gap:14px; grid-template-columns: 2fr 1fr; }
  @media (max-width: 980px){ .grid{ grid-template-columns: 1fr; } }

  .cardx{ background:var(--surface); border:1px solid var(--stroke); border-radius:var(--radius); box-shadow:var(--shadow); }
  .section{ padding:16px 16px 12px; border-bottom:1px solid var(--stroke); }
  .section:last-child{ border-bottom:0; }
  .section h3{ margin:0 0 6px; color:var(--ink); font-size:16px; font-weight:800; }
  .section p{ margin:0 0 12px; color:var(--muted); font-size:13px; }

  /* GRID DE CAMPOS */
  .fields{ display:grid; gap:14px; grid-template-columns: repeat(12,1fr); }
  .col-12{ grid-column: span 12; }
  .col-6{ grid-column: span 6; }
  .col-3{ grid-column: span 3; }

  /* En tablet/móvil todo a una columna para lectura larga */
  @media (max-width: 1024px){ .col-6{ grid-column:span 12; } }
  @media (max-width: 860px){ .col-3{ grid-column:span 6; } }
  @media (max-width: 520px){ .col-3{ grid-column:span 12; } }

  /* CONTROLES XL (más largos y altos) */
  .form-label{ display:block; font-size:14px; color:var(--ink); font-weight:800; margin:0 0 6px; }
  .control{
    width:100%; background:#fff; color:var(--ink) !important;
    border:1px solid var(--stroke); border-radius:14px;
    height:var(--control-h); padding:16px 16px;       /* padding amplio */
    font-size:var(--control-fs); line-height:1.25; outline:none;
    -webkit-appearance:none; appearance:none;
    transition: box-shadow .2s, border-color .2s, background .2s;
  }
  .control::placeholder{ color:#7b93ac; }
  textarea.control{ height:auto; min-height:140px; resize:vertical; font-size:var(--control-fs); }

  select.control{
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22' viewBox='0 0 24 24'%3E%3Cpath fill='%230b4a8f' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
    background-repeat:no-repeat; background-position:right 12px center; background-size:18px;
    padding-right:44px;
  }
  .control:focus{ border-color:var(--ring); box-shadow:0 0 0 4px rgba(119,182,255,.20); }

  /* KPIs */
  .aside{ position:sticky; top:10px; height:fit-content; }
  .kpis{ display:grid; gap:10px; grid-template-columns: 1fr 1fr; }
  .kpi{ background:#eef6ff; border:1px dashed var(--stroke); border-radius:12px; padding:12px; text-align:center; color:var(--ink); }
  .kpi small{ display:block; color:var(--muted); margin-bottom:4px; }

  /* Savebar móvil */
  .savebar{ display:none; position:fixed; left:0; right:0; bottom:10px; z-index:50; padding:8px 12px; }
  .savebar .inner{ max-width:640px; margin:0 auto; background:#fff; border:1px solid var(--stroke);
                   border-radius:14px; box-shadow:var(--shadow); padding:8px; display:flex; align-items:center; justify-content:space-between; gap:8px; }
  @media (max-width: 980px){ .savebar{ display:block; } }

  .alertx{ background:#fff1f2; color:#9f1239; border:1px solid #fecdd3; padding:10px 12px; border-radius:12px; font-size:14px; }

  /* ===== FIX VISIBILIDAD / AUTOFILL / DATE ===== */
  .control, .control option{ color:var(--ink) !important; background-color:#fff !important; }
  input[type="date"].control{ color-scheme:light; color:var(--ink) !important; }
  input[type="date"].control::-webkit-datetime-edit{ color:var(--ink) !important; }
  .control:-webkit-autofill,
  .control:-webkit-autofill:hover,
  .control:-webkit-autofill:focus{
    -webkit-text-fill-color:var(--ink) !important;
    box-shadow:0 0 0px 1000px #fff inset !important;
    transition:background-color 5000s ease-in-out 0s !important;
  }
</style>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="page" x-data="createEnvio()">
  {{-- HERO --}}
  <div class="hero" style="margin-top:100px">
    <div>
      <h2>Registrar gasto de envío</h2>
      <p>Dimensiones <strong>opcionales</strong>. Calculamos peso volumétrico y facturable si se capturan.</p>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap">
      <a href="{{ route('envios-gastos.index') }}" class="btnx ghost">Historial</a>
      <button type="button" class="btnx" @click="prefillERBE()">Cargar ejemplo ERBE</button>
    </div>
  </div>

  <form id="mainForm" class="grid" method="POST" action="{{ route('envios-gastos.store') }}" @submit="onSubmit">
    @csrf

    {{-- MAIN --}}
    <div class="cardx">
      {{-- Datos generales --}}
      <div class="section">
        <h3>Datos generales</h3>
        <p class="hint">Información base del envío.</p>

        <div class="fields">
          <!-- AHORA MUY LARGOS -->
          <div class="col-12">
            <label class="form-label">Referencia</label>
            <input type="text" name="referencia" x-model="f.referencia" class="control" placeholder="ERBE / descripción corta" autocomplete="off" spellcheck="false">
          </div>

          <div class="col-6">
            <label class="form-label">Sucursal</label>
            <select name="sucursal" x-model="f.sucursal" class="control" required>
              <option value="Reynosa">Reynosa</option>
              <option value="CDMX">CDMX</option>
              <option value="Toluca">Toluca</option>
              <option value="Monterrey">Monterrey</option>
              <option value="Guadalajara">Guadalajara</option>
              <option value="Mérida">Mérida</option>
            </select>
          </div>

          <div class="col-6">
            <label class="form-label">Fecha de envío</label>
            <input type="date" name="fecha_envio" x-model="f.fecha_envio" class="control" required>
          </div>

          <div class="col-12">
            <label class="form-label">Destino (opcional)</label>
            <input type="text" name="destino" x-model="f.destino" class="control" placeholder="Ciudad, Estado" autocomplete="off" spellcheck="false">
          </div>

          <div class="col-12">
            <label class="form-label">Transportista (opcional)</label>
            <input type="text" name="transportista" x-model="f.transportista" class="control" placeholder="DHL / Estafeta / FedEx" autocomplete="off" spellcheck="false">
          </div>
        </div>
      </div>

      {{-- Dimensiones y peso --}}
      <div class="section">
        <h3>Dimensiones y peso (opcionales)</h3>
        <p class="hint">Si no capturas dimensiones, se usará solo el peso real. Divisor volumétrico: <strong>5000</strong> cm³/kg.</p>

        <div class="fields">
          <div class="col-3">
            <label class="form-label">Alto (cm)</label>
            <input type="number" step="0.1" min="0" name="alto_cm" x-model.number="f.alto_cm" @input="recalc()" class="control" inputmode="decimal">
          </div>
          <div class="col-3">
            <label class="form-label">Largo (cm)</label>
            <input type="number" step="0.1" min="0" name="largo_cm" x-model.number="f.largo_cm" @input="recalc()" class="control" inputmode="decimal">
          </div>
          <div class="col-3">
            <label class="form-label">Ancho (cm)</label>
            <input type="number" step="0.1" min="0" name="ancho_cm" x-model.number="f.ancho_cm" @input="recalc()" class="control" inputmode="decimal">
          </div>
          <div class="col-3">
            <label class="form-label">Peso real (kg)</label>
            <input type="number" step="0.01" min="0" name="peso_kg" x-model.number="f.peso_kg" @input="recalc()" class="control" inputmode="decimal">
          </div>
        </div>
      </div>

      {{-- Costo --}}
      <div class="section">
        <h3>Costo</h3>
        <p class="hint">Registra el costo total del envío (lo que pagaste).</p>

        <div class="fields">
          <div class="col-6">
            <label class="form-label">Costo MXN</label>
            <input type="number" step="0.01" min="0" name="costo_mxn" x-model.number="f.costo_mxn" class="control" required inputmode="decimal">
          </div>
          <div class="col-12">
            <label class="form-label">Notas</label>
            <textarea name="notas" x-model="f.notas" class="control" placeholder="Observaciones, guía, incidencias, etc."></textarea>
          </div>
        </div>
      </div>

      {{-- Hidden calc para BD --}}
      <input type="hidden" name="peso_volumetrico_kg" :value="f.peso_vol">
      <input type="hidden" name="peso_facturable_kg"  :value="f.peso_fact">

      @if ($errors->any())
        <div class="section">
          <div class="alertx"><strong>Corrige:</strong> {{ implode(', ', $errors->all()) }}</div>
        </div>
      @endif

      {{-- Acciones --}}
      <div class="section" style="display:flex; gap:8px; justify-content:flex-end">
        <a href="{{ route('envios-gastos.index') }}" class="btnx ghost">Cancelar</a>
        <button class="btnx" type="submit">Guardar</button>
      </div>
    </div>

    {{-- ASIDE --}}
    <div class="cardx aside" style="padding:16px">
      <h3 style="margin:0 0 10px; color:var(--ink)">Resumen</h3>
      <div class="kpis">
        <div class="kpi"><small>Volumétrico</small><strong x-text="fmtKg(f.peso_vol)"></strong></div>
        <div class="kpi"><small>Facturable</small><strong x-text="fmtKg(f.peso_fact)"></strong></div>
        <div class="kpi"><small>Dimensiones</small><strong x-text="dimsOk ? 'Usadas' : '—'"></strong></div>
        <div class="kpi"><small>Costo</small><strong x-text="money(f.costo_mxn)"></strong></div>
      </div>
      <div class="hint" style="margin-top:10px">Tip: “Cargar ejemplo ERBE” (138×75×80 cm, 65 kg).</div>
    </div>
  </form>
</div>

{{-- Savebar móvil --}}
<div class="savebar" aria-hidden="true">
  <div class="inner">
    <div class="hint"><strong x-text="money(f.costo_mxn)"></strong> • Facturable <strong x-text="fmtKg(f.peso_fact)"></strong></div>
    <div style="display:flex; gap:8px">
      <a href="{{ route('envios-gastos.index') }}" class="btnx ghost">Cancelar</a>
      <button class="btnx" type="button" onclick="document.getElementById('mainForm').requestSubmit()">Guardar</button>
    </div>
  </div>
</div>

<script>
function createEnvio(){
  return {
    divisor:5000,
    f:{
      referencia:'',
      sucursal:'Reynosa',
      fecha_envio: new Date().toISOString().slice(0,10),
      destino:'',
      transportista:'',
      alto_cm:null, largo_cm:null, ancho_cm:null, peso_kg:null,
      peso_vol:null, peso_fact:null,
      costo_mxn:null, notas:''
    },
    get dimsOk(){ return [this.f.alto_cm,this.f.largo_cm,this.f.ancho_cm].every(v => v && Number(v)>0); },
    fmtKg(v){ return (v ? Number(v).toFixed(2) : '0.00') + ' kg'; },
    money(v){ return Number(v||0).toLocaleString('es-MX',{style:'currency',currency:'MXN'}); },
    recalc(){
      const a=Number(this.f.alto_cm||0), l=Number(this.f.largo_cm||0), an=Number(this.f.ancho_cm||0), r=Number(this.f.peso_kg||0);
      const vol = (a>0 && l>0 && an>0) ? (a*l*an)/this.divisor : 0;
      this.f.peso_vol  = (a>0 && l>0 && an>0) ? Number(vol.toFixed(2)) : null;
      this.f.peso_fact = (r>0 || vol>0) ? Number(Math.max(r, vol).toFixed(2)) : null;
    },
    prefillERBE(){
      this.f.referencia='ERBE';
      this.f.sucursal='Reynosa';
      this.f.destino='REYNOSA'; this.f.transportista='ESTAFETA';
      this.f.alto_cm=138; this.f.largo_cm=75; this.f.ancho_cm=80; this.f.peso_kg=65;
      this.f.costo_mxn=4766.80; this.f.notas='65 kg aprox. 17 de septiembre • Sucursal Reynosa';
      this.recalc();
    },
    onSubmit(){ /* UX ligera */ }
  }
}
</script>
@endsection
