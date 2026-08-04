<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoai.png') }}?v=1">
    <title>@yield('title', 'Sistema de Cotizaciones')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}?v={{ time() }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/locales/es.js"></script>

    @yield('styles')

    <style>
        body{
            margin:0;
            padding:0;
            font-family:"Quicksand", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .cotz-root{
            --cotz-border:#dde2f3;
            --cotz-primary:#4f7dff;
            --cotz-primary-600:#3256e6;
            --cotz-accent:#ff73b5;
            --cotz-muted:#6b7280;
            --cotz-sidebar-w:310px;
            --cotz-topbar-h:56px;
            --cotz-radius-md:14px;
        }

        .cotz-shell{
            min-height:100vh;
            display:flex;
            flex-direction:column;
            transition:filter .28s ease;
        }
        .cotz-shell.cotz-dimmed{
            filter:blur(2px) saturate(.95);
        }

        .cotz-sidebar{
            position:fixed;
            top:0;
            bottom:0;
            left:0;
            width:var(--cotz-sidebar-w);
            max-width:90vw;
            background:linear-gradient(180deg,#f7f8ff,#edf2ff);
            border-right:1px solid var(--cotz-border);
            box-shadow:0 18px 40px rgba(15,23,42,.18);
            display:flex;
            flex-direction:column;
            transform:translateX(-102%);
            transition:transform .45s cubic-bezier(.16,1,.3,1);
            z-index:60;
        }
        .cotz-sidebar.cotz-open{
            transform:translateX(0);
        }

        .cotz-backdrop{
            position:fixed;
            inset:0;
            background:rgba(15,23,42,.45);
            opacity:0;
            pointer-events:none;
            transition:opacity .25s ease;
            z-index:50;
        }
        .cotz-backdrop.cotz-show{
            opacity:1;
            pointer-events:auto;
        }

        .cotz-sidebar__head{
            padding:16px 18px 14px;
            border-bottom:1px solid var(--cotz-border);
            display:flex;
            align-items:flex-start;
            gap:12px;
        }
        .cotz-sidebar__avatar{
            width:46px;height:46px;
            border-radius:18px;
            background:linear-gradient(135deg,#cbd9ff,#93b4ff);
            display:grid;place-items:center;
            color:#fff;font-weight:700;
            overflow:hidden;
            flex-shrink:0;
            box-shadow:0 14px 30px rgba(79,125,255,.35);
        }
        .cotz-sidebar__avatar img{
            width:100%;height:100%;object-fit:cover;display:block;
        }
        .cotz-sidebar__user{flex:1;min-width:0;}
        .cotz-sidebar__name{font-weight:700;font-size:1rem;line-height:1.2;}
        .cotz-sidebar__mail{font-size:.86rem;color:var(--cotz-muted);margin-top:2px;}
        .cotz-sidebar__chip{
            margin-top:6px;
            display:inline-flex;align-items:center;
            padding:3px 8px;
            border-radius:999px;
            background:#e4e8ff;
            color:#283366;
            font-size:.75rem;
            border:1px solid #d4dbff;
        }
        .cotz-sidebar__close{
            margin-left:auto;
            background:transparent;
            border:0;
            cursor:pointer;
            width:32px;height:32px;
            border-radius:999px;
            display:grid;place-items:center;
            color:var(--cotz-muted);
            transition:background .18s ease, transform .08s ease, color .18s ease;
        }
        .cotz-sidebar__close:hover{
            background:rgba(79,125,255,.12);
            color:var(--cotz-primary-600);
        }
        .cotz-sidebar__close:active{transform:scale(.96);}

        .cotz-nav{
            flex:1;
            overflow:auto;
            padding:10px 6px 12px;
            scrollbar-width:none;
            -ms-overflow-style:none;
        }
        .cotz-nav::-webkit-scrollbar{display:none;}
        .cotz-nav__section{
            list-style:none;
            margin:0;
            padding:0;
            display:flex;
            flex-direction:column;
            gap:4px;
        }

        .cotz-nav__link{
            display:flex;align-items:center;gap:10px;
            padding:12px 12px;
            border-radius:var(--cotz-radius-md);
            text-decoration:none;
            font-size:1rem;
            color:#1f2937;
            transition:background .18s ease,color .18s ease,transform .06s ease;
        }
        .cotz-nav__link svg{flex-shrink:0;}
        .cotz-nav__link:hover{
            background:#e7efff;
            color:var(--cotz-primary-600);
            transform:translateX(2px);
        }
        .cotz-nav__link.cotz-active{
            background:#dde6ff;
            color:var(--cotz-primary-600);
            font-weight:600;
        }

        .cotz-nav__group{border-radius:var(--cotz-radius-md);}
        .cotz-nav__group > summary{
            list-style:none;
            cursor:pointer;
            border-radius:var(--cotz-radius-md);
            display:flex;align-items:center;gap:10px;
            padding:12px 12px;
            font-size:1rem;
            color:#1f2937;
            transition:background .18s ease,color .18s ease,transform .06s ease;
        }
        .cotz-nav__group > summary::-webkit-details-marker{display:none;}
        .cotz-nav__group[open] > summary{
            background:#e7efff;
            color:var(--cotz-primary-600);
        }
        .cotz-nav__group > summary:hover{
            background:#e7efff;
            color:var(--cotz-primary-600);
            transform:translateX(2px);
        }
        .cotz-nav__chev{
            margin-left:auto;
            transition:transform .2s ease,opacity .2s ease;
            opacity:.7;
        }
        .cotz-nav__group[open] .cotz-nav__chev{transform:rotate(90deg);}

        .cotz-nav__submenu{
            padding:4px 0 8px 34px;
            display:flex;flex-direction:column;gap:4px;
        }
        .cotz-nav__sublink{
            display:flex;align-items:center;gap:8px;
            padding:9px 10px;
            border-radius:10px;
            text-decoration:none;
            font-size:.95rem;
            color:#374151;
            transition:background .16s ease, color .16s ease, transform .06s ease;
        }
        .cotz-nav__sublink:hover{
            background:rgba(79,125,255,.12);
            color:var(--cotz-primary-600);
            transform:translateX(2px);
        }
        .cotz-nav__sublink.cotz-active{
            background:rgba(79,125,255,.18);
            color:var(--cotz-primary-600);
            font-weight:600;
        }

        .cotz-logout{
            padding:10px 12px 14px;
            border-top:1px solid var(--cotz-border);
            margin-top:6px;
        }
        .cotz-btn-logout{
            width:100%;
            border-radius:18px;
            border:1px solid #ffcdd8;
            background:#ffe4ea;
            padding:11px 14px;
            display:flex;align-items:center;gap:10px;
            color:#9b1231;
            font-weight:600;
            cursor:pointer;
            transition:filter .16s ease,transform .06s ease;
        }
        .cotz-btn-logout:hover{filter:brightness(1.03);}
        .cotz-btn-logout:active{transform:scale(.98);}

        .cotz-topbar{
            position:sticky;
            top:0;
            z-index:30;
            height:var(--cotz-topbar-h);
            display:flex;
            align-items:center;
            gap:12px;
            padding:8px 18px;
            background:linear-gradient(180deg,#edf2ff,#e4ecff);
            border-bottom:1px solid var(--cotz-border);
        }
        .cotz-icon-pill{
            width:42px;height:42px;
            border-radius:18px;
            border:none;
            cursor:pointer;
            display:grid;place-items:center;
            background:radial-gradient(circle at 10% 0%, #f9fbff 0%, #e2e9ff 55%, #d5e1ff 100%);
            box-shadow:0 18px 40px rgba(79,125,255,.35);
            color:#3050c6;
            transition:transform .08s ease,filter .16s ease;
        }
        .cotz-icon-pill:hover{filter:brightness(1.03);}
        .cotz-icon-pill:active{transform:scale(.96);}

        .cotz-topbar__title{font-weight:700; letter-spacing:.3px;}
        .cotz-topbar__right{margin-left:auto; display:flex; align-items:center; gap:12px;}

        .cotz-notif-btn{
            position:relative;
            width:38px;height:38px;
            border-radius:999px;
            border:none;
            display:grid;place-items:center;
            background:rgba(255,255,255,.92);
            box-shadow:0 12px 30px rgba(15,23,42,.12);
            color:#111827;
            cursor:pointer;
            transition:transform .08s ease, filter .16s ease;
        }
        .cotz-notif-btn:hover{filter:brightness(1.02);}
        .cotz-notif-btn:active{transform:scale(.97);}

        .cotz-notif-dot{
            position:absolute;
            top:7px;right:7px;
            width:10px;height:10px;
            border-radius:999px;
            background:var(--cotz-accent);
            box-shadow:0 0 0 4px rgba(255,115,181,.25);
            animation: cotzPulse 1.35s ease-in-out infinite;
        }
        @keyframes cotzPulse{
            0%,100%{transform:scale(1); opacity:1;}
            50%{transform:scale(1.12); opacity:.75;}
        }

        .cotz-notif-badge{
            position:absolute;
            bottom:-6px;
            right:-6px;
            min-width:18px;
            height:18px;
            padding:0 6px;
            border-radius:999px;
            background:#020617;
            color:#fff;
            font-size:11px;
            font-weight:700;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            box-shadow:0 10px 22px rgba(2,6,23,.22);
        }

        .cotz-notif-menu{
            font-family:"Quicksand", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            width:360px;
            max-width:92vw;
            padding:0;
            border-radius:18px;
            border:1px solid rgba(15,23,42,.08);
            box-shadow:0 22px 60px rgba(15,23,42,.18);
            overflow:hidden;
        }
        .cotz-notif-head{
            padding:12px 14px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            background:linear-gradient(180deg, rgba(255,255,255,.98), rgba(248,250,252,.98));
            border-bottom:1px solid rgba(15,23,42,.06);
        }
        .cotz-notif-title{
            display:flex;
            align-items:center;
            gap:8px;
            font-weight:800;
            color:#0f172a;
            letter-spacing:.2px;
            font-size:.92rem;
        }
        .cotz-notif-sub{
            font-size:.78rem;
            color:rgba(15,23,42,.55);
            font-weight:600;
        }
        .cotz-notif-action{
            border:0;
            background:transparent;
            padding:6px 8px;
            border-radius:10px;
            color:var(--cotz-primary-600);
            font-weight:700;
            font-size:.78rem;
            transition:background .16s ease, transform .08s ease;
            white-space:nowrap;
        }
        .cotz-notif-action:hover{background:rgba(79,125,255,.10);}
        .cotz-notif-action:active{transform:scale(.98);}

        .cotz-notif-list{
            max-height:360px;
            overflow:auto;
            padding:8px;
        }
        .cotz-notif-list::-webkit-scrollbar{width:10px;}
        .cotz-notif-list::-webkit-scrollbar-thumb{
            background:rgba(15,23,42,.12);
            border-radius:999px;
            border:3px solid rgba(255,255,255,.9);
        }

        .cotz-notif-row{
            width:100%;
            text-align:left;
            border:1px solid rgba(15,23,42,.06);
            background:rgba(255,255,255,.95);
            border-radius:14px;
            padding:10px 12px;
            display:flex;
            gap:10px;
            align-items:flex-start;
            transition:transform .08s ease, box-shadow .16s ease, border-color .16s ease;
        }
        .cotz-notif-row + .cotz-notif-row{margin-top:8px;}
        .cotz-notif-row:hover{
            transform:translateY(-1px);
            box-shadow:0 14px 30px rgba(15,23,42,.10);
            border-color:rgba(79,125,255,.18);
        }

        .cotz-notif-ico{
            width:34px;height:34px;
            border-radius:12px;
            display:grid;place-items:center;
            background:linear-gradient(135deg, rgba(79,125,255,.16), rgba(255,115,181,.10));
            color:#1f2a55;
            flex-shrink:0;
        }
        .cotz-notif-body{min-width:0; flex:1;}
        .cotz-notif-line1{
            font-weight:800;
            color:#0f172a;
            font-size:.88rem;
            line-height:1.15;
            margin-bottom:2px;
        }
        .cotz-notif-line2{
            color:rgba(15,23,42,.62);
            font-size:.82rem;
            line-height:1.25;
            margin-bottom:6px;
            word-break:break-word;
        }
        .cotz-notif-meta{
            display:flex;
            gap:8px;
            align-items:center;
            font-size:.74rem;
            color:rgba(15,23,42,.45);
            font-weight:700;
        }
        .cotz-notif-pill{
            font-size:.70rem;
            font-weight:800;
            padding:3px 8px;
            border-radius:999px;
            background:rgba(79,125,255,.10);
            color:var(--cotz-primary-600);
            border:1px solid rgba(79,125,255,.18);
        }

        .cotz-notif-empty{
            padding:22px 16px;
            text-align:center;
            color:rgba(15,23,42,.55);
        }
        .cotz-notif-empty-ico{
            width:48px;height:48px;
            margin:0 auto 10px;
            border-radius:16px;
            display:grid;place-items:center;
            background:rgba(15,23,42,.04);
            border:1px solid rgba(15,23,42,.06);
        }
        .cotz-notif-empty h6{
            margin:0;
            font-weight:900;
            color:#0f172a;
            font-size:.92rem;
        }
        .cotz-notif-empty p{
            margin:6px 0 0;
            font-weight:600;
            font-size:.84rem;
            color:rgba(15,23,42,.55);
        }

        .cotz-avatar-sm{
            width:38px;height:38px;
            border-radius:999px;
            background:#d1d5f4;
            display:grid;place-items:center;
            overflow:hidden;
            box-shadow:0 10px 26px rgba(15,23,42,.18);
            color:#111827;
            font-weight:700;
        }
        .cotz-avatar-sm img{width:100%;height:100%;object-fit:cover;}
    </style>
</head>
<body>
@php
    $u  = auth()->user();
    $nm = $u?->name ?? 'Usuario';
    $ini = mb_strtoupper(mb_substr($nm,0,1));
    $mail = $u?->email ?? 'correo@dominio.com';

    $avatarSrc = null;
    if ($u && !empty($u->imagen)) {
        $raw = $u->imagen;
        if (is_string($raw)) {
            if (strpos($raw, 'http://') === 0 || strpos($raw, 'https://') === 0 || strpos($raw, '//') === 0) {
                $avatarSrc = $raw;
            } else {
                $avatarSrc = asset('storage/'.$raw);
            }
        }
    }

    $unreadCount   = 0;
    $notifications = collect();

    if ($u) {
        $unreadCount   = $u->unreadNotifications()->count();
        $notifications = $u->unreadNotifications()->latest()->limit(12)->get();
    }
@endphp

<div class="cotz-root">
    <aside id="cotzSidebar" class="cotz-sidebar" aria-hidden="true" aria-label="Menú lateral">
        <div class="cotz-sidebar__head">
            <div class="cotz-sidebar__avatar">
                @if($avatarSrc)
                    <img src="{{ $avatarSrc }}" alt="Avatar de {{ $nm }}">
                @else
                    {{ $ini }}
                @endif
            </div>
            <div class="cotz-sidebar__user">
                <div class="cotz-sidebar__name">{{ $nm }}</div>
                <div class="cotz-sidebar__mail">{{ $mail }}</div>
                @if($u && method_exists($u,'getRoleNames'))
                    @foreach($u->getRoleNames() as $r)
                        <div class="cotz-sidebar__chip">{{ $r }}</div>
                        @break
                    @endforeach
                @endif
            </div>
            <button class="cotz-sidebar__close" id="cotzBtnClose" aria-label="Cerrar menú">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="cotz-nav">
            <ul class="cotz-nav__section">

                @if($u && $u->id == 19)

                    <li>
                        <a href="{{ route('perfil') }}" class="cotz-nav__link">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                <circle cx="12" cy="8" r="4"/>
                                <path d="M4 20a8 8 0 0 1 16 0"/>
                            </svg>
                            <span>Perfil</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/inventario/servicio') }}" class="cotz-nav__link">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                <rect x="4" y="4" width="16" height="16" rx="2"/>
                                <path d="M4 10h16M10 4v16"/>
                                <path d="M6.5 7.5h2M6.5 13.5h2M12.5 13.5h2"/>
                            </svg>
                            <span>Activos Int/Ext</span>
                        </a>
                    </li>

                @else

                    <li>
                        <details class="cotz-nav__group">
                            <summary>
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                    <rect x="5" y="3" width="12" height="18" rx="2"/>
                                    <path d="M9 7h4M9 11h3M9 15h4"/>
                                    <path d="M17 3v10l2-1 2 1V3z"/>
                                </svg>
                                <span>Cuentas Bancarias</span>
                                <svg class="cotz-nav__chev" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2">
                                    <path d="M9 6l6 6-6 6"/>
                                </svg>
                            </summary>
                            <div class="cotz-nav__submenu">
                                <a href="{{ url('/publicaciones') }}" class="cotz-nav__sublink"><span>Ver publicaciones</span></a>
                                <a href="{{ url('/publicaciones/crear') }}" class="cotz-nav__sublink"><span>+ Agregar</span></a>
                            </div>
                        </details>
                    </li>

                    <li>
                        <a href="{{ url('/inventario') }}" class="cotz-nav__link">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                <path d="M4 9l4-2 4 2v4l-4 2-4-2z"/>
                                <path d="M12 9l4-2 4 2v4l-4 2-4-2z"/>
                                <path d="M8 11l4-2"/>
                            </svg>
                            <span>Inventario</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/inventario/servicio') }}" class="cotz-nav__link">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                <rect x="4" y="4" width="16" height="16" rx="2"/>
                                <path d="M4 10h16M10 4v16"/>
                                <path d="M6.5 7.5h2M6.5 13.5h2M12.5 13.5h2"/>
                            </svg>
                            <span>Activos Int/Ext</span>
                        </a>
                    </li>

                    @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('editor'))
                        <li>
                            <a href="{{ url('/bento') }}" class="cotz-nav__link">
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                    <circle cx="9" cy="9" r="3.3"/>
                                    <path d="M9 6v6M7.7 7.5h2.4"/>
                                    <path d="M15 7h4l-2-2M17 17h-4l2 2"/>
                                    <path d="M17 7v10"/>
                                </svg>
                                <span>Menu Contabilidad</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/productos/cards') }}" class="cotz-nav__link">
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                    <rect x="4" y="4" width="7" height="7" rx="1.5"/>
                                    <rect x="13" y="4" width="7" height="7" rx="1.5"/>
                                    <rect x="4" y="13" width="7" height="7" rx="1.5"/>
                                    <rect x="13" y="13" width="7" height="7" rx="1.5"/>
                                </svg>
                                <span>Productos</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/promos/whatsapp/direct') }}" class="cotz-nav__link">
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                    <path d="M4 7v6l7 7 9-9-7-7H7z"/>
                                    <circle cx="9" cy="9" r="1"/>
                                </svg>
                                <span>Promocionales</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/whatsapp/inbox') }}" class="cotz-nav__link">
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                    <path d="M5 20l1.5-4.5A7 7 0 1 1 20 10a7 7 0 0 1-10.5 6.1z"/>
                                    <path d="M9.5 9.5c.5 1 1.5 2 2.5 2.5L13 11"/>
                                </svg>
                                <span>WhatsApp Help Desk</span>
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ url('/agenda') }}" class="cotz-nav__link">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                <rect x="3" y="4" width="18" height="17" rx="2"/>
                                <path d="M8 2v4M16 2v4M3 10h18"/>
                                <path d="M10 14h4v3h-4z"/>
                            </svg>
                            <span>Agenda</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('fichas.index') }}" class="cotz-nav__link">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                <path d="M7 3h7l5 5v13H7z"/>
                                <path d="M14 3v5h5"/>
                                <path d="M10 14l1.5 2 2.5-4 3 5"/>
                                <path d="M9 18h6"/>
                            </svg>
                            <span>Fichas Técnicas</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/carta-garantia') }}" class="cotz-nav__link">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                <path d="M12 2l7 4v6c0 5-3 8-7 10-4-2-7-5-7-10V6z"/>
                                <path d="M9 11l2 2 4-4"/>
                            </svg>
                            <span>Garantias</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/cuentas') }}" class="cotz-nav__link">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                <rect x="3" y="7" width="18" height="12" rx="2"/>
                                <path d="M15 7V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v2"/>
                                <path d="M17 13h3v4h-3a3 3 0 0 1 0-4z"/>
                            </svg>
                            <span>Gastos Viáticos</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ url('/envios-gastos') }}" class="cotz-nav__link">
                            <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                <path d="M3 7h10v9H5L3 7z"/>
                                <path d="M13 9h4l4 4v3h-8z"/>
                                <circle cx="7" cy="18" r="2"/>
                                <circle cx="18" cy="18" r="2"/>
                            </svg>
                            <span>Paqueterías</span>
                        </a>
                    </li>

                    @auth
                        <li>
                            <a href="{{ route('entregas.index') }}" class="cotz-nav__link">
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                    <rect x="5" y="3" width="14" height="18" rx="2"/>
                                    <path d="M9 8h6"/>
                                    <path d="M8 13v4M10 13v4M12 13v4M14 13v4"/>
                                </svg>
                                <span>Guías</span>
                            </a>
                        </li>

                        <li>
                            <details class="cotz-nav__group">
                                <summary>
                                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                        <path d="M3 11h9V6H6z"/>
                                        <path d="M12 8h5l4 4v5h-9z"/>
                                        <circle cx="7" cy="17" r="2"/>
                                        <circle cx="17" cy="17" r="2"/>
                                    </svg>
                                    <span>Camionetas</span>
                                    <svg class="cotz-nav__chev" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2">
                                        <path d="M9 6l6 6-6 6"/>
                                    </svg>
                                </summary>
                                <div class="cotz-nav__submenu">
                                    <a href="{{ route('camionetas.create') }}" class="cotz-nav__sublink"><span>+ Agregar Camioneta</span></a>
                                    <a href="{{ route('camionetas.index') }}" class="cotz-nav__sublink"><span>Lista de Camionetas</span></a>
                                </div>
                            </details>
                        </li>

                        <li>
                            <details class="cotz-nav__group">
                                <summary>
                                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                        <path d="M4 7l8-4 8 4v10l-8 4-8-4z"/>
                                        <path d="M12 5v6"/>
                                        <path d="M9 9l3 3 3-3"/>
                                    </svg>
                                    <span>Solicitudes de Material</span>
                                    <svg class="cotz-nav__chev" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2">
                                        <path d="M9 6l6 6-6 6"/>
                                    </svg>
                                </summary>
                                <div class="cotz-nav__submenu">
                                    @if(Auth::user()->hasRole('admin'))
                                        <a href="{{ route('solicitudes.admin') }}" class="cotz-nav__sublink"><span>Solicitudes Pendientes</span></a>
                                    @endif
                                    <a href="{{ route('solicitudes.index') }}" class="cotz-nav__sublink"><span>Ver Mis Solicitudes</span></a>
                                    <a href="{{ route('solicitudes.create') }}" class="cotz-nav__sublink"><span>+ Crear Solicitud</span></a>
                                </div>
                            </details>
                        </li>

                        <li>
                            <details class="cotz-nav__group">
                                <summary>
                                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                        <path d="M3 5h3l2 11h11"/>
                                        <path d="M7 9h12l-1 5H8z"/>
                                        <circle cx="9" cy="19" r="1.7"/>
                                        <circle cx="18" cy="19" r="1.7"/>
                                    </svg>
                                    <span>Compras</span>
                                    <svg class="cotz-nav__chev" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2">
                                        <path d="M9 6l6 6-6 6"/>
                                    </svg>
                                </summary>
                                <div class="cotz-nav__submenu">
                                    @if(Auth::user()->hasRole('admin'))
                                        <a href="{{ url('/pedidos') }}" class="cotz-nav__sublink"><span>Pedido Solicitado</span></a>
                                    @endif
                                    <a href="{{ url('/recepciones') }}" class="cotz-nav__sublink"><span>Ver Mis Solicitudes</span></a>
                                    <a href="{{ url('/recepciones/timeline') }}" class="cotz-nav__sublink"><span>Historial Global</span></a>
                                </div>
                            </details>
                        </li>

                        <li>
                            <a href="{{ route('prestamos.index') }}" class="cotz-nav__link">
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                    <rect x="4" y="6" width="16" height="12" rx="2"/>
                                    <path d="M8 10h5M8 14h3"/>
                                    <path d="M14 9l2-2 2 2M16 7v5"/>
                                </svg>
                                <span>Préstamos</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/internal-assets') }}" class="cotz-nav__link">
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M7 21h10"/>
                                    <path d="M9 21V7.2c0-.6.2-1.1.6-1.5l2.3-2.3c.6-.6 1.6-.6 2.2 0l2.3 2.3c.4.4.6.9.6 1.5V21"/>
                                    <path d="M9 12h10"/>
                                    <path d="M11.5 9.5h5"/>
                                </svg>
                                <span>Activos internos</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/tickets') }}" class="cotz-nav__link">
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M9 6h12"/>
                                    <path d="M9 12h12"/>
                                    <path d="M9 18h12"/>
                                    <path d="M3.5 6l1.2 1.2L7 4.9"/>
                                    <path d="M3.5 12l1.2 1.2L7 10.9"/>
                                    <path d="M3.5 18l1.2 1.2L7 16.9"/>
                                </svg>
                                <span>Tareas</span>
                            </a>
                        </li>
                    @endauth

                    @if(Auth::user()->hasRole('admin'))
                        <li>
                            <a href="{{ url('/transactions') }}" class="cotz-nav__link">
                                <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                    <path d="M7 15l3-4 3 3 4-6"/>
                                    <path d="M10 9V7M10 17v-2"/>
                                </svg>
                                <span>Movimientos de caja</span>
                            </a>
                        </li>

                        <li>
                            <details class="cotz-nav__group">
                                <summary>
                                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" fill="none" stroke-width="1.7">
                                        <circle cx="9" cy="8" r="3"/>
                                        <path d="M3 19a6 6 0 0 1 12 0"/>
                                        <circle cx="17" cy="8" r="2.5"/>
                                        <path d="M17 12a4.5 4.5 0 0 1 4 4.5"/>
                                    </svg>
                                    <span>Usuarios</span>
                                    <svg class="cotz-nav__chev" viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2">
                                        <path d="M9 6l6 6-6 6"/>
                                    </svg>
                                </summary>
                                <div class="cotz-nav__submenu">
                                    <a href="{{ route('users.create') }}" class="cotz-nav__sublink"><span>+ Agregar Usuario</span></a>
                                    <a href="{{ url('/usuarios') }}" class="cotz-nav__sublink"><span>Lista de Usuarios</span></a>
                                    <a href="{{ url('/asistencias/historial') }}" class="cotz-nav__sublink"><span>Reporte Asistencias</span></a>
                                    <a href="{{ route('asistencias.index') }}" class="cotz-nav__sublink"><span>Registrar Asistencias</span></a>
                                </div>
                            </details>
                        </li>
                    @endif

                @endif

            </ul>
        </nav>

        <form action="{{ route('logout') }}" method="POST" class="cotz-logout">
            @csrf
            <button type="submit" class="cotz-btn-logout">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" stroke-width="1.8">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <path d="M16 17l5-5-5-5"/>
                    <path d="M21 12H9"/>
                </svg>
                <span>Cerrar sesión</span>
            </button>
        </form>
    </aside>

    <div id="cotzBackdrop" class="cotz-backdrop" aria-hidden="true"></div>

    <div class="cotz-shell" id="cotzShell">
        <header class="cotz-topbar">
            <button id="cotzBtnOpen" class="cotz-icon-pill" aria-label="Abrir menú">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" stroke-width="2">
                    <path d="M3 6h18M3 12h18M3 18h18"/>
                </svg>
            </button>

            <div class="cotz-topbar__title">@yield('titulo','Panel')</div>

            <div class="cotz-topbar__right">
                <div class="dropdown">
                    <button
                        type="button"
                        class="cotz-notif-btn"
                        id="cotzNotifBtn"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        aria-label="Notificaciones"
                    >
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" fill="none" stroke-width="2">
                            <path d="M15 17h5l-1.3-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.7 1.4L4 17h5"/>
                            <path d="M9 21h6"/>
                        </svg>

                        <span id="cotzNotifDot" class="cotz-notif-dot" style="{{ $unreadCount > 0 ? '' : 'display:none;' }}"></span>
                        <span id="cotzNotifBadge" class="cotz-notif-badge" style="{{ $unreadCount > 0 ? '' : 'display:none;' }}">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end cotz-notif-menu" aria-labelledby="cotzNotifBtn">
                        <div class="cotz-notif-head">
                            <div>
                                <div class="cotz-notif-title">
                                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" fill="none" stroke-width="2">
                                        <path d="M15 17h5l-1.3-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.7 1.4L4 17h5"/>
                                        <path d="M9 21h6"/>
                                    </svg>
                                    Notificaciones
                                </div>
                                <div class="cotz-notif-sub" id="cotzNotifSub">
                                    @if($unreadCount > 0)
                                        {{ $unreadCount }} nueva{{ $unreadCount === 1 ? '' : 's' }}
                                    @else
                                        Sin novedades
                                    @endif
                                </div>
                            </div>

                            <form method="POST" action="{{ route('notifications.readAll') }}" id="formReadAllNotifications" class="m-0" style="{{ $unreadCount > 0 ? '' : 'display:none;' }}">
                                @csrf
                                <button type="submit" class="cotz-notif-action">Marcar leídas</button>
                            </form>
                        </div>

                        <div id="cotzNotifBody">
                            @if($unreadCount === 0)
                                <div class="cotz-notif-empty">
                                    <div class="cotz-notif-empty-ico">
                                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" stroke-width="2">
                                            <path d="M15 17h5l-1.3-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.7 1.4L4 17h5"/>
                                            <path d="M9 21h6"/>
                                        </svg>
                                    </div>
                                    <h6>No tienes notificaciones</h6>
                                    <p>Cuando llegue una, aparecerá aquí.</p>
                                </div>
                            @else
                                <div class="cotz-notif-list" id="cotzNotifList">
                                    @foreach($notifications as $notification)
                                        @php
                                            $data = $notification->data ?? [];
                                            $title = $data['title'] ?? 'Notificación';
                                            $msg   = $data['message'] ?? '';
                                            $url   = url('/agenda');

                                            if (!empty($data['url'])) {
                                                $url = $data['url'];
                                            } else {
                                                $rn = $data['routeName'] ?? null;
                                                $rp = $data['routeParams'] ?? [];
                                                if ($rn && \Illuminate\Support\Facades\Route::has($rn)) {
                                                    $url = route($rn, $rp);
                                                }
                                            }
                                        @endphp

                                        <button type="button"
                                                class="cotz-notif-row cotz-notif-item"
                                                data-id="{{ $notification->id }}"
                                                data-url="{{ $url }}">
                                            <div class="cotz-notif-ico">
                                                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" fill="none" stroke-width="2">
                                                    <path d="M8 2v3M16 2v3"/>
                                                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                                                    <path d="M3 10h18"/>
                                                    <path d="M8 14h3"/>
                                                </svg>
                                            </div>

                                            <div class="cotz-notif-body">
                                                <div class="cotz-notif-line1">{{ $title }}</div>
                                                @if($msg)
                                                    <div class="cotz-notif-line2">{{ $msg }}</div>
                                                @endif
                                                <div class="cotz-notif-meta">
                                                    <span class="cotz-notif-pill">Nueva</span>
                                                    <span>•</span>
                                                    <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('perfil') }}" style="text-decoration:none;">
                    <div class="cotz-avatar-sm" title="{{ $nm }}">
                        @if($avatarSrc)
                            <img src="{{ $avatarSrc }}" alt="Avatar de {{ $nm }}">
                        @else
                            {{ $ini }}
                        @endif
                    </div>
                </a>
            </div>
        </header>

        @yield('content')
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
    <div id="cotzNotifToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="cotzNotifToastBody" style="font-family:'Quicksand',system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;">
                Nueva notificación
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

