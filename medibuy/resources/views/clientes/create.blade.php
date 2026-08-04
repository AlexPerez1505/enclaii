@extends('layouts.app')
@section('title', 'Nuevo Cliente')
@section('titulo', 'Nuevo Cliente')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
:root{
    --bg:#fafafa; --card:#ffffff; --ink:#0a0a0a; --text:#404040;
    --muted:#999999; --line:#ececec; --line-strong:#d4d4d4;
    --success:#16a34a; --danger:#dc2626;
}
*{box-sizing:border-box;}
body{margin:0;padding:0;background:var(--bg);color:var(--text);font-family:'Quicksand',sans-serif;overflow-x:hidden;-webkit-font-smoothing:antialiased;}

.page{width:100%;max-width:720px;margin:40px auto;padding:24px;}

.head{margin-bottom:40px;padding-bottom:24px;border-bottom:1px solid var(--line);}
.crumb{font-size:12px;color:var(--muted);font-weight:600;letter-spacing:.3px;margin-bottom:10px;}
.crumb a{color:var(--muted);text-decoration:none;}
.crumb a:hover{color:var(--ink);}
.crumb .sep{margin:0 8px;}
.title{margin:0;font-size:32px;font-weight:700;color:var(--ink);letter-spacing:-.8px;line-height:1.1;}
.sub{margin:8px 0 0;color:var(--muted);font-size:14px;font-weight:500;}

.form{display:flex;flex-direction:column;gap:36px;}
.section-title{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:1.2px;margin:0 0 18px;}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
.grid-2 .full{grid-column:1/-1;}
.field{display:flex;flex-direction:column;gap:8px;position:relative;}
.label{font-size:13px;font-weight:600;color:var(--ink);display:flex;justify-content:space-between;align-items:center;}
.label .opt{font-size:11px;font-weight:500;color:var(--muted);}

