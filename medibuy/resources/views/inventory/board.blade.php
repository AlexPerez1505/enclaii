@extends('layouts.app')
@section('title','Activos e Inventario')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<style>
  :root{
    --bg:#f6f8fc;
    --card:#ffffff;
    --ink:#0f172a;
    --muted:#64748b;
    --line:#e6edf5;

    --teal:#13998f;
    --teal-soft:#e8faf8;

    --green:#16a34a;
    --green-soft:#dcfce7;

    --blue:#2563eb;
    --blue-soft:#dbeafe;

    --amber:#d97706;
    --amber-soft:#fef3c7;

    --red:#ef4444;
    --red-soft:#fee2e2;

    --gray-soft:#f1f5f9;
    --radius-xl:20px;
    --radius-lg:16px;
    --radius-md:12px;
    --shadow:0 10px 24px rgba(15,23,42,.05);
  }

  html,body{
    background:var(--bg);
    color:var(--ink);
    overflow-x:hidden;
  }

  .inventory-page{
    width:100%;
    max-width:none;
    margin:0;
    padding:22px 18px 34px;
  }

  .page-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    flex-wrap:wrap;
    margin-bottom:16px;
  }

  .page-head-left{
    display:flex;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
  }

  .back-btn{
    height:40px;
    border-radius:999px;
    padding:0 14px;
    background:#fff;
    border:1px solid #dbe4ef;
    color:#334155;
    display:inline-flex;
    align-items:center;
    gap:8px;
    text-decoration:none;
    font-size:13px;
    font-weight:700;
    box-shadow:0 6px 16px rgba(15,23,42,.04);
  }

  .back-btn:hover{
    color:#0f172a;
    background:#fff;
  }

  .page-title{
    margin:0;
    font-size:22px;
    font-weight:800;
    letter-spacing:-.03em;
    color:#0b1f44;
  }

  .page-sub{
    margin:3px 0 0;
    color:var(--muted);
    font-size:13px;
  }

  .btn-new{
    border:none;
    background:var(--teal);
    color:#fff;
    padding:10px 15px;
    border-radius:10px;
    font-weight:700;
    font-size:13px;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    gap:8px;
    box-shadow:0 10px 20px rgba(19,153,143,.14);
  }

  .btn-new:hover{
    color:#fff;
    opacity:.97;
  }

  .top-tabs{
    display:flex;
    gap:0;
    background:#eef2f7;
    border-radius:12px;
    padding:4px;
    width:100%;
    max-width:420px;
    margin-bottom:14px;
  }

  .top-tabs .tab-btn{
    flex:1;
    border:none;
    background:transparent;
    color:#64748b;
    font-weight:700;
    font-size:14px;
    padding:9px 14px;
    border-radius:10px;
    transition:.2s ease;
  }

  .top-tabs .tab-btn.active{
    background:#fff;
    color:#0f172a;
    box-shadow:0 2px 8px rgba(15,23,42,.05);
  }

  .filters-wrap{
    background:#fff;
    border:1px solid var(--line);
    border-radius:18px;
    box-shadow:var(--shadow);
    padding:14px;
    margin-bottom:16px;
  }

  .search-box{
    position:relative;
  }

  .search-box .bi{
    position:absolute;
    left:14px;
    top:50%;
    transform:translateY(-50%);
    color:#94a3b8;
    font-size:17px;
  }

  .search-box input{
    padding-left:42px;
    height:46px;
    border-radius:12px;
    border:1px solid #d9e2ec;
    background:#fff;
    font-size:14px;
  }

  .search-box input:focus,
  .filter-select:focus{
    border-color:#b9d8ff;
    box-shadow:0 0 0 4px rgba(37,99,235,.08);
  }

  .filter-select{
    height:46px;
    border-radius:12px;
    border:1px solid #d9e2ec;
    background:#fff;
    font-size:14px;
  }

  .cards-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(290px,1fr));
    gap:16px;
  }

  .asset-card{
    background:#fff;
    border:1px solid #dfe7f0;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 6px 14px rgba(15,23,42,.05);
    cursor:pointer;
    transition:transform .18s ease, box-shadow .18s ease;
  }

  .asset-card:hover{
    transform:translateY(-2px);
    box-shadow:0 14px 24px rgba(15,23,42,.09);
  }

  .asset-media{
    position:relative;
    height:170px;
    background:#dfe6ee;
    overflow:hidden;
  }

  .asset-media img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
  }

  .asset-media-placeholder{
    width:100%;
    height:100%;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#94a3b8;
    font-size:54px;
    background:#eef2f7;
  }

  .top-badges{
    position:absolute;
    top:10px;
    left:10px;
    right:10px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:10px;
  }

  .chip-cat{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:rgba(17,24,39,.62);
    color:#fff;
    border-radius:999px;
    padding:6px 10px;
    font-size:11px;
    font-weight:700;
    backdrop-filter:blur(6px);
  }

  .chip-state{
    display:inline-flex;
    align-items:center;
    gap:6px;
    border-radius:999px;
    padding:6px 10px;
    font-size:11px;
    font-weight:700;
    background:#fff;
    border:1px solid rgba(255,255,255,.75);
  }

  .state-disponible{ color:#0f9f5d; background:#ecfdf3; }
  .state-asignado{ color:#2563eb; background:#e8f0ff; }
  .state-en_reparacion{ color:#d97706; background:#fff6db; }
  .state-dado_de_baja{ color:#ef4444; background:#fff0f0; }

  .asset-body{
    padding:14px 14px 15px;
  }

  .asset-name{
    margin:0 0 2px;
    font-size:16px;
    font-weight:800;
    color:#0f172a;
    line-height:1.25;
  }

  .asset-model{
    margin:0;
    color:#64748b;
    font-size:13px;
    line-height:1.4;
  }

  .asset-tag{
    margin:8px 0 10px;
    color:#94a3b8;
    font-size:12px;
    font-weight:700;
  }

  .asset-loc{
    display:flex;
    align-items:center;
    gap:6px;
    color:#94a3b8;
    font-size:13px;
  }

  .stock-meta{
    margin-top:10px;
  }

  .stock-line{
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-size:13px;
    margin-bottom:4px;
    color:#475569;
  }

  .stock-line strong{
    color:#0f172a;
  }

  .stock-bar{
    height:8px;
    border-radius:999px;
    background:#ece8bf;
    overflow:hidden;
  }

  .stock-fill{
    height:100%;
    border-radius:999px;
  }

  .fill-green{ background:#22c55e; }
  .fill-amber{ background:#eab308; }
  .fill-red{ background:#ef4444; }

  .stock-min{
    margin-top:6px;
    font-size:12px;
    color:#ef4444;
    font-weight:600;
  }

  .empty-state{
    background:#fff;
    border:1px dashed #d8e2ef;
    border-radius:18px;
    padding:40px 22px;
    text-align:center;
    color:#94a3b8;
  }

  .d-none-force{ display:none !important; }

  .screen-overlay{
    position:fixed;
    inset:0;
    background:rgba(15,23,42,.58);
    opacity:0;
    visibility:hidden;
    transition:.22s ease;
    z-index:1040;
  }

  .screen-overlay.show{
    opacity:1;
    visibility:visible;
  }

  .custom-drawer{
    position:fixed;
    top:0;
    right:0;
    width:min(470px, 100vw);
    height:100vh;
    background:#fff;
    border-left:1px solid var(--line);
    z-index:1050;
    transform:translateX(100%);
    transition:transform .25s ease;
    display:flex;
    flex-direction:column;
    box-shadow:-8px 0 30px rgba(15,23,42,.12);
  }

  .custom-drawer.show{
    transform:translateX(0);
  }

  .drawer-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:18px 22px 12px;
    border-bottom:1px solid #eef2f7;
    flex:0 0 auto;
  }

  .drawer-title{
    font-size:18px;
    font-weight:800;
    margin:0;
  }

  .drawer-close{
    width:38px;
    height:38px;
    border-radius:10px;
    border:1px solid #d1d5db;
    background:#fff;
    color:#111827;
    display:grid;
    place-items:center;
    font-size:18px;
  }

  .drawer-body{
    padding:18px 22px 22px;
    overflow:auto;
    flex:1 1 auto;
  }

  .drawer-img{
    width:100%;
    height:188px;
    border-radius:16px;
    overflow:hidden;
    background:#edf2f7;
    margin-bottom:16px;
  }

  .drawer-img img{
    width:100%;
    height:100%;
    object-fit:cover;
  }

  .drawer-placeholder{
    width:100%;
    height:100%;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#c0cad7;
    font-size:58px;
    background:#eef2f7;
  }

  .drawer-chips{
    display:flex;
    flex-wrap:wrap;
    gap:8px;
    margin-bottom:18px;
  }

  .drawer-chip{
    border-radius:8px;
    padding:5px 10px;
    font-size:13px;
    font-weight:700;
    line-height:1;
    border:1px solid #e5e7eb;
    background:#fff;
    color:#111827;
  }

  .drawer-chip.status-disponible{ background:#dcfce7; color:#15803d; border-color:#c7f0d2; }
  .drawer-chip.status-asignado{ background:#dbeafe; color:#1d4ed8; border-color:#bfd8fd; }
  .drawer-chip.status-en_reparacion{ background:#fef3c7; color:#b45309; border-color:#fde68a; }
  .drawer-chip.status-dado_de_baja{ background:#fee2e2; color:#b91c1c; border-color:#fecaca; }

  .info-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px 24px;
    margin-bottom:18px;
  }

  .info-label{
    color:#94a3b8;
    font-size:13px;
    margin-bottom:2px;
  }

  .info-value{
    color:#0f172a;
    font-size:15px;
    font-weight:600;
    line-height:1.35;
  }

  .drawer-section-title{
    font-size:14px;
    font-weight:800;
    color:#111827;
    margin-bottom:6px;
  }

  .drawer-desc{
    color:#475569;
    font-size:14px;
    line-height:1.5;
    margin-bottom:18px;
  }

  .stock-box{
    background:#f8fafc;
    border:1px solid #eef2f7;
    border-radius:16px;
    padding:16px;
    margin-bottom:18px;
  }

  .stock-box-title{
    font-size:14px;
    font-weight:800;
    margin-bottom:14px;
    color:#111827;
  }

  .stock-box-row{
    display:flex;
    justify-content:space-between;
    gap:8px;
    font-size:14px;
    color:#334155;
    margin-bottom:10px;
  }

  .drawer-actions{
    display:grid;
    grid-template-columns:1fr 56px 56px;
    gap:10px;
    padding-top:14px;
    border-top:1px solid #e5e7eb;
  }

  .drawer-actions.no-qr{
    grid-template-columns:1fr 56px;
  }

  .btn-drawer-edit{
    border:none;
    background:var(--teal);
    color:#fff;
    font-weight:800;
    border-radius:8px;
    height:46px;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    text-decoration:none;
  }

  .btn-drawer-icon{
    border:1px solid #e5e7eb;
    background:#fff;
    color:#111827;
    border-radius:8px;
    height:46px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
  }

  .btn-drawer-delete{
    border:none;
    background:#ef4444;
    color:#fff;
    border-radius:8px;
    height:46px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
    width:100%;
  }

  .qr-card{
    border:1px solid #1f2937;
    border-radius:14px;
    overflow:hidden;
    background:#fff;
  }

  .qr-card-head{
    background:linear-gradient(135deg,#0f172a,#16233f);
    color:#fff;
    padding:12px 14px;
  }

  .qr-card-head h6{
    margin:0;
    font-size:12px;
    font-weight:800;
    letter-spacing:.16em;
    text-transform:uppercase;
  }

  .qr-card-head small{
    opacity:.8;
    font-size:11px;
  }

  .qr-card-body{
    padding:14px;
  }

  .qr-box-wrap{
    display:grid;
    grid-template-columns:110px 1fr;
    gap:14px;
    align-items:start;
  }

  #qrCodeBox{
    width:106px;
    height:106px;
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:8px;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
  }

  #qrCodeBox img,
  #qrCodeBox canvas{
    max-width:100%;
    max-height:100%;
  }

  .qr-name{
    font-size:24px;
    font-weight:800;
    color:#0f172a;
    margin-bottom:2px;
  }

  .qr-meta{
    color:#64748b;
    font-size:13px;
    margin-bottom:6px;
  }

  .qr-mini{
    color:#0f172a;
    font-size:12px;
    margin-bottom:3px;
  }

  .qr-foot{
    margin-top:12px;
    padding-top:10px;
    border-top:1px solid #e5e7eb;
    display:flex;
    justify-content:space-between;
    color:#94a3b8;
    font-size:11px;
  }

  .btn-print{
    width:100%;
    height:48px;
    border:none;
    border-radius:10px;
    background:#1e2d4a;
    color:#fff;
    font-weight:800;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    margin-top:14px;
  }

  @media (max-width: 767.98px){
    .inventory-page{
      padding:18px 14px 28px;
    }

    .cards-grid{
      grid-template-columns:1fr;
    }

    .info-grid{
      grid-template-columns:1fr;
    }

    .qr-box-wrap{
      grid-template-columns:1fr;
    }

    #qrCodeBox{
      margin:0 auto;
    }

    .custom-drawer{
      width:100vw;
    }
  }
</style>

@php
  $fixedAssets = $fixedAssets ?? collect();
  $consumables = $consumables ?? collect();
  $fixedCount = $fixedCount ?? $fixedAssets->count();
  $consumableCount = $consumableCount ?? $consumables->count();

  function statusBadgeClass($status){
      return match($status){
          'asignado' => 'state-asignado',
          'en_reparacion' => 'state-en_reparacion',
          'dado_de_baja' => 'state-dado_de_baja',
          default => 'state-disponible'
      };
  }

  function statusDrawerClass($status){
      return match($status){
          'asignado' => 'status-asignado',
          'en_reparacion' => 'status-en_reparacion',
          'dado_de_baja' => 'status-dado_de_baja',
          default => 'status-disponible'
      };
  }

  function statusLabel($status){
      return match($status){
          'asignado' => 'Asignado',
          'en_reparacion' => 'Mantenimiento',
          'dado_de_baja' => 'Baja',
          default => 'Disponible'
      };
  }
@endphp

<div class="inventory-page">
  <div class="page-head">
    <div class="page-head-left">
      <a href="{{ url('/internal-assets') }}" class="back-btn">
        <i class="bi bi-arrow-left"></i>
        <span>Regresar</span>
      </a>

      <div>
        <h1 class="page-title">Activos e Inventario</h1>
        <p class="page-sub">{{ $fixedCount + $consumableCount }} elementos registrados</p>
      </div>
    </div>

    <a href="{{ route('assets.create') }}" class="btn-new">
      <i class="bi bi-plus-lg"></i>
      <span>Nuevo</span>
    </a>
  </div>

  <div class="top-tabs">
    <button type="button" class="tab-btn active" data-tab="activo_fijo">Activos Fijos</button>
    <button type="button" class="tab-btn" data-tab="consumible">Consumibles / Stock</button>
  </div>

  <div class="filters-wrap">
    <div class="row g-3 align-items-center">
      <div class="col-lg-6">
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input type="text" id="boardSearch" class="form-control" placeholder="Buscar por nombre o marca...">
        </div>
      </div>

      <div class="col-md-6 col-lg-3">
        <select id="boardCategory" class="form-select filter-select">
          <option value="">Todas las categorías</option>
          @foreach($categories as $cat)
            <option value="{{ strtolower($cat->name) }}">{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-6 col-lg-3">
        <select id="boardStatus" class="form-select filter-select">
          <option value="">Todos</option>
          <option value="disponible">Disponible</option>
          <option value="asignado">Asignado</option>
          <option value="en_reparacion">Mantenimiento</option>
          <option value="dado_de_baja">Baja</option>
          <option value="bajo_stock">Bajo stock</option>
        </select>
      </div>
    </div>
  </div>

  <div class="tab-panel" id="panel-activo_fijo">
    @if($fixedAssets->isEmpty())
      <div class="empty-state">
        <i class="bi bi-laptop" style="font-size:38px;"></i>
        <div class="mt-2">No hay activos fijos registrados.</div>
      </div>
    @else
      <div class="cards-grid">
        @foreach($fixedAssets as $item)
          @php
            $status = $item->asset_status ?: 'disponible';
            $tag = $item->serial_number ?: ('ID-'.$item->id);
          @endphp

          <div class="asset-card js-item-card"
               data-tab="activo_fijo"
               data-id="{{ $item->id }}"
               data-name="{{ $item->name }}"
               data-type="{{ $item->type }}"
               data-type-label="Activo Fijo"
               data-category="{{ $item->category->name ?? 'Sin categoría' }}"
               data-status="{{ $status }}"
               data-status-label="{{ statusLabel($status) }}"
               data-status-drawer-class="{{ statusDrawerClass($status) }}"
               data-brand="{{ $item->brand ?? '—' }}"
               data-model="{{ $item->model ?? '—' }}"
               data-serial="{{ $item->serial_number ?? '—' }}"
               data-location="{{ $item->location ?? 'Sin ubicación' }}"
               data-notes="{{ $item->notes ?? 'Sin descripción' }}"
               data-unit="{{ $item->unit ?? 'pieza' }}"
               data-stock="{{ (int)($item->stock ?? 0) }}"
               data-stock-min="{{ (int)($item->stock_min ?? 0) }}"
               data-stock-max="{{ (int)($item->stock_max ?? 0) }}"
               data-condition="{{ $item->condition ?? '—' }}"
               data-photo="{{ $item->photo ? asset('storage/'.$item->photo) : '' }}"
               data-edit-url="{{ route('assets.edit', $item->id) }}"
               data-delete-url="{{ route('assets.destroy', $item->id) }}"
               data-qr-text="{{ route('assets.board').'?item='.$item->id }}"
               data-tag="{{ $tag }}"
               data-is-consumable="0"
               data-search="{{ strtolower(trim(($item->name ?? '').' '.($item->brand ?? '').' '.($item->model ?? '').' '.($item->serial_number ?? ''))) }}">
              <div class="asset-media">
                @if($item->photo)
                  <img src="{{ asset('storage/'.$item->photo) }}" alt="{{ $item->name }}">
                @else
                  <div class="asset-media-placeholder">
                    <i class="bi bi-laptop"></i>
                  </div>
                @endif

                <div class="top-badges">
                  <span class="chip-cat">
                    <i class="bi bi-pc-display-horizontal"></i>
                    {{ $item->category->name ?? 'Activo' }}
                  </span>

                  <span class="chip-state {{ statusBadgeClass($status) }}">
                    <i class="bi bi-dot"></i>
                    {{ statusLabel($status) }}
                  </span>
                </div>
              </div>

              <div class="asset-body">
                <h3 class="asset-name">{{ $item->name }}</h3>
                <p class="asset-model">{{ $item->brand }} · {{ $item->model }}</p>
                <div class="asset-tag">{{ $tag }}</div>
                <div class="asset-loc">
                  <i class="bi bi-geo-alt"></i>
                  <span>{{ $item->location ?: 'Sin ubicación' }}</span>
                </div>
              </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  <div class="tab-panel d-none-force" id="panel-consumible">
    @if($consumables->isEmpty())
      <div class="empty-state">
        <i class="bi bi-box-seam" style="font-size:38px;"></i>
        <div class="mt-2">No hay consumibles registrados.</div>
      </div>
    @else
      <div class="cards-grid">
        @foreach($consumables as $item)
          @php
            $stock = (int)($item->stock ?? 0);
            $max = max(1, (int)($item->stock_max ?? 1));
            $min = (int)($item->stock_min ?? 0);
            $pct = max(0, min(100, round(($stock / $max) * 100)));
            $fillClass = $stock <= $min ? 'fill-red' : ($pct <= 40 ? 'fill-amber' : 'fill-green');
            $stockBadge = $stock <= $min ? 'bajo_stock' : 'disponible';
          @endphp

          <div class="asset-card js-item-card"
               data-tab="consumible"
               data-id="{{ $item->id }}"
               data-name="{{ $item->name }}"
               data-type="{{ $item->type }}"
               data-type-label="Consumible"
               data-category="{{ $item->category->name ?? 'Sin categoría' }}"
               data-status="{{ $stockBadge }}"
               data-status-label="Disponible"
               data-status-drawer-class="status-disponible"
               data-brand="{{ $item->brand ?? '—' }}"
               data-model="{{ $item->model ?? '—' }}"
               data-serial="{{ $item->serial_number ?? '—' }}"
               data-location="{{ $item->location ?? 'Sin ubicación' }}"
               data-notes="{{ $item->notes ?? 'Sin descripción' }}"
               data-unit="{{ $item->unit ?? 'pieza' }}"
               data-stock="{{ $stock }}"
               data-stock-min="{{ $min }}"
               data-stock-max="{{ $max }}"
               data-condition="{{ $item->condition ?? '—' }}"
               data-photo="{{ $item->photo ? asset('storage/'.$item->photo) : '' }}"
               data-edit-url="{{ route('assets.edit', $item->id) }}"
               data-delete-url="{{ route('assets.destroy', $item->id) }}"
               data-qr-text=""
               data-tag="ID-{{ $item->id }}"
               data-is-consumable="1"
               data-search="{{ strtolower(trim(($item->name ?? '').' '.($item->brand ?? '').' '.($item->model ?? '').' '.($item->serial_number ?? ''))) }}">
            <div class="asset-media">
              @if($item->photo)
                <img src="{{ asset('storage/'.$item->photo) }}" alt="{{ $item->name }}">
              @else
                <div class="asset-media-placeholder">
                  <i class="bi bi-box-seam"></i>
                </div>
              @endif

              <div class="top-badges">
                <span class="chip-cat">
                  <i class="bi bi-box2"></i>
                  {{ $item->category->name ?? 'Consumible' }}
                </span>

                <span class="chip-state state-disponible">
                  <i class="bi bi-dot"></i>
                  Disponible
                </span>
              </div>
            </div>

            <div class="asset-body">
              <h3 class="asset-name">{{ $item->name }}</h3>
              <p class="asset-model">{{ $item->brand ?: ($item->category->name ?? 'Consumible') }}</p>

              <div class="asset-loc mb-2">
                <i class="bi bi-geo-alt"></i>
                <span>{{ $item->location ?: 'Sin ubicación' }}</span>
              </div>

              <div class="stock-meta">
                <div class="stock-line">
                  <span>Stock</span>
                  <strong>
                    @if($stock <= $min)
                      <i class="bi bi-exclamation-triangle text-danger"></i>
                    @endif
                    {{ $stock }} / {{ $max }} {{ $item->unit ?: 'piezas' }}
                  </strong>
                </div>
                <div class="stock-bar">
                  <div class="stock-fill {{ $fillClass }}" style="width:{{ $pct }}%;"></div>
                </div>
                @if($stock <= $min)
                  <div class="stock-min">Mínimo: {{ $min }} {{ $item->unit ?: 'piezas' }}</div>
                @endif
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</div>

<div class="screen-overlay" id="screenOverlay"></div>

<div class="custom-drawer" id="itemDrawer">
  <div class="drawer-head">
    <h5 class="drawer-title" id="itemDrawerLabel">Detalle</h5>
    <button type="button" class="drawer-close" id="drawerCloseBtn">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <div class="drawer-body">
    <div class="drawer-img" id="drawerImageWrap">
      <div class="drawer-placeholder">
        <i class="bi bi-image"></i>
      </div>
    </div>

    <div class="drawer-chips">
      <span class="drawer-chip" id="drawerStatusChip">Disponible</span>
      <span class="drawer-chip" id="drawerTypeChip">Activo Fijo</span>
      <span class="drawer-chip" id="drawerCategoryChip">Categoría</span>
    </div>

    <div class="info-grid" id="fixedInfoGrid">
      <div>
        <div class="info-label">Marca</div>
        <div class="info-value" id="drawerBrand">—</div>
      </div>
      <div>
        <div class="info-label">Modelo</div>
        <div class="info-value" id="drawerModel">—</div>
      </div>
      <div>
        <div class="info-label">No. Serie</div>
        <div class="info-value" id="drawerSerial">—</div>
      </div>
      <div>
        <div class="info-label">Ubicación</div>
        <div class="info-value" id="drawerLocation">—</div>
      </div>
    </div>

    <div id="consumableStockBox" class="stock-box d-none-force">
      <div class="stock-box-title">Control de Stock</div>

      <div class="stock-box-row">
        <span>Actual: <strong id="drawerStockNow">0</strong></span>
        <span>Máx: <strong id="drawerStockMax">0</strong></span>
      </div>

      <div class="stock-bar mb-2">
        <div id="drawerStockFill" class="stock-fill fill-green" style="width:0%;"></div>
      </div>

      <div class="small text-muted mb-1">
        Mínimo requerido: <span id="drawerStockMin">0</span>
      </div>

      <div id="drawerStockWarning" class="small text-danger fw-semibold d-none-force">
        <i class="bi bi-exclamation-triangle-fill"></i>
        Stock por debajo del mínimo
      </div>
    </div>

    <div class="drawer-section-title">Descripción</div>
    <div class="drawer-desc" id="drawerDescription">Sin descripción</div>

    <div class="drawer-actions" id="drawerActions">
      <a href="#" id="drawerEditBtn" class="btn-drawer-edit">
        <i class="bi bi-pencil"></i>
        <span>Editar</span>
      </a>

      <button type="button" class="btn-drawer-icon" id="drawerQrBtn" data-bs-toggle="modal" data-bs-target="#qrModal">
        <i class="bi bi-qr-code"></i>
      </button>

      <form id="drawerDeleteForm" method="POST" action="">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-drawer-delete" onclick="return confirm('¿Eliminar este registro?')">
          <i class="bi bi-trash"></i>
        </button>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0" style="border-radius:18px; overflow:hidden;">
      <div class="modal-header px-4 pt-4 pb-2 border-0">
        <div>
          <h5 class="modal-title fw-bold" id="qrModalTitle">Etiqueta QR</h5>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body px-4 pt-2 pb-4">
        <div class="qr-card" id="printableQrArea">
          <div class="qr-card-head">
            <h6>Control de Activos</h6>
            <small id="qrHeadCategory">Categoría</small>
          </div>

          <div class="qr-card-body">
            <div class="qr-box-wrap">
              <div id="qrCodeBox"></div>

              <div>
                <div class="qr-name" id="qrName">Activo</div>
                <div class="qr-meta" id="qrSubtitle">Marca / Modelo</div>
                <div class="qr-mini"><strong>S/N:</strong> <span id="qrSerial">—</span></div>
                <div class="qr-mini"><strong>Ubicación:</strong> <span id="qrLocation">—</span></div>
              </div>
            </div>

            <div class="qr-foot">
              <span>ID: <span id="qrIdText">—</span></span>
              <span>Escanea para ver detalle</span>
            </div>
          </div>
        </div>

        <button type="button" class="btn-print" onclick="printQrLabel()">
          <i class="bi bi-printer"></i>
          <span>Imprimir Etiqueta</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  const tabButtons = document.querySelectorAll('.tab-btn');
  const tabPanels = {
    activo_fijo: document.getElementById('panel-activo_fijo'),
    consumible: document.getElementById('panel-consumible')
  };

  let activeTab = 'activo_fijo';

  function setActiveTab(tab){
    activeTab = tab;

    tabButtons.forEach(btn => {
      btn.classList.toggle('active', btn.dataset.tab === tab);
    });

    Object.keys(tabPanels).forEach(key => {
      tabPanels[key].classList.toggle('d-none-force', key !== tab);
    });

    applyBoardFilters();
  }

  tabButtons.forEach(btn => {
    btn.addEventListener('click', () => setActiveTab(btn.dataset.tab));
  });

  const boardSearch = document.getElementById('boardSearch');
  const boardCategory = document.getElementById('boardCategory');
  const boardStatus = document.getElementById('boardStatus');
  const allCards = document.querySelectorAll('.js-item-card');

  function applyBoardFilters(){
    const q = (boardSearch.value || '').trim().toLowerCase();
    const cat = (boardCategory.value || '').trim().toLowerCase();
    const status = (boardStatus.value || '').trim().toLowerCase();

    allCards.forEach(card => {
      const cardTab = card.dataset.tab;
      const cardCat = (card.dataset.category || '').toLowerCase();
      const cardStatus = (card.dataset.status || '').toLowerCase();
      const cardSearch = (card.dataset.search || '').toLowerCase();

      const matchTab = cardTab === activeTab;
      const matchSearch = !q || cardSearch.includes(q);
      const matchCat = !cat || cardCat === cat;
      const matchStatus = !status || cardStatus === status;

      card.classList.toggle('d-none-force', !(matchTab && matchSearch && matchCat && matchStatus));
    });
  }

  boardSearch.addEventListener('input', applyBoardFilters);
  boardCategory.addEventListener('change', applyBoardFilters);
  boardStatus.addEventListener('change', applyBoardFilters);

  setActiveTab('activo_fijo');

  const overlay = document.getElementById('screenOverlay');
  const drawer = document.getElementById('itemDrawer');
  const drawerCloseBtn = document.getElementById('drawerCloseBtn');

  const drawerLabel = document.getElementById('itemDrawerLabel');
  const drawerImageWrap = document.getElementById('drawerImageWrap');
  const drawerStatusChip = document.getElementById('drawerStatusChip');
  const drawerTypeChip = document.getElementById('drawerTypeChip');
  const drawerCategoryChip = document.getElementById('drawerCategoryChip');
  const drawerBrand = document.getElementById('drawerBrand');
  const drawerModel = document.getElementById('drawerModel');
  const drawerSerial = document.getElementById('drawerSerial');
  const drawerLocation = document.getElementById('drawerLocation');
  const drawerDescription = document.getElementById('drawerDescription');
  const drawerEditBtn = document.getElementById('drawerEditBtn');
  const drawerDeleteForm = document.getElementById('drawerDeleteForm');
  const drawerActions = document.getElementById('drawerActions');
  const fixedInfoGrid = document.getElementById('fixedInfoGrid');
  const consumableStockBox = document.getElementById('consumableStockBox');
  const drawerStockNow = document.getElementById('drawerStockNow');
  const drawerStockMax = document.getElementById('drawerStockMax');
  const drawerStockMin = document.getElementById('drawerStockMin');
  const drawerStockFill = document.getElementById('drawerStockFill');
  const drawerStockWarning = document.getElementById('drawerStockWarning');
  const drawerQrBtn = document.getElementById('drawerQrBtn');

  let currentItemData = null;

  function stockFillClass(stock, min, max){
    const pct = max > 0 ? (stock / max) * 100 : 0;
    if (stock <= min) return 'fill-red';
    if (pct <= 40) return 'fill-amber';
    return 'fill-green';
  }

  function openDrawer(){
    overlay.classList.add('show');
    drawer.classList.add('show');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer(){
    overlay.classList.remove('show');
    drawer.classList.remove('show');
    document.body.style.overflow = '';
  }

  function openDrawerWithItem(data){
    currentItemData = data;

    drawerLabel.textContent = data.name || 'Detalle';

    if (data.photo) {
      drawerImageWrap.innerHTML = `<img src="${data.photo}" alt="${data.name || 'imagen'}">`;
    } else {
      drawerImageWrap.innerHTML = `
        <div class="drawer-placeholder">
          <i class="bi ${data.isConsumable ? 'bi-box-seam' : 'bi-laptop'}"></i>
        </div>
      `;
    }

    drawerStatusChip.className = `drawer-chip ${data.statusDrawerClass || 'status-disponible'}`;
    drawerStatusChip.textContent = data.statusLabel || 'Disponible';
    drawerTypeChip.textContent = data.typeLabel || 'Activo';
    drawerCategoryChip.textContent = data.category || 'Categoría';

    drawerBrand.textContent = data.brand || '—';
    drawerModel.textContent = data.model || '—';
    drawerSerial.textContent = data.serial || '—';
    drawerLocation.textContent = data.location || '—';
    drawerDescription.textContent = data.notes || 'Sin descripción';

    drawerEditBtn.href = data.editUrl || '#';
    drawerDeleteForm.action = data.deleteUrl || '#';

    if (data.isConsumable) {
      fixedInfoGrid.classList.add('d-none-force');
      consumableStockBox.classList.remove('d-none-force');
      drawerQrBtn.classList.add('d-none-force');
      drawerActions.classList.add('no-qr');

      const stock = Number(data.stock || 0);
      const min = Number(data.stockMin || 0);
      const max = Number(data.stockMax || 0);
      const pct = max > 0 ? Math.max(0, Math.min(100, Math.round((stock / max) * 100))) : 0;

      drawerStockNow.textContent = `${stock} ${data.unit || ''}`.trim();
      drawerStockMax.textContent = max;
      drawerStockMin.textContent = `${min} ${data.unit || ''}`.trim();
      drawerStockFill.style.width = `${pct}%`;
      drawerStockFill.className = `stock-fill ${stockFillClass(stock, min, max)}`;

      if (stock <= min) {
        drawerStockWarning.classList.remove('d-none-force');
      } else {
        drawerStockWarning.classList.add('d-none-force');
      }
    } else {
      fixedInfoGrid.classList.remove('d-none-force');
      consumableStockBox.classList.add('d-none-force');
      drawerQrBtn.classList.remove('d-none-force');
      drawerActions.classList.remove('no-qr');
    }

    openDrawer();
  }

  function getCardData(card){
    return {
      id: card.dataset.id,
      name: card.dataset.name,
      type: card.dataset.type,
      typeLabel: card.dataset.typeLabel,
      category: card.dataset.category,
      status: card.dataset.status,
      statusLabel: card.dataset.statusLabel,
      statusDrawerClass: card.dataset.statusDrawerClass,
      brand: card.dataset.brand,
      model: card.dataset.model,
      serial: card.dataset.serial,
      location: card.dataset.location,
      notes: card.dataset.notes,
      unit: card.dataset.unit,
      stock: card.dataset.stock,
      stockMin: card.dataset.stockMin,
      stockMax: card.dataset.stockMax,
      condition: card.dataset.condition,
      photo: card.dataset.photo,
      editUrl: card.dataset.editUrl,
      deleteUrl: card.dataset.deleteUrl,
      qrText: card.dataset.qrText,
      tag: card.dataset.tag,
      isConsumable: card.dataset.isConsumable === '1'
    };
  }

  allCards.forEach(card => {
    card.addEventListener('click', function () {
      const data = getCardData(this);
      openDrawerWithItem(data);
    });
  });

  overlay.addEventListener('click', closeDrawer);
  drawerCloseBtn.addEventListener('click', closeDrawer);

  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
      closeDrawer();
    }
  });

  const qrModalTitle = document.getElementById('qrModalTitle');
  const qrHeadCategory = document.getElementById('qrHeadCategory');
  const qrName = document.getElementById('qrName');
  const qrSubtitle = document.getElementById('qrSubtitle');
  const qrSerial = document.getElementById('qrSerial');
  const qrLocation = document.getElementById('qrLocation');
  const qrIdText = document.getElementById('qrIdText');
  const qrCodeBox = document.getElementById('qrCodeBox');

  drawerQrBtn.addEventListener('click', () => {
    if (!currentItemData || currentItemData.isConsumable) return;

    qrModalTitle.textContent = `Etiqueta QR — ${currentItemData.name}`;
    qrHeadCategory.textContent = currentItemData.category || 'Categoría';
    qrName.textContent = currentItemData.name || 'Item';
    qrSubtitle.textContent = `${currentItemData.brand || 'Marca'} / ${currentItemData.model || currentItemData.typeLabel || 'Modelo'}`;
    qrSerial.textContent = currentItemData.serial || currentItemData.tag || '—';
    qrLocation.textContent = currentItemData.location || '—';
    qrIdText.textContent = currentItemData.tag || `ID-${currentItemData.id}`;

    qrCodeBox.innerHTML = '';
    new QRCode(qrCodeBox, {
      text: currentItemData.qrText || `item:${currentItemData.id}`,
      width: 96,
      height: 96,
      correctLevel: QRCode.CorrectLevel.H
    });
  });

  function printQrLabel(){
    const printable = document.getElementById('printableQrArea').outerHTML;
    const win = window.open('', '_blank', 'width=600,height=700');

    win.document.write(`
      <html>
        <head>
          <title>Etiqueta QR</title>
          <style>
            body{font-family:Arial, Helvetica, sans-serif;background:#fff;padding:20px;}
            .qr-card{border:1px solid #1f2937;border-radius:14px;overflow:hidden;background:#fff;}
            .qr-card-head{background:linear-gradient(135deg,#0f172a,#16233f);color:#fff;padding:12px 14px;}
            .qr-card-head h6{margin:0;font-size:12px;font-weight:800;letter-spacing:.16em;text-transform:uppercase;}
            .qr-card-head small{opacity:.8;font-size:11px;}
            .qr-card-body{padding:14px;}
            .qr-box-wrap{display:grid;grid-template-columns:110px 1fr;gap:14px;align-items:start;}
            #qrCodeBox img,#qrCodeBox canvas,canvas,img{max-width:100%;}
            .qr-name{font-size:24px;font-weight:800;color:#0f172a;margin-bottom:2px;}
            .qr-meta{color:#64748b;font-size:13px;margin-bottom:6px;}
            .qr-mini{color:#0f172a;font-size:12px;margin-bottom:3px;}
            .qr-foot{margin-top:12px;padding-top:10px;border-top:1px solid #e5e7eb;display:flex;justify-content:space-between;color:#94a3b8;font-size:11px;}
          </style>
        </head>
        <body>
          ${printable}
          <script>
            window.onload = function(){
              window.print();
              window.onafterprint = function(){ window.close(); }
            }
          <\/script>
        </body>
      </html>
    `);

    win.document.close();
  }
</script>
@endsection
