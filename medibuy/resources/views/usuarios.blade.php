@extends('layouts.app')
@section('title', 'Usuarios')
@section('titulo', 'Usuarios')

@section('content')
<link rel="stylesheet" href="{{ asset('css/usuarios.css') }}?v={{ time() }}">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,700" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
  --danger: #f43f5e;
  --danger-light: #fff1f2;
}

body {
  font-family: "Open Sans", sans-serif;
  background: var(--bg);
  color: #334155;
}

/* Page Wrapper */
.users-page {
  max-width: 1200px;
  margin: 40px auto;
  padding: 0 24px;
}

/* Container unificado con sombreado */
.panel-wrapper {
  background: var(--card);
  border-radius: 24px;
  border: 1px solid var(--line);
  box-shadow: 0 10px 30px -10px rgba(30, 41, 59, 0.05);
  overflow: hidden;
}

/* Header Panel Superior */
.panel-head {
  padding: 32px;
  border-bottom: 1px solid var(--line);
  display: flex;
  align-items: center;
  gap: 14px;
  justify-content: space-between;
  background: #ffffff;
}
.hgroup h2 {
  margin: 0;
  font-weight: 700;
  color: var(--ink);
  font-size: 24px;
  letter-spacing: -0.02em;
}
.hgroup p {
  margin: 6px 0 0;
  color: var(--muted);
  font-size: 14px;
}

/* Contenedor de acciones del encabezado */
.actions-top {
  display: flex;
  align-items: center;
  gap: 12px;
}

/* Estilos personalizados para tus botones de navegación con Bootstrap Icons */
.ft-btn-nav {
  border: 1px solid var(--line);
  border-radius: 12px;
  width: 44px;
  height: 44px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--bg);
  color: #475569;
  font-size: 18px;
  text-decoration: none;
  transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
}
.ft-btn-nav:hover {
  background: #e2e8f0;
  color: var(--ink);
  border-color: #cbd5e1;
  transform: translateY(-2px);
}
.ft-btn-nav:active {
  transform: translateY(0);
}

/* Botones Estándar */
.btn {
  border: 0;
  border-radius: 12px;
  padding: 12px 22px;
  font-weight: 700;
  font-size: 14px;
  cursor: pointer;
  transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  height: 44px; 
}
.btn:hover {
  transform: translateY(-2px);
}
.btn:active { transform: translateY(0); }
.btn-primary {
  background: var(--mint);
  color: #fff;
  box-shadow: 0 4px 14px rgba(72, 207, 173, .3);
}
.btn-primary:hover {
  background: var(--mint-dark);
  box-shadow: 0 6px 20px rgba(72, 207, 173, .4);
}

/* Grid Principal de Tarjetas */
.users-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 24px;
  padding: 32px;
  background: #ffffff;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.users-container.single-view {
  grid-template-columns: 1fr;
}

/* Tarjetas */
.profile-container {
  background: var(--card);
  border-radius: 16px;
  border: 1px solid var(--line);
  padding: 24px;
  text-align: center;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}
.profile-container:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 24px -8px rgba(30, 41, 59, 0.12);
  border-color: #cbd5e1;
}

/* Avatar de Perfil */
.profile-picture {
  width: 84px;
  height: 84px;
  border-radius: 50%;
  margin: 0 auto 16px auto;
  overflow: hidden;
  border: 3px solid #fff;
  box-shadow: 0 0 0 3px #f1f5f9;
  transition: all 0.3s ease;
}
.profile-container:hover .profile-picture {
  box-shadow: 0 0 0 3px var(--mint);
}
.profile-picture img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.user-name {
  font-size: 18px;
  color: var(--ink);
  font-weight: 700;
  margin-bottom: 6px;
}

