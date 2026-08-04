@extends('layouts.app')
@section('title','Ficha de equipo')
@section('titulo','Detalle')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@php
  $router = app('router');

  $estadoActual = $registro->estado_proceso ?? 'registro';

  $fechaUltimoEstado = null;
  try {
    if ($estadoActual === 'registro') {
      $fechaUltimoEstado = $registro->created_at;
    } else {
      $fechaUltimoEstado = optional(
        $registro->relationLoaded('procesos')
          ? $registro->procesos->where('tipo_proceso',$estadoActual)->sortByDesc('created_at')->first()
          : $registro->procesos()->where('tipo_proceso',$estadoActual)->latest('created_at')->first()
      )->created_at;
    }
  } catch (\Throwable $e) {
    $fechaUltimoEstado = null;
  }

  $badgeClass = match($estadoActual){
    'hojalateria'    => 'badge-hojalateria',
    'mantenimiento'  => 'badge-mantenimiento',
    'stock'          => 'badge-stock',
    'vendido'        => 'badge-vendido',
    'defectuoso'     => 'badge-defectuoso',
    default          => 'badge-registro'
  };

  $barcodeUrl = $router->has('registros.imprimir-barcode')
    ? route('registros.imprimir-barcode', $registro->id)
    : '#';

  $pasos   = $pasos ?? ['hojalateria','mantenimiento','stock','vendido'];
  $bonitos = [
    'hojalateria'   => 'Hojalatería',
    'mantenimiento' => 'Mantenimiento',
    'stock'         => 'Stock',
    'vendido'       => 'Vendido'
  ];

  $routes = [];
  foreach($pasos as $p){
    $routes[$p] = $router->has('proceso.'.$p)
      ? route('proceso.'.$p, $registro->id)
      : null;
  }

  if (!isset($componentes)) {
    $componentes = \Illuminate\Support\Facades\DB::table('inv_registro_componentes')
      ->leftJoin('inv_componentes_cat','inv_componentes_cat.id','=','inv_registro_componentes.componente_id')
      ->where('inv_registro_componentes.registro_id',$registro->id)
      ->select([
        'inv_registro_componentes.id',
        'inv_componentes_cat.id as componente_id',
        'inv_registro_componentes.nombre_cache as nombre',
        'inv_registro_componentes.cantidad',
        'inv_registro_componentes.incluido',
        'inv_registro_componentes.notas',
      ])
      ->orderBy('inv_registro_componentes.nombre_cache')
      ->get();
  }

  $editUrl = $router->has('registros.edit')
    ? route('registros.edit',$registro->id)
    : ($router->has('inventario.editar')
        ? route('inventario.editar',$registro->id)
        : url('/registros/'.$registro->id.'/edit'));

  $deleteAction = $router->has('registros.destroy')
    ? route('registros.destroy',$registro->id)
    : ($router->has('registros.eliminar')
        ? route('registros.eliminar',$registro->id)
        : url('/registros/'.$registro->id));

  $editPinValidateUrl = $router->has('registros.validar-pin-edicion')
    ? route('registros.validar-pin-edicion', $registro->id)
    : ($router->has('inventario.validar-pin-edicion')
        ? route('inventario.validar-pin-edicion', $registro->id)
        : null);

  $delProcesoUrlTpl = $router->has('procesos.eliminar')
    ? route('procesos.eliminar', ['registro'=>$registro->id, 'proceso'=>0])
    : url('/registros/'.$registro->id.'/procesos/0');

  $cambiarEstadoUrl = $router->has('registros.cambiar-estado')
    ? route('registros.cambiar-estado', $registro->id)
    : url('/registros/'.$registro->id.'/cambiar-estado');

  $indexUrl = $router->has('registros.index')
    ? route('registros.index')
    : ($router->has('inventario.index')
        ? route('inventario.index')
        : url('/inventario'));

  $stepperCfg = [
    'pasoKeys' => array_values($pasos),
    'labels'   => array_map(fn($p)=>$bonitos[$p] ?? ucfirst($p), $pasos),
    'routes'   => $routes,
  ];
@endphp

<style>
  .gallery-modal{
  position:relative;
  display:flex;
  align-items:center;
  justify-content:center;
  min-height:420px;
}

.gallery-arrow{
  position:absolute;
  top:50%;
  transform:translateY(-50%);
  width:48px;
  height:48px;
  border:none;
  border-radius:999px;
  background:rgba(15,23,42,.75);
  color:#fff;
  display:grid;
  place-items:center;
  z-index:20;
  cursor:pointer;
  transition:.2s ease;
}

.gallery-arrow:hover{
  background:#1d4ed8;
}

.gallery-arrow.left{
  left:12px;
}

.gallery-arrow.right{
  right:12px;
}

.gallery-arrow i{
  font-size:1.5rem;
}

.gallery-counter{
  position:absolute;
  bottom:14px;
  left:50%;
  transform:translateX(-50%);
  background:rgba(15,23,42,.75);
  color:#fff;
  padding:6px 12px;
  border-radius:999px;
  font-size:.85rem;
  font-weight:700;
}
:root{
  --bg:#eaebec;
  --panel:#ffffff;
  --ink:#0f172a;
  --muted:#667085;
  --line:#e7eaf0;
  --pblue:#dbeafe;
  --pblue-700:#1d4ed8;
  --shadow:0 12px 36px rgba(2,6,23,.09);
  --radius:20px;
}
*,*::before,*::after{
  box-sizing:border-box;
}
body{
  background:var(--bg);
  color:var(--ink);
  font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
}
[x-cloak]{
  display:none !important;
}

.hero{
  margin-top:110px;
  background:
    radial-gradient(1200px 150px at 0% 0%, rgba(96,165,250,.18), transparent 40%),
    radial-gradient(1200px 150px at 100% 0%, rgba(14,165,233,.14), transparent 40%),
    #fff;
  border:1px solid var(--line);
  border-radius:18px;
  padding:16px 18px;
  box-shadow:var(--shadow);
}
.hero h1{
  margin:0;
  font-weight:800;
  letter-spacing:-.02em;
}
.hero .subtle{
  color:var(--muted);
}
.hero-actions{
  display:flex;
  align-items:center;
  gap:10px;
  flex-wrap:wrap;
  margin-top:10px;
}
.btn{
  display:inline-flex;
  align-items:center;
  gap:.45rem;
  padding:10px 14px;
  border-radius:14px;
  border:1px solid var(--line);
  background:#fff;
  color:#334155;
  font-weight:800;
  text-decoration:none;
  cursor:pointer;
  transition:transform .04s ease, box-shadow .2s ease, background .2s ease;
}
.btn:active{
  transform:translateY(1px);
}
.btn-blue{
  background:var(--pblue);
  color:#0b2a4a;
  border-color:rgba(96,165,250,.45);
}
.btn-ghost{
  background:#fff;
  color:#334155;
}
.btn-danger-soft{
  background:#fee2e2;
  color:#7f1d1d;
  border-color:#fecaca;
}
.btn-danger-soft:hover{
  background:#fde0e0;
}

.badge-state{
  display:inline-flex;
  align-items:center;
  gap:.35rem;
  padding:6px 10px;
  border-radius:999px;
  font-weight:800;
  font-size:12px;
  letter-spacing:.1px;
  border:1px solid transparent;
}
.badge-registro{
  background:#f1f5f9;
  color:#334155;
  border-color:#e2e8f0;
}
.badge-hojalateria{
  background:#e0f2fe;
  color:#1d4ed8;
  border-color:#bfdbfe;
}
.badge-mantenimiento{
  background:#fef9c3;
  color:#eab308;
  border-color:#fde68a;
}
.badge-stock{
  background:#dcfce7;
  color:#065f46;
  border-color:#bbf7d0;
}
.badge-vendido{
  background:#ffe4e6;
  color:#9f1239;
  border-color:#fecdd3;
}
.badge-defectuoso{
  background:#ffedd5;
  color:#c2410c;
  border-color:#fdba74;
}

.text-muted-ux{
  color:var(--muted);
}

.card-soft{
  background:#fff;
  border:1px solid var(--line);
  border-radius:var(--radius);
  box-shadow:var(--shadow);
}
.card-soft .head{
  padding:16px 18px;
  border-bottom:1px solid var(--line);
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:12px;
}
.card-soft .body{
  padding:18px;
}

.thumb{
  width:64px;
  height:64px;
  border:1px solid var(--line);
  border-radius:12px;
  overflow:hidden;
  display:grid;
  place-items:center;
  background:#f8fafc;
  cursor:pointer;
  transition:transform .12s ease, box-shadow .2s ease;
}
.thumb:hover{
  transform:translateY(-1px);
  box-shadow:0 12px 24px rgba(2,6,23,.08);
}
.thumb img,
.thumb video{
  width:100%;
  height:100%;
  object-fit:cover;
}
.thumb{
  position:relative;
  width:90px;
  height:90px;
  overflow:hidden;
  border-radius:14px;
  border:1px solid #dbe2ea;
  background:#f8fafc;
  cursor:pointer;
}

.thumb img,
.thumb video{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
}

.thumb-play{
  position:absolute;
  inset:0;
  display:flex;
  align-items:center;
  justify-content:center;
  background:rgba(0,0,0,.25);
}

.thumb-play i{
  color:white;
  font-size:2rem;
}

.gallery-modal img,
.gallery-modal video{
  width:100%;
  max-height:80vh;
  object-fit:contain;
  border-radius:12px;
  background:#000;
}

.kpis{
  display:grid;
  grid-template-columns:repeat(2,minmax(0,1fr));
  gap:12px;
}
.kpi{
  background:#f8fafc;
  border:1px solid var(--line);
  border-radius:14px;
  padding:12px;
  min-width:0;
}
.kpi .label{
  color:#6b7280;
  font-size:12px;
  overflow-wrap:anywhere;
  word-break:break-word;
}
.kpi .val{
  font-weight:800;
  white-space:normal;
  overflow-wrap:anywhere;
  word-break:break-word;
}

.comp-table{
  width:100%;
  border-collapse:separate;
  border-spacing:0;
}
.comp-table th,
.comp-table td{
  padding:10px 12px;
  font-size:14px;
  border-bottom:1px solid var(--line);
}
.comp-table thead th{
  color:#6b7280;
  background:#f5f7ff;
  font-weight:700;
}
.comp-table .qty{
  font-weight:800;
}
.comp-empty{
  color:var(--muted);
}

.stepper{
  display:flex;
  gap:10px;
  overflow-x:auto;
  padding:8px;
  -webkit-overflow-scrolling:touch;
}
.stepper::-webkit-scrollbar{
  height:8px;
}
.stepper::-webkit-scrollbar-thumb{
  background:#e6ecff;
  border-radius:999px;
}
.step-pill{
  flex:0 0 auto;
  border:1px solid var(--line);
  background:#fff;
  color:#334155;
  border-radius:999px;
  padding:8px 12px;
  font-weight:800;
  display:inline-flex;
  align-items:center;
  gap:8px;
  cursor:pointer;
  transition:background .2s ease, border-color .2s ease, transform .04s ease;
}
.step-pill.active{
  background:#eef3ff;
  border-color:#cfe0ff;
  color:#193259;
}
.step-pill .dot{
  width:10px;
  height:10px;
  border-radius:999px;
  background:#a3bffa;
}
.step-pill.done .dot{
  background:#22c55e;
}

.panel{
  border:1px solid var(--line);
  background:#fff;
  border-radius:16px;
  padding:14px;
}
.panel + .panel{
  margin-top:10px;
}

.swal2-popup.media{
  padding:0;
  border-radius:16px;
  overflow:hidden;
  box-shadow:0 24px 60px rgba(2,6,23,.25);
}
.media-wrap{
  background:#fff;
  padding:10px;
}
.media-wrap img,
.media-wrap video{
  width:100%;
  max-height:80vh;
  object-fit:contain;
  border-radius:12px;
}

.fab{
  position:fixed;
  right:16px;
  bottom:18px;
  z-index:1100;
  display:none;
}
@media (max-width:576px){
  .fab{
    display:block;
  }
}
.fab-btn{
  width:56px;
  height:56px;
  border-radius:999px;
  border:1px solid #e6ebf5;
  background:#ffffff;
  display:grid;
  place-items:center;
  box-shadow:0 12px 28px rgba(2,6,23,.18);
  transition:transform .06s ease, box-shadow .2s ease, background .2s ease;
}
.fab-btn:active{
  transform:translateY(1px);
}
.fab-btn i{
  font-size:22px;
  color:#0f172a;
  opacity:.8;
}

body.sheet-open{
  overflow:hidden;
}
body.sheet-open .fab{
  display:none !important;
}

