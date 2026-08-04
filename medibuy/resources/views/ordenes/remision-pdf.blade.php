<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Remisión Mantenimiento #{{ $remision->id }}</title>

  <style>
    @page { margin: 0cm 0cm; }

    body{
      font-family: Arial, sans-serif;
      margin: 2cm 1.5cm 2cm 1.5cm;
      font-size: 12px;
      color:#111;
    }

    .header{
      display:flex;
      justify-content:space-between;
      align-items:center;
      position:absolute;
      top:20px;
      width:90%;
    }
    .logo{ width:180px; height:auto; }

    .remision-info{
      font-size:14px;
      margin-right:43px;
      text-align:right;
      display:flex;
      align-items:center;
      line-height:1;
    }

    .info-box{
      border:1px solid #ffffff;
      padding:10px;
      margin-top:10px;
      background:#ffffff;
    }

    .table{
      width:100%;
      border-collapse:collapse;
      margin-top: 6px;
    }
    .table th, .table td{
      border:1px solid #ffffff;
      padding:8px;
      text-align:center;
      vertical-align:top;
    }
    .table th{
      background-color: rgba(30, 115, 190, 0.8);
      color:#fff;
    }

    .total-box{
      text-align:right;
      font-size:13px;
      padding:10px;
      margin-top:-8px;
    }

    .highlight{
      font-weight:bold;
      background-color:#1e73be;
      color:white;
      padding:5px;
      display:inline-block;
    }

    .mini{ font-size:11px; color:#555; }
    em{ color:#666; }

    .neg{ color:#b91c1c; font-weight:bold; }

    .footer-container{
      position:fixed;
      bottom:10px;
      left:0;
      width:100%;
      padding:15px;
      text-align:center;
    }
    .footer{ display:inline-block; text-align:left; }
    .sig-name{ font-weight:bold; font-size:13px; }
    .sig-role{ color:#777; }

    .page-break{ page-break-before:always; }

    .note-box{
      background:#eef4fb;
      border-left:4px solid #1e73be;
      padding:12px 14px;
      font-size:13px;
      color:#333;
      line-height:1.55;
    }
  </style>
</head>

<body>
@php
  $money = function($n){
    $n = (float)($n ?? 0);
    return '$' . number_format($n, 2);
  };

  $clienteNombre = mb_strtoupper(
      trim(($remision->cliente->nombre ?? '').' '.($remision->cliente->apellido ?? '')),
      'UTF-8'
  );
  if(!$clienteNombre) $clienteNombre = 'DESCONOCIDO';

  $tel = $remision->cliente->telefono ?? null;
  $dir = $remision->cliente->direccion ?? ($remision->cliente->comentarios ?? null);

  $fecha = $remision->created_at?->format('d/m/Y H:i') ?? '—';

  $envio     = (float)($remision->envio ?? 0);
  $subtotal  = (float)($remision->subtotal ?? 0);

  $aplicaIva = (bool)($remision->aplicar_iva ?? false);
  $iva       = (float)($remision->iva ?? 0);

  $total = (float)($remision->total ?? 0);
  if($total <= 0){
    $total = $subtotal + ($aplicaIva ? $iva : 0);
  }

  $anticipo = (float)($remision->anticipo ?? 0);

  $totalPagar = (float)($remision->total_pagar ?? 0);
  if($totalPagar <= 0){
    $totalPagar = max(0, $total - $anticipo);
  }

  $items = collect($remision->items ?? [])->map(function($item){
      $cantidad = (float)($item->cantidad ?? 0);
      $pu       = (float)($item->importe_unitario ?? 0);
      $subItem  = (float)($item->subtotal ?? ($cantidad * $pu));

      return (object)[
          'cantidad'         => $cantidad,
          'unidad'           => $item->unidad ?? 'SERVICIO',
          'nombre_item'      => $item->nombre_item ?? '—',
          'descripcion_item' => !empty(trim((string)($item->descripcion_item ?? '')))
                                    ? $item->descripcion_item
                                    : ($remision->remision_descripcion ?? null),
          'importe_unitario' => $pu,
          'subtotal'         => $subItem,
      ];
  });

  function img_to_datauri($path) {
      if (!is_readable($path)) return null;
      $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
      $mime = $ext === 'jpg' || $ext === 'jpeg'
                ? 'image/jpeg'
                : ($ext === 'png' ? 'image/png'
                : ($ext === 'gif' ? 'image/gif' : 'image/'.$ext));
      $data = base64_encode(file_get_contents($path));
      return "data:{$mime};base64,{$data}";
  }

  $logoDataUri  = img_to_datauri(public_path('images/logomedy.png'));
  $firmaDataUri = img_to_datauri(public_path('images/firma.png'));
@endphp

<div class="header">
  @if($logoDataUri)
    <img src="{{ $logoDataUri }}" alt="Grupo MediBuy" class="logo">
  @else
    <span><strong>Grupo MediBuy</strong></span>
  @endif

  <div class="remision-info">
    <span>
      <strong>REMISIÓN MANTENIMIENTO</strong><br>
      <span style="color: red;">No.2026-{{ $remision->id }}</span>
    </span>
  </div>
</div>

<div class="info-box">
  <p><strong>CLIENTE:</strong> {{ $clienteNombre }}</p>

  <p>
    <strong>TELÉFONO:</strong>
    {!! $tel ? mb_strtoupper($tel,'UTF-8') : '<em>DESCONOCIDO</em>' !!}
    <span style="float:right;">
      <strong>FECHA:</strong> {{ $fecha }}
    </span>
  </p>

  <p>
    <strong>DIRECCIÓN:</strong>
    {!! $dir ? mb_strtoupper($dir,'UTF-8') : '<em>DESCONOCIDO</em>' !!}
    <span style="float:right;">
      <strong>VIGENCIA:</strong> 10 DÍAS
    </span>
  </p>
</div>

<table class="table">
  <thead>
    <tr>
      <th style="width:70px;">Unidad</th>
      <th style="width:30px;">Cantidad</th>
      <th>Descripción</th>
      <th style="width:80px;">P. Unitario</th>
      <th style="width:80px;">Importe</th>
    </tr>
  </thead>

  <tbody>
    @forelse($items as $item)
      <tr>
        <td>{{ $item->unidad }}</td>
        <td>{{ rtrim(rtrim(number_format((float)$item->cantidad, 2, '.', ''), '0'), '.') }}</td>
        <td style="text-align:left;">
          {!! !empty($item->descripcion_item) ? nl2br(e($item->descripcion_item)) : '<em>Sin descripción</em>' !!}
        </td>
        <td>{{ $money($item->importe_unitario) }}</td>
        <td>{{ $money($item->subtotal) }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="6"><em>Sin partidas registradas</em></td>
      </tr>
    @endforelse
  </tbody>
</table>

<div class="total-box">
  @if($envio > 0)
    <p><strong>Envío:</strong> {{ $money($envio) }}</p>
  @endif

  <p><strong>Subtotal:</strong> {{ $money($subtotal) }}</p>

  @if($aplicaIva && $iva > 0)
    <p><strong>IVA (16%):</strong> {{ $money($iva) }}</p>
  @endif

  <p><strong>Total:</strong> {{ $money($total) }}</p>

  @if($anticipo > 0)
    <p class="neg"><strong>Anticipo:</strong> - {{ $money($anticipo) }}</p>
  @endif

  <p class="highlight"><strong>Total a pagar:</strong> {{ $money($totalPagar) }}</p>

  @if($anticipo > 0)
    <p class="mini" style="margin-top:6px;">
      <em>* El anticipo se descuenta del total calculado para esta remisión.</em>
    </p>
  @endif
</div>

@if(!empty($remision->nota))
  <p><strong>Nota:</strong> {{ $remision->nota }}</p>
@endif

<div class="footer-container">
  <div class="footer">
    <table cellpadding="4" cellspacing="0" style="font-family: Arial, sans-serif; font-size: 11px; color: #333;">
      <tr>
        <td valign="top" align="left">
          <div style="margin-top: 18px;">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding-right: 8px;" valign="middle">
                  @if($firmaDataUri)
                    <img src="{{ $firmaDataUri }}" alt="Firma" width="130">
                  @else
                    Firma
                  @endif
                </td>
                <td valign="middle">
                  <div class="sig-name">Anahí Tellez</div>
                  <div class="sig-role">Gerente General</div>
                </td>
              </tr>
            </table>
          </div>
        </td>

        <td width="2%" style="border-left: 1px solid #ccc;"></td>

        <td valign="top" align="left">
          <div style="margin-bottom: 3px;"><strong style="color:#555;">Tel:</strong> +52 722 448 5191</div>
          <div style="margin-bottom: 3px;"><strong style="color:#555;">Email:</strong> ventas@grupomedibuy.com</div>
          <div style="margin-bottom: 3px;"><strong style="color:#555;">Web:</strong> grupomedibuy.com</div>
          <div><strong style="color:#555;">Ubicación:</strong> Estado de México C.P. 52060</div>
        </td>
      </tr>
    </table>
  </div>
</div>

<div class="page-break" style="margin-top:-0.5cm; font-family: Arial, sans-serif;">
  <h2 style="color:#1e73be; font-weight:bold; text-align:center; margin-bottom:1.2rem;">
    Términos y condiciones · Remisión 2026-{{ $remision->id }}
  </h2>

  <div style="margin-bottom:1rem;">
    <h3 style="color:#1e73be; font-weight:bold; border-bottom:2px solid #1e73be; padding-bottom:4px;">
      Términos y Condiciones del Servicio
    </h3>

    <div class="note-box">
      <ul style="margin:0; padding-left:18px;">
        <li>Garantía del servicio aplica únicamente a la mano de obra realizada y/o refacciones instaladas; no cubre daños por golpes, humedad, mal uso, variaciones eléctricas, manipulaciones de terceros o desgaste normal.</li>
        <li>Si el equipo presenta fallas ajenas al servicio solicitado, se informará al cliente y se generará una cotización adicional.</li>
        <li>No nos hacemos responsables por accesorios no entregados o no descritos.</li>
        <li>Equipos abandonados por más de 60 días naturales podrán considerarse en abandono para recuperación de costos, previa notificación al contacto registrado.</li>
        <li>Para garantías se requiere esta remisión y evidencia del servicio.</li>
        <li>La garantía no aplica si el equipo fue abierto o intervenido por terceros.</li>
        <li>No aplica garantía debido a daños ocasionados por sobrecarga eléctrica en los componentes electrónicos.</li>
        <li>Grupo MediBuy no se responsabiliza por daños estéticos preexistentes si no fueron reportados al ingreso.</li>
        <li>En caso de envío o paquetería, el cliente acepta el costo de envío indicado en la remisión y el riesgo inherente al transporte.</li>
      </ul>
    </div>
  </div>

  <div style="margin-bottom: 1.2rem;">
    <h3 style="color:#1e73be; font-weight:bold; border-bottom:2px solid #1e73be; padding-bottom:4px;">
      Datos Bancarios para Transferencia
    </h3>

    @if($aplicaIva)
      <table style="width: 100%; font-size: 13px;">
        <tr><td><strong>Banco:</strong></td><td>Bancomer</td></tr>
        <tr><td><strong>Beneficiario:</strong></td><td>Anahí Téllez Ortiz</td></tr>
        <tr><td><strong>Cuenta:</strong></td><td>29 44 26 60 64</td></tr>
        <tr><td><strong>CLABE:</strong></td><td>0121 800 2944 2660 641</td></tr>
        <tr><td><strong>No. Tarjeta:</strong></td><td>4152 3135 5179 3107</td></tr>
        <tr>
          <td><strong>Concepto:</strong></td>
          <td>Remisión 2026-{{ $remision->id }} - {{ $clienteNombre }}</td>
        </tr>
      </table>
    @else
      <table style="width: 100%; font-size: 13px; margin-bottom: .9rem;">
        <tr><td><strong>Banco:</strong></td><td>Santander</td></tr>
        <tr><td><strong>Beneficiario:</strong></td><td>Gabriela Díaz García</td></tr>
        <tr><td><strong>CLABE:</strong></td><td>014 4206 0614 8217 181</td></tr>
        <tr><td><strong>No. Tarjeta:</strong></td><td>5579 0701 2907 7528</td></tr>
        <tr>
          <td><strong>Concepto:</strong></td>
          <td>Remisión 2026-{{ $remision->id }} - {{ $clienteNombre }}</td>
        </tr>
      </table>

      <table style="width: 100%; font-size: 13px;">
        <tr><td><strong>Banco:</strong></td><td>Banamex</td></tr>
        <tr><td><strong>Beneficiario:</strong></td><td>Gabriela Díaz García</td></tr>
        <tr><td><strong>CLABE:</strong></td><td>002 4209 0432 584 1851</td></tr>
        <tr><td><strong>No. Tarjeta:</strong></td><td>5256 7861 2056 8690</td></tr>
        <tr>
          <td><strong>Concepto:</strong></td>
          <td>Remisión 2026-{{ $remision->id }} - {{ $clienteNombre }}</td>
        </tr>
      </table>
    @endif

    <p style="margin-top: .6rem; font-size: 12px;">
      Envíe su comprobante a <strong>compras@grupomedibuy.com</strong> o WhatsApp <strong>+52 722 448 5191</strong>.
    </p>
  </div>
</div>

</body>
</html>