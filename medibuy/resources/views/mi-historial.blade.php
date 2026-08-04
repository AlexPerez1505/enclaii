@extends('layouts.app')

@section('title', 'Mi Historial de Asistencias')
@section('titulo', 'Mis Asistencias')

@section('content')
<style>
    body {
    background-color: #f7fbff; /* Verde muy claro */
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

<div class="asistencia-container">
    <h3>Mi Historial de Asistencias</h3>

    @if (request('fecha_inicio') && request('fecha_fin'))
        <div class="alert alert-info">
            Mostrando asistencias del <strong>{{ \Carbon\Carbon::parse(request('fecha_inicio'))->format('d/m/Y') }}</strong> al
            <strong>{{ \Carbon\Carbon::parse(request('fecha_fin'))->format('d/m/Y') }}</strong>.
        </div>
    @endif

    {{-- Filtro por fechas --}}
    <form method="GET" action="{{ route('mi-historial') }}" class="mb-4">
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

    @if (count($asistencias))
        @php $ultima = $asistencias->sortByDesc('fecha')->first(); @endphp
        @if ($ultima)
            <div class="alert alert-success">
                <strong>Última asistencia:</strong>
                {{ \Carbon\Carbon::parse($ultima->fecha)->format('d/m/Y') }} a las {{ $ultima->hora }}
            </div>
        @endif

        <div id="resumen" class="alert alert-info">
            <strong>Resumen:</strong><br>
            Total registros: <span id="totalRegistros">{{ $asistencias->count() }}</span><br>
            Asistencias: <span id="asistenciasCount">0</span><br>
            Ausencias: <span id="ausenciasCount">0</span><br>
            Retardos: <span id="retardosCount">0</span>
        </div>

        {{-- Tabla de asistencias --}}
        <div class="table-responsive">
            <table id="tablaAsistencias" class="table table-bordered mt-2">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora Entrada</th>
                        <th>Hora Salida</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($asistencias as $a)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($a->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $a->hora }}</td>
                            <td>{{ $a->hora_salida ?? 'N/A' }}</td>
                            <td>
                                @switch(strtolower($a->estado))
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
                                        <span>{{ ucfirst($a->estado) }}</span>
                                @endswitch
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-warning mt-4">No se encontraron asistencias.</div>
    @endif
     <!-- Botón Volver -->
    <div class="mt-4 text-center">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle"></i> Volver
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabla = document.getElementById('tablaAsistencias');
    if (!tabla) return;

    let total = 0, asistencias = 0, ausencias = 0, retardos = 0;

    const filas = tabla.querySelectorAll('tbody tr');
    filas.forEach(fila => {
        total++;
        const estadoText = fila.cells[3].textContent.trim().toLowerCase();
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