.sheet-backdrop{
  position:fixed;
  inset:0;
  background:rgba(15,23,42,.35);
  backdrop-filter: blur(6px);
  opacity:0;
  pointer-events:none;
  transition:.2s;
  z-index:1098;
}
.sheet-backdrop.show{
  opacity:1;
  pointer-events:auto;
}
.sheet{
  position:fixed;
  left:0;
  right:0;
  bottom:-100%;
  z-index:1099;
  background:#fff;
  border-radius:18px 18px 0 0;
  box-shadow:0 -20px 40px rgba(2,6,23,.16);
  padding:16px;
  transition:bottom .28s ease;
  will-change:bottom;
}
.sheet.show{
  bottom:0;
}
.sheet .grab{
  width:56px;
  height:6px;
  background:#e5e7eb;
  border-radius:999px;
  margin:6px auto 14px;
}
.sheet .title{
  font-weight:800;
  margin-bottom:12px;
}
.sheet .grid{
  display:grid;
  gap:10px;
}
.sheet .row2{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:10px;
}
.action{
  display:flex;
  align-items:center;
  gap:12px;
  padding:14px;
  border-radius:14px;
  border:1px solid var(--line);
  background:#fff;
  text-decoration:none;
  font-weight:800;
  color:#111827;
  width:100%;
}
.action .ico{
  width:38px;
  height:38px;
  border-radius:10px;
  display:grid;
  place-items:center;
  background:#f8fafc;
  color:#0f172a;
}

:root{
  --m-bg:#ffffff;
  --m-line:#e7ebf2;
  --m-line2:#dde6f6;
  --m-shadow:0 26px 70px rgba(15,23,42,.18);
  --m-glow:0 0 0 10px rgba(31,75,184,.07);
  --m-blue:#1f4bb8;
  --m-danger:#dc2626;
}
.bank-mask{
  position:fixed;
  inset:0;
  background:rgba(15,23,42,.22);
  opacity:0;
  pointer-events:none;
  transition:.18s ease;
  z-index:5000;
  backdrop-filter: blur(10px) saturate(120%);
  -webkit-backdrop-filter: blur(10px) saturate(120%);
}
.bank-mask.open{
  opacity:1;
  pointer-events:auto;
}

.bank-modal{
  position:fixed;
  left:50%;
  top:50%;
  transform:translate(-50%, -46%) scale(.985);
  width:min(520px, calc(100% - 28px));
  opacity:0;
  pointer-events:none;
  transition:.18s ease;
  z-index:5001;
}
.bank-modal.open{
  opacity:1;
  pointer-events:auto;
  transform:translate(-50%, -50%) scale(1);
}
.bank-card{
  background:rgba(255,255,255,.92);
  border:1px solid var(--m-line2);
  border-radius:18px;
  box-shadow: var(--m-shadow), var(--m-glow);
  overflow:hidden;
}
.bank-top{
  padding:14px 16px;
  display:flex;
  align-items:flex-start;
  justify-content:space-between;
  gap:10px;
  border-bottom:1px solid var(--m-line);
  background:
    radial-gradient(120% 120% at 0% 0%, rgba(31,75,184,.08) 0%, rgba(255,255,255,.0) 55%),
    linear-gradient(180deg, rgba(255,255,255,.75) 0%, rgba(255,255,255,.35) 100%);
}
.bank-brand{
  display:flex;
  gap:12px;
  align-items:center;
}
.bank-badge{
  width:44px;
  height:44px;
  border-radius:14px;
  display:grid;
  place-items:center;
  background: rgba(31,75,184,.08);
  border:1px solid rgba(31,75,184,.14);
}
.bank-badge i{
  color:var(--m-blue);
  font-size:1.15rem;
}
.bank-title{
  font-weight:900;
  color:var(--ink);
  letter-spacing:.2px;
  line-height:1.1;
}
.bank-sub{
  margin-top:2px;
  font-size:.88rem;
  color:var(--muted);
}
.bank-close{
  border:1px solid var(--m-line);
  background:#fff;
  color:#475569;
  width:36px;
  height:36px;
  border-radius:12px;
  display:grid;
  place-items:center;
  transition:.12s ease;
}
.bank-close:hover{
  background:#f8fafc;
}
.bank-body{
  padding:14px 16px 16px;
}
.bank-alert{
  display:flex;
  align-items:flex-start;
  gap:10px;
  padding:10px 12px;
  border-radius:14px;
  border:1px solid var(--m-line);
  background: rgba(31,75,184,.045);
  color:#334155;
  font-size:.92rem;
  line-height:1.35;
}
.bank-alert .dot{
  width:10px;
  height:10px;
  border-radius:999px;
  background: rgba(31,75,184,.75);
  box-shadow: 0 0 0 6px rgba(31,75,184,.10);
  margin-top:4px;
  flex:0 0 auto;
}
.bank-alert b{
  color:var(--ink);
}

.otp-row{
  margin-top:12px;
  display:flex;
  gap:10px;
  justify-content:center;
}
.otp{
  width:54px;
  height:58px;
  text-align:center;
  font-weight:900;
  font-size:1.15rem;
  color:var(--ink);
  background:#fff;
  border:1px solid var(--m-line2);
  border-radius:14px;
  outline:0;
  box-shadow: 0 1px 0 rgba(15,23,42,.02) inset;
  transition:.12s ease;
}
.otp:focus{
  border-color: rgba(31,75,184,.35);
  box-shadow: 0 0 0 6px rgba(31,75,184,.12);
}
.otp.error{
  border-color: rgba(220,38,38,.40);
  box-shadow: 0 0 0 6px rgba(220,38,38,.12);
}
@media (max-width:420px){
  .otp{
    width:44px;
    height:54px;
    border-radius:12px;
  }
  .otp-row{
    gap:8px;
  }
}
.bank-note{
  margin-top:10px;
  text-align:center;
  font-size:.85rem;
  color:#64748b;
}
.loading-dots{
  margin-top:10px;
  display:none;
  justify-content:center;
  gap:6px;
}
.loading-dots span{
  width:7px;
  height:7px;
  border-radius:999px;
  background: rgba(31,75,184,.55);
  opacity:.6;
  animation: dotPulse .9s infinite ease-in-out;
}
.loading-dots span:nth-child(2){
  animation-delay:.12s;
}
.loading-dots span:nth-child(3){
  animation-delay:.24s;
}
@keyframes dotPulse{
  0%,100%{
    transform:translateY(0);
    opacity:.45;
  }
  50%{
    transform:translateY(-4px);
    opacity:1;
  }
}
@keyframes shake {
  0%,100%{
    transform: translateX(0);
  }
  20%{
    transform: translateX(-5px);
  }
  40%{
    transform: translateX(5px);
  }
  60%{
    transform: translateX(-4px);
  }
  80%{
    transform: translateX(4px);
  }
}
.shake {
  animation: shake .28s ease;
}
</style>

