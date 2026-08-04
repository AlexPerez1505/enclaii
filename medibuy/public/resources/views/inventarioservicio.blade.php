<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>


    <!-- Font Awesome (opcional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    

    <!-- DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
</head>

    <style>
    .titulo-seccion {
    font-size: 18px;
    font-weight: bold;
    margin: 20px 0 10px;
    text-align: left;
    margin-inline: 10px;
    margin: 20px;
        }
.texto {
    font-size: 15px;
    font-weight: bold;
    margin: 20px 0 10px;
    text-align: left;
    margin-inline: 10px;
    margin: 20px;
    margin-left: -3px;
}
.titulo-seccionb {
    font-size: 18px;
    font-weight: bold;
    margin: 20px 0 10px;
    text-align: left;
    margin-inline: 10px;
    margin: 20px;
}
h2 {
    align-self: flex-start; /* Alinea el h2 a la izquierda dentro del contenedor centrado */
    margin-left: 60px; /* Ajusta el margen según sea necesario */
    text-align: left; /* Alinea el texto a la izquierda */
}
p{
    font-family: "Helvetica Neue LT Std", Arial, sans-serif;
    font-size: 18px;
    margin-left: 10px;
    margin-right: 10px;
}
.form-controln {
    padding: 0.5rem;
    border-radius: 10px;
    border: none;
    width: 100%;
    box-sizing: border-box;
    background-color: #f0f0f0;
    color: #555555;
    height: 45px;
}

.container {
    display: flex;
    flex-direction: column;
    padding: 20px;
    box-sizing: border-box;
    max-width: 1200px;
    width: 100%;
}
.boton_actualizar {
    background-color: #24843F;
    border: none;
    border-radius: 10px;
    width: 167px;
    height: 45px;
    display: flex;
    padding: 6px;
    align-items: center;
}

.boton_cancelar {
    background-color: #D01C1C;
    border: none;
    border-radius: 10px;
    width: 167px;
    height: auto;
    display: flex;
    padding: 6px;
    align-items: center;
}

.boton_actualizar:hover {
    background-color: rgb(19, 82, 51);
}

.boton_cancelar:hover {
    background-color: #d01c1cd7;
}

.texto_boton_modal {
    margin-left: 5px;
    color: white;
    text-align: center;
    line-height: 1.1;
    font-weight: 400;
}

.boton1 {
    display: flex;
    justify-content: end;
    align-content: center;
    margin-top: 15px;
    margin-bottom: 20px;
}

.boton2 {
    display: flex;
    justify-content: start;
    align-content: center;
    margin-top: 15px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .boton1 {
        justify-content: center;
        margin-bottom: 2px;
    }

    .boton2 {
        justify-content: center;
    }
    h2 {
        margin-right: 50%; /* Ajusta este valor según sea necesario */
    }
}

.encabezadon {
    text-align: center;
    margin-bottom: 50px;
    position: relative;
    padding-top: 61px;
}

.icono-encabezadon {
    position: absolute;
    top: -5px;
    right: 20px;
    padding-top: 10px;
    transform: translateX(-30%);
    z-index: 10;
}
.encabezado {
    text-align: center;
    margin-bottom: 50px;
    position: relative;
    padding-top: 45px;
}

.icono-encabezado {
    position: absolute;
    top: -7px;
    right: 20px;
    transform: translateX(-30%);
    z-index: 10;
}

.icono-encabezadon img {
    width: 220px;
    height: auto;
    top: -79px;
    position: relative;
}
.icono-encabezado img {
    width: 220px;
    height: auto;
}
.icon-rh {
    top: -5px;
    right: 10px;
    text-align: left;
    z-index: 10;
}
.icon-rh img {
    width: 150px;
    height: auto;
}

.content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
}

.form-container {
    background-color: white;
    padding: 1rem;
    border-radius: 5px;
    box-shadow: -4px 1px 16px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 650px;
    width: 100%;
    margin-top: -35px;
    margin-left: 20px;
    margin-right: 30px;
}
.form-container-queja {
    background-color: white;
    padding: 1rem;
    border-radius: 5px;
    box-shadow: -4px 1px 16px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 85%;
    width: 100%;
    margin-top: 35px;
    margin-left: auto;
    margin-right: auto;

}
.form-container-queja {
    display: none;
}
.form-container-lower {
    background-color: white;
    padding: 1rem;
    border-radius: 5px;
    box-shadow: -4px 1px 16px rgba(0, 0, 0, 0.1);
    text-align: center;
    max-width: 85%;
    width: 100%;
    margin-top: 20px;
    margin-left: auto;
    margin-right: auto;
}
.form-container-low {
    background-color: white;
    padding: 1rem;
    border-radius: 5px;
    box-shadow: -4px 1px 16px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    max-width: 85%;
    width: 100%;
    margin-top: 20px;
    margin-left: auto;
    margin-right: auto;
}

.form-grupo  {
    margin-bottom: 10px;
}

.form-grupo  label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    text-align: left;
}
.form-grupo  labelp {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    text-align: center;
}
.form-grupo  labeld {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    text-align: left;
}

.form-controln.iconized {
    border-radius: 0 10px 10px 0;
}

.icon-container {
    width: 40px;
    height: 45px;
    background-color: transparent;
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px solid #F4F4F4;
    border-right: 1px solid #D1D1D1;
    border-radius: 10px 0 0 10px;
}

.icon {
    color: #888;
    width: 23px;
    height: 23px;
}
.icon-pin {
    color: #888;
    width: 15px;
    height: 23px;
}

.custom-checkbox {
    margin-top: -20px;
}

.btn {
    width: 35%;
    padding: 0.5rem;
    border-radius: 10px;
    background-color: #24843F;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 10px;
    margin-top: 1rem;
    justify-content: center;

}
.btnqueja {
    display: flex;
    align-items: center;
    padding: 10px 40px;
    font-size: 14px;
    border: none;
    border-radius: 10px;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin: 0 160px;
    justify-content: center;
}

.btnqueja-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.btnqueja-danger {
    background-color: #FF3131;
    width: 174px;
    height: 60px;
    font-family: 'Roboto', sans-serif;
    font-weight: bold;
    font-size: 16px;
    margin-right: 15px; /* Espacio entre los botones */
}
.btnqueja-danger:hover {
    background-color: #CC2929; /* Un tono más oscuro del color original */
}
.btnqueja-pdf {
    background-color: #FF3131;
    width: 165px;
    height: 60px;
    font-family: 'Roboto', sans-serif;
    font-weight: bold;
    font-size: 16px;
    margin-right: 15px; /* Espacio entre los botones */
    align-items: center; /* Alinea el contenido verticalmente al centro */
    justify-content: center;
    padding: 10px; /* Ajusta el relleno según sea necesario */
    text-align: center;
}
.btnqueja-pdf:hover {
    background-color: #CC2929; /* Un tono más oscuro del color original */
}
.form-grupo.btnqueja-container {
    display: flex;
    flex-wrap: wrap;
}

.form-grupo .btn {
    margin: 10px;

}

.btnqueja-success:hover {
    background-color: #007a00; /* Un tono más oscuro del color original */
}
.btn-text {
    display: inline-block;
    text-align: left;
    line-height: 1.2;
    white-space: pre-wrap;
}
.btnqueja-success {
    background-color: #307C38;
    width: 152px;
    height: 60px;
    font-family: 'Roboto', sans-serif;
    font-weight: bold;
    font-size: 16px;
}
.icono-borrar-queja {
    width: 29px;
    height: 46px;
    margin-right: 5px; /* Espacio entre el icono y el texto */
    margin-bottom: 15px;
}
.icono-pdf-queja {
    width: 35px;
    height: 39px;
    margin-right: 5px; /* Espacio entre el icono y el texto */
    margin-bottom: 5px;
}

.icono-queja {
    width: 29px;
    height: 29px;
    margin-right: 5px; /* Espacio entre el icono y el texto */
}

.btnqueja img {
    height: 24px; /* Ajustar el tamaño del icono */
    width: 24px; /* Asegurar que el icono sea cuadrado */
}

.btn-text {
    display: inline-block;
    text-align: left;
    line-height: 1.2;
}

.btnqueja-success:hover {
    background-color: #218838;
}

.btn-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.btn-container .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 14px;
    padding: 10px 15px;
}
.btn-danger {
    background-color: #E43E3D;
    width: 121px;
    height: 45px;
    font-family: 'Roboto', sans-serif;
    font-weight: bold;
    font-size: 16px;
    margin-right: 15px; /* Espacio entre los botones */

}
.btn-success {
    background-color: blue;
    width: 170px;
    height: 45px;
    font-family: 'Roboto', sans-serif;
    font-weight: bold;
    font-size: 14px;
}

.icono  {
    width: 23px;
    height: 23px;
}
.icono-borrar  {
    width: 33px;
    height: 21px;
}

.btn-danger {
    background-color: #EF403F;
}
.btn-danger:hover {
    background-color: #c0392b;
}

.btn-success {
    background-color: #1E6BB8;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-evenly;
    margin-bottom: 20px;
}

.form-row .form-grupo  {
    flex: 0 0 30%; /* Ajuste para ocupar el 30% en escritorio */
    box-sizing: border-box;
}

.form-fila {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-evenly;
    margin-bottom: 20px;
}


.form-fila .form-grupo  {
    flex: 0 0 0%; /* Ajuste para ocupar el 30% en escritorio */
    box-sizing: border-box;
}

.full-width {
    flex: 0 0 93%;
}

.image-container {
    max-width: 500px;
    margin: 20px auto;
    text-align: center;
}

.image-container img {
    max-width: 100%;
    height: auto;
}

textarea.form-controln {
    height: auto;
    min-height: 120px;
}

