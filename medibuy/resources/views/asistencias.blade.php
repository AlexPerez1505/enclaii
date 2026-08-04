@extends('layouts.app')
@section('title', 'Asistencias')
@section('titulo', 'Asistencia')

@section('content')
<link rel="stylesheet" href="{{ asset('css/asistencias.css') }}?v={{ time() }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
:root {
  --mint: #48cfad;
  --mint-dark: #34c29e;
  --ink: #1e293b;
  --muted: #64748b;
  --line: #e2e8f0;
  --card: #ffffff;
  --bg: #f8fafc;
  --success-light: #f0fdf4;
  --success: #16a34a;
}

body {
  font-family: "Open Sans", sans-serif;
  background: var(--bg);
  color: #334155;
}

.asistencias-page {
  max-width: 1200px;
  margin: 40px auto;
  padding: 0 24px;
}

/* ===== NUEVA DISTRIBUCIÓN DE CABECERA FLEXIBLE ===== */
.page-head {
  margin-bottom: 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap; /* Permite colapsar en móviles */
}
.page-head-text {
  flex-grow: 1;
}
.page-head h2 {
  margin: 0;
  font-weight: 700;
  color: var(--ink);
  font-size: 26px;
  letter-spacing: -0.02em;
}
.page-head p {
  margin: 6px 0 0;
  color: var(--muted);
  font-size: 14px;
}

/* ===== BOTONES DE NAVEGACIÓN Y UTILIDAD ===== */
.head-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn-utility {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: var(--card);
  border: 1px solid var(--line);
  color: var(--ink);
  text-decoration: none;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
}

.btn-utility:hover {
  background: #f1f5f9;
  color: var(--mint-dark);
  border-color: #cbd5e1;
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
}

.btn-utility i {
  font-size: 16px;
}

.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 32px;
}

.card-control {
  background: var(--card);
  border-radius: 20px;
  border: 1px solid var(--line);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 10px 20px -8px rgba(30, 41, 59, 0.05);
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
  display: flex;
  flex-direction: column;
}
.card-control:hover {
  transform: translateY(-4px);
  box-shadow: 0 15px 30px -10px rgba(30, 41, 59, 0.12);
  border-color: #cbd5e1;
}

