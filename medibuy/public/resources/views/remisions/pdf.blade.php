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

    /* ===== Header igual al de tu Venta ===== */
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

    /* ===== Cajas ===== */
    .info-box{
      border:1px solid #ffffff;
      padding:10px;
      margin-top:10px;
      background:#ffffff;
    }

    /* ===== Tabla principal ===== */
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

    /* ===== Totales ===== */
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

    /* ===== Footer ===== */
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

    /* Bloques “tipo aviso” para página 2 */
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
  // ===== Datos base (tolerantes) =====
  $clienteNombre = mb_strtoupper(trim(($remision->cliente->nombre ?? '').' '.($remision->cliente->apellido ?? '')), 'UTF-8');
  if(!$clienteNombre) $clienteNombre = 'DESCONOCIDO';

  $tel   = $remision->cliente->telefono ?? null;
  $dir   = $remision->cliente->direccion ?? ($remision->cliente->comentarios ?? null);

  $fecha   = $remision->created_at?->format('d/m/Y H:i') ?? '—';
  $tecnico = $remision->user->name ?? ($remision->usuario->name ?? null);

  $tipoServicio = $remision->tipo_servicio ?? $remision->tipo_mantenimiento ?? null;

  // Equipo / serie (si existen)
  $equipo = $remision->equipo ?? $remision->equipo_nombre ?? $remision->equipo_descripcion ?? null;
  $marca  = $remision->marca ?? null;
  $modelo = $remision->modelo ?? null;
  $serie  = $remision->numero_serie ?? $remision->serie ?? null;

  // ===== Totales + Envío =====
  // (soporta distintos nombres de campo)
  $envio = (float)(
      $remision->envio
      ?? $remision->costo_envio
      ?? $remision->costo_envio_mxn
      ?? $remision->shipping_cost
      ?? 0
  );

  // Si subtotal/iva/total ya vienen guardados, los respetamos.
  $subtotal = (float)($remision->subtotal ?? 0);
  $iva      = (float)($remision->iva ?? 0);
  $total    = (float)($remision->total ?? ($subtotal + $iva + $envio));
@endphp

<div class="header">
  <img src="{{ public_path('images/logomedy.png') }}" alt="Grupo MediBuy" class="logo">
  <div class="remision-info">
    <span>
      <strong>REMISIÓN</strong><br>
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

 

  {{-- ✅ Reemplazo simple (sin el cuadro grande de Servicio/Mantenimiento) --}}
  @if($tipoServicio || $equipo || $serie || $marca || $modelo)
    <p style="margin: 6px 0 0;">
      <strong>SERVICIO:</strong>
      {!! $tipoServicio ? mb_strtoupper($tipoServicio,'UTF-8') : '<em>—</em>' !!}
      <span style="float:right;">
        <strong>SERIE:</strong> {!! $serie ? mb_strtoupper($serie,'UTF-8') : '<em>SIN SERIE</em>' !!}
      </span>
    </p>

    <p style="margin: 6px 0 0;">
      <strong>EQUIPO:</strong>
      {!! $equipo ? mb_strtoupper($equipo,'UTF-8') : '<em>—</em>' !!}
      <span style="float:right;">
        <strong>MARCA/MODELO:</strong>
        {!! $marca ? mb_strtoupper($marca,'UTF-8') : '<em>—</em>' !!}
        {!! $modelo ? ' / '.mb_strtoupper($modelo,'UTF-8') : '' !!}
      </span>
    </p>
  @endif
</div>

<table class="table">
  <thead>
    <tr>
      <th style="width:90px;">Cantidad</th>
      <th style="width:110px;">Unidad</th>
      <th>Descripción</th>
      <th style="width:140px;">Importe Unitario</th>
      <th style="width:140px;">Subtotal</th>
    </tr>
  </thead>

  <tbody>
    @foreach(($remision->items ?? []) as $item)
      <tr>
        <td>{{ $item->cantidad }}</td>
        <td>{{ $item->unidad }}</td>
        <td style="text-align:left;">
          <strong>{{ mb_strtoupper($item->nombre_item ?? '—', 'UTF-8') }}</strong><br>
          <span class="mini">
            {!! !empty($item->descripcion_item) ? $item->descripcion_item : '<em>Sin descripción</em>' !!}
          </span>
        </td>
        <td>${{ number_format((float)($item->importe_unitario ?? 0), 2) }}</td>
        <td>${{ number_format((float)($item->subtotal ?? 0), 2) }}</td>
      </tr>
    @endforeach
  </tbody>
</table>

<div class="total-box">
  {{-- ✅ ENVÍO --}}
  @if($envio > 0)
    <p><strong>Envío:</strong> ${{ number_format($envio, 2) }}</p>
  @endif

  @if(($remision->aplicar_iva ?? false) && $iva > 0)
    <p><strong>Subtotal:</strong> ${{ number_format($subtotal, 2) }}</p>
    <p><strong>IVA (16%):</strong> ${{ number_format($iva, 2) }}</p>
  @endif

  <p class="highlight"><strong>Total:</strong> ${{ number_format($total, 2) }}</p>

  @if(!empty($remision->importe_letra))
    <p class="mini"><strong>Total en letra:</strong> {{ mb_strtoupper($remision->importe_letra, 'UTF-8') }}</p>
  @endif
</div>

@if(!empty($remision->nota))
  <p><strong>Nota:</strong> {{ $remision->nota }}</p>
@endif

