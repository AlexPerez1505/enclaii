<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.1)), url('{{ asset("images/empresa.jpg") }}') no-repeat center center fixed;
            background-size: cover;
        }
        .titulos {
        font-size: 1.8rem;
        margin-bottom: 20px;
        color: #346733;
        font-weight: bold; /* Hace la letra más gruesa */
    }

    /* Formulario */
    .form-group {
        margin-bottom: 15px;
        position: relative;
    }

    .form-control {
            padding: 0.5rem; /* Ajustado el padding */
            border-radius: 0 10px 10px 0;
            border: none; /* Quitado el borde */
            width: calc(100% - 36px);
            box-sizing: border-box;
            background-color: #F4F4F4; /* Fondo ajustado */
            color: #555555; /* Color del texto */
            height: 40px; /* Altura ajustada para que coincida con el icono */
            outline: none; /* Eliminar el resaltado al seleccionar */
            margin-top:30px;
        }


    .form-control:focus {
        border-color: #00796b;
        outline: none;
        box-shadow: 0 0 5px rgba(0, 121, 107, 0.3);
    }

    .icon-container {
        position: absolute;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
    }

    .icon {
        width: 20px;
        height: auto;
    }

    .toggle-password {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        width: 20px;
        height: auto;
        margin-top:14px;
    }
 /* Botón */
 .btn-primary {
        display: inline-block;
        width: 190px;
        height:40px;
        padding: 12px;
        font-size: 1rem;
        font-weight: 600;
        color: #ffffff;
        background: #1E6BB8;
        border: none;
        border-radius: 8px;
        transition: background 0.3s ease, transform 0.2s ease;
        cursor: pointer;
        margin-left:100px;
    
    }

    .btn-primary:hover {
        background: #155a91;
        transform: translateY(-2px);
    }

    .forgot-password {
        text-align: center;
        margin-top: 10px;
        font-size: 0.9rem;
    }

    .forgot-password a {
        color: #00796b;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .forgot-password a:hover {
        color: #004d40;
    }

    .footer p {
        font-size: 0.85rem;
        text-align: center;
        color: #666;
        margin-top: 20px;
    }
    .logo {
    display: block;
    margin: 0 auto; /* Centrado horizontal */
    width: 400px; /* Aumento del tamaño del logo */
    height: auto; /* Mantiene proporción */
    margin-top: -10px;
}

    .footer-logo {
        display: block;
        margin: 20px auto 0; /* Centrado horizontal con espacio superior */
        width: 30px; /* Tamaño inicial */
        height: 30px; /* Mantiene proporción */
        opacity: 0.9; /* Ligera transparencia */
        transition: transform 0.3s ease, opacity 0.3s ease; /* Animación de hover */
    }
    .form-group .icon-container2 {
            width: 40px;
            height: 40px;
            background-color: #c0cab452;
            display: flex;
            justify-content: center;
            align-items: center;
            border-bottom-left-radius: 10px;
            border-top-left-radius: 10px;
            margin-top:30px;
        }
        .form-group .icon2 {
            width: 23px;
            height: auto;
        }
        .input_consulta {
        display: flex;
        align-items: center;
        padding-bottom: 0px;
        border-bottom: 2px solid #1E6BB8;
        border-radius: 10px;
        width: 100%;
        position: relative; /* Esto es importante para posicionar el icono de manera relativa al campo */
    }


    /* Animación */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
   /* Responsividad */
   /* Responsividad */
    @media (max-width: 768px) {
        .container-fluid {
            max-width: 500px; /* Aumenta el ancho en pantallas más grandes */
            padding: 30px;
        }

        .titulos {
            font-size: 2.2rem; /* Títulos más grandes */
        }

        .form-control {
            font-size: 1.1rem;
        }

        .btn-primary {
            font-size: 1.1rem;
            margin-left:60px;
        }
      

        .logo {
            width: 140px; /* Escala el logo en pantallas grandes */
        }

        
    }
    @import url('https://fonts.googleapis.com/css?family=Roboto:700');

#container {
    color: #999;
    text-transform: uppercase;
    font-size: 28px;
    font-weight: bold;
    padding-top: 10px;
    display: block;
}

#flip {
    height: 50px;
    overflow: hidden;
    display: inline-block;
}

