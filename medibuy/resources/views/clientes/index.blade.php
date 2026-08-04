@extends('layouts.app')
@section('title', 'Clientes')
@section('titulo', 'Clientes')

@section('content')
@include('partials.submenu-cotizaciones')
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- LIBRERÍA PARA EXPORTAR A EXCEL --}}
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<style>
:root{
    --bg:#f9fafb; --card:#ffffff; --ink:#111111; --text:#333333;
    --muted:#888888; --line:#ebebeb;
    --blue:#007aff; --blue-soft:#e6f0ff;
    --success:#15803d; --success-soft:#e6ffe6;
    --danger:#ff4a4a; --danger-soft:#ffebeb;
    --warning:#d97706; --warning-soft:#fef3c7;
    --purple:#7c3aed; --purple-soft:#ede9fe;
    --radius:16px; --shadow:0 4px 12px rgba(0,0,0,0.02);
}

*{box-sizing:border-box;}
body{margin:0;padding:0;background:var(--bg);color:var(--text);font-family:'Quicksand',sans-serif;overflow-x:hidden;}

.page{width:100%;max-width:1400px;margin:auto;padding:24px;}

/* ===== TOPBAR ===== */
.topbar{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:20px;}
.title-area{display:flex;align-items:center;gap:14px;}
.title{margin:0;font-size:28px;font-weight:700;color:var(--ink);letter-spacing:-0.5px;}
.sub{margin:4px 0 0;color:var(--muted);font-size:15px;font-weight:500;}

