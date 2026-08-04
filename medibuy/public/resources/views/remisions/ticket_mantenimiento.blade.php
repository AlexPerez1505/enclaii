<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Orden de Servicio {{ $remision->folio ?? $remision->id }}</title>
<style>
  /* ====== Ticket térmico B/N (MISMO TAMAÑO/DISEÑO) ====== */
  *{ box-sizing:border-box; }
  html,body{ margin:0; padding:0; }
  :root{ --ticket-width:80mm; } /* cambia a 58mm si tu impresora es de 58 */
  body{ font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color:#000; background:#fff; font-size:12px; line-height:1.2; }
  .ticket{ width:var(--ticket-width); margin:0 auto; padding:8px; }

  /* Cajas */
  .box{ border:1px dashed #000; border-radius:6px; padding:8px; margin-bottom:8px; page-break-inside:avoid; }
  .row{ display:table; width:100%; table-layout:fixed; }
  .col{ display:table-cell; vertical-align:top; }
  .col + .col{ padding-left:8px; }

  .h1{ font-size:18px; font-weight:800; margin:0 0 4px 0; }
  .h2{ font-size:13px; font-weight:700; margin:0 0 6px 0; }
  .tiny{ font-size:10px; }
  .muted{ opacity:.9; }
  .mono{ font-family: DejaVu Sans Mono, ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
  .badge{ display:inline-block; border:1px solid #000; padding:1px 6px; border-radius:999px; font-size:10px; }

  .rule{ border:0; border-top:1px dashed #000; margin:6px 0; }
  .dots{ border:0; border-top:1px dotted #000; margin:6px 0; }

  /* Items */
  .items{ list-style:none; margin:0; padding:0; }
  .item{ padding:6px 0; border-top:1px dotted #000; }
  .item:first-child{ border-top:0; }
  .line1,.line2{ display:flex; gap:6px; align-items:baseline; }
  .idx{ width:16px; text-align:right; font-variant-numeric:tabular-nums; }
  .name{ flex:1 1 auto; font-weight:700; }
  .brand{ flex:1 1 auto; text-align:right; }
  .label{ font-weight:700; }
  .code{ border:1px solid #000; border-radius:3px; padding:0 4px; }
  .right{ margin-left:auto; text-align:right; }

  /* ====== Líneas de puntos para firmas/notas ====== */
  .field-row{ display:flex; align-items:baseline; gap:8px; margin:4px 0; }
  .field-row .label{ white-space:nowrap; }
  .fill{ border-bottom:1px dotted #000; height:12px; line-height:12px; }
  .w80{  width:80px;  display:inline-block; }
  .w120{ width:120px; display:inline-block; }
  .w160{ width:160px; display:inline-block; }
  .grow{ flex:1 1 auto; display:inline-block; }

  /* Checks B/N simples */
  .checks{ display:flex; gap:10px; flex-wrap:wrap; }
  .ck{ display:inline-flex; align-items:center; gap:6px; }
  .boxx{ width:10px; height:10px; border:1px solid #000; display:inline-block; }
  .boxx.on{ background:#000; }

  /* Impresión */
  @page{ margin:6mm; }
  @media print{ .ticket{ padding:0; } }
</style>
</head>
<body>
<div class="ticket">

  @php
    $folio = $remision->folio ?? $remision->id;

    $clienteNombre = trim(($remision->cliente->nombre ?? '').' '.($remision->cliente->apellido ?? ''))
      ?: ($remision->cliente->nombre ?? '—');

    $fecha = $remision->created_at?->format('d/m/Y H:i') ?? '—';

    $tecnico = $remision->user->name
      ?? $remision->usuario->name
      ?? '—';

    $tel = $remision->cliente->telefono ?? '—';

    // Tipo: si no existe en DB, queda “PENDIENTE”
    $tipo = trim((string)($remision->tipo_mantenimiento ?? ''));
    $tipoTexto = $tipo ? mb_strtoupper($tipo, 'UTF-8') : 'PENDIENTE';

    // Marcar checks si existe tipo_mantenimiento
    $tipoLower = mb_strtolower($tipoTexto, 'UTF-8');
    $isPrev = str_contains($tipoLower, 'pre');
    $isCorr = str_contains($tipoLower, 'cor');

    $items = collect($remision->items ?? []);
  @endphp

  <!-- Encabezado (USO INTERNO, SIN QR) -->
  <div class="box">
    <div class="row">
      <div class="col" style="width:100%">
        <div class="h1">ORDEN DE SERVICIO</div>
        <div class="badge">USO INTERNO</div>

        <div style="margin-top:6px;">
          <div><b>Folio:</b> <span class="mono code">{{ $folio }}</span></div>
          <div><b>Cliente:</b> {{ $clienteNombre }}</div>
          <div><b>Tel:</b> <span class="mono">{{ $tel }}</span></div>
          <div><b>Fecha:</b> {{ $fecha }}</div>
          <div><b>Técnico:</b> {{ $tecnico }}</div>
        </div>

        <hr class="rule">

        <div class="checks tiny">
          <span class="ck"><span class="boxx {{ $isPrev ? 'on' : '' }}"></span><span class="label">Preventivo</span></span>
          <span class="ck"><span class="boxx {{ $isCorr ? 'on' : '' }}"></span><span class="label">Correctivo</span></span>
          <span class="ck"><span class="boxx"></span><span class="label">Instalación</span></span>
          <span class="ck"><span class="boxx"></span><span class="label">Diagnóstico</span></span>
        </div>

        <div class="tiny muted" style="margin-top:6px;">
          Tipo actual: <span class="mono code">{{ $tipoTexto }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Servicios / Refacciones (SIN DINERO) -->
  <div class="box">
    <div class="h2">Servicios / Refacciones ({{ $items->count() }})</div>

    @if($items->count())
      <ul class="items">
        @foreach($items as $i => $it)
          @php
            $nombre = $it->nombre_item ?? '—';
            $desc   = $it->descripcion_item ?? null;
            $cant   = (int)($it->cantidad ?? 0);
            $uni    = $it->unidad ?? '—';
          @endphp

          <li class="item">
            <div class="line1">
              <span class="idx">{{ $i+1 }}</span>
              <span class="name">{{ $nombre }}</span>
              <span class="brand"><span class="mono code">{{ $cant }} {{ $uni }}</span></span>
            </div>

            @if(!empty($desc))
              <div class="line2 tiny muted">
                <span class="label">Notas:</span>
                <span>{{ $desc }}</span>
              </div>
            @else
              <div class="line2 tiny muted">
                <span class="label">Notas:</span>
                <span class="fill grow"></span>
              </div>
            @endif
          </li>
        @endforeach
      </ul>
    @else
      <div class="tiny muted">Sin ítems capturados.</div>
    @endif
  </div>

  <!-- Observaciones + Firmas -->
  <div class="box">
    <div class="h2">Observaciones / Cierre</div>

    <div class="field-row tiny">
      <span class="label"><b>Observaciones:</b></span>
      <span class="fill grow"></span>
    </div>

    <div class="field-row tiny">
      <span class="label"><b>Recomendaciones:</b></span>
      <span class="fill grow"></span>
    </div>

    <hr class="dots">

    <div class="field-row tiny" style="margin-bottom:6px;">
      <span class="label">Recibió:</span>  <span class="fill w120"></span>
      <span class="label">Área:</span> <span class="fill w120"></span>
    </div>

    <div class="field-row tiny" style="margin-bottom:6px;">
      <span class="label">Técnico:</span>  <span class="fill w120"></span>
      <span class="label">Firma:</span> <span class="fill w160"></span>
    </div>

    <div class="field-row tiny" style="margin-bottom:6px;">
      <span class="label">Cliente:</span>  <span class="fill w120"></span>
      <span class="label">Firma:</span> <span class="fill w160"></span>
    </div>

    <div class="field-row tiny muted">
      <span class="label">Fecha/Hora cierre:</span>
      <span class="fill w160"></span>
    </div>
  </div>

  <div class="tiny muted" style="text-align:center">
    Generado: {{ now()->format('d/m/Y H:i') }} · Sistema Medibuy · USO INTERNO
  </div>

</div>
</body>
</html>
