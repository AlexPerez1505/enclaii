@extends('layouts.app')

@section('title', 'Reporte Asistencias (Excel)')
@section('titulo', 'Asistencias')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

@php
  // ✅ Mes por defecto: mes actual (ej: 2025-12)
  $monthParam = request('month', \Carbon\Carbon::now()->format('Y-m'));

  try {
    $monthStart = \Carbon\Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
  } catch (\Throwable $e) {
    $monthParam = \Carbon\Carbon::now()->format('Y-m');
    $monthStart = \Carbon\Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
  }

  $daysInMonth = $monthStart->daysInMonth;

  $days = [];
  for ($d=1; $d<=$daysInMonth; $d++) {
    $days[] = $monthStart->copy()->day($d)->toDateString(); // Y-m-d
  }

  $asistencias = $asistencias ?? collect();

  // mapa: [user_id][Y-m-d] => asistencia
  $map = [];
  foreach ($asistencias as $a) {
    $ymd = \Carbon\Carbon::parse($a->fecha)->toDateString();
    $map[$a->user_id][$ymd] = $a;
  }

  $mesNombre = $monthStart->copy()->locale('es')->translatedFormat('F Y');

  // ✅ Para que no truene "asistencias.horizontal": usamos URL directa
  $actionUrl = url('/asistencias/historial');
@endphp

