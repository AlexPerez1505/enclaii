<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>OS #{{ $orden->id }}</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">

  <style>
    @page {
      size: letter;
      margin: 12mm 15mm 22mm 15mm;
    }

    :root {
      --bg: #f9fafb;
      --card: #ffffff;
      --ink: #333333;
      --ink-dark: #111111;
      --muted: #888888;
      --line: #ebebeb;
      --blue: #007aff;
      --blue-soft: #e6f0ff;

      --fs: 8.5px;
      --lh: 1.35;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Quicksand', sans-serif;
    }

    body {
      font-size: var(--fs);
      line-height: var(--lh);
      color: var(--ink);
      background: var(--bg);
    }

    .w-100 { width: 100%; }
    .t-right { text-align: right; }
    .t-center { text-align: center; }
    .bold { font-weight: 700; }
    .upper { text-transform: uppercase; }
    .small { font-size: 7.5px; }
    .mb-3 { margin-bottom: 8px; }
    .muted { color: var(--muted); }
    .avoid-break { page-break-inside: avoid; }

    table {
      border-collapse: collapse;
      width: 100%;
      table-layout: fixed;
    }

    th,
    td {
      vertical-align: top;
    }

    .card {
      border-radius: 8px;
      background: var(--card);
      border: 1px solid var(--line);
      margin-bottom: 10px;
      overflow: hidden;
    }

    .card-header {
      background: var(--card);
      border-bottom: 1px solid var(--line);
      padding: 8px 12px;
      font-weight: 700;
      font-size: 9.5px;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      color: var(--ink-dark);
    }

    .card-body {
      padding: 8px 12px;
    }

    .brand-shell {
      padding-bottom: 12px;
      border-bottom: 1px solid var(--line);
      margin-bottom: 16px;
    }

    .brand-row td {
      padding: 0;
      vertical-align: middle;
    }

    .logo {
      max-height: 38px;
      width: auto;
      display: block;
    }

    .doc-title {
      font-size: 18px;
      font-weight: 700;
      letter-spacing: 0.02em;
      color: var(--ink-dark);
    }

    .doc-sub {
      font-size: 8px;
      font-weight: 600;
      color: var(--muted);
      letter-spacing: 0.05em;
      margin-top: 2px;
    }

    .os-badge {
      display: inline-block;
      padding: 4px 12px;
      background: var(--blue-soft);
      font-weight: 700;
      font-size: 10px;
      color: var(--blue);
      border-radius: 999px;
    }

    .kv-table td {
      padding: 2px 4px;
    }

    .kv-table tr:not(:last-child) td {
      border-bottom: 1px solid #fcfcfc;
    }

    .k {
      width: 40%;
      font-weight: 700;
      color: var(--muted);
      font-size: 8px;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .v {
      width: auto;
      color: var(--ink-dark);
      font-weight: 600;
    }

    .inspection-table {
      border: 1px solid var(--line);
      border-radius: 6px;
      overflow: hidden;
    }

    .inspection-table th {
      font-weight: 700;
      background: var(--bg);
      border-bottom: 1px solid var(--line);
      color: var(--muted);
      text-align: left;
      padding: 5px 8px;
      font-size: 8px;
    }

    .inspection-table td {
      padding: 5px 8px;
      border-bottom: 1px solid var(--line);
      color: var(--ink);
      font-size: 8px;
    }

    .inspection-table tr:last-child td {
      border-bottom: none;
    }

    .note-box {
      border: 1px solid var(--line);
      background: var(--bg);
      border-radius: 6px;
      padding: 8px 10px;
      min-height: 28px;
      color: var(--ink);
    }

    .photo-grid-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 8px;
      margin-top: -8px;
    }

    .photo-cell {
      border: 1px solid var(--line);
      background: var(--card);
      border-radius: 8px;
      padding: 6px;
      text-align: center;
      vertical-align: middle;
    }

    .photo-cell img {
      max-width: 100%;
      max-height: 85px;
      display: block;
      margin: 0 auto;
      border-radius: 4px;
    }

    .photo-caption {
      font-size: 7.5px;
      color: var(--muted);
      margin-top: 4px;
      font-weight: 700;
      text-transform: uppercase;
    }

    .legend .row {
      margin-bottom: 4px;
      color: var(--ink-dark);
      font-weight: 600;
      font-size: 8.5px;
    }

    .box {
      width: 10px;
      height: 10px;
      border-radius: 2px;
      border: 1px solid var(--muted);
      display: inline-block;
      vertical-align: middle;
      margin-right: 4px;
    }

    .box.fill {
      background: var(--blue);
      border-color: var(--blue);
    }

    .split td.col-left {
      width: 55%;
      padding-right: 6px;
      padding-left: 0;
    }

    .split td.col-right {
      width: 45%;
      padding-left: 6px;
      padding-right: 0;
    }

    .sign-table {
      margin-top: 15px;
    }

    .sign-table td {
      text-align: center;
      padding-top: 15px;
      border: none;
    }

    .line {
      width: 80%;
      margin: 0 auto 6px;
      border-top: 1px solid var(--line);
    }

    .sig-role {
      color: var(--muted);
      font-size: 8px;
      font-weight: 700;
      letter-spacing: 0.05em;
    }

    .footer-container {
      position: fixed;
      bottom: 10px;
      left: 15mm;
      width: calc(100% - 30mm);
    }

    .footer {
      padding-top: 8px;
      border-top: 1px solid var(--line);
    }

    .footer-table td {
      padding: 0 4px;
      vertical-align: middle;
    }

    .footer-name {
      font-weight: 700;
      font-size: 9.5px;
      color: var(--ink-dark);
    }

    .footer-role {
      color: var(--muted);
      font-size: 8px;
      font-weight: 600;
    }

    .footer-label {
      font-weight: 700;
      color: var(--muted);
    }

    .sig-img {
      max-height: 35px;
      width: auto;
      display: block;
    }
  </style>
