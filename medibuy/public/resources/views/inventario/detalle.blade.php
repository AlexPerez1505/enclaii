@extends('layouts.app') 
@section('title','Ficha de equipo')
@section('titulo','Detalle')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@php
  $router = app('router');

  $estadoActual = $registro->estado_proceso ?? 'registro';

  // Fecha del último cambio del estado actual
  $fechaUltimoEstado = null;
  try {
    if ($estadoActual === 'registro') {
      $fechaUltimoEstado = $registro->created_at;
    } else {
      $fechaUltimoEstado = optional(
        $registro->relationLoaded('procesos')
          ? $registro->procesos->where('tipo_proceso',$estadoActual)->sortByDesc('created_at')->first()
          : $registro->procesos()->where('tipo_proceso',$estadoActual)->latest('created_at')->first()
      )->created_at;
    }
  } catch (\Throwable $e) { $fechaUltimoEstado = null; }

  $badgeClass = match($estadoActual){
    'hojalateria'   => 'badge-hojalateria',
    'mantenimiento' => 'badge-mantenimiento',
    'stock'         => 'badge-stock',
    'vendido'       => 'badge-vendido',
    'defectuoso'    => 'badge-defectuoso',
    default         => 'badge-registro'
  };

  $barcodeUrl = $router->has('registros.imprimir-barcode')
    ? route('registros.imprimir-barcode', $registro->id) : '#';

  // Pasos
  $pasos   = $pasos ?? ['hojalateria','mantenimiento','stock','vendido'];
  $bonitos = ['hojalateria'=>'Hojalatería','mantenimiento'=>'Mantenimiento','stock'=>'Stock','vendido'=>'Vendido'];
  $routes  = [];
  foreach($pasos as $p){
    $routes[$p] = $router->has('proceso.'.$p) ? route('proceso.'.$p,$registro->id) : null;
  }

  // Componentes incluidos (si no vinieron del controlador)
  if (!isset($componentes)) {
    $componentes = \Illuminate\Support\Facades\DB::table('inv_registro_componentes')
      ->leftJoin('inv_componentes_cat','inv_componentes_cat.id','=','inv_registro_componentes.componente_id')
      ->where('inv_registro_componentes.registro_id',$registro->id)
      ->select([
        'inv_registro_componentes.id',
        'inv_componentes_cat.id as componente_id',
        'inv_registro_componentes.nombre_cache as nombre',
        'inv_registro_componentes.cantidad',
        'inv_registro_componentes.incluido',
        'inv_registro_componentes.notas',
      ])
      ->orderBy('inv_registro_componentes.nombre_cache')
      ->get();
  }

  // URLs Edit / Delete
  $editUrl = $router->has('registros.edit')
    ? route('registros.edit',$registro->id)
    : ($router->has('inventario.editar')
        ? route('inventario.editar',$registro->id)
        : url('/registros/'.$registro->id.'/edit'));

  $deleteAction = $router->has('registros.destroy')
    ? route('registros.destroy',$registro->id)
    : ($router->has('registros.eliminar')
        ? route('registros.eliminar',$registro->id)
        : url('/registros/'.$registro->id));

  // Config para Alpine (JSON seguro)
  $stepperCfg = [
    'pasoKeys' => array_values($pasos),
    'labels'   => array_map(fn($p)=>$bonitos[$p] ?? ucfirst($p), $pasos),
    'routes'   => $routes,
  ];
@endphp

<style>
:root{
  --bg:#eaebec; --panel:#ffffff; --ink:#0f172a; --muted:#667085; --line:#e7eaf0;
  --pblue:#dbeafe; --pblue-700:#1d4ed8; --shadow:0 12px 36px rgba(2,6,23,.09); --radius:20px;
}
*,*::before,*::after{ box-sizing:border-box; }
body{ background:var(--bg); color:var(--ink); font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; }
[x-cloak]{ display:none !important; }