@media (max-width: 768px) {
    .form-row .form-grupo  {
        flex: 0 0 100%;
        margin-bottom: 10px;
    }
    .form-fila .form-grupo  {
        flex: 0 0 100%;
        margin-bottom: 10px;
    }


    input.form-controln,
    select.form-controln,
    textarea.form-controln {
        width: 100%;
    }

    .btn-container {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .btn-container .btn {
        width: 60%;
        padding: 10px 15px;
        font-size: 14px;
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: row;
        gap: 0px;
    }
    .btn-container .btn i {
        margin-right: 10px;
    }

    .tooltip-container .tooltip-text {
        width: auto;
        max-width: 60vw;
        left: 50%;
        transform: translateX(-50%);
        margin-left: 0;
    }


}
    .btn-container .btn:first-child {
        margin-top: 10;
    }


.tooltip-container {
    position: relative;
    display: inline-block;
    vertical-align: middle;
}

.tooltip-container .tooltip-text {
    visibility: hidden;
    width: 300px;
    background-color: #D1ECF1;
    color: #4C5267;
    text-align: center;
    border-radius: 6px;
    padding: 5px 0;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -100px;
    opacity: 0;
    transition: opacity 0.3s;
    font-family: "Helvetica Neue LT Std", Arial, sans-serif;
}

.tooltip-container .tooltip-text::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -61px;
    border-width: 8px;
    border-style: solid;
    border-color: #D1ECF1 transparent transparent transparent;
}

input[type="checkbox"] {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    width: 30px;
    height: 30px;
    background-color: white;
    border: 1px solid #6A6D70;
    border-radius: 3px;
    outline: none;
    cursor: pointer;
}
@media (max-width: 768px) {
    .tooltip-container .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: 97px;
        border-width: 8px;
        border-style: solid;
        border-color: #D1ECF1 transparent transparent transparent;

        transform: translateX(-50%);
    }
    .btnqueja {
        width: 80%; /* Botones ocupan el 90% del ancho */
        height: 35%;
        margin: 10px auto; /* Centrar los botones y espaciado vertical */
        padding: 15px; /* Espaciado interno más grande para que se vean alargados */
        font-size: 14px; /* Aumentar el tamaño de la fuente */
        display: flex; /* Flexbox para alinear contenido horizontalmente */
        flex-direction: row; /* Mantener el icono y el texto en una fila */
        align-items: center; /* Centrar verticalmente */
        justify-content: center; /* Centrar horizontalmente */
    }
    .modal-footer .boton-enviar-nota, .modal-footer .boton-cancelar, .modal-footer .boton-cerrar-queja {
        width: 50%;
        margin-bottom: 10px;
    }

    .btnqueja img {
        margin-right: 6px; /* Espaciado entre el icono y el texto */
        height: 24px; /* Altura del icono */
        width: auto; /* Ancho automático para mantener la proporción */
    }
    .btnqueja.btn-danger img {
        height: 39px;
    }
}


input[type="checkbox"]:checked {
    background-color: #6A6D70;
    border: 1px solid #6A6D70;
    position: relative;
}

input[type="checkbox"]:checked::after {
    content: '\2713';
    color: white;
    font-size: 22px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.tooltip-container:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}

.tooltip-container .fas {
    position: relative;
    top: 0;
    margin-right: 5px;
}

.custom-checkbox {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 5px;
}

.form-controln-common {
    height: 45px;
    margin-bottom: auto;
}

.form-controln-lg {
    width: 310px;
}

.form-controln-lq {
    width: 25px;
}

.form-controln-lw,
.form-controln-le {
    width: 150px;
}

.form-controln-md {
    width: 260px;
}

.form-controln-mp {
    width: 270px;
}

.form-controln-ma {
    width: 280px;
}

.form-controln:focus {
    box-shadow: none;
    outline: none;
}
/* Campos de texto específicos */

/* Campos de texto específicos con dimensiones basadas en la imagen proporcionada */
/* Campos de texto específicos con dimensiones compartidas */
.form-controlqueja-la,
.form-controlqueja-lb,
.form-controlqueja-lc,
.form-controlqueja-ld,
.form-controlqueja-le {
    width: 79%;
}

.form-controlqueja-lf,
.form-controlqueja-lg,
.form-controlqueja-lh,
.form-controlqueja-li {
    width: 206px;
}

.form-controlqueja-lj {
    width: 96%;

}
.form-controlqueja-lk {
    width: 200%;

}

.message .bubble.received {
    background-color: #fff9c4;
    margin-right: auto;
    border-bottom-left-radius: 0;
}

.chat-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 20px;
    max-width: 100%; /* Asegura que el contenedor no exceda el ancho de la pantalla */
    overflow: hidden; /* Esconde cualquier contenido que se salga del contenedor */
}

.message {
    display: flex;
    align-items: flex-end;
    margin-bottom: 10px;
     position: relative; /* Permite la posición absoluta del avatar */
}

.message.sent {
    justify-content: flex-end;
}

.message.received {
    justify-content: flex-start;
}

.bubble {
    max-width: 70%;
    padding: 10px;
    border-radius: 15px;
    position: relative;
    display: inline-block;
    word-wrap: break-word; /* Asegura que el texto largo se divida en palabras y no se salga de la burbuja */
    word-break: break-all;
}

.bubble.sent {
    background-color: #e0f7fa;
    text-align: left;
    margin-right: 10px;
    border-radius: 15px 15px 0 15px;
}
.bubble.enviar {
    background-color: #e0f7fa;
    text-align: left;
    margin-right: 10px;
    border-radius: 15px 15px 0 15px;
}
.bubble.enviar2 {
    background-color: #fff9c4;
    text-align: left;
    margin-right: 10px;
    border-radius: 15px 15px 0 15px;
}
 /* Estilo para los mensajes enviados */
 .message.sent .bubble {
    background-color: #e0f7fa;
        text-align: left;
        margin-right: 10px;
        border-radius: 15px 15px 0 15px;
 }

.bubble.received {
    background-color: #f1f1f1;
    text-align: left;
    margin-left: 10px;
    border-radius: 15px 15px 15px 0;
}
.bubble p {
    margin: 0;
}

.timestamp {
    font-size: 0.8em;
    color: #999;
    margin-top: 5px;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.message.sent .avatar {
    order: 2;
    margin-left: 10px;
}

.message.received .avatar {
    order: 1;
    margin-right: 10px;
}

.input-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}
.input-container textarea {
    flex-grow: 1;
    margin-right: 10px;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #ccc;
}
.input-container button {
    padding: 10px 20px;
    border-radius: 10px;
    border: none;
    background-color: #4caf50;
    color: #fff;
}
/* Modal */
.encabezado_modal {
    background-color: #24843f1e;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); /* Añadir sombra al fondo verde */
}

.titulo_modal {
    font-family: "Helvetica Neue LT Std", Arial, sans-serif;
    color: #333333;
    font-weight: 900;
    text-align: center; /* Asegura que el texto esté centrado */
    margin: 0 auto; /* Centra el contenedor */
}

.boton_cerrar {
    background-color: #e43e3d94;
    border: none;
    color: #FFFFFF;
    width: auto;
    height: auto;
    border-radius: 100px;
    position: absolute;
    right: 10px;
    top: 10px;
}

.boton_cerrar:hover {
    background-color: rgb(255, 0, 0);
        border: none;
        color: #FFFFFF;
    border-radius: 100px;
    position: absolute;
}

.boton_modal {
    background-color: #24843F;
    border-radius: 10px;
    width: auto;
    border: none;
    padding: 5px 12px 5px 10px;
    margin-bottom: 10px;
}

.imagen_boton_modal {
    width: 23px;
    height: 23px;
}
.logo-modal {
    width: 104px; /* Ajusta el tamaño a tu preferencia */
    height: 104px; /* Mantén la proporción de aspecto */
}
.modal-footer .btn-success {
    background-color: #24843F;
    border-color: #28a745;
}

.modal-footer .btn-success:hover {
    background-color: #24843F;
    border-color: #1e7e34;
}

.modal-footer .btn-danger {
    background-color: #dc3545;
    border-color: #dc3545;
}

.modal-footer .btn-danger:hover {
    background-color: #c82333;
    border-color: #bd2130;
}
.modal-footer .btn {
    width: 142px;
    height: 45px;
    padding: 10px 0;
    font-size: 18px;
    border-radius: 8px;
}
/* Ajustes específicos para el modal de agregar nota */
.modal-body .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
}
.modal-body .form-group textarea {
    width: 100%;
    height: 120px; /* Ajusta la altura según sea necesario */
    resize: none;
}
.modal-footer {
    display: flex;
    justify-content: space-around;
}
.avatar img{
    width: 500%;
}
.form-grupo.puesto-group {
    margin-left: auto; /* Ajusta este valor según sea necesario */
}
textarea#message {
    margin-left: 1px; /* Ajusta este valor según sea necesario */
}
.form-grupo.full-width .label-container {
    margin-left: auto; /* Ajusta este valor según sea necesario */
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.5em 1em;
    margin-left: 0;
    display: inline-block;
    border: 1px solid #ddd;
    border-radius: 3px;
    color: #333;
    text-decoration: none;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #c82020;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #17a2b8;
    color: white !important;
}
.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(0, 0, 0, 0.05);
}
.row-success {
    background-color: #d4edda !important; /* Verde */
}
.row-danger {
    background-color: #f8d7da !important; /* Rojo */
}
.row-warning {
    background-color: #fff3cd !important; /* Amarillo */
}
.row-info {
    background-color: #d1ecf1 !important; /* Azul claro para Registro */
}
.row-primary {
    background-color: #d1ecf1 !important; /* Lila claro para Hojalatería */
}
.row-defectuoso {
    background-color: #e2e3e5 !important; /* Un gris claro y sobrio */
    color: #333; /* Texto oscuro para buen contraste */
}


.button-text {
    padding-left: 10px;

}
.dataTables_wrapper {
    width: 100%;
    overflow: auto;
}
#example tbody td, #example tfoot th {
    text-align: center;
}
/* Estilos específicos para cada botón */
.btn-excel {
    background-color: #217346;
    color: #fff;
}
/* Estilo para el texto dentro de los botones */
.button-text {
    display: inline-block;
    vertical-align: middle;
    padding-left: 5px;
}

th, td {
    white-space: normal;
}

div.dataTables_wrapper {
    max-width: 100%;
    margin: 0 auto;
}

th input {
    width: 100%;
}

.badge {
    padding: 5px;
    width: 135px;
    height: 30px;
    text-align: center;
    border-radius: 3px;
    color: white;
    font-size: 15px;
    font-weight: lighter; /* Más ligero que el normal */
}

.badge-success {
    background-color: #52A639;
}

.badge-danger {
    background-color: #E43E3D;
}

