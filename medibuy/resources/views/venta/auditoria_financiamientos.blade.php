@extends('layouts.app')
@section('title', 'Asistente Financiero IA')
@section('titulo', 'Asistente Financiero IA')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php
$routeChatIA = \Route::has('financiamientos.auditoria.chat')
    ? route('financiamientos.auditoria.chat')
    : url('/financiamientos/auditoria/chat');

$routeStreamIA = \Route::has('financiamientos.auditoria.chat.stream')
    ? route('financiamientos.auditoria.chat.stream')
    : $routeChatIA;

$routePdfIA = \Route::has('financiamientos.auditoria.pdf')
    ? route('financiamientos.auditoria.pdf')
    : url('/financiamientos/auditoria/pdf');
@endphp

<style>
.b44-shell,
.b44-shell * {
    box-sizing: border-box;
}

.b44-shell {
    --b44-bg-1: #cbeffd;
    --b44-bg-2: #f7f6f4;
    --b44-surface: rgba(255, 255, 255, 0.72);
    --b44-surface-strong: rgba(255, 255, 255, 0.92);
    --b44-border: rgba(15, 23, 42, 0.08);
    --b44-border-strong: rgba(15, 23, 42, 0.14);
    --b44-text: #101828;
    --b44-text-soft: #667085;
    --b44-text-muted: #98a2b3;
    --b44-title: #0b1220;
    --b44-orange: #ff6a1a;
    --b44-orange-hover: #f25d0c;
    --b44-shadow-xs: 0 1px 2px rgba(16, 24, 40, 0.04);
    --b44-shadow-sm: 0 10px 25px rgba(16, 24, 40, 0.08);
    --b44-shadow-md: 0 24px 70px rgba(15, 23, 42, 0.10);
    --b44-radius-xl: 34px;
    --b44-radius-lg: 24px;
    --b44-radius-md: 18px;
    --b44-radius-sm: 14px;
    --b44-transition: 280ms cubic-bezier(.2,.8,.2,1);
    color: var(--b44-text);
}

.b44-page {
    position: relative;
    overflow: hidden;
    border-radius: 28px;
    padding: 28px;
    background:
        radial-gradient(circle at 14% 0%, rgba(128, 214, 255, 0.95) 0%, rgba(128, 214, 255, 0.45) 24%, rgba(255,255,255,0) 52%),
        linear-gradient(180deg, var(--b44-bg-1) 0%, var(--b44-bg-2) 42%, #f6f4f2 100%);
    border: 1px solid rgba(255,255,255,0.55);
    box-shadow: var(--b44-shadow-md);
    min-height: calc(100vh - 120px);
}

.b44-page::before,
.b44-page::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    filter: blur(55px);
    opacity: .45;
    pointer-events: none;
}

.b44-page::before {
    width: 260px;
    height: 260px;
    right: -60px;
    top: 14%;
    background: rgba(255,255,255,.55);
}

.b44-page::after {
    width: 220px;
    height: 220px;
    left: -80px;
    bottom: 4%;
    background: rgba(162, 230, 255, .35);
}

.b44-topbar {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-bottom: 26px;
    flex-wrap: wrap;
}

.b44-back {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    border-radius: 999px;
    background: rgba(255,255,255,.55);
    border: 1px solid rgba(255,255,255,.65);
    text-decoration: none;
    color: var(--b44-text);
    font-weight: 500;
    font-size: .92rem;
    backdrop-filter: blur(8px);
    transition: var(--b44-transition);
}

.b44-back:hover {
    transform: translateY(-1px);
    background: rgba(255,255,255,.8);
    color: var(--b44-title);
}

.b44-badge-top {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border-radius: 999px;
    background: rgba(255,255,255,.58);
    border: 1px solid rgba(255,255,255,.65);
    color: var(--b44-text-soft);
    font-size: .85rem;
    font-weight: 500;
    backdrop-filter: blur(10px);
}

.b44-badge-top::before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #12b76a;
    box-shadow: 0 0 0 4px rgba(18, 183, 106, .12);
}

.b44-grid {
    position: relative;
    z-index: 2;
    display: grid;
    grid-template-columns: minmax(480px, 1.05fr) minmax(460px, .95fr);
    gap: 26px;
    align-items: start;
}

.b44-left {
    min-width: 0;
    padding: 12px 12px 12px 8px;
}

.b44-left-inner {
    max-width: 790px;
    margin: 0 auto;
    text-align: center;
}

.b44-kicker {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    border-radius: 999px;
    background: rgba(255,255,255,.62);
    border: 1px solid rgba(255,255,255,.66);
    color: #475467;
    font-size: .84rem;
    font-weight: 500;
    letter-spacing: .02em;
    backdrop-filter: blur(10px);
    margin-bottom: 20px;
}

.b44-kicker::before {
    content: "";
    width: 9px;
    height: 9px;
    border-radius: 999px;
    background: var(--b44-orange);
}

.b44-title {
    font-size: clamp(3.2rem, 7vw, 5.6rem);
    line-height: .96;
    letter-spacing: -0.06em;
    font-weight: 500;
    color: #000;
    margin: 0 0 20px;
}

.b44-subtitle {
    max-width: 800px;
    margin: 0 auto 34px;
    font-size: clamp(1rem, 1.6vw, 1.22rem);
    line-height: 1.6;
    color: #344054;
    font-weight: 400;
}

.b44-composer {
    position: relative;
    width: min(100%, 820px);
    margin: 0 auto;
    padding: 18px;
    border-radius: 28px;
    background: rgba(255,255,255,.58);
    border: 1px solid rgba(255,255,255,.62);
    box-shadow: 0 12px 35px rgba(15, 23, 42, .08);
    backdrop-filter: blur(18px);
}

.b44-composer-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}

.b44-composer-label {
    font-size: .9rem;
    color: #475467;
    font-weight: 500;
    letter-spacing: .02em;
}

.b44-persona {
    min-width: 230px;
    height: 46px;
    border-radius: 16px;
    border: 1px solid rgba(15, 23, 42, .08);
    background: rgba(255,255,255,.86);
    color: #101828;
    padding: 0 14px;
    outline: none;
    box-shadow: none;
    transition: var(--b44-transition);
    font-weight: 500;
}