/* HERO */
.hero{
  margin-top:110px;
  background:
    radial-gradient(1200px 150px at 0% 0%, rgba(96,165,250,.18), transparent 40%),
    radial-gradient(1200px 150px at 100% 0%, rgba(14,165,233,.14), transparent 40%),
    #fff;
  border:1px solid var(--line); border-radius:18px; padding:16px 18px;
  box-shadow:var(--shadow);
}
.hero h1{ margin:0; font-weight:800; letter-spacing:-.02em; }
.hero .subtle{ color:var(--muted); }
.hero-actions{ display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-top:10px; }
.btn{ display:inline-flex; align-items:center; gap:.45rem; padding:10px 14px; border-radius:14px; border:1px solid var(--line); background:#fff; color:#334155; font-weight:800; text-decoration:none; cursor:pointer; transition:transform .04s ease, box-shadow .2s ease, background .2s ease; }
.btn:active{ transform:translateY(1px) }
.btn-blue{ background:var(--pblue); color:#0b2a4a; border-color:rgba(96,165,250,.45) }
.btn-ghost{ background:#fff; color:#334155; }
.btn-danger-soft{ background:#fee2e2; color:#7f1d1d; border-color:#fecaca; }
.btn-danger-soft:hover{ background:#fde0e0 }

/* BADGES */
.badge-state{
  display:inline-flex; align-items:center; gap:.35rem;
  padding:6px 10px; border-radius:999px; font-weight:800; font-size:12px; letter-spacing:.1px;
  border:1px solid transparent;
}
.badge-registro     { background:#f1f5f9; color:#334155; border-color:#e2e8f0; }
.badge-hojalateria  { background:#e0f2fe; color:#1d4ed8; border-color:#bfdbfe; }
.badge-mantenimiento { background:#fef9c3; color:#eab308; border-color:#fde68a; }
.badge-stock        { background:#dcfce7; color:#065f46; border-color:#bbf7d0; }
.badge-vendido      { background:#ffe4e6; color:#9f1239; border-color:#fecdd3; }
.badge-defectuoso   { background:#ffedd5; color:#c2410c; border-color:#fdba74; }

.text-muted-ux{ color:var(--muted) }

/* CARDS */
.card-soft{ background:#fff; border:1px solid var(--line); border-radius:var(--radius); box-shadow:var(--shadow); }
.card-soft .head{ padding:16px 18px; border-bottom:1px solid var(--line); display:flex; justify-content:space-between; align-items:center; gap:12px; }
.card-soft .body{ padding:18px; }

/* GALERÍA */
.thumb{ width:64px; height:64px; border:1px solid var(--line); border-radius:12px; overflow:hidden; display:grid; place-items:center; background:#f8fafc; cursor:pointer; transition:transform .12s ease, box-shadow .2s ease; }
.thumb:hover{ transform:translateY(-1px); box-shadow:0 12px 24px rgba(2,6,23,.08); }
.thumb img, .thumb video{ width:100%; height:100%; object-fit:cover; }

/* KPIs: 2 por fila */
.kpis{ display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
.kpi{ background:#f8fafc; border:1px solid var(--line); border-radius:14px; padding:12px; min-width:0; }
.kpi .label{ color:#6b7280; font-size:12px; overflow-wrap:anywhere; word-break:break-word; }
.kpi .val{ font-weight:800; white-space:normal; overflow-wrap:anywhere; word-break:break-word; }

/* Tabla componentes */
.comp-table{ width:100%; border-collapse:separate; border-spacing:0; }
.comp-table th, .comp-table td{ padding:10px 12px; font-size:14px; border-bottom:1px solid var(--line); }
.comp-table thead th{ color:#6b7280; background:#f5f7ff; font-weight:700; }
.comp-table .qty{ font-weight:800; }
.comp-empty{ color:var(--muted) }

/* STEPPER */
.stepper{ display:flex; gap:10px; overflow-x:auto; padding:8px; -webkit-overflow-scrolling:touch; }
.stepper::-webkit-scrollbar{ height:8px; } .stepper::-webkit-scrollbar-thumb{ background:#e6ecff; border-radius:999px }
.step-pill{
  flex:0 0 auto; border:1px solid var(--line); background:#fff; color:#334155; border-radius:999px;
  padding:8px 12px; font-weight:800; display:inline-flex; align-items:center; gap:8px; cursor:pointer;
  transition:background .2s ease, border-color .2s ease, transform .04s ease;
}
.step-pill.active{ background:#eef3ff; border-color:#cfe0ff; color:#193259 }
.step-pill .dot{ width:10px; height:10px; border-radius:999px; background:#a3bffa }
.step-pill.done .dot{ background:#22c55e }

.panel{ border:1px solid var(--line); background:#fff; border-radius:16px; padding:14px; }
.panel + .panel{ margin-top:10px; }

/* SweetAlert2 media */
.swal2-popup.media{ padding:0; border-radius:16px; overflow:hidden; box-shadow:0 24px 60px rgba(2,6,23,.25); }
.media-wrap{ background:#fff; padding:10px; }
.media-wrap img,.media-wrap video{ width:100%; max-height:80vh; object-fit:contain; border-radius:12px; }

/* FAB + Bottom Sheet (móvil) */
.fab{ position:fixed; right:16px; bottom:18px; z-index:1100; display:none; }
@media (max-width:576px){ .fab{ display:block; } }
.fab-btn{
  width:56px; height:56px; border-radius:999px; border:1px solid #e6ebf5; background:#ffffff;
  display:grid; place-items:center; box-shadow:0 12px 28px rgba(2,6,23,.18);
  transition:transform .06s ease, box-shadow .2s ease, background .2s ease;
}
.fab-btn:active{ transform:translateY(1px) }
.fab-btn i{ font-size:22px; color:#0f172a; opacity:.8; }

/* Ocultar FAB cuando el sheet esté abierto */
body.sheet-open { overflow:hidden; }
body.sheet-open .fab{ display:none !important; }

.sheet-backdrop{ position:fixed; inset:0; background:rgba(15,23,42,.35); backdrop-filter: blur(6px); opacity:0; pointer-events:none; transition:.2s; z-index:1098; }
.sheet-backdrop.show{ opacity:1; pointer-events:auto; }
.sheet{
  position:fixed; left:0; right:0; bottom:-100%; z-index:1099; background:#fff;
  border-radius:18px 18px 0 0; box-shadow:0 -20px 40px rgba(2,6,23,.16);
  padding:16px; transition:bottom .28s ease; will-change:bottom;
}
.sheet.show{ bottom:0; }
.sheet .grab{ width:56px; height:6px; background:#e5e7eb; border-radius:999px; margin:6px auto 14px; }
.sheet .title{ font-weight:800; margin-bottom:12px; }
.sheet .grid{ display:grid; gap:10px; }
.sheet .row2{ display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.action{
  display:flex; align-items:center; gap:12px; padding:14px; border-radius:14px; border:1px solid var(--line); background:#fff;
  text-decoration:none; font-weight:800; color:#111827; width:100%;
}
.action .ico{ width:38px; height:38px; border-radius:10px; display:grid; place-items:center; background:#f8fafc; color:#0f172a; }
</style>

{{-- Stepper global (definido ANTES de usarlo) --}}
<script>
window.Stepper = function(cfg){
  return {
    pasoKeys: cfg?.pasoKeys || [],
    labels:   cfg?.labels   || [],
    routes:   cfg?.routes   || {},
    current:  0,
    init(){ this.current = 0; }, // ver cualquier paso
    max(){ return this.labels.length - 1; },
    go(i){
      i = Number(i);
      if (!Number.isFinite(i)) return;
      if (i < 0) i = 0;
      if (i > this.max()) i = this.max();
      this.current = i;
    },
    next(){ this.go(this.current + 1); },
    prev(){ this.go(this.current - 1); },
  };
};
</script>

<div class="container animated-entry">

  {{-- HERO --}}
  <div class="hero mb-3" style="margin-top:25px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap:3">
      <div class="d-flex align-items-center gap-3">
        <a href="{{ Route::has('inventario.index') ? route('inventario.index') : url('/inventario') }}" class="btn btn-ghost" title="Volver">
          <i class="bi bi-arrow-left"></i>
        </a>
        <div>
          <h1 class="h4 mb-0">Ficha de equipo</h1>
          <div class="subtle">
            <strong>
              {{ mb_strtoupper($registro->tipo_equipo ?? '-', 'UTF-8') }}
              @if($registro->subtipo_equipo)
                · {{ mb_strtoupper($registro->subtipo_equipo, 'UTF-8') }}
              @endif
            </strong>
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">
        <span class="badge-state {{ $badgeClass }}">
          <i class="bi bi-circle-fill" style="font-size:.55rem"></i>
          <span class="text-capitalize">{{ $estadoActual }}</span>
        </span>
        <span class="text-muted-ux small ms-2">
          Último cambio: {{ $fechaUltimoEstado ? $fechaUltimoEstado->format('Y-m-d H:i') : '—' }}
        </span>
      </div>
    </div>

    {{-- Acciones (desktop) --}}
    <div class="hero-actions">
      <a class="btn btn-blue d-none d-sm-inline-flex" target="_blank" href="{{ $barcodeUrl }}"><i class="bi bi-upc-scan"></i> Imprimir etiqueta</a>
      @if(!empty($registro->documentoPDF))
        <a class="btn btn-ghost d-none d-sm-inline-flex" target="_blank" href="{{ $registro->documentoPDF }}"><i class="bi bi-file-earmark-pdf"></i> Ver PDF</a>
      @endif
      <a href="{{ $editUrl }}" class="btn btn-ghost d-none d-sm-inline-flex">
        <i class="bi bi-pencil-square"></i> Editar
      </a>
      <button type="button" id="btnDelete" class="btn btn-danger-soft d-none d-sm-inline-flex">
        <i class="bi bi-trash3"></i> Eliminar
      </button>
    </div>
  </div>

  <div class="row gy-4 gx-4">
    {{-- IZQUIERDA: Ficha técnica + Componentes + Firma --}}
    <div class="col-12 col-lg-5">
      <div class="card-soft">
        <div class="head">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-cpu"></i>
            <strong>Ficha técnica</strong>
          </div>
        </div>
        <div class="body">
          {{-- Galería del registro --}}
          <div class="d-flex flex-wrap gap-2 mb-3">
            @php $thumbs = array_filter([$registro->evidencia1, $registro->evidencia2, $registro->evidencia3]); @endphp
            @forelse($thumbs as $src)
              <div class="thumb" onclick="previewImage('{{ $src }}')" title="Ver">
                <img src="{{ $src }}" alt="Evidencia">
              </div>
            @empty
              <div class="text-muted-ux small">Sin imágenes</div>
            @endforelse

            @if(!empty($registro->video))
              <div class="thumb" onclick="previewVideo('{{ $registro->video }}')" title="Reproducir">
                <i class="bi bi-play-btn" style="font-size:1.4rem;opacity:.65"></i>
              </div>
            @endif
          </div>

          {{-- KPIs (2 por fila) --}}
          <div class="kpis mb-3">
            <div class="kpi"><div class="label">Marca</div><div class="val">{{ $registro->marca ?? '—' }}</div></div>
            <div class="kpi"><div class="label">Modelo</div><div class="val">{{ $registro->modelo ?? '—' }}</div></div>
          </div>
          <div class="kpis mb-3">
            <div class="kpi"><div class="label">Serie</div><div class="val">{{ $registro->numero_serie ?? '—' }}</div></div>
            <div class="kpi"><div class="label">Año</div><div class="val">{{ $registro->anio ?? '—' }}</div></div>
          </div>
          <div class="kpis mb-3">
            <div class="kpi"><div class="label">Fecha adquisición</div><div class="val">{{ optional($registro->fecha_adquisicion)->format('Y-m-d') ?? '—' }}</div></div>
            <div class="kpi"><div class="label">Registrado por</div><div class="val">{{ $registro->user_name ?? '—' }}</div></div>
          </div>

          <div class="panel mb-2">
            <div class="fw-bold mb-1">Descripción</div>
            <div class="text-muted-ux">{{ $registro->descripcion ?? '—' }}</div>
          </div>
          @if($registro->observaciones)
            <div class="panel">
              <div class="fw-bold mb-1">Observaciones</div>
              <div class="text-muted-ux">{{ $registro->observaciones }}</div>
            </div>
          @endif
        </div>
      </div>

      {{-- COMPONENTES --}}
      <div class="card-soft mt-3">
        <div class="head">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-puzzle"></i>
            <strong>Componentes incluidos</strong>
          </div>
          <span class="badge-state badge-registro">{{ $componentes->count() }} ítem(s)</span>
        </div>
        <div class="body">
          @if($componentes->isEmpty())
            <div class="comp-empty small">No hay componentes registrados para este equipo.</div>
          @else
            <div class="table-responsive">
              <table class="comp-table">
                <thead>
                  <tr>
                    <th>Componente</th>
                    <th class="text-center">Cant.</th>
                    <th>Notas</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($componentes as $c)
                    <tr>
                      <td>{{ $c->nombre }}</td>
                      <td class="text-center qty">{{ (int)$c->cantidad }}</td>
                      <td class="text-muted-ux">{{ $c->notas ?: '—' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

      {{-- Firma --}}
      @if(!empty($registro->firma_digital))
        <div class="card-soft mt-3">
          <div class="head"><strong><i class="bi bi-pencil me-1"></i> Firma digital</strong></div>
          <div class="body">
            <div class="thumb" style="width:100%; height:auto; max-height:200px; border-radius:14px; cursor:pointer"
                 onclick="previewImage('{{ $registro->firma_digital }}')">
              <img src="{{ $registro->firma_digital }}" alt="Firma" style="object-fit:contain;background:#fff">
            </div>
          </div>
        </div>
      @endif
    </div>

    {{-- DERECHA: Procesos (x-data usa data-cfg) --}}
    <div class="col-12 col-lg-7"
         x-data="Stepper(JSON.parse($el.dataset.cfg))"
         x-init="init()"
         data-cfg='@json($stepperCfg, JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT)'>

      <div class="card-soft">
        <div class="head">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-diagram-3"></i>
            <strong>Procesos del equipo</strong>
          </div>
          <div class="small text-muted-ux">Puedes ver cualquier paso. (“Vendido” solo se hace en remisión).</div>
        </div>

        <div class="body">
          {{-- Stepper --}}
          <div class="stepper mb-2">
            @foreach($pasos as $idx => $paso)
              @php $isDone = !empty($procesos[$paso] ?? null); @endphp
              <button type="button"
                      class="step-pill {{ $isDone ? 'done' : '' }}"
                      :class="{'active': current === {{ $idx }}}"
                      @click="go({{ $idx }})">
                <span class="dot"></span>
                {{ $bonitos[$paso] ?? ucfirst($paso) }}
              </button>
            @endforeach
          </div>

          {{-- Paneles --}}
          @foreach($pasos as $idx => $paso)
            @php
              $proceso   = $procesos[$paso] ?? null;
              $label     = $bonitos[$paso] ?? ucfirst($paso);
              $isVendido = ($paso === 'vendido');

              $evids = [];
              if ($proceso) {
                for ($e=1; $e<=3; $e++) {
                  $k = "evidencia$e";
                  if (!empty($proceso->$k)) $evids[] = asset('storage/'.$proceso->$k);
                }
              }
            @endphp

            <div x-show="current === {{ $idx }}"
                 x-transition.opacity.scale.origin.top.duration.150ms
                 x-cloak>
              <div class="panel">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="badge-state {{ $proceso ? 'badge-stock' : 'badge-registro' }}">
                    <i class="bi bi-circle-fill" style="font-size:.55rem"></i>
                    {{ $proceso ? 'Completado' : 'Pendiente' }}
                  </span>
                  <span class="small text-muted-ux">
                    @if($proceso && $proceso->created_at)
                      · Hecho el {{ $proceso->created_at->format('Y-m-d H:i') }}
                    @endif
                  </span>
                </div>

                @if(!$proceso)
                  <div class="text-muted-ux mb-3">Este proceso aún no ha sido registrado.</div>
                  @if(!$isVendido)
                    @if($routes[$paso])
                      <a href="{{ $routes[$paso] }}" class="btn btn-blue">
                        <i class="bi bi-plus-lg"></i> Completar {{ $label }}
                      </a>
                    @else
                      <button type="button" class="btn btn-ghost" disabled>
                        <i class="bi bi-lock"></i> Ruta no definida
                      </button>
                    @endif
                  @endif
                @else
                  {{-- Descripción --}}
                  <div class="mb-3">
                    <div class="fw-bold mb-1">Descripción</div>
                    <div class="text-muted-ux">{{ $proceso->descripcion_proceso ?? '—' }}</div>
                  </div>

                  {{-- ===================== CHECKLIST DEL PROCESO ===================== --}}
                  @php
                    // Acepta array casteado o string JSON
                    $raw = $proceso->checklist ?? null;
                    $sections = null;

                    if (is_array($raw)) {
                      $sections = $raw;
                    } elseif (is_string($raw)) {
                      $sections = json_decode($raw, true);
                    }

                    // KPIs
                    $tot = 0; $ok = 0; $bad = 0; $na = 0;
                    if (is_array($sections)) {
                      foreach ($sections as $sec) {
                        $items = isset($sec['items']) && is_array($sec['items']) ? $sec['items'] : [];
                        foreach ($items as $it) {
                          $tot++;
                          $done = $it['done'] ?? ($it['pass'] ?? ($it['ok'] ?? null));
                          if (is_string($done)) {
                            $done = in_array(strtolower($done), ['1','true','ok','si','sí','yes']);
                          }
                          if ($done === true)      $ok++;
                          elseif ($done === false) $bad++;
                          else                     $na++;
                        }
                      }
                    }
                  @endphp

                  @if(is_array($sections) && $tot > 0)
                    <div class="fw-bold mb-1">Checklist</div>
                    <div class="mb-2 small text-muted-ux">
                      Completados: <strong>{{ $ok }}</strong> / {{ $tot }}
                      @if($bad>0) · No realizados: <strong class="text-danger">{{ $bad }}</strong>@endif
                      @if($na>0)  · N/A: <strong>{{ $na }}</strong>@endif
                    </div>

                    @foreach($sections as $sec)
                      @php
                        $title = $sec['name'] ?? $sec['title'] ?? 'Sección';
                        $items = isset($sec['items']) && is_array($sec['items']) ? $sec['items'] : [];
                      @endphp

                      @if(count($items))
                        <div class="panel" style="padding:10px; margin-bottom:8px;">
                          <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-semibold" style="text-transform:capitalize">
                              {{ $title }}
                            </div>
                          </div>

                          <div class="d-flex flex-column gap-1">
                            @foreach($items as $it)
                              @php
                                $labelIt = $it['label'] ?? $it['nombre'] ?? 'Ítem';
                                $doneIt  = $it['done'] ?? ($it['pass'] ?? ($it['ok'] ?? null));
                                if (is_string($doneIt)) {
                                  $doneIt = in_array(strtolower($doneIt), ['1','true','ok','si','sí','yes']);
                                }
                                $noteIt  = $it['note'] ?? ($it['notes'] ?? ($it['nota'] ?? null));

                                $ico   = $doneIt===true ? 'bi-check-circle-fill' : ($doneIt===false ? 'bi-x-circle-fill' : 'bi-dash-circle');
                                $color = $doneIt===true ? '#16a34a'             : ($doneIt===false ? '#dc2626'             : '#64748b');
                              @endphp

                              <div class="d-flex align-items-start gap-2">
                                <i class="bi {{ $ico }}" style="color:{{ $color }}; font-size:1rem;"></i>
                                <div>
                                  <div class="fw-semibold">{{ $labelIt }}</div>
                                  @if($noteIt)
                                    <div class="small text-muted-ux">{{ $noteIt }}</div>
                                  @endif
                                </div>
                              </div>
                            @endforeach
                          </div>
                        </div>
                      @endif
                    @endforeach
                  @endif
                  {{-- =================== /CHECKLIST DEL PROCESO =================== --}}

                  {{-- Adjuntos --}}
                  @if($evids || ($proceso && ($proceso->video || $proceso->documento_pdf)))
                    <div class="fw-bold mb-1 mt-3">Adjuntos</div>
                  @endif
                  <div class="d-flex flex-wrap gap-2 mb-2">
                    @foreach($evids as $src)
                      <div class="thumb" onclick="previewImage('{{ $src }}')">
                        <img src="{{ $src }}" alt="Evidencia">
                      </div>
                    @endforeach
                    @if($proceso && $proceso->video)
                      <div class="thumb" onclick="previewVideo('{{ asset('storage/'.$proceso->video) }}')">
                        <i class="bi bi-play-btn" style="font-size:1.4rem;opacity:.65"></i>
                      </div>
                    @endif
                    @if($proceso && $proceso->documento_pdf)
                      <a class="btn btn-ghost" target="_blank" href="{{ asset('storage/'.$proceso->documento_pdf) }}">
                        <i class="bi bi-file-earmark-pdf"></i> Ver PDF
                      </a>
                    @endif
                  </div>
                @endif

                <div class="d-flex justify-content-between mt-2">
                  <button class="btn btn-ghost" :disabled="current===0" @click="prev()"><i class="bi bi-arrow-left"></i> Anterior</button>
                  <button class="btn btn-ghost" :disabled="current===max()" @click="next()">Siguiente <i class="bi bi-arrow-right"></i></button>
                </div>
              </div>
            </div>
          @endforeach

        </div>
      </div>
    </div>
  </div>
</div>

{{-- FAB (móvil) --}}
<div class="fab">
  <button type="button" id="openActionsFab" class="fab-btn" aria-label="Más acciones">
    <i class="bi bi-three-dots"></i>
  </button>
</div>

{{-- Bottom sheet acciones (móvil) --}}
<div class="sheet-backdrop" id="sheetBackdrop"></div>
<div class="sheet" id="sheetPanel">
  <div class="grab"></div>
  <div class="title">Acciones</div>
  <div class="grid">
    <a class="action" target="_blank" href="{{ $barcodeUrl }}">
      <div class="ico"><i class="bi bi-upc-scan"></i></div>
      Imprimir etiqueta
    </a>

    <div class="row2">
      <a class="action" href="{{ $editUrl }}">
        <div class="ico"><i class="bi bi-pencil-square"></i></div>
        Editar
      </a>
      <button type="button" class="action" id="mDelete">
        <div class="ico"><i class="bi bi-trash3"></i></div>
        Eliminar
      </button>
    </div>
  </div>
</div>

{{-- Form oculto para eliminar --}}
<form id="deleteForm" action="{{ $deleteAction }}" method="POST" class="d-none">
  @csrf
  @method('DELETE')
</form>

<script>
/* SweetAlert media preview */
function previewImage(src){
  Swal.fire({
    html: `<div class="media-wrap"><img src="${src}" alt="preview"></div>`,
    width: Math.min(1000, window.innerWidth - 40),
    showConfirmButton: false,
    showCloseButton: true,
    background: '#fff',
    customClass: { popup: 'media' }
  });
}
function previewVideo(src){
  Swal.fire({
    html: `<div class="media-wrap"><video src="${src}" controls autoplay playsinline></video></div>`,
    width: Math.min(1100, window.innerWidth - 40),
    showConfirmButton: false,
    showCloseButton: true,
    background: '#fff',
    customClass: { popup: 'media' }
  });
}

/* Eliminar (desktop y móvil) */
function confirmDelete(){
  Swal.fire({
    title: 'Eliminar equipo',
    text: 'Esta acción no se puede deshacer. ¿Deseas continuar?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#dc2626',
    reverseButtons: true
  }).then((res) => {
    if(res.isConfirmed){ document.getElementById('deleteForm').submit(); }
  });
}
document.getElementById('btnDelete')?.addEventListener('click', confirmDelete);
document.getElementById('mDelete')?.addEventListener('click', () => { hideSheet(); confirmDelete(); });

/* Bottom sheet (móvil) + FAB: al abrir, ocultar FAB */
const backdrop = document.getElementById('sheetBackdrop');
const panel    = document.getElementById('sheetPanel');
function showSheet(){
  panel.classList.add('show');
  backdrop.classList.add('show');
  document.body.classList.add('sheet-open');   // <-- oculta FAB y bloquea scroll
}
function hideSheet(){
  panel.classList.remove('show');
  backdrop.classList.remove('show');
  document.body.classList.remove('sheet-open'); // <-- vuelve a mostrar FAB
}
document.getElementById('openActionsFab')?.addEventListener('click', showSheet);
backdrop?.addEventListener('click', hideSheet);
</script>
@endsection
