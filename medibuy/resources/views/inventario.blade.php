@extends('layouts.app')
@section('title','Inventario')
@section('titulo','Inventario')

@section('styles')
{{-- Preconnect para acelerar la conexión a los CDN --}}
<link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
<link rel="preconnect" href="https://unpkg.com" crossorigin>

{{-- bootstrap-icons cargado sin bloquear el render inicial --}}
<link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"></noscript>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection

@section('content')
<style>
  /* ══════════════════════════════════════════════════════════
     TOKENS LOCALES — VALORES FIJOS (NO dependen de --cotz-*)
  ══════════════════════════════════════════════════════════ */
  .inv-root {
    --inv-bg:          #e8e9e9;
    --inv-panel:       #ffffff;
    --inv-panel-2:     #f3f4f6;
    --inv-panel-3:     #e5e7eb;
    --inv-text:        #0f172a;
    --inv-text-2:      #334155;
    --inv-muted:       #64748b;
    --inv-border:      #e2e8f0;
    --inv-input-bg:    #ffffff;
    --inv-input-brd:   #e2e8f0;
    --inv-shadow:      0 10px 30px rgba(2,6,23,.06);
    --inv-radius:      22px;
    --inv-primary:     #4f7dff;

    /* Acento azul */
    --inv-blue:        #1d4ed8;
    --inv-blue-bg:     #dbeafe;
    --inv-blue-brd:    rgba(96,165,250,.45);
    --inv-blue-txt:    #0b2a4a;

    /* Acento verde */
    --inv-green:       #046c4e;
    --inv-green-bg:    #dcfce7;
    --inv-green-brd:   rgba(52,211,153,.45);
    --inv-green-txt:   #064e3b;

    /* OTP / Bank modal */
    --inv-m-bg:        rgba(255,255,255,.92);
    --inv-m-border:    #dde6f6;
    --inv-m-shadow:    0 26px 70px rgba(15,23,42,.18);
    --inv-m-glow:      0 0 0 10px rgba(31,75,184,.07);
    --inv-m-blue:      #1f4bb8;
    --inv-m-text:      #0f172a;
    --inv-m-muted:     #667085;
    --inv-m-alert-bg:  rgba(31,75,184,.045);
    --inv-m-line:      #e7ebf2;
    --inv-m-otp-bg:    #ffffff;
    --inv-m-otp-brd:   #dde6f6;
    --inv-m-note:      #64748b;
    --inv-m-close-bg:  #ffffff;
    --inv-m-close-brd: #e7ebf2;
    --inv-m-close-txt: #475569;
  }

  *, *::before, *::after { box-sizing: border-box; }

  /* ── Wrapper ────────────────────────────────────────────── */
  .inv-root {
    color: var(--inv-text);
    background: var(--inv-bg);
  }
  .inv-wrap {
    max-width: 1160px;
    margin: 0 auto;
    padding: 0 16px;
    overflow-x: hidden;
  }

  /* ══════════════════════════════════════════════════════════
     OTP / BANK MODAL
  ══════════════════════════════════════════════════════════ */
  .bank-mask {
    position: fixed; inset: 0;
    background: rgba(15,23,42,.22);
    opacity: 0; pointer-events: none;
    transition: .18s ease; z-index: 5000;
    backdrop-filter: blur(10px);
  }
  .bank-mask.open { opacity: 1; pointer-events: auto; }

  .bank-modal {
    position: fixed; left: 50%; top: 50%;
    transform: translate(-50%,-46%) scale(.985);
    width: min(520px, calc(100% - 28px));
    opacity: 0; pointer-events: none;
    transition: .18s ease; z-index: 5001;
  }
  .bank-modal.open {
    opacity: 1; pointer-events: auto;
    transform: translate(-50%,-50%) scale(1);
  }

  .bank-card {
    background: var(--inv-m-bg);
    border: 1px solid var(--inv-m-border);
    border-radius: 18px;
    box-shadow: var(--inv-m-shadow), var(--inv-m-glow);
    overflow: hidden;
    transition: background .25s ease, border-color .25s ease;
  }

  .bank-top {
    padding: 14px 16px;
    display: flex; align-items: flex-start;
    justify-content: space-between; gap: 10px;
    border-bottom: 1px solid var(--inv-m-line);
    background: radial-gradient(120% 120% at 0% 0%, rgba(31,75,184,.08) 0%, transparent 55%),
                linear-gradient(180deg, rgba(255,255,255,.04) 0%, transparent 100%);
    transition: border-color .25s ease;
  }

  .bank-brand { display: flex; gap: 12px; align-items: center; }
  .bank-badge {
    width: 44px; height: 44px; border-radius: 14px;
    display: grid; place-items: center;
    background: rgba(31,75,184,.08);
    border: 1px solid rgba(31,75,184,.14);
  }
  .bank-badge i { color: var(--inv-m-blue); font-size: 1.15rem; }

  .bank-title { font-weight: 900; color: var(--inv-m-text); letter-spacing: .2px; line-height: 1.1; transition: color .25s ease; }
  .bank-sub   { margin-top: 2px; font-size: .88rem; color: var(--inv-m-muted); transition: color .25s ease; }

  .bank-close {
    border: 1px solid var(--inv-m-close-brd);
    background: var(--inv-m-close-bg);
    color: var(--inv-m-close-txt);
    width: 36px; height: 36px;
    border-radius: 12px; display: grid; place-items: center;
    cursor: pointer;
    transition: background .12s ease, border-color .25s ease, color .25s ease;
  }
  .bank-close:hover { background: var(--inv-panel-2); }

  .bank-body { padding: 14px 16px 16px; }

  .bank-alert {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 10px 12px; border-radius: 14px;
    border: 1px solid var(--inv-m-line);
    background: var(--inv-m-alert-bg);
    color: var(--inv-m-text);
    font-size: .92rem; line-height: 1.35;
    transition: background .25s ease, border-color .25s ease, color .25s ease;
  }
  .bank-alert .dot {
    width: 10px; height: 10px; border-radius: 999px;
    background: rgba(31,75,184,.75);
    box-shadow: 0 0 0 6px rgba(31,75,184,.10);
    margin-top: 4px; flex: 0 0 auto;
  }

  .otp-row { margin-top: 12px; display: flex; gap: 10px; justify-content: center; }
  .otp {
    width: 54px; height: 58px;
    text-align: center; font-weight: 900; font-size: 1.15rem;
    color: var(--inv-m-text);
    background: var(--inv-m-otp-bg);
    border: 1px solid var(--inv-m-otp-brd);
    border-radius: 14px; outline: 0;
    transition: border-color .12s ease, box-shadow .12s ease,
                background .25s ease, color .25s ease;
  }
  .otp:focus  { border-color: rgba(31,75,184,.35); box-shadow: 0 0 0 6px rgba(31,75,184,.12); }
  .otp.error  { border-color: rgba(220,38,38,.40); box-shadow: 0 0 0 6px rgba(220,38,38,.12); }

  .bank-note  { margin-top: 10px; text-align: center; font-size: .85rem; color: var(--inv-m-note); transition: color .25s ease; }

  .loading-dots { margin-top: 10px; display: none; justify-content: center; gap: 6px; }
  .loading-dots span {
    width: 7px; height: 7px; border-radius: 999px;
    background: rgba(31,75,184,.55); opacity: .6;
    animation: dotPulse .9s infinite ease-in-out;
  }
  .loading-dots span:nth-child(2) { animation-delay: .12s; }
  .loading-dots span:nth-child(3) { animation-delay: .24s; }

  @keyframes dotPulse  { 0%,100%{transform:translateY(0);opacity:.45} 50%{transform:translateY(-4px);opacity:1} }
  @keyframes shake     { 0%,100%{transform:translateX(0)} 20%{transform:translateX(-5px)} 40%{transform:translateX(5px)} 60%{transform:translateX(-4px)} 80%{transform:translateX(4px)} }
  @keyframes spin      { to { transform: rotate(360deg); } }
  .shake { animation: shake .28s ease; }

  /* ══════════════════════════════════════════════════════════
     HERO
  ══════════════════════════════════════════════════════════ */
  .inv-hero {
    background:
      radial-gradient(1200px 150px at 0% 0%,   rgba(96,165,250,.14), transparent 40%),
      radial-gradient(1200px 150px at 100% 0%,  rgba(14,165,233,.10), transparent 40%),
      var(--inv-panel);
    border: 1px solid var(--inv-border);
    border-radius: 18px;
    padding: 16px 18px;
    box-shadow: var(--inv-shadow);
    display: flex; align-items: center;
    justify-content: space-between; gap: 12px; flex-wrap: wrap;
    margin: 18px 0; overflow: hidden;
    transition: background .25s ease, border-color .25s ease, box-shadow .25s ease;
  }

  .inv-hero-chip {
    width: 56px; height: 56px; border-radius: 16px;
    display: inline-flex; align-items: center; justify-content: center;
    background: var(--inv-panel);
    border: 1px solid var(--inv-border);
    transition: background .25s ease, border-color .25s ease;
  }

  /* ── Botones hero ───────────────────────────────────────── */
  .inv-btn {
    display: inline-flex; align-items: center; gap: .45rem;
    padding: 10px 13px; border-radius: 14px;
    border: 1px solid var(--inv-border);
    background: var(--inv-panel);
    color: var(--inv-text);
    font-weight: 800; text-decoration: none; cursor: pointer;
    font-family: inherit; font-size: .9rem;
    transition: transform .04s ease, box-shadow .2s ease,
                background .2s ease, border-color .25s ease, color .25s ease;
    white-space: nowrap;
  }
  .inv-btn:active { transform: translateY(1px); }
  .inv-btn:hover  { background: var(--inv-panel-2); }

  .inv-btn-blue  { background: var(--inv-blue-bg); color: var(--inv-blue-txt); border-color: var(--inv-blue-brd); }
  .inv-btn-blue:hover  { filter: brightness(1.04); }
  .inv-btn-green { background: var(--inv-green-bg); color: var(--inv-green-txt); border-color: var(--inv-green-brd); }
  .inv-btn-green:hover { filter: brightness(1.04); }
  .inv-btn-icon  { padding: 9px 11px; }

  /* ── Buscador ───────────────────────────────────────────── */
  .inv-search {
    position: relative; flex: 1 1 300px; min-width: 0;
    background: var(--inv-input-bg);
    border: 1px solid var(--inv-input-brd);
    border-radius: 14px; padding-left: 42px;
    transition: background .25s ease, border-color .25s ease;
  }
  .inv-search input {
    border: none; outline: none; background: transparent;
    padding: 12px 14px; width: 100%;
    color: var(--inv-text); font-family: inherit; font-size: .93rem;
    transition: color .25s ease;
  }
  .inv-search input::placeholder { color: var(--inv-muted); }
  .inv-search .ico {
    position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
    font-size: 18px; color: var(--inv-blue); opacity: .9;
  }

  /* ── Selects hero ───────────────────────────────────────── */
  .inv-select {
    border: 1px solid var(--inv-input-brd);
    border-radius: 14px; padding: 10px 12px;
    background: var(--inv-input-bg);
    color: var(--inv-text);
    min-width: 160px; font-family: inherit; font-size: .9rem;
    transition: background .25s ease, border-color .25s ease, color .25s ease;
  }
  .inv-select:focus {
    outline: none;
    border-color: var(--inv-primary);
    box-shadow: 0 0 0 3px rgba(79,125,255,.18);
  }

  .inv-hero-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; width: 100%; }

  /* ══════════════════════════════════════════════════════════
     TABLA
  ══════════════════════════════════════════════════════════ */
  .inv-table-wrap {
    background: var(--inv-panel);
    border: 1px solid var(--inv-border);
    border-radius: var(--inv-radius);
    box-shadow: var(--inv-shadow);
    overflow: hidden;
    transition: background .25s ease, border-color .25s ease, box-shadow .25s ease;
  }
  .inv-table-scroll { overflow: auto; max-width: 100%; }

  .inv-table {
    width: 100%; border-collapse: separate; border-spacing: 0;
    table-layout: fixed;
  }
  .inv-table col:nth-child(1){ width: 27%; }
  .inv-table col:nth-child(2){ width: 12%; }
  .inv-table col:nth-child(3){ width: 18%; }
  .inv-table col:nth-child(4){ width: 12%; }
  .inv-table col:nth-child(5){ width: 13%; }
  .inv-table col:nth-child(6){ width: 18%; }

  .inv-table .th {
    padding: 16px 18px; text-align: left; font-size: 14px;
    color: var(--inv-muted); font-weight: 700;
    background: var(--inv-panel-2);
    border-bottom: 1px solid var(--inv-border);
    white-space: nowrap;
    transition: background .25s ease, color .25s ease, border-color .25s ease;
  }
  .inv-table tr.trow {
    border-bottom: 1px solid var(--inv-border);
    background: var(--inv-panel);
    transition: background .2s ease, border-color .25s ease;
  }
  .inv-table tr.trow:hover { background: var(--inv-panel-2); }
  .inv-table tr.trow.expanded { background: var(--inv-blue-bg) !important; }

  .inv-table td.td {
    padding: 16px 18px; text-align: left; font-size: 14px;
    vertical-align: middle;
    color: var(--inv-text);
    transition: color .25s ease;
  }

  /* ── Tile mini imagen ───────────────────────────────────── */
  .inv-tile {
    width: 52px; height: 52px;
    border: 1px solid var(--inv-border);
    border-radius: 10px; overflow: hidden;
    background: var(--inv-panel-2);
    display: grid; place-items: center; flex-shrink: 0;
    transition: background .25s ease, border-color .25s ease;
  }
  .inv-tile img { width: 100%; height: 100%; object-fit: cover; }

  .inv-equip-main { display: flex; align-items: center; gap: 12px; }
  .inv-equip-title { margin-bottom: 2px; display: flex; flex-wrap: wrap; column-gap: 4px; }
  .inv-equip-tipo    { font-weight: 700; color: var(--inv-text); }
  .inv-equip-subtipo { font-weight: 700; color: var(--inv-text); }
  .inv-equip-dot     { color: var(--inv-muted); }
  .inv-equip-model   { font-size: .84rem; color: var(--inv-muted); line-height: 1.3; transition: color .25s ease; }

  /* ── Badges de estado ───────────────────────────────────── */
  .inv-badge {
    display: inline-flex; align-items: center; gap: .35rem;
    padding: 4px 10px; border-radius: 999px;
    font-weight: 800; font-size: 12px;
    border: 1px solid transparent; width: fit-content;
  }
  .inv-badge-registro     { background: var(--inv-panel-2); color: var(--inv-text-2);  border-color: var(--inv-border); }
  .inv-badge-hojalateria  { background: #e0f2fe; color: #1d4ed8; border-color: #bfdbfe; }
  .inv-badge-mantenimiento{ background: #fef9c3; color: #a16207; border-color: #fde68a; }
  .inv-badge-stock        { background: #dcfce7; color: #065f46; border-color: #bbf7d0; }
  .inv-badge-vendido      { background: #ffe4e6; color: #9f1239; border-color: #fecdd3; }
  .inv-badge-defectuoso   { background: #ffedd5; color: #c2410c; border-color: #fdba74; }


  .inv-state-meta { color: var(--inv-muted); font-size: 12px; margin-top: 6px; transition: color .25s ease; }

  /* ── Acciones celda ─────────────────────────────────────── */
  .inv-cell-actions { display: flex; gap: 6px; justify-content: flex-end; }
  .inv-cell-actions .inv-btn { padding: 7px 9px; border-radius: 10px; }
  .inv-cell-actions .inv-btn i { font-size: 15px; }

  /* ── Fila expandible ────────────────────────────────────── */
  .inv-expand-row { display: none; background: var(--inv-panel-2); border-bottom: 1px solid var(--inv-border); transition: background .25s ease; }
  .inv-expand-row.open { display: table-row; }
  .inv-expand-panel {
    padding: 18px 20px;
    display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 14px;
  }
  .inv-expand-images { display: flex; gap: 10px; margin-bottom: 14px; }
  .inv-expand-thumb {
    width: 90px; height: 90px; border-radius: 12px; overflow: hidden;
    border: 1px solid var(--inv-border); background: var(--inv-panel);
    display: grid; place-items: center;
    transition: background .25s ease, border-color .25s ease;
  }
  .inv-expand-thumb img { width: 100%; height: 100%; object-fit: cover; }
  .inv-expand-field label {
    font-size: 11px; font-weight: 700; color: var(--inv-muted);
    text-transform: uppercase; letter-spacing: .04em;
    display: block; margin-bottom: 3px;
    transition: color .25s ease;
  }
  .inv-expand-field span { font-size: 13px; color: var(--inv-text); font-weight: 600; transition: color .25s ease; }
  .inv-expand-close {
    display: inline-flex; align-items: center; gap: 6px;
    margin-top: 14px; padding: 7px 14px;
    border-radius: 10px; border: 1px solid var(--inv-border);
    background: var(--inv-panel); font-size: 13px; font-weight: 700;
    color: var(--inv-text-2); cursor: pointer;
    transition: background .15s ease, border-color .25s ease, color .25s ease;
  }
  .inv-expand-close:hover { background: var(--inv-panel-3); }

  /* ══════════════════════════════════════════════════════════
     PAGINACIÓN
  ══════════════════════════════════════════════════════════ */
  .inv-pagination {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px; border-top: 1px solid var(--inv-border);
    background: var(--inv-panel);
    border-radius: 0 0 var(--inv-radius) var(--inv-radius);
    flex-wrap: wrap; gap: 10px;
    transition: background .25s ease, border-color .25s ease;
  }
  .inv-pag-info { font-size: 13px; color: var(--inv-muted); transition: color .25s ease; }
  .inv-pag-btns { display: flex; gap: 8px; }
  .inv-pag-btn {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 7px 14px; border-radius: 10px;
    border: 1px solid var(--inv-border);
    background: var(--inv-panel); color: var(--inv-text);
    font-weight: 700; font-size: 13px; font-family: inherit;
    cursor: pointer;
    transition: background .15s ease, border-color .25s ease, color .25s ease;
  }
  .inv-pag-btn:hover:not(:disabled) { background: var(--inv-panel-2); }
  .inv-pag-btn:disabled { opacity: .4; cursor: not-allowed; }

  .inv-pag-pages { display: flex; gap: 4px; }
  .inv-pag-page {
    width: 34px; height: 34px; border-radius: 9px;
    border: 1px solid var(--inv-border);
    background: var(--inv-panel); color: var(--inv-text);
    font-weight: 700; font-size: 13px; font-family: inherit;
    cursor: pointer; display: grid; place-items: center;
    transition: background .15s ease, border-color .25s ease, color .25s ease;
  }
  .inv-pag-page.active { background: var(--inv-blue-bg); color: var(--inv-blue-txt); border-color: var(--inv-blue-brd); }
  .inv-pag-page:hover:not(.active) { background: var(--inv-panel-2); }

  /* ══════════════════════════════════════════════════════════
     MODAL EXPORTAR / BARCODE — anula estilos Bootstrap
  ══════════════════════════════════════════════════════════ */
  .inv-modal-content {
    background: var(--inv-panel) !important;
    border: 1px solid var(--inv-border) !important;
    border-radius: 18px !important;
    color: var(--inv-text) !important;
    transition: background .25s ease, border-color .25s ease;
  }
  .inv-modal-header {
    border-bottom: 1px solid var(--inv-border) !important;
    background: var(--inv-panel-2) !important;
    transition: background .25s ease, border-color .25s ease;
  }
  .inv-modal-body { padding: 16px !important; }

  .inv-option-card {
    background: var(--inv-panel-2);
    border: 1px solid var(--inv-border);
    border-radius: 14px; padding: 12px;
    transition: background .25s ease, border-color .25s ease;
  }
  .inv-option-card small { color: var(--inv-muted); transition: color .25s ease; }

  /* ── Iframe barcode bg ──────────────────────────────────── */
  .inv-barcode-preview {
    position: relative;
    border: 1px solid var(--inv-border);
    border-radius: 12px; overflow: hidden;
    background: var(--inv-panel-2);
    height: 220px;
    transition: background .25s ease, border-color .25s ease;
  }
  .inv-barcode-spinner {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 8px;
    background: var(--inv-panel-2); z-index: 2;
    transition: background .25s ease;
  }
  .inv-spinner-ring {
    width: 28px; height: 28px;
    border: 3px solid var(--inv-border);
    border-top-color: var(--inv-blue);
    border-radius: 50%;
    animation: spin .7s linear infinite;
  }

  /* ══════════════════════════════════════════════════════════
     FAB + BOTTOM SHEET
  ══════════════════════════════════════════════════════════ */
  .inv-fab { position: fixed; right: 16px; bottom: 18px; z-index: 60; display: none; }
  @media (max-width: 576px) { .inv-fab { display: block; } }

  .inv-fab-btn {
    width: 56px; height: 56px; border-radius: 999px;
    border: 1px solid var(--inv-border);
    background: var(--inv-panel);
    display: grid; place-items: center;
    box-shadow: 0 14px 28px rgba(2,6,23,.12);
    cursor: pointer;
    transition: transform .06s ease, box-shadow .2s ease,
                background .25s ease, border-color .25s ease;
  }
  .inv-fab-btn:hover { background: var(--inv-panel-2); }
  .inv-fab-btn i { font-size: 22px; color: var(--inv-blue); }

  .inv-sheet-backdrop {
    position: fixed; inset: 0;
    background: rgba(15,23,42,.35);
    backdrop-filter: blur(8px) saturate(1.05);
    opacity: 0; pointer-events: none;
    transition: opacity .2s ease; z-index: 70;
  }
  .inv-sheet-backdrop.show { opacity: 1; pointer-events: auto; }

  .inv-sheet {
    position: fixed; left: 0; right: 0; bottom: -100%; z-index: 80;
    background: var(--inv-panel);
    border-radius: 18px 18px 0 0;
    box-shadow: 0 -20px 40px rgba(2,6,23,.16);
    padding: 14px 14px 18px;
    transition: bottom .28s ease, background .25s ease;
  }
  .inv-sheet.show { bottom: 0; }

  .inv-sheet-grab {
    width: 60px; height: 6px;
    background: var(--inv-border); border-radius: 999px;
    margin: 6px auto 12px;
    transition: background .25s ease;
  }
  .inv-sheet-title { font-weight: 800; color: var(--inv-text); margin: 4px 0 12px; transition: color .25s ease; }
  .inv-sheet-group { display: grid; gap: 14px; }
  .inv-sheet-label { font-weight: 700; font-size: .88rem; color: var(--inv-text-2); margin-bottom: 4px; transition: color .25s ease; }

  .inv-sheet-select {
    width: 100%; padding: 10px 12px; border-radius: 10px;
    background: var(--inv-input-bg); color: var(--inv-text);
    border: 1px solid var(--inv-input-brd);
    font-family: inherit; font-size: .92rem;
    transition: background .25s ease, border-color .25s ease, color .25s ease;
  }

  .inv-sheet-btn-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
  .inv-sheet-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
    height: 48px; width: 100%; border-radius: 12px; font-weight: 800;
    border: 1px solid var(--inv-border); background: var(--inv-panel);
    color: var(--inv-text); font-family: inherit; cursor: pointer;
    transition: background .15s ease, border-color .25s ease, color .25s ease;
  }
  .inv-sheet-btn.primary {
    background: var(--inv-blue-bg); color: var(--inv-blue-txt);
    border-color: var(--inv-blue-brd);
  }
  .inv-sheet-btn:hover { filter: brightness(1.04); }

  .inv-sheet-add {
    display: flex; align-items: center; gap: 12px; padding: 14px;
    border-radius: 14px; border: 1px solid var(--inv-green-brd);
    background: var(--inv-green-bg);
    text-decoration: none; color: var(--inv-green-txt); font-weight: 800;
    transition: background .15s ease, border-color .25s ease, color .25s ease;
  }
  .inv-sheet-add-ico {
    width: 40px; height: 40px; border-radius: 12px;
    display: grid; place-items: center;
    background: rgba(4,108,78,.12); color: var(--inv-green);
    font-size: 1.1rem;
  }
  .inv-sheet-add-sub { font-weight: 600; font-size: .875rem; margin-top: 2px; transition: color .25s ease; }

  /* ══════════════════════════════════════════════════════════
     RESPONSIVE
  ══════════════════════════════════════════════════════════ */
  @media (max-width: 576px) {
    .inv-hero-actions .export-desktop { display: none; }
    .inv-hero-actions .inv-select.cat-filter { display: none; }

    .inv-table.is-stacked thead { display: none; }
    .inv-table.is-stacked,
    .inv-table.is-stacked tbody,
    .inv-table.is-stacked tr.trow,
    .inv-table.is-stacked td.td { display: block; width: 100%; }
    .inv-table.is-stacked tr.trow { padding: 12px 14px; }
    .inv-table.is-stacked tr.trow + tr.trow { border-top: 1px solid var(--inv-border); }
    .inv-table.is-stacked td.td {
      border: none; padding: 10px 0;
      display: grid; grid-template-columns: minmax(96px,40%) 1fr;
      gap: 8px; align-items: flex-start; word-wrap: break-word;
    }
    .inv-table.is-stacked td.td::before { content: attr(data-label); font-weight: 700; color: var(--inv-muted); }
    .inv-table.is-stacked td.td[data-label="Acciones"] { grid-template-columns: 1fr; }
    .inv-table.is-stacked .inv-cell-actions { justify-content: flex-start; }
    .inv-table.is-stacked .inv-expand-row.open { display: block; }
    .inv-expand-panel { grid-template-columns: 1fr 1fr; }

    .inv-equip-main { align-items: flex-start; }
    .inv-equip-title { flex-direction: column; row-gap: 2px; }
    .inv-equip-dot   { display: none; }
  }

  /* ── Skeleton mientras carga la tabla ───────────────────── */
  .inv-skel-row td { padding: 16px 18px; }
  .inv-skel-bar {
    height: 14px; border-radius: 6px;
    background: linear-gradient(90deg, var(--inv-panel-2) 25%, var(--inv-panel-3) 37%, var(--inv-panel-2) 63%);
    background-size: 400% 100%;
    animation: skelShine 1.4s ease infinite;
  }
  @keyframes skelShine { 0%{background-position:100% 50%} 100%{background-position:0 50%} }
</style>

{{-- CSRF --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="inv-root">

  {{-- ══ OTP / BANK MODAL ══ --}}
  <div id="bankMask"  class="bank-mask"></div>
  <div id="bankModal" class="bank-modal" role="dialog" aria-modal="true">
    <div class="bank-card">
      <div class="bank-top">
        <div class="bank-brand">
          <div class="bank-badge"><i class="bi bi-shield-lock"></i></div>
          <div>
            <div id="bankTitle" class="bank-title">Confirmación segura</div>
            <div id="bankSub"   class="bank-sub">Escribe el PIN de 6 dígitos</div>
          </div>
        </div>
        <button type="button" id="bankClose" class="bank-close"><i class="bi bi-x-lg"></i></button>
      </div>
      <div class="bank-body">
        <div class="bank-alert">
          <div class="dot"></div>
          <div id="bankAlertText">Al completar los <b>6 dígitos</b>, se confirma automáticamente.</div>
        </div>
        <div class="otp-row">
          <input class="otp" inputmode="numeric" autocomplete="one-time-code" maxlength="1">
          <input class="otp" inputmode="numeric" maxlength="1">
          <input class="otp" inputmode="numeric" maxlength="1">
          <input class="otp" inputmode="numeric" maxlength="1">
          <input class="otp" inputmode="numeric" maxlength="1">
          <input class="otp" inputmode="numeric" maxlength="1">
        </div>
        <div class="bank-note">Puedes <b>pegar</b> el PIN completo.</div>
        <div id="otpLoading" class="loading-dots"><span></span><span></span><span></span></div>
      </div>
    </div>
  </div>

  {{-- ══ PAGE WRAP ══ --}}
  <div class="inv-wrap" x-data="InventarioUI()">

    {{-- HERO --}}
    <div class="inv-hero">
      <div class="d-flex align-items-center justify-content-between w-100 gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-2">
          <button onclick="volverInteligente();" class="inv-btn inv-btn-icon" title="Volver">
            <i class="bi bi-arrow-left" style="font-size:1.1rem"></i>
          </button>
          <a href="{{ url('/home') }}" class="inv-btn inv-btn-icon" title="Inicio">
            <i class="bi bi-house-door-fill" style="font-size:1.1rem"></i>
          </a>
        </div>
        <div class="d-flex align-items-center gap-3 flex-grow-1 justify-content-center" style="margin-right:96px;">
          <div class="inv-hero-chip">
            <i class="bi bi-clipboard-check" style="font-size:1.25rem;color:var(--inv-blue)"></i>
          </div>
          <h1 class="h4 mb-0" style="color:var(--inv-text);font-weight:800;">Inventario</h1>
        </div>
      </div>

      <div class="inv-hero-actions">
        <div class="inv-search">
          <i class="ico bi bi-search"></i>
          <input type="search" placeholder="Buscar equipo, serie, marca…" x-model="$store.inv.q">
        </div>

        <select class="inv-select" x-model="$store.inv.filtroEstado">
          <option value="">Todos los estados</option>
          <option value="registro">Registro</option>
          <option value="hojalateria">Hojalatería</option>
          <option value="mantenimiento">Mantenimiento</option>
          <option value="stock">Stock</option>
          <option value="vendido">Vendido</option>
          <option value="defectuoso">Defectuoso</option>
        </select>

        <select class="inv-select cat-filter export-desktop" x-model="$store.inv.filtroCategoria">
          <option value="">Todas las categorías</option>
          @foreach(collect($productos ?? [])->pluck('tipo_equipo')->unique()->filter()->sort()->values() as $cat)
            <option value="{{ $cat }}">{{ $cat }}</option>
          @endforeach
        </select>

        <button type="button" class="inv-btn inv-btn-blue inv-btn-icon export-desktop"
                data-bs-toggle="modal" data-bs-target="#exportModal" title="Exportar">
          <i class="bi bi-download"></i>
        </button>

        <button class="inv-btn inv-btn-icon" onclick="location.reload()" title="Actualizar">
          <i class="bi bi-arrow-clockwise"></i>
        </button>

        <a href="{{ route('registros.create') }}" class="inv-btn inv-btn-green inv-btn-icon" title="Agregar equipo">
          <i class="bi bi-plus-circle"></i>
        </a>
      </div>
    </div>

    {{-- TABLA --}}
    <div class="inv-table-wrap">
      <div class="inv-table-scroll">
        <table class="inv-table" :class="{'is-stacked': isMobile}">
          <colgroup><col><col><col><col><col><col></colgroup>
          <thead>
            <tr>
              <th class="th">Equipo</th>
              <th class="th">Serie</th>
              <th class="th">Estado</th>
              <th class="th">Fecha adquisición</th>
              <th class="th">Registrado por</th>
              <th class="th" style="text-align:right;">Acciones</th>
            </tr>
          </thead>
          {{--
            IMPORTANTE: el <tbody> ahora empieza VACÍO (solo un
            skeleton de carga). JavaScript es quien pinta únicamente
            las filas de la página actual (10 por defecto) a partir
            del JSON en #inv-data. Esto evita mandar cientos de filas
            de HTML + imágenes que el navegador tenía que descargar
            aunque estuvieran ocultas.
          --}}
          <tbody id="inv-tbody">
            <tr class="inv-skel-row"><td class="td" colspan="6"><div class="inv-skel-bar" style="width:70%"></div></td></tr>
            <tr class="inv-skel-row"><td class="td" colspan="6"><div class="inv-skel-bar" style="width:55%"></div></td></tr>
            <tr class="inv-skel-row"><td class="td" colspan="6"><div class="inv-skel-bar" style="width:65%"></div></td></tr>
          </tbody>
        </table>
      </div>

      {{-- Paginación --}}
      <div class="inv-pagination" id="paginationWrap">
        <div class="inv-pag-info" id="paginationInfo"></div>
        <div class="inv-pag-btns">
          <button class="inv-pag-btn" id="pagPrev" disabled>
            <i class="bi bi-chevron-left"></i> Anterior
          </button>
          <div class="inv-pag-pages" id="pagPages"></div>
          <button class="inv-pag-btn" id="pagNext">
            Siguiente <i class="bi bi-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>

    {{-- MODAL EXPORTAR --}}
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content inv-modal-content">
          <div class="modal-header inv-modal-header">
            <div>
              <div class="fw-bold" id="exportModalLabel" style="color:var(--inv-text);letter-spacing:.2px">Exportar inventario</div>
              <div class="small" style="color:var(--inv-muted)">El PDF se agrupa por <b>categoría / tipo de equipo</b>.</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body inv-modal-body">
            <div class="inv-option-card mb-3">
              <div class="fw-semibold mb-1" style="color:var(--inv-text)">Estado a exportar</div>
              <small>Se usará el mismo estado filtrado en la vista.</small>
            </div>
            <div class="inv-option-card mb-3">
              <div class="fw-semibold mb-2" style="color:var(--inv-text)">Formato</div>
              <div class="d-flex gap-2">
                <input class="btn-check" type="radio" name="fmtExport" id="fmtPdf" value="pdf" checked>
                <label class="btn btn-outline-primary w-100" for="fmtPdf">
                  <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                </label>
                <input class="btn-check" type="radio" name="fmtExport" id="fmtExcel" value="excel">
                <label class="btn btn-outline-success w-100" for="fmtExcel">
                  <i class="bi bi-file-earmark-spreadsheet me-1"></i> Excel
                </label>
              </div>
              <div class="mt-3">
                <small id="pdfHint"   style="color:var(--inv-muted)">El PDF se abrirá en otra pestaña.</small>
                <small id="excelHint" class="d-none" style="color:var(--inv-muted)">Se descargará un archivo .xlsx</small>
              </div>
            </div>
            <div class="d-flex gap-2">
              <button type="button" class="inv-btn w-100" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" class="inv-btn inv-btn-blue w-100" id="btnConfirmExport">
                <i class="bi bi-download"></i> Exportar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- FAB --}}
    <div class="inv-fab">
      <button type="button" id="openInvSheet" class="inv-fab-btn" aria-label="Filtros">
        <i class="bi bi-sliders"></i>
      </button>
    </div>
    <div class="inv-sheet-backdrop" id="invSheetBackdrop"></div>
    <div class="inv-sheet" id="invSheetPanel" x-data>
      <div class="inv-sheet-grab"></div>
      <div class="inv-sheet-title">Filtros</div>
      <div class="inv-sheet-group">
        <div>
          <div class="inv-sheet-label">Estado</div>
          <select class="inv-sheet-select" x-model="$store.inv.filtroEstado">
            <option value="">Todos los estados</option>
            <option value="registro">Registro</option>
            <option value="hojalateria">Hojalatería</option>
            <option value="mantenimiento">Mantenimiento</option>
            <option value="stock">Stock</option>
            <option value="vendido">Vendido</option>
            <option value="defectuoso">Defectuoso</option>
          </select>
        </div>
        <div>
          <div class="inv-sheet-label">Categoría</div>
          <select class="inv-sheet-select" x-model="$store.inv.filtroCategoria">
            <option value="">Todas las categorías</option>
            @foreach(collect($productos ?? [])->pluck('tipo_equipo')->unique()->filter()->sort()->values() as $cat)
              <option value="{{ $cat }}">{{ $cat }}</option>
            @endforeach
          </select>
        </div>
        <button type="button" class="inv-sheet-btn primary"
          @click="document.getElementById('closeInvSheet')?.click(); setTimeout(()=>document.querySelector('[data-bs-target=\"#exportModal\"]')?.click(),80);">
          <i class="bi bi-download"></i> Exportar
        </button>
        <a href="{{ route('registros.create') }}" class="inv-sheet-add">
          <div class="inv-sheet-add-ico"><i class="bi bi-plus-lg"></i></div>
          <div>
            <div style="font-weight:800;">Agregar nuevo equipo</div>
            <div class="inv-sheet-add-sub">Regístralo y adjunta evidencias.</div>
          </div>
        </a>
        <div class="inv-sheet-btn-row">
          <button class="inv-sheet-btn primary" id="applyInvSheet"><i class="bi bi-check2-circle"></i> Aplicar</button>
          <button class="inv-sheet-btn" id="closeInvSheet"><i class="bi bi-x-lg"></i> Cerrar</button>
        </div>
      </div>
    </div>

    {{-- MODAL BARCODE --}}
    <div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content inv-modal-content">
          <div class="modal-header inv-modal-header">
            <div>
              <div class="fw-bold" id="barcodeModalLabel" style="color:var(--inv-text);letter-spacing:.2px">Imprimir etiqueta</div>
              <div class="small" id="barcodeModalSerie" style="color:var(--inv-muted)"></div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body inv-modal-body">
            <div class="inv-barcode-preview">
              <div id="barcodeSpinner" class="inv-barcode-spinner">
                <div class="inv-spinner-ring"></div>
                <span style="font-size:12px;color:var(--inv-muted)">Generando etiqueta…</span>
              </div>
              <iframe id="barcodeFrame" src="" style="width:100%;height:100%;border:none;display:block;" title="Vista previa de etiqueta"></iframe>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:12px;">
              <a id="barcodePrintLink" href="#" target="_blank" class="inv-btn inv-btn-blue" style="justify-content:center;">
                <i class="bi bi-printer"></i> Imprimir
              </a>
              <a id="barcodeDownloadLink" href="#" download class="inv-btn inv-btn-blue" style="justify-content:center;">
                <i class="bi bi-download"></i> Descargar
              </a>
            </div>
            <div style="display:flex;justify-content:center;margin-top:10px;">
              <button type="button" class="inv-btn inv-btn-green" data-bs-dismiss="modal" style="padding:10px 40px;">
                Cerrar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>{{-- /inv-wrap --}}
</div>{{-- /inv-root --}}

@php
  /*
   * ══════════════════════════════════════════════════════════
   * DATOS PARA JAVASCRIPT (evita renderizar cientos de <tr> en HTML)
   * ══════════════════════════════════════════════════════════
   * Orden: fecha de adquisición más reciente primero.
   *
   * IMPORTANTE — N+1 query:
   * Solo usamos la relación "procesos" si YA viene precargada
   * desde el controlador (ej. Producto::with('procesos')->get()
   * o ->with(['procesos' => fn($q) => $q->latest()])).
   * Si no viene precargada, usamos updated_at/created_at
   * directamente SIN lanzar una consulta nueva por cada fila.
   * Antes, cada producto sin la relación precargada disparaba
   * una consulta extra a la BD — con 100+ productos eso es
   * lo que hacía que la vista "se quedara cargando".
   *
   * RECOMENDACIÓN para tu controlador:
   *   $productos = Producto::with(['procesos' => function ($q) {
   *       $q->orderByDesc('created_at');
   *   }])->get();
   */
  $productosOrdenados = collect($productos ?? [])
    ->sortByDesc(fn($r) => $r->fecha_adquisicion
        ? \Carbon\Carbon::parse($r->fecha_adquisicion)->timestamp
        : 0)
    ->values();

  $filasInventario = $productosOrdenados->map(function ($r) {
      $estado = $r->estado_proceso
        ?: ([1=>'stock',2=>'vendido',3=>'mantenimiento',4=>'defectuoso'][$r->estado_actual] ?? 'registro');

      $fechaUltimoEstado = null;
      if ($r->relationLoaded('procesos')) {
          $procesoEstado = $r->procesos->where('tipo_proceso', $estado)->sortByDesc('created_at')->first();
          if ($procesoEstado) $fechaUltimoEstado = $procesoEstado->created_at;
      }
      if (!$fechaUltimoEstado) $fechaUltimoEstado = $r->updated_at ?: $r->created_at;

      $barcodeUrl = Route::has('registros.imprimir-barcode') ? route('registros.imprimir-barcode', $r->id) : '#';
      $detalleUrl = Route::has('inventario.detalle') ? route('inventario.detalle', $r->id) : url('/inventario/detalle/'.$r->id);

      $badgeClass = match($estado){
        'hojalateria'   => 'inv-badge-hojalateria',
        'mantenimiento' => 'inv-badge-mantenimiento',
        'stock'         => 'inv-badge-stock',
        'vendido'       => 'inv-badge-vendido',
        'defectuoso'    => 'inv-badge-defectuoso',
        default         => 'inv-badge-registro'
      };

      $evidenciasRaw = array_filter([
        $r->evidencia1 ?? null,
        $r->evidencia2 ?? null,
        $r->evidencia3 ?? null,
      ]);
      $evidencias = array_values(array_map(
        fn($ev) => Str::startsWith($ev, ['http://','https://']) ? $ev : asset('storage/'.ltrim($ev,'/')),
        $evidenciasRaw
      ));

      return [
        'id'               => $r->id,
        'tipo'             => $r->tipo_equipo ?? 'Equipo',
        'subtipo'          => $r->subtipo_equipo ?? '—',
        'marca'            => $r->marca ?? '',
        'modelo'           => $r->modelo ?? '',
        'serie'            => $r->numero_serie ?? '—',
        'estado'           => $estado,
        'badgeClass'       => $badgeClass,
        'ultimoCambio'     => $fechaUltimoEstado ? $fechaUltimoEstado->format('Y-m-d H:i') : '—',
        'fechaAdquisicion' => optional($r->fecha_adquisicion)->format('Y-m-d') ?? '—',
        'registradoPor'    => $r->user_name ?? '—',
        'descripcion'      => $r->descripcion ?? null,
        'imagenPrincipal'  => $evidencias[0] ?? null,
        'evidencias'       => $evidencias,
        'detalleUrl'       => $detalleUrl,
        'editUrl'          => route('registros.edit', $r->id),
        'pinUrl'           => route('registros.validar-pin-edicion', $r->id),
        'deleteUrl'        => route('registros.destroy', $r->id),
        'barcodeUrl'       => $barcodeUrl,
        'previewUrl'       => Route::has('registros.preview-barcode') ? route('registros.preview-barcode', $r->id) : $barcodeUrl,
      ];
  })->values();
@endphp

{{-- JSON con TODOS los productos: liviano (sin HTML), lo consume el JS de abajo --}}
<script type="application/json" id="inv-data">{!! $filasInventario->toJson() !!}</script>

@endsection

@section('scripts')
<script>
/* ════════════════════════════════
   BOTÓN VOLVER — sessionStorage
════════════════════════════════ */
(function () {
  const MODULE_PREFIX   = '/inventario';
  const STORAGE_KEY     = 'inventario_volver_target';
  const FALLBACK_URL    = "{{ url('/home') }}";
  const INTERNAL_PREFIXES = [MODULE_PREFIX, '/registros'];
  const referrer = document.referrer;
  let isInternal = false;
  if (referrer) {
    try {
      const u = new URL(referrer);
      isInternal = INTERNAL_PREFIXES.some(p => u.origin === window.location.origin && u.pathname.startsWith(p));
    } catch {}
  }
  if (referrer && !isInternal) {
    sessionStorage.setItem(STORAGE_KEY, referrer);
  } else if (!sessionStorage.getItem(STORAGE_KEY)) {
    sessionStorage.setItem(STORAGE_KEY, FALLBACK_URL);
  }
  window.volverInteligente = () => {
    window.location.href = sessionStorage.getItem(STORAGE_KEY) || FALLBACK_URL;
  };
})();

/* ════════════════════════════════
   ALPINE STORE + COMPONENTE
════════════════════════════════ */
document.addEventListener('alpine:init', () => {
  Alpine.store('inv', { q: '', filtroEstado: '', filtroCategoria: '' });
  Alpine.data('InventarioUI', () => ({
    isMobile: window.matchMedia('(max-width: 576px)').matches,
  }));
});

/* ════════════════════════════════
   HELPER: unlock UI
════════════════════════════════ */
function unlockUi() {
  document.body.classList.remove('modal-open');
  document.body.style.removeProperty('overflow');
  document.body.style.removeProperty('padding-right');
  document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
  document.getElementById('invSheetBackdrop')?.classList.remove('show');
  document.getElementById('invSheetPanel')?.classList.remove('show');
}

function escapeHtml(str) {
  return (str ?? '').toString()
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

/* ════════════════════════════════
   DATOS: se leen UNA vez desde el
   <script type="application/json">
   generado por el servidor (liviano,
   sin HTML) y a partir de aquí TODO
   el render de filas lo hace JS.
════════════════════════════════ */
let INV_DATA = [];
try {
  const raw = document.getElementById('inv-data')?.textContent || '[]';
  INV_DATA = JSON.parse(raw);
} catch (e) {
  console.error('No se pudo leer inv-data', e);
  INV_DATA = [];
}

const PER_PAGE = 10;
let currentPage = 1;

function getFilteredRows() {
  const store = Alpine.store('inv');
  const q   = (store.q || '').toLowerCase().trim();
  const est = (store.filtroEstado    || '').toLowerCase().trim();
  const cat = (store.filtroCategoria || '').toLowerCase().trim();
  return INV_DATA.filter(item => {
    if (est && String(item.estado || '').toLowerCase().trim() !== est) return false;
    if (cat && String(item.tipo   || '').toLowerCase().trim() !== cat) return false;
    if (!q) return true;
    return [item.tipo, item.subtipo, item.marca, item.modelo, item.serie, item.registradoPor]
      .join(' ').toLowerCase().includes(q);
  });
}

/* ── Construye el HTML de UNA fila + su fila expandible ── */
function buildRowHtml(item) {
  const imgTile = item.imagenPrincipal
    ? `<img src="${escapeHtml(item.imagenPrincipal)}" alt="prev" loading="lazy">`
    : `<i class="bi bi-box" style="color:var(--inv-muted)"></i>`;

  const evidenciasHtml = item.evidencias && item.evidencias.length
    ? `<div style="grid-column:1/-1">
         <div style="font-size:11px;font-weight:700;color:var(--inv-muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px;">Evidencias</div>
         <div class="inv-expand-images">
           ${item.evidencias.map(ev => `<div class="inv-expand-thumb"><img src="${escapeHtml(ev)}" alt="evidencia" loading="lazy"></div>`).join('')}
         </div>
       </div>`
    : '';

  const descripcionHtml = item.descripcion
    ? `<div class="inv-expand-field" style="grid-column:1/-1"><label>Descripción</label><span>${escapeHtml(item.descripcion)}</span></div>`
    : '';

  return `
    <tr class="trow" id="row-${item.id}">
      <td class="td" data-label="Equipo">
        <div class="inv-equip-main">
          <div class="inv-tile">${imgTile}</div>
          <div>
            <div class="inv-equip-title">
              <span class="inv-equip-tipo">${escapeHtml(item.tipo)}</span>
              <span class="inv-equip-dot">•</span>
              <span class="inv-equip-subtipo">${escapeHtml(item.subtipo)}</span>
            </div>
            <div class="inv-equip-model">${escapeHtml(item.marca)} ${escapeHtml(item.modelo)}</div>
          </div>
        </div>
      </td>
      <td class="td" data-label="Serie">
        <span style="font-weight:600;color:var(--inv-text)">${escapeHtml(item.serie)}</span>
      </td>
      <td class="td" data-label="Estado">
        <span class="inv-badge ${item.badgeClass}">
          <i class="bi bi-circle-fill" style="font-size:.55rem"></i>
          <span class="text-capitalize">${escapeHtml(item.estado)}</span>
        </span>
        <div class="inv-state-meta">Último cambio: ${escapeHtml(item.ultimoCambio)}</div>
      </td>
      <td class="td" data-label="Fecha adquisición">${escapeHtml(item.fechaAdquisicion)}</td>
      <td class="td" data-label="Registrado por">${escapeHtml(item.registradoPor)}</td>
      <td class="td" data-label="Acciones">
        <div class="inv-cell-actions">
          <a class="inv-btn inv-btn-blue" href="${item.detalleUrl}" title="Ver detalle"><i class="bi bi-eye"></i></a>
          <button type="button" class="inv-btn js-inv-edit"
                  data-edit-url="${item.editUrl}" data-pin-url="${item.pinUrl}" title="Editar">
            <i class="bi bi-pencil-square"></i>
          </button>
          <button type="button" class="inv-btn js-inv-delete"
                  data-delete-url="${item.deleteUrl}" style="color:#dc2626;" title="Eliminar">
            <i class="bi bi-trash"></i>
          </button>
          <button type="button" class="inv-btn js-inv-barcode"
                  data-barcode-url="${item.barcodeUrl}" data-preview-url="${item.previewUrl}"
                  data-serie="${escapeHtml(item.serie)}" title="Imprimir etiqueta">
            <i class="bi bi-upc-scan"></i>
          </button>
        </div>
      </td>
    </tr>
    <tr class="inv-expand-row" id="expand-${item.id}">
      <td colspan="6">
        <div class="inv-expand-panel">
          ${evidenciasHtml}
          <div class="inv-expand-field"><label>Tipo</label><span>${escapeHtml(item.tipo)}</span></div>
          <div class="inv-expand-field"><label>Subtipo</label><span>${escapeHtml(item.subtipo)}</span></div>
          <div class="inv-expand-field"><label>Marca</label><span>${escapeHtml(item.marca) || '—'}</span></div>
          <div class="inv-expand-field"><label>Modelo</label><span>${escapeHtml(item.modelo) || '—'}</span></div>
          <div class="inv-expand-field"><label>Número de serie</label><span>${escapeHtml(item.serie)}</span></div>
          <div class="inv-expand-field"><label>Estado</label><span class="text-capitalize">${escapeHtml(item.estado)}</span></div>
          <div class="inv-expand-field"><label>Fecha adquisición</label><span>${escapeHtml(item.fechaAdquisicion)}</span></div>
          <div class="inv-expand-field"><label>Registrado por</label><span>${escapeHtml(item.registradoPor)}</span></div>
          ${descripcionHtml}
          <div style="grid-column:1/-1">
            <button type="button" class="inv-expand-close js-inv-expand" data-id="${item.id}">
              <i class="bi bi-chevron-up"></i> Cerrar detalle
            </button>
          </div>
        </div>
      </td>
    </tr>
  `;
}

function renderTable() {
  const filtered   = getFilteredRows();
  const total      = filtered.length;
  const totalPages = Math.max(1, Math.ceil(total / PER_PAGE));
  currentPage = Math.min(currentPage, totalPages);
  const start = (currentPage - 1) * PER_PAGE;
  const pageItems = filtered.slice(start, start + PER_PAGE);

  const tbody = document.getElementById('inv-tbody');
  if (tbody) {
    tbody.innerHTML = pageItems.length
      ? pageItems.map(buildRowHtml).join('')
      : `<tr class="trow"><td class="td" colspan="6" style="color:var(--inv-muted);">No hay registros aún.</td></tr>`;
  }

  const info = document.getElementById('paginationInfo');
  if (info) {
    const from = total === 0 ? 0 : start + 1;
    const to   = Math.min(start + PER_PAGE, total);
    info.textContent = `Mostrando ${from}–${to} de ${total} registros`;
  }

  const pagPrev = document.getElementById('pagPrev');
  const pagNext = document.getElementById('pagNext');
  if (pagPrev) pagPrev.disabled = currentPage <= 1;
  if (pagNext) pagNext.disabled = currentPage >= totalPages;

  const pagPages = document.getElementById('pagPages');
  if (pagPages) {
    pagPages.innerHTML = '';
    const range = getPaginationRange(currentPage, totalPages);
    range.forEach(p => {
      if (p === '…') {
        const sp = document.createElement('span');
        sp.textContent = '…';
        sp.style.cssText = 'display:grid;place-items:center;width:34px;height:34px;color:var(--inv-muted);font-size:13px;';
        pagPages.appendChild(sp);
      } else {
        const btn = document.createElement('button');
        btn.className   = 'inv-pag-page' + (p === currentPage ? ' active' : '');
        btn.textContent = p;
        btn.addEventListener('click', () => { currentPage = p; renderTable(); });
        pagPages.appendChild(btn);
      }
    });
  }
}

function getPaginationRange(current, total) {
  if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
  if (current <= 4)          return [1,2,3,4,5,'…',total];
  if (current >= total - 3)  return [1,'…',total-4,total-3,total-2,total-1,total];
  return [1,'…',current-1,current,current+1,'…',total];
}

/* ── Debounce para el buscador: evita re-renderizar en cada tecla ── */
let searchDebounceTimer = null;
function scheduleRender(immediate = false) {
  if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
  if (immediate) { renderTable(); return; }
  searchDebounceTimer = setTimeout(renderTable, 180);
}

document.addEventListener('alpine:init', () => {
  setTimeout(() => {
    const store = Alpine.store('inv');
    // El buscador (q) usa debounce; los selects (estado/categoria) responden al instante.
    let qVal = store.q, estVal = store.filtroEstado, catVal = store.filtroCategoria;

    Object.defineProperty(store, 'q', {
      get() { return qVal; },
      set(v) { qVal = v; currentPage = 1; scheduleRender(false); }
    });
    Object.defineProperty(store, 'filtroEstado', {
      get() { return estVal; },
      set(v) { estVal = v; currentPage = 1; scheduleRender(true); }
    });
    Object.defineProperty(store, 'filtroCategoria', {
      get() { return catVal; },
      set(v) { catVal = v; currentPage = 1; scheduleRender(true); }
    });

    renderTable();
  }, 100);
});

document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('pagPrev')?.addEventListener('click', () => { currentPage--; renderTable(); });
  document.getElementById('pagNext')?.addEventListener('click', () => { currentPage++; renderTable(); });

  /* ── BOTTOM SHEET ── */
  const openBtn  = document.getElementById('openInvSheet');
  const closeBtn = document.getElementById('closeInvSheet');
  const applyBtn = document.getElementById('applyInvSheet');
  const backdrop = document.getElementById('invSheetBackdrop');
  const panel    = document.getElementById('invSheetPanel');
  const openSheet  = () => { if (window.matchMedia('(max-width:576px)').matches) { panel.classList.add('show'); backdrop.classList.add('show'); } };
  const hideSheet  = () => { panel.classList.remove('show'); backdrop.classList.remove('show'); };
  openBtn?.addEventListener('click', openSheet);
  closeBtn?.addEventListener('click', () => { hideSheet(); unlockUi(); });
  applyBtn?.addEventListener('click', () => { hideSheet(); unlockUi(); });
  backdrop?.addEventListener('click', () => { hideSheet(); unlockUi(); });
  document.getElementById('exportModal')?.addEventListener('hidden.bs.modal', unlockUi);

  /* ── EXPORT HINTS ── */
  const fmtPdf   = document.getElementById('fmtPdf');
  const fmtExcel = document.getElementById('fmtExcel');
  const pdfHint  = document.getElementById('pdfHint');
  const exHint   = document.getElementById('excelHint');
  const refreshHints = () => { pdfHint?.classList.toggle('d-none', !fmtPdf?.checked); exHint?.classList.toggle('d-none', fmtPdf?.checked); };
  fmtPdf?.addEventListener('change', refreshHints);
  fmtExcel?.addEventListener('change', refreshHints);
  refreshHints();

  /* ── EXPORT ACTION ── */
  document.getElementById('btnConfirmExport')?.addEventListener('click', () => {
    const fmt    = document.querySelector('input[name="fmtExport"]:checked')?.value || 'pdf';
    const estado = (Alpine.store('inv')?.filtroEstado || '').toLowerCase().trim();
    const modalEl = document.getElementById('exportModal');
    const modalIns = modalEl && window.bootstrap?.Modal
      ? (bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl)) : null;
    const hardClose = () => { modalIns?.hide(); unlockUi(); };
    const base = fmt === 'excel' ? @json(route('registros.exportExcel')) : @json(route('registros.export.pdf'));
    const url  = new URL(base, window.location.origin);
    if (estado) url.searchParams.set('estado_proceso', estado);
    hardClose();
    setTimeout(() => window.open(url.toString(), '_blank', 'noopener'), 80);
  });

  /* ── CLICKS DELEGADOS (expand / edit / delete / barcode) ── */
  document.addEventListener('click', e => {
    /* Expandir */
    const expandBtn = e.target.closest('.js-inv-expand');
    if (expandBtn) {
      const id = expandBtn.dataset.id;
      const expandEl = document.getElementById('expand-' + id);
      const rowEl    = document.getElementById('row-' + id);
      if (!expandEl) return;
      const isOpen = expandEl.classList.contains('open');
      document.querySelectorAll('.inv-expand-row.open').forEach(el => { el.classList.remove('open'); el.style.display = 'none'; });
      document.querySelectorAll('.trow.expanded').forEach(el => el.classList.remove('expanded'));
      if (!isOpen) { expandEl.classList.add('open'); expandEl.style.display = ''; rowEl?.classList.add('expanded'); }
      return;
    }
    /* Editar */
    const editBtn = e.target.closest('.js-inv-edit');
    if (editBtn) { openOTP('editar', { edit: editBtn.dataset.editUrl, pin: editBtn.dataset.pinUrl }); return; }
    /* Eliminar */
    const delBtn = e.target.closest('.js-inv-delete');
    if (delBtn) { openOTP('eliminar', { delete: delBtn.dataset.deleteUrl }); return; }
    /* Barcode */
    const barcodeBtn = e.target.closest('.js-inv-barcode');
    if (barcodeBtn) {
      const url        = barcodeBtn.dataset.barcodeUrl;
      const previewUrl = barcodeBtn.dataset.previewUrl || url;
      const serie      = barcodeBtn.dataset.serie || '—';
      if (!url || url === '#') return;
      const subtitle  = document.getElementById('barcodeModalSerie');
      const frame     = document.getElementById('barcodeFrame');
      const spinner   = document.getElementById('barcodeSpinner');
      const printLink = document.getElementById('barcodePrintLink');
      const dlLink    = document.getElementById('barcodeDownloadLink');
      if (subtitle) subtitle.textContent = serie !== '—' ? `Serie: ${serie}` : '';
      if (spinner)  spinner.style.display = 'flex';
      if (frame) { frame.src = ''; frame.onload = () => { if (spinner) spinner.style.display = 'none'; }; setTimeout(() => { frame.src = previewUrl; }, 150); }
      if (printLink) { printLink.href = url; printLink.removeAttribute('download'); }
      if (dlLink)    { dlLink.href = url; dlLink.setAttribute('download', `Etiqueta_${serie}.pdf`); }
      const modalEl = document.getElementById('barcodeModal');
      if (modalEl && window.bootstrap?.Modal) {
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modalEl.addEventListener('hidden.bs.modal', () => { if (frame) frame.src = ''; if (spinner) spinner.style.display = 'flex'; }, { once: true });
        modal.show();
      }
    }
  });

  unlockUi();
});

/* ════════════════════════════════
   OTP / BANK MODAL
════════════════════════════════ */
(function () {
  const CSRF          = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const bankMask      = document.getElementById('bankMask');
  const bankModal     = document.getElementById('bankModal');
  const bankClose     = document.getElementById('bankClose');
  const bankTitle     = document.getElementById('bankTitle');
  const bankSub       = document.getElementById('bankSub');
  const bankAlertText = document.getElementById('bankAlertText');
  const otpInputs     = Array.from(document.querySelectorAll('.otp'));
  const bankCard      = document.querySelector('.bank-card');
  const loading       = document.getElementById('otpLoading');

  let activeMode = null, activeEditUrl = null, activePinUrl = null, activeDeleteUrl = null;
  let submitting = false;

  const onlyDigits = s => (s||'').toString().replace(/\D+/g,'');
  const getOTP     = ()  => otpInputs.map(i => i.value || '').join('');

  function clearOTP() {
    otpInputs.forEach(i => { i.value = ''; i.classList.remove('error'); i.disabled = false; });
    submitting = false;
    if (loading) loading.style.display = 'none';
  }
  function focusFirst() { setTimeout(() => otpInputs[0]?.focus(), 60); }

  function openOTP(mode, urls = {}) {
    activeMode = mode; activeEditUrl = urls.edit || null;
    activePinUrl = urls.pin || null; activeDeleteUrl = urls.delete || null;
    if (mode === 'editar') {
      if (bankTitle)     bankTitle.textContent  = 'Autorización de edición';
      if (bankSub)       bankSub.textContent    = 'PIN de 6 dígitos para continuar';
      if (bankAlertText) bankAlertText.innerHTML = 'Al completar los <b>6 dígitos</b>, se abrirá la edición.';
    } else {
      if (bankTitle)     bankTitle.textContent  = 'Confirmar eliminación';
      if (bankSub)       bankSub.textContent    = 'PIN de 6 dígitos para eliminar';
      if (bankAlertText) bankAlertText.innerHTML = 'Al completar los <b>6 dígitos</b>, se eliminará el registro.';
    }
    clearOTP();
    bankMask?.classList.add('open');
    bankModal?.classList.add('open');
    focusFirst();
  }
  window.openOTP = openOTP;

  function closeOTP() {
    bankMask?.classList.remove('open');
    bankModal?.classList.remove('open');
    activeMode = activeEditUrl = activePinUrl = activeDeleteUrl = null;
  }
  function shake()      { if (!bankCard) return; bankCard.classList.remove('shake'); void bankCard.offsetWidth; bankCard.classList.add('shake'); }
  function flashError() { otpInputs.forEach(i => i.classList.add('error')); setTimeout(() => otpInputs.forEach(i => i.classList.remove('error')), 420); shake(); }
  function resetForRetry(msg) {
    submitting = false;
    if (loading) loading.style.display = 'none';
    otpInputs.forEach(i => { i.value = ''; i.disabled = false; i.classList.remove('error'); });
    flashError();
    alert(msg || 'PIN incorrecto, intenta de nuevo.');
    focusFirst();
  }

  async function runAction(pin) {
    if (activeMode === 'editar') {
      try {
        if (activePinUrl) {
          const r = await fetch(activePinUrl, { method:'POST', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'}, body: JSON.stringify({aprobacion_pin:pin}) });
          const d = await r.json().catch(() => ({}));
          if (!r.ok) throw new Error(d?.message || d?.error || 'PIN incorrecto.');
        }
        const redirectUrl = activeEditUrl;
        closeOTP();
        window.location.href = redirectUrl;
      } catch(e) { resetForRetry(e?.message); }
      return;
    }
    if (activeMode === 'eliminar') {
      try {
        const r = await fetch(activeDeleteUrl, { method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'}, body: JSON.stringify({aprobacion_pin:pin}) });
        const d = await r.json().catch(() => ({}));
        if (!r.ok) throw new Error(d?.message || d?.error || 'No se pudo eliminar.');
        closeOTP();
        window.location.reload();
      } catch(e) { resetForRetry(e?.message); }
    }
  }

  function autoSubmit() {
    if (submitting) return;
    const pin = getOTP();
    if (pin.length === 6 && !otpInputs.some(i => !i.value)) {
      submitting = true;
      otpInputs.forEach(i => i.disabled = true);
      if (loading) loading.style.display = 'flex';
      runAction(pin);
    }
  }

  otpInputs.forEach((input, idx) => {
    input.addEventListener('input', () => {
      input.value = onlyDigits(input.value).slice(0, 1);
      if (input.value && otpInputs[idx + 1]) otpInputs[idx + 1].focus();
      autoSubmit();
    });
    input.addEventListener('keydown', e => {
      if (e.key === 'Backspace' && !input.value && otpInputs[idx - 1]) { otpInputs[idx - 1].focus(); otpInputs[idx - 1].value = ''; }
    });
    input.addEventListener('paste', e => {
      e.preventDefault();
      const paste = onlyDigits((e.clipboardData || window.clipboardData).getData('text')).slice(0, 6);
      if (!paste) return;
      clearOTP();
      paste.split('').forEach((ch, i) => { if (otpInputs[i]) otpInputs[i].value = ch; });
      otpInputs[Math.min(paste.length, 6) - 1]?.focus();
      autoSubmit();
    });
  });

  bankMask?.addEventListener('click',  closeOTP);
  bankClose?.addEventListener('click', closeOTP);
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeOTP(); });
})();
</script>
@endsection