.b44-persona:focus {
    border-color: rgba(16, 24, 40, .18);
    box-shadow: 0 0 0 4px rgba(16, 24, 40, .05);
}

.b44-input-wrap {
    position: relative;
    border-radius: 24px;
    background: rgba(249, 247, 246, .92);
    border: 1px solid rgba(15, 23, 42, .07);
    padding: 18px 82px 18px 18px;
    min-height: 180px;
    box-shadow: inset 0 1px 0 rgba(255,255,255,.75);
}

.b44-textarea {
    width: 100%;
    min-height: 138px;
    border: none !important;
    background: transparent !important;
    outline: none !important;
    resize: none;
    box-shadow: none !important;
    padding: 0 !important;
    color: #344054;
    font-size: 1.06rem;
    line-height: 1.75;
    font-weight: 400;
}

.b44-textarea::placeholder {
    color: #667085;
}

.b44-send {
    position: absolute;
    right: 16px;
    bottom: 16px;
    width: 54px;
    height: 54px;
    border: none;
    border-radius: 999px;
    background: var(--b44-orange);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 25px rgba(255, 106, 26, .35);
    transition: var(--b44-transition);
    cursor: pointer;
}

.b44-send:hover:not(:disabled) {
    transform: translateY(-2px) scale(1.03);
    background: var(--b44-orange-hover);
}

.b44-send:disabled {
    opacity: .6;
    cursor: not-allowed;
    transform: none;
}

.b44-send svg {
    width: 22px;
    height: 22px;
}

.b44-send-loader {
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255,255,255,.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: b44spin .7s linear infinite;
    display: none;
}

.b44-send.is-loading .b44-send-icon {
    display: none;
}

.b44-send.is-loading .b44-send-loader {
    display: block;
}

.b44-composer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    margin-top: 16px;
    flex-wrap: wrap;
}

.b44-loading {
    display: none;
    align-items: center;
    gap: 10px;
    color: #475467;
    font-size: .92rem;
    font-weight: 500;
}

.b44-loading .dot {
    width: 9px;
    height: 9px;
    border-radius: 999px;
    background: var(--b44-orange);
    box-shadow:
        16px 0 0 rgba(255,106,26,.6),
        32px 0 0 rgba(255,106,26,.3);
    animation: b44pulse 1.1s infinite ease-in-out;
    margin-right: 34px;
}

.b44-tools {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.b44-btn-link,
.b44-btn-ghost {
    height: 42px;
    padding: 0 16px;
    border-radius: 999px;
    border: 1px solid rgba(15, 23, 42, .08);
    background: rgba(255,255,255,.7);
    color: #344054;
    font-weight: 500;
    font-size: .88rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: var(--b44-transition);
}

.b44-btn-link:hover,
.b44-btn-ghost:hover {
    background: rgba(255,255,255,.95);
    transform: translateY(-1px);
    color: #101828;
}

.b44-btn-ghost:disabled {
    opacity: .5;
    cursor: not-allowed;
    transform: none;
}

.b44-hint {
    margin: 48px 0 18px;
    color: #667085;
    font-size: .96rem;
    font-weight: 500;
    letter-spacing: .01em;
    text-transform: uppercase;
}

.b44-suggests {
    width: min(100%, 780px);
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 14px;
}

.b44-chip {
    min-height: 52px;
    padding: 0 24px;
    border-radius: 999px;
    border: 1px solid rgba(15, 23, 42, .11);
    background: rgba(255,255,255,.62);
    color: #101828;
    font-size: 1rem;
    font-weight: 500;
    transition: var(--b44-transition);
    backdrop-filter: blur(10px);
}

.b44-chip:hover {
    transform: translateY(-2px);
    background: rgba(255,255,255,.9);
    box-shadow: var(--b44-shadow-sm);
    border-color: rgba(15, 23, 42, .18);
}

.b44-right {
    min-width: 0;
    position: sticky;
    top: 18px;
    max-width: 100%;
}

.b44-side {
    background: rgba(255,255,255,.64);
    border: 1px solid rgba(255,255,255,.68);
    box-shadow: 0 20px 55px rgba(15, 23, 42, .10);
    border-radius: 30px;
    padding: 18px;
    backdrop-filter: blur(18px);
    min-width: 0;
    width: 100%;
    max-width: 100%;
    overflow: hidden;
}

.b44-side-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    padding: 8px 8px 16px;
    border-bottom: 1px solid rgba(15, 23, 42, .07);
    flex-wrap: wrap;
}

.b44-side-title {
    font-size: 1.08rem;
    font-weight: 600;
    color: #101828;
    margin-bottom: 4px;
}

.b44-side-sub {
    font-size: .9rem;
    color: #667085;
    font-weight: 400;
}

.b44-side-head-right {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.b44-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 40px;
    padding: 0 14px;
    border-radius: 999px;
    font-size: .86rem;
    font-weight: 500;
    letter-spacing: .01em;
    border: 1px solid transparent;
}

.b44-status::before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 999px;
}

.b44-status.idle {
    background: rgba(255,255,255,.82);
    color: #344054;
    border-color: rgba(15, 23, 42, .08);
}

.b44-status.idle::before {
    background: #98a2b3;
}

.b44-status.loading {
    background: rgba(255, 106, 26, .10);
    color: #9a3412;
    border-color: rgba(255, 106, 26, .15);
}

.b44-status.loading::before {
    background: var(--b44-orange);
    animation: b44blink 1s infinite;
}

.b44-status.done {
    background: rgba(18, 183, 106, .10);
    color: #087443;
    border-color: rgba(18, 183, 106, .15);
}

.b44-status.done::before {
    background: #12b76a;
}

.b44-status.error {
    background: rgba(240, 68, 56, .10);
    color: #b42318;
    border-color: rgba(240, 68, 56, .16);
}

.b44-status.error::before {
    background: #f04438;
}

.b44-live {
    display: grid;
    grid-template-columns: 1.3fr .7fr;
    gap: 12px;
    margin: 16px 0 14px;
}

