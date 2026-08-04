@extends('layouts.app')
@section('title', 'Activos')
@section('titulo', 'Gestión de Activos')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

@php
  $serviciosCollection = $servicios instanceof \Illuminate\Pagination\AbstractPaginator
      ? $servicios->getCollection()
      : collect($servicios);

  $resolverAmbito = function($s){
      $raw = strtolower(trim((string)(
          $s->mantenimiento_tipo
          ?? $s->ambito
          ?? $s->tipo_servicio
          ?? $s->interno_externo
          ?? $s->origen
          ?? ''
      )));

      if ($raw !== '') {
          if (str_contains($raw, 'extern') || str_contains($raw, 'fuera')) return 'externos';
          if (str_contains($raw, 'intern')) return 'internos';
      }

      return 'internos';
  };

  $internosCount = $serviciosCollection->filter(fn($s) => $resolverAmbito($s) === 'internos')->count();
  $externosCount = $serviciosCollection->filter(fn($s) => $resolverAmbito($s) === 'externos')->count();
  $totalCount = $internosCount + $externosCount;
@endphp

<style>
:root {
  /* PALETA CORPORATIVA ESTRICTA */
  --bg: #f9fafb; 
  --card: #ffffff; 
  --title: #111111;
  --ink: #333333; 
  --muted: #888888; 
  --line: #ebebeb; 
  --blue: #007aff; 
  --blue-soft: #e6f0ff; 
  --success: #15803d; 
  --success-soft: #e6ffe6;  
  --danger: #ff4a4a; 
  --danger-soft: #ffebeb;

  /* VARIABLES DE DISEÑO MÍNIMO */
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --shadow-base: 0 4px 12px rgba(0,0,0,0.02);
  --shadow-hover: 0 8px 24px rgba(0,0,0,0.06);
  --shadow-focus: 0 0 0 3px var(--blue-soft);
  --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  --font-family: 'Quicksand', sans-serif;
}

/* RESET & BASE */
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  background-color: var(--bg);
  font-family: var(--font-family);
  color: var(--ink);
  -webkit-font-smoothing: antialiased;
}
h1, h2, h3, h4, h5, h6 { color: var(--title); font-weight: 700; margin-bottom: 0.5rem; }

/* LAYOUT */
.premium-wrapper {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px 24px;
}

/* UTILIDADES Y TEXTO */
.text-muted { color: var(--muted); }
.text-center { text-align: center; }
.font-monospace { font-family: monospace; font-size: 0.9em; letter-spacing: 0.05em; color: var(--title); }

/* ANIMACIONES */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-enter { animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }

/* HEADER Y STATS */
.hero-panel{
    position: sticky;
    top: 55px;
    z-index: 1000;
    background:#fff;
    border:1px solid #dfe7f5;
    border-radius:22px;
    padding:12px 16px;
    margin-bottom:12px;
    box-shadow:0 4px 12px rgba(0,0,0,.03);
    backdrop-filter: blur(10px);
    transition: all .25s ease;
}

