@extends('layouts.app')

@section('title','Avance Ticket #'.$ticket->id)
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>

<style>
  :root{ --ink:#0f172a; --muted:#6b7280; --line:#e7ebf0; --bg:#f3f7ff; }
  body{ background:var(--bg); color:var(--ink); }
  .wrap{ max-width:1100px; margin:90px auto 30px; padding:0 14px; }
  .cardx{ background:#fff; border:1px solid var(--line); border-radius:16px; box-shadow:0 10px 28px rgba(2,6,23,.06); overflow:hidden; }
  .head{ padding:14px 16px; border-bottom:1px solid var(--line); display:flex; align-items:center; justify-content:space-between; gap:10px; }
  .body{ padding:16px; }
  .pill{ display:inline-flex; align-items:center; gap:.35rem; padding:.25rem .55rem; border-radius:999px; font-weight:800; font-size:.75rem; border:1px solid var(--line); background:#fff; }
  .s-pending{ background:#f1f5f9; border-color:#e2e8f0; }
  .s-in_progress{ background:#fef9c3; border-color:#fde68a; }
  .s-done{ background:#dcfce7; border-color:#bbf7d0; }
  .s-blocked{ background:#fee2e2; border-color:#fecaca; }
  .rowx{ display:flex; gap:10px; flex-wrap:wrap; color:var(--muted); font-size:.92rem; }
  .item{ border:1px solid var(--line); border-radius:14px; padding:12px; margin-bottom:12px; background:#fff; }
  .file{ display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 12px; border:1px solid var(--line); border-radius:12px; margin-bottom:8px; }
  .file .left{ display:flex; align-items:center; gap:10px; }
  .file .name{ font-weight:800; }
  .small{ color:var(--muted); font-size:.82rem; }
  .btnx{ display:inline-flex; align-items:center; gap:.45rem; padding:.55rem .9rem; border-radius:12px; border:1px solid var(--line); background:#fff; text-decoration:none; font-weight:800; color:#111827; }
</style>

<div class="wrap">

  <div class="cardx mb-3">
    <div class="head">
      <div>
        <div style="font-weight:900; font-size:18px;">Avance / Supervisión — Ticket #{{ $ticket->id }}</div>
        <div class="small">Solo lectura. Aquí ves quién avanzó, qué hizo, cómo, observaciones y evidencias.</div>
      </div>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btnx" href="{{ route('tickets.show',$ticket) }}"><i class="bi bi-eye"></i> Ver ticket</a>
        <a class="btnx" href="{{ route('tickets.work',$ticket) }}"><i class="bi bi-play-circle"></i> Vista asignado</a>
        <a class="btnx" href="{{ route('tickets.index') }}"><i class="bi bi-list"></i> Listado</a>
      </div>
    </div>

    <div class="body">
      <div class="rowx mb-2">
        <span><i class="bi bi-person-badge"></i> Creador: <b>{{ $ticket->creator->name }}</b></span>
        <span><i class="bi bi-person-check"></i> Asignado: <b>{{ $ticket->assignee?->name ?? '—' }}</b></span>
        <span><i class="bi bi-people"></i> Watchers: <b>{{ $ticket->watchers->count() }}</b></span>
      </div>
      <div style="white-space:pre-wrap;">{{ $ticket->description ?? $ticket->body ?? 'Sin descripción.' }}</div>
    </div>
  </div>

  <div class="cardx">
    <div class="head">
      <div style="font-weight:900;"><i class="bi bi-check2-square"></i> Checklist (bitácora)</div>
      <div class="small">{{ $ticket->checklistItems->count() }} items</div>
    </div>

    <div class="body">
      @forelse($ticket->checklistItems as $it)
        @php
          $cls = match($it->status){
            'pending' => 's-pending',
            'in_progress' => 's-in_progress',
            'done' => 's-done',
            'blocked' => 's-blocked',
            default => 's-pending',
          };
          $label = match($it->status){
            'pending' => 'Pendiente',
            'in_progress' => 'En progreso',
            'done' => 'Hecho',
            'blocked' => 'Bloqueado',
            default => $it->status,
          };
        @endphp

        <div class="item">
          <div style="display:flex; justify-content:space-between; gap:10px; flex-wrap:wrap;">
            <div style="font-weight:900;">
              #{{ $it->position+1 }} — {{ $it->text }}
              @if($it->required) <span class="pill" style="margin-left:6px;">Obligatorio</span> @endif
              @if($it->evidence_required) <span class="pill" style="margin-left:6px;">Requiere evidencia</span> @endif
            </div>
            <span class="pill {{ $cls }}">{{ $label }}</span>
          </div>

          <div class="small mt-2">
            Último update:
            <b>{{ $it->updater?->name ?? '—' }}</b>
            @if($it->updated_at_action) • {{ $it->updated_at_action->format('Y-m-d H:i') }} @endif
          </div>

          @if($it->what_done)
            <div class="mt-2"><b>Qué hizo:</b><div style="white-space:pre-wrap;">{{ $it->what_done }}</div></div>
          @endif
          @if($it->how_done)
            <div class="mt-2"><b>Cómo:</b><div style="white-space:pre-wrap;">{{ $it->how_done }}</div></div>
          @endif
          @if($it->observations)
            <div class="mt-2"><b>Observaciones:</b><div style="white-space:pre-wrap;">{{ $it->observations }}</div></div>
          @endif

          <div class="mt-3">
            <div class="small" style="font-weight:800;"><i class="bi bi-paperclip"></i> Evidencias</div>
            @forelse($it->attachments as $a)
              <div class="file">
                <div class="left">
                  <i class="bi bi-file-earmark"></i>
                  <div>
                    <div class="name">{{ $a->original_name }}</div>
                    <div class="small">{{ $a->mime }} • {{ number_format(($a->size ?? 0)/1024, 1) }} KB</div>
                  </div>
                </div>
                <a class="btnx" href="{{ \Storage::disk($a->disk)->url($a->path) }}" target="_blank">
                  <i class="bi bi-box-arrow-up-right"></i> Abrir
                </a>
              </div>
            @empty
              <div class="small">Sin evidencias.</div>
            @endforelse
          </div>
        </div>
      @empty
        <div class="small">No hay checklist.</div>
      @endforelse
    </div>
  </div>

</div>
@endsection