@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
:root{
  --bg:#f4f6f9; --card:#ffffff; --ink:#2a2e35; --muted:#7a7f87; --line:#e9ecef;
  --mint:#48cfad; --mint-dark:#34c29e; --brand:#339af0;
}
*{box-sizing:border-box}
body{font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg)}
.wrap{max-width:1000px;margin:90px auto 40px;padding:0 16px}

.panel{background:var(--card);border-radius:16px;box-shadow:0 16px 40px rgba(18,38,63,.1);overflow:hidden;border:1px solid var(--line)}
.head{padding:20px 22px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:12px}
.head h2{margin:0;color:var(--ink);font-weight:800}
.body{display:grid;grid-template-columns:1.2fr .8fr;gap:18px;padding:18px 22px}
@media (max-width: 980px){ .body{grid-template-columns:1fr} }

.card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:16px}
.h{display:flex;align-items:center;justify-content:space-between;margin:0 0 10px}
.h h3{margin:0;color:#111827;font-size:16px}
.help{font-size:12px;color:var(--muted)}

.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
@media (max-width: 700px){ .grid{grid-template-columns:1fr} }

.field{display:grid;gap:6px}
.label{font-size:12px;color:var(--muted)}
.input, .select, textarea{
  width:100%;border:1px solid var(--line);border-radius:12px;padding:12px 14px;
  outline:none;transition:border .2s, box-shadow .2s;background:#fff;color:var(--ink);
}
.input:focus, .select:focus, textarea:focus{border-color:var(--brand);box-shadow:0 0 0 4px rgba(51,154,240,.12)}
.error{color:#e03131;font-size:12px}

.kpi{display:grid;gap:10px}
.totalCard{border:1px solid var(--line);border-radius:14px;padding:14px;background:#f8fafc}
.totalCard b{font-size:22px}
.badge{display:inline-block;border:1px solid var(--line);border-radius:999px;padding:3px 10px;font-size:12px;background:#fff}
.btn{border:0;border-radius:12px;padding:12px 14px;font-weight:800;cursor:pointer;transition:transform .05s,box-shadow .2s}
.btn:active{transform:translateY(1px)}
.btn-primary{background:var(--brand);color:#fff;box-shadow:0 10px 22px rgba(51,154,240,.25)}
.btn-primary:hover{background:#1c7ed6}
.btn-ghost{background:#fff;border:1px solid var(--line)}
.rowActions{display:flex;gap:10px;flex-wrap:wrap}
</style>

<div class="wrap">
  <form id="cuentaForm" action="{{ route('cuentas.update', $cuenta->id) }}" method="POST" novalidate>
    @csrf
    @method('PUT')

    <div class="panel">
      <div class="head">
        <h2>Editar cuenta #{{ $cuenta->id }}</h2>
        <div class="rowActions">
          <a class="btn btn-ghost" href="{{ route('cuentas.index') }}">Volver</a>
          <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
      </div>

      <div class="body">
        {{-- Columna izquierda --}}
        <div class="card">
          <div class="h">
            <h3>Datos del viaje</h3>
          </div>

          <div class="grid">
            {{-- Lugar --}}
            <div class="field" style="grid-column:1/-1">
              <label class="label" for="lugar">Lugar</label>
              <input class="input" id="lugar" name="lugar" type="text" value="{{ old('lugar', $cuenta->lugar) }}" required>
              @error('lugar') <div class="error">{{ $message }}</div> @enderror
            </div>

            {{-- Camioneta (solo Crafter y Caddy) --}}
            <div class="field">
              <label class="label" for="camioneta">Camioneta</label>
              <select class="select" id="camioneta" name="camioneta" required>
                @php $cam = old('camioneta', $cuenta->camioneta); @endphp
                <option value="Crafter" @selected($cam==='Crafter')>Crafter</option>
                <option value="Caddy"   @selected($cam==='Caddy')>Caddy</option>
              </select>
              @error('camioneta') <div class="error">{{ $message }}</div> @enderror
              <div class="help">Puedes ajustar la gasolina manualmente.</div>
            </div>

            {{-- Casetas --}}
            <div class="field">
              <label class="label" for="casetas">Casetas</label>
              <input class="input" id="casetas" name="casetas" type="number" step="0.01" value="{{ old('casetas', $cuenta->casetas) }}" required>
              @error('casetas') <div class="error">{{ $message }}</div> @enderror
            </div>

            {{-- Gasolina --}}
            <div class="field">
              <label class="label" for="gasolina">Gasolina</label>
              <input class="input" id="gasolina" name="gasolina" type="number" step="0.01" value="{{ old('gasolina', $cuenta->gasolina) }}" required>
              @error('gasolina') <div class="error">{{ $message }}</div> @enderror
            </div>

            {{-- Viáticos --}}
            <div class="field">
              <label class="label" for="viaticos">Viáticos</label>
              <input class="input" id="viaticos" name="viaticos" type="number" step="0.01" value="{{ old('viaticos', $cuenta->viaticos) }}" required>
              @error('viaticos') <div class="error">{{ $message }}</div> @enderror
            </div>

            {{-- Adicional --}}
            <div class="field">
              <label class="label" for="adicional">Adicional</label>
              <input class="input" id="adicional" name="adicional" type="number" step="0.01" value="{{ old('adicional', $cuenta->adicional) }}" min="0">
              @error('adicional') <div class="error">{{ $message }}</div> @enderror
            </div>

            {{-- Descripción adicional --}}
            @php
              $showDesc = (float) old('adicional', $cuenta->adicional) > 0;
            @endphp
            <div class="field" id="descripcionGroup" style="grid-column:1/-1;{{ $showDesc ? '' : 'display:none;' }}">
              <label class="label" for="descripcion">Descripción del adicional</label>
              <textarea class="input" id="descripcion" name="descripcion" rows="2">{{ old('descripcion', $cuenta->descripcion) }}</textarea>
              @error('descripcion') <div class="error">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>

        {{-- Columna derecha --}}
        <div class="kpi">
          <div class="card totalCard">
            <div class="h">
              <h3>Resumen</h3>
              <span class="badge">Se suma + $500</span>
            </div>
            <div class="field">
              <label class="label">Total</label>
              <input class="input" id="total" name="total" type="text" readonly>
            </div>
            <div class="help">casetas + gasolina + viáticos + adicional + 500</div>
          </div>

          <div class="card">
            <div class="h"><h3>Acciones</h3></div>
            <div class="rowActions">
              <a class="btn btn-ghost" href="{{ route('cuentas.index') }}">Cancelar</a>
              <button type="submit" class="btn btn-primary" style="flex:1">Actualizar cuenta</button>
            </div>
          </div>
        </div>

      </div> {{-- /body --}}
    </div> {{-- /panel --}}
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const camioneta = document.getElementById('camioneta');
  const casetas = document.getElementById('casetas');
  const gasolina = document.getElementById('gasolina');
  const viaticos = document.getElementById('viaticos');
  const adicional = document.getElementById('adicional');
  const descripcion = document.getElementById('descripcion');
  const descripcionGroup = document.getElementById('descripcionGroup');
  const total = document.getElementById('total');

  // Sugerencias de gasolina SOLO para Crafter y Caddy
  const sugerenciasGasolina = {
    'Crafter': 2000,
    'Caddy'  : 1200,
  };

  function calcularTotal() {
    const n = x => parseFloat(x || 0);
    const suma = n(casetas.value) + n(gasolina.value) + n(viaticos.value) + n(adicional.value) + 500;
    total.value = suma.toFixed(2);
  }

  function toggleDescripcion() {
    if (parseFloat(adicional.value) > 0) {
      descripcionGroup.style.display = 'block';
    } else {
      descripcionGroup.style.display = 'none';
      if (descripcion) descripcion.value = '';
    }
  }

  camioneta?.addEventListener('change', (e) => {
    const sug = sugerenciasGasolina[e.target.value];
    if (typeof sug !== 'undefined') {
      gasolina.value = String(sug);
      calcularTotal();
    }
  });

  [casetas, gasolina, viaticos, adicional].forEach(el => {
    el.addEventListener('input', () => {
      calcularTotal();
      if (el === adicional) toggleDescripcion();
    });
    el.addEventListener('change', () => {
      calcularTotal();
      if (el === adicional) toggleDescripcion();
    });
  });

  // Inicial
  calcularTotal();
  toggleDescripcion();
});
</script>
@endsection
