<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Propuesta #{{ $propuesta->id }}</title>
   <style>
        @page { margin: 0cm 0cm; }

        body {
            font-family: Arial, sans-serif;
            margin: 2cm 1.5cm 2cm 1.5cm;
            font-size: 12px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: absolute;
            top: 20px;
            width: 90%;
        }

        .logo {
            width: 180px;
            height: auto;
        }

        .venta-info {
            font-size: 14px;
            margin-right: 43px;
            text-align: right;
            display: flex;
            align-items: center;
            line-height: 1;
        }

        .info-box {
            border: 1px solid #ffffff;
            padding: 10px;
            margin-top: 10px;
            background-color: #ffffff;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
        }

        .table th, .table td {
            border: 1px solid #ffffff;
            padding: 8px;
            text-align: center;
        }

        .table th {
            background-color: rgba(30, 115, 190, 0.8);
            color: white;
        }

        .highlight {
            font-weight: bold;
            background-color: #1e73be;
            color: white;
            padding: 5px;
            display: inline-block;
        }

        .total-box {
            text-align: right;
            font-size: 13px;
            padding: 10px;
            margin-top: -20px;
        }

.footer-container {
    position: fixed;
    bottom: 10px;
    left: 0;
    width: 100%;
    padding: 15px;
    text-align: center;
}

.footer {
    display: inline-block;
    text-align: left;
}


    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logomedy.png') }}" alt="Grupo MediBuy" class="logo">
        <div class="venta-info">
            <span><strong>Cotización:</strong><br>
            <span style="color: red;">No.2026{{ $propuesta->id }}</span></span>
        </div>
    </div>

<div class="info-box">
    <p><strong>CLIENTE:</strong> 
        {{ mb_strtoupper(trim(($propuesta->cliente->nombre ?? '') . ' ' . ($propuesta->cliente->apellido ?? '')), 'UTF-8') ?: '<em>DESCONOCIDO</em>' }}
    </p>

    <p><strong>TELÉFONO:</strong> 
        {!! $propuesta->cliente->telefono 
            ? mb_strtoupper($propuesta->cliente->telefono, 'UTF-8') 
            : '<em>DESCONOCIDO</em>' !!}
        <span style="float: right;">
            <strong>FECHA:</strong> {{ $propuesta->created_at->format('d/m/Y H:i') }}
        </span>
    </p>

    <p><strong>DIRECCIÓN:</strong> 
        {!! $propuesta->cliente->comentarios 
            ? mb_strtoupper($propuesta->cliente->comentarios, 'UTF-8') 
            : '<em>DESCONOCIDO</em>' !!}
        <span style="float: right;">
            <strong>RFC EMISOR:</strong> <em>TEOA890725GC0</em>
        </span>
    </p>

    @if($propuesta->cliente->email)
    <p><strong>EMAIL:</strong> {{ $propuesta->cliente->email }}
        <span style="float: right;">
            <strong>EMISOR:</strong> <em>ANAHÍ TELLEZ ORTIZ</em>
        </span>
    </p>
    @endif

    <p><strong>LUGAR:</strong> {{ $propuesta->lugar }}
        <span style="float: right;">
            <strong>RÉGIMEN FISCAL:</strong> 
            <em>Personas Físicas con Actividades Empresariales y Profesionales</em>
        </span>
    </p>
