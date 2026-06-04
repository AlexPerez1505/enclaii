import { useEffect, useRef } from 'react'
import { Link } from 'react-router-dom'

const MAILTO = 'mailto:licencias@cizcalli.gob.mx?subject=Quiero%20SIMED'

const razones = [
  { t: 'Integración total', d: 'SIMED se acopla a tu flujo actual de endoscopia sin frenar a tu equipo.' },
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
      cx = lerp(cx, mx, 0.06)
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

        .dv-title { color: var(--title); }
        .dv-sub { color: var(--sub); }
        .dv-accent { color: var(--accent-text); }

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
        <section className="relative min-h-screen flex flex-col items-center justify-center px-6 text-center">
          <div className="pointer-events-none absolute inset-0">
            <div data-speed="0.45" data-mouse="40" className="panel"
              style={{ width: 220, height: 150, top: '12%', left: '8%',
                backgroundImage: `${overlay}, url('/panel1.jpg')`, animationDelay: '0s' }} />
            <div data-speed="0.28" data-mouse="-30" className="panel"
              style={{ width: 180, height: 250, top: '20%', right: '10%',
                backgroundImage: `${overlay}, url('/panel2.jpg')`, animationDelay: '1.2s' }} />
            <div data-speed="0.6" data-mouse="55" className="panel"
              style={{ width: 150, height: 150, top: '40%', left: '16%',
                backgroundImage: `${overlay}, url('/panel3.jpg')`, animationDelay: '2s' }} />
            <div data-speed="0.36" data-mouse="-45" className="panel"
              style={{ width: 200, height: 130, top: '8%', left: '40%',
                backgroundImage: `${overlay}, url('/panel4.jpg')`, animationDelay: '0.6s' }} />
            <div data-speed="0.52" data-mouse="35" className="panel"
              style={{ width: 130, height: 130, bottom: '14%', right: '18%',
                backgroundImage: `${overlay}, url('/panel5.jpg')`, animationDelay: '1.6s' }}>      
            </div>
          </div>

          <span className="dv-reveal dv-accent inline-flex items-center gap-2 text-xs font-medium tracking-[0.3em] uppercase mb-6 px-4 py-1.5 rounded-full glass">
            <span className="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse" />
            Sumérgete en SIMED
          </span>
          <h1 className="dv-reveal dv-title text-4xl md:text-7xl font-light leading-[1.02] tracking-tight max-w-4xl">
            ¿Por qué tu institución
            <br />
            <span className="dv-grad">debería sumergirse con nosotros?</span>
          </h1>
          <p className="dv-reveal dv-sub mt-6 text-lg md:text-xl max-w-2xl leading-relaxed">
            Desciende y descubre cómo SIMED transforma tu servicio de endoscopia,
            capa por capa, sin perder de vista lo esencial.
          </p>

          <div className="dv-reveal dv-sub mt-10 flex flex-col sm:flex-row items-center gap-2 text-sm">
            <svg className="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
            Desplázate para bucear
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

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {razones.map((r, i) => (
              <div key={r.t} className="dv-reveal glass rounded-3xl p-7" style={{ transitionDelay: `${i * 80}ms` }}>
                <div className="w-12 h-12 rounded-2xl grid place-items-center mb-5 text-lg font-semibold"
                  style={{ background: 'var(--num-bg)', border: '1px solid var(--num-border)', color: 'var(--num-text)' }}>
                  {String(i + 1).padStart(2, '0')}
                </div>
                <h3 className="dv-title text-xl font-medium mb-2">{r.t}</h3>
                <p className="dv-sub leading-relaxed">{r.d}</p>
              </div>
            ))}
          </div>
        </section>

        {/* CTA */}
        <section className="max-w-3xl mx-auto px-6 pb-32 text-center">
          <div className="dv-reveal glass rounded-3xl p-10 md:p-14">
            <h2 className="dv-title text-3xl md:text-4xl font-light mb-4">
              Has llegado al fondo.
              <br />
              <span className="dv-grad">Es momento de salir a flote con SIMED.</span>
            </h2>
            <p className="dv-sub mb-8 max-w-xl mx-auto">
              Agenda una demostración y descubre cómo se siente trabajar con una corriente a favor.
            </p>
            <div className="flex flex-col sm:flex-row justify-center gap-3">
              <a href={MAILTO} target="_blank" rel="noopener noreferrer"
                className="dv-btn dv-btn-primary inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl font-semibold shadow-lg shadow-cyan-500/30">
                Solicitar demo
              </a>
              <Link to="/contacto"
                className="dv-btn dv-btn-ghost inline-flex items-center justify-center px-8 py-4 rounded-xl font-medium">
                Ir a contacto
              </Link>
            </div>
          </div>
        </section>
      </div>
    </div>
  )
}