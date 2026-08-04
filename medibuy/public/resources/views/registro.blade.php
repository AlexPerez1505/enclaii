<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <link rel="icon" type="image/png" href="{{ asset('images/logoai.png') }}?v=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <title>Registro</title>
    <style>
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

        /* Contenedores */
        .contenedor-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .contenedor_general {
            background-color: white;
    padding: 1rem;
    border-radius: 5px;
    box-shadow: -4px 1px 16px rgba(0, 0, 0, 0.1);
    width: 100%; /* Aseguramos que ocupe el 100% en pantallas pequeñas */
    max-width: 85%; /* Limita el ancho máximo a 85% en pantallas grandes */
    height: auto;
    margin-top: 25px;
    transition: transform 0.3s ease;
    box-sizing: border-box; /* Asegura que el padding no afecte el ancho total */
}
   
         /* Botones */
        .form-grupo  {
            margin-bottom: 35px;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
        }

        .btn {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 15px;
            border-radius: 10px;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 1.6rem;
        }

        .btn-guardar {
            background-color: #1E6BB8;
            gap: 8px;
            width: 140px;
            height: 45px;
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
            font-size: 15px;
            text-align: center;
            border: none;
            color: white;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-guardar:hover {
            background-color: #155a91;
            transform: scale(1.05);
        }

        .btn-borrar {
            background-color: #E43E3D;
            width: 140px;
            height: 45px;
            font-family: 'Roboto', sans-serif;
            font-weight: bold;
            font-size: 15px;
            text-align: center;
            border: none;
            color: white;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-borrar:hover {
            background-color: #bf2f2e;
            transform: scale(1.05);
        }

        .icono  {
            width: 23px;
            height: 23px;
        }
        .icono-borrar  {
            width: 33px;
            height: 21px;
        }
        /* Contenedores para los campos de texto */
        .label_nomina{
            font-family: "Helvetica Neue LT Std", Arial, sans-serif;
            font-weight: bold;
            color: #333333;
            font-size: 15px;
        }
        .form-group {
            margin-bottom: 0.75rem;
            text-align: left;
            position: relative;
            display: flex;
            align-items: center;
        }
        .input_consulta {
            display: flex;
            align-items: center;
            padding-bottom: 0px;
            border-bottom: 2px solid #1E6BB8;
            border-radius: 10px;
            width: 100%;
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
        }
        .form-group .icon2 {
            width: 23px;
            height: auto;
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
    padding-top: 70px; /* Ajusta este valor al alto del encabezado */
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
    .form-grupo {
        width: 100%;
        margin-top: 0 !important; /* Elimina la superposición */
    }

    .image-preview-container,
    #video-preview-container {
        width: 100%;
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
         
            .preview-contenedor {
                max-width: 100%; /* No limitar el ancho en pantallas pequeñas */
                padding: 10px;
                box-shadow: none;
            }
            .file-input-text {
                font-size: 14px; /* Reducir el tamaño del texto para pantallas más pequeñas */
                height: 40px; /* Reducir la altura para ajustarse mejor en pantallas pequeñas */
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
        /* Media query para pantallas grandes */
        @media (min-width: 768px) {
            .custom-margin {
                margin-left: 40px;
            }
            .customer-margin {
                margin-left: 50px;
            }
        }
        .contenedor_observaciones{
            width: 100%;
            max-width: 1150px; /* Ancho máximo en pantallas grandes */


            margin-left: 40px;
        }
        /* Línea de separación */
        .division {
            width: 95%;
            margin: 0 auto;
            margin-top: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
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
        }

        .select {
            appearance: auto;
            border-radius: 10px 10px 10px 10px;
            width: 100%;
        }

        textarea::placeholder {
            color: #6c757d; /* Color gris oscuro */
            font-style: italic;
        }
        /* Unique Container Styles */
        .custom-container {
            max-width: 600px;
            margin: 50px auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        /* Unique Legend Styles */
        .custom-legend {
            font-size: 1.6rem;
            font-weight: 500;
            color: #444;
            margin-bottom: 15px;
            border-bottom: 3px solid #0066cc;
            padding-bottom: 5px;
        }

        /* Unique Form Group Styles */
        .custom-form-group {
            margin-bottom: 20px;
        }


        /* Unique Textarea Styles */
        .custom-textarea {
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: #f4f4f4;
            padding: 12px;
            font-size: 1rem;
            color: #333;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .custom-textarea:focus {
            border-color: #0066cc;
        }

        /* Unique Canvas Styles */
        .custom-canvas {
            width: 100%;
            height: 180px;
            border: 1px solid #bbb;
            border-radius: 6px;
            background-color: #fcfcfc;
            cursor: crosshair;
        }

        /* Unique Button Styles */
        .custom-button {
            background-color: #0066cc;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            margin-top: 10px;
        }

        .custom-button:hover {
            background-color: #004a99;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 102, 204, 0.3);
        }

        /* Hidden Input */
        .custom-hidden-input {
            display: none;
        }
        .info-icon {
            position: relative;
            display: inline-block;
            cursor: pointer;
            margin-left: 5px;
            width: 20px;
            height: 20px;
            background-color: #D9D9D9;
            border-radius: 50%;
            text-align: center;
        }

        .info-icon:hover .info-tooltip {
            display: block;
        }

        .info-tooltip {
            display: none;
            position: absolute;
            bottom: 120%;
            background-color: #D1ECF1;
            color: #000000;
            padding: 5px 10px;
            border-radius: 4px;
            white-space: nowrap;
            z-index: 1;
            font-weight: normal;
            padding: 15px;
            border: 1px solid #94ABAF;
            max-width: 500px;
            font-size: 12px;
        }

        .info-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 2%;
            border-width: 5px;
            border-style: solid;
            border-color: #94ABAF transparent transparent transparent;
        }
        .icon-container {
            width: 40px;
            height: 40px;
            background-color: transparent;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #F4F4F4;
            border-right: 1px solid #D1D1D1;
            border-radius: 10px 0 0 10px;

        }

        /* Elimina la flecha del navegador */
        .select.no-arrow {
            -webkit-appearance: none; /* Para navegadores WebKit (Chrome, Safari) */
            -moz-appearance: none;    /* Para Firefox */
            appearance: none;         /* Estándar */

        }
        /* estilos imagenes */
        .input-containerr {
            display: flex;
            align-items: center;
            height: 45px;
            border-radius: 10px;
            background-color: #fff;
            overflow: hidden;
            border: 1px solid #F4F4F4;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            padding: 0 15px; /* Espaciado interno uniforme */
            cursor: pointer;
            font-size: 13px;
            font-weight: normal;
            height: 100%; /* Alineación completa con el contenedor */
            gap: 15px; /* Espacio entre el ícono y el texto */
            background-color: transparent;
            align-items: center; /* Alinea verticalmente ícono y texto */
            justify-content: center; /* Centra contenido horizontalmente */
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .icon-containerr {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-pin {
            width: 20px; /* Ajustar según tus necesidades */
            height: 25px;
            color: #888;
        }


        .file-input {
            display: none;
        }
        .file-input-text {
            padding: 0.5rem; /* Ajustado el padding */
            border-radius: 0 10px 10px 0;
            border: none;
            width: 100%; /* Aseguramos que ocupe todo el espacio disponible */
            box-sizing: border-box;
            background-color: #F4F4F4; /* Fondo ajustado */
            color: #555555; /* Color del texto */
            height: 45px; /* Altura ajustada para que coincida con el icono */
            outline: none; /* Eliminar el resaltado al seleccionar */
            overflow: hidden; /* Evita que el texto sobresalga */
            text-overflow: ellipsis; /* Texto largo se mostrará con '...' */
            display: flex; /* Usar flexbox */
            align-items: center; /* Centrar verticalmente */
            justify-content: center; /* Centrar horizontalmente */
        }
        .preview-container {
            display: flex;
    flex-direction: column; /* Asegura que las imágenes se apilen verticalmente */
    gap: 10px;
}
        .video-preview video {
            border: 2px solid #ccc;
            border-radius: 5px;
            margin-top: 10px;
            max-width: 100%;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);

            transition: transform 0.3s ease;
        }
        .video-preview video:hover {
            transform: scale(1.05); /* Efecto de hover para un diseño más moderno */
        }
        .image-preview {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between; /* Alinea las imágenes de forma ordenada */
}

        .preview-contenedor {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            background-color: #F9F9F9;
            max-width: 400px; /* Máximo ancho para una apariencia más ordenada */
            margin: 0 auto; /* Centrar el contenedor */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .image-preview-container {
            display: flex;
    flex-wrap: wrap; /* Permite que las imágenes se ajusten si hay muchas */
    justify-content: center;
    gap: 10px;
    overflow-x: auto; /* Habilita el scroll horizontal si hay muchas imágenes */
    max-width: 100%;
    padding: 10px 0;
    box-sizing: border-box;

}


        /* Estilo de la imagen */
        .image-preview-container img {
            max-width: 100px;
    height: auto;
    margin: 5px;
    border-radius: 8px; /* Bordes redondeados */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

       /* Ampliación de la imagen al pasar el ratón */
.image-preview-container img:hover {
    transform: scale(1.1); /* Escalar la imagen individual al pasar el ratón */

}

/* Ajustes en pantallas pequeñas */
@media screen and (max-width: 600px) {
    .contenedor_general {
        width: 100%; /* Se asegura que ocupe el 100% del contenedor padre */
    }

    .image-preview-container {
        gap: 5px; /* Reduce el espacio entre imágenes */
        justify-content: flex-start; /* Alinea las imágenes al principio */
    }

    .image-preview-container img {
        max-width: 80px; /* Ajusta el tamaño de las imágenes en pantallas pequeñas */
    }
    }
    


        .preview-title {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-weight: 500;
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
            text-align: center;
        }
                .upload-message {
                    margin-top: 10px;
                    color: green;
                    color: #5cb85c; /* Verde para mensajes normales */
                    font-weight: bold;
                    /* Color de éxito */
                }

                .upload-message.error {
                    color: #d9534f; /* Rojo para errores */
                    font-weight: bold;
                    /* Color de error */
                }


        .remove-btn {
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(255, 0, 0, 0.7);
            border: none;
            color: white;
            border-radius: 50%;
            cursor: pointer;
            padding: 0;
            width: 20px;
            height: 20px;
            font-weight: bold;
            line-height: 20px;
            text-align: center;
        }
        .cuadro {
            display: block;
            text-align: left; /* Asegura que los textos estén alineados a la izquierda */
            margin: 0; /* Elimina cualquier margen que pueda centrar el contenido */
            padding-left: 35px; /* Asegura que no haya padding a la izquierda */
            width: 90%; /* Asegura que los elementos ocupen todo el ancho disponible */
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
    max-height: calc(100vh - 50px); /* Ajustar según el espacio del logo y bienvenida */
    overflow-y: auto; /* Permite el desplazamiento solo para el contenido debajo */
    padding: 10px;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-grow: 1;
}
/* Ocultar la barra de desplazamiento en navegadores basados en WebKit (Chrome, Safari) */
.menu-items::-webkit-scrollbar {
    display: none;
}

/* En Firefox, usar -moz para ocultar la barra de desplazamiento */
.menu-items {
    scrollbar-width: none; /* Ocultar barra en Firefox */
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





.modal-title {
    margin: 0;
}

.boton_cerrar {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: white;
    cursor: pointer;
}

.modal-body {
    padding: 20px;
    font-size: 1rem;
    color: #333;
}

.text-center {
    text-align: center;
}

.logo-modal {
    width: 80px;
    height: 80px;
    margin: 10px auto;
    border-radius: 50%;
    background-color: #f1f1f1;
    display: block;
    object-fit: cover;
}

.modal-footer {
    padding: 15px;
    background-color: #f9f9f9;
    border-top: 1px solid #e5e5e5;
    display: flex;
    justify-content: center;
    gap: 15px;
}

.boton_actualizar {
    padding: 10px 20px;
    font-size: 1rem;
    font-weight: bold;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.boton_actualizar img {
    width: 20px;
    height: 20px;
}

.boton_actualizar:hover {
    background-color: #164d84;
    transform: translateY(-3px);
}

.btn-primary {
    background-color: #1e6bb8;
    color: white;
    border: none;
    border-radius: 5px;
    padding: 10px 20px;
    font-weight: bold;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.btn-primary:hover {
    background-color: #164d84;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.btn-secondary {
    background-color: #f1f1f1;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px 20px;
    font-weight: bold;
    transition: background-color 0.3s ease, color 0.3s ease;
}

.btn-secondary:hover {
    background-color: #ddd;
    color: #000;
}

/* Responsive Adjustments */
@media (max-width: 576px) {
    .modal-content {
        padding: 15px;
    }

    .modal-header {
        font-size: 1.2rem;
        padding: 15px;
    }

    .modal-footer {
        flex-direction: column;
        gap: 10px;
    }

    .boton_actualizar,
    .btn-primary,
    .btn-secondary {
        width: 100%;
    }
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
.preview-containerr {
            padding: 10px; /* Reducir el padding en pantallas pequeñas */
            border-radius: 5px; /* Redondear más los bordes en pantallas pequeñas */
            max-width: 100%; /* Asegurar que el contenedor ocupe todo el ancho */
            }
            .preview-containerr {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            background-color: #F9F9F9;
            max-width: 320px; /* Máximo ancho para una apariencia más ordenada */
            margin: 0 auto; /* Centrar el contenedor */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .submenu {
    display: none;
    list-style: none;
    padding-left: 20px;
}

.submenu li {
    padding: 5px 0;
}

.swal2-popup {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            border-radius: 15px;
        }

        .swal2-title {
            color: #343a40;
            font-weight: bold;
        }

        .swal2-content {
            color: #495057;
            font-size: 16px;
        }

    </style>
</head>
<body>
    <div class="header-container">
    <div class="menu-hamburguesa">
        <button onclick="toggleMenu()" class="menu-icon">
            <img src="{{ asset('images/menu.png') }}" alt="Menu Icon">
        </button>

        <nav id="menu-sidebar" class="menu-sidebar">
            <div class="menu-header">
                <img src="{{ asset('images/logomedy.png') }}" alt="Logo" class="menu-logo">
            </div>

            @auth
                <div class="welcome-section">
                    <a href="{{ route('perfil') }}" class="welcome-link">
                        <p>Bienvenido,</p>
                        <p class="user-name">{{ Auth::user()->name }}</p>
                    </a>
                </div>
            @endauth

            <ul class="menu-items">
                <!-- Publicaciones -->
                <li>
                    <a href="#" onclick="toggleSubmenu(event, 'submenu-publicacion')">
                        <img src="{{ asset('images/publicacion.png') }}" alt="Icono de Publicacion" class="menu-icon-image">
                        Publicaciones
                    </a>
                    <ul id="submenu-publicacion" class="submenu">
                        <li><a href="{{ url('/publicaciones') }}">Ver publicaciones</a></li>
                        <li><a href="{{ url('/publicaciones/crear') }}">+ Agregar</a></li>
                    </ul>
                </li>
                <!-- Inventario -->
                <li>
                    <a href="#" onclick="toggleSubmenu(event, 'submenu-inventario')">
                        <img src="{{ asset('images/inventario.png') }}" alt="Icono de Inventario" class="menu-icon-image">
                        Inventario
                    </a>
                    <ul id="submenu-inventario" class="submenu">
                        <li><a href="{{ url('/') }}">Registro Interno</a></li>
                        <li><a href="{{ url('/inventario') }}">Inventario Interno</a></li>
                        <li><a href="{{ url('/servicio') }}">Registro Externo</a></li>
                        <li><a href="{{ url('/inventario/servicio') }}">Inventario Externo</a></li>
                    </ul>
                </li>

                @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('editor'))
                    <!-- Cotizaciones -->
                    <li>
                        <a href="#" onclick="toggleSubmenu(event, 'submenu-cotizaciones')">
                            <img src="{{ asset('images/cotizaciones.png') }}" alt="Icono de Cotizaciones" class="menu-icon-image">
                            Cotizaciones
                        </a>
                        <ul id="submenu-cotizaciones" class="submenu">
                            <li><a href="{{ url('/propuestas') }}">Cotización</a></li>
                            <li><a href="{{ url('/clientes/vista') }}">Clientes</a></li>
                        </ul>
                    </li>
                    <!-- Mantenimiento -->
                    <li>
                        <a href="#" onclick="toggleSubmenu(event, 'submenu-orden')">
                            <img src="{{ asset('images/orden.png') }}" alt="Icono de Orden" class="menu-icon-image">
                            Mantenimiento
                        </a>
                        <ul id="submenu-orden" class="submenu">
                            <li><a href="{{ url('/remisions/create') }}">+ Crear Orden</a></li>
                            <li><a href="{{ url('/remisions') }}">Historial</a></li>
                        </ul>
                    </li>

                    <!-- Remisiones y Financiamientos -->
                    <li>
                        <a href="{{ url('/ventas') }}">
                            <img src="{{ asset('images/remisiones.png') }}" alt="Icono de remisiones" class="menu-icon-image">
                            Remisiones
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('/ventas/deudores') }}">
                            <img src="{{ asset('images/cuentas.png') }}" alt="Icono de deudores" class="menu-icon-image">
                            Financiamientos
                        </a>
                    </li>
                    <!-- Productos -->
                    <li>
                        <a href="{{ url('/productos/cards') }}">
                            <img src="{{ asset('images/productos.png') }}" alt="Icono de productos" class="menu-icon-image">
                            Productos
                        </a>
                    </li>
                    <li>
                    <a href="{{ url('/promos/whatsapp/direct') }}">
                        <img src="{{ asset('images/cupon.png') }}" alt="Icono de viaticos" class="menu-icon-image">
                        Promocionales
                    </a>
                    </li>
                    <li>
                        <a href="{{ url('/whatsapp/inbox') }}">
                            <img src="{{ asset('images/whatsapp.png') }}" alt="Icono de viaticos" class="menu-icon-image">
                            WhatsApp Help Desk
                        </a>
                    </li>
                @endif

                <!-- Ítems generales -->
                <li>
                    <a href="{{ url('/agenda') }}">
                        <img src="{{ asset('images/agenda.png') }}" alt="Icono de agenda" class="menu-icon-image">
                        Agenda
                    </a>
                </li>
                <li>
                    <a href="{{ route('fichas.index') }}">
                        <img src="{{ asset('images/documento.png') }}" alt="Icono de fichas técnicas" class="menu-icon-image">
                        Fichas Técnicas
                    </a>
                </li>
                <li>
                    <a href="{{ url('/carta-garantia') }}">
                        <img src="{{ asset('images/garantia.png') }}" alt="Icono de garantia" class="menu-icon-image">
                        Garantias
                    </a>
                </li>
                <li>
                    <a href="{{ url('/cuentas') }}">
                        <img src="{{ asset('images/viaticos.png') }}" alt="Icono de viaticos" class="menu-icon-image">
                        Gastos Viáticos
                    </a>
                </li>
                @auth
                    <!-- Guías -->
                <li>
                    <a href="{{ route('entregas.index') }}">
                        <img src="{{ asset('images/fedex.png') }}" alt="Icono de viaticos" class="menu-icon-image">
                        Guías
                    </a>
                </li>
                    <!-- Camionetas -->
                    <li>
                        <a href="#" onclick="toggleSubmenu(event, 'submenu-camionetas')">
                            <img src="{{ asset('images/cady.png') }}" alt="Icono de Camionetas" class="menu-icon-image">
                            Camionetas
                        </a>
                        <ul id="submenu-camionetas" class="submenu">
                            <li><a href="{{ route('camionetas.create') }}">+ Agregar Camioneta</a></li>
                            <li><a href="{{ route('camionetas.index') }}">Lista de Camionetas</a></li>
                        </ul>
                    </li>

                    <!-- Solicitudes de Material -->
                    <li>
                        <a href="#" onclick="toggleSubmenu(event, 'submenu-solicitudes-material')">
                            <img src="{{ asset('images/material.png') }}" alt="Icono de Solicitudes de Material" class="menu-icon-image">
                            Solicitudes de Material
                        </a>
                        <ul id="submenu-solicitudes-material" class="submenu">
                            @if(Auth::user()->hasRole('admin'))
                                <li><a href="{{ route('solicitudes.admin') }}">Solicitudes Pendientes</a></li>
                            @endif
                            <li><a href="{{ route('solicitudes.index') }}">Ver Mis Solicitudes</a></li>
                            <li><a href="{{ route('solicitudes.create') }}">+ Crear Solicitud</a></li>
                        </ul>
                    </li>

                    <!-- Pedidos -->
                    <li>
                        <a href="#" onclick="toggleSubmenu(event, 'submenu-pedidos')">
                            <img src="{{ asset('images/compras.png') }}" alt="Icono de Pedidos" class="menu-icon-image">
                            Compras
                        </a>
                        <ul id="submenu-pedidos" class="submenu">
                            @if(Auth::user()->hasRole('admin'))
                                <li><a href="{{ url('/pedidos') }}">Pedido Solicitado</a></li>
                            @endif
                            <li><a href="{{ url('/recepciones') }}">Ver Mis Solicitudes</a></li>
                            <li><a href="{{ url('/recepciones/timeline') }}">Historial Global</a></li>
                        </ul>
                    </li>

                    <!-- Préstamos -->
                    <li>
                        <a href="{{ route('prestamos.index') }}">
                            <img src="{{ asset('images/endoscopia.png') }}" alt="Icono de Préstamos" class="menu-icon-image">
                            Préstamos
                        </a>
                    </li>
                @endauth

                @if(Auth::user()->hasRole('admin'))
                    <li>
                        <a href="{{ url('/transactions') }}">
                            <img src="{{ asset('images/presupuesto.png') }}" alt="Icono de gastos" class="menu-icon-image">
                            Movimientos de caja
                        </a>
                    </li>
                    <!-- Usuarios -->
                    <li>
                        <a href="#" onclick="toggleSubmenu(event, 'submenu-usuarios')">
                            <img src="{{ asset('images/empleado.png') }}" alt="Icono de Usuarios" class="menu-icon-image">
                            Usuarios
                        </a>
                        <ul id="submenu-usuarios" class="submenu">
                            <li><a href="{{ route('users.create') }}">+ Agregar Usuario</a></li>
                            <li><a href="{{ url('/usuarios') }}">Lista de Usuarios</a></li>
                            <li><a href="{{ url('/asistencias/historial') }}">Reporte Asistencias</a></li>
                            <li><a href="{{ route('asistencias.index') }}">Registrar Asistencias</a></li>
                        </ul>
                    </li>
                @endif

                @auth
                    <!-- Cerrar Sesión -->
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="logout-button">
                                <img src="{{ asset('images/cerrar.png') }}" alt="Icono de Cerrar Sesión" class="menu-icon-image">
                                Cerrar Sesión
                            </button>
                        </form>
                    </li>
                @else
                    <li>
                        <a href="{{ route('login') }}">
                            <i class="icon-class icon-login"></i> Iniciar Sesión
                        </a>
                    </li>
                @endauth
            </ul>
        </nav>

        <!-- Overlay para cerrar el menú -->
        <div id="menu-overlay" class="menu-overlay" onclick="closeMenu()"></div>
    </div>
        <h1 class="titulos">Registro de Inventario</h1>
    <div class="gradient-bg-animation"></div>
    </div>
    <div class="contenedor-wrapper">
        <div class="contenedor_general">
        <form id="registroForm" action="{{ route('registro.guardar') }}" method="POST" enctype="multipart/form-data">
    @csrf
                <div class="row text-start">
                                <div class="col-10" style="margin-top: 25px;">
                                    <h5 class="titulos_encabezado">Información del Equipo</h5>
                                </div>
                                <div class="row" style="margin: 0 auto; gap: 10px;">
   <div class="container">
    <div class="row">
        <div class="col-md-4 col-12 mb-3">
            <label for="Tipo de Equipo" class="label_nomina d-block mb-1">Tipo de Equipo</label>
            <div class="form-group w-100">
                <div class="input_consulta" style="width: 100%;">
                    <div class="icon-container2" style="border: none;">
                        <img src="{{ asset('images/tipo.png') }}" alt="Tipo de Equipo" class="icon2">
                    </div>
                    <select class="form-control" id="tipoEquipo" name="Tipo de Equipo" style="background-color: #ffff; display:block;" value="{{ old('Tipo_de_Equipo') }}"  required>
                        <option value="">Selecciona un tipo de equipo</option>
                        <option value="endoscopia">Endoscopia</option>
                        <option value="laparoscopia">Laparoscopia</option>
                        <option value="quirofano">Quirofano</option>
                        <option value="hospitalizacion">Hospitalización</option>
                        <option value="cirujia">Cirujia</option>
                        <option value="artroscopia">Artroscopia</option>
                        <option value="autoclave">Autoclave</option>
                        <option value="ginecologia">Ginecología</option>
                        <option value="otros">Otros</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-12 mb-3" id="subtipoEquipoContainer" style="display: none;">
    <label for="Subtipo de Equipo" class="label_nomina align-self-start mb-1">Subtipo de Equipo</label>
    <div class="form-group">
        <div class="input_consulta" style="width: 100%;">
            <div class="icon-container2" style="border: none;">
                <img src="{{ asset('images/producto.png') }}" alt="Subtipo de Equipo" class="icon2">
            </div>
            <select class="form-control" id="subtipoEquipo" name="Subtipo de Equipo" style="background-color: #ffff; display:block; width: 100%;" value="{{ old('Subtipo_de_Equipo') }}"required>
            </select>
        </div>
    </div>
</div>
<div class="col-md-4 col-12 mb-3" id="subtipoEquipoOtroContainer" style="display: none;">
    <label for="Subtipo de Equipo" class="label_nomina align-self-start mb-1">Especifica el Subtipo de Equipo</label>
    <div class="form-group">
        <div class="input_consulta" style="width: 100%;">
            <div class="icon-container2" style="border: none;">
                <img src="{{ asset('images/producto.png') }}" alt="Subtipo de Equipo" class="icon2">
            </div>
            <input type="text" class="form-control" id="subtipoEquipoOtro" name="Subtipo de Equipo Otro" placeholder="Especifica el subtipo" value="{{ old('Subtipo_de_Equipo_Otro') }}"style="background-color: #ffff; display:block; width: 100%;" />
        </div>
    </div>
</div>
        <div class="col-md-4 col-12 mb-3">
            <label for="Numero de Serie" class="label_nomina align-self-start mb-1">Número de Serie</label>
            <div class="form-group">
                <div class="input_consulta" style="width: 100%;">
                    <div class="icon-container2" style="border: none;">
                        <img src="{{ asset('images/serie.png') }}" alt="Número de Serie" class="icon2">
                    </div>
                    <input
                    type="text"
                    class="form-control"
                    style="background-color: #ffff; display:block; "
                    name="Numero de Serie"
                    placeholder="Número de Serie"
                    value="{{ old('Numero_de_Serie') }}"
                    required>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-12 mb-3">
            <label for="Marca" class="label_nomina align-self-start mb-1">Marca</label>
            <div class="form-group">
                <div class="input_consulta" style="width: 100%;">
                    <div class="icon-container2" style="border: none;">
                        <img src="{{ asset('images/marca.png') }}" alt="Marca" class="icon2">
                    </div>
                    <input
                    type="text"
                    class="form-control"
                    style="background-color: #ffff; display:block;"
                    name="Marca"
                    placeholder="Ej. Olympus, Storz"
                     value="{{ old('Marca') }}"
                    required>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-12 mb-3">
            <label for="Modelo" class="label_nomina align-self-start mb-1">Modelo</label>
            <div class="form-group">
                <div class="input_consulta" style="width: 100%;">
                    <div class="icon-container2" style="border: none;">
                        <img src="{{ asset('images/modelo.png') }}" alt="Modelo" class="icon2">
                    </div>
                    <input
                    type="text"
                    class="form-control"
                    style="background-color: #ffff; display:block;"
                    name="Modelo"
                    placeholder="Ej. CF-HQ190L"
                    value="{{ old('Modelo') }}"
                    required>
                </div>
            </div>
        </div>

        <div class="col-md-2 col-12 mb-3">
    <label for="Año" class="label_nomina d-block mb-1">Año</label>
    <div class="form-group">
        <div class="input_consulta" style="width: 100%;">
            <div class="icon-container2" style="border: none;">
                <img src="{{ asset('images/anio.png') }}" alt="Año" class="icon2">
            </div>
            <input
                type="text"
                class="form-control"
                style="background-color: #ffff; display:block;"
                name="Año"
                placeholder="Ejemplo: 2023"
                maxlength="4"
                pattern="\d{4}"
                title="El año debe ser un número de 4 dígitos"
                value="{{ old('Año') }}">
        </div>
    </div>
</div>
        <div class="col-md-4 col-12">
            <label for="descripcion" class="label_nomina d-block mb-1">Descripción</label>
            <div class="form-group">
                <textarea class="form-control select" name="descripcion" placeholder="Escribe aquí una descripción detallada del equipo" rows="6" required>{{ old('descripcion') }}</textarea>
            </div>
        </div>
    </div>
</div>
<div class="division"></div>
<div class="row text-start">
    <div class="col-10" style="margin-top: 17px;">
 <h5 class="titulos_encabezado">Evidencia</h5>
 </div>
<div class="row d-flex justify-content-between align-items-start" style="margin: 15px;">
    <div class="col-md-3 col-12 mb-3">
        <label for="fecha_inicial" class="label_nomina mb-1">Fecha de Adquisición</label>
        <div class="form-group">
            <input type="date" class="form-control select" id="fecha_inicial" name="fecha_inicial" value="{{ old('fecha_inicial') }}"required>
        </div>
    </div>
    <div class="form-grupo col-md-6">
    <div class="d-block w-100">
        <label class="label_nomina mb-1">Fotos del equipo
            <div class="info-icon">?
                <div class="info-tooltip text-center">Máximo 3 (una por campo).</div>
            </div>
        </label>

        <!-- Input para Imagen 1 -->
        <div class="d-flex align-items-center input-containerr">
            <label for="evidencia1" class="file-input-label d-flex align-items-center">
                <div class="icon-containerr">
                    <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de pin" class="icon-pin">
                </div>
                <span>Seleccione archivo 1</span>
            </label>
            <input type="file" id="evidencia1" name="evidencia1" class="file-input" onchange="updatePreview(this, 'preview1')" accept="image/*">
            <div id="file-input-text-images1" class="file-input-text">Sin selección</div>
        </div>

        <!-- Input para Imagen 2 -->
        <div class="d-flex align-items-center input-containerr mt-2">
            <label for="evidencia2" class="file-input-label d-flex align-items-center">
                <div class="icon-containerr">
                    <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de pin" class="icon-pin">
                </div>
                <span>Seleccione archivo 2</span>
            </label>
            <input type="file" id="evidencia2" name="evidencia2" class="file-input" onchange="updatePreview(this, 'preview2')" accept="image/*">
            <div id="file-input-text-images2" class="file-input-text">Sin selección</div>
        </div>

        <!-- Input para Imagen 3 -->
        <div class="d-flex align-items-center input-containerr mt-2">
            <label for="evidencia3" class="file-input-label d-flex align-items-center">
                <div class="icon-containerr">
                    <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de pin" class="icon-pin">
                </div>
                <span>Seleccione archivo 3</span>
            </label>
            <input type="file" id="evidencia3" name="evidencia3" class="file-input" onchange="updatePreview(this, 'preview3')" accept="image/*">
            <div id="file-input-text-images3" class="file-input-text">Sin selección</div>
        </div>

 <!-- Contenedor para previsualizar las imágenes -->
<div id="preview-container" class="preview-container mt-3">
    <h5 class="preview-title">Previsualización de las Imágenes:</h5>
    <div class="image-preview-container">
        <div id="preview1" class="image-preview"></div>
        <div id="preview2" class="image-preview"></div>
        <div id="preview3" class="image-preview"></div>
    </div>
</div>
    </div>
</div>
<div class="form-grupo col-md-6" style="margin-top: -200px; position: relative;">
    <div class="d-block w-100">
        <label for="video-evidencia" class="label_nomina mb-1" >Video del equipo
            <div class="info-icon">?
                <div class="info-tooltip text-center">Máximo 1.</div>
            </div>
        </label>
        <div class="d-flex align-items-center input-containerr">
            <label for="video-evidencia" class="file-input-label d-flex align-items-center">
                <div class="icon-containerr">
                    <img src="{{ asset('images/adjunto-archivo.png') }}" alt="Icono de video" class="icon-pin">
                </div>
                <span>Seleccione archivos</span>
            </label>
            <input type="file" id="video-evidencia" name="video-evidencia" class="file-input"
                onchange="updateVideoPreview(this, 'file-input-text-video')" 
                accept="video/mp4,video/avi,video/mpeg,video/webm,video/quicktime">
            <div id="file-input-text-video" class="file-input-text">Sin selección</div>
        </div>
        <!-- Área de previsualización -->
        <div id="video-preview-container" class="preview-containerr mt-3" style="display: none;">
            <h5 class="preview-title">Previsualización del Video:</h5>
            <div id="video-preview" class="video-preview"></div>
            <div id="video-message" class="upload-message"></div>
        </div>
    </div>
</div>
<div class="division"></div>

                <!-- Observaciones -->
                <h5 class="titulos_encabezado">Notas Adicionales</h5>
                 <!-- Campo para subir PDF -->
                 <div class="cuadro mt-4">
    <!-- Observaciones -->
    <div class="mb-4">
        <label for="observaciones" class="form-label">Observaciones</label>
        <textarea
            id="observaciones"
            name="observaciones"
            class="form-control select"
            placeholder="Espacio para comentarios adicionales"
            rows="4"
            required>{{ old('observaciones') }}</textarea>
    </div>
<!-- Nombre del usuario que agrega el registro -->
@auth
<div class="mb-4">
    <label for="user_name" class="form-label">Registrado por</label>
    <input type="text" id="user_name" name="user_name" class="form-control" 
           value="{{ Auth::user()->name }}" readonly />
</div>
@endauth

    <!-- Firma Digital -->
    <div class="mb-3">
        <label for="firmaCanvas" class="form-label">Firma Digital</label>
        <div class="border rounded mb-2" style="height: 150px; overflow: hidden;">
            <canvas id="firmaCanvas" class="w-100 h-100"></canvas>
        </div>
        <div class="text-end">
            <button id="limpiarFirma" class="btn btn-primary" type="button">Limpiar Firma</button>
        </div>
        <input type="hidden" id="firmaInput" name="firmaDigital"value="{{ old('firmaDigital') }}" />
    </div>
</div>
</div>
<body>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('firmaCanvas');
    const ctx = canvas.getContext('2d');
    const limpiarFirma = document.getElementById('limpiarFirma');
    const firmaInput = document.getElementById('firmaInput');
    let dibujando = false;

    // Establecer dimensiones responsivas del canvas
    function ajustarCanvas() {
        const ratio = window.devicePixelRatio || 1;
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        ctx.scale(ratio, ratio);
        ctx.lineCap = 'round';
        ctx.lineWidth = 2;
        ctx.strokeStyle = '#333';
    }
    ajustarCanvas();
    window.addEventListener('resize', ajustarCanvas);

    function obtenerPosicion(event) {
        const rect = canvas.getBoundingClientRect();
        const clientX = event.touches ? event.touches[0].clientX : event.clientX;
        const clientY = event.touches ? event.touches[0].clientY : event.clientY;
        return {
            x: clientX - rect.left,
            y: clientY - rect.top
        };
    }

    function comenzarDibujo(event) {
        event.preventDefault();
        dibujando = true;
        const { x, y } = obtenerPosicion(event);
        ctx.beginPath();
        ctx.moveTo(x, y);
    }

    function dibujar(event) {
        if (!dibujando) return;
        event.preventDefault();
        const { x, y } = obtenerPosicion(event);
        ctx.lineTo(x, y);
        ctx.stroke();
    }

    function detenerDibujo(event) {
        if (!dibujando) return;
        dibujando = false;
        ctx.closePath();
        firmaInput.value = canvas.toDataURL('image/png');
    }

    // Soporte para mouse
    canvas.addEventListener('mousedown', comenzarDibujo);
    canvas.addEventListener('mousemove', dibujar);
    canvas.addEventListener('mouseup', detenerDibujo);
    canvas.addEventListener('mouseleave', detenerDibujo);

    // Soporte para touch
    canvas.addEventListener('touchstart', comenzarDibujo, { passive: false });
    canvas.addEventListener('touchmove', dibujar, { passive: false });
    canvas.addEventListener('touchend', detenerDibujo);
    canvas.addEventListener('touchcancel', detenerDibujo);

    // Botón limpiar
    limpiarFirma.addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        firmaInput.value = '';
    });
});
</script>

<script>
function updatePreview(input, previewId) {
    const file = input.files[0];
    const previewContainer = document.getElementById(previewId);
    const fileInputText = document.getElementById('file-input-text-images' + previewId.charAt(previewId.length - 1)); // Obtener el texto de estado

    // Limpiar contenido anterior
    previewContainer.innerHTML = '';

    if (file && file.type.startsWith('image/')) {
        const imgContainer = document.createElement('div');
        imgContainer.classList.add('image-preview-container');
        imgContainer.style.position = 'relative';
        imgContainer.style.maxWidth = '100%';  // Asegura que no se expanda más allá de su contenedor

        const img = document.createElement('img');
        const fileURL = URL.createObjectURL(file);
        img.src = fileURL;
        img.style.maxWidth = '100px';  // Limita el tamaño de la imagen
        img.style.margin = '5px';
        img.onload = () => URL.revokeObjectURL(fileURL);

        const removeBtn = document.createElement('button');
        removeBtn.innerHTML = '&times;';
        removeBtn.className = 'remove-btn';
        removeBtn.style.position = 'absolute';
        removeBtn.style.top = '0';
        removeBtn.style.right = '0';
        removeBtn.style.background = 'red';
        removeBtn.style.color = 'white';
        removeBtn.style.border = 'none';
        removeBtn.style.borderRadius = '50%';
        removeBtn.style.cursor = 'pointer';

        // Eliminar imagen
        removeBtn.onclick = function () {
            input.value = ''; // Elimina el archivo seleccionado
            previewContainer.innerHTML = ''; // Limpia la previsualización
            fileInputText.textContent = 'Sin selección'; // Vuelve a mostrar "Sin selección"
        };

        imgContainer.appendChild(img);
        imgContainer.appendChild(removeBtn);
        previewContainer.appendChild(imgContainer);

        // Cambia el texto a "Imagen seleccionada"
        fileInputText.textContent = 'Imagen seleccionada';
    } else {
        // Si no es una imagen válida, muestra "Sin selección"
        fileInputText.textContent = 'Sin selección';
    }
}
</script>




<script>
function updateVideoPreview(input) {
    const file = input.files[0]; // Solo se permite un archivo
    const videoPreview = document.getElementById('video-preview');
    const message = document.getElementById('video-message');
    const previewContainer = document.getElementById('video-preview-container');
    const fileInputText = document.getElementById('file-input-text-video'); // Elemento para mostrar el nombre del archivo

    // Reiniciar vista previa
    videoPreview.innerHTML = '';
    message.textContent = '';

    // Validar si se seleccionó un archivo
    if (file) {
        // Comprobar si el archivo es un video
        if (file.type.startsWith('video/')) {
            // Mostrar mensaje minimalista en lugar del nombre del archivo
            fileInputText.textContent = 'Video seleccionado';

            // Crear el elemento <video>
            const video = document.createElement('video');
            video.controls = true; // Habilitar controles del video
            video.width = 300; // Ancho del video
            video.src = URL.createObjectURL(file); // Crear URL para el video

            // Liberar memoria una vez que se haya cargado el video completamente
            video.onloadeddata = () => {
                video.onended = () => {
                    URL.revokeObjectURL(video.src);
                };
            };

            // Crear botón de eliminar
            const removeBtn = document.createElement('button');
            removeBtn.className = 'remove-btn';
            removeBtn.innerHTML = '&times;'; // Tache (X)
            removeBtn.onclick = function () {
                // Eliminar el video y el botón de eliminar
                videoPreview.innerHTML = '';
                input.value = ''; // Limpiar el input
                previewContainer.style.display = 'none'; // Ocultar el contenedor de previsualización
                fileInputText.textContent = 'Sin selección';
            };

            // Crear contenedor para el video y el botón de eliminar
            const videoContainer = document.createElement('div');
            videoContainer.className = 'video-container';
            videoContainer.style.position = 'relative'; // Asegura que el botón se posicione sobre el video
            videoContainer.appendChild(video);
            videoContainer.appendChild(removeBtn);

            // Agregar el video y el botón de eliminar al contenedor de previsualización
            videoPreview.appendChild(videoContainer);

            message.textContent = 'Se ha seleccionado un video.';
            message.className = 'upload-message';

            // Mostrar el contenedor de previsualización
            previewContainer.style.display = 'block';
        } else {
            // Mostrar mensaje de error si el archivo no es un video
            message.textContent = 'El archivo seleccionado no es un video válido.';
            message.className = 'upload-message error';
            previewContainer.style.display = 'none';
            input.value = ''; // Limpiar el input
            fileInputText.textContent = 'Sin selección';
        }
    } else {
        // Restablecer si no hay archivo seleccionado
        previewContainer.style.display = 'none';
        fileInputText.textContent = 'Sin selección';
    }
}

</script>



</div>
</div>
</div>

<!-- Botones -->
<div class="form-grupo btn-container">
    <!-- Botón Guardar -->
    <button type="submit" class="btn btn-guardar enviar" id="submitBtn">
        <img src="{{ asset('images/guardar.png') }}" alt="Enviar icono" class="icono">
        Guardar
    </button>

    <!-- Botón Borrar -->
    <button type="reset" class="btn btn-borrar borrar" id="resetBtn">
        <img src="{{ asset('images/erase.png') }}" alt="Borrar icon" class="icono-borrar">
        Borrar
    </button>
</div>
<!-- Modal -->
<div class="modal fade" id="NutrimoilEnviado" tabindex="-1" role="dialog" aria-labelledby="NutrimoilEnviadoLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header encabezado_modal">
                <h5 class="modal-title titulo_modal" id="addNoteModalLabel">Tu registro se ha guardado exitosamente.</h5>
                <button type="button" class="close boton_cerrar" onclick="cerrarModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <img src="{{ asset('images/confirmar.png') }}" alt="Logo de encabezado" class="logo-modal">
                </div>
                <p class="text-center">El equipo se ha guardado. Puedes proceder a agregar otro o cerrar este mensaje <b>Grupo MediBuy</b>.</p>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button type="button" class="boton_actualizar boton1" id="newNutrimailBtn">
                    <img class="imagen_boton_modal" src="{{ asset('images/agregar.png') }}" alt="Nuevo Nutrimail">
                    <span class="texto_boton_modal">Agregar Otro</span>
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Verifica si la sesión contiene el mensaje de éxito
        @if(session('success'))
            const modal = new bootstrap.Modal(document.getElementById('NutrimoilEnviado'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        @endif

        // Cierra el modal
        document.querySelector('.boton_cerrar').addEventListener('click', function () {
            const modalInstance = bootstrap.Modal.getInstance(document.getElementById('NutrimoilEnviado'));
            modalInstance.hide();
        });

        // Maneja el botón "Agregar Otro"
        document.getElementById('newNutrimailBtn').addEventListener('click', function () {
            // Refresca la página para borrar todo
            location.reload();
        });
    });
</script>

<script>
    // Cargar los archivos de sonido desde la ruta en public
    const clickSoundGuardar = new Audio('/assets/sounds/borrar.mp3');
    const clickSoundBorrar = new Audio('/assets/sounds/subir.mp3');

    // Agregar evento click al botón Guardar
    document.getElementById('submitBtn').addEventListener('click', () => {
        clickSoundGuardar.currentTime = 0; // Reinicia el sonido para reproducirlo desde el inicio
        clickSoundGuardar.play(); // Reproduce el sonido
    });

    // Agregar evento click al botón Borrar
    document.getElementById('resetBtn').addEventListener('click', () => {
        clickSoundBorrar.currentTime = 0; // Reinicia el sonido para reproducirlo desde el inicio
        clickSoundBorrar.play(); // Reproduce el sonido
    });
</script>
<script>
    // Seleccionar el botón reset
    document.getElementById('resetBtn').addEventListener('click', () => {
        // Limpiar el formulario
        const form = document.getElementById('registroForm');
        form.reset(); // Resetea los valores de todos los campos de entrada

        // Limpiar previsualización de video
        const videoPreview = document.getElementById('video-preview');
        const fileInputTextVideo = document.getElementById('file-input-text-video');
        const videoPreviewContainer = document.getElementById('video-preview-container');

        videoPreview.innerHTML = '';
        fileInputTextVideo.textContent = 'Sin selección';
        videoPreviewContainer.style.display = 'none';

        // Limpiar previsualización de imágenes
        const imagePreview = document.getElementById('image-preview');
        const fileInputTextImages = document.getElementById('file-input-text-images');
        const previewContainer = document.getElementById('preview-container');
        const message = document.getElementById('message');

        imagePreview.innerHTML = ''; // Eliminar todas las imágenes
        fileInputTextImages.textContent = 'Sin selección';
        message.textContent = ''; // Limpiar mensaje
        previewContainer.style.display = 'none'; // Ocultar contenedor de imágenes

        // Limpiar canvas de firma
        const canvas = document.getElementById('firmaCanvas');
        const ctx = canvas.getContext('2d');
        const firmaInput = document.getElementById('firmaInput');

        ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpiar el contenido del canvas
        firmaInput.value = ''; // Reiniciar el valor del input oculto

        // Opcional: Limpiar cualquier otro campo o acción relacionada
        // Si necesitas borrar alguna otra cosa que no se ha considerado, puedes agregarla aquí
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const yearInput = document.querySelector('input[name="Año"]');
    
    yearInput.addEventListener('input', () => {
        const value = yearInput.value;
        if (!/^\d{0,4}$/.test(value)) {
            yearInput.value = value.slice(0, -1); // Elimina el último carácter si no es válido
        }
    });
});

</script>

<script>
     // Definir los subtipo de equipo por categorías
     const tiposEquipos = {
        endoscopia: [
            "Adaptador USB", "Adaptador para Sonda", "Bomba de Irrigación", "Bomba de Secreción", "Boquillas", "Broncoscopio", 
            "Cable", "Cable Bipolar", "Cable USB", "Cámara con Cabezal", "Carrito", "Cepillo de Limpieza", "Colonoscopio", 
            "Contenedor de Liquidos","Case de transporte", "Duodenoscopio", "Eliminador", "Engrapadora","Fuente de Luz", "Gastroscopio", "Kit de Limpieza", 
            "Lineas de Irrigación", "Mause", "Monitor", "Pigtail", "Pigtel", "Pinza de Biopsia", "Pinza de Biopsia Hot", 
            "Pinza de Extracción", "Pinza de Polipectomia", "Probador", "Probador de Fuga", "Procesador", 
            "Proctector", "Sistema", "Sistema Endoscopia", "Tapon-ETO", "Teclado", "Video Carro","Regulador de CO2 Endoscopia", "Argon Plasma", "Electrocauterio", "válvulas"
        ],
        laparoscopia: [
            "Adaptador", "Adaptador Para Ligasure", "Adaptador Bipolar Ligasure", "Armonico", "Cabezal", "Camara", "Cable USB", "Camilla", "Charolas de Esterilización", "Coblator",
            "Clips para Monitor", "Eliminador", "Fibra de Luz", "Forcetriad", "Fuente de Luz", "Insuflador", "Lampara XENON", 
            "Lente", "Maletin/Case", "Manguera de Insuflación", "Manguera para Bomba de Agua", "Manguera y Yugo", 
            "Monitor", "Pedestal", "Pieza de Mano", "Pinza", "Rasurador y Radio Frecuencia", "Set de Artroscopia", 
            "Trasmisor", "Trocar", "Video Carro", "Video Grabador", "Yugo", "Carro FT10", "FT10", "Carro Forcetriad", 
        ],

        quirofano: [
            "Arco en C", "Cartucho", "Desfibrador", "Electrocauterio", "Eliminador", "Lámpara de Cirugía", "Lámpara de Quirofano", 
            "Máquina de Anestesia", "Mesa de Cirugía", "Consola Quirurjica", "Monitor Signos Vitales","Pedal Monopolar", "Pedal Bipolar"
        ],

        hospitalizacion: [
             "Aspirador", "Cama Hospitalaria Eléctrica", "Camilla", "Cuna Térmica", "Incubadora", "Mesa de Exploración" ,"Ventilador"
        ],
        cirujia: [
            "Lapíz para Electrocauterio", "Placa para Electrocauterio", "Brazalete"
        ],
        artroscopia: [
            "Set de Taladros de Artroscopia", "Serfas de radiofrecuencia", "Meditronic", "Set de astroscopia para muñeca y tobillo"
        ],
        autoclave :[
          "Autoclave de cámara 95 L " 
        ],
        ginecologia: [
            "Mesa de Exploración", "Cama de Ginecología"
        ],
        otros: [] // Dejar vacío si no tiene subtipo específico
    };

    // Función para actualizar los subtipo de equipo según la categoría seleccionada
    document.getElementById("tipoEquipo").addEventListener("change", function() {
    const tipoSeleccionado = this.value;
    const subtipoSelect = document.getElementById("subtipoEquipo");
    const subtipoContainer = document.getElementById("subtipoEquipoContainer");
    const subtipoOtroContainer = document.getElementById("subtipoEquipoOtroContainer");

    // Limpiar las opciones anteriores
    subtipoSelect.innerHTML = '<option value="">Selecciona un subtipo</option>';

    if (tipoSeleccionado) {
        if (tipoSeleccionado === "otros") {
            // Mostrar el campo de texto para "Otros"
            subtipoContainer.style.display = "none";
            subtipoOtroContainer.style.display = "block";
        } else {
            // Mostrar el select para subtipos específicos
            subtipoOtroContainer.style.display = "none";
            subtipoContainer.style.display = "block";

            // Agregar las opciones correspondientes
            const opciones = tiposEquipos[tipoSeleccionado];
            opciones.forEach(subtipo => {
                const option = document.createElement("option");
                option.value = subtipo.toLowerCase().replace(/\s+/g, '_'); // Formateo limpio
                option.textContent = subtipo;
                subtipoSelect.appendChild(option);
            });
        }
    } else {
        // Ocultar ambos contenedores si no se selecciona ningún tipo
        subtipoContainer.style.display = "none";
        subtipoOtroContainer.style.display = "none";
    }
});

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
        function toggleMenu() {
            const menu = document.querySelector('.menu-hamburguesa .menu-items');
            menu.classList.toggle('active');
        }
    </script>

<script>
   let startX;

function toggleMenu() {
    const menu = document.getElementById('menu-sidebar');
    const overlay = document.getElementById('menu-overlay');
    const body = document.body;

    if (menu.classList.contains('open')) {
        closeMenu();
    } else {
        menu.classList.add('open');
        overlay.classList.add('active');
        body.classList.add('menu-open');
    }
}

function closeMenu() {
    const menu = document.getElementById('menu-sidebar');
    const overlay = document.getElementById('menu-overlay');
    const body = document.body;

    menu.classList.remove('open');
    overlay.classList.remove('active');
    body.classList.remove('menu-open');
}

// Manejo de gestos en pantallas táctiles
document.addEventListener('touchstart', function (e) {
    startX = e.touches[0].clientX;
});

document.addEventListener('touchmove', function (e) {
    const currentX = e.touches[0].clientX;
    const menu = document.getElementById('menu-sidebar');

    if (startX > currentX && startX - currentX > 50 && menu.classList.contains('open')) {
        closeMenu();
    }
});
</script>
<script>
function toggleSubmenu(event, submenuId) {
    event.preventDefault(); // Evita que el enlace recargue la página
    const submenu = document.getElementById(submenuId);
    submenu.style.display = (submenu.style.display === "block") ? "none" : "block";
}
</script>


    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById("registroForm").addEventListener("submit", function (event) {
                event.preventDefault(); // Evita el envío normal del formulario

                let formData = new FormData(this); // Captura los datos del formulario

                // Mostrar SweetAlert con la barra de progreso
                Swal.fire({
    title: "Subiendo archivo...",
    html: `
        <style>
            .swal2-popup {
                border-radius: 1rem !important;
                font-family: 'Segoe UI', sans-serif;
            }
            .progress-container {
                width: 100%;
                background-color: #f3f4f6;
                border-radius: 1rem;
                overflow: hidden;
                height: 16px;
                box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
                margin-top: 10px;
            }
            .progress-bar {
                height: 100%;
                width: 0%;
                background: linear-gradient(to right, #4f46e5, #3b82f6);
                transition: width 0.3s ease;
            }
            .progress-text {
                margin-top: 12px;
                font-weight: 600;
                font-size: 14px;
                color: #374151;
            }
        </style>
        <div class="progress-container">
            <div id="progress-bar" class="progress-bar"></div>
        </div>
        <div class="progress-text">Progreso: <span id="upload-progress">0%</span></div>
    `,
    allowOutsideClick: false,
    showConfirmButton: false,
    didOpen: () => {
        Swal.showLoading();
    }
});


                // Crear XMLHttpRequest
                const xhr = new XMLHttpRequest();

                // Evento para el progreso de la carga
                xhr.upload.addEventListener('progress', function (e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100); // Calcula el porcentaje de carga
                        document.getElementById('upload-progress').textContent = percent + '%';
                        document.getElementById('progress-bar').style.width = percent + '%';
                    }
                });

                // Configurar la solicitud
                xhr.open('POST', this.action, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('input[name="_token"]').value);

                // Enviar la solicitud
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        let data = JSON.parse(xhr.responseText);
                        if (data.success) {
                            Swal.fire({
                                title: "¡Registro guardado!",
                                text: "El registro se guardó correctamente.",
                                icon: "success"
                            }).then(() => {
                                window.location.href = "{{ route('inventario') }}"; // Redirige a la vista de inventario
                            });
                        } else {
                            Swal.fire({
                                title: "Error",
                                text: "Hubo un problema al guardar el registro.",
                                icon: "error"
                            });
                        }
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: "Hubo un problema al guardar el registro.",
                            icon: "error"
                        });
                    }
                };

                // Manejo de errores
                xhr.onerror = function () {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo conectar con el servidor.",
                        icon: "error"
                    });
                };

                // Enviar el formulario con los datos
                xhr.send(formData);
            });
        });
    </script>





            </form>
        </div>
    </div>
</body>
</html>
