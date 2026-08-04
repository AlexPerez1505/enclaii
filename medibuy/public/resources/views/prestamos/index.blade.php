@extends('layouts.app')

@section('title', 'Préstamos')
@section('titulo', 'Préstamos')

@section('content')

<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

<style>
:root{
  --mint:#48cfad; --mint-dark:#34c29e;
  --ink:#2a2e35; --muted:#7a7f87; --line:#e9ecef; --card:#ffffff;
}
*{box-sizing:border-box}
body{font-family:"Open Sans",sans-serif;background:#eaebec}

/* Page */
.edit-wrap{max-width:1100px;margin:110px auto 40px;padding:0 16px;}
.panel{background:var(--card);border-radius:16px;box-shadow:0 16px 40px rgba(18,38,63,.12);overflow:hidden;}
.panel-head{padding:22px 26px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:14px;justify-content:space-between;}
.hgroup h2{margin:0;font-weight:700;color:var(--ink);}
.hgroup p{margin:2px 0 0;color:var(--muted);font-size:14px}
.actions-top{display:flex;gap:10px;flex-wrap:wrap}

/* Toolbar + Search */
.toolbar{padding:18px 26px;display:flex;gap:12px;align-items:center;justify-content:space-between}
.searchbar{display:flex;align-items:center;gap:10px;position:relative}
.icon-btn{
  width:42px;height:42px;border-radius:999px;border:1px solid var(--line);
  background:#fff;display:grid;place-items:center;cursor:pointer;
  transition:transform .1s ease, border-color .2s ease, box-shadow .2s ease;
}
.icon-btn:hover{border-color:#dfe3e8;box-shadow:0 6px 16px rgba(18,38,63,.08)}
.icon-btn:active{transform:scale(.98)}
.searchform .input{
  border:1px solid var(--line);border-radius:12px;padding:10px 12px;background:#fff;
  width:0;opacity:0;pointer-events:none;transition:width .25s ease,opacity .2s ease;
}
.searchbar.open .searchform .input{width:240px;opacity:1;pointer-events:auto}
@media (min-width: 900px){
  .icon-btn{display:none}
  .searchform .input{width:260px;opacity:1;pointer-events:auto}
}
.btn{border:0;border-radius:12px;padding:10px 14px;font-weight:700;cursor:pointer;transition:transform .05s,box-shadow .2s,background .2s,color .2s;}
.btn:active{transform:translateY(1px)}
.btn-primary{background:var(--mint);color:#fff;box-shadow:0 12px 22px rgba(72,207,173,.26);}
.btn-primary:hover{background:var(--mint-dark)}
.btn-ghost{background:#fff;color:var(--ink);border:1px solid var(--line);}
.btn-ghost:hover{border-color:#dfe3e8}
.btn-icon{border:1px solid var(--line);background:#fff;border-radius:10px;padding:8px;display:inline-grid;place-items:center;transition:transform .08s ease,border-color .2s ease;}
.btn-icon:hover{border-color:#dfe3e8;transform:translateY(-1px)}
.btn-danger{background:#ef4444;color:#fff}

/* Table (desktop) */
.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:12px 14px;border-bottom:1px solid #edf0f3;text-align:left;vertical-align:middle}
.table thead th{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.03em}
.row-link{color:inherit;text-decoration:none}
tbody tr{transition:background .2s ease, transform .08s ease}
tbody tr:hover{background:#fafbfc}
tbody tr:active{transform:scale(.999)}

/* Responsive table -> cards */
@media (max-width: 760px){
  .table thead{display:none}
  .table, .table tbody, .table tr, .table td{display:block;width:100%}
  .table tr{
    background:#fff;border:1px solid var(--line);border-radius:12px;
    padding:10px;margin:10px 0;box-shadow:0 10px 22px rgba(18,38,63,.06);
  }
  .table td{
    border-bottom:none;padding:8px 6px;
  }
  .table td::before{
    content: attr(data-label);
    display:block;font-size:11px;color:var(--muted);
    text-transform:uppercase;letter-spacing:.03em;margin-bottom:2px;
  }
  .td-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:8px}
}

/* Pills (estado) */
.pill{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700}
.pill--activo{background:#e7fdf6;color:#127c64}
.pill--retrasado{background:#fff4e5;color:#9a5800}
.pill--devuelto{background:#eef7ff;color:#0b4c8c}
.pill--cancelado{background:#f7f7f8;color:#6a6e76}
.pill--vendido{background:#ffeef0;color:#b6232a}

/* Empty */
.empty{padding:36px;text-align:center;color:var(--muted)}
</style>

<div class="edit-wrap" style="margin-top:25px;">
  <div class="panel">
    <div class="panel-head">
      <div class="hgroup">
        <h2>Préstamos (paquetes)</h2>
        <p>Administra tus paquetes y consulta sus equipos.</p>
      </div>
      <div class="actions-top">
        <a href="{{ route('prestamos.create') }}" class="btn btn-primary">+ Nuevo paquete</a>
      </div>
    </div>

    {{-- Toolbar con búsqueda animada --}}
    <div class="toolbar">
      <div class="searchbar" id="searchbar">
        <button type="button" class="icon-btn" id="searchToggle" aria-label="Buscar">
          {{-- Lupa --}}
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="7"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
        </button>
        <form method="GET" class="searchform" action="{{ route('prestamos.index') }}">
          <input class="input" type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por cliente o folio...">
        </form>
        @if(request('q'))
          <a class="btn btn-ghost" href="{{ route('prestamos.index') }}">Limpiar</a>
        @endif
      </div>
    </div>

    @php
      $list = $prestamos;
      if(request('q')){
        $q = mb_strtolower(request('q'));
        $list = $prestamos->filter(function($p) use ($q){
          $cliente = optional($p->cliente)->nombre ?? '';
          return str_contains((string)$p->id, $q) ||
                 str_contains(mb_strtolower($cliente), $q);
        });
      }
    @endphp

    @if($list->isEmpty())
      <div class="empty">No hay préstamos que mostrar.</div>
    @else
      <div style="overflow-x:auto;">
        <table class="table">
          <thead>
            <tr>
              <th>Folio</th>
              <th>Cliente</th>
              <th>Estado</th>
              <th>Salida</th>
              <th>Regreso est.</th>
              <th>Equipos</th>
              <th>Usuario</th>
              <th style="text-align:right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($list as $p)
              @php
                $pill = match($p->estado){
                  'retrasado'=>'pill--retrasado',
                  'devuelto'=>'pill--devuelto',
                  'cancelado'=>'pill--cancelado',
                  'vendido'=>'pill--vendido',
                  default=>'pill--activo'
                };
              @endphp
              <tr>
                <td data-label="Folio">
                  <a class="row-link" href="{{ route('prestamos.show',$p->id) }}"><strong>#{{ $p->id }}</strong></a>
                </td>
                <td data-label="Cliente">{{ optional($p->cliente)->nombre ?? '—' }}</td>
                <td data-label="Estado"><span class="pill {{ $pill }}">{{ ucfirst($p->estado) }}</span></td>
                <td data-label="Salida">{{ optional($p->fecha_prestamo)->format('Y-m-d') }}</td>
                <td data-label="Regreso est.">{{ optional($p->fecha_devolucion_estimada)->format('Y-m-d') }}</td>
                <td data-label="Equipos">{{ $p->relationLoaded('registros') ? $p->registros->count() : $p->registros()->count() }}</td>
                <td data-label="Usuario">{{ $p->user_name }}</td>
                <td data-label="Acciones" style="text-align:right">
                  <div class="td-actions">
                    <a class="btn-icon" title="Ver" href="{{ route('prestamos.show',$p->id) }}">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                      </svg>
                    </a>
                    <a class="btn-icon" title="Editar" href="{{ route('prestamos.edit',$p->id) }}">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 20h9"/>
                        <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/>
                      </svg>
                    </a>
                    <form action="{{ route('prestamos.destroy',$p->id) }}" method="POST" style="display:inline" onsubmit="return confirmDelete(this);">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn-icon" title="Eliminar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                          <polyline points="3 6 5 6 21 6"/>
                          <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/>
                          <line x1="10" y1="11" x2="10" y2="17"/>
                          <line x1="14" y1="11" x2="14" y2="17"/>
                        </svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>

<script>
// Lupa que abre/cierra
const bar = document.getElementById('searchbar');
const tog = document.getElementById('searchToggle');
tog?.addEventListener('click', ()=>{
  bar.classList.toggle('open');
  if(bar.classList.contains('open')){
    bar.querySelector('input')?.focus();
  }
});
document.addEventListener('click', (e)=>{
  if(!bar.contains(e.target) && window.innerWidth < 900){
    bar.classList.remove('open');
  }
});

function confirmDelete(form){
  return confirm('¿Eliminar este préstamo? Esta acción no se puede deshacer.');
}
</script>
@endsection
