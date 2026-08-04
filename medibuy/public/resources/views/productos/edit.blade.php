@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

<style>
:root{
  --mint:#48cfad;
  --mint-dark:#34c29e;
  --ink:#2a2e35;
  --muted:#7a7f87;
  --line:#e9ecef;
  --card:#ffffff;
}
*{box-sizing:border-box}
body{font-family:"Open Sans",sans-serif;background:#eaebec}

/* Page */
.edit-wrap{ max-width:980px;margin:110px auto 40px;padding:0 16px; }
.panel{ background:var(--card); border-radius:16px; box-shadow:0 16px 40px rgba(18,38,63,.12); overflow:hidden; }
.panel-head{ padding:22px 26px; border-bottom:1px solid var(--line); display:flex;align-items:center;gap:14px;justify-content:space-between; }
.hgroup h2{margin:0;font-weight:700;color:var(--ink);}
.hgroup p{margin:2px 0 0;color:var(--muted);font-size:14px}
.back-link{ display:inline-flex;align-items:center;gap:8px; color:var(--muted);text-decoration:none;padding:8px 12px;border-radius:10px; border:1px solid var(--line);background:#fff; }
.back-link:hover{color:var(--ink);border-color:#dfe3e8}

/* Form */
.form{ padding:26px; }
.grid{ display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px; }
@media (max-width: 800px){ .grid{grid-template-columns:1fr} }

.field{
  position:relative;background:#fff;border:1px solid var(--line);
  border-radius:12px;padding:16px 14px 10px;transition:box-shadow .2s,border-color .2s;
}
.field:focus-within{border-color:#d8dee6;box-shadow:0 8px 24px rgba(18,38,63,.08)}
.field input{
  width:100%;border:0;outline:0;background:transparent;
  font-size:15px;color:var(--ink);padding-top:10px;
}
.field label{
  position:absolute;left:14px;top:14px;color:var(--muted);font-size:13px;
  transition:transform .15s ease, color .15s ease, font-size .15s ease, top .15s ease;
  pointer-events:none;
}
.field input::placeholder{color:transparent;}
.field input:focus + label,
.field input:not(:placeholder-shown) + label{
  top:8px;transform:translateY(-10px);font-size:11px;color:var(--mint-dark);
}

/* Price adornment */
.field .prefix{
  position:absolute;right:14px;top:50%;transform:translateY(-10%);
  color:#a2a7ae;font-size:13px;
}

/* Select familias */
.field-select{ border:1px solid var(--line);border-radius:12px;padding:12px 14px;background:#fff; }
.field-select label{ display:block;color:var(--muted);font-size:12px;margin-bottom:6px;font-weight:600; }
.select-multi{
  width:100%;border:1px solid #e6e9ee;border-radius:10px;padding:8px 10px;min-height:44px;
  outline:none;background:#fafbfc;font-size:14px;
}
.hint{color:var(--muted);font-size:12px;margin-top:6px}
.chips{ display:flex;gap:6px;flex-wrap:wrap;margin-top:8px; }
.chip{
  display:inline-flex;align-items:center;gap:6px;
  background:#eef2ff;border:1px solid #e5e7eb;color:#374151;
  padding:4px 8px;border-radius:999px;font-size:12px;
}
.chip button{
  border:none;background:transparent;color:#6b7280;cursor:pointer;font-size:13px;line-height:1;
}
.chip button:hover{ color:#111827 }

/* Dropzone / Image */
.block{ border:1px dashed #dfe3e8;border-radius:14px;padding:16px;background:#fafbfc; }
.uploader{ display:grid;grid-template-columns:140px 1fr;gap:16px;align-items:center; }
@media (max-width: 600px){ .uploader{grid-template-columns:1fr} }
.thumb{ width:140px;height:140px;border-radius:12px;overflow:hidden;background:#f0f2f5; display:grid;place-items:center;border:1px solid #edf0f3; }
.thumb img{width:100%;height:100%;object-fit:cover}
.drop{ display:flex;align-items:center;gap:14px;flex-wrap:wrap; }
.input-file{display:none}
.drop .btn{
  background:var(--mint);color:#fff;border:none;border-radius:999px;
  padding:10px 16px;cursor:pointer;box-shadow:0 10px 20px rgba(72,207,173,.25);
}
.drop .btn:hover{background:var(--mint-dark)}
.small{color:var(--muted);font-size:12px}

/* Actions */
.actions{ display:flex;gap:12px;justify-content:flex-end;margin-top:10px;padding:0 26px 26px; }
.btn{ border:0;border-radius:12px;padding:12px 18px;font-weight:700;cursor:pointer; transition:transform .05s ease, box-shadow .2s ease, background .2s ease,color .2s ease; }
.btn:active{transform:translateY(1px)}
.btn-primary{ background:var(--mint);color:#fff;box-shadow:0 12px 22px rgba(72,207,173,.26); }
.btn-primary:hover{background:var(--mint-dark)}
.btn-ghost{ background:#fff;color:var(--ink);border:1px solid var(--line); }
.btn-ghost:hover{border-color:#dfe3e8}

/* Error styles */
.is-invalid{border-color:#f9c0c0 !important}
.error{color:#cc4b4b;font-size:12px;margin-top:6px}
</style>

<div class="edit-wrap">
  <div class="panel">
    <div class="panel-head">
      <div class="hgroup">
        <h2>Editar producto</h2>
        <p>Actualiza la información y la imagen del producto.</p>
      </div>
      <a href="{{ url()->previous() }}" class="back-link" title="Volver">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Volver
      </a>
    </div>

    @php
      // Fallback por si el controlador no envía $familias (mejor pasarlo desde el controlador)
      $familias = $familias ?? \App\Models\Familia::orderBy('nombre')->get();
      $familiasSeleccionadas = old('familias', isset($producto->familias) ? $producto->familias->pluck('id')->toArray() : []);
    @endphp

    <form class="form" action="{{ route('productos.update', $producto->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="grid">
        {{-- Tipo de equipo --}}
        <div>
          <div class="field @error('tipo_equipo') is-invalid @enderror">
            <input type="text" name="tipo_equipo" id="f-tipo" value="{{ old('tipo_equipo', $producto->tipo_equipo) }}" placeholder=" " required>
            <label for="f-tipo">Tipo de equipo</label>
          </div>
          @error('tipo_equipo')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- Marca --}}
        <div>
          <div class="field @error('marca') is-invalid @enderror">
            <input type="text" name="marca" id="f-marca" value="{{ old('marca', $producto->marca) }}" placeholder=" ">
            <label for="f-marca">Marca</label>
          </div>
          @error('marca')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- Modelo --}}
        <div>
          <div class="field @error('modelo') is-invalid @enderror">
            <input type="text" name="modelo" id="f-modelo" value="{{ old('modelo', $producto->modelo) }}" placeholder=" ">
            <label for="f-modelo">Modelo</label>
          </div>
          @error('modelo')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- Precio --}}
        <div>
          <div class="field @error('precio') is-invalid @enderror">
            <input type="number" step="0.01" name="precio" id="f-precio" value="{{ old('precio', $producto->precio) }}" placeholder=" ">
            <label for="f-precio">Precio</label>
            <span class="prefix">$ MXN</span>
          </div>
          @error('precio')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- Familias (multiselección) --}}
        <div style="grid-column:1/-1;">
          <div class="field-select @error('familias') is-invalid @enderror">
            <label for="familias">Familias (opcional)</label>
            <select id="familias" name="familias[]" class="select-multi" multiple size="6">
              @foreach($familias as $fam)
                <option value="{{ $fam->id }}" {{ in_array($fam->id, $familiasSeleccionadas) ? 'selected' : '' }}>
                  {{ $fam->nombre }}
                </option>
              @endforeach
            </select>
            <div class="hint">Mantén presionadas Ctrl / Cmd para seleccionar varias.</div>
            <div id="chips" class="chips"></div>
          </div>
          @error('familias')<div class="error">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- Imagen --}}
      <div class="block" style="margin-top:22px;">
        <div class="uploader">
          <div class="thumb">
            <img id="preview" src="{{ $producto->imagen ? asset('storage/'.$producto->imagen) : 'https://via.placeholder.com/280x280.png?text=Sin+imagen' }}" alt="Vista previa">
          </div>
          <div class="drop">
            <label class="btn" for="imagen">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;">
                <path d="M12 5v14M5 12h14"/>
              </svg>
              Cambiar imagen
            </label>
            <input id="imagen" class="input-file" type="file" name="imagen" accept="image/*">
            <span class="small">Formatos: JPG/PNG. Máx 2MB.</span>
          </div>
        </div>
        @error('imagen')<div class="error" style="margin-top:8px;">{{ $message }}</div>@enderror
      </div>

      <div class="actions">
        <button type="button" class="btn btn-ghost" onclick="window.history.back()">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar cambios</button>
      </div>
    </form>
  </div>
</div>

<script>
// Preview de imagen dinámica
document.getElementById('imagen')?.addEventListener('change', function(e){
  const file = e.target.files && e.target.files[0];
  if(!file) return;
  const ok = /^image\//.test(file.type);
  if(!ok) { alert('Selecciona una imagen válida.'); this.value=''; return; }
  const max = 2 * 1024 * 1024;
  if(file.size > max){ alert('La imagen supera 2MB.'); this.value=''; return; }
  const reader = new FileReader();
  reader.onload = ev => document.getElementById('preview').src = ev.target.result;
  reader.readAsDataURL(file);
});

// Formatear precio a 2 decimales al salir
const precio = document.getElementById('f-precio');
if(precio){
  precio.addEventListener('blur', ()=> {
    if(precio.value !== '') {
      precio.value = parseFloat(precio.value).toFixed(2);
    }
  });
}

// Chips para familias seleccionadas
const sel = document.getElementById('familias');
const chips = document.getElementById('chips');

function renderChips(){
  if(!sel || !chips) return;
  chips.innerHTML = '';
  Array.from(sel.selectedOptions).forEach(opt => {
    const span = document.createElement('span');
    span.className = 'chip';
    span.innerHTML = `${opt.text} <button type="button" aria-label="Quitar">&times;</button>`;
    span.querySelector('button').addEventListener('click', () => {
      opt.selected = false;
      renderChips();
    });
    chips.appendChild(span);
  });
}
sel?.addEventListener('change', renderChips);
renderChips();
</script>
@endsection