.hero-panel:hover{
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.hero-top{
    position:relative;
    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:8px;
}
.hero-actions{
    position:absolute;
    left:0;
    display:flex;
    align-items:center;
    gap:8px;
}

.hero-title{
    display:flex;
    align-items:center;
    gap:14px;
}

.hero-icon{
    width:40px;
    height:40px;
    border-radius:14px;
    border:1px solid #dbe5ff;
    background:#f8fbff;
    color:#3b82f6;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1rem;
}

.hero-title h1{
    margin-bottom:2px;
    font-size:1.35rem;
    font-weight:700;
}

.hero-title p{
    margin:0;
    color:#6b7280;
    font-size:.80rem;
    line-height:1.2;
}

.hero-filters{
    display:grid;
    grid-template-columns: 1fr 260px 300px auto;
    gap:12px;
    align-items:center;
    width:100%;
}

.hero-search{
    width:100%;
}

.custom-tabs{
    width:260px;
    min-width:260px;
}

.hero-select{
    width:100%;
}

.hero-btn{
    white-space:nowrap;
    height:38px;
    padding:0 14px;
    border-radius:14px;
    background:#eef4ff;
    border:1px solid #d7e4ff;
    color:#2957c8;
    font-weight:700;
    text-decoration:none;
    display:flex;
    align-items:center;
    gap:8px;
    transition:.25s;
}
.premium-input,
.premium-select{
    width:100%;
    appearance:auto;
    -webkit-appearance:auto;
}
.hero-btn:hover{
    background:#e5efff;
    transform:translateY(-2px);
}

/* ALERTAS DINÁMICAS AUTODESTRUIBLES */
.alert-box {
  padding: 16px 20px;
  border-radius: var(--radius-md);
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 12px;
  font-weight: 600;
  font-size: 0.95rem;
  box-shadow: 0 4px 12px rgba(0,0,0,0.02);
}
.alert-box.success { background: var(--success-soft); color: var(--success); border: 1px solid rgba(21, 128, 61, 0.15); }
.alert-box.danger { background: var(--danger-soft); color: var(--danger); border: 1px solid rgba(255, 74, 74, 0.15); }

/* TABS ESTILO APPLE */
.custom-tabs {
  display: flex;
  background: var(--bg);
  padding: 6px;
  border-radius: var(--radius-md);
  border: 1px solid var(--line);
  position: relative;
}
.tab-btn {
  flex: 1;
  background: transparent;
  border: none;
  padding: 10px 24px;
  border-radius: var(--radius-sm);
  font-family: var(--font-family);
  font-weight: 600;
  font-size: 0.9rem;
  color: var(--muted);
  cursor: pointer;
  transition: var(--transition);
  position: relative;
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  overflow: hidden;
}
.tab-btn.active { color: var(--blue); }
.tab-indicator {
  position: absolute;
  top: 6px; bottom: 6px;
  width: calc(50% - 6px);
  background: var(--card);
  border-radius: var(--radius-sm);
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
  transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
  z-index: 1;
}
.indicator-right { transform: translateX(100%); }
.tab-badge {
  background: var(--line);
  color: var(--ink);
  min-width: 24px;
  height: 24px;
  padding: 0 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
  transition: var(--transition);
}
.tab-btn.active .tab-badge {
  background: var(--blue-soft);
  color: var(--blue);
}

/* INPUTS Y FORMULARIOS */
.toolbar-actions {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 1;
  justify-content: flex-end;
  flex-wrap: wrap;
}
.search-wrapper {
  position: relative;
  width: 100%;
  max-width: none;
}
.search-wrapper i {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted);
}
.premium-input, .premium-select {
  width: 100%;
  padding: 12px 16px;
  background: var(--card);
  border: 1px solid var(--line);
  border-radius: var(--radius-sm);
  font-family: var(--font-family);
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--ink);
  transition: var(--transition);
}
.premium-input { padding-left: 44px; }
.premium-select { width: 100%; min-width: 280px; cursor: pointer; }
.premium-input:focus, .premium-select:focus {
  outline: none;
  border-color: var(--blue);
  box-shadow: var(--shadow-focus);
}

/* BOTONES */
.btn-primary {
  background: var(--blue);
  color: var(--card);
  border: none;
  padding: 12px 24px;
  border-radius: var(--radius-sm);
  font-family: var(--font-family);
  font-weight: 600;
  font-size: 0.9rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: var(--transition);
  text-decoration: none;
  cursor: pointer;
}
.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0, 122, 255, 0.2);
}
.btn-primary:active { transform: scale(0.98); }

.btn-icon-outline {
  background: var(--card);
  border: 1px solid var(--line);
  color: var(--muted);
  width: 44px; height: 44px;
  border-radius: var(--radius-sm);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: var(--transition);
  cursor: pointer;
  font-size: 1.1rem;
}
.btn-icon-outline:hover {
  color: var(--blue);
  border-color: var(--blue);
  background: var(--blue-soft);
  transform: translateY(-2px);
}

