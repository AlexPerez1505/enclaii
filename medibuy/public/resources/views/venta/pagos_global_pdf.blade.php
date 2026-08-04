<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Plan de pagos - Remisión 2025-{{ $venta->id }}</title>
  <style>
    @page {
      size: letter;
      margin: 1.8cm 1.7cm;
    }

    * {
      box-sizing: border-box;
    }

    body {
      font-family: system-ui, -apple-system, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
      font-size: 11px;
      color: #0f172a;
      margin: 0;
      padding: 0;
    }

    h1, h2, h3, h4 {
      margin: 0;
      padding: 0;
    }

    .page-wrap {
      padding: 8px 4px 0;
    }

    /* ===== ENCABEZADO TEXTO IZQ / LOGO DER ===== */
    .header-band {
      width: 100%;
      padding-bottom: 8px;
      margin-bottom: 8px;
      border-bottom: 1px solid #e5e7eb;
      font-size: 0;
    }

    .header-col {
      display: inline-block;
      vertical-align: top;
      width: 50%;
      font-size: 11px;
    }

    .header-col-right {
      text-align: right;
    }

    .header-title {
      font-size: 14px;
      font-weight: 600;
      letter-spacing: .06em;
      text-transform: uppercase;
      color: #111827;
    }

    .header-sub {
      font-size: 10px;
      color: #6b7280;
      margin-top: 2px;
    }

    .badge-remision {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 999px;
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .06em;
      background: #eff6ff;
      color: #1d4ed8;
      border: 1px solid #bfdbfe;
      margin-top: 4px;
    }

    .logo {
      width: 120px;
      height: auto;
    }

    .muted { color: #6b7280; }
    .small { font-size: 10px; }

    /* ===== CUADROS SUPERIORES EN MISMA FILA ===== */
    .info-grid{
      width: 100%;
      margin-bottom: 8px;
      font-size: 0;
    }

    .info-col{
      display: inline-block;
      vertical-align: top;
      width: 49%;
      font-size: 11px;
    }

    .info-col + .info-col{
      margin-left: 2%;
    }

    .cliente-box,
    .venta-box {
      border-radius: 6px;
      border: 1px solid #e5e7eb;
      padding: 7px 9px;
      background: #f9fafb;
    }

    .box-title {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .10em;
      color: #6b7280;
      margin-bottom: 4px;
    }

    .info-pairs {
      display: table;
      width: 100%;
      font-size: 10.5px;
    }

    .info-row{
      display: table-row;
    }

    .info-label,
    .info-value{
      display: table-cell;
      padding-bottom: 2px;
    }

    .info-label {
      width: 40%;
      color: #6b7280;
      white-space: nowrap;
      padding-right: 6px;
    }

    .info-value {
      width: 60%;
      font-weight: 500;
      color: #111827;
    }

    /* ===== SECCIÓN TABLA PRINCIPAL ===== */
    .section-title {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .12em;
      color: #6b7280;
      margin-top: 4px;
      margin-bottom: 2px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 4px;
    }

    th, td {
      padding: 5px 4px;
    }

    th {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: #4b5563;
      text-align: left;
      border-bottom: 1px solid #d1d5db;
      white-space: nowrap;
    }

    td {
      font-size: 10.2px;
      border-bottom: 1px solid #edf2f7;
      vertical-align: top;
    }

    tbody tr:last-child td {
      border-bottom: none;
    }

    .text-right { text-align: right; }
    .text-center { text-align: center; }

    tbody tr:nth-child(even) td {
      background: #f9fafb;
    }

    /* ===== FIRMAS EN CELDA ===== */
    .firmas-celda {
      font-size: 8px;
      line-height: 1.3;
    }

    .firma-mini {
      margin-bottom: 3px;
    }

    .firma-mini-linea {
      border-bottom: 0.6px solid #cbd5f5;
      margin-bottom: 1px;
      height: 0;
      width: 100%;
    }

    .firma-mini-label {
      text-align: left;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: #6b7280;
    }

    /* ===== RESUMEN GENERAL ===== */
    .resumen-box {
      margin-top: 8px;
      border-radius: 6px;
      border: 1px solid #e5e7eb;
      padding: 7px 9px;
      font-size: 10.2px;
    }

    .resumen-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 3px;
    }

    .resumen-title {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .12em;
      color: #6b7280;
    }

    .resumen-body {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      flex-wrap: wrap;
    }

    .res-item {
      min-width: 30%;
      margin-top: 2px;
    }

    .res-label {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: #6b7280;
    }

    .res-value {
      font-size: 11px;
      font-weight: 600;
      color: #111827;
    }

    .res-value.negativo {
      color: #b91c1c;
    }

    .res-hint {
      font-size: 9px;
      color: #6b7280;
      margin-top: 2px;
    }

    /* ===== TABLAS SECUNDARIAS (TRADE-IN / ANTICIPO) ===== */
    .secondary-table{
      margin-top: 8px;
      font-size: 10.2px;
    }

    .secondary-table table{
      margin-top: 3px;
    }

    .secondary-title{
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .12em;
      color: #6b7280;
    }

    /* ===== OBSERVACIONES ===== */
    .obs-box {
      margin-top: 8px;
      font-size: 10.2px;
      border-top: 1px solid #e5e7eb;
      padding-top: 6px;
    }

    .obs-title {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: .12em;
      color: #6b7280;
      margin-bottom: 3px;
    }

    .obs-box ul {
      margin: 0;
      padding-left: 1.1rem;
    }

    .obs-box li {
      margin-bottom: 2px;
    }
  </style>
