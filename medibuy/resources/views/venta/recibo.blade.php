<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recibo de Pago - Grupo MediBuy</title>
  <style>
    @page { size: A4; margin: 40px; } 

    /* ====== RESET & BASE ====== */
    * { box-sizing: border-box; }

    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      font-size: 12px;
      color: #334155; /* Gris carbón suave para excelente lectura */
      background: #FFFFFF;
      margin: 0; 
    }

    /* ====== PALETA PASTEL + UN POCO MÁS DE "PESO" ======
       (mismos tonos, más saturados/contrastados)
    */
    .border-subtle { border: 1px solid #CBD5E1; } /* antes #E2E8F0 */
    .bg-pastel-blue { background-color: #EEF6FF; } /* antes #F8FAFC */
    .bg-pastel-green { background-color: #EAFBF1; } /* antes #F0FDF4 */
    .text-muted { color: #475569; } /* antes #64748B */

    /* ====== HEADER MINIMALISTA ====== */
    .header-block {
      width: 100%;
      padding-bottom: 20px;
      border-bottom: 2px solid #94A3B8; /* antes #CBD5E1 */
      margin-bottom: 30px;
    }
    .header-table { width: 100%; border-collapse: collapse; }
    .header-table td { vertical-align: middle; }
    
    .logo { height: 45px; width: auto; display: block; }
    
    .header-title {
      font-size: 22px;
      font-weight: normal;
      color: #334155; /* antes #475569 */
      margin: 0;
      text-transform: uppercase;
      letter-spacing: 3px;
      text-align: right;
    }
    .header-folio {
      font-size: 13px;
      color: #64748B; /* antes #94A3B8 */
      text-align: right;
      margin-top: 8px;
      letter-spacing: 1px;
    }

    /* ====== INFO CLIENTE Y RESUMEN ====== */
    .info-block {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    .info-block td {
      padding: 15px 20px;
      vertical-align: top;
    
      border-radius: 4px;
    }
    .info-spacer { width: 15px; background: transparent !important; border: none !important; }

    .info-label {
      font-size: 10px;
      color: #64748B; /* antes #94A3B8 */
      text-transform: uppercase;
      letter-spacing: 1px;
      margin: 0 0 6px 0;
    }
    .info-value {
      font-size: 14px;
      color: #334155;
      margin: 0;
      font-weight: bold;
    }

    /* ====== TABLA DE DETALLES (ESTILO LEDGER LIGERO) ====== */
    .section-title {
      font-size: 12px;
      color: #475569; /* antes #64748B */
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 12px;
    }

    .details-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    .details-table th {
      background-color: #E2E8F0; /* antes #F1F5F9 */
      color: #475569;            /* antes #64748B */
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
      padding: 12px 10px;
      text-align: left;
      border-bottom: 1px solid #94A3B8; /* antes #CBD5E1 */
      font-weight: normal;
    }
    .details-table th.right, .details-table td.right { text-align: right; }
    .details-table td {
      padding: 14px 10px;
      border-bottom: 1px solid #CBD5E1; /* antes #E2E8F0 */
      font-size: 12px;
      color: #334155; /* antes #475569 */
    }

    /* ====== CAJA DE MONTO TOTAL (PASTEL) ====== */
    .total-container {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 40px;
    }
    .total-box {
      background-color: #E0F2FE; /* antes #F0F9FF */
      border: 1px solid #BAE6FD; /* antes #E0F2FE */
      padding: 25px;
      text-align: right;
      border-radius: 4px;
    }
    .total-label {
      font-size: 11px;
      color: #0284C7; /* más fuerte y legible */
      text-transform: uppercase;
      letter-spacing: 1px;
      margin: 0 0 8px 0;
    }
    .total-amount {
      font-size: 34px;
      font-weight: normal;
      color: #0369A1; /* antes #0284C7 (un poco más profundo) */
      margin: 0;
      letter-spacing: 1px;
    }
    .currency { font-size: 14px; color: #0284C7; font-weight: normal; }

    /* ETIQUETAS DE ESTADO PASTEL (más intensas) */
    .status-badge {
      display: inline-block;
      padding: 8px 14px;
      font-size: 11px;
      letter-spacing: 1px;
      text-transform: uppercase;
      border-radius: 4px;
      margin-top: 15px;
    }
    .status-paid { background-color: #DCFCE7; color: #166534; border: 1px solid #86EFAC; }
    .status-pending { background-color: #FFEDD5; color: #9A3412; border: 1px solid #FDBA74; }

    /* ====== VERIFICACIÓN Y SEGURIDAD ====== */
    .security-block {
      border-top: 1px dashed #94A3B8; /* más fuerte */
      padding-top: 20px;
      margin-bottom: 20px;
    }
    .security-title {
      font-size: 10px;
      color: #64748B; /* antes #94A3B8 */
      text-transform: uppercase;
      letter-spacing: 1px;
      margin: 0 0 8px 0;
    }
    .hash-code {
      font-family: 'Courier New', Courier, monospace;
      font-size: 12px;
      background: #EEF6FF; /* antes #F8FAFC */
      border: 1px solid #CBD5E1; /* más fuerte */
      padding: 6px 10px;
      color: #334155; /* antes #64748B */
      letter-spacing: 2px;
      border-radius: 3px;
      display: inline-block;
    }

    /* ====== FOOTER ====== */
    .footer {
      text-align: center;
      color: #64748B; /* antes #94A3B8 */
      font-size: 10px;
      line-height: 1.5;
    }
  </style>
</head>
<body>

@php
  // LÓGICA DE DATOS
  $clienteNombre = trim(($pago->venta->cliente->nombre ?? '') . ' ' . ($pago->venta->cliente->apellido ?? ''));
  if($clienteNombre === '') $clienteNombre = 'Cliente no especificado';

  $fechaPagoTxt = $pago->fecha_pago ? \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y - H:i') : '—';

  $detalle = $pago->detalle_metodos ?? null;
  if(is_string($detalle)){
    $tmp = json_decode($detalle, true);
    if(json_last_error() === JSON_ERROR_NONE) $detalle = $tmp;
  }

  $detalleRows = collect(is_array($detalle) ? $detalle : [])
      ->map(function($r){
        return [
          'metodo' => trim((string)($r['metodo'] ?? '')),
          'monto'  => (float)($r['monto'] ?? 0),
        ];
      })
      ->filter(fn($r)=> $r['metodo'] !== '' && $r['monto'] > 0)
      ->values();

  $esMixto = $detalleRows->count() > 0;

  $metodoTxt = trim((string)($pago->metodo_pago ?? ''));
  if($metodoTxt === '') $metodoTxt = 'No especificado';

  $hash = strtoupper(substr(sha1($pago->id . '|' . number_format((float)$pago->monto, 2, '.', '') . '|' . ($pago->fecha_pago ?? '')), 0, 16));
  $folioStr = str_pad($pago->id, 8, '0', STR_PAD_LEFT);
  $remisionStr = str_pad($pago->venta->id, 8, '0', STR_PAD_LEFT);
@endphp

  <div class="header-block">
    <table class="header-table">
      <tr>
        <td style="width: 50%;">
          <img src="{{ public_path('images/logomedy.png') }}" alt="Grupo MediBuy" class="logo">
        </td>
        <td style="width: 50%;">
          <p class="header-title">Recibo de Pago</p>
          <p class="header-folio">Ref. Operación: {{ $folioStr }}</p>
        </td>
      </tr>
    </table>
  </div>

  <table class="info-block">
    <tr>
      <td style="width: 48%;">
        <p class="info-label">Titular de la cuenta</p>
        <p class="info-value">{{ $clienteNombre }}</p>
      </td>
      <td class="info-spacer"></td>
      <td style="width: 48%;">
        <p class="info-label">Fecha de Pago</p>
        <p class="info-value">{{ $fechaPagoTxt }}</p>
      </td>
    </tr>
  </table>

  <div class="section-title">Conceptos facturados</div>
  <table class="details-table">
    <thead>
      <tr>
        <th>Descripción del Plan</th>
        <th>Número de Remisión</th>
        <th>Vía de Pago Registrada</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>{{ $pago->venta->plan }}</strong></td>
        <td>{{ $remisionStr }}</td>
        <td>{{ ucfirst(strtolower($metodoTxt)) }}</td>
      </tr>
    </tbody>
  </table>

  @if($esMixto)
    <div class="section-title" style="margin-top: 10px;">Desglose de fondos</div>
    <table class="details-table">
      <thead>
        <tr>
          <th>Instrumento de Pago</th>
          <th class="right">Importe Asignado</th>
        </tr>
      </thead>
      <tbody>
        @foreach($detalleRows as $r)
          <tr>
            <td>{{ ucfirst(strtolower($r['metodo'])) }}</td>
            <td class="right">${{ number_format((float)$r['monto'], 2) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif

  <table class="total-container">
    <tr>
      <td style="width: 50%; vertical-align: top;">
        @if($pago->aprobado)
          <div class="status-badge status-paid">Liquidado Exitosamente</div>
        @else
          <div class="status-badge status-pending">Acreditación Pendiente</div>
        @endif
      </td>
      <td style="width: 50%;">
        <div class="total-box">
          <p class="total-label">Importe Total Recibido</p>
          <p class="total-amount">${{ number_format((float)$pago->monto, 2) }} <span class="currency">MXN</span></p>
        </div>
      </td>
    </tr>
  </table>

  <div class="security-block">
    <p class="security-title">Validación de Autenticidad</p>
    <p style="margin: 0 0 10px 0; font-size: 11px; color: #64748B;">
      Cadena de seguridad generada algorítmicamente para esta transacción.
    </p>
    <div class="hash-code">{{ $hash }}</div>
    <p style="margin: 10px 0 0 0; font-size: 10px; color: #64748B;">
      Para validar, ingrese este código en: https://medibuy.grupomedibuy.com/verificar-recibo
    </p>
  </div>

  <div class="footer">
    <p style="margin: 0;">Grupo MediBuy — Documento de carácter informativo.</p>
    <p style="margin: 4px 0 0 0;">
      Este recibo confirma la recepción de fondos, pero no constituye una factura con validez fiscal a menos que se expida el CFDI correspondiente.
    </p>
  </div>

</body>
</html>