.user-puesto-badge {
  display: inline-block;
  background: #f1f5f9;
  color: var(--muted);
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 11px;
  font-weight: 700;
  margin-bottom: 16px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

/* Información Básica */
.basic-info-wrapper {
  border-top: 1px solid #f1f5f9;
  padding-top: 14px;
  text-align: left;
}
.basic-info {
  font-size: 13.5px;
  color: #475569;
  margin-bottom: 8px;
  display: flex;
  justify-content: space-between;
}
.basic-info strong {
  color: var(--muted);
  font-weight: 600;
}

/* Información Extra Oculta */
.extra-info {
  display: none;
  text-align: left;
  margin-top: 24px;
  animation: fadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(14px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Separadores visuales */
.info-section-title {
  font-size: 11px;
  text-transform: uppercase;
  color: var(--muted);
  font-weight: 700;
  letter-spacing: 0.08em;
  margin: 28px 0 14px 0;
  padding-bottom: 6px;
  border-bottom: 2px solid #f1f5f9;
  display: flex;
  align-items: center;
  gap: 6px;
}
.info-section-title::before {
  content: '';
  display: inline-block;
  width: 6px;
  height: 6px;
  background: var(--mint);
  border-radius: 50%;
}

/* Grid de métricas */
.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 12px;
  margin-bottom: 16px;
}
.metric-card {
  background: #f8fafc;
  border: 1px solid var(--line);
  padding: 12px;
  border-radius: 12px;
  text-align: center;
  transition: background 0.2s;
}
.metric-card:hover {
  background: #f1f5f9;
}
.metric-card .num {
  display: block;
  font-size: 20px;
  font-weight: 700;
  color: var(--ink);
}
.metric-card .label {
  font-size: 11px;
  color: var(--muted);
  font-weight: 600;
  margin-top: 2px;
}

/* Filas de Información */
.info-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 12px;
  border-radius: 8px;
  font-size: 14px;
  transition: background 0.15s;
}
.info-row:nth-child(even) {
  background: #f8fafc;
}
.info-row:hover {
  background: #f1f5f9;
}
.info-row strong {
  color: #475569;
  font-weight: 600;
}
.info-row span {
  color: var(--ink);
  font-weight: 500;
}

/* Acciones */
.user-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 32px;
  padding-top: 20px;
  border-top: 1px solid var(--line);
}
.btn-delete-user {
  border: none;
  background: var(--danger-light);
  color: var(--danger);
  padding: 11px 24px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.btn-delete-user:hover {
  background: var(--danger);
  color: #fff;
  box-shadow: 0 4px 14px rgba(244, 63, 94, 0.25);
}

/* Estado Expandido */
.profile-container.expanded {
  cursor: default;
  text-align: left;
  padding: 36px;
  background: #fff;
  box-shadow: 0 20px 40px -15px rgba(30, 41, 59, 0.1);
  border-color: var(--mint);
}
.profile-container.expanded:hover {
  transform: none;
}
.profile-container.expanded .profile-header-expanded {
  display: flex;
  align-items: center;
  gap: 24px;
  border-bottom: 1px solid var(--line);
  padding-bottom: 24px;
}
.profile-container.expanded .profile-picture {
  margin: 0;
  width: 96px;
  height: 96px;
  box-shadow: 0 0 0 3px var(--mint);
}
.profile-container.expanded .extra-info {
  display: block;
}

/* Responsive */
@media (max-width: 640px) {
  .profile-container.expanded .profile-header-expanded {
    flex-direction: column;
    text-align: center;
  }
  .profile-container.expanded .profile-picture {
    margin: 0 auto;
  }
}
</style>

<div class="users-page">
  
  <div class="panel-wrapper">
    
    <div class="panel-head">
      <div class="hgroup">
        <h2>Control de Usuarios</h2>
        <p>Gestiona los colaboradores del sistema y sus accesos.</p>
      </div>
      
      <div class="actions-top">
        <a href="javascript:history.back()" class="ft-btn-nav" title="Volver atrás">
          <i class="bi bi-arrow-left"></i>
        </a>

        <a href="{{ url('/home') }}" class="ft-btn-nav" title="Volver al inicio">
          <i class="bi bi-house-door-fill"></i>
        </a>

        <a href="{{ route('users.create') }}" class="btn btn-primary">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          Agregar usuario
        </a>
      </div>
    </div>

    <div class="users-container">
      @foreach ($usuarios as $user)
        <div class="profile-container" onclick="toggleUserInfo(this)">
          
          <div class="profile-header-expanded">
            <div class="profile-picture">
              <img
                src="{{ $user->imagen ? asset('storage/' . $user->imagen) : asset('images/default-profile.png') }}"
                alt="Foto de perfil"
              >
            </div>
            <div>
              <div class="user-name">{{ $user->name }}</div>
              <div class="user-puesto-badge">{{ $user->puesto ?? 'Sin puesto' }}</div>
            </div>
          </div>

          <div class="basic-info-wrapper">
            <div class="basic-info"><strong>Nómina:</strong> <span>{{ $user->nomina ?? '—' }}</span></div>
            <div class="basic-info"><strong>Teléfono:</strong> <span>{{ $user->phone ?? '—' }}</span></div>
          </div>

          <div class="extra-info">
            
            <div class="info-section-title">Control de Asistencia y Vacaciones</div>
            <div class="metrics-grid">
              <div class="metric-card"><span class="num">{{ $user->asistencias ?? '0' }}</span><span class="label">Asistencias</span></div>
              <div class="metric-card"><span class="num" style="color: var(--danger);">{{ $user->faltas ?? '0' }}</span><span class="label">Faltas</span></div>
              <div class="metric-card"><span class="num" style="color: #d97706;">{{ $user->retardos ?? '0' }}</span><span class="label">Retardos</span></div>
              <div class="metric-card"><span class="num" style="color: #2563eb;">{{ $user->vacaciones_disponibles ?? '0' }} d</span><span class="label">Vac. Disponibles</span></div>
              <div class="metric-card"><span class="num">{{ $user->vacaciones_utilizadas ?? '0' }} d</span><span class="label">Vac. Usadas</span></div>
              <div class="metric-card"><span class="num">{{ $user->permisos ?? '0' }} d</span><span class="label">Permisos Disp.</span></div>
            </div>

            <div class="info-section-title">Datos Generales</div>
            <div class="info-row"><strong>Número de Usuario (Nómina):</strong> <span>{{ $user->nomina ?? 'No registrado' }}</span></div>
            <div class="info-row"><strong>Correo Electrónico:</strong> <span>{{ $user->email }}</span></div>
            <div class="info-row"><strong>Teléfono celular:</strong> <span>{{ $user->phone ?? 'No registrado' }}</span></div>
            <div class="info-row"><strong>Cargo / Función:</strong> <span>{{ $user->cargo ?? 'No registrado' }}</span></div>
            <div class="info-row"><strong>Fecha de Ingreso:</strong> <span>{{ $user->fecha_ingreso ?? 'No registrado' }}</span></div>
            <div class="info-row"><strong>Domicilio:</strong> <span>{{ $user->domicilio ?? 'No registrado' }}</span></div>

            <div class="info-section-title">Documentación e Identificaciones</div>
            <div class="info-row"><strong>CURP:</strong> <span>{{ $user->curp ?? 'No registrado' }}</span></div>
            <div class="info-row"><strong>INE:</strong> <span>{{ $user->ine ?? 'No registrado' }}</span></div>
            <div class="info-row"><strong>Licencia de Conducir:</strong> <span>{{ $user->licencia ?? 'No registrado' }}</span></div>
            <div class="info-row"><strong>Acta de Nacimiento:</strong> <span>{{ $user->acta_de_nacimiento ?? 'No registrado' }}</span></div>

            <div class="info-section-title">Contacto de Emergencia Principal</div>
            <div class="info-row"><strong>Nombre:</strong> <span>{{ $user->nombre_contacto_emergencia ?? 'No registrado' }}</span></div>
            <div class="info-row"><strong>Teléfono:</strong> <span>{{ $user->numero_contacto_emergencia ?? 'No registrado' }}</span></div>
            <div class="info-row"><strong>Domicilio:</strong> <span>{{ $user->domicilio_contacto_emergencia ?? 'No registrado' }}</span></div>

            @if($user->nombre_contacto_emergencia_secundario)
              <div class="info-section-title">Contacto de Emergencia Secundario</div>
              <div class="info-row"><strong>Nombre:</strong> <span>{{ $user->nombre_contacto_emergencia_secundario }}</span></div>
              <div class="info-row"><strong>Teléfono:</strong> <span>{{ $user->numero_contacto_emergencia_secundario ?? 'No registrado' }}</span></div>
              <div class="info-row"><strong>Domicilio:</strong> <span>{{ $user->domicilio_contacto_emergencia_secundario ?? 'No registrado' }}</span></div>
            @endif

            <div class="user-actions" onclick="event.stopPropagation()">
              <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="delete-form" onsubmit="confirmDelete(event, this, '{{ $user->name }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-delete-user">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  Eliminar usuario
                </button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>

  </div>
</div>

<script>
  // Escucha de mensajes de confirmación exitosa o error de Laravel al refrescar
  document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
      Swal.fire({
        title: '¡Acción Completada!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#2563eb',
        customClass: {
          popup: 'nip-popup-custom'
        }
      });
    @endif

    @if(session('error'))
      Swal.fire({
        title: 'Hubo un problema',
        text: "{{ session('error') }}",
        icon: 'error',
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#ef4444',
        customClass: {
          popup: 'nip-popup-custom'
        }
      });
    @endif
  });

  function toggleUserInfo(element) {
    const usersContainer = document.querySelector('.users-container');
    const basicInfoBlocks = element.querySelectorAll('.basic-info-wrapper');

    if (element.classList.contains('expanded')) {
      element.classList.remove('expanded');
      usersContainer.classList.remove('single-view');
      basicInfoBlocks.forEach(info => info.style.display = 'block');
      return;
    }

    document.querySelectorAll('.profile-container').forEach(container => {
      container.classList.remove('expanded');
      container.querySelectorAll('.basic-info-wrapper').forEach(info => info.style.display = 'block');
    });

    element.classList.add('expanded');
    usersContainer.classList.add('single-view');
    basicInfoBlocks.forEach(info => info.style.display = 'none');
  }

  function confirmDelete(e, form, userName) {
    e.preventDefault();
    
    Swal.fire({
      showConfirmButton: false, 
      showCloseButton: true,
      showCancelButton: false,
      allowOutsideClick: false,
      customClass: {
        popup: 'nip-popup-custom'
      },
      html: `
        <style>
          .nip-popup-custom {
            border-radius: 24px !important;
            padding: 32px !important;
            max-width: 480px !important;
            font-family: "Open Sans", sans-serif !important;
          }
          .nip-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
            text-align: left;
          }
          .nip-icon-shield {
            background: #eff6ff;
            color: #2563eb;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
          }
          .nip-title-group h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
          }
          .nip-title-group p {
            margin: 4px 0 0;
            font-size: 14px;
            color: #64748b;
          }
          .nip-alert-info {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13.5px;
            color: #166534;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            text-align: left;
          }
          .nip-alert-info input[type="radio"] {
            pointer-events: none;
            accent-color: #22c55e;
            transform: scale(1.1);
          }
          .nip-inputs-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 16px;
          }
          .nip-field {
            width: 50px;
            height: 60px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            transition: all 0.2s;
          }
          .nip-field:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
            outline: none;
          }
          .nip-error-msg {
            display: none;
            color: #ef4444;
            font-size: 13px;
            font-weight: 600;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 12px;
          }
        </style>

        <div class="nip-header">
          <div class="nip-icon-shield">
            <i class="bi bi-shield-check"></i>
          </div>
          <div class="nip-title-group">
            <h3>Confirmación segura</h3>
            <p>Escribe el PIN de 6 dígitos para eliminar a ${userName}</p>
          </div>
        </div>

        <div class="nip-alert-info">
          <input type="radio" checked readonly>
          <span>Al completar los <strong>6 dígitos</strong>, se validará el acceso de forma segura.</span>
        </div>

        <div class="nip-inputs-container">
          <input type="text" class="nip-field" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="one-time-code">
          <input type="text" class="nip-field" maxlength="1" pattern="[0-9]*" inputmode="numeric">
          <input type="text" class="nip-field" maxlength="1" pattern="[0-9]*" inputmode="numeric">
          <input type="text" class="nip-field" maxlength="1" pattern="[0-9]*" inputmode="numeric">
          <input type="text" class="nip-field" maxlength="1" pattern="[0-9]*" inputmode="numeric">
          <input type="text" class="nip-field" maxlength="1" pattern="[0-9]*" inputmode="numeric">
        </div>

        <div id="nipError" class="nip-error-msg">
          <i class="bi bi-x-circle-fill"></i> El PIN es incorrecto
        </div>
      `,
      didOpen: () => {
        const inputs = document.querySelectorAll('.nip-field');
        const errorDiv = document.getElementById('nipError');
        inputs[0].focus();

        inputs.forEach((input, index) => {
          input.addEventListener('input', async (e) => {
            input.value = input.value.replace(/[^0-9]/g, '');
            
            if(errorDiv.style.display === 'flex') {
              errorDiv.style.display = 'none';
            }

            if (input.value.length === 1 && index < inputs.length - 1) {
              inputs[index + 1].focus();
            }

            const pinCompleto = Array.from(inputs).map(i => i.value).join('');
            if (pinCompleto.length === 6) {
              inputs.forEach(i => i.disabled = true);

              try {
                const formData = new FormData(form);
                formData.append('action_pin', pinCompleto);

                const response = await fetch(form.action, {
                  method: 'POST',
                  headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                  },
                  body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                  Swal.close();
                  window.location.reload(); 
                } else {
                  inputs.forEach(i => {
                    i.disabled = false;
                    i.value = ''; 
                  });
                  errorDiv.style.display = 'flex';
                  inputs[0].focus(); 
                }
              } catch (error) {
                inputs.forEach(i => {
                  i.disabled = false;
                  i.value = '';
                });
                errorDiv.innerText = "Error al conectar con el servidor.";
                errorDiv.style.display = 'flex';
                inputs[0].focus();
              }
            }
          });

          input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && input.value.length === 0 && index > 0) {
              inputs[index - 1].focus();
            }
          });
        });
      }
    });
  }
</script>
@endsection