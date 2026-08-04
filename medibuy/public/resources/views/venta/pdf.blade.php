<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Venta #{{ $venta->id }}</title>
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

        .logo { width: 180px; height: auto; }

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
            margin-top: -15px;
        }

        .mini {
            font-size: 11px;
            color: #555;
        }

        /* =========================================================
           ✅ TRADE-IN MODERNO / MINIMALISTA
           ========================================================= */
        .tradein-card{
            margin-top:12px;
            border:1px solid #e6eef6;
            border-radius:12px;
            background:#fbfdff;
            padding:10px 12px;
        }
        .tradein-head{
            width:100%;
            margin-bottom:8px;
        }
        .tradein-head .left{
            float:left;
            font-size:12px;
            font-weight:700;
            color:#0f3d7a;
            letter-spacing:.3px;
            text-transform:uppercase;
        }
        .tradein-head .right{
            float:right;
        }
        .tradein-head:after{ content:""; display:block; clear:both; }

        .tradein-total-chip{
            font-size:11.5px;
            font-weight:700;
            color:#b91c1c;
            background:#fff1f2;
            border:1px solid #fecdd3;
            padding:3px 8px;
            border-radius:999px;
            white-space:nowrap;
        }

        .tradein-table{
            width:100%;
            border-collapse:collapse;
            font-size:11.2px;
        }
        .tradein-table th{
            background:#f3f6fb;
            color:#334155;
            text-transform:uppercase;
            font-size:10px;
            letter-spacing:.45px;
            padding:6px 6px;
            border-bottom:1px solid #e6eef6;
            text-align:left;
        }
        .tradein-table td{
            padding:7px 6px;
            border-bottom:1px solid #eef2f7;
            vertical-align:top;
            text-align:left;
        }
        .tradein-table tr:last-child td{ border-bottom:none; }

        .tradein-eq{
            font-weight:700;
            color:#111827;
        }
        .tradein-meta{
            color:#64748b;
            font-size:10.5px;
            margin-top:2px;
        }
        .tradein-series{
            font-size:10.3px;
            color:#0b3b8a;
            background:#eaf2ff;
            padding:1px 6px;
            border-radius:6px;
            display:inline-block;
        }
        .tradein-value{
            text-align:right !important;
            font-weight:700;
            color:#b91c1c;
            white-space:nowrap;
        }
        /* ========================================================= */

        .neg { color:#b91c1c; font-weight:bold; }

        .footer-container {
            position: fixed;
            bottom: 10px;
            left: 0;
            width: 100%;
            padding: 15px;
            text-align: center;
        }
        .footer { display: inline-block; text-align: left; }
    </style>
</head>
<body>

@php
    // =========================
    //  TRADE-IN / TOTALES
    // =========================
    $tradeins = $venta->tradeins ?? collect();

    $totalOriginal = (float)($venta->total_original ?? $venta->total ?? 0);
    // tu controller guarda "valor", NO "valor_a_cuenta"
    $tradeinTotal  = (float)($venta->tradein_total ?? ($tradeins->sum('valor') ?? 0));
    $totalNeto     = (float)($venta->total_neto ?? max($totalOriginal - $tradeinTotal, 0));

    // Pagos reales aprobados
    $pagosAprobados   = $venta->pagosReales?->filter(fn($p)=>$p->aprobado) ?? collect();
    $totalPagado      = (float)$pagosAprobados->sum('monto');
    $hayPagoAprobado  = $pagosAprobados->count() > 0;

    // Anticipo real (tabla pagos.es_anticipo)
    $anticipoReales    = $pagosAprobados->filter(fn($p)=> (bool)($p->es_anticipo ?? false));
    $montoAnticipoReal = (float)$anticipoReales->sum('monto');

    // Base de progreso y saldo restante: SIEMPRE contra el total original
    $baseProgreso   = $totalOriginal;
    $saldoRestante  = max($baseProgreso - $totalPagado, 0);
    $estaLiquidada  = $saldoRestante <= 0.01;
@endphp

<div class="header">
    <img src="{{ public_path('images/logomedy.png') }}" alt="Grupo MediBuy" class="logo">
    <div class="venta-info">
        <span>
            <strong>Remisión:</strong><br>
            <span style="color: red;">No.2026-{{ $venta->id }}</span>
        </span>
    </div>
</div>

<div class="info-box">
    <p><strong>CLIENTE:</strong>
        {{ mb_strtoupper(trim(($venta->cliente->nombre ?? '') . ' ' . ($venta->cliente->apellido ?? '')), 'UTF-8') ?: 'DESCONOCIDO' }}
    </p>
    <p><strong>TELÉFONO:</strong>
        {!! $venta->cliente->telefono ? mb_strtoupper($venta->cliente->telefono, 'UTF-8') : '<em>DESCONOCIDO</em>' !!}
        <span style="float:right;">
            <strong>Fecha:</strong> {{ $venta->created_at->format('d/m/Y H:i') }}
        </span>
    </p>
    <p><strong>DIRECCIÓN:</strong>
        {!! $venta->cliente->comentarios ? mb_strtoupper($venta->cliente->comentarios, 'UTF-8') : '<em>DESCONOCIDO</em>' !!}
        <span style="float:right;">
            <strong>RFC Emisor:</strong> <em>TEOA890725GC0</em>
        </span>
    </p>
    @if($venta->cliente->email)
        <p><strong>EMAIL:</strong> {{ $venta->cliente->email }}
            <span style="float:right;">
                <strong>EMISOR:</strong> <em>ANAHÍ TELLEZ ORTIZ</em>
            </span>
        </p>
    @endif
    <p><strong>LUGAR:</strong> {{ $venta->lugar }}
        <span style="float:right;">
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
        @foreach($venta->productos as $item)
        <tr>
            <td>
                <img src="{{ public_path('storage/' . ($item->producto->imagen ?? 'default.jpg')) }}" width="50" alt="Imagen del producto">
            </td>
            <td style="text-align:left;">
                <strong>{{ mb_strtoupper($item->producto->tipo_equipo ?? '—', 'UTF-8') }}</strong><br>
                {{ mb_strtoupper($item->producto->modelo ?? '', 'UTF-8') }} |
                {{ mb_strtoupper($item->producto->marca ?? '', 'UTF-8') }}<br>
                @if($item->registro?->numero_serie)
                    <span style="color:#1a73e8;">Serie: {{ $item->registro->numero_serie }}</span>
                @else
                    <span style="color:#999;">Sin número de serie</span>
                @endif
            </td>
            <td>{{ $item->cantidad }}</td>
            <td>${{ number_format($item->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- =========================
     ✅ EQUIPO A CUENTA (TRADE-IN)
     ========================= --}}
@if($tradeins->count() > 0)
    <div class="tradein-card">
        <div class="tradein-head">
            <div class="left">Equipo a cuenta (Trade-in)</div>
            <div class="right">
                <span class="tradein-total-chip">
                    Descuento aplicado: - ${{ number_format($tradeinTotal,2) }}
                </span>
            </div>
        </div>

        <table class="tradein-table">
            <thead>
                <tr>
                    <th style="width:55%;">Equipo recibido</th>
                    <th style="width:20%;">Serie</th>
                    <th style="width:25%; text-align:right;">Valor a cuenta</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tradeins as $ti)
                    @php
                        $marcaModelo = trim(($ti->marca ?? '').' '.($ti->modelo ?? ''));
                    @endphp
                    <tr>
                        <td>
                            <div class="tradein-eq">
                                {{ mb_strtoupper($ti->descripcion ?? 'EQUIPO', 'UTF-8') }}
                            </div>
                            <div class="tradein-meta">
                                {{ $marcaModelo ? mb_strtoupper($marcaModelo,'UTF-8') : '—' }}
                            </div>
                            @if(!empty($ti->observaciones))
                                <div class="tradein-meta">
                                    Obs: {{ $ti->observaciones }}
                                </div>
                            @endif
                        </td>

                        <td>
                            @if(!empty($ti->numero_serie))
                                <span class="tradein-series">
                                    {{ mb_strtoupper($ti->numero_serie,'UTF-8') }}
                                </span>
                            @else
                                <span class="tradein-meta">Sin serie</span>
                            @endif
                        </td>

                        <td class="tradein-value">
                            - ${{ number_format((float)($ti->valor ?? 0), 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<div class="total-box">
    @if($venta->subtotal > 0)
        <p><strong>Subtotal:</strong> ${{ number_format($venta->subtotal, 2) }}</p>
    @endif

    @if($venta->descuento > 0)
        <p><strong>Descuento:</strong> ${{ number_format($venta->descuento, 2) }}</p>
    @endif

    @if($venta->envio > 0)
        <p><strong>Envío:</strong> ${{ number_format($venta->envio, 2) }}</p>
    @endif

    @if($venta->iva > 0)
        <p><strong>IVA:</strong> ${{ number_format($venta->iva, 2) }}</p>
    @endif

    @if($tradeins->count() > 0)
        <p><strong>Total original:</strong> ${{ number_format($totalOriginal, 2) }}</p>
        <p class="neg"><strong>(-) Trade-in:</strong> - ${{ number_format($tradeinTotal, 2) }}</p>
        <p class="highlight"><strong>Total neto:</strong> ${{ number_format($totalNeto, 2) }}</p>
    @else
        <p class="highlight"><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>
    @endif

    @if($montoAnticipoReal > 0)
        <p><strong>Anticipo pagado:</strong> ${{ number_format($montoAnticipoReal, 2) }}</p>
    @endif

    @if($saldoRestante > 0.01)
        <p><strong>Saldo restante (sobre total original):</strong> ${{ number_format($saldoRestante, 2) }}</p>
    @else
        <p><strong>Saldo restante:</strong> $0.00 (Liquidado)</p>
    @endif
</div>

@if($venta->nota)
    <p><strong>Nota:</strong> {{ $venta->nota }}</p>
@endif

<div style="text-align:center; margin-top:5px;">
    <p><strong>Escanea este código QR para acceder a esta venta:</strong></p>
    <img src="data:image/png;base64,{{ $qr }}" alt="QR Code" style="width:100px; height:100px;">
</div>

<div class="footer-container">
    <div class="footer">
        <table cellpadding="4" cellspacing="0" style="font-family: Arial, sans-serif; font-size: 11px; color: #333;">
            <tr>
                <td valign="top" align="left">
                    <div style="margin-top: 20px;">
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="padding-right: 8px;" valign="middle">
                                    <img src="{{ public_path('images/firma.png') }}" alt="Firma" width="130">
                                </td>
                                <td valign="middle">
                                    <div style="font-weight: bold; font-size: 13px;">Anahí Tellez</div>
                                    <div style="color: #777;">Gerente General</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>

                <td width="2%" style="border-left: 1px solid #ccc;"></td>

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

{{-- ======================================================
     PÁGINA 2: FORMA DE PAGO
     ====================================================== --}}
<div style="page-break-before: always; margin-top: -0.5cm; font-family: Arial, sans-serif;">
    <h2 style="color:#1e73be; font-weight:bold; text-align:center; margin-bottom:1.5rem;">
        Forma de Pago - Remisión 2026-{{ $venta->id }}
    </h2>

    {{-- ====== CONTADO ====== --}}
    @if($venta->plan === 'contado')

        @if(!$hayPagoAprobado)
            <div style="margin-bottom: 2rem;">
                <div style="background-color:#fff7ed; border-left:4px solid #f59e0b; padding:16px;">
                    <h3 style="color:#b45309; margin:0 0 10px;">Venta de contado con pago pendiente</h3>
                    <p style="margin:0; font-size:13px; color:#333;">
                        Esta remisión es de contado, pero aún no hay un pago aprobado registrado.
                        En cuanto se registre, este apartado se marcará con los pagos correspondientes.
                    </p>
                </div>
            </div>
        @else
            @if($estaLiquidada)
                <div style="margin-bottom: 1rem;">
                    <div style="background-color:#eaf7ea; border-left:4px solid #28a745; padding:16px;">
                        <h3 style="color:#28a745; margin:0 0 10px;">Pago registrado correctamente</h3>
                        <p style="margin:0; font-size:13px; color:#333;">
                            Se ha registrado el pago completo de esta remisión.
                            Agradecemos su preferencia.
                        </p>
                        <p class="mini" style="margin-top:8px;">
                            Total pagado (pagos aprobados): ${{ number_format($totalPagado,2) }}
                        </p>
                    </div>
                </div>
            @else
                <div style="margin-bottom: 1rem;">
                    <div style="background-color:#fff7ed; border-left:4px solid #f59e0b; padding:16px;">
                        <h3 style="color:#b45309; margin:0 0 10px;">Pagos parciales registrados</h3>
                        <p style="margin:0; font-size:13px; color:#333;">
                            Se han registrado pagos parciales para esta remisión de contado.
                            Aún queda un saldo pendiente por liquidar.
                        </p>
                        <p class="mini" style="margin-top:8px;">
                            Total pagado (pagos aprobados): ${{ number_format($totalPagado,2) }}<br>
                            Saldo restante sobre el total original: ${{ number_format($saldoRestante,2) }}
                        </p>
                    </div>
                </div>
            @endif

            {{-- Lista de pagos aprobados (incluye anticipo real) --}}
            <table width="100%" style="border-collapse:collapse; font-size:12px; margin-bottom:1rem;">
                <thead>
                    <tr style="background:#eef2ff; color:#1e293b;">
                        <th style="padding:6px; border:1px solid #d1d5db; text-align:left;">Fecha</th>
                        <th style="padding:6px; border:1px solid #d1d5db; text-align:left;">Detalle</th>
                        <th style="padding:6px; border:1px solid #d1d5db; text-align:right;">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pagosAprobados as $pagoReal)
                        @php
                            $esAnticipoReal = (bool)($pagoReal->es_anticipo ?? false);
                        @endphp
                        <tr>
                            <td style="padding:6px; border:1px solid #e5e7eb;">
                                {{ \Carbon\Carbon::parse($pagoReal->created_at)->format('d/m/Y H:i') }}
                            </td>
                            <td style="padding:6px; border:1px solid #e5e7eb;">
                                Pago aprobado
                                @if($esAnticipoReal)
                                    <span style="background:#e0f2fe; color:#1d4ed8; font-size:10px; padding:2px 6px; border-radius:999px; margin-left:4px;">
                                        Anticipo
                                    </span>
                                @endif
                                @if($pagoReal->metodo_pago)
                                    <span class="mini"> · {{ mb_strtoupper($pagoReal->metodo_pago,'UTF-8') }}</span>
                                @endif
                            </td>
                            <td style="padding:6px; border:1px solid #e5e7eb; text-align:right;">
                                ${{ number_format($pagoReal->monto,2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

    {{-- ====== FINANCIAMIENTO / CRÉDITO ====== --}}
    @else

        @php
            function traducirMes($fecha) {
                $meses = [
                    'January'=>'enero','February'=>'febrero','March'=>'marzo','April'=>'abril','May'=>'mayo','June'=>'junio',
                    'July'=>'julio','August'=>'agosto','September'=>'septiembre','October'=>'octubre','November'=>'noviembre','December'=>'diciembre',
                ];
                $dia = $fecha->format('d');
                $mesIngles = $fecha->format('F');
                $mesEspanol = $meses[$mesIngles] ?? $mesIngles;
                $anio = $fecha->format('Y');
                return "{$dia} de {$mesEspanol} de {$anio}";
            }

            $pagosFinanciamiento = $venta->pagosFinanciamiento()->orderBy('fecha_pago')->get();

            $pagosTxt = [];
            $montoInicial = 0;

            foreach ($pagosFinanciamiento as $pago) {
                $desc = trim((string)$pago->descripcion);
                $fechaCarbon = \Carbon\Carbon::parse($pago->fecha_pago);
                $fechaEs = traducirMes($fechaCarbon);
                $monto = (float)$pago->monto;

                if (strtolower($desc) === 'pago inicial') {
                    $montoInicial = $monto;
                    $label = 'Anticipo';
                } else {
                    $label = ucfirst($desc);
                }

                $pagosTxt[] = "{$label} - {$fechaEs}: $" . number_format($monto, 2, '.', ',');
            }

            $total = $totalNeto;
            $plazoMeses = max(count($pagosFinanciamiento->filter(fn($p)=>strtolower(trim($p->descripcion))!=='pago inicial')), 0);
            $montoFinanciadoBase = max($total - $montoInicial, 0);
            $tasaInteresMensual = 0.05;

            $totalPagosMensuales = (float)$pagosFinanciamiento
                ->filter(fn($p)=>strtolower(trim($p->descripcion))!=='pago inicial')
                ->sum('monto');

            $montoConIntereses = $totalPagosMensuales;

            $col1 = array_slice($pagosTxt, 0, 5);
            $col2 = array_slice($pagosTxt, 5);
        @endphp

        @if (!empty($pagosTxt))
            <div style="margin-bottom:1rem;">
                <h3 style="color:#1e73be; font-weight:bold; border-bottom:2px solid #1e73be; padding-bottom:4px;">
                    Detalles del Financiamiento
                </h3>

                <table width="100%" style="background-color:#eef4fb; border-left:4px solid #1e73be; font-size:13px; padding:10px;">
                    <tr valign="top">
                        <td width="50%" style="padding-right: 15px;">
                            @foreach ($col1 as $linea)
                                <p style="margin:0 0 6px; color:#333;"><strong>{{ $linea }}</strong></p>
                            @endforeach
                        </td>
                        <td width="50%">
                            @foreach ($col2 as $linea)
                                <p style="margin:0 0 6px; color:#333;"><strong>{{ $linea }}</strong></p>
                            @endforeach
                        </td>
                    </tr>
                </table>
            </div>
        @endif

        <div style="margin-bottom:1rem;">
            <h3 style="color:#1e73be; font-weight:bold; border-bottom:2px solid #1e73be; padding-bottom:4px;">
                Resumen del Financiamiento
            </h3>
            <table style="width: 100%; font-size: 13px; margin-top: 0.5rem;">
                <tr>
                    <td><strong>Total neto de la venta:</strong></td>
                    <td>${{ number_format($total, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td><strong>Anticipo:</strong></td>
                    <td>${{ number_format($montoInicial, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td><strong>Monto financiado (sin intereses):</strong></td>
                    <td>${{ number_format($montoFinanciadoBase, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td><strong>Plazo:</strong></td>
                    <td>{{ $plazoMeses }} {{ \Illuminate\Support\Str::plural('meses', $plazoMeses) }}</td>
                </tr>

                @if($venta->plan === 'credito')
                    <tr>
                        <td><strong>Tasa de interés mensual:</strong></td>
                        <td>5%</td>
                    </tr>
                    <tr>
                        <td><strong>Total a pagar con intereses:</strong></td>
                        <td>
                            <span style="color:#1e73be; font-weight:bold;">
                                ${{ number_format($montoConIntereses, 2, '.', ',') }}
                            </span>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    @endif
</div>

{{-- ======================================================
     TÉRMINOS Y CONDICIONES
     ====================================================== --}}
<div style="margin-bottom: 2rem;">
    <h3 style="color:#1e73be; font-weight:bold; border-bottom:2px solid #1e73be; padding-bottom:4px;">
        Términos y Condiciones
    </h3>

    @php $mesesGarantia = $venta->meses_garantia ?? 6; @endphp

    @if($venta->plan === 'contado')
        <ul style="font-size: 13px; line-height: 1.6;">
            <li>Este pago es único y debe realizarse al momento de la entrega o en la fecha acordada.</li>
            <li>El equipo será propiedad del cliente una vez confirmado el pago total.</li>
            <li>La garantía del equipo es de {{ $mesesGarantia }} meses a partir de la fecha de entrega.</li>
            <li>Los productos están sujetos a disponibilidad. Precios sujetos a cambio sin previo aviso.</li>
        </ul>
    @else
        <ul style="font-size: 13px; line-height: 1.6;">
            <li>Todos los pagos deberán realizarse puntualmente según el calendario acordado.</li>
            <li>En caso de retraso en el pago, se aplicará un cargo moratorio del 5% mensual sobre el monto vencido.</li>
            <li>El equipo permanecerá como propiedad de <strong>Grupo MediBuy</strong> hasta la liquidación total del pago.</li>
            <li>La garantía del equipo es de {{ $mesesGarantia }} meses a partir de la fecha de entrega.</li>
            <li>Los precios pueden cambiar sin previo aviso. Los productos están sujetos a disponibilidad.</li>
            <li>Cualquier ajuste a las condiciones de pago deberá ser autorizado por escrito por la empresa.</li>
        </ul>
    @endif
</div>

{{-- ======================================================
     DATOS BANCARIOS
     ====================================================== --}}
<div style="margin-bottom: 2rem;">
    <h3 style="color:#1e73be; font-weight:bold; border-bottom:2px solid #1e73be; padding-bottom:4px;">
        Datos Bancarios para Transferencia
    </h3>

    @if (empty($venta->iva) || $venta->iva == 0 || $venta->iva == 0.0)
        <table style="width: 100%; font-size: 13px; margin-bottom: 1rem;">
            <tr><td><strong>Banco:</strong> </td><td>Santander</td></tr>
            <tr><td><strong>Nombre del beneficiario:</strong></td><td>Gabriela Díaz García</td></tr>
            <tr><td><strong>CLABE:</strong></td><td>014 4206 0614 8217 181</td></tr>
            <tr><td><strong>No. de Tarjeta:</strong></td><td>5579 0701 2907 7528</td></tr>
            <tr><td><strong>Concepto:</strong></td>
                <td>Remisión 2026-{{ $venta->id }} - {{ mb_strtoupper($venta->cliente->nombre) }}</td>
            </tr>
        </table>

        <table style="width: 100%; font-size: 13px;">
            <tr><td><strong>Banco:</strong></td> <td>Banamex</td></tr>
            <tr><td><strong>Nombre del beneficiario:</strong></td><td>Gabriela Díaz García</td></tr>
            <tr><td><strong>CLABE:</strong></td><td>002 4209 0432 584 1851</td></tr>
            <tr><td><strong>No. de Tarjeta:</strong></td><td>5256 7861 2056 8690</td></tr>
            <tr><td><strong>Concepto:</strong></td>
                <td>Remisión 2026-{{ $venta->id }} - {{ mb_strtoupper($venta->cliente->nombre) }}</td>
            </tr>
        </table>
    @elseif ($venta->iva > 1)
        <table style="width: 100%; font-size: 13px;">
            <tr><td><strong>Banco:</strong></td> <td>Bancomer</td></tr>
            <tr><td><strong>Nombre del beneficiario:</strong></td><td>Anahí Téllez Ortiz</td></tr>
            <tr><td><strong>Cuenta:</strong></td><td>29 44 26 60 64</td></tr>
            <tr><td><strong>CLABE:</strong></td><td>0121 800 2944 2660 641</td></tr>
            <tr><td><strong>No. de Tarjeta:</strong></td><td>4152 3145 7282 6959</td></tr>
            <tr><td><strong>Concepto:</strong></td>
                <td>Remisión 2026-{{ $venta->id }} - {{ mb_strtoupper($venta->cliente->nombre) }}</td>
            </tr>
        </table>
    @endif

    <p style="margin-top: 0.5rem; font-size: 12px;">
        Por favor, envíe el comprobante de pago al correo: <strong>compras@grupomedibuy.com</strong>
        o vía WhatsApp al <strong>+52 722 448 5191</strong>.
    </p>
</div>

@if($venta->plan !== 'contado')
<div style="page-break-before: always; font-family: Arial, sans-serif; font-size: 13px; color: #333; line-height: 1.6;">
    <h2 style="text-align:center; color:#1e73be; font-weight:bold; margin-bottom:1.5rem;">Contrato de Compra-Venta</h2>

    <p><strong>CONTRATO DE COMPRA-VENTA</strong> que celebran por una parte <strong>ANAHÍ TELLEZ ORTIZ</strong>, en su carácter de vendedora, y por la otra <strong>{{ mb_strtoupper($venta->cliente->nombre . ' ' . $venta->cliente->apellido, 'UTF-8') }}</strong>, en su carácter de comprador, al tenor de las siguientes cláusulas:</p>

    <h4 style="color:#1e73be; margin-top: 1.5rem;">PRIMERA. OBJETO</h4>
    <p>La vendedora se obliga a transferir al comprador la propiedad de los equipos detallados en la remisión número <strong>2026-{{ $venta->id }}</strong>, y este se obliga a pagar el precio convenido.</p>

    <h4 style="color:#1e73be; margin-top: 1rem;">SEGUNDA. PRECIO Y FORMA DE PAGO</h4>
    <p>El precio total de la compraventa es de <strong>${{ number_format($totalNeto, 2) }} MXN</strong>.
    El comprador ha convenido en pagar dicha cantidad a crédito conforme al plan de pagos descrito en este documento.</p>

    <h4 style="color:#1e73be; margin-top: 1rem;">TERCERA. ENTREGA</h4>
    <p>La entrega de los equipos se hará en el domicilio del comprador o en el lugar designado, en perfectas condiciones de funcionamiento y acompañado de la garantía correspondiente.</p>

    @php $mesesGarantia = $venta->meses_garantia ?? 6; @endphp

    <h4 style="color:#1e73be; margin-top: 1rem;">CUARTA. GARANTÍA</h4>
    <p>La vendedora otorga una garantía de {{ $mesesGarantia }} meses sobre el funcionamiento de los equipos, contados a partir de la fecha de entrega.</p>

    <h4 style="color:#1e73be; margin-top: 1rem;">QUINTA. RESERVA DE DOMINIO</h4>
    <p>Hasta que no se liquide el total del precio convenido, los equipos seguirán siendo propiedad de la vendedora.</p>

    <h4 style="color:#1e73be; margin-top: 1rem;">SEXTA. JURISDICCIÓN</h4>
    <p>Para la interpretación y cumplimiento del presente contrato, las partes se someten a las leyes y tribunales del Estado de México, renunciando a cualquier otro fuero que pudiera corresponderles.</p>

    <div style="margin-top: 2rem;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; text-align: center;">
                    <p><strong>LA VENDEDORA</strong></p><br><br>
                    <img src="{{ public_path('images/firma.png') }}" alt="Firma" width="130"><br>
                    <strong>ANAHÍ TÉLLEZ ORTIZ</strong><br>
                    Gerente General
                </td>
                <td style="width: 50%; text-align: center;">
                    <p><strong>EL COMPRADOR</strong></p><br><br><br><br>
                    <strong>{{ mb_strtoupper($venta->cliente->nombre . ' ' . $venta->cliente->apellido, 'UTF-8') }}</strong><br>
                    Cliente
                </td>
            </tr>
        </table>
    </div>
</div>
@endif

</body>
</html>
