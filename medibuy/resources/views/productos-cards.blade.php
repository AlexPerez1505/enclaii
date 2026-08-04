{{-- resources/views/productos-cards.blade.php --}}
@extends('layouts.app')
@section('title', 'Productos')
@section('titulo', 'Productos')

@section('content')
<link rel="stylesheet" href="{{ asset('css/productos.css') }}?v={{ time() }}">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
      background: #ffffff;
      font-family: "Open Sans", sans-serif;
      color: #333;
  }

  .catalog-wrapper {
      max-width: 1300px;
      margin: 30px auto;
      padding: 0 20px;
  }

  .catalog-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      padding-bottom: 20px;
      border-bottom: 1px solid #eaeaea;
      margin-bottom: 30px;
      gap: 20px;
      flex-wrap: wrap;
  }

  .top-categories {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      white-space: nowrap;
      padding-bottom: 10px;
      flex: 1;
      min-width: 300px;
  }

  .top-categories::-webkit-scrollbar {
      height: 4px;
  }

  .top-categories::-webkit-scrollbar-thumb {
      background: #ddd;
      border-radius: 4px;
  }

  .top-categories a {
      text-decoration: none;
      color: #888;
      font-size: 14px;
      font-weight: 600;
      text-transform: capitalize;
      transition: color 0.2s;
  }

  .top-categories a:hover,
  .top-categories a.active {
      color: #000;
  }

  .catalog-tools {
      display: flex;
      gap: 15px;
      align-items: center;
      flex-shrink: 0;
  }

  .clean-search-wrap {
      position: relative;
  }

  .clean-search {
      border: none;
      border-bottom: 1px solid #ccc;
      padding: 6px 10px 6px 25px;
      outline: none;
      font-size: 14px;
      width: 220px;
      transition: all 0.3s;
      background: #fff;
  }

  .clean-search:focus {
      border-bottom-color: #000;
      width: 250px;
  }

  .clean-search-wrap svg {
      position: absolute;
      left: 0;
      top: 8px;
      width: 16px;
      color: #999;
  }

  .btn-export {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: 1px solid #eaeaea;
      background: #fff;
      color: #000;
      padding: 8px 14px;
      border-radius: 4px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      text-decoration: none;
  }

  .btn-export:hover {
      border-color: #000;
      color: #000;
  }

  .catalog-body {
      display: flex;
      flex-direction: row;
      gap: 40px;
      align-items: flex-start;
  }

  .catalog-sidebar {
      width: 250px;
      flex-shrink: 0;
      border-right: 1px solid #eaeaea;
      padding-right: 20px;
  }

  .catalog-main {
      flex-grow: 1;
      min-width: 0;
  }

  .sidebar-block {
      margin-bottom: 30px;
  }

  .sidebar-block h3 {
      font-size: 18px;
      font-weight: 700;
      margin-bottom: 15px;
      color: #000;
  }

  .sub-filter {
      font-size: 14px;
      font-weight: 700;
      margin-top: 20px;
      margin-bottom: 12px;
      color: #000;
  }

  .filter-list {
      list-style: none;
      padding: 0;
      margin: 0;
      max-height: 300px;
      overflow-y: auto;
      padding-right: 10px;
  }

  .filter-list::-webkit-scrollbar {
      width: 4px;
  }

  .filter-list::-webkit-scrollbar-thumb {
      background: #ddd;
      border-radius: 4px;
  }

  .filter-list li {
      margin-bottom: 12px;
  }

  .filter-list label {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 13px;
      color: #555;
      cursor: pointer;
      text-transform: capitalize;
      transition: color 0.2s;
      line-height: 1.4;
  }

  .filter-list label:hover {
      color: #000;
  }

  .filter-list input[type="radio"] {
      accent-color: #000;
      width: 14px;
      height: 14px;
      margin-top: 3px;
      cursor: pointer;
      flex-shrink: 0;
  }

  .catalog-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 30px 20px;
      align-content: start;
  }

  .product-card-scope {
      text-decoration: none;
      color: inherit;
      display: block;
      position: relative;
      min-width: 0;
  }

  .product-image-wrap {
      background: #f4f5f7;
      aspect-ratio: 4 / 5;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
      margin-bottom: 15px;
      position: relative;
      border-radius: 8px;
  }

  .product-image-wrap img {
      width: 100%;
      height: 100%;
      object-fit: contain;
      padding: 20px;
      transition: transform 0.4s ease;
  }

  .product-card-scope:hover .product-image-wrap img {
      transform: scale(1.05);
  }

  .product-info {
      text-align: center;
      padding: 0 4px;
  }

  .product-title {
      font-size: 14px;
      color: #666;
      margin-bottom: 6px;
      font-weight: 400;
      text-transform: capitalize;
      line-height: 1.3;
  }

  .product-price {
      font-size: 18px;
      font-weight: 700;
      color: #000;
      margin-bottom: 4px;
  }

  .product-details {
      font-size: 12px;
      color: #000;
      font-weight: 700;
      margin-top: 5px;
      text-transform: uppercase;
  }

  .admin-actions {
      position: absolute;
      bottom: 12px;
      right: 12px;
      display: flex;
      gap: 8px;
      opacity: 0;
      transition: opacity 0.2s;
  }

  .product-card-scope:hover .admin-actions {
      opacity: 1;
  }

  .btn-icon {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: grid;
      place-items: center;
      color: #fff;
      text-decoration: none;
      border: none;
      cursor: pointer;
      background: rgba(0,0,0,0.6);
      transition: background 0.2s, transform 0.2s;
  }

  .btn-icon:hover {
      background: #000;
      transform: scale(1.1);
      color: #fff;
  }

  .btn-icon.delete {
      background: rgba(239, 83, 80, 0.9);
  }

  .btn-icon.delete:hover {
      background: #e53935;
  }

  .btn-icon svg {
      width: 16px;
      height: 16px;
  }

  .package-title-row {
      grid-column: 1 / -1;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid #eaeaea;
  }

  .package-title-row h3 {
      font-size: 18px;
      font-weight: 700;
      color: #000;
      margin: 0;
  }

  .fab-add {
      position: fixed;
      right: 22px;
      bottom: 22px;
      z-index: 99;
      width: 56px;
      height: 56px;
      border-radius: 50%;
      background: #000;
      color: #fff;
      display: grid;
      place-items: center;
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
      text-decoration: none;
      cursor: pointer;
      transition: transform 0.2s;
  }

  .fab-add:hover {
      transform: translateY(-3px);
      color: #fff;
  }

  .fab-add svg {
      width: 24px;
      height: 24px;
  }

  .modal-pro .modal-content {
      border: none;
      border-radius: 12px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  }

  .modal-pro .modal-header {
      background: #fafafa;
      border-bottom: 1px solid #eee;
      padding: 20px 24px;
  }

  .modal-pro .modal-title {
      font-weight: 700;
      color: #000;
      font-size: 1.2rem;
  }

  .modal-pro .modal-body {
      padding: 24px;
  }

  .modal-pro .modal-footer {
      background: #fafafa;
      border-top: 1px solid #eee;
      padding: 16px 24px;
      gap: 10px;
  }

  .field-label {
      font-size: 12px;
      font-weight: 700;
      color: #666;
      margin-bottom: 8px;
      text-transform: uppercase;
  }

  .select-pro,
  .input-pro {
      border: 1px solid #ddd;
      border-radius: 6px;
      padding: 10px 14px;
      width: 100%;
      outline: none;
  }

  .select-pro:focus,
  .input-pro:focus {
      border-color: #000;
  }

  .btn-pro {
      border: 1px solid #ddd;
      background: #fff;
      color: #000;
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: 600;
      text-decoration: none;
      transition: 0.2s;
  }

  .btn-pro.primary {
      background: #000;
      color: #fff;
      border-color: #000;
  }

  .btn-pro.success {
      background: #10b981;
      color: #fff;
      border-color: #10b981;
  }

  .btn-pro:hover {
      opacity: 0.8;
      color: inherit;
  }

  .btn-pro.primary:hover,
  .btn-pro.success:hover {
      color: #fff;
  }

  .check-wrap {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 13px;
      font-weight: 600;
      color: #555;
  }

  .empty-catalog-message {
      grid-column: 1 / -1;
      text-align: center;
      color: #888;
      padding: 45px 15px;
      border: 1px dashed #ddd;
      border-radius: 10px;
      background: #fafafa;
  }

  /* =========================================================
     RESPONSIVE BONITO PARA CELULAR
  ========================================================= */

  @media (max-width: 768px) {
      body {
          background: #fff;
      }

      .catalog-wrapper {
          margin: 10px auto 80px;
          padding: 0 12px;
          max-width: 100%;
      }

      .catalog-header {
          display: flex;
          flex-direction: column;
          align-items: stretch;
          gap: 14px;
          margin-bottom: 16px;
          padding-bottom: 14px;
          border-bottom: 1px solid #eeeeee;
      }

      .top-categories {
          width: 100%;
          min-width: 0;
          flex: none;
          display: flex;
          gap: 18px;
          overflow-x: auto;
          overflow-y: hidden;
          padding: 4px 2px 10px;
          white-space: nowrap;
          scrollbar-width: none;
      }

      .top-categories::-webkit-scrollbar {
          display: none;
      }

      .top-categories a {
          flex: 0 0 auto;
          font-size: 13px;
          color: #777;
          padding: 6px 0;
      }

      .top-categories a.active {
          color: #000;
          font-weight: 700;
          position: relative;
      }

      .top-categories a.active::after {
          content: "";
          position: absolute;
          left: 0;
          right: 0;
          bottom: -2px;
          height: 2px;
          background: #000;
          border-radius: 999px;
      }

      .catalog-tools {
          width: 100%;
          display: grid;
          grid-template-columns: 1fr auto auto;
          gap: 8px;
          align-items: center;
      }

      .catalog-tools .btn-export {
          font-size: 0;
          min-width: 42px;
          padding: 0 10px;
          height: 40px;
          border-radius: 999px;
          background: #111;
          border-color: #111;
          color: #fff;
      }

      .catalog-tools .btn-export:hover {
          color: #fff;
          border-color: #111;
      }

      .catalog-tools .btn-export svg {
          width: 18px;
          height: 18px;
      }

      .clean-search-wrap {
          width: 100%;
          position: relative;
      }

      .clean-search {
          width: 100%;
          height: 44px;
          border: 1px solid #e5e5e5;
          border-radius: 999px;
          padding: 0 14px 0 40px;
          font-size: 14px;
          background: #fafafa;
      }

      .clean-search:focus {
          width: 100%;
          background: #fff;
          border-color: #111;
          box-shadow: 0 8px 20px rgba(0,0,0,.06);
      }

      .clean-search-wrap svg {
          left: 15px;
          top: 50%;
          width: 17px;
          transform: translateY(-50%);
          color: #777;
      }

      .catalog-body {
          flex-direction: column;
          gap: 16px;
      }

      .catalog-sidebar {
          width: 100%;
          border-right: none;
          padding-right: 0;
          border-bottom: 1px solid #eeeeee;
          padding-bottom: 14px;
      }

      .catalog-main {
          width: 100%;
      }

      .sidebar-block {
          margin-bottom: 16px;
      }

      .sidebar-block h3 {
          font-size: 15px;
          margin-bottom: 10px;
      }

      .sub-filter {
          font-size: 12px;
          margin-top: 12px;
          margin-bottom: 8px;
          color: #555;
          text-transform: uppercase;
          letter-spacing: .3px;
      }

      .filter-list {
          max-height: none;
          display: flex;
          gap: 8px;
          overflow-x: auto;
          overflow-y: hidden;
          padding: 0 0 8px;
          margin: 0;
          scrollbar-width: none;
      }

      .filter-list::-webkit-scrollbar {
          display: none;
      }

      .filter-list li {
          margin-bottom: 0;
          flex: 0 0 auto;
      }

      .filter-list label {
          min-height: 36px;
          display: inline-flex;
          align-items: center;
          gap: 7px;
          border: 1px solid #e5e5e5;
          border-radius: 999px;
          padding: 8px 12px;
          background: #fafafa;
          color: #555;
          font-size: 12px;
          line-height: 1;
          white-space: nowrap;
          transition: .2s ease;
      }

      .filter-list label:has(input:checked) {
          background: #111;
          color: #fff;
          border-color: #111;
      }

      .filter-list input[type="radio"] {
          width: 12px;
          height: 12px;
          margin: 0;
          accent-color: #111;
      }

      .catalog-grid {
          display: grid;
          grid-template-columns: repeat(2, minmax(0, 1fr));
          gap: 20px 12px;
          width: 100%;
      }

      .product-card-scope {
          min-width: 0;
      }

      .product-image-wrap {
          aspect-ratio: 1 / 1.18;
          border-radius: 14px;
          margin-bottom: 9px;
          background: #f5f6f7;
          box-shadow: 0 6px 18px rgba(0,0,0,.04);
      }

      .product-image-wrap img {
          padding: 12px;
          object-fit: contain;
      }

      .product-card-scope:hover .product-image-wrap img {
          transform: none;
      }

      .product-info {
          padding: 0 3px;
      }

      .product-title {
          font-size: 12px;
          line-height: 1.25;
          margin-bottom: 5px;
          color: #555;
          min-height: 30px;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
      }

      .product-price {
          font-size: 17px;
          line-height: 1.1;
          margin-bottom: 3px;
      }

      .product-details {
          font-size: 10px;
          letter-spacing: .3px;
          color: #666;
          margin-top: 4px;
      }

      .admin-actions {
          opacity: 1;
          bottom: 8px;
          right: 8px;
          gap: 6px;
      }

      .btn-icon {
          width: 31px;
          height: 31px;
          background: rgba(0,0,0,.72);
          backdrop-filter: blur(4px);
      }

      .btn-icon.delete {
          background: rgba(229,57,53,.88);
      }

      .btn-icon svg {
          width: 14px;
          height: 14px;
      }

      .package-title-row {
          margin-top: 10px;
          padding-top: 18px;
      }

      .package-title-row h3 {
          font-size: 15px;
          letter-spacing: .3px;
      }

      .empty-catalog-message {
          padding: 28px 14px;
          font-size: 13px;
          border-radius: 14px;
      }

      .fab-add {
          width: 52px;
          height: 52px;
          right: 16px;
          bottom: 16px;
      }

      .modal-pro .modal-dialog {
          margin: 12px;
      }

      .modal-pro .modal-content {
          border-radius: 16px;
      }

      .modal-pro .modal-footer {
          display: grid;
          grid-template-columns: 1fr 1fr;
      }

      .modal-pro .modal-footer .btn-pro {
          text-align: center;
          justify-content: center;
      }
  }

  @media (max-width: 360px) {
      .catalog-wrapper {
          padding: 0 10px;
      }

      .catalog-grid {
          gap: 18px 10px;
      }

      .product-image-wrap {
          border-radius: 12px;
      }

      .product-image-wrap img {
          padding: 10px;
      }

      .product-title {
          font-size: 11px;
      }

      .product-price {
          font-size: 15px;
      }

      .product-details {
          font-size: 9px;
      }
  }

  /* =========================================================
     ESTILOS PARA IMPORTACIÓN
     ========================================================= */

  /* Dropzone */
  #dropzone {
      border: 2px dashed #ddd;
      border-radius: 12px;
      padding: 30px 20px;
      text-align: center;
      cursor: pointer;
      background: #fafafa;
      transition: all 0.3s;
      min-height: 150px;
  }

  #dropzone:hover {
      border-color: #000;
      background: #f5f5f5;
  }

  #dropzone.dragover {
      border-color: #000;
      background: #f0f0f0;
      transform: scale(1.02);
  }

  /* File info */
  #fileInfo {
      display: none;
      background: #f8f9fa;
      border-radius: 8px;
      padding: 12px 15px;
      margin-top: 10px;
      align-items: center;
      justify-content: space-between;
  }

  #fileInfo.visible {
      display: flex !important;
  }

  #fileInfo .file-name {
      font-weight: 600;
      color: #000;
  }

  #fileInfo .file-size {
      color: #666;
      font-size: 13px;
      margin-left: 10px;
  }

  .remove-file-btn {
      background: none;
      border: none;
      color: #e53935;
      cursor: pointer;
      font-size: 18px;
      padding: 0 10px;
  }
</style>

@php
  use Illuminate\Support\Str;

  $productosCollection = collect($productos ?? []);

  $grouped = $productosCollection
      ->groupBy(function($item) {
          return $item->tipo_equipo ? strtoupper(trim($item->tipo_equipo)) : 'OTROS';
      })
      ->sortKeys();

  $tipos = $productosCollection
      ->pluck('tipo_equipo')
      ->filter()
      ->map(fn($v) => trim((string) $v))
      ->unique()
      ->sort()
      ->values();

  $subtipos = $productosCollection
      ->pluck('subtipo_equipo')
      ->filter()
      ->map(fn($v) => trim((string) $v))
      ->unique()
      ->sort()
      ->values();

  $marcas = $productosCollection
      ->pluck('marca')
      ->filter()
      ->map(fn($v) => trim((string) $v))
      ->unique()
      ->sort()
      ->values();
@endphp

<div class="catalog-wrapper">

    <div class="catalog-header">
        <nav class="top-categories">
            <a href="#" class="cat-link active" data-val="">Todos los productos</a>

            @foreach($tipos as $t)
                <a href="#" class="cat-link" data-val="{{ $t }}">{{ strtolower($t) }}</a>
            @endforeach
        </nav>

        <div class="catalog-tools">
            <div class="clean-search-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="7"></circle>
                  <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input id="q" type="text" class="clean-search" placeholder="Buscar modelo, tipo..." autocomplete="off">
            </div>

            @if(auth()->check() && auth()->user()->hasRole('admin'))
                <button type="button" class="btn-export" data-bs-toggle="modal" data-bs-target="#modalImport">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                        <path d="M12 3v12"/>
                        <path d="M8 11l4 4 4-4"/>
                        <path d="M4 21h16"/>
                        <path d="M4 12h16"/>
                    </svg>
                    Importar
                </button>
            @endif

            <button type="button" class="btn-export" data-bs-toggle="modal" data-bs-target="#modalExport">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
                  <path d="M12 3v12"></path>
                  <path d="M8 11l4 4 4-4"></path>
                  <path d="M4 21h16"></path>
                </svg>
                Exportar
            </button>
        </div>
    </div>

    <div class="catalog-body">

        <aside class="catalog-sidebar">
            <div class="sidebar-block">
                <h3>Categorías</h3>

                <ul class="filter-list">
                    <li>
                        <label>
                            <input type="radio" name="filterTipo" value="" checked>
                            Todas
                        </label>
                    </li>

                    @foreach($tipos as $tipoOpt)
                        <li>
                            <label>
                                <input type="radio" name="filterTipo" value="{{ $tipoOpt }}">
                                {{ strtolower($tipoOpt) }}
                            </label>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="sidebar-block">
                <h3>Filtrar por</h3>

                <h4 class="sub-filter">Marca</h4>
                <ul class="filter-list">
                    <li>
                        <label>
                            <input type="radio" name="filterMarca" value="" checked>
                            Todas
                        </label>
                    </li>

                    @foreach($marcas as $marcaOpt)
                        <li>
                            <label>
                                <input type="radio" name="filterMarca" value="{{ $marcaOpt }}">
                                {{ strtolower($marcaOpt) }}
                            </label>
                        </li>
                    @endforeach
                </ul>

                <h4 class="sub-filter">Subtipo</h4>
                <ul class="filter-list">
                    <li>
                        <label>
                            <input type="radio" name="filterSubtipo" value="" checked>
                            Todos
                        </label>
                    </li>

                    @foreach($subtipos as $subOpt)
                        <li>
                            <label>
                                <input type="radio" name="filterSubtipo" value="{{ $subOpt }}">
                                {{ strtolower($subOpt) }}
                            </label>
                        </li>
                    @endforeach
                </ul>

                <h4 class="sub-filter">Disponibilidad</h4>
                <ul class="filter-list">
                    <li>
                        <label>
                            <input type="radio" name="filterStock" value="all" checked>
                            Todos
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="radio" name="filterStock" value="with_stock">
                            En stock
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="radio" name="filterStock" value="without_stock">
                            Sin stock
                        </label>
                    </li>
                </ul>
            </div>
        </aside>

        <main class="catalog-main">
            <div class="catalog-grid" id="mainGrid">

                @if(!empty($grouped) && $grouped->count() > 0)
                    @foreach($grouped as $tipo => $items)
                        @foreach($items as $p)
                            @php
                                $nombre   = $p->nombre ?? $p->tipo_equipo ?? 'PRODUCTO';
                                $tipoStr  = $p->tipo_equipo ?? $nombre ?? '';
                                $subtipo  = $p->subtipo_equipo ?? '';
                                $marca    = $p->marca ?? '';
                                $modelo   = $p->modelo ?? '';
                                $precioV  = (float)($p->precio ?? 0);
                                $precio   = number_format($precioV, 2);
                                $stock    = (int)($p->stock ?? 0);

                                $imgRaw   = $p->imagen ?? $p->imagen_url ?? $p->foto_url ?? null;
                                $img      = $imgRaw
                                            ? (Str::startsWith($imgRaw, ['http://','https://']) ? $imgRaw : asset('storage/'.$imgRaw))
                                            : 'https://via.placeholder.com/800x800.png?text=NO+IMG';

                                $searchString = $nombre.' '.$tipoStr.' '.$subtipo.' '.$marca.' '.$modelo.' '.$precioV.' '.$stock.' producto equipo';
                            @endphp

                            <div class="product-card-scope catalog-item"
                                 data-kind="producto"
                                 data-search="{{ e($searchString) }}"
                                 data-tipo="{{ e($tipoStr) }}"
                                 data-subtipo="{{ e($subtipo) }}"
                                 data-marca="{{ e($marca) }}"
                                 data-stock="{{ $stock }}">

                                <div class="product-image-wrap">
                                    <img src="{{ $img }}" alt="{{ $nombre }}">

                                    @if(auth()->check() && auth()->user()->hasRole('admin'))
                                        <div class="admin-actions">
                                            <a class="btn-icon" href="{{ route('productos.edit', $p->id) }}" title="Editar" onclick="event.stopPropagation();">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M12 20h9"/>
                                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                                </svg>
                                            </a>

                                            <form class="delete-form" action="{{ route('productos.destroy', $p->id) }}" method="POST" style="margin:0" onclick="event.stopPropagation();">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn-icon delete" title="Eliminar">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"/>
                                                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                        <path d="M10 11v6M14 11v6"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                <div class="product-info">
                                    <div class="product-title">{{ $tipoStr ?: $nombre }} {{ $modelo }}</div>
                                    <div class="product-price">${{ $precio }}</div>
                                    <div class="product-details">{{ $marca ?: 'Genérico' }}</div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @endif

                @if(!empty($paquetes) && count($paquetes))
                    <div class="package-title-row package-section-title">
                        <h3>PAQUETES</h3>
                    </div>

                    @foreach($paquetes as $pkg)
                        @php
                            $pkgName   = $pkg->nombre ?? 'PAQUETE';
                            $pkgImgRaw = $pkg->imagen ?? optional(optional($pkg->productos)->first())->imagen ?? null;
                            $pkgImg    = $pkgImgRaw
                                ? (Str::startsWith($pkgImgRaw, ['http://','https://']) ? $pkgImgRaw : asset('storage/'.$pkgImgRaw))
                                : 'https://via.placeholder.com/800x800.png?text=PAQUETE';

                            $namesIn = '';
                            $pkgTipos = '';
                            $pkgSubtipos = '';
                            $pkgMarcas = '';
                            $pkgStockStatus = 'without_stock';

                            if (isset($pkg->productos)) {
                                $namesIn = collect($pkg->productos)->map(function($pp) {
                                    return ($pp->nombre ?? $pp->tipo_equipo ?? '') . ' ' .
                                           ($pp->subtipo_equipo ?? '') . ' ' .
                                           ($pp->marca ?? '') . ' ' .
                                           ($pp->modelo ?? '');
                                })->join(' ');

                                $pkgTipos = collect($pkg->productos)
                                    ->pluck('tipo_equipo')
                                    ->filter()
                                    ->map(fn($v) => trim($v))
                                    ->unique()
                                    ->join(' | ');

                                $pkgSubtipos = collect($pkg->productos)
                                    ->pluck('subtipo_equipo')
                                    ->filter()
                                    ->map(fn($v) => trim($v))
                                    ->unique()
                                    ->join(' | ');

                                $pkgMarcas = collect($pkg->productos)
                                    ->pluck('marca')
                                    ->filter()
                                    ->map(fn($v) => trim($v))
                                    ->unique()
                                    ->join(' | ');

                                $pkgStockStatus = collect($pkg->productos)->contains(fn($pp) => (int)($pp->stock ?? 0) > 0)
                                    ? 'with_stock'
                                    : 'without_stock';
                            }

                            $pkgTotal = isset($pkg->productos)
                                ? $pkg->productos->sum(function($pp) {
                                    return (float)($pp->precio ?? 0) * max(1, (int)($pp->pivot->cantidad ?? 1));
                                  })
                                : 0;

                            $pkgTotalFmt = number_format($pkgTotal, 2);
                            $searchString = $pkgName.' '.$namesIn.' '.$pkgTotal.' paquete combo';
                        @endphp

                        <div class="product-card-scope catalog-item pkg-card"
                             data-kind="paquete"
                             data-search="{{ e($searchString) }}"
                             data-tipo="{{ e($pkgTipos) }}"
                             data-subtipo="{{ e($pkgSubtipos) }}"
                             data-marca="{{ e($pkgMarcas) }}"
                             data-stock="{{ $pkgStockStatus }}">

                            <div class="product-image-wrap">
                                <img src="{{ $pkgImg }}" alt="{{ $pkgName }}">

                                @if(auth()->check() && auth()->user()->hasRole('admin'))
                                    <div class="admin-actions">
                                        <a class="btn-icon" href="{{ route('paquetes.edit', $pkg->id) }}" title="Editar" onclick="event.stopPropagation();">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 20h9"/>
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                            </svg>
                                        </a>

                                        <form class="delete-form" action="{{ route('paquetes.destroy', $pkg->id) }}" method="POST" style="margin:0" onclick="event.stopPropagation();">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn-icon delete" title="Eliminar">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    <path d="M10 11v6M14 11v6"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <div class="product-info">
                                <div class="product-title">{{ $pkgName }}</div>
                                <div class="product-price">${{ $pkgTotalFmt }}</div>
                                <div class="product-details">COMBO</div>
                            </div>
                        </div>
                    @endforeach
                @endif

                @if((empty($productos) || !count($productos)) && (empty($paquetes) || !count($paquetes)))
                    <p class="empty-catalog-message">No hay productos registrados.</p>
                @endif

            </div>
        </main>
    </div>
