@extends('layouts.app')
@section('title','Inventario')
@section('titulo','Inventario')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
:root{
  --bg:#eaebec; --panel:#ffffff; --text:#0f172a; --muted:#667085; --border:#e7eaf0;
  --pblue:#dbeafe; --pblue-700:#1d4ed8; --pgreen:#dcfce7; --pgreen-700:#046c4e;
  --shadow:0 10px 30px rgba(2,6,23,.06); --radius:22px;
}
*,*::before,*::after{ box-sizing:border-box; }
body{ background:var(--bg); color:var(--text); font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; }
.page-wrap{ max-width:1160px; margin:0 auto; padding:0 16px; overflow-x:hidden; }

/* HERO */
.hero{
  background:
    radial-gradient(1200px 150px at 0% 0%, rgba(96,165,250,.18), transparent 40%),
    radial-gradient(1200px 150px at 100% 0%, rgba(14,165,233,.14), transparent 40%),
    #fff;
  border:1px solid var(--border); border-radius:18px; padding:16px 18px;
  box-shadow:var(--shadow);
  display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
  margin:18px 0; overflow:hidden;
}
.hero .chip{ width:56px; height:56px; border-radius:16px; display:inline-flex; align-items:center; justify-content:center; background:#fff; border:1px solid #dce7ff; }
.hero h1{ margin:0; font-weight:800; letter-spacing:-.02em; }
.subtle{ color:var(--muted); }
.hero-actions{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; width:100%; }
.search{ position:relative; flex:1 1 420px; min-width:0; background:#fff; border:1px solid var(--border); border-radius:14px; padding-left:42px; }
.search input{ border:none; outline:none; background:transparent; padding:12px 14px; width:100%; color:#111827; }
.search .ico{ position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:18px; color:#1d4ed8; opacity:.9; }

.hero-select{ border:1px solid var(--border); border-radius:14px; padding:10px 12px; background:#fff; min-width:220px; }

/* BOTONES */
.btn{
  display:inline-flex; align-items:center; gap:.45rem;
  padding:12px 14px; border-radius:14px;
  border:1px solid var(--border); background:#fff; color:#334155;
  font-weight:800; text-decoration:none; cursor:pointer;
  transition:transform .04s ease, box-shadow .2s ease, background .2s ease;
}
.btn:active{ transform:translateY(1px) }
.btn-utility{ box-shadow:0 4px 10px rgba(2,6,23,.04); }
.btn-blue{ background:var(--pblue); color:#0b2a4a; border-color:rgba(96,165,250,.45); }
.btn-green{ background:var(--pgreen); color:#064e3b; border-color:rgba(52,211,153,.45); }

/* TABLA */
.table-wrap{ background:#fff; border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
.table-scroll{ overflow:auto; max-width:100%; }
.inv-table{ width:100%; border-collapse:separate; border-spacing:0; table-layout:fixed; }
.inv-table .th,.inv-table .td{ padding:16px 18px; text-align:left; font-size:14px; }
.inv-table .th{ color:#6b7280; font-weight:700; background:#eef3ff; border-bottom:1px solid var(--border); white-space:nowrap; }
.inv-table tr.trow{ display:table-row; border-bottom:1px solid var(--border); background:#fff; }
.inv-table tr.trow:hover{ background:#fcfcff; }
.inv-table td.td{ display:table-cell; vertical-align:middle; }
.cell-actions{ display:flex; gap:8px; justify-content:flex-end; }
.no-results{ color:#667085; }

.tile-mini{ width:52px; height:52px; border:1px solid var(--border); border-radius:10px; overflow:hidden; background:#f8fafc; display:grid; place-items:center }
.tile-mini img{ width:100%; height:100%; object-fit:cover }

/* Layout info equipo */
.equip-main{ display:flex; align-items:center; gap:12px; }
.equip-main-text-title{ margin-bottom:2px; display:flex; flex-wrap:wrap; column-gap:4px; }
.equip-tipo{ font-weight:700; }
.equip-subtipo{ font-weight:700; }
.equip-dot{ color:#9ca3af; }
.equip-model{ line-height:1.3; }

/* BADGES */
.badge-state{
  display:inline-flex; align-items:center; gap:.35rem;
  padding:4px 10px; border-radius:999px;
  font-weight:800; font-size:12px;
  border:1px solid transparent; width:fit-content;
}
.badge-registro{ background:#f1f5f9; color:#334155; border-color:#e2e8f0; }
.badge-hojalateria{ background:#e0f2fe; color:#1d4ed8; border-color:#bfdbfe; }
.badge-mantenimiento{ background:#fef9c3; color:#a16207; border-color:#fde68a; }
.badge-stock{ background:#dcfce7; color:#065f46; border-color:#bbf7d0; }
.badge-vendido{ background:#ffe4e6; color:#9f1239; border-color:#fecdd3; }
.badge-defectuoso{ background:#ffedd5; color:#c2410c; border-color:#fdba74; }
.state-meta{ color:#667085; font-size:12px; margin-top:6px; }

.inv-table col:nth-child(1){ width:32%; }
.inv-table col:nth-child(2){ width:14%; }
.inv-table col:nth-child(3){ width:18%; }
.inv-table col:nth-child(4){ width:14%; }
.inv-table col:nth-child(5){ width:12%; }
.inv-table col:nth-child(6){ width:10%; }

/* MÓVIL */
@media (max-width:576px){
  .hero-actions .btn-utility.export-desktop{ display:none; }
  .hero-actions .hero-select{ display:none; }

  .equip-main{ align-items:flex-start; }
  .equip-main-text{ max-width:210px; }
  .equip-main-text-title{ margin-bottom:4px; flex-direction:column; row-gap:2px; }
  .equip-dot{ display:none; }

  .inv-table.is-stacked thead{ display:none; }
  .inv-table.is-stacked, .inv-table.is-stacked tbody, .inv-table.is-stacked tr.trow, .inv-table.is-stacked td.td{ display:block; width:100%; }
  .inv-table.is-stacked tr.trow{ padding:12px 14px; }
  .inv-table.is-stacked tr.trow + tr.trow{ border-top:1px solid var(--border); }
  .inv-table.is-stacked td.td{
    border:none; padding:10px 0;
    display:grid; grid-template-columns:minmax(96px,40%) 1fr; gap:8px; align-items:flex-start;
    word-wrap:break-word;
  }
  .inv-table.is-stacked td.td::before{ content:attr(data-label); font-weight:700; color:#6b7280; }
  .inv-table.is-stacked td.td[data-label="Acciones"]{ grid-template-columns:1fr; }
  .inv-table.is-stacked .cell-actions{ justify-content:flex-start; }
}

/* FAB + Bottom sheet */
.inv-fab{ position:fixed; right:16px; bottom:18px; z-index:60; display:none; }
@media (max-width:576px){ .inv-fab{ display:block; } }
.inv-fab-btn{
  width:56px; height:56px; border-radius:999px; border:1px solid #dce7ff; background:#ffffff;
  display:grid; place-items:center; box-shadow:0 14px 28px rgba(2,6,23,.12);
  transition:transform .06s ease, box-shadow .2s ease, background .2s ease;
}
.inv-fab-btn:hover{ background:#f4fbff; box-shadow:0 18px 36px rgba(2,6,23,.16) }
.inv-fab-btn i{ font-size:22px; color:#2563eb; }

.inv-sheet-backdrop{ position:fixed; inset:0; background:rgba(15,23,42,.35); backdrop-filter: blur(8px) saturate(1.05); opacity:0; pointer-events:none; transition:opacity .2s ease; z-index:70; }
.inv-sheet-backdrop.show{ opacity:1; pointer-events:auto; }
.inv-sheet{
  position:fixed; left:0; right:0; bottom:-100%; z-index:80; background:#fff;
  border-radius:18px 18px 0 0; box-shadow:0 -20px 40px rgba(2,6,23,.16);
  padding:14px 14px 18px; transition:bottom .28s ease; will-change:bottom;
}
.inv-sheet.show{ bottom:0; }
.inv-sheet .grab{ width:60px; height:6px; background:#e5e7eb; border-radius:999px; margin:6px auto 12px; }
.inv-sheet .title{ font-weight:800; color:#0f172a; margin:4px 0 12px; }
.inv-sheet .group{ display:grid; gap:14px; }
.inv-sheet .btn-row{ display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.inv-sheet .inv-btn-sheet{
  display:inline-flex; align-items:center; justify-content:center; gap:.5rem;
  height:48px; width:100%;
  border-radius:12px; font-weight:800; border:1px solid var(--border);
  background:#fff; color:#334155;
  box-shadow:0 6px 16px rgba(2,6,23,.05);
  transition:transform .04s ease, box-shadow .2s ease, background .2s ease;
}
.inv-sheet .inv-btn-sheet:active{ transform:translateY(1px) }
.inv-sheet .inv-btn-sheet.primary{ background:linear-gradient(180deg,#eef4ff,#e3ecff); color:#0b2a4a; border-color:#cfe0ff; }
.inv-sheet .inv-btn-sheet.secondary{ background:#ffffff; color:#334155; border-color:var(--border); }

.inv-sheet .add-equipo{
  display:flex; align-items:center; gap:12px; padding:14px;
  border-radius:14px; border:1px solid #b8f3df; background:#eafff6;
  text-decoration:none; color:#0b6b53; font-weight:800;
  box-shadow:0 6px 16px rgba(11,107,83,.08);
}
.inv-sheet .add-equipo .ico{
  width:40px; height:40px; border-radius:12px; display:grid; place-items:center;
  background:#dff9ef; color:#0b6b53; font-size:1.1rem;
}
.inv-sheet .add-equipo .sub{
  font-weight:600; color:#0b6b53; opacity:.9; font-size:.875rem; margin-top:2px;
}

/* MODAL EXPORTAR */
.modal-content{ border-radius:18px; border:1px solid var(--border); }
.modal-header{ border-bottom:1px solid var(--border); }
.modal-footer{ border-top:1px solid var(--border); }
.option-card{
  border:1px solid var(--border);
  border-radius:14px;
  padding:12px;
  background:#fff;
}
.option-card small{ color:#667085; }
</style>

<div class="page-wrap" x-data="InventarioUI()">
  {{-- HERO --}}
  <div class="hero">
    <div class="d-flex align-items-center gap-3">
      <div class="chip"><i class="bi bi-clipboard-check" style="font-size:1.25rem;color:#1d4ed8"></i></div>
      <div>
        <h1 class="h4 mb-0">Inventario</h1>
        <div class="small subtle">Consulta y gestiona tus equipos en tiempo real.</div>
      </div>
    </div>

    <div class="hero-actions">
      <div class="search">
        <i class="ico bi bi-search"></i>
        <input type="search" placeholder="Buscar" x-model="$store.inv.q">
      </div>

      {{-- DESKTOP: filtro estado --}}
      <select class="hero-select btn-utility" x-model="$store.inv.filtroEstado">
        <option value="">Todos los estados</option>
        <option value="registro">Registro</option>
        <option value="hojalateria">Hojalatería</option>
        <option value="mantenimiento">Mantenimiento</option>
        <option value="stock">Stock</option>
        <option value="vendido">Vendido</option>
        <option value="defectuoso">Defectuoso</option>
      </select>

      {{-- NUEVO BOTÓN EXPORTAR (abre modal) --}}
      <button
        type="button"
        class="btn btn-blue btn-utility export-desktop"
        data-bs-toggle="modal"
        data-bs-target="#exportModal"
        title="Exportar"
      >
        <i class="bi bi-download"></i> Exportar
      </button>

      <button class="btn btn-utility" onclick="location.reload()">Actualizar</button>

      <a href="{{ route('registros.create') }}" class="btn btn-green btn-utility">
        <i class="bi bi-plus-circle"></i> Agregar equipo
      </a>
    </div>
  </div>

  {{-- TABLA --}}
  <div class="table-wrap">
    <div class="table-scroll">
      <table class="inv-table" :class="{'is-stacked': isMobile}">
        <colgroup><col><col><col><col><col><col></colgroup>
        <thead>
          <tr>
            <th class="th">Equipo</th>
            <th class="th">Serie</th>
            <th class="th">Estado</th>
            <th class="th">Fecha adquisición</th>
            <th class="th">Registrado por</th>
            <th class="th text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach(($productos ?? []) as $r)
            @php
              /*
               * ESTADO ACTUAL:
               * 1) Si tiene procesos, tomamos SIEMPRE el ÚLTIMO proceso creado.
               *    Ese es el estado actual (mantenimiento, stock, vendido, etc.).
               * 2) Si no tiene procesos, usamos estado_proceso o estado_actual.
               */

              $estado = null;
              $fechaUltimoEstado = null;

              if (method_exists($r, 'procesos')) {
                  $procesoActual = $r->relationLoaded('procesos')
                      ? $r->procesos->sortByDesc('created_at')->first()
                      : $r->procesos()->orderByDesc('created_at')->first();

                  if ($procesoActual) {
                      $estado = $procesoActual->tipo_proceso;
                      $fechaUltimoEstado = $procesoActual->created_at;
                  }
              }

              if (!$estado) {
                  // Fallback: campo en registros
                  $estado = $r->estado_proceso
                      ?: ([1 => 'stock', 2 => 'vendido', 3 => 'mantenimiento', 4 => 'defectuoso'][$r->estado_actual] ?? 'registro');

                  if ($estado === 'registro') {
                      $fechaUltimoEstado = $r->created_at;
                  } else {
                      $fechaUltimoEstado = $r->updated_at ?: $r->created_at;
                  }
              }

              $detalleUrl = Route::has('inventario.detalle') ? route('inventario.detalle', $r->id) : url('/inventario/detalle/'.$r->id);
              $barcodeUrl = Route::has('registros.imprimir-barcode') ? route('registros.imprimir-barcode', $r->id) : '#';

              $badgeClass = match($estado){
                'hojalateria'   => 'badge-hojalateria',
                'mantenimiento' => 'badge-mantenimiento',
                'stock'         => 'badge-stock',
                'vendido'       => 'badge-vendido',
                'defectuoso'    => 'badge-defectuoso',
                default         => 'badge-registro'
              };
            @endphp

            <tr class="trow"
                x-show="filtra(
                  {{ json_encode([
                    'tipo'=>$r->tipo_equipo,
                    'subtipo'=>$r->subtipo_equipo,
                    'marca'=>$r->marca,
                    'modelo'=>$r->modelo,
                    'serie'=>$r->numero_serie,
                    'estado'=>$estado,
                    'user'=>$r->user_name
                  ]) }},
                  $store.inv.q,
                  $store.inv.filtroEstado
                )"
                :style="filtra(
                  {{ json_encode([
                    'tipo'=>$r->tipo_equipo,
                    'subtipo'=>$r->subtipo_equipo,
                    'marca'=>$r->marca,
                    'modelo'=>$r->modelo,
                    'serie'=>$r->numero_serie,
                    'estado'=>$estado,
                    'user'=>$r->user_name
                  ]) }},
                  $store.inv.q,
                  $store.inv.filtroEstado
                ) ? '' : 'display:none !important'">

              {{-- EQUIPO --}}
              <td class="td" data-label="Equipo">
                <div class="equip-main">
                  <div class="tile-mini">
                    @if($r->evidencia1)
                      <img src="{{ Str::startsWith($r->evidencia1, ['http://','https://']) ? $r->evidencia1 : asset('storage/'.ltrim($r->evidencia1,'/')) }}" alt="prev">
                    @else
                      <i class="bi bi-box text-muted"></i>
                    @endif
                  </div>
                  <div class="equip-main-text">
                    <div class="equip-main-text-title">
                      <span class="equip-tipo">{{ $r->tipo_equipo ?? 'Equipo' }}</span>
                      <span class="equip-dot">•</span>
                      <span class="equip-subtipo">{{ $r->subtipo_equipo ?? '—' }}</span>
                    </div>
                    <div class="equip-model text-muted small">
                      {{ $r->marca }} {{ $r->modelo }}
                    </div>
                  </div>
                </div>
              </td>

              <td class="td" data-label="Serie"><span class="fw-semibold">{{ $r->numero_serie }}</span></td>

              <td class="td" data-label="Estado">
                <span class="badge-state {{ $badgeClass }}">
                  <i class="bi bi-circle-fill" style="font-size:.55rem"></i>
                  <span class="text-capitalize">{{ $estado }}</span>
                </span>
                <div class="state-meta">
                  @if($fechaUltimoEstado)
                    Último cambio: {{ $fechaUltimoEstado->format('Y-m-d H:i') }}
                  @else
                    Último cambio: — 
                  @endif
                </div>
              </td>

              <td class="td" data-label="Fecha adquisición">{{ optional($r->fecha_adquisicion)->format('Y-m-d') ?? '—' }}</td>
              <td class="td" data-label="Registrado por">{{ $r->user_name ?? '—' }}</td>

              <td class="td" data-label="Acciones">
                <div class="cell-actions">
                  <a class="btn btn-blue btn-utility" href="{{ $detalleUrl }}"><i class="bi bi-eye"></i></a>
                  <a class="btn btn-utility" target="_blank" href="{{ $barcodeUrl }}" title="Imprimir etiqueta">
                    <i class="bi bi-upc-scan"></i>
                  </a>
                </div>
              </td>
            </tr>
          @endforeach

          @if(empty($productos) || count($productos)===0)
            <tr class="trow"><td class="td no-results" colspan="6">No hay registros aún.</td></tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>

  {{-- MODAL EXPORTAR (PDF / EXCEL) --}}
  <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <div class="fw-bold" id="exportModalLabel" style="letter-spacing:.2px">Exportar inventario</div>
            <div class="small subtle">
              El PDF se agrupa por <b>categoría / tipo de equipo</b>. Se respeta el estado seleccionado.
            </div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <div class="option-card mb-3">
            <div class="fw-semibold mb-1">Estado a exportar</div>
            <small>
              Se usará el mismo estado que tienes filtrado en la vista.
              Si en la parte superior tienes "Todos los estados", aquí también se exportan todos.
            </small>
          </div>

          <div class="option-card mb-3">
            <div class="fw-semibold mb-2">Formato</div>
            <div class="d-flex gap-2">
              <input class="btn-check" type="radio" name="fmtExport" id="fmtPdf" value="pdf" checked>
              <label class="btn btn-outline-primary w-100" for="fmtPdf">
                <i class="bi bi-file-earmark-pdf me-1"></i> PDF (agrupado por categoría)
              </label>

              <input class="btn-check" type="radio" name="fmtExport" id="fmtExcel" value="excel">
              <label class="btn btn-outline-success w-100" for="fmtExcel">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Excel (.xlsx)
              </label>
            </div>

            <div class="mt-3">
              <small class="text-muted d-block" id="pdfHint">
                El PDF se abrirá en otra pestaña listo para imprimir o guardar.
              </small>
              <small class="text-muted d-none" id="excelHint">
                Se descargará un archivo <b>.xlsx</b> de Excel.
              </small>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="button" class="btn btn-utility w-100" data-bs-dismiss="modal">
              Cancelar
            </button>
            <button type="button" class="btn btn-blue w-100" id="btnConfirmExport">
              <i class="bi bi-download"></i> Exportar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- FAB + Bottom sheet (móvil) --}}
  <div class="inv-fab">
    <button type="button" id="openInvSheet" class="inv-fab-btn" aria-label="Filtros">
      <i class="bi bi-sliders"></i>
    </button>
  </div>
  <div class="inv-sheet-backdrop" id="invSheetBackdrop"></div>

  <div class="inv-sheet" id="invSheetPanel" x-data>
    <div class="grab"></div>
    <div class="title">Filtros</div>

    <div class="group">
      <div>
        <label class="form-label fw-semibold mb-1">Estado</label>
        <select class="form-select" x-model="$store.inv.filtroEstado">
          <option value="">Todos los estados</option>
          <option value="registro">Registro</option>
          <option value="hojalateria">Hojalatería</option>
          <option value="mantenimiento">Mantenimiento</option>
          <option value="stock">Stock</option>
          <option value="vendido">Vendido</option>
          <option value="defectuoso">Defectuoso</option>
        </select>
      </div>

      {{-- Exportar (móvil) abre el mismo modal --}}
      <button
        type="button"
        class="inv-btn-sheet primary"
        @click="document.getElementById('closeInvSheet')?.click(); setTimeout(()=>document.querySelector('[data-bs-target=\"#exportModal\"]')?.click(), 80);"
      >
        <i class="bi bi-download"></i> Exportar
      </button>

      <a href="{{ route('registros.create') }}" class="add-equipo">
        <div class="ico"><i class="bi bi-plus-lg"></i></div>
        <div>
          <div class="title">Agregar nuevo equipo</div>
          <div class="sub">Regístralo y adjunta evidencias desde tu móvil.</div>
        </div>
      </a>

      <div class="btn-row">
        <button class="inv-btn-sheet primary" id="applyInvSheet">
          <i class="bi bi-check2-circle"></i> Aplicar filtros
        </button>
        <button class="inv-btn-sheet secondary" id="closeInvSheet">
          <i class="bi bi-x-lg"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.store('inv', {
    q: '',
    filtroEstado: '',
  });

  Alpine.data('InventarioUI', InventarioUI);
});

function InventarioUI(){
  return {
    isMobile: window.matchMedia('(max-width: 576px)').matches,

    filtra(row, q, estado){
      q = (q || '').toLowerCase().trim();
      estado = (estado || '').toLowerCase().trim();

      const estadoOk = estado ? (String(row.estado||'').toLowerCase().trim() === estado) : true;
      if(!q) return estadoOk;

      const blob = [row.tipo,row.subtipo,row.marca,row.modelo,row.serie,row.user]
        .join(' ').toLowerCase();
      return estadoOk && blob.includes(q);
    },
  };
}

/* Helper global para desbloquear la UI */
function unlockUi(){
  document.body.classList.remove('modal-open');
  document.body.style.removeProperty('overflow');
  document.body.style.removeProperty('padding-right');
  document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
  const bsBackdrop = document.getElementById('invSheetBackdrop');
  const bsPanel = document.getElementById('invSheetPanel');
  bsBackdrop?.classList.remove('show');
  bsPanel?.classList.remove('show');
}

/* Bottom sheet + export */
document.addEventListener('DOMContentLoaded', () => {
  const openBtn   = document.getElementById('openInvSheet');
  const closeBtn  = document.getElementById('closeInvSheet');
  const applyBtn  = document.getElementById('applyInvSheet');
  const backdrop  = document.getElementById('invSheetBackdrop');
  const panel     = document.getElementById('invSheetPanel');
  const exportModalEl = document.getElementById('exportModal');

  const open = () => {
    if (window.matchMedia('(max-width: 576px)').matches) {
      panel.classList.add('show');
      backdrop.classList.add('show');
    }
  };
  const hide = () => {
    panel.classList.remove('show');
    backdrop.classList.remove('show');
  };

  openBtn?.addEventListener('click', open);
  closeBtn?.addEventListener('click', () => { hide(); unlockUi(); });
  applyBtn?.addEventListener('click', () => { hide(); unlockUi(); });
  backdrop?.addEventListener('click', () => { hide(); unlockUi(); });

  if (exportModalEl) {
    exportModalEl.addEventListener('hidden.bs.modal', unlockUi);
  }

  const fmtPdf   = document.getElementById('fmtPdf');
  const fmtExcel = document.getElementById('fmtExcel');
  const pdfHint  = document.getElementById('pdfHint');
  const excelHint= document.getElementById('excelHint');

  function refreshHints(){
    const isPdf = fmtPdf?.checked;
    pdfHint?.classList.toggle('d-none', !isPdf);
    excelHint?.classList.toggle('d-none', isPdf);
  }
  fmtPdf?.addEventListener('change', refreshHints);
  fmtExcel?.addEventListener('change', refreshHints);
  refreshHints();

  const btnExport = document.getElementById('btnConfirmExport');
  btnExport?.addEventListener('click', () => {
    const fmt = document.querySelector('input[name="fmtExport"]:checked')?.value || 'pdf';
    const estado = (Alpine.store('inv')?.filtroEstado || '').toLowerCase().trim();

    const modalEl = document.getElementById('exportModal');
    let modalInstance = null;
    if (modalEl && window.bootstrap && bootstrap.Modal) {
      modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    }

    const hardCloseModal = () => {
      if (modalInstance) modalInstance.hide();
      unlockUi();
    };

    if (fmt === 'excel') {
      const base = @json(route('registros.exportExcel'));
      const url = new URL(base, window.location.origin);
      if (estado) url.searchParams.set('estado_proceso', estado);

      hardCloseModal();
      setTimeout(() => {
        window.open(url.toString(), '_blank', 'noopener');
      }, 80);
    } else {
      const base = @json(route('registros.export.pdf'));
      const url = new URL(base, window.location.origin);
      if (estado) url.searchParams.set('estado_proceso', estado);

      hardCloseModal();
      setTimeout(() => {
        window.open(url.toString(), '_blank', 'noopener');
      }, 80);
    }
  });

  unlockUi();
});
</script>

@endsection
