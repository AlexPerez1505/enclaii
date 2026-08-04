<!DOCTYPE html>  
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Remisión</title>

  <!-- Bootstrap + Iconos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <!-- Fuente Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('css/remisiones.css') }}?v={{ time() }}">

  <style>
    .pastel-progress-bar{height:100%;border-radius:18px;font-weight:bold;color:#555;display:flex;align-items:center;justify-content:center;transition:width .8s ease-in-out,background .4s ease-in-out}
    .table th,.table td{vertical-align:middle;padding:.75rem}
    .table-borderless th,.table-borderless td{border:none}
    .table thead{border-bottom:2px solid #eee}
    .card{background-color:#fefefe;border-radius:1rem}
    .card-header{background-color:transparent;border-bottom:none}

    :root{
      --line:#e6ecf4;
      --text:#0f172a;
      --bg:#ffffff;
      --muted:#64748b;

      --brand:#2563eb;
      --brand-50:#eff6ff;
      --brand-100:#bfdbfe;

      --success:#16a34a;
      --success-50:#ecfdf3;
      --success-100:#86efac;

      --danger:#dc2626;
      --danger-50:#fee2e2;
      --danger-100:#fecaca;
    }

    html,body{
      height:100%;
      font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont,
                   "SF Pro Text", "Segoe UI", "Roboto", "Helvetica Neue", sans-serif;
    }

    body{
      color:var(--text);
      background:
        radial-gradient(1200px 520px at 50% -10%, rgba(207,231,255,.95), transparent 60%),
        radial-gradient(900px 420px at 50% 110%, rgba(255,220,190,.65), transparent 65%),
        linear-gradient(180deg,#eef5ff 0%, #f6f7fb 58%, #fff3e6 100%);
      background-attachment: fixed;
      -webkit-font-smoothing: antialiased;
      text-rendering: geometricPrecision;
    }

    .page-header{
      display:flex; gap:16px; align-items:center; justify-content:space-between; flex-wrap:wrap;
    }

    .action-bar{
      display:flex;
      gap:10px;
      flex-wrap:wrap;
      align-items:center;
    }

    .btn-chip{
      --chip-h: 42px;

      display:inline-flex;
      align-items:center;
      justify-content:center;
      gap:.55rem;

      height:var(--chip-h);
      min-width:170px;
      padding:.62rem 1.05rem;

      border-radius:999px;
      border:1px solid rgba(15,23,42,.10);
      background:rgba(255,255,255,.92);
      color:#0f172a;

      font-weight:600;
      font-size:14px;
      line-height:1;
      text-decoration:none;
      cursor:pointer;
      white-space:nowrap;

      box-shadow:0 6px 18px rgba(15,23,42,.06);
      backdrop-filter: blur(6px);
      transition:
        background .18s ease,
        border-color .18s ease,
        box-shadow .18s ease,
        transform .08s ease,
        color .18s ease;
      flex:0 0 auto;
    }
    .btn-chip i{font-size:15px}
    .btn-chip:hover{
      background:rgba(255,255,255,1);
      border-color: rgba(15,23,42,.18);
      box-shadow:0 10px 24px rgba(15,23,42,.10);
      transform: translateY(-1px);
    }
    .btn-chip:active{ transform: translateY(0) }
    .btn-chip:focus-visible{
      outline:2px solid var(--brand-100);
      outline-offset:2px;
    }

    .btn-chip.-danger{
      background: rgba(254,226,226,.70);
      border-color: var(--danger-100);
      color:#991b1b;
    }
    .btn-chip.-danger:hover{
      background: rgba(254,226,226,1);
      border-color: #fca5a5;
    }

    .btn-chip.-success{
      background: rgba(220,252,231,.65);
      border-color: var(--success-100);
      color:#15803d;
    }
    .btn-chip.-success:hover{
      background: rgba(220,252,231,1);
      border-color:#4ade80;
    }

    .btn-chip.-primary{
      background: rgba(219,234,254,.70);
      border-color: var(--brand-100);
      color:#1d4ed8;
    }
    .btn-chip.-primary:hover{
      background: rgba(219,234,254,1);
      border-color:#93c5fd;
    }

    .btn-chip.-ghost{
      background: rgba(255,255,255,.92);
      border-color: rgba(15,23,42,.10);
      color:#0f172a;
    }
    .btn-chip.-ghost:hover{
      background:#fff;
    }

    .btn-chip .spinner{
      width:16px; height:16px; border:2px solid currentColor; border-right-color:transparent;
      border-radius:50%; display:none; animation: spin .6s linear infinite;
    }
    .btn-chip.is-loading .label{ visibility:hidden }
    .btn-chip.is-loading .spinner{ display:inline-block }
    @keyframes spin{ to{ transform:rotate(360deg) } }

    @media (max-width: 576px){
      .action-bar{
        width:100%;
        display:grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap:8px;
        align-items:stretch;
      }
      .btn-chip{
        width:100%;
        min-width:0;
        height:38px;
        font-size:13px;
        padding:.5rem .7rem;
        box-shadow:0 4px 12px rgba(15,23,42,.06);
      }
      .btn-chip i{font-size:14px}
    }
    @media (max-width: 360px){
      .action-bar{ grid-template-columns: 1fr; }
    }

    .fin-card{
      border:1px solid #e3e9f5;border-radius:20px;
      box-shadow:0 14px 35px rgba(15,23,42,.10);
      background:rgba(255,255,255,.96);
      backdrop-filter: blur(8px);
    }
    .fin-head{
      display:flex;align-items:center;justify-content:space-between;gap:12px;
      padding:16px 18px;border-bottom:1px dashed #e5e9f3;background:transparent;
      border-radius:20px 20px 0 0
    }
    .fin-title{display:flex;align-items:center;gap:12px}
    .fin-icon{
      width:42px;height:42px;border-radius:14px;display:grid;place-items:center;
      background:linear-gradient(135deg,#f3f6ff,#e0f2ff);
      color:#1e3a8a;font-size:18px; flex:0 0 auto;
    }
    .fin-sub{font-size:12px;color:#64748b;margin-top:-2px}
    .fin-chip{
      display:inline-flex;align-items:center;gap:6px;padding:7px 11px;border-radius:999px;
      font-weight:700;font-size:11px;letter-spacing:.3px;text-transform:uppercase;
      border:1px solid transparent; white-space:nowrap;
    }
    .fin-chip.ok{background:#ecfdf3;color:#047857;border-color:#bbf7d0}
    .fin-chip.warn{background:#fff7ed;color:#9a3412;border-color:#fed7aa}
    .fin-chip.neutral{background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe}

    .fin-metrics{
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(160px,1fr));
      gap:10px;padding:12px 16px;
    }
    .fin-metric{
      background:#f8fafc;border:1px solid #e9eef6;border-radius:14px;padding:12px;
      display:flex;flex-direction:column;gap:4px;min-height:80px; min-width:0;
    }
    .fin-metric .label{
      font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.4px
    }
    .fin-metric .value{
      font-size:18px;font-weight:700;color:#0f172a;
      white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    }
    .fin-metric .hint{font-size:11px;color:#94a3b8}

    .fin-section{padding:10px 16px 16px}
    .fin-section-card{
      background:#f9fafb;border:1px solid #e9eef6;border-radius:16px;padding:12px
    }
    .fin-sec-head{
      display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:8px; flex-wrap:wrap;
    }
    .fin-sec-left{display:flex;align-items:center;gap:8px}
    .fin-sec-badge{
      width:34px;height:34px;border-radius:10px;display:grid;place-items:center;
      background:#ecfeff;color:#0f766e;font-size:16px
    }

    .fin-table{width:100%;font-size:13px;background:#fff;border-radius:12px;overflow:hidden}
    .fin-table th{
      background:#f1f5f9;color:#0f172a;font-weight:700;text-transform:uppercase;font-size:11px;letter-spacing:.5px
    }
    .fin-table td,.fin-table th{padding:9px 10px;border-bottom:1px solid #eef2f7}
    .fin-table tr:last-child td{border-bottom:none}
    .fin-money{font-weight:700}
    .fin-money.neg{color:#b91c1c}

    .fin-progress-wrap{margin-top:12px}
    .fin-progress{
      height:26px;border-radius:999px;background:#f1f5f9;position:relative;overflow:hidden;border:1px solid #e9eef6
    }
    .fin-progress-bar{
      height:100%;border-radius:999px;font-weight:700;color:#0f172a;display:flex;align-items:center;justify-content:center;
      transition:width .8s ease-in-out,background .4s ease-in-out;font-size:12px
    }

    .fin-mini-list .item{
      display:flex;align-items:center;justify-content:space-between;
      padding:8px 10px;border:1px dashed #e9eef6;border-radius:10px;background:#fff;margin-top:6px
    }
    .fin-mini-list .left{display:flex;align-items:center;gap:8px}
    .fin-mini-dot{
      width:10px;height:10px;border-radius:999px;background:#22c55e;box-shadow:0 0 0 3px #dcfce7
    }
    .fin-mini-dot.warn{background:#f59e0b;box-shadow:0 0 0 3px #fef3c7}
    .fin-mini-title{font-weight:600;font-size:13px;color:#0f172a}
    .fin-mini-sub{font-size:12px;color:#64748b}

    .fin-kv{
      display:grid; grid-template-columns: 1fr 1fr; gap:8px 12px; font-size:14px;
    }
    .fin-kv .k{color:#64748b; font-weight:600;}
    .fin-kv .v{font-weight:500; color:#0f172a;}
    @media (max-width: 576px){
      .fin-kv{ grid-template-columns: 1fr; }
    }
  </style>
</head>

<body>
  <div class="header-container">
    @auth
      <div class="back-button">
        <button onclick="window.history.back()" class="menu-icon" aria-label="Regresar">
          <img src="{{ asset('images/atras.png') }}" alt="Regresar">
        </button>
      </div>
    @endauth
    <h1 class="titulos">Remisión</h1>
    <div class="gradient-bg-animation"></div>
  </div>

  <div class="container mt-5">

    {{-- ===================== CÁLCULOS GLOBALES ===================== --}}
    @php
      // Total "lista" de la venta
      $totalOriginal = (float)($venta->total_original ?? $venta->total ?? 0);

      // Trade-in (equipos a cuenta)
      $tradeins     = $venta->tradeins ?? collect();
      $tradeinTotal = (float)($venta->tradein_total ?? $tradeins->sum('valor_a_cuenta') ?? 0);

      // Plan de pagos (PagosFinanciamiento) que llega a la vista como $pagos
      $pagosPlan = collect($pagos ?? []);

      // Pagos reales registrados en tabla pagos
      $pagosReales          = $venta->pagosReales ?? collect();
      $pagosRealesAprobados = $pagosReales->where('aprobado', true);

      // ANTICIPO (pagos reales donde es_anticipo = 1 y APROBADO)
      $pagosAnticipoAprobados = $pagosRealesAprobados->filter(fn($p) => ($p->es_anticipo ?? false));
      $anticipoPagado         = (float)$pagosAnticipoAprobados->sum('monto');

      // BASE NETA DESPUÉS DE TRADE-IN
      $baseNeta = max($totalOriginal - $tradeinTotal, 0);

      // MONTO FINANCIADO / CONTRATO = base - anticipo pagado
      $totalContrato = max($baseNeta - $anticipoPagado, 0);

      // PAGOS DEL PLAN APROBADOS (cuotas de PagosFinanciamiento)
      $pagosPlanAprobados = $pagosPlan->filter(function($pf){
          return $pf->pago
              && $pf->pago->aprobado
              && !($pf->pago->es_anticipo ?? false); // por si algún pago de plan se marcó como anticipo
      });
      $pagadoContrato = (float)$pagosPlanAprobados->sum('monto');

      // SALDO = monto financiado - pagos del plan aprobados
      $saldoContrato = max($totalContrato - $pagadoContrato, 0);

      // PARA BARRA DE PROGRESO: usamos el monto financiado como base
      $baseProgreso = $totalContrato > 0 ? $totalContrato : $baseNeta;
      $porcentajePagado = $baseProgreso > 0
          ? min(100, round(($pagadoContrato / $baseProgreso) * 100, 2))
          : 0;

      $estaLiquidada = $saldoContrato <= 0.01;

      if ($porcentajePagado < 25)        $frase="¡Empezar es ganar, aunque sea poco!";
      elseif ($porcentajePagado < 50)    $frase="Ya calentaste motores, sigue rodando.";
      elseif ($porcentajePagado < 75)    $frase="Casi en la cima, no mires para atrás.";
      elseif ($porcentajePagado < 100)   $frase="¡La meta se asoma, dale con todo!";
      else                               $frase="¡Listo! Has desbloqueado el modo campeón.";

      $tooltipText = "Has pagado $".number_format($pagadoContrato,2)." de $".number_format($baseProgreso,2).". ".$frase;

      // Total plan programado (suma de pagos_financiamiento)
      $montoFinanciadoPlan = (float)$pagosPlan->sum('monto');
    @endphp

    {{-- Mensajes de resultado WhatsApp --}}
    @if(session('wa_success'))
      <div class="alert alert-success border-0" style="border-radius:12px">{{ session('wa_success') }}</div>
    @endif
    @if(session('wa_info'))
      <div class="alert alert-warning border-0" style="border-radius:12px">
        {{ session('wa_info') }}
        @if(session('wa_fail'))
          <details class="mt-2">
            <summary>Ver detalles</summary>
            <pre style="white-space:pre-wrap;background:#f8fafc;border:1px dashed #e6ecf4;border-radius:10px;padding:10px;font-size:12px">{{ json_encode(session('wa_fail'), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
          </details>
        @endif
      </div>
    @endif

    <!-- ====== Header con acciones ====== -->
    <div class="page-header mb-4">
      <h2 class="mb-0">Resumen de Venta N.2025-{{ $venta->id }}</h2>

      <div class="action-bar" role="toolbar" aria-label="Acciones de la remisión">
        <!-- PDF (ORIGINAL) -->
        <button
          type="button"
          class="btn-chip -danger pdf-btn"
          data-url="{{ route('ventas.pdf', $venta->id) }}"
          data-filename="Remision_{{ $venta->id }}_{{ preg_replace('/[^A-Za-z0-9 _-]/','', $venta->cliente->nombre ?? 'Cliente') }}.pdf"
          title="Descargar PDF">
          <i class="fa-solid fa-file-pdf"></i>
          <span class="label">Descargar PDF</span>
          <span class="spinner" aria-hidden="true"></span>
        </button>

        <!-- PDF (ALTERNO) -->
        <button
          type="button"
          class="btn-chip -danger pdf-btn"
          data-url="{{ route('ventas.pdf.alt', $venta->id) }}"
          data-filename="Remision_Alt_{{ $venta->id }}_{{ preg_replace('/[^A-Za-z0-9 _-]/','', $venta->cliente->nombre ?? 'Cliente') }}.pdf"
          title="Descargar PDF alterno">
          <i class="fa-regular fa-file-pdf"></i>
          <span class="label">PDF alterno</span>
          <span class="spinner" aria-hidden="true"></span>
        </button>

        @auth
        <!-- WhatsApp -->
        <form method="POST" action="{{ route('ventas.whatsapp.plantilla', $venta) }}" id="form-wa" class="m-0 d-inline">
          @csrf
          <input type="hidden" name="template_name" value="doc_pdf_utility_v2">
          <input type="hidden" name="template_lang"  value="es_MX">
          <button type="submit" id="btn-wa" class="btn-chip -success" title="Enviar por WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
            <span class="label">Enviar por WhatsApp</span>
            <span class="spinner" aria-hidden="true"></span>
          </button>
        </form>

        <!-- Etiqueta 4×8 -->
        <a href="{{ route('ventas.etiqueta', $venta->id) }}"
           class="btn-chip -ghost"
           target="_blank" rel="noopener" title="Imprimir etiqueta 4×8 con QR">
          <i class="fa-solid fa-tag"></i>
          <span class="label">Etiqueta 4×8 (QR)</span>
          <span class="spinner" aria-hidden="true"></span>
        </a>

        <!-- Checklist -->
        <a href="{{ route('checklists.wizard', $venta->id) }}"
           class="btn-chip -primary"
           target="_blank" rel="noopener" title="Abrir checklist">
          <i class="fa-solid fa-clipboard-check"></i>
          <span class="label">Abrir checklist</span>
          <span class="spinner" aria-hidden="true"></span>
        </a>
        @endauth
      </div>
    </div>

    {{-- ====== CONTENIDO ====== --}}
    <div class="row g-4">
      {{-- DATOS DE LA VENTA --}}
      <div class="col-md-6 d-flex">
        <div class="fin-card w-100 border-0">
          <div class="fin-head">
            <div class="fin-title">
              <div class="fin-icon"><i class="fa-solid fa-receipt"></i></div>
              <div>
                <div class="fw-bold">Datos de la venta</div>
                <div class="fin-sub">Información general</div>
              </div>
            </div>
            <span class="fin-chip neutral">{{ mb_strtoupper($venta->plan ?? 'N/A','UTF-8') }}</span>
          </div>

          <div class="fin-section">
            <div class="fin-section-card">
              <div class="fin-kv">
                <div class="k">Cliente</div>
                <div class="v">{{ mb_strtoupper($venta->cliente->nombre . ' ' . $venta->cliente->apellido, 'UTF-8') }}</div>

                <div class="k">Teléfono cliente</div>
                <div class="v">{{ mb_strtoupper($venta->cliente->telefono, 'UTF-8') }}</div>

                <div class="k">Dirección</div>
                <div class="v">{{ mb_strtoupper($venta->cliente->comentarios, 'UTF-8') }}</div>

                <div class="k">Asesor</div>
                <div class="v">{{ mb_strtoupper($venta->usuario->name, 'UTF-8') }}</div>

                <div class="k">Teléfono asesor</div>
                <div class="v">{{ $venta->usuario->phone }}</div>

                <div class="k">Fecha</div>
                <div class="v">{{ $venta->created_at->format('d/m/Y H:i') }}</div>

                <div class="k">Meses garantía</div>
                <div class="v">{{ $venta->meses_garantia ? $venta->meses_garantia . ' meses' : 'N/A' }}</div>

                <div class="k">Nota</div>
                <div class="v">{{ $venta->nota ? mb_strtoupper($venta->nota, 'UTF-8') : 'N/A' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- PRODUCTOS + RESUMEN DE MONTOS --}}
      <div class="col-md-6 d-flex">
        <div class="fin-card w-100 border-0">
          <div class="fin-head">
            <div class="fin-title">
              <div class="fin-icon"><i class="fa-solid fa-cart-shopping"></i></div>
              <div>
                <div class="fw-bold">Productos seleccionados</div>
                <div class="fin-sub">Detalle de la compra</div>
              </div>
            </div>
            <span class="fin-chip neutral">{{ $venta->productos->count() }} items</span>
          </div>

          <div class="fin-section">
            <div class="fin-section-card">
              <div class="table-responsive">
                <table class="table fin-table mb-2">
                  <thead>
                    <tr>
                      <th style="width:60px">Equipo</th>
                      <th>Descripción</th>
                      <th class="text-center" style="width:90px">Cantidad</th>
                      <th class="text-end" style="width:120px">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($venta->productos as $item)
                      <tr>
                        <td>
                          <img src="{{ $item->producto ? asset('storage/' . $item->producto->imagen) : asset('images/imagen-no-disponible.png') }}"
                               alt="{{ $item->producto->nombre ?? 'Producto eliminado' }}"
                               class="rounded shadow-sm"
                               style="width:48px;height:48px;object-fit:cover;">
                        </td>
                        <td>
                          @if ($item->producto)
                            <span class="fw-semibold d-block">{{ mb_strtoupper($item->producto->tipo_equipo ?? '—', 'UTF-8') }}</span>
                            <small class="text-muted d-block">
                              {{ mb_strtoupper($item->producto->modelo ?? '', 'UTF-8') }} |
                              {{ mb_strtoupper($item->producto->marca ?? '', 'UTF-8') }}
                            </small>
                            @if ($item->registro)
                              <small class="text-info-emphasis d-block mt-1">Serie: {{ $item->registro->numero_serie }}</small>
                            @endif
                          @else
                            <span class="text-danger fst-italic">Producto eliminado</span>
                          @endif
                        </td>
                        <td class="text-center">{{ $item->cantidad }}</td>
                        <td class="text-end fin-money">${{ number_format($item->subtotal, 2) }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>

                <div class="d-flex flex-column gap-1 text-end small mt-2">
                  <div><strong>Subtotal:</strong> ${{ number_format($venta->subtotal, 2) }}</div>
                  @if($venta->descuento > 0)
                    <div><strong>Descuento:</strong> <span class="text-warning">${{ number_format($venta->descuento, 2) }}</span></div>
                  @endif
                  @if($venta->envio > 0)
                    <div><strong>Envío:</strong> ${{ number_format($venta->envio, 2) }}</div>
                  @endif
                  <div><strong>IVA:</strong> ${{ number_format($venta->iva, 2) }}</div>

                  <div><strong>Total original:</strong> ${{ number_format($totalOriginal, 2) }}</div>

                  @if($tradeinTotal > 0)
                    <div>
                      <strong>Trade-in (equipo a cuenta):</strong>
                      <span class="text-danger">- ${{ number_format($tradeinTotal, 2) }}</span>
                    </div>
                  @endif

                  @if($anticipoPagado > 0)
                    <div>
                      <strong>Anticipo pagado:</strong>
                      <span class="text-danger">- ${{ number_format($anticipoPagado, 2) }}</span>
                    </div>
                  @endif

                  <div>
                    <strong>Monto financiado (plan / contrato):</strong>
                    ${{ number_format($totalContrato, 2) }}
                  </div>

                  <div class="fw-bold fs-5 mt-2 text-success-emphasis">
                    <strong>Saldo pendiente del contrato:</strong>
                    ${{ number_format($saldoContrato, 2) }}
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <br>

    <div class="row g-4">

 {{-- ====== RESUMEN FINANCIERO ====== --}}
      <div class="col-md-6 d-flex">
        <div class="fin-card w-100 border-0">
          @php
            $tradeins = $venta->tradeins ?? collect();
            $totalOriginal = (float)($venta->total_original ?? $venta->total ?? 0);
            $tradeinTotal  = (float)($venta->tradein_total ?? $tradeins->sum('valor_a_cuenta') ?? 0);
            $totalNeto     = (float)($venta->total_neto ?? max($totalOriginal - $tradeinTotal, 0));

            $pagosAprobados = $venta->pagosReales->filter(fn($p)=>$p->aprobado);
            $totalPagado    = (float)$pagosAprobados->sum('monto');

            // AHORA EL SALDO SE CALCULA CONTRA EL TOTAL ORIGINAL (POR EJ. 2,000)
            $baseProgreso   = $totalOriginal;
            $saldoRestante  = max($baseProgreso - $totalPagado, 0);
            $porcentajePagado = $baseProgreso > 0
                ? min(100, round(($totalPagado / $baseProgreso) * 100, 2))
                : 0;

            $estaLiquidada = $saldoRestante <= 0.01;

            if ($porcentajePagado < 25)        $frase="¡Empezar es ganar, aunque sea poco!";
            elseif ($porcentajePagado < 50)    $frase="Ya calentaste motores, sigue rodando.";
            elseif ($porcentajePagado < 75)    $frase="Casi en la cima, no mires para atrás.";
            elseif ($porcentajePagado < 100)   $frase="¡La meta se asoma, dale con todo!";
            else                               $frase="¡Listo! Has desbloqueado el modo campeón.";

            $tooltipText = "Has pagado $".number_format($totalPagado,2)." de $".number_format($baseProgreso,2).". ".$frase;
          @endphp

          <div class="fin-head">
            <div class="fin-title">
              <div class="fin-icon"><i class="fa-solid fa-chart-line"></i></div>
              <div>
                <div class="fw-bold">Resumen financiero</div>
                <div class="fin-sub">{{ mb_strtoupper($venta->cliente->nombre.' '.$venta->cliente->apellido,'UTF-8') }}</div>
              </div>
            </div>

            <div class="d-flex gap-2 align-items-center">
              <span class="fin-chip neutral">
                {{ mb_strtoupper($venta->plan ?? 'N/A','UTF-8') }}
              </span>

              @if($estaLiquidada)
                <span class="fin-chip ok"><i class="fa-solid fa-circle-check"></i> Pagado</span>
              @else
                <span class="fin-chip warn"><i class="fa-solid fa-clock"></i> Pendiente</span>
              @endif
            </div>
          </div>

          <div class="fin-metrics">
            <div class="fin-metric">
              <div class="label">Total neto</div>
              <div class="value">${{ number_format($totalNeto,2) }}</div>
              <div class="hint">Después de trade-in</div>
            </div>

            <div class="fin-metric" style="background:#fff;border-style:dashed;">
              <div class="label">Pagado</div>
              <div class="value">${{ number_format($totalPagado,2) }}</div>
              <div class="hint">Pagos aprobados</div>
            </div>

            <div class="fin-metric" style="background:#fefce8;">
              <div class="label">Saldo restante</div>
              <div class="value">${{ number_format($saldoRestante,2) }}</div>
              <div class="hint">{{ $estaLiquidada ? 'Liquidado' : 'Pendiente' }}</div>
            </div>
          </div>

          <div class="fin-section">
            <div class="fin-section-card">
              <div class="fin-sec-head">
                <div class="fin-sec-left">
                  <div class="fin-sec-badge"><i class="fa-solid fa-rotate"></i></div>
                  <div class="fw-bold">Equipo a cuenta (Trade-in)</div>
                </div>

                <div class="text-end">
                  <div class="small text-muted">Total trade-in</div>
                  <div class="fin-money neg">- ${{ number_format($tradeinTotal,2) }}</div>
                </div>
              </div>

              @if($tradeins->count() > 0)
                <div class="table-responsive">
                  <table class="table fin-table mb-2">
                    <thead>
                      <tr>
                        <th>Equipo</th>
                        <th>Marca / Modelo</th>
                        <th>Serie</th>
                        <th class="text-end">Valor</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($tradeins as $ti)
                        <tr>
                          <td class="fw-semibold">{{ mb_strtoupper($ti->tipo_equipo ?? '—','UTF-8') }}</td>
                          <td>
                            {{ mb_strtoupper($ti->marca ?? '—','UTF-8') }}
                            / {{ mb_strtoupper($ti->modelo ?? '—','UTF-8') }}
                          </td>
                          <td>{{ mb_strtoupper($ti->numero_serie ?? '—','UTF-8') }}</td>
                          <td class="text-end fin-money">${{ number_format($ti->valor_a_cuenta ?? 0,2) }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="text-muted small fst-italic px-1">
                  No se registraron equipos a cuenta en esta venta.
                </div>
              @endif

              <div class="d-flex justify-content-end gap-3 small mt-2 flex-wrap">
                <div><strong>Original:</strong> ${{ number_format($totalOriginal,2) }}</div>
                <div><strong>Neto:</strong> ${{ number_format($totalNeto,2) }}</div>
              </div>
            </div>

            <div class="fin-progress-wrap">
              <label class="form-label fw-semibold mb-2 text-secondary-emphasis">
                Progreso del pago: <span id="porcentajeContador" class="fw-bold">{{ $porcentajePagado }}%</span>
              </label>

              <div class="fin-progress position-relative shadow" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $tooltipText }}">
                <div
                  class="fin-progress-bar"
                  id="barraProgreso"
                  data-porcentaje="{{ $porcentajePagado }}"
                  aria-valuenow="{{ $porcentajePagado }}" aria-valuemin="0" aria-valuemax="100"
                  style="
                    width:0%;
                    background:
                      {{ $porcentajePagado < 50
                          ? 'linear-gradient(90deg,#fecaca,#fda4af)'
                          : ($porcentajePagado < 100
                              ? 'linear-gradient(90deg,#fde68a,#fbbf24)'
                              : 'linear-gradient(90deg,#bbf7d0,#34d399)') }};
                  "
                >
                  {{ $porcentajePagado }}%
                </div>
              </div>
            </div>

            @if(strtolower($venta->plan ?? '') === 'contado' && !$estaLiquidada)
              <div class="alert alert-warning border-0 mt-3 py-2 px-3" style="border-radius:12px;">
                <i class="fa-solid fa-circle-exclamation me-1"></i>
                Venta de contado con pago pendiente. 
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- ====== PLAN DE PAGOS ====== --}}
      <div class="col-md-6 d-flex">
        <div class="fin-card w-100 border-0">

          @php
            $hayPagoContado = $venta->plan === 'contado' && $pagosRealesAprobados->count() > 0;
            $mostrarColumnaDocumento = $pagosPlan->contains(fn($p)=>$p->documentos && $p->documentos->isNotEmpty());
            $hayFilasPlan = $pagosPlan->count() > 0
                            || $pagosAnticipoAprobados->isNotEmpty()
                            || $tradeins->isNotEmpty();
          @endphp

          <div class="fin-head">
            <div class="fin-title">
              <div class="fin-icon"><i class="fa-solid fa-calendar-check"></i></div>
              <div>
                <div class="fw-bold">Plan de pagos</div>
                <div class="fin-sub">
                  {{ mb_strtoupper($venta->cliente->nombre.' '.$venta->cliente->apellido,'UTF-8') }}
                </div>
              </div>
            </div>

            <div class="d-flex gap-2 align-items-center">
              <span class="fin-chip neutral">
                {{ mb_strtoupper($venta->plan ?? 'N/A','UTF-8') }}
              </span>

              @if( ($venta->plan === 'contado'  && $hayPagoContado)
                || ($venta->plan !== 'contado' && $pagosPlan->every(fn($p)=>$p->pago && $p->pago->aprobado)) )
                <span class="fin-chip ok"><i class="fa-solid fa-circle-check"></i> Pagado</span>
              @else
                <span class="fin-chip warn"><i class="fa-solid fa-clock"></i> Pendiente</span>
              @endif
            </div>
          </div>

          <div class="fin-section">
            @if ($venta->plan === 'contado')

              {{-- ================= CONTADO ================= --}}
              <div class="fin-section-card">
                <div class="fin-sec-head">
                  <div class="fin-sec-left">
                    <div class="fin-sec-badge" style="background:#ecfdf3;color:#047857">
                      <i class="fa-solid {{ $hayPagoContado ? 'fa-circle-check' : 'fa-clock' }}"></i>
                    </div>
                    <div class="fw-bold">
                      {{ $hayPagoContado ? 'Pago registrado correctamente' : 'Pago pendiente' }}
                    </div>
                  </div>
                  <div class="text-end small text-muted">
                    Contado
                  </div>
                </div>

                @if($hayPagoContado)
                  <div class="text-muted small">
                    Gracias por su compra. El pago se realizó en una o varias exhibiciones.
                  </div>

                  <div class="fin-mini-list mt-2">
                    @foreach($pagosRealesAprobados as $pagoReal)
                      @php
                        $esAnticipoReal = (bool)($pagoReal->es_anticipo ?? false);
                      @endphp
                      <div class="item">
                        <div class="left">
                          <span class="fin-mini-dot"></span>
                          <div>
                            <div class="fin-mini-title">
                              Pago aprobado
                              @if($esAnticipoReal)
                                <span class="badge bg-info-subtle text-info-emphasis ms-1">Anticipo</span>
                              @endif
                            </div>
                            <div class="fin-mini-sub">
                              {{ \Carbon\Carbon::parse($pagoReal->created_at)->format('d/m/Y H:i') }}
                            </div>
                          </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                          <div class="fw-bold">
                            ${{ number_format($pagoReal->monto,2) }}
                          </div>
                          <a href="{{ route('pagos.recibo', $pagoReal->id) }}"
                             target="_blank"
                             class="btn btn-sm btn-outline-primary">
                            Ver recibo
                          </a>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @else
                  <div class="fin-mini-list mt-2">
                    <div class="item">
                      <div class="left">
                        <span class="fin-mini-dot warn"></span>
                        <div>
                          <div class="fin-mini-title">Esperando pago</div>
                          <div class="fin-mini-sub">Sin registros aprobados</div>
                        </div>
                      </div>
                      <div class="fw-bold text-warning-emphasis">
                        $0.00
                      </div>
                    </div>
                  </div>
                @endif
              </div>

            @else

              {{-- ================= FINANCIAMIENTO / CRÉDITO ================= --}}

              {{-- Resumen del financiamiento --}}
              <div class="fin-section-card mb-3">
                <div class="fw-bold mb-2" style="color:#1e73be">Resumen del financiamiento</div>
                <table class="table table-sm mb-0">
                  <tr>
                    <td><strong>Total de la venta:</strong></td>
                    <td>${{ number_format($totalOriginal, 2, '.', ',') }}</td>
                  </tr>
                  
                 
                  <tr>
                    <td><strong>Monto financiado (contrato):</strong></td>
                    <td class="text-primary fw-bold">
                      ${{ number_format($totalContrato, 2, '.', ',') }}
                    </td>
                  </tr>
                  <tr>
                    <td><strong>Total pagos del plan (programado):</strong></td>
                    <td>${{ number_format($montoFinanciadoPlan, 2, '.', ',') }}</td>
                  </tr>
                  <tr>
                    <td><strong>Pagos del plan aprobados:</strong></td>
                    <td>${{ number_format($pagadoContrato, 2, '.', ',') }}</td>
                  </tr>
              
                </table>
              </div>

              {{-- Tabla de pagos programados --}}
              <div class="fin-section-card">
                <div class="fw-bold mb-2">Pagos programados</div>

                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Monto</th>
                        <th>Recibo</th>
                        @if($mostrarColumnaDocumento)<th>Factura</th>@endif
                        <th>Estado</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if($hayFilasPlan)

                        {{-- ANTICIPOS PAGADOS (para que aparezcan con su recibo) --}}
                        @foreach($pagosAnticipoAprobados as $pagoAnt)
                          <tr>
                            <td>
                              Anticipo aplicado
                              <div class="small text-muted">
                                Pago de anticipo registrado en caja.
                              </div>
                              <span class="badge bg-info-subtle text-info-emphasis mt-1">Anticipo</span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($pagoAnt->created_at)->format('d/m/Y') }}</td>
                            <td>${{ number_format($pagoAnt->monto, 2) }}</td>
                            <td>
                              <a href="{{ route('pagos.recibo', $pagoAnt->id) }}"
                                 class="btn btn-sm btn-outline-primary"
                                 target="_blank">
                                Ver recibo
                              </a>
                            </td>
                            @if($mostrarColumnaDocumento)
                              <td><span class="text-muted small">—</span></td>
                            @endif
                            <td>
                              <span class="badge bg-success">Pagado</span>
                            </td>
                          </tr>
                        @endforeach

                        {{-- TRADE-IN COMO FILAS INFORMATIVAS (si quieres ver recibo del trade-in) --}}
                        @foreach($tradeins as $ti)
                          <tr>
                            <td>
                              Equipo a cuenta (trade-in)
                              <div class="small text-muted">
                                {{ mb_strtoupper($ti->tipo_equipo ?? '—','UTF-8') }}
                                · {{ mb_strtoupper($ti->marca ?? '—','UTF-8') }}
                                @if($ti->modelo)
                                  · {{ mb_strtoupper($ti->modelo,'UTF-8') }}
                                @endif
                                @if($ti->numero_serie)
                                  · Serie: {{ mb_strtoupper($ti->numero_serie,'UTF-8') }}
                                @endif
                              </div>
                              <span class="badge bg-secondary-subtle text-secondary-emphasis mt-1">
                                Crédito aplicado a la venta
                              </span>
                            </td>
                            <td>
                              @if($ti->created_at)
                                {{ \Carbon\Carbon::parse($ti->created_at)->format('d/m/Y') }}
                              @else
                                —
                              @endif
                            </td>
                            <td>- ${{ number_format($ti->valor_a_cuenta ?? 0, 2) }}</td>
                            <td>
                              @if(Route::has('ventas.tradein.recibo'))
                                <a href="{{ route('ventas.tradein.recibo', $ti->id) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   target="_blank">
                                  Ver recibo
                                </a>
                              @else
                                <span class="text-muted small">—</span>
                              @endif
                            </td>
                            @if($mostrarColumnaDocumento)
                              <td><span class="text-muted small">—</span></td>
                            @endif
                            <td>
                              <span class="badge bg-success">Aplicado</span>
                            </td>
                          </tr>
                        @endforeach

                        {{-- CUOTAS DEL PLAN (PagoFinanciamiento) --}}
                        @foreach ($pagosPlan as $pagoFin)
                          @php
                            $estaPagadoCuota = $pagoFin->pago && $pagoFin->pago->aprobado;
                          @endphp
                          <tr>
                            <td>
                              {{ $pagoFin->descripcion ?? 'N/A' }}
                            </td>
                            <td>
                              @if($pagoFin->fecha_pago)
                                {{ \Carbon\Carbon::parse($pagoFin->fecha_pago)->format('d/m/Y') }}
                              @else
                                —
                              @endif
                            </td>
                            <td>${{ number_format($pagoFin->monto, 2) }}</td>
                            <td>
                              @if($estaPagadoCuota)
                                <a href="{{ route('pagos.recibo', $pagoFin->pago->id) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   target="_blank">
                                  Ver recibo
                                </a>
                              @else
                                <span class="text-muted small">—</span>
                              @endif
                            </td>
                            @if($mostrarColumnaDocumento)
                              <td>
                                @if($pagoFin->documentos && $pagoFin->documentos->isNotEmpty())
                                  <a href="{{ Storage::url($pagoFin->documentos->first()->ruta_archivo) }}"
                                     target="_blank"
                                     class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-regular fa-file-pdf"></i> Abrir
                                  </a>
                                @else
                                  <span class="text-muted small">—</span>
                                @endif
                              </td>
                            @endif
                            <td>
                              @if($estaPagadoCuota)
                                <span class="badge bg-success">Pagado</span>
                              @else
                                <span class="badge bg-warning text-dark">Pendiente</span>
                              @endif
                            </td>
                          </tr>
                        @endforeach

                      @else
                        <tr>
                          <td colspan="{{ $mostrarColumnaDocumento ? 6 : 5 }}" class="text-center">
                            No hay pagos programados
                          </td>
                        </tr>
                      @endif
                    </tbody>
                  </table>
                </div>
              </div>

            @endif
          </div>
        </div>
      </div>
    </div>

    <hr>
    <p class="fw-semibold small text-center text-secondary-emphasis mt-3">
      Si tienes duda, queja o aclaración, manda mensaje al
      <a href="tel:+522724485191" class="fw-bold text-decoration-none">+52 722 448 5191</a>
      o al correo
      <a href="mailto:compras@grupomedibuy.com" class="fw-bold text-decoration-none">compras@grupomedibuy.com</a>.
    </p>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Tooltips + Progreso -->
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const tt = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tt.map(el => new bootstrap.Tooltip(el));

      const barra = document.getElementById('barraProgreso');
      if (!barra) return;
      const porcentaje = parseInt(barra.dataset.porcentaje || 0);
      const contador = document.getElementById('porcentajeContador');
      let actual = 0;

      const anim = setInterval(()=>{
        if (actual >= porcentaje){ clearInterval(anim); return; }
        actual++;
        barra.style.width = actual+'%';
        if (contador) contador.textContent = actual+'%';
      }, 15);
    });
  </script>

  <!-- UX botones -->
  <script>
    async function downloadWithLoader(btn){
      if (!btn || btn.classList.contains('is-loading')) return;

      const url = btn.dataset.url;
      const suggested = btn.dataset.filename || 'documento.pdf';

      try{
        btn.classList.add('is-loading');
        btn.setAttribute('aria-busy','true');
        btn.disabled = true;

        const resp = await fetch(url, {
          method:'GET',
          credentials:'same-origin',
          headers: { 'Accept':'application/pdf' }
        });

        const ct = (resp.headers.get('Content-Type') || '').toLowerCase();

        if (!resp.ok || !ct.includes('application/pdf')) {
          let hint = '';
          try { hint = await resp.text(); } catch(_){}
          console.error('Descarga no-PDF. Status:', resp.status, 'CT:', ct, 'Body:', hint?.slice(0,500));
          alert('No se pudo descargar el PDF. (¿Sesión expirada o error 500/419?). Vuelve a iniciar sesión y reintenta.');
          return;
        }

        const blob = await resp.blob();
        const head = await blob.slice(0, 5).text();
        if (head !== '%PDF-') {
          alert('El archivo recibido no es un PDF válido.');
          return;
        }

        const cd = resp.headers.get('Content-Disposition') || '';
        const match = /filename\*?=(?:UTF-8'')?["']?([^"';\n]+)["']?/i.exec(cd);
        const filename = match ? decodeURIComponent(match[1]) : suggested;

        const link = document.createElement('a');
        const urlBlob = URL.createObjectURL(blob);
        link.href = urlBlob;
        link.download = filename.endsWith('.pdf') ? filename : (filename + '.pdf');
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(urlBlob);

      } catch(err){
        console.error(err);
        alert('Error al descargar el PDF. ' + (err?.message || 'Intenta de nuevo.'));
      } finally {
        btn.classList.remove('is-loading');
        btn.removeAttribute('aria-busy');
        btn.disabled = false;
      }
    }

    document.querySelectorAll('.pdf-btn').forEach(btn=>{
      btn.addEventListener('click', ()=> downloadWithLoader(btn));
    });

    const formWa = document.getElementById('form-wa');
    const btnWa  = document.getElementById('btn-wa');
    formWa?.addEventListener('submit', function(){
      if (!btnWa) return;
      btnWa.classList.add('is-loading');
      btnWa.setAttribute('aria-busy','true');
      btnWa.disabled = true;
    });

    document.querySelectorAll('.action-bar a.btn-chip').forEach(link=>{
      if(link.classList.contains('pdf-btn')) return;
      link.addEventListener('click', ()=> link.classList.add('is-loading'), { once:true });
    });
  </script>

</body>
</html>
