@extends('layouts.app')
@section('title', 'Agregar')
@section('titulo', 'Agregar Usuario')
@section('content')
<link rel="stylesheet" href="{{ asset('css/agregar.css') }}?v={{ time() }}">
<!-- Primero, carga jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Luego, carga Bootstrap -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<style>
    body{
        background: #F5FAFF;
    }
</style>
<body> 
    
    <div class="container">
  
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label for="nomina" class="label_nomina">Usuario</label>
            <div class="form-group">
                <div class="input_consulta">
                    <div class="icon-container2">
                        <img src="{{ asset('images/name.png') }}" alt="Nómina" class="icon2">
                    </div>
                    <input type="text" class="form-control" name="nomina" style="background-color: #ffff; display:block; width: 100%;" placeholder="Número de Usuario" required>
                </div>
            </div>

            <label for="password" class="label_nomina">Contraseña</label>
<div class="form-group">
    <div class="input_consulta">
        <div class="icon-container2">
            <img src="{{ asset('images/contraseña.png') }}" alt="Contraseña" class="icon2">
        </div>
        <input type="password" class="form-control"style="background-color: #ffff; display:block; width: 100%;" name="password" id="password" placeholder="Contraseña" required>
        <img src="{{ asset('images/ojo.png') }}" alt="Ver" class="toggle-password" id="verPassword">
    </div>
</div>

        

            <label for="email" class="label_nomina">Correo Electrónico</label>
            <div class="form-group">
                <div class="input_consulta">
                    <div class="icon-container2">
                        <img src="{{ asset('images/correo.png') }}" alt="Correo Electrónico" class="icon2">
                    </div>
                    <input type="email" class="form-control" name="email" style="background-color: #ffff; display:block; width: 100%;" placeholder="Correo Electrónico" required>
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <div style="width: 50%;">
                    <label for="name" class="label_nomina">Nombre</label>
                    <div class="form-group">
                        <div class="input_consulta">
                            <div class="icon-container2">
                                <img src="{{ asset('images/nombre.png') }}" alt="Nombre" class="icon2">
                            </div>
                            <input type="text" class="form-control" name="name" style="background-color: #ffff; display:block; width: 100%;" placeholder="Nombre" required>
                        </div>
                    </div>
                </div>
                <div style="width: 50%;">
                    <label for="phone" class="label_nomina">Teléfono</label>
                    <div class="form-group">
                        <div class="input_consulta">
                            <div class="icon-container2">
                                <img src="{{ asset('images/telefono.png') }}" alt="Teléfono" class="icon2">
                            </div>
                            <input type="text" class="form-control" name="phone" style="background-color: #ffff; display:block; width: 100%;" placeholder="Teléfono" required>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <div style="width: 50%;">
                    <label for="cargo" class="label_nomina">Cargo</label>
                    <div class="form-group">
                        <div class="input_consulta">
                            <div class="icon-container2">
                                <img src="{{ asset('images/puesto.png') }}" alt="Cargo" class="icon2">
                            </div>
                            <input type="text" class="form-control" name="cargo" style="background-color: #ffff; display:block; width: 100%;" placeholder="Cargo" required>
                        </div>
                    </div>
                </div>
                <div style="width: 50%;">
                    <label for="puesto" class="label_nomina">Puesto</label>
                    <div class="form-group">
                        <div class="input_consulta">
                            <div class="icon-container2">
                                <img src="{{ asset('images/puesto.png') }}" alt="Puesto" class="icon2">
                            </div>
                            <input type="text" class="form-control" name="puesto" style="background-color: #ffff; display:block; width: 100%;" placeholder="Puesto" required>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 10px;">
                <div style="width: 33%;">
                    <label for="vacaciones_disponibles" class="label_nomina">Vacaciones</label>
                    <div class="form-group">
                        <div class="input_consulta">
                            <div class="icon-container2">
                                <img src="{{ asset('images/vacaciones.png') }}" alt="Vacaciones" class="icon2">
                            </div>
                            <input type="text" class="form-control" name="vacaciones_disponibles" style="background-color: #ffff; display:block; width: 100%;" placeholder="Vacaciones disponibles">   
                        </div>
                    </div>
                </div>
                <div style="width: 33%;">
                    <label for="permisos" class="label_nomina">Permisos</label>
                    <div class="form-group">
                        <div class="input_consulta">
                            <div class="icon-container2">
                                <img src="{{ asset('images/salida.png') }}" alt="Permisos" class="icon2">
                            </div>
                            <input type="text" class="form-control" name="permisos" style="background-color: #ffff; display:block; width: 100%;" placeholder="Permisos Disponibles">
                        </div>
                    </div>
                </div>
                <div style="width: 33%;">
                    <label for="role" class="label_nomina">Acceso</label>
                    <div class="form-group">
                        <div class="input_consulta">
                            <div class="icon-container2">
                                <img src="{{ asset('images/acceso.png') }}" alt="Acceso" class="icon2">
                            </div>
                            <select class="form-control" name="role" style="background-color: #ffff; display:block; width: 100%;" required>
                                <option value="">Selecciona</option>
                                <option value="admin">Acceso Completo</option>
                                <option value="editor">Acceso Medio</option>
                                <option value="user">Usuario</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

           
       

            <button type="submit" class="btn btn-primary btn-custom">Registrar</button>



        </form>
    </div>
    <!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="usuario_creado" tabindex="-1" role="dialog" aria-labelledby="UsuarioCreadoLabel" 
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header encabezado_modal text-center">
                <h5 class="modal-title titulo_modal">¡Usuario guardado exitosamente!</h5>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('images/confirmar.jpeg') }}" alt="Logo de encabezado" class="logo-modal">
                </div>
                <p class="text-center mensaje-modal">
                    El usuario se ha registrado correctamente en el sistema.  
                    Puedes proceder a cerrar este mensaje.  
                    <b>Grupo MediBuy</b>.
                </p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
            <button class="btn btn-listo btn-reducido" onclick="cerrarModal()">Listo</button>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <script type="text/javascript">
        $(document).ready(function() {
            // Abre el modal solo si hay un mensaje de éxito
            $('#usuario_creado').modal('show');
        });
    </script>
@endif
<script>
    function cerrarModal() {
    $('#usuario_creado').modal('hide'); // Cierra el modal
}

$(document).ready(function() {
    // Verifica si hay un mensaje de éxito para abrir el modal
    @if(session('success'))
        $('#usuario_creado').modal('show'); // Muestra el modal si hay un éxito
    @endif
});

</script>



<script>
    document.getElementById('verPassword').addEventListener('click', function() {
    var passwordField = document.getElementById('password');
    var icon = this;

    // Cambiar el tipo de la contraseña entre 'password' y 'text'
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.src = "{{ asset('images/ojo.png') }}"; // Cambia la imagen a "ojo abierto"
    } else {
        passwordField.type = 'password';
        icon.src = "{{ asset('images/ojo.png') }}"; // Cambia la imagen a "ojo cerrado"
    }
});

</script>

</body>



@endsection
