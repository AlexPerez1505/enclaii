@extends('layouts.app')
@section('title','Paqueterias')
@section('titulo','Paqueterias')

@section('content')
<style>
  :root{
    --bg:#f5f9ff; --card:#ffffff; --ink:#123b66; --muted:#5b728a; --line:#dbeafe;
    --brand:#cfe7ff; --brand-ink:#0b4a8f; --radius:16px; --shadow:0 12px 32px rgba(8,32,67,.08);
    --ok:#c7f9cc; --ok-ink:#166534;
  }
  body{ background:var(--bg); }
  .wrap{ max-width:1100px; margin:18px auto; padding:0 12px; }

  /* Toolbar SOLO buscador */
  .toolbar{
    position:sticky; top:8px; z-index:10;
    background:linear-gradient(180deg, rgba(207,231,255,.9), rgba(255,255,255,.95));
    border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow);
    padding:12px; margin-bottom:12px;
  }
  .searchbar{ display:block; }
  .search-field{ position:relative; }
  .search-field input{
    width:100%; border:1px solid var(--line); border-radius:14px;
    padding:12px 44px 12px 40px;  /* espacio: izquierda lupa, derecha spinner */
    font-size:14px; color:var(--ink); background:#fff; outline:none;
  }
  /* Lupa (favicon style) */
  .search-field .ico{
    position:absolute; left:12px; top:50%; transform:translateY(-50%);
    width:18px; height:18px; display:inline-block; pointer-events:none; opacity:.9;
  }
  /* Spinner sutil a la derecha mientras debounce */
  .search-field .spin{
    position:absolute; right:12px; top:50%; transform:translateY(-50%);
    width:16px; height:16px; border:2px solid #bee3ff; border-top-color:#0b4a8f;
    border-radius:50%; animation: spin .8s linear infinite; display:none;
  }
  .search-field.loading .spin{ display:block; }
  @keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }

  /* KPIs + CTA */
  .kpis{ display:flex; gap:10px; flex-wrap:wrap; margin:10px 0; }
  .kpi{
    background:#eff6ff; border:1px dashed var(--line); border-radius:12px; padding:10px 12px;
    font-size:13px; color:var(--ink);
  }
  .cta-right{ margin-left:auto; }
  .btn{
    border:1px solid var(--line); border-radius:12px; padding:10px 16px;
    font-weight:800; background:var(--brand); color:var(--brand-ink);
    text-decoration:none; display:inline-flex; align-items:center; justify-content:center; gap:8px; box-shadow:var(--shadow);
  }

  .card{ background:var(--card); border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow); }

  /* Tabla (desktop) */
  .tablewrap{ overflow:auto; }
  table{ width:100%; border-collapse:collapse; border:1px solid var(--line); border-radius:12px; overflow:hidden; }
  thead th{ background:#eff6ff; color:var(--ink); text-align:left; font-size:13px; }
  th, td{ padding:10px 10px; border-bottom:1px solid var(--line); white-space:nowrap; font-size:14px; }
  tbody tr:hover{ background:#f7fbff; }

  /* Cards (móvil) */
  .cards{ display:none; gap:10px; }
  .ship-card{
    background:#fff; border:1px solid var(--line); border-radius:14px; padding:12px;
    display:grid; gap:6px;
  }
  .ship-head{ display:flex; justify-content:space-between; gap:8px; align-items:center; }
  .chip{ background:#dbeafe; color:#0b4a8f; border-radius:999px; padding:4px 8px; font-size:11px; font-weight:800; }
  .ship-meta{ color:var(--muted); font-size:12px; display:flex; gap:10px; flex-wrap:wrap; }

  .ok{ background:var(--ok); color:var(--ok-ink); border:1px solid #dcfce7; }

  /* Responsive */
  @media (max-width: 860px){
    .tablewrap{ display:none; }
    .cards{ display:grid; }
    .cta-right{ width:100%; margin-left:0; }
    .btn{ width:100%; }
  }
</style>

<div class="wrap" style="margin-top:100px;">
  <!-- Toolbar con SOLO buscador (sin botón) -->
  <div class="toolbar">
    <form class="searchbar" action="{{ route('envios-gastos.index') }}" method="GET" id="autoSearchForm">
      <div class="search-field" id="searchField">
        <!-- Icono lupa SVG -->
        <svg class="ico" viewBox="0 0 24 24" aria-hidden="true">
          <circle cx="11" cy="11" r="7" fill="none" stroke="#0b4a8f" stroke-width="2"></circle>
          <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="#0b4a8f" stroke-width="2" stroke-linecap="round"></line>
        </svg>
        <input
          type="text"
          name="q"
          value="{{ $q }}"
          placeholder="Buscar: referencia, destino, sucursal, transportista (escribe para buscar)"
          aria-label="Buscar envíos"
          autocomplete="off"
          id="qInput"
        >
        <span class="spin" aria-hidden="true"></span>
      </div>
    </form>

    <!-- KPIs + CTA -->
    <div class="kpis">
      <div class="kpi">Registros: <strong>{{ number_format($conteo) }}</strong></div>
      <div class="kpi">Gasto total: <strong>${{ number_format($totalGasto,2) }} MXN</strong></div>
      <div class="kpi">Promedio: <strong>${{ number_format($conteo ? ($totalGasto/$conteo) : 0,2) }} MXN</strong></div>
      <div class="cta-right">
        <a class="btn" href="{{ route('envios-gastos.create') }}">+ Registrar envío</a>
      </div>
    </div>

    @if (session('ok'))
      <div class="kpi ok">{{ session('ok') }}</div>
    @endif
  </div>

  <!-- Tabla (desktop) -->
  <div class="card">
    <div class="tablewrap">
      <table>
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Referencia</th>
            <th>Sucursal</th>
            <th>Destino</th>
            <th>Transp.</th>
            <th>Peso (kg)</th>
            <th>Vol (kg)</th>
            <th>Facturable (kg)</th>
            <th>Costo MXN</th>
            <th>Notas</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($envios as $e)
            <tr>
              <td>{{ optional($e->fecha_envio)->format('Y-m-d') }}</td>
              <td>{{ $e->referencia }}</td>
              <td>{{ $e->sucursal }}</td>
              <td>{{ $e->destino }}</td>
              <td>{{ $e->transportista }}</td>
              <td>{{ number_format($e->peso_kg ?? 0, 2) }}</td>
              <td>{{ number_format($e->peso_volumetrico_kg ?? 0, 2) }}</td>
              <td>{{ number_format($e->peso_facturable_kg ?? 0, 2) }}</td>
              <td><strong>${{ number_format($e->costo_mxn, 2) }}</strong></td>
              <td style="max-width:340px; white-space:normal">{{ $e->notas }}</td>
            </tr>
          @empty
            <tr><td colspan="10" style="color:var(--muted)">Sin resultados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Cards (móvil) -->
    <div class="cards" style="padding:12px">
      @forelse ($envios as $e)
        <div class="ship-card">
          <div class="ship-head">
            <div>
              <strong style="color:var(--ink)">{{ $e->referencia ?? 'Sin ref.' }}</strong>
              <div class="ship-meta">
                <span>{{ optional($e->fecha_envio)->format('Y-m-d') }}</span>
                <span>•</span>
                <span>{{ $e->sucursal }}</span>
              </div>
            </div>
            <span class="chip">${{ number_format($e->costo_mxn,2) }}</span>
          </div>
          <div class="ship-meta">
            <span>Destino: <strong style="color:var(--ink)">{{ $e->destino ?? '—' }}</strong></span>
            <span>Transp.: <strong style="color:var(--ink)">{{ $e->transportista ?? '—' }}</strong></span>
          </div>
          <div class="ship-meta">
            <span>Peso: <strong style="color:var(--ink)">{{ number_format($e->peso_kg ?? 0,2) }}</strong> kg</span>
            <span>Vol: <strong style="color:var(--ink)">{{ number_format($e->peso_volumetrico_kg ?? 0,2) }}</strong> kg</span>
            <span>Fact.: <strong style="color:var(--ink)">{{ number_format($e->peso_facturable_kg ?? 0,2) }}</strong> kg</span>
          </div>
          @if($e->notas)
            <div class="ship-meta" style="white-space:normal">{{ $e->notas }}</div>
          @endif
        </div>
      @empty
        <div class="ship-card" style="color:var(--muted)">Sin resultados.</div>
      @endforelse
    </div>

    <div style="padding:12px">
      {{ $envios->links() }}
    </div>
  </div>
</div>

<!-- Auto-búsqueda (debounce 500ms, sin botón) -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('qInput');
    const form  = document.getElementById('autoSearchForm');
    const field = document.getElementById('searchField');
    let composing = false;
    let t;

    if(!input || !form) return;

    input.addEventListener('compositionstart', () => composing = true);
    input.addEventListener('compositionend',   () => { composing = false; trigger(); });

    input.addEventListener('input', () => {
      if (composing) return;
      trigger();
    });

    input.addEventListener('keydown', (e) => {
      // ESC para limpiar y buscar
      if (e.key === 'Escape') {
        input.value = '';
        trigger();
      }
    });

    function trigger(){
      clearTimeout(t);
      field.classList.add('loading');
      t = setTimeout(() => {
        // requestSubmit para respetar método=GET de Laravel
        if (form.requestSubmit) form.requestSubmit();
        else form.submit();
      }, 500);
    }
  });
</script>
@endsection
