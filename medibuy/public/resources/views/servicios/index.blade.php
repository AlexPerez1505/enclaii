@extends('layouts.app')
@section('title','Mantenimiento interno')
@section('titulo','Mantenimiento interno')

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
.page-wrap{ max-width:1160px; margin:0 auto; padding:0 16px 40px; overflow-x:hidden; }

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
.subtle{ color:var(--muted); font-size:13px; }
.hero-actions{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; width:100%; }
.search{ position:relative; flex:1 1 360px; min-width:0; background:#fff; border:1px solid var(--border); border-radius:14px; padding-left:42px; }
.search input{ border:none; outline:none; background:transparent; padding:10px 14px; width:100%; color:#111827; font-size:14px; }
.search .ico{ position:absolute; left:12px; top:50%; transform:translateY(-50%); font-size:18px; color:#1d4ed8; opacity:.9; }

.hero-select{ border:1px solid var(--border); border-radius:14px; padding:9px 12px; background:#fff; min-width:200px; font-size:13px; }

.btn{ display:inline-flex; align-items:center; gap:.45rem; padding:10px 13px; border-radius:14px; border:1px solid var(--border); background:#fff; color:#334155; font-weight:800; text-decoration:none; cursor:pointer; transition:transform .04s ease, box-shadow .2s ease, background .2s ease; font-size:13px; }
.btn:active{ transform:translateY(1px) }
.btn-utility{ box-shadow:0 4px 10px rgba(2,6,23,.04); }
.btn-blue{ background:var(--pblue); color:#0b2a4a; border-color:rgba(96,165,250,.45); }
.btn-green{ background:var(--pgreen); color:#064e3b; border-color:rgba(52,211,153,.45); }

/* TABLA */
.table-wrap{ background:#fff; border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }
.table-scroll{ overflow:auto; max-width:100%; }
.inv-table{ width:100%; border-collapse:separate; border-spacing:0; table-layout:fixed; }
.inv-table .th,.inv-table .td{ padding:14px 16px; text-align:left; font-size:13px; }
.inv-table .th{ color:#6b7280; font-weight:700; background:#eef3ff; border-bottom:1px solid var(--border); white-space:nowrap; }
.inv-table tr.trow{ display:table-row; border-bottom:1px solid var(--border); background:#fff; }
.inv-table tr.trow:hover{ background:#fcfcff; }
.inv-table td.td{ display:table-cell; vertical-align:middle; }
.cell-actions{ display:flex; gap:6px; justify-content:flex-end; flex-wrap:wrap; }
.no-results{ color:#667085; }

/* small thumbnail */
.tile-mini{ width:46px; height:46px; border:1px solid var(--border); border-radius:10px; overflow:hidden; background:#f8fafc; display:grid; place-items:center }
.tile-mini img{ width:100%; height:100%; object-fit:cover }

/* BADGES */
.badge-state{ display:inline-flex; align-items:center; gap:.35rem; padding:5px 9px; border-radius:999px; font-weight:800; font-size:11px; border:1px solid transparent; }
.badge-registro{ background:#f1f5f9; color:#334155; border-color:#e2e8f0; }
.badge-salida{ background:#fef3c7; color:#92400e; border-color:#fde68a; }
.badge-regreso{ background:#dcfce7; color:#166534; border-color:#bbf7d0; }
.badge-entregado{ background:#e0e7ff; color:#3730a3; border-color:#c7d2fe; }
.badge-defectuoso{ background:#fee2e2; color:#b91c1c; border-color:#fecaca; }
.badge-otro{ background:#e5e7eb; color:#374151; border-color:#d1d5db; }
.state-meta{ color:#667085; font-size:11px; margin-top:4px; }

.inv-table col:nth-child(1){ width:30%; }
.inv-table col:nth-child(2){ width:12%; }
.inv-table col:nth-child(3){ width:16%; }
.inv-table col:nth-child(4){ width:14%; }
.inv-table col:nth-child(5){ width:14%; }
.inv-table col:nth-child(6){ width:14%; }

/* MÓVIL */
@media (max-width:576px){
  .hero-actions .btn-utility{ display:none; }
  .hero-actions .hero-select{ display:none; }

  .inv-table.is-stacked thead{ display:none; }
  .inv-table.is-stacked, .inv-table.is-stacked tbody, .inv-table.is-stacked tr.trow, .inv-table.is-stacked td.td{ display:block; width:100%; }
  .inv-table.is-stacked tr.trow{ padding:10px 14px; }
  .inv-table.is-stacked tr.trow + tr.trow{ border-top:1px solid var(--border); }
  .inv-table.is-stacked td.td{
    border:none; padding:9px 0;
    display:grid; grid-template-columns:minmax(96px,40%) 1fr; gap:6px; align-items:flex-start;
    word-wrap:break-word;
  }
  .inv-table.is-stacked td.td::before{ content:attr(data-label); font-weight:700; color:#6b7280; font-size:12px; }
  .inv-table.is-stacked td.td[data-label="Acciones"]{ grid-template-columns:1fr; }
  .inv-table.is-stacked .cell-actions{ justify-content:flex-start; }
}
</style>

<div class="page-wrap" x-data="MantenimientoUI()">
  {{-- HERO --}}
  <div class="hero">
    <div class="d-flex align-items-center gap-3">
      <div class="chip">
        <i class="bi bi-gear-wide-connected" style="font-size:1.4rem;color:#1d4ed8"></i>
      </div>
      <div>
        <h1 class="h4 mb-0">Mantenimiento interno</h1>
        <div class="small subtle">Monitorea el estado de tus equipos de servicio, doctor y flujos.</div>
      </div>
    </div>

    <div class="hero-actions">
      <div class="search">
        <i class="ico bi bi-search"></i>
        <input type="search" placeholder="Buscar por tipo, serie, marca, modelo, doctor..." x-model="$store.serv.q">
      </div>

      <select class="hero-select" x-model="$store.serv.estado">
        <option value="">Todos los estados</option>
        <option value="registro">Registro</option>
        <option value="salida">Salida</option>
        <option value="regreso">Regreso</option>
        <option value="entregado">Entregado</option>
        <option value="defectuoso">Defectuoso</option>
      </select>

      <button class="btn btn-utility" onclick="location.reload()">
        <i class="bi bi-arrow-clockwise"></i> Actualizar
      </button>

      <a href="{{ url('/servicio') }}" class="btn btn-green btn-utility">
        <i class="bi bi-plus-circle"></i> Agregar registro
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
            <th class="th">Doctor</th>
            <th class="th">Creado</th>
            <th class="th text-end">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($servicios as $s)
            @php
              $estado = $s->estado_proceso ?? 'registro';
              $badgeClass = match($estado){
                'salida','salida_mantenimiento','btn-salida-mantenimiento' => 'badge-salida',
                'regreso','entrada_mantenimiento' => 'badge-regreso',
                'entregado','salida_dueno','btn-salida-dueno' => 'badge-entregado',
                'defectuoso' => 'badge-defectuoso',
                default => 'badge-registro',
              };
              $estadoTexto = ucfirst(str_replace('_',' ',$estado));
            @endphp

            <tr class="trow"
                x-show="filtra(
                  {{ json_encode([
                    'tipo'   => $s->tipo_equipo,
                    'subtipo'=> $s->subtipo_equipo,
                    'marca'  => $s->marca,
                    'modelo' => $s->modelo,
                    'serie'  => $s->numero_serie,
                    'estado' => $estado,
                    'user'   => $s->user_name,
                    'doctor' => $s->nombre_doctor,
                  ]) }},
                  $store.serv.q,
                  $store.serv.estado
                )"
                :style="filtra(
                  {{ json_encode([
                    'tipo'   => $s->tipo_equipo,
                    'subtipo'=> $s->subtipo_equipo,
                    'marca'  => $s->marca,
                    'modelo' => $s->modelo,
                    'serie'  => $s->numero_serie,
                    'estado' => $estado,
                    'user'   => $s->user_name,
                    'doctor' => $s->nombre_doctor,
                  ]) }},
                  $store.serv.q,
                  $store.serv.estado
                ) ? '' : 'display:none !important'">

              {{-- Equipo --}}
              <td class="td" data-label="Equipo">
                <div class="d-flex align-items-center gap-3">
                  <div class="tile-mini">
                    @if($s->evidencia1)
                      <img src="{{ $s->evidencia1 }}" alt="prev">
                    @else
                      <i class="bi bi-gear text-muted"></i>
                    @endif
                  </div>
                  <div>
                    <div class="fw-bold">
                      {{ $s->tipo_equipo ?? 'Equipo' }}
                      @if($s->subtipo_equipo)
                        <span class="text-muted">•</span> {{ $s->subtipo_equipo }}
                      @endif
                    </div>
                    <div class="text-muted small">
                      {{ $s->marca }} {{ $s->modelo }}
                    </div>
                  </div>
                </div>
              </td>

              {{-- Serie --}}
              <td class="td" data-label="Serie">
                <span class="fw-semibold">{{ $s->numero_serie ?? '—' }}</span>
              </td>

              {{-- Estado --}}
              <td class="td" data-label="Estado">
                <span class="badge-state {{ $badgeClass }}">
                  <i class="bi bi-circle-fill" style="font-size:.55rem"></i>
                  <span class="text-capitalize">{{ $estadoTexto }}</span>
                </span>
              </td>

              {{-- Doctor --}}
              <td class="td" data-label="Doctor">
                {{ $s->nombre_doctor ?? '—' }}
              </td>

              {{-- Creado --}}
              <td class="td" data-label="Creado">
                {{ $s->created_at?->format('Y-m-d H:i') ?? '—' }}
              </td>

              {{-- Acciones --}}
              <td class="td" data-label="Acciones">
                <div class="cell-actions">
                  {{-- Ver --}}
                  <a href="{{ route('servicios.show', $s) }}" class="btn btn-blue btn-utility" title="Ver detalle">
                    <i class="bi bi-eye"></i>
                  </a>

                  {{-- Flujos --}}
                  <button type="button"
                          class="btn btn-utility"
                          title="Salida a mantenimiento"
                          onclick="window.location.href='{{ url('/movimientos/salida-mantenimiento/'.$s->id) }}'">
                    <i class="bi bi-box-arrow-right"></i>
                  </button>

                  <button type="button"
                          class="btn btn-utility"
                          title="Regreso de mantenimiento"
                          onclick="window.location.href='{{ url('/movimientos/entrada-mantenimiento/'.$s->id) }}'">
                    <i class="bi bi-arrow-bar-left"></i>
                  </button>

                  <button type="button"
                          class="btn btn-utility"
                          title="Entrega a destinatario"
                          onclick="window.location.href='{{ url('/movimientos/salida-dueno/'.$s->id) }}'">
                    <i class="bi bi-person-check"></i>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr class="trow">
              <td class="td no-results" colspan="6">No hay registros aún en mantenimiento interno.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.store('serv', { q:'', estado:'' });
  Alpine.data('MantenimientoUI', MantenimientoUI);
});

function MantenimientoUI(){
  return {
    isMobile: window.matchMedia('(max-width: 576px)').matches,

    filtra(row, q, estado){
      q = (q || '').toLowerCase().trim();
      estado = (estado || '').toLowerCase().trim();

      const estadoOk = estado
        ? (String(row.estado || '').toLowerCase().includes(estado))
        : true;

      if(!q) return estadoOk;

      const blob = [
        row.tipo, row.subtipo, row.marca, row.modelo,
        row.serie, row.user, row.doctor
      ].join(' ').toLowerCase();

      return estadoOk && blob.includes(q);
    },
  };
}
</script>
@endsection
