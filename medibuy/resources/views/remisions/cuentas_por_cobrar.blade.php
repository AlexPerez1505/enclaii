@extends('layouts.app')

@section('content')
<!-- Bootstrap JS (necesario para los modales) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<style>
    body {
        background-color: #f7fbff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    h2 {
        color: #26547c;
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
        font-size: 1.8rem;
    }

    .search-container {
        max-width: 500px;
        margin: 0 auto 40px;
        position: relative;
    }

    .search-container input {
        width: 100%;
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid #d0e6f7;
        font-size: 0.95rem;
        background-color: #ffffff;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }

    .search-container input:focus {
        outline: none;
        border-color: #8ce3d2;
        box-shadow: 0 0 0 3px rgba(140, 227, 210, 0.3);
    }

    .remision-card {
        background-color: #ffffff;
        border: 1px solid #e3edf7;
        border-radius: 16px;
        margin-bottom: 30px;
        transition: all 0.3s ease;
        padding: 20px;
    }

    .remision-card:hover {
        border-color: #c5dff4;
        background-color: #f9fdff;
    }

    .remision-header {
        background-color: #dbefff;
        padding: 12px 20px;
        border-radius: 12px;
        color: #26547c;
        font-weight: 500;
        margin-bottom: 15px;
        font-size: 1rem;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    .table thead {
        background-color: #eaf6ff;
    }

    .table th, .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #edf2f7;
        font-size: 0.95rem;
    }

    .table th {
        color: #26547c;
        font-weight: 600;
    }

    .table td {
        color: #333;
    }

    .btn-primary {
        background-color: #b2f0e4;
        color: #00695c;
        border: none;
        font-weight: 500;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 0.875rem;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #8ce3d2;
        color: #004d40;
    }
    .btn-custom {
    background-color: #d0e1ff; /* azul suave */
    color: #003366; /* azul oscuro */
    border: none;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.875rem;
    transition: background-color 0.3s ease;
    text-decoration: none;
    display: inline-block;
}

.btn-custom:hover {
    background-color: #aac7ff;
    color: #001f4d;
}
.btn-custom-alt {
    background-color: #e3d0ff; /* morado claro */
    color: #4a148c; /* morado oscuro */
    border: none;
    font-weight: 500;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.875rem;
    transition: background-color 0.3s ease;
}

.btn-custom-alt:hover {
    background-color: #d1b3ff;
    color: #310064;
}


    @media (max-width: 768px) {
        .table th, .table td {
            font-size: 0.85rem;
            padding: 10px;
        }

        .remision-header {
            font-size: 0.95rem;
        }
    }
</style>
<div class="container" style="margin-top: 90px;">


    <!-- Buscador -->
    <div class="search-container">
        <input type="text" id="buscador" placeholder="Buscar por cliente o ítem...">
    </div>

    <!-- Tarjetas -->
    <div id="remision-list">
        @foreach($remisiones as $remision)
            <div class="remision-card mb-4 p-3 border rounded">
                <div class="remision-header mb-2">
                    <strong>Cliente:</strong> {{ $remision->cliente->nombre }} |
                    <strong>Fecha:</strong> {{ $remision->created_at->format('d/m/Y') }}
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Ítem</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>A Cuenta</th>
                                <th>Restante</th>
                                <th>Pagos</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($remision->items as $item)
                                <tr>
                                    <td>{{ $item->nombre_item }}</td>
                                    <td>{{ $item->cantidad }}</td>
                                    <td>${{ number_format($item->subtotal, 2) }}</td>
                                    <td>${{ number_format($item->a_cuenta, 2) }}</td>
                                    <td><strong>${{ number_format($item->restante, 2) }}</strong></td>
                                    <td>
                                        <!-- Botón para abrir el modal -->
                                     <button type="button" class="btn-custom-alt" data-bs-toggle="modal" data-bs-target="#modalPago-{{ $item->id }}">
    Registrar pago
</button>


                                        <!-- Acciones -->
                                        <div class="mt-2 d-flex flex-wrap gap-1">
<a href="{{ route('pagos.index', $item->id) }}" class="btn-custom">Ver pagos</a>
<a href="{{ route('pagos.recibo.pdf', $item->id) }}" class="btn-custom" target="_blank">Recibo PDF</a>

                                        </div>

                                        <!-- Modal -->
                                        <div class="modal fade" id="modalPago-{{ $item->id }}" tabindex="-1" aria-labelledby="modalPagoLabel-{{ $item->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('pagos.store') }}" method="POST">
                                                        @csrf
                                                        <div class="modal-heade">
                                                            <h5 class="modal-title" id="modalPagoLabel-{{ $item->id }}">Registrar Pago</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="item_remision_id" value="{{ $item->id }}">

                                                            <div class="mb-3">
                                                                <label>Monto</label>
                                                                <input type="number" name="monto" class="form-control" placeholder="$" step="0.01" min="0.01" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label>Fecha de pago</label>
                                                                <input type="date" name="fecha_pago" class="form-control" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label>Método de pago</label>
                                                                <select name="metodo_pago" class="form-select" required>
                                                                    <option value="efectivo">Efectivo</option>
                                                                    <option value="transferencia">Transferencia</option>
                                                                    <option value="tarjeta">Tarjeta</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-success">Guardar pago</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Fin Modal -->
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-3">
                    <a href="{{ route('remisions.show', $remision->id) }}" class="btn btn-primary">Ver Remisión</a>
                </div>
            </div>
        @endforeach
    </div>
</div>


<script>
    document.getElementById('buscador').addEventListener('keyup', function () {
        const filtro = this.value.toLowerCase();
        const remisiones = document.querySelectorAll('#remision-list .remision-card');

        remisiones.forEach(card => {
            const headerText = card.querySelector('.remision-header').textContent.toLowerCase();
            const tableText = card.querySelector('.table').textContent.toLowerCase();

            if (headerText.includes(filtro) || tableText.includes(filtro)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>
@endsection