<script>
window.Stepper = function(cfg){
  return {
    pasoKeys: cfg?.pasoKeys || [],
    labels:   cfg?.labels   || [],
    routes:   cfg?.routes   || {},
    current:  0,

    init(){
      this.current = 0;
    },

    max(){
      return this.labels.length - 1;
    },

    go(i){
      i = Number(i);
      if (!Number.isFinite(i)) return;
      if (i < 0) i = 0;
      if (i > this.max()) i = this.max();
      this.current = i;
    },

    next(){
      this.go(this.current + 1);
    },

    prev(){
      this.go(this.current - 1);
    },
  };
};
</script>

<div class="container animated-entry">

  <div class="hero mb-3" style="margin-top:25px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="d-flex align-items-center gap-3">
          <button type="button"
          onclick="history.back()"
          class="btn btn-ghost" class= title="Volver">
          <i class="bi bi-arrow-left"></i>
          </button>
        </a>
        <div>
          <h1 class="h4 mb-0">Ficha de equipo</h1>
          <div class="subtle">
            <strong>
              {{ mb_strtoupper($registro->tipo_equipo ?? '-', 'UTF-8') }}
              @if($registro->subtipo_equipo)
                · {{ mb_strtoupper($registro->subtipo_equipo, 'UTF-8') }}
              @endif
            </strong>
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        <span class="badge-state {{ $badgeClass }}">
          <i class="bi bi-circle-fill" style="font-size:.55rem"></i>
          <span class="text-capitalize">{{ $estadoActual }}</span>
        </span>

        <span class="text-muted-ux small ms-2">
          Último cambio: {{ $fechaUltimoEstado ? $fechaUltimoEstado->format('Y-m-d H:i') : '—' }}
        </span>
      </div>
    </div>

    <div class="hero-actions">
      <a class="btn btn-blue d-none d-sm-inline-flex" target="_blank" href="{{ $barcodeUrl }}">
        <i class="bi bi-upc-scan"></i> Imprimir etiqueta
      </a>

      <button type="button" class="btn btn-blue d-none d-sm-inline-flex" onclick="openCambioProceso()">
        <i class="bi bi-diagram-3"></i> Cambiar proceso
      </button>

      @if(!empty($registro->documentoPDF))
        <a class="btn btn-ghost d-none d-sm-inline-flex" target="_blank" href="{{ $registro->documentoPDF }}">
          <i class="bi bi-file-earmark-pdf"></i> Ver PDF
        </a>
      @endif

      
    </div>
  </div>

  <div class="row gy-4 gx-4">
    <div class="col-12 col-lg-5">
      <div class="card-soft">
        <div class="head">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-cpu"></i>
            <strong>Ficha técnica</strong>
          </div>
        </div>

        <div class="body">
          <div class="d-flex flex-wrap gap-2 mb-3">
            @php
              $thumbs = array_filter([$registro->evidencia1, $registro->evidencia2, $registro->evidencia3]);
            @endphp

            @php
    $galleryItems = [];

    foreach($thumbs as $img){

        $galleryItems[] = [
            'type' => 'image',
            'src'  => asset($img)
        ];
    }

    if(!empty($registro->video)){

        $galleryItems[] = [
            'type' => 'video',
            'src'  => asset($registro->video)
        ];
    }
@endphp

<div class="d-flex flex-wrap gap-2 mb-3">

@forelse($galleryItems as $i => $item)

    <div class="thumb"
         onclick='openGallery(@json($galleryItems), {{ $i }})'>

        @if($item['type'] === 'image')

            <img src="{{ $item['src'] }}"
                 alt="Imagen">

        @else

            <video muted>
                <source src="{{ $item['src'] }}">
            </video>

            <div class="thumb-play">
                <i class="bi bi-play-fill"></i>
            </div>

        @endif

    </div>

@empty

    <div class="text-muted-ux small">
        Sin archivos
    </div>

@endforelse

