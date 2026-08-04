@extends('layouts.app')
@section('title', 'Asistencias')
@section('titulo', 'Asistencia')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<style>
    body { background: #F5FAFF; }
</style>

<body>

    {{-- Alerts --}}
    @if (session('error_asistencia'))
        <script>
            Swal.fire({ icon:'error', title:'Error', text:@json(session('error_asistencia')) });
        </script>
    @endif

    @if (session('error_permiso'))
        <script>
            Swal.fire({ icon:'error', title:'Permiso insuficiente', text:@json(session('error_permiso')) });
        </script>
    @endif

    @if (session('error_vacaciones'))
        <script>
            Swal.fire({ icon:'error', title:'Vacaciones agotadas', text:@json(session('error_vacaciones')) });
        </script>
    @endif

    @if (session('success'))
        <script>
            Swal.fire({ icon:'success', title:'¡Éxito!', text:@json(session('success')) });
        </script>
    @endif


    {{-- ✅ IMPORTAR EXCEL (1 o 2 archivos) --}}
    <div class="form-container" style="margin-bottom:16px;">
        <form action="{{ \Illuminate\Support\Facades\Route::has('asistencias.importarExcel') ? route('asistencias.importarExcel') : url('/asistencias/importar-excel') }}"
              method="POST"
              enctype="multipart/form-data"
              class="asistencias-form">
            @csrf

            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                <div>
                    <h4 style="margin:0; font-weight:800; color:#0f172a;">Importar Excel de Asistencias</h4>
                    <p style="margin:6px 0 0 0; color:#64748b; font-size:13px;">
                        Puedes subir 1 o 2 archivos. Se extraen horas y se registran entradas/salidas + comidas con tus reglas.
                    </p>
                </div>
            </div>

            <div class="form-group" style="margin-top:14px;">
                <label class="label_nomina">Excel Entrada / Salida (opcional):</label>
                <input type="file" name="archivo_entrada_salida" class="form-control select" accept=".xlsx,.xls,.csv">
                <small style="color:#64748b; display:block; margin-top:6px;">
                    Toma primera marca como entrada y última como salida.
                </small>
            </div>

            <div class="form-group">
                <label class="label_nomina">Excel Comida (opcional):</label>
                <input type="file" name="archivo_comida" class="form-control select" accept=".xlsx,.xls,.csv">
                <small style="color:#64748b; display:block; margin-top:6px;">
                    Detecta marcas dentro de ventanas de almuerzo/comida y calcula retardos.
                </small>
            </div>

            <button type="submit" class="btn btn-primary btn-custom" style="margin-top:6px;">
                Importar Excel
            </button>
        </form>
    </div>


    {{-- FORMULARIO MANUAL --}}
    <div class="form-container">
        <form action="{{ route('asistencias.store') }}" method="POST" class="asistencias-form">
            @csrf

            <div class="form-group">
                <label for="user_id" class="label_nomina">Empleado:</label>
                <div class="form-group">
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/nombre.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <select class="form-control" name="user_id" style="background-color:#fff; display:block; width:100%;" required>
                            <option value="">Selecciona</option>
                            @foreach ($usuarios as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label for="estado" class="label_nomina">Estado:</label>
                <div class="form-group">
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/asistencia.png') }}" alt="Acceso" class="icon2">
                        </div>

                        <select class="form-control" name="estado" style="background-color:#fff; display:block; width:100%;" required>
                            <option value="">Selecciona</option>
                            <option value="asistencia">Asistencia</option>
                            <option value="falta">Falta</option>
                            <option value="permiso">Permiso</option>
                            <option value="vacaciones">Vacaciones</option>
                            <option value="retardo">Retardo</option>
                            <option value="salida">Salida</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="fecha" class="label_nomina">Fecha:</label>
                    <input type="date" class="form-control select" name="fecha" required value="{{ \Carbon\Carbon::today()->toDateString() }}">
                </div>

                <div class="form-group">
                    <label for="hora" class="label_nomina">Hora:</label>
                    <input type="time" class="form-control select" name="hora" id="hora" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-custom">Registrar</button>
        </form>
    </div>


    {{-- JS --}}
    <script>
        // Poner hora actual al cargar
        document.addEventListener("DOMContentLoaded", function() {
            const now = new Date();
            const h = now.getHours().toString().padStart(2, '0');
            const m = now.getMinutes().toString().padStart(2, '0');
            const el = document.getElementById("hora");
            if (el) el.value = `${h}:${m}`;
        });
    </script>

    <script>
        // Oculta hora si falta/permiso/vacaciones
        document.addEventListener('DOMContentLoaded', function() {
            const estadoSelect = document.querySelector('select[name="estado"]');
            const horaInput = document.querySelector('input[name="hora"]');
            const horaGroup = horaInput ? horaInput.closest('.form-group') : null;

            function toggleHora() {
                if (!estadoSelect || !horaInput || !horaGroup) return;
                const estado = (estadoSelect.value || '').toLowerCase();

                if (estado === 'falta' || estado === 'permiso' || estado === 'vacaciones') {
                    horaGroup.style.display = 'none';
                    horaInput.required = false;
                    horaInput.value = '';
                } else {
                    horaGroup.style.display = 'block';
                    horaInput.required = true;
                }
            }

            if (estadoSelect) {
                estadoSelect.addEventListener('change', toggleHora);
                toggleHora();
            }
        });
    </script>

    <script>
        // Si ya tiene entrada, cambia automáticamente a "salida"
        document.addEventListener('DOMContentLoaded', function () {
            const userSelect = document.querySelector('select[name="user_id"]');
            const fechaInput = document.querySelector('input[name="fecha"]');
            const estadoSelect = document.querySelector('select[name="estado"]');

            async function verificarAsistencia() {
                const userId = userSelect?.value;
                const fecha = fechaInput?.value;
                if (!userId || !fecha) return;

                try {
                    const response = await fetch(`{{ url('/asistencia/verificar') }}?user_id=${encodeURIComponent(userId)}&fecha=${encodeURIComponent(fecha)}`);
                    const data = await response.json();

                    if (data.tieneEntrada) {
                        estadoSelect.value = 'salida';
                        estadoSelect.dispatchEvent(new Event('change'));

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'Empleado ya registrado',
                            text: 'El estado se cambió a "Salida" automáticamente.',
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true,
                        });
                    }
                } catch (error) {
                    console.error('Error al verificar asistencia:', error);
                }
            }

            userSelect?.addEventListener('change', verificarAsistencia);
            fechaInput?.addEventListener('change', verificarAsistencia);
        });
    </script>

</body>
@endsection
