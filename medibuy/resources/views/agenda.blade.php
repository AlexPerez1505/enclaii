@extends('layouts.app')
@section('title','Agenda')
@section('titulo','Agenda')

@section('content')
@php
  // Fallback para la lista de usuarios (invitados)
  $usersRoute = \Illuminate\Support\Facades\Route::has('agenda.users')
    ? route('agenda.users')
    : url('/usuarios');
@endphp

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">

<style>
  #agenda-cal{
    --ink:#0f172a;
    --muted:#6b7280;
    --line:#e5e7eb;
    --bg:#f4f5fb;
    --card:#ffffff;
    --brand:#2563eb;
    --brand-soft:#dbeafe;

    font-family:'Outfit',system-ui,-apple-system,blinkmacsystemfont,"Segoe UI",sans-serif;
    background:radial-gradient(circle at top,#eef2ff,#f9fafb);
    min-height:calc(100vh - 80px);
    padding:clamp(16px,3vw,28px);
  }
  #agenda-cal .wrap{max-width:1200px;margin:0 auto}

  /* ---------- HEADER ---------- */
  #agenda-cal .top{
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    margin-bottom:14px;
  }
  #agenda-cal h1{
    margin:0;
    font-size:clamp(20px,2.4vw,28px);
    color:var(--ink);
    letter-spacing:.02em;
  }
  #agenda-cal .top-sub{
    font-size:13px;
    color:var(--muted);
  }
  #agenda-cal .actions{display:flex;gap:10px;flex-wrap:wrap}
  #agenda-cal .btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:9px 14px;
    border-radius:999px;
    border:1px solid #d1d5db;
    background:#fff;
    color:var(--ink);
    font-size:13px;
    text-decoration:none;
    cursor:pointer;
    transition:.18s ease all;
  }
  #agenda-cal .btn span.icon{
    display:inline-flex;
    width:18px;
    height:18px;
    border-radius:999px;
    align-items:center;
    justify-content:center;
    border:1px solid #dbeafe;
    background:#eff6ff;
    font-size:11px;
  }
  #agenda-cal .btn.primary{
    color:#0a0a0a;
    border-color:transparent;
    font-weight:600;
  }
  #agenda-cal .btn:hover{
    transform:translateY(-1px);
    box-shadow:0 12px 28px rgba(15,23,42,.12);
  }
  #agenda-cal .btn.primary:hover{
    box-shadow:0 18px 40px rgba(37,99,235,.4);
  }

  /* ---------- FULLCALENDAR --------- */
  .fc{
    background:var(--card);
    border-radius:18px;
    border:1px solid var(--line);
    padding:6px;
    box-shadow:0 20px 55px rgba(15,23,42,.07);
  }
  .fc .fc-toolbar.fc-header-toolbar{
    padding:8px 10px 6px;
    margin-bottom:4px;
    gap:8px;
    flex-wrap:wrap;
  }
  .fc .fc-toolbar-title{
    font-weight:700;
    color:var(--ink);
    font-size:16px;
  }
  .fc .fc-button{
    border-radius:999px;
    padding:4px 10px;
    border:1px solid #e5e7eb;
    background:#fff;
    color:#111827;
    font-size:12px;
    box-shadow:none;
  }
  .fc .fc-button-primary{
    background:#eff6ff;
    border-color:#dbeafe;
    color:#1d4ed8;
  }
  .fc .fc-button-primary:not(:disabled).fc-button-active{
    background:#2563eb;
    border-color:#2563eb;
    color:#f9fafb;
  }
  .fc .fc-daygrid-day-number{
    color:#4b5563;
    font-size:11px;
    padding:4px 6px;
  }
  .fc .fc-col-header-cell-cushion{
    padding:6px 4px;
    font-size:11px;
    font-weight:600;
    color:#6b7280;
    text-transform:uppercase;
    letter-spacing:.08em;
  }
  .fc .fc-day-today{
    background:rgba(219,234,254,.6);
  }

  /* ---------- EVENTOS (PILLS) ---------- */
  .agenda-event-pill{
    border-radius:999px !important;
    border-width:1px !important;
    padding:2px 6px !important;
    font-size:11px !important;
    line-height:1.25 !important;
    display:flex;
    align-items:center;
    gap:4px;
    overflow:hidden;
    white-space:nowrap;
  }
  .agenda-event-pill .time-dot{
    display:inline-block;
    width:6px;
    height:6px;
    border-radius:999px;
    margin-right:4px;
    flex-shrink:0;
  }
  .agenda-event-pill .title{
    font-weight:600;
    flex:1;
    min-width:0;
    text-overflow:ellipsis;
    overflow:hidden;
  }

  /* ---------- MODAL CUSTOM ---------- */
  #agenda-modal-backdrop{
    position:fixed;
    inset:0;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(15,23,42,.32);
    z-index:50;
    backdrop-filter:blur(3px);
    opacity:0;
    pointer-events:none;
    transition:opacity .22s ease-out;
  }
  #agenda-modal-backdrop.show{
    opacity:1;
    pointer-events:auto;
  }

  #agenda-modal{
    width:min(720px,92vw);
    background:#ffffff;
    border-radius:20px;
    border:1px solid #e5e7eb;
    box-shadow:0 26px 70px rgba(15,23,42,.35);
    overflow:hidden;
    transform:translateY(18px) scale(.97);
    opacity:0;
    transition:transform .22s ease-out, opacity .22s ease-out;
  }
  #agenda-modal-backdrop.show #agenda-modal{
    transform:translateY(0) scale(1);
    opacity:1;
  }

  #agenda-modal .head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:14px 18px;
    background:linear-gradient(120deg,#eff6ff,#ffffff);
    border-bottom:1px solid #e5e7eb;
  }
  #agenda-modal .head h3{
    margin:0;
    font-size:17px;
    color:var(--ink);
  }
  #agenda-modal .body{
    padding:18px;
    display:grid;
    grid-template-columns:minmax(0,1.3fr) minmax(0,1fr);
    gap:18px;
    max-height:calc(100vh - 190px);
    overflow-y:auto;
  }
  #agenda-modal .col-block-title{
    font-size:11px;
    letter-spacing:.12em;
    text-transform:uppercase;
    color:#9ca3af;
    font-weight:700;
    margin-bottom:4px;
  }
  #agenda-modal .grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px;
  }
  #agenda-modal label{
    display:block;
    font-weight:600;
    font-size:13px;
    margin:10px 0 5px;
    color:var(--ink);
  }
  #agenda-modal input,
  #agenda-modal select,
  #agenda-modal textarea{
    width:100%;
    padding:9px 11px;
    border-radius:12px;
    border:1px solid #e5e7eb;
    background:#f9fafb;
    font-size:13px;
  }
  #agenda-modal textarea{resize:vertical;min-height:70px}
  #agenda-modal .foot{
    display:flex;
    gap:10px;
    justify-content:flex-end;
    padding:14px 18px;
    background:#f9fafb;
    border-top:1px solid #e5e7eb;
  }
  #agenda-modal .btn{
    border-radius:999px;
    padding:8px 14px;
    border:1px solid #d1d5db;
    background:#fff;
    font-size:13px;
    cursor:pointer;
    transition:.16s ease all;
  }
  #agenda-modal .btn.danger{
    border-color:#fecaca;
    background:#fef2f2;
    color:#b91c1c;
  }

  /* 🔳 Botón GUARDAR: negro, texto blanco, hover invertido */
  #agenda-modal #btn-save{
    background:#020617;
    color:#ffffff;
    border-color:#020617;
    font-weight:600;
  }
  #agenda-modal #btn-save:hover{
    background:#ffffff;
    color:#020617;
    border-color:#020617;
  }

  #agenda-modal .btn.primary{
    font-weight:600;
  }

  /* Chips invitados modernos */
  .chip-modern{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:6px 10px;
    border-radius:999px;
    border:1px solid #e5e7eb;
    background:#f9fafb;
    font-size:12px;
  }
  .chip-avatar{
    width:22px;
    height:22px;
    border-radius:999px;
    background:linear-gradient(135deg,#2563eb,#4f46e5);
    color:#f9fafb;
    font-size:11px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    flex-shrink:0;
  }
  .chip-main{
    display:flex;
    flex-direction:column;
    min-width:0;
  }
  .chip-name{
    font-weight:600;
    color:#0f172a;
    white-space:nowrap;
    text-overflow:ellipsis;
    overflow:hidden;
  }
  .chip-meta{
    font-size:11px;
    color:#6b7280;
    white-space:nowrap;
    text-overflow:ellipsis;
    overflow:hidden;
  }

  .hint-text{
    font-size:11px;
    color:#9ca3af;
    margin-top:3px;
  }

  @media (max-width:768px){
    #agenda-cal{padding:14px}
    #agenda-modal .body{
      grid-template-columns:1fr;
    }
    #agenda-modal .grid{grid-template-columns:1fr}
    .fc{padding:4px}
  }