</div>

            
          </div>

          <div class="kpis mb-3">
            <div class="kpi">
              <div class="label">Marca</div>
              <div class="val">{{ $registro->marca ?? '—' }}</div>
            </div>
            <div class="kpi">
              <div class="label">Modelo</div>
              <div class="val">{{ $registro->modelo ?? '—' }}</div>
            </div>
          </div>

          <div class="kpis mb-3">
            <div class="kpi">
              <div class="label">Serie</div>
              <div class="val">{{ $registro->numero_serie ?? '—' }}</div>
            </div>
            <div class="kpi">
              <div class="label">Año</div>
              <div class="val">{{ $registro->anio ?? '—' }}</div>
            </div>
          </div>

          <div class="kpis mb-3">
            <div class="kpi">
              <div class="label">Fecha adquisición</div>
              <div class="val">{{ optional($registro->fecha_adquisicion)->format('Y-m-d') ?? '—' }}</div>
            </div>
            <div class="kpi">
              <div class="label">Registrado por</div>
              <div class="val">{{ $registro->user_name ?? '—' }}</div>
            </div>
          </div>

          <div class="panel mb-2">
            <div class="fw-bold mb-1">Descripción</div>
            <div class="text-muted-ux">{{ $registro->descripcion ?? '—' }}</div>
          </div>

          @if($registro->observaciones)
            <div class="panel">
              <div class="fw-bold mb-1">Observaciones</div>
              <div class="text-muted-ux">{{ $registro->observaciones }}</div>
            </div>
          @endif
        </div>
      </div>

      <div class="card-soft mt-3">
        <div class="head">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-puzzle"></i>
            <strong>Componentes incluidos</strong>
          </div>
          <span class="badge-state badge-registro">{{ $componentes->count() }} ítem(s)</span>
        </div>

        <div class="body">
          @if($componentes->isEmpty())
            <div class="comp-empty small">No hay componentes registrados para este equipo.</div>
          @else
            <div class="table-responsive">
              <table class="comp-table">
                <thead>
                  <tr>
                    <th>Componente</th>
                    <th class="text-center">Cant.</th>
                    <th>Notas</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($componentes as $c)
                    <tr>
                      <td>{{ $c->nombre }}</td>
                      <td class="text-center qty">{{ (int)$c->cantidad }}</td>
                      <td class="text-muted-ux">{{ $c->notas ?: '—' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

      @if(!empty($registro->firma_digital))
        <div class="card-soft mt-3">
          <div class="head">
            <strong><i class="bi bi-pencil me-1"></i> Firma digital</strong>
          </div>
          <div class="body">
            <div class="thumb" style="width:100%; height:auto; max-height:200px; border-radius:14px; cursor:pointer"
                 onclick="previewImage('{{ $registro->firma_digital }}')">
              <img src="{{ $registro->firma_digital }}" alt="Firma" style="object-fit:contain;background:#fff">
            </div>
          </div>
        </div>
      @endif
    </div>

    <div class="col-12 col-lg-7"
         x-data="Stepper(JSON.parse($el.dataset.cfg))"
         x-init="init()"
         data-cfg='@json($stepperCfg, JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)'>

      <div class="card-soft">
        <div class="head">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-diagram-3"></i>
            <strong>Procesos del equipo</strong>
          </div>
          <div class="small text-muted-ux">
            Puedes ver cualquier paso. (“Vendido” solo se hace en remisión).
          </div>
        </div>

        <div class="stepper mb-2">
    @foreach($pasos as $idx => $paso)
        @php
            $isDone = !empty($procesos[$paso] ?? null);
        @endphp

        <div class="step-pill {{ $isDone ? 'done' : '' }}"
             :class="{'active': current === {{ $idx }}}">

            <span class="dot"></span>

            {{ $bonitos[$paso] ?? ucfirst($paso) }}

        </div>
    @endforeach
</div>

          @foreach($pasos as $idx => $paso)
            @php
              $proceso   = $procesos[$paso] ?? null;
              $label     = $bonitos[$paso] ?? ucfirst($paso);
              $isVendido = ($paso === 'vendido');

              $evids = [];
              if ($proceso) {
                for ($e=1; $e<=3; $e++) {
                  $k = "evidencia$e";
                  if (!empty($proceso->$k)) {
                    $evids[] = asset('storage/'.$proceso->$k);
                  }
                }
              }
            @endphp

            <div x-show="current === {{ $idx }}"
                 x-transition.opacity.scale.origin.top.duration.150ms
                 x-cloak>
              <div class="panel">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="badge-state {{ $proceso ? 'badge-stock' : 'badge-registro' }}">
                    <i class="bi bi-circle-fill" style="font-size:.55rem"></i>
                    {{ $proceso ? 'Completado' : 'Pendiente' }}
                  </span>

                  <span class="small text-muted-ux">
                    @if($proceso && $proceso->created_at)
                      · Hecho el {{ $proceso->created_at->format('Y-m-d H:i') }}
                    @endif
                  </span>
                </div>

                @if(!$proceso)
                  <div class="text-muted-ux mb-3">Este proceso aún no ha sido registrado.</div>

                  @if(!$isVendido)
                    @if($routes[$paso])
                      <a href="{{ $routes[$paso] }}" class="btn btn-blue">
                        <i class="bi bi-plus-lg"></i> Completar {{ $label }}
                      </a>
                    @else
                      <button type="button" class="btn btn-ghost" disabled>
                        <i class="bi bi-lock"></i> Ruta no definida
                      </button>
                    @endif
                  @endif
                @else
                  <div class="mb-3">
                    <div class="fw-bold mb-1">Descripción</div>
                    <div class="text-muted-ux">{{ $proceso->descripcion_proceso ?? '—' }}</div>
                  </div>

                  @php
                    $raw = $proceso->checklist ?? null;
                    $sections = null;

                    if (is_array($raw)) {
                      $sections = $raw;
                    } elseif (is_string($raw)) {
                      $sections = json_decode($raw, true);
                    }

                    $tot = 0;
                    $ok = 0;
                    $bad = 0;
                    $na = 0;

                    if (is_array($sections)) {
                      foreach ($sections as $sec) {
                        $items = isset($sec['items']) && is_array($sec['items']) ? $sec['items'] : [];

                        foreach ($items as $it) {
                          $tot++;
                          $done = $it['done'] ?? ($it['pass'] ?? ($it['ok'] ?? null));

                          if (is_string($done)) {
                            $done = in_array(strtolower($done), ['1','true','ok','si','sí','yes']);
                          }

                          if ($done === true) {
                            $ok++;
                          } elseif ($done === false) {
                            $bad++;
                          } else {
                            $na++;
                          }
                        }
                      }
                    }
                  @endphp

                  @if(is_array($sections) && $tot > 0)
                    <div class="fw-bold mb-1">Checklist</div>
                    <div class="mb-2 small text-muted-ux">
                      Completados: <strong>{{ $ok }}</strong> / {{ $tot }}
                      @if($bad>0)
                        · No realizados: <strong class="text-danger">{{ $bad }}</strong>
                      @endif
                      @if($na>0)
                        · N/A: <strong>{{ $na }}</strong>
                      @endif
                    </div>

                    @foreach($sections as $sec)
                      @php
                        $title = $sec['name'] ?? $sec['title'] ?? 'Sección';
                        $items = isset($sec['items']) && is_array($sec['items']) ? $sec['items'] : [];
                      @endphp

                      @if(count($items))
                        <div class="panel" style="padding:10px; margin-bottom:8px;">
                          <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold" style="text-transform:capitalize">
                              {{ $title }}
                            </div>
                          </div>

                          <div class="d-flex flex-column gap-1">
                            @foreach($items as $it)
                              @php
                                $labelIt = $it['label'] ?? $it['nombre'] ?? 'Ítem';
                                $doneIt  = $it['done'] ?? ($it['pass'] ?? ($it['ok'] ?? null));

                                if (is_string($doneIt)) {
                                  $doneIt = in_array(strtolower($doneIt), ['1','true','ok','si','sí','yes']);
                                }

                                $noteIt  = $it['note'] ?? ($it['notes'] ?? ($it['nota'] ?? null));

                                $ico = $doneIt === true
                                  ? 'bi-check-circle-fill'
                                  : ($doneIt === false ? 'bi-x-circle-fill' : 'bi-dash-circle');

                                $color = $doneIt === true
                                  ? '#16a34a'
                                  : ($doneIt === false ? '#dc2626' : '#64748b');
                              @endphp

                              <div class="d-flex align-items-start gap-2">
                                <i class="bi {{ $ico }}" style="color:{{ $color }}; font-size:1rem;"></i>
                                <div>
                                  <div class="fw-semibold">{{ $labelIt }}</div>
                                  @if($noteIt)
                                    <div class="small text-muted-ux">{{ $noteIt }}</div>
                                  @endif
                                </div>
                              </div>
                            @endforeach
                          </div>
                        </div>
                      @endif
                    @endforeach
                  @endif

                  @if($evids || ($proceso && ($proceso->video || $proceso->documento_pdf)))
                    <div class="fw-bold mb-1 mt-3">Adjuntos</div>
                  @endif

                  <div class="d-flex flex-wrap gap-2 mb-2">
                    @foreach($evids as $src)
                      <div class="thumb" onclick="previewImage('{{ $src }}')">
                        <img src="{{ $src }}" alt="Evidencia">
                      </div>
                    @endforeach

                    @if($proceso && $proceso->video)
                      <div class="thumb" onclick="previewVideo('{{ asset('storage/'.$proceso->video) }}')">
                        <i class="bi bi-play-btn" style="font-size:1.4rem;opacity:.65"></i>
                      </div>
                    @endif

                    @if($proceso && $proceso->documento_pdf)
                      <a class="btn btn-ghost" target="_blank" href="{{ asset('storage/'.$proceso->documento_pdf) }}">
                        <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                      </a>
                    @endif
                  </div>

                  <div class="mt-2">
                    <button type="button"
                            class="btn btn-danger-soft"
                            onclick="openDeleteProcesoOTP({{ (int)$proceso->id }}, '{{ addslashes($label) }}')">
                      <i class="bi bi-trash3"></i> Eliminar proceso
                    </button>
                  </div>
                @endif

                <div class="d-flex justify-content-between mt-3">
                  <button class="btn btn-ghost" :disabled="current===0" @click="prev()">
                    <i class="bi bi-arrow-left"></i> Anterior
                  </button>

                  <button class="btn btn-ghost" :disabled="current===max()" @click="next()">
                    Siguiente <i class="bi bi-arrow-right"></i>
                  </button>
                </div>
              </div>
            </div>
          @endforeach

        </div>
      </div>
    </div>
  </div>
</div>

<div class="fab">
  <button type="button" id="openActionsFab" class="fab-btn" aria-label="Más acciones">
    <i class="bi bi-three-dots"></i>
  </button>
</div>

<div class="sheet-backdrop" id="sheetBackdrop"></div>

<div class="sheet" id="sheetPanel">
  <div class="grab"></div>
  <div class="title">Acciones</div>

  <div class="grid">
    <a class="action" target="_blank" href="{{ $barcodeUrl }}">
      <div class="ico"><i class="bi bi-upc-scan"></i></div>
      Imprimir etiqueta
    </a>

    <button type="button" class="action" onclick="hideSheet(); openCambioProceso();">
      <div class="ico"><i class="bi bi-diagram-3"></i></div>
      Cambiar proceso
    </button>

    <div class="row2">
      <button type="button" class="action js-open-edit-equipo" id="mEdit">
        <div class="ico"><i class="bi bi-pencil-square"></i></div>
        Editar
      </button>

      <button type="button" class="action js-open-delete-equipo" id="mDelete">
        <div class="ico"><i class="bi bi-trash3"></i></div>
        Eliminar
      </button>
    </div>
  </div>
</div>

<div id="bankMask" class="bank-mask" aria-hidden="true"></div>

<div id="bankModal" class="bank-modal" role="dialog" aria-modal="true" aria-label="Aprobación por PIN">
  <div class="bank-card">
    <div class="bank-top">
      <div class="bank-brand">
        <div class="bank-badge"><i class="bi bi-shield-lock"></i></div>
        <div>
          <div id="bankTitle" class="bank-title">Confirmación segura</div>
          <div id="bankSub" class="bank-sub">Escribe el PIN de 6 dígitos</div>
        </div>
      </div>

      <button type="button" id="bankClose" class="bank-close" aria-label="Cerrar">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <div class="bank-body">
      <div class="bank-alert">
        <div class="dot"></div>
        <div id="bankAlertText">
          Al completar los <b>6 dígitos</b>, se confirma automáticamente.
        </div>
      </div>

      <div class="otp-row" aria-label="PIN de 6 dígitos">
        <input class="otp" inputmode="numeric" autocomplete="one-time-code" maxlength="1" aria-label="Dígito 1">
        <input class="otp" inputmode="numeric" maxlength="1" aria-label="Dígito 2">
        <input class="otp" inputmode="numeric" maxlength="1" aria-label="Dígito 3">
        <input class="otp" inputmode="numeric" maxlength="1" aria-label="Dígito 4">
        <input class="otp" inputmode="numeric" maxlength="1" aria-label="Dígito 5">
        <input class="otp" inputmode="numeric" maxlength="1" aria-label="Dígito 6">
      </div>

      <div class="bank-note">
        Puedes <b>pegar</b> el PIN completo.
      </div>

      <div id="otpLoading" class="loading-dots" aria-hidden="true">
        <span></span><span></span><span></span>
      </div>
    </div>
  </div>
</div>

<script>
let galleryItems = [];
let currentGalleryIndex = 0;

function openGallery(items, index = 0){

    galleryItems = items;
    currentGalleryIndex = index;

    showGallery();
}

function showGallery(){

    const item = galleryItems[currentGalleryIndex];

    let media = '';

    if(item.type === 'image'){

        media = `
            <img
                src="${item.src}"
                alt="preview"
            >
        `;

    }else{

        media = `
            <video controls autoplay playsinline>
                <source src="${item.src}">
            </video>
        `;
    }

    Swal.fire({

        html: `
            <div class="gallery-modal">

                <button class="gallery-arrow left"
                        onclick="prevGallery()">

                    <i class="bi bi-chevron-left"></i>

                </button>

                ${media}

                <button class="gallery-arrow right"
                        onclick="nextGallery()">

                    <i class="bi bi-chevron-right"></i>

                </button>

                <div class="gallery-counter">
                    ${currentGalleryIndex + 1}
                    /
                    ${galleryItems.length}
                </div>

            </div>
        `,

        width: 'auto',
        padding: '12px',
        background:'#fff',

        showCloseButton:true,
        showConfirmButton:false,

        customClass:{
            popup:'media'
        },

        didOpen: () => {

            document.addEventListener(
                'keydown',
                galleryKeyboard
            );
        },

        willClose: () => {

            document.removeEventListener(
                'keydown',
                galleryKeyboard
            );
        }
    });
}

function renderGalleryContent(){

    const item = galleryItems[currentGalleryIndex];

    let media = '';

    if(item.type === 'image'){

        media = `
            <img
                src="${item.src}"
                alt="preview"
            >
        `;

    }else{

        media = `
            <video controls autoplay playsinline>
                <source src="${item.src}">
            </video>
        `;
    }

    const html = `
        <div class="gallery-modal">

            <button class="gallery-arrow left"
                    onclick="prevGallery()">

                <i class="bi bi-chevron-left"></i>

            </button>

            ${media}

            <button class="gallery-arrow right"
                    onclick="nextGallery()">

                <i class="bi bi-chevron-right"></i>

            </button>

            <div class="gallery-counter">
                ${currentGalleryIndex + 1}
                /
                ${galleryItems.length}
            </div>

        </div>
    `;

    const container = Swal.getHtmlContainer();

    if(container){
        container.innerHTML = html;
    }
}

function nextGallery(){

    currentGalleryIndex++;

    if(currentGalleryIndex >= galleryItems.length){
        currentGalleryIndex = 0;
    }

    renderGalleryContent();
}

function prevGallery(){

    currentGalleryIndex--;

    if(currentGalleryIndex < 0){
        currentGalleryIndex = galleryItems.length - 1;
    }

    renderGalleryContent();
}

function galleryKeyboard(e){

    if(e.key === 'ArrowRight'){
        nextGallery();
    }

    if(e.key === 'ArrowLeft'){
        prevGallery();
    }
}

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 2600,
  timerProgressBar: true,
  customClass: {
    popup: 'shadow-sm'
  },
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer);
    toast.addEventListener('mouseleave', Swal.resumeTimer);
  }
});