</div>
    <table class="table">
        <thead>
            <tr>
                <th>Equipo</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($propuesta->productos as $producto)
            <tr>
                <td>
                    <img src="{{ public_path('storage/' . ($producto->producto->imagen ?? 'default.jpg')) }}" width="50" alt="Imagen del producto">
                </td>
                <td>
                    {{ mb_strtoupper($producto->producto->tipo_equipo ?? '—', 'UTF-8') }}
                    {{ mb_strtoupper($producto->producto->modelo ?? '', 'UTF-8') }}<br>
                    {{ mb_strtoupper($producto->producto->marca ?? '', 'UTF-8') }}
                </td>
                <td>{{ $producto->cantidad }}</td>
                <td>${{ number_format($producto->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        @if($propuesta->subtotal > 0)
            <p><strong>Subtotal:</strong> ${{ number_format($propuesta->subtotal, 2) }}</p>
        @endif

        @if($propuesta->descuento > 0)
            <p><strong>Descuento:</strong> ${{ number_format($propuesta->descuento, 2) }}</p>
        @endif

        @if($propuesta->envio > 0)
            <p><strong>Envío:</strong> ${{ number_format($propuesta->envio, 2) }}</p>
        @endif

        @if($propuesta->iva > 0)
            <p><strong>IVA:</strong> ${{ number_format($propuesta->iva, 2) }}</p>
        @endif

        <p class="highlight"><strong>Total:</strong> ${{ number_format($propuesta->total, 2) }}</p>
    </div>
 @if($propuesta->nota)
        <p><strong>NOTA:</strong> {{ $propuesta->nota }}</p>
        @endif
<div style="text-align: center; margin-top: 5px; font-family: Arial, sans-serif;">
    <p style="font-size: 12px; color: #333; margin-bottom: 12px;">
        Escanea este código QR para consultar la propuesta en línea:
    </p>
    <img src="data:image/png;base64,{{ $qr }}" alt="Código QR" style="width: 100px; height: 100px;">
</div>

<div class="footer-container">
    <div class="footer">
        <table cellpadding="4" cellspacing="0" style="font-family: Arial, sans-serif; font-size: 11px; color: #333;">
            <tr>
                <!-- Columna izquierda con logotipo + datos -->
<td valign="top" align="left">
    <div style="margin-top: 20px;"> <!-- 👈 Ajusta el valor de margin-top según lo que necesites -->
        <table cellpadding="0" cellspacing="0">
            <tr>
                <!-- Firma -->
                <td style="padding-right: 8px;" valign="middle">
                    <img src="{{ public_path('images/firma.png') }}" alt="Firma" width="130">
                </td>
                <!-- Nombre y cargo -->
                <td valign="middle">
                    <div style="font-weight: bold; font-size: 13px;">Anahí Tellez</div>
                    <div style="color: #777;">Gerente General</div>
                </td>
            </tr>
        </table>
    </div>
</td>
                <!-- Separador -->
                <td style="width: 20px;">
                 <!-- Línea divisoria -->
                <td width="2%" style="border-left: 1px solid #ccc;"></td>
</td>
                <!-- Columna derecha con info de contacto -->
                <td valign="top" align="left">
                    <div style="margin-bottom: 3px;"><strong style="color:#555;">Tel:</strong>+52 722 448 5191</div>
                    <div style="margin-bottom: 3px;"><strong style="color:#555;">Email:</strong> ventas@grupomedibuy.com</div>
                    <div style="margin-bottom: 3px;"><strong style="color:#555;">Web:</strong> grupomedibuy.com</div>
                    <div><strong style="color:#555;">Ubicación:</strong> Estado de México C.P. 52060</div>
                </td>
            </tr>
        </table>
    </div>
</div>




<div style="page-break-before: always; margin-top: -0.5cm; font-family: Arial, sans-serif;">
    <h2 style="color: #1e73be; font-weight: bold; text-align: center; margin-bottom: 1.5rem;">
        Forma de Pago - Cotización No.2026{{ $propuesta->id }}
    </h2>

    @if($propuesta->plan === 'contado')
    <div style="margin-bottom: 2rem;">
        <div style="background-color: #eaf7ea; border-left: 4px solid #28a745; padding: 16px;">
            <h3 style="color: #28a745; margin: 0 0 10px;">Pago en una sola exhibición</h3>
            <p style="margin: 0; font-size: 13px; color: #333;">
                Esta propuesta contempla el pago en una sola exhibición. 
                Estamos a su disposición para resolver cualquier duda y acompañarle en el proceso de adquisición.
            </p>
        </div>
    </div>
@else


    @php
        function traducirMes($fecha) {
            $meses = [
                'January' => 'enero',
                'February' => 'febrero',
                'March' => 'marzo',
                'April' => 'abril',
                'May' => 'mayo',
                'June' => 'junio',
                'July' => 'julio',
                'August' => 'agosto',
                'September' => 'septiembre',
                'October' => 'octubre',
                'November' => 'noviembre',
                'December' => 'diciembre',
            ];

            $dia = $fecha->format('d');
            $mes = $meses[$fecha->format('F')] ?? $fecha->format('F');
            $anio = $fecha->format('Y');

            return "{$dia} de {$mes} de {$anio}";
        }

        $pagosFinanciamiento = $propuesta->pagosFinanciamiento()->orderBy('fecha_pago')->get();

        $pagos = [];
        $montoInicial = 0;

        foreach ($pagosFinanciamiento as $pago) {
            $etiqueta = ucfirst($pago->descripcion);
            $fecha = traducirMes(\Carbon\Carbon::parse($pago->fecha_pago));
            $monto = $pago->monto;

            if (strtolower($etiqueta) === 'pago inicial') {
                $montoInicial = $monto;
            }

            $pagos[] = "{$etiqueta} - {$fecha}: $" . number_format($monto, 2, '.', ',');
        }

        $total = $propuesta->total;
        $plazoMeses = count($pagos) - 1;
        $montoFinanciadoBase = $total - $montoInicial;
        $tasaInteresMensual = 0.05;

        $totalPagosMensuales = $pagosFinanciamiento->filter(fn($p) => strtolower($p->descripcion) !== 'pago inicial')->sum('monto');
        $montoConIntereses = $totalPagosMensuales;

        $col1 = array_slice($pagos, 0, 5);
        $col2 = array_slice($pagos, 5);
    @endphp

    @if (!empty($pagos))
        <div style="margin-bottom: 1rem;">
            <h3 style="color: #1e73be; font-weight: bold; border-bottom: 2px solid #1e73be; padding-bottom: 4px;">
                Detalles del Financiamiento
            </h3>

            <table width="100%" style="background-color: #eef4fb; border-left: 4px solid #1e73be; font-size: 13px; padding: 10px;">
                <tr valign="top">
                    <td width="50%" style="padding-right: 15px;">
                        @foreach ($col1 as $linea)
                            <p style="margin: 0 0 6px; color: #333;"><strong>{{ $linea }}</strong></p>
                        @endforeach
                    </td>
                    <td width="50%">
                        @foreach ($col2 as $linea)
                            <p style="margin: 0 0 6px; color: #333;"><strong>{{ $linea }}</strong></p>
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>
    @endif

    <div style="margin-bottom: 1rem;">
        <h3 style="color: #1e73be; font-weight: bold; border-bottom: 2px solid #1e73be; padding-bottom: 4px;">
            Resumen del Financiamiento
        </h3>
        <table style="width: 100%; font-size: 13px; margin-top: 0.5rem;">
            <tr>
                <td><strong>Total de la propuesta:</strong></td>
                <td>${{ number_format($total, 2, '.', ',') }}</td>
            </tr>
            <tr>
                <td><strong>Pago inicial:</strong></td>
                <td>${{ number_format($montoInicial, 2, '.', ',') }}</td>
            </tr>
            <tr>
                <td><strong>Monto financiado (sin intereses):</strong></td>
                <td>${{ number_format($montoFinanciadoBase, 2, '.', ',') }}</td>
            </tr>
            <tr>
                <td><strong>Plazo:</strong></td>
                <td>{{ $plazoMeses }} {{ Str::plural('meses', $plazoMeses) }}</td>
            </tr>

            @if($propuesta->plan === 'credito')
                <tr>
                    <td><strong>Tasa de interés mensual:</strong></td>
                    <td>5%</td>
                </tr>
                <tr>
                    <td><strong>Total a pagar con intereses:</strong></td>
                    <td><span style="color: #1e73be; font-weight: bold;">
                        ${{ number_format($montoConIntereses, 2, '.', ',') }}
                    </span></td>
                </tr>
            @endif
        </table>
    </div>
    @endif
</div>

<div style="margin-bottom: 1rem;">
    <h3 style="color: #1e73be; font-weight: bold; border-bottom: 2px solid #1e73be; padding-bottom: 4px;">
        Términos y Condiciones
    </h3>

    @if($propuesta->plan === 'contado')
        <ul style="font-size: 13px; line-height: 1.6;">
            <li>Este pago es único y debe realizarse al momento de la entrega o en la fecha acordada.</li>
            <li>El equipo será propiedad del cliente una vez confirmado el pago total.</li>
            <li>La garantía del equipo es de 6 meses a partir de la fecha de entrega.</li>
            <li>Los productos están sujetos a disponibilidad. Precios sujetos a cambio sin previo aviso.</li>
        </ul>
    @else
        <ul style="font-size: 13px; line-height: 1.6;">
            <li>Todos los pagos deberán realizarse puntualmente según el calendario acordado.</li>
            <li>En caso de retraso en el pago, se aplicará un cargo moratorio del 5% mensual sobre el monto vencido.</li>
            <li>El equipo permanecerá como propiedad de <strong>Grupo MediBuy</strong> hasta la liquidación total del pago.</li>
            <li>La garantía del equipo es de 6 meses a partir de la fecha de entrega.</li>
            <li>Los precios pueden cambiar sin previo aviso. Los productos están sujetos a disponibilidad.</li>
            <li>Cualquier ajuste a las condiciones de pago deberá ser autorizado por escrito por la empresa.</li>
        </ul>
    @endif
</div>

<div style="margin-bottom: 1rem;">
    <h3 style="color: #1e73be; font-weight: bold; border-bottom: 2px solid #1e73be; padding-bottom: 4px;">
        Datos Bancarios para Transferencia
    </h3>
    @if (empty($propuesta->iva) || $propuesta->iva == 0)
        <table style="width: 100%; font-size: 13px; margin-bottom: 1rem;">
            <tr><td><strong>Banco:</strong></td><td>Santander</td></tr>
            <tr><td><strong>Beneficiario:</strong></td><td>Gabriela Díaz García</td></tr>
            <tr><td><strong>CLABE:</strong></td><td>014420606148217181</td></tr>
            <tr><td><strong>Tarjeta:</strong></td><td>5579 0701 2907 7528</td></tr>
            <tr><td><strong>Concepto:</strong></td><td>Cotización No.2026{{ $propuesta->id }} - {{ mb_strtoupper($propuesta->cliente->nombre) }}</td></tr>
        </table>
        <table style="width: 100%; font-size: 13px;">
            <tr><td><strong>Banco:</strong></td><td>Banamex</td></tr>
            <tr><td><strong>Beneficiario:</strong></td><td>Gabriela Díaz García</td></tr>
            <tr><td><strong>CLABE:</strong></td><td>002420904325841851</td></tr>
            <tr><td><strong>Tarjeta:</strong></td><td>5256 7861 2056 8690</td></tr>
            <tr><td><strong>Concepto:</strong></td><td>Cotización No.2026{{ $propuesta->id }} - {{ mb_strtoupper($propuesta->cliente->nombre) }}</td></tr>
        </table>
    @else
        <table style="width: 100%; font-size: 13px;">
            <tr><td><strong>Banco:</strong></td><td>Bancomer</td></tr>
            <tr><td><strong>Beneficiario:</strong></td><td>Anahí Téllez Ortiz</td></tr>
            <tr><td><strong>Cuenta:</strong></td><td>2944266064</td></tr>
            <tr><td><strong>CLABE:</strong></td><td>012180029442660641</td></tr>
            <tr><td><strong>Tarjeta:</strong></td><td>4152 3145 7282 6959</td></tr>
            <tr><td><strong>Concepto:</strong></td><td>Cotización No.2026{{ $propuesta->id }} - {{ mb_strtoupper($propuesta->cliente->nombre) }}</td></tr>
        </table>
    @endif

    <p style="margin-top: 0.5rem; font-size: 12px;">
        Por favor, envíe el comprobante de pago al correo: <strong>compras@grupomedibuy.com</strong> o vía WhatsApp al <strong>+52 722 448 5191</strong>.
    </p>
</div>


</body>

</html>
