@extends('layouts.app')

@section('title','Ticket #'.$ticket->id)
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

{{-- FilePond (Evidencias en comentario) --}}
<link href="https://unpkg.com/filepond@^4/dist/filepond.min.css" rel="stylesheet">
<script src="https://unpkg.com/filepond@^4/dist/filepond.min.js"></script>

@php
  $canEdit = (int)auth()->id() === (int)$ticket->creator_id;

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

  // Helpers anti-crash (array | Collection | null)
  $ticketAttachments = is_iterable($ticket->attachments ?? null) ? collect($ticket->attachments) : collect();
  $ticketChildren    = is_iterable($ticket->children ?? null) ? collect($ticket->children) : collect();
  $ticketComments    = is_iterable($ticket->comments ?? null) ? collect($ticket->comments) : collect();
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

  .pill{ display:inline-flex; align-items:center; gap:.35rem; padding:.26rem .5rem; border-radius:999px; font-size:.72rem; font-weight:700; border:1px solid var(--line); background:#fff; color:#334155; }
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
  .file .name{ font-weight:700; }
  .file .small{ color:var(--muted); font-size:.82rem; }
  .file a{ text-decoration:none; }
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

        <span class="pill pill-type"><i class="bi bi-tag"></i> {{ $typeLabel }}</span>
        <span class="pill pill-area"><i class="bi bi-diagram-3"></i> {{ $areaLabel }}</span>
      </div>
    </div>
  </div>

  <div class="actions">
    @if($canEdit)
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

      <a href="{{ route('tickets.edit',$ticket) }}" class="btn btn-soft">
        <i class="bi bi-pencil"></i> Editar
      </a>
    @endif

    <a href="{{ route('tickets.index') }}" class="btn btn-soft-gray">
      <i class="bi bi-list"></i> Listado
    </a>
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

      <div class="desc">{{ $desc ?: 'Sin descripción.' }}</div>
    </div>
  </div>

  <div class="row g-3">

    {{-- ================= Evidencias Ticket + Checklist + Subtickets ================= --}}
    <div class="col-lg-4">
      {{-- Evidencias del ticket --}}
      <div class="block mb-3">
        <div class="head">
          <span><i class="bi bi-paperclip me-1"></i> Evidencias</span>
          <span class="small" style="color:var(--muted)">{{ $ticketAttachments->count() }}</span>
        </div>
        <div class="body">
          @forelse($ticketAttachments as $a)
            <div class="file">
              <div class="left">
                <i class="bi bi-file-earmark"></i>
                <div>
                  <div class="name">{{ $a->original_name ?? 'Archivo' }}</div>
                  <div class="small">{{ $a->mime ?? '—' }} • {{ number_format((($a->size ?? 0)/1024), 1) }} KB</div>
                </div>
              </div>
              @php
                $url = null;
                if(isset($a->disk, $a->path)){
                  try { $url = \Storage::disk($a->disk)->url($a->path); } catch(\Throwable $e) { $url = null; }
                }
              @endphp
              @if($url)
                <a class="btn btn-soft-gray" href="{{ $url }}" target="_blank" rel="noopener">
                  <i class="bi bi-box-arrow-up-right"></i>
                </a>
              @endif
            </div>
          @empty
            <div style="color:var(--muted)">Sin evidencias.</div>
          @endforelse
        </div>
      </div>

      {{-- Checklist --}}
      <div class="block mb-3">
        <div class="head">
          <span><i class="bi bi-check2-square me-1"></i> Checklist</span>
          <span class="small" style="color:var(--muted)">{{ is_array($ticket->checklist ?? null) ? count($ticket->checklist) : 0 }}</span>
        </div>
        <div class="body">
          @php $cl = is_array($ticket->checklist ?? null) ? $ticket->checklist : []; @endphp
          @forelse($cl as $i => $item)
            @php
              $text = $item['text'] ?? ('Item '.($i+1));
              $req = (bool)($item['required'] ?? false);
              $ev  = (bool)($item['evidence_required'] ?? false);
              $types = $item['evidence_types'] ?? [];
            @endphp
            <div style="background:#fff;border:1px solid var(--line);border-radius:12px;padding:10px 12px;margin-bottom:8px;">
              <div style="font-weight:700;">{{ $text }}</div>
              <div class="small" style="color:var(--muted); margin-top:4px;">
                @if($req) <span class="pill" style="margin-right:6px;">Obligatorio</span> @endif
                @if($ev)  <span class="pill" style="margin-right:6px;">Requiere evidencia</span> @endif
                @if(!empty($types))
                  <span class="pill">Tipos: {{ is_array($types) ? implode(', ', $types) : (string)$types }}</span>
                @endif
              </div>
            </div>
          @empty
            <div style="color:var(--muted)">Sin checklist.</div>
          @endforelse
        </div>
      </div>

      {{-- Subtickets --}}
      <div class="block">
        <div class="head">
          <span><i class="bi bi-diagram-2 me-1"></i> Subtickets</span>
          <span class="small" style="color:var(--muted)">{{ $ticketChildren->count() }}</span>
        </div>
        <div class="body">
          @forelse($ticketChildren as $ch)
            <div style="background:#fff;border:1px solid var(--line);border-radius:12px;padding:10px 12px;margin-bottom:8px;">
              <div style="font-weight:700;">
                <a href="{{ route('tickets.show',$ch) }}">#{{ $ch->id }} — {{ \Illuminate\Support\Str::limit($ch->title, 50) }}</a>
              </div>
              <div class="small" style="color:var(--muted)">
                {{ $ch->status }} • {{ $ch->priority }}
              </div>
            </div>
          @empty
            <div style="color:var(--muted)">Sin subtickets.</div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- ================= Conversación (con evidencias por comentario) ================= --}}
    <div class="col-lg-8">
      <div class="block">
        <div class="head">
          <span><i class="bi bi-chat-text me-1"></i> Comentarios</span>
          <span class="small" style="color:var(--muted)"><i class="bi bi-chat-dots"></i> {{ $ticketComments->count() }}</span>
        </div>

        <div class="body">
          @forelse($ticketComments as $c)
            @php
              $commentAttachments = is_iterable($c->attachments ?? null) ? collect($c->attachments) : collect();
            @endphp

            <div style="border-bottom:1px dashed var(--line); padding-bottom:12px; margin-bottom:12px;">
              <div class="d-flex align-items-center gap-2">
                <div style="width:36px;height:36px;border-radius:50%;display:grid;place-items:center;background:#fff;border:1px solid var(--line);font-weight:700;">
                  {{ \Illuminate\Support\Str::of($c->user->name)->substr(0,1)->upper() }}
                </div>
                <div>
                  <div style="line-height:1.1">{{ $c->user->name }}</div>
                  <div class="small" style="color:var(--muted)">{{ $c->created_at->diffForHumans() }}</div>
                </div>
              </div>

              <div class="mt-2 desc">{{ $c->body }}</div>

              {{-- Evidencias del comentario --}}
              @if($commentAttachments->count())
                <div class="mt-2">
                  @foreach($commentAttachments as $a)
                    <div class="file" style="margin-bottom:6px;">
                      <div class="left">
                        <i class="bi bi-file-earmark"></i>
                        <div>
                          <div class="name">{{ $a->original_name ?? 'Archivo' }}</div>
                          <div class="small">{{ $a->mime ?? '—' }} • {{ number_format((($a->size ?? 0)/1024), 1) }} KB</div>
                        </div>
                      </div>
                      @php
                        $url = null;
                        if(isset($a->disk, $a->path)){
                          try { $url = \Storage::disk($a->disk)->url($a->path); } catch(\Throwable $e) { $url = null; }
                        }
                      @endphp
                      @if($url)
                        <a class="btn btn-soft-gray" href="{{ $url }}" target="_blank" rel="noopener">
                          <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                      @endif
                    </div>
                  @endforeach
                </div>
              @endif
            </div>
          @empty
            <div class="text-center" style="color:var(--muted)">Sé el primero en comentar.</div>
          @endforelse

          {{-- Nuevo comentario con evidencias --}}
          <form action="{{ route('tickets.comments.store',$ticket) }}" method="POST" class="mt-3">
            @csrf

            <label class="form-label" style="font-weight:600">Agregar comentario</label>
            <textarea name="body" rows="4" class="form-control" placeholder="Escribe un mensaje útil para avanzar…"></textarea>

            <div class="mt-2">
              <label class="form-label" style="font-weight:600">Evidencias del comentario</label>
              <input type="file" id="commentEvidences" multiple>
              <div id="commentAttachmentHidden"></div>

              <div class="small" style="color:var(--muted)">
                <i class="bi bi-paperclip"></i>
                Subida instantánea desactivada hasta definir la ruta <code>uploads.store</code>.
              </div>
            </div>

            <div class="d-flex justify-content-end mt-2">
              <button class="btn btn-soft"><i class="bi bi-send me-1"></i> Publicar</button>
            </div>
          </form>

        </div>
      </div>
    </div>

  </div>
</div>

<script>
(function(){
  const el = document.getElementById('commentEvidences');
  if(!el || typeof FilePond === 'undefined') return;

  // ⚠️ Sin ruta uploads.store aún: desactivamos el upload remoto para que NO truene.
  FilePond.create(el, {
    allowMultiple: true,
    instantUpload: false,
    storeAsFile: true
  });
})();
</script>

@endsection