{{-- resources/views/productos-cards.blade.php --}}
@extends('layouts.app')
@section('title', 'Productos')
@section('titulo', 'Productos')

@section('content')
<link rel="stylesheet" href="{{ asset('css/productos.css') }}?v={{ time() }}">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">

<style>
  .section-title{max-width:1200px;margin:32px auto 8px;padding:0 16px;color:#4b5563;font-weight:700;letter-spacing:.3px}
  .section-subtitle{max-width:1200px;margin:8px auto 16px;padding:0 16px;color:#6b7280;font-weight:700}
  .chip{font-size:12px;background:#eef2ff;color:#374151;padding:2px 8px;border-radius:999px;border:1px solid #e5e7eb}
  .pkg-total-row{display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding-top:8px;border-top:1px dashed rgba(0,0,0,.12);font-weight:700}
  .pkg-line{display:flex;justify-content:space-between;align-items:center;gap:10px}
  .pkg-line small{color:#6b7280}
  .search-wrap{max-width:900px;margin:120px auto 0;padding:0 16px}
  /* Toast minimal */
  .toast-bank{position:fixed;left:50%;transform:translateX(-50%);bottom:22px;z-index:9999;background:#fff;border:1px solid #e7ebf0;border-radius:999px;padding:8px 14px;box-shadow:0 14px 32px rgba(19,36,63,.10);font-weight:700;color:#1f2f49;display:none}
  /* Espaciado entre secciones de familia */
  .family-group{margin-bottom:32px}
</style>

{{-- =================== Buscador =================== --}}
<div class="search-wrap" style="margin-top:20px;">
  <div class="search" id="liveSearch">
    <button id="btnOpen" aria-label="Buscar">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="7"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
      </svg>
    </button>
    <input id="q" type="text" placeholder="Buscar por nombre, marca, modelo, familia o precio…" autocomplete="off"/>
    <button class="clear" id="btnClear" aria-label="Limpiar">✕</button>
  </div>
</div>

@php
  use Illuminate\Support\Str;

  /**
   * Construimos $grouped asegurando:
   * - Un producto puede estar en varias familias (se duplica visualmente).
   * - Siempre existe la llave 'Sin familia' si hay productos sin familias.
   * - Orden natural por nombre de familia.
   */
  $grouped = [];

  if (!empty($productos)) {
      foreach ($productos as $p) {
          $familias = (isset($p->familias) && $p->familias->count())
              ? $p->familias->pluck('nombre')->all()
              : ['Sin familia'];

          foreach ($familias as $famName) {
              $grouped[$famName][] = $p;
          }
      }
      uksort($grouped, function($a,$b){ return strnatcasecmp($a,$b); });
  }
@endphp

@if(!empty($grouped))
  <div class="section-title" id="productsTitle">Productos</div>

  @foreach($grouped as $famName => $items)
    <section class="family-group" data-family="{{ \Illuminate\Support\Str::slug($famName) }}">
      <div class="section-subtitle family-header">Familia: {{ $famName }}</div>

      <div class="cards-grid">
        @foreach($items as $p)
          @php
            $nombre   = $p->tipo_equipo ?? $p->nombre ?? 'Producto';
            $precioV  = (float)($p->precio ?? 0);
            $precio   = number_format($precioV, 2);
            $desc     = trim(($p->marca ?? '').' · '.($p->modelo ?? ''), ' ·');

            $imgRaw   = $p->imagen ?? $p->imagen_url ?? $p->foto_url ?? null;
            $img      = $imgRaw
                          ? (\Illuminate\Support\Str::startsWith($imgRaw, ['http://','https://']) ? $imgRaw : asset('storage/'.$imgRaw))
                          : 'https://via.placeholder.com/800x800.png?text=Producto';

            $familiasStr = isset($p->familias) ? $p->familias->pluck('nombre')->join(' ') : ($famName === 'Sin familia' ? '' : $famName);

            // Texto base para búsqueda (el JS lo normaliza)
            $searchText  = trim(($nombre ?? '').' '.($p->marca ?? '').' '.($p->modelo ?? '').' '.($p->descripcion ?? '').' '.$familiasStr.' '.$precioV);
          @endphp

          <div
            class="card-scope"
            data-search="{{ $searchText }}"
            data-price="{{ $precioV }}"
            data-brand="{{ $p->marca ?? '' }}"
            data-model="{{ $p->modelo ?? '' }}"
            data-families="{{ $familiasStr }}"
            data-name="{{ $nombre }}"
          >
            <div id="make-3D-space">
              <div id="product-card">
                {{-- FRONT --}}
                <div id="product-front">
                  <div class="shadow"></div>
                  <div class="media-area">
                    <img src="{{ $img }}" alt="{{ $nombre }}">
                    <div class="image_overlay"></div>
                    <div id="view_details">Ver Foto</div>
                  </div>
                  <div class="stats">
                    <div class="stats-container">
                      <span class="product_price">${{ $precio }}</span>
                      <span class="product_name">{{ $nombre }}</span>
                      <p>{{ \Illuminate\Support\Str::limit($desc ?: ($p->descripcion ?? ' '), 60) }}</p>

                      @if(isset($p->familias) && $p->familias->count())
                        <div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap">
                          @foreach($p->familias as $fam)
                            <span class="chip">{{ $fam->nombre }}</span>
                          @endforeach
                        </div>
                      @elseif($famName !== 'Sin familia')
                        <div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap">
                          <span class="chip">{{ $famName }}</span>
                        </div>
                      @endif

                      <div class="product-options" style="margin-top:6px">
                        <strong>DETALLES</strong>
                        <span>Marca: {{ $p->marca ?? '—' }} | Modelo: {{ $p->modelo ?? '—' }}</span>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- BACK --}}
                <div id="product-back">
                  <div class="shadow"></div>
                  <div id="carousel">
                    <ul><li><img src="{{ $img }}" alt="foto {{ $nombre }}"></li></ul>
                    <div class="arrows-perspective">
                      <div class="carouselPrev"><div class="y"></div><div class="x"></div></div>
                      <div class="carouselNext"><div class="y"></div><div class="x"></div></div>
                    </div>
                  </div>

                  @if(auth()->check() && auth()->user()->hasRole('admin'))
                  <div class="card-actions">
                    <a class="fab fab--edit" href="{{ route('productos.edit', $p->id) }}" title="Editar {{ $nombre }}">
                      <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                      </svg>
                    </a>
                    <form class="delete-form" action="{{ route('productos.destroy', $p->id) }}" method="POST">
                      @csrf @method('DELETE')
                      <button type="submit" class="fab fab--del" title="Eliminar {{ $nombre }}">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                          <polyline points="3 6 5 6 21 6"/>
                          <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                          <path d="M10 11v6M14 11v6"/>
                          <path d="M9 6V4a2 2 0 0 1 2-2h2a 2 2 0 0 1 2 2v2"/>
                        </svg>
                      </button>
                    </form>
                  </div>
                  @endif

                  <div id="flip-back"><div id="cy"></div><div id="cx"></div></div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </section>
  @endforeach

  @if(auth()->check() && auth()->user()->hasRole('admin'))
    <a href="{{ route('productos.create') }}" class="fab-add" title="Agregar producto" aria-label="Agregar producto">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
    </a>
  @endif
@endif

{{-- =================== Paquetes =================== --}}
@if(!empty($paquetes) && count($paquetes))
  <div class="section-title" id="packagesTitle">Paquetes</div>
  <div class="cards-grid" id="cardsGridPkg">
    @foreach($paquetes as $pkg)
      @php
        $pkgName   = $pkg->nombre ?? 'Paquete';
        $pkgImgRaw = $pkg->imagen ?? optional(optional($pkg->productos)->first())->imagen ?? null;
        $pkgImg    = $pkgImgRaw
          ? (\Illuminate\Support\Str::startsWith($pkgImgRaw, ['http://','https://']) ? $pkgImgRaw : asset('storage/'.$pkgImgRaw))
          : 'https://via.placeholder.com/800x800.png?text=Paquete';

        $count     = property_exists($pkg,'productos_count')
                      ? $pkg->productos_count
                      : (isset($pkg->productos) ? $pkg->productos->count() : 0);
        $pkgDesc   = $count ? "Incluye {$count} producto".($count===1?'':'s') : 'Paquete sin contenido asignado';

        $namesIn   = '';
        if (isset($pkg->productos)) {
          $namesIn = collect($pkg->productos)->map(function($pp){
            return trim(($pp->nombre ?? ($pp->tipo_equipo ?? '')) . ' ' . ($pp->marca ?? '') . ' ' . ($pp->modelo ?? ''));
          })->join(' ');
        }
        $pkgTotal = isset($pkg->productos)
          ? $pkg->productos->sum(function($pp){
              $precio = (float) ($pp->precio ?? 0);
              $cant   = (int) ($pp->pivot->cantidad ?? 1);
              return $precio * max(1, $cant);
            })
          : 0;
        $pkgTotalFmt = number_format($pkgTotal, 2);

        $searchText = trim($pkgName.' '.$namesIn.' '.$pkgTotal);
      @endphp

      <div
        class="card-scope"
        data-search="{{ $searchText }}"
        data-price="{{ $pkgTotal }}"
        data-name="{{ $pkgName }}"
      >
        <div id="make-3D-space">
          <div id="product-card">
            {{-- FRONT --}}
            <div id="product-front">
              <div class="shadow"></div>
              <div class="media-area">
                <img src="{{ $pkgImg }}" alt="{{ $pkgName }}">
                <div class="image_overlay"></div>
                <div id="view_details">Ver contenido</div>
              </div>
              <div class="stats">
                <div class="stats-container">
                  <span class="product_price">${{ $pkgTotalFmt }}</span>
                  <span class="product_name">{{ $pkgName }}</span>
                  <p>{{ \Illuminate\Support\Str::limit($pkgDesc, 70) }}</p>
                  <div class="product-options">
                    <strong>DETALLES</strong>
                    <span>Contenido: {{ $count ?: '—' }} • Total: ${{ $pkgTotalFmt }}</span>
                  </div>
                </div>
              </div>
            </div>

            {{-- BACK --}}
            <div id="product-back">
              <div class="shadow"></div>

              <div style="padding:14px 16px 6px;max-height:260px;overflow:auto;">
                @if(isset($pkg->productos) && $pkg->productos->count())
                  <ul style="list-style:none;margin:0;padding:0;display:grid;gap:10px;">
                    @foreach($pkg->productos as $pp)
                      @php
                        $ppName = $pp->nombre ?? ($pp->tipo_equipo ?? 'Producto');
                        $ppImgR = $pp->imagen ?? null;
                        $ppImg  = $ppImgR
                                  ? (\Illuminate\Support\Str::startsWith($ppImgR, ['http://','https://']) ? $ppImgR : asset('storage/'.$ppImgR))
                                  : 'https://via.placeholder.com/80.png?text=IMG';
                        $cant   = (int) ($pp->pivot->cantidad ?? 1);
                        $precio = (float) ($pp->precio ?? 0);
                        $sub    = $precio * max(1, $cant);
                      @endphp
                      <li class="pkg-line">
                        <div style="display:flex;align-items:center;gap:10px;">
                          <img src="{{ $ppImg }}" alt="{{ $ppName }}" style="width:44px;height:44px;object-fit:cover;border-radius:8px;border:1px solid rgba(0,0,0,.08)">
                          <div style="line-height:1.1">
                            <div style="font-weight:600">{{ \Illuminate\Support\Str::limit($ppName, 40) }}</div>
                            <small>{{ trim(($pp->marca ?? '').' '.($pp->modelo ?? '')) }}</small>
                          </div>
                        </div>
                        <div style="text-align:right;min-width:140px">
                          <div><small>Precio</small> ${{ number_format($precio,2) }}</div>
                          <div><small>Cant.</small> x{{ max(1,$cant) }}</div>
                          <div><small>Subt.</small> ${{ number_format($sub,2) }}</div>
                        </div>
                      </li>
                    @endforeach
                  </ul>

                  <div class="pkg-total-row">
                    <span>Total del paquete</span>
                    <span>${{ $pkgTotalFmt }}</span>
                  </div>
                @else
                  <div style="color:#6b7280">Aún no has agregado productos a este paquete.</div>
                @endif
              </div>

              @if(auth()->check() && auth()->user()->hasRole('admin'))
              <div class="card-actions">
                <a class="fab fab--edit" href="{{ route('paquetes.edit', $pkg->id) }}" title="Editar paquete">
                  <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2 2 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                  </svg>
                </a>
                <form class="delete-form" action="{{ route('paquetes.destroy', $pkg->id) }}" method="POST">
                  @csrf @method('DELETE')
                  <button type="submit" class="fab fab--del" title="Eliminar paquete">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                      <path d="M10 11v6M14 11v6"/><path d="M9 6V4a 2 2 0 0 1 2-2h2a 2 2 0 0 1 2 2v2"/>
                    </svg>
                  </button>
                </form>
              </div>
              @endif

              <div id="flip-back"><div id="cy"></div><div id="cx"></div></div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endif

@if((empty($productos) || !count($productos)) && (empty($paquetes) || !count($paquetes)))
  <p style="margin:16px;color:#7a7f87">No hay productos ni paquetes.</p>
@endif

<div id="toast" class="toast-bank">Sin coincidencias exactas, mostrando todo</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function(){
  const $search = $('#liveSearch');
  const $q      = $('#q');
  const $toast  = $('#toast');

  // Abrir/cerrar buscador
  $('#btnOpen').on('click', function(e){
    e.preventDefault();
    if (!$search.hasClass('open')) {
      $search.addClass('open');
      setTimeout(()=> $q.trigger('focus'), 10);
    } else {
      $q.trigger('focus');
    }
  });
  $('#btnClear').on('click', function(){
    $q.val('');
    resetAll();            // ⬅️ Reinicia todo al limpiar
    $q.focus();
  });
  $q.on('blur', function(){ if(!this.value) $search.removeClass('open'); });

  // Utils
  const showToast = (msg)=>{
    $toast.text(msg||''); $toast.stop(true,true).fadeIn(120).delay(1200).fadeOut(200);
  };
  const normalize = (s)=> (s||'')
      .toString()
      .toLowerCase()
      .normalize('NFD')
      .replace(/\p{Diacritic}/gu,'')   // quita acentos
      .replace(/\s+/g,'');             // quita espacios

  // Mostrar todo (productos + paquetes) y títulos/secciones
  function resetAll(){
    $('.card-scope').show();           // muestra TODAS las tarjetas
    $('.family-group').show();         // muestra TODAS las familias
    $('#productsTitle').show();
    $('#packagesTitle').show();
  }

  /**
   * Oculta secciones de familia que no tengan tarjetas visibles.
   * (La cabecera “Productos” solo se oculta si no hay ninguna familia visible)
   */
  function updateGroupsVisibility(){
    $('.family-group').each(function(){
      const hasVisibleCards = $(this).find('.card-scope:visible').length > 0;
      $(this).toggle(hasVisibleCards);
    });

    const anyProductFamily = $('.family-group:visible').length > 0;
    $('#productsTitle').toggle(anyProductFamily);

    const anyPackage = $('#cardsGridPkg .card-scope:visible').length > 0;
    $('#packagesTitle').toggle(anyPackage);
  }

  // ===== BUSCADOR GLOBAL =====
  $q.on('input', function(){
    const raw = this.value || '';
    const trimmed = raw.trim();
    if (trimmed.length === 0) {
      // Si el buscador queda vacío -> reestablece TODO
      resetAll();            // ⬅️ fuerza mostrar todo
      return;                // evitamos cálculos innecesarios
    }

    const tokens = trimmed.split(/\s+/).filter(Boolean).map(normalize);

    let matches = 0;
    $('.card-scope').each(function(){
      const $card = $(this);
      const data  = (
        ($card.data('search')||'') + ' ' +
        ($card.data('name')||'') + ' ' +
        ($card.data('brand')||'') + ' ' +
        ($card.data('model')||'') + ' ' +
        ($card.data('families')||'') + ' ' +
        ($card.data('price')||'')
      ).toString();

      const haystack = normalize(data);

      let show = tokens.some(t => haystack.indexOf(t) !== -1); // OR
      $card.toggle(show);
      if (show) matches++;
    });

    updateGroupsVisibility();

    // Evitar vacío: si no hubo matches, restauramos todo y avisamos
    if (matches === 0) {
      resetAll();
      showToast('Sin coincidencias exactas, mostrando todo');
    }
  });

  // ===== Tarjetas (flip) =====
  $('.card-scope').each(function(){
    var $scope = $(this);
    var $card  = $scope.find('#product-card');
    var $front = $scope.find('#product-front');
    var $back  = $scope.find('#product-back');
    var $view  = $scope.find('#view_details');
    var $flipB = $scope.find('#flip-back');

    $card.hover(
      function(){ $(this).addClass('animate'); },
      function(){ $(this).removeClass('animate'); }
    );

    $view.on('click', function(){
      $card.addClass('flip-10');
      setTimeout(function(){
        $card.removeClass('flip-10').addClass('flip90')
          .find('div.shadow').show().fadeTo(80,1,function(){
            $front.add($front.find('div.shadow')).hide();
          });
      },50);
      setTimeout(function(){
        $card.removeClass('flip90').addClass('flip190');
        $back.show().find('div.shadow').show().fadeTo(90,0);
        setTimeout(function(){
          $card.removeClass('flip190').addClass('flip180').find('div.shadow').hide();
          $card.addClass('on-back');
          $scope.find('#cx,#cy').addClass('s1');
          setTimeout(function(){ $scope.find('#cx,#cy').addClass('s2'); },100);
          setTimeout(function(){ $scope.find('#cx,#cy').addClass('s3'); },200);
        },100);
      },150);
    });

    $flipB.on('click', function(){
      $card.removeClass('flip180').addClass('flip190');
      setTimeout(function(){
        $card.removeClass('flip190').addClass('flip90');
        $back.find('div.shadow').css('opacity',0).fadeTo(100,1,function(){
          $back.add($back.find('div.shadow')).hide();
          $front.add($front.find('div.shadow')).show();
        });
      },50);
      setTimeout(function(){
        $card.removeClass('flip90').addClass('flip-10');
        $front.find('div.shadow').show().fadeTo(100,0);
        setTimeout(function(){
          $front.find('div.shadow').hide();
          $card.removeClass('flip-10 on-back').css('transition','100ms ease-out');
          $scope.find('#cx,#cy').removeClass('s1 s2 s3');
        },100);
      },150);
    });

    // Evitar flips al usar acciones
    $scope.find('.card-actions .fab, .card-actions form').on('click', function(e){
      e.stopPropagation();
    });

    // Confirmación de borrado
    $scope.find('.delete-form').on('submit', function(e){
      e.preventDefault(); e.stopPropagation();
      const form = this;
      Swal.fire({
        title: '¿Eliminar?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e53935',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((r)=>{ if(r.isConfirmed) form.submit(); });
    });
  });

  // Estado inicial: todo visible
  resetAll();
});

// Toast si hay éxito
@if(session('success'))
Swal.fire({
  toast: true,
  position: 'top-end',
  icon: 'success',
  title: @json(session('success')),
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true
});
@endif
</script>
@endsection
