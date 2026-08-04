<div class="agenda-side-shell">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap');

        :root {
            --bg: #f4f5f7;
            --card: #ffffff;
            --input-bg: #f9fafb;

            --ink-dark: #0f172a;
            --ink: #334155;
            --muted: #64748b;
            --muted-light: #94a3b8;

            --line: #e2e8f0;

            --blue: #007aff;
            --blue-soft: #eff6ff;

            --success: #15803d;
            --success-soft: #f0fdf4;

            --danger: #ef4444;
            --danger-soft: #fef2f2;

            --warning: #c2410c;
            --warning-soft: #fff7ed;
        }

        .agenda-side-shell,
        .agenda-side-shell * {
            box-sizing: border-box;
            font-family: 'Quicksand', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .agenda-side-shell {
            width: 92px;
            flex: 0 0 92px;
            position: relative;
            z-index: 40;
            overflow: visible;
        }

        .agenda-side-menu {
            position: sticky;
            top: 18px;
            min-height: calc(100vh - 120px);
            padding: 18px 10px;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            overflow: visible;
            z-index: 41;
        }

        .agenda-side-menu:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
            border-color: rgba(0, 122, 255, 0.14);
        }

        @keyframes agendaLogoFloat {
            0% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-2px);
            }
            100% {
                transform: translateY(0);
            }
        }

        @keyframes agendaLogoShine {
            0% {
                transform: translateX(-130%) skewX(-18deg);
                opacity: 0;
            }
            20% {
                opacity: 1;
            }
            45% {
                transform: translateX(180%) skewX(-18deg);
                opacity: 0;
            }
            100% {
                transform: translateX(180%) skewX(-18deg);
                opacity: 0;
            }
        }

        .agenda-side-logo {
            width: 52px;
            height: 52px;
            margin-bottom: 2px;
            border-radius: 14px;
            background: var(--blue-soft);
            border: 1px solid rgba(0, 122, 255, 0.12);
            color: var(--blue);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
            flex-shrink: 0;
        }

        .agenda-side-logo::after {
            content: '';
            position: absolute;
            inset: 0 auto 0 -60%;
            width: 52%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255,255,255,0.78),
                transparent
            );
            animation: agendaLogoShine 4.5s ease-in-out infinite;
            pointer-events: none;
        }

        .agenda-side-logo svg {
            width: 24px;
            height: 24px;
            stroke: currentColor;
            stroke-width: 2.35;
            animation: agendaLogoFloat 3.2s ease-in-out infinite;
            position: relative;
            z-index: 1;
        }

        .agenda-side-divider {
            width: 34px;
            height: 1px;
            background: var(--line);
            border-radius: 999px;
        }

        .agenda-side-nav {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            overflow: visible;
        }

        .agenda-side-link {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--muted);
            background: transparent;
            border: 1px solid transparent;
            position: relative;
            transition:
                color 0.22s ease,
                background 0.22s ease,
                border-color 0.22s ease,
                box-shadow 0.22s ease,
                transform 0.22s ease;
            overflow: visible;
            z-index: 42;
        }

        .agenda-side-link svg {
            width: 23px;
            height: 23px;
            stroke: currentColor;
            stroke-width: 2;
            transition: transform 0.22s ease, stroke-width 0.22s ease;
        }

        .agenda-side-link:hover {
            color: var(--blue);
            background: var(--input-bg);
            border-color: var(--line);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }

        .agenda-side-link:hover svg {
            transform: scale(1.04);
            stroke-width: 2.2;
        }

        .agenda-side-link:active {
            transform: scale(0.98);
        }

        .agenda-side-link.is-active {
            color: var(--blue);
            background: var(--blue-soft);
            border-color: rgba(0, 122, 255, 0.18);
            box-shadow: 0 8px 18px rgba(0, 122, 255, 0.10);
        }

        .agenda-side-link[data-tip]::after {
            content: attr(data-tip);
            position: absolute;
            left: calc(100% + 14px);
            top: 50%;
            transform: translateY(-50%) scale(0.96);
            transform-origin: left center;
            background: var(--ink-dark);
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: -0.01em;
            white-space: nowrap;
            padding: 9px 14px;
            border-radius: 999px;
            opacity: 0;
            pointer-events: none;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.16);
            transition:
                opacity 0.2s ease,
                transform 0.2s ease;
            z-index: 9999;
        }

        .agenda-side-link[data-tip]:hover::after {
            opacity: 1;
            transform: translateY(-50%) scale(1);
        }

        .agenda-side-link:focus-visible {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px var(--blue-soft);
        }

        @media (max-width: 900px) {
            .agenda-side-shell {
                width: 100%;
                flex: 1 1 100%;
            }

            .agenda-side-menu {
                position: relative;
                top: 0;
                width: 100%;
                min-height: auto;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 12px;
                margin-bottom: 14px;
                border-radius: 16px;
                gap: 12px;
            }

            .agenda-side-menu:hover {
                transform: none;
            }

            .agenda-side-logo {
                width: 48px;
                height: 48px;
                min-width: 48px;
                margin-bottom: 0;
                border-radius: 13px;
            }

            .agenda-side-logo svg {
                width: 23px;
                height: 23px;
            }

            .agenda-side-divider {
                display: none;
            }

            .agenda-side-nav {
                width: auto;
                flex-direction: row;
                justify-content: flex-end;
                gap: 10px;
            }

            .agenda-side-link {
                width: 48px;
                height: 48px;
                border-radius: 13px;
            }

            .agenda-side-link svg {
                width: 22px;
                height: 22px;
            }

            .agenda-side-link[data-tip]::after {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .agenda-side-menu {
                padding: 10px;
                border-radius: 14px;
            }

            .agenda-side-logo,
            .agenda-side-link {
                width: 44px;
                height: 44px;
                border-radius: 12px;
            }

            .agenda-side-nav {
                gap: 8px;
            }
        }
    </style>

    <aside class="agenda-side-menu">
        <div class="agenda-side-logo" title="Sistema de Agenda">
            <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                <polyline points="2 12 12 17 22 12"></polyline>
                <polyline points="2 17 12 22 22 17"></polyline>
            </svg>
        </div>

        <div class="agenda-side-divider"></div>

        <nav class="agenda-side-nav">
            <a
                href="{{ route('agenda.calendar') }}"
                class="agenda-side-link {{ request()->routeIs('agenda.calendar') ? 'is-active' : '' }}"
                data-tip="Calendario"
                aria-label="Calendario"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="17" rx="4"></rect>
                    <path d="M8 2v4M16 2v4M3 9h18"></path>
                </svg>
            </a>

            <a
                href="{{ route('agenda.summary') }}"
                class="agenda-side-link {{ request()->routeIs('agenda.summary') ? 'is-active' : '' }}"
                data-tip="Resumen"
                aria-label="Resumen"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 6.5h14"></path>
                    <path d="M5 12h14"></path>
                    <path d="M5 17.5h9"></path>
                </svg>
            </a>
        </nav>
    </aside>
</div>