.btn btn-info {

    align-items: center;
}
.btn-info {
    background-color: #17a2b8;
    color: white;
    border-radius: 20%;
    width: 30px;
    height: 30px;
    padding: 5px;
    align-items: center;
    justify-content: center;
    transition: background 0.3s ease, transform 0.2s ease;
    border: none; /* Eliminar bordes duros */
}
.btn-info:hover {
    background-color: #117a8b; /* Un tono más oscuro en hover */
    transform: scale(1.1); /* Pequeño efecto de zoom al pasar el cursor */
}
#btn-salida-mantenimiento {
    background-color: #FFC107; /* Amarillo */
}

#btn-salida-mantenimiento:hover {
    background-color: #D39E00; /* Un amarillo más oscuro en hover */
    transform: scale(1.1); /* Pequeño efecto de zoom al pasar el cursor */
}
#btn-hojalateria {
    background-color: #007bff; /* Amarillo */
}

#btn-hojalateria:hover {
    background-color: #0056b3; /* Un amarillo más oscuro en hover */
    transform: scale(1.1); /* Pequeño efecto de zoom al pasar el cursor */
}
#btn-entrada-mantenimiento {
    background-color: #52a639; /* Amarillo */
}

#btn-entrada-mantenimiento:hover {
    background-color: #3d7d29; /* Un amarillo más oscuro en hover */
    transform: scale(1.1); /* Pequeño efecto de zoom al pasar el cursor */
}
#btn-salida-dueno {
    background-color: #e43e3d; /* Amarillo */
}

#btn-salida-dueno:hover {
    background-color: #B52F2F; /* Un amarillo más oscuro en hover */
    transform: scale(1.1); /* Pequeño efecto de zoom al pasar el cursor */
}
#btn-defectuoso {
    background-color: #6c757d; /* Gris neutro */
}

#btn-defectuoso:hover {
    background-color: #5a6268; /* Gris más oscuro al hacer hover */
    border-color: #5a6268;
    transform: scale(1.1); /* Efecto de zoom al pasar el cursor */
}



.dt-button.buttons-html5.btn-excel {
    background-color: #339D4A;
    color: white;
    padding: 5px 2px; /* Ajusta según sea necesario */
    text-align: center;
    border-width: 0;

}

.dt-button.buttons-html5.btn-csv {
    background-color: #014490;
    color: white;
    padding: 5px 2px; /* Ajusta según sea necesario */
    text-align: center;
    border-width: 0;
}

.dt-button.buttons-html5.btn-pdf {
    background-color: #FF3131;
    color: white;
    padding: 5px 2px; /* Ajusta según sea necesario */
    text-align: center;
    border-width: 0;
}
/* Hover para cada botón */
.dt-button.buttons-html5.btn-excel:hover {
    background-color: #2d8a3f; /* Color más oscuro para Excel */
}

.dt-button.buttons-html5.btn-csv:hover {
    background-color: #013a7c; /* Color más oscuro para CSV */
}

.dt-button.buttons-html5.btn-pdf:hover {
    background-color: #e62929; /* Color más oscuro para PDF */
}

.dt-button, .buttons-pdf, .buttons-html5, .btn-pdf {
    width: 92px;
    height: 30px;
    font-size: 15px;
    line-height: 80px; /* Igual que la altura para centrar verticalmente */

    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.dt-button:focus {
    outline: none;
}

/* Cambiar color de fondo al pasar el ratón */
.dt-button:hover, .buttons-pdf:hover, .buttons-html5:hover, .btn-pdf:hover {
    background-color: #45a049;
}

/* Icono o logo dentro del botón */
.dt-button .button-text, .buttons-pdf .button-text, .buttons-html5 .button-text, .btn-pdf .button-text {
    margin-left: -5px; /* Espaciado entre el icono y el texto */
}

.dt-button .button-icon, .buttons-pdf .button-icon, .buttons-html5 .button-icon, .btn-pdf .button-icon {
    margin-right: 5px; /* Espaciado entre el borde y el icono */
}

table {
    font-family: "Helvetica Neue LT Std", Arial, sans-serif;
}

.btn-modal-custom {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    font-size: 16px;
}

.btn-modal-custom img {
    width: 20px;
    height: 20px;
    margin-right: 10px;
}

.btn-modal-custom span {
    display: inline-block;
    vertical-align: middle;
}

.modal-footer {
    display: flex;
    justify-content: center;
}

.boton-enviar-nota {
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    background-color: #24843F;
    width: 177px;
    height: 60px;
    border: none;
    border-radius: 10px;
    font-family: 'Roboto', sans-serif;
    font-weight: bold;
    font-size: 15px;
    margin-right: 45px;
}

.boton-enviar-nota .imagen-boton-enviar-nota {
    width: 43px;
    height: 31px;
    margin-right: 5px;
}

.boton-cancelar {
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    background-color: #E43E3D;
    width: 157px;
    height: 60px;
    border: none;
    border-radius: 10px;
    font-family: 'Roboto', sans-serif;
    font-weight: bold;
    font-size: 15px;
}

.boton-cancelar .imagen-boton-cancelar {
    width: 71px;
    height: 45px;
    margin-right: -5px;
}

.boton-cerrar-queja {
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    background-color: #24843F;
    width: 177px;
    height: 60px;
    border: none;
    border-radius: 10px;
    font-family: 'Roboto', sans-serif;
    font-weight: bold;
    font-size: 15px;
    margin-right: -35px;
}

.boton-cerrar-queja .imagen-boton-cerrar-queja {
    width: 43px;
    height: 31px;
    margin-right: 10px;
}

.length-and-buttons {
    display: flex;
    align-items: center;
    margin-top: 30px;
    margin-bottom: -40px;
}

.length-and-buttons .dt-buttons {
    margin-left: 20px; /* Ajusta el valor según sea necesario */
    margin-bottom: -10px;
}

/* Media Queries para hacer la interfaz responsiva */
@media (max-width: 768px) {
    .download-buttons {
        flex-direction: column;
        align-items: stretch;

    }

    .length-and-buttons {
        flex-direction: column;
        align-items: stretch;
        margin-top: -0px;
        margin-bottom: 30px;
    }

    .boton-enviar-nota,
    .boton-cancelar,
    .boton-cerrar-queja {
        width: 100%;
        margin-right: 0;
        margin-bottom: 10px; /* Espaciado entre botones */
    }
    .form-grupo .btn {
        width: 70%;
    }
    

}



.form-container-queja {
    position: relative; /* Añadido para que el botón de cierre se posicione correctamente */
}

.close-btn {
    position: absolute;
    top: 20px;
    right: 20px; /* Ajuste más flexible en el eje horizontal */
    background: red;
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    font-size: 16px; /* Reducir el tamaño de fuente en pantallas más pequeñas */
    line-height: 20px;
    text-align: center;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
@media (max-width: 768px) {
    .close-btn {
        top: 10px; /* Reducir el espacio superior en pantallas más pequeñas */
        right: 10px; /* Ajustar la distancia al borde derecho */
        width: 25px; /* Hacerlo más pequeño en pantallas pequeñas */
        height: 25px; /* Mantener proporción */
        font-size: 14px; /* Disminuir el tamaño de la fuente */
    }
}
.form-container-queja {
    display: none;
}
#anio {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #F8F9FA;
    border-radius: 10px;
    background-color: #F4F4F4;
    text-align: center;
    color: #333;
    width: 136px;
    margin-top: 10px; /* Espacio entre el label y el select */
    margin-bottom: -15px;
}

#anio:focus {
    outline: none;
    border-color: #F8F9FA;
}
option {
    padding: 5px;
}

label[for="anio"] {
    font-size: 15px;
    color: #333;
   margin-right: 20%;
}
.main-container {
    display: flex;
    flex-direction: column-reverse;
    align-items: center;
    width: 100%;
}
@media (max-width: 768px) {
    .download-buttons {
        flex-direction: column;
        align-items: stretch;
    }

    .length-and-buttons {
        flex-direction: column;
        align-items: stretch;
        margin-top: -0px;
        margin-bottom: 30px;
    }

    .boton-enviar-nota,
    .boton-cancelar,
    .boton-cerrar-queja {
        width: 100%;
        margin-right: 0;
        margin-bottom: 10px; /* Espaciado entre botones */
    }
    .form-grupo .btn {
        width: 70%;
    }


    /* Ajustes para el contenedor del formulario */
    .form-container-low {
        width: 100%; /* Asegura que ocupe el 100% del ancho de la pantalla */
    }

    /* Ajustes para el contenedor izquierdo */
    .left-container {
        display: flex;
        flex-direction: column;
        align-items: center; /* Centra el contenido horizontalmente */
        margin: 0 auto; /* Asegura que el contenedor esté centrado */
    }
    #anio {
        margin: 0 auto 10px; /* Centra el filtro y añade un pequeño margen inferior */
    }

    /* Ajustes para el contenedor de la gráfica */

    .legend-container {
        margin-top: 20px; /* Espacio entre el filtro y la leyenda */
        margin-bottom: 0; /* Elimina el margen inferior */
    }

    .legend-item {
        margin-bottom: 10px; /* Elimina el margen inferior de cada elemento de la leyenda */
    }
    label[for="anio"], #anio {
        display: block;
        margin: 0 auto 10px; /* Centra el elemento y añade un pequeño margen inferior */
        text-align: center; /* Centra el texto dentro del label */
    }
}
@media (max-width: 767.98px) {
    .form-group .d-flex {
        display: flex;
    align-items: center;
    }
    .form-group .icon-container {
        margin-bottom: 1px;
    }
    .form-group .form-controlqueja-li {
        width: 100%;
    }
}
@media (min-width: 768px) {
    .form-group .form-controlqueja-li {
        width: auto;
    }
}
.input-containerr {
    display: flex;
    align-items: center;
    width: 310px;
    height: 45px;
    border-radius: 10px;
    background-color: #fff;
    overflow: hidden;
    border: 1px solid #F4F4F4;
}

.icon-containerr {
    margin-left: 10px;
    margin-right: 0px;
    display: flex;
    align-items: center;

}

.file-input-label {
    padding: 10px;
    cursor: pointer;
    font-weight: normal;
    font-size: 13px;
    margin: 11px -2px;
    height: 45px;
    display: flex;
    align-items: center;
text-align: center;
}

.file-input {
    display: none;
}

