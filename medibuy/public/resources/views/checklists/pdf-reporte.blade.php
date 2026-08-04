<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de Checklist</title>
  <style>
    /* ===== Paleta BBVA-like ===== */
    :root{
      --bbva-primary:#072146;   /* azul profundo */
      --bbva-accent:#1464A5;    /* azul medio */
      --bbva-sky:#E6F0FA;       /* azul cielo */
      --bbva-mint:#DAF2E4;      /* verde éxito muy suave */
      --bbva-amber:#FFF4D6;     /* ámbar aviso suave */
      --bbva-danger:#FDE2E1;    /* rojo suave */
      --bbva-ink:#111827;       /* texto principal */
      --bbva-muted:#6B7280;     /* texto secundario */
      --bbva-line:#E5E7EB;      /* líneas */
    }

    *{ box-sizing:border-box; }
    html,body{ margin:0; padding:0; }
    body{ font-family: DejaVu Sans, Arial, sans-serif; color:var(--bbva-ink); font-size:12px; line-height:1.35; }

    /* ===== Encabezado ===== */
    .brandbar{
      background:var(--bbva-primary);
      color:#fff;
      padding:14px 20px;
    }
    .brandbar h1{
      margin:0;
      font-size:20px;
      letter-spacing:.2px;
    }
    .brandbar .meta{
      font-size:11px; opacity:.9; margin-top:2px;
    }

    .container{ padding:18px 22px; }

    /* ===== Secciones / Cards ===== */
    .section{
      background:#fff;
      border:1px solid var(--bbva-line);
      border-radius:8px;
      padding:12px 14px;
      margin-bottom:14px;
    }
    .section h2{
      margin:0 0 8px 0;
      color:var(--bbva-accent);
      font-size:15px;
    }
    .section h3{
      margin:12px 0 6px 0;
      color:#2D3748;
      font-size:13px;
    }
    .muted{ color:var(--bbva-muted); }
    .small{ font-size:11px; }

    /* ===== Resumen Ejecutivo ===== */
    .kpi-grid{
      width:100%;
      display:table;
      border-collapse:separate;
      border-spacing:8px 0;
    }
    .kpi{
      display:inline-block;
      width:24%;
      vertical-align:top;
      background:var(--bbva-sky);
      border:1px solid var(--bbva-line);
      border-radius:8px;
      padding:10px 12px;
      margin:0 6px 8px 0;
    }
    .kpi .label{ font-size:11px; color:var(--bbva-muted); }
    .kpi .value{ font-size:18px; font-weight:700; color:var(--bbva-primary); margin-top:2px; }
    .kpi.ok{ background:var(--bbva-mint); }
    .kpi.warn{ background:var(--bbva-amber); }
    .kpi.danger{ background:var(--bbva-danger); }

    /* ===== Badges ===== */
    .badge{
      display:inline-block; border:1px solid var(--bbva-line); background:#fff;
      padding:2px 8px; border-radius:999px; font-size:10px; margin-right:6px;
    }
    .badge.ok{ background:var(--bbva-mint); color:#065F46; border-color:#A7F3D0; }
    .badge.warn{ background:var(--bbva-amber); color:#92400E; border-color:#FDE68A; }
    .badge.err{ background:var(--bbva-danger); color:#7F1D1D; border-color:#FECACA; }

    /* ===== Tablas ===== */
    table{ width:100%; border-collapse:collapse; }
    th, td{ border:1px solid var(--bbva-line); padding:6px 7px; vertical-align:top; }
    th{
      background:var(--bbva-sky);
      color:var(--bbva-ink);
      font-weight:700;
    }
    tr:nth-child(even) td{ background:#FAFCFF; }

    /* ===== Firmas / Evidencias ===== */
    .sig{ max-height:60px; border:1px solid var(--bbva-line); border-radius:4px; margin-top:6px; }
    .thumb{ height:46px; border:1px solid var(--bbva-line); border-radius:4px; margin-right:6px; margin-bottom:6px; }

    /* ===== Utilidades ===== */
    .grid-2{ display:table; width:100%; table-layout:fixed; }
    .col{ display:table-cell; vertical-align:top; }
    .col + .col{ padding-left:10px; }

    .avoid-break{ page-break-inside:avoid; }
    .page-break{ page-break-before:always; }

    ul.compact{ margin:6px 0 0 16px; padding:0; }
    ul.compact li{ margin:0 0 2px 0; }
    code{
      background:#F3F4F6; padding:1px 4px; border-radius:4px;
      font-family: DejaVu Sans Mono, monospace; font-size:11px;
    }
  </style>
</head>
<body>

  {{-- BRAND BAR --}}
  <div class="brandbar">
    <h1>Reporte de Checklist</h1>
    <div class="meta">
      Venta: <b>{{ $venta->folio ?? $venta->id }}</b> ·
      Creado: <b>{{ $checklist->created_at?->format('d/m/Y H:i') ?? '—' }}</b> ·
      Actualizado: <b>{{ $checklist->updated_at?->format('d/m/Y H:i') ?? '—' }}</b>
    </div>
  </div>

  <div class="container">

    {{-- =================== RESUMEN EJECUTIVO =================== --}}
    @php
      $prodCount = !empty($productos) ? count($productos) : 0;

      $ingVer = is_array($ingenieria?->verificados) ? $ingenieria->verificados : (json_decode($ingenieria->verificados ?? '[]', true) ?: []);
      $ingNo  = is_array($ingenieria?->no_verificados) ? $ingenieria->no_verificados : (json_decode($ingenieria->no_verificados ?? '[]', true) ?: []);
      $ingBy  = $ingenieria?->usuario->name ?? '—';

      $embVer = is_array($embalaje?->verificados) ? $embalaje->verificados : (json_decode($embalaje->verificados ?? '[]', true) ?: []);
      $embNo  = is_array($embalaje?->no_verificados) ? $embalaje->no_verificados : (json_decode($embalaje->no_verificados ?? '[]', true) ?: []);
      $embBy  = $embalaje?->usuario->name ?? '—';

      $entVer = is_array($entrega?->verificados) ? $entrega->verificados : (json_decode($entrega->verificados ?? '[]', true) ?: []);
      $entNo  = is_array($entrega?->no_verificados) ? $entrega->no_verificados : (json_decode($entrega->no_verificados ?? '[]', true) ?: []);
      $entBy  = $entrega?->usuario->name ?? '—';

      $entDat = is_array($entrega?->datos_entrega) ? $entrega->datos_entrega : (json_decode($entrega->datos_entrega ?? '[]', true) ?: []);
      $tipoEntrega = $entDat['tipo_entrega'] ?? '—';
    @endphp

    <div class="section avoid-break">
      <h2>Resumen ejecutivo</h2>
      <div class="kpi-grid">
        <div class="kpi"><div class="label">Productos</div><div class="value">{{ $prodCount }}</div></div>
        <div class="kpi ok"><div class="label">Ing. verificados</div><div class="value">{{ count($ingVer) }}</div></div>
        <div class="kpi warn"><div class="label">Emb. verificados</div><div class="value">{{ count($embVer) }}</div></div>
        <div class="kpi"><div class="label">Entrega (tipo)</div><div class="value">{{ ucfirst($tipoEntrega) }}</div></div>
      </div>

      <table class="avoid-break" style="margin-top:6px">
        <thead>
          <tr>
            <th style="width:28%">Fase</th>
            <th style="width:24%">Hecho por</th>
            <th style="width:24%">Verificados</th>
            <th style="width:24%">No verificados</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Ingeniería</td>
            <td>{{ $ingBy }}</td>
            <td><span class="badge ok">{{ count($ingVer) }}</span></td>
            <td><span class="badge {{ count($ingNo) ? 'warn' : '' }}">{{ count($ingNo) }}</span></td>
          </tr>
          <tr>
            <td>Embalaje</td>
            <td>{{ $embBy }}</td>
            <td><span class="badge ok">{{ count($embVer) }}</span></td>
            <td><span class="badge {{ count($embNo) ? 'warn' : '' }}">{{ count($embNo) }}</span></td>
          </tr>
          <tr>
            <td>Entrega</td>
            <td>{{ $entBy }}</td>
            <td><span class="badge ok">{{ count($entVer) }}</span></td>
            <td><span class="badge {{ count($entNo) ? 'warn' : '' }}">{{ count($entNo) }}</span></td>
          </tr>
        </tbody>
      </table>
    </div>

    {{-- ============== PRODUCTOS ============== --}}
    @if(!empty($productos) && count($productos))
      <div class="section avoid-break">
        <h2>Productos</h2>
        <table>
          <thead>
          <tr>
            <th style="width:40%">Equipo</th>
            <th style="width:40%">Marca / Modelo</th>
            <th style="width:20%">Serie</th>
          </tr>
          </thead>
          <tbody>
          @foreach($productos as $p)
            <tr>
              <td>{{ $p->tipo_equipo ?? '—' }}</td>
              <td>{{ trim(($p->marca ?? '').' '.($p->modelo ?? '')) ?: '—' }}</td>
              <td><code>{{ $p->numero_serie ?? '—' }}</code></td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    @endif

    {{-- ============== INGENIERÍA ============== --}}
    @if(!empty($ingenieria))
      @php
        $ingComp = is_array($ingenieria->componentes) ? $ingenieria->componentes : json_decode($ingenieria->componentes ?? '[]', true);
        $ingObs  = $ingenieria->observaciones ?? $ingenieria->incidente ?? null;
        $ingEvi  = is_array($ingenieria->evidencias) ? $ingenieria->evidencias : (json_decode($ingenieria->evidencias ?? '[]', true) ?: []);
        $ingUser = $ingenieria->usuario->name ?? '—';
        $ingAt   = $ingenieria->created_at?->format('d/m/Y H:i') ?? '—';
      @endphp
      <div class="section">
        <h2>Ingeniería</h2>
        <div class="small muted">Realizado por: <b>{{ $ingUser }}</b> · Fecha: <b>{{ $ingAt }}</b></div>

        @if($ingObs)
          <h3>Observaciones</h3>
          <div class="avoid-break">{{ $ingObs }}</div>
        @endif

        @if(!empty($ingComp))
          <h3>Componentes por serie</h3>
          <table>
            <thead>
            <tr>
              <th style="width:25%">Serie</th>
              <th style="width:45%">Componentes</th>
              <th style="width:30%">Resumen</th>
            </tr>
            </thead>
            <tbody>
            @foreach($ingComp as $serie => $comps)
              @php
                $ok = 0; $total = 0; $list = [];
                if (is_array($comps)) {
                  foreach($comps as $name => $val){ $total++; if($val) $ok++; $list[] = [$name, $val]; }
                }
              @endphp
              <tr>
                <td><code>{{ $serie }}</code></td>
                <td>
                  <ul class="compact">
                    @foreach($list as [$name,$val])
                      <li>{{ $name }} {!! $val ? '✅' : '—' !!}</li>
                    @endforeach
                  </ul>
                </td>
                <td>{{ $ok }} de {{ $total }} correctos</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        @endif

        @if(!empty($ingEvi))
          <h3>Evidencias</h3>
          <div class="avoid-break">
            @foreach($ingEvi as $p)
              @php $path = is_string($p) ? public_path($p) : null; @endphp
              @if($path && file_exists($path) && preg_match('/\.(jpe?g|png|webp)$/i', $path))
                <img class="thumb" src="{{ $path }}">
              @endif
            @endforeach
          </div>
        @endif

        <div class="avoid-break">
          @php
            $fr = $ingenieria->firma_responsable ? public_path($ingenieria->firma_responsable) : null;
            $fs = $ingenieria->firma_supervisor  ? public_path($ingenieria->firma_supervisor)  : null;
          @endphp
          @if($fr && file_exists($fr))
            <div class="small"><b>Firma Responsable</b></div>
            <img class="sig" src="{{ $fr }}">
          @endif
          @if($fs && file_exists($fs))
            <div class="small"><b>Firma Supervisor</b></div>
            <img class="sig" src="{{ $fs }}">
          @endif
        </div>
      </div>
    @endif

    {{-- ============== EMBALAJE ============== --}}
    @if(!empty($embalaje))
      @php
        $embComp = is_array($embalaje->componentes) ? $embalaje->componentes : json_decode($embalaje->componentes ?? '[]', true);
        $embObs  = $embalaje->observaciones ?? null;
        $embEvi  = is_array($embalaje->evidencias) ? $embalaje->evidencias : (json_decode($embalaje->evidencias ?? '[]', true) ?: []);
        $embUser = $embalaje->usuario->name ?? '—';
        $embAt   = $embalaje->created_at?->format('d/m/Y H:i') ?? '—';
      @endphp
      <div class="section">
        <h2>Embalaje</h2>
        <div class="small muted">Realizado por: <b>{{ $embUser }}</b> · Fecha: <b>{{ $embAt }}</b></div>

        @if($embObs)
          <h3>Observaciones</h3>
          <div class="avoid-break">{{ $embObs }}</div>
        @endif

        @if(!empty($embComp))
          <h3>Componentes por serie</h3>
          <table>
            <thead>
            <tr>
              <th style="width:25%">Serie</th>
              <th style="width:45%">Componentes</th>
              <th style="width:30%">Resumen</th>
            </tr>
            </thead>
            <tbody>
            @foreach($embComp as $serie => $comps)
              @php
                $ok = 0; $total = 0; $list = [];
                if (is_array($comps)) {
                  foreach($comps as $name => $val){ $total++; if($val) $ok++; $list[] = [$name, $val]; }
                }
              @endphp
              <tr>
                <td><code>{{ $serie }}</code></td>
                <td>
                  <ul class="compact">
                    @foreach($list as [$name,$val])
                      <li>{{ $name }} {!! $val ? '✅' : '—' !!}</li>
                    @endforeach
                  </ul>
                </td>
                <td>{{ $ok }} de {{ $total }} correctos</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        @endif

        @if(!empty($embEvi))
          <h3>Evidencias</h3>
          <div class="avoid-break">
            @foreach($embEvi as $p)
              @php $path = is_string($p) ? public_path($p) : null; @endphp
              @if($path && file_exists($path) && preg_match('/\.(jpe?g|png|webp)$/i', $path))
                <img class="thumb" src="{{ $path }}">
              @endif
            @endforeach
          </div>
        @endif

        <div class="avoid-break">
          @php
            $fr = $embalaje->firma_responsable ? public_path($embalaje->firma_responsable) : null;
            $fs = $embalaje->firma_supervisor  ? public_path($embalaje->firma_supervisor)  : null;
          @endphp
          @if($fr && file_exists($fr))
            <div class="small"><b>Firma Responsable</b></div>
            <img class="sig" src="{{ $fr }}">
          @endif
          @if($fs && file_exists($fs))
            <div class="small"><b>Firma Supervisor</b></div>
            <img class="sig" src="{{ $fs }}">
          @endif
        </div>
      </div>
    @endif

    {{-- ============== ENTREGA ============== --}}
    @if(!empty($entrega))
      @php
        $entObs = $entrega->observaciones ?? null;
        $entDat = is_array($entrega->datos_entrega) ? $entrega->datos_entrega : (json_decode($entrega->datos_entrega ?? '[]', true) ?: []);
        $entVer = is_array($entrega->verificados) ? $entrega->verificados : (json_decode($entrega->verificados ?? '[]', true) ?: []);
        $entNo  = is_array($entrega->no_verificados) ? $entrega->no_verificados : (json_decode($entrega->no_verificados ?? '[]', true) ?: []);
        $entUser= $entrega->usuario->name ?? '—';
        $entAt  = $entrega->created_at?->format('d/m/Y H:i') ?? '—';

        $labels = [
          'tipo_entrega'            => 'Tipo de entrega',
          'recibe_nombre'           => 'Recibe (nombre)',
          'recibe_cargo'            => 'Cargo / Área',
          'lugar_entrega'           => 'Lugar de entrega',
          'fecha_entrega'           => 'Fecha/hora entrega',
          'capacitacion'            => 'Se brindó capacitación',
          'documentacion_entregada' => 'Documentación entregada',
          'paqueteria'              => 'Paquetería',
          'guia'                    => 'Guía / Tracking',
          'direccion_envio'         => 'Dirección de envío',
          'fecha_envio'             => 'Fecha de envío',
          'contacto_envio'          => 'Contacto',
          'telefono_envio'          => 'Teléfono',
        ];
      @endphp
      <div class="section">
        <h2>Entrega</h2>
        <div class="small muted">Realizado por: <b>{{ $entUser }}</b> · Fecha: <b>{{ $entAt }}</b></div>

        <h3>Resumen</h3>
        <table class="avoid-break">
          <thead>
          <tr>
            <th style="width:40%">Campo</th>
            <th style="width:60%">Valor</th>
          </tr>
          </thead>
          <tbody>
          @foreach($labels as $key => $label)
            @php $val = $entDat[$key] ?? null; @endphp
            @if(isset($val) && $val !== '')
              <tr>
                <td>{{ $label }}</td>
                <td>
                  @if(in_array($key, ['capacitacion','documentacion_entregada']))
                    {{ $val ? 'Sí' : 'No' }}
                  @else
                    {{ $val }}
                  @endif
                </td>
              </tr>
            @endif
          @endforeach
          @if($entObs)
            <tr>
              <td><b>Comentario final</b></td>
              <td>{{ $entObs }}</td>
            </tr>
          @endif
          </tbody>
        </table>

        <div style="margin-top:8px">
          <span class="badge ok">Series entregadas: {{ count($entVer) }}</span>
          <span class="badge {{ count($entNo)?'warn':'' }}">Pendientes: {{ count($entNo) }}</span>
        </div>

        @if(!empty($entVer))
          <h3>Entregadas</h3>
          <ul class="compact avoid-break">
            @foreach($entVer as $s) <li><code>{{ $s }}</code></li> @endforeach
          </ul>
        @endif

        @if(!empty($entNo))
          <h3>Pendientes</h3>
          <ul class="compact avoid-break">
            @foreach($entNo as $s) <li><code>{{ $s }}</code></li> @endforeach
          </ul>
        @endif

        <div class="avoid-break" style="margin-top:6px">
          @php
            $fc = $entrega->firma_cliente ? public_path($entrega->firma_cliente) : null;
            $fe = $entrega->firma_entrega ? public_path($entrega->firma_entrega) : null;
          @endphp
          @if($fc && file_exists($fc))
            <div class="small"><b>Firma Cliente</b></div>
            <img class="sig" src="{{ $fc }}">
          @endif
          @if($fe && file_exists($fe))
            <div class="small"><b>Firma Entrega</b></div>
            <img class="sig" src="{{ $fe }}">
          @endif
        </div>
      </div>
    @endif

    <div class="small muted" style="text-align:center; margin-top:10px;">
      Generado automáticamente el {{ now()->format('d/m/Y H:i') }}
    </div>

  </div>
</body>
</html>