@yield('scripts')

<script>
(function(){
    const shell    = document.getElementById('cotzShell');
    const sidebar  = document.getElementById('cotzSidebar');
    const backdrop = document.getElementById('cotzBackdrop');
    const btnOpen  = document.getElementById('cotzBtnOpen');
    const btnClose = document.getElementById('cotzBtnClose');

    let open = false;

    function applyOverlay(){
        backdrop.classList.toggle('cotz-show', open);
        shell.classList.toggle('cotz-dimmed', open);
        document.body.classList.toggle('menu-open', open);
        sidebar.setAttribute('aria-hidden', open ? 'false' : 'true');
    }
    function openSidebar(){
        if(open) return;
        open = true;
        sidebar.classList.add('cotz-open');
        applyOverlay();
    }
    function closeSidebar(){
        if(!open) return;
        open = false;
        sidebar.classList.remove('cotz-open');
        applyOverlay();
    }

    if(btnOpen)  btnOpen.addEventListener('click', openSidebar);
    if(btnClose) btnClose.addEventListener('click', closeSidebar);
    if(backdrop) backdrop.addEventListener('click', closeSidebar);

    window.addEventListener('keydown', e => { if(e.key === 'Escape'){ closeSidebar(); } });

    let startX;
    document.addEventListener('touchstart', e => { startX = e.touches[0].clientX; });
    document.addEventListener('touchmove', e => {
        const currentX = e.touches[0].clientX;
        if(startX > currentX && startX - currentX > 50 && sidebar.classList.contains('cotz-open')){
            closeSidebar();
        }
    });
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const POLL_URL    = "{{ route('notifications.poll') }}";
    const READ_URL    = "{{ route('notifications.read') }}";
    const READALL_URL = "{{ route('notifications.readAll') }}";

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const dot   = document.getElementById('cotzNotifDot');
    const badge = document.getElementById('cotzNotifBadge');
    const sub   = document.getElementById('cotzNotifSub');
    const body  = document.getElementById('cotzNotifBody');
    const readAllForm = document.getElementById('formReadAllNotifications');

    const toastEl   = document.getElementById('cotzNotifToast');
    const toastBody = document.getElementById('cotzNotifToastBody');
    const toast     = toastEl ? new bootstrap.Toast(toastEl, { delay: 4500 }) : null;

    let cache = null;
    let lastCount = Number({{ (int)$unreadCount }});

    function escapeHtml(s){
        return String(s ?? '')
            .replaceAll('&','&amp;')
            .replaceAll('<','&lt;')
            .replaceAll('>','&gt;')
            .replaceAll('"','&quot;')
            .replaceAll("'","&#039;");
    }

    function setCount(n){
        if (!dot || !badge || !sub) return;

        if (n > 0) {
            dot.style.display = '';
            badge.style.display = '';
            badge.textContent = n > 99 ? '99+' : String(n);
            sub.textContent = `${n} nueva${n === 1 ? '' : 's'}`;
            if (readAllForm) readAllForm.style.display = '';
        } else {
            dot.style.display = 'none';
            badge.style.display = 'none';
            sub.textContent = 'Sin novedades';
            if (readAllForm) readAllForm.style.display = 'none';
        }
    }

    function renderEmpty(){
        if (!body) return;
        body.innerHTML = `
            <div class="cotz-notif-empty">
                <div class="cotz-notif-empty-ico">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" fill="none" stroke-width="2">
                        <path d="M15 17h5l-1.3-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.7 1.4L4 17h5"/>
                        <path d="M9 21h6"/>
                    </svg>
                </div>
                <h6>No tienes notificaciones</h6>
                <p>Cuando llegue una, aparecerá aquí.</p>
            </div>
        `;
    }

    function renderList(items){
        if (!body) return;

        const oldList = document.getElementById('cotzNotifList');
        const prevScroll = oldList ? oldList.scrollTop : 0;

        body.innerHTML = `
            <div class="cotz-notif-list" id="cotzNotifList">
                ${items.map(it => `
                    <button type="button"
                            class="cotz-notif-row cotz-notif-item"
                            data-id="${escapeHtml(it.id)}"
                            data-url="${escapeHtml(it.url || '#')}">
                        <div class="cotz-notif-ico">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" fill="none" stroke-width="2">
                                <path d="M8 2v3M16 2v3"/>
                                <rect x="3" y="5" width="18" height="16" rx="2"/>
                                <path d="M3 10h18"/>
                                <path d="M8 14h3"/>
                            </svg>
                        </div>
                        <div class="cotz-notif-body">
                            <div class="cotz-notif-line1">${escapeHtml(it.title || 'Notificación')}</div>
                            ${it.message ? `<div class="cotz-notif-line2">${escapeHtml(it.message)}</div>` : ``}
                            <div class="cotz-notif-meta">
                                <span class="cotz-notif-pill">Nueva</span>
                                <span>•</span>
                                <span>${escapeHtml(it.time || '')}</span>
                            </div>
                        </div>
                    </button>
                `).join('')}
            </div>
        `;

        const newList = document.getElementById('cotzNotifList');
        if (newList) newList.scrollTop = prevScroll;
    }

    function sameData(a, b){
        return JSON.stringify(a) === JSON.stringify(b);
    }

    async function poll(){
        try{
            const r = await fetch(POLL_URL, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            const ct = r.headers.get('content-type') || '';
            if (!r.ok || !ct.includes('application/json')) {
                throw new Error('poll_not_json_or_not_ok');
            }

            const data  = await r.json();
            const count = Number(data.unreadCount || 0);
            const items = Array.isArray(data.items) ? data.items : [];

            const nextCache = { unreadCount: count, items };
            if (cache && sameData(cache, nextCache)) return;

            if (count > lastCount && toast && items[0]) {
                toastEl.classList.remove('text-bg-success','text-bg-danger','text-bg-warning');
                toastEl.classList.add('text-bg-dark');
                toastBody.textContent = `${items[0].title || 'Nueva notificación'}${items[0].message ? ' — ' + items[0].message : ''}`;
                toast.show();
            }

            lastCount = count;
            cache = nextCache;

            setCount(count);
            if (count === 0) renderEmpty();
            else renderList(items);

        }catch(e){
        }
    }

    document.addEventListener('click', function(e){
        const btn = e.target.closest('.cotz-notif-item');
        if (!btn) return;

        e.preventDefault();

        const id  = btn.getAttribute('data-id');
        const url = btn.getAttribute('data-url') || '#';

        if (id) {
            fetch(READ_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ id })
            }).catch(()=>{}).finally(() => {
                if (url && url !== '#') window.location.href = url;
            });
        } else {
            if (url && url !== '#') window.location.href = url;
        }
    });

    if (readAllForm) {
        readAllForm.addEventListener('submit', function(e){
            e.preventDefault();

            fetch(READALL_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            }).catch(()=>{}).finally(() => {
                poll();
            });
        });
    }

    poll();
    setInterval(poll, 3000);

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) poll();
    });
});
</script>

</body>
</html>