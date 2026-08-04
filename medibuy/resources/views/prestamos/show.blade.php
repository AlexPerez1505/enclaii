@extends('layouts.app')

@section('title', 'Préstamos')
@section('titulo', 'Préstamos')

@section('content')
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

<style>
:root{
  --mint:#48cfad; --mint-dark:#34c29e;
  --ink:#2a2e35; --muted:#7a7f87; --line:#e9ecef; --card:#ffffff;
  --ok:#dcfce7; --ok-ink:#166534; --warn:#fef3c7; --warn-ink:#92400e; --sold:#fee2e2; --sold-ink:#991b1b;
}
*{box-sizing:border-box}
body{font-family:"Open Sans",sans-serif;background:#eaebec}

.edit-wrap{max-width:1100px;margin:110px auto 40px;padding:0 16px;}
.panel{background:var(--card);border-radius:16px;box-shadow:0 16px 40px rgba(18,38,63,.12);overflow:hidden;}
.panel-head{padding:22px 26px;border-bottom:1px solid var(--line);display:flex;align-items:center;gap:14px;justify-content:space-between;}
.hgroup h2{margin:0;font-weight:700;color:var(--ink);}
.hgroup p{margin:2px 0 0;color:var(--muted);font-size:14px}
.back-link{display:inline-flex;align-items:center;gap:8px;color:var(--muted);text-decoration:none;padding:8px 12px;border-radius:10px;border:1px solid var(--line);background:#fff;}
.back-link:hover{color:var(--ink);border-color:#dfe3e8}

.body{padding:22px 26px;display:grid;grid-template-columns:1fr 380px;gap:22px}
@media (max-width: 1000px){ .body{grid-template-columns:1fr} }

/* Bloques y campos */
.block{border:1px dashed #dfe3e8;border-radius:14px;padding:16px;background:#fafbfc;}
.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
@media (max-width: 700px){ .grid{grid-template-columns:1fr} }
.field{background:#fff;border:1px solid var(--line);border-radius:12px;padding:12px 14px;}
.field .label{font-size:12px;color:var(--muted);margin-bottom:6px}
.field .value{font-weight:700;color:var(--ink)}

/* Tabla */
.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:10px 12px;border-bottom:1px solid #edf0f3;text-align:left;vertical-align:top}
.table thead th{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.03em}
tbody tr{transition:background .2s ease}
tbody tr:hover{background:#fafbfc}
.wrap{word-break:break-word}

/* Responsive table -> cards */
@media (max-width: 760px){
  .table thead{display:none}
  .table, .table tbody, .table tr, .table td{display:block;width:100%}
  .table tr{
    background:#fff;border:1px solid var(--line);border-radius:12px;
    padding:10px;margin:10px 0;box-shadow:0 10px 22px rgba(18,38,63,.06);
  }
  .table td{border-bottom:none;padding:8px 6px;}
  .table td::before{
    content: attr(data-label);
    display:block;font-size:11px;color:var(--muted);
    text-transform:uppercase;letter-spacing:.03em;margin-bottom:2px;
  }
}

/* Pills estado */
.pill{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700}
.pill--activo{background:#e7fdf6;color:#127c64}
.pill--retrasado{background:#fff4e5;color:#9a5800}
.pill--devuelto{background:#eef7ff;color:#0b4c8c}
.pill--cancelado{background:#f7f7f8;color:#6a6e76}
.pill--vendido{background:#ffeef0;color:#b6232a}

/* Buttons */
.btn{border:0;border-radius:12px;padding:10px 14px;font-weight:700;cursor:pointer;transition:transform .05s,box-shadow .2s,background .2s,color .2s;}
.btn:active{transform:translateY(1px)}
.btn-primary{background:var(--mint);color:#fff;box-shadow:0 12px 22px rgba(72,207,173,.26);}
.btn-primary:hover{background:var(--mint-dark)}
.btn-ghost{background:#fff;color:#000;border:1px solid var(--line);}
.btn-ghost:hover{border-color:#dfe3e8}
.btn-danger{background:#ef4444;color:#fff}
.actions{display:flex;gap:10px;flex-wrap:wrap}

/* Chips y KPIs */
.chips{display:flex;flex-wrap:wrap;gap:8px}
.chip{border-radius:999px;padding:6px 10px;font-size:12px;border:1px solid var(--line);background:#fff}
.chip--warn{background:var(--warn);color:var(--warn-ink);border-color:#f5d48a}
.chip--sold{background:var(--sold);color:var(--sold-ink);border-color:#f5bdbd}

.kpis{display:grid;grid-template-columns:repeat(4, minmax(0,1fr));gap:10px;margin-top:10px}
.kpi{background:#fff;border:1px solid var(--line);border-radius:12px;padding:10px;text-align:center}
.kpi b{display:block;font-size:20px;color:var(--ink)}
.kpi span{font-size:12px;color:var(--muted)}

/* Side */
.side{display:grid;gap:14px}
.thumb{border:1px solid #edf0f3;border-radius:12px;overflow:hidden;background:#fff;display:grid;place-items:center}
.thumb img{width:100%;height:auto;display:block}

/* Print */
@media print{
  body{background:#fff}
  .panel{box-shadow:none;border-radius:0}
  .panel-head .actions, .back-link{display:none !important}
  .edit-wrap{margin:0;max-width:none}
  .body{grid-template-columns:1fr}
}
</style>

@php
  // === Métricas para el resumen (no JS) ===
  $total     = $prestamo->registros->count();
  $salidos   = $prestamo->registros->whereNotNull('pivot.salida_scanned_at')->count();
  $devueltos = $prestamo->registros->whereNotNull('pivot.devolucion_scanned_at')->count();
  $vendidos  = $prestamo->registros->whereNotNull('pivot.vendido_scanned_at')->count();

  // Faltantes de devolución: no devueltos y no vendidos
  $faltanDevol = $prestamo->registros->filter(function($r){
      return empty($r->pivot->devolucion_scanned_at) && empty($r->pivot->vendido_scanned_at);
  });

  // Vendidos list
  $listVendidos = $prestamo->registros->whereNotNull('pivot.vendido_scanned_at');
@endphp

<div class="edit-wrap" style="margin-top:25px;">
  <div class="panel">
    <div class="panel-head">
      <div class="hgroup">
        <h2>Paquete #{{ $prestamo->id }}</h2>
        <p>Detalle del préstamo y equipos asociados.</p>
      </div>
      <div class="actions">
        <a href="{{ route('prestamos.index') }}" class="back-link" title="Volver">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
          Volver
        </a>

        {{-- Registrar regreso (si no está devuelto y tiene equipos) --}}
        @if($prestamo->estado !== 'devuelto' && $prestamo->registros->count() > 0)
          <a class="btn btn-primary" href="{{ route('prestamos.return.page', $prestamo->id) }}" title="Registrar regreso">
            Registrar regreso
          </a>
        @endif

        {{-- Descargar PDF --}}
        <a class="btn btn-ghost" href="{{ route('prestamos.pdf', $prestamo->id) }}" title="Descargar PDF">
          Descargar PDF
        </a>

        <a class="btn btn-ghost" href="{{ route('prestamos.edit', $prestamo->id) }}">Editar</a>
        <form action="{{ route('prestamos.destroy', $prestamo->id) }}" method="POST" onsubmit="return confirm('¿Eliminar este préstamo?');" style="display:inline">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger">Eliminar</button>
        </form>
      </div>
    </div>

    <div class="body">
      {{-- ===== LEFT ===== --}}
      <div>
        {{-- ===== Resumen del paquete (nuevo) ===== --}}
        <div class="block" style="margin-bottom:18px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
            <strong>Resumen del paquete</strong>
            <span class="small">{{ $total }} equipo(s)</span>
          </div>

          <div class="kpis">
            <div class="kpi"><b>{{ $total }}</b><span>Total</span></div>
            <div class="kpi"><b>{{ $salidos }}</b><span>Salidos</span></div>
            <div class="kpi"><b>{{ $devueltos }}</b><span>Devueltos</span></div>
            <div class="kpi"><b>{{ $vendidos }}</b><span>Vendidos</span></div>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
            <div>
              <div class="small" style="margin-bottom:6px;"><strong>Faltantes de devolución</strong></div>
              <div class="chips">
                @forelse($faltanDevol as $r)
                  <span class="chip chip--warn">• {{ $r->numero_serie }}</span>
                @empty
                  <span class="small">— Ninguno —</span>
                @endforelse
              </div>
            </div>
            <div>
              <div class="small" style="margin-bottom:6px;"><strong>Vendidos</strong></div>
              <div class="chips">
                @forelse($listVendidos as $r)
                  <span class="chip chip--sold">• {{ $r->numero_serie }}</span>
                @empty
                  <span class="small">— Ninguno —</span>
                @endforelse
              </div>
            </div>
          </div>
        </div>

        {{-- ===== Datos del préstamo (lo que ya tenías) ===== --}}
        <div class="grid">
          <div class="field">
            <div class="label">Cliente</div>
            <div class="value">{{ optional($prestamo->cliente)->nombre ?? '—' }}</div>
          </div>
          <div class="field">
            <div class="label">Estado</div>
            @php
              $cl = match($prestamo->estado){
                'retrasado'=>'pill--retrasado',
                'devuelto'=>'pill--devuelto',
                'cancelado'=>'pill--cancelado',
                'vendido'=>'pill--vendido',
                default=>'pill--activo'
              };
            @endphp
            <div class="value"><span class="pill {{ $cl }}">{{ ucfirst($prestamo->estado) }}</span></div>
          </div>
          <div class="field">
            <div class="label">Fecha de salida</div>
            <div class="value">{{ optional($prestamo->fecha_prestamo)->format('Y-m-d') }}</div>
          </div>
          <div class="field">
            <div class="label">Regreso estimado</div>
            <div class="value">{{ optional($prestamo->fecha_devolucion_estimada)->format('Y-m-d') }}</div>
          </div>
          @if($prestamo->fecha_devolucion_real)
          <div class="field">
            <div class="label">Regreso real</div>
            <div class="value">{{ optional($prestamo->fecha_devolucion_real)->format('Y-m-d') }}</div>
          </div>
          @endif
          <div class="field" style="grid-column:1/-1">
            <div class="label">Observaciones</div>
            <div class="value">{{ $prestamo->observaciones ?: '—' }}</div>
          </div>
          <div class="field">
            <div class="label">Usuario</div>
            <div class="value">{{ $prestamo->user_name }}</div>
          </div>
          <div class="field">
            <div class="label">Creado</div>
            <div class="value">{{ optional($prestamo->created_at)->format('Y-m-d H:i') }}</div>
          </div>
        </div>

        {{-- ===== Tabla de equipos (mantiene lo previo + columnas nuevas) ===== --}}
        <div class="block" style="margin-top:18px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
            <strong>Equipos en el paquete</strong>
            <span class="small">{{ $prestamo->registros->count() }} equipo(s)</span>
          </div>
          <div style="overflow-x:auto;">
            <table class="table">
              <thead>
                <tr>
                  <th style="width:60px">#</th>
                  <th>Serie</th>
                  <th>Subtipo</th>
                  <th>Marca</th>
                  <th>Modelo</th>
                  <th>Salida</th>
                  <th>Devolución</th>
                  <th>Vendido</th>
                </tr>
              </thead>
              <tbody>
                @forelse($prestamo->registros as $i => $r)
                  <tr>
                    <td data-label="#">{{ $i+1 }}</td>
                    <td data-label="Serie"><code class="wrap">{{ $r->numero_serie }}</code></td>
                    <td data-label="Subtipo" class="wrap">{{ $r->subtipo_equipo ?? '—' }}</td>
                    <td data-label="Marca" class="wrap">{{ $r->marca ?? '—' }}</td>
                    <td data-label="Modelo" class="wrap">{{ $r->modelo ?? '—' }}</td>
                    <td data-label="Salida">
                      {{ $r->pivot->salida_scanned_at ? \Carbon\Carbon::parse($r->pivot->salida_scanned_at)->format('Y-m-d H:i') : '—' }}
                    </td>
                    <td data-label="Devolución">
                      {{ $r->pivot->devolucion_scanned_at ? \Carbon\Carbon::parse($r->pivot->devolucion_scanned_at)->format('Y-m-d H:i') : '—' }}
                    </td>
                    <td data-label="Vendido">
                      {{ $r->pivot->vendido_scanned_at ? \Carbon\Carbon::parse($r->pivot->vendido_scanned_at)->format('Y-m-d H:i') : '—' }}
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="8" class="small">No hay equipos vinculados.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- ===== RIGHT ===== --}}
      <div class="side">
        <div class="field">
          <div class="label">Estado del paquete</div>
          @php
            $cl = match($prestamo->estado){
              'retrasado'=>'pill--retrasado',
              'devuelto'=>'pill--devuelto',
              'cancelado'=>'pill--cancelado',
              'vendido'=>'pill--vendido',
              default=>'pill--activo'
            };
          @endphp
          <div class="value"><span class="pill {{ $cl }}">{{ ucfirst($prestamo->estado) }}</span></div>
        </div>

        <div class="field">
          <div class="label">Firma digital</div>
          <div class="thumb">
            @if($prestamo->firmaDigital)
              <img src="{{ $prestamo->firmaDigital }}" alt="Firma">
            @else
              <div class="small">Sin firma</div>
            @endif
          </div>
          <div class="small" style="margin-top:8px;">Ruta: /storage/firmas</div>
        </div>

        <div class="field">
          <div class="label">Fechas</div>
          <div class="small">
            Salida: <strong>{{ optional($prestamo->fecha_prestamo)->format('Y-m-d') }}</strong><br>
            Est. regreso: <strong>{{ optional($prestamo->fecha_devolucion_estimada)->format('Y-m-d') }}</strong><br>
            @if($prestamo->fecha_devolucion_real)
              Regreso real: <strong>{{ optional($prestamo->fecha_devolucion_real)->format('Y-m-d') }}</strong>
            @endif
          </div>
        </div>

        <div class="field">
          <div class="label">Usuario</div>
          <div class="value">{{ $prestamo->user_name }}</div>
        </div>

        <div class="field">
          <div class="label">Creado</div>
          <div class="value">{{ optional($prestamo->created_at)->format('Y-m-d H:i') }}</div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