function toastSuccess(message){
  Toast.fire({
    icon: 'success',
    title: message || 'Operación realizada correctamente.'
  });
}

function toastError(message){
  Toast.fire({
    icon: 'error',
    title: message || 'Ocurrió un error.'
  });
}

function toastInfo(message){
  Toast.fire({
    icon: 'info',
    title: message || 'Información.'
  });
}

const backdrop = document.getElementById('sheetBackdrop');
const panel    = document.getElementById('sheetPanel');

function showSheet(){
  panel.classList.add('show');
  backdrop.classList.add('show');
  document.body.classList.add('sheet-open');
}

function hideSheet(){
  panel.classList.remove('show');
  backdrop.classList.remove('show');
  document.body.classList.remove('sheet-open');
}

document.getElementById('openActionsFab')?.addEventListener('click', showSheet);
backdrop?.addEventListener('click', hideSheet);

const bankMask      = document.getElementById('bankMask');
const bankModal     = document.getElementById('bankModal');
const bankClose     = document.getElementById('bankClose');
const bankTitle     = document.getElementById('bankTitle');
const bankSub       = document.getElementById('bankSub');
const bankAlertText = document.getElementById('bankAlertText');
const otpInputs     = Array.from(document.querySelectorAll('.otp'));
const bankCard      = document.querySelector('.bank-card');
const loading       = document.getElementById('otpLoading');

