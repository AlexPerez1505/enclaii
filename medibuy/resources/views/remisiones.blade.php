<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema en Mantenimiento - Salud</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(30deg, #01448F, #1E6BB8, #3498DB, #87CEFA, #B0E0E6);
            background-size: 400% 400%;
            animation: gradientMove 7s ease infinite;
        }

        .content {
            text-align: center;
            color: white;
        }

        h1 {
            font-size: 50px;
            font-weight: bold;
            margin-bottom: 20px;
            animation: jumpText 1.5s ease-in-out infinite;
        }

        p {
            font-size: 24px;
            animation: fadeIn 2s ease-in-out forwards;
        }

        /* Icono de engranaje girando */
        .gear-loader {
            width: 80px;
            height: 80px;
            margin: 30px auto;
            border: 10px solid transparent;
            border-top: 10px solid white;
            border-radius: 50%;
            animation: spinGear 2s linear infinite;
        }

        /* Animaciones */
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        @keyframes jumpText {
            0% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0); }
        }

        @keyframes spinGear {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Diseño del pie de página */
        .footer {
            position: absolute;
            bottom: 20px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
            width: 100%;
        }

        .footer a {
            color: #ff4081;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <!-- Contenido central -->
    <div class="content">
        <h1>¡Sistema en Desarrollo!</h1>
        <p>Estamos actualizando el sistema en GrupoMedibuy para brindarte mas soluciones. <br>Por favor, vuelve pronto.<br></p>
        <div class="gear-loader"></div>
    </div>

    <!-- Pie de página -->
    <div class="footer">
        <p>¿Tienes preguntas? <a href="https://wa.me/5649806155?text=Hola,%20necesito%20más%20información%20del%20sistema.">Contáctanos</a></p>
        
    </div>

</body>
</html>