</div>

@if(auth()->check() && auth()->user()->hasRole('admin'))
  <a href="{{ route('productos.create') }}" class="fab-add" title="AGREGAR">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 5v14M5 12h14"/>
    </svg>
  </a>
@endif

<!-- MODAL EXPORTAR -->
<div class="modal fade modal-pro" id="modalExport" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <div class="modal-title">Exportar Catálogo</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12">
            <div class="field-label">Qué exportar</div>
            <select id="exportScope" class="select-pro">
              <option value="all">Todo (Productos + Paquetes)</option>
              <option value="productos">Solo Productos</option>
              <option value="paquetes">Solo Paquetes</option>
            </select>
          </div>

          <div class="col-12 mt-3">
            <label class="check-wrap w-100">
              <input id="exportUseAi" type="checkbox">
              Usar IA para optimizar archivo WooCommerce
            </label>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-pro" data-bs-dismiss="modal">Cancelar</button>
        <a id="btnPdf" class="btn-pro" href="#" target="_blank">PDF</a>
        <a id="btnXlsx" class="btn-pro primary" href="#">Excel</a>
        <a id="btnWooXlsx" class="btn-pro success" href="#">WooCommerce</a>
      </div>
    </div>
  </div>
</div>

<!-- MODAL IMPORTAR -->
<div class="modal fade modal-pro" id="modalImport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:600px;">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <div class="modal-title">📤 Importar Productos</div>
                    <small class="text-muted">Carga un archivo CSV con tus productos</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form action="{{ route('import.productos.procesar') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Modo de importación -->
                        <div class="col-12">
                            <div class="field-label">Modo de importación</div>
                            <div class="d-flex gap-3 flex-wrap" style="padding: 10px 0;">
                                <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                                    <input type="radio" name="modo" value="replace" checked>
                                    <span><strong>🔄 Reemplazar</strong><br><small class="text-muted">Elimina todo y carga los nuevos</small></span>
                                </label>
                                <label class="d-flex align-items-center gap-2" style="cursor:pointer;">
                                    <input type="radio" name="modo" value="update">
                                    <span><strong>📝 Actualizar</strong><br><small class="text-muted">Actualiza existentes y agrega nuevos</small></span>
                                </label>
                            </div>
                        </div>

                        <!-- Selección de archivo -->
                        <div class="col-12">
                            <div class="field-label">Archivo CSV / Excel</div>
                            <div id="dropzone" style="border: 2px dashed #ddd; border-radius: 12px; padding: 30px 20px; text-align: center; cursor: pointer; background: #fafafa; transition: all 0.3s; min-height: 150px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="48" height="48" style="color:#999; margin-bottom: 10px;">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                <h5 style="margin: 10px 0 5px 0; font-size: 16px;">Arrastra tu archivo aquí</h5>
                                <p style="color: #999; font-size: 13px; margin: 0;">o haz clic para seleccionar un archivo CSV, XLS o XLSX</p>
                                <input type="file" name="archivo" id="archivoInput" accept=".csv,.xls,.xlsx" style="display:none" required>
                            </div>

                            <div id="fileInfo" style="display:none; background: #f8f9fa; border-radius: 8px; padding: 12px 15px; margin-top: 10px; align-items: center; justify-content: space-between;">
                                <div>
                                    <span id="fileName" style="font-weight: 600;">archivo.csv</span>
                                    <span id="fileSize" style="color: #666; font-size: 13px; margin-left: 10px;">(0 KB)</span>
                                </div>
                                <button type="button" id="removeFileBtn" style="background: none; border: none; color: #e53935; cursor: pointer; font-size: 18px; padding: 0 10px;">✕</button>
                            </div>

                            @error('archivo')
                                <div style="color: #e53935; font-size: 13px; margin-top: 5px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Plantilla -->
                        <div class="col-12">
                            <div style="background: #f8f9fa; border-radius: 8px; padding: 15px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                                    <div>
                                        <strong style="font-size: 13px;">📋 ¿Necesitas una plantilla?</strong>
                                        <span style="font-size: 12px; color: #666; display: block;">Descarga el formato de ejemplo</span>
                                    </div>
                                    <a href="{{ route('import.plantilla') }}" class="btn-pro" style="padding: 6px 16px; font-size: 13px; text-decoration: none;">
                                        📄 Descargar Plantilla
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-pro" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-pro primary" id="importBtn">
                        📥 Importar Productos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function(){
  const $q = $('#q');

  const normalize = (s) => {
    return (s || '')
      .toString()
      .trim()
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/\s+/g, ' ')
      .trim();
  };

  const tokensFrom = (value) => normalize(value).split(' ').filter(Boolean);

  const getFilterValue = (name) => {
    return $(`input[name="${name}"]:checked`).val() || '';
  };

  const updatePackageTitle = () => {
    const visiblePackages = $('.pkg-card:visible').length;
    $('.package-section-title').toggle(visiblePackages > 0);
  };

  const applyFilters = () => {
    const text = normalize($q.val());
    const tokens = tokensFrom(text);

    const tipo = normalize(getFilterValue('filterTipo'));
    const subtipo = normalize(getFilterValue('filterSubtipo'));
    const marca = normalize(getFilterValue('filterMarca'));
    const stock = getFilterValue('filterStock') || 'all';

    let visibleCount = 0;

    $('.catalog-item').each(function(){
      const $card = $(this);

      const dataSearch = normalize($card.attr('data-search'));
      const dataTipo = normalize($card.attr('data-tipo'));
      const dataSubtipo = normalize($card.attr('data-subtipo'));
      const dataMarca = normalize($card.attr('data-marca'));
      const dataStock = ($card.attr('data-stock') || '').toString().trim();
      const kind = $card.attr('data-kind') || 'producto';

      let show = true;

      if (tokens.length) {
        show = tokens.every(token => dataSearch.includes(token));
      }

      if (show && tipo !== '') {
        show = dataTipo.includes(tipo);
      }

      if (show && subtipo !== '') {
        show = dataSubtipo.includes(subtipo);
      }

      if (show && marca !== '') {
        show = dataMarca.includes(marca);
      }

      if (show && stock !== 'all') {
        if (kind === 'paquete') {
          show = dataStock === stock;
        } else {
          const numericStock = parseInt(dataStock || '0', 10);
          show = stock === 'with_stock' ? numericStock > 0 : numericStock <= 0;
        }
      }

      $card.toggle(show);

      if (show) {
        visibleCount++;
      }
    });

    updatePackageTitle();

    $('#emptyCatalogMessage').remove();

    if (visibleCount === 0) {
      $('#mainGrid').append(`
        <div id="emptyCatalogMessage" class="empty-catalog-message">
          No se encontraron coincidencias.
        </div>
      `);
    }
  };

  $q.on('input', applyFilters);

  $('input[name="filterTipo"], input[name="filterSubtipo"], input[name="filterMarca"], input[name="filterStock"]').on('change', function(){
    const selectedTipo = normalize(getFilterValue('filterTipo'));

    $('.cat-link').removeClass('active');
    let foundTopLink = false;

    $('.cat-link').each(function(){
      const linkValue = normalize($(this).data('val') || '');

      if (linkValue === selectedTipo) {
        $(this).addClass('active');
        foundTopLink = true;
      }
    });

    if (!foundTopLink || selectedTipo === '') {
      $('.cat-link[data-val=""]').addClass('active');
    }

    applyFilters();
  });

  $('.cat-link').on('click', function(e){
    e.preventDefault();

    $('.cat-link').removeClass('active');
    $(this).addClass('active');

    const selectedValue = normalize($(this).data('val') || '');

    $('input[name="filterTipo"]').each(function(){
      const currentValue = normalize($(this).val() || '');
      $(this).prop('checked', currentValue === selectedValue);
    });

    applyFilters();
  });

  $('.delete-form').on('submit', function(e){
    e.preventDefault();

    const form = this;

    Swal.fire({
      title: '¿ELIMINAR?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#e53935',
      cancelButtonColor: '#000',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((r) => {
      if (r.isConfirmed) {
        form.submit();
      }
    });
  });

  function buildExportUrl(base, forceProductos = false){
    const q = ($q.val() || '').trim();
    const params = new URLSearchParams();

    if (q) {
      params.set('q', q);
    }

    const scope = forceProductos ? 'productos' : ($('#exportScope').val() || 'all');
    params.set('scope', scope);

    const t = getFilterValue('filterTipo');
    const s = getFilterValue('filterSubtipo');
    const m = getFilterValue('filterMarca');
    const st = getFilterValue('filterStock');

    if (t) params.set('tipo', t);
    if (s) params.set('subtipo', s);
    if (m) params.set('marca', m);
    if (st && st !== 'all') params.set('stock', st);

    params.set('ai', $('#exportUseAi').is(':checked') ? '1' : '0');

    const qs = params.toString();
    return qs ? (base + (base.includes('?') ? '&' : '?') + qs) : base;
  }

  const syncExportButtons = () => {
    $('#btnPdf').attr('href', buildExportUrl(@json(route('catalogo.export.pdf'))));
    $('#btnXlsx').attr('href', buildExportUrl(@json(route('catalogo.export.xlsx'))));
    $('#btnWooXlsx').attr('href', buildExportUrl(@json(route('productos.export.woocommerce')), true));
  };

  $('#modalExport').on('show.bs.modal', syncExportButtons);

  $('input[name="filterTipo"], input[name="filterSubtipo"], input[name="filterMarca"], input[name="filterStock"], #exportScope, #exportUseAi').on('change', syncExportButtons);

  $('#btnPdf, #btnXlsx, #btnWooXlsx').on('click', function() {
    Swal.fire({
      title: 'Generando archivo',
      text: 'Por favor, espere...',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    setTimeout(() => {
      Swal.close();

      const modalEl = document.getElementById('modalExport');
      const modalInstance = bootstrap.Modal.getInstance(modalEl);

      if (modalInstance) {
        modalInstance.hide();
      }
    }, 3000);
  });

  applyFilters();
  syncExportButtons();

  // ============================================
  // DROPZONE PARA IMPORTACIÓN - VERSIÓN SIMPLIFICADA
  // ============================================

  const dropzone = document.getElementById('dropzone');
  const fileInput = document.getElementById('archivoInput');
  const fileInfo = document.getElementById('fileInfo');
  const fileName = document.getElementById('fileName');
  const fileSize = document.getElementById('fileSize');
  const removeFileBtn = document.getElementById('removeFileBtn');
  const importForm = document.getElementById('importForm');

  if (dropzone && fileInput) {
    // Click en dropzone
    dropzone.addEventListener('click', function(e) {
      if (e.target.tagName !== 'INPUT') {
        fileInput.click();
      }
    });

    // Drag and drop
    dropzone.addEventListener('dragover', function(e) {
      e.preventDefault();
      this.style.borderColor = '#000';
      this.style.background = '#f0f0f0';
    });

    dropzone.addEventListener('dragleave', function(e) {
      e.preventDefault();
      this.style.borderColor = '#ddd';
      this.style.background = '#fafafa';
    });

    dropzone.addEventListener('drop', function(e) {
      e.preventDefault();
      this.style.borderColor = '#ddd';
      this.style.background = '#fafafa';
      
      if (e.dataTransfer.files.length) {
        fileInput.files = e.dataTransfer.files;
        mostrarArchivo(fileInput.files[0]);
      }
    });

    // Cambio de archivo
    fileInput.addEventListener('change', function() {
      if (this.files.length) {
        mostrarArchivo(this.files[0]);
      }
    });

    // Botón quitar archivo
    if (removeFileBtn) {
      removeFileBtn.addEventListener('click', function() {
        limpiarArchivo();
      });
    }

    function mostrarArchivo(file) {
      const ext = file.name.split('.').pop().toLowerCase();
      const validExtensions = ['csv', 'xls', 'xlsx'];
      
      if (!validExtensions.includes(ext)) {
        alert('Por favor, selecciona un archivo .csv, .xls o .xlsx');
        limpiarArchivo();
        return;
      }

      if (file.size > 10 * 1024 * 1024) {
        alert('El archivo no puede superar los 10MB');
        limpiarArchivo();
        return;
      }

      fileName.textContent = file.name;
      fileSize.textContent = `(${(file.size / 1024).toFixed(1)} KB)`;
      
      dropzone.style.display = 'none';
      fileInfo.style.display = 'flex';
    }

    function limpiarArchivo() {
      fileInput.value = '';
      dropzone.style.display = 'block';
      fileInfo.style.display = 'none';
    }

    // Validar antes de enviar
    if (importForm) {
      importForm.addEventListener('submit', function(e) {
        if (!fileInput.files.length) {
          e.preventDefault();
          alert('Por favor, selecciona un archivo para importar.');
          return false;
        }
        
        const file = fileInput.files[0];
        const validExtensions = ['csv', 'xls', 'xlsx'];
        const ext = file.name.split('.').pop().toLowerCase();
        
        if (!validExtensions.includes(ext)) {
          e.preventDefault();
          alert('Formato de archivo no válido. Usa .csv, .xls o .xlsx');
          return false;
        }
        
        return true;
      });
    }
  }

  // Limpiar modal al cerrar
  document.addEventListener('hidden.bs.modal', function (event) {
    if (event.target && event.target.id === 'modalImport') {
      limpiarArchivo();
    }
  });

});
</script>

@if(session('success'))
<script>
Swal.fire({
  toast: true,
  position: 'top-end',
  icon: 'success',
  title: @json(session('success')),
  showConfirmButton: false,
  timer: 3000
});
</script>
@endif

@if(session('error'))
<script>
Swal.fire({
  toast: true,
  position: 'top-end',
  icon: 'error',
  title: @json(session('error')),
  showConfirmButton: false,
  timer: 3500
});
</script>
@endif

@endsection