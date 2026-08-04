<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Cotización</title>

  <!-- Bootstrap + Iconos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <!-- Fuente Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tu CSS base -->
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

    /* Tarjetas / layout financiero reutilizado */
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

    .fin-section-card + .fin-section-card{margin-top:10px;}

    .fin-table{width:100%;font-size:13px;background:#fff;border-radius:12px;overflow:hidden}
    .fin-table th{
      background:#f1f5f9;color:#0f172a;font-weight:700;text-transform:uppercase;font-size:11px;letter-spacing:.5px
    }
    .fin-table td,.fin-table th{padding:9px 10px;border-bottom:1px solid #eef2f7}
    .fin-table tr:last-child td{border-bottom:none}

    .fin-money{font-weight:700}
    .fin-money.neg{color:#b91c1c}

    .fin-kv{
      display:grid; grid-template-columns: 1fr 1fr; gap:8px 12px; font-size:14px;
    }
    .fin-kv .k{color:#64748b; font-weight:600;}
    .fin-kv .v{font-weight:500; color:#0f172a;}
    @media (max-width: 576px){
      .fin-kv{ grid-template-columns: 1fr; }
    }

    .header-container{
      position:relative;
      padding:1.5rem 1.25rem 1.25rem;
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:1rem;
      overflow:hidden;
      border-radius:0 0 1.5rem 1.5rem;
      background:radial-gradient(circle at top left,#4f46e5 0,#38bdf8 40%,#ecfeff 100%);
      color:#fff;
    }
    .header-container .titulos{
      margin:0;
      font-weight:700;
      letter-spacing:.03em;
    }
    .gradient-bg-animation{
      position:absolute;
      inset:0;
      background:radial-gradient(circle at 20% 0%,rgba(255,255,255,.25),transparent 55%);
      pointer-events:none;
      opacity:.7;
    }
    .header-container > *{ position:relative; z-index:2; }

    .back-button .menu-icon{
      border:none;
      background:rgba(15,23,42,.2);
      border-radius:999px;
      padding:.4rem .7rem;
      display:flex;
      align-items:center;
      justify-content:center;
    }
    .back-button img{ width:22px;height:22px;filter:invert(1); }
  </style>
</head>

<body>

  <div class="container mt-5 mb-5">
    {{-- ===================== CÁLCULOS GLOBALES ===================== --}}
    @php
      // Total de la cotización
      $totalCotizacion = (float)($propuesta->total ?? 0);

      // Trade–in de la tabla propuesta_tradeins (relación $propuesta->tradeins)
      $tradeins      = $propuesta->tradeins ?? collect();
      $tradeinTotal  = (float)$tradeins->sum('valor_a_cuenta');

      // Total del contrato (después de tomar a cuenta)
      $totalContrato = max($totalCotizacion - $tradeinTotal, 0);

      // Plan de pagos (relación pagosFinanciamiento)
      $pagosPlan = $propuesta->pagosFinanciamiento ?? collect();
      $pagoInicial = $pagosPlan->first(fn($p) => strtolower(trim($p->descripcion ?? '')) === 'pago inicial');
      $montoInicial = (float)($pagoInicial->monto ?? 0);
      $pagosResto = $pagosPlan->filter(fn($p) => strtolower(trim($p->descripcion ?? '')) !== 'pago inicial');
      $plazoMeses = $pagosResto->count();
      $montoTotalPlan = (float)$pagosPlan->sum('monto');

      $labelsMes = \Illuminate\Support\Str::plural('mes', $plazoMeses);

      $anio = optional($propuesta->created_at)->format('Y') ?? date('Y');

      $vigencia = optional($propuesta->created_at)->copy()?->addDays(30);
    @endphp

    {{-- Mensajes de WhatsApp de la cotización --}}
    @if(session('wa_success'))
      <div class="alert alert-success border-0" style="border-radius:12px">{{ session('wa_success') }}</div>
    @endif
    @if(session('wa_info'))
      <div class="alert alert-warning border-0" style="border-radius:12px">
        {{ session('wa_info') }}
      </div>
    @endif

    <!-- ====== Header con acciones ====== -->
    <div class="page-header mb-4">
      <h2 class="mb-0">
        Resumen de Cotización N.{{ $anio }}{{ str_pad($propuesta->id,4,'0',STR_PAD_LEFT) }}
      </h2>

      <div class="action-bar" role="toolbar" aria-label="Acciones de la cotización">
        <!-- PDF -->
        <a href="{{ route('propuestas.pdf', $propuesta->id) }}"
           class="btn-chip -danger"
           target="_blank"
           rel="noopener"
           title="Descargar PDF">
          <i class="fa-solid fa-file-pdf"></i>
          <span class="label">Descargar PDF</span>
          <span class="spinner" aria-hidden="true"></span>
        </a>

        @auth
        <!-- WhatsApp -->
        <form method="POST" action="{{ route('propuestas.whatsapp.plantilla', $propuesta) }}" id="form-wa" class="m-0">
          @csrf
          <input type="hidden" name="template_name" value="doc_pdf_utility_v4">
          <input type="hidden" name="template_lang"  value="es_MX">
          <button type="submit" id="btn-wa" class="btn-chip -success" title="Enviar por WhatsApp">
            <i class="fa-brands fa-whatsapp"></i>
            <span class="label">
              @if(session('wa_success')) Enviado ✓ @else Enviar por WhatsApp @endif
            </span>
            <span class="spinner" aria-hidden="true"></span>
          </button>
        </form>
        @endauth
      </div>
    </div>

    {{-- ================== PRIMERA FILA: INFO + PRODUCTOS ================== --}}
    <div class="row g-4">
      {{-- DATOS DE LA COTIZACIÓN --}}
      <div class="col-md-6 d-flex">
        <div class="fin-card w-100 border-0">
          <div class="fin-head">
            <div class="fin-title">
              <div class="fin-icon"><i class="fa-solid fa-file-signature"></i></div>
              <div>
                <div class="fw-bold">Datos de la cotización</div>
                <div class="fin-sub">Información general</div>
              </div>
            </div>
            <span class="fin-chip neutral">
              {{ mb_strtoupper($propuesta->plan ?? 'N/A','UTF-8') }}
            </span>
          </div>

          <div class="fin-section">
            <div class="fin-section-card">
              <div class="fin-kv">
                <div class="k">Cliente</div>
                <div class="v">
                  {{ mb_strtoupper($propuesta->cliente->nombre.' '.$propuesta->cliente->apellido,'UTF-8') }}
                </div>

                <div class="k">Teléfono cliente</div>
                <div class="v">{{ $propuesta->cliente->telefono ?? 'NO REGISTRADO' }}</div>

                <div class="k">Correo</div>
                <div class="v">{{ $propuesta->cliente->email ?? 'NO REGISTRADO' }}</div>

                <div class="k">Dirección / Comentarios</div>
                <div class="v">{{ $propuesta->cliente->comentarios ?? 'SIN COMENTARIOS' }}</div>

                <div class="k">Lugar de cotización</div>
                <div class="v">{{ $propuesta->lugar ?? 'N/A' }}</div>

                <div class="k">Nota interna</div>
                <div class="v">{{ $propuesta->nota ?? 'SIN NOTA' }}</div>

                <div class="k">Asesor</div>
                <div class="v">{{ mb_strtoupper($propuesta->usuario->name,'UTF-8') }}</div>

                <div class="k">Fecha</div>
                <div class="v">{{ $propuesta->created_at?->format('d/m/Y H:i') }}</div>
              </div>
            </div>

            <div class="fin-metrics mt-2">
              <div class="fin-metric">
                <div class="label">Total cotización</div>
                <div class="value">${{ number_format($totalCotizacion,2) }}</div>
                <div class="hint">Antes de equipos a cuenta</div>
              </div>
              <div class="fin-metric">
                <div class="label">Equipos a cuenta</div>
                <div class="value fin-money neg">
                  - ${{ number_format($tradeinTotal,2) }}
                </div>
                <div class="hint">propuesta_tradeins</div>
              </div>
              <div class="fin-metric" style="background:#fefce8;">
                <div class="label">Total del contrato</div>
                <div class="value text-success">
                  ${{ number_format($totalContrato,2) }}
                </div>
                <div class="hint">Después de tomar a cuenta</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- PRODUCTOS --}}
      <div class="col-md-6 d-flex">
        <div class="fin-card w-100 border-0">
          <div class="fin-head">
            <div class="fin-title">
              <div class="fin-icon"><i class="fa-solid fa-cart-shopping"></i></div>
              <div>
                <div class="fw-bold">Productos seleccionados</div>
                <div class="fin-sub">Detalle del equipo cotizado</div>
              </div>
            </div>
            <span class="fin-chip neutral">
              {{ $propuesta->productos->count() }} items
            </span>
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
                    @foreach ($propuesta->productos as $item)
                      <tr>
                        <td>
                          @php
                            $img = $item->producto?->imagen
                              ? asset('storage/'.$item->producto->imagen)
                              : asset('images/imagen-no-disponible.png');
                          @endphp
                          <img src="{{ $img }}"
                               alt="{{ $item->producto->nombre ?? 'Producto eliminado' }}"
                               class="rounded shadow-sm"
                               style="width:48px;height:48px;object-fit:cover;">
                        </td>
                        <td>
                          @if ($item->producto)
                            <span class="fw-semibold d-block">
                              {{ mb_strtoupper($item->producto->tipo_equipo ?? '—','UTF-8') }}
                            </span>
                            <small class="text-muted d-block">
                              {{ mb_strtoupper($item->producto->modelo ?? '', 'UTF-8') }} |
                              {{ mb_strtoupper($item->producto->marca ?? '', 'UTF-8') }}
                            </small>
                          @else
                            <span class="text-danger fst-italic">Producto eliminado</span>
                          @endif
                        </td>
                        <td class="text-center">{{ $item->cantidad }}</td>
                        <td class="text-end fin-money">
                          ${{ number_format((float)$item->subtotal, 2) }}
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>

                <div class="d-flex flex-column gap-1 text-end small mt-2">
                  <div><strong>Subtotal:</strong> ${{ number_format((float)$propuesta->subtotal, 2) }}</div>
                  @if(($propuesta->descuento ?? 0) > 0)
                    <div>
                      <strong>Descuento:</strong>
                      <span class="text-warning">
                        ${{ number_format((float)$propuesta->descuento, 2) }}
                      </span>
                    </div>
                  @endif
                  @if(($propuesta->envio ?? 0) > 0)
                    <div><strong>Envío:</strong> ${{ number_format((float)$propuesta->envio, 2) }}</div>
                  @endif
                  <div><strong>IVA:</strong> ${{ number_format((float)$propuesta->iva, 2) }}</div>
                  <div class="fw-bold fs-5 mt-2 text-success-emphasis">
                    <strong>Total:</strong> ${{ number_format((float)$propuesta->total, 2) }}
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    {{-- ================== SEGUNDA FILA: TRADE–IN + PLAN DE PAGOS ================== --}}
    <div class="row g-4 mt-4">

      {{-- TRADE–IN DETALLADO / RESUMEN FINANCIERO --}}
      <div class="col-md-6 d-flex">
        <div class="fin-card w-100 border-0">
          <div class="fin-head">
            <div class="fin-title">
              <div class="fin-icon"><i class="fa-solid fa-rotate"></i></div>
              <div>
                <div class="fw-bold">Equipos a cuenta (Trade-in)</div>
                <div class="fin-sub">Tabla propuesta_tradeins</div>
              </div>
            </div>
            <span class="fin-chip {{ $tradeinTotal > 0 ? 'ok' : 'neutral' }}">
              {{ $tradeinTotal > 0 ? 'CON TRADE-IN' : 'SIN TRADE-IN' }}
            </span>
          </div>

          <div class="fin-section">
            <div class="fin-section-card">
              @if($tradeins->count() > 0)
                <div class="table-responsive">
                  <table class="table fin-table mb-2">
                    <thead>
                      <tr>
                        <th>Tipo de equipo</th>
                        <th>Marca / Modelo</th>
                        <th>No. serie</th>
                        <th class="text-end">Valor a cuenta</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($tradeins as $ti)
                        <tr>
                          <td>{{ mb_strtoupper($ti->tipo_equipo ?? '—','UTF-8') }}</td>
                          <td>
                            {{ mb_strtoupper($ti->marca ?? '—','UTF-8') }}
                            @if($ti->modelo)
                              / {{ mb_strtoupper($ti->modelo,'UTF-8') }}
                            @endif
                          </td>
                          <td>{{ mb_strtoupper($ti->numero_serie ?? '—','UTF-8') }}</td>
                          <td class="text-end fin-money">
                            ${{ number_format((float)$ti->valor_a_cuenta, 2) }}
                          </td>
                        </tr>
                      @endforeach
                      <tr>
                        <td colspan="3" class="text-end fw-semibold">Total equipos a cuenta</td>
                        <td class="text-end fw-semibold fin-money neg">
                          - ${{ number_format($tradeinTotal, 2) }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              @else
                {{-- Cuando NO hay trade-in mostramos un bloque informativo --}}
                <div class="d-flex align-items-start gap-3">
                  <div class="badge rounded-pill bg-light text-secondary d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                    <i class="fa-solid fa-circle-info"></i>
                  </div>
                  <div class="small">
                    <div class="fw-semibold mb-1">Esta cotización no incluye equipos a cuenta.</div>
                    <p class="mb-1 text-muted">
                      El <strong>total del contrato</strong> corresponde al total de la cotización sin aplicar trade-in.
                    </p>
                    <p class="mb-0 text-muted">
                      Si más adelante el cliente entrega equipo a cuenta, aquí se verá reflejado el valor descontado y el nuevo total del contrato.
                    </p>
                  </div>
                </div>
              @endif
            </div>

            <div class="fin-metrics mt-2">
              <div class="fin-metric">
                <div class="label">Total cotización</div>
                <div class="value">${{ number_format($totalCotizacion,2) }}</div>
                <div class="hint">
                  {{ $tradeinTotal > 0 ? 'Sin aplicar trade-in' : 'Monto original de la propuesta' }}
                </div>
              </div>
              <div class="fin-metric" style="background:#fefce8;">
                <div class="label">Total del contrato</div>
                <div class="value text-success">
                  ${{ number_format($totalContrato,2) }}
                </div>
                <div class="hint">
                  {{ $tradeinTotal > 0 ? 'Después de tomar a cuenta los equipos' : 'Igual al total de la cotización (sin trade-in)' }}
                </div>
              </div>
            </div>

            {{-- Bloque de notas y vigencia --}}
            <div class="fin-section-card mt-2">
              <div class="fw-semibold mb-2">
                <i class="fa-regular fa-clock me-1"></i>
                Notas de la cotización
              </div>
              <ul class="small text-muted mb-0 ps-3">
                @if($vigencia)
                  <li>
                    <strong>Vigencia estimada:</strong>
                    {{ $vigencia->format('d/m/Y') }}.
                  </li>
                @endif
                <li>
                  Los montos aquí mostrados son informativos y pueden ajustarse al momento de formalizar la venta.
                </li>
                <li>
                  {{ $tradeinTotal > 0
                      ? 'El valor del trade-in está sujeto a inspección física y validación técnica del equipo entregado.'
                      : 'En caso de aplicar equipos a cuenta (trade-in), el valor se determinará tras la revisión física del equipo.' }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      {{-- PLAN DE PAGOS ESTIMADO --}}
      <div class="col-md-6 d-flex">
        <div class="fin-card w-100 border-0">
          <div class="fin-head">
            <div class="fin-title">
              <div class="fin-icon"><i class="fa-solid fa-calendar-check"></i></div>
              <div>
                <div class="fw-bold">Plan de pagos estimado</div>
                <div class="fin-sub">
                  {{ mb_strtoupper($propuesta->cliente->nombre.' '.$propuesta->cliente->apellido,'UTF-8') }}
                </div>
              </div>
            </div>
            <span class="fin-chip neutral">
              {{ mb_strtoupper($propuesta->plan ?? 'N/A','UTF-8') }}
            </span>
          </div>

          <div class="fin-section">
            <div class="fin-section-card mb-3">
              <div class="fw-bold mb-2" style="color:#1e73be">
                Resumen del financiamiento
              </div>
              <table class="table table-sm mb-0">
                <tr>
                  <td><strong>Total del contrato:</strong></td>
                  <td>${{ number_format($totalContrato, 2, '.', ',') }}</td>
                </tr>
                <tr>
                  <td><strong>Total plan estimado:</strong></td>
                  <td>${{ number_format($montoTotalPlan, 2, '.', ',') }}</td>
                </tr>
                @if($montoInicial > 0)
                  <tr>
                    <td><strong>Pago inicial estimado:</strong></td>
                    <td>${{ number_format($montoInicial, 2, '.', ',') }}</td>
                  </tr>
                @endif
                @if($plazoMeses > 0)
                  <tr>
                    <td><strong>Plazo estimado:</strong></td>
                    <td>{{ $plazoMeses }} {{ $labelsMes }}</td>
                  </tr>
                @endif
              </table>
            </div>

            <div class="fin-section-card">
              <div class="fw-bold mb-2">Detalle de pagos (presupuesto)</div>
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Descripción</th>
                      <th>Fecha estimada</th>
                      <th class="text-end">Monto</th>
                      <th class="text-center">Estado</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($pagosPlan as $pago)
                      <tr>
                        <td>{{ $pago->descripcion ?? 'N/A' }}</td>
                        <td>
                          @if($pago->fecha_pago)
                            {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}
                          @else
                            —
                          @endif
                        </td>
                        <td class="text-end">
                          ${{ number_format((float)$pago->monto, 2) }}
                        </td>
                        <td class="text-center">
                          <span class="badge bg-secondary-subtle text-secondary-emphasis">
                            Estimado
                          </span>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="4" class="text-center text-muted">
                          No se definió un plan de pagos para esta cotización.
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    <hr class="mt-5">
    <p class="fw-semibold small text-center text-secondary-emphasis mt-3">
      Esta cotización es informativa y puede ajustarse al momento de generar la venta y la remisión final.
    </p>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Solo loader para WhatsApp -->
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const formWa = document.getElementById('form-wa');
      const btnWa  = document.getElementById('btn-wa');

      formWa?.addEventListener('submit', function(){
        if (!btnWa) return;
        btnWa.classList.add('is-loading');
        btnWa.setAttribute('aria-busy','true');
        btnWa.disabled = true;
      });
    });
  </script>
</body>
</html>