.file-input-text {
    flex: 1;
    height: 45px;
    padding: 0 10px;
    background-color: #F4F4F4;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    font-size: 14px;
    border-left: 1px solid #ccc;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #6c757d;
    font-weight: normal;
}
.animated-alert-icon {
    color: #e74c3c; /* Color de alerta (rojo) */
    font-size: 1.3rem; /* Tamaño del ícono */
    margin-right: 5px; /* Espacio entre el ícono y el texto */
    animation: pulse 105s infinite; /* Animación pulsante */
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.3); /* Escalado más grande para mayor visibilidad */
    }
    100% {
        transform: scale(1);
    }
}


/* Estilos para la notificación de confirmación */
.confirmacion-queja {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #4CAF50; /* Color de fondo verde */
    color: #ffffff; /* Color del texto */
    padding: 20px 30px;
    border-radius: 12px;
    box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
    text-align: center;
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out, visibility 0s linear 0.5s;
}

/* Estilo y animación del checkmark */
.checkmark-circle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #ffffff;
    position: relative;
    margin: 0 auto 15px;
}

.checkmark-circle .background {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: #4CAF50;
    position: absolute;
    top: 0;
    left: 0;
    transform: scale(0);
    transition: transform 0.5s ease-in-out;
}

.checkmark-circle .checkmark {
    width: 28px;
    height: 14px;
    border-left: 4px solid #ffffff;
    border-bottom: 4px solid #ffffff;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-45deg) scale(0);
    transition: transform 0.5s ease-in-out 0.25s;
}

.confirmacion-queja.visible .background {
    transform: scale(1);
}

.confirmacion-queja.visible .checkmark {
    transform: translate(-50%, -50%) rotate(-45deg) scale(1);
}

/* Mostrar la notificación */
.confirmacion-queja.visible {
    opacity: 1;
    visibility: visible;
    transition: opacity 0.5s ease-in-out, transform 0.5s ease-in-out;
}
#acceptAnonWarningBtn {
    width: 160px;/* Aumenta el espacio interno del botón */
}
.custom-close-button {
    background-color: red;
    /* Fondo rojo */
    border-radius: 50%;
    /* Hacer el botón circular */
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    /* Color de la "X" que SweetAlert pone */
    font-size: 20px;
    font-weight: bold;
    line-height: 1;
    position: absolute;
    right: 10px;
    /* Ajustar la posición */
    top: 10px;
    /* Ajustar la posición */
    transition: background-color 0.3s ease, transform 0.3s ease;
    /* Transición suave */
}

/* Efecto hover en el fondo del botón */
.custom-close-button:hover {
    background-color: #eb636b;
    /* Cambiar el fondo a un rojo más oscuro al pasar el ratón */
    transform: scale(1.1);
    /* Aumentar ligeramente el tamaño del botón */
}





.swal2-image.zoomed {
    transform: scale(2);
    /* Ajusta el nivel de zoom según tus necesidades */
    z-index: 1000;
    /* Asegúrate de que la imagen ampliada esté encima de otros elementos */
}

.swal2-footer {
    display: flex;
    justify-content: space-between;
}

.swal2-footer button {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
}

.chat-container {
    max-height: 400px;
    overflow-y: scroll;
    padding: 10px;
}

.message {
    margin-bottom: 10px;
    display: flex;
}

.bubble {
    padding: 10px;
    border-radius: 10px;
}
.select-no-arrow {
    -webkit-appearance: none;
    /* Safari y Chrome */
    -moz-appearance: none;
    /* Firefox */
    appearance: none;
    /* Otros navegadores modernos */

    padding-right: 10px;
    /* Espacio para mantener el texto alineado */
}
.timestamp {
    font-size: 0.8em;
    text-align: right;
    color: #888;
}

  .preview-container {
      border: 1px solid #ccc;
      padding: 10px;
      border-radius: 5px;
      background-color: #F4F4F4;
  }

  .image-preview {
      margin-top: 10px;
      display: flex;
      flex-wrap: wrap;
      /* Permite múltiples imágenes en línea */
  }

  .image-preview img {
      width: 80px;
      /* Ajustar tamaño según sea necesario */
      height: 90px;
      object-fit: cover;
      margin: 5px;
      border-radius: 5px;
      border: 1px solid #F4F4F4;
      /* Color de borde */
      position: relative;
      /* Para posicionar el botón de eliminar */
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

  .upload-message {
      margin-top: 10px;
      color: green;
      /* Color de éxito */
  }

  .upload-message.error {
      color: red;
      /* Color de error */
  }

  .preview-title {
      font-family: 'Helvetica Neue', Arial, sans-serif;
      font-weight: 500;
      font-size: 17px;
      color: #333;
      margin-bottom: 10px;
      letter-spacing: 0.5px;
  }



   /* Contenedor principal */
   .form-container-low {
       display: flex;
       gap: 20px;
       align-items: flex-start;
       padding: 20px;
       background-color: #fff;
       border-radius: 10px;
       box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
   }

   /* Contenedor izquierdo */
   .left-container {
       display: flex;
       flex-direction: column;
       gap: 20px;
   }

   label {
       font-size: 16px;
       font-weight: bold;
   }

   select {
       padding: 8px;
       font-size: 14px;
       border: 1px solid #ddd;
       border-radius: 5px;
   }

   /* Contenedor de leyendas */
   .legend-container {
       display: flex;
       flex-direction: column;
       gap: 10px;
       padding: 10px;
       background-color: #fdfdfd;
       border: 1px solid #ddd;
       border-radius: 8px;
       box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
       max-width: 250px;
   }

   /* Elemento de leyenda */
   .legend-item {
       display: flex;
       align-items: center;
       gap: 10px;
   }

   .color-box {
       width: 20px;
       height: 20px;
       border-radius: 4px;
       border: 1px solid #ccc;
   }

   .legend-label {
        font-family: 'Roboto', sans-serif;
            /* Fuente moderna y clara */
            font-size: 12px;
            /* Tamaño de fuente pequeño y limpio */
            color: #333333;
            /* Color oscuro para buena legibilidad */
            letter-spacing: 0.5px;
            /* Espaciado suave entre las letras */
            font-weight: 400;
            /* Peso de fuente normal para mayor claridad */

            margin-bottom: 8px;
            /* Espaciado inferior para separar de otros elementos */
        text-align: left;
            /* Alineación a la izquierda */
            display: inline-block;
                /* Para alineación de elementos */
   }

   /* Contenedor del gráfico */
  .chart-container {
      flex: 1;
      display: flex;
      justify-content: center;
      height: 400px;
      /* Define una altura fija para la gráfica */
      width: 50%;
      align-items: center;
      margin-left: -100px;
      /* Mueve la gráfica hacia la izquierda */
  }

   canvas {
       width: 80% !important;
       height: 100% !important;
   }
   .strikethrough {
       text-decoration: line-through;
       color: #aaa;
   }
   .chartjs-tooltip {
       z-index: 10000;
       /* Asegura que los tooltips estén visibles encima de otros elementos */
   }

        
        .titulos {
            font-family: "Helvetica Neue LT Std", Arial, sans-serif;
            font-weight: 900;
            color: #333333;
            font-size: 30px;
            line-height: 24px;
            text-align: center; /* Centra el texto */
            grid-column: 2; /* Coloca el título en la columna central del grid */
            margin: 0 auto; /* Centra el texto horizontalmente dentro de la columna */
            transform: translateX(-35%); /* Mueve el título ligeramente hacia la izquierda */
        }

        .titulos_encabezado {
            font-family: "Helvetica Neue LT Std", Arial, sans-serif;
            font-weight: 400;
            font-size: 20px;
            line-height: 24px;
            margin-left: 25px;
        }
        .logo {
            width: 230px; /* Tamaño del logo en pantallas grandes */
            height: auto;
        }
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
         /* Estilos básicos */

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

}

.welcome-section .user-name {
    font-weight: bold;
    font-size: 14px;
    color: #33373b;
}




/* Modo responsive para pantallas pequeñas */
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