/* TOOLTIP PERSONALIZADO */
[data-tooltip] { position: relative; }
[data-tooltip]::before, [data-tooltip]::after {
  position: absolute; left: 50%; opacity: 0; visibility: hidden; pointer-events: none; transition: var(--transition); z-index: 9999;
}
[data-tooltip]::before {
  content: attr(data-tooltip); bottom: calc(100% + 12px); transform: translateX(-50%) translateY(6px); background: var(--title); color: #ffffff; padding: 8px 12px; border-radius: var(--radius-sm); font-family: var(--font-family); font-size: 0.78rem; font-weight: 600; white-space: nowrap; box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}
[data-tooltip]::after {
  content: ''; bottom: calc(100% + 6px); transform: translateX(-50%) translateY(6px); border: 6px solid transparent; border-top-color: var(--title);
}
[data-tooltip]:hover::before, [data-tooltip]:hover::after, [data-tooltip]:focus-visible::before, [data-tooltip]:focus-visible::after {
  opacity: 1; visibility: visible; transform: translateX(-50%) translateY(0);
}

/* TABLA CORPORATIVA */
.table-container {
  background: var(--card); border-radius: var(--radius-lg); box-shadow: var(--shadow-base); border: 1px solid var(--line); overflow: hidden; width: 100%; max-width: 100%; margin: 0 auto;
}
.table-responsive { width: 100%; overflow-x: hidden; }
.corp-table { width: 100%; border-collapse: collapse; text-align: left; table-layout: fixed; }
.corp-table th {
  background: var(--bg); padding: 14px 16px; font-size: 0.75rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid var(--line); white-space: nowrap; text-align: center; vertical-align: middle;
}
.sort-header { display: flex; align-items: center; justify-content: center; gap: 6px; cursor: pointer; user-select: none; }
.sort-header i { font-size: .8rem; color: #9ca3af; transition: .2s; }
.sort-header:hover i { color: var(--blue); }
.corp-table td { padding: 8px 12px; font-size: 0.9rem; font-weight: 500; border-bottom: 1px solid var(--line); vertical-align: middle; transition: var(--transition); text-align: right; }
.corp-table tbody tr { transition: var(--transition); background: var(--card); }
.corp-table tbody tr:last-child td { border-bottom: none; }
.corp-table tbody tr:hover { transform: translateY(-2px); box-shadow: var(--shadow-hover); position: relative; z-index: 10; }

/* ELEMENTOS DE TABLA */
.item-cell { display: flex; justify-content: flex-start; align-items: center; gap: 10px; white-space: normal; min-width: 0; text-align: left; }
.item-img-box { width: 34px; height: 34px; border-radius: var(--radius-md); overflow: hidden; background: var(--bg); border: 1px solid var(--line); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.item-img-box img { width: 100%; height: 100%; object-fit: cover; }
.item-img-box i { font-size: 1rem; color: var(--muted); }
.item-info-title { font-weight: 600; color: var(--title); font-size: .88rem; margin: 0; white-space: normal; word-break: break-word; }
.item-info-title, .item-info-sub { text-align: left; }
.item-info-sub { font-size: 0.8rem; color: var(--muted); display: flex; align-items: center; gap: 6px; }

/* ETIQUETAS (BADGES) */
.corp-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; }
.corp-badge::before { content: ''; width: 8px; height: 8px; border-radius: 50%; }
.badge-success { background: var(--success-soft); color: var(--success); }
.badge-success::before { background: var(--success); }
.badge-danger { background: var(--danger-soft); color: var(--danger); }
.badge-danger::before { background: var(--danger); }
.badge-info { background: var(--blue-soft); color: var(--blue); }
.badge-info::before { background: var(--blue); }
.badge-neutral { background: var(--bg); color: var(--muted); border: 1px solid var(--line); }
.badge-neutral::before { background: var(--muted); }

/* ACCIONES */
.action-cell { opacity: 0.6; transition: var(--transition); text-align: right; }
.corp-table tbody tr:hover .action-cell { opacity: 1; }
.action-buttons { display: flex; justify-content: flex-end; gap: 8px; }
.btn-ghost {
    width: 42px; height: 42px; border: none; border-radius: 12px; display: flex; align-items: center; justify-content: center; text-decoration: none; cursor: pointer; font-size: 1rem; transition: .25s ease; box-shadow: 0 2px 8px rgba(0,0,0,.05);
}
.btn-ghost:hover { transform: translateY(-3px) scale(1.08); box-shadow: 0 8px 18px rgba(0,0,0,.12); }
.btn-view { background: #eef6ff; color: #2563eb; }
.btn-process { background: #eef6ff; color: #2563eb; }
.btn-validate { background: #ecfdf3; color: #16a34a; }
.btn-edit { background: #eef6ff; color: #2563eb; }
.btn-delete { background: #fff1f2; color: #dc2626; }

/* ASIGNACIÓN */
.user-avatar { width: 26px; height: 26px; border-radius: 50%; background: var(--bg); border: 1px solid var(--line); color: var(--muted); display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
.assignment-cell { display: flex; justify-content: flex-start; align-items: center; gap: 6px; width: 100%; text-align: left; font-weight: 600; color: var(--ink); }

/* PAGINACIÓN */
.pagination { display: flex; gap: 8px; list-style: none; padding: 0; justify-content: center; margin-top: 32px; flex-wrap: wrap; }
.page-item .page-link { border: 1px solid var(--line); background: var(--card); color: var(--ink); padding: 10px 16px; border-radius: var(--radius-sm); text-decoration: none; transition: var(--transition); font-weight: 600; font-family: var(--font-family); }
.page-item.active .page-link { background: var(--blue); color: #fff; border-color: var(--blue); }

/* RESPONSIVE */
@media (max-width: 992px) {
  .corp-table thead { display: none; }
  .corp-table, .corp-table tbody, .corp-table tr, .corp-table td { display: block; width: 100%; }
  .corp-table tr { margin-bottom: 24px; border: 1px solid var(--line); border-radius: var(--radius-lg); box-shadow: var(--shadow-base); padding: 16px; }
  .corp-table td { padding: 12px 0; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; text-align: left; }
  .corp-table td::before { content: attr(data-label); font-size: 0.75rem; font-weight: 700; color: var(--muted); text-transform: uppercase; }
}

[x-cloak] { display: none !important; }

/* ==========================================================================
   ESTILOS PREMIUM DEL MODAL DE VERIFICACIÓN (OTP/PIN DE REFERENCIA)
   ========================================================================== */
.otp-blur-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(15, 23, 42, 0.15);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  z-index: 9999; display: flex; align-items: center; justify-content: center;
}
.otp-modal-card {
  background: #ffffff; padding: 36px 32px; border-radius: 24px;
  width: 100%; max-width: 540px;
  box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(226, 232, 240, 0.8);
  position: relative;
}
.otp-close-btn {
  position: absolute; top: 20px; right: 20px;
  width: 32px; height: 32px; border-radius: 50%;
  background: #f8fafc; border: 1px solid #f1f5f9;
  color: #64748b; display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all 0.2s ease;
}
.otp-close-btn:hover { background: #f1f5f9; color: #0f172a; }

.otp-header { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; text-align: left; }
.otp-icon-container {
  width: 44px; height: 44px; background: #eff6ff; color: #2563eb;
  border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
}
.otp-title-main { font-size: 1.15rem; font-weight: 700; color: #0f172a; margin: 0; }
.otp-title-sub { font-size: 0.88rem; color: #64748b; margin: 2px 0 0 0; }

.otp-info-banner {
  background: #f0f7ff; border-radius: 12px; padding: 12px 16px;
  display: flex; align-items: center; gap: 10px; margin-bottom: 28px;
  color: #1e40af; font-size: 0.88rem; font-weight: 500; text-align: left;
}
.otp-info-banner-dot { width: 6px; height: 6px; background: #2563eb; border-radius: 50%; }

/* GRILLA DE INPUTS INDIVIDUALES */
.otp-inputs-group { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; margin-bottom: 20px; }
.otp-box-input {
  width: 100%; height: 64px; text-align: center;
  font-size: 1.5rem; font-weight: 700; color: #0f172a;
  background: #ffffff; border: 2px solid #e2e8f0; border-radius: 14px;
  outline: none; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}
.otp-box-input:focus {
  border-color: #2563eb;
  box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
  background: #ffffff;
}
.otp-box-input.filled { border-color: #e2e8f0; background: #ffffff; }

.otp-footer-text { font-size: 0.85rem; color: #94a3b8; font-weight: 500; margin: 0; }
.otp-footer-text strong { color: #475569; font-weight: 600; cursor: pointer; }
</style>

<div class="premium-wrapper" x-data="CorporateDashboard('{{ request('tab', 'internos') }}')">
@php
    $nuevoActivoUrl = Route::has('servicios.create')
        ? route('servicios.create')
        : url('/servicio');
@endphp
  <div class="hero-panel animate-enter">
    <div class="hero-top">
      <div class="hero-actions">
        <a href="javascript:void(0);" onclick="volverInteligente();" class="btn-icon-outline" title="Regresar">
            <i class="bi bi-arrow-left"></i>
        </a>
        <a href="{{ url('/home') }}" class="btn-icon-outline" title="Inicio">
            <i class="bi bi-house-door-fill"></i>
        </a>
      </div>

      <div class="hero-title">
          <div class="hero-icon">
              <i class="bi bi-hdd-network"></i>
          </div>
          <div>
              <h1>Gestión de Mantenimiento</h1>
              <p>Administración, trazabilidad y control del proceso obligatorio de servicio.</p>
          </div>
      </div>
    </div>

    <div class="hero-filters">
        <div class="search-wrapper hero-search">
            <i class="bi bi-search"></i>
            <input type="text" class="premium-input" placeholder="Buscar equipo..." x-model="$store.dashboard.search">
        </div>

        <div class="custom-tabs">
            <div class="tab-indicator" :class="{ 'indicator-right': $store.dashboard.tab === 'externos' }"></div>
            <button class="tab-btn" :class="{ 'active': $store.dashboard.tab === 'internos' }" @click="setTab('internos')" type="button">
                <span>Internos</span>
                <span class="tab-badge">{{ $internosCount }}</span>
            </button>
            <button class="tab-btn" :class="{ 'active': $store.dashboard.tab === 'externos' }" @click="setTab('externos')" type="button">
                <span>Externos</span>
                <span class="tab-badge">{{ $externosCount }}</span>
            </button>
        </div>

        <select class="premium-select hero-select" x-model="$store.dashboard.status">
            <option value="">Todos los estados</option>
            <option value="pendiente_entrega">Pendiente entrega</option>
            <option value="pendiente_salida_foraneo">Pendiente salida foráneo</option>
            <option value="pendiente_regreso_foraneo">Pendiente regreso foráneo</option>
            <option value="pendiente_salida_cliente">Pendiente salida cliente</option>
            <option value="requiere_os">Requiere OS</option>
            <option value="completado">Completado</option>
            <option value="defectuoso">Defectuoso</option>
        </select>
        
        <a href="{{ $nuevoActivoUrl }}" class="hero-btn">
            <i class="bi bi-plus-lg"></i> Nuevo activo
        </a>
    </div>
  </div>

  @if (session('ok'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 2500)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="alert-box success">
      <i class="bi bi-check-circle-fill"></i> {{ session('ok') }}
    </div>
  @endif

  @if (session('error'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 3500)"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="alert-box danger">
      <i class="bi bi-x-octagon-fill"></i> {{ session('error') }}
    </div>
  @endif

  <div class="table-container animate-enter delay-2">
    <div class="table-responsive">
      <table class="corp-table">
        <thead>
          <tr>
            <th><div class="sort-header" @click="ordenarTabla(0)">Especificaciones del Equipo <i class="bi bi-arrow-down-up"></i></div></th>
            <th><div class="sort-header" @click="ordenarTabla(1)">Número de Serie <i class="bi bi-arrow-down-up"></i></div></th>
            <th><div class="sort-header" @click="ordenarTabla(2)">Ámbito <i class="bi bi-arrow-down-up"></i></div></th>
            <th><div class="sort-header" @click="ordenarTabla(3)">Estado Operativo <i class="bi bi-arrow-down-up"></i></div></th>
            <th><div class="sort-header" @click="ordenarTabla(4)">Asignación <i class="bi bi-arrow-down-up"></i></div></th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($servicios as $s)
            @php
              $estado = $s->estado_proceso ?? 'registro';

              $badgeClass = match($estado){
                'requiere_os'               => 'badge-info',
                'os_validada'               => 'badge-success',
                'defectuoso'                => 'badge-danger',
                'pendiente_entrega'         => 'badge-info',
                'pendiente_salida_foraneo'  => 'badge-info',
                'pendiente_regreso_foraneo' => 'badge-info',
                'pendiente_salida_cliente'  => 'badge-info',
                'completado'                => 'badge-success',
                default                     => 'badge-neutral',
              };

              $estadoTexto = match($estado){
                'requiere_os'               => 'Requiere OS',
                'os_validada'               => 'OS validada',
                'pendiente_entrega'         => 'Pendiente entrega',
                'pendiente_salida_foraneo'  => 'Pendiente salida foráneo',
                'pendiente_regreso_foraneo' => 'Pendiente regreso foráneo',
                'pendiente_salida_cliente'  => 'Pendiente salida cliente',
                'completado'                => 'Completado',
                default                     => ucfirst(str_replace('_',' ',$estado)),
              };

              $ambito = $resolverAmbito($s);
              $ambitoTexto = $ambito === 'externos' ? 'Externo' : 'Interno';
              $ambitoIcon = $ambito === 'externos' ? 'bi-box-arrow-up-right' : 'bi-building';

              $foto = $s->evidencia1 ?? null;
              if ($foto && !\Illuminate\Support\Str::startsWith($foto, ['http://', 'https://'])) {
                  $foto = asset('storage/' . ltrim(preg_replace('#^/?storage/#', '', $foto), '/'));
              }
            @endphp

            <tr class="trow" data-equipo="{{ strtolower($s->tipo_equipo ?? '') }}"
                x-show="filtrar(@js(['tipo' => $s->tipo_equipo, 'subtipo' => $s->subtipo_equipo, 'marca' => $s->marca, 'modelo' => $s->modelo, 'serie' => $s->numero_serie, 'estado' => $estado, 'user' => $s->user_name, 'doctor' => $s->nombre_doctor, 'ambito' => $ambito]))"
                x-transition.opacity.duration.300ms>

              <td data-label="Equipo">
                <div class="item-cell">
                  <div class="item-img-box">
                    @if($foto) <img src="{{ $foto }}" alt="Foto"> @else <i class="bi bi-display"></i> @endif
                  </div>
                  <div>
                    <div class="item-info-title">{{ $s->tipo_equipo ?? 'Equipo General' }}</div>
                    <div class="item-info-sub">
                        <i class="bi bi-tag-fill"></i> {{ trim(($s->marca ?? '') . ' ' . ($s->modelo ?? '')) ?: 'Especificación pendiente' }}
                    </div>
                  </div>
                </div>
              </td>

              <td data-label="Serie" style="text-align:left;">
                <span class="font-monospace"><i class="bi bi-upc-scan" style="color: var(--muted); margin-right: 4px;"></i> {{ $s->numero_serie ?? 'N/A' }}</span>
              </td>

              <td data-label="Ámbito">
                <span style="color: var(--title); font-weight: 600; display: inline-flex; align-items: center; justify-content:flex-start; gap: 6px;">
                  <i class="bi {{ $ambitoIcon }} text-muted"></i> {{ $ambitoTexto }}
                </span>
              </td>

              <td data-label="Estado"><span class="corp-badge {{ $badgeClass }}">{{ $estadoTexto }}</span></td>

              <td data-label="Asignación">
                <div class="assignment-cell"><div class="user-avatar"><i class="bi bi-person-fill"></i></div>{{ $s->nombre_doctor ?? 'Sin asignación' }}</div>
              </td>

              <td data-label="Acciones" class="action-cell">
                <div class="action-buttons">
                  @if(Route::has('servicios.show'))
                    <a href="{{ route('servicios.show', $s->id) }}" class="btn-ghost btn-view" data-tooltip="Inspeccionar detalle"><i class="bi bi-eye-fill"></i></a>
                  @endif

                  @if(Route::has('servicio.proceso'))
                    <a href="{{ route('servicio.proceso', $s->id) }}" class="btn-ghost btn-process" data-tooltip="Abrir proceso"><i class="bi bi-diagram-3-fill"></i></a>
                  @endif

                  @if(($s->estado_proceso ?? '') === 'requiere_os' && Route::has('servicio.os.form'))
                    <a href="{{ route('servicio.os.form', $s->id) }}" class="btn-ghost btn-validate" data-tooltip="Validar Orden de Servicio"><i class="bi bi-patch-check-fill"></i></a>
                  @endif

                  @if(Route::has('servicios.edit'))
                    <button type="button" @click="solicitarPin('{{ route('servicios.edit', $s->id) }}', 'link')" class="btn-ghost btn-edit" data-tooltip="Modificar"><i class="bi bi-pencil-square"></i></button>
                  @endif

                  @if(Route::has('servicios.destroy'))
                    <button type="button" @click="solicitarPin('form-delete-{{ $s->id }}', 'form')" class="btn-ghost btn-delete" data-tooltip="Eliminar registro"><i class="bi bi-trash-fill"></i></button>
                    <form id="form-delete-{{ $s->id }}" action="{{ route('servicios.destroy', $s->id) }}" method="POST" style="display: none;">
                      @csrf @method('DELETE')
                      <input type="hidden" name="action_pin" class="input-pin-hidden">
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 64px 20px;">
                <div style="max-width: 320px; margin: 0 auto; color: var(--muted);">
                  <i class="bi bi-inboxes" style="font-size: 3rem; color: var(--line); margin-bottom: 16px; display: block;"></i>
                  <h5 style="color: var(--title); font-weight: 700; font-size: 1.25rem;">Base de datos vacía</h5>
                  <p style="font-size: 0.95rem; margin-top: 8px;">Aún no se ha registrado ningún activo en esta sección del sistema.</p>
                </div>
              </td>
            </tr>
          @endforelse

          @if($serviciosCollection->count() > 0)
            <tr x-show="sinCoincidencias()" style="display:none;">
              <td colspan="6" style="text-align: center; padding: 64px 20px;">
                <div style="max-width: 320px; margin: 0 auto; color: var(--muted);">
                  <i class="bi bi-search" style="font-size: 2.5rem; color: var(--line); margin-bottom: 16px; display: block;"></i>
                  <h5 style="color: var(--title); font-weight: 700; font-size: 1.25rem;">Sin coincidencias</h5>
                  <p style="font-size: 0.95rem; margin-top: 8px;">Intenta ajustando los filtros de búsqueda o el selector de estado.</p>
                </div>
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>

  @if($servicios instanceof \Illuminate\Pagination\AbstractPaginator)
    <div>{{ $servicios->links() }}</div>
  @endif

  <div x-show="$store.dashboard.pinModalOpen" 
       class="otp-blur-overlay"
       x-cloak
       x-transition:enter="ease-out duration-300"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="ease-in duration-200"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0">
      
      <div class="otp-modal-card" @click.away="$store.dashboard.pinModalOpen = false"
           x-transition:enter="ease-out duration-300"
           x-transition:enter-start="opacity-0 translate-y-4 scale-95"
           x-transition:enter-end="opacity-100 translate-y-0 scale-100"
           x-transition:leave="ease-in duration-200"
           x-transition:leave-start="opacity-100 translate-y-0 scale-100"
           x-transition:leave-end="opacity-0 translate-y-4 scale-95">
          
          <button type="button" class="otp-close-btn" @click="$store.dashboard.pinModalOpen = false">
              <i class="bi bi-x-lg"></i>
          </button>

          <div class="otp-header">
              <div class="otp-icon-container">
                  <i class="bi bi-shield-check"></i>
              </div>
              <div>
                  <h4 class="otp-title-main">Confirmación segura</h4>
                  <p class="otp-title-sub">Escribe el PIN de 6 dígitos</p>
              </div>
          </div>
          
          <div class="otp-info-banner">
              <div class="otp-info-banner-dot"></div>
              <span>Al completar los <strong>6 dígitos</strong>, se confirma automáticamente.</span>
          </div>
          
          <div class="otp-inputs-group">
              <template x-for="index in 6">
                  <input type="text" 
                         maxlength="1" 
                         class="otp-box-input"
                         :class="{ 'filled': pinArray[index - 1] !== '' }"
                         x-model="pinArray[index - 1]"
                         @input="handleInput($event, index - 1)"
                         @keydown="handleKeyDown($event, index - 1)"
                         @paste="handlePaste($event)"
                         :id="'otp-' + (index - 1)"
                         inputmode="numeric"
                         pattern="[0-9]*"
                         autocomplete="one-time-code">
              </template>
          </div>
          
          <div x-show="pinError"
     x-transition
     class="alert alert-danger mt-3">

    PIN incorrecto. Intenta nuevamente.

</div>
          <p class="otp-footer-text">Puedes <strong @click="document.getElementById('otp-0').focus()">pegar</strong> el PIN completo.</p>
      </div>
  </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.store('dashboard', {
    search: '',
    status: '',
    order: 'asc',
    tab: '{{ request('tab', 'internos') === 'externos' ? 'externos' : 'internos' }}',
    pinModalOpen: false,
    targetTarget: null,
    targetType: null
  });

  Alpine.data('CorporateDashboard', function(initialTab = 'internos') {
    return {
      pinArray: ['', '', '', '', '', ''],
      pinError: false,

      init() {
        this.$store.dashboard.tab = initialTab === 'externos' ? 'externos' : 'internos';
        
        this.$watch('$store.dashboard.pinModalOpen', value => {
          if (value) {
              this.pinArray = ['', '', '', '', '', ''];
              setTimeout(() => {
                  const firstInput = document.getElementById('otp-0');
                  if (firstInput) firstInput.focus();
              }, 100);
          }
        });
      },

      setTab(tab) {
        this.$store.dashboard.tab = tab;
        const url = new URL(window.location.href);
        url.searchParams.set('tab', tab);
        window.history.replaceState({}, '', url);
      },

      solicitarPin(target, type) {

    this.pinError = false;
    this.pinArray = ['', '', '', '', '', ''];

    this.$store.dashboard.targetTarget = target;
    this.$store.dashboard.targetType = type;
    this.$store.dashboard.pinModalOpen = true;
},

      handleInput(e, index) {
        const val = e.target.value;
        if (!/^\d*$/.test(val)) {
            this.pinArray[index] = '';
            return;
        }

        if (val.length > 0) {
            this.pinArray[index] = val.lastIndexOf(val) !== -1 ? val.slice(-1) : val;
            if (index < 5) {
                document.getElementById(`otp-${index + 1}`).focus();
            }
        }
        this.verificarYProcesar();
      },

      handleKeyDown(e, index) {
        if (e.key === 'Backspace') {
            if (this.pinArray[index] === '' && index > 0) {
                this.pinArray[index - 1] = '';
                document.getElementById(`otp-${index - 1}`).focus();
            } else {
                this.pinArray[index] = '';
            }
            this.verificarYProcesar();
        }
      },

      handlePaste(e) {
        e.preventDefault();
        const clipboardData = e.clipboardData || window.clipboardData;
        const pastedData = clipboardData.getData('Text').trim();

        if (/^\d+$/.test(pastedData)) {
            const digits = pastedData.split('').slice(0, 6);
            digits.forEach((digit, i) => {
                this.pinArray[i] = digit;
            });
            
            const nextFocus = Math.min(digits.length, 5);
            document.getElementById(`otp-${nextFocus}`).focus();
            
            this.verificarYProcesar();
        }
      },

      verificarYProcesar() {
        const pinCompleto = this.pinArray.join('');
        if (pinCompleto.length === 6) {
            this.enviarFormularioConPin(pinCompleto);
        }
      },

      enviarFormularioConPin(pin) {

    const pinCorrecto = "{{ env('APROBACION_PIN') }}";

    if (pin !== pinCorrecto) {

        this.pinError = true;

        this.pinArray = ['', '', '', '', '', ''];

        setTimeout(() => {
            document.getElementById('otp-0').focus();
        }, 100);

        return;
    }

    const target = this.$store.dashboard.targetTarget;
    const type = this.$store.dashboard.targetType;

    if (type === 'link') {

        window.location.href = target;

    } else if (type === 'form') {

        const formulario = document.getElementById(target);

        if (formulario) {
            formulario.submit();
        }
    }

    this.$store.dashboard.pinModalOpen = false;
},

      ordenarTabla(columna) {
        const tbody = document.querySelector('.corp-table tbody');
        const filas = Array.from(tbody.querySelectorAll('tr.trow'));
        const ordenActual = this.$store.dashboard.order;

        filas.sort((a, b) => {
            let valorA = a.children[columna].innerText.trim().toLowerCase();
            let valorB = b.children[columna].innerText.trim().toLowerCase();
            return ordenActual === 'asc' ? valorA.localeCompare(valorB, 'es') : valorB.localeCompare(valorA, 'es');
        });

        filas.forEach(fila => tbody.appendChild(fila));
        this.$store.dashboard.order = ordenActual === 'asc' ? 'desc' : 'asc';
      },

      filtrar(row) {
        const q = (this.$store.dashboard.search || '').toLowerCase().trim();
        const status = (this.$store.dashboard.status || '').toLowerCase().trim();
        const tab = (this.$store.dashboard.tab || '').toLowerCase().trim();

        const statusOk = status ? String(row.estado || '').toLowerCase().includes(status) : true;
        const tabOk = tab ? String(row.ambito || '').toLowerCase() === tab : true;

        if (!q) return statusOk && tabOk;

        const dataStr = [
          row.tipo, row.subtipo, row.marca, row.modelo,
          row.serie, row.user, row.doctor, row.ambito, row.estado
        ].join(' ').toLowerCase();

        return statusOk && tabOk && dataStr.includes(q);
      },

      sinCoincidencias() {
        const rows = document.querySelectorAll('tr.trow');
        let visibles = 0;
        rows.forEach(row => {
          const style = window.getComputedStyle(row);
          if (style.display !== 'none') visibles++;
        });
        return visibles === 0;
      }
    }
  });
});
</script>

<script>
  /**
   * "Volver inteligente": evita que el botón de Volver se quede atrapado
   * en pantallas internas del propio módulo de activos/servicios (ej.
   * create/store/edit/proceso) cuando regresamos de esos flujos.
   *
   * Idea: guardamos en sessionStorage la página EXTERNA real desde la que
   * entraste al módulo. Mientras te muevas dentro del propio módulo, no
   * la sobreescribimos, así que "Volver" siempre te manda al punto de
   * entrada real, sin importar cuántos saltos internos haya en medio.
   *
   * IMPORTANTE: ajusta MODULE_PREFIX si la URL de tus rutas de
   * activos/servicios no empieza con "/servicio".
   */
  (function () {
    const MODULE_PREFIX = '/servicio';
    const STORAGE_KEY = 'servicios_volver_target';
    const FALLBACK_URL = '{{ url('/home') }}';

    const referrer = document.referrer;
    let referrerIsInternalModule = false;

    if (referrer) {
      try {
        const referrerUrl = new URL(referrer);
        referrerIsInternalModule =
          referrerUrl.origin === window.location.origin &&
          referrerUrl.pathname.startsWith(MODULE_PREFIX);
      } catch (e) {
        // referrer inválido, lo ignoramos
      }
    }

    if (referrer && !referrerIsInternalModule) {
      // Venimos de fuera del módulo: este es el nuevo destino de "Volver"
      sessionStorage.setItem(STORAGE_KEY, referrer);
    } else if (!sessionStorage.getItem(STORAGE_KEY)) {
      // No hay referrer útil ni destino guardado todavía: usamos un respaldo
      sessionStorage.setItem(STORAGE_KEY, FALLBACK_URL);
    }
    // Si el referrer SÍ es interno (create/store/edit/proceso), no tocamos el valor guardado.

    window.volverInteligente = function () {
      const target = sessionStorage.getItem(STORAGE_KEY) || FALLBACK_URL;
      window.location.href = target;
    };
  })();
</script>
@endsection