</style>

<div id="agenda-cal">
  <div class="wrap">
    {{-- HEADER TOP: título + botón nuevo evento --}}
    <div class="top">
      <div>
        <h1>Agenda</h1>
        <div class="top-sub">
          Calendario con invitados, notas y recordatorios por correo y WhatsApp.
        </div>
      </div>
      <div class="actions">
        <button id="btn-new" class="btn primary">
          <span class="icon">+</span>
          <span>Nuevo evento</span>
        </button>
      </div>
    </div>

    {{-- CALENDARIO --}}
    <div id="calendar"></div>
  </div>

  {{-- ===== Modal Crear/Editar ===== --}}
  <div id="agenda-modal-backdrop">
    <div id="agenda-modal" role="dialog" aria-modal="true">
      <div class="head">
        <h3 id="modal-title">Nuevo evento</h3>
        <button id="btn-close" class="btn" style="border:none;background:transparent;font-size:18px;">✕</button>
      </div>
      <div class="body">
        {{-- Columna izquierda: información base --}}
        <div>
          <div class="col-block-title">Evento</div>
          <form id="agenda-form">
            @csrf
            <input type="hidden" name="id" id="ev-id">

            <label>Título *</label>
            <input name="title" id="ev-title" required>

            <label>Ubicación</label>
            <input name="location" id="ev-location">

            <label>Inicio *</label>
            <input type="datetime-local" name="start_at" id="ev-start" required>

            <label>Invitados</label>
            <select id="ev-guests" name="guests[]" multiple></select>
            <div id="ev-chips" style="margin-top:6px;display:flex;flex-wrap:wrap;gap:6px;"></div>

            <label>Observaciones / notas</label>
            <textarea name="notes" id="ev-notes" rows="3"></textarea>
          </form>
        </div>

        {{-- Columna derecha: recordatorios --}}
        <div>
          <div class="col-block-title">Recordatorio</div>

          <div class="grid">
            <div>
              <label>Recordar (minutos antes)</label>
              <input type="number" id="ev-offset" min="1" max="10080" value="60">
              <div class="hint-text">Ejemplo: 1440 = 1 día antes.</div>
            </div>
            <div>
              <label>Repetición</label>
              <select id="ev-repeat">
                <option value="none">Sin repetición</option>
                <option value="daily">Diaria</option>
                <option value="weekly">Semanal</option>
                <option value="monthly">Mensual</option>
              </select>
            </div>
          </div>

        </div>
      </div>
      <div class="foot">
        <button id="btn-delete" class="btn danger" style="display:none">Eliminar</button>
        <button id="btn-save" class="btn primary">Guardar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const DEFAULT_TZ = 'America/Mexico_City';

  /* ===== Endpoints ===== */
  const ENDPOINTS = {
    users: @json($usersRoute),
    events: "/eventos",
    store:  "/eventos",
    update: (id)=> `/eventos/${id}`
  };

  /* ===== Invitados ===== */
  const usersMap = {}; // {id:{name,email,phone}}

  async function loadUsers(){
    try{
      const res = await fetch(ENDPOINTS.users, {headers:{'X-Requested-With':'XMLHttpRequest'}});
      const list = await res.json();
      const sel = document.getElementById('ev-guests');
      sel.innerHTML = '';
      list.forEach(u=>{
        usersMap[String(u.id)] = {
          name:  u.name,
          email: u.email || '',
          phone: u.phone || ''
        };
        const opt = document.createElement('option');
        opt.value = u.id;
        opt.textContent = u.name;
        sel.appendChild(opt);
      });
      enableMultiToggle();   // para no usar CTRL
      updateChips();
    }catch(e){
      console.warn('No se pudieron cargar usuarios', e);
    }
  }

  function getInitials(name){
    if(!name) return '?';
    const parts = String(name).trim().split(/\s+/);
    if(parts.length === 1) return parts[0].charAt(0).toUpperCase();
    return (parts[0].charAt(0) + parts[1].charAt(0)).toUpperCase();
  }

  function updateChips(){
    const sel  = document.getElementById('ev-guests');
    const wrap = document.getElementById('ev-chips');
    wrap.innerHTML = '';
    Array.from(sel.selectedOptions).forEach(opt=>{
      const id = String(opt.value);
      const u  = usersMap[id] || {name: opt.textContent, email:'', phone:''};
      const metaPieces = [];
      if(u.email) metaPieces.push(u.email);
      if(u.phone) metaPieces.push(u.phone);
      const meta = metaPieces.join(' • ');

      const chip = document.createElement('span');
      chip.className = 'chip-modern';
      chip.innerHTML = `
        <div class="chip-avatar">${getInitials(u.name)}</div>
        <div class="chip-main">
          <div class="chip-name" title="${u.name}">${u.name}</div>
          <div class="chip-meta">${meta || 'Sin datos de contacto'}</div>
        </div>
        <button type="button" aria-label="Quitar" style="border:0;background:transparent;line-height:1;cursor:pointer;font-size:14px;">&times;</button>
      `;
      chip.querySelector('button').addEventListener('click', ()=>{
        opt.selected = false;
        updateChips();
      });
      wrap.appendChild(chip);
    });
  }

  // Permite seleccionar múltiples invitados con clic (sin CTRL)
  function enableMultiToggle(){
    const sel = document.getElementById('ev-guests');
    if(!sel) return;
    sel.addEventListener('mousedown', (e)=>{
      const option = e.target;
      if(option.tagName === 'OPTION'){
        e.preventDefault();
        option.selected = !option.selected;
        updateChips();
      }
    });
  }

  document.getElementById('ev-guests').addEventListener('change', updateChips);

  const guestsArray = (sel)=> Array.from(sel.selectedOptions).map(o=>o.value);

  /* ===== Utilidades fechas ===== */
  const toLocalInput = (iso)=>{
    if(!iso) return '';
    const d = new Date(iso);
    if(isNaN(d)) return '';
    const p = (n)=> String(n).padStart(2,'0');
    return `${d.getFullYear()}-${p(d.getMonth()+1)}-${p(d.getDate())}T${p(d.getHours())}:${p(d.getMinutes())}`;
  };

  // Convierte un Date JS a string "YYYY-MM-DDTHH:mm" en hora local
  const dateToLocalString = (d)=>{
    if(!d) return null;
    const p = (n)=> String(n).padStart(2,'0');
    return `${d.getFullYear()}-${p(d.getMonth()+1)}-${p(d.getDate())}T${p(d.getHours())}:${p(d.getMinutes())}`;
  };

  /* ===== Template interno para mensaje de WhatsApp (solo referencia / logs) ===== */
  function buildInternalTemplate(ev, forName='__NOMBRE__'){
    const s = ev.start ? new Date(ev.start) : null;
    const fmt = (d)=> d ? d.toLocaleString('es-MX',{dateStyle:'medium', timeStyle:'short'}) : '—';
    const lines = [
      `Hola ${forName},`,
      `Te recordamos el evento "${ev.title||'-'}" programado para ${fmt(s)}.`,
      ev.location ? `Ubicación: ${ev.location}.` : null,
      ev.notes ? `Notas: ${ev.notes}.` : null,
      `Si necesitas reprogramar o tienes dudas, responde a este mensaje.`,
      `— Agenda`
    ].filter(Boolean);
    return lines.join('\n');
  }

  /* ===== Modal ===== */
  const modalBackdrop = document.getElementById('agenda-modal-backdrop');
  const btnNew   = document.getElementById('btn-new');
  const btnClose = document.getElementById('btn-close');
  const btnSave  = document.getElementById('btn-save');
  const btnDelete= document.getElementById('btn-delete');

  const f = {
    id:       document.getElementById('ev-id'),
    title:    document.getElementById('ev-title'),
    location: document.getElementById('ev-location'),
    start:    document.getElementById('ev-start'),
    guests:   document.getElementById('ev-guests'),
    notes:    document.getElementById('ev-notes'),
    offset:   document.getElementById('ev-offset'),
    repeat:   document.getElementById('ev-repeat'),
  };

  let editingId = null;

  function openModal(mode='new', data=null){
    modalBackdrop.classList.add('show');
    document.body.style.overflow = 'hidden';

    if(mode === 'new'){
      document.getElementById('modal-title').textContent = 'Nuevo evento';
      btnDelete.style.display = 'none';
      editingId = null;

      f.id.value = '';
      f.title.value = '';
      f.location.value = '';
      f.start.value = '';
      f.notes.value = '';
      f.offset.value = 60;
      f.repeat.value = 'none';
      Array.from(f.guests.options).forEach(o=> o.selected=false);
      updateChips();
    }else if(data){
      document.getElementById('modal-title').textContent = 'Editar evento';
      btnDelete.style.display = 'inline-flex';
      editingId = data.id;

      f.id.value = data.id;
      f.title.value = data.title || '';
      f.location.value = data.extendedProps?.location || '';
      f.start.value = toLocalInput(data.start);
      f.notes.value = data.extendedProps?.notes || '';

      const ids = data.extendedProps?.guests || [];
      Array.from(f.guests.options).forEach(o=>{
        const val = o.value;
        o.selected = ids.includes(val) || ids.includes(+val);
      });
      updateChips();

      f.offset.value = data.extendedProps?.remind_offset_minutes ?? 60;
      f.repeat.value = data.extendedProps?.repeat ?? 'none';
    }
  }

  function closeModal(){
    modalBackdrop.classList.remove('show');
    document.body.style.overflow = 'auto';
  }

  btnNew.addEventListener('click', ()=> openModal('new'));
  btnClose.addEventListener('click', closeModal);
  modalBackdrop.addEventListener('click', (e)=>{ if(e.target === modalBackdrop) closeModal(); });

  /* ===== FullCalendar ===== */
  const calendarEl = document.getElementById('calendar');

  const palette = [
    { bg: '#fee2e2', border: '#fecaca' }, // rojo suave
    { bg: '#dbeafe', border: '#bfdbfe' }, // azul
    { bg: '#dcfce7', border: '#bbf7d0' }, // verde
    { bg: '#fef3c7', border: '#fde68a' }, // amarillo
    { bg: '#ede9fe', border: '#ddd6fe' }, // morado
    { bg: '#cffafe', border: '#a5f3fc' }, // cian
  ];

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    height: 'auto',
    locale: 'es',
    firstDay: 1,
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },
    selectable: true,
    editable: true,
    eventStartEditable: true,
    eventDurationEditable: true,

    events: {
      url: ENDPOINTS.events,
      failure() {
        alert('No se pudo cargar la agenda.');
      }
    },

    dateClick(info){
      openModal('new');
      // prefijar inicio en el día seleccionado (09:00) SIN jugar con timezone
      f.start.value = info.dateStr + 'T09:00';
      f.title.focus();
    },

    eventClick(info){
      openModal('edit', info.event.toPlainObject());
    },

    eventDrop: saveDragResize,
    eventResize: saveDragResize,

    eventDidMount(info){
      const idNum = parseInt(info.event.id || '0', 10);
      const color = palette[idNum % palette.length];
      const el = info.el;

      el.classList.add('agenda-event-pill');
      el.style.backgroundColor = color.bg;
      el.style.borderColor = color.border;
      el.style.color = '#0f172a';

      const title = info.event.title || '';
      const timeText = info.timeText || '';

      el.innerHTML = `
        <span class="time-dot" style="background:${color.border};"></span>
        <span class="title">${timeText ? timeText + ' ' : ''}${title}</span>
      `;
    },
  });

  function saveDragResize(info){
    const ext = info.event.extendedProps || {};

    fetch(ENDPOINTS.update(info.event.id), {
      method: 'PUT',
      headers:{
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': csrf,
        'Accept':'application/json'
      },
      body: JSON.stringify({
        // Mandamos la hora en formato local, igual que el input
        start: info.event.start ? dateToLocalString(info.event.start) : null,
        timezone: ext.timezone || DEFAULT_TZ,
      })
    }).then(()=> calendar.refetchEvents())
      .catch(()=> alert('No se pudo mover el evento.'));
  }

  calendar.render();

  /* ===== Guardar (crear/editar) ===== */
  btnSave.addEventListener('click', async () => {
    const payload = {
      title: f.title.value.trim(),
      location: f.location.value.trim() || null,
      all_day: false,
      // 👇 mandamos el valor TAL CUAL del input (ej. "2025-12-11T17:50")
      start: f.start.value,
      guests: guestsArray(f.guests),
      notes: (f.notes.value || '').trim() || null,
      repeat: f.repeat.value || 'none',
      timezone: DEFAULT_TZ,
      remind_offset_minutes: parseInt(f.offset.value || '60', 10),
    };

    if(!payload.title || !payload.start){
      alert('Título e inicio son obligatorios.');
      return;
    }

    const url = editingId ? ENDPOINTS.update(editingId) : ENDPOINTS.store;
    const method = editingId ? 'PUT' : 'POST';

    try{
      const res = await fetch(url, {
        method,
        headers:{
          'Content-Type':'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept':'application/json'
        },
        body: JSON.stringify(payload)
      });
      if(!res.ok) throw new Error('Error en el guardado');
      closeModal();
      editingId = null;
      calendar.refetchEvents();
    }catch(e){
      console.error(e);
      alert('No se pudo guardar el evento. Revisa los campos.');
    }
  });

  /* ===== Eliminar ===== */
  btnDelete.addEventListener('click', async () => {
    if(!editingId) return;
    if(!confirm('¿Eliminar el evento de forma permanente?')) return;
    try{
      const res = await fetch(ENDPOINTS.update(editingId), {
        method:'DELETE',
        headers:{
          'X-CSRF-TOKEN': csrf,
          'Accept':'application/json'
        }
      });
      if(!res.ok) throw new Error('Error al eliminar');
      closeModal();
      editingId = null;
      calendar.refetchEvents();
    }catch(e){
      console.error(e);
      alert('No se pudo eliminar el evento.');
    }
  });

  /* ===== Init: cargar invitados ===== */
  loadUsers();
});
</script>
@endsection
