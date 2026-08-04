@extends('layouts.app')

@section('title','Trabajar Ticket #'.$ticket->id)
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

<link href="https://unpkg.com/filepond@^4/dist/filepond.min.css" rel="stylesheet">
<script src="https://unpkg.com/filepond@^4/dist/filepond.min.js"></script>

@php
  // Labels
  $typeLabel = match($ticket->ticket_type){
    'incidencia' => 'Incidencia',
    'requerimiento' => 'Requerimiento',
    'tarea' => 'Tarea',
    'bug' => 'Bug / Error',
    default => ucfirst((string)$ticket->ticket_type),
  };

  $areaLabel = match($ticket->area){
    'hojalateria' => 'Hojalatería',
    'mantenimiento' => 'Mantenimiento',
    'sistemas' => 'Sistemas',
    'administracion' => 'Administración',
    'otra' => 'Otra',
    default => $ticket->area ?: '—',
  };

  $desc = $ticket->description ?? $ticket->body ?? null;

  // Checklist desde DB (json)
  $cl = is_array($ticket->checklist ?? null) ? $ticket->checklist : [];

  // Attachments ticket
  $ticketAttachments = is_iterable($ticket->attachments ?? null) ? collect($ticket->attachments) : collect();

  // Comments (para sacar avances checklist_update)
  $ticketComments = is_iterable($ticket->comments ?? null) ? collect($ticket->comments) : collect();

  // Sacar último update por índice
  $updatesByIndex = [];
  foreach ($ticketComments as $c) {
      $raw = (string)($c->body ?? '');
      $j = json_decode($raw, true);
      if (!is_array($j)) continue;
      if (($j['type'] ?? null) !== 'checklist_update') continue;

      $idx = (int)($j['check_index'] ?? -1);
      if ($idx < 0) continue;

      // guarda el más reciente (comments vienen latest() normalmente, pero no lo asumimos)
      $at = optional($c->created_at);
      if (!isset($updatesByIndex[$idx]) || ($at && $updatesByIndex[$idx]['at'] && $at->gt($updatesByIndex[$idx]['at']))) {
          $updatesByIndex[$idx] = [
              'at' => $at,
              'user' => $c->user?->name ?? '—',
              'action' => $j['action'] ?? 'undone',
              'what' => $j['what'] ?? '',
              'how' => $j['how'] ?? '',
              'notes' => $j['notes'] ?? '',
          ];
      }
  }

  // Pills
  $statusPill = match($ticket->status){
    'open' => ['Abierto','pill-status-open'],
    'in_progress' => ['En progreso','pill-status-in_progress'],
    'resolved' => ['Resuelto','pill-status-resolved'],
    'closed' => ['Cerrado','pill-status-closed'],
    default => [$ticket->status,'pill-status-closed'],
  };

  $prioPill = match($ticket->priority){
    'low' => ['Baja','pill-prio-low'],
    'medium' => ['Media','pill-prio-medium'],
    'high' => ['Alta','pill-prio-high'],
    'urgent' => ['Urgente','pill-prio-urgent'],
    default => [$ticket->priority,'pill-prio-medium'],
  };

  $visPill = match($ticket->visibility){
    'private' => ['Privado','pill-vis-private'],
    'shared' => ['Seleccionados','pill-vis-shared'],
    'public' => ['Público','pill-vis-public'],
    default => [ucfirst((string)$ticket->visibility),'pill-vis-private'],
  };

  $doneCount = 0;
  foreach ($cl as $i => $_) {
    if (($updatesByIndex[$i]['action'] ?? null) === 'done') $doneCount++;
  }
@endphp

