@extends('layouts.app')

@section('title', 'WhatsApp')
@section('titulo', 'WhatsApp')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

@php
  $waMediaUrlTpl = \Illuminate\Support\Facades\Route::has('wa.media')
      ? route('wa.media', 'MEDIA_ID_PLACEHOLDER')
      : null;

  // === Acciones (fallbacks) ===
  $sendUrl = \Illuminate\Support\Facades\Route::has('wa.chat.send')
      ? route('wa.chat.send', $currentUser)
      : (\Illuminate\Support\Facades\Route::has('wa.send') ? route('wa.send', $currentUser) : url("/whatsapp/inbox/{$currentUser}/send"));

  $closeUrl = \Illuminate\Support\Facades\Route::has('wa.close')
      ? route('wa.close', $currentUser)
      : (\Illuminate\Support\Facades\Route::has('wa.chat.close') ? route('wa.chat.close', $currentUser) : null);

  $claimUrl = \Illuminate\Support\Facades\Route::has('wa.claim')
      ? route('wa.claim', $currentUser)
      : url("/whatsapp/inbox/{$currentUser}/claim");

  $releaseUrl = \Illuminate\Support\Facades\Route::has('wa.release')
      ? route('wa.release', $currentUser)
      : url("/whatsapp/inbox/{$currentUser}/release");

  $aiSuggestUrl = \Illuminate\Support\Facades\Route::has('wa.ai.suggest')
      ? route('wa.ai.suggest', $currentUser)
      : url("/whatsapp/inbox/{$currentUser}/ai-suggest");

  $aiSendUrl = \Illuminate\Support\Facades\Route::has('wa.ai.send')
      ? route('wa.ai.send', $currentUser)
      : url("/whatsapp/inbox/{$currentUser}/ai-send");

  // Endpoint fetch (tu código ya lo usaba así)
  $fetchUrlTpl = url('/whatsapp/chat') . '/USER_PLACEHOLDER/fetch';
@endphp

