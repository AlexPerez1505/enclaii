import { useEffect, useRef, useState } from 'react'
import { Link } from 'react-router-dom'

const MAILTO = 'mailto:licencias@cizcalli.gob.mx?subject=Solicitud%20de%20demo%20SIMED'

const Icon = {
  mail: (p) => (<svg {...p} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.6} d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>),
  phone: (p) => (<svg {...p} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.6} d="M3 5a2 2 0 012-2h2.2a1 1 0 01.95.68l1 3a1 1 0 01-.25 1L7.6 9.4a13 13 0 007 7l1.7-1.3a1 1 0 011-.25l3 1a1 1 0 01.68.95V19a2 2 0 01-2 2A16 16 0 013 5z" /></svg>),
  pin: (p) => (<svg {...p} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.6} d="M12 21s-7-5.2-7-11a7 7 0 1114 0c0 5.8-7 11-7 11z" /><circle cx="12" cy="10" r="2.5" strokeWidth={1.6} /></svg>),
  clock: (p) => (<svg {...p} fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" strokeWidth={1.6} /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.6} d="M12 7v5l3 2" /></svg>),
}

const TELEFONO = '55 1234 5678'
const TELEFONO_LINK = 'tel:+525512345678'

export default function Contacto() {
  const glowRef = useRef(null)
  const auroraRef = useRef(null)
  const gridRef = useRef(null)
  const [sent, setSent] = useState(false)

  useEffect(() => {
    window.scrollTo(0, 0)
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches

    const io = new IntersectionObserver((entries) => {
      entries.forEach((en) => { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target) } })
    }, { threshold: 0.15, rootMargin: '0px 0px -8% 0px' })
    document.querySelectorAll('.reveal').forEach((el) => io.observe(el))

    if (reduce) return () => io.disconnect()

    let mx = 0.5, my = 0.3, cx = 0.5, cy = 0.3, sy = 0, raf = 0
    const onMove = (e) => {
      mx = e.clientX / innerWidth; my = e.clientY / innerHeight
      document.querySelectorAll('[data-spot]').forEach((el) => {
        const r = el.getBoundingClientRect()
        el.style.setProperty('--spx', `${((e.clientX - r.left) / r.width) * 100}%`)
        el.style.setProperty('--spy', `${((e.clientY - r.top) / r.height) * 100}%`)
      })
    }
    const onScroll = () => { sy = window.scrollY }
    const lerp = (a, b, n) => a + (b - a) * n
    const loop = () => {
      cx = lerp(cx, mx, 0.06); cy = lerp(cy, my, 0.06)
      const dx = (cx - 0.5), dy = (cy - 0.5)
      if (glowRef.current) glowRef.current.style.transform = `translate3d(${dx * 90}px, ${dy * 90}px, 0)`
      if (auroraRef.current) auroraRef.current.style.transform = `translate3d(${dx * -40}px, ${dy * -40 - sy * 0.12}px, 0)`
      if (gridRef.current) gridRef.current.style.transform = `translate3d(0, ${sy * 0.05}px, 0)`
      raf = requestAnimationFrame(loop)
    }
    window.addEventListener('pointermove', onMove, { passive: true })
    window.addEventListener('scroll', onScroll, { passive: true })
    raf = requestAnimationFrame(loop)
    return () => {
      io.disconnect()
      window.removeEventListener('pointermove', onMove)
      window.removeEventListener('scroll', onScroll)
      cancelAnimationFrame(raf)
    }
  }, [])

  const handleSubmit = (e) => {
    e.preventDefault()
    const d = new FormData(e.currentTarget)
    const nombre = encodeURIComponent(d.get('nombre') || '')
    const correo = encodeURIComponent(d.get('correo') || '')
    const tel = encodeURIComponent(d.get('telefono') || '')
    const mensaje = encodeURIComponent(d.get('mensaje') || '')
    setSent(true)
    window.location.href =
      `mailto:licencias@cizcalli.gob.mx?subject=Contacto%20SIMED%20-%20${nombre}` +
      `&body=${mensaje}%0A%0ADe:%20${nombre}%20(${correo})%0ATel:%20${tel}`
    setTimeout(() => setSent(false), 4000)
  }

  const metodos = [
    { icon: Icon.mail, label: 'Correo', value: 'licencias@cizcalli.gob.mx', href: MAILTO },
    { icon: Icon.phone, label: 'Teléfono', value: TELEFONO, href: TELEFONO_LINK },
    { icon: Icon.pin, label: 'Dirección', value: 'Cuautitlán Izcalli, Edo. de México' },
    { icon: Icon.clock, label: 'Horario', value: 'Lun – Vie · 9:00 a 18:00' },
  ]

  return (
    <section className="ctc min-h-screen pt-32 pb-28 relative overflow-hidden">
      <style>{`
        .ctc {
          --eo: cubic-bezier(0.16, 1, 0.3, 1);
          --accent:#2196f3; --accent-text:#60a5fa;
          --bg-from:#08122b; --bg-to:#050d1f;
          --text:#e2e8f0; --title:#ffffff; --muted:#94a3b8; --placeholder:#64748b;
          /* Cards transparentes (modo oscuro) */
          --card-bg:rgba(255,255,255,0.02); --card-border:rgba(255,255,255,0.10);
          --card-shadow:0 1px 0 rgba(255,255,255,0.05) inset, 0 30px 70px -24px rgba(0,0,0,0.6);
          --field-bg:rgba(255,255,255,0.04); --field-border:rgba(255,255,255,0.12);
          --grid:rgba(148,163,184,0.05);
          --aurora-op:.55; --glow-op:1; --pt-op:1;
          --pill-bg:rgba(33,150,243,0.10); --pill-border:rgba(33,150,243,0.20);
          --chip-bg:rgba(33,150,243,0.12); --chip-border:rgba(33,150,243,0.20);
          --map-filter:grayscale(0.4) contrast(1.05) brightness(0.9);
          --map-pill-bg:rgba(5,13,31,0.55);
          --circle-border:rgba(255,255,255,0.10);
          color: var(--text);
        }
        :root[data-theme="light"] .ctc {
          --accent-text:#1d4ed8;
          --bg-from:#eaf2ff; --bg-to:#ffffff;
          --text:#1e293b; --title:#0b1220; --muted:#64748b; --placeholder:#94a3b8;
          /* Cards transparentes (liquid glass, modo claro) */
          --card-bg:rgba(255,255,255,0.16); --card-border:rgba(255,255,255,0.55);
          --card-shadow:0 1px 0 rgba(255,255,255,0.7) inset, 0 20px 50px -24px rgba(15,23,42,0.18);
          --field-bg:rgba(255,255,255,0.20); --field-border:rgba(15,23,42,0.10);
          --grid:rgba(15,23,42,0.05);
          --aurora-op:.30; --glow-op:.55; --pt-op:.45;
          --pill-bg:rgba(33,150,243,0.10); --pill-border:rgba(33,150,243,0.25);
          --chip-bg:rgba(33,150,243,0.12); --chip-border:rgba(33,150,243,0.25);
          --map-filter:grayscale(0.1) contrast(1) brightness(1);
          --map-pill-bg:rgba(255,255,255,0.45);
          --circle-border:rgba(15,23,42,0.12);
        }

        .ctc-base { position:absolute; inset:0; background: linear-gradient(to bottom, var(--bg-from), var(--bg-to)); transition: background .5s ease; }
        .ctc-aurora { position:absolute; inset:-20%; will-change:transform; filter: blur(60px); opacity:var(--aurora-op); transition:opacity .5s ease;
          background:
            radial-gradient(40% 40% at 25% 30%, rgba(33,150,243,0.55), transparent 60%),
            radial-gradient(35% 35% at 75% 25%, rgba(99,102,241,0.45), transparent 60%),
            radial-gradient(45% 45% at 60% 75%, rgba(14,165,233,0.40), transparent 60%);
          animation: aurora 18s ease-in-out infinite alternate; }
        @keyframes aurora { 0%{transform:translate3d(0,0,0) scale(1)} 50%{transform:translate3d(-3%,2%,0) scale(1.08)} 100%{transform:translate3d(3%,-2%,0) scale(1.04)} }

        .ctc-grid { position:absolute; inset:0; will-change:transform;
          background-image: linear-gradient(var(--grid) 1px, transparent 1px), linear-gradient(90deg, var(--grid) 1px, transparent 1px);
          background-size: 60px 60px;
          mask-image: radial-gradient(ellipse 80% 60% at 50% 20%, #000 35%, transparent 80%);
          -webkit-mask-image: radial-gradient(ellipse 80% 60% at 50% 20%, #000 35%, transparent 80%); }
        .ctc-glow { position:absolute; inset:0; will-change:transform; opacity:var(--glow-op); transition:opacity .5s ease;
          background: radial-gradient(620px 420px at 50% 16%, rgba(33,150,243,0.18), transparent 70%); }

        .pt { position:absolute; width:4px; height:4px; border-radius:9999px; background:var(--accent);
          box-shadow:0 0 12px 2px rgba(96,165,250,0.55); opacity:var(--pt-op); animation: float 9s ease-in-out infinite; }
        @keyframes float { 0%,100%{transform:translateY(0); opacity:calc(var(--pt-op) * .3)} 50%{transform:translateY(-26px); opacity:var(--pt-op)} }

        .reveal { opacity:0; transform: translateY(34px); filter: blur(12px);
          transition: opacity .9s var(--eo), transform .9s var(--eo), filter .9s var(--eo); }
        .reveal.in { opacity:1; transform:none; filter:blur(0); }
        .reveal.s1{transition-delay:.06s}.reveal.s2{transition-delay:.13s}.reveal.s3{transition-delay:.20s}
        .reveal.s4{transition-delay:.27s}.reveal.s5{transition-delay:.34s}.reveal.s6{transition-delay:.41s}

        .card { position:relative; background: var(--card-bg); border:1px solid var(--card-border);
          backdrop-filter: blur(18px) saturate(1.5); -webkit-backdrop-filter: blur(18px) saturate(1.5);
          box-shadow: var(--card-shadow);
          transition: background .5s ease, border-color .5s ease, box-shadow .5s ease; }

        [data-spot] { --spx:50%; --spy:0%; }
        [data-spot]::before { content:''; position:absolute; inset:0; border-radius:inherit; pointer-events:none;
          background: radial-gradient(360px circle at var(--spx) var(--spy), rgba(33,150,243,0.14), transparent 45%);
          opacity:0; transition:opacity .35s ease; }
        @media (hover:hover){ [data-spot]:hover::before { opacity:1; } }

        .t-title { color: var(--title); }
        .t-muted { color: var(--muted); }
        .t-text  { color: var(--text); }
        .t-accent{ color: var(--accent-text); }

        .pill { background: var(--pill-bg); border:1px solid var(--pill-border); color: var(--accent-text);
          backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }
        .chip { background: var(--chip-bg); border:1px solid var(--chip-border); color: var(--accent-text); }
        .circle { border:1px solid var(--circle-border); background: var(--card-bg); }

        .method { border:1px solid transparent; transition: background .25s ease, border-color .25s ease, transform .25s var(--eo); }
        @media (hover:hover){ .method:hover { background: rgba(33,150,243,0.07); border-color: rgba(33,150,243,0.30); transform: translateX(3px); } }

        .field { background: var(--field-bg); border:1px solid var(--field-border); color: var(--text);
          backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
          transition: border-color .2s ease, box-shadow .2s ease, background .2s ease; }
        .field::placeholder { color: var(--placeholder); }
        .field:focus { border-color:var(--accent); box-shadow: 0 0 0 4px rgba(33,150,243,0.14); }

        /* Botón estático (sin imán, sin flotación) — solo brillo en su sitio */
        .btn { transition: transform .18s var(--eo), background .2s ease, box-shadow .25s ease; }
        .btn:active { transform: scale(0.975); }
        .btn-primary { position:relative; overflow:hidden; background: var(--accent); color:#fff;
          animation: btnGlow 2.8s ease-in-out infinite; }
        .btn-primary:hover { background:#1e88e5; }
        .btn-primary::after { content:''; position:absolute; top:0; left:0; width:100%; height:100%; pointer-events:none;
          background: linear-gradient(120deg, transparent 35%, rgba(255,255,255,0.45) 50%, transparent 65%);
          transform: translateX(-160%); animation: shine 3.4s ease-in-out infinite; }
        @keyframes shine { 0%, 55% { transform: translateX(-160%); } 100% { transform: translateX(160%); } }
        @keyframes btnGlow {
          0%,100% { box-shadow: 0 10px 28px -12px rgba(33,150,243,0.55); }
          50%     { box-shadow: 0 18px 46px -10px rgba(33,150,243,0.95); }
        }
        .btn-ghost { background: var(--field-bg); border:1px solid var(--field-border); color: var(--text); }
        .btn-ghost:hover { border-color: rgba(33,150,243,0.45); }

        .map-pill { background: var(--map-pill-bg); border:1px solid var(--card-border); color: var(--text);
          backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }

        @media (prefers-reduced-motion: reduce){
          .reveal{opacity:1!important;transform:none!important;filter:none!important}
          .ctc-aurora,.ctc-glow,.ctc-grid,.pt,.btn-primary,.btn-primary::after{animation:none!important;transform:none!important}
        }
      `}</style>

      <div className="ctc-base" />
      <div ref={auroraRef} className="ctc-aurora" />
      <div ref={gridRef} className="ctc-grid" />
      <div ref={glowRef} className="ctc-glow" />
      <span className="pt" style={{ top: '22%', left: '12%', animationDelay: '0s' }} />
      <span className="pt" style={{ top: '38%', left: '84%', animationDelay: '1.2s' }} />
      <span className="pt" style={{ top: '64%', left: '20%', animationDelay: '2.1s' }} />
      <span className="pt" style={{ top: '74%', left: '70%', animationDelay: '.6s' }} />
      <span className="pt" style={{ top: '16%', left: '60%', animationDelay: '3s' }} />

      <div className="relative z-10 max-w-6xl mx-auto px-6">
        {/* Volver */}
        <Link to="/" className="reveal t-muted inline-flex items-center gap-2 text-sm hover:opacity-80 transition-opacity mb-10 group">
          <span className="circle grid place-items-center w-8 h-8 rounded-full transition-colors">
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" /></svg>
          </span>
          Volver al inicio
        </Link>

        {/* Header */}
        <div className="max-w-2xl mb-16">
          <span className="reveal s1 pill inline-flex items-center gap-2 text-xs font-medium tracking-[0.25em] uppercase mb-5 px-4 py-1.5 rounded-full">
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
            Sistema en línea · Contacto
          </span>
          <h1 className="reveal s2 t-title text-4xl md:text-6xl font-light tracking-tight leading-[1.05] mb-5">
            Hablemos de tu
            <br />
            <span className="bg-gradient-to-r from-[#2196f3] via-[#60a5fa] to-[#93c5fd] bg-clip-text text-transparent">
              servicio de endoscopia
            </span>
          </h1>
          <p className="reveal s3 t-muted text-lg leading-relaxed">
            Escríbenos, llámanos o agenda una demostración de SIMED. Respondemos en menos de 24 horas hábiles.
          </p>
        </div>

        {/* Layout principal */}
        <div className="grid lg:grid-cols-5 gap-6">
          {/* Formulario */}
          <div data-spot className="reveal s4 lg:col-span-3 card rounded-3xl p-8 md:p-10">
            <div className="flex items-center justify-between mb-7">
              <h2 className="t-title text-xl font-medium">Envíanos un mensaje</h2>
              <span className="hidden sm:inline-flex items-center gap-1.5 text-xs t-muted">
                <span className="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" /> Respuesta en 24 h
              </span>
            </div>

            <form onSubmit={handleSubmit} className="space-y-5">
              <div className="grid sm:grid-cols-2 gap-5">
                <div>
                  <label className="block text-sm t-muted mb-2">Nombre</label>
                  <input name="nombre" required placeholder="Tu nombre"
                    className="field w-full rounded-xl px-4 py-3.5 focus:outline-none" />
                </div>
                <div>
                  <label className="block text-sm t-muted mb-2">Teléfono</label>
                  <input name="telefono" type="tel" inputMode="tel" placeholder="55 1234 5678"
                    className="field w-full rounded-xl px-4 py-3.5 focus:outline-none" />
                </div>
              </div>
              <div>
                <label className="block text-sm t-muted mb-2">Correo</label>
                <input name="correo" type="email" required placeholder="tucorreo@ejemplo.com"
                  className="field w-full rounded-xl px-4 py-3.5 focus:outline-none" />
              </div>
              <div>
                <label className="block text-sm t-muted mb-2">Mensaje</label>
                <textarea name="mensaje" rows={5} required placeholder="Cuéntanos sobre tu institución y lo que necesitas"
                  className="field w-full rounded-xl px-4 py-3.5 focus:outline-none resize-none" />
              </div>

              <div className="flex flex-col sm:flex-row gap-3 pt-1">
                {/* Botón estático en su lugar */}
                <button type="submit"
                  className="btn btn-primary flex-1 inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl font-semibold text-base">
                  {sent ? 'Abriendo tu correo…' : 'Enviar mensaje'}
                  {!sent && (<svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>)}
                </button>
                <a href={TELEFONO_LINK}
                  className="btn btn-ghost inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl font-medium text-base">
                  <Icon.phone className="w-5 h-5" /> Llamar ahora
                </a>
              </div>
            </form>
          </div>

          {/* Columna derecha */}
          <div className="lg:col-span-2 space-y-6">
            <div data-spot className="reveal s5 card rounded-3xl p-3">
              {metodos.map((m) => {
                const Inner = (
                  <>
                    <span className="chip grid place-items-center w-11 h-11 rounded-xl shrink-0">
                      <m.icon className="w-5 h-5" />
                    </span>
                    <span className="min-w-0">
                      <span className="block text-xs uppercase tracking-wider t-muted">{m.label}</span>
                      <span className="block t-text truncate">{m.value}</span>
                    </span>
                  </>
                )
                return m.href ? (
                  <a key={m.label} href={m.href} target={m.href.startsWith('mailto') ? '_blank' : undefined} rel="noopener noreferrer"
                    className="method flex items-center gap-4 p-4 rounded-2xl">{Inner}</a>
                ) : (
                  <div key={m.label} className="method flex items-center gap-4 p-4 rounded-2xl">{Inner}</div>
                )
              })}
            </div>

            {/* Mapa */}
            <div className="reveal s6 card rounded-3xl overflow-hidden relative min-h-[280px]">
              <div className="map-pill absolute top-4 left-4 z-10 inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs">
                <Icon.pin className="w-3.5 h-3.5 t-accent" /> Cuautitlán Izcalli
              </div>
              <iframe
                title="Ubicación"
                src="https://www.google.com/maps?q=Cuautitl%C3%A1n%20Izcalli%2C%20Estado%20de%20M%C3%A9xico&output=embed"
                className="w-full h-full min-h-[280px]"
                style={{ border: 0, filter: 'var(--map-filter)' }}
                loading="lazy" referrerPolicy="no-referrer-when-downgrade" allowFullScreen
              />
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}