.b44-mini-card {
    border-radius: 22px;
    padding: 16px 18px;
    background: rgba(255,255,255,.86);
    border: 1px solid rgba(15, 23, 42, .07);
    box-shadow: var(--b44-shadow-xs);
    min-height: 84px;
    min-width: 0;
    overflow: hidden;
}

.b44-mini-label {
    font-size: .78rem;
    color: #667085;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 10px;
}

.b44-prompt-mirror {
    color: #101828;
    font-size: .95rem;
    line-height: 1.65;
    white-space: pre-wrap;
    word-break: break-word;
    font-weight: 400;
}

.b44-persona-mirror {
    color: #101828;
    font-size: .95rem;
    font-weight: 500;
    line-height: 1.5;
}

.b44-chat {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-height: 260px;
    overflow-y: auto;
    padding-right: 4px;
    margin-bottom: 14px;
}

.b44-msg {
    max-width: 100%;
    padding: 14px 16px;
    border-radius: 18px;
    font-size: .92rem;
    line-height: 1.65;
    white-space: pre-wrap;
    word-break: break-word;
    animation: b44slide .25s ease;
    font-weight: 400;
}

.b44-msg.user {
    align-self: flex-end;
    background: linear-gradient(180deg, #1f2937 0%, #111827 100%);
    color: #fff;
    border-bottom-right-radius: 8px;
}

.b44-msg.assistant {
    align-self: flex-start;
    background: rgba(255,255,255,.88);
    border: 1px solid rgba(15, 23, 42, .07);
    color: #344054;
    border-bottom-left-radius: 8px;
}

.b44-msg.assistant.pending {
    color: #667085;
    background: rgba(255,255,255,.94);
}

.b44-msg.assistant.pending::after {
    content: " ...";
    animation: b44dots 1.2s infinite;
}

.b44-analysis-viewport {
    max-height: calc(100vh - 430px);
    overflow: auto;
    padding-right: 4px;
}

.b44-analysis {
    display: grid;
    gap: 14px;
    min-width: 0;
}

.b44-empty {
    border-radius: 24px;
    padding: 30px 22px;
    background: rgba(255,255,255,.86);
    border: 1px dashed rgba(15, 23, 42, .12);
    text-align: center;
}

.b44-empty-mark {
    width: 54px;
    height: 54px;
    margin: 0 auto 14px;
    border-radius: 18px;
    background: linear-gradient(180deg, rgba(255,106,26,.12), rgba(255,106,26,.05));
    border: 1px solid rgba(255,106,26,.18);
    display: grid;
    place-items: center;
    color: #c2410c;
    font-size: 1.45rem;
    font-weight: 500;
}

.b44-empty-title {
    color: #101828;
    font-size: 1rem;
    font-weight: 500;
    margin-bottom: 6px;
}

.b44-empty-text {
    color: #667085;
    font-size: .92rem;
    line-height: 1.65;
    font-weight: 400;
}

.b44-summary,
.b44-card,
.b44-table-card,
.b44-chart-card {
    border-radius: 24px;
    padding: 18px;
    background: rgba(255,255,255,.9);
    border: 1px solid rgba(15, 23, 42, .08);
    box-shadow: var(--b44-shadow-xs);
    width: 100%;
    max-width: 100%;
    min-width: 0;
    overflow: hidden;
}

.b44-card-title {
    margin: 0 0 14px;
    color: #101828;
    font-size: .98rem;
    font-weight: 600;
}

.b44-card-sub {
    color: #667085;
    font-size: .84rem;
    margin-bottom: 12px;
    font-weight: 400;
}

.b44-kpis {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    min-width: 0;
}

.b44-kpi {
    border-radius: 20px;
    padding: 16px;
    background: linear-gradient(180deg, rgba(255,255,255,.96), rgba(248,250,252,.96));
    border: 1px solid rgba(15, 23, 42, .07);
    min-width: 0;
    overflow: hidden;
}

.b44-kpi-label {
    font-size: .75rem;
    color: #667085;
    text-transform: uppercase;
    letter-spacing: .06em;
    font-weight: 500;
    margin-bottom: 8px;
}

.b44-kpi-value {
    font-size: 1.22rem;
    font-weight: 600;
    color: #101828;
    line-height: 1.2;
    word-break: break-word;
}

.b44-kpi-detail {
    font-size: .82rem;
    color: #667085;
    line-height: 1.5;
    margin-top: 8px;
    font-weight: 400;
}

.b44-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    min-width: 0;
}

.b44-list {
    margin: 0;
    padding-left: 18px;
    color: #344054;
}

.b44-list li {
    margin-bottom: 8px;
    line-height: 1.6;
    font-weight: 400;
    word-break: break-word;
}

.b44-bottleneck {
    padding: 14px 0;
    border-bottom: 1px solid rgba(15, 23, 42, .08);
}

.b44-bottleneck:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.b44-bottleneck:first-child {
    padding-top: 0;
}

.b44-bottleneck-title {
    color: #101828;
    font-weight: 500;
    margin-bottom: 6px;
}

.b44-bottleneck-impact {
    color: #667085;
    font-size: .88rem;
    margin-bottom: 6px;
    font-weight: 400;
}

.b44-table-wrap {
    width: 100%;
    max-width: 100%;
    overflow-x: auto;
    overflow-y: hidden;
    border: 1px solid rgba(15, 23, 42, .08);
    border-radius: 18px;
}

.b44-table {
    width: 100%;
    min-width: 580px;
    border-collapse: collapse;
    font-size: .9rem;
}

.b44-table th,
.b44-table td {
    padding: 12px 14px;
    border-bottom: 1px solid rgba(15, 23, 42, .07);
    text-align: left;
    vertical-align: top;
}

.b44-table th {
    background: rgba(248,250,252,.95);
    color: #667085;
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .05em;
    font-weight: 500;
    white-space: nowrap;
}

.b44-table td {
    color: #344054;
    background: rgba(255,255,255,.96);
    font-weight: 400;
    white-space: normal;
    word-break: break-word;
}

.b44-table tr:last-child td {
    border-bottom: none;
}

.b44-charts {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
    min-width: 0;
}

.b44-chart-box {
    position: relative;
    min-height: 260px;
    width: 100%;
    max-width: 100%;
    overflow: hidden;
}

.b44-chart-box canvas {
    display: block;
    width: 100% !important;
    max-width: 100% !important;
}

