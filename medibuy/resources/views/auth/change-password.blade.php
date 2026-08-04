<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5faff;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }
        h1 {
            font-size: 1.5rem;
            color: #0056b3;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .user-greeting {
            text-align: center;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            color: #555;
        }
        .alert {
            padding: 0.75rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        label {
            font-weight: bold;
            font-size: 0.9rem;
            color: #555;
        }
        input {
            padding: 0.75rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border-color 0.3s ease;
        }
        input:focus {
            border-color: #0056b3;
            outline: none;
        }
        button {
            background-color: #0056b3;
            color: #fff;
            padding: 0.75rem;
            font-size: 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #004094;
        }
        .small-text {
            font-size: 0.8rem;
            color: #666;
            text-align: center;
            margin-top: 1rem;
        }
        .welcome-link {
            display: block;
            text-decoration: none;
            color: #333;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            
            border: 1px solid transparent;
            border-radius: 5px;
            background-color: #7CB8EB;
            transition: all 0.3s ease, transform 0.3s ease; /* Agregamos transición para transform */
        }
        
        .welcome-link:hover {
            color: black;
            background-color: #7CB8EB;
            border: 1px solid #7CB8EB;
            text-decoration: none;
            transform: scale(0.9); /* Aumenta el tamaño en un 10% */
        }
@media (max-width: 375px) {
    .titulos {
        font-size: 20px;
        transform: translateX(-30%); /* Elimina el desplazamiento lateral en pantallas pequeñas */
    }
}
.header-container {
            position: fixed; /* Hace que el contenedor sea fijo */
            top: 0; /* Lo fija en la parte superior de la pantalla */
            left: 0; /* Lo fija al inicio del viewport horizontal */
            width: 100%; /* Asegura que el encabezado abarque todo el ancho de la pantalla */
            z-index: 1000; /* Coloca el encabezado por encima de otros elementos */
            background-color: #ffffff; /* Añade un fondo para que no se superponga con otros elementos */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Añade una ligera sombra para destacarlo */
            display: grid;
            grid-template-columns: auto 1fr auto; /* Tres columnas: menú, título, espacio */
            align-items: center;
            padding: 10px;
            box-sizing: border-box;
        }
        body {
    padding-top: 10px; /* Ajusta este valor al alto del encabezado */
}
       /* Logo de encabezado */
       .logo {
            width: 230px; /* Tamaño del logo en pantallas grandes */
            height: auto;
        }
          /* Media query para pantallas pequeñas */
          @media (max-width: 768px) {
            body.menu-open .contenedor_general {
        transform: translateX(250px);
    }
    body {
    padding-top: -100% !important; /* Ajusta este valor al alto del encabezado */
}
    .header-container {
        grid-template-columns: 1fr; /* Una sola columna en pantallas pequeñas */
        text-align: center; /* Centra todo en el eje horizontal */
    }
    .menu-hamburguesa {
        justify-self: start; /* Alinea el menú al inicio en pantallas pequeñas */
    }
            .logo {
                width: 180px;
                order: -1;
            }
            .titulos {
        font-size: 20px;
        transform: translateX(-45%); /* Elimina el desplazamiento lateral en pantallas pequeñas */
    }
            .custom-margin {
                margin-left: 0;
            }
            .customer-margin {
            margin-left: 0 !important;
            }
            .contenedor {
                    margin-left: 14px; !important; /* Forzamos a sobrescribir otros estilos */
                    margin-right: 30px;
            }
            .contenedor_observaciones {
                    margin-left: 14px !important;
                    margin-right: 14px !important;
            }
            .image-preview {
                display: flex;
                flex-wrap: wrap;
                justify-content: center; /* Centrar las imágenes */
            }
            .image-preview-container {
                width: calc(50% - 10px); /* Dos imágenes en una fila */
                margin-right: 5px;
                margin-bottom: 10px;
            }
            .preview-contenedor {
                max-width: 100%; /* No limitar el ancho en pantallas pequeñas */
                padding: 10px;
                box-shadow: none;
            }
            .file-input-text {
                font-size: 14px; /* Reducir el tamaño del texto para pantallas más pequeñas */
                height: 40px; /* Reducir la altura para ajustarse mejor en pantallas pequeñas */
            }
            .preview-container {
            padding: 10px; /* Reducir el padding en pantallas pequeñas */
            border-radius: 5px; /* Redondear más los bordes en pantallas pequeñas */
            max-width: 100%; /* Asegurar que el contenedor ocupe todo el ancho */
            }
            .video-preview video {
                max-width: 100%; /* Asegurar que el video no se desborde */
                height: 280px; /* Mantener la proporción */
                border-radius: 5px; /* Redondear los bordes del video */
            }
            .cuadro {
                margin-left: -15px !important;
                margin-right: 5px !important;
                width: 100%;
                padding: -200px;
            }
        }
         /* Estilos para el menú hamburguesa */
.menu-hamburguesa {
    position: relative;
    z-index: 10000;
    grid-column: 1; /* Coloca el menú en la primera columna */
}

.menu-hamburguesa button {
    background-color: #ffffff;

    border: none;
    padding: 10px 15px;
    cursor: pointer;
    z-index: 10001;
}

.menu-sidebar {
    position: fixed;
    top: 0;
    left: -250px;
    width: 250px;
    height: 100%;
    background: #F4F4F4;
    color: #fff;

    transition: left 0.3s ease;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5);
    z-index: 10001;
    display: flex;
    flex-direction: column;
}

