{{-- resources/views/pdfs/transaction.blade.php --}}
@php
  /** @var \App\Models\CashTransaction $trx */
  $labels = ['allocation'=>'Entrada','disbursement'=>'Entrega','return'=>'Devolución'];
  $tipo   = $labels[$trx->type] ?? ucfirst($trx->type);
  $folio  = 'TRX-'.str_pad((string)$trx->id, 6, '0', STR_PAD_LEFT);

  // Helpers para Dompdf (embebemos imágenes como base64)
  if (!function_exists('pdf_img_b64')) {
      function pdf_img_b64(?string $storagePath): ?string {
          if (!$storagePath) return null;
          $full = public_path('storage/'.$storagePath);
          if (!is_file($full)) return null;
          $ext  = strtolower(pathinfo($full, PATHINFO_EXTENSION));
          $mime = $ext === 'png' ? 'image/png' : (in_array($ext, ['jpg','jpeg']) ? 'image/jpeg' : null);
          if (!$mime) return null;
          try { $data = base64_encode(@file_get_contents($full)); } catch (\Throwable $e) { $data = null; }
          return $data ? "data:{$mime};base64,{$data}" : null;
      }
  }
  if (!function_exists('pdf_is_image')) {
      function pdf_is_image(string $path): bool {
          return (bool) preg_match('/\.(png|jpe?g)$/i', $path);
      }
  }
  if (!function_exists('pdf_fname')) {
      function pdf_fname(string $p): string { return basename($p); }
  }

  // Firmas (si no hay, no se muestran)
  $sigMgr = pdf_img_b64($trx->manager_signature_path);
  $sigUsr = pdf_img_b64($trx->counterparty_signature_path);

  // Evidencias (imágenes embebidas, otros archivos listados)
  $evid = is_array($trx->evidence_paths ?? null) ? $trx->evidence_paths : [];
  $evImgs = array_values(array_filter($evid, fn($p)=>pdf_is_image($p) && pdf_img_b64($p)));
  $evDocs = array_values(array_filter($evid, fn($p)=>!pdf_is_image($p)));
