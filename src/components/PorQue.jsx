import { useEffect, useRef } from 'react'
import { Link } from 'react-router-dom'


const MAILTO = 'mailto:licencias@cizcalli.gob.mx?subject=Quiero%20ENCLAII'

const razones = [
  { t: 'Integración total', d: 'ENCLAII se acopla a tu flujo actual de endoscopia sin frenar a tu equipo.' },
  { t: 'Trazabilidad completa', d: 'Cada estudio, imagen y reporte queda registrado y auditable.' },
  { t: 'Reportes automáticos', d: 'Genera informes clínicos en minutos, no en horas.' },
  { t: 'Seguridad de datos', d: 'Información protegida y respaldada bajo estándares institucionales.' },
  { t: 'Soporte en español', d: 'Acompañamiento real, cercano y sin tecnicismos innecesarios.' },
  { t: 'Ahorro de tiempo', d: 'Menos papeleo, más tiempo para lo que importa: el paciente.' },
]

const bubbles = Array.from({ length: 22 }).map(() => ({
  left: Math.random() * 100,
  size: 6 + Math.random() * 26,
  dur: 7 + Math.random() * 12,
  delay: Math.random() * 12,
  op: 0.15 + Math.random() * 0.4,
}))

// Capa oscura para que las fotos combinen con el agua
const overlay = 'linear-gradient(rgba(2,20,40,0.35), rgba(2,20,40,0.35))'

const getCardBackStyle = (title) => {
  const images = {
    'Integración total': '/panel7.jpg',
    'Trazabilidad completa': '/panel6.jpg',
    'Soporte en español': '/panel8.jpg',
    'Reportes automáticos': '/panel9.jpg',
    'Seguridad de datos': '/panel10.jpg',
    'Ahorro de tiempo': '/panel11.jpg',
  }

  return images[title]
    ? { backgroundImage: `${overlay}, url('${images[title]}')`, backgroundSize: 'cover', backgroundPosition: 'center' }
    : undefined
}