<style>
  :root{
    --ink:#0f172a; --muted:#6b7280; --line:#e7ebf0; --bg:#f3f7ff;
    --pblue-50:#eef6ff; --pblue-75:#f3f8ff; --pblue-700:#1f4bb8;
    --soft-blue-bg:#edf4ff;  --soft-blue-fg:#1f4bb8;  --soft-blue-br:#dbe7ff;
    --soft-gray-bg:#f4f7fb;  --soft-gray-fg:#475569;  --soft-gray-br:#e5eaf5;
    --soft-green-bg:#eefbf5; --soft-green-fg:#0f766e; --soft-green-br:#c7f2e9;
    --gA: linear-gradient(135deg, #ffffff 0%, #f9fbff 60%, #eef4ff 100%);
    --gB: linear-gradient(135deg, #ffffff 0%, #f9fbff 55%, #f0f7ff 100%);
  }
  body{ background:var(--bg); color:var(--ink); }

  .wrap{ width:min(1100px, calc(100% - 24px)); margin:94px auto 30px; }
  .hero{
    padding:16px 18px;
    background:
      radial-gradient(120% 120% at 0% 0%, var(--pblue-50) 0%, #fff 55%),
      radial-gradient(120% 120% at 100% 0%, var(--pblue-75) 0%, #fff 55%);
    border:1px solid #dce7ff; border-radius:18px;
    box-shadow:0 8px 24px rgba(31,75,184,.08);
    display:flex; align-items:flex-start; justify-content:space-between; gap:14px; flex-wrap:wrap;
  }
  .hero .left{ display:flex; gap:12px; align-items:flex-start; flex-wrap:wrap; }
  .hero .avatar-id{
    width:44px; height:44px; border-radius:50%; display:grid; place-items:center;
    background:#fff; border:1px solid #dce7ff; color:var(--pblue-700); font-weight:900;
  }
  .hero h1{ margin:0; font-size:20px; font-weight:900; }
  .hero .sub{ color:var(--muted); font-size:13px; }
  .inline-pills{ display:flex; gap:.35rem; flex-wrap:wrap; margin-top:6px; }

  .btnx{
    border:1px solid transparent; border-radius:12px; padding:.6rem .95rem; font-weight:900;
    display:inline-flex; align-items:center; gap:.45rem; cursor:pointer; text-decoration:none;
    transition:.15s ease;
    line-height:1;
  }
  .btn-soft{
    background:var(--soft-blue-bg); color:var(--soft-blue-fg); border-color:var(--soft-blue-br);
    box-shadow:0 6px 16px rgba(31,75,184,.08);
  }
  .btn-soft:hover{ background:#fff; color:#111827; border-color:#e5e7eb; box-shadow:0 10px 24px rgba(2,6,23,.14); }
  .btn-soft-gray{
    background:var(--soft-gray-bg); color:var(--soft-gray-fg); border-color:var(--soft-gray-br);
    box-shadow:0 6px 16px rgba(71,85,105,.08);
  }
  .btn-soft-gray:hover{ background:#fff; color:#111827; border-color:#e5e7eb; box-shadow:0 10px 24px rgba(2,6,23,.14); }
  .btn-soft-green{
    background:var(--soft-green-bg); color:var(--soft-green-fg); border-color:var(--soft-green-br);
    box-shadow:0 6px 16px rgba(16,185,129,.10);
  }
  .btn-soft-green:hover{ background:#fff; color:#111827; border-color:#e5e7eb; box-shadow:0 10px 24px rgba(2,6,23,.14); }

  .block{
    background:var(--gA);
    border:1px solid var(--line);
    border-radius:16px; box-shadow:0 10px 28px rgba(2,6,23,.06);
    overflow:hidden;
    margin-top:12px;
  }
  .block.alt{ background:var(--gB); }
  .block .head{
    padding:12px 14px; border-bottom:1px solid var(--line);
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    color:#334155; font-weight:700;
  }
  .block .body{ padding:14px; }

  .pill{ display:inline-flex; align-items:center; gap:.35rem; padding:.26rem .5rem; border-radius:999px; font-size:.72rem; font-weight:900; border:1px solid var(--line); background:#fff; color:#334155; }
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
  .pill-type{ background:#eef6ff; color:#1d4ed8; border-color:#bfdbfe }
  .pill-area{ background:#f5f3ff; color:#6d28d9; border-color:#ddd6fe }

  .meta{ color:var(--muted); font-size:.92rem; display:flex; gap:12px; flex-wrap:wrap; }
  .desc{ color:#0f172a; white-space:pre-wrap; }

  .file{
    display:flex; align-items:center; justify-content:space-between; gap:10px;
    padding:10px 12px; background:#fff; border:1px solid var(--line); border-radius:12px;
    margin-bottom:8px;
  }
  .file .left{ display:flex; align-items:center; gap:10px; }
  .file .name{ font-weight:900; }
  .file .small{ color:var(--muted); font-size:.82rem; }
  .file a{ text-decoration:none; }

  .item{
    background:#fff; border:1px solid var(--line); border-radius:16px;
    padding:12px; margin-bottom:12px;
  }
  .item .title{ font-weight:900; }
  .mini{ color:var(--muted); font-size:.86rem; }

  .grid{ display:grid; grid-template-columns: 1fr; gap:12px; }
  @media(min-width:992px){
    .grid{ grid-template-columns: 360px 1fr; align-items:start; }
  }
</style>

<div class="wrap">

  {{-- ================= HERO ================= --}}
  <div class="hero">
    <div class="left">
      <div class="avatar-id">#{{ $ticket->id }}</div>
      <div>
        <h1>{{ \Illuminate\Support\Str::limit($ticket->title, 90) }}</h1>
        <div class="sub">Trabajar checklist, evidencias y avances (como vista show + acciones).</div>

        <div class="inline-pills">
          <span class="pill {{ $statusPill[1] }}"><i class="bi bi-activity"></i> {{ $statusPill[0] }}</span>
          <span class="pill {{ $prioPill[1] }}"><i class="bi bi-flag"></i> {{ $prioPill[0] }}</span>
          <span class="pill {{ $visPill[1] }}"><i class="bi bi-shield-lock"></i> {{ $visPill[0] }}</span>

          <span class="pill pill-type"><i class="bi bi-tag"></i> {{ $typeLabel }}</span>
          <span class="pill pill-area"><i class="bi bi-diagram-3"></i> {{ $areaLabel }}</span>

          <span class="pill" title="Checklist completado">
            <i class="bi bi-check2-square"></i> {{ $doneCount }}/{{ count($cl) }}
          </span>
        </div>
      </div>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <a class="btnx btn-soft" href="{{ route('tickets.show',$ticket) }}"><i class="bi bi-eye"></i> Ver ticket</a>
      <a class="btnx btn-soft" href="{{ route('tickets.progress',$ticket) }}"><i class="bi bi-graph-up"></i> Ver avance</a>
      <a class="btnx btn-soft-gray" href="{{ route('tickets.index') }}"><i class="bi bi-list"></i> Listado</a>
    </div>
  </div>

  {{-- ================= Resumen (como show) ================= --}}
  <div class="block alt">
    <div class="body">
      <div class="meta mb-2">
        <span><i class="bi bi-person-badge"></i> Creador: <b>{{ $ticket->creator->name }}</b></span>
        <span><i class="bi bi-person-check"></i> Asignado: <b>{{ $ticket->assignee?->name ?? '—' }}</b></span>
        <span><i class="bi bi-clock-history"></i> {{ $ticket->created_at?->diffForHumans() }}</span>
      </div>

      <div class="desc">{{ $desc ?: 'Sin descripción.' }}</div>
    </div>
  </div>

  <div class="grid">

    {{-- ================= Panel izquierdo: Evidencias ticket ================= --}}
    <div>
      <div class="block">
        <div class="head">
          <span><i class="bi bi-paperclip me-1"></i> Evidencias del Ticket</span>
          <span class="mini">{{ $ticketAttachments->count() }}</span>
        </div>
        <div class="body">
          @forelse($ticketAttachments as $a)
            @php
              $url = null;
              if(isset($a->disk, $a->path)){
                try { $url = \Storage::disk($a->disk)->url($a->path); } catch(\Throwable $e) { $url = null; }
              }
            @endphp

            <div class="file">
              <div class="left">
                <i class="bi bi-file-earmark"></i>
                <div>
                  <div class="name">{{ $a->original_name ?? 'Archivo' }}</div>
                  <div class="small">{{ $a->mime ?? '—' }} • {{ number_format((($a->size ?? 0)/1024), 1) }} KB</div>
                </div>
              </div>
              @if($url)
                <a class="btnx btn-soft-gray" href="{{ $url }}" target="_blank" rel="noopener">
                  <i class="bi bi-box-arrow-up-right"></i>
                </a>
              @endif
            </div>
          @empty
            <div class="mini">Sin evidencias.</div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- ================= Panel derecho: Checklist completo + acciones ================= --}}
    <div>
      <div class="block">
        <div class="head">
          <span><i class="bi bi-check2-square me-1"></i> Checklist</span>
          <span class="mini">{{ count($cl) }} items</span>
        </div>

        <div class="body">

          @if(session('success'))
            <div class="alert alert-success rounded-3">{{ session('success') }}</div>
          @endif
          @if($errors->any())
            <div class="alert alert-danger rounded-3">
              <b>Hay errores:</b>
              <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
              </ul>
            </div>
          @endif

          @forelse($cl as $i => $item)
            @php
              $text = trim((string)($item['text'] ?? 'Item '.($i+1)));
              $req  = (bool)($item['required'] ?? false);
              $ev   = (bool)($item['evidence_required'] ?? false);
              $types = $item['evidence_types'] ?? [];

              $u = $updatesByIndex[$i] ?? null;
              $isDone = ($u['action'] ?? null) === 'done';
              $stateLabel = $isDone ? 'Hecho' : 'Pendiente';
            @endphp

            <div class="item">
              <div style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                <div class="title">
                  #{{ $i+1 }} — {{ $text }}
                  @if($req) <span class="pill" style="margin-left:6px;">Obligatorio</span> @endif
                  @if($ev)  <span class="pill" style="margin-left:6px;">Requiere evidencia</span> @endif
                  @if(!empty($types))
                    <span class="pill" style="margin-left:6px;">Tipos: {{ is_array($types) ? implode(', ', $types) : (string)$types }}</span>
                  @endif
                </div>

                <span class="pill {{ $isDone ? 'pill-prio-low' : 'pill-status-open' }}">
                  <i class="bi {{ $isDone ? 'bi-check2-circle' : 'bi-circle' }}"></i> {{ $stateLabel }}
                </span>
              </div>

              <div class="mini" style="margin-top:6px;">
                Último avance:
                <b>{{ $u['user'] ?? '—' }}</b>
                @if(($u['at'] ?? null))
                  • {{ $u['at']->diffForHumans() }}
                @endif
              </div>

              @if($u)
                <div style="margin-top:10px; background:#f8fbff; border:1px dashed #dbe7ff; border-radius:12px; padding:10px;">
                  <div class="mini"><b>Qué hice:</b> {{ $u['what'] ?: '—' }}</div>
                  @if(!empty($u['how']))  <div class="mini" style="margin-top:6px;"><b>Cómo:</b> {{ $u['how'] }}</div> @endif
                  @if(!empty($u['notes'])) <div class="mini" style="margin-top:6px;"><b>Observaciones:</b> {{ $u['notes'] }}</div> @endif
                </div>
              @endif

              {{-- FORM DE AVANCE (usa tu storeWork) --}}
              <form action="{{ route('tickets.work.store',$ticket) }}" method="POST" style="margin-top:12px;">
                @csrf

                <input type="hidden" name="check_index" value="{{ $i }}">

                <div class="row g-2">
                  <div class="col-md-3">
                    <label class="mini" style="font-weight:900;">Acción</label>
                    <select name="action" class="form-select">
                      <option value="done">Marcar como HECHO</option>
                      <option value="undone">Marcar como PENDIENTE</option>
                    </select>
                  </div>

                  <div class="col-md-9">
                    <label class="mini" style="font-weight:900;">Qué hice *</label>
                    <input type="text" name="what" class="form-control" required maxlength="2000"
                           placeholder="Ej: Reparé X, probé Y, cambié Z...">
                  </div>

                  <div class="col-md-6">
                    <label class="mini" style="font-weight:900;">Cómo</label>
                    <textarea name="how" class="form-control" rows="2" maxlength="2000"
                              placeholder="Ej: pasos, procedimiento, validaciones..."></textarea>
                  </div>

                  <div class="col-md-6">
                    <label class="mini" style="font-weight:900;">Observaciones</label>
                    <textarea name="notes" class="form-control" rows="2" maxlength="2000"
                              placeholder="Bloqueos, pendientes, notas, etc."></textarea>
                  </div>
                </div>

                {{-- Evidencias --}}
                <div style="margin-top:10px;">
                  <label class="mini" style="font-weight:900;"><i class="bi bi-paperclip"></i> Evidencia de este item</label>
                  <input type="file" class="pond-item" data-hidden="hidden-item-{{ $i }}" multiple>
                  <div id="hidden-item-{{ $i }}"></div>
                  <div class="mini">Se sube primero, y al guardar avance se vincula a este update.</div>
                </div>

                <div class="d-flex justify-content-end" style="margin-top:10px;">
                  <button type="submit" class="btnx btn-soft-green">
                    <i class="bi bi-save"></i> Guardar avance
                  </button>
                </div>
              </form>

            </div>
          @empty
            <div class="mini">No hay checklist en este ticket (checklist vacío).</div>
          @endforelse

        </div>
      </div>
    </div>

  </div>
</div>

<script>
(function(){
  if(typeof FilePond === 'undefined') return;

  document.querySelectorAll('.pond-item').forEach((input) => {
    const hiddenId = input.getAttribute('data-hidden');
    const hiddenWrap = document.getElementById(hiddenId);

    FilePond.create(input, {
      allowMultiple: true,
      instantUpload: true,
      server: {
        process: {
          url: "{{ route('uploads.store') }}",
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
          onload: (res) => {
            const j = JSON.parse(res);
            const h = document.createElement('input');
            h.type = 'hidden';
            h.name = 'attachment_ids[]';
            h.value = j.id;
            hiddenWrap.appendChild(h);
            return String(j.id);
          }
        }
      }
    });
  });
})();
</script>
@endsection