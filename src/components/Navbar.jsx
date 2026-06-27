import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import AnimatedThemeToggler from './AnimatedThemeToggler'

const links = [
  { href: '/#pilares',       label: 'Pilares' },
  { href: '/#planes',        label: 'Planes' },
  { href: '/#como-funciona', label: 'Cómo funciona' },
  { href: '/#modulos',       label: 'Módulos' },
]

export default function Navbar() {
  const [open, setOpen] = useState(false)
  const [scrolled, setScrolled] = useState(false)
  const [showModal, setShowModal] = useState(false)
  const emptyForm = { nombre: '', institucion: '', email: '', telefono: '', tipo: '', mensaje: '' }
  const [formData, setFormData] = useState(emptyForm)
  const [errors, setErrors] = useState({})
  const [enviado, setEnviado] = useState(false)

  const validate = (data) => {
    const e = {}
    if (!data.nombre.trim() || data.nombre.trim().length < 3)
      e.nombre = 'Ingresa tu nombre completo (mín. 3 caracteres).'
    if (!data.institucion.trim() || data.institucion.trim().length < 3)
      e.institucion = 'Ingresa el nombre de tu institución.'
    const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/
    if (!emailReg.test(data.email))
      e.email = 'Ingresa un correo electrónico válido.'
    const telReg = /^[+]?[\d\s\-().]{7,15}$/
    if (!data.telefono.trim() || !telReg.test(data.telefono))
      e.telefono = 'Ingresa un teléfono válido (7-15 dígitos).'
    if (!data.tipo)
      e.tipo = 'Selecciona el tipo de institución.'
    if (!data.mensaje.trim() || data.mensaje.trim().length < 10)
      e.mensaje = 'Describe brevemente tu necesidad (mín. 10 caracteres).'
    return e
  }

  const handleChange = e => {
    const updated = { ...formData, [e.target.name]: e.target.value }
    setFormData(updated)
    if (errors[e.target.name]) {
      const { [e.target.name]: _, ...rest } = errors
      setErrors(rest)
    }
  }

  const [enviando, setEnviando] = useState(false)
  const [errorServidor, setErrorServidor] = useState('')

  const handleSubmit = async e => {
    e.preventDefault()
    const errs = validate(formData)
    if (Object.keys(errs).length > 0) { setErrors(errs); return }
    setErrors({})
    setEnviando(true)
    setErrorServidor('')
    try {
      const url = location.hostname === 'localhost' || location.hostname === '127.0.0.1'
        ? 'http://localhost:3001/api/solicitar-demo'
        : '/send-demo.php'
      const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      })
      const data = await res.json()
      if (!res.ok) throw new Error(data.error || 'Error al enviar.')
      setEnviado(true)
      setTimeout(() => { setShowModal(false); setEnviado(false); setFormData(emptyForm) }, 2500)
    } catch (err) {
      setErrorServidor(err.message)
    } finally {
      setEnviando(false)
    }
  }

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 60)
    window.addEventListener('scroll', onScroll, { passive: true })
    return () => window.removeEventListener('scroll', onScroll)
  }, [])

  useEffect(() => {
    const onOpenModal = () => setShowModal(true)
    window.addEventListener('open-demo-modal', onOpenModal)
    return () => window.removeEventListener('open-demo-modal', onOpenModal)
  }, [])

  return (
    <>
    <header
      data-scrolled={scrolled ? 'true' : 'false'}
      className="nav fixed top-0 left-0 right-0 z-[100] transition-all duration-500"
    >
      <style>{`
        .nav {
          --nav-text:#94a3b8; --nav-hover:#2196f3;
          --nav-bar: rgba(5,13,31,0.90); --nav-border: rgba(255,255,255,0.10);
          --nav-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
          --mobile-bg:#050d1f; --mobile-text:#cbd5e1;
        }
        :root[data-theme="light"] .nav {
          --nav-text:#475569; --nav-hover:#0284c7;
          --nav-bar: rgba(255,255,255,0.85); --nav-border: rgba(15,23,42,0.10);
          --nav-shadow: 0 10px 30px -12px rgba(15,23,42,0.18);
          --mobile-bg:#ffffff; --mobile-text:#334155;
        }
        /* Barra de fondo según scroll */
        .nav[data-scrolled="true"] {
          background: var(--nav-bar);
          backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
          border-bottom: 1px solid var(--nav-border);
          box-shadow: var(--nav-shadow);
        }
        .nav[data-scrolled="false"] { background: transparent; border-bottom: 1px solid transparent; }

        .nav-link { color: var(--nav-text); transition: color .2s ease; }
        .nav-link:hover { color: var(--nav-hover); }

        .nav-link-contact { color: #e2e8f0; transition: color .2s ease; }
        .nav-link-contact:hover { color: #60a5fa; }
        :root[data-theme="light"] .nav-link-contact { color: #1e293b; }
        :root[data-theme="light"] .nav-link-contact:hover { color: #0284c7; }
        .nav-link::after { content:''; position:absolute; bottom:0; left:0; width:0; height:1px;
          background: var(--nav-hover); transition: width .3s ease; }
        .nav-link:hover::after { width:100%; }

        .nav-burger { color: var(--nav-text); }
        .nav-burger:hover { color: var(--nav-hover); }

        .nav-mobile { background: var(--mobile-bg); border-bottom:1px solid var(--nav-border); }
        .nav-mobile a { color: var(--mobile-text); transition: color .2s ease; }
        .nav-mobile a:hover { color: var(--nav-hover); }

        /* Modal tema */
        .demo-modal-bg { background: #0a1628; border: 1px solid rgba(33,150,243,0.3); }
        .demo-modal-title { color: #f1f5f9; }
        .demo-modal-sub { color: #94a3b8; }
        .demo-input { background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.12); color: #f1f5f9; }
        .demo-input::placeholder { color: #64748b; }
        .demo-select { background: #0f1e3d; border: 1px solid rgba(255,255,255,0.12); color: #f1f5f9; }
        :root[data-theme="light"] .demo-modal-bg { background: #ffffff; border: 1px solid rgba(2,132,199,0.25); }
        :root[data-theme="light"] .demo-modal-title { color: #0f172a; }
        :root[data-theme="light"] .demo-modal-sub { color: #475569; }
        :root[data-theme="light"] .demo-input { background: #f8fafc; border: 1px solid #cbd5e1; color: #0f172a; }
        :root[data-theme="light"] .demo-input::placeholder { color: #94a3b8; }
        :root[data-theme="light"] .demo-select { background: #f1f5f9; border: 1px solid #cbd5e1; color: #0f172a; }

        /* Cambio de logo según tema */
        .logo-light { display: none; }                       /* oculto en oscuro */
        :root[data-theme="light"] .logo-dark { display: none; }   /* oculta el de oscuro en claro */
        :root[data-theme="light"] .logo-light { display: block; } /* muestra el blanco en claro */
      `}</style>

      <nav
        className={`max-w-6xl mx-auto px-6 flex items-center justify-between transition-all duration-500 ${
          scrolled ? 'h-20' : 'h-28'
        }`}
      >
        {/* Logo (dos versiones: una por tema) */}
        <Link to="/" className="flex items-center group -my-4">
          {/* Logo para fondo oscuro */}
          <img
            src="/logo.png"
            alt="ENCLAII"
            className={`logo-dark w-auto transition-all duration-500 object-contain
              drop-shadow-[0_0_16px_rgba(33,150,243,0.55)]
              group-hover:drop-shadow-[0_0_32px_rgba(33,150,243,1)]
              group-hover:scale-105
              ${scrolled ? 'h-20' : 'h-28'}`}
          />
          {/* Logo para fondo claro/blanco */}
          <img
            src="/logoblanco.png"
            alt="ENCLAII"
            className={`logo-light w-auto transition-all duration-500 object-contain
              drop-shadow-[0_0_12px_rgba(2,132,199,0.4)]
              group-hover:drop-shadow-[0_0_28px_rgba(2,132,199,0.8)]
              group-hover:scale-105
              ${scrolled ? 'h-20' : 'h-28'}`}
          />
        </Link>

        {/* Links desktop */}
        <ul className="hidden md:flex gap-8 text-sm font-medium items-center">
          {links.map(l => (
            <li key={l.href}>
              <a href={l.href} className="nav-link relative">{l.label}</a>
            </li>
          ))}
          <li>
            <Link to="/por-que" className="nav-link relative">¿Por qué ENCLAII?</Link>
          </li>
          <li>
            <Link to="/contacto" className="nav-link-contact relative">Contacto</Link>
          </li>
        </ul>

        {/* Toggle + CTA (desktop) */}
        <div className="hidden md:flex items-center gap-3">
          <AnimatedThemeToggler />
          <button
            onClick={() => setShowModal(true)}
            className="inline-flex items-center gap-2 bg-[#2196f3] text-white px-5 py-2 rounded-lg
              text-sm font-medium hover:bg-[#1e88e5] transition-all duration-200
              shadow-lg shadow-[#2196f3]/25 hover:shadow-[#2196f3]/40 hover:-translate-y-px"
          >
            Solicitar demo
          </button>
        </div>

        {/* Toggle + Burger (mobile) */}
        <div className="md:hidden flex items-center gap-2">
          <AnimatedThemeToggler />
          <button
            className="nav-burger p-2"
            onClick={() => setOpen(!open)}
            aria-label="Toggle menu"
          >
            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                d={open ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'}
              />
            </svg>
          </button>
        </div>
      </nav>

      {/* Mobile menu */}
      <div
        className={`nav-mobile md:hidden overflow-hidden transition-all duration-300
          ${open ? 'max-h-80 opacity-100' : 'max-h-0 opacity-0'}`}
      >
        <div className="px-6 py-4 space-y-3">
          {links.map(l => (
            <a key={l.href} href={l.href} onClick={() => setOpen(false)} className="block">{l.label}</a>
          ))}
          <Link to="/por-que" onClick={() => setOpen(false)} className="block">¿Por qué ENCLAII?</Link>
          <Link to="/contacto" onClick={() => setOpen(false)} className="block">Contacto</Link>
          <button
            onClick={() => { setOpen(false); setShowModal(true) }}
            className="block w-full bg-[#2196f3] text-white text-center px-4 py-2 rounded-lg font-medium"
          >
            Solicitar demo
          </button>
        </div>
      </div>
    </header>

      {/* MODAL SOLICITAR DEMO */}
      {showModal && (
        <div className="fixed inset-0 z-[200] flex items-center justify-center p-4" style={{ background: 'rgba(0,0,0,0.7)', backdropFilter: 'blur(6px)' }}
          onClick={e => { if (e.target === e.currentTarget) setShowModal(false) }}>
          <div className="demo-modal-bg relative w-full max-w-lg rounded-3xl p-8 shadow-2xl">

            {/* Botón cerrar */}
            <button onClick={() => setShowModal(false)} className="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/10 transition-colors">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>

            {enviado ? (
              <div className="text-center py-8">
                <div className="w-16 h-16 rounded-full bg-[#2196f3]/20 flex items-center justify-center mx-auto mb-4">
                  <svg className="w-8 h-8 text-[#2196f3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <h3 className="demo-modal-title text-xl font-semibold mb-2">¡Solicitud enviada!</h3>
                <p className="demo-modal-sub">Nos pondremos en contacto contigo pronto.</p>
              </div>
            ) : (
              <>
                <h2 className="demo-modal-title text-2xl font-semibold mb-1">Solicitar demo</h2>
                <p className="demo-modal-sub text-sm mb-6">Completa el formulario y te contactamos a la brevedad.</p>

                <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                  <div className="grid grid-cols-2 gap-4">
                    <div className="flex flex-col gap-1">
                      <label className="demo-modal-sub text-xs font-medium">Nombre completo *</label>
                      <input name="nombre" value={formData.nombre} onChange={handleChange}
                        placeholder="Dr. Juan López"
                        className={`demo-input rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#2196f3] ${errors.nombre ? 'border-red-500 border-2' : ''}`} />
                      {errors.nombre && <span className="text-red-400 text-xs mt-0.5">{errors.nombre}</span>}
                    </div>
                    <div className="flex flex-col gap-1">
                      <label className="demo-modal-sub text-xs font-medium">Institución *</label>
                      <input name="institucion" value={formData.institucion} onChange={handleChange}
                        placeholder="Hospital / Clínica"
                        className={`demo-input rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#2196f3] ${errors.institucion ? 'border-red-500 border-2' : ''}`} />
                      {errors.institucion && <span className="text-red-400 text-xs mt-0.5">{errors.institucion}</span>}
                    </div>
                  </div>
                  <div className="grid grid-cols-2 gap-4">
                    <div className="flex flex-col gap-1">
                      <label className="demo-modal-sub text-xs font-medium">Correo electrónico *</label>
                      <input type="email" name="email" value={formData.email} onChange={handleChange}
                        placeholder="correo@hospital.com"
                        className={`demo-input rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#2196f3] ${errors.email ? 'border-red-500 border-2' : ''}`} />
                      {errors.email && <span className="text-red-400 text-xs mt-0.5">{errors.email}</span>}
                    </div>
                    <div className="flex flex-col gap-1">
                      <label className="demo-modal-sub text-xs font-medium">Teléfono *</label>
                      <input name="telefono" value={formData.telefono} onChange={handleChange}
                        placeholder="+52 55 0000 0000"
                        className={`demo-input rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#2196f3] ${errors.telefono ? 'border-red-500 border-2' : ''}`} />
                      {errors.telefono && <span className="text-red-400 text-xs mt-0.5">{errors.telefono}</span>}
                    </div>
                  </div>
                  <div className="flex flex-col gap-1">
                    <label className="demo-modal-sub text-xs font-medium">Tipo de institución *</label>
                    <select name="tipo" value={formData.tipo} onChange={handleChange}
                      className={`demo-select rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#2196f3] ${errors.tipo ? 'border-red-500 border-2' : ''}`}>
                      <option value="">Selecciona una opción</option>
                      <option value="hospital">Hospital público</option>
                      <option value="clinica">Clínica privada</option>
                      <option value="consultorio">Consultorio</option>
                      <option value="red">Red médica</option>
                      <option value="otro">Otro</option>
                    </select>
                      {errors.tipo && <span className="text-red-400 text-xs mt-0.5">{errors.tipo}</span>}
                  </div>
                  <div className="flex flex-col gap-1">
                    <label className="demo-modal-sub text-xs font-medium">¿Qué te interesa conocer? *</label>
                    <textarea name="mensaje" value={formData.mensaje} onChange={handleChange} rows={3}
                      placeholder="Cuéntanos brevemente tu necesidad..."
                      className={`demo-input rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-[#2196f3] resize-none ${errors.mensaje ? 'border-red-500 border-2' : ''}`} />
                    {errors.mensaje && <span className="text-red-400 text-xs mt-0.5">{errors.mensaje}</span>}
                  </div>
                  {errorServidor && (
                    <p className="text-red-400 text-xs text-center bg-red-500/10 rounded-lg px-3 py-2">{errorServidor}</p>
                  )}
                  <button type="submit" disabled={enviando}
                    className="mt-2 w-full py-3 rounded-xl font-semibold text-white text-sm transition-all duration-200 hover:-translate-y-px disabled:opacity-60 disabled:cursor-not-allowed"
                    style={{ background: 'linear-gradient(90deg, #1565c0, #2196f3)', boxShadow: '0 4px 20px rgba(33,150,243,0.4)' }}>
                    {enviando ? 'Enviando...' : 'Enviar solicitud'}
                  </button>
                </form>
              </>
            )}
          </div>
        </div>
      )}
    </>
  )
}