export default function PorQue() {
  const rootRef = useRef(null)

  useEffect(() => {
    window.scrollTo(0, 0)
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches

    const io = new IntersectionObserver((entries) => {
      entries.forEach((en) => { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target) } })
    }, { threshold: 0.15 })
    document.querySelectorAll('.dv-reveal').forEach((el) => io.observe(el))

    if (reduce) return () => io.disconnect()

    const layers = Array.from(document.querySelectorAll('[data-speed]'))
    let mx = 0.5, cx = 0.5, sy = 0, raf = 0
    const onMove = (e) => { mx = e.clientX / innerWidth }
    const onScroll = () => { sy = window.scrollY }
    const lerp = (a, b, n) => a + (b - a) * n
    const loop = () => {
      cx = lerp(cx, mx, 0.14)
      const dx = (cx - 0.5)
      layers.forEach((el) => {
        const sp = parseFloat(el.dataset.speed)
        const my = parseFloat(el.dataset.mouse || '0')
        el.style.transform = `translate3d(${dx * my}px, ${-sy * sp}px, 0)`
      })
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

  return (
    <div ref={rootRef} className="dive relative overflow-hidden">
      <style>{`
        .dive {
          --eo: cubic-bezier(0.16,1,0.3,1);
          /* OSCURO (abismo) */
          --bg: linear-gradient(180deg,
              #1e7fb8 0%, #12618f 12%, #0a4570 28%,
              #06304f 45%, #041f38 62%, #020f20 80%, #01060f 100%);
          --text:#dbeafe; --title:#ffffff; --sub: rgba(186,230,253,0.8);
          --accent-text:#67e8f9;
          --card-back-overlay: rgba(2,15,35,0.55); --card-back-shadow: rgba(0,0,0,0.9);
          --g1:#67e8f9; --g2:#38bdf8; --g3:#60a5fa;
          --glass-bg: rgba(10,40,70,0.35); --glass-border: rgba(173,216,255,0.20);
          --panel-border: rgba(173,216,255,0.25);
          --rays-op: .5; --bubble: rgba(255,255,255,0.8);
          --num-bg: rgba(34,211,238,0.15); --num-border: rgba(103,232,249,0.3); --num-text:#a5f3fc;
          --btn-bg:#22d3ee; --btn-text:#02233a; --btn-bg-hover:#67e8f9;
          color: var(--text);
        }
        :root[data-theme="light"] .dive {
          /* CLARO (superficie / aguas claras) */
          --bg: linear-gradient(180deg,
              #f5fbff 0%, #e4f5ff 16%, #cdecfb 34%,
              #aeddf3 54%, #8fcde9 74%, #74bcdd 100%);
          --text:#0c4a6e; --title:#062c43; --sub: rgba(12,74,110,0.78);
          --accent-text:#0369a1;
          --card-back-overlay: rgba(255,255,255,0.60); --card-back-shadow: rgba(0,0,0,0.15);
          --g1:#0891b2; --g2:#0284c7; --g3:#1d4ed8;
          --glass-bg: rgba(255,255,255,0.55); --glass-border: rgba(2,132,199,0.18);
          --panel-border: rgba(2,132,199,0.20);
          --rays-op: .25; --bubble: rgba(255,255,255,0.9);
          --num-bg: rgba(2,132,199,0.12); --num-border: rgba(2,132,199,0.30); --num-text:#0369a1;
          --btn-bg:#0ea5e9; --btn-text:#ffffff; --btn-bg-hover:#0284c7;
        }

        .dive-bg { position:fixed; inset:0; z-index:0; pointer-events:none; background: var(--bg); transition: background .6s ease; }

        .dive-rays { position:fixed; top:0; left:0; right:0; height:70vh; z-index:1; pointer-events:none; opacity:var(--rays-op);
          background:
            linear-gradient(100deg, transparent 40%, rgba(173,216,255,0.10) 45%, transparent 50%),
            linear-gradient(80deg, transparent 55%, rgba(173,216,255,0.08) 60%, transparent 66%),
            linear-gradient(110deg, transparent 20%, rgba(173,216,255,0.06) 26%, transparent 32%);
          mix-blend-mode: screen; animation: rays 9s ease-in-out infinite alternate; }
        @keyframes rays { from{transform:translateX(-3%) skewX(-2deg)} to{transform:translateX(3%) skewX(2deg)} }

        .dive-bubbles { position:fixed; inset:0; z-index:2; pointer-events:none; overflow:hidden; }
        .bub { position:absolute; bottom:-40px; border-radius:9999px;
          background: radial-gradient(circle at 30% 30%, var(--bubble), rgba(180,220,255,0.15) 60%, transparent 70%);
          box-shadow: inset 0 0 6px rgba(255,255,255,0.4); animation: rise linear infinite; }
        @keyframes rise {
          0% { transform: translateY(0) translateX(0); opacity:0; }
          10%{ opacity:1; } 50%{ transform: translateY(-55vh) translateX(14px); }
          100%{ transform: translateY(-110vh) translateX(-10px); opacity:0; }
        }

        .dive-content { position:relative; z-index:5; }

        .panel { position:absolute; border-radius:20px; overflow:hidden; will-change:transform;
          border:1px solid var(--panel-border);
          box-shadow: 0 30px 80px -20px rgba(0,0,0,0.45), inset 0 1px 0 rgba(255,255,255,0.18);
          background-size:cover; background-position:center; animation: floaty 6s ease-in-out infinite; }
        @keyframes floaty { 0%,100%{ filter:brightness(1) } 50%{ filter:brightness(1.08) } }

        .dv-reveal { opacity:0; transform: translateY(40px); filter: blur(12px);
          transition: opacity 1s var(--eo), transform 1s var(--eo), filter 1s var(--eo); }
        .dv-reveal.in { opacity:1; transform:none; filter:blur(0); }

        .glass { background: var(--glass-bg); border:1px solid var(--glass-border);
          backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);
          box-shadow: 0 30px 70px -24px rgba(0,0,0,0.45), inset 0 1px 0 rgba(255,255,255,0.18);
          transition: background .6s ease, border-color .6s ease; }

        .dv-title { color: var(--title); font-family: 'Playfair Display', Georgia, serif; }

        .dv-sub { color: var(--sub); }
        .dv-accent { color: var(--accent-text); }

        .flip-card { perspective: 1000px; height: 220px; animation: card-float 4s ease-in-out infinite; }
        .flip-card:nth-child(2) { animation-delay: 0.5s; }
        .flip-card:nth-child(3) { animation-delay: 1s; }
        .flip-card-inner { position: relative; width: 100%; height: 100%; transition: transform 0.6s; transform-style: preserve-3d; }
        .flip-card:hover .flip-card-inner { transform: rotateY(180deg); }
        .flip-card-front, .flip-card-back { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; }
        .flip-card-back { transform: rotateY(180deg); }
        @keyframes card-float {
          0%,100% { transform: translateY(0px); }
          50% { transform: translateY(-6px); }
        }

        /* Texto con degradado theme-aware (siempre legible) */
        .dv-grad {
          background: linear-gradient(90deg, var(--g1), var(--g2), var(--g3));
          -webkit-background-clip: text; background-clip: text; color: transparent;
        }

        .dv-btn { transition: transform .16s var(--eo), box-shadow .25s ease, background .2s ease; }
        .dv-btn:active { transform: scale(0.97); }
        .dv-btn-primary { background: var(--btn-bg); color: var(--btn-text); }
        .dv-btn-primary:hover { background: var(--btn-bg-hover); }
        .dv-btn-ghost { background: var(--glass-bg); color: var(--text); border:1px solid var(--glass-border); }

        @media (prefers-reduced-motion: reduce){
          .dive-rays,.bub,.panel{ animation:none!important }
          .dv-reveal{ opacity:1!important; transform:none!important; filter:none!important }
          [data-speed]{ transform:none!important }
        }
      `}</style>

      <div className="dive-bg" />
      <div className="dive-rays" />
      <div className="dive-bubbles">
        {bubbles.map((b, i) => (
          <span key={i} className="bub" style={{
            left: `${b.left}%`, width: b.size, height: b.size,
            opacity: b.op, animationDuration: `${b.dur}s`, animationDelay: `${b.delay}s`,
          }} />
        ))}
      </div>

      <div className="dive-content">
        {/* HERO */}
        <section className="relative flex items-center px-6 pt-40 pb-8">
          <div className="w-full max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-start">

            {/* Texto + imágenes izquierda */}
            <div className="z-10 flex flex-col">
              {/* Etiqueta pequeña */}
              <div className="dv-reveal flex items-center gap-3 mb-3" style={{ transitionDelay: '50ms' }}>
                <div className="h-px w-8" style={{ background: 'var(--g2)' }} />
                <span className="dv-accent text-xs font-semibold tracking-[0.3em] uppercase">¿Por qué ENCLAII?</span>
              </div>

              {/* Título */}
              <h1 className="dv-reveal dv-title text-4xl md:text-6xl font-light leading-[1.1] tracking-tight text-center lg:text-left" style={{ transitionDelay: '120ms' }}>
                ¿Por qué tu institución
                <br />
                <span className="dv-grad" style={{ fontStyle: 'italic', fontWeight: 500 }}>
                  debería sumergirse con nosotros?
                </span>
              </h1>

              {/* Separador */}
              <div className="dv-reveal my-4 h-px max-w-xs" style={{ background: 'linear-gradient(90deg, var(--g2), transparent)', transitionDelay: '200ms' }} />

              {/* Subtítulo */}
              <p className="dv-reveal dv-sub text-lg md:text-xl leading-relaxed" style={{ transitionDelay: '260ms' }}>
                Desciende y descubre cómo ENCLAII transforma tu servicio de endoscopia,
                capa por capa, sin perder de vista lo esencial.
              </p>
              {/* Imágenes decorativas rectas de mayor a menor */}
              <div className="dv-reveal mt-6 flex gap-6 items-center" style={{ transitionDelay: '300ms' }}>
                <div className="panel" data-speed="0.2" data-mouse="20"
                  style={{ position: 'relative', width: 160, height: 110, flexShrink: 0,
                    backgroundImage: `${overlay}, url('/panel1.jpg')`, animationDelay: '0s' }} />
                <div className="panel" data-speed="0.3" data-mouse="-15"
                  style={{ position: 'relative', width: 120, height: 110, flexShrink: 0,
                    backgroundImage: `${overlay}, url('/panel2.jpg')`, animationDelay: '0.3s' }} />
                <div className="panel" data-speed="0.25" data-mouse="25"
                  style={{ position: 'relative', width: 85, height: 110, flexShrink: 0,
                    backgroundImage: `${overlay}, url('/panel3.jpg')`, animationDelay: '0.6s' }} />
              </div>

            </div>

            {/* Video derecha */}
            <div className="dv-reveal relative z-10 flex justify-center -mt-8" style={{ transitionDelay: '200ms' }}>
              <div className="rounded-3xl overflow-hidden" style={{ maxWidth: '320px', width: '100%' }}>
                <video
                  className="w-full h-auto object-contain rounded-3xl"
                  src="/endoscopia.mp4"
                  autoPlay
                  muted
                  loop
                  playsInline
                />
              </div>
            </div>

          </div>
        </section>

        {/* RAZONES */}
        <section className="max-w-6xl mx-auto px-6 py-24">
          <h2 className="dv-reveal dv-title text-3xl md:text-5xl font-light text-center mb-4">
            Lo que encontrarás <span className="dv-accent">en la profundidad</span>
          </h2>
          <p className="dv-reveal dv-sub text-center max-w-2xl mx-auto mb-16">
            Seis razones para dejar atrás el papeleo y nadar hacia un servicio moderno.
          </p>

          <div className="grid lg:grid-cols-[1.15fr_1fr_1fr] gap-6 items-center">
            <div className="dv-reveal glass rounded-3xl overflow-hidden" style={{ transitionDelay: '520ms' }}>
              <video
                className="w-full h-full min-h-[500px] object-cover rounded-2xl"
                src="/enclaii-demo.mp4"
                autoPlay
                muted
                loop
                playsInline
              />
            </div>

            <div className="grid gap-6">
              {razones.slice(0, 3).map((r, i) => (
                <div key={r.t} className="flip-card dv-reveal" style={{ transitionDelay: `${i * 80}ms` }}>
                  <div className="flip-card-inner">
                    <div className="flip-card-front glass rounded-3xl p-7 flex flex-col">
                      <div className="w-12 h-12 rounded-2xl grid place-items-center mb-5 text-lg font-semibold"
                        style={{ background: 'var(--num-bg)', border: '1px solid var(--num-border)', color: 'var(--num-text)' }}>
                        {String(i + 1).padStart(2, '0')}
                      </div>
                      <h3 className="dv-title text-xl font-semibold mb-2">{r.t}</h3>
                      <p className="dv-sub leading-relaxed">{r.d}</p>
                    </div>
                    <div
                      className="flip-card-back rounded-3xl flex flex-col justify-center items-center text-center overflow-hidden"
                      style={getCardBackStyle(r.t)}
                    >
                      <div className="w-full h-full flex flex-col justify-center items-center p-7" style={{ background: 'var(--card-back-overlay)', backdropFilter: 'blur(2px)' }}>
                        <h3 className="dv-title text-lg font-bold" style={{ textShadow: '0 2px 10px var(--card-back-shadow)' }}>{r.t}</h3>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>

            <div className="grid gap-6">
              {razones.slice(3).map((r, i) => (
                <div key={r.t} className="flip-card dv-reveal" style={{ transitionDelay: `${(i + 3) * 80}ms` }}>
                  <div className="flip-card-inner">
                    <div className="flip-card-front glass rounded-3xl p-7 flex flex-col">
                      <div className="w-12 h-12 rounded-2xl grid place-items-center mb-5 text-lg font-semibold"
                        style={{ background: 'var(--num-bg)', border: '1px solid var(--num-border)', color: 'var(--num-text)' }}>
                        {String(i + 4).padStart(2, '0')}
                      </div>
                      <h3 className="dv-title text-xl font-semibold mb-2">{r.t}</h3>
                      <p className="dv-sub leading-relaxed">{r.d}</p>
                    </div>
                    <div
                      className="flip-card-back rounded-3xl flex flex-col justify-center items-center text-center overflow-hidden"
                      style={getCardBackStyle(r.t)}
                    >
                      <div className="w-full h-full flex flex-col justify-center items-center p-7" style={{ background: 'var(--card-back-overlay)', backdropFilter: 'blur(2px)' }}>
                        <h3 className="dv-title text-lg font-bold" style={{ textShadow: '0 2px 10px var(--card-back-shadow)' }}>{r.t}</h3>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* CTA */}
        <section className="max-w-6xl mx-auto px-6 pb-32">

          {/* Línea divisoria */}
          <div className="dv-reveal flex items-center gap-4 mb-16" style={{ transitionDelay: '0ms' }}>
            <div className="flex-1 h-px" style={{ background: 'linear-gradient(90deg, transparent, var(--g2))' }} />
            <span className="dv-accent text-xs font-medium tracking-[0.3em] uppercase px-3">el fondo</span>
            <div className="flex-1 h-px" style={{ background: 'linear-gradient(90deg, var(--g2), transparent)' }} />
          </div>

          {/* Texto izquierda + Botones derecha */}
          <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-10">

            {/* Texto izquierda */}
            <div className="dv-reveal max-w-xl" style={{ transitionDelay: '100ms' }}>
              <p className="dv-sub text-sm font-medium tracking-[0.25em] uppercase mb-4">Has llegado al fondo.</p>
              <h2 className="dv-title text-4xl md:text-5xl font-light leading-tight mb-6">
                Es momento de<br />
                <span className="dv-grad">salir a flote con ENCLAII.</span>
              </h2>
              <p className="dv-sub text-lg leading-relaxed">
                Agenda una demostración y descubre cómo se siente trabajar con una corriente a favor.
              </p>
            </div>

            {/* Botones derecha */}
            <div className="dv-reveal flex flex-col gap-8 lg:items-end" style={{ transitionDelay: '250ms' }}>
              <a href={MAILTO} target="_blank" rel="noopener noreferrer"
                className="dv-btn dv-btn-primary inline-flex items-center justify-center gap-2 px-10 py-4 rounded-2xl font-semibold text-base shadow-lg shadow-cyan-500/30">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Solicitar demo
              </a>
              <Link to="/contacto"
                className="dv-btn dv-btn-ghost inline-flex items-center justify-center gap-2 px-10 py-4 rounded-2xl font-medium text-base">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
                Ir a contacto
              </Link>
            </div>

          </div>

          {/* Línea final */}
          <div className="dv-reveal mt-16" style={{ transitionDelay: '400ms' }}>
            <div className="h-px" style={{ background: 'linear-gradient(90deg, transparent, var(--g2), transparent)' }} />
          </div>

        </section>
      </div>
    </div>
  )
}