{{-- Footer firma/contacto (igual estilo al de tu Venta) --}}
<div class="footer-container">
  <div class="footer">
    <table cellpadding="4" cellspacing="0" style="font-family: Arial, sans-serif; font-size: 11px; color: #333;">
      <tr>
        <td valign="top" align="left">
          <div style="margin-top: 18px;">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td style="padding-right: 8px;" valign="middle">
                  <img src="{{ public_path('images/firma.png') }}" alt="Firma" width="130">
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

{{-- ======================================================
   PÁGINA 2: TÉRMINOS + DATOS BANCARIOS
   ====================================================== --}}
<div class="page-break" style="margin-top:-0.5cm; font-family: Arial, sans-serif;">
  <h2 style="color:#1e73be; font-weight:bold; text-align:center; margin-bottom:1.2rem;">
    Términos y condiciones · Remisión 2025-{{ $remision->id }}
  </h2>

  <div style="margin-bottom:1rem;">
    <h3 style="color:#1e73be; font-weight:bold; border-bottom:2px solid #1e73be; padding-bottom:4px;">
      Términos y Condiciones del Servicio
    </h3>

    <div class="note-box">
      <ul style="margin:0; padding-left:18px;">
        <li>El cliente autoriza el diagnóstico y/o servicio descrito; cualquier trabajo adicional requiere autorización previa (firma, WhatsApp o correo).</li>
        <li>El diagnóstico puede generar costo si el cliente decide no realizar la reparación o no autoriza el presupuesto.</li>
        <li>Los tiempos de entrega dependen de disponibilidad de refacciones, proveedores y carga de trabajo; se notificará cualquier retraso.</li>
        <li>Garantía del servicio aplica únicamente a la mano de obra realizada y/o refacciones instaladas; no cubre daños por golpes, humedad, mal uso, variaciones eléctricas, manipulaciones de terceros o desgaste normal.</li>
        <li>Si el equipo presenta fallas ajenas al servicio solicitado (daños previos o fallas ocultas), se informará al cliente; su corrección puede generar costos adicionales.</li>
        <li>No nos hacemos responsables por accesorios no entregados o no descritos (cargadores, cables, sondas, transductores, pedales, maletines, etc.).</li>
        <li>El cliente es responsable de respaldar su información/configuración antes del servicio cuando aplique; no garantizamos recuperación de datos.</li>
        <li>Presupuestos y cotizaciones tienen vigencia de 10 días naturales; precios sujetos a cambio sin previo aviso.</li>
        <li>El equipo podrá permanecer en resguardo mientras se autoriza el servicio. Después de 15 días naturales sin respuesta del cliente, pueden aplicar cargos por almacenaje.</li>
        <li>Equipos abandonados por más de 60 días naturales podrán considerarse en abandono para recuperación de costos (previa notificación al contacto registrado).</li>
        <li>Para garantías: se requiere esta remisión y evidencia del servicio. La garantía no aplica si el equipo fue abierto o intervenido por terceros.</li>
        <li>Grupo MediBuy no se responsabiliza por daños estéticos preexistentes (rayones, golpes, desgaste) si no fueron reportados al ingreso.</li>
        <li>En caso de envío/paquetería, el cliente acepta el costo de envío indicado en la remisión y el riesgo inherente al transporte (daños por manejo del carrier no atribuibles a la empresa).</li>
      </ul>
    </div>
  </div>

  <div style="margin-bottom: 1.2rem;">
    <h3 style="color:#1e73be; font-weight:bold; border-bottom:2px solid #1e73be; padding-bottom:4px;">
      Datos Bancarios para Transferencia
    </h3>

    @if(($remision->aplicar_iva ?? false))
      <table style="width: 100%; font-size: 13px;">
        <tr><td><strong>Banco:</strong></td><td>Bancomer</td></tr>
        <tr><td><strong>Beneficiario:</strong></td><td>Anahí Téllez Ortiz</td></tr>
        <tr><td><strong>Cuenta:</strong></td><td>29 44 26 60 64</td></tr>
        <tr><td><strong>CLABE:</strong></td><td>0121 800 2944 2660 641</td></tr>
        <tr><td><strong>No. Tarjeta:</strong></td><td>4152 3135 5179 3107</td></tr>
        <tr>
          <td><strong>Concepto:</strong></td>
          <td>Remisión 2025-{{ $remision->id }} - {{ $clienteNombre }}</td>
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
          <td>Remisión 2025-{{ $remision->id }} - {{ $clienteNombre }}</td>
        </tr>
      </table>

      <table style="width: 100%; font-size: 13px;">
        <tr><td><strong>Banco:</strong></td><td>Banamex</td></tr>
        <tr><td><strong>Beneficiario:</strong></td><td>Gabriela Díaz García</td></tr>
        <tr><td><strong>CLABE:</strong></td><td>002 4209 0432 584 1851</td></tr>
        <tr><td><strong>No. Tarjeta:</strong></td><td>5256 7861 2056 8690</td></tr>
        <tr>
          <td><strong>Concepto:</strong></td>
          <td>Remisión 2025-{{ $remision->id }} - {{ $clienteNombre }}</td>
        </tr>
      </table>
    @endif

    <p style="margin-top: .6rem; font-size: 12px;">
      Envíe su comprobante a <strong>compras@grupomedibuy.com</strong> o WhatsApp <strong>+52 722 448 5191</strong>.
    </p>
  </div>

 
    {{-- Si tienes un pie/imagen, descomenta --}}
    {{-- <img src="{{ public_path('images/pie.jpeg') }}" alt="Pie" style="width:95%;"> --}}
  </div>
</div>

</body>
</html>
