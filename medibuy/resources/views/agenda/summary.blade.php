@extends('layouts.app')
@section('title', 'Resumen de Agenda')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">

<div id="agenda-summary">
    <style>
        :root,
        #agenda-summary {
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

            --indigo: #4338ca;
            --emerald: #059669;
            --violet: #7c3aed;
            --rose: #e11d48;
            --sky: #0284c7;
            --amber: #d97706;
        }

        #agenda-summary,
        #agenda-summary * {
            box-sizing: border-box;
            font-family: 'Quicksand', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        #agenda-summary {
            min-height: calc(100vh - 80px);
            background: var(--bg);
            padding: 20px;
            color: var(--ink);
            font-size: 13px;
        }

        #agenda-summary .agenda-layout {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            max-width: 1280px;
            margin: 0 auto;
        }

        #agenda-summary .agenda-main {
            flex: 1;
            min-width: 0;
        }

        #agenda-summary .wrap {
            max-width: 1280px;
            margin: 0 auto;
        }

        #agenda-summary .topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }

        #agenda-summary .title {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: var(--ink-dark);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        #agenda-summary .sub {
            margin-top: 5px;
            font-size: 14px;
            font-weight: 500;
            color: var(--muted);
        }

        #agenda-summary .actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        #agenda-summary .btn {
            min-height: 38px;
            padding: 0 18px;
            border-radius: 999px;
            border: none;
            outline: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease, border-color .2s ease;
        }

        #agenda-summary .btn:active {
            transform: scale(.98);
        }

        #agenda-summary .btn.primary {
            background: var(--blue);
            color: #fff;
            box-shadow: 0 8px 18px rgba(0, 122, 255, .18);
        }

        #agenda-summary .btn.primary:hover {
            background: #006ce4;
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(0, 122, 255, .22);
        }

        #agenda-summary .btn.secondary {
            background: var(--card);
            color: var(--ink-dark);
            border: 1px solid var(--line);
        }

        #agenda-summary .btn.secondary:hover {
            background: var(--input-bg);
        }

        #agenda-summary .btn.ai {
            background: var(--card);
            color: var(--blue);
            border: 1px solid rgba(0, 122, 255, .18);
            box-shadow: 0 4px 12px rgba(0,0,0,.02);
        }

        #agenda-summary .btn.ai:hover {
            background: var(--blue-soft);
            transform: translateY(-1px);
        }

        #agenda-summary .btn.danger-ghost {
            margin-right: auto;
            background: transparent;
            color: var(--danger);
            box-shadow: none;
        }

        #agenda-summary .btn.danger-ghost:hover {
            background: var(--danger-soft);
        }

        #agenda-summary .grid {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(320px, 1fr);
            gap: 20px;
            align-items: start;
        }

        #agenda-summary .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,.02);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        #agenda-summary .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 34px rgba(15,23,42,.06);
        }

        #agenda-summary .card-head {
            padding: 18px 20px;
            border-bottom: 1px solid var(--line);
            font-size: 16px;
            font-weight: 700;
            color: var(--ink-dark);
        }

        #agenda-summary .list {
            display: flex;
            flex-direction: column;
        }

        #agenda-summary .event-row {
            display: grid;
            grid-template-columns: 26px 4px minmax(0, 1fr) auto;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
            cursor: pointer;
            transition: background .2s ease;
        }

        #agenda-summary .event-row:last-child {
            border-bottom: none;
        }

        #agenda-summary .event-row:hover {
            background: var(--input-bg);
        }

        #agenda-summary .check {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            border: 2px solid var(--line);
            background: var(--card);
            cursor: pointer;
            transition: all .2s ease;
            position: relative;
            outline: none;
        }

        #agenda-summary .check.done {
            border-color: var(--success);
            background: var(--success);
            box-shadow: 0 8px 16px rgba(21,128,61,.22);
        }

        #agenda-summary .check.done::after {
            content: "";
            position: absolute;
            left: 8px;
            top: 4px;
            width: 6px;
            height: 12px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        #agenda-summary .bar {
            width: 4px;
            height: 34px;
            border-radius: 999px;
        }

        #agenda-summary .meta {
            min-width: 0;
        }

        #agenda-summary .event-title {
            margin-bottom: 5px;
            font-size: 15px;
            font-weight: 700;
            color: var(--ink-dark);
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #agenda-summary .event-sub {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
        }

        #agenda-summary .event-sub span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        #agenda-summary .event-sub svg {
            width: 15px;
            height: 15px;
            color: var(--muted-light);
            stroke-width: 2.4;
        }

        #agenda-summary .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        #agenda-summary .badge.urgent {
            background: var(--danger-soft);
            color: var(--danger);
        }

        #agenda-summary .today-body {
            padding: 20px;
        }

        #agenda-summary .empty,
        #agenda-summary .today-empty {
            padding: 42px 20px;
            color: var(--muted-light);
            text-align: center;
            font-size: 13px;
            font-weight: 600;
        }

        #agenda-summary .timeline {
            position: relative;
            padding-left: 22px;
        }

        #agenda-summary .timeline::before {
            content: "";
            position: absolute;
            left: 5px;
            top: 10px;
            bottom: 0;
            width: 2px;
            background: var(--line);
            border-radius: 999px;
        }

        #agenda-summary .timeline-item {
            position: relative;
            padding-bottom: 20px;
            cursor: pointer;
            transition: transform .2s ease;
        }

        #agenda-summary .timeline-item:hover {
            transform: translateX(2px);
        }

        #agenda-summary .timeline-item:last-child {
            padding-bottom: 0;
        }

        #agenda-summary .timeline-dot {
            position: absolute;
            left: -22px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 999px;
            box-shadow: 0 0 0 3px var(--card), 0 0 0 4px var(--line);
        }

        #agenda-summary .timeline-time {
            margin-bottom: 5px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        #agenda-summary .timeline-title {
            color: var(--ink-dark);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.2;
        }

        #agenda-summary .timeline-loc {
            margin-top: 3px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        #agenda-summary .c-indigo { background: var(--indigo); }
        #agenda-summary .c-emerald { background: var(--emerald); }
        #agenda-summary .c-violet { background: var(--violet); }
        #agenda-summary .c-rose { background: var(--rose); }
        #agenda-summary .c-sky { background: var(--sky); }
        #agenda-summary .c-amber { background: var(--amber); }

        #agenda-summary .overlay {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(15,23,42,.42);
            backdrop-filter: blur(4px);
            z-index: 9999;
            padding: 16px;
        }

        #agenda-summary .custom-modal {
            width: min(480px, 100%);
            max-height: 90vh;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,.25);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
            animation: summaryPremiumFade .22s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        #agenda-summary .custom-modal.ai-modal {
            width: min(560px, 100%);
        }

        @keyframes summaryPremiumFade {
            from {
                opacity: 0;
                transform: translateY(15px) scale(.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        #agenda-summary .x {
            width: 30px;
            height: 30px;
            border: none;
            background: transparent;
            border-radius: 8px;
            color: var(--muted);
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background .2s ease, color .2s ease;
        }

        #agenda-summary .x:hover {
            background: var(--input-bg);
            color: var(--ink-dark);
        }

        #agenda-summary .custom-top-bar {
            position: absolute;
            inset: 0 0 auto 0;
            height: 6px;
            background: var(--blue);
            border-radius: 16px 16px 0 0;
        }

        #agenda-summary .show-content {
            padding: 36px 32px 10px;
            overflow-y: auto !important;
        }

        #agenda-summary .show-content::-webkit-scrollbar,
        #agenda-summary .custom-modal-body::-webkit-scrollbar,
        #agenda-summary .chips::-webkit-scrollbar,
        #agenda-summary .custom-select-options::-webkit-scrollbar {
            width: 5px;
        }

        #agenda-summary .show-content::-webkit-scrollbar-thumb,
        #agenda-summary .custom-modal-body::-webkit-scrollbar-thumb,
        #agenda-summary .chips::-webkit-scrollbar-thumb,
        #agenda-summary .custom-select-options::-webkit-scrollbar-thumb {
            background: var(--line);
            border-radius: 999px;
        }

        #agenda-summary .show-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
        }

        #agenda-summary .show-check {
            width: 30px;
            height: 30px;
            margin-top: 3px;
            border-radius: 9px;
            border: 2px solid var(--line);
            background: var(--card);
            flex-shrink: 0;
            cursor: pointer;
            position: relative;
            transition: all .2s ease;
        }

        #agenda-summary .show-check.done {
            border-color: var(--success);
            background: var(--success);
            box-shadow: 0 8px 18px rgba(21,128,61,.22);
        }

        #agenda-summary .show-check.done::after {
            content: "";
            position: absolute;
            left: 9px;
            top: 5px;
            width: 7px;
            height: 13px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        #agenda-summary .show-title-wrapper {
            flex: 1;
            min-width: 0;
        }

        #agenda-summary .show-title-wrapper h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: var(--ink-dark);
            line-height: 1.2;
        }

        #agenda-summary .show-desc {
            margin-top: 7px;
            font-size: 15px;
            color: var(--muted);
            line-height: 1.5;
            font-weight: 500;
        }

        #agenda-summary .show-details-box {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 20px 22px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        #agenda-summary .detail-row {
            display: flex;
            align-items: center;
            gap: 14px;
            color: var(--ink-dark);
            font-size: 15px;
            font-weight: 600;
        }

        #agenda-summary .detail-row svg {
            width: 20px;
            height: 20px;
            color: var(--muted);
            stroke-width: 2;
            flex-shrink: 0;
        }

        #agenda-summary .show-badges {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-top: 22px;
        }

        #agenda-summary .show-badge {
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }

        #agenda-summary .show-badge.general {
            background: #f1f5f9;
            color: var(--ink-dark);
        }

        #agenda-summary .show-badge.warning {
            background: var(--warning-soft);
            color: var(--warning);
        }

        #agenda-summary .show-badge.danger {
            background: var(--danger-soft);
            color: var(--danger);
        }

        #agenda-summary .show-badge.info {
            background: var(--blue-soft);
            color: var(--blue);
        }

        #agenda-summary .show-foot {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding: 16px 32px 32px;
            background: var(--card);
            flex-shrink: 0;
        }

        #agenda-summary .btn-pill {
            min-height: 44px;
            padding: 0 24px;
            border-radius: 999px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s ease;
        }

        #agenda-summary .btn-pill:active {
            transform: scale(.98);
        }

        #agenda-summary .btn-outline {
            background: var(--card);
            color: var(--ink-dark);
            border: 1px solid var(--line);
        }

        #agenda-summary .btn-outline:hover {
            background: var(--input-bg);
        }

        #agenda-summary .btn-blue {
            background: var(--blue);
            color: #fff;
        }

        #agenda-summary .btn-blue:hover {
            background: #006ce4;
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(0,122,255,.25);
        }

        #agenda-summary .custom-modal-head {
            padding: 20px 24px 16px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        #agenda-summary .custom-modal-head h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--ink-dark);
        }

        #agenda-summary .custom-modal-body {
            padding: 20px 24px;
            overflow-y: auto !important;
            flex: 1;
        }

        #agenda-summary .custom-modal-foot {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding: 16px 24px;
            border-top: 1px solid var(--line);
            background: var(--input-bg);
            flex-shrink: 0;
        }

        #agenda-summary .field {
            margin-bottom: 16px;
        }

        #agenda-summary .field label {
            display: block;
            margin-bottom: 7px;
            font-size: 12px;
            font-weight: 700;
            color: var(--ink-dark);
        }

        #agenda-summary .input,
        #agenda-summary .select,
        #agenda-summary .textarea {
            width: 100%;
            height: 42px;
            border: 1px solid var(--line);
            background: var(--input-bg);
            border-radius: 8px;
            padding: 0 12px;
            color: var(--ink);
            font-size: 13px;
            font-weight: 600;
            outline: none;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        #agenda-summary .textarea {
            min-height: 86px;
            resize: vertical;
            padding: 11px 12px;
        }

        #agenda-summary .textarea.ai-textarea {
            min-height: 150px;
            font-size: 14px;
            line-height: 1.5;
        }

        #agenda-summary .input:focus,
        #agenda-summary .textarea:focus {
            border-color: var(--blue);
            background: var(--card);
            box-shadow: 0 0 0 3px var(--blue-soft);
        }

        #agenda-summary .input::placeholder,
        #agenda-summary .textarea::placeholder {
            color: var(--muted-light);
        }

        #agenda-summary .custom-select-target {
            display: none !important;
        }

        #agenda-summary .custom-select-wrapper {
            position: relative;
            width: 100%;
        }

        #agenda-summary .custom-select-trigger {
            width: 100%;
            height: 42px;
            background: var(--input-bg);
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 0 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            cursor: pointer;
            color: var(--ink);
            font-size: 13px;
            font-weight: 600;
            transition: all .2s ease;
        }

        #agenda-summary .custom-select-trigger:hover,
        #agenda-summary .custom-select-wrapper.open .custom-select-trigger {
            border-color: var(--blue);
            background: var(--card);
            box-shadow: 0 0 0 3px var(--blue-soft);
        }

        #agenda-summary .custom-select-options {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 16px 36px rgba(15,23,42,.14);
            z-index: 10020;
            max-height: 190px;
            overflow-y: auto;
            padding: 5px;
            opacity: 0;
            visibility: hidden;
            transform: scale(.98) translateY(-4px);
            transform-origin: top center;
            transition: all .18s ease;
        }

        #agenda-summary .custom-select-wrapper.open .custom-select-options {
            opacity: 1;
            visibility: visible;
            transform: scale(1) translateY(0);
        }

        #agenda-summary .custom-select-option {
            padding: 9px 11px;
            border-radius: 8px;
            color: var(--ink);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s ease, color .15s ease;
        }

        #agenda-summary .custom-select-option:hover {
            background: var(--blue-soft);
            color: var(--blue);
        }

        #agenda-summary .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        #agenda-summary .colors {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        #agenda-summary .color {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            border: none;
            cursor: pointer;
            position: relative;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        #agenda-summary .color:hover {
            transform: scale(1.12);
        }

        #agenda-summary .color.active {
            box-shadow: 0 0 0 3px var(--card), 0 0 0 5px var(--blue-soft);
        }

        #agenda-summary .switch-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #agenda-summary .switch {
            width: 42px;
            height: 24px;
            border-radius: 999px;
            background: var(--line);
            border: none;
            position: relative;
            cursor: pointer;
            transition: background .25s ease;
        }

        #agenda-summary .switch::after {
            content: "";
            position: absolute;
            top: 3px;
            left: 3px;
            width: 18px;
            height: 18px;
            border-radius: 999px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(15,23,42,.12);
            transition: left .25s ease;
        }

        #agenda-summary .switch.active {
            background: var(--success);
        }

        #agenda-summary .switch.active::after {
            left: 21px;
        }

        #agenda-summary .chips {
            margin-top: 10px;
            max-height: 128px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        #agenda-summary .chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: var(--card);
        }

        #agenda-summary .chip-avatar {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            background: var(--blue-soft);
            color: var(--blue);
            border: 1px solid rgba(0,122,255,.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            flex-shrink: 0;
        }

        #agenda-summary .chip-meta {
            flex: 1;
            min-width: 0;
        }

        #agenda-summary .chip-name {
            font-size: 12px;
            font-weight: 700;
            color: var(--ink-dark);
            line-height: 1.1;
        }

        #agenda-summary .chip-sub {
            margin-top: 2px;
            font-size: 10px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #agenda-summary .chip-remove {
            width: 26px;
            height: 26px;
            border: none;
            background: transparent;
            border-radius: 7px;
            color: var(--muted-light);
            cursor: pointer;
            font-size: 17px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        #agenda-summary .chip-remove:hover {
            background: var(--danger-soft);
            color: var(--danger);
        }

        #agenda-summary .ai-intro {
            margin: -4px 0 16px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
            font-weight: 500;
        }

        #agenda-summary .ai-examples {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        #agenda-summary .ai-example {
            border: 1px solid var(--line);
            background: var(--card);
            color: var(--muted);
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all .2s ease;
        }

        #agenda-summary .ai-example:hover {
            background: var(--blue-soft);
            color: var(--blue);
            border-color: rgba(0,122,255,.18);
            transform: translateY(-1px);
        }

        #agenda-summary .ai-status {
            display: none;
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: var(--blue-soft);
            color: var(--blue);
            font-size: 13px;
            font-weight: 700;
        }

        #agenda-summary .ai-status.error {
            background: var(--danger-soft);
            color: var(--danger);
        }

        #agenda-confirm-overlay {
            z-index: 10000;
        }

        #agenda-summary .confirm-box {
            width: min(400px, 100%);
            padding: 32px;
            border-radius: 16px;
            background: var(--card);
            border: 1px solid var(--line);
            box-shadow: 0 25px 50px -12px rgba(0,0,0,.25);
        }

        #agenda-summary .confirm-title {
            margin-bottom: 9px;
            font-size: 24px;
            font-weight: 700;
            color: var(--ink-dark);
            line-height: 1.2;
        }

        #agenda-summary .confirm-desc {
            margin-bottom: 26px;
            font-size: 15px;
            line-height: 1.5;
            color: var(--muted);
            font-weight: 500;
        }

        #agenda-summary .confirm-desc strong {
            color: var(--ink-dark);
            font-weight: 700;
        }

        #agenda-summary .confirm-foot {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        #agenda-summary .btn-pill-danger {
            background: var(--danger);
            color: #fff;
            box-shadow: 0 8px 16px rgba(239,68,68,.3);
        }

        #agenda-summary .btn-pill-danger:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        @media (max-width: 1080px) {
            #agenda-summary .grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            #agenda-summary {
                padding: 12px;
            }

            #agenda-summary .agenda-layout {
                flex-direction: column;
            }

            #agenda-summary .agenda-main {
                width: 100%;
            }

            #agenda-summary .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            #agenda-summary .actions,
            #agenda-summary .actions .btn {
                width: 100%;
            }

            #agenda-summary .grid-2 {
                grid-template-columns: 1fr;
            }

            #agenda-summary .event-row {
                grid-template-columns: 24px 4px minmax(0, 1fr);
            }

            #agenda-summary .badge {
                grid-column: 3;
                justify-self: start;
                margin-top: 4px;
            }

            #agenda-summary .overlay {
                padding: 0;
                align-items: flex-end;
            }

            #agenda-summary .custom-modal {
                width: 100%;
                max-height: 95vh;
                border-radius: 16px 16px 0 0;
                border-bottom: none;
            }

            #agenda-summary .show-foot,
            #agenda-summary .custom-modal-foot {
                padding: 16px 20px 20px;
                flex-direction: column-reverse;
            }

            #agenda-summary .btn-pill,
            #agenda-summary .custom-modal-foot .btn {
                width: 100%;
            }

            #agenda-summary .confirm-box {
                margin: 16px;
                padding: 28px;
            }

            #agenda-summary .confirm-foot {
                flex-direction: column-reverse;
            }

            #agenda-summary .confirm-foot .btn {
                width: 100%;
            }
        }
    </style>

    <div class="agenda-layout">
        @include('agenda.partials.sidebar')

        <div class="agenda-main">
            <div class="wrap">
                <div class="topbar">
                    <div>
                        <h1 class="title">Resumen de agenda</h1>
                        <div class="sub">Consulta próximos eventos y la agenda programada para hoy.</div>
                    </div>

                    <div class="actions">
                        <button type="button" id="btn-ai-event" class="btn ai">✨ Crear con IA</button>
                        <button type="button" id="btn-new-event" class="btn primary">＋ Nuevo evento</button>
                    </div>
                </div>

                <div class="grid">
                    <div class="card">
                        <div class="card-head">Próximos eventos</div>
                        <div id="upcoming-list" class="list">
                            <div class="empty">Cargando eventos...</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">Agenda de hoy</div>
                        <div id="today-list" class="today-body">
                            <div class="today-empty">Cargando agenda de hoy...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL IA --}}
    <div id="agenda-ai-overlay" class="overlay">
        <div class="custom-modal ai-modal">
            <div class="custom-modal-head">
                <h3>Crear evento con IA</h3>
                <button type="button" id="ai-close" class="x">×</button>
            </div>

            <div class="custom-modal-body">
                <p class="ai-intro">
                    Escribe o pega lo que necesitas agendar. La IA llenará automáticamente título,
                    fecha, hora, prioridad, categoría, color, recordatorio, ubicación y notas.
                </p>

                <div class="field">
                    <label>Instrucción</label>
                    <textarea
                        id="ai-prompt"
                        class="textarea ai-textarea"
                        placeholder="Ejemplo: Agenda mañana a las 10 am una reunión urgente con sistemas para revisar APIs, recordarme 30 minutos antes, ubicación sala de juntas."
                    ></textarea>

                    <div class="ai-examples">
                        <button type="button" class="ai-example" data-example="Agenda mañana a las 10 am una reunión urgente con sistemas para revisar APIs, recordarme 30 minutos antes, ubicación sala de juntas.">Sistemas mañana</button>
                        <button type="button" class="ai-example" data-example="Crear evento hoy a las 4 pm para seguimiento con cliente de ventas, prioridad media, recordarme 15 minutos antes.">Ventas hoy</button>
                        <button type="button" class="ai-example" data-example="Programar mañana todo el día inventario de almacén, prioridad alta, recordarme 1 día antes.">Inventario</button>
                    </div>

                    <div id="ai-status" class="ai-status"></div>
                </div>
            </div>

            <div class="custom-modal-foot">
                <button type="button" id="ai-cancel" class="btn secondary">Cancelar</button>
                <button type="button" id="ai-generate" class="btn primary">Llenar campos</button>
            </div>
        </div>
    </div>

    {{-- MODAL CONFIRMAR ELIMINACIÓN --}}
    <div id="agenda-confirm-overlay" class="overlay">
        <div class="confirm-box">
            <div class="confirm-title">Eliminar evento</div>
            <div class="confirm-desc">
                ¿Estás seguro de que deseas eliminar <strong>este evento</strong>? Esta acción es permanente.
            </div>
            <div class="confirm-foot">
                <button type="button" id="btn-confirm-cancel" class="btn secondary">Cancelar</button>
                <button type="button" id="btn-confirm-delete" class="btn btn-pill-danger">Eliminar</button>
            </div>
        </div>
    </div>

    {{-- MODAL VER EVENTO --}}
    <div id="agenda-show-overlay" class="overlay">
        <div class="custom-modal">
            <div class="custom-top-bar" id="show-top-bar"></div>

            <div class="show-content">
                <div class="show-header">
                    <button type="button" class="show-check" id="show-check"></button>

                    <div class="show-title-wrapper">
                        <h3 id="show-title">Evento</h3>
                        <div class="show-desc" id="show-desc"></div>
                    </div>

                    <button type="button" id="show-close-x" class="x">×</button>
                </div>

                <div class="show-details-box">
                    <div class="detail-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span id="show-date">Fecha</span>
                    </div>

                    <div class="detail-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                        <span id="show-time">Hora</span>
                    </div>

                    <div class="detail-row" id="show-location-row" style="display:none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        <span id="show-location">Ubicación</span>
                    </div>

                    <div class="detail-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        <span id="show-reminder">Recordatorio</span>
                    </div>
                </div>

                <div class="show-badges">
                    <span class="show-badge general" id="show-category">General</span>
                    <span class="show-badge" id="show-priority">Prioridad</span>
                </div>
            </div>

            <div class="show-foot">
                <button type="button" id="btn-show-close" class="btn-pill btn-outline">Cerrar</button>
                <button type="button" id="btn-show-edit" class="btn-pill btn-blue">Editar evento</button>
            </div>
        </div>
    </div>

    {{-- MODAL FORMULARIO --}}
    <div id="agenda-summary-overlay" class="overlay">
        <div class="custom-modal">
            <div class="custom-modal-head">
                <h3 id="summary-modal-title">Nuevo evento</h3>
                <button type="button" id="summary-close" class="x">×</button>
            </div>

            <div class="custom-modal-body">
                <form id="summary-form">
                    @csrf

                    <input type="hidden" id="ev-id">
                    <input type="hidden" id="ev-color" value="indigo">
                    <input type="hidden" id="ev-completed" value="0">
                    <input type="hidden" id="ev-all-day" value="0">

                    <div class="field">
                        <label>Título</label>
                        <input id="ev-title" class="input" type="text" required>
                    </div>

                    <div class="field">
                        <label>Color</label>
                        <div class="colors" id="color-picker">
                            <button type="button" class="color c-indigo active" data-color="indigo"></button>
                            <button type="button" class="color c-emerald" data-color="emerald"></button>
                            <button type="button" class="color c-violet" data-color="violet"></button>
                            <button type="button" class="color c-rose" data-color="rose"></button>
                            <button type="button" class="color c-sky" data-color="sky"></button>
                            <button type="button" class="color c-amber" data-color="amber"></button>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label>Fecha</label>
                            <input id="ev-date" class="input" type="date" required>
                        </div>

                        <div class="field">
                            <label>Categoría</label>
                            <select id="ev-category" class="select custom-select-target">
                                <option value="administracion">Administración</option>
                                <option value="sistemas">Sistemas</option>
                                <option value="almacen">Almacén</option>
                                <option value="contabilidad">Contabilidad</option>
                                <option value="logistica">Logística</option>
                                <option value="ventas">Ventas</option>
                                <option value="general" selected>General</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <div class="switch-row">
                            <button type="button" id="all-day-switch" class="switch"></button>
                            <span style="font-size:13px;font-weight:700;color:var(--ink-dark);">Todo el día</span>
                        </div>
                    </div>

                    <div class="grid-2" id="time-grid">
                        <div class="field">
                            <label>Hora inicio</label>
                            <input id="ev-start-time" class="input" type="time" value="09:00">
                        </div>

                        <div class="field">
                            <label>Hora fin</label>
                            <input id="ev-end-time" class="input" type="time" value="10:00">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label>Recordatorio</label>
                            <select id="ev-offset" class="select custom-select-target">
                                <option value="5">5 minutos antes</option>
                                <option value="15" selected>15 minutos antes</option>
                                <option value="30">30 minutos antes</option>
                                <option value="60">1 hora antes</option>
                                <option value="1440">1 día antes</option>
                            </select>
                        </div>

                        <div class="field">
                            <label>Prioridad</label>
                            <select id="ev-priority" class="select custom-select-target">
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label>Ubicación</label>
                        <input id="ev-location" class="input" type="text" placeholder="Agregar ubicación">
                    </div>

                    <div class="field">
                        <label>Invitados</label>
                        <select id="ev-users" class="select custom-select-target" size="5"></select>

                        <div id="users-error" style="display:none;margin-top:6px;color:var(--danger);font-size:12px;font-weight:700;">
                            Selecciona al menos un usuario.
                        </div>

                        <div id="ev-chips" class="chips"></div>
                        <input type="hidden" id="ev-user-ids" value="[]">
                    </div>

                    <div class="field" style="margin-bottom:0;">
                        <label>Notas</label>
                        <textarea id="ev-notes" class="textarea" placeholder="Notas adicionales..."></textarea>
                    </div>
                </form>
            </div>

            <div class="custom-modal-foot">
                <button type="button" id="btn-delete" class="btn danger-ghost" style="display:none;">Eliminar</button>
                <button type="button" id="btn-cancel" class="btn secondary">Cancelar</button>
                <button type="button" id="btn-save" class="btn primary">Guardar</button>
            </div>
        </div>
    </div>

    <script>
        window.agendaSummaryRoutes = {
            feed: @json(route('agenda.feed')),
            users: @json(route('agenda.users')),
            store: @json(route('agenda.store')),
            calendar: @json(route('agenda.calendar')),
            base: @json(url('/agenda')),
            aiParse: @json(\Illuminate\Support\Facades\Route::has('agenda.ai.parse') ? route('agenda.ai.parse') : url('/agenda/ai/parse'))
        };
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const routes = window.agendaSummaryRoutes;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const upcomingList = document.getElementById('upcoming-list');
            const todayList = document.getElementById('today-list');

            const overlay = document.getElementById('agenda-summary-overlay');
            const btnNew = document.getElementById('btn-new-event');
            const btnClose = document.getElementById('summary-close');
            const btnCancel = document.getElementById('btn-cancel');
            const btnSave = document.getElementById('btn-save');
            const btnDelete = document.getElementById('btn-delete');
            const modalTitle = document.getElementById('summary-modal-title');

            const btnAiEvent = document.getElementById('btn-ai-event');
            const aiOverlay = document.getElementById('agenda-ai-overlay');
            const aiClose = document.getElementById('ai-close');
            const aiCancel = document.getElementById('ai-cancel');
            const aiGenerate = document.getElementById('ai-generate');
            const aiPrompt = document.getElementById('ai-prompt');
            const aiStatus = document.getElementById('ai-status');

            const confirmOverlay = document.getElementById('agenda-confirm-overlay');
            const btnConfirmCancel = document.getElementById('btn-confirm-cancel');
            const btnConfirmDelete = document.getElementById('btn-confirm-delete');

            const showOverlay = document.getElementById('agenda-show-overlay');
            const btnShowCloseX = document.getElementById('show-close-x');
            const btnShowClose = document.getElementById('btn-show-close');
            const btnShowEdit = document.getElementById('btn-show-edit');

            const showCheck = document.getElementById('show-check');
            const showTopBar = document.getElementById('show-top-bar');
            const showTitle = document.getElementById('show-title');
            const showDesc = document.getElementById('show-desc');
            const showDate = document.getElementById('show-date');
            const showTime = document.getElementById('show-time');
            const showLocationRow = document.getElementById('show-location-row');
            const showLocation = document.getElementById('show-location');
            const showReminder = document.getElementById('show-reminder');
            const showCategory = document.getElementById('show-category');
            const showPriority = document.getElementById('show-priority');

            const usersSelect = document.getElementById('ev-users');
            const usersError = document.getElementById('users-error');
            const chipsWrap = document.getElementById('ev-chips');
            const userIdsHidden = document.getElementById('ev-user-ids');
            const allDaySwitch = document.getElementById('all-day-switch');
            const timeGrid = document.getElementById('time-grid');
            const colorButtons = [...document.querySelectorAll('#color-picker .color')];

            const f = {
                id: document.getElementById('ev-id'),
                title: document.getElementById('ev-title'),
                color: document.getElementById('ev-color'),
                date: document.getElementById('ev-date'),
                category: document.getElementById('ev-category'),
                allDay: document.getElementById('ev-all-day'),
                startTime: document.getElementById('ev-start-time'),
                endTime: document.getElementById('ev-end-time'),
                offset: document.getElementById('ev-offset'),
                priority: document.getElementById('ev-priority'),
                location: document.getElementById('ev-location'),
                notes: document.getElementById('ev-notes'),
                completed: document.getElementById('ev-completed')
            };

            let USERS_CACHE = [];
            let selectedIds = new Set();
            let rawEvents = [];
            let currentEditingEvent = null;

            const COLOR_CLASS = {
                indigo: 'c-indigo',
                emerald: 'c-emerald',
                violet: 'c-violet',
                rose: 'c-rose',
                sky: 'c-sky',
                amber: 'c-amber'
            };

            const HEX_COLORS = {
                indigo: '#4338ca',
                emerald: '#059669',
                violet: '#7c3aed',
                rose: '#e11d48',
                sky: '#0284c7',
                amber: '#d97706'
            };

            const ICON_TIME = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            `;

            const ICON_PIN = `
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
            `;

            function buildCustomSelects() {
                document.querySelectorAll('.custom-select-target:not(.initialized)').forEach(select => {
                    select.classList.add('initialized');

                    const wrapper = document.createElement('div');
                    wrapper.className = 'custom-select-wrapper';

                    select.parentNode.insertBefore(wrapper, select);
                    wrapper.appendChild(select);

                    const trigger = document.createElement('div');
                    trigger.className = 'custom-select-trigger';
                    trigger.innerHTML = `
                        <span class="text"></span>
                        <svg width="14" height="14" fill="none" stroke="var(--muted-light)" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    `;

                    const optionsWrap = document.createElement('div');
                    optionsWrap.className = 'custom-select-options';

                    function syncUI() {
                        optionsWrap.innerHTML = '';

                        Array.from(select.options).forEach(opt => {
                            const optionEl = document.createElement('div');
                            optionEl.className = 'custom-select-option';
                            optionEl.textContent = opt.textContent;

                            if (opt.disabled) {
                                optionEl.style.opacity = '.4';
                                optionEl.style.pointerEvents = 'none';
                            }

                            optionEl.addEventListener('click', e => {
                                e.stopPropagation();

                                if (opt.disabled) return;

                                select.value = opt.value;
                                trigger.querySelector('.text').textContent = opt.textContent;
                                wrapper.classList.remove('open');
                                select.dispatchEvent(new Event('change', { bubbles: true }));
                            });

                            optionsWrap.appendChild(optionEl);
                        });

                        const selectedOpt = select.options[select.selectedIndex];
                        trigger.querySelector('.text').textContent = selectedOpt ? selectedOpt.textContent : 'Selecciona...';
                    }

                    select._syncCustomSelect = syncUI;
                    syncUI();

                    trigger.addEventListener('click', e => {
                        e.stopPropagation();

                        document.querySelectorAll('.custom-select-wrapper').forEach(w => {
                            if (w !== wrapper) w.classList.remove('open');
                        });

                        wrapper.classList.toggle('open');
                    });

                    wrapper.appendChild(trigger);
                    wrapper.appendChild(optionsWrap);
                });
            }

            document.addEventListener('click', () => {
                document.querySelectorAll('.custom-select-wrapper').forEach(w => w.classList.remove('open'));
            });

            function syncCustomSelect(select) {
                if (select && typeof select._syncCustomSelect === 'function') {
                    select._syncCustomSelect();
                }
            }

            function forceSyncCustomSelects() {
                document.querySelectorAll('.custom-select-target.initialized').forEach(select => {
                    syncCustomSelect(select);
                });
            }

            buildCustomSelects();

            function pad(n) {
                return String(n).padStart(2, '0');
            }

            function formatDateInput(date) {
                const d = new Date(date);
                return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
            }

            function formatTimeInput(date) {
                const d = new Date(date);
                return `${pad(d.getHours())}:${pad(d.getMinutes())}`;
            }

            function initials(name = '') {
                const parts = String(name).trim().split(/\s+/).filter(Boolean);
                return ((parts[0]?.[0] || '') + (parts[1]?.[0] || '')).toUpperCase() || 'U';
            }

            function escapeHtml(str = '') {
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function isTodayDate(d) {
                const n = new Date();

                return d.getFullYear() === n.getFullYear()
                    && d.getMonth() === n.getMonth()
                    && d.getDate() === n.getDate();
            }

            function isTomorrowDate(d) {
                const n = new Date();
                n.setHours(0, 0, 0, 0);
                n.setDate(n.getDate() + 1);

                return d.getFullYear() === n.getFullYear()
                    && d.getMonth() === n.getMonth()
                    && d.getDate() === n.getDate();
            }

            function formatDateLabel(d) {
                if (isTodayDate(d)) return 'Hoy';
                if (isTomorrowDate(d)) return 'Mañana';

                const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                const months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

                return `${days[d.getDay()]}, ${d.getDate()} ${months[d.getMonth()]}`;
            }

            function formatFullDateES(d) {
                return new Intl.DateTimeFormat('es-MX', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                }).format(d).replace(/^./, s => s.toUpperCase());
            }

            function formatHour(d) {
                return new Intl.DateTimeFormat('es-MX', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }).format(d);
            }

            function formatReminderText(minutes) {
                minutes = Number(minutes || 0);

                if (!minutes) return 'Sin recordatorio';
                if (minutes === 5) return '5 min antes';
                if (minutes === 15) return '15 min antes';
                if (minutes === 30) return '30 min antes';
                if (minutes === 60) return '1 hora antes';
                if (minutes === 1440) return '1 día antes';

                return `${minutes} min antes`;
            }

            function setColor(color) {
                f.color.value = color || 'indigo';

                colorButtons.forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.color === f.color.value);
                });
            }

            function setAllDay(value) {
                const checked = !!value;

                f.allDay.value = checked ? '1' : '0';
                allDaySwitch.classList.toggle('active', checked);
                timeGrid.style.display = checked ? 'none' : 'grid';
            }

            function syncHiddenUsers() {
                userIdsHidden.value = JSON.stringify(Array.from(selectedIds));
            }

            function renderChips() {
                chipsWrap.innerHTML = '';

                Array.from(selectedIds).forEach(id => {
                    const user = USERS_CACHE.find(u => Number(u.id) === Number(id));
                    if (!user) return;

                    const chip = document.createElement('div');
                    chip.className = 'chip';
                    chip.innerHTML = `
                        <div class="chip-avatar">${initials(user.name)}</div>
                        <div class="chip-meta">
                            <div class="chip-name">${escapeHtml(user.name || '')}</div>
                            <div class="chip-sub">${escapeHtml([user.email, user.phone].filter(Boolean).join(' • '))}</div>
                        </div>
                        <button type="button" class="chip-remove">×</button>
                    `;

                    chip.querySelector('.chip-remove').addEventListener('click', () => {
                        selectedIds.delete(Number(id));

                        const opt = usersSelect.querySelector(`option[value="${id}"]`);
                        if (opt) opt.disabled = false;

                        renderChips();
                        syncHiddenUsers();
                        syncCustomSelect(usersSelect);
                    });

                    chipsWrap.appendChild(chip);
                });
            }

            function setSelectedUserIds(ids) {
                selectedIds = new Set((ids || []).map(v => Number(v)).filter(Boolean));

                [...usersSelect.options].forEach(opt => {
                    if (opt.value) opt.disabled = false;
                });

                selectedIds.forEach(id => {
                    const opt = usersSelect.querySelector(`option[value="${id}"]`);
                    if (opt) opt.disabled = true;
                });

                renderChips();
                syncHiddenUsers();
                syncCustomSelect(usersSelect);
            }

            function addUserToSelection(id) {
                id = Number(id);

                if (!id || selectedIds.has(id)) return;

                selectedIds.add(id);

                const opt = usersSelect.querySelector(`option[value="${id}"]`);
                if (opt) {
                    opt.disabled = true;
                    usersSelect.value = '';
                }

                usersError.style.display = 'none';
                renderChips();
                syncHiddenUsers();
                syncCustomSelect(usersSelect);
            }

            usersSelect.addEventListener('change', () => addUserToSelection(usersSelect.value));

            async function loadUsersOnce() {
                if (USERS_CACHE.length) return;

                usersSelect.innerHTML = '<option value="" disabled selected>Cargando usuarios...</option>';
                syncCustomSelect(usersSelect);

                try {
                    const res = await fetch(routes.users, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) throw new Error('Error usuarios');

                    const json = await res.json();
                    USERS_CACHE = Array.isArray(json) ? json : (Array.isArray(json?.data) ? json.data : []);

                    usersSelect.innerHTML = '<option value="" disabled selected>Selecciona un usuario...</option>';

                    USERS_CACHE.forEach(user => {
                        const opt = document.createElement('option');
                        opt.value = user.id;
                        opt.textContent = user.name || user.email || 'Sin nombre';
                        usersSelect.appendChild(opt);
                    });

                    syncCustomSelect(usersSelect);
                } catch (e) {
                    console.error(e);
                    usersSelect.innerHTML = '<option value="" disabled selected>Error al cargar usuarios</option>';
                    syncCustomSelect(usersSelect);
                }
            }

            function resetForm(date = null) {
                f.id.value = '';
                f.title.value = '';
                f.color.value = 'indigo';
                f.date.value = date ? formatDateInput(date) : formatDateInput(new Date());
                f.category.value = 'general';
                f.startTime.value = '09:00';
                f.endTime.value = '10:00';
                f.offset.value = '15';
                f.priority.value = 'media';
                f.location.value = '';
                f.notes.value = '';
                f.completed.value = '0';

                setColor('indigo');
                setAllDay(false);
                setSelectedUserIds([]);
                usersError.style.display = 'none';
                forceSyncCustomSelects();
            }

            function openModal(mode = 'new', ev = null) {
                overlay.style.display = 'flex';
                document.body.style.overflow = 'hidden';

                if (mode === 'new') {
                    modalTitle.textContent = 'Nuevo evento';
                    btnSave.textContent = 'Guardar';
                    btnDelete.style.display = 'none';
                    resetForm();
                    return;
                }

                const ex = ev.extendedProps || {};
                const start = ev.start ? new Date(ev.start) : new Date();
                const end = ev.end ? new Date(ev.end) : new Date(start.getTime() + 60 * 60 * 1000);

                modalTitle.textContent = 'Editar evento';
                btnSave.textContent = 'Guardar cambios';
                btnDelete.style.display = 'inline-flex';

                f.id.value = ev.id || '';
                f.title.value = ev.title || '';
                f.date.value = formatDateInput(start);
                f.category.value = ex.category || 'general';
                f.startTime.value = formatTimeInput(start);
                f.endTime.value = formatTimeInput(end);
                f.offset.value = String(ex.remind_offset_minutes ?? 15);
                f.priority.value = ex.priority || 'media';
                f.location.value = ex.location || '';
                f.notes.value = ex.notes || ex.description || '';
                f.completed.value = ex.completed ? '1' : '0';

                setColor(ex.color || 'indigo');
                setAllDay(!!ev.allDay);
                setSelectedUserIds(ex.user_ids || []);
                usersError.style.display = 'none';
                forceSyncCustomSelects();
            }

            function closeModal() {
                overlay.style.display = 'none';

                if (
                    confirmOverlay.style.display !== 'flex' &&
                    showOverlay.style.display !== 'flex' &&
                    aiOverlay.style.display !== 'flex'
                ) {
                    document.body.style.overflow = '';
                }
            }

            function openShowModal(ev) {
                currentEditingEvent = ev;

                const ex = ev.extendedProps || {};
                const start = ev.start ? new Date(ev.start) : new Date();
                const end = ev.end ? new Date(ev.end) : null;
                const baseColor = HEX_COLORS[ex.color || 'indigo'] || HEX_COLORS.indigo;

                showTopBar.style.background = baseColor;
                showTitle.textContent = ev.title || 'Sin título';
                showDesc.textContent = ex.notes || ex.description || 'Sin notas';

                showCheck.classList.toggle('done', !!ex.completed);
                showDate.textContent = formatFullDateES(start);
                showTime.textContent = ev.allDay ? 'Todo el día' : `${formatHour(start)}${end ? ` — ${formatHour(end)}` : ''}`;

                if (ex.location) {
                    showLocationRow.style.display = 'flex';
                    showLocation.textContent = ex.location;
                } else {
                    showLocationRow.style.display = 'none';
                    showLocation.textContent = '';
                }

                showReminder.textContent = formatReminderText(ex.remind_offset_minutes);
                showCategory.textContent = ex.category ? String(ex.category).charAt(0).toUpperCase() + String(ex.category).slice(1) : 'General';

                showPriority.className = 'show-badge';

                const prio = ex.priority || 'media';

                if (prio === 'baja') {
                    showPriority.textContent = 'Prioridad Baja';
                    showPriority.classList.add('info');
                } else if (prio === 'media') {
                    showPriority.textContent = 'Prioridad Media';
                    showPriority.classList.add('warning');
                } else {
                    showPriority.textContent = 'Prioridad Alta';
                    showPriority.classList.add('danger');
                }

                showOverlay.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeShowModal() {
                showOverlay.style.display = 'none';

                if (
                    overlay.style.display !== 'flex' &&
                    confirmOverlay.style.display !== 'flex' &&
                    aiOverlay.style.display !== 'flex'
                ) {
                    document.body.style.overflow = '';
                }
            }

            function openAiModal() {
                aiOverlay.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                aiStatus.style.display = 'none';
                aiStatus.classList.remove('error');
                setTimeout(() => aiPrompt.focus(), 80);
            }

            function closeAiModal() {
                aiOverlay.style.display = 'none';

                if (
                    overlay.style.display !== 'flex' &&
                    showOverlay.style.display !== 'flex' &&
                    confirmOverlay.style.display !== 'flex'
                ) {
                    document.body.style.overflow = '';
                }
            }

            function showAiStatus(message, isError = false) {
                aiStatus.textContent = message;
                aiStatus.classList.toggle('error', !!isError);
                aiStatus.style.display = 'block';
            }

            function normalizeTime(value, fallback) {
                if (!value) return fallback;

                const clean = String(value).trim();

                if (/^\d{2}:\d{2}$/.test(clean)) return clean;

                if (/^\d{1}:\d{2}$/.test(clean)) return `0${clean}`;

                return fallback;
            }

            function normalizeAiPayload(payload = {}) {
                const today = formatDateInput(new Date());

                return {
                    title: payload.title || payload.titulo || payload.name || 'Evento generado con IA',
                    date: /^\d{4}-\d{2}-\d{2}$/.test(String(payload.date || '')) ? payload.date : today,
                    start_time: normalizeTime(payload.start_time || payload.startTime, '09:00'),
                    end_time: normalizeTime(payload.end_time || payload.endTime, '10:00'),
                    all_day: !!payload.all_day || !!payload.allDay,
                    category: payload.category || 'general',
                    priority: payload.priority || 'media',
                    remind_offset_minutes: Number(payload.remind_offset_minutes || payload.reminder || 15),
                    location: payload.location || '',
                    notes: payload.notes || '',
                    color: payload.color || 'indigo'
                };
            }

            function localAiParse(prompt) {
                const clean = String(prompt || '').trim();
                const text = clean.toLowerCase();
                const now = new Date();

                let date = new Date();

                if (text.includes('pasado mañana')) {
                    date.setDate(now.getDate() + 2);
                } else if (text.includes('mañana')) {
                    date.setDate(now.getDate() + 1);
                }

                let start = '09:00';
                let end = '10:00';

                const time24 = text.match(/\b([01]?\d|2[0-3]):([0-5]\d)\b/);
                const time12 = text.match(/\b([1-9]|1[0-2])\s*(am|pm)\b/);

                if (time24) {
                    start = `${String(time24[1]).padStart(2, '0')}:${time24[2]}`;
                } else if (time12) {
                    let hour = Number(time12[1]);
                    const ampm = time12[2];

                    if (ampm === 'pm' && hour < 12) hour += 12;
                    if (ampm === 'am' && hour === 12) hour = 0;

                    start = `${String(hour).padStart(2, '0')}:00`;
                }

                const endDate = new Date(`2000-01-01T${start}:00`);
                endDate.setHours(endDate.getHours() + 1);
                end = `${String(endDate.getHours()).padStart(2, '0')}:${String(endDate.getMinutes()).padStart(2, '0')}`;

                const allDay = text.includes('todo el día') || text.includes('todo el dia');

                let category = 'general';
                if (text.includes('sistema') || text.includes('software') || text.includes('api') || text.includes('soporte')) category = 'sistemas';
                if (text.includes('almacén') || text.includes('almacen') || text.includes('inventario')) category = 'almacen';
                if (text.includes('contabilidad') || text.includes('factura') || text.includes('pago')) category = 'contabilidad';
                if (text.includes('logística') || text.includes('logistica') || text.includes('entrega')) category = 'logistica';
                if (text.includes('venta') || text.includes('cliente')) category = 'ventas';
                if (text.includes('administración') || text.includes('administracion') || text.includes('junta')) category = 'administracion';

                let priority = 'media';
                if (text.includes('urgente') || text.includes('importante') || text.includes('alta prioridad')) priority = 'alta';
                if (text.includes('baja prioridad') || text.includes('sin urgencia')) priority = 'baja';

                let reminder = 15;
                if (text.includes('5 minutos')) reminder = 5;
                if (text.includes('30 minutos')) reminder = 30;
                if (text.includes('1 hora') || text.includes('una hora')) reminder = 60;
                if (text.includes('1 día') || text.includes('un día') || text.includes('un dia')) reminder = 1440;

                return {
                    title: clean.split(/[.,\n]/)[0].slice(0, 90) || 'Evento generado con IA',
                    date: formatDateInput(date),
                    start_time: allDay ? '09:00' : start,
                    end_time: allDay ? '18:00' : end,
                    all_day: allDay,
                    category,
                    priority,
                    remind_offset_minutes: reminder,
                    location: '',
                    notes: clean,
                    color: priority === 'alta' ? 'rose' : 'indigo'
                };
            }

            function applyAiToForm(data) {
                const payload = normalizeAiPayload(data);

                if (overlay.style.display !== 'flex') {
                    openModal('new');
                }

                f.title.value = payload.title;
                f.date.value = payload.date;
                f.startTime.value = payload.start_time;
                f.endTime.value = payload.end_time;
                f.category.value = payload.category;
                f.priority.value = payload.priority;
                f.offset.value = String(payload.remind_offset_minutes);
                f.location.value = payload.location;
                f.notes.value = payload.notes;

                setColor(payload.color);
                setAllDay(payload.all_day);

                forceSyncCustomSelects();
                closeAiModal();
            }

            async function generateEventWithAi() {
                const prompt = aiPrompt.value.trim();

                if (!prompt) {
                    showAiStatus('Escribe primero qué evento quieres crear.', true);
                    return;
                }

                try {
                    aiGenerate.disabled = true;
                    aiGenerate.textContent = 'Interpretando...';
                    showAiStatus('La IA está llenando los campos...');

                    const res = await fetch(routes.aiParse, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            prompt,
                            timezone: 'America/Mexico_City',
                            today: formatDateInput(new Date())
                        })
                    });

                    if (!res.ok) {
                        const fallback = localAiParse(prompt);
                        applyAiToForm(fallback);
                        return;
                    }

                    const json = await res.json();
                    applyAiToForm(json);
                } catch (e) {
                    console.error(e);
                    const fallback = localAiParse(prompt);
                    applyAiToForm(fallback);
                } finally {
                    aiGenerate.disabled = false;
                    aiGenerate.textContent = 'Llenar campos';
                }
            }

            showCheck.addEventListener('click', async () => {
                if (!currentEditingEvent) return;

                try {
                    await toggleComplete(currentEditingEvent);
                    await loadSummary();

                    const updated = rawEvents.find(ev => String(ev.id) === String(currentEditingEvent.id));

                    if (updated) {
                        currentEditingEvent = updated;
                        showCheck.classList.toggle('done', !!updated.extendedProps.completed);
                    }
                } catch (err) {
                    console.error(err);
                    alert('No se pudo actualizar el estado del evento.');
                }
            });

            btnShowCloseX.addEventListener('click', closeShowModal);
            btnShowClose.addEventListener('click', closeShowModal);

            showOverlay.addEventListener('click', e => {
                if (e.target === showOverlay) closeShowModal();
            });

            btnShowEdit.addEventListener('click', () => {
                closeShowModal();

                loadUsersOnce().then(() => {
                    if (currentEditingEvent) openModal('edit', currentEditingEvent);
                });
            });

            btnAiEvent.addEventListener('click', openAiModal);
            aiClose.addEventListener('click', closeAiModal);
            aiCancel.addEventListener('click', closeAiModal);

            aiOverlay.addEventListener('click', e => {
                if (e.target === aiOverlay) closeAiModal();
            });

            aiGenerate.addEventListener('click', generateEventWithAi);

            document.querySelectorAll('.ai-example').forEach(btn => {
                btn.addEventListener('click', () => {
                    aiPrompt.value = btn.dataset.example || '';
                    aiPrompt.focus();
                });
            });

            btnNew.addEventListener('click', async () => {
                await loadUsersOnce();
                openModal('new');
            });

            btnClose.addEventListener('click', closeModal);
            btnCancel.addEventListener('click', closeModal);

            overlay.addEventListener('click', e => {
                if (e.target === overlay) closeModal();
            });

            colorButtons.forEach(btn => {
                btn.addEventListener('click', () => setColor(btn.dataset.color));
            });

            allDaySwitch.addEventListener('click', () => {
                setAllDay(f.allDay.value !== '1');
            });

            function buildPayload() {
                let userIds = [];

                try {
                    userIds = JSON.parse(userIdsHidden.value || '[]');
                } catch (e) {
                    userIds = [];
                }

                if (!userIds.length) {
                    usersError.style.display = 'block';
                    return null;
                }

                const isAllDay = f.allDay.value === '1';

                const startAt = isAllDay
                    ? `${f.date.value}T00:00:00`
                    : `${f.date.value}T${f.startTime.value}:00`;

                const endAt = isAllDay
                    ? `${f.date.value}T23:59:00`
                    : `${f.date.value}T${f.endTime.value}:00`;

                return {
                    title: f.title.value,
                    description: f.notes.value || '',
                    start_at: new Date(startAt).toISOString(),
                    end_at: new Date(endAt).toISOString(),
                    remind_offset_minutes: parseInt(f.offset.value || '15', 10),
                    repeat_rule: 'none',
                    user_ids: userIds,
                    send_email: 1,
                    send_whatsapp: 1,
                    timezone: 'America/Mexico_City',
                    all_day: isAllDay ? 1 : 0,
                    completed: parseInt(f.completed.value || '0', 10),
                    color: f.color.value || 'indigo',
                    category: f.category.value || 'general',
                    priority: f.priority.value || 'media',
                    location: f.location.value || '',
                    notes: f.notes.value || ''
                };
            }

            btnSave.addEventListener('click', async () => {
                const payload = buildPayload();

                if (!payload) return;

                try {
                    btnSave.disabled = true;
                    btnSave.textContent = 'Guardando...';

                    const url = f.id.value ? `${routes.base}/${f.id.value}` : routes.store;
                    const method = f.id.value ? 'PUT' : 'POST';

                    const res = await fetch(url, {
                        method,
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify(payload)
                    });

                    if (!res.ok) throw new Error('Error al guardar');

                    closeModal();
                    await loadSummary();
                } catch (e) {
                    console.error(e);
                    alert('No se pudo guardar el evento.');
                } finally {
                    btnSave.disabled = false;
                    btnSave.textContent = f.id.value ? 'Guardar cambios' : 'Guardar';
                }
            });

            btnDelete.addEventListener('click', () => {
                if (!f.id.value) return;

                confirmOverlay.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });

            btnConfirmCancel.addEventListener('click', () => {
                confirmOverlay.style.display = 'none';

                if (
                    overlay.style.display !== 'flex' &&
                    showOverlay.style.display !== 'flex' &&
                    aiOverlay.style.display !== 'flex'
                ) {
                    document.body.style.overflow = '';
                }
            });

            btnConfirmDelete.addEventListener('click', async () => {
                confirmOverlay.style.display = 'none';

                try {
                    btnDelete.disabled = true;
                    btnDelete.textContent = 'Eliminando...';

                    const res = await fetch(`${routes.base}/${f.id.value}`, {
                        method: 'DELETE',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) throw new Error('No se pudo eliminar');

                    closeModal();
                    await loadSummary();
                } catch (e) {
                    console.error(e);
                    alert('No se pudo eliminar el evento.');
                } finally {
                    btnDelete.disabled = false;
                    btnDelete.textContent = 'Eliminar';
                }
            });

            async function toggleComplete(ev) {
                const ex = ev.extendedProps || {};
                const nextCompleted = !Boolean(Number(ex.completed ?? 0));

                const res = await fetch(`${routes.base}/${encodeURIComponent(ev.id)}/toggle-completed`, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        completed: nextCompleted ? 1 : 0
                    })
                });

                if (!res.ok) {
                    let errorText = '';

                    try {
                        const errorJson = await res.json();
                        errorText = errorJson.message || JSON.stringify(errorJson);
                    } catch (e) {
                        errorText = await res.text();
                    }

                    console.error('Error actualizando estado:', res.status, errorText);
                    throw new Error('No se pudo actualizar el estado.');
                }

                const json = await res.json();

                ev.extendedProps.completed = json.completed ? 1 : 0;
                ev.extendedProps.next_reminder_at = json.next_reminder_at || null;

                return json;
            }

            function normalizeEvents(feed) {
                const source = Array.isArray(feed)
                    ? feed
                    : Array.isArray(feed?.events)
                        ? feed.events
                        : Array.isArray(feed?.data)
                            ? feed.data
                            : [];

                return source.map(ev => {
                    const startValue = ev.start || ev.start_at || ev.startAt || null;
                    const endValue = ev.end || ev.end_at || ev.endAt || null;
                    const extendedProps = ev.extendedProps || ev.extended_props || {};

                    return {
                        ...ev,
                        id: ev.id,
                        title: ev.title || ev.name || '',
                        start: startValue ? new Date(startValue) : null,
                        end: endValue ? new Date(endValue) : null,
                        allDay: !!(ev.allDay ?? ev.all_day),
                        extendedProps: {
                            ...extendedProps,
                            completed: Number(extendedProps.completed ?? ev.completed ?? 0),
                            color: extendedProps.color || ev.color || 'indigo',
                            category: extendedProps.category || ev.category || 'general',
                            priority: extendedProps.priority || ev.priority || 'media',
                            location: extendedProps.location || ev.location || '',
                            notes: extendedProps.notes || ev.notes || ev.description || '',
                            description: extendedProps.description || ev.description || ev.notes || '',
                            remind_offset_minutes: extendedProps.remind_offset_minutes ?? ev.remind_offset_minutes ?? 15,
                            repeat_rule: extendedProps.repeat_rule || ev.repeat_rule || 'none',
                            user_ids: extendedProps.user_ids || ev.user_ids || []
                        }
                    };
                }).filter(ev => ev.start && !Number.isNaN(ev.start.getTime()));
            }

            function renderUpcoming(events) {
                const now = new Date();
                now.setHours(0, 0, 0, 0);

                const upcoming = events
                    .filter(ev => ev.start && ev.start >= now && !ev.extendedProps.completed)
                    .sort((a, b) => a.start - b.start);

                if (!upcoming.length) {
                    upcomingList.innerHTML = `<div class="empty">No hay eventos próximos.</div>`;
                    return;
                }

                upcomingList.innerHTML = upcoming.map(ev => {
                    const ex = ev.extendedProps || {};
                    const barClass = COLOR_CLASS[ex.color || 'indigo'] || 'c-indigo';
                    const urgent = ex.priority === 'alta';

                    return `
                        <div class="event-row" data-id="${escapeHtml(ev.id)}">
                            <button type="button" class="check ${ex.completed ? 'done' : ''}" data-check-id="${escapeHtml(ev.id)}"></button>

                            <div class="bar ${barClass}"></div>

                            <div class="meta">
                                <div class="event-title">${escapeHtml(ev.title || '')}</div>

                                <div class="event-sub">
                                    <span>${escapeHtml(formatDateLabel(ev.start))}</span>
                                    ${ev.allDay ? '' : `<span>${ICON_TIME} ${escapeHtml(formatHour(ev.start))}</span>`}
                                    ${ex.location ? `<span>${ICON_PIN} ${escapeHtml(ex.location)}</span>` : ''}
                                </div>
                            </div>

                            ${urgent ? `<div class="badge urgent">Urgente</div>` : `<div></div>`}
                        </div>
                    `;
                }).join('');

                upcomingList.querySelectorAll('.event-row').forEach(row => {
                    row.addEventListener('click', e => {
                        if (e.target.closest('.check')) return;

                        const ev = rawEvents.find(x => String(x.id) === String(row.dataset.id));

                        if (ev) openShowModal(ev);
                    });
                });

                upcomingList.querySelectorAll('.check').forEach(chk => {
                    chk.addEventListener('click', async e => {
                        e.stopPropagation();

                        const ev = rawEvents.find(x => String(x.id) === String(chk.dataset.checkId));

                        if (!ev) return;

                        try {
                            await toggleComplete(ev);
                            await loadSummary();
                        } catch (err) {
                            console.error(err);
                            alert('No se pudo actualizar el evento.');
                        }
                    });
                });
            }

            function renderToday(events) {
                const today = events
                    .filter(ev => ev.start && isTodayDate(ev.start))
                    .sort((a, b) => a.start - b.start);

                if (!today.length) {
                    todayList.innerHTML = `<div class="today-empty">Sin eventos para hoy.</div>`;
                    return;
                }

                todayList.innerHTML = `
                    <div class="timeline">
                        ${today.map(ev => {
                            const ex = ev.extendedProps || {};
                            const dotClass = COLOR_CLASS[ex.color || 'indigo'] || 'c-indigo';

                            return `
                                <div class="timeline-item" data-id="${escapeHtml(ev.id)}">
                                    <div class="timeline-dot ${dotClass}"></div>
                                    <div class="timeline-time">
                                        ${ev.allDay ? 'Todo el día' : `${escapeHtml(formatHour(ev.start))}${ev.end ? ` — ${escapeHtml(formatHour(ev.end))}` : ''}`}
                                    </div>
                                    <div class="timeline-title">${escapeHtml(ev.title || '')}</div>
                                    ${ex.location ? `<div class="timeline-loc">${escapeHtml(ex.location)}</div>` : ''}
                                </div>
                            `;
                        }).join('')}
                    </div>
                `;

                todayList.querySelectorAll('.timeline-item').forEach(item => {
                    item.addEventListener('click', () => {
                        const ev = rawEvents.find(x => String(x.id) === String(item.dataset.id));

                        if (ev) openShowModal(ev);
                    });
                });
            }

            async function loadSummary() {
                try {
                    const res = await fetch(routes.feed, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) throw new Error('Error al cargar agenda');

                    const json = await res.json();

                    rawEvents = normalizeEvents(json);

                    renderUpcoming(rawEvents);
                    renderToday(rawEvents);
                } catch (e) {
                    console.error(e);

                    upcomingList.innerHTML = `<div class="empty">No se pudieron cargar los eventos.</div>`;
                    todayList.innerHTML = `<div class="today-empty">No se pudo cargar la agenda de hoy.</div>`;
                }
            }

            loadSummary();
            loadUsersOnce();
        });
    </script>
</div>
@endsection