@endphp
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Recibo {{ $folio }}</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <style>
    /* --------- Base/Tipografía segura --------- */
    *{ box-sizing:border-box; }
    html,body{ margin:0; padding:0; font-family: DejaVu Sans, sans-serif; color:#0f172a; }
    body{ font-size:12px; line-height:1.35; }

    /* Ancho seguro para A4/Letter @96dpi (≈794/816px). Usamos 720px + paddings */
    .wrap{ width:100%; max-width:720px; margin:0 auto; padding:22px 24px; }

    .muted{ color:#64748b; }
    .small{ font-size:11px; }
    .tag{ display:inline-block; padding:4px 10px; border-radius:999px; background:#eef2ff; color:#3730a3; font-weight:700; font-size:11px; border:1px solid #c7d2fe; }

    /* --------- Header --------- */
    .header{
      display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:16px;
      border-bottom:1px solid #e5e7eb; padding-bottom:10px;
    }
    .brand{ display:flex; align-items:center; gap:10px; }
    .brand-logo{ width:40px; height:40px; border-radius:8px; background:#0ea5e9; display:inline-block; }
    .brand-title{ font-size:16px; font-weight:800; letter-spacing:.3px; }
    .folio{ text-align:right; }
    .folio .code{ font-weight:800; font-size:14px; }
    .folio .date{ color:#64748b; font-size:12px; }

    /* --------- Box / grids --------- */
    .box{
      border:1px solid #e5e7eb; border-radius:10px; padding:12px 14px; margin-bottom:10px; background:#fff;
      page-break-inside: avoid;
    }
    .grid{ display:flex; flex-wrap:wrap; gap:10px; }
    .col{ flex:1 1 210px; }
    .label{ color:#64748b; font-size:10px; text-transform:uppercase; letter-spacing:.04em; margin-bottom:4px; }
    .value{ font-size:13px; font-weight:700; }

    .amount{ font-size:22px; font-weight:900; color:#111827; letter-spacing:.2px; }

    table.meta{ width:100%; border-collapse:separate; border-spacing:0; }
    table.meta td{ padding:7px 8px; vertical-align:top; }
    table.meta tr + tr td{ border-top:1px dashed #e5e7eb; }

    /* --------- Firmas --------- */
    .signs{ display:flex; gap:12px; margin-top:10px; }
    .sign{ flex:1 1 0; border:1px dashed #cbd5e1; border-radius:10px; padding:8px 10px; min-height:120px; page-break-inside: avoid; }
    .sign .line{ height:60px; display:flex; align-items:center; justify-content:center; }
    .sign .line img{ max-height:58px; max-width:100%; }
    .sign .who{ margin-top:6px; border-top:1px solid #e5e7eb; padding-top:6px; }
    .sign .who .name{ font-weight:700; }
    .sign .who .role{ color:#64748b; font-size:10px; }

    /* --------- Evidencias --------- */
    .ev{ margin-top:10px; }
    .ev-grid{ display:flex; flex-wrap:wrap; gap:8px; }
    .ev-card{
      width: calc(33.333% - 6px); border:1px solid #e5e7eb; border-radius:8px; padding:6px; background:#fff; page-break-inside: avoid;
    }
    .ev-card img{ width:100%; height:140px; object-fit:cover; border-radius:6px; }
    .ev-list{ margin:6px 0 0 18px; padding:0; }

    .footer{ margin-top:12px; color:#64748b; font-size:10px; text-align:center; }

    /* --------- Colores por tipo --------- */
    .type-allocation .tag{ background:#eaf7ef; border-color:#cdebd7; color:#166534; }
    .type-disbursement .tag{ background:#fdecec; border-color:#f2c8c8; color:#b91c1c; }
    .type-return .tag{ background:#eaf7fb; border-color:#c9e7f0; color:#0e7490; }
  </style>
</head>
<body class="type-{{ $trx->type }}">
  <div class="wrap">
    {{-- Header --}}
    <div class="header">
      <div class="brand">
        {{-- Reemplaza por tu imagen si quieres: <img class="brand-logo" src="{{ public_path('images/logo.png') }}"> --}}
        <span class="brand-logo"></span>
        <div>
          <div class="brand-title">MEDIBUY</div>
          <div class="small muted">Recibo de {{ strtolower($tipo) }}</div>
        </div>
      </div>
      <div class="folio">
        <div class="code">{{ $folio }}</div>
        <div class="date">Generado: {{ now()->format('Y-m-d H:i') }}</div>
        <div class="tag" style="margin-top:6px;">{{ $tipo }}</div>
      </div>
    </div>

    {{-- Resumen --}}
    <div class="box">
      <div class="grid">
        <div class="col">
          <div class="label">Monto</div>
          <div class="amount">${{ number_format((float)$trx->amount, 2) }} MXN</div>
        </div>
        <div class="col">
          <div class="label">Fecha de operación</div>
          <div class="value">{{ $trx->created_at->format('Y-m-d H:i') }}</div>
        </div>
        <div class="col">
          <div class="label">Estatus</div>
          <div class="value">
            @if($trx->type === 'disbursement')
              {{ $trx->acknowledged_at ? 'Autorizada por usuario' : 'Pendiente de firma' }}
            @else
              Registrada
            @endif
          </div>
        </div>
      </div>

      <table class="meta" style="margin-top:6px;">
        <tr>
          <td width="28%" class="label">Encargado (admin)</td>
          <td class="value">{{ $trx->manager->name ?? ('ID '.$trx->manager_id) }}</td>
        </tr>
        <tr>
          <td class="label">{{ $trx->type === 'allocation' ? 'Jefa (admin)' : 'Usuario' }}</td>
          <td class="value">{{ $trx->counterparty->name ?? ('ID '.$trx->counterparty_id) }}</td>
        </tr>
        <tr>
          <td class="label">Concepto</td>
          <td class="value">{{ $trx->purpose ?: '—' }}</td>
        </tr>
      </table>
    </div>

    {{-- Firmas: solo mostramos las que existan --}}
    @php
      $showUser = (bool) $sigUsr;
      $showMgr  = (bool) $sigMgr;
    @endphp
    @if($showUser || $showMgr)
      <div class="signs">
        @if($showUser)
          <div class="sign">
            <div class="line"><img src="{{ $sigUsr }}" alt="Firma usuario"></div>
            <div class="who">
              <div class="name">{{ $trx->counterparty->name ?? 'Usuario' }}</div>
              <div class="role">{{ $trx->type === 'allocation' ? 'Jefa (admin)' : 'Usuario' }}</div>
              @if($trx->acknowledged_at)
                <div class="small muted">Firmado: {{ \Carbon\Carbon::parse($trx->acknowledged_at)->format('Y-m-d H:i') }}</div>
              @endif
            </div>
          </div>
        @endif
        @if($showMgr)
          <div class="sign">
            <div class="line"><img src="{{ $sigMgr }}" alt="Firma encargado"></div>
            <div class="who">
              <div class="name">{{ $trx->manager->name ?? 'Encargado' }}</div>
              <div class="role">Encargado (admin)</div>
            </div>
          </div>
        @endif
      </div>
    @endif

    {{-- Evidencias: imágenes embebidas + lista de otros archivos (PDF, etc.) --}}
    @if(count($evImgs) || count($evDocs))
      <div class="box ev">
        <div class="label" style="margin-bottom:6px;">Evidencias adjuntas</div>

        @if(count($evImgs))
          <div class="ev-grid">
            @foreach($evImgs as $p)
              @php $src = pdf_img_b64($p); @endphp
              @if($src)
                <div class="ev-card">
                  <img src="{{ $src }}" alt="Evidencia">
                </div>
              @endif
            @endforeach
          </div>
        @endif

        @if(count($evDocs))
          <ol class="ev-list small">
            @foreach($evDocs as $p)
              <li>{{ pdf_fname($p) }}</li>
            @endforeach
          </ol>
          <div class="small muted" style="margin-top:4px;">* Archivos no imagen se listan por nombre.</div>
        @endif
      </div>
    @endif

    <div class="footer">
      Documento generado automáticamente por el sistema de caja. {{ $folio }} · {{ request()->getHttpHost() }}
    </div>
  </div>
</body>
</html>
