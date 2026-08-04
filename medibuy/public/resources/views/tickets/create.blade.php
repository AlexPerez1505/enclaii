@extends('layouts.app')

@section('title','Nuevo ticket')
@section('content')
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

<style>
:root{
  --mint:#48cfad; --mint-dark:#34c29e;
  --ink:#2a2e35; --muted:#7a7f87; --line:#e9ecef; --card:#ffffff;
}

/* ===== Base (MÓVIL por defecto) ===== */
*{box-sizing:border-box}
body{font-family:"Open Sans",sans-serif;background:#eaebec;color:var(--ink)}

.edit-wrap{
  /* móvil “como antes” más ancho y cómodo */
  max-width:940px;
  margin:80px auto 34px;
  padding:0 16px;
}
.panel{
  background:var(--card); border-radius:12px;
  box-shadow:0 12px 30px rgba(18,38,63,.12); overflow:hidden;
}
.panel-head{
  padding:18px 18px;
  border-bottom:1px solid var(--line);
  display:flex;align-items:center;gap:12px;justify-content:space-between;
}
.hgroup h2{margin:0;font-weight:700;color:var(--ink);font-size:20px;}
.hgroup p{margin:2px 0 0;color:var(--muted);font-size:13px}
.back-link{
  display:inline-flex;align-items:center;gap:6px;
  color:var(--muted);text-decoration:none;padding:8px 10px;border-radius:10px;
  border:1px solid var(--line);background:#fff;font-size:12px;
}
.back-link:hover{color:var(--ink);border-color:#dfe3e8}

.form{ padding:20px; }
.grid{ display:grid;grid-template-columns:1fr;gap:16px; } /* móvil: 1 columna */

/* En móvil los campos ocupan todo el ancho */
.compact{ max-width:100%; }

/* Fields */
.field{
  position:relative;background:#fff;border:1px solid var(--line);
  border-radius:10px;padding:14px 12px 10px;
  transition:box-shadow .2s,border-color .2s;
}
.field:focus-within{border-color:#d8dee6;box-shadow:0 8px 22px rgba(18,38,63,.08)}
.field input, .field textarea, .field select{
  width:100%;border:0;outline:0;background:transparent;
  font-size:14px;color:var(--ink);padding-top:6px;resize:vertical;
}
.field textarea{min-height:120px}
.field label{
  position:absolute;left:12px;top:12px;color:var(--muted);font-size:12.5px;
  transition:transform .15s ease,color .15s ease,font-size .15s ease,top .15s ease;
  pointer-events:none;
}
.field input::placeholder,.field textarea::placeholder{color:transparent}
.field :is(input,textarea,select):focus + label,
.field :is(input,textarea):not(:placeholder-shown) + label{
  top:4px;transform:translateY(-8px);font-size:11px;color:var(--mint-dark);
}

/* Select */
.select-wrap{
  border:1px solid var(--line);border-radius:10px; padding:10px 12px;
  background:#fff
}
.select-wrap:focus-within{border-color:#d8dee6;box-shadow:0 8px 22px rgba(18,38,63,.08)}
.select-wrap label{display:block;color:var(--muted);font-size:12px;margin-bottom:6px}
.select-wrap select{border:0;outline:0;width:100%;background:transparent;font-size:14px}

/* Multiselect chips */
.ms{
  border:1px solid var(--line); border-radius:10px; background:#fff;
  padding:10px 10px 12px; transition:box-shadow .2s,border-color .2s;
}
.ms:focus-within{border-color:#d8dee6; box-shadow:0 8px 22px rgba(18,38,63,.08)}
.ms label{display:block;color:var(--muted);font-size:12px;margin:0 0 6px 2px}
.ms-box{ display:flex; flex-wrap:wrap; gap:8px; align-items:center; min-height:40px; }
.ms-chip{
  display:inline-flex; align-items:center; gap:6px;
  background:#f3f6f7; color:#1f2937; border:1px solid #e7eef1;
  padding:5px 9px; border-radius:999px; font-size:12.5px; font-weight:700;
}
.ms-chip .x{
  width:16px; height:16px; display:inline-grid; place-items:center;
  border-radius:50%; background:#e9f2f0; color:#0f3a33; cursor:pointer; font-size:12px;
}
.ms-input{
  flex:1 1 200px; min-width:140px; border:0; outline:0; padding:5px 6px; font-size:14px;
}
.ms-dropdown{
  position:absolute; left:0; right:0; top:calc(100% + 6px); z-index:20;
  background:#fff; border:1px solid var(--line); border-radius:10px;
  box-shadow:0 14px 30px rgba(18,38,63,.12); max-height:240px; overflow:auto; display:none;
}
.ms-dropdown.open{ display:block; }
.ms-item{ padding:10px 12px; cursor:pointer; display:flex; align-items:center; justify-content:space-between; }
.ms-item:hover{ background:#f7faf9 }
.ms-item .name{ font-size:13px; color:#111827; }
.ms-item .hint{ font-size:11px; color:#94a3b8 }

/* Actions */
.actions{
  display:flex;gap:10px;justify-content:flex-end;margin-top:8px;padding:0 18px 18px;
}
.btn{
  border:0;border-radius:10px;padding:11px 16px;font-weight:700;cursor:pointer;
  transition:transform .05s ease, box-shadow .2s ease, background .2s ease,color .2s ease;
  font-size:13.5px;
}
.btn:active{transform:translateY(1px)}
.btn-primary{ background:var(--mint);color:#fff;box-shadow:0 8px 18px rgba(72,207,173,.22); }
.btn-primary:hover{background:var(--mint-dark)}
.btn-ghost{ background:#fff;color:var(--ink);border:1px solid var(--line); }
.btn-ghost:hover{border-color:#dfe3e8}

/* Errores */
.is-invalid{border-color:#f9c0c0 !important}
.error{color:#cc4b4b;font-size:11px;margin-top:6px}

/* ===== Escritorio (solo aquí lo hago más PEQUEÑO) ===== */
@media (min-width: 992px){
  .edit-wrap{ max-width:800px; } /* más compacto en desktop */
  .grid{
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:14px;
  }
  .compact{ max-width:360px; }                 /* limitar ancho solo en desktop */
  .form{ padding:18px; }
  .panel-head{ padding:16px 18px; }
  .hgroup h2{ font-size:18px; }
  .hgroup p{ font-size:12px; }
  .field{ padding:12px 12px 8px; }
  .field input, .field textarea, .field select{ font-size:13.5px; }
  .field textarea{ min-height:100px; }
  .select-wrap{ padding:8px 10px; }
  .select-wrap select{ font-size:13.5px; }
  .ms{ padding:8px 8px 10px; }
  .ms-box{ min-height:36px; gap:6px; }
  .ms-chip{ padding:4px 8px; font-size:12px; }
  .ms-input{ min-width:120px; font-size:13px; padding:4px 6px; }
  .ms-item{ padding:8px 10px; }
  .actions{ padding:0 16px 16px; }
  .btn{ padding:10px 14px; font-size:13px; }
}
</style>

<div class="edit-wrap">
  <div class="panel">
    <div class="panel-head">
      <div class="hgroup">
        <h2>Nuevo ticket</h2>
        <p>Completa la información y asigna responsables.</p>
      </div>
      <a href="{{ route('tickets.index') }}" class="back-link" title="Volver">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        Volver
      </a>
    </div>

    <form class="form" action="{{ route('tickets.store') }}" method="POST">
      @csrf

      <div class="grid">
        {{-- Título --}}
        <div class="compact @error('title') is-invalid @enderror">
          <div class="field">
            <input type="text" name="title" id="f-title" value="{{ old('title') }}" placeholder=" " required maxlength="180">
            <label for="f-title">Título *</label>
          </div>
          @error('title')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- Prioridad (incluye urgent para coincidir con validación/controlador) --}}
        <div class="compact @error('priority') is-invalid @enderror">
          <div class="select-wrap">
            <label for="f-priority">Prioridad *</label>
            <select id="f-priority" name="priority" required>
              <option value="low"    @selected(old('priority')==='low')>Baja</option>
              <option value="medium" @selected(old('priority','medium')==='medium')>Media</option>
              <option value="high"   @selected(old('priority')==='high')>Alta</option>
              <option value="urgent" @selected(old('priority')==='urgent')>Urgente</option>
            </select>
          </div>
          @error('priority')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- Visibilidad (valor "shared" para coincidir con la DB) --}}
        <div class="compact @error('visibility') is-invalid @enderror">
          <div class="select-wrap">
            <label for="f-visibility">Visibilidad *</label>
            <select id="f-visibility" name="visibility" required>
              <option value="private" @selected(old('visibility')==='private')>Privado (sólo creador)</option>
              <option value="shared"  @selected(old('visibility')==='shared' || old('visibility')==='selected')>Seleccionados</option>
              <option value="public"  @selected(old('visibility','public')==='public')>Todos</option>
            </select>
          </div>
          @error('visibility')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- Asignados (chips) => se envían como assignees[] y el controlador los mapea a watchers --}}
        <div class="compact @error('assignees') is-invalid @enderror" style="position:relative;">
          <div class="ms" id="assigneesMS">
            <label>Asignados (varios)</label>
            <div class="ms-list">
              <div class="ms-box" id="assigneesBox">
                {{-- Chips se renderizan aquí --}}
                <input type="text" id="assigneesInput" class="ms-input" placeholder="Escribe para buscar…">
              </div>
              <div class="ms-dropdown" id="assigneesDropdown"></div>
            </div>
          </div>
          @error('assignees')<div class="error">{{ $message }}</div>@enderror
          {{-- Hidden inputs dinámicos --}}
          <div id="assigneesHidden"></div>
        </div>

        {{-- Descripción (fila completa) --}}
        <div style="grid-column:1 / -1" class="@error('body') is-invalid @enderror">
          <div class="field">
            <textarea name="body" id="f-body" placeholder=" ">@if(old('body')){{ old('body') }}@endif</textarea>
            <label for="f-body">Descripción</label>
          </div>
          @error('body')<div class="error">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="actions">
        <a href="{{ route('tickets.index') }}" class="btn btn-ghost">Cancelar</a>
        <button type="submit" class="btn btn-primary">Crear</button>
      </div>
    </form>
  </div>
</div>

<script>
(function(){
  // ------ Fuente de usuarios (id, name) desde PHP ------
  const AVAILABLE = @json(
    collect($users ?? [])->map(fn($u)=>['id'=>$u->id,'name'=>$u->name])->values()
  );

  // Estado de seleccionados
  const selected = new Map(); // id -> name

  // Prefill desde old('assignees')
  @php
    $oldAssignees = collect(old('assignees', []))
      ->map(fn($id)=> (object)['id'=>$id, 'name'=>optional(($users ?? collect())->firstWhere('id',$id))->name])
      ->filter(fn($u)=>$u->name);
  @endphp
  const prefill = @json($oldAssignees->values());
  prefill.forEach(u => selected.set(String(u.id), u.name));

  // Elementos
  const box = document.getElementById('assigneesBox');
  const input = document.getElementById('assigneesInput');
  const dd = document.getElementById('assigneesDropdown');
  const hiddenWrap = document.getElementById('assigneesHidden');

  function renderHidden(){
    hiddenWrap.innerHTML = '';
    for(const [id] of selected){
      const h = document.createElement('input');
      h.type = 'hidden'; h.name = 'assignees[]'; h.value = id; // 👈 se envían como assignees[]
      hiddenWrap.appendChild(h);
    }
  }

  function renderChips(){
    box.querySelectorAll('.ms-chip').forEach(n => n.remove());
    for(const [id, name] of selected){
      const chip = document.createElement('span');
      chip.className = 'ms-chip';
      chip.innerHTML = `<span class="txt">${name}</span><span class="x" data-id="${id}" title="Quitar">×</span>`;
      box.insertBefore(chip, input);
    }
    renderHidden();
  }

  function openDD(){ dd.classList.add('open'); }
  function closeDD(){ dd.classList.remove('open'); }

  function filterUsers(q){
    q = (q||'').toLowerCase().trim();
    const already = new Set([...selected.keys()]);
    return AVAILABLE
      .filter(u => !already.has(String(u.id)))
      .filter(u => !q || String(u.name).toLowerCase().includes(q))
      .slice(0, 15);
  }

  function renderDD(items){
    dd.innerHTML = '';
    if(!items.length){ closeDD(); return; }
    items.forEach(u => {
      const row = document.createElement('div');
      row.className = 'ms-item';
      row.innerHTML = `<span class="name">${u.name}</span><span class="hint">ID ${u.id}</span>`;
      row.addEventListener('click', () => {
        selected.set(String(u.id), u.name);
        input.value = '';
        renderChips(); closeDD(); input.focus();
      });
      dd.appendChild(row);
    });
    openDD();
  }

  input.addEventListener('input', () => renderDD(filterUsers(input.value)));
  input.addEventListener('focus', () => renderDD(filterUsers(input.value)));
  document.addEventListener('click', (e) => { if(!dd.contains(e.target) && e.target !== input) closeDD(); });

  // Quitar chip
  box.addEventListener('click', (e) => {
    const x = e.target.closest('.x');
    if(!x) return;
    const id = x.getAttribute('data-id');
    selected.delete(String(id));
    renderChips();
  });

  // Accesos rápidos
  input.addEventListener('keydown', (e) => {
    if(e.key === 'Enter'){
      e.preventDefault();
      const first = dd.querySelector('.ms-item');
      if(first){ first.click(); }
    }
    if(e.key === 'Backspace' && !input.value){
      const last = [...selected.keys()].pop();
      if(last){ selected.delete(last); renderChips(); }
    }
  });

  // Init
  renderChips();
})();
</script>
@endsection