let activeMode = null;
let activeProcesoId = null;
let activeProcesoLabel = null;
let submitting = false;

const CSRF                  = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const DEL_PROCESO_TPL       = @json($delProcesoUrlTpl);
const EDIT_URL              = @json($editUrl);
const EDIT_PIN_VALIDATE_URL = @json($editPinValidateUrl);
const DELETE_EQUIPO_URL     = @json($deleteAction);
const INDEX_URL             = @json($indexUrl);
const CAMBIAR_ESTADO_URL    = @json($cambiarEstadoUrl);
const ESTADO_ACTUAL         = @json($estadoActual);

function onlyDigits(s){
  return (s || '').toString().replace(/\D+/g,'');
}

function getOTP(){
  return otpInputs.map(i => i.value || '').join('');
}

function clearOTP(){
  otpInputs.forEach(i => {
    i.value = '';
    i.classList.remove('error');
    i.disabled = false;
  });

  submitting = false;

  if(loading) {
    loading.style.display = 'none';
  }
}

function focusFirst(){
  setTimeout(() => otpInputs[0]?.focus(), 60);
}

function setOTPTexts(mode){
  if(mode === 'editar'){
    if(bankTitle) bankTitle.textContent = 'Autorización de edición';
    if(bankSub) bankSub.textContent = 'Escribe el PIN de 6 dígitos para continuar';
    if(bankAlertText) bankAlertText.innerHTML = 'Al completar los <b>6 dígitos</b>, se validará el acceso y se abrirá la edición.';
    return;
  }

  if(mode === 'proceso'){
    if(bankTitle) bankTitle.textContent = 'Eliminar proceso';
    if(bankSub) bankSub.textContent = 'Escribe el PIN de 6 dígitos';
    if(bankAlertText) bankAlertText.innerHTML = 'Al completar los <b>6 dígitos</b>, se eliminará el proceso automáticamente.';
    return;
  }

  if(bankTitle) bankTitle.textContent = 'Eliminar registro';
  if(bankSub) bankSub.textContent = 'Escribe el PIN de 6 dígitos';
  if(bankAlertText) bankAlertText.innerHTML = 'Al completar los <b>6 dígitos</b>, se validará el acceso y se eliminará el registro.';
}

function openOTP(mode, payload={}){
  activeMode = mode;
  activeProcesoId = payload.procesoId ?? null;
  activeProcesoLabel = payload.label ?? null;

  setOTPTexts(mode);
  clearOTP();

  bankMask?.classList.add('open');
  bankModal?.classList.add('open');

  focusFirst();
}

function closeOTP(){
  bankMask?.classList.remove('open');
  bankModal?.classList.remove('open');

  activeMode = null;
  activeProcesoId = null;
  activeProcesoLabel = null;
}

function shake(){
  if(!bankCard) return;

  bankCard.classList.remove('shake');
  void bankCard.offsetWidth;
  bankCard.classList.add('shake');
}

function flashError(){
  otpInputs.forEach(i => i.classList.add('error'));

  setTimeout(() => {
    otpInputs.forEach(i => i.classList.remove('error'));
  }, 420);

  shake();
}

function resetOtpForRetry(message){
  submitting = false;

  if(loading) {
    loading.style.display = 'none';
  }

  otpInputs.forEach(i => {
    i.value = '';
    i.disabled = false;
    i.classList.remove('error');
  });

  flashError();
  toastError(message || 'NIP incorrecto.');
  focusFirst();
}

document.querySelectorAll('.js-open-edit-equipo').forEach(btn => {
  btn.addEventListener('click', () => {
    hideSheet();
    openOTP('editar');
  });
});