.card-head {
  padding: 28px 32px;
  border-bottom: 1px solid var(--line);
  display: flex;
  align-items: center;
  gap: 16px;
}
.card-icon-box {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}
.card-icon-excel { background: #ecfdf5; color: #107c41; }
.card-icon-manual { background: #f0fdfa; color: var(--mint-dark); }

.card-title-group h3 { margin: 0; font-size: 16px; font-weight: 700; color: var(--ink); }
.card-title-group p { margin: 4px 0 0; font-size: 12.5px; color: var(--muted); line-height: 1.4; }

.card-body { padding: 32px; flex-grow: 1; }
.form-group { margin-bottom: 22px; }
.label_custom { display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px; }

.input-icon-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}
.input-icon-wrapper i {
  position: absolute;
  left: 16px;
  color: var(--muted);
  font-size: 15px;
  pointer-events: none;
  transition: color 0.2s;
}

.form-control-custom {
  width: 100%;
  padding: 12px 16px 12px 46px;
  font-size: 14px;
  font-family: inherit;
  color: var(--ink);
  background-color: #ffffff;
  border: 1px solid var(--line);
  border-radius: 12px;
  transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  outline: none;
  box-sizing: border-box;
}
.form-control-custom:focus {
  border-color: var(--mint);
  box-shadow: 0 0 0 4px rgba(72, 207, 173, 0.15);
}

.form-control-custom.file-loaded {
  background-color: var(--success-light);
  border-color: var(--success);
  color: #052e16;
}
.form-control-custom.file-loaded + i {
  color: var(--success) !important;
}

input[type="file"].form-control-custom { padding: 9px 12px 9px 46px; cursor: pointer; }
input[type="file"].form-control-custom::file-selector-button {
  background: #f1f5f9; border: 1px solid var(--line); padding: 4px 10px; border-radius: 6px;
  font-weight: 600; font-size: 12px; color: #475569; margin-right: 10px; cursor: pointer;
}

.help-text { font-size: 12px; color: var(--muted); display: block; margin-top: 6px; line-height: 1.4; }
.card-footer { padding: 0 32px 32px 32px; }

.btn-submit {
  width: 100%; border: 0; border-radius: 12px; padding: 14px 24px; font-weight: 700; font-size: 14px;
  cursor: pointer; transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
  display: inline-flex; align-items: center; justify-content: center; gap: 8px;
  background: var(--mint); color: #fff; box-shadow: 0 4px 14px rgba(72, 207, 173, .3);
}
.btn-submit:hover { background: var(--mint-dark); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(72, 207, 173, .4); }
.btn-submit:disabled { background: #cbd5e1; color: #94a3b8; cursor: not-allowed; box-shadow: none; transform: none; }

.fade-in-group { animation: fadeIn 0.3s ease-in-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }

@media (max-width: 640px) { 
  .cards-grid { grid-template-columns: 1fr; } 
  .page-head { flex-direction: column; align-items: flex-start; }
  .head-actions { width: 100%; justify-content: flex-start; }
}
</style>

{{-- ALERTAS DETALLADAS DE RESPUESTA --}}
@if (session('error_asistencia'))
    <script>
      Swal.fire({ 
        icon: 'error', 
        title: 'Error de Importación', 
        text: @json(session('error_asistencia')), 
        confirmButtonColor: '#ef4444',
        footer: '<span style="color:#64748b">Revisa la estructura de las columnas de tu Excel.</span>'
      });
    </script>
@endif
@if (session('error_permiso'))
    <script>Swal.fire({ icon:'error', title:'Permiso insuficiente', text:@json(session('error_permiso')), confirmButtonColor: '#ef4444' });</script>
@endif
@if (session('error_vacaciones'))
    <script>Swal.fire({ icon:'error', title:'Vacaciones agotadas', text:@json(session('error_vacaciones')), confirmButtonColor: '#ef4444' });</script>
@endif
@if (session('success'))
    <script>
      Swal.fire({ 
        icon: 'success', 
        title: '¡Proceso Terminado!', 
        text: @json(session('success')), 
        confirmButtonColor: '#48cfad' 
      });
    </script>
@endif

<div class="asistencias-page">
  
  <div class="page-head">
    <div class="page-head-text">
      <h2>Módulo de Asistencias</h2>
      <p>Selecciona el método de captura para administrar las jornadas de los colaboradores.</p>
    </div>
    
    {{-- ACCIONES DE NAVEGACIÓN SUPERIOR --}}
    <div class="head-actions">
      {{-- BOTÓN IR AL INICIO --}}
      <a href="{{ url('/home') }}" class="btn-utility" title="Ir al inicio">
        <i class="fa-solid fa-house"></i>
      </a>
      
      {{-- BOTÓN VOLVER ATRÁS --}}
      <a href="javascript:void(0);" onclick="goBackSmart();" class="btn-utility" title="Volver atrás">
        <i class="fa-solid fa-arrow-left"></i>
      </a>
    </div>
  </div>

  <div class="cards-grid">
    
    {{-- CARD 1: EXCEL CON SEGUIMIENTO DE CARGA --}}
    <div class="card-control">
      <div class="card-head">
        <div class="card-icon-box card-icon-excel">
          <i class="fa-solid fa-file-excel"></i>
        </div>
        <div class="card-title-group">
          <h3>Importación Masiva</h3>
          <p>Procesa los reportes extraídos del biométrico.</p>
        </div>
      </div>
      
      <form id="excelForm" action="{{ \Illuminate\Support\Facades\Route::has('asistencias.importarExcel') ? route('asistencias.importarExcel') : url('/asistencias/importar-excel') }}"
            method="POST"
            enctype="multipart/form-data"
            style="display: flex; flex-direction: column; flex-grow: 1;">
        @csrf
        
        <div class="card-body">
          <div class="form-group">
            <label class="label_custom">Excel Entrada / Salida (Opcional):</label>
            <div class="input-icon-wrapper">
              <input type="file" name="archivo_entrada_salida" class="form-control-custom js-file-input" accept=".xlsx,.xls,.csv">
              <i class="fa-solid fa-right-to-bracket"></i>
            </div>
            <span class="help-text">Toma la primera marca como entrada y la última como salida.</span>
          </div>

          <div class="form-group">
            <label class="label_custom">Excel Comida (Opcional):</label>
            <div class="input-icon-wrapper">
              <input type="file" name="archivo_comida" class="form-control-custom js-file-input" accept=".xlsx,.xls,.csv">
              <i class="fa-solid fa-utensils"></i>
            </div>
            <span class="help-text">Detecta los registros dentro del bloque de almuerzo.</span>
          </div>
        </div>

        <div class="card-footer">
          <button type="submit" id="btnSubmitExcel" class="btn-submit">
            <i class="fa-solid fa-cloud-arrow-up"></i> <span id="btnTextExcel">Procesar Archivos</span>
          </button>
        </div>
      </form>
    </div>

    {{-- CARD 2: REGISTRO MANUAL --}}
    <div class="card-control">
      <div class="card-head">
        <div class="card-icon-box card-icon-manual">
          <i class="fa-solid fa-user-pen"></i>
        </div>
        <div class="card-title-group">
          <h3>Registro Individual</h3>
          <p>Inserta asistencias o justifica incidencias manualmente.</p>
        </div>
      </div>

      <form action="{{ route('asistencias.store') }}" method="POST" style="display: flex; flex-direction: column; flex-grow: 1;">
        @csrf
        
        <div class="card-body">
          <div class="form-group">
            <label for="user_id" class="label_custom">Empleado:</label>
            <div class="input-icon-wrapper">
              <select class="form-control-custom" name="user_id" required>
                <option value="">Selecciona un colaborador...</option>
                @foreach ($usuarios as $user)
                  <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
              </select>
              <i class="fa-solid fa-user"></i>
            </div>
          </div>

          <div class="form-group">
            <label for="estado" class="label_custom">Estado / Incidencia:</label>
            <div class="input-icon-wrapper">
              <select class="form-control-custom" name="estado" required>
                <option value="">Selecciona estado...</option>
                <option value="asistencia">Asistencia (Entrada)</option>
                <option value="falta">Falta</option>
                <option value="permiso">Permiso</option>
                <option value="vacaciones">Vacaciones</option>
                <option value="retardo">Retardo</option>
                <option value="salida">Salida</option>
              </select>
              <i class="fa-solid fa-business-time"></i>
            </div>
          </div>

          <div class="form-group">
            <label for="fecha" class="label_custom">Fecha del Registro:</label>
            <div class="input-icon-wrapper">
              <input type="date" class="form-control-custom" name="fecha" required value="{{ \Carbon\Carbon::today()->toDateString() }}">
              <i class="fa-solid fa-calendar-day"></i>
            </div>
          </div>

          <div class="form-group" id="hora-group">
            <label for="hora" class="label_custom">Hora del Registro:</label>
            <div class="input-icon-wrapper">
              <input type="time" class="form-control-custom" name="hora" id="hora" required>
              <i class="fa-solid fa-clock"></i>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <button type="submit" class="btn-submit">
            <i class="fa-solid fa-floppy-disk"></i> Guardar Registro
          </button>
        </div>
      </form>
    </div>

  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    
    // CONTROL DE SEGUIMIENTO PARA EL EXCEL
    const excelForm = document.getElementById('excelForm');
    const fileInputs = document.querySelectorAll('.js-file-input');
    const btnSubmitExcel = document.getElementById('btnSubmitExcel');
    const btnTextExcel = document.getElementById('btnTextExcel');

    fileInputs.forEach(input => {
      input.addEventListener('change', function() {
        const icon = this.parentNode.querySelector('i');
        if (this.files.length > 0) {
          this.classList.add('file-loaded');
          if (icon) {
            icon.className = 'fa-solid fa-circle-check';
          }
        } else {
          this.classList.remove('file-loaded');
        }
      });
    });

    if (excelForm) {
      excelForm.addEventListener('submit', function(e) {
        let tieneArchivo = false;
        fileInputs.forEach(input => {
          if (input.files.length > 0) tieneArchivo = true;
        });

        if (!tieneArchivo) {
          e.preventDefault();
          Swal.fire({
            icon: 'warning',
            title: 'Faltan archivos',
            text: 'Por favor, selecciona al menos un archivo Excel antes de procesar.',
            confirmButtonColor: '#48cfad'
          });
          return;
        }

        btnSubmitExcel.disabled = true;
        btnTextExcel.innerText = "Subiendo y analizando Excel...";
        
        const btnIcon = btnSubmitExcel.querySelector('i');
        if (btnIcon) {
          btnIcon.className = 'fa-solid fa-spinner fa-spin';
        }

        Swal.fire({
          title: 'Subiendo documento...',
          text: 'Estamos leyendo las filas del biométrico y calculando las asistencias. Por favor, no cierres esta ventana.',
          allowOutsideClick: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
      });
    }

    // LOGICA OPERATIVA DE HORA Y ASISTENCIA
    const now = new Date();
    const h = now.getHours().toString().padStart(2, '0');
    const m = now.getMinutes().toString().padStart(2, '0');
    const el = document.getElementById("hora");
    if (el) el.value = `${h}:${m}`;

    const estadoSelect = document.querySelector('select[name="estado"]');
    const horaInput = document.getElementById("hora");
    const horaGroup = document.getElementById("hora-group");

    function toggleHora() {
      if (!estadoSelect || !horaInput || !horaGroup) return;
      const estado = (estadoSelect.value || '').toLowerCase();

      if (['falta', 'permiso', 'vacaciones'].includes(estado)) {
        horaGroup.style.display = 'none';
        horaGroup.classList.remove('fade-in-group');
        horaInput.required = false;
        horaInput.value = '';
      } else {
        horaGroup.style.display = 'block';
        horaGroup.classList.add('fade-in-group');
        horaInput.required = true;
      }
    }

    if (estadoSelect) {
      estadoSelect.addEventListener('change', toggleHora);
      toggleHora();
    }

    const userSelect = document.querySelector('select[name="user_id"]');
    const fechaInput = document.querySelector('input[name="fecha"]');

    async function verificarAsistencia() {
      const userId = userSelect?.value;
      const fecha = fechaInput?.value;
      if (!userId || !fecha) return;

      try {
        const response = await fetch(`{{ url('/asistencia/verificar') }}?user_id=${encodeURIComponent(userId)}&fecha=${encodeURIComponent(fecha)}`);
        const data = await response.json();

        if (data.tieneEntrada && estadoSelect) {
          estadoSelect.value = 'salida';
          toggleHora();

          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Colaborador ya cuenta con entrada',
            text: 'Se seleccionó "Salida" automáticamente.',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
          });
        }
      } catch (error) {
        console.error('Error al consultar asistencia concurrente:', error);
      }
    }

    userSelect?.addEventListener('change', verificarAsistencia);
    fechaInput?.addEventListener('change', verificarAsistencia);
  });
  function goBackSmart() {
  // Obtiene el historial guardado en sessionStorage
  let nav = JSON.parse(sessionStorage.getItem('navHistory') || '[]');
  const current = window.location.href;

  // Elimina la página actual del historial si está al final
  while (nav.length > 0 && nav[nav.length - 1] === current) {
    nav.pop();
  }

  // Si queda algo en el historial, navega ahí
  if (nav.length > 0) {
    const destino = nav.pop();
    sessionStorage.setItem('navHistory', JSON.stringify(nav));
    window.location.href = destino;
  } else {
    // Fallback: si no hay historial, va al home
    window.location.href = "{{ url('/home') }}";
  }
}

// Registra cada visita en el historial propio (evita duplicados consecutivos)
(function() {
  let nav = JSON.parse(sessionStorage.getItem('navHistory') || '[]');
  const current = window.location.href;
  if (nav[nav.length - 1] !== current) {
    nav.push(current);
    // Limita a 20 entradas para no saturar
    if (nav.length > 20) nav.shift();
    sessionStorage.setItem('navHistory', JSON.stringify(nav));
  }
})();
</script>
@endsection