<style>
  :root{
    --bg:#f6f7fb; --card:#fff; --ink:#0f172a; --muted:#64748b; --line:#e5e7eb; --soft:#f8fafc;
    --ok:#16a34a; --warn:#f59e0b; --bad:#ef4444;
    --shadow:0 14px 40px rgba(2,6,23,.08);
    --radius:18px;
    --focus:0 0 0 4px rgba(37,99,235,.12);
  }

  body{ background:var(--bg)!important; font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,Arial; }

  /* ✅ Más compacto: panel más “slim” pero pro */
  .wrap{ max-width:1480px; margin:14px auto; padding:0 12px; }
  .panel{ background:var(--card); border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow); overflow:hidden; }

  .head{
    padding:14px 16px;
    border-bottom:1px solid var(--line);
    display:flex; align-items:center; justify-content:space-between;
    gap:10px; flex-wrap:wrap;
  }

  .title{
    margin:0;
    font-size:16px;
    font-weight:900;
    color:var(--ink);
    letter-spacing:-.02em;
    display:flex; align-items:center; gap:10px;
  }
  .title .mini-dot{
    width:10px;height:10px;border-radius:999px;
    background:linear-gradient(180deg, rgba(37,99,235,.25), rgba(37,99,235,.08));
    border:1px solid rgba(37,99,235,.18);
    box-shadow:0 8px 16px rgba(2,6,23,.06);
  }
  .sub{ margin:6px 0 0; color:var(--muted); font-size:12px; line-height:1.35; }

  .control{
    border:1px solid var(--line);
    border-radius:12px;
    background:#fff;
    padding:8px 10px;
    font-size:13px;
    color:var(--ink);
    outline:none;
    transition:.18s ease;
  }
  .control:hover{ border-color:rgba(15,23,42,.18); }
  .control:focus{ border-color:rgba(37,99,235,.45); box-shadow:var(--focus); }

  .legend{
    padding:10px 16px;
    border-bottom:1px solid var(--line);
    display:flex; gap:8px; flex-wrap:wrap;
    align-items:center;
    color:var(--muted);
    font-size:11px;
    background:linear-gradient(180deg, rgba(248,250,252,.95), rgba(248,250,252,.65));
  }
  .pill{
    display:inline-flex; align-items:center; gap:8px;
    padding:6px 9px;
    border-radius:999px;
    border:1px solid rgba(229,231,235,.9);
    background:#fff;
    font-weight:900;
  }
  .dot{ width:9px; height:9px; border-radius:999px; display:inline-block; }
  .dot-ok{ background:rgba(22,163,74,.78); }
  .dot-warn{ background:rgba(245,158,11,.90); }
  .dot-bad{ background:rgba(239,68,68,.86); }

  .meta-right{
    margin-left:auto;
    display:flex; align-items:center; gap:10px;
    color:var(--muted);
    font-weight:800;
  }
  .kbd{
    font-size:10px;
    padding:3px 7px;
    border-radius:999px;
    border:1px solid rgba(229,231,235,.9);
    background:#fff;
    color:#475569;
    font-weight:900;
  }

  /* ✅ Scroll container con sombras sutiles para indicar deslizamiento */
  .scroll{
    overflow:auto;
    max-height:74vh;
    -webkit-overflow-scrolling:touch;
    background:
      linear-gradient(to right, rgba(2,6,23,.06), rgba(2,6,23,0)) left/18px 100% no-repeat,
      linear-gradient(to left, rgba(2,6,23,.06), rgba(2,6,23,0)) right/18px 100% no-repeat;
    background-attachment:local, local;
  }

  table{
    width:max-content;
    min-width:100%;
    border-collapse:separate;
    border-spacing:0;
    background:#fff;
  }

  /* ✅ Header más chico y elegante */
  thead th{
    position:sticky;
    top:0;
    z-index:3;
    background:var(--soft);
    border-bottom:1px solid var(--line);
    color:#64748b;
    font-size:11px;
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.10em;
    padding:8px 8px;
    white-space:nowrap;
    text-align:center;
  }

  /* Sticky columns */
  th.sticky-left, td.sticky-left{
    position:sticky;
    left:0;
    z-index:4;
    background:#fff;
    border-right:1px solid var(--line);
  }
  th.sticky-left2, td.sticky-left2{
    position:sticky;
    left:300px; /* ✅ más compacto */
    z-index:4;
    background:#fff;
    border-right:1px solid var(--line);
  }

  /* ✅ Celdas más pequeñas (todo se ve “mini excel” pero legible) */
  tbody td{
    border-bottom:1px solid rgba(229,231,235,.95);
    padding:7px 7px;
    font-size:12px;
    color:var(--ink);
    white-space:nowrap;
    vertical-align:middle;
  }
  tbody tr:nth-child(even) td{ background:#fbfdff; }
  tbody tr:hover td{ background:#f6f9ff; }

  /* ✅ Columnas compactas */
  .nameCol{ min-width:300px; max-width:300px; }
  .sumCol{ min-width:140px; max-width:140px; text-align:center; }
  .cell{ min-width:96px; padding:6px 6px!important; }

  .cell-inner{
    display:flex;
    flex-direction:column;
    gap:3px;
    line-height:1.05;
  }

  .mono{ font-variant-numeric:tabular-nums; font-feature-settings:"tnum" 1; }
  .time{ font-weight:900; font-size:11px; }
  .small{ font-size:10px; color:var(--muted); font-weight:800; }

  /* ✅ Badges mini */
  .badgeState{
    display:inline-flex; align-items:center; gap:6px;
    font-size:10px;
    font-weight:900;
    padding:3px 7px;
    border-radius:999px;
    border:1px solid;
    width:fit-content;
  }
  .b-ok{ color:var(--ok); border-color:rgba(22,163,74,.28); background:rgba(22,163,74,.10); }
  .b-warn{ color:var(--warn); border-color:rgba(245,158,11,.30); background:rgba(245,158,11,.12); }
  .b-bad{ color:var(--bad); border-color:rgba(239,68,68,.26); background:rgba(239,68,68,.10); }

  .kpi{ display:flex; gap:6px; justify-content:center; flex-wrap:wrap; margin-top:4px; }

  /* ✅ Mejor “ux”: info de swipe */
  .swipe-hint{
    display:none;
    align-items:center;
    gap:8px;
    padding:10px 14px;
    border-bottom:1px solid var(--line);
    background:linear-gradient(180deg, rgba(248,250,252,.92), rgba(248,250,252,.55));
    color:var(--muted);
    font-size:12px;
    font-weight:800;
  }

  /* ✅ Mobile: no tocamos desktop, pero todo más usable */
  @media (max-width: 768px){
    .wrap{ margin:12px auto; padding:0 10px; }
    .panel{ border-radius:16px; }

    .head{ padding:12px 12px; }
    .title{ font-size:15px; }
    .sub{ font-size:11.5px; }

    .head form{ width:100%; }
    .control{ width:100%; padding:11px 11px; border-radius:14px; }

    .legend{ padding:10px 12px; gap:8px; }
    .pill{ padding:6px 9px; font-size:11px; }

    .meta-right{ margin-left:0; width:100%; justify-content:space-between; }
    .swipe-hint{ display:flex; }

    .scroll{ max-height:72vh; }

    /* 🔥 Compacto móvil */
    .nameCol{ min-width:220px; max-width:220px; }
    th.sticky-left2, td.sticky-left2{ left:220px; }
    .sumCol{ min-width:132px; max-width:132px; }
    .cell{ min-width:92px; }

    tbody td{ padding:7px 6px; font-size:11.5px; }
    .badgeState{ font-size:9.5px; padding:3px 7px; }
    .time{ font-size:11px; }
    .small{ font-size:10px; }
  }

  @media (max-width: 420px){
    .nameCol{ min-width:200px; max-width:200px; }
    th.sticky-left2, td.sticky-left2{ left:200px; }
    .sumCol{ min-width:128px; max-width:128px; }
    .cell{ min-width:88px; }
  }
</style>

<div class="wrap">
  <div class="panel">

    <div class="head">
      <div>
        <h3 class="title">
          <span class="mini-dot"></span>
          Reporte Asistencias
        </h3>
        <p class="sub">
          Mes:
          <strong style="color:var(--ink); text-transform:capitalize;">{{ $mesNombre }}</strong>
          <span style="color:var(--muted);"> · Hoy: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
        </p>
      </div>

      {{-- ✅ SIN BOTÓN: al cambiar mes se manda solo --}}
      <form method="GET" action="{{ $actionUrl }}" id="monthForm">
        <input type="month"
               name="month"
               class="control"
               value="{{ $monthParam }}"
               onchange="document.getElementById('monthForm').submit()">
      </form>
    </div>

    <div class="legend">
      <span class="pill"><span class="dot dot-ok"></span> Asistencia</span>
      <span class="pill"><span class="dot dot-warn"></span> Retardo</span>
      <span class="pill"><span class="dot dot-bad"></span> Falta</span>

      <span class="meta-right">
        <span>Cada celda: Entrada / Salida</span>
        <span class="kbd"><i class="bi bi-arrows-move"></i> scroll</span>
      </span>
    </div>

    <div class="swipe-hint">
      <i class="bi bi-hand-index-thumb"></i>
      Desliza horizontal para días · vertical para empleados
      <span class="kbd">TIP</span>
    </div>

    <div class="scroll">
      <table>
        <thead>
          <tr>
            <th class="sticky-left nameCol" style="text-align:left;">Empleado</th>
            <th class="sticky-left2 sumCol">Totales</th>

            @foreach($days as $ymd)
              @php $d=\Carbon\Carbon::parse($ymd); @endphp
              <th class="mono">{{ $d->day }}</th>
            @endforeach
          </tr>
        </thead>

        <tbody>
          @foreach($usuarios as $u)
            @php
              $ok=0; $bad=0; $warn=0;

              foreach ($days as $ymd) {
                $a = $map[$u->id][$ymd] ?? null;
                if (!$a) continue;

                $st = strtolower($a->estado ?? '');
                if (str_contains($st,'falta')) $bad++;
                elseif (str_contains($st,'retardo')) $warn++;
                else $ok++;
              }
            @endphp

            <tr>
              <td class="sticky-left nameCol" style="text-align:left;">
                <div style="font-weight:900;color:var(--ink); font-size:12px; line-height:1.1;">{{ $u->name }}</div>
                <div style="color:var(--muted); font-size:11px; font-weight:700;">
                  {{ $u->puesto ?? $u->cargo ?? '—' }}
                </div>
              </td>

              <td class="sticky-left2 sumCol">
                <div class="mono" style="font-weight:900; font-size:12px;">{{ $ok + $warn + $bad }}</div>
                <div class="kpi">
                  <span class="badgeState b-ok"><i class="bi bi-check-circle-fill"></i> {{ $ok }}</span>
                  <span class="badgeState b-warn"><i class="bi bi-clock-fill"></i> {{ $warn }}</span>
                  <span class="badgeState b-bad"><i class="bi bi-x-circle-fill"></i> {{ $bad }}</span>
                </div>
              </td>

              @foreach($days as $ymd)
                @php
                  $a = $map[$u->id][$ymd] ?? null;

                  $entrada = $a->hora ?? null;
                  $salida  = $a->hora_salida ?? null;

                  $estado = strtolower($a->estado ?? '');

                  $badge='b-ok'; $label='Asistencia'; $icon='bi-check-circle-fill';

                  if ($a) {
                    if (str_contains($estado,'falta')) { $badge='b-bad'; $label='Falta'; $icon='bi-x-circle-fill'; }
                    elseif (str_contains($estado,'retardo')) { $badge='b-warn'; $label='Retardo'; $icon='bi-clock-fill'; }
                  }
                @endphp

                <td class="cell">
                  @if(!$a)
                    <div class="cell-inner">
                      <div class="small">—</div>
                      <div class="small">—</div>
                    </div>
                  @else
                    <div class="cell-inner">
                      <div class="time mono">{{ $entrada ?: '—' }}</div>
                      <div class="small mono">Salida: {{ $salida ?: '—' }}</div>
                      <span class="badgeState {{ $badge }}"><i class="bi {{ $icon }}"></i> {{ $label }}</span>
                    </div>
                  @endif
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>

      </table>
    </div>

  </div>
</div>
@endsection