.menu-sidebar.open {
    left: 0;
}
/* Overlay (fondo oscuro al abrir menú) */
.menu-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    z-index: 10000;
}

.menu-overlay.active {
    display: block;
}
/* Estilos de los ítems del menú */
.menu-items {
    list-style: none;
    padding: 0;
    margin: 0;
    flex-grow: 1;
}

.menu-items li {
    padding: 15px 20px;
    border-bottom: 1px solid #CCCCCC;
    transition: background 0.3s ease, transform 0.3s ease;
    position: relative;
}

.menu-items li a {
    text-decoration: none;
    color: #333;
    display: flex;
    align-items: center;
    width: 210px;
    height: 35px;
    transition: color 0.3s ease;
    position: relative;
    overflow: hidden;
}

.menu-items li i {
    margin-right: 10px;
    font-size: 18px;
    transition: transform 0.3s ease, color 0.3s ease;
}

/* Subrayado animado */
.menu-items li a::after {
    content: "";
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #007bff;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease-in-out;
}

.menu-items li a:hover::after {
    transform: scaleX(1);
}

.menu-items li:hover {
    background: rgba(0, 123, 255, 0.1);
    transform: scale(1.02);
    border-radius: 5px;
}

.menu-items li a:hover {
    color: #01579b;
}

.menu-items li a:hover i {
    color: #007bff;
    transform: rotate(10deg);
}



/* Imagen del icono */
.menu-icon-image {
    width: 34px; /* Tamaño del icono */
    height: 34px;
    margin-right: 5px; /* Espacio entre el icono y el texto */
    object-fit: contain;
}
.menu-icon-imagen {
    width: 25px; /* Tamaño del icono */
    height: 25px;
    margin-right: 11px; /* Espacio entre el icono y el texto */
    margin-left: 3px;
    object-fit: contain;
}

.logout-button {
    border: none;
    color: black;
    display: flex;
    align-items: center;
    cursor: pointer;
    width: 100%;
    text-align: left;
    padding: 0;
}

.logout-button i {
    margin-right: 10px;
}
.menu-logo {
    width: 220px;
    height: auto;
}
/* Logo del menú */
.menu-header {
    padding: 20px;
    background: #F4F4F4;
    text-align: center;
}
.menu-hamburguesa .menu-icon img {
    width: 34px;
    height: 34px;
}