<style>
  :root{
    --bg:#f6f7fb;
    --card:#ffffff;
    --line:#e7edf5;
    --text:#0f172a;
    --muted:#64748b;

    --out:#22c55e;
    --out2:#16a34a;

    --in:#ffffff;
    --in-border:#e5e7eb;

    --shadow: 0 14px 45px rgba(2, 6, 23, .08);
    --radius: 18px;

    --warn:#f59e0b;
    --danger:#ef4444;
    --info:#0ea5e9;
  }

  body{ background: var(--bg); }

  .chat-wrap{
    max-width: 1800px;
    width:100%;
    margin:10px auto;
    padding:0 16px;
}

  .chat-convo{
    display:flex;
    flex-direction:column;

    height:calc(100vh - 120px);

    background:#fff;

    border:none;

    border-radius:28px;

    overflow:hidden;

    box-shadow:
      0 25px 60px rgba(15,23,42,.08);
}

  .chat-header{
    display:flex;
    align-items:center;
    justify-content:space-between;

    gap:18px;

    padding:20px 24px;

    background:#ffffff;

    border-bottom:1px solid #edf2f7;
}

  .chat-header .left{
    display:flex;
    gap: 12px;
    align-items:center;
    min-width: 0;
  }

  .avatar{
    width: 60px;
    height: 60px;
    border-radius: 14px;
    overflow:hidden;
    flex: 0 0 auto;
    box-shadow: 0 10px 20px rgba(34,197,94,.14);
    background: linear-gradient(135deg, var(--out), var(--out2));
    display:grid;
    place-items:center;
    border:3px solid #fff;
  }
  .avatar img{ width:100%; height:100%; object-fit:cover; display:block; }

  .chat-title{ min-width:0; }
  .chat-title .title{
    font-weight: 900;
    color: var(--text);
    display:flex;
    align-items:center;
    gap: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 68vw;
  }

  .chat-title .sub{
    margin-top: 2px;
    font-size: .85rem;
    color: var(--muted);
    display:flex;
    align-items:center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .badge{
    font-size: .72rem;
    padding: 4px 10px;
    border-radius: 999px;
    background: #f1f5f9;
    color: #334155;
    border: 1px solid #e2e8f0;
    font-weight: 800;
    display:inline-flex;
    align-items:center;
    gap: 6px;
  }

  /* ===== Right actions ===== */
  .actions{
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap: wrap;
    justify-content:flex-end;
  }
  .action-group{
    display:flex;
    align-items:center;
    gap:10px;
}

.btn-info{
    border:1px solid #93c5fd;
    background:#fff;
    color:#1d4ed8;
}

.btn-info:hover{
    background:#eff6ff;
}

  .btn{
    border: 1px solid var(--line);
    background:#fff;
    color:#0f172a;
    border-radius: 12px;
    padding: 10px 12px;
    cursor:pointer;
    font-weight: 900;
    display:inline-flex;
    align-items:center;
    gap: 8px;
    transition: background .12s ease, transform .12s ease, border-color .12s ease;
    white-space: nowrap;
  }
  .btn:hover{ background:#f8fafc; }
  .btn:active{ transform: translateY(1px); }
  .btn:disabled{opacity:.55;cursor:not-allowed}

  .btn-primary{
    border-color: rgba(34,197,94,.25);
    box-shadow: 0 10px 22px rgba(34,197,94,.12);
  }
  .btn-danger{
    border-color: rgba(239,68,68,.35);
    color:#991b1b;
    background:#fff;
  }
  .btn-danger:hover{ background:#fff5f5; }

  .btn-warn{
    border-color: rgba(245,158,11,.35);
    color:#92400e;
  }

  .btn-info{
    border-color: rgba(14,165,233,.35);
    color:#075985;
  }

  .divider{
    width:1px;height:34px;background:var(--line);
    margin: 0 4px;
  }

  /* ===== Body ===== */
  .chat-body{
    position:relative;

    flex:1;

    overflow-y:auto;

    padding:24px;

    display:flex;

    flex-direction:column;

    gap:14px;

    background:
      radial-gradient(circle at center,
      rgba(0,0,0,.015) 1px,
      transparent 1px);

    background-size:24px 24px;

    background-color:#f5f7fb;
}

  .day-sep{
    align-self:center;
    font-size: .78rem;
    color: #64748b;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    padding: 6px 10px;
    margin: 6px 0;
    font-weight: 900;
  }

  .group{
    display:flex;
    flex-direction:column;
    width:100%;
    max-width:100%;
}

  .msg{
    position:relative;

    padding:14px 18px;

    border-radius:18px;

    font-size:.96rem;

    line-height:1.6;

    word-wrap:break-word;

    box-shadow:
      0 6px 16px rgba(0,0,0,.04);

    animation:fadeIn .16s ease;
}

  .msg.in{
    background:#fff;
    border:none;
    border-bottom-left-radius:6px;

    max-width:65%;

    width:fit-content;

    align-self:flex-start;

    box-shadow:0 4px 12px rgba(0,0,0,.05);
}

  .msg.out{
    background:linear-gradient(
        135deg,
        #25d366,
        #16a34a
    );

    color:#fff;

    max-width:65%;

    width:fit-content;

    margin-left:auto;

    align-self:flex-end;

    border-bottom-right-radius:6px;
}

  .msg img{
    max-width: 520px;
    width: 100%;
    border-radius: 12px;
    display:block;
  }

  .msg.in a{ color: var(--text); text-decoration: underline; }
  .msg.out a{ color:#fff; text-decoration: underline; }

  .meta{
    display:flex;
    align-items:center;
    gap: 8px;
    margin-top: 5px;
    font-size: .76rem;
    color: var(--muted);
    font-weight: 700;
  }
  .meta.right{
    justify-content:flex-end;
    width:65%;
    margin-left:auto;
}

  .ticks{ font-size: .92em; opacity: .95; }
  .ticks.read{ color:#34b7f1 }

  /* ===== AI panel ===== */
  .ai-panel{
    display:none;
    border-top: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
    background: #fff;
    padding: 12px 14px;
  }

  .ai-card{
    border: 1px solid #e2e8f0;
    background: linear-gradient(180deg,#ffffff,#f8fafc);
    border-radius: 16px;
    padding: 12px;
    box-shadow: 0 10px 30px rgba(2, 6, 23, .06);
  }

  .ai-head{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin-bottom: 8px;
  }
  .ai-title{
    font-weight: 1000;
    color: var(--text);
    display:flex;
    align-items:center;
    gap: 8px;
  }
  .ai-meta{
    font-size:.82rem;
    color: var(--muted);
    font-weight: 800;
  }

  .ai-reason{
    display:none;
    margin-top: 8px;
    font-size:.86rem;
    font-weight: 900;
    color:#7c2d12;
    background:#fff7ed;
    border:1px solid #fed7aa;
    border-radius: 14px;
    padding: 10px 12px;
  }

  .ai-text{
    width:100%;
    min-height: 92px;
    resize: vertical;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 10px 12px;
    font-size: 0.98rem;
    outline:none;
    color: var(--text);
    background:#fff;
  }

  .ai-actions{
    display:flex;
    gap:10px;
    flex-wrap: wrap;
    margin-top: 10px;
    justify-content:flex-end;
  }

  /* ===== Adjuntos ===== */
  .attach-bar{
    display:none;
    align-items:center;
    gap:10px;
    padding: 10px 14px;
    border-top: 1px solid var(--line);
    border-bottom: 1px solid var(--line);
    background: #ffffff;
  }

  .attach-chip{
    display:flex;
    align-items:center;
    gap:10px;
    background:#f1f5f9;
    border:1px solid #e2e8f0;
    border-radius: 14px;
    padding: 10px 12px;
    flex:1;
    min-width: 0;
  }

  .attach-thumb{
    width:44px;height:44px;object-fit:cover;
    border-radius:10px;border:1px solid #e5e7eb;background:#fff;
    flex:0 0 auto;
  }

  .attach-name{
    font-size: .92rem;
    color:#0f172a;
    font-weight: 900;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 62vw;
  }
  .attach-size{ font-size: .78rem; color:#64748b; font-weight: 800; }

  .attach-remove{
    border:none;background:#ef4444;color:#fff;
    border-radius: 12px;padding:10px 12px;cursor:pointer;font-weight: 900;
    display:grid; place-items:center;
  }
  .attach-remove:active{ transform: translateY(1px); }

  /* ===== Typing ===== */
  .typing{
    position:sticky;
    bottom: 0;
    align-self:flex-start;
    margin-top:auto;
    display:none;
    align-items:center;
    gap: 8px;
    background:#f1f5f9;
    color:#334155;
    padding: 8px 10px;
    border-radius: 14px;
    font-size: .85rem;
    border: 1px solid #e2e8f0;
    font-weight: 900;
  }
  .typing i{
    width:6px;height:6px;background:#94a3b8;border-radius:50%;
    display:inline-block;animation:blink 1.2s infinite
  }
  .typing i:nth-child(2){animation-delay:.2s}
  .typing i:nth-child(3){animation-delay:.4s}

  /* ===== Composer ===== */
  .composer{
    display:flex;

    gap:12px;

    align-items:center;

    padding:18px 20px;

    background:#fff;

    border-top:1px solid #edf2f7;
}

  .field{
    flex:1;

    display:flex;

    align-items:center;

    gap:12px;

    border:none;

    border-radius:18px;

    padding:14px 16px;

    background:#f8fafc;

    box-shadow:
      inset 0 0 0 1px #e2e8f0;
}

  .field input[type=text]{
    flex:1;
    border:none;
    outline:none;
    font-size: 1rem;
    background: transparent;
    color: var(--text);
    min-width: 0;
  }

  .icon-btn{
    border: 1px solid var(--line);
    background:#fff;
    color:#475569;
    border-radius: 12px;
    width: 40px; height: 40px;
    display:grid; place-items:center;
    cursor:pointer;
    transition: background .12s ease, transform .12s ease;
    flex: 0 0 auto;
  }
  .icon-btn:hover{ background: #f8fafc; }
  .icon-btn:active{ transform: translateY(1px); }

  .send-btn{
    border:none;

    border-radius:16px;

    padding:14px 18px;

    cursor:pointer;

    color:#fff;

    font-weight:900;

    background:
      linear-gradient(
        135deg,
        #22c55e,
        #16a34a
      );

    box-shadow:
      0 12px 24px rgba(34,197,94,.25);
}
  .send-btn:disabled{opacity:.55;cursor:not-allowed}

  .sr-file{
    position:absolute; left:-9999px; top:-9999px;
    width:1px; height:1px; opacity:0; pointer-events:none;
  }

  /* Tiny toast */
  .toast{
    position: fixed;
    right: 18px;
    bottom: 18px;
    z-index: 9999;
    background: #0f172a;
    color: #fff;
    padding: 12px 14px;
    border-radius: 14px;
    box-shadow: 0 18px 55px rgba(2,6,23,.18);
    font-weight: 900;
    display:none;
    align-items:center;
    gap:10px;
    max-width: 420px;
  }
  .toast.show{ display:flex; }
  .toast .muted{ opacity:.85; font-weight:800; }

  @keyframes blink{0%,80%,100%{opacity:0}40%{opacity:1}}
  @keyframes fadeIn{from{opacity:0;transform:translateY(3px)}to{opacity:1;transform:none}}

  @media (max-width: 1200px){
    .chat-wrap{ max-width: 1100px; }
    .group{ max-width: 78%; }
    .msg img{ max-width: 420px; }
  }

  @media (max-width: 992px){
    .chat-wrap{ max-width: 980px; }
    .group{ max-width: 84%; }
    .msg img{ max-width: 360px; }
  }

  @media (max-width: 768px){
    .chat-wrap{ margin:0; padding:0; margin-top: 72px; }
    .chat-convo{
      height: 100dvh;
      border-radius:0;
      border-left:0; border-right:0;
    }
    .chat-title .title{ max-width: 62vw; }
    .group{ max-width: 92%; }
    .msg img{ max-width: 74vw; }
    .btn span.hide-sm{ display:none; }
    .divider{ display:none; }
  }
  .wa-layout{
    display:flex;
    height:calc(100vh - 100px);
    gap:0;
    border-radius:24px;
    overflow:hidden;
    box-shadow:0 20px 50px rgba(0,0,0,.08);
}
.wa-layout.conversations-only .chat-convo{
    display:none;
}

.wa-layout.conversations-only .chat-sidebar{
    flex:1;
    width:auto;
    max-width:none;
    border-right:none;
}

.chat-sidebar{
    width:340px;
    background:#fff;
    border-right:1px solid #dfe3e8;
    display:flex;
    flex-direction:column;
}
.chat-sidebar{
    flex-shrink:0;
}
.sidebar-top{
    padding:15px;
    border-bottom:1px solid #e5e7eb;
    background:#f8fafc;
}

.sidebar-title{
    display:flex;
    align-items:center;
    gap:10px;
    font-weight:700;
    margin-bottom:15px;
}

.sidebar-title i{
    color:#25d366;
    font-size:22px;
}

.search-box{
    display:flex;
    align-items:center;
    background:white;
    border:1px solid #dfe3e8;
    border-radius:8px;
    padding:8px 12px;
}

.search-box i{
    color:#64748b;
}

.search-box input{
    border:none;
    outline:none;
    width:100%;
    margin-left:8px;
}

.sidebar-header{
    padding:20px;
    border-bottom:1px solid #e5e7eb;
    font-weight:700;
}

.chat-list{
    flex:1;
    overflow-y:auto;
}

.chat-item{
    display:flex;
    gap:12px;
    padding:14px;
    text-decoration:none;
    color:#111827;
    border-bottom:1px solid #eef2f7;
    transition:.2s;
}
.chat-row{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.chat-time{
    font-size:12px;
    color:#64748b;
}

.chat-item:hover{
    background:#f8fafc;
}

.chat-item.active{
    background:#e8f5e9;
    border-left:4px solid #25d366;
}

.chat-avatar{
    width:50px;
    height:50px;
    border-radius:50%;
    background:#25d366;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
}

.chat-info{
    flex:1;
    min-width:0;
}

.chat-name{
    font-weight:700;
}

.chat-preview{
    font-size:13px;
    color:#64748b;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.chat-convo{
    flex:1;
    display:flex;
    flex-direction:column;
    background:white;
}
</style>

<div class="wa-layout" id="waLayout">

    <div class="chat-sidebar">

    <div class="sidebar-top">

        

        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text"
                   id="searchConversation"
                   placeholder="Buscar mensaje...">
        </div>

    </div>

    <div class="chat-list">

        @foreach($conversations as $conv)

            <a href="{{ route('wa.chat', $conv->wa_user) }}"
               class="chat-item {{ $currentUser == $conv->wa_user ? 'active' : '' }}">

                <div class="chat-avatar">
                    {{ strtoupper(substr($conv->display_name,0,2)) }}
                </div>

                <div class="chat-info">

                    <div class="chat-row">

                        <div class="chat-name">
                            {{ $conv->display_name }}
                        </div>

                        <div class="chat-time">
                            {{ \Carbon\Carbon::parse($conv->last_at)->format('d/m/y') }}
                        </div>

                    </div>

                    <div class="chat-preview">
                        {{ Str::limit($conv->last_message,50) }}
                    </div>

                </div>

            </a>

        @endforeach

    </div>

</div>

    <!-- Conversación -->
    
      <div id="chatConvo" class="chat-convo"
     data-user="{{ $currentUser }}"
     data-handover="0">

    <!-- Header -->
    <div class="chat-header">

        <div class="left">
            <div class="avatar">
                {{ strtoupper(substr($currentConversation->display_name ?? 'U',0,2)) }}
            </div>

            <div class="chat-title">
                <div class="title">
                    {{ $currentConversation->display_name ?? 'Usuario' }}
                </div>

                <div class="sub">
                    <span id="mode-badge" class="badge">
                        <i class="bi bi-robot"></i>
                        Automático
                    </span>
                </div>
            </div>
        </div>

        <div class="actions">

    <div class="action-group">

        <button id="btn-claim"
                type="button"
                class="btn btn-warn">
            <i class="bi bi-person-gear"></i>
            <span class="hide-sm">Tomar</span>
        </button>

        <button id="btn-release"
                type="button"
                class="btn btn-info">
            <i class="bi bi-robot"></i>
            <span class="hide-sm">Liberar</span>
        </button>

    </div>

    <div class="divider"></div>

    <div class="action-group">

        <button id="btn-ai-suggest"
                type="button"
                class="btn btn-info">
            <i class="bi bi-stars"></i>
            <span class="hide-sm">Sugerir IA</span>
        </button>

        <button id="btn-ai-send"
                type="button"
                class="btn btn-primary">
            <i class="bi bi-send-check"></i>
            <span class="hide-sm">Enviar IA</span>
        </button>

    </div>

    <div class="divider"></div>

    <button
    type="button"
    class="btn btn-danger"
    onclick="window.location.href='{{ route('wa.inbox') }}'">
    Cerrar chat
</button>

    @if(!empty($closeUrl))
    <button id="btn-close"
            type="button"
            class="btn btn-danger">
        <i class="bi bi-x-circle"></i>
        <span class="hide-sm">Cerrar y encuestar</span>
    </button>
    @endif

</div>

    </div>

    <!-- Panel de sugerencia IA -->
    <div id="ai-panel" class="ai-panel">
        <div class="ai-card">
            <div class="ai-head">
                <div class="ai-title"><i class="bi bi-stars"></i> Sugerencia IA</div>
                <div id="ai-meta" class="ai-meta"></div>
            </div>

            <textarea id="ai-text" class="ai-text" placeholder="La sugerencia de la IA aparecerá aquí..."></textarea>

            <div id="ai-reason" class="ai-reason"></div>

            <div class="ai-actions">
                <button type="button" id="btn-ai-handover" class="btn btn-warn">
                    <i class="bi bi-person-up"></i>
                    <span>Escalar a asesor</span>
                </button>
                <button type="button" id="btn-ai-clear" class="btn">
                    <i class="bi bi-x-lg"></i>
                    <span>Limpiar</span>
                </button>
                <button type="button" id="btn-ai-copy" class="btn">
                    <i class="bi bi-clipboard"></i>
                    <span>Copiar</span>
                </button>
                <button type="button" id="btn-ai-send-panel" class="btn btn-primary">
                    <i class="bi bi-send-check"></i>
                    <span>Enviar esta respuesta</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mensajes -->
    <div id="chat-body" class="chat-body">

        @foreach($messages as $msg)

            <div class="group"
                 data-id="{{ $msg->id }}"
                 data-ts="{{ $msg->wa_timestamp }}">

                <div class="msg {{ $msg->direction == 'in' ? 'in' : 'out' }}">
                    {{ $msg->text }}
                </div>

                <div class="meta {{ $msg->direction == 'out' ? 'right' : '' }}">
                    <span class="time"
                          data-ts="{{ $msg->wa_timestamp }}">
                    </span>
                </div>

            </div>

        @endforeach

        <!-- Indicador "escribiendo" -->
        <div id="typing" class="typing">
            <span>Escribiendo</span>
            <i></i><i></i><i></i>
        </div>

    </div>

    <!-- Barra de adjunto seleccionado -->
    <div id="attach-bar" class="attach-bar">
        <div id="attach-chip" class="attach-chip"></div>
        <button type="button" id="attach-remove" class="attach-remove">
            <i class="bi bi-trash"></i>
        </button>
    </div>

    <!-- Formulario -->
    <form id="chat-form"
          action="{{ $sendUrl }}"
          method="POST"
          enctype="multipart/form-data"
          class="composer">

        @csrf

        <input type="file"
               id="file-input"
               class="sr-file">

        <button type="button"
                id="btn-attach"
                class="icon-btn">
            <i class="bi bi-paperclip"></i>
        </button>

        <div class="field">
            <input type="text"
                   id="chat-input"
                   placeholder="Escribe un mensaje...">
        </div>

        <button id="send-btn"
                type="submit"
                class="send-btn">
            <i class="bi bi-send"></i>
        </button>

    </form>

</div>
          </div>
</div>

<div id="toast" class="toast">
  <i class="bi bi-check-circle"></i>
  <div>
    <div id="toast-title">Listo</div>
    <div class="muted" id="toast-msg"></div>
  </div>
</div>

<script>
  const chatBody   = document.getElementById('chat-body');
  const chatForm   = document.getElementById('chat-form');
  const chatInput  = document.getElementById('chat-input');
  let   fileInput  = document.getElementById('file-input');
  const btnAttach  = document.getElementById('btn-attach');
  const sendBtn    = document.getElementById('send-btn');
  const typing     = document.getElementById('typing');

  const attachBar  = document.getElementById('attach-bar');
  const attachChip = document.getElementById('attach-chip');
  const attachRmBtn= document.getElementById('attach-remove');

  const modeBadge  = document.getElementById('mode-badge');
  const btnClose   = document.getElementById('btn-close');

  const btnClaim   = document.getElementById('btn-claim');
  const btnRelease = document.getElementById('btn-release');
  const btnCloseChat = document.getElementById('btn-close-chat');
  const waLayout = document.getElementById('waLayout');

  const btnAiSuggest = document.getElementById('btn-ai-suggest');
  const btnAiSendTop = document.getElementById('btn-ai-send');

  const aiPanel   = document.getElementById('ai-panel');
  const aiText    = document.getElementById('ai-text');
  const aiMeta    = document.getElementById('ai-meta');
  const aiReason  = document.getElementById('ai-reason');
  const btnAiCopy = document.getElementById('btn-ai-copy');
  const btnAiClear= document.getElementById('btn-ai-clear');
  const btnAiSendPanel = document.getElementById('btn-ai-send-panel');
  const btnAiHandover  = document.getElementById('btn-ai-handover');

  const toastEl = document.getElementById('toast');
  const toastTitle = document.getElementById('toast-title');
  const toastMsg = document.getElementById('toast-msg');

  const convoEl = document.querySelector('.chat-convo');
  const user    = convoEl.dataset.user;
  let handover  = convoEl.dataset.handover === '1';

  const MEDIA_URL_TPL = @json($waMediaUrlTpl);
  const CLOSE_URL     = @json($closeUrl);
  const FETCH_URL_TPL = @json($fetchUrlTpl);

  const CLAIM_URL     = @json($claimUrl);
  const RELEASE_URL   = @json($releaseUrl);
  const AI_SUGGEST_URL= @json($aiSuggestUrl);
  const AI_SEND_URL   = @json($aiSendUrl);

  const mediaUrl = (id) => MEDIA_URL_TPL ? MEDIA_URL_TPL.replace('MEDIA_ID_PLACEHOLDER', encodeURIComponent(id)) : null;
  const fetchUrl = (u) => FETCH_URL_TPL.replace('USER_PLACEHOLDER', encodeURIComponent(u));

  const csrf = () => document.querySelector('input[name=_token]')?.value || '';

  function toast(title, msg=''){
    toastTitle.textContent = title;
    toastMsg.textContent = msg;
    toastEl.classList.add('show');
    clearTimeout(window.__toastT);
    window.__toastT = setTimeout(()=> toastEl.classList.remove('show'), 2200);
  }

  function setModeBadge(isHandover, agentName=''){
    handover = !!isHandover;
    convoEl.dataset.handover = handover ? '1' : '0';

    if (!modeBadge) return;
    if (handover){
      modeBadge.innerHTML = `<i class="bi bi-person-check"></i><span>En atención${agentName ? ' · '+escapeHtml(agentName) : ''}</span>`;
    } else {
      modeBadge.innerHTML = `<i class="bi bi-robot"></i><span>Automático</span>`;
    }
  }

  function showAiPanel(show=true){
    if(!aiPanel) return;
    aiPanel.style.display = show ? 'block' : 'none';
  }

  function setAiInfo(metaText='', reasonText='', summaryText=''){
    if(!aiMeta || !aiReason) return;

    aiMeta.textContent = metaText;
    if (reasonText || summaryText){
      aiReason.style.display = 'block';
      aiReason.innerHTML = `
        <div style="display:flex;align-items:center;gap:8px;">
          <i class="bi bi-exclamation-triangle"></i>
          <span>${escapeHtml(reasonText || 'Requiere asesor')}</span>
        </div>
        ${summaryText ? `<div style="margin-top:6px;color:#9a3412;font-weight:800;">${escapeHtml(summaryText)}</div>` : ''}
      `;
    } else {
      aiReason.style.display = 'none';
      aiReason.innerHTML = '';
    }
  }

  /* === Picker de archivos (showPicker -> click fallback) === */
  btnAttach.addEventListener('click', (e) => {
    e.preventDefault();
    if (typeof fileInput.showPicker === 'function') {
      try { fileInput.showPicker(); return; } catch (err) {}
    }
    fileInput.click();
  });

  /* === Forzar MX siempre === */
  const MX_TZ = 'America/Mexico_City';
  const timeFmt = new Intl.DateTimeFormat('es-MX', { timeZone: MX_TZ, hour:'2-digit', minute:'2-digit', hour12:false });

  /* ===== Autoscroll con respeto al scroll del usuario ===== */
  let pinnedToBottom = true;
  const isNearBottom=(t=90)=> (chatBody.scrollHeight - chatBody.clientHeight - chatBody.scrollTop) <= t;
  const scrollToBottom=()=> chatBody.scrollTop = chatBody.scrollHeight;

  const preserveScrollWhile=(fn)=>{
    if (pinnedToBottom){ fn(); scrollToBottom(); return; }
    const oldBottom = chatBody.scrollHeight - chatBody.scrollTop;
    fn();
    requestAnimationFrame(()=> chatBody.scrollTop = chatBody.scrollHeight - oldBottom);
  };

  chatBody.addEventListener('scroll', ()=> pinnedToBottom = isNearBottom());
  scrollToBottom();

  /* ======== PARSEO DE TIEMPOS – asumir UTC si no hay zona ======== */
  const RE_NO_TZ = /^\d{4}-\d{2}-\d{2}(?:[ T]\d{2}:\d{2}:\d{2})$/;

  function toDate(ts){
    if (!ts) return null;
    if (typeof ts === 'number') return (ts > 1e12) ? new Date(ts) : new Date(ts*1000);
    if (/^\d{10}$/.test(ts)) return new Date(Number(ts)*1000);
    if (/^\d{13}$/.test(ts)) return new Date(Number(ts));
    if (typeof ts === 'string' && RE_NO_TZ.test(ts)) return new Date(ts.replace(' ', 'T') + 'Z');
    const d = new Date(ts);
    return isNaN(d.getTime()) ? null : d;
  }

  function normalizeTsIso(ts){
    if (!ts) return '';
    if (typeof ts === 'string' && RE_NO_TZ.test(ts)) return new Date(ts.replace(' ', 'T') + 'Z').toISOString();
    if (typeof ts === 'number') { const ms = ts > 1e12 ? ts : ts*1000; return new Date(ms).toISOString(); }
    if (/^\d{10}$/.test(ts)) return new Date(Number(ts)*1000).toISOString();
    if (/^\d{13}$/.test(ts)) return new Date(Number(ts)).toISOString();
    const d = new Date(ts);
    return isNaN(d.getTime()) ? '' : d.toISOString();
  }

  function tsToSec(ts){ const d = toDate(ts); return d ? Math.floor(d.getTime()/1000) : 0; }

  function renderTimes(){
    document.querySelectorAll('.time[data-ts]').forEach(el=>{
      const d = toDate(el.dataset.ts);
      if (!d) return;
      el.textContent = timeFmt.format(d);
    });
  }
  renderTimes();

  /* ===== Separadores por día ===== */
  const dayFmtKey = new Intl.DateTimeFormat('en-CA', { timeZone: MX_TZ, year:'numeric', month:'2-digit', day:'2-digit' });

  function dayKeyFor(d){
    const parts = dayFmtKey.formatToParts(d);
    const y = parts.find(p=>p.type==='year').value;
    const m = parts.find(p=>p.type==='month').value;
    const da= parts.find(p=>p.type==='day').value;
    return `${y}-${m}-${da}`;
  }

  function ensureDaySeparator(d){
    const key = dayKeyFor(d);
    if (chatBody.querySelector(`.day-sep[data-day="${key}"]`)) return;

    const label = (()=>{
      const today = dayKeyFor(new Date());
      const yd    = dayKeyFor(new Date(Date.now()-86400000));
      if (key===today) return 'Hoy';
      if (key===yd)    return 'Ayer';
      return new Intl.DateTimeFormat('es-MX',{timeZone:MX_TZ, day:'2-digit', month:'short', year:'numeric'}).format(d);
    })();

    const sep = `<div class="day-sep" data-day="${key}">${label}</div>`;
    const nodes = [...chatBody.querySelectorAll('.group')];
    let inserted = false;

    const startOfDay = new Date(d); startOfDay.setHours(0,0,0,0);
    const sSec = Math.floor(startOfDay.getTime()/1000);

    for (const node of nodes){
      const nSec = tsToSec(node.dataset.ts);
      if (nSec >= sSec){ node.insertAdjacentHTML('beforebegin', sep); inserted = true; break; }
    }
    if (!inserted) chatBody.insertAdjacentHTML('beforeend', sep);
  }

  /* ===== UX envío ===== */
  function updateSendEnabled(){
    const hasText = chatInput.value.trim().length>0;
    const hasFile = fileInput.files.length>0;
    sendBtn.disabled = !(hasText || hasFile);
  }

  chatInput.addEventListener('input', ()=>{
    if (typing) typing.style.display = chatInput.value.trim().length ? 'flex' : 'none';
    updateSendEnabled();
  });

  let previewUrl=null;
  const formatBytes=b=>{ if(!b&&b!==0)return''; const u=['B','KB','MB','GB']; let i=0; while(b>=1024&&i<u.length-1){b/=1024;i++} return `${b.toFixed(1)} ${u[i]}`; };

  function showAttachmentPreview(file){
    if (previewUrl){ URL.revokeObjectURL(previewUrl); previewUrl=null; }
    attachChip.innerHTML='';

    const size=formatBytes(file.size);
    let inner='';

    if ((file.type||'').startsWith('image/')){
      previewUrl = URL.createObjectURL(file);
      inner = `
        <img src="${previewUrl}" class="attach-thumb" alt="Imagen">
        <div style="min-width:0">
          <div class="attach-name">${escapeHtml(file.name)}</div>
          <div class="attach-size">${escapeHtml(size)} • Imagen</div>
        </div>`;
    } else {
      inner = `
        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='44' height='44'%3E%3Crect width='44' height='44' rx='10' fill='%23fff' stroke='%23e5e7eb'/%3E%3Cpath d='M15 13h10l4 4v14a2 2 0 0 1-2 2H15a2 2 0 0 1-2-2V15a2 2 0 0 1 2-2z' fill='%23f1f5f9' stroke='%23cbd5e1'/%3E%3C/svg%3E" class="attach-thumb" alt="Documento">
        <div style="min-width:0">
          <div class="attach-name">${escapeHtml(file.name)}</div>
          <div class="attach-size">${escapeHtml(size)} • Documento</div>
        </div>`;
    }

    attachBar.style.display='flex';
    attachChip.insertAdjacentHTML('beforeend', inner);
  }

  function bindFileListener(){
    fileInput.addEventListener('change', ()=>{
      if (fileInput.files.length) showAttachmentPreview(fileInput.files[0]);
      else hideAttachmentPreview();
      updateSendEnabled();
    });
  }
  bindFileListener();

  function hideAttachmentPreview(){
    if(previewUrl){URL.revokeObjectURL(previewUrl); previewUrl=null;}
    attachChip.innerHTML='';
    attachBar.style.display='none';
  }

  function clearFileInputHard(){
    const clone = fileInput.cloneNode(true);
    clone.value = '';
    fileInput.replaceWith(clone);
    fileInput = clone;
    bindFileListener();
  }

  function clearAttachmentAll(){
    hideAttachmentPreview();
    clearFileInputHard();
    updateSendEnabled();
  }
  if (attachRmBtn) {
    attachRmBtn.addEventListener('click', clearAttachmentAll);
}

  chatInput.addEventListener('keydown', e=>{
    if (e.key==='Enter' && !e.shiftKey){
      e.preventDefault();
      if(!sendBtn.disabled) chatForm.requestSubmit();
    }
  });

  function detectType(){
    const f=fileInput.files[0];
    if(!f) return 'text';
    return (f.type||'').startsWith('image/') ? 'image' : 'document';
  }

  /* ===== Envío AJAX (manual asesor) ===== */
  let sending=false;

  chatForm.addEventListener('submit', async e=>{
    e.preventDefault();
    if(sending) return;

    const hasFile=fileInput.files.length>0;
    const typed=chatInput.value.trim();
    if(!hasFile && !typed) return;

    sending=true;
    sendBtn.disabled=true;

    const fd=new FormData();
    fd.append('_token', csrf());

    const type=hasFile ? detectType() : 'text';
    fd.append('type', type);
    if (hasFile) fd.append('file', fileInput.files[0]);
    else fd.append('text', typed);

    chatInput.value='';
    if (typing) typing.style.display='none';
    clearAttachmentAll();

    try{
      const res = await fetch(chatForm.action, {
        method:'POST',
        body:fd,
        headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
        cache:'no-store'
      });
      const msg = await res.json();
      preserveScrollWhile(()=> upsertMessage(msg));
      renderTimes();

      // Si mandas manual, normalmente ya estás en handover
      setModeBadge(true, ''); // el backend pondrá nombre; aquí solo refleja que es mano humana
      toast('Enviado', 'Mensaje enviado por asesor');
    }catch(err){
      console.error('send error', err);
      toast('Error', 'No se pudo enviar');
    }finally{
      sending=false;
      updateSendEnabled();
    }
  });

  /* ===== Helpers de render ===== */
  const escapeHtml=s=>(s||'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const escapeAttr=s=>escapeHtml(s).replace(/"/g,'&quot;');
  const statusIcon=s=> s==='read'?{text:'✓✓',cls:'ticks read'} : s==='delivered'?{text:'✓✓',cls:'ticks'} : s==='sent'?{text:'✓',cls:'ticks'} : {text:'·',cls:'ticks'};

  function messageHtml(m){
    const dir = m.direction==='in' ? 'in':'out';
    let content='';

    if (m.type==='text'){
      content = (m.text?escapeHtml(m.text).replace(/\n/g,'<br>'):'');
    } else if (m.type==='image'){
      const u = m.media_id ? (mediaUrl(m.media_id) || m.media_link || '#') : (m.media_link || '#');
      content = `<img src="${escapeAttr(u)}" alt="Imagen">`;
    } else if (m.type==='document'){
      const u = m.media_id ? (mediaUrl(m.media_id) || m.media_link || '#') : (m.media_link || '#');
      const name = m.media_filename || 'Documento';
      content = `<i class="bi bi-file-earmark-text"></i> <a href="${escapeAttr(u)}" target="_blank">${escapeHtml(name)}</a>`;
    } else {
      content = escapeHtml(m.text||'');
    }

    const tsIso = normalizeTsIso(m.wa_timestamp);
    const st = m.status||'';
    const ic = statusIcon(st);
    const metaRight = dir==='out' ? ' right' : '';

    const d = toDate(tsIso);
    if (d) ensureDaySeparator(d);

    return `
      <div class="group" data-id="${m.id}" data-ts="${escapeAttr(tsIso)}">
        <div class="msg ${dir}">${content}</div>
        <div class="meta${metaRight}">
          <span class="time" data-ts="${escapeAttr(tsIso)}"></span>
          ${dir==='out' ? `<span class="${ic.cls}" data-status="${escapeAttr(st)}">${ic.text}</span>` : ''}
        </div>
      </div>
    `;
  }

  function insertSorted(html, tsIso){
    const tsSec = tsToSec(tsIso);
    const nodes = chatBody.querySelectorAll('.group');
    for (const node of nodes){
      const nSec = tsToSec(node.dataset.ts);
      if (nSec > tsSec){ node.insertAdjacentHTML('beforebegin', html); return; }
    }
    chatBody.insertAdjacentHTML('beforeend', html);
  }

  function upsertMessage(m){
    const node = chatBody.querySelector(`.group[data-id="${m.id}"]`);
    if (!node){
      const html  = messageHtml(m);
      const tsIso = normalizeTsIso(m.wa_timestamp);
      insertSorted(html, tsIso);

      const justImg = chatBody.querySelector(`.group[data-id="${m.id}"] img`);
      if (justImg) justImg.addEventListener('load', ()=>{ if (pinnedToBottom) scrollToBottom(); }, {once:true});
    } else {
      if (m.direction==='out'){
        const ticksEl = node.querySelector('.ticks');
        if (ticksEl){
          const ic = statusIcon(m.status||'');
          ticksEl.textContent = ic.text;
          ticksEl.className = ic.cls;
          ticksEl.dataset.status = m.status||'';
        }
      }
      const tEl = node.querySelector('.time');
      const tsIso = normalizeTsIso(m.wa_timestamp);
      if (tEl && tsIso){ tEl.dataset.ts = tsIso; node.dataset.ts = tsIso; }
    }
  }

  /* ===== Polling ===== */
  async function tick(){
    try{
      const res = await fetch(fetchUrl(user), { headers:{'Accept':'application/json'}, cache:'no-store' });
      const msgs = await res.json();
      preserveScrollWhile(()=>{ msgs.forEach(m=>upsertMessage(m)); });
      if (pinnedToBottom) scrollToBottom();
      renderTimes();
    }catch(e){
      console.error('poll error', e);
    }
  }
  setInterval(tick, 2500);

  /* ===== Handover: Claim / Release ===== */
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
      cache:'no-store'
    });
    let json = {};
    try { json = await res.json(); } catch(e) {}
    if (!res.ok) throw {status:res.status, json};
    return json;
  }

  if (btnClaim){
    btnClaim.addEventListener('click', async ()=>{
      btnClaim.disabled = true;
      try{
        const r = await postJson(CLAIM_URL, {});
        setModeBadge(true, (r.agent && r.agent.name) ? r.agent.name : '');
        showAiPanel(false);
        toast('Tomada', 'Conversación en modo asesor');
      }catch(e){
        console.error('claim err', e);
        toast('Error', 'No se pudo tomar');
      }finally{
        btnClaim.disabled = false;
      }
    });
  }

  if (btnRelease){
    btnRelease.addEventListener('click', async ()=>{
      btnRelease.disabled = true;
      try{
        const r = await postJson(RELEASE_URL, {});
        setModeBadge(false, '');
        toast('Liberada', 'IA en modo automático');
      }catch(e){
        console.error('release err', e);
        toast('Error', 'No se pudo liberar');
      }finally{
        btnRelease.disabled = false;
      }
    });
  }

  /* ===== IA: Suggest / Send ===== */
  async function aiSuggest(){
    if (!btnAiSuggest) return;
    btnAiSuggest.disabled = true;
    try{
      showAiPanel(true);
      if (aiText) aiText.value = '';
      setAiInfo('Generando…', '', '');
      const r = await postJson(AI_SUGGEST_URL, {});
      const reply = (r.reply || '').trim();
      if (aiText) aiText.value = reply;
      setAiInfo(r.handover ? 'Sugiere escalar' : 'Sugerencia lista', r.reason || '', r.summary || '');

      if (!reply) toast('IA', r.handover ? 'IA sugiere asesor' : 'IA no devolvió texto');
      else toast('IA lista', r.handover ? 'Recomendó asesor' : 'Respuesta sugerida');

      // Si IA sugiere handover, no activamos automático aquí; solo informamos.
    }catch(e){
      console.error('ai suggest err', e);
      setAiInfo('', '', '');
      toast('Error', 'IA falló');
    }finally{
      btnAiSuggest.disabled = false;
    }
  }

  async function aiSend(message){
    if (btnAiSendTop) btnAiSendTop.disabled = true;
    if (btnAiSendPanel) btnAiSendPanel.disabled = true;
    try{
      const payload = message ? { message } : {};
      const r = await postJson(AI_SEND_URL, payload);

      if (r.handover){
        // La IA decidió que debe entrar asesor
        setModeBadge(true, '');
        setAiInfo('Escalado', r.reason || 'Escalado por IA', r.summary || '');
        toast('Escalado', 'IA marcó handover');
        return;
      }

      if (r.sent && r.message){
        preserveScrollWhile(()=> upsertMessage(r.message));
        renderTimes();
        setModeBadge(false, '');
        toast('Enviado', 'Respuesta IA enviada');
      } else {
        toast('IA', 'No se envió');
      }
    }catch(e){
      console.error('ai send err', e);
      toast('Error', 'No se pudo enviar IA');
    }finally{
      if (btnAiSendTop) btnAiSendTop.disabled = false;
      if (btnAiSendPanel) btnAiSendPanel.disabled = false;
    }
  }

  if (btnAiSuggest) btnAiSuggest.addEventListener('click', aiSuggest);
  if (btnAiSendTop) btnAiSendTop.addEventListener('click', ()=> aiSend((aiText?.value||'').trim() || null));

  if (btnAiSendPanel){
    btnAiSendPanel.addEventListener('click', ()=>{
      const msg = (aiText?.value||'').trim();
      if (!msg) return toast('IA', 'No hay texto para enviar');
      aiSend(msg);
    });
  }

  if (btnAiCopy){
    btnAiCopy.addEventListener('click', async ()=>{
      try{
        await navigator.clipboard.writeText(aiText?.value||'');
        toast('Copiado', 'Respuesta IA al portapapeles');
      }catch(e){
        toast('Error', 'No se pudo copiar');
      }
    });
  }

  if (btnAiClear){
    btnAiClear.addEventListener('click', ()=>{
      if (aiText) aiText.value = '';
      setAiInfo('', '', '');
      toast('Listo', 'Panel IA limpio');
    });
  }

  if (btnAiHandover){
    btnAiHandover.addEventListener('click', async ()=>{
      btnAiHandover.disabled = true;
      try{
        const r = await postJson(CLAIM_URL, {});
        setModeBadge(true, (r.agent && r.agent.name) ? r.agent.name : '');
        toast('Escalado', 'Pasó a asesor');
      }catch(e){
        console.error(e);
        toast('Error', 'No se pudo escalar');
      }finally{
        btnAiHandover.disabled = false;
      }
    });
  }

  /* ===== Cerrar conversación (si existe ruta) ===== */
  if (btnClose && CLOSE_URL){
    btnClose.addEventListener('click', async ()=>{
      btnClose.disabled = true;
      try{
        await fetch(CLOSE_URL, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf()
          }
        });
        setModeBadge(false, '');
        toast('Cerrado', 'Encuesta enviada / conversación cerrada');
      }catch(e){
        console.error('close error', e);
        toast('Error', 'No se pudo cerrar');
        btnClose.disabled = false;
      }
    });
  }

  updateSendEnabled();
  if(btnCloseChat){

    btnCloseChat.addEventListener('click', () => {

        window.location.href = '/whatsapp/inbox';

    });

}
/* ===== Buscador de conversaciones ===== */
document.addEventListener('DOMContentLoaded', function () {

    const buscador = document.getElementById('searchConversation');

    if (!buscador) return;

    buscador.addEventListener('keyup', function () {

        const filtro = this.value.toLowerCase().trim();

        document.querySelectorAll('.chat-item').forEach(item => {

            const texto = item.textContent.toLowerCase();

            if (texto.includes(filtro)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }

        });

    });

});
</script>
@endsection