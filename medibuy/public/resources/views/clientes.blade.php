@extends('layouts.app')
@section('title', 'Lista de Clientes')
@section('titulo', 'Lista de Clientes')
@section('content')
<style>
    body{
        background: #F5FAFF;
    }
</style>
<body>
<div class="form-container-lower">
<div class="table-responsive">
    <table id="clientesTable" class="stripe row-border order-column" style="width:100%">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Correo Electrónico</th>
                <th>Teléfono</th>
                <th>Dirección</th>
                <th>Fecha de Registro</th>
                <th>Asesor de Ventas</th> <!-- Nueva columna -->
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->nombre }}</td>
                    <td>{{ $cliente->apellido }}</td>
                    <td>{{ $cliente->email }}</td>
                    <td>{{ $cliente->telefono }}</td>
                    <td>{{ $cliente->comentarios }}</td>
                    <td>{{ $cliente->created_at->format('d/m/Y') }}</td>
                    <td>
                        <select class="asesor-select" data-cliente-id="{{ $cliente->id }}">
                            <option value="">Seleccionar</option>
                            <option value="Jesús Tellez" {{ $cliente->asesor == 'Jesús Tellez' ? 'selected' : '' }}>Jesús Tellez</option>
                            <option value="Gabriela Diaz" {{ $cliente->asesor == 'Gabriela Diaz' ? 'selected' : '' }}>Gabriela Diaz</option>
                            <option value="Joel Diaz" {{ $cliente->asesor == 'Joel Diaz' ? 'selected' : '' }}>Joel Diaz</option>
                            <option value="Anahí Tellez" {{ $cliente->asesor == 'Anahí Tellez' ? 'selected' : '' }}>Anahí Tellez</option>
                        </select>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
        <tr>
                <th><input type="text" placeholder="Buscar Nombre" /></th>
                <th><input type="text" placeholder="Buscar Apellido" /></th>
                <th><input type="text" placeholder="Buscar Email" /></th>
                <th><input type="text" placeholder="Buscar Teléfono" /></th>
                <th><input type="text" placeholder="Buscar Dirección" /></th>
                <th><input type="text" placeholder="Buscar Fecha" /></th>
                <th><input type="text" placeholder="Buscar Asesor" data-index="6" /></th>

            </tr>
        </tfoot>
    </table>
</div>
</div>
@endsection

@section('scripts')
<!-- Cargar jQuery y DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function () {
    function initDataTable() {
        $('#clientesTable tfoot th').each(function (i) {
            const title = $('#clientesTable thead th').eq($(this).index()).text();
            $(this).html(`<input type="text" placeholder="Buscar ${title}" data-index="${i}" />`);
        });

        var table = $('#clientesTable').DataTable({
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
            dom: '<"top d-flex align-items-center"lB>frtip', // 🔹 Alinear botones junto al selector de registros
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '<span class="button-icon"><img src="{{ asset('images/excel.png') }}" alt="Excel" height="20"></span><span class="button-text">Excel</span>',
className: 'btn-excel',

                    title: 'Lista de Clientes de Grupo Medibuy',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function (data, row, column, node) {
                                if ($(node).find('select').length) {
                                    return $(node).find('select option:selected').text();
                                }
                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: '<span class="button-icon"><img src="{{ asset('images/csv.png') }}" alt="CSV" height="20"></span><span class="button-text">CSV</span>',
className: 'btn-csv',

                    title: 'Lista de Clientes de Grupo Medibuy',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function (data, row, column, node) {
                                if ($(node).find('select').length) {
                                    return $(node).find('select option:selected').text();
                                }
                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                   text: '<span class="button-icon"><img src="{{ asset('images/pdf.png') }}" alt="PDF" height="20"></span><span class="button-text">PDF</span>',
className: 'btn-pdf',

                    title: 'Lista de Clientes de Grupo Medibuy',
                    exportOptions: {
                        columns: ':visible',
                        format: {
                            body: function (data, row, column, node) {
                                if ($(node).find('select').length) {
                                    return $(node).find('select option:selected').text();
                                }
                                return data;
                            }
                        }
                    }
                }
            ]
        });

        // 🔍 Filtrado de búsqueda en el footer
        $('#clientesTable tfoot input').on('keyup change', function () {
            var index = $(this).data('index');
            table.column(index).search(this.value).draw();
        });

        // 🔍 Filtro especial para la columna "Asesor"
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            var inputValue = $('#clientesTable tfoot input[data-index="6"]').val().toLowerCase();
            var asesorText = $($('#clientesTable tbody tr').eq(dataIndex).find('td').eq(6)).find('select option:selected').text().toLowerCase();

            return inputValue === "" || asesorText.includes(inputValue);
        });
    }

    initDataTable();
    // Capturar el cambio de asesor y guardar en la BD
$(document).on('change', '.asesor-select', function () {
    var clienteId = $(this).data('cliente-id');
    var asesor = $(this).val();

    $.ajax({
        url: "{{ route('clientes.updateAsesor') }}",
        method: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            id: clienteId,
            asesor: asesor
        },
        success: function (response) {
            console.log("Asesor actualizado", response);
        },
        error: function (xhr) {
            console.error("Error al actualizar asesor", xhr.responseText);
        }
    });
});

});


</script>
</body>
@endsection