</head>
<body>

<div class="page-wrap">
  @php
    // Totales base
    $totalCuotas     = $totalCuotas ?? ($pagosPlan->count() ?? 0);
    $totalProgramado = isset($pagosPlan) ? (float) $pagosPlan->sum('monto') : 0;

    // Total original de la venta (sin trade-in / anticipos)
    $totalOriginal = isset($totalOriginal)
        ? (float) $totalOriginal
        : (float) ($venta->total_original ?? $venta->total ?? 0);

    // Total neto (ya con trade-in / anticipos aplicados)
    $totalNeto = isset($totalNeto)
        ? (float) $totalNeto
        : (float) ($venta->total_neto ?? $totalOriginal);

    // Armar pagos de anticipo / trade-in si no vienen desde el controlador
    $pagosRealizados = $venta->pagos ?? collect();

    if (!isset($pagosAnticipo)) {
        $pagosAnticipo = $pagosRealizados->filter(function ($pago) {
            return ($pago->tipo ?? null) === 'anticipo'
                || ($pago->metodo ?? null) === 'anticipo'
                || ($pago->es_anticipo ?? false);
        })->values();
    }

    if (!isset($pagosTradeIn)) {
        $pagosTradeIn = $pagosRealizados->filter(function ($pago) {
            return ($pago->tipo ?? null) === 'trade-in'
                || ($pago->metodo ?? null) === 'trade-in'
                || ($pago->es_tradein ?? false);
        })->values();
    }

    // Nombre del vendedor (usuario que registró la venta / asesor)
    $sellerName    = $venta->asesor
        ?? (optional($venta->user)->name)
        ?? '________________';
    $clienteNombre = mb_strtoupper(
        trim(($venta->cliente->nombre ?? '').' '.($venta->cliente->apellido ?? '')),
        'UTF-8'
    );

    $tieneAnticipos = isset($pagosAnticipo) && count($pagosAnticipo);
    $tieneTradeIn   = isset($pagosTradeIn) && count($pagosTradeIn);
  @endphp

  {{-- ENCABEZADO: PLAN + CLIENTE + REMISIÓN IZQ / LOGO DER --}}
  <div class="header-band">
    <div class="header-col">
      <div class="header-title">Plan de pagos</div>
      <div class="header-sub">
        Cliente: {{ $clienteNombre }}
      </div>
      <div class="badge-remision">
        Remisión 2025-{{ $venta->id }}
      </div>
    </div>
    <div class="header-col header-col-right">
      @if(file_exists(public_path('images/logomedy.png')))
        <img src="{{ public_path('images/logomedy.png') }}" alt="Logo" class="logo">
      @endif
    </div>
  </div>

  {{-- CUADROS SUPERIORES EN DOS COLUMNAS --}}
  <div class="info-grid">
    <div class="info-col">
      <div class="cliente-box">
        <div class="box-title">Datos del cliente</div>
        <div class="info-pairs">
          <div class="info-row">
            <div class="info-label">Nombre</div>
            <div class="info-value">{{ $clienteNombre }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Plan</div>
            <div class="info-value">{{ mb_strtoupper($venta->plan ?? 'N/A','UTF-8') }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Fecha remisión</div>
            <div class="info-value">{{ $venta->created_at->format('d/m/Y H:i') }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="info-col">
      <div class="venta-box">
        <div class="box-title">Resumen de la operación</div>
        <div class="info-pairs">
          <div class="info-row">
            <div class="info-label">Total original</div>
            <div class="info-value">
              ${{ number_format($totalOriginal, 2) }}
            </div>
          </div>
          <div class="info-row">
            <div class="info-label">Total neto</div>
            <div class="info-value">
              ${{ number_format($totalNeto, 2) }}
            </div>
          </div>
          <div class="info-row">
            <div class="info-label">Pagos programados</div>
            <div class="info-value">{{ $totalCuotas }}</div>
          </div>
          <div class="info-row">
            <div class="info-label">Asesor / Vendedor</div>
            <div class="info-value">{{ mb_strtoupper($sellerName, 'UTF-8') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- TABLA PRINCIPAL: ANTICIPOS + TRADE-IN + PLAN --}}
  <div class="section-title">Calendario de pagos</div>

  @php
    $rowIndex = 1;
  @endphp

  @if(!$pagosPlan->count() && !$tieneAnticipos && !$tieneTradeIn)
    <p class="muted small">
      No hay pagos registrados para esta remisión.
    </p>
  @else
    <table>
      <thead>
      <tr>
        <th style="width:5%;">#</th>
        <th style="width:15%;">Fecha</th>
        <th style="width:22%;">Concepto</th>
        <th style="width:15%;">Pago</th>
        <th style="width:18%;">Código único</th>
        <th style="width:12%;" class="text-right">Monto</th>
        <th style="width:13%;">Firmas</th>
      </tr>
      </thead>
      <tbody>

      {{-- ANTICIPOS EN EL CALENDARIO --}}
      @if($tieneAnticipos)
        @foreach($pagosAnticipo as $pagoAnt)
          @php
            $anFecha = $pagoAnt->fecha_pago
                        ? \Carbon\Carbon::parse($pagoAnt->fecha_pago)->format('d/m/Y')
                        : '—';
            $anDesc  = trim($pagoAnt->descripcion ?? 'Pago de anticipo');
            $anMonto = (float) ($pagoAnt->monto ?? 0);
          @endphp
          <tr>
            <td class="text-center">{{ $rowIndex }}</td>
            <td>{{ $anFecha }}</td>
            <td>{{ $anDesc }}</td>
            <td>Anticipo</td>
            <td><span class="small">________________________</span></td>
            <td class="text-right">${{ number_format($anMonto, 2) }}</td>
            <td class="firmas-celda">
              <div class="firma-mini">
                <div class="firma-mini-linea"></div>
                <div class="firma-mini-label">Vendedor</div>
              </div>
              <div class="firma-mini">
                <div class="firma-mini-linea"></div>
                <div class="firma-mini-label">Cliente</div>
              </div>
              <div class="firma-mini">
                <div class="firma-mini-linea"></div>
                <div class="firma-mini-label">Gerente</div>
              </div>
            </td>
          </tr>
          @php $rowIndex++; @endphp
        @endforeach
      @endif

      {{-- TRADE-IN EN EL CALENDARIO --}}
      @if($tieneTradeIn)
        @foreach($pagosTradeIn as $pagoTi)
          @php
            $tiFecha = $pagoTi->fecha_pago
                        ? \Carbon\Carbon::parse($pagoTi->fecha_pago)->format('d/m/Y')
                        : '—';
            $tiDesc  = trim($pagoTi->descripcion ?? 'Pago por trade-in');
            $tiMonto = (float) ($pagoTi->monto ?? 0);
          @endphp
          <tr>
            <td class="text-center">{{ $rowIndex }}</td>
            <td>{{ $tiFecha }}</td>
            <td>{{ $tiDesc }}</td>
            <td>Trade-in</td>
            <td><span class="small">________________________</span></td>
            <td class="text-right">${{ number_format($tiMonto, 2) }}</td>
            <td class="firmas-celda">
              <div class="firma-mini">
                <div class="firma-mini-linea"></div>
                <div class="firma-mini-label">Vendedor</div>
              </div>
              <div class="firma-mini">
                <div class="firma-mini-linea"></div>
                <div class="firma-mini-label">Cliente</div>
              </div>
              <div class="firma-mini">
                <div class="firma-mini-linea"></div>
                <div class="firma-mini-label">Gerente</div>
              </div>
            </td>
          </tr>
          @php $rowIndex++; @endphp
        @endforeach
      @endif

      {{-- PAGOS DEL PLAN (FINANCIAMIENTO) --}}
      @foreach($pagosPlan->values() as $index => $pagoPlan)
        @php
          $numeroPago  = $index + 1;
          $fechaPlan   = $pagoPlan->fecha_pago
                          ? \Carbon\Carbon::parse($pagoPlan->fecha_pago)->format('d/m/Y')
                          : '—';

          $descripcion = trim($pagoPlan->descripcion ?? '');
          if ($descripcion === '') {
              $descripcion = $numeroPago === 1 ? 'Pago inicial' : "{$numeroPago}° pago";
          }
        @endphp
        <tr>
          <td class="text-center">{{ $rowIndex }}</td>
          <td>{{ $fechaPlan }}</td>
          <td>{{ $descripcion }}</td>
          <td>Pago {{ $numeroPago }} de {{ $totalCuotas }}</td>

          {{-- Código único: se llena a mano al imprimir --}}
          <td>
            <span class="small">________________________</span>
          </td>

          <td class="text-right">
            ${{ number_format($pagoPlan->monto, 2) }}
          </td>

          {{-- Firmas por pago --}}
          <td class="firmas-celda">
            <div class="firma-mini">
              <div class="firma-mini-linea"></div>
              <div class="firma-mini-label">Vendedor</div>
            </div>
            <div class="firma-mini">
              <div class="firma-mini-linea"></div>
              <div class="firma-mini-label">Cliente</div>
            </div>
            <div class="firma-mini">
              <div class="firma-mini-linea"></div>
              <div class="firma-mini-label">Gerente</div>
            </div>
          </td>
        </tr>
        @php $rowIndex++; @endphp
      @endforeach

      </tbody>
    </table>
  @endif

  {{-- RESUMEN GENERAL ABAJO (USANDO TOTAL NETO COMO BASE VS PLAN) --}}
  @php
    $dif = $totalProgramado - $totalNeto;
  @endphp
  <div class="resumen-box">
    <div class="resumen-header">
      <div class="resumen-title">Resumen general del plan</div>
    </div>
    <div class="resumen-body">
      <div class="res-item">
        <div class="res-label">Total original de la venta</div>
        <div class="res-value">
          ${{ number_format($totalOriginal, 2) }}
        </div>
      </div>
      <div class="res-item">
        <div class="res-label">Total neto (después de trade-in / anticipos)</div>
        <div class="res-value">
          ${{ number_format($totalNeto, 2) }}
        </div>
      </div>
      <div class="res-item">
        <div class="res-label">Total programado en este calendario (plan)</div>
        <div class="res-value">
          ${{ number_format($totalProgramado, 2) }}
        </div>
      </div>
     
    </div>
  </div>

  {{-- OBSERVACIONES --}}
  <div class="obs-box">
    <div class="obs-title">Observaciones</div>
    <ul>
      <li>El código único de cada pago se escribirá manualmente en el espacio correspondiente.</li>
      <li>El <strong>total original</strong> corresponde al valor de la venta antes de aplicar trade-in o anticipos.</li>
      <li>El <strong>total neto</strong> corresponde al saldo después de aplicar trade-in y anticipos, y es la base del calendario de pagos.</li>
      <li>Los pagos de anticipo y trade-in se muestran dentro del calendario de pagos y también en sus secciones de detalle.</li>
      <li>Cualquier modificación al plan (cambios de fecha, monto o número de pagos) deberá ser acordada por escrito y puede requerir la emisión de un nuevo plan de pagos.</li>
    </ul>
  </div>
</div>

</body>
</html>
