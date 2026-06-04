import { useEffect, useRef } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

/* ── Capítulos de la historia de ENCLAII ── */
const CHAPTERS = [
  {
    num: '01',
    tag: 'El problema',
    title: 'Los estudios quedan\natrapados en sistemas\nfragmentados',
    text: 'Los equipos de endoscopia generan imágenes y video que terminan en servidores locales, CDs o sistemas incompatibles entre sí. El especialista pierde tiempo valioso buscando antecedentes y los pacientes esperan.',
    accent: '#ef4444',
    bg: 'radial-gradient(ellipse 70% 60% at 20% 50%, rgba(239,68,68,0.12) 0%, transparent 70%)',
    visual: <Visual1 />,
  },
  {
    num: '02',
    tag: 'La captura',
    title: 'ENCLAII se conecta\na tu equipo y captura\nen tiempo real',
    text: 'Sin pasos adicionales para el médico. ENCLAII se integra con tu torre endoscópica y digitaliza cada estudio de forma automática, asociándolo al expediente del paciente desde el primer segundo.',
    accent: '#2196f3',
    bg: 'radial-gradient(ellipse 70% 60% at 20% 50%, rgba(33,150,243,0.14) 0%, transparent 70%)',
    visual: <Visual2 />,
  },
  {
    num: '03',
    tag: 'Inteligencia Artificial',
    title: 'La IA detecta\nlo que el ojo\npuede perder',
    text: 'Los modelos de inteligencia artificial de ENCLAII analizan cada frame en tiempo real. Marcan automáticamente hallazgos relevantes como pólipos, lesiones o irregularidades, apoyando el criterio del especialista con evidencia objetiva.',
    accent: '#a855f7',
    bg: 'radial-gradient(ellipse 70% 60% at 20% 50%, rgba(168,85,247,0.14) 0%, transparent 70%)',
    visual: <Visual3 />,
  },
  {
    num: '04',
    tag: 'El resultado',
    title: 'Reportes completos\nal instante, desde\ncualquier lugar',
    text: 'Con ENCLAII, el especialista genera reportes clínicos estructurados en segundos. Accesibles desde cualquier dispositivo, compartibles con el paciente o referido de forma segura. Mejor atención, más rápida.',
    accent: '#10b981',
    bg: 'radial-gradient(ellipse 70% 60% at 20% 50%, rgba(16,185,129,0.12) 0%, transparent 70%)',
    visual: <Visual4 />,
  },
]