</head>

<body>

@php
  use Carbon\Carbon;

  $tecBase     = isset($tecnicoName) && $tecnicoName ? $tecnicoName : (optional($orden->user)->name ?? 'N/A');
  $tecnicoUP   = mb_strtoupper($tecBase, 'UTF-8');

  $clienteFull  = trim((optional($orden->cliente)->nombre ?? '').' '.(optional($orden->cliente)->apellido ?? ''));
  $telefonoCli  = $orden->cliente->telefono   ?? 'N/A';
  $direccionCli = $orden->cliente->direccion  ?? ($orden->cliente->comentarios ?? 'N/A');

  $fEntrada = $orden->fecha_entrada ? Carbon::parse($orden->fecha_entrada)->format('d/m/Y') : 'N/A';
  $fMtoDate = $orden->fecha_mantenimiento ? Carbon::parse($orden->fecha_mantenimiento) : null;
  $fMto     = $fMtoDate ? $fMtoDate->format('d/m/Y') : 'N/A';

  $proxRaw     = $orden->getRawOriginal('proximo_mantenimiento');
  $proxFecha   = null;
  $proxMeses   = null;

  if ($proxRaw !== null && $proxRaw !== '') {
      if (is_numeric($proxRaw)) {
          $proxMeses = (int) $proxRaw;

          if ($fMtoDate) {
              $proxFecha = (clone $fMtoDate)->addMonths($proxMeses);
          }
      } else {
          try {
              $proxFecha = Carbon::parse($proxRaw);
          } catch (\Throwable $e) {
              $proxFecha = null;
          }

          if ($proxFecha && $fMtoDate) {
              $proxMeses = $fMtoDate->diffInMonths($proxFecha);
          }
      }
  }

  $proxTexto = 'N/A';

  if ($proxFecha instanceof \Carbon\Carbon) {
      $proxTexto = $proxFecha->format('d/m/Y') . ($proxMeses ? ' ('.$proxMeses.' meses)' : '');
  } elseif (is_numeric($proxRaw) && $fMtoDate) {
      $proxTexto = $proxMeses.' meses';
  }

  $hasDataUri1 = !empty($fotoDataUri);
  $hasDataUri2 = !empty($fotoDataUri2);
  $hasDataUri3 = !empty($fotoDataUri3);
  $hasAbsPath1 = !empty($fotoAbs) && file_exists($fotoAbs);

  /*
  |--------------------------------------------------------------------------
  | CHECKLIST DE INSPECCIÓN
  |--------------------------------------------------------------------------
  | Si no hay parámetros reales, no se imprime la tarjeta.
  */

  $raw = $orden->mto_preventivo ?? [];
  $grouped = [];

  foreach ($raw as $key => $val) {
      if (is_array($val) && isset($val['seccion'], $val['item'])) {
          $sec = trim((string) $val['seccion']);
          $item = trim((string) ($val['item'] ?? ''));

          if ($sec !== '' && $item !== '') {
              $grouped[$sec][] = [
                  'item' => $item,
                  'estatus' => $val['estatus'] ?? 'Revisado',
              ];
          }
      } elseif (is_array($val)) {
          foreach ($val as $v) {
              if (is_array($v) && isset($v['item'])) {
                  $sec = is_string($key) ? $key : ($v['seccion'] ?? 'Sección');
                  $sec = trim((string) $sec);
                  $item = trim((string) ($v['item'] ?? ''));

                  if ($sec !== '' && $item !== '') {
                      $grouped[$sec][] = [
                          'item' => $item,
                          'estatus' => $v['estatus'] ?? 'Revisado',
                      ];
                  }
              }
          }
      }
  }

  $grouped = array_filter($grouped, function ($items) {
      return is_array($items) && count($items) > 0;
  });

  ksort($grouped);

  $mostrarChecklistInspeccion = count($grouped) > 0;

  $acciones = collect($orden->mto_realizado ?? [])->filter(function ($item) {
      return trim((string) $item) !== '';
  })->values()->all();

  $mostrarAccionesRealizadas = count($acciones) > 0;

  $obsRaw = $orden->observaciones ?? $orden->observacion ?? $orden->comentarios ?? '';

  if (is_array($obsRaw)) {
      $obsRaw = implode("\n", array_filter($obsRaw));
  }

  $observacionesTexto = trim((string) $obsRaw);

  $logoDataUri = $logoDataUri ?? null;

  if (!$logoDataUri) {
      $logoFile = public_path('images/logomedy.png');

      if (is_readable($logoFile)) {
          $mime = function_exists('mime_content_type') ? mime_content_type($logoFile) : 'image/png';
          $logoDataUri = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($logoFile));
      }
  }

  $firmaDataUri = $firmaDataUri ?? null;

  if (!$firmaDataUri) {
      $firmaFile = public_path('images/firma.png');

      if (is_readable($firmaFile)) {
          $mimeF = function_exists('mime_content_type') ? mime_content_type($firmaFile) : 'image/png';
          $firmaDataUri = 'data:'.$mimeF.';base64,'.base64_encode(file_get_contents($firmaFile));
      }
  }

  $tipoMantenimiento = mb_strtolower(trim((string)(
      $orden->tipo_mantenimiento ?? $orden->tipo_servicio ?? $orden->servicio ?? 'preventivo'
  )), 'UTF-8');

  if (!in_array($tipoMantenimiento, ['preventivo', 'correctivo', 'mixto'], true)) {
      $tipoMantenimiento = 'preventivo';
  }

  $isPreventivo = in_array($tipoMantenimiento, ['preventivo', 'mixto'], true);
  $isCorrectivo = in_array($tipoMantenimiento, ['correctivo', 'mixto'], true);

  $tipoMantenimientoLabel = match ($tipoMantenimiento) {
      'correctivo' => 'Correctivo',
      'mixto' => 'Mixto',
      default => 'Preventivo',
  };