.b44-skeleton {
    position: relative;
    overflow: hidden;
}

.b44-skeleton::after {
    content: "";
    position: absolute;
    inset: 0;
    transform: translateX(-100%);
    background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,.58) 50%, rgba(255,255,255,0) 100%);
    animation: b44shimmer 1.35s infinite;
}

.b44-skeleton-line {
    height: 12px;
    border-radius: 999px;
    background: rgba(15, 23, 42, .08);
    margin-bottom: 10px;
}

.b44-skeleton-line.lg { height: 16px; width: 68%; }
.b44-skeleton-line.md { width: 90%; }
.b44-skeleton-line.sm { width: 52%; }

@keyframes b44spin {
    to { transform: rotate(360deg); }
}

@keyframes b44pulse {
    0%, 100% { transform: translateX(0); opacity: 1; }
    50% { transform: translateX(3px); opacity: .7; }
}

@keyframes b44blink {
    0%, 100% { opacity: 1; }
    50% { opacity: .45; }
}

@keyframes b44slide {
    from { opacity: 0; transform: translateY(7px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes b44shimmer {
    100% { transform: translateX(100%); }
}

@keyframes b44dots {
    0%   { content: " ."; }
    33%  { content: " .."; }
    66%  { content: " ..."; }
    100% { content: " ."; }
}

@media (max-width: 1399px) {
    .b44-grid {
        grid-template-columns: minmax(420px, 1fr) minmax(400px, .92fr);
    }
}

@media (max-width: 1199px) {
    .b44-grid {
        grid-template-columns: 1fr;
    }

    .b44-right {
        position: relative;
        top: auto;
    }

    .b44-live,
    .b44-grid-2,
    .b44-kpis {
        grid-template-columns: 1fr;
    }

    .b44-left-inner {
        max-width: 100%;
    }

    .b44-composer {
        width: 100%;
    }

    .b44-analysis-viewport {
        max-height: none;
        overflow: visible;
    }
}

@media (max-width: 767px) {
    .b44-page {
        border-radius: 20px;
        padding: 18px;
    }

    .b44-title {
        font-size: 2.7rem;
    }

    .b44-subtitle {
        font-size: .98rem;
        margin-bottom: 22px;
    }

    .b44-input-wrap {
        min-height: 160px;
        padding: 16px 72px 16px 16px;
    }

    .b44-textarea {
        min-height: 120px;
        font-size: .98rem;
    }

    .b44-composer-bottom,
    .b44-composer-top,
    .b44-side-head {
        flex-direction: column;
        align-items: stretch;
    }

    .b44-persona {
        width: 100%;
        min-width: 0;
    }

    .b44-chip {
        width: 100%;
        padding: 12px 18px;
    }

    .b44-suggests {
        width: 100%;
    }

    .b44-chat {
        max-height: 240px;
    }
}
</style>

<div class="container-fluid px-0 b44-shell">
    <div class="b44-page">
        @if($isAdmin)
            <div class="b44-topbar">
                <a href="{{ url('/ventas/deudores') }}" class="b44-back">
                    <span>←</span>
                    <span>Volver a financiamientos</span>
                </a>

               
            </div>

            <div class="b44-grid">
                <section class="b44-left">
                    <div class="b44-left-inner">
                    

                        <h1 class="b44-title">
                            Convierte tus datos<br>en decisiones
                        </h1>

                        <p class="b44-subtitle">
                            Analiza tu cartera, detecta riesgos, revisa clientes críticos y genera reportes ejecutivos
                            con una experiencia más limpia, más estable y con seguimiento conversacional.
                        </p>

                        <div class="b44-composer">
                            <div class="b44-composer-top">
                                <div class="b44-composer-label">Describe el análisis que quieres generar</div>

                                <select id="aiPersona" class="b44-persona">
                                    <option value="director_financiero">Director Financiero</option>
                                    <option value="contador">Contador</option>
                                    <option value="administrador">Administrador</option>
                                    <option value="cobranza">Analista de Cobranza</option>
                                </select>
                            </div>

                            <div class="b44-input-wrap">
                                <textarea
                                    id="aiQuestion"
                                    class="b44-textarea"
                                    placeholder="Ejemplo: dime solo el resumen general de cobranza, luego te pregunto por clientes críticos y después por el más atrasado."
                                ></textarea>

                                <button type="button" class="b44-send" id="btnSendAI" aria-label="Enviar solicitud">
                                    <span class="b44-send-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 19V5"></path>
                                            <path d="M5 12l7-7 7 7"></path>
                                        </svg>
                                    </span>
                                    <span class="b44-send-loader"></span>
                                </button>
                            </div>

                            <div class="b44-composer-bottom">
                                <div id="aiLoading" class="b44-loading">
                                    <span class="dot"></span>
                                    <span>Procesando análisis financiero...</span>
                                </div>

                                <div class="b44-tools">
                                    <button type="button" class="b44-btn-link" id="btnClearAI">Reiniciar conversación</button>
                                </div>
                            </div>
                        </div>

                        <div class="b44-hint">Prueba una consulta rápida</div>

                        <div class="b44-suggests">
                            <button type="button" class="b44-chip">Dame solo un resumen corto de cobranza</button>
                            <button type="button" class="b44-chip">¿Quién es el cliente más crítico?</button>
                            <button type="button" class="b44-chip">Muéstrame solo clientes atrasados</button>
                            <button type="button" class="b44-chip">¿Qué debo priorizar esta semana?</button>
                            <button type="button" class="b44-chip">Explícame el principal cuello de botella</button>
                        </div>
                    </div>
                </section>

                <aside class="b44-right">
                    <div class="b44-side">
                        <div class="b44-side-head">
                            <div>
                                <div class="b44-side-title">Resultado estructurado</div>
                                <div class="b44-side-sub">Resumen corto arriba, detalle completo en tablas, KPIs y gráficas.</div>
                            </div>

                            <div class="b44-side-head-right">
                                <span class="b44-status idle" id="b44StatusBadge">Listo</span>
                                <button type="button" class="b44-btn-ghost" id="btnPdfIA" disabled>Exportar PDF</button>
                            </div>
                        </div>

                        <div class="b44-live">
                            <div class="b44-mini-card">
                                <div class="b44-mini-label">Solicitud actual</div>
                                <div id="b44PromptMirror" class="b44-prompt-mirror">
                                    Empieza a escribir y aquí verás la solicitud actual antes de generar el análisis.
                                </div>
                            </div>

                            <div class="b44-mini-card">
                                <div class="b44-mini-label">Perfil activo</div>
                                <div id="b44PersonaMirror" class="b44-persona-mirror">Director Financiero</div>
                            </div>
                        </div>

                        <div class="b44-chat" id="aiChatBox">
                            <div class="b44-msg assistant">
Soy su asistente financiero corporativo. Puedo responder de forma corta y dejar el detalle completo en el panel derecho.
                            </div>
                        </div>

                        <div class="b44-analysis-viewport">
                            <div id="aiAnalysis" class="b44-analysis">
                                <div class="b44-empty">
                                    <div class="b44-empty-mark">+</div>
                                    <div class="b44-empty-title">Tu análisis aparecerá aquí</div>
                                    <div class="b44-empty-text">
                                        Cuando envíes una solicitud, en este panel derecho se mostrarán KPIs, resumen, tablas y gráficas sin salirse del contenedor.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form id="formPdfIA" method="POST" action="{{ $routePdfIA }}" style="display:none;">
                            @csrf
                            <input type="hidden" name="analysis" id="pdfAnalysisInput">
                            <input type="hidden" name="chart_images" id="pdfChartImagesInput">
                        </form>
                    </div>
                </aside>
            </div>
        @else
            <div class="alert alert-secondary mb-0">
                No tiene permisos de administrador para utilizar el Asistente IA.
            </div>
        @endif
    </div>
</div>

@if($isAdmin)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const STORAGE_KEY      = 'fin_auditoria_ai_conversation_v3';
    const ANALYSIS_KEY     = 'fin_auditoria_ai_last_analysis_v3';
    const PERSONA_KEY      = 'fin_auditoria_ai_persona_v3';

    const aiChatBox        = document.getElementById('aiChatBox');
    const aiQuestion       = document.getElementById('aiQuestion');
    const aiPersona        = document.getElementById('aiPersona');
    const aiLoading        = document.getElementById('aiLoading');
    const aiAnalysis       = document.getElementById('aiAnalysis');
    const btnSendAI        = document.getElementById('btnSendAI');
    const btnClearAI       = document.getElementById('btnClearAI');
    const btnPdfIA         = document.getElementById('btnPdfIA');
    const promptMirror     = document.getElementById('b44PromptMirror');
    const personaMirror    = document.getElementById('b44PersonaMirror');
    const statusBadge      = document.getElementById('b44StatusBadge');
    const csrf             = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const routeChat        = @json($routeChatIA);
    const routeStream      = @json($routeStreamIA);

    let conversation = [];
    let lastAnalysis = null;
    let chartInstances = [];
    let currentAssistantMessageEl = null;
    let pendingPhaseText = '';
    let isTypingSummary = false;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    function safeJsonParse(value, fallback = null) {
        try {
            return JSON.parse(value);
        } catch (e) {
            return fallback;
        }
    }

    function saveState() {
        try {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(conversation));
            sessionStorage.setItem(ANALYSIS_KEY, JSON.stringify(lastAnalysis));
            sessionStorage.setItem(PERSONA_KEY, aiPersona.value || 'director_financiero');
        } catch (e) {}
    }

    function clearState() {
        try {
            sessionStorage.removeItem(STORAGE_KEY);
            sessionStorage.removeItem(ANALYSIS_KEY);
            sessionStorage.removeItem(PERSONA_KEY);
        } catch (e) {}
    }

    function restoreState() {
        try {
            const savedConversation = safeJsonParse(sessionStorage.getItem(STORAGE_KEY), []);
            const savedAnalysis = safeJsonParse(sessionStorage.getItem(ANALYSIS_KEY), null);
            const savedPersona = sessionStorage.getItem(PERSONA_KEY);

            if (savedPersona && [...aiPersona.options].some(opt => opt.value === savedPersona)) {
                aiPersona.value = savedPersona;
            }

            if (Array.isArray(savedConversation) && savedConversation.length) {
                conversation = savedConversation;
                renderConversation();
            } else {
                renderConversation();
            }

            if (savedAnalysis && typeof savedAnalysis === 'object') {
                lastAnalysis = savedAnalysis;
                renderAnalysis(savedAnalysis);
                btnPdfIA.disabled = false;
                setStatus('done', 'Listo');
            } else {
                renderEmptyState();
            }
        } catch (e) {
            renderConversation();
            renderEmptyState();
        }
    }

    function setStatus(type, text) {
        statusBadge.className = 'b44-status ' + type;
        statusBadge.textContent = text;
    }

    function syncPersonaMirror() {
        personaMirror.textContent = aiPersona.options[aiPersona.selectedIndex]?.text || 'Director Financiero';
        saveState();
    }

    function syncPromptMirror(value = null) {
        const text = (value ?? aiQuestion.value ?? '').trim();
        promptMirror.textContent = text || 'Empieza a escribir y aquí verás la solicitud actual antes de generar el análisis.';
    }

    function autoResizeTextarea() {
        aiQuestion.style.height = 'auto';
        aiQuestion.style.height = Math.max(aiQuestion.scrollHeight, 138) + 'px';
    }

    function scrollChatToBottom() {
        aiChatBox.scrollTop = aiChatBox.scrollHeight;
    }

    function addMessage(role, text, pending = false) {
        const div = document.createElement('div');
        div.className = `b44-msg ${role}${pending ? ' pending' : ''}`;
        div.innerHTML = escapeHtml(text).replace(/\n/g, '<br>');
        aiChatBox.appendChild(div);
        scrollChatToBottom();
        return div;
    }

    function renderConversation() {
        aiChatBox.innerHTML = '';

        if (!conversation.length) {
            addMessage('assistant', 'Soy su asistente financiero corporativo. Puedo responder de forma corta y dejar el detalle completo en el panel derecho.');
            return;
        }

        conversation.forEach(item => {
            addMessage(item.role === 'user' ? 'user' : 'assistant', item.content || '');
        });
    }

    function destroyCharts() {
        chartInstances.forEach(chart => {
            try { chart.destroy(); } catch (e) {}
        });
        chartInstances = [];
    }

    function renderEmptyState(message = null) {
        aiAnalysis.innerHTML = `
            <div class="b44-empty">
                <div class="b44-empty-mark">+</div>
                <div class="b44-empty-title">${escapeHtml(message ? 'No se pudo generar el análisis' : 'Tu análisis aparecerá aquí')}</div>
                <div class="b44-empty-text">
                    ${escapeHtml(message || 'Cuando envíes una solicitud, en este panel derecho se mostrarán KPIs, resumen, tablas y gráficas sin salirse del contenedor.')}
                </div>
            </div>
        `;
    }

    function renderLoadingState() {
        aiAnalysis.innerHTML = `
            <div class="b44-summary b44-skeleton">
                <div class="b44-skeleton-line lg"></div>
                <div class="b44-skeleton-line md"></div>
                <div class="b44-skeleton-line md"></div>
                <div class="b44-skeleton-line sm"></div>
            </div>
            <div class="b44-kpis">
                <div class="b44-kpi b44-skeleton" style="min-height: 118px;"></div>
                <div class="b44-kpi b44-skeleton" style="min-height: 118px;"></div>
                <div class="b44-kpi b44-skeleton" style="min-height: 118px;"></div>
                <div class="b44-kpi b44-skeleton" style="min-height: 118px;"></div>
            </div>
            <div class="b44-card b44-skeleton" style="min-height: 150px;"></div>
            <div class="b44-card b44-skeleton" style="min-height: 200px;"></div>
        `;
    }

    function renderList(items) {
        if (!items || !items.length) {
            return `<div class="b44-empty-text">Sin elementos reportados.</div>`;
        }
        return `<ul class="b44-list">${items.map(item => `<li>${escapeHtml(item)}</li>`).join('')}</ul>`;
    }

    function renderCuellos(items) {
        if (!items || !items.length) {
            return `<div class="b44-empty-text">Sin cuellos de botella relevantes reportados en este análisis.</div>`;
        }

        return items.map(item => `
            <div class="b44-bottleneck">
                <div class="b44-bottleneck-title">${escapeHtml(item.titulo || '')}</div>
                <div class="b44-bottleneck-impact">Impacto: ${escapeHtml(item.impacto || '')}</div>
                <div class="b44-empty-text">${escapeHtml(item.detalle || '')}</div>
            </div>
        `).join('');
    }

    function renderTable(table) {
        const headers = (table.columns || []).map(col => `<th>${escapeHtml(col)}</th>`).join('');
        const rows = (table.rows || []).map(row => `
            <tr>${(row || []).map(col => `<td>${escapeHtml(col)}</td>`).join('')}</tr>
        `).join('');

        return `
            <div class="b44-table-card">
                <div class="b44-card-title">${escapeHtml(table.title || 'Reporte tabular')}</div>
                <div class="b44-table-wrap">
                    <table class="b44-table">
                        <thead><tr>${headers}</tr></thead>
                        <tbody>${rows || `<tr><td colspan="20">Sin datos disponibles.</td></tr>`}</tbody>
                    </table>
                </div>
            </div>
        `;
    }

    function renderCharts(charts) {
        if (!charts || !charts.length) return '';

        const html = charts.map((chart, index) => `
            <div class="b44-chart-card">
                <div class="b44-card-title">${escapeHtml(chart.title || 'Representación gráfica')}</div>
                <div class="b44-chart-box">
                    <canvas id="b44Chart_${index}"></canvas>
                </div>
            </div>
        `).join('');

        setTimeout(() => {
            destroyCharts();

            charts.forEach((chart, index) => {
                const canvas = document.getElementById(`b44Chart_${index}`);
                if (!canvas) return;

                const type = (chart.type || 'bar').toLowerCase();
                const isDoughnut = type === 'doughnut' || type === 'pie';

                const instance = new Chart(canvas, {
                    type,
                    data: {
                        labels: chart.labels || [],
                        datasets: [{
                            label: chart.title || 'Serie',
                            data: chart.values || [],
                            backgroundColor: isDoughnut
                                ? [
                                    'rgba(17, 24, 39, 0.88)',
                                    'rgba(255, 106, 26, 0.85)',
                                    'rgba(102, 112, 133, 0.80)',
                                    'rgba(148, 163, 184, 0.82)',
                                    'rgba(18, 183, 106, 0.80)'
                                ]
                                : 'rgba(255, 106, 26, 0.82)',
                            borderColor: isDoughnut
                                ? [
                                    '#111827',
                                    '#ff6a1a',
                                    '#667085',
                                    '#94a3b8',
                                    '#12b76a'
                                ]
                                : '#ff6a1a',
                            borderWidth: 1.2,
                            borderRadius: isDoughnut ? 0 : 10,
                            tension: type === 'line' ? 0.32 : 0,
                            maxBarThickness: 48
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: 4
                        },
                        plugins: {
                            legend: {
                                display: isDoughnut,
                                position: 'bottom',
                                labels: {
                                    boxWidth: 10,
                                    usePointStyle: true
                                }
                            }
                        },
                        scales: isDoughnut ? {} : {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    color: '#667085',
                                    maxRotation: 0,
                                    autoSkip: true
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(15, 23, 42, 0.06)' },
                                border: { display: false },
                                ticks: { color: '#667085' }
                            }
                        }
                    }
                });

                chartInstances.push(instance);
            });
        }, 80);

        return `<div class="b44-charts">${html}</div>`;
    }

    function renderAnalysis(payload) {
        lastAnalysis = payload;
        btnPdfIA.disabled = false;
        setStatus('done', 'Listo');

        const narrative = payload.narrative || {};
        const kpis = payload.kpis || [];
        const tables = payload.tables || [];
        const charts = payload.charts || [];

        const kpisHtml = kpis.length
            ? `<div class="b44-kpis">
                ${kpis.map(kpi => `
                    <div class="b44-kpi">
                        <div class="b44-kpi-label">${escapeHtml(kpi.label || '')}</div>
                        <div class="b44-kpi-value">${escapeHtml(kpi.value || '')}</div>
                        <div class="b44-kpi-detail">${escapeHtml(kpi.detail || '')}</div>
                    </div>
                `).join('')}
               </div>`
            : '';

        const tablesHtml = tables.map(renderTable).join('');
        const chartsHtml = renderCharts(charts);

        aiAnalysis.innerHTML = `
            <div class="b44-summary">
                <div class="b44-card-title">Resumen ejecutivo</div>
                <div class="b44-card-sub">Generado bajo perfil: ${escapeHtml(payload.persona || aiPersona.value || '')}</div>
                <div class="b44-empty-text" style="color:#344054; white-space:pre-line;">${escapeHtml(narrative.resumen_ejecutivo || 'Se generó el análisis correctamente.')}</div>
            </div>

            ${kpisHtml}

            <div class="b44-grid-2">
                <div class="b44-card">
                    <div class="b44-card-title">Hallazgos</div>
                    ${renderList(narrative.hallazgos || [])}
                </div>

                <div class="b44-card">
                    <div class="b44-card-title">Recomendaciones</div>
                    ${renderList(narrative.recomendaciones || [])}
                </div>
            </div>

            <div class="b44-card">
                <div class="b44-card-title">Cuellos de botella</div>
                ${renderCuellos(narrative.cuellos_botella || [])}
            </div>

            ${chartsHtml}
            ${tablesHtml}
        `;

        saveState();
    }

    function createPendingAssistantMessage(initialText = 'Analizando información') {
        currentAssistantMessageEl = addMessage('assistant', initialText, true);
        pendingPhaseText = initialText;
    }

    function updatePendingAssistantMessage(text) {
        pendingPhaseText = text || pendingPhaseText || 'Analizando información';
        if (!currentAssistantMessageEl) {
            createPendingAssistantMessage(pendingPhaseText);
            return;
        }
        currentAssistantMessageEl.innerHTML = escapeHtml(pendingPhaseText).replace(/\n/g, '<br>');
    }

    async function typeTextInElement(element, text, delay = 16) {
        if (!element) return;
        isTypingSummary = true;
        element.innerHTML = '';
        for (let i = 0; i < text.length; i++) {
            element.innerHTML += escapeHtml(text[i]).replace(/\n/g, '<br>');
            scrollChatToBottom();
            await new Promise(resolve => setTimeout(resolve, delay));
        }
        isTypingSummary = false;
    }

    function cleanText(text) {
        return String(text || '')
            .replace(/\s+/g, ' ')
            .replace(/\s([,.!?;:])/g, '$1')
            .trim();
    }

    function compactSummary(text, maxLength = 210) {
        let value = cleanText(text);
        if (!value) return '';

        const sentences = value.split(/(?<=[.!?])\s+/).filter(Boolean);
        let compact = sentences.slice(0, 2).join(' ');

        if (!compact) compact = value;
        if (compact.length > maxLength) {
            compact = compact.slice(0, maxLength).trim();
            compact = compact.replace(/[,:;.\-–—\s]+$/, '') + '…';
        }

        return compact;
    }

    function buildChatSummary(payload) {
        const narrative = payload?.narrative || {};
        const candidates = [
            narrative.resumen_corto,
            narrative.respuesta_corta,
            narrative.resumen_ejecutivo,
            narrative.respuesta_detallada
        ].filter(Boolean);

        const picked = candidates[0] || 'Listo. Ya te mostré el resultado en el panel derecho.';
        return compactSummary(picked, 220);
    }

    async function finalizeAssistantSummary(payload, fallbackText = null) {
        const finalText = compactSummary(fallbackText || buildChatSummary(payload), 220) || 'Listo. Ya te mostré el resultado en el panel derecho.';

        if (!currentAssistantMessageEl) {
            currentAssistantMessageEl = addMessage('assistant', '', false);
        }

        currentAssistantMessageEl.classList.remove('pending');
        await typeTextInElement(currentAssistantMessageEl, finalText, 15);

        conversation.push({ role: 'assistant', content: finalText });
        currentAssistantMessageEl = null;
        saveState();
    }

    async function handleJsonResponse(resp) {
        const data = await resp.json();

        if (!resp.ok || !data.ok) {
            const message = data.message || 'No se pudo generar el análisis. Intente nuevamente.';
            if (currentAssistantMessageEl) {
                currentAssistantMessageEl.classList.remove('pending');
                currentAssistantMessageEl.innerHTML = escapeHtml(message);
                currentAssistantMessageEl = null;
            } else {
                addMessage('assistant', message);
            }
            renderEmptyState(message);
            setStatus('error', 'Error');
            return;
        }

        const payload = data.data || {};
        renderAnalysis(payload);
        await finalizeAssistantSummary(payload);
    }

    async function handleNdjsonStream(resp) {
        const reader = resp.body.getReader();
        const decoder = new TextDecoder();
        let buffer = '';
        let analysisPayload = null;
        let streamFinished = false;
        let errorMessage = null;

        const phaseMap = {
            'Preparando análisis': 'Preparando contexto financiero',
            'Consultando OpenAI': 'Analizando cartera y redactando respuesta',
            'Procesando estructura final': 'Organizando tablas, KPIs y gráficas'
        };

        const processLine = (line) => {
            const trimmed = (line || '').trim();
            if (!trimmed) return;

            let event;
            try {
                event = JSON.parse(trimmed);
            } catch (e) {
                return;
            }

            switch (event.type) {
                case 'start':
                    setStatus('loading', 'Analizando');
                    updatePendingAssistantMessage(phaseMap[event.label] || 'Preparando contexto financiero');
                    break;

                case 'phase':
                    setStatus('loading', 'Analizando');
                    updatePendingAssistantMessage(phaseMap[event.label] || event.label || 'Analizando información');
                    break;

                case 'token':
                    // Ya no mostramos el JSON crudo del stream.
                    break;

                case 'message':
                    break;

                case 'analysis':
                    analysisPayload = event.data || null;
                    if (analysisPayload) {
                        renderAnalysis(analysisPayload);
                    }
                    break;

                case 'done':
                    streamFinished = true;
                    break;

                case 'error':
                    errorMessage = event.message || 'No se pudo generar el análisis.';
                    break;
            }
        };

        while (true) {
            const { value, done } = await reader.read();
            if (done) break;

            buffer += decoder.decode(value, { stream: true });
            const lines = buffer.split(/\r?\n/);
            buffer = lines.pop() || '';
            lines.forEach(processLine);
        }

        if (buffer.trim()) {
            processLine(buffer.trim());
        }

        if (errorMessage) {
            setStatus('error', 'Error');
            renderEmptyState(errorMessage);

            if (currentAssistantMessageEl) {
                currentAssistantMessageEl.classList.remove('pending');
                currentAssistantMessageEl.innerHTML = escapeHtml(errorMessage);
                conversation.push({ role: 'assistant', content: errorMessage });
                currentAssistantMessageEl = null;
                saveState();
            } else {
                addMessage('assistant', errorMessage);
            }

            return;
        }

        if (analysisPayload) {
            await finalizeAssistantSummary(analysisPayload);
        } else {
            const msg = 'La transmisión terminó antes de completar el análisis.';
            setStatus('error', 'Incompleto');
            renderEmptyState(msg);

            if (currentAssistantMessageEl) {
                currentAssistantMessageEl.classList.remove('pending');
                currentAssistantMessageEl.innerHTML = escapeHtml(msg);
                conversation.push({ role: 'assistant', content: msg });
                currentAssistantMessageEl = null;
                saveState();
            } else {
                addMessage('assistant', msg);
            }
        }

        if (!streamFinished) {
            setStatus('done', 'Listo');
        }
    }

    async function sendQuestion(customQuestion = null) {
        const question = (customQuestion ?? aiQuestion.value ?? '').trim();

        if (!question || isTypingSummary) {
            aiQuestion.focus();
            return;
        }

        syncPromptMirror(question);
        syncPersonaMirror();

        addMessage('user', question);
        conversation.push({ role: 'user', content: question });
        saveState();

        aiQuestion.value = '';
        aiQuestion.style.height = '138px';

        btnSendAI.disabled = true;
        btnSendAI.classList.add('is-loading');
        aiLoading.style.display = 'inline-flex';
        setStatus('loading', 'Analizando');
        renderLoadingState();
        createPendingAssistantMessage('Preparando contexto financiero');

        try {
            const resp = await fetch(routeStream, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/x-ndjson, application/json, text/plain'
                },
                body: JSON.stringify({
                    question,
                    persona: aiPersona?.value || 'director_financiero',
                    conversation
                })
            });

            if (!resp.ok) {
                try {
                    const errorJson = await resp.json();
                    const message = errorJson.message || 'No se pudo generar el análisis.';
                    if (currentAssistantMessageEl) {
                        currentAssistantMessageEl.classList.remove('pending');
                        currentAssistantMessageEl.innerHTML = escapeHtml(message);
                        conversation.push({ role: 'assistant', content: message });
                        currentAssistantMessageEl = null;
                        saveState();
                    } else {
                        addMessage('assistant', message);
                    }
                    renderEmptyState(message);
                } catch (e) {
                    const message = 'No se pudo generar el análisis.';
                    if (currentAssistantMessageEl) {
                        currentAssistantMessageEl.classList.remove('pending');
                        currentAssistantMessageEl.innerHTML = escapeHtml(message);
                        conversation.push({ role: 'assistant', content: message });
                        currentAssistantMessageEl = null;
                        saveState();
                    } else {
                        addMessage('assistant', message);
                    }
                    renderEmptyState(message);
                }
                setStatus('error', 'Error');
                return;
            }

            const contentType = (resp.headers.get('content-type') || '').toLowerCase();

            if (contentType.includes('application/json') || !resp.body || routeStream === routeChat) {
                await handleJsonResponse(resp);
            } else {
                await handleNdjsonStream(resp);
            }
        } catch (error) {
            const message = 'Ha ocurrido un error de conexión con el servidor IA.';
            if (currentAssistantMessageEl) {
                currentAssistantMessageEl.classList.remove('pending');
                currentAssistantMessageEl.innerHTML = escapeHtml(message);
                conversation.push({ role: 'assistant', content: message });
                currentAssistantMessageEl = null;
                saveState();
            } else {
                addMessage('assistant', message);
            }
            renderEmptyState(message);
            setStatus('error', 'Sin conexión');
        } finally {
            aiLoading.style.display = 'none';
            btnSendAI.disabled = false;
            btnSendAI.classList.remove('is-loading');
        }
    }

    btnSendAI.addEventListener('click', function () {
        sendQuestion();
    });

    aiQuestion.addEventListener('input', function () {
        autoResizeTextarea();
        syncPromptMirror();
    });

    aiQuestion.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendQuestion();
        }
    });

    aiPersona.addEventListener('change', function () {
        syncPersonaMirror();
    });

    document.querySelectorAll('.b44-chip').forEach(chip => {
        chip.addEventListener('click', function () {
            const text = this.textContent.trim();
            aiQuestion.value = text;
            autoResizeTextarea();
            syncPromptMirror(text);
            sendQuestion(text);
        });
    });

    btnClearAI.addEventListener('click', function () {
        conversation = [];
        lastAnalysis = null;
        btnPdfIA.disabled = true;
        destroyCharts();
        setStatus('idle', 'Listo');
        currentAssistantMessageEl = null;
        pendingPhaseText = '';
        aiQuestion.value = '';
        aiQuestion.style.height = '138px';

        clearState();
        renderConversation();
        syncPromptMirror();
        syncPersonaMirror();
        renderEmptyState();
    });

    btnPdfIA.addEventListener('click', function () {
        if (!lastAnalysis) return;

        const chartImages = chartInstances.map(chart => {
            try {
                return chart.toBase64Image('image/png', 1);
            } catch (e) {
                return null;
            }
        }).filter(Boolean);

        document.getElementById('pdfAnalysisInput').value = JSON.stringify(lastAnalysis);
        document.getElementById('pdfChartImagesInput').value = JSON.stringify(chartImages);
        document.getElementById('formPdfIA').submit();
    });

    restoreState();
    syncPersonaMirror();
    syncPromptMirror();
    autoResizeTextarea();
});
</script>
@endif
@endsection