@php
  $isEdit = isset($ticket);
@endphp

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
  .form-card{ border:1px solid #e7eaf0; border-radius:16px; box-shadow:0 10px 28px rgba(2,6,23,.06); }
  .hint{ color:#667085; font-size:.9rem; }
  .chip{ display:inline-flex; align-items:center; gap:.4rem; padding:.35rem .6rem; border-radius:999px; border:1px solid #e7eaf0; background:#fff; font-weight:700; font-size:.8rem; }
</style>

<div class="card form-card">
  <div class="card-body">
    <div class="mb-3">
      <label class="form-label fw-bold">Título</label>
      <input type="text" name="title" class="form-control" required maxlength="180"
             value="{{ old('title', $ticket->title ?? '') }}" placeholder="Resumen breve del problema">
    </div>

    <div class="mb-3">
      <label class="form-label fw-bold">Descripción</label>
      <textarea name="body" class="form-control" rows="6" placeholder="Contexto, pasos para reproducir, evidencias...">{{ old('body', $ticket->body ?? '') }}</textarea>
      <div class="hint mt-1"><i class="bi bi-info-circle"></i> Puedes adjuntar archivos en comentarios después de crear el ticket.</div>
    </div>

    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label fw-bold">Prioridad</label>
        <select name="priority" class="form-select" required>
          @foreach(['low'=>'Baja','medium'=>'Media','high'=>'Alta'] as $k=>$v)
            <option value="{{ $k }}" @selected(old('priority', $ticket->priority ?? 'medium')===$k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-bold">Estado</label>
        <select name="status" class="form-select" required>
          @foreach(['open'=>'Abierto','in_progress'=>'En progreso','resolved'=>'Resuelto','closed'=>'Cerrado'] as $k=>$v)
            <option value="{{ $k }}" @selected(old('status', $ticket->status ?? 'open')===$k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-bold">Visibilidad</label>
        <select name="visibility" class="form-select" required>
          @foreach(['private'=>'Privado','selected'=>'Seleccionados','public'=>'Público'] as $k=>$v)
            <option value="{{ $k }}" @selected(old('visibility', $ticket->visibility ?? 'private')===$k)>{{ $v }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-md-6">
        <label class="form-label fw-bold">Asignado a</label>
        <select name="assignee_id" class="form-select">
          <option value="">— Sin asignar —</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(old('assignee_id', $ticket->assignee_id ?? null)==$u->id)>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-bold">Usuarios con visibilidad (ver/seguir)</label>
        <select name="watchers[]" class="form-select" multiple size="6">
          @php
            $selected = collect(old('watchers', $selectedWatcherIds ?? []))->map(fn($i)=>(int)$i)->all();
          @endphp
          @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(in_array($u->id, $selected, true))>{{ $u->name }}</option>
          @endforeach
        </select>
        <div class="hint mt-1"><i class="bi bi-people"></i> Solo aplica si la visibilidad es <span class="chip">Seleccionados</span>. En modo <span class="chip">Público</span> todos lo verán.</div>
      </div>
    </div>
  </div>
</div>
