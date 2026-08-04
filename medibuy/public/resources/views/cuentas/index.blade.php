@extends('layouts.app')

@section('title', 'Viáticos')
@section('titulo', 'Viáticos')

@section('content')
<div class="container py-4">

    <h2 class="text-primary fw-bold mb-4 text-center">Registro de Cuentas</h2>

    <div class="d-flex justify-content-center flex-wrap gap-3 mb-4">
        <a href="{{ route('cuentas.create') }}" class="btn btn-azul shadow-sm px-4 py-2 rounded-3">
            <i class="bi bi-plus-circle me-1"></i> Nueva Cuenta
        </a>
        <a href="{{ route('cuentas.exportar.pdf') }}" class="btn btn-verde shadow-sm px-4 py-2 rounded-3">
            <i class="bi bi-file-earmark-pdf me-1"></i> Exportar PDF
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    @endif

    @if($cuentas->count() > 0)
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="bg-white rounded-4 shadow-sm p-3">
                <h5 class="text-primary mb-3">Listado de Cuentas</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light pastel-thead">
                            <tr>
                                <th>Lugar</th>
                                <th>Camioneta</th>
                                <th>Casetas</th>
                                <th>Gasolina</th>
                                <th>Viáticos</th>
                                <th>Adicional</th>
                                <th>Descripción</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cuentas as $cuenta)
                            <tr>
                                <td>{{ $cuenta->lugar }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $cuenta->camioneta }}</span></td>
                                <td>${{ number_format($cuenta->casetas, 2) }}</td>
                                <td>${{ number_format($cuenta->gasolina, 2) }}</td>
                                <td>${{ number_format($cuenta->viaticos, 2) }}</td>
                                <td>${{ number_format($cuenta->adicional, 2) }}</td>
                                <td class="text-start small">{{ $cuenta->descripcion }}</td>
                                <td><strong>${{ number_format($cuenta->total, 2) }}</strong></td>
                                <td class=" d-flex justify-content-center gap-2">
                                    {{-- Botón Editar --}}
                                    <a href="{{ route('cuentas.edit', $cuenta->id) }}" class="btn btn-warning btn-sm rounded-3">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Botón Eliminar --}}
                                    <form action="{{ route('cuentas.destroy', $cuenta->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta cuenta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-rojo btn-sm rounded-3">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Columna de gráficas --}}
        <div class="col-lg-5">
            <div class="bg-white rounded-4 shadow-sm p-3 mb-4">
                <h5 class="text-primary text-center mb-3">Totales por Lugar</h5>
                <div style="height: 360px;">
                    <canvas id="graficaTotales" class="w-100 h-100"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-4 shadow-sm p-3">
                <h5 class="text-primary text-center mb-3">Totales por Camioneta</h5>
                <div style="height: 320px;">
                    <canvas id="graficaCamionetas" class="w-100 h-100"></canvas>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info text-center pastel-azul rounded-4 shadow-sm mt-4">
        No hay cuentas registradas.
    </div>
    @endif

</div>

<style>
    body { background-color: #f4f6f9; }
    .btn-azul { background-color: #a5d8ff; color: #004085; }
    .btn-verde { background-color: #c3f0ca; color: #155724; }
    .btn-rojo { background-color: #ffc9c9; color: #721c24; height: 32px; width: 36px; display:grid; place-items:center; }
    .btn-rojo:hover { background-color: #ffaeb1; color: #664d03; }
    .btn-warning { background-color: #fff3cd; color: #856404; height: 32px; width: 36px; border: none; display:grid; place-items:center; }
    .btn-warning:hover { background-color: #ffe08a; color: #664d03; }
    .pastel-azul { background-color: #d0ebff; border: 1px solid #b0d4f1; padding: 10px; }
    .pastel-thead { background-color: #ffe5ec; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function colorPastelAleatorio() {
        const r = Math.floor(150 + Math.random() * 105);
        const g = Math.floor(150 + Math.random() * 105);
        const b = Math.floor(150 + Math.random() * 105);
        return `rgba(${r}, ${g}, ${b}, 0.7)`;
    }

    // ====== Gráfica por LUGAR (la que ya tenías) ======
    const coloresLugar = [
        @foreach($cuentas as $cuenta)
            colorPastelAleatorio(),
        @endforeach
    ];

    const ctxLugar = document.getElementById('graficaTotales').getContext('2d');
    new Chart(ctxLugar, {
        type: 'bar',
        data: {
            labels: [
                @foreach($cuentas as $cuenta)
                    '{{ $cuenta->lugar }} (ID {{ $cuenta->id }})',
                @endforeach
            ],
            datasets: [{
                label: 'Total ($)',
                data: [
                    @foreach($cuentas as $cuenta)
                        {{ $cuenta->total }},
                    @endforeach
                ],
                backgroundColor: coloresLugar,
                borderColor: '#339af0',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 50 } } },
            plugins: { legend: { display: false } }
        }
    });

    // ====== Gráfica agregada por CAMIONETA ======
    const cuentas = @json($cuentas->map->only(['camioneta','total'])->values());
    const totalesCamioneta = {};
    cuentas.forEach(c => {
        const key = c.camioneta || 'Sin especificar';
        totalesCamioneta[key] = (totalesCamioneta[key] || 0) + parseFloat(c.total || 0);
    });
    const labelsCam = Object.keys(totalesCamioneta);
    const dataCam = Object.values(totalesCamioneta);
    const coloresCam = labelsCam.map(() => colorPastelAleatorio());

    const ctxCam = document.getElementById('graficaCamionetas').getContext('2d');
    new Chart(ctxCam, {
        type: 'doughnut',
        data: {
            labels: labelsCam,
            datasets: [{
                data: dataCam,
                backgroundColor: coloresCam,
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const v = ctx.parsed || 0;
                            return `${ctx.label}: $${v.toLocaleString(undefined,{minimumFractionDigits:2})}`;
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
});
</script>
@endsection
