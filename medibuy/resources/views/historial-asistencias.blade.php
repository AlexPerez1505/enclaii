@extends('layouts.app')

@section('title', 'Historial de Asistencias')
@section('titulo', 'Asistencias')

@section('content')
<style>
    body {
        background-color: #fdf2f8;
        font-family: 'Inter', sans-serif;
    }

    .asistencia-container {
        max-width: 800px;
        margin: 3rem auto;
        padding: 2.5rem;
        background-color: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h3 {
        color: #be185d;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    label {
        font-weight: 600;
        color: #7c3aed;
    }

    select.form-control, input[type="date"] {
        background-color: #fef2f2;
        border: 1px solid #f3e8ff;
        border-radius: 0.65rem;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        width: 100%;
    }

    select:focus, input[type="date"]:focus {
        border-color: #f472b6;
        box-shadow: 0 0 0 3px rgba(252, 231, 243, 0.5);
        outline: none;
    }

    .table thead th {
        background-color: #fce7f3;
        color: #7c3aed;
        font-weight: 600;
        text-align: center;
    }

    .table tbody td {
        vertical-align: middle;
        text-align: center;
    }

    .alert-warning {
        background-color: #fff7ed;
        border-left: 5px solid #f59e0b;
        color: #78350f;
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        font-size: 0.95rem;
    }

    .alert-info {
        background-color: #e0f2fe;
        border-left: 5px solid #0284c7;
        color: #0369a1;
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        margin-bottom: 1rem;
    }

    .alert-success {
        background-color: #dcfce7;
        border-left: 5px solid #16a34a;
        color: #14532d;
        padding: 1rem 1.25rem;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 576px) {
        .asistencia-container { padding: 1.5rem; }
        .table { font-size: 0.85rem; }
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="asistencia-container">
    <h3>Reporte de Asistencias</h3>
@if (request('fecha_inicio') && request('fecha_fin'))
    <div class="alert alert-info">
        Mostrando asistencias del <strong>{{ \Carbon\Carbon::parse(request('fecha_inicio'))->format('d/m/Y') }}</strong> al
        <strong>{{ \Carbon\Carbon::parse(request('fecha_fin'))->format('d/m/Y') }}</strong>.
    </div>
@endif

    {{-- Selector de usuario --}}
    <form action="{{ route('asistencias.historial') }}" method="GET">
        <div class="mb-4">
            <label for="usuarioHistorial" class="form-label">Selecciona un usuario:</label>
            <select name="id" id="usuarioHistorial" class="form-control" required onchange="this.form.submit()">
                <option value="">-- Selecciona un usuario --</option>
                @foreach ($usuarios as $user)
                    <option value="{{ $user->id }}" {{ $id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if ($id && count($asistencias))

        {{-- Última asistencia destacada --}}
        @php
            $ultima = $asistencias->sortByDesc('fecha')->first();
        @endphp
        @if ($ultima)
            <div class="alert alert-success">
                <strong>Última asistencia:</strong>
                {{ \Carbon\Carbon::parse($ultima->fecha)->format('d/m/Y') }} a las {{ $ultima->hora }}
            </div>
        @endif

        {{-- Resumen con valores iniciales (se actualizará con JS) --}}
        <div id="resumen" class="alert alert-info">
            <strong>Resumen:</strong><br>
            Total registros: <span id="totalRegistros">{{ $asistencias->count() }}</span><br>
            Asistencias: <span id="asistenciasCount">0</span><br>
            Ausencias: <span id="ausenciasCount">0</span><br>
            Retardos: <span id="retardosCount">0</span>
        </div>

{{-- Filtro por rango de fechas --}}
<form method="GET" action="{{ route('asistencias.historial') }}" class="mb-4">
    <input type="hidden" name="id" value="{{ $id }}">

    <div class="row">
        <div class="col-md-6 mb-2">
            <label for="fecha_inicio">Desde:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
        </div>
        <div class="col-md-6 mb-2">
            <label for="fecha_fin">Hasta:</label>
            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
        </div>
    </div>

    <button type="submit" class="btn btn-primary mt-2">
        <i class="bi bi-funnel-fill"></i> Filtrar
    </button>
</form>



        {{-- Tabla --}}
        <h5 class="mb-3 text-center">Historial de {{ $usuarios->firstWhere('id', $id)->name }}:</h5>
        <div class="table-responsive">
           <table id="tablaAsistencias" class="table table-bordered mt-2">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Hora Entrada</th>
            <th>Hora Salida</th>  {{-- Nueva columna --}}
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($asistencias as $asistencia)
            <tr>
                <td>{{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}</td>
                <td>{{ $asistencia->hora }}</td>
                <td>{{ $asistencia->hora_salida ?? 'N/A' }}</td> {{-- Mostrar hora salida o N/A si no existe --}}
                <td>
                    @switch(strtolower($asistencia->estado))
                        @case('asistencia')
                        @case('asistió')
                            <span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Asistencia</span>
                            @break
                        @case('falta')
                        @case('faltó')
                            <span class="text-danger fw-bold"><i class="bi bi-x-circle-fill"></i> Falta</span>
                            @break
                        @case('retardo')
                            <span class="text-warning fw-bold"><i class="bi bi-clock-fill"></i> Retardo</span>
                            @break
                        @default
                            <span>{{ ucfirst($asistencia->estado) }}</span>
                    @endswitch
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

        </div>

    @elseif($id)
        <div class="alert alert-warning mt-4">
            No se encontraron asistencias para este usuario.
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabla = document.getElementById('tablaAsistencias');
    if (!tabla) return;

    let total = 0, asistencias = 0, ausencias = 0, retardos = 0;

    const filas = tabla.querySelectorAll('tbody tr');
    filas.forEach(fila => {
        total++;
        const estadoCell = fila.cells[3];

        // Sacamos el texto visible (sin iconos)
        let estadoText = estadoCell.textContent.trim().toLowerCase();

        if (estadoText.includes('asistencia') || estadoText.includes('asistió')) {
            asistencias++;
        } else if (estadoText.includes('falta') || estadoText.includes('faltó')) {
            ausencias++;
        } else if (estadoText.includes('retardo')) {
            retardos++;
        }
    });

    document.getElementById('totalRegistros').textContent = total;
    document.getElementById('asistenciasCount').textContent = asistencias;
    document.getElementById('ausenciasCount').textContent = ausencias;
    document.getElementById('retardosCount').textContent = retardos;
});
</script>
@endsection