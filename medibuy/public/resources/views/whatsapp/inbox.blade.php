@extends('layouts.app')

@section('title', 'WhatsApp')
@section('titulo', 'WhatsApp')

@section('content')
@php
  use Illuminate\Support\Str;
  use App\Models\Cliente;

  $getDisplayName = function (?string $msisdn) {
      if (!$msisdn) return null;
      static $cache = [];
      $digits = preg_replace('/\D+/', '', $msisdn);
      $key = substr($digits, -10);
      if (isset($cache[$key])) return $cache[$key];

      $cli = Cliente::where('telefono', 'like', "%{$key}%")->first();
      $name = null;
      if ($cli) {
          $name = trim(implode(' ', array_filter([$cli->nombre ?? null, $cli->apellido ?? null]))) ?: null;
      }
      return $cache[$key] = $name;
  };

  $formatMsisdn = function (?string $msisdn) {
      if (!$msisdn) return '';
      $d = preg_replace('/\D+/', '', $msisdn);
      if (Str::startsWith($d, '521'))      $d = '+52 ' . substr($d, 3);
      elseif (Str::startsWith($d, '52'))   $d = '+52 ' . substr($d, 2);
      return trim($d);
  };

  $getPeer = function ($row) {
      foreach (['from', 'msisdn', 'peer', 'telefono', 'phone'] as $field) {
          if (isset($row->$field) && $row->$field) return $row->$field;
      }
      return null;
  };

  $getLastAt = function ($row) {
      foreach (['last_at', 'last_ts', 'wa_timestamp'] as $field) {
          if (!empty($row->$field)) return \Carbon\Carbon::parse($row->$field);
      }
      return null;
  };

  $inboxPollUrl = \Illuminate\Support\Facades\Route::has('wa.inbox.fetch')
      ? route('wa.inbox.fetch')
      : url('/whatsapp/inbox/fetch');

  // Acciones rápidas (opcionales)
  $claimUrlTpl = \Illuminate\Support\Facades\Route::has('wa.claim')
      ? route('wa.claim', 'PEER_PLACEHOLDER')
      : null;

  $releaseUrlTpl = \Illuminate\Support\Facades\Route::has('wa.release')
      ? route('wa.release', 'PEER_PLACEHOLDER')
      : null;
@endphp

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
  :root{
    --bg:#f6f7fb; --card:#ffffff; --text:#0f172a; --muted:#64748b; --line:#e7edf5; --hover:#f8fafc;
    --brand:#16a34a; --brand2:#22c55e;
    --chip:#f1f5f9; --chip-b:#e2e8f0;

    --badge:#ecfdf5; --badge-b:#a7f3d0; --badge-t:#065f46;
    --badge2:#eff6ff; --badge2-b:#bfdbfe; --badge2-t:#1d4ed8;

    --shadow: 0 14px 45px rgba(2, 6, 23, .08);
    --radius: 18px;
  }
  body { background: var(--bg); }

  .wa-shell{ max-width: 1140px; margin: 24px auto; padding: 0 14px; margin-top: 86px; }
  .wa-card{ background: var(--card); border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); }

  .wa-top{
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    padding: 14px 16px; border-bottom: 1px solid var(--line); background: #fff;
  }

  .wa-title{ display:flex; align-items:center; gap:10px; min-width: 0; }
  .wa-title .icon{
    width: 38px; height: 38px; display:grid; place-items:center; border-radius: 12px;
    background: rgba(34,197,94,.12); color: var(--brand); flex: 0 0 auto;
  }
  .wa-title h2{ margin: 0; font-size: 1rem; font-weight: 800; color: var(--text); line-height: 1.1; }
  .wa-title p{ margin: 2px 0 0; font-size: .85rem; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 520px; }

  .wa-actions{ display:flex; align-items:center; justify-content:flex-end; gap:10px; flex: 0 0 auto; }

  .chip{
    display:inline-flex; align-items:center; gap:8px;
    background: var(--chip); border: 1px solid var(--chip-b); border-radius: 999px;
    padding: 6px 10px; font-weight: 700; font-size: .82rem; color:#334155; user-select:none; white-space: nowrap;
  }
  .chip .dot{ width: 8px; height: 8px; border-radius: 50%; background:#a1a1aa; }
  .chip.ok .dot{ background:#22c55e; }
  .chip.warn .dot{ background:#f59e0b; }

  .search{
    display:flex; align-items:center; gap:8px; border: 1px solid var(--line); background:#fff; border-radius: 12px;
    padding: 8px 10px; min-width: 320px;
  }
  .search i{ color:#94a3b8; font-size: 1rem; }
  .search input{ border:none; outline:none; width:100%; font-size: .95rem; background: transparent; color: var(--text); }

  .icon-btn{
    border: 1px solid var(--line); background:#fff; border-radius: 12px; width: 40px; height: 40px;
    display:grid; place-items:center; color:#475569; transition: background .12s ease, transform .12s ease;
  }
  .icon-btn:hover{ background: var(--hover); }
  .icon-btn:active{ transform: translateY(1px); }

  .new-indicator{
    position: sticky; top: 0; z-index: 5; display:none; justify-content:center; padding: 8px 0;
    background: rgba(255,255,255,.92); backdrop-filter: blur(6px); border-bottom: 1px solid var(--line);
  }
  .new-chip{
    display:inline-flex; align-items:center; gap:8px; border-radius: 999px; padding: 8px 12px;
    border: 1px solid var(--chip-b); background: var(--chip); color:#0f172a; font-weight: 800; cursor:pointer;
  }

  .list{ display:block; }

  .row{
    display:flex; align-items:center; gap: 12px; padding: 12px 14px; border-bottom: 1px solid var(--line);
    text-decoration:none; color:inherit; background:#fff; transition: background .12s ease;
    position: relative;
  }
  .row:hover{ background: var(--hover); }

  .row.unread .name{ font-weight: 900; }
  .row.unread .preview{ color: var(--text); font-weight: 700; }

  .row--new{ animation: fadeIn .16s ease both; }
  @keyframes fadeIn{ from{ opacity: 0; transform: translateY(2px); } to{ opacity: 1; transform: none; } }
  .list.no-anim .row{ transition:none !important; animation:none !important; }

  .avatar{
    width: 48px; height: 48px; border-radius: 14px; flex: 0 0 auto; overflow:hidden;
    background: linear-gradient(135deg, var(--brand), var(--brand2));
    box-shadow: 0 10px 22px rgba(34,197,94,.16);
    display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800;
  }
  .avatar img{ width:100%; height:100%; object-fit:cover; }

  .info{ flex:1; min-width:0; }
  .name-line{ display:flex; align-items:center; gap: 8px; min-width:0; }
  .name{
    font-weight: 800; color: #0b1220; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    min-width:0; max-width: 560px;
  }

  .badge{
    font-size: .72rem; background: var(--badge); color: var(--badge-t); border: 1px solid var(--badge-b);
    border-radius: 999px; padding: 2px 8px; font-weight: 800; white-space: nowrap;
    display:inline-flex; align-items:center; gap:6px;
  }
  .badge.auto{
    background: var(--badge2); color: var(--badge2-t); border-color: var(--badge2-b);
  }

  .preview{
    font-size: .9rem; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-top: 2px;
  }

  .meta{ margin-left:auto; text-align:right; flex: 0 0 auto; min-width: 170px; }
  .time{ font-size: .82rem; color: #94a3b8; font-weight: 700; }
  .agent{
    margin-top: 4px; font-size: .8rem; color: #94a3b8; display:flex; align-items:center; justify-content:flex-end;
    gap:6px; white-space: nowrap;
  }

  /* Acciones rápidas en hover (Tomar / Liberar) */
  .row-actions{
    position:absolute; right: 14px; top: 50%; transform: translateY(-50%);
    display:none; gap:8px;
  }
  .row:hover .row-actions{ display:flex; }

  .mini-btn{
    border: 1px solid var(--line);
    background:#fff;
    border-radius: 12px;
    padding: 8px 10px;
    font-weight: 900;
    font-size: .82rem;
    color:#0f172a;
    display:inline-flex;
    align-items:center;
    gap:6px;
    cursor:pointer;
  }
  .mini-btn:active{ transform: translateY(1px); }
  .mini-btn.take{ border-color: rgba(245,158,11,.35); color:#92400e; }
  .mini-btn.release{ border-color: rgba(34,197,94,.30); color:#065f46; }

  .empty{ padding: 28px 16px; color: var(--muted); text-align:center; }

  @media (max-width: 992px){
    .search{ min-width: 260px; }
    .name{ max-width: 420px; }
    .meta{ min-width: 140px; }
    .row-actions{ display:none !important; } /* en tablet/móvil no estorba */
  }

  @media (max-width: 768px){
    .wa-shell{ margin: 0; padding: 0; margin-top: 72px; }
    .wa-card{ border-radius: 0; border-left: 0; border-right: 0; }

    .wa-top{ flex-direction: column; align-items: stretch; gap: 10px; }
    .wa-actions{ width: 100%; justify-content: space-between; gap: 10px; }
    .search{ flex: 1; min-width: 0; }

    .meta{ min-width: 92px; }
    .agent{ display:none; }
    .name{ max-width: 60vw; }
  }
</style>

<div class="wa-shell">
  <div class="wa-card" style="margin-top:-50px;">

    <div class="wa-top">
      <div class="wa-title">
        <div class="icon"><i class="bi bi-chat-dots"></i></div>
        <div style="min-width:0">
          <h2>Bandeja de WhatsApp</h2>
          <p>Busca por nombre, número o contenido del último mensaje.</p>
        </div>
      </div>

      <div class="wa-actions">
        @isset($inboxAvailability)
          <span class="chip {{ $inboxAvailability ? 'ok' : 'warn' }}">
            <span class="dot"></span>
            {{ $inboxAvailability ? 'En línea' : 'Fuera de línea' }}
          </span>
        @endisset

        <div class="search">
          <i class="bi bi-search"></i>
          <input id="search" type="text" placeholder="Buscar…">
        </div>

        <button class="icon-btn" type="button" id="btn-refresh" title="Actualizar">
          <i class="bi bi-arrow-clockwise"></i>
        </button>
      </div>
    </div>

    <div id="new-indicator" class="new-indicator">
      <div id="new-chip" class="new-chip">
        <i class="bi bi-arrow-up"></i>
        <span>Ver <b id="new-count">0</b> nuevo(s)</span>
      </div>
    </div>

    <div id="list" class="list">
      @forelse($threads as $t)
        @php
          $peer = $getPeer($t);
        @endphp

        @if(!$peer)
          @continue
        @endif

        @php
          $display   = $getDisplayName($peer) ?? $formatMsisdn($peer);
          $avatarUrl = "https://ui-avatars.com/api/?name=".urlencode($display)."&background=16a34a&color=fff&rounded=true&size=64";

          $lastAt = $getLastAt($t);
          $iso    = $lastAt ? $lastAt->utc()->toIso8601String() : null;

          $lastText = $t->last_in_text ?? $t->last_text ?? $t->preview ?? '—';
          $unread   = (int) ($t->unread_count ?? 0);
          $agent    = $t->agent_name ?? null;
          $handover = !empty($t->handover);

          $peerKey = preg_replace('/\D+/', '', $peer);
        @endphp

        <a class="row {{ $unread > 0 ? 'unread' : '' }}"
           href="{{ route('wa.chat', $peer) }}"
           data-peer="{{ $peerKey }}"
           data-ts="{{ $iso }}"
           data-display="{{ e($display) }}"
           data-name="{{ Str::lower($display) }}"
           data-number="{{ Str::lower($formatMsisdn($peer)) }}"
           data-preview="{{ Str::lower($lastText) }}"
           data-handover="{{ $handover ? '1' : '0' }}"
           data-agent="{{ e($agent ?? '') }}">

          <div class="avatar">
            <img src="{{ $avatarUrl }}" alt="{{ $display }}">
          </div>

          <div class="info">
            <div class="name-line">
              <div class="name">{{ $display }}</div>

              @if($unread > 0)
                <span class="badge" data-badge="unread">
                  <i class="bi bi-envelope"></i>
                  {{ $unread }} nuevo{{ $unread > 1 ? 's' : '' }}
                </span>
              @endif

              {{-- ✅ Siempre mostrar modo --}}
              @if($handover)
                <span class="badge" data-badge="handover">
                  <i class="bi bi-person-check"></i> En atención
                </span>
              @else
                <span class="badge auto" data-badge="auto">
                  <i class="bi bi-robot"></i> Automático
                </span>
              @endif
            </div>

            <div class="preview" data-role="preview">{{ \Illuminate\Support\Str::limit($lastText, 110) }}</div>
          </div>

          <div class="meta">
            <div class="time" data-ts="{{ $iso }}"></div>
            @if($agent)
              <div class="agent" data-role="agent">
                <i class="bi bi-person"></i> <span>{{ $agent }}</span>
              </div>
            @else
              <div class="agent" data-role="agent" style="display:none;"></div>
            @endif
          </div>

          {{-- ✅ Acciones rápidas (opcional) --}}
          <div class="row-actions" aria-hidden="true">
            <button class="mini-btn take" type="button" data-action="take" title="Tomar conversación">
              <i class="bi bi-person-gear"></i> Tomar
            </button>
            <button class="mini-btn release" type="button" data-action="release" title="Liberar a IA">
              <i class="bi bi-robot"></i> Liberar
            </button>
          </div>
        </a>
      @empty
        <div class="empty">Aún no hay conversaciones.</div>
      @endforelse
    </div>

  </div>
</div>

@php $POLL_URL = $inboxPollUrl ?: null; @endphp

<script>
  /* =================== Búsqueda local =================== */
  const q = document.getElementById('search');
  const list = document.getElementById('list');

  q?.addEventListener('input', () => {
    const v = q.value.trim().toLowerCase();
    list.querySelectorAll('.row').forEach(row => {
      const hay = row.dataset.name.includes(v) ||
                  row.dataset.number.includes(v) ||
                  row.dataset.preview.includes(v);
      row.style.display = hay ? 'flex' : 'none';
    });
  });

  /* =================== Tiempo MX =================== */
  const MX_TZ  = 'America/Mexico_City';
  const LOCALE = 'es-MX';

  function sameDayMX(d1, d2){
    const fmt = (d) => new Intl.DateTimeFormat('en-CA', { timeZone: MX_TZ, year:'numeric', month:'2-digit', day:'2-digit' }).format(d);
    return fmt(d1) === fmt(d2);
  }

  function renderTimes(){
    const now = new Date();
    document.querySelectorAll('.time[data-ts]').forEach(el => {
      const iso = el.dataset.ts;
      if (!iso) { el.textContent=''; return; }
      const d = new Date(iso);
      if (Number.isNaN(d.getTime())) { el.textContent = ''; return; }
      if (sameDayMX(d, now)){
        el.textContent = new Intl.DateTimeFormat(LOCALE,{
          hour:'2-digit', minute:'2-digit', hour12:false, timeZone: MX_TZ
        }).format(d);
      } else {
        el.textContent = new Intl.DateTimeFormat(LOCALE,{
          day:'2-digit', month:'2-digit', year:'2-digit', timeZone: MX_TZ
        }).format(d);
      }
    });
  }
  renderTimes();
  setInterval(renderTimes, 60000);

  /* =================== Polling bandeja =================== */
  const POLL_URL = @json($POLL_URL);
  const RELOAD_FALLBACK = !POLL_URL;

  const newIndicator = document.getElementById('new-indicator');
  const newChip = document.getElementById('new-chip');
  const newCountEl = document.getElementById('new-count');
  let pending = new Map();

  let pinnedToTop = true;
  const isNearTop=(t=60)=> (window.scrollY || document.documentElement.scrollTop || 0) <= t;
  const onScroll=()=>{
    pinnedToTop = isNearTop();
    if (pinnedToTop && pending.size) flushPending();
  };
  window.addEventListener('scroll', onScroll, {passive:true});
  pinnedToTop = isNearTop();

  function hideEmptyPlaceholder(){
    const el = document.querySelector('#list .empty');
    if (el) el.remove();
  }

  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }
  function previewLimit(s, n=110){ s = s || '—'; return s.length>n ? s.slice(0,n-1)+'…' : s; }
  function avatarUrlByName(name){
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=16a34a&color=fff&rounded=true&size=64`;
  }
  function rowSelector(peerDigits){ return `.row[data-peer="${peerDigits}"]`; }

  // ✅ Más robusto que route() con placeholder
  const chatUrlTpl = @json(url('/whatsapp/chat') . '/PEER_PLACEHOLDER');

  function formatMsisdnClient(msisdn){
    const d = (msisdn||'').replace(/\D+/g,'');
    if (!d) return '';
    if (d.startsWith('521')) return '+52 ' + d.slice(3);
    if (d.startsWith('52'))  return '+52 ' + d.slice(2);
    return '+' + d;
  }

  function buildRowHtml(t, withIntroAnim=false){
    const peerDigits = (t.peer||'').replace(/\D+/g,'');
    const display    = t.display_name || formatMsisdnClient(t.peer);
    const iso        = t.last_at || '';
    const unread     = Number(t.unread_count || 0);
    const handover   = !!t.handover;
    const agent      = t.agent_name || '';
    const lastText   = t.last_text || '—';
    const preview    = previewLimit(lastText);

    const href = chatUrlTpl.replace('PEER_PLACEHOLDER', encodeURIComponent(t.peer || ''));

    const modeBadge = handover
      ? `<span class="badge" data-badge="handover"><i class="bi bi-person-check"></i> En atención</span>`
      : `<span class="badge auto" data-badge="auto"><i class="bi bi-robot"></i> Automático</span>`;

    const agentHtml = agent
      ? `<div class="agent" data-role="agent"><i class="bi bi-person"></i> <span>${escapeHtml(agent)}</span></div>`
      : `<div class="agent" data-role="agent" style="display:none;"></div>`;

    return `
      <a class="row ${withIntroAnim?'row--new':''} ${unread>0?'unread':''}"
         href="${href}"
         data-peer="${peerDigits}"
         data-ts="${iso}"
         data-display="${escapeHtml(display)}"
         data-name="${escapeHtml(display.toLowerCase())}"
         data-number="${escapeHtml(formatMsisdnClient(t.peer).toLowerCase())}"
         data-preview="${escapeHtml((lastText||'').toLowerCase())}"
         data-handover="${handover?'1':'0'}"
         data-agent="${escapeHtml(agent)}">

        <div class="avatar">
          <img src="${avatarUrlByName(display)}" alt="${escapeHtml(display)}">
        </div>

        <div class="info">
          <div class="name-line">
            <div class="name">${escapeHtml(display)}</div>
            ${unread>0?`<span class="badge" data-badge="unread"><i class="bi bi-envelope"></i> ${unread} nuevo${unread>1?'s':''}</span>`:''}
            ${modeBadge}
          </div>
          <div class="preview" data-role="preview">${escapeHtml(preview)}</div>
        </div>

        <div class="meta">
          <div class="time" data-ts="${iso}"></div>
          ${agentHtml}
        </div>

        <div class="row-actions" aria-hidden="true">
          <button class="mini-btn take" type="button" data-action="take" title="Tomar conversación">
            <i class="bi bi-person-gear"></i> Tomar
          </button>
          <button class="mini-btn release" type="button" data-action="release" title="Liberar a IA">
            <i class="bi bi-robot"></i> Liberar
          </button>
        </div>
      </a>
    `;
  }

  function updateRowContent(row, t){
    const display  = t.display_name || row.dataset.display || formatMsisdnClient(t.peer);
    const iso      = t.last_at || '';
    const unread   = Number(t.unread_count || 0);
    const handover = !!t.handover;
    const agent    = t.agent_name || '';
    const lastText = t.last_text || '—';
    const preview  = previewLimit(lastText);

    row.dataset.ts = iso;
    row.dataset.display = display;
    row.dataset.name = display.toLowerCase();
    row.dataset.number = formatMsisdnClient(t.peer).toLowerCase();
    row.dataset.preview = (lastText||'').toLowerCase();
    row.dataset.handover = handover ? '1' : '0';
    row.dataset.agent = agent;

    row.classList.toggle('unread', unread>0);

    // badge unread
    const bUnread = row.querySelector('.badge[data-badge="unread"]');
    if (unread>0){
      if (bUnread) bUnread.innerHTML = `<i class="bi bi-envelope"></i> ${unread} nuevo${unread>1?'s':''}`;
      else row.querySelector('.name-line').insertAdjacentHTML('beforeend', `<span class="badge" data-badge="unread"><i class="bi bi-envelope"></i> ${unread} nuevo${unread>1?'s':''}</span>`);
    } else if (bUnread){ bUnread.remove(); }

    // modo
    const bH = row.querySelector('.badge[data-badge="handover"]');
    const bA = row.querySelector('.badge[data-badge="auto"]');
    if (handover){
      if (bA) bA.remove();
      if (!bH) row.querySelector('.name-line').insertAdjacentHTML('beforeend', `<span class="badge" data-badge="handover"><i class="bi bi-person-check"></i> En atención</span>`);
    } else {
      if (bH) bH.remove();
      if (!bA) row.querySelector('.name-line').insertAdjacentHTML('beforeend', `<span class="badge auto" data-badge="auto"><i class="bi bi-robot"></i> Automático</span>`);
    }

    const prevEl = row.querySelector('[data-role="preview"]');
    if (prevEl) prevEl.textContent = preview;

    const agentEl = row.querySelector('[data-role="agent"]');
    if (agentEl){
      if (agent){
        agentEl.style.display='';
        agentEl.innerHTML = `<i class="bi bi-person"></i> <span>${escapeHtml(agent)}</span>`;
      } else {
        agentEl.style.display='none';
        agentEl.innerHTML = '';
      }
    }

    const timeEl = row.querySelector('.time');
    if (timeEl) timeEl.dataset.ts = iso;
  }

  function upsertThread(t){
    const peerDigits = (t.peer||'').replace(/\D+/g,'');
    if (!peerDigits) return;

    let row = document.querySelector(rowSelector(peerDigits));

    if (!row){
      hideEmptyPlaceholder();

      if (!pinnedToTop){
        pending.set(peerDigits, t);
        newCountEl.textContent = pending.size;
        newIndicator.style.display = 'flex';
        return;
      }

      list.insertAdjacentHTML('afterbegin', buildRowHtml(t, true));
      requestAnimationFrame(()=>{ renderTimes(); });
      return;
    }

    updateRowContent(row, t);
    if (pinnedToTop) reorderListByTs();
  }

  function flushPending(){
    if (!pending.size) return;

    hideEmptyPlaceholder();
    list.classList.add('no-anim');

    const items = Array.from(pending.values()).sort((a,b)=> new Date(b.last_at||0) - new Date(a.last_at||0));
    pending.clear();
    newIndicator.style.display = 'none';
    newCountEl.textContent = '0';

    const prevY = window.scrollY || document.documentElement.scrollTop || 0;

    items.forEach(t=>{
      const peerDigits = (t.peer||'').replace(/\D+/g,'');
      const sel = rowSelector(peerDigits);
      const existing = document.querySelector(sel);
      if (!existing){
        list.insertAdjacentHTML('afterbegin', buildRowHtml(t, true));
      } else {
        updateRowContent(existing, t);
      }
    });

    reorderListByTs();
    window.scrollTo({top: prevY});
    requestAnimationFrame(()=>{ list.classList.remove('no-anim'); renderTimes(); });
  }

  newChip?.addEventListener('click', flushPending);

  function reorderListByTs(){
    if (!pinnedToTop) return;
    const rows = Array.from(list.querySelectorAll('.row[data-ts]'));
    rows.sort((a,b)=>{
      const ta = new Date(a.dataset.ts || 0).getTime() || 0;
      const tb = new Date(b.dataset.ts || 0).getTime() || 0;
      return tb - ta;
    });
    list.classList.add('no-anim');
    const prevY = window.scrollY || document.documentElement.scrollTop || 0;
    rows.forEach(r => list.appendChild(r));
    window.scrollTo({top: prevY});
    requestAnimationFrame(()=> list.classList.remove('no-anim'));
  }

  function maxIsoFromDom(){
    let max = 0;
    list.querySelectorAll('.row[data-ts]').forEach(r=>{
      const t = new Date(r.dataset.ts || 0).getTime();
      if (!isNaN(t) && t>max) max = t;
    });
    return max ? new Date(max).toISOString() : '';
  }

  // ============ Polling ============
  let etag = null;

  async function poll(){
    if (RELOAD_FALLBACK) return;

    try{
      const since = maxIsoFromDom();
      const headers = {'Accept':'application/json'};
      if (etag) headers['If-None-Match'] = etag;

      const res = await fetch(`${POLL_URL}?since=${encodeURIComponent(since)}`, {
        headers, cache: 'no-store', credentials: 'same-origin'
      });

      if (res.status === 304) return;
      if (!res.ok) return;

      etag = res.headers.get('ETag') || etag;
      const data = await res.json();

      if (Array.isArray(data)){
        if (data.length) hideEmptyPlaceholder();
        const prevY = window.scrollY || document.documentElement.scrollTop || 0;
        data.forEach(upsertThread);
        if (pinnedToTop) renderTimes();
        window.scrollTo({top: prevY});
      }
    }catch(err){
      console.error('poll exception', err);
    }
  }

  let pollingTimer = null;
  function startPolling(){
    if (pollingTimer) clearInterval(pollingTimer);
    poll();
    pollingTimer = setInterval(()=>{ if (!document.hidden) poll(); }, 2500);
  }

  document.addEventListener('visibilitychange', ()=>{ if (!document.hidden) poll(); });
  document.getElementById('btn-refresh')?.addEventListener('click', ()=> poll());
  startPolling();

  /* =================== Acciones rápidas Tomar/Liberar (OPCIONAL) =================== */
  const CLAIM_URL_TPL   = @json($claimUrlTpl);
  const RELEASE_URL_TPL = @json($releaseUrlTpl);

  function csrf(){ return document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name=_token]')?.value || ''; }
  async function postJson(url, body={}){
    const res = await fetch(url, {
      method:'POST',
      headers:{
        'X-Requested-With':'XMLHttpRequest',
        'Accept':'application/json',
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': csrf()
      },
      body: JSON.stringify(body),
      cache:'no-store',
      credentials:'same-origin'
    });
    let json = {};
    try{ json = await res.json(); }catch(e){}
    if (!res.ok) throw {status:res.status, json};
    return json;
  }

  function replaceTpl(tpl, peerRaw){
    if (!tpl) return null;
    return tpl.replace('PEER_PLACEHOLDER', encodeURIComponent(peerRaw));
  }

  list.addEventListener('click', async (e)=>{
    const btn = e.target.closest('button[data-action]');
    if (!btn) return;

    const row = btn.closest('.row');
    if (!row) return;

    e.preventDefault();
    e.stopPropagation();

    const action = btn.dataset.action;
    const peerDigits = row.dataset.peer;
    const peerRaw = (row.getAttribute('href') || '').split('/').pop(); // fallback
    // Si quieres exactitud total: agrega data-peer-raw en HTML. Por simplicidad usamos href.

    if (action === 'take'){
      const url = replaceTpl(CLAIM_URL_TPL, decodeURIComponent(peerRaw||''));
      if (!url) return console.warn('No CLAIM route');
      btn.disabled = true;
      try{
        const r = await postJson(url, {});
        row.dataset.handover = '1';
        row.dataset.agent = (r.agent && r.agent.name) ? r.agent.name : (row.dataset.agent||'');
        // refresca la UI con un “mini thread”
        updateRowContent(row, {
          peer: decodeURIComponent(peerRaw||''),
          display_name: row.dataset.display,
          last_at: row.dataset.ts,
          unread_count: 0,
          handover: true,
          agent_name: row.dataset.agent,
          last_text: row.querySelector('[data-role="preview"]')?.textContent || '—'
        });
      }catch(err){
        console.error(err);
      }finally{
        btn.disabled = false;
      }
    }

    if (action === 'release'){
      const url = replaceTpl(RELEASE_URL_TPL, decodeURIComponent(peerRaw||''));
      if (!url) return console.warn('No RELEASE route');
      btn.disabled = true;
      try{
        await postJson(url, {});
        row.dataset.handover = '0';
        row.dataset.agent = '';
        updateRowContent(row, {
          peer: decodeURIComponent(peerRaw||''),
          display_name: row.dataset.display,
          last_at: row.dataset.ts,
          unread_count: 0,
          handover: false,
          agent_name: '',
          last_text: row.querySelector('[data-role="preview"]')?.textContent || '—'
        });
      }catch(err){
        console.error(err);
      }finally{
        btn.disabled = false;
      }
    }
  });
</script>
@endsection