/* Estilo responsive para pantallas pequeñas */
@media (max-width: 768px) {
    .header-container {
        grid-template-columns: 1fr; /* Una sola columna en pantallas pequeñas */
        text-align: center; /* Centra todo en el eje horizontal */
    }
    body.menu-open .content {
        transform: translateX(250px);
    }
    body.menu-open .form-container-lower {
        transform: translateX(250px);
    }
    .titulos {
        font-size: 20px;
        transform: translateX(-15%); /* Elimina el desplazamiento lateral en pantallas pequeñas */
    }
    .menu-hamburguesa {
        justify-self: start; /* Alinea el menú al inicio */
    }
    .logo{
        flex: 0 0 auto; /* Mantén el logo en su lugar */
        height: 50px;
        width: 110px;
    }
   
}
@media (max-width: 375px) {
    .titulos {
        font-size: 20px;
        transform: translateX(-15%); /* Elimina el desplazamiento lateral en pantallas pequeñas */
    }
}
/* Contenedor principal */
.document-pdf-container {
    width: 509px;
    height: 500px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    background: linear-gradient(135deg, #f9f9f9, #ffffff);
    overflow: hidden;
}

/* Título estilizado */
.document-title {
    font-size: 1.8em;
    font-weight: bold;
    text-align: center;
    margin-bottom: 20px;
    color: #444;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Contenedor del iframe */
.document-content {
    position: relative;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 10px;
}

/* Estilo del iframe */
.document-frame {
    width: 100%;
    height: 800px;
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    background-color: #ffffff;
    transform-origin: top left;
}

/* Hover en desktop para mejorar la estética */
.document-frame:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Estilo para pantallas pequeñas */
@media (max-width: 768px) {
    .document-pdf-container {
        margin: 10px auto;
        padding: 15px;
        width: 290px;
        height: 350px;
    }

    .document-title {
        font-size: 1.5em;
        margin-bottom: 15px;
    }

    .document-frame {
        width: 500px;
        height: 800px;
        
    }
}
.video-preview-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
    margin: 20px auto;
    border: 2px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    width: 95%;
    height: 400px; /* Altura fija */
    overflow: hidden; /* Evita que el contenido desborde */
}

.video-preview-container h5.preview-title {
    margin-top: 20px;
    margin-bottom: -10px;
    font-size: 1.2rem;
    font-weight: bold;
    color: #333;
    text-align: center;
}

.video-preview-container video {
    max-width: 100%;
    height: 100%; /* Ajusta el video para que ocupe toda la altura del contenedor */
    object-fit: cover; /* Evita deformaciones */
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
#example tbody td {
    text-transform: uppercase;
}

/* Styles for the "Bienvenido" text */
.welcome-section p:first-of-type {
    font-size: 14px;

    color: #333;
    
}
body {
    padding-top: 70px; /* Ajusta este valor al alto del encabezado */
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
        .submenu {
    display: none;
    list-style: none;
    padding-left: 20px;
}

.submenu li {
    padding: 5px 0;
}
 /* Línea de separación */
 .division {
            width: 95%;
            margin: 0 auto;
            margin-top: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
        }
        .evidencias-container {
    display: flex;
    justify-content: center; /* Centra las imágenes horizontalmente */
    align-items: center; /* Alinea verticalmente */
    gap: 10px;
    overflow-x: auto;
    white-space: nowrap;
    padding: 10px; /* Añade un poco de espacio alrededor */
}

.evidencias-container {
    display: flex;
    justify-content: center; /* Centra las imágenes horizontalmente */
    align-items: center; /* Alinea verticalmente */
    gap: 10px;
    overflow-x: auto; /* Permite desplazamiento horizontal */
    white-space: nowrap;
    padding: 10px;
    scrollbar-width: none; /* Oculta el scroll en Firefox */
    -ms-overflow-style: none; /* Oculta el scroll en Edge e IE */
}

.evidencias-container::-webkit-scrollbar {
    display: none; /* Oculta el scroll en Chrome, Safari y Opera */
}

.evidencia-img {
    width: 100%; /* Se adapta al contenedor */
    max-width: 391.325px; /* Tamaño máximo */
    height: 294px;
    object-fit: cover;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.evidencia-img:hover {
    transform: scale(1.02); /* Pequeño zoom */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); /* Sombra más pronunciada */
}

/* Responsivo para pantallas más pequeñas */
@media (max-width: 768px) {
    .evidencias-container {
        justify-content: flex-start; /* Alinea imágenes a la izquierda */
        overflow-x: scroll; /* Asegura desplazamiento en móviles */
    }

    .evidencia-img {
        
        width: 250px;
        height: 160px;
    }
}





.evidencia-pdf {
    width: 100px;
    height: 100px;
    border: none;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}
/* Contenedor del video */
.video-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center; /* Centra todos los elementos */
    text-align: center;
}

/* Video */
.video-container {
    display: flex;
    justify-content: center; /* Centra el video */
    align-items: center; /* Centra el video verticalmente */
    width: 100%;
    max-width: 400px;
    height: 400px;
    margin: 0 auto;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);

}

/* Ajuste del video */
.video-container video {
    width: 600px;
    height: 400px;
    max-height: 100%;
    border-radius: 12px;
}

/* Etiqueta */
.video-label {
    font-weight: bold;
    margin-bottom: 8px;
    display: block;
    text-align: center;
}
/* Media Query para pantallas pequeñas (por ejemplo, móviles) */
@media (max-width: 767px) {
    .video-container {
        max-width: 100%; /* Permite que el video ocupe el 100% del ancho de la pantalla */
        height: auto; /* Ajusta la altura según el contenido */
    }

    .video-container video {
        width: 100%;   /* Hace que el video ocupe todo el ancho disponible */
        height: 200px;  /* Mantiene las proporciones originales del video */
        max-width: 100%; /* Evita que el video se haga más grande que el contenedor */
    }
}


.proceso-wrapper {
    max-width: 600px; /* Limita el ancho máximo */
    width: 100%;
    margin: 20px auto;
    text-align: center;
}

.ficha-tecnica {
    width: 100%;
    max-width: 500px; /* Tamaño uniforme */
    height: 500px;
}

textarea {
    resize: none; /* Evita que el usuario cambie el tamaño */
}
.ficha-tecnica {
    display: block; /* Para que respete el centrado */
    margin: 0 auto; /* Centrado horizontal */
    width: 100%;
    max-width: 500px;
    height: 500px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}
.proceso-wrapper a.btn {
    display: inline-block;
    margin: 10px auto;
}


.check-icon {
    width: 24px;
    height: 24px;
    font-size: 1rem;
}



    </style>
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
                        Compras
                    </a>
                </li>

                @auth
                    <!-- Guías -->
                    <li>
                        <a href="#" onclick="toggleSubmenu(event, 'submenu-guias')">
                            <img src="{{ asset('images/fedex.png') }}" alt="Icono de Guías" class="menu-icon-image">
                            Guias
                        </a>
                        <ul id="submenu-guias" class="submenu">
                            <li><a href="{{ route('guias.create') }}">+ Crear Guía</a></li>
                            <li><a href="{{ route('entrega.create') }}">Entregar Guía</a></li>
                            <li><a href="{{ route('entregas.index') }}">Ver Entregas</a></li>
                        </ul>
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
                            <img src="{{ asset('images/material.png') }}" alt="Icono de Pedidos" class="menu-icon-image">
                            Pedidos
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

        <div id="menu-overlay" class="menu-overlay" onclick="closeMenu()"></div>
    </div>

    <h1 class="titulos">@yield('titulo', 'Mantenimiento Int')</h1>
    <div class="gradient-bg-animation"></div>
</div>


        <!-- Contenido Principal -->
        <div class="content">
            <!-- Formulario -->
            <div class="form-container-queja">
            <form action="{{ route('inventario') }}" method="get">
                    @csrf
                    <div class="titulo-seccion">Informacion del Equipo</div>
                    <div class="form-fila">
                        <!-- Tipo de Equipo -->
                        
    <!-- Primera fila con cuatro campos -->
    <div class="form-row">
    <div class="form-group col-md-3">
        <label for="Tipo_de_equipo">Tipo de Equipo</label>
        <div class="form-group w-100 d-flex align-items-center">
            <div class="icon-container">
                <img src="{{ asset('images/tipo.png') }}" alt="Tipo de Equipo icon" class="icon">
            </div>
            <input type="text" id="Tipo_de_equipo" name="Tipo_de_equipo"
                class="form-controln form-controlqueja-la iconized" readonly>
        </div>
    </div>

    <div class="form-group col-md-3">
        <label for="Subtipo">Subtipo de Equipo</label>
        <div class="form-group w-100 d-flex align-items-center">
            <div class="icon-container">
                <img src="{{ asset('images/producto.png') }}" alt="Subtipo de Equipo icon" class="icon">
            </div>
            <input type="text" id="Subtipo" name="Subtipo"
                class="form-controln form-controlqueja-lb iconized" readonly>
        </div>
    </div>

    <div class="form-group col-md-3">
        <label for="Serie">Serie</label>
        <div class="form-group w-100 d-flex align-items-center">
            <div class="icon-container">
                <img src="{{ asset('images/serie.png') }}" alt="Número de serie icon" class="icon">
            </div>
            <input type="text" id="Serie" name="Serie"
                class="form-controln form-controlqueja-lc iconized" readonly>
        </div>
    </div>

    <div class="form-group col-md-3">
        <label for="Marca">Marca</label>
        <div class="form-group w-100 d-flex align-items-center">
            <div class="icon-container">
                <img src="{{ asset('images/marca.png') }}" alt="Marca icon" class="icon">
            </div>
            <input type="text" id="Marca" name="Marca"
                class="form-controln form-controlqueja-ld iconized" readonly>
        </div>
    </div>
</div>

<!-- Segunda fila -->
<div class="form-row">
    <div class="form-group col-md-3">
        <label for="Modelo">Modelo</label>
        <div class="form-group w-100 d-flex align-items-center">
            <div class="icon-container">
                <img src="{{ asset('images/modelo.png') }}" alt="Modelo icon" class="icon">
            </div>
            <input type="text" id="Modelo" name="Modelo"
                class="form-controln form-controlqueja-le iconized" readonly>
        </div>
    </div>

    <div class="form-group col-md-3">
        <label for="EstadoActual">Estado Terminado</label>
        <div class="form-group w-100 d-flex align-items-center">
            <div class="icon-container">
                <img src="{{ asset('images/modelo.png') }}" alt="Estado icon" class="icon">
            </div>
            <input type="text" id="estado_proceso" name="estado_proceso"
                class="form-controln form-controlqueja-le iconized" readonly>
        </div>
    </div>

    <div class="form-group col-md-3">
        <label for="Fecha_adquisición">Fecha de Adquisición</label>
        <div class="form-group w-100 d-flex align-items-center">
            <div class="icon-container">
                <img src="{{ asset('images/modelo.png') }}" alt="Fecha de adquisición icon" class="icon">
            </div>
            <input type="text" id="Fecha_adquisicion" name="Fecha_adquisicion"
                class="form-controln form-controlqueja-le iconized" readonly>
        </div>
    </div>

   
    
    <!-- Tercera fila -->
    <div class="form-group col-md-3">
        <label for="Año">Año</label>
        <div class="form-group w-100">
            <input type="text" id="Año" name="Año" class="form-controln form-controlqueja-lf" readonly>
        </div>
    </div>
</div>
</div>
    <div class="form-row">
        
    <div class="form-group col-md-3">
        <label for="photo">Evidencia</label>
        <div class="form-group w-100 d-flex align-items-center">
            <div class="icon-container">
                <img src="{{ asset('images/imagen.jpeg') }}" alt="Foto del equipo" class="icon-pin">
            </div>
            <button id="viewFilesButton" class="form-controln form-controlqueja-li iconized flex-grow-1" type="button">
                Ver archivos
            </button>
        </div>
    </div>
</div>

<!-- Contenedor de imágenes -->
<div id="fileContainer" style="display: none;">
    @if(!empty($photos) && is_array($photos))
        @foreach($photos as $photo)
            <img class="evidence-image" src="{{ asset($photo) }}" alt="Evidencia" style="max-width: 1%; margin: -10px;">
        @endforeach
    @else
        <p>No hay evidencia disponible</p>
    @endif
</div>




    


