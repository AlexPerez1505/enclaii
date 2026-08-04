@extends('layouts.app')

@section('title', 'Clientes')
@section('titulo', 'Agregar')

@section('content')
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
<script src="https://cdn.jsdelivr.net/gh/cfinke/Typo.js/typo/typo.js"></script>

<style>
    .form-contenedor {
    background: #ffffff;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 700px;
    text-align: center;
    animation: fade-in 0.5s ease-in-out;
    margin: auto; /* Centra el formulario */
    margin-top:100px;
    margin-bottom:130px;

}
    body {
        background-color: #f0f4f8;
        font-family: 'Inter', sans-serif;
    }

.sugerencias {
    position: absolute;
    z-index: 1000;
    width: 100%;
    top: 100%;
    left: 0;
    background: white;
    border: 1px solid #ccc;
    border-top: none;
    display: none;
    max-height: 140px;
    overflow-y: auto;
}
.sugerencias li {
    padding: 6px 12px;
    cursor: pointer;
}
.sugerencias li:hover {
    background-color: #f8f9fa;
}

</style>
<body>
    

<div class="form-contenedor container " style="margin-top:120px;">
    <form action="{{ route('clientes.store') }}" method="POST">
        @csrf
        <h5 class="titulos_encabezado mb-4"><strong>Nuevo Cliente</strong></h5>
      <div class="row">
    <!-- Nombre -->
    <div class="col-md-6 mb-3 position-relative">
        <div class="form-group">
            <label for="nombre" class="label_nomina">Nombre</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
                </div>
                <input type="text" name="nombre" id="nombre" class="form-control" style="background-color: #fff;" required autocomplete="off">
            </div>
            <small id="nombre-error" class="text-danger mt-1 d-block"></small>
            <ul id="nombre-sugerencias" class="list-group sugerencias"></ul>
        </div>
    </div>

    <!-- Apellido -->
    <div class="col-md-6 mb-3 position-relative">
        <div class="form-group">
            <label for="apellido" class="label_nomina">Apellido</label>
            <div class="input_consulta">
                <div class="icon-container2">
                    <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
                </div>
                <input type="text" name="apellido" id="apellido" class="form-control" style="background-color: #fff;" required autocomplete="off">
            </div>
            <small id="apellido-error" class="text-danger mt-1 d-block"></small>
            <ul id="apellido-sugerencias" class="list-group sugerencias"></ul>
        </div>
    </div>
</div>



<script>
    // Carga diccionario español Hunspell (deberías alojar o conseguir estos archivos)
    var dictionary;

    fetch('/dictionaries/es_ES.aff')
        .then(res => res.text())
        .then(affData => {
            fetch('/dictionaries/es_ES.dic')
                .then(res => res.text())
                .then(dicData => {
                    dictionary = new Typo("es_ES", affData, dicData, { platform: 'any' });
                });
        });

    function checkSpelling(word) {
        if (!dictionary) return true; // Diccionario no cargado aún

        return dictionary.check(word);
    }

    function getSuggestions(word) {
        if (!dictionary) return [];
        return dictionary.suggest(word);
    }

    // Ejemplo uso
    const word = "jose";
    if (!checkSpelling(word)) {
        console.log("Palabra incorrecta, sugerencias:", getSuggestions(word));
    }
</script>


        <div class="row">
   <!-- Teléfono -->
<div class="col-md-4 mb-3">
    <div class="form-group">
        <label for="telefono" class="label_nomina">Teléfono</label>
        <div class="input_consulta">
            <div class="icon-container2">
                <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
            </div>
            <input type="text" name="telefono" id="telefono"
                   class="form-control @error('telefono') is-invalid @enderror"
                   style="background-color: #fff;" value="{{ old('telefono') }}">
        </div>
        <small id="telefono-error" class="text-danger d-block mt-1"></small>
        @error('telefono')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Email -->
<div class="col-md-4 mb-3">
    <div class="form-group">
        <label for="email" class="label_nomina">Correo</label>
        <div class="input_consulta">
            <div class="icon-container2">
                <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
            </div>
            <input type="email" name="email" id="email"
                   class="form-control @error('email') is-invalid @enderror"
                   style="background-color: #fff;" value="{{ old('email') }}">
        </div>
        <small id="email-error" class="text-danger d-block mt-1"></small>
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

            <!-- Categoría -->
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label for="categoria_id" class="label_nomina">Categoría</label>
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <select name="categoria_id" class="form-control" style="background-color: #fff;" required>
                            <option value="">Seleccione una categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comentarios -->
        <div class="row">
            <div class="col-12 mb-3">
                <div class="form-group">
                    <label for="comentarios" class="label_nomina">Dirección</label>
                    <div class="input_consulta">
                        <div class="icon-container2">
                            <img src="{{ asset('images/placa.png') }}" alt="Acceso" class="icon2">
                        </div>
                        <textarea name="comentarios" class="form-control" style="background-color: #fff;" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botón -->
        <div class="text-end mt-4">
            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.5rem; border-radius: 0.5rem;">
                Guardar Cliente
            </button>
        </div>
    </form>
</div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const telefonoInput = document.getElementById('telefono');
        const emailInput = document.getElementById('email');
        const telefonoError = document.getElementById('telefono-error');
        const emailError = document.getElementById('email-error');
        const form = document.querySelector('form');

        let telefonoValido = true;
        let emailValido = true;

        async function checkUnique() {
            const telefono = telefonoInput.value.trim();
            const email = emailInput.value.trim();

            if (!telefono && !email) return;

            try {
                const response = await fetch('{{ route('clientes.check-unique') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ telefono, email })
                });

                const data = await response.json();

                // Limpiar errores
                telefonoError.textContent = '';
                emailError.textContent = '';
                telefonoInput.classList.remove('is-invalid');
                emailInput.classList.remove('is-invalid');

                telefonoValido = !data.error_telefono;
                emailValido = !data.error_email;

                if (data.error_telefono) {
                    telefonoError.textContent = data.error_telefono;
                    telefonoInput.classList.add('is-invalid');
                }

                if (data.error_email) {
                    emailError.textContent = data.error_email;
                    emailInput.classList.add('is-invalid');
                }

            } catch (error) {
                console.error('Error al validar unicidad:', error);
                telefonoValido = emailValido = false;
            }
        }

        telefonoInput.addEventListener('blur', checkUnique);
        emailInput.addEventListener('blur', checkUnique);

        form.addEventListener('submit', async function (e) {
            await checkUnique();

            if (!telefonoValido || !emailValido) {
                e.preventDefault();
                alert('Hay errores en el formulario:\n' +
                      (!telefonoValido ? '- Teléfono duplicado\n' : '') +
                      (!emailValido ? '- Correo duplicado\n' : '')
                );
            }
        });
    });
</script>



@endsection