.btn-back{display:inline-flex;align-items:center;justify-content:center;width:42px;height:42px;border-radius:10px;background:var(--card);border:1px solid var(--line);color:var(--text);text-decoration:none;transition:.2s ease;font-size:16px;}
.btn-back:hover{background:var(--bg);border-color:#d1d5db;transform:translateY(-2px);}

/* ===== STATS ===== */
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;}
.stat{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:18px 20px;display:flex;align-items:center;gap:14px;transition:.2s ease;}
.stat:hover{transform:translateY(-2px);box-shadow:0 8px 16px rgba(0,0,0,.03);}
.stat-ic{width:46px;height:46px;border-radius:12px;display:grid;place-items:center;font-size:18px;}
.stat-ic.blue{background:var(--blue-soft);color:var(--blue);}
.stat-ic.green{background:var(--success-soft);color:var(--success);}
.stat-ic.purple{background:var(--purple-soft);color:var(--purple);}
.stat-ic.warn{background:var(--warning-soft);color:var(--warning);}
.stat-info .lbl{font-size:12px;color:var(--muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;}
.stat-info .val{font-size:22px;font-weight:700;color:var(--ink);}

/* ===== BAR ===== */
.bar{display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-bottom:20px;}
.search{flex:1;min-width:260px;display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:10px;border:1px solid var(--line);background:var(--card);transition:.2s ease;position:relative;}
.search .ic{color:var(--muted);font-size:16px;}
.search input{width:100%;border:0;outline:0;background:transparent;font-family:'Quicksand',sans-serif;font-size:15px;font-weight:600;color:#111;}
.search input::placeholder{color:var(--muted);}
.search:focus-within{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-soft);}
.search .kbd{font-size:11px;font-weight:700;color:var(--muted);padding:2px 6px;border:1px solid var(--line);border-radius:5px;background:var(--bg);}
.search .clear-btn{background:none;border:none;color:var(--muted);cursor:pointer;padding:4px;border-radius:6px;display:none;}
.search .clear-btn:hover{background:var(--bg);color:var(--danger);}
.search.has-text .clear-btn{display:inline-flex;}
.search.has-text .kbd{display:none;}

.select-filter{padding:12px 16px;border-radius:10px;border:1px solid var(--line);background:var(--card);font-family:'Quicksand',sans-serif;font-size:14px;font-weight:600;color:var(--text);outline:none;cursor:pointer;min-width:160px;}
.select-filter:focus{border-color:var(--blue);box-shadow:0 0 0 3px var(--blue-soft);}

.view-toggle{display:inline-flex;background:var(--card);border:1px solid var(--line);border-radius:10px;padding:4px;gap:4px;}
.view-toggle button{background:none;border:none;cursor:pointer;padding:8px 14px;border-radius:7px;font-weight:700;font-size:13px;color:var(--muted);display:inline-flex;align-items:center;gap:6px;transition:.18s ease;}
.view-toggle button.active{background:var(--blue);color:#fff;}
.view-toggle button:not(.active):hover{background:var(--bg);color:var(--text);}

.btn-primary{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:48px;padding:12px 22px;border-radius:10px;background:var(--blue);color:#fff;font-size:15px;font-weight:700;text-decoration:none;border:none;cursor:pointer;transition:.2s ease;white-space:nowrap;}
.btn-primary:hover{filter:brightness(1.05);transform:translateY(-1px);}

.btn-export{background:#10b981 !important;}
.btn-export:hover{background:#059669 !important;}

.results-info{margin:0 0 16px;font-size:13px;color:var(--muted);font-weight:600;}
.results-info b{color:var(--ink);}

/* ===== NOTICE ===== */
.notice{background:var(--danger-soft);border-radius:16px;padding:20px;margin-bottom:24px;}
.notice-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px;flex-wrap:wrap;}
.notice-title{display:flex;align-items:center;gap:12px;font-weight:700;color:var(--danger);font-size:15px;}
.notice-count{color:var(--danger);font-size:13px;font-weight:700;}
.alertas{list-style:none;padding:0;margin:0;display:grid;gap:10px;}
.alertas li{background:var(--card);border:1px solid var(--line);border-radius:10px;padding:14px 16px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;}
.alertas .txt{font-size:14px;font-weight:600;color:var(--text);}
.btn-outline{background:var(--card);border:1px solid var(--blue);color:var(--blue);font-family:'Quicksand',sans-serif;font-weight:700;font-size:13px;border-radius:8px;padding:9px 14px;cursor:pointer;}

/* ===== BADGES ===== */
.badge-nuevo{display:inline-flex;align-items:center;gap:4px;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-size:10px;font-weight:800;padding:3px 8px;border-radius:999px;text-transform:uppercase;letter-spacing:.5px;animation:pulse 2s ease-in-out infinite;}
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(16,185,129,.5);}50%{box-shadow:0 0 0 5px rgba(16,185,129,0);}}

.pill{display:inline-flex;align-items:center;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:700;}
.pill-si{background:var(--success-soft);color:var(--success);}
.pill-no{background:#f1f5f9;color:var(--muted);}
.pill-cat{background:#f1f5f9;color:#475569;border-radius:8px;font-size:12px;font-weight:700;padding:4px 10px;}

/* ===== CARDS GRID ===== */
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:20px;}
.card-c{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);padding:22px;display:flex;flex-direction:column;gap:18px;transition:.2s ease;animation:fadeIn .25s ease;}
.card-c:hover{transform:translateY(-3px);box-shadow:0 14px 26px rgba(0,0,0,.05);border-color:#d1d5db;}
@keyframes fadeIn{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:translateY(0);}}

.c-head{display:flex;justify-content:space-between;gap:12px;align-items:flex-start;}
.who{display:flex;align-items:center;gap:14px;flex:1;min-width:0;}
.avatar{width:46px;height:46px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,var(--blue-soft),#dbeafe);color:var(--blue);font-weight:700;font-size:16px;flex-shrink:0;}
.info-who{flex:1;min-width:0;}
.name{margin:0;font-size:17px;font-weight:700;color:var(--ink);display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.meta{margin:3px 0 0;font-size:13px;color:var(--muted);}

.rows{display:grid;gap:12px;}
.rowx{display:flex;align-items:flex-start;gap:12px;}
.rowx .ic{width:22px;display:grid;place-items:center;color:var(--muted);flex-shrink:0;}
.rowx .k{font-size:11px;color:var(--muted);margin-bottom:2px;font-weight:600;text-transform:uppercase;letter-spacing:.4px;}
.rowx .v{font-size:14px;font-weight:600;color:var(--text);line-height:1.5;overflow-wrap:anywhere;}

.acciones{margin-top:auto;display:flex;justify-content:space-between;align-items:center;gap:10px;padding-top:14px;border-top:1px solid var(--line);flex-wrap:wrap;}
.acciones-btns{display:flex;gap:6px;}
.icon-btn{width:38px;height:38px;border-radius:9px;display:grid;place-items:center;background:transparent;color:var(--muted);text-decoration:none;border:none;cursor:pointer;transition:.15s ease;}
.icon-btn:hover{background:var(--bg);color:var(--text);}
.icon-btn.danger:hover{background:var(--danger-soft);color:var(--danger);}

/* ===== TABLA ===== */
.tabla-wrap{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow);overflow:hidden;}
.tabla-scroll{overflow-x:auto;max-height:70vh;}
.tabla{width:100%;border-collapse:collapse;font-size:14px;}
.tabla thead th{background:#f1f5fb;color:var(--muted);font-weight:700;padding:14px 16px;text-align:left;border-bottom:1px solid var(--line);white-space:nowrap;position:sticky;top:0;z-index:5;}
.tabla tbody tr{border-bottom:1px solid var(--line);transition:background .15s ease;}
.tabla tbody tr:hover{background:#f8faff;}
.tabla tbody td{padding:14px 16px;vertical-align:middle;color:var(--text);font-weight:600;}
.tabla .td-id{color:var(--muted);font-weight:700;font-size:13px;width:60px;}
.tabla .td-nombre .nombre-principal{font-weight:700;color:var(--ink);display:inline-flex;align-items:center;gap:8px;}
.tabla .td-nombre .nombre-sub{font-size:12px;color:var(--muted);margin-top:2px;}
.tabla .td-acciones{white-space:nowrap;text-align:right;}
.tabla .icon-btn{width:34px;height:34px;display:inline-grid;}

mark.hl{background:#fef08a;color:var(--ink);padding:0 2px;border-radius:3px;font-weight:700;}

/* ===== EMPTY ===== */
.empty-state{grid-column:1/-1;text-align:center;padding:64px 24px;background:var(--card);border:1px dashed var(--line);border-radius:var(--radius);}
.empty-state .ic{font-size:48px;color:var(--line);}
.empty-state h3{margin:14px 0 6px;color:var(--ink);}
.empty-state p{margin:0;color:var(--muted);}

/* ===== PIN OVERLAY ===== */
.pin-overlay{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;}
.pin-overlay.active{display:flex;}
.pin-modal{background:#fff;border-radius:20px;padding:28px 28px 24px;max-width:390px;width:calc(100% - 32px);position:relative;}
.pin-close{position:absolute;top:14px;right:14px;width:30px;height:30px;border-radius:50%;background:#f1f5f9;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:.15s ease;}
.pin-close:hover{background:#fee2e2;border-color:#fca5a5;color:#dc2626;}
.pin-header{display:flex;align-items:center;gap:14px;margin-bottom:20px;}
.pin-icon{width:44px;height:44px;border-radius:12px;background:#eef0ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.pin-info-hint{background:#f8faff;border-radius:12px;padding:12px 16px;margin-bottom:22px;display:flex;align-items:center;gap:10px;}
.pin-dot{width:10px;height:10px;border-radius:50%;background:#4f5bd5;flex-shrink:0;}
.pin-boxes{display:flex;gap:10px;justify-content:center;margin-bottom:14px;}
.pin-box{width:46px;height:54px;border-radius:12px;border:1.5px solid #e2e8f0;text-align:center;font-size:22px;font-weight:700;color:#111;outline:none;font-family:'Quicksand',sans-serif;transition:border-color .15s,box-shadow .15s,background .15s;background:#fff;}
.pin-box:focus{border-color:#4f5bd5;box-shadow:0 0 0 3px rgba(79,91,213,0.15);}
.pin-box.success{border-color:#15803d !important;background:#f0fdf4 !important;box-shadow:0 0 0 3px rgba(21,128,61,0.12) !important;}
.pin-box.error{border-color:#ff4a4a !important;background:#ffebeb !important;box-shadow:0 0 0 3px rgba(255,74,74,0.15) !important;}
.pin-foot{text-align:center;font-size:13px;font-weight:600;color:#888;margin:0;min-height:20px;}

/* ===== SUBLAYOUT ===== */
@media (min-width:768px){
    .page{
        width:auto !important;
        margin-left:calc(88px + clamp(16px,2vw,32px)) !important;
        margin-right:clamp(16px,2vw,32px) !important;
        max-width:calc(100% - 88px - clamp(32px,4vw,64px)) !important;
    }
}
@media (max-width:768px){
    .topbar{flex-direction:column;align-items:flex-start;}
    .bar{flex-direction:column;align-items:stretch;}
    .search,.select-filter,.btn-primary,.view-toggle{width:100%;}
    .view-toggle{justify-content:center;}
    .pin-boxes{gap:7px;}
    .pin-box{width:40px;height:48px;font-size:18px;}
}
@media (max-width:480px){
    .grid{grid-template-columns:1fr;}
}
</style>

@php
  $asesoresFijos = [
    'Jesús Tellez',
    'Gabriela Diaz',
    'Joel Diaz',
    'Anahí Tellez',
    'Jose Alex',
    'Megan Diaz',
    'Victor Guerrero',
  ];

  $totalClientes  = count($clientes);
  $clientesNuevos = collect($clientes)->filter(fn($c) => isset($c->created_at) && \Carbon\Carbon::parse($c->created_at)->gte(now()->subDays(7)))->count();
  $conPromocion   = collect($clientes)->where('recibe_promocion', 1)->count();
  $pendientes     = $alertasGenerales->count() ?? 0;

  $clientesOrdenados = collect($clientes)->sortByDesc(function($c){
    return isset($c->created_at) ? \Carbon\Carbon::parse($c->created_at)->timestamp : 0;
  });
@endphp

{{-- ===== MODAL PIN DE AUTORIZACIÓN (solo para Eliminar) ===== --}}
<div id="pinOverlay" class="pin-overlay" role="dialog" aria-modal="true" aria-label="Autorización de eliminación">
  <div class="pin-modal">
    <button id="pinClose" class="pin-close" type="button" title="Cerrar">
      <i class="fa-solid fa-xmark" style="font-size:13px;color:#64748b;"></i>
    </button>

    <div class="pin-header">
      <div class="pin-icon">
        <i class="fa-solid fa-shield-halved" style="font-size:20px;color:#4f5bd5;"></i>
      </div>
      <div>
        <p style="margin:0;font-size:16px;font-weight:700;color:#111;">Autorización de eliminación</p>
        <p style="margin:0;font-size:13px;font-weight:500;color:#888;">PIN de 6 dígitos para continuar</p>
      </div>
    </div>

    <div class="pin-info-hint">
      <div class="pin-dot"></div>
      <p style="margin:0;font-size:13px;font-weight:600;color:#475569;">
        Al completar los <strong style="color:#111;">6 dígitos</strong>, se confirmará la eliminación.
      </p>
    </div>

    <div class="pin-boxes" id="pinBoxes">
      <input class="pin-box" data-pin maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
      <input class="pin-box" data-pin maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
      <input class="pin-box" data-pin maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
      <input class="pin-box" data-pin maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
      <input class="pin-box" data-pin maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
      <input class="pin-box" data-pin maxlength="1" inputmode="numeric" pattern="[0-9]" autocomplete="off">
    </div>

    <p class="pin-foot" id="pinHint">
      Puedes <strong style="color:#333;">pegar</strong> el PIN completo.
    </p>
  </div>
</div>

<div class="page">

  {{-- TOPBAR --}}
  <div class="topbar">
    <div>
      <div class="title-area" style="gap:8px;">
        <a href="{{ url()->previous() }}" class="btn-back" title="Volver">
          <i class="fa-solid fa-arrow-left"></i>
        </a>
        <a href="{{ url('/home') }}" class="btn-back" title="Inicio">
          <i class="fa-solid fa-house"></i>
        </a>
        <h1 class="title" style="margin-left:6px;">Clientes</h1>
      </div>
      <p class="sub">Administra y encuentra clientes rápidamente.</p>
    </div>
    <a href="{{ route('clientes.create') }}" class="btn-primary">
      <i class="fa-solid fa-plus"></i> Nuevo cliente
    </a>
  </div>

  {{-- STATS --}}
  <div class="stats">
    <div class="stat">
      <div class="stat-ic blue"><i class="fa-solid fa-users"></i></div>
      <div class="stat-info"><div class="lbl">Total</div><div class="val" id="contador-clientes">{{ $totalClientes }}</div></div>
    </div>
    <div class="stat">
      <div class="stat-ic green"><i class="fa-solid fa-user-plus"></i></div>
      <div class="stat-info"><div class="lbl">Nuevos (7d)</div><div class="val">{{ $clientesNuevos }}</div></div>
    </div>
    <div class="stat">
      <div class="stat-ic purple"><i class="fa-solid fa-gift"></i></div>
      <div class="stat-info"><div class="lbl">Con promoción</div><div class="val">{{ $conPromocion }}</div></div>
    </div>
    <div class="stat">
      <div class="stat-ic warn"><i class="fa-solid fa-bell"></i></div>
      <div class="stat-info"><div class="lbl">Pendientes</div><div class="val">{{ $pendientes }}</div></div>
    </div>
  </div>

  {{-- BAR DE FILTROS --}}
  <div class="bar">
    <div class="search" role="search" id="searchBox">
      <div class="ic"><i class="fa-solid fa-magnifying-glass"></i></div>
      <input type="search" id="searchCliente" placeholder="Buscar por nombre, teléfono, correo, asesor...">
      <button class="clear-btn" id="clearSearch" type="button" title="Limpiar"><i class="fa-solid fa-xmark"></i></button>
      <span class="kbd">Ctrl K</span>
    </div>

    <select id="filterAsesor" class="select-filter" aria-label="Filtrar por asesor">
      <option value="">Todos los asesores</option>
      @foreach($asesoresFijos as $asesor)
        <option value="{{ strtolower(\Illuminate\Support\Str::ascii($asesor)) }}">{{ $asesor }}</option>
      @endforeach
    </select>

    <select id="filterPromo" class="select-filter" aria-label="Filtrar por promoción">
      <option value="">Promoción: todos</option>
      <option value="1">Con promoción</option>
      <option value="0">Sin promoción</option>
    </select>

    <select id="orderBy" class="select-filter" aria-label="Ordenar">
      <option value="nuevo" selected>Más recientes</option>
      <option value="nombre">Nombre A–Z</option>
      <option value="asesor">Asesor A–Z</option>
      <option value="default">Orden original</option>
    </select>

    <div class="view-toggle" role="tablist" aria-label="Cambiar vista">
      <button id="btnViewCards" type="button"><i class="fa-solid fa-grip"></i> Cards</button>
      <button id="btnViewTable" class="active" type="button"><i class="fa-solid fa-table-list"></i> Tabla</button>
    </div>

    {{-- BOTÓN DE EXPORTAR A EXCEL --}}
    <button id="exportExcel" class="btn-primary btn-export">
      <i class="fa-solid fa-file-excel"></i> Exportar Excel
    </button>
  </div>

  <p class="results-info">
    Mostrando <b id="resCount">{{ $totalClientes }}</b> de <b>{{ $totalClientes }}</b> clientes
  </p>

  {{-- ALERTAS --}}
  @if($alertasGenerales->isNotEmpty())
    <div class="notice">
      <div class="notice-head">
        <div class="notice-title"><i class="fa-solid fa-bell"></i> Atenciones pendientes</div>
        <div class="notice-count">{{ $alertasGenerales->count() }} pendiente(s)</div>
      </div>
      <ul class="alertas">
        @foreach($alertasGenerales as $alerta)
          <li>
            <div class="txt">
              @if(isset($alerta['cliente']))
                {{ $alerta['cliente']->nombre }} {{ $alerta['cliente']->apellido }} –
              @else
                Cliente no disponible –
              @endif
              @if($alerta['dias'] < 0)
                seguimiento vencido ({{ \Carbon\Carbon::parse($alerta['fecha'])->format('d/m/Y') }})
              @elseif($alerta['dias'] === 0)
                seguimiento para hoy ({{ \Carbon\Carbon::parse($alerta['fecha'])->format('d/m/Y') }})
              @else
                seguimiento en {{ $alerta['dias'] }} días ({{ \Carbon\Carbon::parse($alerta['fecha'])->format('d/m/Y') }})
              @endif
            </div>
            <form method="POST" action="{{ route('seguimientos.completar', $alerta['seguimiento_id']) }}" class="m-0">
              @csrf @method('PATCH')
              <button type="submit" class="btn-outline">Completado</button>
            </form>
          </li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- VISTA CARDS --}}
  <div class="grid" id="view-cards" style="display:none;">
    @forelse($clientesOrdenados as $cliente)
      @php
        $esNuevo    = isset($cliente->created_at) && \Carbon\Carbon::parse($cliente->created_at)->gte(now()->subDays(7));
        $catName    = $cliente->categoria->nombre ?? 'Sin categoría';
        $asesorName = $cliente->asesor ?? '';
        $iniciales  = strtoupper(mb_substr($cliente->nombre ?? '?',0,1).mb_substr($cliente->apellido ?? '',0,1));
        $createdTs  = isset($cliente->created_at) ? \Carbon\Carbon::parse($cliente->created_at)->timestamp : 0;
      @endphp
      <div class="card-c"
           data-asesor="{{ $asesorName }}"
           data-promo="{{ $cliente->recibe_promocion ? '1' : '0' }}"
           data-nuevo="{{ $esNuevo ? '1' : '0' }}"
           data-created="{{ $createdTs }}"
           data-nombre="{{ strtolower($cliente->nombre.' '.$cliente->apellido) }}"
           data-search="{{ strtolower($cliente->nombre.' '.$cliente->apellido.' '.$cliente->telefono.' '.($cliente->email ?? '').' '.$asesorName.' '.($cliente->congreso_conocido ?? '')) }}">
        <div class="c-head">
          <div class="who">
            <div class="avatar">{{ $iniciales }}</div>
            <div class="info-who">
              <h3 class="name">
                <span class="name-text">{{ $cliente->nombre }} {{ $cliente->apellido }}</span>
                @if($esNuevo)<span class="badge-nuevo"><i class="fa-solid fa-sparkles"></i> Nuevo</span>@endif
              </h3>
              <p class="meta">#{{ $cliente->id }} · {{ $asesorName ?: 'Sin asesor' }}</p>
            </div>
          </div>
        </div>

        <div class="rows">
          @if($cliente->telefono)
          <div class="rowx">
            <div class="ic"><i class="fa-solid fa-phone"></i></div>
            <div><div class="k">Teléfono</div><div class="v">{{ $cliente->telefono }}</div></div>
          </div>
          @endif
          @if($cliente->email)
          <div class="rowx">
            <div class="ic"><i class="fa-solid fa-envelope"></i></div>
            <div><div class="k">Correo</div><div class="v">{{ $cliente->email }}</div></div>
          </div>
          @endif
          @if($cliente->congreso_conocido)
          <div class="rowx">
            <div class="ic"><i class="fa-solid fa-building-columns"></i></div>
            <div><div class="k">Congreso</div><div class="v">{{ $cliente->congreso_conocido }}</div></div>
          </div>
          @endif
          <div class="rowx">
            <div class="ic"><i class="fa-solid fa-gift"></i></div>
            <div>
              <div class="k">Promoción</div>
              <div class="v">
                @if($cliente->recibe_promocion)<span class="pill pill-si">SÍ</span>@else<span class="pill pill-no">NO</span>@endif
              </div>
            </div>
          </div>
        </div>

        <div class="acciones">
          <span class="pill-cat">{{ $catName }}</span>
          <div class="acciones-btns">
            <a class="icon-btn" href="{{ route('seguimientos.index', $cliente->id) }}" title="Seguimientos"><i class="fa-solid fa-file-lines"></i></a>

            {{-- EDITAR directo (sin PIN) --}}
            <a class="icon-btn"
               href="{{ route('clientes.edit', $cliente->id) }}"
               title="Editar">
              <i class="fa-solid fa-pen"></i>
            </a>

            {{-- ELIMINAR con PIN --}}
            <button type="button"
              class="icon-btn danger btn-pin-eliminar"
              data-nombre="{{ $cliente->nombre }} {{ $cliente->apellido }}"
              data-action="{{ route('clientes.destroy', $cliente->id) }}"
              title="Eliminar">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    @empty
      <div class="empty-state">
        <div class="ic"><i class="fa-solid fa-folder-open"></i></div>
        <h3>Sin clientes</h3>
        <p>Aún no hay clientes registrados.</p>
      </div>
    @endforelse
  </div>

  {{-- VISTA TABLA (DEFAULT) --}}
  <div class="tabla-wrap" id="view-table">
    <div class="tabla-scroll">
      <table class="tabla">
        <thead>
          <tr>
            <th>ID</th><th>Cliente</th><th>Asesor</th><th>Promoción</th>
            <th>Congreso</th><th>Categoría</th><th style="text-align:right;">Acciones</th>
          </tr>
        </thead>
        <tbody id="lista-clientes">
          @forelse($clientesOrdenados as $cliente)
            @php
              $esNuevo    = isset($cliente->created_at) && \Carbon\Carbon::parse($cliente->created_at)->gte(now()->subDays(7));
              $catName    = $cliente->categoria->nombre ?? 'Sin categoría';
              $asesorName = $cliente->asesor ?? '';
              $createdTs  = isset($cliente->created_at) ? \Carbon\Carbon::parse($cliente->created_at)->timestamp : 0;
            @endphp
            <tr
              data-asesor="{{ $asesorName }}"
              data-promo="{{ $cliente->recibe_promocion ? '1' : '0' }}"
              data-nuevo="{{ $esNuevo ? '1' : '0' }}"
              data-created="{{ $createdTs }}"
              data-nombre="{{ strtolower($cliente->nombre.' '.$cliente->apellido) }}"
              data-search="{{ strtolower($cliente->nombre.' '.$cliente->apellido.' '.$cliente->telefono.' '.($cliente->email ?? '').' '.$asesorName.' '.($cliente->congreso_conocido ?? '')) }}">
              <td class="td-id">#{{ $cliente->id }}</td>
              <td class="td-nombre">
                <div class="nombre-principal">
                  <span class="name-text">{{ $cliente->nombre }} {{ $cliente->apellido }}</span>
                  @if($esNuevo)<span class="badge-nuevo"><i class="fa-solid fa-sparkles"></i> Nuevo</span>@endif
                </div>
                @if($cliente->telefono)<div class="nombre-sub">{{ $cliente->telefono }}</div>@endif
              </td>
              <td>{{ $asesorName ?: '—' }}</td>
              <td>
                @if($cliente->recibe_promocion)<span class="pill pill-si">SÍ</span>@else<span class="pill pill-no">NO</span>@endif
              </td>
              <td>{{ $cliente->congreso_conocido ?: '—' }}</td>
              <td><span class="pill-cat">{{ $catName }}</span></td>
              <td class="td-acciones">
                <a class="icon-btn" href="{{ route('seguimientos.index', $cliente->id) }}" title="Seguimientos">
                  <i class="fa-solid fa-file-lines"></i>
                </a>

                {{-- EDITAR directo (sin PIN) --}}
                <a class="icon-btn"
                   href="{{ route('clientes.edit', $cliente->id) }}"
                   title="Editar">
                  <i class="fa-solid fa-pen"></i>
                </a>

                {{-- ELIMINAR con PIN --}}
                <button type="button"
                  class="icon-btn danger btn-pin-eliminar"
                  data-nombre="{{ $cliente->nombre }} {{ $cliente->apellido }}"
                  data-action="{{ route('clientes.destroy', $cliente->id) }}"
                  title="Eliminar">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" style="text-align:center;padding:48px;color:var(--muted);">No hay clientes registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div id="empty-state" class="empty-state" style="display:none;margin-top:16px;">
    <div class="ic"><i class="fa-solid fa-folder-open"></i></div>
    <h3>No encontramos resultados</h3>
    <p>Intenta con otras palabras o limpia los filtros.</p>
  </div>

</div>{{-- fin .page --}}

{{-- Formulario oculto para eliminar --}}
<form id="formEliminarOculto" method="POST" style="display:none;">
  @csrf
  @method('DELETE')
</form>

<script>
document.addEventListener('DOMContentLoaded', function(){

  /* ============================================================
     BÚSQUEDA / FILTROS / VISTAS
  ============================================================ */
  const input        = document.getElementById('searchCliente');
  const searchBox    = document.getElementById('searchBox');
  const clearBtn     = document.getElementById('clearSearch');
  const selAsesor    = document.getElementById('filterAsesor');
  const selPromo     = document.getElementById('filterPromo');
  const orderBy      = document.getElementById('orderBy');
  const btnCards     = document.getElementById('btnViewCards');
  const btnTable     = document.getElementById('btnViewTable');
  const viewCards    = document.getElementById('view-cards');
  const viewTable    = document.getElementById('view-table');
  const emptyState   = document.getElementById('empty-state');
  const resCount     = document.getElementById('resCount');
  const contadorTop  = document.getElementById('contador-clientes');

  const norm = s => (s||'').toString()
    .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
    .toLowerCase().replace(/\s+/g,' ').trim();

  function setView(mode){
    const isCards = mode === 'cards';
    viewCards.style.display = isCards ? '' : 'none';
    viewTable.style.display = isCards ? 'none' : '';
    btnCards.classList.toggle('active', isCards);
    btnTable.classList.toggle('active', !isCards);
    localStorage.setItem('clientes_view', mode);
  }
  btnCards.addEventListener('click', () => setView('cards'));
  btnTable.addEventListener('click', () => setView('table'));
  setView(localStorage.getItem('clientes_view') || 'table');

  const cards = Array.from(viewCards.querySelectorAll('.card-c'));
  const trs   = Array.from(viewTable.querySelectorAll('#lista-clientes tr[data-search]'));

  function clearHighlights(){
    document.querySelectorAll('.name-text').forEach(el => { el.innerHTML = el.textContent; });
  }
  function highlight(term){
    if(!term) return;
    const re = new RegExp('(' + term.replace(/[.*+?^${}()|[\]\\]/g,'\\$&') + ')','gi');
    document.querySelectorAll('.name-text').forEach(el => {
      el.innerHTML = el.textContent.replace(re, '<mark class="hl">$1</mark>');
    });
  }

  function applyFilters(){
    const q      = norm(input.value);
    const fAse   = (selAsesor.value || '').trim().toLowerCase();
    const fPromo = selPromo.value;
    let visibleC = 0, visibleT = 0;

    clearHighlights();

    const passes = (el) => {
      const matchText   = !q || norm(el.dataset.search).includes(q);
      const matchAsesor = !fAse || norm(el.dataset.asesor) === fAse;
      const matchPromo  = !fPromo || el.dataset.promo === fPromo;
      return matchText && matchAsesor && matchPromo;
    };

    cards.forEach(el => { const show = passes(el); el.style.display = show ? '' : 'none'; if(show) visibleC++; });
    trs.forEach(el   => { const show = passes(el); el.style.display = show ? '' : 'none'; if(show) visibleT++; });

    if(q) highlight(input.value.trim());

    const visible = Math.max(visibleC, visibleT);
    resCount.textContent = visible;
    if(contadorTop) contadorTop.textContent = visible;
    emptyState.style.display = visible === 0 ? 'block' : 'none';
    searchBox.classList.toggle('has-text', !!input.value);
  }

  function sortItems(mode){
    const sorter = (a,b) => {
      if(mode === 'nombre') return (a.dataset.nombre||'').localeCompare(b.dataset.nombre||'','es');
      if(mode === 'asesor') return (a.dataset.asesor||'').localeCompare(b.dataset.asesor||'','es');
      if(mode === 'nuevo')  return (parseInt(b.dataset.created)||0) - (parseInt(a.dataset.created)||0);
      return 0;
    };
    if(mode !== 'default'){
      cards.sort(sorter).forEach(el => viewCards.appendChild(el));
      const tbody = document.getElementById('lista-clientes');
      trs.sort(sorter).forEach(el => tbody.appendChild(el));
    }
  }
  orderBy.addEventListener('change', () => sortItems(orderBy.value));

  const debounce = (fn,w) => { let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), w); }; };
  input.addEventListener('input', debounce(applyFilters, 200));
  input.addEventListener('search', applyFilters);
  selAsesor.addEventListener('change', applyFilters);
  selPromo.addEventListener('change', applyFilters);
  clearBtn.addEventListener('click', () => { input.value = ''; applyFilters(); input.focus(); });

  document.addEventListener('keydown', e => {
    if((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k'){
      e.preventDefault(); input.focus(); input.select();
    }
  });

  sortItems('nuevo');

  /* ============================================================
     TOASTS
  ============================================================ */
  const Toast = Swal.mixin({
    toast:true, position:'top-end', showConfirmButton:false,
    timer:3400, timerProgressBar:true
  });
  @if(session('success'))
    Toast.fire({ icon:'success', title:'Éxito', text:@json(session('success')) });
  @endif
  @if(session('error'))
    Toast.fire({ icon:'error', title:'Ocurrió un problema', text:@json(session('error')) });
  @endif

  /* ============================================================
     MODAL PIN DE AUTORIZACIÓN (solo Eliminar)
  ============================================================ */
  const PIN_CORRECTO  = '{{ env("APROBACION_PIN") }}';

  const overlay   = document.getElementById('pinOverlay');
  const pinInputs = Array.from(document.querySelectorAll('[data-pin]'));
  const pinHint   = document.getElementById('pinHint');
  const pinClose  = document.getElementById('pinClose');

  let pinCallback  = null;
  let pinBloqueado = false;

  function resetPin(){
    pinInputs.forEach(i => {
      i.value = '';
      i.classList.remove('success','error');
    });
    pinHint.innerHTML = 'Puedes <strong style="color:#333;">pegar</strong> el PIN completo.';
    pinBloqueado = false;
  }

  function abrirPin(callback){
    pinCallback = callback;
    resetPin();
    overlay.classList.add('active');
    pinInputs[0].focus();
  }

  function cerrarPin(){
    overlay.classList.remove('active');
    pinCallback = null;
    resetPin();
  }

  function verificarPin(){
    if(pinBloqueado) return;
    const ingresado = pinInputs.map(i => i.value).join('');
    if(ingresado.length < 6) return;

    pinBloqueado = true;

    if(ingresado === String(PIN_CORRECTO)){
      pinInputs.forEach(i => i.classList.add('success'));
      pinHint.innerHTML = '<span style="color:#15803d;font-weight:700;"><i class="fa-solid fa-check" style="margin-right:6px;"></i>Autorizado</span>';
      setTimeout(() => {
        const cb = pinCallback;
        cerrarPin();
        if(cb) cb();
      }, 450);
    } else {
      pinInputs.forEach(i => i.classList.add('error'));
      pinHint.innerHTML = '<span style="color:#ff4a4a;font-weight:700;"><i class="fa-solid fa-xmark" style="margin-right:6px;"></i>PIN incorrecto. Intenta de nuevo.</span>';
      setTimeout(() => {
        pinInputs.forEach(i => { i.classList.remove('error'); i.value = ''; });
        pinHint.innerHTML = 'Puedes <strong style="color:#333;">pegar</strong> el PIN completo.';
        pinBloqueado = false;
        pinInputs[0].focus();
      }, 1300);
    }
  }

  /* Navegación entre cajas del PIN */
  pinInputs.forEach((inp, i) => {
    inp.addEventListener('input', () => {
      inp.value = inp.value.replace(/\D/g,'').slice(-1);
      if(inp.value && i < pinInputs.length - 1) pinInputs[i+1].focus();
      verificarPin();
    });
    inp.addEventListener('keydown', e => {
      if(e.key === 'Backspace' && !inp.value && i > 0) pinInputs[i-1].focus();
      if(e.key === 'Escape') cerrarPin();
    });
    inp.addEventListener('paste', e => {
      e.preventDefault();
      const pasted = (e.clipboardData || window.clipboardData)
        .getData('text').replace(/\D/g,'').slice(0,6);
      pasted.split('').forEach((ch, j) => { if(pinInputs[j]) pinInputs[j].value = ch; });
      const last = Math.min(pasted.length, pinInputs.length) - 1;
      if(pinInputs[last]) pinInputs[last].focus();
      verificarPin();
    });
  });

  pinClose.addEventListener('click', cerrarPin);

  overlay.addEventListener('click', e => {
    if(e.target === overlay) cerrarPin();
  });

  document.addEventListener('keydown', e => {
    if(e.key === 'Escape' && overlay.classList.contains('active')) cerrarPin();
  });

  /* ============================================================
     BOTONES ELIMINAR → PEDIR PIN
  ============================================================ */
  document.querySelectorAll('.btn-pin-eliminar').forEach(btn => {
    btn.addEventListener('click', function(){
      const nombre = this.dataset.nombre || 'este cliente';
      const action = this.dataset.action;

      abrirPin(() => {
        Swal.fire({
          icon: 'warning',
          title: 'Eliminar cliente',
          html: `Vas a eliminar a <b>${nombre}</b>.<br>Esta acción no se puede deshacer.`,
          showCancelButton: true,
          confirmButtonText: 'Sí, eliminar',
          cancelButtonText: 'Cancelar',
          reverseButtons: true,
          focusCancel: true,
          confirmButtonColor: '#ff4a4a'
        }).then(r => {
          if(r.isConfirmed){
            const form = document.getElementById('formEliminarOculto');
            form.action = action;
            form.submit();
          }
        });
      });
    });
  });

  /* ============================================================
     EXPORTAR A EXCEL
  ============================================================ */
  document.getElementById('exportExcel').addEventListener('click', function(e) {
    e.preventDefault();
    
    // Determinar qué vista está activa
    const isCardsView = document.getElementById('view-cards').style.display !== 'none';
    let clientesData = [];
    
    if (isCardsView) {
      // Obtener datos de la vista Cards
      const cards = document.querySelectorAll('#view-cards .card-c');
      cards.forEach(card => {
        if (card.style.display === 'none') return;
        
        const nombre = card.querySelector('.name-text')?.textContent.trim() || '—';
        const telefono = card.querySelector('.rowx .v')?.textContent.trim() || '—';
        const asesor = card.querySelector('.info-who .meta')?.textContent.replace('#', '').trim() || '—';
        const promo = card.querySelector('.pill')?.textContent.trim() || 'NO';
        const categoria = card.querySelector('.pill-cat')?.textContent.trim() || 'Sin categoría';
        const congreso = card.querySelector('.rowx .v')?.textContent.trim() || '—';
        
        clientesData.push({ nombre, telefono, asesor, promo, categoria, congreso });
      });
    } else {
      // Obtener datos de la vista Tabla
      const rows = document.querySelectorAll('#lista-clientes tr[data-search]');
      rows.forEach(row => {
        if (row.style.display === 'none') return;
        
        const cells = row.querySelectorAll('td');
        if (cells.length < 6) return;
        
        const nombre = cells[1]?.querySelector('.name-text')?.textContent.trim() || cells[1]?.textContent.trim() || '—';
        const telefono = cells[1]?.querySelector('.nombre-sub')?.textContent.trim() || '—';
        const asesor = cells[2]?.textContent.trim() || '—';
        const promo = cells[3]?.textContent.trim() || 'NO';
        const congreso = cells[4]?.textContent.trim() || '—';
        const categoria = cells[5]?.textContent.trim() || 'Sin categoría';
        
        clientesData.push({ nombre, telefono, asesor, promo, categoria, congreso });
      });
    }
    
    if (clientesData.length === 0) {
      Swal.fire({
        icon: 'info',
        title: 'Sin datos para exportar',
        text: 'No hay clientes visibles para exportar.',
        confirmButtonColor: '#007aff'
      });
      return;
    }
    
    // Preparar datos para Excel
    const excelData = [
      ['Cliente', 'Teléfono', 'Asesor', 'Promoción', 'Categoría', 'Congreso']
    ];
    
    clientesData.forEach(c => {
      excelData.push([
        c.nombre,
        c.telefono,
        c.asesor,
        c.promo,
        c.categoria,
        c.congreso
      ]);
    });
    
    try {
      // Crear libro de trabajo
      const wb = XLSX.utils.book_new();
      const ws = XLSX.utils.aoa_to_sheet(excelData);
      
      // Configurar ancho de columnas
      ws['!cols'] = [
        { wch: 45 }, // Cliente
        { wch: 20 }, // Teléfono
        { wch: 25 }, // Asesor
        { wch: 15 }, // Promoción
        { wch: 25 }, // Categoría
        { wch: 30 }  // Congreso
      ];
      
      XLSX.utils.book_append_sheet(wb, ws, 'Clientes');
      
      // Generar y descargar
      const filename = `clientes_${new Date().toISOString().split('T')[0]}.xlsx`;
      XLSX.writeFile(wb, filename);
      
      // Mostrar notificación de éxito
      Toast.fire({
        icon: 'success',
        title: 'Exportación completada',
        text: `Se exportaron ${clientesData.length} clientes.`
      });
    } catch (error) {
      console.error('Error de exportación:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error al exportar',
        text: 'Ocurrió un error al generar el archivo Excel.',
        confirmButtonColor: '#ff4a4a'
      });
    }
  });

});
</script>
@endsection