.input,.textarea,.select{
    width:100%;font-family:'Quicksand',sans-serif;font-size:15px;font-weight:600;
    color:var(--ink);background:var(--card);border:1px solid var(--line);
    border-radius:8px;padding:12px 14px;outline:none;transition:.15s ease;
}
.input::placeholder,.textarea::placeholder{color:#c5c5c5;font-weight:500;}
.input:hover,.textarea:hover,.select:hover{border-color:var(--line-strong);}
.input:focus,.textarea:focus,.select:focus{border-color:var(--ink);background:#fff;}
.textarea{resize:vertical;min-height:80px;}
.select{
    appearance:none;
    background-image:linear-gradient(45deg,transparent 50%, var(--muted) 50%),linear-gradient(135deg, var(--muted) 50%, transparent 50%);
    background-position:calc(100% - 18px) 50%, calc(100% - 13px) 50%;
    background-size:5px 5px;background-repeat:no-repeat;padding-right:36px;cursor:pointer;
}
.input.error,.select.error,.textarea.error{border-color:var(--danger);background:#fef7f7;}
.input.success,.select.success{border-color:var(--success);}
.help{font-size:12px;color:var(--muted);font-weight:500;}
.err{font-size:12px;color:var(--danger);font-weight:600;display:none;}
.ok{font-size:12px;color:var(--success);font-weight:600;display:none;}
.err.show,.ok.show{display:block;}

.toggle-group{display:flex;gap:8px;}
.toggle-card{flex:1;cursor:pointer;background:var(--card);border:1px solid var(--line);border-radius:8px;padding:14px;text-align:center;font-size:14px;font-weight:600;color:var(--text);transition:.15s ease;}
.toggle-card input{display:none;}
.toggle-card:hover{border-color:var(--line-strong);}
.toggle-card.active{background:var(--ink);border-color:var(--ink);color:#fff;}

.actions{display:flex;justify-content:flex-end;gap:12px;flex-wrap:wrap;margin-top:8px;padding-top:28px;border-top:1px solid var(--line);}
.btn{font-family:'Quicksand',sans-serif;font-size:14px;font-weight:700;padding:12px 22px;border-radius:8px;border:1px solid transparent;cursor:pointer;transition:.15s ease;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:8px;}
.btn-ghost{background:transparent;color:var(--muted);}
.btn-ghost:hover{color:var(--ink);}
.btn-dark{background:var(--ink);color:#fff;}
.btn-dark:hover{background:#262626;}
.btn-dark:disabled{opacity:.5;cursor:not-allowed;}

@media (max-width:600px){
    .page{padding:20px;margin:16px auto;}
    .title{font-size:26px;}
    .grid-2{grid-template-columns:1fr;}
    .actions{flex-direction:column-reverse;}
    .actions .btn{width:100%;}
}
</style>

@php
  $asesoresFijos = [
    ['value' => 'Jesús Tellez',      'label' => 'JESÚS TELLEZ'],
    ['value' => 'Gabriela Diaz',     'label' => 'GABRIELA DÍAZ GARCIA'],
    ['value' => 'Joel Diaz',         'label' => 'ING. JOEL DÍAZ GARCIA'],
    ['value' => 'Anahí Tellez',      'label' => 'ANAHÍ TELLEZ ORTIZ'],
    ['value' => 'Jose Alex',         'label' => 'ING. JOSE ALEX ESQUIVEL PEREZ'],
    ['value' => 'Megan Diaz',        'label' => 'MEGAN DIAZ RAYON'],
    ['value' => 'Victor Guerrero',   'label' => 'VICTOR GUERRERO CORTEZ'],
  ];

  $congresos = [
    'AMCG ECOS INTERNACIONAL DE CIRUGIA GENERAL',
    'AMCG CONGRESO INTERNACIONAL DE CIRUGIA GENERAL',
    'AMCE CONGRESO INTERNACIONAL DE CIRUGIA ENDOSCOPICA',
    'AMECRA XXIX CONGRESO INTERNACIONAL DE ASOCIACIÓN MEX DE CIRUGIA RECONS, ARTICULAR Y ARTROSCOPICA',
    'CVDL CONGRESO DE VETERINARIA',
    'AMG ECOS INTERNACIONALES DE GASTROENTEROLOGIA',
    'AMG SEMANA NACIONAL GASTRO',
    'COLEGIO DE ESPECIALISTAS EN CIRUGIA GENERAL',
    'otro',
  ];
@endphp

<div class="page">
  @if($errors->any())
  <div style="background:#fef2f2;border:1px solid #dc2626;padding:12px;border-radius:8px;margin-bottom:16px;">
    <ul style="margin:0;padding-left:16px;color:#dc2626;font-size:13px;">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

  <div class="head">
    <div class="crumb">
      <a href="{{ route('clientes.index') }}">Clientes</a>
      <span class="sep">/</span>
      <span>Nuevo</span>
    </div>
    <h1 class="title">Nuevo Cliente</h1>
    <p class="sub">Completa la información para registrar un nuevo cliente.</p>
  </div>

  <form action="{{ route('clientes.store') }}" method="POST" id="clienteForm" novalidate class="form">
    @csrf
    <input type="hidden" name="redirect_to" value="{{ $redirectTo ?? route('clientes.index') }}">

    <div>
      <p class="section-title">Datos personales</p>

      <div class="grid-2">

        <div class="field">
          <label for="nombre" class="label">Nombre</label>
          <input type="text" name="nombre" id="nombre" class="input"
                 required autocomplete="off" data-upper="1"
                 value="{{ old('nombre') }}" placeholder="JUAN">
          <small class="err" id="nombre-err"></small>
        </div>

        <div class="field">
          <label for="apellido" class="label">Apellido</label>
          <input type="text" name="apellido" id="apellido" class="input"
                 required autocomplete="off" data-upper="1"
                 value="{{ old('apellido') }}" placeholder="PÉREZ">
          <small class="err" id="apellido-err"></small>
        </div>

        <div class="field">
          <label for="telefono" class="label">
            Teléfono <span class="opt">10 dígitos · Único</span>
          </label>
          <input type="tel" name="telefono" id="telefono" class="input" required
                 value="{{ old('telefono') }}"
                 inputmode="numeric" maxlength="10" minlength="10"
                 autocomplete="tel" placeholder="5512345678">
          <small class="err" id="telefono-err"></small>
          <small class="ok" id="telefono-ok">Disponible</small>
        </div>

        <div class="field">
          <label for="email" class="label">
          Correo <span class="opt">Único · Opcional</span>
        </label>
          <input type="email" name="email" id="email" class="input"
                 value="{{ old('email') }}"
                 autocomplete="email" placeholder="correo@ejemplo.com">
          <small class="err" id="email-err"></small>
          <small class="ok" id="email-ok">Disponible</small>
        </div>

      </div>
    </div>

    <div>
      <p class="section-title">Información comercial</p>

      <div class="grid-2">

        <div class="field">
         <label for="asesor" class="label">
            Asesor de ventas
          </label>

         <input type="text" name="asesor" id="asesor" class="input" value="{{ auth()->user()->name }}" readonly required >
         </div>

        <div class="field">
        <label for="categoria_id" class="label">
         Categoría
         </label>

        <select name="categoria_id" id="categoria_id" class="select" required>
          <option value="">— Selecciona una categoría —</option>

             @foreach($categorias as $categoria)
            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
          {{ mb_strtoupper($categoria->nombre,'UTF-8') }}
            </option>
            @endforeach
         </select>
         </div>

        <div class="field full">
          <label for="congreso_conocido" class="label">
          Congreso conocido
        </label>
          <select name="congreso_conocido" id="congreso_conocido" class="select" required>
            <option value="">— Selecciona un congreso —</option>
            @foreach($congresos as $cg)
              <option value="{{ $cg }}" {{ old('congreso_conocido') == $cg ? 'selected' : '' }}>{{ $cg }}</option>
            @endforeach
          </select>
        </div>

        <div class="field full">
          <label class="label">¿Recibe promoción?</label>
          <div class="toggle-group" id="promo-group">
            <label class="toggle-card" data-value="1">
              <input type="radio" name="recibe_promocion" value="1" required
                {{ old('recibe_promocion') === '1' ? 'checked' : '' }}>
              Sí
            </label>
            <label class="toggle-card" data-value="0">
              <input type="radio" name="recibe_promocion" value="0" required
                {{ old('recibe_promocion') === '0' ? 'checked' : '' }}>
              No
            </label>
          </div>
          @error('recibe_promocion')<small class="err show">{{ $message }}</small>@enderror
        </div>

      </div>
    </div>

    <div>
      <p class="section-title">Información adicional</p>

      <div class="grid-2">
        <div class="field full">
          <label for="comentarios" class="label">
           Dirección / Comentarios
          </label>
          <textarea name="comentarios" id="comentarios" class="textarea" required
          placeholder="Calle, número, colonia, ciudad...">{{ old('comentarios') }}</textarea>
        </div>
      </div>
    </div>

    <div class="actions">
      <a href="{{ $redirectTo ?? route('clientes.index') }}" class="btn btn-ghost">Cancelar</a>
      <button type="submit" class="btn btn-dark" id="btnGuardar">Guardar cliente</button>
    </div>

  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const form         = document.getElementById('clienteForm');
  const telefonoInp  = document.getElementById('telefono');
  const emailInp     = document.getElementById('email');
  const telefonoErr  = document.getElementById('telefono-err');
  const emailErr     = document.getElementById('email-err');
  const telefonoOk   = document.getElementById('telefono-ok');
  const emailOk      = document.getElementById('email-ok');
  const btnGuardar   = document.getElementById('btnGuardar');

  let telefonoValido = true;
  let emailValido = true;

  function toUpperKeepCursor(el){
    const start = el.selectionStart, end = el.selectionEnd;
    const before = el.value, upper = before.toLocaleUpperCase('es-MX');
    if (before !== upper){
      el.value = upper;
      try { el.setSelectionRange(start, end); } catch(e){}
    }
  }
  document.querySelectorAll('[data-upper="1"]').forEach(el => {
    el.addEventListener('input', () => toUpperKeepCursor(el));
    el.addEventListener('blur',  () => { el.value = (el.value || '').toLocaleUpperCase('es-MX').trim(); });
  });
  emailInp?.addEventListener('blur', () => {
    emailInp.value = (emailInp.value || '').trim().toLowerCase();
  });

  // Solo dígitos, máximo 10
  telefonoInp?.addEventListener('input', function(){
    let v = this.value.replace(/\D+/g, '');
    if (v.length > 10) v = v.slice(0, 10);
    this.value = v;
  });

  function setError(inp, errEl, okEl, msg){
    inp.classList.add('error'); inp.classList.remove('success');
    errEl.textContent = msg; errEl.classList.add('show');
    okEl?.classList.remove('show');
  }
  function setOk(inp, errEl, okEl){
    inp.classList.remove('error'); inp.classList.add('success');
    errEl.classList.remove('show'); okEl?.classList.add('show');
  }
  function clearState(inp, errEl, okEl){
    inp.classList.remove('error','success');
    errEl.classList.remove('show'); okEl?.classList.remove('show');
  }

  function validarTelefonoLargo(){
    const v = (telefonoInp?.value || '').trim();
    if (v.length === 0) {
      clearState(telefonoInp, telefonoErr, telefonoOk);
      return true;
    }
    if (v.length !== 10) {
      setError(telefonoInp, telefonoErr, telefonoOk, 'El teléfono debe tener 10 dígitos.');
      return false;
    }
    return true;
  }

  async function checkUnique(){
    const telefono = (telefonoInp.value || '').replace(/\D+/g,'');
    const email    = (emailInp.value || '').trim().toLowerCase();

    if(!telefono && !email){
      telefonoValido = true; emailValido = true;
      clearState(telefonoInp, telefonoErr, telefonoOk);
      clearState(emailInp, emailErr, emailOk);
      return;
    }

    // Validación local de longitud antes de consultar
    if (telefono && telefono.length !== 10) {
      setError(telefonoInp, telefonoErr, telefonoOk, 'El teléfono debe tener 10 dígitos.');
      telefonoValido = false;
    }

    try{
      const res = await fetch('{{ route('clientes.check-unique') }}', {
        method:'POST',
        headers:{
          'Content-Type':'application/json',
          'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({ telefono, email })
      });
      const data = await res.json();

      if(!telefono){
        clearState(telefonoInp, telefonoErr, telefonoOk); telefonoValido = true;
      } else if(data.error_telefono){
        setError(telefonoInp, telefonoErr, telefonoOk, data.error_telefono); telefonoValido = false;
      } else if (telefono.length === 10) {
        setOk(telefonoInp, telefonoErr, telefonoOk); telefonoValido = true;
      }

      if(!email){
        clearState(emailInp, emailErr, emailOk); emailValido = true;
      } else if(data.error_email){
        setError(emailInp, emailErr, emailOk, data.error_email); emailValido = false;
      } else {
        setOk(emailInp, emailErr, emailOk); emailValido = true;
      }
    } catch(e){
      console.error('Error al validar unicidad:', e);
    }
  }
  telefonoInp?.addEventListener('blur', checkUnique);
  emailInp?.addEventListener('blur', checkUnique);

  function updatePromo(){
    document.querySelectorAll('#promo-group .toggle-card').forEach(card => {
      const input = card.querySelector('input');
      card.classList.toggle('active', input.checked);
    });
  }
  document.querySelectorAll('#promo-group input').forEach(inp => {
    inp.addEventListener('change', updatePromo);
  });
  updatePromo();

  form.addEventListener('submit', async function(e){
    e.preventDefault();

    // Validar campos requeridos (excluye email)
    const campos = form.querySelectorAll('input[required], select[required], textarea[required]');
    for (const campo of campos) {
      if (!campo.value.trim()) {
        campo.classList.add('error');
        Swal.fire({
          icon:'warning',
          title:'Campos obligatorios',
          text:'Completa todos los campos antes de guardar.'
        });
        return;
      }
    }

    // Uppercase y trim
    document.querySelectorAll('[data-upper="1"]').forEach(el => {
      el.value = (el.value || '').toLocaleUpperCase('es-MX').trim();
    });
    if(emailInp) emailInp.value = (emailInp.value || '').trim().toLowerCase();

    // Validar longitud teléfono
    if (!validarTelefonoLargo()) {
      Swal.fire({ icon:'error', title:'Teléfono inválido', text:'El teléfono debe tener exactamente 10 dígitos.' });
      return;
    }

    // Validar promoción
    const promo = document.querySelector('input[name="recibe_promocion"]:checked');
    if(!promo){
      Swal.fire({ icon:'warning', title:'Falta información', text:'Selecciona si recibe promoción.' });
      return;
    }

    // Validar unicidad solo si hay teléfono o email
    const tel = (telefonoInp.value || '').replace(/\D+/g,'');
    const mail = (emailInp.value || '').trim().toLowerCase();

    if(tel || mail){
      await checkUnique();
      if(!telefonoValido || !emailValido){
        Swal.fire({
          icon:'error', title:'Datos duplicados',
          html:(!telefonoValido ? '<div>• Teléfono inválido o ya registrado</div>' : '') +
               (!emailValido    ? '<div>• Correo ya registrado</div>' : '')
        });
        return;
      }
    }

    // Todo OK — enviar
    btnGuardar.disabled = true;
    btnGuardar.textContent = 'Guardando...';
    form.submit();
  });
});
</script>
@endsection