<div class="row">
    <!-- Mensaje -->
    <div class="form-grupo col-12 col-md-6 px-3">
        <div class="label-container">
            <label for="message">Descripción</label>
            <div class="d-flex align-items-center">
                <textarea id="message" name="message" rows="4" class="form-controln form-controlqueja-lj w-100" readonly></textarea>
            </div>
        </div>
    </div>
 


    <!-- Observaciones -->
    <div class="form-grupo col-12 col-md-6 px-3">
        <div class="label-container">
            <label for="observaciones">Observaciones</label>
            <div class="d-flex align-items-center">
                <textarea id="observaciones" name="observaciones" rows="4" class="form-controln form-controlqueja-lj w-100" readonly></textarea>
            </div>
        </div>
    </div>
    <div class="container">
    <div class="row align-items-center">
        <!-- Columna del video -->
        <div class="col-md-6">
            <div id="videoContainer" class="video-preview-container">
                <!-- Aquí se inyectará el video con JavaScript -->
            </div>
        </div>

        <!-- Columna de la firma digital -->
        <div class="col-md-6 text-center">
            <label for="firma" class="form-label">Firma Digital</label><br>
            <img id="firmaDigitalImagen" src="" alt="Firma Digital" style="max-width: 100%; height: auto; display: none;" class="img-fluid border border-dark rounded shadow">

            <!-- Nombre del firmante -->
            <div id="firmaUsuarioNombre" style="display: none; margin-top: 10px;">
                <strong>Firmado por:</strong> <br> <span id="nombreUsuarioTexto" class="text-primary fst-italic" style="font-style: italic; color: #0d6efd;"></span>
            </div>
        </div>
    </div>
</div>




</div>
<div class="division"></div>
<div class="titulo-seccion">Reportes</div>
<div id="procesosContainer"></div>



                    <!--  
                    <div class="form-grupo btnqueja-container">
                        <button type="reset" class="btn btnqueja-danger" data-toggle="modal" data-target="#addNoteModa2">
                            <img src="{{ asset('images/borrar.png') }}" alt="Borrar icon" class="icono-queja">
                            Borrar
                        </button>
                        <button type="button" class="btn btnqueja-success" data-toggle="modal" data-target="#addNoteModal">
                            <img src="{{ asset('images/editar.png') }}" alt="Enviar icon" class="icono-queja">
                            Modificar
                        </button>-->

                    </div>

                </form>
            </div>
        </div>
      
        <div class="form-container-lower">
    <div class="titulo-seccion">Lista de productos</div>
    <table id="example" class="stripe row-border order-column" style="width:100%">
        <thead>
            <tr>
                <th>Estado terminado</th>
                <th>Tipo de Producto</th>
                <th>Subtipo de Producto</th>
                <th>Número de serie</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Fecha de creación</th> <!-- Nueva columna -->
                <th>Detalles</th>
            </tr>
        </thead>
        <tbody>
            @foreach($servicios as $servicio) <!-- Cambié productos por servicios -->
                <tr>
                    <td>
                        @if($isPdfExport ?? false)
                            {{-- Texto plano para PDF --}}
                            @if($servicio->estado_proceso)
                                {{ ucfirst($servicio->estado_proceso) }}
                            @else
                                Registro
                            @endif
                        @else
                            {{-- Etiquetas HTML para la vista web --}}
                            @if($servicio->estado_proceso)
                                <span class="badge 
                                    @switch($servicio->estado_proceso)
                                        @case('registro') badge-info @break
                                        @case('btn-salida-mantenimiento') badge-primary @break
                                        @case('salida') badge-warning @break
                                        @case('regreso') badge-success @break
                                        @case('defectuoso') badge-secondary @break
                                        @case('btn-salida-dueno') badge-danger @break
                                        @default badge-danger @break
                                    @endswitch
                                ">
                                    {{ ucfirst($servicio->estado_proceso) }}
                                </span>
                            @else
                                <span class="badge badge-info">Registro</span>
                            @endif
                        @endif
                    </td>
                    <td>{{ $servicio->tipo_equipo }}</td> <!-- Cambié producto->tipo_equipo por servicio->tipo_equipo -->
                    <td>{{ $servicio->subtipo_equipo ?? 'N/A' }}</td> <!-- Cambié producto->subtipo_equipo por servicio->subtipo_equipo -->
                    <td>{{ $servicio->numero_serie }}</td> <!-- Cambié producto->numero_serie por servicio->numero_serie -->
                    <td>{{ $servicio->marca }}</td> <!-- Cambié producto->marca por servicio->marca -->
                    <td>{{ $servicio->modelo }}</td> <!-- Cambié producto->modelo por servicio->modelo -->
                    <td>{{ $servicio->created_at->format('d/m/Y H:i') }}</td> <!-- Mostrar la fecha de creación -->
                    <td>
                        <button class="btn btn-info btn-detalles" data-id="{{ $servicio->id }}" data-estado="{{ $servicio->estado_proceso }}">
                            <i class="fa fa-eye"></i>
                        </button>

                      
    <button class="btn btn-info btn-salida-mantenimiento" id="btn-salida-mantenimiento" data-id="{{ $servicio->id }}" data-estado="{{ $servicio->estado_proceso }}">
        <i class="fa fa-sign-out-alt"></i> 
    </button>

    <button class="btn btn-info btn-entrada-mantenimiento" id="btn-entrada-mantenimiento" data-id="{{ $servicio->id }}" data-estado="{{ $servicio->estado_proceso }}">
        <i class="fa fa-sign-in-alt"></i>
    </button>
    

    <button class="btn btn-info btn-salida-dueno" id="btn-salida-dueno" data-id="{{ $servicio->id }}" data-estado="{{ $servicio->estado_proceso }}">
        <i class="fa fa-user-check"></i> 
    </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Estado terminado</th>
                <th>Tipo de Producto</th>
                <th>Subtipo de Producto</th>
                <th>Número de serie</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Fecha de creación</th> <!-- Nueva columna -->
                <th>Detalles</th>
            </tr>
        </tfoot>
    </table>
</div>
<script>
function actualizarProcesosPendientes(id) {
    $.ajax({
        url: `/procesos/${id}/pendientes`,
        type: 'GET',
        success: function(procesosPendientes) {
            console.log(procesosPendientes);

            // Ocultar todos los botones primero
            $(".btn-proceso").hide();

            // Mostrar solo los botones correspondientes a los procesos pendientes
            procesosPendientes.forEach(proceso => {
                $(`#btn-${proceso}`).show();
            });
        },
        error: function() {
            console.error("Error al obtener los procesos pendientes.");
        }
    });
}

// Salida a mantenimiento
$(document).on('click', '.btn-salida-mantenimiento', function () {
    var id = $(this).data('id');
    window.location.href = "/movimientos/" + "salida-mantenimiento/" + id;
});

// Entrada de mantenimiento
$(document).on('click', '.btn-entrada-mantenimiento', function () {
    var id = $(this).data('id');
    window.location.href = "/movimientos/" + "entrada-mantenimiento/" + id;
});

// Salida a dueño
$(document).on('click', '.btn-salida-dueno', function () {
    var id = $(this).data('id');
    window.location.href = "/movimientos/" + "salida-dueno/" + id;
});
// Ejecutar la función de actualización al cargar
$(document).ready(function() {
    var id = $('#registro-id').val();
    if (id) {
        actualizarProcesosPendientes(id);
    } else {
        console.error("No se encontró el ID del registro.");
    }
});
</script>



<script>
$(document).ready(function () {
    $('.btn-salida-mantenimiento, .btn-entrada-mantenimiento, .btn-salida-dueno').on('click', function (e) {
        const estadoActual = $(this).data('estado');
        const boton = $(this);

        const ordenProcesos = ['salida', 'regreso', 'entregado'];

        if (estadoActual === 'defectuoso') {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Equipo marcado como defectuoso',
                text: 'No se puede realizar ningún proceso adicional.',
                confirmButtonColor: '#d33',
            });
            return false;
        }

        const tipoProceso = boton.hasClass('btn-salida-mantenimiento') ? 'salida'
                          : boton.hasClass('btn-entrada-mantenimiento') ? 'regreso'
                          : boton.hasClass('btn-salida-dueno') ? 'entregado'
                          : null;

        const indiceEstado = ordenProcesos.indexOf(estadoActual);
        const indiceBoton = ordenProcesos.indexOf(tipoProceso);

        if (indiceBoton > indiceEstado + 1) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Proceso incompleto',
                text: `Primero debes completar el proceso de "${ordenProcesos[indiceEstado + 1]}".`,
                confirmButtonColor: '#3085d6',
            });
            return false;
        }

        if (indiceBoton < indiceEstado) {
            e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Proceso ya avanzado',
                text: `Este equipo ya está en el proceso de "${estadoActual}", no puedes regresar a "${tipoProceso}".`,
                confirmButtonColor: '#3085d6',
            });
            return false;
        }
    });
});

</script>





<script>

