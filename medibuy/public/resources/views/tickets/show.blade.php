@extends('layouts.app')

@section('title','Ticket #'.$ticket->id)
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

@php
  $canEdit = (int)auth()->id() === (int)$ticket->creator_id;
@endphp

<style>
  :root{
    --ink:#0f172a; --muted:#6b7280; --line:#e7ebf0; --bg:#f3f7ff;
    --pblue-50:#eef6ff; --pblue-75:#f3f8ff; --pblue-700:#1f4bb8;

    /* Chips */
    --chip1:#e0f2fe; --chip1-fg:#075985; --chip1-br:#bae6fd;
    --chip2:#f5f3ff; --chip2-fg:#5b21b6; --chip2-br:#ddd6fe;
    --chip3:#ecfdf5; --chip3-fg:#065f46; --chip3-br:#bbf7d0;
    --chip4:#fff7ed; --chip4-fg:#9a3412; --chip4-br:#fed7aa;
    --chip5:#fef2f2; --chip5-fg:#991b1b; --chip5-br:#fecaca;

    /* Pastel buttons (como antes) */
    --soft-blue-bg:#edf4ff;  --soft-blue-fg:#1f4bb8;  --soft-blue-br:#dbe7ff;
    --soft-gray-bg:#f4f7fb;  --soft-gray-fg:#475569;  --soft-gray-br:#e5eaf5;
    --soft-green-bg:#eefbf5; --soft-green-fg:#0f766e; --soft-green-br:#c7f2e9;

    /* Blocks */
    --gA: linear-gradient(135deg, #ffffff 0%, #f9fbff 60%, #eef4ff 100%);
    --gB: linear-gradient(135deg, #ffffff 0%, #f9fbff 55%, #f0f7ff 100%);
  }

  body{ background:var(--bg); color:var(--ink); }

  /* ---------- HERO ---------- */
  .hero{
    width:min(1100px, calc(100% - 24px));
    margin:94px auto 12px;
    padding:16px 18px;
    background:
      radial-gradient(120% 120% at 0% 0%, var(--pblue-50) 0%, #fff 55%),
      radial-gradient(120% 120% at 100% 0%, var(--pblue-75) 0%, #fff 55%);
    border:1px solid #dce7ff; border-radius:18px;
    box-shadow:0 8px 24px rgba(31,75,184,.08);
    display:flex; align-items:start; justify-content:space-between; gap:14px; flex-wrap:wrap;
  }
  .hero .left{ display:flex; gap:12px; align-items:flex-start; flex-wrap:wrap; }
  .hero .avatar-id{
    width:44px; height:44px; border-radius:50%; display:grid; place-items:center;
    background:#fff; border:1px solid #dce7ff; color:var(--pblue-700); font-weight:700;
  }
  .hero h1{ margin:0; font-size:20px }
  .hero .sub{ color:var(--muted); font-size:13px }
  .inline-pills{ display:flex; gap:.35rem; flex-wrap:wrap; margin-top:6px }

  /* ---------- Pastel Buttons ---------- */
  .btn{
    border:1px solid transparent; border-radius:12px; padding:.6rem .95rem; font-weight:700;
    display:inline-flex; align-items:center; gap:.45rem; cursor:pointer; text-decoration:none;
    transition:background .15s ease, color .15s ease, box-shadow .15s ease, border-color .15s ease;
    line-height:1;
  }
  .btn-soft{
    background:var(--soft-blue-bg); color:var(--soft-blue-fg); border-color:var(--soft-blue-br);
    box-shadow:0 6px 16px rgba(31,75,184,.08);
  }
  .btn-soft:hover{
    background:#fff; color:#111827; border-color:#e5e7eb;
    box-shadow:0 10px 24px rgba(2,6,23,.14);
  }
  .btn-soft-gray{
    background:var(--soft-gray-bg); color:var(--soft-gray-fg); border-color:var(--soft-gray-br);
    box-shadow:0 6px 16px rgba(71,85,105,.08);
  }
  .btn-soft-gray:hover{
    background:#fff; color:#111827; border-color:#e5e7eb;
    box-shadow:0 10px 24px rgba(2,6,23,.14);
  }
  .btn-soft-green{
    background:var(--soft-green-bg); color:var(--soft-green-fg); border-color:var(--soft-green-br);
    box-shadow:0 6px 16px rgba(16,185,129,.10);
  }
  .btn-soft-green:hover{
    background:#fff; color:#111827; border-color:#e5e7eb;
    box-shadow:0 10px 24px rgba(2,6,23,.14);
  }
  .btn-mini{ font-size:.78rem; padding:.36rem .6rem; border-radius:9px; }

  /* ---------- Blocks ---------- */
  .wrap{ max-width:1100px; }
  .block{
    background:var(--gA);
    border:1px solid var(--line);
    border-radius:16px; box-shadow:0 10px 28px rgba(2,6,23,.06);
    overflow:hidden;
  }
  .block.alt{ background:var(--gB); }
  .block .head{
    padding:12px 14px; border-bottom:1px solid var(--line);
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    color:#334155; font-weight:600;
  }
  .block .body{ padding:14px; }

  /* ---------- Pills ---------- */
  .pill{ display:inline-flex; align-items:center; gap:.35rem; padding:.26rem .5rem; border-radius:999px; font-size:.72rem; font-weight:700; border:1px solid var(--line); background:#fff; color:#334155; }
  .pill i{ font-size:.9em }
  .pill-prio-low{ background:#ecfdf5; color:#065f46; border-color:#bbf7d0 }
  .pill-prio-medium{ background:#fff7ed; color:#9a3412; border-color:#fed7aa }
  .pill-prio-high{ background:#fef2f2; color:#991b1b; border-color:#fecaca }
  .pill-prio-urgent{ background:#fee2e2; color:#7f1d1d; border-color:#fecaca }
  .pill-status-open{ background:#e0f2fe; color:#075985; border-color:#bae6fd }
  .pill-status-in_progress{ background:#fef9c3; color:#713f12; border-color:#fde68a }
  .pill-status-resolved{ background:#ecfdf5; color:#065f46; border-color:#bbf7d0 }
  .pill-status-closed{ background:#f3f4f6; color:#374151; border-color:#e5e7eb }
  .pill-vis-private{ background:#eef2ff; color:#3730a3; border-color:#c7d2fe }
  .pill-vis-shared{ background:#f5f3ff; color:#6d28d9; border-color:#ddd6fe }
  .pill-vis-public{ background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe }

  /* ---------- Texto ---------- */
  .meta{ color:var(--muted); font-size:.92rem; display:flex; gap:12px; flex-wrap:wrap; }
  .meta i{ opacity:.7 }
  .desc{ color:#0f172a; white-space:pre-wrap; }

  /* ---------- Chips (color) ---------- */
  .chips{ display:flex; flex-wrap:wrap; gap:8px; }
  .chip{
    --bg: var(--chip1); --fg: var(--chip1-fg); --br: var(--chip1-br);
    display:inline-flex; align-items:center; gap:.45rem;
    padding:.32rem .65rem; border-radius:999px; border:1px solid var(--br);
    background: var(--bg); color: var(--fg); font-size:.78rem; font-weight:700;
  }
  .chip[data-variant="2"]{ --bg:var(--chip2); --fg:var(--chip2-fg); --br:var(--chip2-br); }
  .chip[data-variant="3"]{ --bg:var(--chip3); --fg:var(--chip3-fg); --br:var(--chip3-br); }
  .chip[data-variant="4"]{ --bg:var(--chip4); --fg:var(--chip4-fg); --br:var(--chip4-br); }
  .chip[data-variant="5"]{ --bg:var(--chip5); --fg:var(--chip5-fg); --br:var(--chip5-br); }
  .chip .x{
    width:18px;height:18px;border-radius:50%;display:grid;place-items:center;
    background:#ffffffaa; color:inherit; cursor:pointer; font-size:12px;
    border:1px solid var(--br);
  }

  /* ---------- Editor de acceso ---------- */
  .access-ui{ display:flex; flex-direction:column; gap:10px; }
  .access-ui .search{
    display:flex; gap:8px; align-items:center; background:#fff; border:1px solid var(--line);
    padding:.5rem .6rem; border-radius:10px;
  }
  .access-ui .search input{
    flex:1; border:0; outline:0; background:transparent; font-size:.95rem;
  }
  .options{
    background:#fff; border:1px solid var(--line); border-radius:10px; max-height:225px; overflow:auto;
  }
  .opt{
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    padding:.48rem .65rem; border-bottom:1px solid #f0f3f8; font-size:.92rem;
  }
  .opt:last-child{ border-bottom:0 }
  .opt .small{ color:var(--muted); font-size:.78rem }

  .btn-add{ background:var(--soft-blue-bg); color:var(--soft-blue-fg); border-color:var(--soft-blue-br); box-shadow:0 4px 10px rgba(31,75,184,.08); }
  .btn-add:hover{ background:#fff; color:#111827; border-color:#e5e7eb; box-shadow:0 8px 18px rgba(2,6,23,.14); }
  .btn-rem{ background:#fff5f5; color:#7f1d1d; border-color:#fecaca; box-shadow:0 4px 10px rgba(127,29,29,.06); }
  .btn-rem:hover{ background:#fff; color:#111827; border-color:#e5e7eb; box-shadow:0 8px 18px rgba(2,6,23,.14); }

  a, a:visited{ color:inherit; text-decoration:none }
  a:hover{ opacity:.9 }

  @media (max-width: 767.98px){
    .hero{ padding:14px; }
    .hero .actions{ width:100%; display:flex; gap:.5rem; flex-wrap:wrap }
  }
</style>

{{-- ================= HERO ================= --}}
<div class="hero">
  <div class="left">
    <div class="avatar-id">#{{ $ticket->id }}</div>
    <div>
      <h1>{{ \Illuminate\Support\Str::limit($ticket->title, 90) }}</h1>
      <div class="sub">Detalle, estado y conversación</div>
      <div class="inline-pills">
        <span class="pill pill-status-{{ $ticket->status }}">
          @switch($ticket->status)
            @case('open') <i class="bi bi-lightning-charge"></i> Abierto @break
            @case('in_progress') <i class="bi bi-hourglass-split"></i> En progreso @break
            @case('resolved') <i class="bi bi-check2-circle"></i> Resuelto @break
            @case('closed') <i class="bi bi-archive"></i> Cerrado @break
          @endswitch
        </span>
        <span class="pill pill-prio-{{ $ticket->priority }}">
          @switch($ticket->priority)
            @case('low') <i class="bi bi-arrow-down"></i> Baja @break
            @case('medium') <i class="bi bi-arrow-right"></i> Media @break
            @case('high') <i class="bi bi-arrow-up"></i> Alta @break
            @case('urgent') <i class="bi bi-exclamation-octagon"></i> Urgente @break
          @endswitch
        </span>
        <span class="pill pill-vis-{{ $ticket->visibility }}">
          @switch($ticket->visibility)
            @case('private') <i class="bi bi-lock"></i> Privado @break
            @case('shared')  <i class="bi bi-people"></i> Seleccionados @break
            @case('public')  <i class="bi bi-broadcast-pin"></i> Público @break
          @endswitch
        </span>
      </div>
    </div>
  </div>

  <div class="actions">
    @if($canEdit)
      {{-- Avanzar estado (ciclo) --}}
      <form action="{{ route('tickets.update',$ticket) }}" method="POST" class="d-inline">
        @csrf @method('PUT')
        @php
          $next = match($ticket->status){
            'open' => 'in_progress', 'in_progress' => 'resolved', 'resolved' => 'closed', default => 'open'
          };
        @endphp
        <input type="hidden" name="status" value="{{ $next }}">
        <button class="btn btn-soft" title="Avanzar a {{ ucfirst(str_replace('_',' ',$next)) }}">
          <i class="bi bi-fast-forward"></i> {{ ucfirst(str_replace('_',' ',$next)) }}
        </button>
      </form>

      @if(Route::has('tickets.notify'))
        <form action="{{ route('tickets.notify',$ticket) }}" method="POST" class="d-inline">
          @csrf
          <button class="btn btn-soft-green"><i class="bi bi-whatsapp"></i> Notificar</button>
        </form>
      @endif

      <a href="{{ route('tickets.edit',$ticket) }}" class="btn btn-soft">
        <i class="bi bi-pencil"></i> Editar
      </a>
    @endif
  </div>
</div>

<div class="container wrap">
  {{-- ================= Resumen ================= --}}
  <div class="block alt mb-3">
    <div class="body">
      <div class="meta mb-2">
        <span><i class="bi bi-person-badge"></i> Creador: {{ $ticket->creator->name }}</span>
        <span><i class="bi bi-person-check"></i> Asignado: {{ $ticket->assignee?->name ?? '—' }}</span>
        <span><i class="bi bi-clock-history"></i> {{ $ticket->created_at->diffForHumans() }}</span>
        <span><i class="bi bi-hash"></i> ID {{ $ticket->id }}</span>
      </div>

      @php $desc = $ticket->description ?? $ticket->body ?? null; @endphp
      <div class="desc">{{ $desc ?: 'Sin descripción.' }}</div>
    </div>
  </div>

  <div class="row g-3">
    {{-- ================= Acceso (watchers) ================= --}}
    <div class="col-lg-4">
      <div class="block">
        <div class="head">
          <span><i class="bi bi-people me-1"></i> Acceso al ticket</span>
          <span class="small" style="color:var(--muted)">{{ $ticket->watchers->count() }}</span>
        </div>
        <div class="body">
          {{-- Chips actuales con quitar inline (sólo creador y si visibility=shared) --}}
          <div class="chips mb-2" id="chips">
            @forelse($ticket->watchers as $i => $w)
              <span class="chip" data-id="{{ $w->id }}" data-variant="{{ ($i % 5)+1 }}">
                <span class="txt">{{ $w->name }}</span>
                @if($canEdit && $ticket->visibility === 'shared')
                  <button type="button" class="x" title="Quitar" data-remove="{{ $w->id }}">×</button>
                @endif
              </span>
            @empty
              <span class="small" style="color:var(--muted)">Sin seleccionados.</span>
            @endforelse
          </div>

          {{-- Editor: buscar y agregar/quitar (solo creador y si visibility = "shared") --}}
          <form action="{{ route('tickets.watchers.update',$ticket) }}" method="POST" id="accessForm">
            @csrf @method('PUT')

            {{-- Hidden mirror de seleccionados --}}
            <div id="hiddenWatchers">
              @foreach($ticket->watchers as $w)
                <input type="hidden" name="watchers[]" value="{{ $w->id }}">
              @endforeach
            </div>

            <div class="access-ui"
                 @if(!$canEdit || $ticket->visibility!=='shared') style="opacity:.6; pointer-events:none" @endif>
              <div class="search">
                <i class="bi bi-search" style="color:var(--muted)"></i>
                <input type="text" id="userQuery" placeholder="Buscar usuario por nombre…">
              </div>

              <div class="options" id="options">
                @foreach($users as $u)
                  @php $has = $ticket->watchers->pluck('id')->contains($u->id); @endphp
                  <div class="opt" data-name="{{ \Illuminate\Support\Str::lower($u->name) }}" data-id="{{ $u->id }}">
                    <div>
                      <div>{{ $u->name }}</div>
                      <div class="small">ID {{ $u->id }}</div>
                    </div>
                    @if($has)
                      <button type="button" class="btn btn-rem btn-mini" data-remove="{{ $u->id }}"><i class="bi bi-dash"></i> Quitar</button>
                    @else
                      <button type="button" class="btn btn-add btn-mini" data-add="{{ $u->id }}"><i class="bi bi-plus"></i> Agregar</button>
                    @endif
                  </div>
                @endforeach
              </div>

              <button class="btn btn-soft-gray mt-2 w-100">
                <i class="bi bi-people-fill me-1"></i> Guardar cambios de acceso
              </button>
              <div class="small" style="color:var(--muted)">
                <i class="bi bi-info-circle"></i>
                @if($ticket->visibility!=='shared')
                  La visibilidad actual no permite gestionar accesos.
                @else
                  Sólo el creador puede modificar accesos.
                @endif
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- Metadatos --}}
      <div class="block mt-3">
        <div class="head"><span>Metadatos</span></div>
        <div class="body small" style="color:var(--muted); display:flex; flex-direction:column; gap:6px;">
          <span><i class="bi bi-calendar2"></i> Creado: {{ $ticket->created_at->format('d/m/Y H:i') }}</span>
          <span><i class="bi bi-calendar2-check"></i> Actualizado: {{ $ticket->updated_at->format('d/m/Y H:i') }}</span>
          <span><i class="bi bi-clipboard-check"></i> Estado: {{ ucfirst(str_replace('_',' ',$ticket->status)) }}</span>
          <span><i class="bi bi-link-45deg"></i> <a href="{{ route('tickets.show',$ticket) }}">Abrir enlace</a></span>
        </div>
      </div>
    </div>

    {{-- ================= Conversación ================= --}}
    <div class="col-lg-8">
      <div class="block">
        <div class="head">
          <span><i class="bi bi-chat-text me-1"></i> Comentarios</span>
          <span class="small" style="color:var(--muted)"><i class="bi bi-chat-dots"></i> {{ $ticket->comments->count() }}</span>
        </div>
        <div class="body">
          @forelse($ticket->comments as $c)
            <div class="comment" style="border-bottom:1px dashed var(--line); padding-bottom:12px; margin-bottom:12px;">
              <div class="d-flex align-items-center gap-2">
                <div class="avatar">{{ \Illuminate\Support\Str::of($c->user->name)->substr(0,1)->upper() }}</div>
                <div>
                  <div style="line-height:1.1">{{ $c->user->name }}</div>
                  <div class="small" style="color:var(--muted)">{{ $c->created_at->diffForHumans() }}</div>
                </div>
              </div>
              <div class="mt-2 desc">{{ $c->body }}</div>
            </div>
          @empty
            <div class="text-center" style="color:var(--muted)">Sé el primero en comentar.</div>
          @endforelse

          {{-- Nuevo comentario --}}
          <form action="{{ route('tickets.comments.store',$ticket) }}" method="POST" class="mt-3">
            @csrf
            <label class="form-label" style="font-weight:600">Agregar comentario</label>
            <textarea name="body" rows="4" class="form-control" placeholder="Escribe un mensaje útil para avanzar… (Shift+Enter para salto)"></textarea>
            <div class="d-flex justify-content-end mt-2">
              <button class="btn btn-soft"><i class="bi bi-send me-1"></i> Publicar</button>
            </div>
          </form>
        </div>
      </div>

      {{-- Acciones rápidas --}}
      <div class="block mt-3">
        <div class="body d-flex flex-wrap" style="gap:10px;">
          @if($canEdit)
            <form action="{{ route('tickets.update',$ticket) }}" method="POST" class="d-inline">
              @csrf @method('PUT')
              <input type="hidden" name="status" value="open">
              <button class="btn btn-soft-gray"><i class="bi bi-arrow-counterclockwise"></i> Reabrir</button>
            </form>
          @endif
          <a href="{{ route('tickets.index') }}" class="btn btn-soft-gray"><i class="bi bi-list"></i> Volver al listado</a>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ================= JS: UI de agregar/quitar acceso (chips + lista) ================= --}}
<script>
(function(){
  const visibilityIsShared = @json($ticket->visibility === 'shared');
  const canEdit = @json($canEdit);

  // Estado en memoria: conjunto de IDs seleccionados
  const selected = new Set(@json($ticket->watchers->pluck('id')->values()));

  // DOM
  const chipsWrap = document.getElementById('chips');
  const hiddenWrap = document.getElementById('hiddenWatchers');
  const options = document.getElementById('options');
  const q = document.getElementById('userQuery');

  function syncHidden(){
    hiddenWrap.innerHTML = '';
    [...selected].forEach(id=>{
      const i = document.createElement('input');
      i.type = 'hidden'; i.name = 'watchers[]'; i.value = id;
      hiddenWrap.appendChild(i);
    });
  }

  function makeChip(id, name, variant){
    const span = document.createElement('span');
    span.className = 'chip';
    span.dataset.variant = variant;
    span.dataset.id = id;
    span.innerHTML = `<span class="txt">${name}</span>` + (visibilityIsShared && canEdit ? `<button type="button" class="x" data-remove="${id}" title="Quitar">×</button>` : '');
    return span;
  }

  function refreshChips(){
    chipsWrap.innerHTML = '';
    const rows = options.querySelectorAll('.opt');
    let idx = 0;
    rows.forEach(r=>{
      const id = r.getAttribute('data-id');
      if(selected.has(Number(id))){
        const name = r.querySelector('div > div:first-child, div').textContent.trim();
        const chip = makeChip(id, name, (idx % 5) + 1);
        chipsWrap.appendChild(chip);
        idx++;
      }
    });
    if(idx===0){
      const empty = document.createElement('span');
      empty.className = 'small';
      empty.style.color = 'var(--muted)';
      empty.textContent = 'Sin seleccionados.';
      chipsWrap.appendChild(empty);
    }
  }

  function updateOptionButtons(){
    options.querySelectorAll('.opt').forEach(r=>{
      const id = Number(r.getAttribute('data-id'));
      const btnAdd = r.querySelector('[data-add]');
      const btnRem = r.querySelector('[data-remove]');
      const can = (visibilityIsShared && canEdit);
      if(selected.has(id)){
        if(btnAdd && can) btnAdd.outerHTML = `<button type="button" class="btn btn-rem btn-mini" data-remove="${id}"><i class="bi bi-dash"></i> Quitar</button>`;
        if(btnAdd && !can) btnAdd.disabled = true;
      }else{
        if(btnRem && can) btnRem.outerHTML = `<button type="button" class="btn btn-add btn-mini" data-add="${id}"><i class="bi bi-plus"></i> Agregar</button>`;
        if(btnRem && !can) btnRem.disabled = true;
      }
    });
  }

  function filterOptions(){
    const term = (q.value || '').toLowerCase().trim();
    options.querySelectorAll('.opt').forEach(r=>{
      const name = r.getAttribute('data-name');
      r.style.display = (!term || name.includes(term)) ? '' : 'none';
    });
  }

  // Delegación clicks add/remove (lista)
  options.addEventListener('click', (e)=>{
    if(!(visibilityIsShared && canEdit)) return;
    const add = e.target.closest('[data-add]');
    const rem = e.target.closest('[data-remove]');
    if(add){
      const id = Number(add.getAttribute('data-add'));
      selected.add(id);
      syncHidden(); updateOptionButtons(); refreshChips();
    }
    if(rem){
      const id = Number(rem.getAttribute('data-remove'));
      selected.delete(id);
      syncHidden(); updateOptionButtons(); refreshChips();
    }
  });

  // Quitar desde chip
  chipsWrap.addEventListener('click', (e)=>{
    if(!(visibilityIsShared && canEdit)) return;
    const x = e.target.closest('.x');
    if(!x) return;
    const id = Number(x.getAttribute('data-remove'));
    selected.delete(id);
    syncHidden(); updateOptionButtons(); refreshChips();
  });

  q.addEventListener('input', filterOptions);

  // Init
  syncHidden(); updateOptionButtons(); refreshChips(); filterOptions();
})();
</script>
@endsection
