@component('mail::message')
# 📦 Nueva Solicitud de Material Recibida

Hola **Administrador**,<br><br>

Se ha recibido una nueva solicitud de material a través del sistema **GrupoMediBuy**. A continuación, se detallan los datos proporcionados:

---

### 📝 Detalles de la Solicitud

- **Categoría:** {{ $solicitud->categoria }}
- **Material Solicitado:** {{ $solicitud->material }}
- **Cantidad Requerida:** {{ $solicitud->cantidad }}
- **Justificación:** {{ $solicitud->justificacion ?? 'No especificada.' }}

---

### 📥 ¿Qué hacer ahora?

Para revisar, autorizar o rechazar esta solicitud, ingrese al sistema dando clic en el botón a continuación:

@component('mail::button', ['url' => 'https://medibuy.grupomedibuy.com/login'])
Ver solicitudes
@endcomponent

---

### 🤝 Agradecimiento

Agradecemos su atención y compromiso para mantener el flujo adecuado de materiales dentro de la organización.

---

**Atentamente,**  
Equipo de Soporte – GrupoMediBuy








@endcomponent