$(document).ready(function () {
    // Inicializar contenedores y eventos
    function initFormContainers() {
        $('.form-container-queja').hide();

        if (!$('.form-container-queja .close-btn').length) {
            $('.form-container-queja').prepend('<button class="close-btn">&times;</button>');
        }

        $('.form-container-queja').on('click', '.close-btn', function () {
            $('.form-container-queja').slideUp();
        });
    }

    function handleViewDetailsClick() {
        $('#example tbody').on('click', 'button', function () {
            const productId = $(this).data('id');
            $('.form-container-queja').slideDown().attr('data-product-id', productId);
            $('.form-container-lower').slideDown();

            $('html, body').animate({
                scrollTop: $('.form-container-lower').offset().top
            }, 1000);
        });
    }

    function initDataTable() {
        $('#example tfoot th').each(function (i) {
            const title = $('#example thead th').eq($(this).index()).text();
            $(this).html(`<input type="text" placeholder="${title}" data-index="${i}" />`);
        });

        const table = $('#example').DataTable({
            language: {
                sProcessing: "Procesando...",
                sLengthMenu: "Mostrar _MENU_ registros",
                sZeroRecords: "No se encontraron resultados",
                sEmptyTable: "Ningún dato disponible en esta tabla",
                sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                sSearch: "Buscar:",
                oPaginate: {
                    sFirst: "Primero",
                    sLast: "Último",
                    sNext: "Siguiente",
                    sPrevious: "Anterior"
                },
                oAria: {
                    sSortAscending: ": Activar para ordenar la columna de manera ascendente",
                    sSortDescending: ": Activar para ordenar la columna de manera descendente"
                }
            },
            dom: '<"length-and-buttons"lB>frtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: `<span class="button-icon">
         <img src="{{ asset('images/excel.png') }}" alt="Excel" height="20">
       </span>
       <span class="button-text">Excel</span>`,

                    className: 'btn-excel',
                    title: 'Mantenimiento Externo Grupo MediBuy',
                    exportOptions: {
                        columns: ':not(:last-child)',
                        format: {
                            body: function (data) {
                                return data.toString().toUpperCase();
                            }
                        }
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: '<span class="button-icon"><img src="{{ asset('images/csv.png') }}" alt="Csv" height="20"></span><span class="button-text">CSV</span>',
                    className: 'btn-csv',
                    title: 'Mantenimiento Externo Grupo MediBuy'
                },
                {
                    extend: 'pdfHtml5',
text: '<span class="button-icon"><img src="{{ asset('images/pdf.png') }}" alt="Pdf" height="20"></span><span class="button-text">PDF</span>',
className: 'btn-pdf',
title: 'Mantenimiento Externo Grupo MediBuy',
exportOptions: {
    columns: ':not(:last-child)', // Excluye la última columna (botón de detalles)
    format: {
        body: function (data, row, column, node) {
            // Convierte el contenido HTML a texto plano, pero deja las etiquetas intactas
            let plainText = $('<div>').html(data).text().trim(); // Obtén solo texto
            return plainText.toUpperCase(); // Devuelve en mayúsculas
        }
    }
},
customize: function (doc) {
    // Opcional: Personaliza el diseño del PDF
    doc.styles.tableBodyEven.alignment = 'center';
    doc.styles.tableBodyOdd.alignment = 'center';
    doc.styles.tableHeader.alignment = 'center';
}
}


            ],
            columnDefs: [
                {
                    targets: 0, // Define la columna con las etiquetas HTML
                    render: function (data, type, row) {
                        // Renderizar las etiquetas como HTML, sin convertirlas a texto plano
                        return $('<div>').html(data).text() === data ? data : data;
                    }
                }
            ]
        });

       // Aplicar estilos a las filas después de cada redibujo de la tabla (filtro, orden, etc.)
       table.on('draw', function () {
            applyRowStyling();
        });

        $(table.table().container()).on('keyup', 'tfoot input', function () {
            table
                .column($(this).data('index'))
                .search(this.value)
                .draw();
        });
    }
    function applyRowStyling() { 
    $('#example tbody tr').each(function () {
        const status = $(this).find('td').eq(0).find('span').text().trim();

        if (status === 'Regreso') {
            $(this).addClass('row-success'); // Verde
        } else if (status === 'Entregado') {
            $(this).addClass('row-danger'); // Rojo
        } else if (status === 'Salida') {
            $(this).addClass('row-warning'); // Amarillo
        } else if (status === 'Registro') {
            $(this).addClass('row-info'); // Azul
        } else if (status === 'Hojalateria') {
            $(this).addClass('row-info'); // Azul más fuerte
        } else if (status === 'Defectuoso') {
            $(this).addClass('row-defectuoso'); // Gris
        }
    });
}



    initFormContainers();
    handleViewDetailsClick();
    initDataTable();
    applyRowStyling();
});
</script>
<script>
$(document).ready(function () {
    $(document).on('click', '.btn-info', function () {
        var id = $(this).data('id');

        if (!id) {
            alert('No se encontró un ID válido.');
            return;
        }

        $('#loadingIndicator').show();

        $.ajax({
            url: '/detalles/' + id,
            method: 'GET',
            success: function (response) {
                $('#loadingIndicator').hide();

                if (response.error) {
                    alert(response.error);
                    return;
                }

                // Información general del servicio
                $('#Tipo_de_equipo').val(response.tipo_equipo || '');
                $('#Subtipo').val(response.subtipo_equipo || '');
                $('#Serie').val(response.numero_serie || '');
                $('#Marca').val(response.marca || '');
                $('#Modelo').val(response.modelo || '');
                $('#Año').val(response.anio || '');
                $('#estado_proceso').val(response.estado_proceso || '');
                $('#Fecha_adquisicion').val(response.fecha_adquisicion || '');
                $('#message').val(response.descripcion || '');
                $('#observaciones').val(response.observaciones || '');

                // Documento PDF
                $('#documentoPDF').next('iframe').remove();
                if (response.firma_digital) {
    // Asegúrate de que la ruta sea la correcta
    let firmaUrl = response.firma_digital;
    $('#firmaDigitalImagen').attr('src', firmaUrl).show();

    // Mostrar nombre del usuario si existe
    if (response.user_name) {
        $('#nombreUsuarioTexto').text(response.user_name);
        $('#firmaUsuarioNombre').show();
    } else {
        $('#firmaUsuarioNombre').hide();
    }
} else {
    $('#firmaDigitalImagen').hide();
    $('#firmaUsuarioNombre').hide();
}



                if (response.documentoPDF) {
                    const pdfPath = response.documentoPDF.startsWith('/storage/') ? response.documentoPDF : '/storage/' + response.documentoPDF;

                    if (/iPhone|iPad|iPod/.test(navigator.userAgent)) {
                        $('#documentoPDF').hide();
                        window.open(pdfPath, '_blank');
                    } else if (/Android/.test(navigator.userAgent)) {
                        $('#documentoPDF').attr('src', 'https://docs.google.com/gview?embedded=true&url=' + encodeURIComponent(pdfPath)).show();
                    } else {
                        $('#documentoPDF').attr('src', pdfPath).show();
                    }

                    $('#downloadPDF').attr('href', pdfPath).show();
                } else {
                    $('#documentoPDF').attr('src', '').hide();
                }

            // Evidencias generales
$('#fileContainer').empty();

let evidencias = [response.evidencia1, response.evidencia2, response.evidencia3].filter(Boolean);
if (evidencias.length > 0) {
                    evidencias.forEach(function (photo) {
                        const photoUrl = photo.startsWith('/storage/') || photo.startsWith('http') ? photo : `/storage/${photo}`;
                        $('#fileContainer').append(`<img class="evidence-image" src="${photoUrl}" alt="Evidencia de queja" style="max-width: 100%; display: none;">`);
                    });
                    $('#fileContainer').show();
                } else {
                    $('#fileContainer').hide();
                }



// Video general
if (response.video) {
                    const videoUrl = response.video.startsWith('/storage/') || response.video.startsWith('http') ? response.video : `/storage/${response.video}`;
                    $('#videoContainer').html(`
                        <h5 class="preview-title">Previsualización del Video:</h5>
                        <video controls class="video-preview" style="max-width: 100%; margin: 10px;">
                            <source src="${videoUrl}" type="video/mp4">
                            Tu navegador no soporta la reproducción de videos.
                        </video>
                    `).show();
                } else {
                    $('#videoContainer').html('<p>No hay video disponible</p>').show();
                }

                // Movimientos
             // Movimientos
$('#procesosContainer').empty();
if (response.movimientos && response.movimientos.length > 0) {
    // Ordena los movimientos por tipo: primero salida, luego entrada, y finalmente entrega
    response.movimientos.sort((a, b) => {
        const order = ['salida', 'entrada', 'entrega'];
        return order.indexOf(a.tipo_movimiento) - order.indexOf(b.tipo_movimiento);
    });

    response.movimientos.forEach(mov => {
    let tipoBadge = '';
    switch (mov.tipo_movimiento) {
        case 'salida_mantenimiento':
            tipoBadge = `
                <div class="reporte-separador bg-danger-subtle text-danger border-start border-4 border-danger mb-4 p-3 rounded">
                    🚚 <strong>Reporte de Salida a Mantenimiento</strong>
                </div>`;
            break;
        case 'entrada_mantenimiento':
            tipoBadge = `
                <div class="reporte-separador bg-success-subtle text-success border-start border-4 border-success mb-4 p-3 rounded">
                    📦 <strong>Reporte de Regreso a Planta</strong>
                </div>`;
            break;
        case 'salida_dueno':
            tipoBadge = `
                <div class="reporte-separador bg-primary-subtle text-primary border-start border-4 border-primary mb-4 p-3 rounded">
                    📤 <strong>Reporte de Entrega a Destinatario</strong>
                </div>`;
            break;
        default:
            tipoBadge = `
                <div class="reporte-separador bg-secondary-subtle text-dark border-start border-4 border-dark mb-4 p-3 rounded">
                    🔄 <strong>Otro Movimiento</strong>
                </div>`;
    }




        // Aquí puedes agregar el código para renderizar el tipoBadge
    


 
let movHTML = `
    <div class="col-12 d-flex justify-content-center align-items-center">
        <div class="proceso-wrapper text-center">
            ${tipoBadge}
            <label for="descripcion_movimiento_${mov.id}" class="video-label">
                Descripción del Movimiento (ID: ${mov.id}):
            </label>
            <div class="d-flex justify-content-center">
                <textarea id="descripcion_movimiento_${mov.id}" rows="4" class="form-controln form-controlqueja-lj w-100 text-center" readonly>${mov.descripcion}</textarea>
            </div>
        </div>
    </div>
`;
        // Evidencias del movimiento
        let evidenciasMov = [mov.evidencia1, mov.evidencia2, mov.evidencia3].filter(Boolean);
if (evidenciasMov.length > 0) {
    movHTML += `<div class="evidencias-container mt-3">`;
    evidenciasMov.forEach(ev => {
        // Verifica si 'ev' es una URL completa (contiene 'http')
        const evUrl = ev.startsWith('http') ? ev : '/storage/' + ev;
        movHTML += evUrl.match(/\.(jpg|jpeg|png|gif)$/i)
            ? `<img src="${evUrl}" alt="Evidencia" class="evidencia-img">`
            : `<iframe src="${evUrl}" class="evidencia-pdf"></iframe>`;
    });
    movHTML += `</div>`;
}


        // Video del movimiento 
        if (mov.video) { 
    // Verifica si 'mov.video' es una URL completa (contiene 'http')
    const videoMovUrl = mov.video.startsWith('http') ? mov.video : '/storage/' + mov.video;
    const shareVideoUrl = encodeURIComponent(window.location.origin + videoMovUrl);

    movHTML += `
        <div class="col-12 d-flex justify-content-center align-items-center mt-3">
            <div class="video-wrapper" style="max-width: 640px; width: 100%;">
                <label class="video-label">Video del Movimiento (ID: ${mov.id}):</label>
                <div class="video-container position-relative video-menu-wrapper">
                    <video controls class="mov-video" style="width: 100%; border-radius: 8px;">
                        <source src="${videoMovUrl}" type="video/mp4">
                        Tu navegador no soporta el formato de video.
                    </video>

                    <!-- Botón de menú (3 puntos) -->
                    <div class="menu-toggle position-absolute top-0 end-0 m-2" style="z-index: 10; cursor: pointer;">
                        <img src="https://img.icons8.com/material-outlined/24/000000/more.png" alt="Más opciones" style="width: 30px; height: 30px;">
                    </div>

                    <!-- Menú de opciones -->
                    <div class="menu-options position-absolute top-0 end-0 mt-5 me-2 p-2 bg-white shadow rounded border"
                        style="display: none; z-index: 20; min-width: 160px;">
                        <a href="https://wa.me/?text=${shareVideoUrl}" target="_blank" 
                        class="d-block mb-1 text-decoration-none text-dark">
                            📤 Compartir por WhatsApp
                        </a>
                        <a href="${videoMovUrl}" download 
                        class="d-block text-decoration-none text-dark">
                            ⬇️ Descargar video
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Checklist del movimiento (como array JSON) 
console.log('📦 Valor original de mov.checklist:', mov.checklist);

if (mov.checklist) {
    let itemsChecklist = [];

    try {
        if (typeof mov.checklist === 'string') {
            console.log('🧪 Checklist es un string, intentando parsear...');
            itemsChecklist = JSON.parse(mov.checklist);
        } else {
            console.log('✅ Checklist ya es un array:', mov.checklist);
            itemsChecklist = mov.checklist;
        }
    } catch (e) {
        console.error('❌ Error al parsear el checklist:', e);
        itemsChecklist = [];
    }

    console.log('🔍 Resultado después del parseo:', itemsChecklist);
    console.log('🧾 Es un array:', Array.isArray(itemsChecklist));
    console.log('📏 Longitud del array:', itemsChecklist.length);

    if (Array.isArray(itemsChecklist) && itemsChecklist.length > 0) {
        console.log('✅ El checklist tiene elementos, procediendo a renderizar.');

        const decodedChecklist = itemsChecklist.map((item, index) => {
            console.log(`📝 Item [${index}]:`, item);
            return `
                <li class="list-group-item d-flex align-items-center gap-3 py-3">
                    <span class="check-icon bg-success-subtle text-success-emphasis rounded-circle d-flex justify-content-center align-items-center">
                        <i class="bi bi-check-lg"></i>
                    </span>
                    <span class="text-body">${item}</span>
                </li>
            `;
        }).join('');

        movHTML += `
            <div class="col-12 d-flex justify-content-center align-items-center mt-4">
                <div class="w-100" style="max-width: 600px;">
                    <label class="form-label fw-semibold text-dark fs-5 mb-3">
                        Checklist del Movimiento (ID: ${mov.id})
                    </label>
                    <ul class="list-group list-group-flush shadow-sm rounded-3 border">
                        ${decodedChecklist}
                    </ul>
                </div>
            </div>
        `;
    } else {
        console.warn('⚠️ El checklist está vacío o no es un array válido.');
    }
} else {
    console.warn('⚠️ No se encontró el campo mov.checklist o está vacío.');
}





        $('#procesosContainer').append(movHTML);
    });

    $('#procesosContainer').show();
} else {
    $('#procesosContainer').hide();
}

            },
            error: function (xhr, status, error) {
                $('#loadingIndicator').hide();
                console.error('Error al obtener detalles:', error);
                alert('Ocurrió un error al obtener los detalles del equipo.');
            }
        });
    });
});
</script>

