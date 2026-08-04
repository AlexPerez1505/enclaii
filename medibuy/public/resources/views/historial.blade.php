@extends('layouts.app')
@section('title', 'Historial')
@section('titulo', 'Historial de Cotizaciones')
@section('content')
<style>
    body{
        background: #F5FAFF;
    }
</style>
<body>
<div class="form-container-lower">
    <div class="table-responsive">
        <table id="cotizacionesTable" class="stripe row-border order-column" style="width:100%">
            <thead>
                <tr>
                    <th>Cotización</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Registrado por</th> <!-- Nueva columna -->
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cotizaciones as $cotizacion)
                    @php
                        $cliente = json_decode($cotizacion->cliente, true);
                        $nombreCliente = $cliente['nombre'] ?? 'Sin Nombre';
                        $estado = $cotizacion->valido_hasta && \Carbon\Carbon::parse($cotizacion->valido_hasta)->isPast() ? 'Vencida' : 'Activa';
                        $estadoClase = $estado === 'Activa' ? 'fila-activa' : 'fila-vencida';
                    @endphp
                    <tr class="{{ $estadoClase }}">
                        <td style="color: red;">2025-{{ $cotizacion->id }}</td>
                        <td>{{ $nombreCliente }}</td>
                        <td>{{ \Carbon\Carbon::parse($cotizacion->created_at)->format('d/m/Y') }}</td>
                        <td>{{ number_format($cotizacion->total, 2) }}</td>
                        <td>{{ $estado }}</td>
                        <td>{{ $cotizacion->registrado_por ?: 'Desconocido' }}</td> 
                        <td>
                            <button class="btn btn-descargapdf descargar-pdf" data-id="{{ $cotizacion->id }}">Descargar PDF</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection


@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function () {
    $('#cotizacionesTable').DataTable({
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sEmptyTable: "Ningún dato disponible en esta tabla",
            sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch: "Buscar:",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            }
        },
        dom: '<"top d-flex align-items-center"lB>frtip',
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<span class="button-icon"><img src="{{ asset('images/excel.png') }}" alt="Excel" height="20"></span><span class="button-text">Excel</span>',
                className: 'btn-excel',
                title: 'Historial de Cotizaciones de Grupo Medibuy',
                exportOptions: { columns: ':not(:last-child)' } // Excluir la última columna (Acciones)
            },
            {
                extend: 'csvHtml5',
                text: '<span class="button-icon"><img src="{{ asset('images/csv.png') }}" alt="CSV" height="20"></span><span class="button-text">CSV</span>',
                className: 'btn-csv',
                title: 'Historial de Cotizaciones de Grupo Medibuy',
                exportOptions: { columns: ':not(:last-child)' } // Excluir la última columna (Acciones)
            },
            {
                extend: 'pdfHtml5',
                text: '<span class="button-icon"><img src="{{ asset('images/pdf.png') }}" alt="PDF" height="20"></span><span class="button-text">PDF</span>',
                className: 'btn-pdf',
                title: 'Historial de Cotizaciones de Grupo Medibuy',
                exportOptions: { columns: ':not(:last-child)' } // Excluir la última columna (Acciones)
            }
        ]

    });
});

</script>
<script>
    $(document).on('click', '.descargar-pdf', function () {
    let id = $(this).data('id');

    fetch(`/descargar-cotizacion/${id}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.blob())
    .then(blob => {
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `cotizacion_${id}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    })
    .catch(error => console.error('Error al descargar el PDF:', error));
});

</script>
</body>
@endsection