#flip > div > div {
    color: #fff;
    padding: 4px 12px;
    height: 45px;
    margin-bottom: 45px;
    display: inline-block;
    font-size: 1.5rem;
    border-radius: 5px;
}

#flip div:first-child {
    animation: show 5s linear infinite;
}

#flip div div {
    background: #42c58a;
}

#flip div:first-child div {
    background: #4ec7f3;
}

#flip div:last-child div {
    background: #DC143C;
}

@keyframes show {
    0% { margin-top: -270px; }
    5% { margin-top: -180px; }
    33% { margin-top: -180px; }
    38% { margin-top: -90px; }
    66% { margin-top: -90px; }
    71% { margin-top: 0px; }
    99.99% { margin-top: 0px; }
    100% { margin-top: -270px; }
}

    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
<div class="w-full max-w-md p-8 space-y-6 bg-white shadow-lg rounded-xl">
    <div class="flex justify-center">
            <img src="{{ asset('images/logomedy.png') }}" alt="Logo" class="logo w-1/2">
        </div>
        <div class="row justify-content-center" style="display: none">
            <div class="col-12 d-flex justify-content-center">
                <div class="icono-header">
                </div>
            </div>
        </div>

       <div class="row justify-content-center">
    <div class="col-12 text-center">
        <div id="container">
            Impulsamos 
            <div id="flip">
                <div><div>Salud</div></div>
                <div><div>Innovación</div></div>
                <div><div>Confianza</div></div>
            </div>
            Contigo!
        </div>
    </div>
</div>


    
        <div class="login-container">
    <div class="inner-container">    
  
        <form method="POST" action="{{ route('Login') }}">
            @csrf
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="form-group">
                        <div class="input_consulta" style="width: 100%;">
                            <div class="icon-container2" style="border: none;">
                                <img src="{{ asset('images/matricula.png') }}" alt="Usuario" class="icon2">
                            </div>
                            <input type="text" class="form-control" style="background-color: #ffff; display:block; width: 100%;" name="nomina" placeholder="Usuario" value="{{ old('nomina') }}" required>
                        </div>
                        <!-- Error debajo del campo de usuario -->
                        @if ($errors->has('nomina'))
                            <div style="color: red; font-size: 17px;">
                                {{ $errors->first('nomina') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="form-group">
                        <div class="input_consulta" style="width: 100%;">
                            <div class="icon-container2" style="border: none;">
                                <img src="{{ asset('images/seguro.png') }}" alt="Contraseña" class="icon" style="width: 30px;">
                            </div>
                            <input type="password" class="form-control" name="contrasena" id="password" style="background-color: #ffff; display:block; width: 100%;" placeholder="Contraseña" required>
                            <img src="{{ asset('images/ojo.png') }}" alt="Ver" class="toggle-password" id="verPassword">
                        </div>
                        <!-- Error debajo del campo de contraseña -->
                        @if ($errors->has('contrasena'))
                            <div style="color: red; font-size: 17px;">
                                {{ $errors->first('contrasena') }}
                            </div>
                        @endif
                    </div>
             
   

            <!-- Error general debajo del formulario -->
            @if ($errors->has('message'))
                <div style="color: red; font-size: 12px;">
                    {{ $errors->first('message') }}
                </div>
            @endif
          
            <button type="submit" class="btn btn-primary block mx-auto mt-6">Entrar</button>
            <div class="forgot-password">
                <!-- Aquí va la opción de recuperación de contraseña si es necesario -->
            </div>
        </form>
    </div>
</div>

        <div class="footer">
            <div class="row justify-content-center">
                <div class="col-10 col-md-10">
                    <p class="small-text">
                        ¿Olvidaste tu contraseña? 
                        <a href="https://wa.me/5653350901?text=Hola,%20ayúdame%20a%20cambiar%20mi%20contraseña." 
                           target="_blank" 
                           style="color: #0056b3; text-decoration: none;">
                           Manda un mensaje
                        </a>
                    </p>
                </div>
            </div>
           
        </div>
    </div>
</div>

<script>
    const togglePasswordButton = document.getElementById('verPassword');
    const passwordInput = document.getElementById('password');

    togglePasswordButton.addEventListener('click', function () {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    });
</script>
</body>
</html>