@endphp

<div class="page">

  <div class="brand-shell">
    <table class="w-100 brand-row">
      <tr>
        <td style="width:30%">
          @if($logoDataUri)
            <img src="{{ $logoDataUri }}" alt="Grupo MediBuy" class="logo">
          @endif
        </td>

        <td class="t-center" style="width:40%">
          <div class="doc-title upper">Orden de Servicio</div>
          <div class="doc-sub">GRUPO MEDIBUY · DEPARTAMENTO TÉCNICO</div>
        </td>

        <td class="t-right" style="width:30%">
          <span class="os-badge">OS # <span>{{ $orden->id }}</span></span>
        </td>
      </tr>
    </table>
  </div>

  <div class="card avoid-break">
    <div class="card-header">Información General</div>

    <div class="card-body">
      <table>
        <tr>
          <td style="width:50%; padding-right:8px;">
            <table class="kv-table">
              <tr>
                <td class="k">Cliente</td>
                <td class="v">{{ $clienteFull ?: 'N/A' }}</td>
              </tr>

              <tr>
                <td class="k">Representante</td>
                <td class="v">{{ $tecnicoUP }}</td>
              </tr>

              <tr>
                <td class="k">Teléfono</td>
                <td class="v">{{ $telefonoCli }}</td>
              </tr>

              <tr>
                <td class="k">Dirección</td>
                <td class="v">{{ $direccionCli }}</td>
              </tr>
            </table>
          </td>

          <td style="width:50%; padding-left:8px;">
            <table class="kv-table">
              <tr>
                <td class="k">Fecha de Ingreso</td>
                <td class="v">{{ $fEntrada }}</td>
              </tr>

              <tr>
                <td class="k">Fecha Servicio</td>
                <td class="v">{{ $fMto }}</td>
              </tr>

              <tr>
                <td class="k">Próximo Serv.</td>
                <td class="v">{{ $proxTexto }}</td>
              </tr>

              <tr>
                <td class="k">Clasificación</td>
                <td class="v">{{ $tipoMantenimientoLabel }}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </div>

  <div class="card avoid-break">
    <div class="card-header">Especificaciones del Equipo</div>

    <div class="card-body">
      <table>
        <tr>
          <td class="k" style="width:15%">Equipo</td>
          <td class="v" style="width:35%">{{ $orden->equipo ?? 'N/A' }}</td>
          <td class="k" style="width:15%">Marca</td>
          <td class="v" style="width:35%">{{ $orden->marca ?? 'N/A' }}</td>
        </tr>

        <tr>
          <td class="k">Modelo</td>
          <td class="v">{{ $orden->modelo ?? 'N/A' }}</td>
          <td class="k">No. de Serie</td>
          <td class="v">{{ $orden->numero_serie ?? 'N/A' }}</td>
        </tr>
      </table>
    </div>
  </div>

  <table class="w-100 split">
    <tr>
      <td class="col-left">

        @if($mostrarChecklistInspeccion)
          <div class="card avoid-break">
            <div class="card-header">Checklist de Inspección</div>

            <div class="card-body">
              @foreach($grouped as $sec => $items)
                <table class="inspection-table mb-3">
                  <thead>
                    <tr>
                      <th style="width:70%">{{ strtoupper($sec) }}</th>
                      <th style="width:30%">RESULTADO</th>
                    </tr>
                  </thead>

                  <tbody>
                    @foreach($items as $it)
                      <tr>
                        <td style="font-weight: 500;">{{ $it['item'] }}</td>
                        <td class="bold" style="color: var(--ink-dark);">{{ $it['estatus'] }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              @endforeach
            </div>
          </div>
        @endif

        @if($mostrarAccionesRealizadas)
          <div class="card avoid-break">
            <div class="card-header">Acciones Realizadas</div>

            <div class="card-body">
              <div class="note-box">
                <ul style="margin:0; padding-left:12px; line-height: 1.4; font-weight: 500;">
                  @foreach($acciones as $a)
                    <li>{{ $a }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        @endif

      </td>

      <td class="col-right">

        <div class="card avoid-break">
          <div class="card-header">Registro Fotográfico</div>

          <div class="card-body">
            @if($hasDataUri1 || $hasDataUri2 || $hasDataUri3 || $hasAbsPath1)
              <table class="photo-grid-table">
                @if($hasDataUri1 || $hasAbsPath1 || $hasDataUri2)
                  <tr>
                    @if($hasDataUri1 || $hasAbsPath1)
                      <td class="photo-cell"
                          @if(!$hasDataUri2) colspan="2" @endif
                          style="width: {{ $hasDataUri2 ? '50%' : '100%' }};">
                        <img src="{{ $hasDataUri1 ? $fotoDataUri : $fotoAbs }}" alt="Foto 1">

                        <div class="photo-caption">
                          Perspectiva 1 @if($orden->numero_serie) (SN: {{ $orden->numero_serie }}) @endif
                        </div>
                      </td>
                    @endif

                    @if($hasDataUri2)
                      <td class="photo-cell" style="width: 50%;">
                        <img src="{{ $fotoDataUri2 }}" alt="Foto 2">
                        <div class="photo-caption">Perspectiva 2</div>
                      </td>
                    @endif
                  </tr>
                @endif

                @if($hasDataUri3)
                  <tr>
                    <td class="photo-cell" colspan="2">
                      <img src="{{ $fotoDataUri3 }}" alt="Foto 3" style="max-height: 90px;">
                      <div class="photo-caption">Perspectiva 3</div>
                    </td>
                  </tr>
                @endif
              </table>
            @else
              <div class="note-box t-center muted small" style="min-height: 80px; display:flex; align-items:center; justify-content:center;">
                — Sin evidencia fotográfica adjunta —
              </div>
            @endif
          </div>
        </div>

        <div class="card avoid-break">
          <div class="card-header">Dictamen / Observaciones</div>

          <div class="card-body">
            <div class="legend mb-3">
              <span class="row" style="margin-right: 12px;">
                <span class="box {{ $isPreventivo ? 'fill' : '' }}"></span> Preventivo
              </span>

              <span class="row">
                <span class="box {{ $isCorrectivo ? 'fill' : '' }}"></span> Correctivo
              </span>
            </div>

            @if($observacionesTexto !== '')
              <div class="note-box" style="line-height: 1.4; font-weight: 500;">
                {!! nl2br(e($observacionesTexto)) !!}
              </div>
            @else
              <div class="note-box t-center muted small">
                — Sin observaciones —
              </div>
            @endif
          </div>
        </div>

      </td>
    </tr>
  </table>

  <table class="w-100 sign-table avoid-break">
    <tr>
      <td style="width:50%">
        <div class="line"></div>
        <div class="bold" style="color: var(--ink-dark); font-size: 9px;">{{ $tecnicoUP }}</div>
        <div class="upper sig-role">Ingeniero / Técnico Responsable</div>
      </td>

      <td style="width:50%">
        <div class="line"></div>
        <div class="bold" style="color: var(--ink-dark); font-size: 9px;">{{ $clienteFull ?: 'N/A' }}</div>
        <div class="upper sig-role">Conformidad del Cliente</div>
      </td>
    </tr>
  </table>
</div>

<div class="footer-container">
  <div class="footer">
    <table class="footer-table" cellpadding="0" cellspacing="0">
      <tr>
        <td style="width: 50%;" valign="middle">
          <table cellpadding="0" cellspacing="0">
            <tr>
              <td valign="middle" style="padding-right:12px;">
                @if($firmaDataUri)
                  <img src="{{ $firmaDataUri }}" alt="Firma Autorizada" class="sig-img">
                @endif
              </td>

              <td valign="middle">
                <div class="footer-name">ANAHÍ TÉLLEZ</div>
                <div class="footer-role upper">Dirección Operativa</div>
              </td>
            </tr>
          </table>
        </td>

        <td style="width: 50%; text-align:right;" valign="middle">
          <div style="font-size: 8px; margin-bottom: 2px; color: var(--ink-dark);">
            <span class="footer-label">TELÉFONO:</span> +52 722 448 5191
          </div>

          <div style="font-size: 8px; margin-bottom: 2px; color: var(--ink-dark);">
            <span class="footer-label">CORREO:</span> ventas@grupomedibuy.com
          </div>

          <div style="font-size: 8px; color: var(--ink-dark);">
            <span class="footer-label">PORTAL:</span> grupomedibuy.com
          </div>
        </td>
      </tr>
    </table>
  </div>
</div>

</body>
</html>