export default function ScrollStory() {
  const sectionRef = useRef(null)

  useEffect(() => {
    const ctx = gsap.context(() => {
      const panels  = gsap.utils.toArray('.ss-panel',  sectionRef.current)
      const bgs     = gsap.utils.toArray('.ss-bg',     sectionRef.current)
      const dots    = gsap.utils.toArray('.ss-dot',    sectionRef.current)
      const visuals = gsap.utils.toArray('.ss-visual', sectionRef.current)

      /* Estado inicial: todo oculto salvo capítulo 0 */
      gsap.set(panels.slice(1),  { opacity: 0, y: 50 })
      gsap.set(visuals.slice(1), { opacity: 0, scale: 0.85 })
      gsap.set(bgs.slice(1),     { opacity: 0 })
      gsap.set(dots,             { scale: 1, opacity: 0.25 })
      gsap.set(dots[0],          { scale: 1.8, opacity: 1 })

      /* Timeline maestro pineado + scrub + snap */
      const tl = gsap.timeline({
        scrollTrigger: {
          trigger: sectionRef.current,
          start: 'top top',
          end: () => `+=${(CHAPTERS.length - 1) * window.innerHeight}`,
          pin: true,
          pinSpacing: true,
          scrub: 1,
          anticipatePin: 1,
          invalidateOnRefresh: true,
          snap: {
            snapTo: 1 / (CHAPTERS.length - 1),
            duration: { min: 0.2, max: 0.5 },
            delay: 0.08,
            ease: 'power2.inOut',
          },
        },
      })

      /* Construye crossfades capítulo a capítulo */
      for (let i = 0; i < CHAPTERS.length - 1; i++) {
        const out = i
        const inn = i + 1

        tl.to(panels[out],  { opacity: 0, y: -50,     duration: 0.5, ease: 'power2.in' }, i)
          .to(visuals[out], { opacity: 0, scale: 0.85, duration: 0.5, ease: 'power2.in' }, i)
          .to(bgs[out],     { opacity: 0,              duration: 0.5 }, i)
          .to(dots[out],    { scale: 1, opacity: 0.25, duration: 0.4 }, i)

        tl.fromTo(panels[inn],
          { opacity: 0, y: 50 },
          { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' }, i + 0.4)
         .fromTo(visuals[inn],
          { opacity: 0, scale: 0.85 },
          { opacity: 1, scale: 1, duration: 0.5, ease: 'power2.out' }, i + 0.4)
         .to(bgs[inn],  { opacity: 1, duration: 0.5 }, i + 0.4)
         .to(dots[inn], { scale: 1.8, opacity: 1, duration: 0.4 }, i + 0.4)
      }
    }, sectionRef)

    return () => ctx.revert()
  }, [])

  return (
    <section ref={sectionRef} id="story" className="relative">
      <div className="h-screen overflow-hidden relative">

        {/* Fondos por capítulo */}
        {CHAPTERS.map((ch, i) => (
          <div
            key={i}
            className="ss-bg absolute inset-0"
            style={{ background: `${ch.bg}, linear-gradient(135deg, #050d1f 0%, #0a1733 100%)` }}
          />
        ))}

        {/* Grid decorativo fijo */}
        <div
          className="absolute inset-0 pointer-events-none"
          style={{
            backgroundImage: 'linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px)',
            backgroundSize: '80px 80px',
            opacity: 0.02,
          }}
        />

        {/* Layout principal */}
        <div className="relative z-10 h-full grid grid-cols-1 md:grid-cols-2 max-w-6xl mx-auto px-6">

          {/* Columna izquierda: texto */}
          <div className="relative flex items-center py-24">
            {CHAPTERS.map((ch, i) => (
              <div key={i} className="ss-panel absolute max-w-lg pr-8">
                <div className="flex items-center gap-3 mb-6">
                  <span
                    className="text-xs font-bold tracking-[0.35em] uppercase"
                    style={{ color: ch.accent }}
                  >
                    {ch.num}
                  </span>
                  <div className="h-px w-8" style={{ background: ch.accent }} />
                  <span className="text-xs tracking-widest text-slate-500 uppercase">{ch.tag}</span>
                </div>

                <h2 className="text-3xl md:text-5xl font-light text-white leading-tight mb-6 whitespace-pre-line">
                  {ch.title}
                </h2>

                <p className="text-slate-400 text-base md:text-lg leading-relaxed">
                  {ch.text}
                </p>

                <div className="flex items-center gap-2 mt-8 text-xs text-slate-600 uppercase tracking-widest">
                  <span>{i + 1}</span>
                  <span>/</span>
                  <span>{CHAPTERS.length}</span>
                </div>
              </div>
            ))}
          </div>

          {/* Columna derecha: visual */}
          <div className="hidden md:flex items-center justify-center py-24 relative">
            {CHAPTERS.map((ch, i) => (
              <div key={i} className="ss-visual absolute inset-0 flex items-center justify-center">
                {ch.visual}
              </div>
            ))}
          </div>
        </div>

        {/* Puntos de progreso lateral */}
        <div className="absolute right-6 md:right-10 top-1/2 -translate-y-1/2 flex flex-col gap-4 z-20">
          {CHAPTERS.map((ch, i) => (
            <button
              key={i}
              className="ss-dot w-2 h-2 rounded-full transition-colors"
              style={{ background: ch.accent }}
              aria-label={`Ir al capítulo ${i + 1}`}
            />
          ))}
        </div>

        {/* Nombre de sección fijo arriba */}
        <div className="absolute top-0 left-0 right-0 h-20 flex items-end justify-center pb-4 z-10 pointer-events-none">
          <span className="text-[10px] text-slate-600 tracking-[0.5em] uppercase">Historia de ENCLAII</span>
        </div>
      </div>
    </section>
  )
}

/* ════════════════════════════════════════════════
   Visuales SVG animados por capítulo
════════════════════════════════════════════════ */

/* Capítulo 1 — Fragmentado, caótico */
function Visual1() {
  const nodes = [
    { cx: 80,  cy: 80  }, { cx: 220, cy: 60  }, { cx: 150, cy: 150 },
    { cx: 60,  cy: 220 }, { cx: 240, cy: 210 }, { cx: 310, cy: 130 },
    { cx: 130, cy: 290 }, { cx: 280, cy: 300 },
  ]
  return (
    <svg viewBox="0 0 380 380" className="w-72 h-72 md:w-96 md:h-96">
      <defs>
        <filter id="glow1">
          <feGaussianBlur stdDeviation="3" result="blur" />
          <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
        </filter>
      </defs>
      {nodes.slice(0, 5).map((n, i) => (
        <line key={i}
          x1={n.cx} y1={n.cy}
          x2={nodes[(i + 2) % nodes.length].cx}
          y2={nodes[(i + 2) % nodes.length].cy}
          stroke="#ef4444" strokeWidth="1" strokeDasharray="6 8" opacity="0.25"
        />
      ))}
      {nodes.map((n, i) => (
        <g key={i} filter="url(#glow1)">
          <circle cx={n.cx} cy={n.cy} r="10" fill="none" stroke="#ef4444" strokeWidth="1.5" opacity="0.5" />
          <circle cx={n.cx} cy={n.cy} r="3" fill="#ef4444" opacity="0.7">
            <animate attributeName="opacity" values="0.7;0.3;0.7" dur={`${1.5 + i * 0.3}s`} repeatCount="indefinite" />
          </circle>
          <line x1={n.cx - 5} y1={n.cy - 5} x2={n.cx + 5} y2={n.cy + 5} stroke="#ef4444" strokeWidth="1" opacity="0.5" />
          <line x1={n.cx + 5} y1={n.cy - 5} x2={n.cx - 5} y2={n.cy + 5} stroke="#ef4444" strokeWidth="1" opacity="0.5" />
        </g>
      ))}
      <text x="50" y="40" fill="#ef4444" fontSize="9" opacity="0.4" fontFamily="monospace">DICOM local</text>
      <text x="190" y="35" fill="#ef4444" fontSize="9" opacity="0.4" fontFamily="monospace">CD sin leer</text>
      <text x="260" y="95" fill="#ef4444" fontSize="9" opacity="0.4" fontFamily="monospace">Sistema A</text>
      <text x="20" y="260" fill="#ef4444" fontSize="9" opacity="0.4" fontFamily="monospace">Sistema B</text>
    </svg>
  )
}

/* Capítulo 2 — Captura, conectado */
function Visual2() {
  return (
    <svg viewBox="0 0 380 380" className="w-72 h-72 md:w-96 md:h-96">
      <defs>
        <filter id="glow2">
          <feGaussianBlur stdDeviation="4" result="blur" />
          <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
        </filter>
        <radialGradient id="rg2" cx="50%" cy="50%" r="50%">
          <stop offset="0%" stopColor="#2196f3" stopOpacity="0.3" />
          <stop offset="100%" stopColor="#2196f3" stopOpacity="0" />
        </radialGradient>
      </defs>
      <circle cx="190" cy="190" r="100" fill="url(#rg2)" />
      {[60, 90, 120].map((r, i) => (
        <circle key={i} cx="190" cy="190" r={r} fill="none" stroke="#2196f3" strokeWidth="1" opacity="0.2">
          <animate attributeName="r" values={`${r};${r + 8};${r}`} dur={`${2 + i * 0.5}s`} repeatCount="indefinite" />
          <animate attributeName="opacity" values="0.2;0.05;0.2" dur={`${2 + i * 0.5}s`} repeatCount="indefinite" />
        </circle>
      ))}
      <g filter="url(#glow2)" transform="translate(140, 155)">
        <path d="M30 40 Q10 40 10 25 Q10 10 25 10 Q28 0 40 2 Q50 -5 58 8 Q70 6 70 20 Q70 35 55 35 Z"
          fill="none" stroke="#2196f3" strokeWidth="2" />
        <line x1="35" y1="28" x2="35" y2="50" stroke="#2196f3" strokeWidth="1.5" opacity="0.6" />
        <polyline points="28,44 35,50 42,44" fill="none" stroke="#2196f3" strokeWidth="1.5" opacity="0.6" />
      </g>
      {[[60, 100], [320, 100], [60, 280], [320, 280]].map(([x, y], i) => (
        <g key={i}>
          <line x1={x} y1={y} x2="190" y2="190" stroke="#2196f3" strokeWidth="1" opacity="0.3" strokeDasharray="4 4">
            <animate attributeName="stroke-dashoffset" values="0;-16" dur="1s" repeatCount="indefinite" />
          </line>
          <circle cx={x} cy={y} r="6" fill="none" stroke="#2196f3" strokeWidth="1.5" opacity="0.6" />
          <circle cx={x} cy={y} r="2.5" fill="#2196f3" opacity="0.8" />
        </g>
      ))}
      <text x="130" y="345" fill="#2196f3" fontSize="10" opacity="0.5" fontFamily="monospace" textAnchor="middle">Sincronización automática</text>
    </svg>
  )
}

/* Capítulo 3 — IA, escáner */
function Visual3() {
  const highlights = [[150, 140], [220, 170], [170, 220], [250, 240]]
  return (
    <svg viewBox="0 0 380 380" className="w-72 h-72 md:w-96 md:h-96">
      <defs>
        <clipPath id="screen3"><rect x="80" y="70" width="220" height="220" rx="8" /></clipPath>
        <filter id="glow3"><feGaussianBlur stdDeviation="3" result="b" /><feMerge><feMergeNode in="b" /><feMergeNode in="SourceGraphic" /></feMerge></filter>
      </defs>
      <rect x="80" y="70" width="220" height="220" rx="8" fill="none" stroke="#a855f7" strokeWidth="1.5" opacity="0.5" />
      {[100, 130, 160, 190, 220, 250, 280].map(v => (
        <g key={v}>
          <line x1="80" y1={v} x2="300" y2={v} stroke="#a855f7" strokeWidth="0.5" opacity="0.12" clipPath="url(#screen3)" />
          <line x1={v + 10} y1="70" x2={v + 10} y2="290" stroke="#a855f7" strokeWidth="0.5" opacity="0.12" clipPath="url(#screen3)" />
        </g>
      ))}
      <line x1="80" y1="160" x2="300" y2="160" stroke="#a855f7" strokeWidth="2" opacity="0.7" clipPath="url(#screen3)">
        <animate attributeName="y1" values="80;280;80" dur="3s" repeatCount="indefinite" />
        <animate attributeName="y2" values="80;280;80" dur="3s" repeatCount="indefinite" />
        <animate attributeName="opacity" values="0.7;0.3;0.7" dur="3s" repeatCount="indefinite" />
      </line>
      {highlights.map(([x, y], i) => (
        <g key={i} filter="url(#glow3)">
          <rect x={x - 12} y={y - 12} width="24" height="24" rx="3"
            fill="none" stroke="#a855f7" strokeWidth="1.5" opacity="0.8" clipPath="url(#screen3)">
            <animate attributeName="opacity" values="0.8;0.3;0.8" dur={`${1 + i * 0.4}s`} repeatCount="indefinite" />
          </rect>
          <circle cx={x} cy={y} r="2.5" fill="#a855f7" clipPath="url(#screen3)">
            <animate attributeName="opacity" values="1;0.4;1" dur={`${1 + i * 0.4}s`} repeatCount="indefinite" />
          </circle>
        </g>
      ))}
      <g filter="url(#glow3)">
        <rect x="90" y="80" width="36" height="16" rx="4" fill="#a855f7" opacity="0.2" />
        <text x="108" y="92" fill="#a855f7" fontSize="9" textAnchor="middle" fontFamily="monospace" fontWeight="bold">IA</text>
      </g>
      <text x="190" y="320" fill="#a855f7" fontSize="10" opacity="0.5" fontFamily="monospace" textAnchor="middle">Confianza: 94.7%</text>
    </svg>
  )
}

/* Capítulo 4 — Reporte, resultado limpio */
function Visual4() {
  return (
    <svg viewBox="0 0 380 380" className="w-72 h-72 md:w-96 md:h-96">
      <defs>
        <filter id="glow4"><feGaussianBlur stdDeviation="3" result="b" /><feMerge><feMergeNode in="b" /><feMergeNode in="SourceGraphic" /></feMerge></filter>
      </defs>
      <rect x="100" y="60" width="180" height="240" rx="8" fill="none" stroke="#10b981" strokeWidth="1.5" opacity="0.6" />
      <rect x="100" y="60" width="180" height="40" rx="8" fill="#10b981" opacity="0.08" />
      <text x="190" y="86" fill="#10b981" fontSize="10" textAnchor="middle" fontFamily="monospace" opacity="0.8">REPORTE ENDOSCÓPICO</text>
      {[130, 150, 165, 195, 210, 225, 250, 265, 280].map((y, i) => (
        <rect key={i} x={i % 3 === 0 ? 120 : 120} y={y}
          width={i % 3 === 0 ? 140 : i % 3 === 1 ? 100 : 120}
          height="6" rx="3" fill="#10b981" opacity={i % 3 === 0 ? 0.25 : 0.12} />
      ))}
      <g filter="url(#glow4)" transform="translate(155, 170)">
        <circle cx="35" cy="35" r="28" fill="none" stroke="#10b981" strokeWidth="2" opacity="0.7" />
        <polyline points="20,35 30,45 50,22" fill="none" stroke="#10b981" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
          <animate attributeName="stroke-dasharray" values="0,60;60,0" dur="0.8s" fill="freeze" begin="0.5s" />
        </polyline>
      </g>
      {[[80, 340], [190, 350], [300, 340]].map(([x, y], i) => (
        <g key={i}>
          <circle cx={x} cy={y} r="14" fill="none" stroke="#10b981" strokeWidth="1" opacity="0.4" />
          <text x={x} y={y + 4} fill="#10b981" fontSize="7" textAnchor="middle" opacity="0.6" fontFamily="monospace">
            {['Email', 'PDF', 'QR'][i]}
          </text>
        </g>
      ))}
      <line x1="94" y1="340" x2="176" y2="350" stroke="#10b981" strokeWidth="1" strokeDasharray="3 3" opacity="0.3" />
      <line x1="204" y1="350" x2="286" y2="340" stroke="#10b981" strokeWidth="1" strokeDasharray="3 3" opacity="0.3" />
    </svg>
  )
}