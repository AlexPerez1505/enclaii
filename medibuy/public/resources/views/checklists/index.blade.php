@extends('layouts.app')

@section('title', 'Checklists')
@section('titulo', 'Checklists')

@section('content')
<style>
    :root {
        --pastel-blue: #d0ebff;
        --accent-blue: #228be6;
        --pastel-green: #d3f9d8;
        --accent-green: #38b000;
        --pastel-yellow: #fff3bf;
        --accent-yellow: #f59f00;
        --pastel-red: #ffd6d6;
        --accent-red: #fa5252;
        --pastel-gray: #f1f3f5;
    }
    body {
        background: var(--pastel-gray);
    }
    .main-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(100,100,100,.09);
        padding: 32px 24px 24px 24px;
        margin-bottom: 32px;
        animation: fadeIn .7s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px);}
        to { opacity: 1; transform: none;}
    }
    h1 {
        color: var(--accent-blue);
        font-weight: 700;
        font-size: 2rem;
        letter-spacing: -1px;
    }
    table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        margin-top: 16px;
    }
    th {
        background: var(--pastel-blue);
        color: var(--accent-blue);
        border: none;
        font-weight: 600;
    }
    td {
        vertical-align: middle !important;
    }
    .btn-action {
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        border-radius: 12px;
        font-size: 1.3rem;
        margin: 0 3px;
        box-shadow: 0 2px 8px rgba(150,180,230,.06);
        transition: box-shadow .16s, transform .16s, background .16s;
        cursor: pointer;
    }
    .btn-action:focus { outline: none; box-shadow: 0 0 0 2px #b3dafe; }
    .btn-view    { background: var(--pastel-blue); }
    .btn-view i  { color: var(--accent-blue); }
    .btn-edit    { background: var(--pastel-green); }
    .btn-edit i  { color: var(--accent-green); }
    .btn-delete  { background: var(--pastel-red); }
    .btn-delete i{ color: var(--accent-red); }
    .btn-action:hover {
        box-shadow: 0 4px 18px rgba(150,180,230,.17);
        transform: scale(1.09);
        filter: brightness(1.10);
    }
    .btn-add {
        background: var(--pastel-blue);
        color: var(--accent-blue);
        border: none;
        font-weight: 600;
        border-radius: 12px;
        padding: 8px 22px;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px rgba(150,180,230,.08);
        transition: box-shadow .2s, transform .2s, filter .2s;
    }
    .btn-add:hover {
        filter: brightness(1.07);
        box-shadow: 0 4px 16px rgba(150,180,230,.15);
    }
    .btn-wizard { transition: box-shadow .2s, filter .2s, background .2s; }
    .btn-wizard-pend {
        background: var(--pastel-yellow) !important;
    }
    .btn-wizard-pend i { color: var(--accent-yellow); }
    .btn-wizard-ok {
        background: var(--pastel-green) !important;
    }
    .btn-wizard-ok i { color: var(--accent-green); }
    @media (max-width: 650px){
        table, thead, tbody, th, td, tr { display: block; }
        th, td { border: none; }
        tr { margin-bottom: 1rem; }
    }
</style>

<div class="container mt-4">
    <div class="main-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Checklists</h1>
            <a href="{{ route('checklists.create') }}" class="btn btn-add">+ Nuevo Checklist</a>
        </div>
        @if(session('success'))
            <div class="alert alert-success mb-3">
                {{ session('success') }}
            </div>
        @endif
        <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Etapa</th>
                    <th>Responsable</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Tipo de entrega</th>
                    <th style="width:150px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($checklists as $c)
                <tr>
                    <td>{{ $c->item->nombre }}</td>
                    <td>{{ ucfirst($c->etapa) }}</td>
                    <td>{{ $c->usuario->name ?? '' }}</td>
                    <td>{{ $c->fecha_inicio }}</td>
                    <td>{{ $c->fecha_fin }}</td>
                    <td>{{ $c->tipo_entrega }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('checklists.show', $c) }}" class="btn-action btn-view" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('checklists.edit', $c) }}" class="btn-action btn-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('checklists.destroy', $c) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('¿Eliminar este checklist?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            {{-- Botón Wizard, cambia de color si terminado --}}
                            @php
                                // Reemplaza por tus condiciones de finalización reales:
                                $terminado = $c->ingenieria_completado && $c->embalaje_completado && $c->entrega_completado;
                            @endphp
                            <a href="{{ route('checklists.proceso', $c) }}"
                               class="btn-action btn-wizard {{ $terminado ? 'btn-wizard-ok' : 'btn-wizard-pend' }}"
                               title="{{ $terminado ? 'Checklist COMPLETO' : 'Proceso por pasos' }}">
                                <i class="bi bi-list-check"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Sin checklists registrados aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection
