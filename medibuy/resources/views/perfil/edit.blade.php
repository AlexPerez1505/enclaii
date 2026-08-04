@extends('layouts.app')

@section('title','Editar Perfil')
@section('titulo','Editar Perfil')

@section('content')

<style>

.edit-profile-wrapper{
    max-width: 950px;
    margin: auto;
}

/* HERO */
.profile-hero{
    background: linear-gradient(135deg, #60a5fa 0%, #93c5fd 50%);;
    color:white;
    padding:30px;
    border-radius:20px;
    margin-bottom:25px;
    box-shadow:0 15px 35px rgba(37,99,235,.20);
}

.profile-hero h2{
    margin:0;
    font-weight:700;
}

.profile-hero p{
    margin:6px 0 0;
    opacity:.85;
}

/* CARD */
.profile-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 35px rgba(0,0,0,.08);
}

.profile-card .card-body{
    padding:30px;
}

/* SECCIONES */
.section-title{
    font-size:1rem;
    font-weight:700;
    color:#1e293b;
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:10px;
}

.section-divider{
    height:1px;
    background:#e2e8f0;
    margin:30px 0;
}

/* LABELS */
.form-label{
    font-size:.9rem;
    font-weight:600;
    color:#475569;
    margin-bottom:8px;
}

/* INPUTS */
.form-control{
    border-radius:12px;
    border:1px solid #dbe2ea;
    padding:12px 15px;
    transition:.25s;
}

.form-control:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 4px rgba(37,99,235,.15);
}

/* BOTONES */
.btn-save{
    background:#2563eb;
    border:none;
    border-radius:12px;
    padding:12px 22px;
    font-weight:600;
}

.btn-save:hover{
    background:#1d4ed8;
}

.btn-cancel{
    border-radius:12px;
    padding:12px 22px;
    font-weight:600;
}

/* CONTACTOS */
.contact-box{
    background:#f8fafc;
    border:1px solid #e2e8f0;
    border-radius:16px;
    padding:20px;
    margin-top:10px;
}


.textarea-domicilio{
    height: 50px;
    resize: none;
    overflow-y: auto;
    line-height: 1.6;
    scrollbar-width: thin;
}

.btn-save,
.btn-cancel{
    min-width: 180px;
    height: 45px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:600;
    border-radius:12px;
}

</style>

<div class="edit-profile-wrapper">

    <div class="profile-hero">
        <h2>Editar Perfil</h2>
        <p>Actualiza tu información personal y contactos de emergencia.</p>
    </div>

    <div class="card profile-card">
        <div class="card-body">

            <form action="{{ route('perfil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="section-title">
                    👤 Información Personal
                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name',$user->name) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ old('email',$user->email) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Teléfono
                        </label>
                        <input type="text"
                               name="phone"
                               class="form-control"
                               value="{{ old('phone',$user->phone) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">CURP</label>
                        <input type="text"
                               name="curp"
                               class="form-control"
                               value="{{ old('curp',$user->curp) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Cargo</label>
                        <input type="text"
                               name="cargo"
                               class="form-control"
                               value="{{ old('cargo',$user->cargo) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Puesto</label>
                        <input type="text"
                               name="puesto"
                               class="form-control"
                               value="{{ old('puesto',$user->puesto) }}">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Domicilio</label>
                        <textarea name="domicilio"
          class="form-control textarea-domicilio">{{ old('domicilio',$user->domicilio) }}</textarea>
                    </div>

                    <hr class="my-3">

                

<div class="row">

    <!-- CONTACTO PRINCIPAL -->
    <div class="col-md-6 mb-3">
        <div class="contact-box h-100">

            <div class="section-title">
                Contacto de Emergencia Principal
            </div>

            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text"
                       name="nombre_contacto_emergencia"
                       class="form-control"
                       value="{{ old('nombre_contacto_emergencia',$user->nombre_contacto_emergencia) }}">
            </div>

            <div>
                <label class="form-label">Teléfono</label>
                <input type="text"
                       name="numero_contacto_emergencia"
                       class="form-control"
                       value="{{ old('numero_contacto_emergencia',$user->numero_contacto_emergencia) }}">
            </div>

        </div>
    </div>

    <!-- CONTACTO SECUNDARIO -->
    <div class="col-md-6 mb-3">
        <div class="contact-box h-100">

            <div class="section-title">
                Contacto de Emergencia Secundario
            </div>

            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text"
                       name="nombre_contacto_emergencia_secundario"
                       class="form-control"
                       value="{{ old('nombre_contacto_emergencia_secundario',$user->nombre_contacto_emergencia_secundario) }}">
            </div>

            <div>
                <label class="form-label">Teléfono</label>
                <input type="text"
                       name="numero_contacto_emergencia_secundario"
                       class="form-control"
                       value="{{ old('numero_contacto_emergencia_secundario',$user->numero_contacto_emergencia_secundario) }}">
            </div>

        </div>
    </div>

</div>

                <div class="d-flex justify-content-center align-items-center gap-3 mt-4">
    <button type="submit" class="btn btn-save text-white">
        Guardar cambios
    </button>

    <a href="{{ route('perfil') }}" class="btn btn-secondary btn-cancel">
        Cancelar
    </a>
</div>

            </form>

        </div>
    </div>

</div>

@endsection