document.querySelectorAll('.js-open-delete-equipo').forEach(btn => {
  btn.addEventListener('click', () => {
    hideSheet();
    openOTP('equipo');
  });
});

window.openDeleteProcesoOTP = function(procesoId, label){
  openOTP('proceso', {
    procesoId,
    label
  });
}

bankMask?.addEventListener('click', closeOTP);
bankClose?.addEventListener('click', closeOTP);

document.addEventListener('keydown', (e) => {
  if(e.key === 'Escape') {
    closeOTP();
  }
});

function urlDeleteProceso(id){
  return DEL_PROCESO_TPL.replace(/0$/, String(id));
}

async function parseJsonSafe(response){
  return await response.json().catch(() => ({}));
}

async function openCambioProceso(){
  const { value: nuevoEstado } = await Swal.fire({
    title: 'Cambiar proceso',
    text: 'Selecciona a qué etapa quieres mover este equipo.',
    input: 'select',
    inputValue: ESTADO_ACTUAL,
    inputOptions: {
      registro: 'Registro',
      hojalateria: 'Hojalatería',
      mantenimiento: 'Mantenimiento',
      stock: 'Stock',
      vendido: 'Vendido',
      defectuoso: 'Defectuoso'
    },
    showCancelButton: true,
    confirmButtonText: 'Continuar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#1f4bb8',
    cancelButtonColor: '#64748b',
    inputValidator: (value) => {
      if (!value) {
        return 'Selecciona un proceso.';
      }
    }
  });

  if (!nuevoEstado) return;

  const nombres = {
    registro: 'Registro',
    hojalateria: 'Hojalatería',
    mantenimiento: 'Mantenimiento',
    stock: 'Stock',
    vendido: 'Vendido',
    defectuoso: 'Defectuoso'
  };

  const confirm = await Swal.fire({
    title: '¿Confirmar cambio?',
    html: `El equipo se moverá a: <b>${nombres[nuevoEstado] || nuevoEstado}</b>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, cambiar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#1f4bb8',
    cancelButtonColor: '#64748b'
  });

  if (!confirm.isConfirmed) return;

  try{
    Swal.fire({
      title: 'Actualizando proceso...',
      text: 'Espera un momento.',
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    const r = await fetch(CAMBIAR_ESTADO_URL, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': CSRF,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        estado_proceso: nuevoEstado
      })
    });

    const data = await parseJsonSafe(r);

    if(!r.ok || data?.success === false){
      throw new Error(data?.message || data?.error || 'No se pudo cambiar el proceso.');
    }

    Swal.close();

    toastSuccess(data?.message || 'Proceso actualizado correctamente.');

    setTimeout(() => {
      window.location.reload();
    }, 800);

  }catch(e){
    Swal.close();
    toastError(e?.message || 'No se pudo cambiar el proceso.');
  }
}

async function runActionWithPin(pin){
  if(activeMode === 'editar'){
    try{
      if(EDIT_PIN_VALIDATE_URL){
        const r = await fetch(EDIT_PIN_VALIDATE_URL, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': CSRF,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            aprobacion_pin: pin,
            registro_id: {{ (int) $registro->id }}
          })
        });

        const data = await parseJsonSafe(r);

        if(!r.ok){
          throw new Error(data?.message || data?.error || 'NIP incorrecto.');
        }
      }

      closeOTP();
      window.location.href = EDIT_URL;

    }catch(e){
      resetOtpForRetry(e?.message || 'No se pudo validar el NIP.');
    }

    return;
  }

  if(activeMode === 'equipo'){
    try{
      const r = await fetch(DELETE_EQUIPO_URL, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': CSRF,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          aprobacion_pin: pin
        })
      });

      const data = await parseJsonSafe(r);

      if(!r.ok){
        throw new Error(data?.message || data?.error || 'No se pudo eliminar el registro.');
      }

      closeOTP();
      toastSuccess(data?.message || 'Registro eliminado correctamente.');

      setTimeout(() => {
        window.location.href = INDEX_URL;
      }, 850);

    }catch(e){
      resetOtpForRetry(e?.message || 'No se pudo eliminar el registro.');
    }

    return;
  }

  if(activeMode === 'proceso'){
    try{
      const r = await fetch(urlDeleteProceso(activeProcesoId), {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': CSRF,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          aprobacion_pin: pin
        })
      });

      const data = await parseJsonSafe(r);

      if(!r.ok){
        throw new Error(data?.message || data?.error || 'No se pudo eliminar el proceso.');
      }

      closeOTP();
      toastSuccess(data?.message || 'Proceso eliminado correctamente.');

      setTimeout(() => {
        window.location.reload();
      }, 850);

    }catch(e){
      resetOtpForRetry(e?.message || 'No se pudo eliminar el proceso.');
    }

    return;
  }
}

function autoSubmitIfReady(){
  if(submitting) return;

  const pin = getOTP();

  if(pin.length === 6 && !otpInputs.some(i => !i.value)){
    submitting = true;

    otpInputs.forEach(i => {
      i.disabled = true;
    });

    if(loading) {
      loading.style.display = 'flex';
    }

    runActionWithPin(pin);
  }
}

otpInputs.forEach((input, idx) => {
  input.addEventListener('input', () => {
    const v = onlyDigits(input.value).slice(0,1);
    input.value = v;

    if(v && otpInputs[idx+1]) {
      otpInputs[idx+1].focus();
    }

    autoSubmitIfReady();
  });

  input.addEventListener('keydown', (e) => {
    if(e.key === 'Backspace' && !input.value && otpInputs[idx-1]){
      otpInputs[idx-1].focus();
      otpInputs[idx-1].value = '';
    }

    if(e.key === 'Enter'){
      e.preventDefault();

      if(getOTP().length !== 6 || otpInputs.some(i => !i.value)){
        flashError();
        focusFirst();
      }
    }
  });

  input.addEventListener('paste', (e) => {
    e.preventDefault();

    const paste = onlyDigits((e.clipboardData || window.clipboardData).getData('text')).slice(0,6);

    if(!paste) return;

    clearOTP();

    paste.split('').forEach((ch, i) => {
      if(otpInputs[i]) {
        otpInputs[i].value = ch;
      }
    });

    otpInputs[Math.min(paste.length,6)-1]?.focus();

    autoSubmitIfReady();
  });
});

document.addEventListener('DOMContentLoaded', () => {
  @if(session('success'))
    toastSuccess(@json(session('success')));
  @endif

  @if(session('error'))
    toastError(@json(session('error')));
  @endif
});
</script>

@endsection