/* Sección de bienvenida */
.welcome-section {
    padding: 15px 20px;
    background: #7cb8eb;
    border-bottom: 1px solid #444;
    color: black;
    font-size: 14px;
}

.welcome-section .user-name {
    font-weight: bold;
    color: #33373b;
}

/* Estilo para ocultar el contenido al moverlo */
        /* Estilos proporcionados */
        .titulos {
            font-family: "Helvetica Neue LT Std", Arial, sans-serif;
            font-weight: 900;
            color: #333333;
            font-size: 30px;
            line-height: 24px;
            text-align: center; /* Centra el texto */
            grid-column: 2; /* Coloca el título en la columna central del grid */
            margin: 0 auto; /* Centra el texto horizontalmente dentro de la columna */
            transform: translateX(-10%); /* Mueve el título ligeramente hacia la izquierda */
        }

        .titulos_encabezado {
            font-family: "Helvetica Neue LT Std", Arial, sans-serif;
            font-weight: 400;
            font-size: 20px;
            line-height: 24px;
            margin-left: 25px;
        }
        /* Linea de encabezado*/
        .gradient-bg-animation {
            width: 100%;
            bottom: 0; /* Posiciona la línea en la parte inferior del encabezado */
            position: absolute;
            height: 4px;
            border-radius: 2.5px;
            background: linear-gradient(26deg, #01448F 20%, #1E6BB8 40%, #3498DB 60%, #87CEFA 80%, #B0E0E6 100%);
            background-size: 300% 100%;
            animation: gradient 3s cubic-bezier(0.25, 0.46, 0.45, 0.94) infinite;
            margin-top: 10px;
        }

        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        .password-container {
    position: relative;
    display: flex;
    align-items: center;
}

.password-container input {
    padding-right: 30px;  /* Deja espacio para el icono del ojo */
    width: 100%;
}

.password-container img.togglePassword {
    position: absolute;
    right: 10px;  /* Ajusta la distancia del ojo al borde derecho */
    top: 50%;  /* Centra verticalmente */
    transform: translateY(-50%);
    cursor: pointer;
    width: 20px;  /* Ajusta el tamaño del icono */
    height: 20px;
}
.back-button {
        display: flex;
        align-items: center;
    }

    .menu-icon {
        background: none;
        border: none;
        cursor: pointer;
        padding: 5px;
    }

    .menu-icon img {
        width: 45px;
        height: 40px;
    }

    </style>
</head>
<body>
<div class="header-container">
<div class="back-button">
    <button onclick="window.location.href='{{ route('perfil') }}'" class="menu-icon">
        <img src="{{ asset('images/atras.png') }}" alt="Regresar">
    </button>
</div>

    <h1 class="titulos">Cambiar Contraseña</h1>

    <!-- Línea animada -->
    <div class="gradient-bg-animation"></div>
</div>

    <div class="container">
 

        <div class="user-greeting">
            Hola, <strong>{{ Auth::user()->name }}</strong> 👋
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('auth.update-password') }}" onsubmit="return validatePassword()">
    @csrf
    <div>
        <label for="current_password">Contraseña Actual</label>
        <div class="password-container">
            <input type="password" id="current_password" name="current_password" required placeholder="Ingresa tu contraseña actual">
            <img src="{{ asset('images/ojo.png') }}" class="togglePassword" 
                 onclick="togglePassword('current_password')" 
                 style="cursor: pointer;">
        </div>
    </div>

    <div>
        <label for="new_password">Nueva Contraseña</label>
        <div class="password-container">
            <input type="password" id="new_password" name="new_password" required placeholder="Ingresa tu nueva contraseña" oninput="checkPasswordStrength()">
            <img src="{{ asset('images/ojo.png') }}" class="togglePassword" 
                 onclick="togglePassword('new_password')" 
                 style="cursor: pointer;">
        </div>
        <ul id="password-requirements" style="color: red; font-size: 14px; display: none;">
            <li id="length">❌ Mínimo 8 caracteres</li>
            <li id="lowercase">❌ Al menos una letra minúscula</li>
            <li id="uppercase">❌ Al menos una letra mayúscula</li>
            <li id="number">❌ Al menos un número</li>
            <li id="special">❌ Al menos un carácter especial (@, #, $, etc.)</li>
        </ul>
    </div>

    <div>
        <label for="new_password_confirmation">Confirmar Nueva Contraseña</label>
        <div class="password-container">
            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required placeholder="Confirma tu nueva contraseña" oninput="checkPasswordMatch()">
            <img src="{{ asset('images/ojo.png') }}" class="togglePassword" 
                 onclick="togglePassword('new_password_confirmation')" 
                 style="cursor: pointer;">
        </div>
        <small id="confirm-error" style="color: red; display: none;">Las contraseñas no coinciden.</small>
    </div>

    <button type="submit">Actualizar Contraseña</button>
</form>

<p class="small-text">
    ¿Olvidaste tu contraseña? 
    <a href="https://wa.me/5649806155?text=Hola,%20ayúdame%20a%20cambiar%20mi%20contraseña.%20Mi%20ID%20de%20usuario%20es:%20{{ Auth::user()->nomina }}" 
       target="_blank" 
       style="color: #0056b3; text-decoration: none;">
       Manda un mensaje
    </a>
</p>


<script>
function togglePassword(inputId) {
    // Obtener el campo de entrada
    var input = document.getElementById(inputId);
    var eyeIcon = input.nextElementSibling;

    // Cambiar el tipo del campo entre "password" y "text"
    if (input.type === "password") {
        input.type = "text";  // Mostrar la contraseña
        eyeIcon.src = "{{ asset('images/ojo.png') }}";  // Cambiar la imagen del ojo a abierto
    } else {
        input.type = "password";  // Ocultar la contraseña
        eyeIcon.src = "{{ asset('images/ojo.png') }}";  // Cambiar la imagen del ojo a cerrado
    }
}


</script>

<script>
function checkPasswordStrength() {
    const password = document.getElementById('new_password').value;
    const requirements = document.getElementById('password-requirements');
    
    // Elementos individuales
    const length = document.getElementById('length');
    const lowercase = document.getElementById('lowercase');
    const uppercase = document.getElementById('uppercase');
    const number = document.getElementById('number');
    const special = document.getElementById('special');

    // Mostrar la lista de requisitos
    requirements.style.display = "block";

    // Verificar cada requisito y actualizar el estado visual
    updateRequirement(length, password.length >= 8);
    updateRequirement(lowercase, /[a-z]/.test(password));
    updateRequirement(uppercase, /[A-Z]/.test(password));
    updateRequirement(number, /[0-9]/.test(password));
    updateRequirement(special, /[@$!%*?&]/.test(password));
}

// Función para actualizar cada requisito con ❌ o ✅
function updateRequirement(element, condition) {
    if (condition) {
        element.style.color = "green";
        element.innerHTML = `✅ ${element.textContent.slice(2)}`; // Cambia a palomita
    } else {
        element.style.color = "red";
        element.innerHTML = `❌ ${element.textContent.slice(2)}`; // Cambia a tache
    }
}


function checkPasswordMatch() {
    const password = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('new_password_confirmation').value;
    const confirmError = document.getElementById('confirm-error');

    if (password !== confirmPassword) {
        confirmError.style.display = "block";
    } else {
        confirmError.style.display = "none";
    }
}

function validatePassword() {
    const password = document.getElementById('new_password').value;
    
    // Validar con expresiones regulares
    const isValid = password.length >= 8 &&
                    /[a-z]/.test(password) &&
                    /[A-Z]/.test(password) &&
                    /[0-9]/.test(password) &&
                    /[@$!%*?&]/.test(password);

    return isValid;
}
</script>
</html>
