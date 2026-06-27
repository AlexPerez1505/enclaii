import express from 'express'
import nodemailer from 'nodemailer'
import cors from 'cors'
import dotenv from 'dotenv'

dotenv.config()

const app = express()
app.use(cors())
app.use(express.json())

const transporter = nodemailer.createTransport({
  host: process.env.MAIL_HOST,
  port: Number(process.env.MAIL_PORT),
  secure: process.env.MAIL_ENCRYPTION === 'ssl',
  auth: {
    user: process.env.MAIL_USERNAME,
    pass: process.env.MAIL_PASSWORD,
  },
})

app.post('/api/solicitar-demo', async (req, res) => {
  const { nombre, institucion, email, telefono, tipo, mensaje } = req.body

  if (!nombre || !institucion || !email || !telefono || !tipo || !mensaje) {
    return res.status(400).json({ ok: false, error: 'Todos los campos son obligatorios.' })
  }

  const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/
  if (!emailReg.test(email)) {
    return res.status(400).json({ ok: false, error: 'Correo electrónico inválido.' })
  }

  try {
    await transporter.sendMail({
      from: `"${process.env.MAIL_FROM_NAME}" <${process.env.MAIL_FROM_ADDRESS}>`,
      to: process.env.MAIL_TO,
      subject: `Nueva solicitud de demo — ${nombre}`,
      html: `
        <div style="font-family:sans-serif;max-width:600px;margin:auto;padding:32px;background:#f8fafc;border-radius:12px;">
          <h2 style="color:#1565c0;margin-bottom:4px;">Nueva solicitud de demo</h2>
          <p style="color:#64748b;margin-top:0;">Recibida desde enclaii.com</p>
          <hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0;" />
          <table style="width:100%;border-collapse:collapse;">
            <tr><td style="padding:8px 0;color:#64748b;width:160px;">Nombre</td><td style="padding:8px 0;color:#0f172a;font-weight:600;">${nombre}</td></tr>
            <tr><td style="padding:8px 0;color:#64748b;">Institución</td><td style="padding:8px 0;color:#0f172a;font-weight:600;">${institucion}</td></tr>
            <tr><td style="padding:8px 0;color:#64748b;">Correo</td><td style="padding:8px 0;color:#0f172a;font-weight:600;">${email}</td></tr>
            <tr><td style="padding:8px 0;color:#64748b;">Teléfono</td><td style="padding:8px 0;color:#0f172a;font-weight:600;">${telefono}</td></tr>
            <tr><td style="padding:8px 0;color:#64748b;">Tipo</td><td style="padding:8px 0;color:#0f172a;font-weight:600;">${tipo}</td></tr>
            <tr><td style="padding:8px 0;color:#64748b;vertical-align:top;">Mensaje</td><td style="padding:8px 0;color:#0f172a;">${mensaje}</td></tr>
          </table>
          <hr style="border:none;border-top:1px solid #e2e8f0;margin:20px 0;" />
          <p style="color:#94a3b8;font-size:12px;text-align:center;">ENCLAII — Sistema de Endoscopía</p>
        </div>
      `,
    })

    res.json({ ok: true })
  } catch (err) {
    console.error('Error al enviar correo:', err)
    res.status(500).json({ ok: false, error: 'No se pudo enviar el correo. Intenta más tarde.' })
  }
})

const PORT = process.env.PORT || 3001
app.listen(PORT, () => console.log(`Servidor ENCLAII corriendo en puerto ${PORT}`))
