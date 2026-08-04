{{-- resources/views/ordenes/pdf.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>OS #{{ $orden->id }}</title>
  <style>
    @page {
      size: letter;
      margin: 22mm 18mm 30mm 18mm;
    }
    html, body { margin:0; padding:0; }

    :root{
      --fs: 9.4px;
      --lh: 1.35;
      --pad: 3px 5px;

      /* Paleta pastel */
      --ink:#0f172a;
      --muted:#6b7280;
      --bg-page:#f9fafb;
      --card:#ffffff;
      --pastel-blue:#e0f2fe;
      --pastel-mint:#dcfce7;
      --pastel-lilac:#ede9fe;
      --soft-line:#e5e7eb;
    }

    body{
      font-family: "Söhne", "Circular Std", "Poppins", system-ui, -apple-system,
                   "Segoe UI", "Helvetica Neue", Arial, sans-serif;
      font-size: var(--fs);
      line-height: var(--lh);
      color: var(--ink);
      background: var(--bg-page);
    }

    .page{
      padding: 6mm 4mm;
      box-sizing: border-box;
      background:#ffffff;
    }

    .w-100{width:100%}
    .t-right{text-align:right}
    .t-center{text-align:center}
    .bold{font-weight:700}
    .semibold{font-weight:600}
    .upper{text-transform:uppercase}
    .small{font-size: calc(var(--fs) - 1px)}
    .xs{font-size: calc(var(--fs) - 1.4px)}
    .mb-5{margin-bottom: 12px}
    .mb-3{margin-bottom: 6px}
    .muted{color:var(--muted)}
    .avoid-break{page-break-inside:avoid}

    table{ border-collapse:collapse; width:100%; table-layout:fixed; }
    th,td{ padding: var(--pad); vertical-align:top; }

    /* Cards suaves para secciones */
    .card{
      border-radius:8px;
      background: var(--card);
      padding:6px 7px;
      box-sizing:border-box;
    }
    .card-soft-blue{
      background: linear-gradient(135deg, #ffffff, var(--pastel-blue));
    }
    .card-soft-mint{
      background: linear-gradient(135deg, #ffffff, var(--pastel-mint));
    }
    .card-soft-lilac{
      background: linear-gradient(135deg, #ffffff, var(--pastel-lilac));
    }

    .section-header{
      font-weight:700;
      letter-spacing:.08em;
      text-transform:uppercase;
      font-size: calc(var(--fs) - 0.4px);
      color:#4b5563;
      margin-bottom:4px;
    }

    .sub-header{
      font-weight:700;
      letter-spacing:.08em;
      text-transform:uppercase;
      font-size: calc(var(--fs) - 0.7px);
      color:#4b5563;
      margin:10px 0 4px;
    }

    .section-divider{
      height:1px;
      background:linear-gradient(90deg, transparent, var(--soft-line), transparent);
      margin:12px 0 18px;
    }

    /* Caja para texto / observaciones */
    .note-box{
      border:1px dashed rgba(209,213,219,0.9);
      background:#ffffff;
      border-radius:10px;
      padding:6px 8px;
      box-sizing:border-box;
      min-height: 34px;
    }

    /* HEADER / BRANDING */
    .brand-row td{ border:none; padding:0; }

    .brand-shell{
      padding:6px 9px;
      border-radius:10px;
      background: linear-gradient(90deg, #ffffff, #eff6ff);
      border:1px solid #e5e7eb;
    }

    .logo{
      max-height: 46px;
      width:auto;
      display:block;
    }

    .doc-title{
      font-size: 16px;
      font-weight:800;
      letter-spacing:.12em;
      color:#111827;
    }

    .doc-sub{
      font-size: calc(var(--fs) - 0.2px);
      color:var(--muted);
      letter-spacing:.04em;
    }

    .title-accent{
      height:3px;
      width:150px;
      margin:6px auto 0;
      background: linear-gradient(90deg, #a5b4fc, #7dd3fc, #bbf7d0);
      border-radius:999px;
    }

    .os-badge{
      display:inline-block;
      padding:4px 9px;
      border-radius:999px;
      background:#f5f3ff;
      font-weight:700;
      letter-spacing:.08em;
      font-size: calc(var(--fs) - 0.3px);
      color:#4c1d95;
      border:1px solid rgba(148,163,184,.6);
    }
    .os-badge span{ font-weight:800; }

    .soft-divider{
      height:1px;
      background:linear-gradient(90deg, transparent, var(--soft-line), transparent);
      margin-top:10px;
    }

    /* Tablas clave-valor */
    .kv-table td{
      padding:2px 1px;
    }
    .kv-table tr:not(:last-child) td{
      border-bottom:1px dashed rgba(209,213,219,0.7);
    }
    .k{
      width:32%;
      font-weight:600;
      color:#111827;
      padding-right:4px;
    }
    .v{
      width:auto;
      color:#111827;
    }

    /* INSPECCIÓN */
    .inspection-table{
      font-size: var(--fs);
    }
    .inspection-table th{
      font-weight:600;
      text-align:left;
      padding-top:4px;
      padding-bottom:4px;
      background:rgba(191,219,254,0.7);
      border-bottom:1px solid rgba(209,213,219,0.9);
    }
    .inspection-table td{
      padding-top:3px;
      padding-bottom:3px;
    }
    .inspection-table tbody tr:nth-child(odd){
      background: #f9fafb;
    }
    .inspection-table tbody tr:nth-child(even){
      background: #ffffff;
    }

    /* FOTO – grande y estandarizada */
    .photo-block{
      margin-bottom:6px;
    }
    .photo-label{
      font-weight:600;
      text-transform:uppercase;
      letter-spacing:.06em;
      font-size: calc(var(--fs) - 0.4px);
      color:#4b5563;
      margin-bottom:3px;
    }
    .photo{
      border-radius:10px;
      background: #f3f4ff;
      padding:6px;
      min-height: 150px;
      max-height: 220px;
      display:flex;
      align-items:center;
      justify-content:center;
      overflow:hidden;
      box-sizing:border-box;
    }
    .photo img{
      max-width:100%;
      max-height:205px;
      width:auto;
      height:auto;
      display:block;
    }
    .photo-caption{
      font-size: calc(var(--fs) - 0.2px);
      color:#4b5563;
      margin-top:4px;
      text-align:center;
    }

    .legend .row{
      display:flex;
      gap:5px;
      margin-bottom:2px;
      align-items:flex-start;
    }
    .box{
      width:8px;
      height:8px;
      border-radius:2px;
      border:1px solid #4b5563;
      display:inline-block;
      margin-right:4px;
    }
    .box.fill{ background:#4b5563; }

    /* FIRMAS – más espacio para firmar */
    .sign{
      margin-top:24px;
    }
    .sign td{
      text-align:center;
      padding-top:30px;
      padding-bottom:30px;
      border:none;
    }
    .line{
      width:82%;
      margin:0 auto 10px;
      border-top:1px solid #1f2937;
    }
    .sig-role{
      color:var(--muted);
    }

    /* LAYOUT columnas Inspección + Foto */
    .split td.col-left{ width:58%; padding-right:5px; }
    .split td.col-right{ width:42%; padding-left:5px; }

    /* FOOTER */
    .footer-container{
      position: fixed;
      bottom: 10px;
      left: 18mm;
      width: calc(100% - 36mm);
      text-align: left;
    }
    .footer{
      display:block;
      text-align:left;
      padding-top:6px;
      border-top:1px solid #e5e7eb;
      background:transparent;
    }
    .footer-table{
      table-layout:auto;
      width:100%;
      font-family: "Söhne", "Circular Std", "Poppins", system-ui, -apple-system,
                   "Segoe UI", "Helvetica Neue", Arial, sans-serif;
      font-size: 10.5px;
      color:#374151;
    }
    .footer-table td{
      padding:0 8px;
      vertical-align:middle;
    }
    .footer-name{
      font-weight:700;
      font-size:11.5px;
      color:#111827;
      letter-spacing:.06em;
    }
    .footer-role{
      color:#6b7280;
      font-size:10px;
    }
    .footer-label{
      color:#4b5563;
      font-weight:600;
      font-size:10px;
    }
    .sig-cell{ padding-right:16px; }

    .sig-img{
      display:block;
      max-width:130px;
      max-height:42px;
      width:auto;
      height:auto;
      margin-top:4px;
    }
  </style>
</head>
<body>

@php
  use Carbon\Carbon;

  $tecBase     = isset($tecnicoName) && $tecnicoName ? $tecnicoName : (optional($orden->user)->name ?? 'N/A');
  $tecnicoUP   = mb_strtoupper($tecBase, 'UTF-8');

  $clienteFull  = trim((optional($orden->cliente)->nombre ?? '').' '.(optional($orden->cliente)->apellido ?? ''));
  $telefonoCli  = $orden->cliente->telefono   ?? 'N/A';
  $direccionCli = $orden->cliente->direccion  ?? ($orden->cliente->comentarios ?? 'N/A');

  $fEntrada = $orden->fecha_entrada ? Carbon::parse($orden->fecha_entrada)->format('d/m/Y') : 'N/A';
  $fMtoDate = $orden->fecha_mantenimiento ? Carbon::parse($orden->fecha_mantenimiento) : null;
  $fMto     = $fMtoDate ? $fMtoDate->format('d/m/Y') : 'N/A';

  $proxRaw     = $orden->getRawOriginal('proximo_mantenimiento');
  $proxFecha   = null;
  $proxMeses   = null;

  if($proxRaw !== null && $proxRaw !== ''){
      if (is_numeric($proxRaw)) {
          $proxMeses = (int)$proxRaw;
          if ($fMtoDate) {
              $proxFecha = (clone $fMtoDate)->addMonths($proxMeses);
          }
      } else {
          try {
              $proxFecha = Carbon::parse($proxRaw);
          } catch (\Throwable $e) {
              $proxFecha = null;
          }
          if ($proxFecha && $fMtoDate) {
              $proxMeses = $fMtoDate->diffInMonths($proxFecha);
          }
      }
  }

  $proxTexto = 'N/A';
  if ($proxFecha instanceof \Carbon\Carbon) {
      $proxTexto = $proxFecha->format('d/m/Y') . ($proxMeses ? ' ('.$proxMeses.' meses)' : '');
  } elseif (is_numeric($proxRaw) && $fMtoDate) {
      $proxTexto = $proxMeses.' meses';
  }

  $hasDataUri = !empty($fotoDataUri);
  $hasAbsPath = !empty($fotoAbs) && file_exists($fotoAbs);

  $raw = $orden->mto_preventivo ?? [];
  $grouped = [];
  foreach ($raw as $key=>$val) {
    if (is_array($val) && isset($val['seccion'], $val['item'])) {
      $sec = (string)$val['seccion'];
      $grouped[$sec][] = ['item'=>$val['item'], 'estatus'=>$val['estatus'] ?? 'Revisado'];
    } elseif (is_array($val)) {
      foreach ($val as $v) {
        if (is_array($v) && isset($v['item'])) {
          $sec = is_string($key) ? $key : ($v['seccion'] ?? 'Sección');
          $grouped[$sec][] = ['item'=>$v['item'], 'estatus'=>$v['estatus'] ?? 'Revisado'];
        }
      }
    }
  }
  ksort($grouped);

  // Acciones (lo que ya tenías)
  $acciones = collect($orden->mto_realizado ?? [])->filter()->values()->all();

  // ✅ OBSERVACIONES (ajusta aquí si tu columna se llama diferente)
  $obsRaw = $orden->observaciones ?? $orden->observacion ?? $orden->comentarios ?? '';
  if (is_array($obsRaw)) {
    $obsRaw = implode("\n", array_filter($obsRaw));
  }
  $observacionesTexto = trim((string)$obsRaw);
@endphp

<div class="page">
  {{-- ========= HEADER ========= --}}
  <div class="brand-shell">
    <table class="w-100 brand-row">
      <tr>
        <td style="width:30%">
          @if(!empty($logoDataUri))
            <img src="{{ $logoDataUri }}" alt="Grupo MediBuy" class="logo">
          @endif
        </td>
        <td class="t-center" style="width:40%">
          <div class="doc-title upper">Orden de Servicio</div>
          <div class="doc-sub">Grupo MediBuy · Servicio Técnico</div>
          <div class="title-accent"></div>
        </td>
        <td class="t-right" style="width:30%">
          <span class="os-badge">
            OS&nbsp;# <span>{{ $orden->id }}</span>
          </span>
        </td>
      </tr>
    </table>
  </div>

  <div class="soft-divider"></div>

  {{-- ========= RESUMEN ========= --}}
  <div class="card card-soft-blue mb-5 avoid-break">
    <div class="section-header">Resumen</div>
    <table>
      <tr>
        <td style="width:55%; padding-right:6px;">
          <table class="kv-table">
            <tr><td class="k">Cliente</td><td class="v">{{ $clienteFull ?: 'N/A' }}</td></tr>
            <tr><td class="k">Representante</td><td class="v">{{ $tecnicoUP }}</td></tr>
            <tr><td class="k">Teléfono</td><td class="v">{{ $telefonoCli }}</td></tr>
            <tr><td class="k">Dirección</td><td class="v">{{ $direccionCli }}</td></tr>
          </table>
        </td>
        <td style="width:45%; padding-left:6px;">
          <table class="kv-table">
            <tr><td class="k">Fecha de mantto.</td><td class="v">{{ $fMto }}</td></tr>
            <tr><td class="k">Próximo mantto.</td><td class="v">{{ $proxTexto }}</td></tr>
            <tr><td class="k">Fecha de entrada</td><td class="v">{{ $fEntrada }}</td></tr>
          </table>
        </td>
      </tr>
    </table>
  </div>

  {{-- ========= EQUIPO ========= --}}
  <div class="card card-soft-mint mb-5 avoid-break">
    <div class="section-header">Descripción del equipo</div>
    <table>
      <tr>
        <td class="k">Equipo</td>
        <td class="v">{{ $orden->equipo ?? 'N/A' }}</td>
        <td class="semibold" style="width:18%">Marca</td>
        <td style="width:20%">{{ $orden->marca ?? 'N/A' }}</td>
      </tr>
      <tr>
        <td class="semibold">Modelo</td>
        <td>{{ $orden->modelo ?? 'N/A' }}</td>
        <td class="semibold">Serie</td>
        <td>{{ $orden->numero_serie ?? 'N/A' }}</td>
      </tr>
    </table>
  </div>

  {{-- ========= INSPECCIÓN + FOTO ========= --}}
  <table class="w-100 split avoid-break">
    <tr>
      <td class="col-left">
        <div class="card card-soft-lilac">
          <div class="section-header">Inspección preventiva de mantenimiento</div>

          @php $printed = 0; @endphp
          @foreach($grouped as $sec => $items)
            <table class="inspection-table mb-3">
              <thead>
                <tr>
                  <th style="width:70%">{{ strtoupper($sec) }}</th>
                  <th style="width:30%">Resultado</th>
                </tr>
              </thead>
              <tbody>
                @forelse($items as $it)
                  <tr>
                    <td>{{ $it['item'] }}</td>
                    <td>{{ $it['estatus'] }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="2" class="t-center muted">— Sin datos —</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
            @php $printed++; @endphp
          @endforeach

          @if($printed===0)
            <table class="inspection-table">
              <thead>
                <tr>
                  <th style="width:70%">Inspección</th>
                  <th style="width:30%">Resultado</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td colspan="2" class="t-center muted">— Sin datos —</td>
                </tr>
              </tbody>
            </table>
          @endif

          {{-- ✅ AQUÍ SE PASAN LAS ACCIONES (debajo, como el espacio de la imagen) --}}
          <div class="sub-header">Acciones realizadas</div>
          @if(!empty($acciones))
            <div class="note-box">
              <ul style="margin:0; padding-left:14px;">
                @foreach($acciones as $a)
                  <li>{{ $a }}</li>
                @endforeach
              </ul>
            </div>
          @else
            <div class="note-box">
              <div class="small muted">— Sin acciones registradas —</div>
            </div>
          @endif
        </div>
      </td>

      <td class="col-right">
        <div class="card mb-3">
          <div class="photo-block">
            <div class="photo-label">Fotografía del equipo</div>
            <div class="photo">
              @if($hasDataUri)
                <img src="{{ $fotoDataUri }}" alt="Foto del equipo">
              @elseif($hasAbsPath)
                <img src="{{ $fotoAbs }}" alt="Foto del equipo">
              @else
                <div class="small muted">Sin imagen</div>
              @endif
            </div>
            <div class="photo-caption">
              Equipo @if($orden->numero_serie) · Serie: {{ $orden->numero_serie }} @endif
            </div>
          </div>
        </div>

        <div class="card card-soft-blue">
          <div class="section-header">Tipo de mantenimiento</div>
          <table>
            <tr>
              <td style="width:45%; padding-right:4px;">
                @php
                  $isPreventivo = true;
                  $isCorrectivo = in_array('correctivo', array_map('mb_strtolower', $acciones));
                @endphp
                <div class="legend">
                  <div class="row"><span class="box {{ $isPreventivo ? 'fill' : '' }}"></span> Preventivo</div>
                  <div class="row"><span class="box {{ $isCorrectivo ? 'fill' : '' }}"></span> Correctivo</div>
                </div>
              </td>

              {{-- ✅ AQUÍ VAN LAS OBSERVACIONES (antes estaban las acciones) --}}
              <td style="width:55%; padding-left:4px;">
                @if($observacionesTexto !== '')
                  <div class="note-box">{!! nl2br(e($observacionesTexto)) !!}</div>
                @else
                  <div class="note-box">
                    <div class="small muted">— Sin observaciones —</div>
                  </div>
                @endif
              </td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  </table>

  <div class="section-divider"></div>

  {{-- ========= FIRMAS ========= --}}
  <table class="w-100 sign avoid-break">
    <tr>
      <td style="width:50%">
        <div class="line"></div>
        <div class="bold">{{ $tecnicoUP }}</div>
        <div class="xs sig-role">RESPONSABLE DEL MANTENIMIENTO</div>
      </td>
      <td style="width:50%">
        <div class="line"></div>
        <div class="bold">{{ $clienteFull ?: 'N/A' }}</div>
        <div class="xs sig-role">CLIENTE</div>
      </td>
    </tr>
  </table>
</div>

{{-- ========= FOOTER ========= --}}
<div class="footer-container">
  <div class="footer">
    <table class="footer-table" cellpadding="0" cellspacing="0">
      <tr>
        <td class="sig-cell" valign="middle">
          <table cellpadding="0" cellspacing="0">
            <tr>
              <td valign="middle" style="padding-right:10px;">
                @if(!empty($firmaDataUri))
                  <img src="{{ $firmaDataUri }}" alt="Firma" class="sig-img">
                @endif
              </td>
              <td valign="middle">
                <div class="footer-name">{{ mb_strtoupper('Anahí Téllez','UTF-8') }}</div>
                <div class="footer-role">Gerente General</div>
              </td>
            </tr>
          </table>
        </td>
        <td valign="middle" style="text-align:right;">
          <div><span class="footer-label">Tel:</span> +52 722 448 5191</div>
          <div><span class="footer-label">Email:</span> ventas@grupomedibuy.com</div>
          <div><span class="footer-label">Web:</span> grupomedibuy.com</div>
          <div><span class="footer-label">Ubicación:</span> Estado de México C.P. 52060</div>
        </td>
      </tr>
    </table>
  </div>
</div>

</body>
</html>
