<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Plan de pagos - Remisión 2025-{{ $venta->id }}</title>
  <style>
    @page { size: letter; margin: 40px; }
    * { box-sizing: border-box; }

    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      font-size: 11px;
      color: #334155;
      margin: 0;
      padding: 0;
      background: #FFFFFF;
    }
    h1, h2, h3, h4, p { margin: 0; padding: 0; }

    .text-muted { color: #64748B; }
    .text-accent { color: #0284C7; }

    .header-table {
      width: 100%;
      border-collapse: collapse;
      border-bottom: 2px solid #CBD5E1;
      padding-bottom: 15px;
      margin-bottom: 25px;
    }
    .header-table td { vertical-align: bottom; padding-bottom: 15px; }

    .header-title {
      font-size: 18px;
      font-weight: normal;
      color: #475569;
      text-transform: uppercase;
      letter-spacing: 3px;
    }
    .header-sub {
      font-size: 11px;
      color: #64748B;
      margin-top: 6px;
      letter-spacing: 0.5px;
    }
    .badge-remision {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 4px;
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
      background: #F0F9FF;
      color: #0284C7;
      border: 1px solid #BAE6FD;
      margin-top: 8px;
    }
    .logo { width: 130px; height: auto; display: block; float: right; }

    .layout-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 25px;
    }
    .layout-table td { vertical-align: top; }
    .info-spacer { width: 4%; }

    .info-box {
      border-radius: 4px;
      padding: 15px;
      width: 48%;
    }
    .box-title {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: #94A3B8;
      margin-bottom: 10px;
      border-bottom: 1px solid #E2E8F0;
      padding-bottom: 4px;
    }

    .info-pairs { width: 100%; border-collapse: collapse; font-size: 11px; }
    .info-pairs td { padding-bottom: 6px; }
    .info-label { color: #64748B; width: 45%; }
    .info-value { color: #334155; font-weight: bold; width: 55%; }

    .section-title {
      font-size: 12px;
      color: #64748B;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 12px;
    }

    .main-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    .main-table th {
      background-color: #F1F5F9;
      color: #64748B;
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: 1px;
      padding: 10px 6px;
      text-align: left;
      border-bottom: 1px solid #CBD5E1;
      font-weight: normal;
    }
    .main-table td {
      padding: 12px 6px;
      font-size: 11px;
      color: #475569;
      border-bottom: 1px solid #E2E8F0;
      vertical-align: middle;
    }
    .main-table tr:nth-child(even) td { background-color: #F8FAFC; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .amount-cell { font-weight: bold; color: #0F172A; }

    .code-space {
      display: inline-block;
      width: 100%;
      height: 20px;
      background-color: #FFFFFF;
      border: 1px dashed #CBD5E1;
      border-radius: 3px;
    }

    .firmas-celda { font-size: 8px; width: 60px; }
    .firma-mini { margin-bottom: 6px; }
    .firma-mini-linea {
      border-bottom: 1px solid #94A3B8;
      margin-bottom: 2px;
      width: 100%;
    }
    .firma-mini-label {
      text-align: left;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #94A3B8;
      font-size: 7px;
    }

    .summary-layout {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    .summary-box {
      background-color: #F0F9FF;
      border: 1px solid #E0F2FE;
      padding: 15px;
      border-radius: 4px;
      width: 32%;
    }
    .summary-spacer { width: 2%; }
    .res-label {
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #38BDF8;
      margin-bottom: 4px;
    }
    .res-value {
      font-size: 18px;
      font-weight: normal;
      color: #0284C7;
      letter-spacing: 0.5px;
    }

    .obs-box {
      border-top: 1px dashed #CBD5E1;
      padding-top: 15px;
      font-size: 10px;
      color: #64748B;
      line-height: 1.5;
    }
    .obs-title {
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: #94A3B8;
      margin-bottom: 8px;
    }
    .obs-box ul { margin: 0; padding-left: 15px; }
    .obs-box li { margin-bottom: 4px; }

    /* ===== TOTALES DE PRODUCTOS (compacto) ===== */
    .totales-row td {
      padding: 4px 6px;
      border-bottom: 1px solid #E2E8F0;
      font-size: 9px;
    }
    .totales-row-final td {
      padding: 5px 6px;
      font-size: 10px;
      font-weight: bold;
    }
  </style>
</head>
<body>

  @php
    if(!function_exists('normalizaMetodoPago')){
      function normalizaMetodoPago($s){
        $s = trim((string)$s);
        if($s==='') return '';
        $t = mb_strtolower($s,'UTF-8');
        $t = str_replace(['_', '-'], ' ', $t);
        $t = preg_replace('/\s+/u',' ', $t) ?? $t;
        $t = trim($t);
        $map = [
          'transferencia'          => 'Transferencia bancaria',
          'transferencia bancaria' => 'Transferencia bancaria',
          'spei'                   => 'Transferencia bancaria',
          'tarjeta'                => 'Tarjeta',
          'tdd'                    => 'Tarjeta',
          'tdc'                    => 'Tarjeta',
          'efectivo'               => 'Efectivo',
          'cheque'                 => 'Cheque',
          'deposito'               => 'Depósito',
          'depósito'               => 'Depósito',
        ];
        return $map[$t] ?? ucwords($t);
      }
    }

    if(!function_exists('conceptoPagoReal')){
      function conceptoPagoReal($pago){
        $metodo     = normalizaMetodoPago($pago->metodo_pago ?? ($pago->metodo ?? ''));
        $esAnticipo = (bool)($pago->es_anticipo ?? false);
        $esTradeIn  = (bool)($pago->es_tradein  ?? false);
        $raw = mb_strtolower(trim((string)($pago->metodo_pago ?? $pago->metodo ?? '')),'UTF-8');
        $raw = str_replace(['_', '-'], ' ', $raw);
        $raw = preg_replace('/\s+/u',' ', $raw) ?? $raw;
        if(!$esAnticipo && preg_match('/\banticipo\b/u', $raw))           $esAnticipo = true;
        if(!$esTradeIn  && preg_match('/\btrade\s*in\b|\btrade-in\b/u',$raw)) $esTradeIn = true;
        $desc = trim((string)($pago->descripcion ?? ''));
        $auto = ($desc === '')
             || preg_match('/^pago$/iu',              $desc)
             || preg_match('/^pago\s*\d+$/iu',        $desc)
             || preg_match('/pago\s+de\s+anticipo/iu',$desc);
        if(!$auto) return $desc;
        if($esAnticipo) return $metodo ? "Anticipo ({$metodo})" : "Anticipo";
        if($esTradeIn)  return "Trade-in";
        return $metodo ? "Pago ({$metodo})" : "Pago";
      }
    }

    // ── Totales base ──────────────────────────────────────────────
    $totalCuotas     = $totalCuotas   ?? ($pagosPlan->count() ?? 0);
    $totalProgramado = isset($pagosPlan) ? (float)$pagosPlan->sum('monto') : 0;
    $totalOriginal   = isset($totalOriginal)
                         ? (float)$totalOriginal
                         : (float)($venta->total_original ?? $venta->total ?? 0);
    $totalNeto       = isset($totalNeto)
                         ? (float)$totalNeto
                         : (float)($venta->total_neto ?? $totalOriginal);

    // ── Variables para sección de productos ───────────────────────
    $tradeins       = $venta->tradeins ?? collect();
    $tradeinTotal   = (float)($venta->tradein_total ?? $tradeins->sum('valor_a_cuenta') ?? 0);
    $mostrarTradeIn = ($tradeinTotal > 0) && ($tradeins->count() > 0);

    $pagosRealesAprobados   = ($venta->pagosReales ?? collect())->where('aprobado', true);
    $pagosAnticipoAprobados = $pagosRealesAprobados->filter(fn($p) => ($p->es_anticipo ?? false));
    $anticipoPagado         = (float)$pagosAnticipoAprobados->sum('monto');

    // ── Pagos realizados (anticipo / trade-in para calendario) ────
    $pagosRealizados = $venta->pagos ?? collect();

    if (!isset($pagosAnticipo)) {
      $pagosAnticipo = $pagosRealizados->filter(function ($p) {
        $raw = mb_strtolower(trim((string)($p->metodo_pago ?? $p->metodo ?? '')),'UTF-8');
        $raw = str_replace(['_', '-'], ' ', $raw);
        $raw = preg_replace('/\s+/u',' ', $raw) ?? $raw;
        return (bool)($p->es_anticipo ?? false) || preg_match('/\banticipo\b/u', $raw);
      })->values();
    }

    if (!isset($pagosTradeIn)) {
      $pagosTradeIn = $pagosRealizados->filter(function ($p) {
        $raw = mb_strtolower(trim((string)($p->metodo_pago ?? $p->metodo ?? '')),'UTF-8');
        $raw = str_replace(['_', '-'], ' ', $raw);
        $raw = preg_replace('/\s+/u',' ', $raw) ?? $raw;
        return (bool)($p->es_tradein ?? false) || preg_match('/\btrade\s*in\b|\btrade-in\b/u', $raw);
      })->values();
    }

    $clienteNombre  = mb_strtoupper(
                        trim(($venta->cliente->nombre ?? '').' '.($venta->cliente->apellido ?? '')),
                        'UTF-8'
                      );
    $tieneAnticipos = isset($pagosAnticipo) && $pagosAnticipo->count() > 0;
    $tieneTradeIn   = isset($pagosTradeIn)  && $pagosTradeIn->count()  > 0;
  @endphp

  {{-- ── ENCABEZADO ─────────────────────────────────────────────── --}}
  <table class="header-table">
    <tr>
      <td style="width:60%;">
        <p class="header-title">Calendario de Pagos</p>
        <p class="header-sub">Titular de la cuenta: <strong>{{ $clienteNombre }}</strong></p>
        <div class="badge-remision">Ref. Operación: 2025-{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</div>
      </td>
      <td style="width:40%; text-align:right;">
        @if(file_exists(public_path('images/logomedy.png')))
          <img src="{{ public_path('images/logomedy.png') }}" alt="Grupo MediBuy" class="logo">
        @endif
      </td>
    </tr>
  </table>

  {{-- ── CUADROS SUPERIORES ──────────────────────────────────────── --}}
  <table class="layout-table">
    <tr>
      <td class="info-box">
        <div class="box-title">Datos del Cliente</div>
        <table class="info-pairs">
          <tr>
            <td class="info-label">Nombre de Titular</td>
            <td class="info-value">{{ $clienteNombre }}</td>
          </tr>
          <tr>
            <td class="info-label">Plan Contratado</td>
            <td class="info-value">{{ mb_strtoupper($venta->plan ?? 'N/D','UTF-8') }}</td>
          </tr>
          <tr>
            <td class="info-label">Fecha de Emisión</td>
            <td class="info-value">{{ $venta->created_at->format('d/m/Y - H:i') }}</td>
          </tr>
        </table>
      </td>

      <td class="info-spacer"></td>

      <td class="info-box">
        <div class="box-title">Resumen de Operación</div>
        <table class="info-pairs">
          <tr>
            <td class="info-label">Importe Original</td>
            <td class="info-value">${{ number_format($totalOriginal, 2) }}</td>
          </tr>
          <tr>
            <td class="info-label">Importe a Financiar</td>
            <td class="info-value text-accent">${{ number_format($totalNeto, 2) }}</td>
          </tr>
          <tr>
            <td class="info-label">Cuotas Programadas</td>
            <td class="info-value">{{ $totalCuotas }} exhibiciones</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  {{-- ── PRODUCTOS SELECCIONADOS ─────────────────────────────────── --}}
  @if($venta->productos->count() > 0)
    <div class="section-title" style="font-size:10px; margin-top:5px;">Productos Seleccionados</div>

    <table class="main-table" style="margin-bottom:12px;">
      <thead>
        <tr>
          <th style="width:38%; font-size:8px; padding:5px 6px;">Descripción</th>
          <th style="width:22%; font-size:8px; padding:5px 6px;">Marca / Modelo</th>
          <th style="width:20%; font-size:8px; padding:5px 6px;">N° Serie</th>
          <th style="width:8%;  font-size:8px; padding:5px 6px;" class="text-center">Cant.</th>
          <th style="width:12%; font-size:8px; padding:5px 6px;" class="text-right">Subtotal</th>
        </tr>
      </thead>
      <tbody>

        {{-- filas de productos --}}
        @foreach($venta->productos as $item)
          @php
            $producto   = $item->producto;
            $subLinea   = (float)($item->subtotal ?? 0);
            $esRegalo   = $subLinea <= 0;
            $tipoEquipo = mb_strtoupper($producto->tipo_equipo ?? '—', 'UTF-8');
            $marca      = mb_strtoupper($producto->marca  ?? '—', 'UTF-8');
            $modelo     = mb_strtoupper($producto->modelo ?? '—', 'UTF-8');
            $serie      = $item->registro
                            ? mb_strtoupper($item->registro->numero_serie, 'UTF-8')
                            : '—';
          @endphp
          <tr>
            <td style="padding:4px 6px; font-size:9px; border-bottom:1px solid #E2E8F0;">
              @if($producto)
                <strong>{{ $tipoEquipo }}</strong>
              @else
                <em style="color:#94A3B8;">Producto eliminado</em>
              @endif
            </td>
            <td style="padding:4px 6px; font-size:9px; border-bottom:1px solid #E2E8F0;">
              {{ $marca }} / {{ $modelo }}
            </td>
            <td style="padding:4px 6px; font-size:8px; color:#64748B; border-bottom:1px solid #E2E8F0;">
              {{ $serie }}
            </td>
            <td style="padding:4px 6px; font-size:9px; border-bottom:1px solid #E2E8F0;" class="text-center">
              {{ $item->cantidad }}
            </td>
            <td style="padding:4px 6px; font-size:9px; border-bottom:1px solid #E2E8F0;" class="text-right amount-cell">
              @if($esRegalo)
                <span style="color:#94A3B8; font-size:8px;">Regalo</span>
              @else
                ${{ number_format($subLinea, 2) }}
              @endif
            </td>
          </tr>
        @endforeach

        {{-- separador --}}
        <tr>
          <td colspan="5" style="padding:0; border-bottom:1px solid #CBD5E1;"></td>
        </tr>

        {{-- subtotal --}}
        <tr class="totales-row">
          <td colspan="4" class="text-right" style="color:#64748B; text-transform:uppercase; letter-spacing:1px;">
            Subtotal
          </td>
          <td class="text-right amount-cell">
            ${{ number_format((float)($venta->subtotal ?? $totalOriginal), 2) }}
          </td>
        </tr>

        {{-- descuento --}}
        @if((float)($venta->descuento ?? 0) > 0)
          <tr class="totales-row">
            <td colspan="4" class="text-right" style="color:#64748B; text-transform:uppercase; letter-spacing:1px;">
              Descuento
            </td>
            <td class="text-right" style="color:#DC2626; font-weight:bold;">
              - ${{ number_format((float)$venta->descuento, 2) }}
            </td>
          </tr>
        @endif

        {{-- envío --}}
        @if((float)($venta->envio ?? 0) > 0)
          <tr class="totales-row">
            <td colspan="4" class="text-right" style="color:#64748B; text-transform:uppercase; letter-spacing:1px;">
              Envío
            </td>
            <td class="text-right amount-cell">
              ${{ number_format((float)$venta->envio, 2) }}
            </td>
          </tr>
        @endif

        {{-- IVA --}}
        @if((float)($venta->iva ?? 0) > 0)
          <tr class="totales-row">
            <td colspan="4" class="text-right" style="color:#64748B; text-transform:uppercase; letter-spacing:1px;">
              IVA
            </td>
            <td class="text-right amount-cell">
              ${{ number_format((float)$venta->iva, 2) }}
            </td>
          </tr>
        @endif

        {{-- trade-in --}}
        @if($mostrarTradeIn && $tradeinTotal > 0)
          <tr class="totales-row">
            <td colspan="4" class="text-right" style="color:#64748B; text-transform:uppercase; letter-spacing:1px;">
              Trade-in (equipo a cuenta)
            </td>
            <td class="text-right" style="color:#DC2626; font-weight:bold;">
              - ${{ number_format($tradeinTotal, 2) }}
            </td>
          </tr>
        @endif

        {{-- anticipo --}}
        @if($anticipoPagado > 0)
          <tr class="totales-row">
            <td colspan="4" class="text-right" style="color:#64748B; text-transform:uppercase; letter-spacing:1px;">
              Anticipo aplicado
            </td>
            <td class="text-right" style="color:#DC2626; font-weight:bold;">
              - ${{ number_format($anticipoPagado, 2) }}
            </td>
          </tr>
        @endif

        {{-- TOTAL FINAL --}}
        <tr class="totales-row-final">
          <td colspan="4" class="text-right"
              style="color:#0F172A; text-transform:uppercase; letter-spacing:1px; border-top:1px solid #CBD5E1;">
            Total
          </td>
          <td class="text-right amount-cell"
              style="border-top:1px solid #CBD5E1;">
            ${{ number_format($totalOriginal, 2) }}
          </td>
        </tr>

      </tbody>
    </table>
  @endif
  {{-- ── FIN PRODUCTOS ────────────────────────────────────────────── --}}

  {{-- ── TABLA PRINCIPAL: PROGRAMACIÓN ──────────────────────────── --}}
  <div class="section-title">Programación de Amortizaciones</div>

  @php $rowIndex = 1; @endphp

  @if((!isset($pagosPlan) || !$pagosPlan->count()) && !$tieneAnticipos && !$tieneTradeIn)
    <p style="color:#94A3B8; font-size:11px;">No hay pagos registrados para este plan.</p>
  @else
    <table class="main-table">
      <thead>
        <tr>
          <th style="width:4%;"  class="text-center">#</th>
          <th style="width:12%;">Fecha Liq.</th>
          <th style="width:25%;">Concepto</th>
          <th style="width:16%;">Tipo de Mov.</th>
          <th style="width:18%;">Código de Valid.</th>
          <th style="width:15%;" class="text-right">Monto</th>
          <th style="width:10%;">Firmas</th>
        </tr>
      </thead>
      <tbody>

        {{-- anticipos --}}
        @if($tieneAnticipos)
          @foreach($pagosAnticipo as $pagoAnt)
            @php
              $anFecha    = $pagoAnt->fecha_pago
                              ? \Carbon\Carbon::parse($pagoAnt->fecha_pago)->format('d/m/Y')
                              : '—';
              $anConcepto = conceptoPagoReal($pagoAnt);
              $anMonto    = (float)($pagoAnt->monto ?? 0);
            @endphp
            <tr>
              <td class="text-center">{{ $rowIndex }}</td>
              <td>{{ $anFecha }}</td>
              <td><strong>{{ $anConcepto }}</strong></td>
              <td>Liquidación Previa</td>
              <td><span class="code-space"></span></td>
              <td class="text-right amount-cell">${{ number_format($anMonto, 2) }}</td>
              <td class="firmas-celda">
                <div class="firma-mini"><div class="firma-mini-linea"></div><div class="firma-mini-label">Vendedor</div></div>
                <div class="firma-mini"><div class="firma-mini-linea"></div><div class="firma-mini-label">Cliente</div></div>
              </td>
            </tr>
            @php $rowIndex++; @endphp
          @endforeach
        @endif

        {{-- trade-in --}}
        @if($tieneTradeIn)
          @foreach($pagosTradeIn as $pagoTi)
            @php
              $tiFecha    = $pagoTi->fecha_pago
                              ? \Carbon\Carbon::parse($pagoTi->fecha_pago)->format('d/m/Y')
                              : '—';
              $tiConcepto = conceptoPagoReal($pagoTi);
              $tiMonto    = (float)($pagoTi->monto ?? 0);
            @endphp
            <tr>
              <td class="text-center">{{ $rowIndex }}</td>
              <td>{{ $tiFecha }}</td>
              <td><strong>{{ $tiConcepto }}</strong></td>
              <td>Abono en Especie</td>
              <td><span class="code-space"></span></td>
              <td class="text-right amount-cell">${{ number_format($tiMonto, 2) }}</td>
              <td class="firmas-celda">
                <div class="firma-mini"><div class="firma-mini-linea"></div><div class="firma-mini-label">Vendedor</div></div>
                <div class="firma-mini"><div class="firma-mini-linea"></div><div class="firma-mini-label">Cliente</div></div>
              </td>
            </tr>
            @php $rowIndex++; @endphp
          @endforeach
        @endif

        {{-- pagos del plan --}}
        @foreach($pagosPlan->values() as $index => $pagoPlan)
          @php
            $numeroPago = $index + 1;
            $fechaPlan  = $pagoPlan->fecha_pago
                            ? \Carbon\Carbon::parse($pagoPlan->fecha_pago)->format('d/m/Y')
                            : '—';
            $desc = trim((string)($pagoPlan->descripcion ?? ''));
            $auto = ($desc === '')
                 || preg_match('/^pago$/iu',       $desc)
                 || preg_match('/^pago\s*\d+$/iu', $desc);
            if($auto) $desc = ($numeroPago === 1)
                                ? 'Amortización inicial'
                                : "{$numeroPago}° amortización";
            $tipoTxt = "Exhibición {$numeroPago}/{$totalCuotas}";
          @endphp
          <tr>
            <td class="text-center">{{ $rowIndex }}</td>
            <td>{{ $fechaPlan }}</td>
            <td>{{ $desc }}</td>
            <td>{{ $tipoTxt }}</td>
            <td><span class="code-space"></span></td>
            <td class="text-right amount-cell">${{ number_format((float)$pagoPlan->monto, 2) }}</td>
            <td class="firmas-celda">
              <div class="firma-mini"><div class="firma-mini-linea"></div><div class="firma-mini-label">Vendedor</div></div>
              <div class="firma-mini"><div class="firma-mini-linea"></div><div class="firma-mini-label">Cliente</div></div>
            </td>
          </tr>
          @php $rowIndex++; @endphp
        @endforeach

      </tbody>
    </table>
  @endif

  {{-- ── RESUMEN GENERAL ─────────────────────────────────────────── --}}
  <table class="summary-layout">
    <tr>
      <td class="summary-box">
        <p class="res-label">Base de la Venta</p>
        <p class="res-value" style="color:#334155;">${{ number_format($totalOriginal, 2) }}</p>
      </td>
      <td class="summary-spacer"></td>
      <td class="summary-box">
        <p class="res-label">Saldo Neto a Financiar</p>
        <p class="res-value" style="color:#334155;">${{ number_format($totalNeto, 2) }}</p>
      </td>
      <td class="summary-spacer"></td>
      <td class="summary-box" style="background-color:#E0F2FE; border-color:#BAE6FD;">
        <p class="res-label" style="color:#0284C7;">Total Programado (Plan)</p>
        <p class="res-value" style="font-weight:bold;">${{ number_format($totalProgramado, 2) }}</p>
      </td>
    </tr>
  </table>

  {{-- ── OBSERVACIONES ───────────────────────────────────────────── --}}
  <div class="obs-box">
    <p class="obs-title">Políticas y Observaciones del Plan</p>
    <ul>
      <li>El código único de validación de cada pago será registrado manualmente por el asesor en el espacio provisto al momento de la liquidación.</li>
      <li>El <strong>Importe Original</strong> refiere al valor de lista o negociado de los servicios antes de deducciones por Trade-in o anticipos previos.</li>
      <li>El <strong>Saldo Neto a Financiar</strong> representa el monto total diferido en las exhibiciones calendarizadas.</li>
      @if($tieneAnticipos || $tieneTradeIn)
        <li>Se han documentado en este cronograma las liquidaciones previas (Anticipos/Trade-in) para conciliación contable.</li>
      @endif
      <li>Toda reestructuración del presente calendario (modificación de montos, plazos o fechas de liquidación) requerirá autorización gerencial y la emisión de un nuevo convenio.</li>
    </ul>
  </div>

</body>
</html>s