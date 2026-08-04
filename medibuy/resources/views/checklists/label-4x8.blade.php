<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Etiqueta Venta {{ $venta->folio ?? $venta->id }}</title>
<style>
  /* ====== Ticket térmico B/N ====== */
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

  /* QR minimalista */
  .qr-wrap{ text-align:center; }
  .qr{ width:88px; height:auto; display:inline-block; background:#fff; border:1px solid #000; border-radius:10px; padding:6px; image-rendering:pixelated; }

  /* Productos (ilimitados) */
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
  /* Línea punteada “rellenable” */
  .fill{ border-bottom:1px dotted #000; height:12px; line-height:12px; }
  /* Anchos útiles */
  .w80{  width:80px;  display:inline-block; }
  .w120{ width:120px; display:inline-block; }
  .w160{ width:160px; display:inline-block; }
  .grow{ flex:1 1 auto; display:inline-block; } /* ocupa el resto de la línea */

  /* Impresión */
  @page{ margin:6mm; }
  @media print{ .ticket{ padding:0; } }
</style>
</head>
<body>
<div class="ticket">

  <!-- Encabezado -->
  <div class="box">
    <div class="row">
      <div class="col" style="width:65%">
        <div class="h1">REMISIÓN / VENTA</div>
        <div class="badge">USO INTERNO</div>
        <div><b>Remisión:</b> <span class="big">{{ $venta->folio ?? $venta->id }}</span></div>
        <div><b>Cliente:</b> {{ $venta->cliente->nombre ?? ($venta->cliente_nombre ?? '—') }}</div>
        <div><b>Fecha:</b> {{ $venta->created_at?->format('d/m/Y H:i') ?? '—' }}</div>
        @if(!empty($venta->vendedor))
          <div><b>Vendedor:</b> {{ $venta->vendedor }}</div>
        @endif
        <hr class="rule">
        <div class="tiny muted">Escanea para abrir el checklist.</div>
      </div>
      <div class="col" style="width:35%">
        @if(!empty($qr_path))
          <div class="qr-wrap">
            <img src="{{ public_path($qr_path) }}" class="qr" alt="QR">
            <div class="tiny">SCAN</div>
          </div>
        @endif
      </div>
    </div>
  </div>

  <!-- Productos -->
  @php $items = collect($productos ?? []); @endphp
  @if($items->count())
    <div class="box">
      <div class="h2">Productos ({{ $items->count() }})</div>
      <ul class="items">
        @foreach($items as $i => $p)
          @php
            $equipo = $p->tipo_equipo ?? '—';
            $marcaModelo = trim(($p->marca ?? '').' '.($p->modelo ?? '')) ?: '—';
            $serie = $p->numero_serie ?? '—';
          @endphp
          <li class="item">
            <div class="line1">
              <span class="idx">{{ $i+1 }}</span>
              <span class="name">{{ $equipo }}</span>
              <span class="brand">{{ $marcaModelo }}</span>
            </div>
            <div class="line2 tiny">
              <span class="label">Serie:</span>
              <span class="mono code">{{ $serie }}</span>
              @if(!empty($p->accesorios))
                <span>·</span><span class="label">Accesorios:</span><span>{{ $p->accesorios }}</span>
              @endif
              @if(!empty($p->notas))
                <span class="right"><span class="label">Notas:</span> {{ $p->notas }}</span>
              @endif
            </div>
          </li>
        @endforeach
      </ul>
    </div>
  @endif

  <!-- Notas y firma con puntos -->
  <div class="box">
    <!-- Notas con línea punteada a lo ancho -->
    <div class="field-row tiny">
      <span class="label"><b>Notas:</b></span>
      <span class="fill grow"></span>
    </div>
    @if(!empty($venta->notas))
      <div class="tiny mono" style="margin-top:2px;">{{ $venta->notas }}</div>
    @endif

    <hr class="dots">

    <!-- Fila 1 -->
    <div class="field-row tiny" style="margin-bottom:6px;">
      <span class="label">Preparó:</span>  <span class="fill w120"></span>
      <span class="label">Verificó:</span> <span class="fill w120"></span>
      <span class="label">Entregó:</span>  <span class="fill w160"></span>
    </div>

    <!-- Fila 2 -->
    <div class="field-row tiny muted">
      <span class="label">Fecha/Hora salida:</span>
      <span class="fill w160"></span>
    </div>
  </div>

  <div class="tiny muted" style="text-align:center">
    Generado: {{ now()->format('d/m/Y H:i') }} · Sistema Medibuy · USO INTERNO
  </div>

</div>
</body>
</html>
