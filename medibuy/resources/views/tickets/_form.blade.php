@php
  $isEdit = isset($ticket);

  // Valores default
  $ticketType = old('ticket_type', $ticket->ticket_type ?? 'incidencia');
  $area       = old('area', $ticket->area ?? null);

  // watchers seleccionados (para edit)
  $selectedWatcherIds = collect(old('watchers', $selectedWatcherIds ?? ($ticket->watchers->pluck('id')->all() ?? [])))
    ->map(fn($i)=>(int)$i)->all();

  // checklist: si viene como array en old, úsalo; si viene del ticket, úsalo; si no, []
  $checklistVal = old('checklist');
  if (is_null($checklistVal)) {
      $checklistVal = $ticket->checklist ?? [];
  }
@endphp

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

{{-- FilePond (Evidencias) --}}
<link href="https://unpkg.com/filepond@^4/dist/filepond.min.css" rel="stylesheet">
<script src="https://unpkg.com/filepond@^4/dist/filepond.min.js"></script>

<style>
  .form-card{ border:1px solid #e7eaf0; border-radius:16px; box-shadow:0 10px 28px rgba(2,6,23,.06); }
  .hint{ color:#667085; font-size:.9rem; }
  .chip{ display:inline-flex; align-items:center; gap:.4rem; padding:.35rem .6rem; border-radius:999px; border:1px solid #e7eaf0; background:#fff; font-weight:700; font-size:.8rem; }
  .minihelp{font-size:.82rem;color:#6b7280}
  .kpi{font-weight:700;color:#111827}
</style>

<div class="card form-card">
  <div class="card-body">

    {{-- Título --}}
    <div class="mb-3">
      <label class="form-label fw-bold">Título</label>
      <input type="text" name="title" class="form-control" required maxlength="180"
             value="{{ old('title', $ticket->title ?? '') }}" placeholder="Resumen breve del problema / requerimiento">
      @error('title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    {{-- Tipo / Área --}}
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label fw-bold">Tipo de Ticket</label>
        <select name="ticket_type" class="form-select" required>
          <option value="incidencia"     @selected($ticketType==='incidencia')>Incidencia</option>
          <option value="requerimiento"  @selected($ticketType==='requerimiento')>Requerimiento</option>
          <option value="tarea"          @selected($ticketType==='tarea')>Tarea</option>
          <option value="bug"            @selected($ticketType==='bug')>Bug / Error</option>
        </select>
        @error('ticket_type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>

      <div class="col-md-6">
        <label class="form-label fw-bold">Área</label>
        <select name="area" class="form-select">
          <option value="">— Selecciona —</option>
          <option value="hojalateria"    @selected($area==='hojalateria')>Hojalatería</option>
          <option value="mantenimiento"  @selected($area==='mantenimiento')>Mantenimiento</option>
          <option value="sistemas"       @selected($area==='sistemas')>Sistemas</option>
          <option value="administracion" @selected($area==='administracion')>Administración</option>
          <option value="otra"           @selected($area==='otra')>Otra</option>
        </select>
        <div class="minihelp mt-1"><i class="bi bi-diagram-3"></i> Te ayuda a filtrar y asignar correctamente.</div>
        @error('area')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- Descripción --}}
    <div class="mb-3 mt-3">
      <label class="form-label fw-bold">Descripción</label>
      <textarea name="body" class="form-control" rows="6"
        placeholder="Contexto, pasos, qué se requiere, criterios de aceptación...">{{ old('body', $ticket->description ?? $ticket->body ?? '') }}</textarea>
      @error('body')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      <div class="hint mt-1"><i class="bi bi-info-circle"></i> Puedes adjuntar evidencias aquí mismo (abajo) y también en comentarios.</div>
    </div>

    {{-- Checklist (simple: JSON o array en request) --}}
    <div class="mb-3">
      <label class="form-label fw-bold">Checklist (opcional)</label>
      <textarea name="checklist_json" class="form-control" rows="4"
        placeholder='Ejemplo: [{"text":"Foto antes","required":true,"evidence_required":true,"evidence_types":["image"]}]'>{{ old('checklist_json', is_array($checklistVal) ? json_encode($checklistVal, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES) : (string)$checklistVal) }}</textarea>
      <div class="minihelp mt-1">
        <i class="bi bi-check2-square"></i> Si lo llenas, debe ser JSON válido. (Luego lo hacemos visual con IA).
      </div>
      @error('checklist')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      @error('checklist_json')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    {{-- Prioridad / Estado / Visibilidad --}}
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label fw-bold">Prioridad</label>
        <select name="priority" class="form-select" required>
          @foreach(['low'=>'Baja','medium'=>'Media','high'=>'Alta','urgent'=>'Urgente'] as $k=>$v)
            <option value="{{ $k }}" @selected(old('priority', $ticket->priority ?? 'medium')===$k)>{{ $v }}</option>
          @endforeach
        </select>
        @error('priority')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label fw-bold">Estado</label>
        <select name="status" class="form-select" required>
          @foreach(['open'=>'Abierto','in_progress'=>'En progreso','resolved'=>'Resuelto','closed'=>'Cerrado'] as $k=>$v)
            <option value="{{ $k }}" @selected(old('status', $ticket->status ?? 'open')===$k)>{{ $v }}</option>
          @endforeach
        </select>
        @error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>
      <div class="col-md-4">
        <label class="form-label fw-bold">Visibilidad</label>
        <select name="visibility" class="form-select" required>
          {{-- Compat: "selected" también lo aceptas y el controller lo convierte a shared --}}
          <option value="private"   @selected(old('visibility', $ticket->visibility ?? 'private')==='private')>Privado</option>
          <option value="shared"    @selected(old('visibility', $ticket->visibility ?? '')==='shared')>Seleccionados</option>
          <option value="selected"  @selected(old('visibility')==='selected')>Seleccionados (compat)</option>
          <option value="public"    @selected(old('visibility', $ticket->visibility ?? '')==='public')>Público</option>
        </select>
        @error('visibility')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>
    </div>

    {{-- Asignado + Watchers --}}
    <div class="row g-3 mt-1">
      <div class="col-md-6">
        <label class="form-label fw-bold">Asignado a</label>
        <select name="assignee_id" class="form-select">
          <option value="">— Sin asignar —</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(old('assignee_id', $ticket->assignee_id ?? null)==$u->id)>{{ $u->name }}</option>
          @endforeach
        </select>
        @error('assignee_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        <div class="minihelp mt-1"><i class="bi bi-person-check"></i> Responsable principal.</div>
      </div>

      <div class="col-md-6">
        <label class="form-label fw-bold">Observadores (Watchers)</label>
        <select name="watchers[]" class="form-select" multiple size="6">
          @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(in_array($u->id, $selectedWatcherIds, true))>{{ $u->name }}</option>
          @endforeach
        </select>
        @error('watchers')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        <div class="hint mt-1">
          <i class="bi bi-people"></i>
          Solo aplica si la visibilidad es <span class="chip">Seleccionados</span>.
          En modo <span class="chip">Público</span> todos lo verán.
        </div>
      </div>
    </div>

    {{-- Evidencias --}}
    <div class="mt-3">
      <label class="form-label fw-bold">Evidencias / Adjuntos</label>
      <input type="file" id="evidences" multiple>
      <div class="minihelp mt-1">
        <i class="bi bi-paperclip"></i> Puedes subir: video, imagen, foto, Word, Excel, PDF, etc.
        <span class="kpi">Se guardan y se vinculan al ticket al enviar.</span>
      </div>

      {{-- Hidden inputs dinámicos: attachment_ids[] --}}
      <div id="attachmentHidden"></div>

      @error('attachment_ids')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      @error('attachment_ids.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

  </div>
</div>

<script>
(function(){
  // FilePond config
  const el = document.getElementById('evidences');
  if(!el || typeof FilePond === 'undefined') return;

  const hidden = document.getElementById('attachmentHidden');

  const pond = FilePond.create(el, {
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
          hidden.appendChild(h);
          return String(j.id);
        }
      }
    }
  });
})();
</script>