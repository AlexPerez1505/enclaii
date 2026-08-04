{{-- resources/views/camionetas/index.blade.php --}}
@extends('layouts.app')
@section('title','Automovil')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400..700&display=swap"/>

@php
  use Illuminate\Support\Facades\Storage;

  $q = trim((string) request('q',''));

  $camionetas = $camionetas ?? \App\Models\Camioneta::query()
    ->when($q !== '', function($query) use ($q){
      $query->where(function($s) use ($q){
        $s->where('placa','like',"%{$q}%")
          ->orWhere('marca','like',"%{$q}%")
          ->orWhere('modelo','like',"%{$q}%")
          ->orWhere('vin','like',"%{$q}%")
          ->orWhere('color','like',"%{$q}%")
          ->orWhere('anio','like',"%{$q}%");
      });
    })
    ->orderBy('placa')
    ->paginate(18);
@endphp

<style>
  :root{
    --bg:#ffffff;
    --ink:#0f172a;
    --muted:#64748b;
    --line:#e8edf6;

    --shadow: 0 20px 48px rgba(2,6,23,.10);

    --green:#22c55e;
    --green-ink:#0f7a3a;
    --green-soft: rgba(34,197,94,.14);
    --green-ring: rgba(34,197,94,.24);
  }

  *{ box-sizing:border-box; }
  body{ background:var(--bg) !important; }
  .material-symbols-outlined{ font-variation-settings:'FILL' 0,'wght' 500,'GRAD' 0,'opsz' 22; }

  .fleet-page{ background:var(--bg); padding: 16px 0 24px; }

  .fleet-wrap{
    max-width: 1480px;
    margin: 0 auto;
    padding: 0 10px;
  }

  .fleet-head{
    display:flex; align-items:flex-end; justify-content:space-between;
    gap: 14px; padding: 10px 0 18px;
  }
  .fleet-title{ line-height:1.05; }
  .fleet-title .kicker{ color: var(--muted); font-weight: 500; letter-spacing:.2px; margin-top:4px; }
  .fleet-title h1{ margin:0; font-size: 34px; font-weight: 900; color: var(--ink); letter-spacing: -.4px; }

  .fleet-actions{ display:flex; align-items:center; gap: 10px; flex-wrap: wrap; justify-content:flex-end; }

  .search{ position:relative; min-width: 420px; max-width: 560px; flex: 1 1 420px; }
  .search input{
    width:100%; height: 48px; border-radius: 999px;
    border: 1px solid var(--line); background:#fff;
    padding: 0 14px 0 44px; outline:none;
    font-family: Outfit, system-ui, -apple-system, Segoe UI, Roboto, Arial;
    font-size: 14px; color: var(--ink);
    box-shadow: 0 10px 30px rgba(2,6,23,.04);
  }
  .search input:focus{
    border-color: rgba(59,130,246,.25);
    box-shadow: 0 0 0 4px rgba(59,130,246,.10), 0 14px 38px rgba(2,6,23,.06);
  }
  .search .ico{
    position:absolute; left: 14px; top: 50%;
    transform: translateY(-50%); color:#94a3b8; font-size:20px; pointer-events:none;
  }

  .btnx{
    border:0; border-radius: 999px; height: 44px; padding: 0 14px;
    display:inline-flex; align-items:center; gap: 8px;
    font-weight: 500; font-size: 13px; text-decoration:none; cursor:pointer;
    transition: transform .14s ease, box-shadow .14s ease;
    user-select:none; white-space:nowrap;
  }
  .btnx .material-symbols-outlined{ font-size: 20px; }
  .btnx-primary {
    background: linear-gradient(180deg, #f4f7f8 0%, #ecf0f3 100%);
    border: 1px solid #8fbcd4;
    color: var(--ink);
    box-shadow: 0 4px 12px rgba(143, 188, 212, 0.25);
    transition: all 0.3s ease;
  }
  .btnx-primary:hover {
    background: linear-gradient(180deg, #ffffff 0%, #f4f7f8 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(143, 188, 212, 0.4);
    border-color: #6fa4bf;
  }
  .btnx-soft{ background:#fff; color: var(--ink); border: 1px solid var(--line); box-shadow: 0 12px 28px rgba(2,6,23,.06); }

  .empty{ border: 1px dashed rgba(15,23,42,.18); border-radius: 18px; padding: 18px; color: var(--muted); text-align:center; }

  .cards-grid{
    display:grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
  }
  @media (max-width: 1200px){
    .cards-grid{ grid-template-columns: 1fr; }
  }

  .h-card{
    background:#fff;
    border: 1px solid rgba(15,23,42,.07);
    border-radius: 28px;
    box-shadow: var(--shadow);
    overflow:hidden;
    display:flex;
    min-height: 380px;
    transition: transform .28s ease, box-shadow .28s ease;
  }
  .h-card:hover{
    transform: translateY(-5px);
    box-shadow: 0 28px 64px rgba(2,6,23,.14);
  }
  @media (max-width: 576px){
    .h-card{ flex-direction: column-reverse; min-height:auto; }
  }

  .h-info{
    flex: 1;
    padding: 26px;
    display:flex;
    flex-direction: column;
    min-width: 300px;
    background:#fff;
    position: relative;
    z-index: 2;
  }

  .h-thumb-top{
    width:100%;
    height:170px;
    border-radius: 20px;
    background:#f3f4f6;
    overflow:hidden;
    margin-bottom: 18px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }
  .h-thumb-top img{ width:100%; height:100%; object-fit: cover; display:block; }
  .h-thumb-top .noimg{
    height:100%; display:flex; align-items:center; justify-content:center;
    color:#9ca3af; font-size: 13px;
  }

  .h-plate{
    display:inline-block;
    font-size: .78rem;
    font-weight: 900;
    background:#f3f4f6;
    color: #0f172a;
    padding: 6px 12px;
    border-radius: 12px;
    margin-bottom: 12px;
    width: fit-content;
    letter-spacing: .06em;
    text-transform: uppercase;
  }

  .h-brand{
    font-size: 2.15rem;
    font-weight: 350;
    color: #0f172a;
    line-height: 1;
    margin-bottom: 8px;
    letter-spacing: -0.03em;
  }

  .h-model{
    font-size: 1.05rem;
    color: #64748b;
    font-weight: 400;
    margin-bottom: 12px;
  }

  .h-data-grid{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px 18px;
  }
  .h-data-item{ padding: 2px 2px; }
  .h-data-label{
    color: #64748b;
    font-size: 12px;
    font-weight: 500;
    margin-bottom: 6px;
  }
  .h-data-value{
    color: #0f172a;
    font-size: 16px;
    font-weight: 900;
    line-height: 1.1;
    letter-spacing: .2px;
  }

  .h-btn{
    margin-top: 16px;
    background: var(--green-soft);
    color: var(--green-ink);
    border: 1px solid rgba(34,197,94,.22);
    padding: 12px 18px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap: 8px;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    width: fit-content;
  }
  .h-btn:hover{
    transform: translateY(-2px);
    border-color: rgba(34,197,94,.35);
    box-shadow: 0 0 0 6px var(--green-ring), 0 18px 40px rgba(2,6,23,.10);
    color: var(--green-ink);
  }

  .h-visual{
    flex: 1.35;
    background:#f3f4f6;
    position:relative;
    overflow:hidden;
    min-height: 300px;
  }
  .h-visual img{
    width:100%;
    height:100%;
    object-fit: cover;
    display:block;
    transition: transform .5s ease;
  }
  .h-card:hover .h-visual img{ transform: scale(1.02); }

  .h-visual::after{
    content:'';
    position:absolute;
    inset:0;
    background: linear-gradient(90deg, #ffffff 0%, rgba(255,255,255,0.82) 14%, rgba(255,255,255,0) 52%);
    pointer-events:none;
    z-index:1;
  }

  @media (max-width: 760px){
    .fleet-head{ flex-direction: column; align-items: stretch; gap: 12px; padding-bottom: 14px; }
    .fleet-actions{ justify-content: stretch; gap: 10px; }
    .search{ min-width: 0; max-width: 100%; flex: 1 1 auto; }
    .btnx{ flex: 1 1 auto; justify-content:center; }
    .fleet-wrap{ padding: 0 10px; }
  }
</style>

<div class="fleet-page">
  <div class="fleet-wrap">

    <div class="fleet-head">
      <div class="fleet-title">
        <h1>Automóviles</h1>
        <div class="kicker">vehículos · Catálogo de unidades</div>
      </div>

      <div class="fleet-actions">
        <form method="GET" action="{{ route('camionetas.index') }}" class="search">
          <span class="material-symbols-outlined ico">search</span>
          <input type="text" name="q" value="{{ $q }}" placeholder="Buscar por placa / marca / modelo..." autocomplete="off">
        </form>

        <a class="btnx btnx-primary" href="{{ route('camionetas.create') }}">
          <span class="material-symbols-outlined">add</span>
          Nueva
        </a>

        <a class="btnx btnx-soft" href="{{ route('camionetas.index') }}">
          <span class="material-symbols-outlined">refresh</span>
        </a>
      </div>
    </div>

    @if(($camionetas ?? collect())->count() === 0)
      <div class="empty">No hay unidades para mostrar.</div>
    @else
      <div class="cards-grid">
        @foreach($camionetas as $camioneta)
          @php
            $plate = $camioneta->placa ?: '—';
            $brand = $camioneta->marca ?: '—';
            $model = trim(($camioneta->modelo ?: '').' '.($camioneta->anio ?: ''));
            $vin   = $camioneta->vin ?: '—';
            $color = $camioneta->color ?: '—';
            $year  = $camioneta->anio ?: '—';

            $fotos = [];
            if (!empty($camioneta->fotos)) {
              $decoded = json_decode($camioneta->fotos, true);

              if (is_array($decoded)) {
                foreach ($decoded as $fotoPath) {
                  if (!empty($fotoPath)) {
                    $fotos[] = Storage::url($fotoPath);
                  }
                }
              } else {
                $fotos[] = Storage::url($camioneta->fotos);
              }
            }

            $left = $fotos[0] ?? asset('images/default-car.jpg');
            $hero = $fotos[0] ?? asset('images/default-car.jpg');
          @endphp

          <article class="h-card">
            <div class="h-info">
              <div class="h-thumb-top">
                @if(!empty($fotos))
                  <img src="{{ $left }}" alt="Imagen de {{ $brand }} {{ $camioneta->modelo }}" loading="lazy">
                @else
                  <div class="noimg">Sin imagen</div>
                @endif
              </div>

              <div class="h-plate">{{ $plate }}</div>
              <div class="h-brand">{{ $brand }}</div>
              <div class="h-model">{{ $model }}</div>

              <div class="h-data-grid">
                <div class="h-data-item">
                  <div class="h-data-label">Placa</div>
                  <div class="h-data-value">{{ $plate }}</div>
                </div>
                <div class="h-data-item">
                  <div class="h-data-label">Año</div>
                  <div class="h-data-value">{{ $year }}</div>
                </div>

                <div class="h-data-item">
                  <div class="h-data-label">Marca</div>
                  <div class="h-data-value">{{ $brand }}</div>
                </div>
                <div class="h-data-item">
                  <div class="h-data-label">Modelo</div>
                  <div class="h-data-value">{{ $camioneta->modelo ?: '—' }}</div>
                </div>

                <div class="h-data-item">
                  <div class="h-data-label">VIN</div>
                  <div class="h-data-value">{{ $vin }}</div>
                </div>
                <div class="h-data-item">
                  <div class="h-data-label">Color</div>
                  <div class="h-data-value">{{ $color }}</div>
                </div>
              </div>

              <a class="h-btn" href="{{ route('camionetas.show', $camioneta) }}">
                Ver más
                <span class="material-symbols-outlined" style="font-size:18px;">chevron_right</span>
              </a>
            </div>

            <div class="h-visual">
              <img src="{{ $hero }}" alt="Imagen principal de {{ $brand }} {{ $camioneta->modelo }}" loading="lazy">
            </div>
          </article>
        @endforeach
      </div>

      @if(method_exists($camionetas,'links'))
        <div style="margin-top:14px">
          {{ $camionetas->appends(['q'=>$q])->links() }}
        </div>
      @endif
    @endif

  </div>
</div>
@endsection