<script>
document.addEventListener('click', function(e) {
    // Mostrar u ocultar el menú si se hace clic en el botón de opciones
    if (e.target.closest('.menu-toggle')) {
        const wrapper = e.target.closest('.video-menu-wrapper');
        const menu = wrapper.querySelector('.menu-options');
        const isVisible = menu.style.display === 'block';
        
        // Cerrar todos los menús abiertos
        document.querySelectorAll('.menu-options').forEach(m => m.style.display = 'none');
        
        // Alternar visibilidad del actual
        if (!isVisible) {
            menu.style.display = 'block';
        }

        e.stopPropagation();
    } else {
        // Si haces clic fuera, oculta todos los menús
        document.querySelectorAll('.menu-options').forEach(m => m.style.display = 'none');
    }
});

// Ocultar menú al reproducir video
document.addEventListener('play', function(e) {
    if (e.target.classList.contains('mov-video')) {
        const wrapper = e.target.closest('.video-menu-wrapper');
        const menu = wrapper.querySelector('.menu-options');
        if (menu) menu.style.display = 'none';
    }
}, true); // Usa `true` para capturar en fase de captura
</script>


     

 
<script>
document.getElementById('viewFilesButton').addEventListener('click', function () {
    // Verificar si el contenedor tiene imágenes cargadas
    setTimeout(() => {
        const imageElements = document.querySelectorAll('#fileContainer img');
        const images = Array.from(imageElements).map(img => img.getAttribute('src'));

        if (images.length > 0) {
            let currentIndex = 0;
            let touchStartX = 0;
            let touchEndX = 0;

            function showImage(index) {
                Swal.fire({
                    html: `
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <img src="${images[index]}" 
                                style="width: 400px; height: 300px; object-fit: contain; border-radius: 8px; transition: transform 0.3s;" id="swalImage">
                            <div style="display: flex; gap: 5px; margin-top: 10px;">
                                ${images.map((_, i) => `
                                    <span style="width: 8px; height: 8px; background-color: ${i === index ? '#333' : '#ddd'}; border-radius: 50%;"></span>
                                `).join('')}
                            </div>
                        </div>
                    `,
                    width: 'auto',
                    padding: '15px',
                    background: '#f9f9f9',
                    showConfirmButton: false,
                    showCloseButton: true,
                    customClass: {
                        popup: 'compact-popup',
                        closeButton: 'custom-close-button'
                    },
                    backdrop: 'rgba(0, 0, 0, 0.5)',
                    willOpen: (popup) => {
                        const image = popup.querySelector('img');
                        image.addEventListener('click', nextImage);

                        popup.addEventListener('keydown', handleKeyboard);
                        popup.addEventListener('touchstart', handleTouchStart);
                        popup.addEventListener('touchmove', handleTouchMove);
                        popup.addEventListener('touchend', handleTouchEnd);
                    },
                    willClose: (popup) => {
                        const image = popup.querySelector('img');
                        image.removeEventListener('click', nextImage);

                        popup.removeEventListener('keydown', handleKeyboard);
                        popup.removeEventListener('touchstart', handleTouchStart);
                        popup.removeEventListener('touchmove', handleTouchMove);
                        popup.removeEventListener('touchend', handleTouchEnd);
                    },
                });
            }

            function nextImage() {
                currentIndex = (currentIndex + 1) % images.length;
                showImage(currentIndex);
            }

            function prevImage() {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                showImage(currentIndex);
            }

            function handleKeyboard(evt) {
                if (evt.key === 'ArrowRight') {
                    nextImage();
                } else if (evt.key === 'ArrowLeft') {
                    prevImage();
                }
            }

            function handleTouchStart(evt) {
                touchStartX = evt.touches[0].clientX;
            }

            function handleTouchMove(evt) {
                touchEndX = evt.touches[0].clientX;
            }

            function handleTouchEnd() {
                const diffX = touchEndX - touchStartX;
                if (diffX > 50) {
                    prevImage();
                } else if (diffX < -50) {
                    nextImage();
                }
            }

            showImage(currentIndex);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Sin imágenes',
                text: 'No hay evidencia disponible.',
                background: '#f9f9f9',
                confirmButtonColor: '#333'
            });
        }
    }, 200); // Agregar pequeño retraso para asegurarse de que las imágenes ya fueron agregadas
});



</script>

<script>
   document.addEventListener('DOMContentLoaded', function () {
    const viewVideoButton = document.getElementById('viewVideoButton');
    const videoContainer = document.getElementById('videoContainer');
    const videoPreview = document.getElementById('videoPreview');
    const videoSource = document.getElementById('videoSource');

    viewVideoButton.addEventListener('click', function () {
        // Obtener la URL del video desde el atributo data-video-url
        const videoURL = viewVideoButton.getAttribute('data-video-url');

        // Verificar si la URL es válida
        if (videoURL) {
            videoSource.src = videoURL; // Asignar la URL al source del video
            videoPreview.load(); // Recargar el video para mostrarlo
            videoContainer.style.display = 'block'; // Mostrar el contenedor del video
        } else {
            alert('No se encontró un archivo de video.');
        }
    });
});


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
    let timeout;
    const INACTIVITY_LIMIT = 120 * 60 * 1000; // 10 minutos en milisegundos
    let lastActivityTime = Date.now(); // Establece el tiempo inicial de actividad

    function resetTimer() {
        clearTimeout(timeout);
        timeout = setTimeout(logoutUser, INACTIVITY_LIMIT); // Redirige después de 10 minutos
        lastActivityTime = Date.now(); // Resetea el tiempo de la última actividad
    }

    function logoutUser() {
        // Redirige al usuario a la ruta de logout
        window.location.href = "{{ route('logout') }}"; // Asegúrate de que la ruta 'logout' esté definida en tus rutas
    }

    // Detectar cualquier actividad del usuario (movimiento del ratón, teclas presionadas, etc.)
    document.onload = resetTimer;
    document.onmousemove = resetTimer;
    document.onkeypress = resetTimer;
    document.onscroll = resetTimer;
    document.onclick = resetTimer;

    // Verificar automáticamente la sesión cada vez que el usuario realice una acción
    setInterval(function() {
        // Si han pasado más de 10 minutos desde la última actividad, cerramos la sesión
        if (Date.now() - lastActivityTime > INACTIVITY_LIMIT) {
            logoutUser();
        }
    }, 10000); // Verificar cada 10 segundos si ha pasado el tiempo de inactividad
</script>
<script>
function toggleSubmenu(event, submenuId) {
    event.preventDefault(); // Evita que el enlace recargue la página
    const submenu = document.getElementById(submenuId);
    submenu.style.display = (submenu.style.display === "block") ? "none" : "block";
}
</script>

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>








