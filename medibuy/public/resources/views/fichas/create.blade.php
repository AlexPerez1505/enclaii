@extends('layouts.app')

@section('title', 'FichaTecnica')
@section('titulo', 'Ficha Tecnica')

@section('content')
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('css/sweetalert-custom.css') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="form-container">
    <form id="fichaForm" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <div class="form-group">
                <label class="label_nomina">Nombre de la ficha</label>
                <div class="input_consulta">
                    <div class="icon-container2">
                        <img src="{{ asset('images/fichatecnica.png') }}" alt="Acceso" class="icon2">
                    </div>
                    <input type="text" name="nombre" class="form-control" style="background-color: #ffff; display:block; width: 100%;" required>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <div class="form-group">
                <label class="label_nomina">Archivo PDF</label>
                <div class="info-icon">?
                    <div class="info-tooltip text-center">Solo archivos en formato PDF</div>
                </div>
                <div class="d-flex align-items-start input-containerr">
                    <label for="archivo" class="file-input-label d-flex align-items-center">
                        <div class="icon-containerr me-2">
                            <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de archivo" class="icon-pin">
                        </div>
                        <span id="file-label-text-1">Seleccione un archivo</span>
                    </label>
                    <input type="file" id="archivo" name="archivo" class="file-input" accept=".pdf" required hidden>
                    <div id="file-input-text-1" class="file-input-text ms-2">No hay selección</div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Subir</button>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const fileInput = document.getElementById("archivo");
        const fileText = document.getElementById("file-input-text-1");
        const form = document.getElementById("fichaForm");

        fileInput.addEventListener("change", function () {
            fileText.textContent = fileInput.files.length > 0 ? "Archivo seleccionado" : "Sin selección";
        });

        // ✅ Alerta de carga personalizada
        form.addEventListener("submit", function (event) {
            event.preventDefault();

            Swal.fire({
                title: "Subiendo...",
                text: "Por favor, espera mientras se sube la ficha técnica.",
                icon: "info",
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: {
                    popup: 'border-radius-15',
                    title: 'font-weight-bold text-dark',
                    content: 'font-size-16',
                },
                didOpen: () => {
                    Swal.showLoading();

                    setTimeout(() => {
                        fetch("{{ route('fichas.store') }}", {
                            method: "POST",
                            body: new FormData(form),
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: "¡Ficha Técnica Subida!",
                                    text: data.message,
                                    icon: "success",
                                    showCancelButton: true,
                                    confirmButtonColor: "#28a745",
                                    cancelButtonColor: "#6c757d",
                                    confirmButtonText: "Ir a lista",
                                    cancelButtonText: "Permanecer aquí",
                                    customClass: {
                                        popup: "border-radius-15",
                                        title: "font-weight-bold text-dark",
                                        content: "font-size-16",
                                        confirmButton: "btn-custom-confirm",
                                        cancelButton: "btn-custom-cancel",
                                    },
                                    buttonsStyling: false
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "{{ route('fichas.index') }}";
                                    }
                                });
                            } else {
                                Swal.fire("Error", "Hubo un problema al subir la ficha.", "error");
                            }
                        })
                        .catch(() => {
                            Swal.fire("Error", "No se pudo conectar con el servidor.", "error");
                        });
                    }, 5000);
                }
            });
        });
    });
</script>
@endsection
