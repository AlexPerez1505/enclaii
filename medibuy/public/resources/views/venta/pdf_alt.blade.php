{{-- resources/views/venta/pdf_alt.blade.php --}}
@php
    // Paleta pastel profesional (segura para DomPDF)
    $ink      = '#1f2937';  // gris oscuro texto
    $muted    = '#667085';  // gris secundario
    $line     = '#e6edf8';  // borde suave
    $pastel   = '#eef5ff';  // azul pastel fondo
    $accent   = '#1a3aa9';  // azul acento

    // Normalizaciones para impresión
    $nombreCliente  = mb_strtoupper(trim(($venta->cliente->nombre ?? '').' '.($venta->cliente->apellido ?? '')), 'UTF-8');
    $nombreVendedor = mb_strtoupper($venta->usuario->name ?? 'VENDEDOR', 'UTF-8');
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Remisión No.2026-{{ $venta->id }}</title>
    <style>
        @page { margin: 14mm 14mm 16mm 14mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: {{ $ink }}; }

        /* Layout base por tablas (compatible DomPDF) */
        .w-100{ width:100%; }
        .mt-6{ margin-top: 6px; }
        .mt-10{ margin-top: 10px; }
        .p-10{ padding:10px; }
        .rounded{ border-radius: 12px; }
        .bg-pastel{ background: {{ $pastel }}; }
        .text-right{ text-align:right; }
        .text-center{ text-align:center; }
        .text-muted{ color: {{ $muted }}; }
        .title{ color: {{ $accent }}; font-weight: 800; }
        .pill{ display:inline-block; padding:4px 10px; border-radius:999px; background:#fff; border:1px solid {{ $line }}; font-weight:700; font-size:11px; color: {{ $accent }}; }
        .logo{ height: 46px; }

        table { border-collapse: collapse; }
        th, td { padding: 8px 10px; vertical-align: top; }
        .thead th{ font-size:11px; color:#5b6472; border-bottom:1px solid {{ $line }}; }
        .tbody td{ border-bottom:1px solid {{ $line }}; }
        .tfoot td{ font-weight:700; }

        .box { border:1px solid {{ $line }}; border-radius:12px; padding:10px; }
        .box h3 { margin:0 0 6px 0; font-size:12.5px; color: {{ $accent }}; }

        /* Nota conformidad */
        .nota{
            border-left:3px solid {{ $accent }}; background:#fafcff; padding:8px 10px;
            font-size:11px; color:#374151; line-height:1.45;
        }
    </style>
</head>
<body>

    {{-- ENCABEZADO: Logo izquierda + Remisión derecha --}}
    <table class="w-100 bg-pastel rounded p-10" style="border:1px solid {{ $line }};">
        <tr>
            <td style="width:50%;"><img src="{{ public_path('images/logomedy.png') }}" alt="Grupo MediBuy" class="logo"></td>
            <td style="width:50%;" class="text-right">
                <div class="title" style="font-size:18px; font-weight:900;">REMISIÓN</div>
                <div class="text-muted mt-6">Documento de entrega</div>
                <div class="mt-6"><strong>Remisión:</strong><br>
                    <span style="color:#d11a2a;font-weight:900;">No.2026-{{ $venta->id }}</span>
                </div>
            </td>
        </tr>
    </table>

    {{-- Fila: Cliente y Vendedor (misma fila) --}}
    <table class="w-100 mt-10">
        <tr>
            <td style="width:50%;">
                <div class="box">
                    <h3>Cliente</h3>
                    <div style="font-weight:800;">{{ $nombreCliente }}</div>
                    @if(!empty($venta->cliente->telefono))
                        <div class="text-muted">Tel. {{ $venta->cliente->telefono }}</div>
                    @endif
                    @if(!empty($venta->cliente->comentarios))
                        <div class="text-muted">{{ $venta->cliente->comentarios }}</div>
                    @endif
                </div>
            </td>
            <td style="width:50%;">
                <div class="box">
                    <h3>Vendedor</h3>
                    <div style="font-weight:800;">{{ $nombreVendedor }}</div>
                    @if(!empty($venta->usuario->phone))
                        <div class="text-muted">Tel. {{ $venta->usuario->phone }}</div>
                    @endif
                    @if(!empty($venta->usuario->email))
                        <div class="text-muted">{{ $venta->usuario->email }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- Venta: datos compactos --}}
    <table class="w-100 mt-10">
        <tr>
            <td>
                <div class="box">
                    <h3>Venta</h3>
                    <table>
                        <tr>
                            <td class="text-muted">Fecha:</td>
                            <td><strong>{{ $venta->created_at?->format('d/m/Y') }}</strong></td>
                        </tr>
                        @if(!empty($venta->lugar))
                        <tr>
                            <td class="text-muted">Lugar:</td>
                            <td><strong>{{ $venta->lugar }}</strong></td>
                        </tr>
                        @endif
                        @if(!empty($venta->plan))
                        <tr>
                            <td class="text-muted">Plan:</td>
                            <td><span class="pill">{{ strtoupper($venta->plan) }}</span></td>
                        </tr>
                        @endif
                        @if(($venta->meses_garantia ?? 0) > 0)
                        <tr>
                            <td class="text-muted">Garantía:</td>
                            <td><strong>{{ $venta->meses_garantia }} meses</strong></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- Conceptos (con Serie si existe) --}}
    <div class="box mt-10">
        <h3>Conceptos</h3>
        <table class="w-100">
            <thead class="thead">
                <tr>
                    <th>Producto</th>
                    <th style="width:80px;">Cant.</th>
                    <th style="width:120px;">P. Unit.</th>
                    <th style="width:120px;">Subtotal</th>
                </tr>
            </thead>
            <tbody class="tbody">
            @foreach($venta->productos as $vp)
                @php
                    $p = $vp->producto;
                    $nombreLargo = trim(($p->tipo_equipo ?? '').' '.($p->marca ?? '').' '.($p->modelo ?? ''));
                    // Serie: solo si existe relación registro y número
                    $serie = optional($vp->registro)->numero_serie;
                @endphp
                <tr>
                    <td>
                        <div>{{ $nombreLargo ?: '—' }}</div>
                        @if(!empty($serie))
                            <div class="text-muted" style="font-size:10.5px; margin-top:2px;">
                                Serie: <strong>{{ $serie }}</strong>
                            </div>
                        @endif
                    </td>
                    <td>{{ (int)$vp->cantidad }}</td>
                    <td>
                        @if(($vp->precio_unitario ?? 0) > 0)
                            ${{ number_format($vp->precio_unitario, 2) }}
                        @else — @endif
                    </td>
                    <td>${{ number_format($vp->subtotal, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot class="tfoot">
                <tr>
                    <td colspan="3" class="text-right">Subtotal</td>
                    <td>${{ number_format($venta->subtotal, 2) }}</td>
                </tr>
                @if(($venta->descuento ?? 0) > 0)
                <tr>
                    <td colspan="3" class="text-right">Descuento</td>
                    <td>- ${{ number_format($venta->descuento, 2) }}</td>
                </tr>
                @endif
                @if(($venta->envio ?? 0) > 0)
                <tr>
                    <td colspan="3" class="text-right">Envío</td>
                    <td>${{ number_format($venta->envio, 2) }}</td>
                </tr>
                @endif
                @if(($venta->iva ?? 0) > 0)
                <tr>
                    <td colspan="3" class="text-right">IVA</td>
                    <td>${{ number_format($venta->iva, 2) }}</td>
                </tr>
                @endif
                <tr>
                    <td colspan="3" class="text-right">Total</td>
                    <td>${{ number_format($venta->total, 2) }}</td>
                </tr>
               
            </tfoot>
        </table>
    </div>

    {{-- Nota de conformidad --}}
    <div class="nota mt-10">
        <strong>CONFORMIDAD DEL CLIENTE:</strong>
        Yo, <em>{{ $nombreCliente }}</em>, declaro haber recibido a satisfacción los productos y/o servicios descritos
        en este documento, completos y en buen estado de funcionamiento. Acepto los términos de garantía indicados
        y confirmo que la entrega se realizó sin faltantes.
    </div>

    {{-- FIRMAS EN LA MISMA FILA --}}
    <div class="mt-10">
        <table class="w-100">
            <tr>
                <td style="width:50%;" class="text-center">
                    <p><strong>VENDEDOR</strong></p>
                    <br><br><br><br>
                    <strong>{{ $nombreVendedor }}</strong><br>
                    Agente de Ventas
                </td>
                <td style="width:50%;" class="text-center">
                    <p><strong>COMPRADOR</strong></p>
                    <br><br><br><br>
                    <strong>{{ $nombreCliente }}</strong><br>
                    Cliente
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
