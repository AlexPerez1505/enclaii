import { useEffect, useRef, useState, useCallback } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

const PASOS = [
  {
    n: '01',
    title: 'Conecta el equipo',
    text: 'SIMED se integra con tu torre endoscópica. La captura inicia automáticamente, sin pasos adicionales para el médico o el técnico.',
    features: ['Compatible con marcas principales', 'Instalación en 1 día', 'Sin cambiar tu flujo actual'],
    color: '#2196f3',
    icon: (
      <svg viewBox="0 0 64 64" className="w-12 h-12" fill="none" stroke="currentColor" strokeWidth="1.5">
        <rect x="8" y="20" width="20" height="28" rx="3" />
        <rect x="36" y="14" width="20" height="36" rx="3" />
        <line x1="28" y1="34" x2="36" y2="34" />
        <circle cx="48" cy="32" r="5" />
        <circle cx="18" cy="34" r="5" />
      </svg>
    ),
    image: '/Conexion.png',
  },
  {
    n: '02',
    title: 'Captura el estudio',
    text: 'Imágenes y video se digitalizan en tiempo real y se asocian automáticamente al expediente del paciente desde el primer fotograma.',
    features: ['Alta resolución 4K', 'Video en tiempo real', 'Metadatos automáticos'],
    color: '#06b6d4',
    icon: (
      <svg viewBox="0 0 64 64" className="w-12 h-12" fill="none" stroke="currentColor" strokeWidth="1.5">
        <circle cx="32" cy="32" r="16" />
        <circle cx="32" cy="32" r="6" />
        <line x1="32" y1="10" x2="32" y2="16" />
        <line x1="32" y1="48" x2="32" y2="54" />
        <line x1="10" y1="32" x2="16" y2="32" />
        <line x1="48" y1="32" x2="54" y2="32" />
      </svg>
    ),
  },
  {
    n: '03',
    title: 'IA analiza hallazgos',
    text: 'Los modelos de inteligencia artificial analizan cada frame. Destacan automáticamente pólipos, lesiones o irregularidades para revisión del especialista.',
    features: ['Análisis en tiempo real', 'Alta sensibilidad diagnóstica', 'Aprendizaje continuo'],
    color: '#a855f7',
    icon: (
      <svg viewBox="0 0 64 64" className="w-12 h-12" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M20 44 L32 16 L44 44" />
        <line x1="24" y1="36" x2="40" y2="36" />
        <circle cx="32" cy="54" r="4" />
        <path d="M14 20 Q10 32 14 44" />
        <path d="M50 20 Q54 32 50 44" />
      </svg>
    ),
  },
  {
    n: '04',
    title: 'Genera el reporte',
    text: 'Crea reportes clínicos estructurados en segundos. Compártelos con el paciente o médico referido vía correo, PDF o enlace seguro con QR.',
    features: ['Plantillas clínicas', 'Firma digital incluida', 'Acceso 24/7 en la nube'],
    color: '#10b981',
    icon: (
      <svg viewBox="0 0 64 64" className="w-12 h-12" fill="none" stroke="currentColor" strokeWidth="1.5">
        <rect x="14" y="8" width="36" height="48" rx="4" />
        <line x1="22" y1="24" x2="42" y2="24" />
        <line x1="22" y1="32" x2="38" y2="32" />
        <line x1="22" y1="40" x2="36" y2="40" />
        <polyline points="24,16 28,20 36,12" />
      </svg>
    ),
  },
]

function ConexionImagen({ src, alt }) {
  return (
    <div className="cf-img-wrap">
      <div className="cf-img-glow" aria-hidden />
      <div className="cf-img-frame">
        <img src={src} alt={alt} width={440} height={440} loading="lazy" decoding="async" />
      </div>
    </div>
  )
}

const FlipHint = ({ label }) => (
  <span className="cf-flip-hint mt-5 inline-flex items-center gap-1.5 text-xs text-slate-500">
    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden>
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
    </svg>
    {label}
  </span>
)

function PasoInfoFlipCard({ paso }) {
  const [flipped, setFlipped] = useState(false)
  const [paused, setPaused] = useState(false)
  const intervalRef = useRef(null)

  const toggle = useCallback(() => setFlipped(f => !f), [])

  useEffect(() => {
    if (paused) return undefined
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches
    if (prefersReduced) return undefined

    intervalRef.current = window.setInterval(() => {
      setFlipped(f => !f)
    }, 5200)

    return () => {
      if (intervalRef.current) window.clearInterval(intervalRef.current)
    }
  }, [paused])

  return (
    <div
      className="cf-flip w-full"
      onMouseEnter={() => setPaused(true)}
      onMouseLeave={() => setPaused(false)}
    >
      <button
        type="button"
        className={`cf-flip-inner${flipped ? ' is-flipped' : ''}`}
        onClick={toggle}
        aria-pressed={flipped}
        aria-label={flipped ? 'Voltear y ver descripción general' : 'Voltear y ver beneficios'}
      >
        {/* Frente: descripción principal */}
        <div className="cf-flip-face cf-flip-front">
          <div className="cf-flip-content">
            <span
              className="absolute top-3 right-4 text-6xl md:text-7xl font-bold leading-none select-none pointer-events-none"
              style={{ color: paso.color, opacity: 0.08 }}
              aria-hidden
            >
              {paso.n}
            </span>
            <div className="mb-4" style={{ color: paso.color }}>{paso.icon}</div>
            <span className="text-[10px] tracking-[0.35em] uppercase" style={{ color: paso.color }}>
              Paso {paso.n}
            </span>
            <h3 className="text-2xl md:text-4xl font-light text-white mt-2 mb-4 leading-tight">
              {paso.title}
            </h3>
            <p className="text-slate-400 text-sm md:text-base leading-relaxed border-l-2 pl-5" style={{ borderColor: paso.color }}>
              {paso.text}
            </p>
            <FlipHint label="Toca para ver beneficios" />
          </div>
        </div>

        {/* Reverso: beneficios / otra información */}
        <div className="cf-flip-face cf-flip-back">
          <div className="cf-flip-content">
            <span className="text-[10px] tracking-[0.35em] uppercase" style={{ color: paso.color }}>
              Beneficios clave
            </span>
            <h3 className="text-xl md:text-2xl font-light text-white mt-2 mb-5 leading-tight">
              ¿Por qué conectar con SIMED?
            </h3>
            <ul className="space-y-3 text-left">
              {paso.features.map(f => (
                <li key={f} className="flex items-start gap-3 text-sm md:text-base text-slate-300">
                  <span
                    className="w-2 h-2 rounded-full shrink-0 mt-1.5"
                    style={{ background: paso.color }}
                  />
                  {f}
                </li>
              ))}
            </ul>
            <p className="text-slate-500 text-xs md:text-sm mt-5 leading-relaxed">
              Integración sin interrumpir tu rutina clínica: el equipo sigue operando igual, con respaldo digital automático.
            </p>
            <FlipHint label="Toca para volver" />
          </div>
        </div>
      </button>
    </div>
  )
}

export default function ComoFunciona() {
  const sectionRef = useRef(null)
  const trackRef   = useRef(null)

  useEffect(() => {
    const ctx = gsap.context(() => {
      /* ── Scroll horizontal con pin de GSAP (no sticky CSS) ── */
      const horizontal = gsap.to(trackRef.current, {
        x: () => -(trackRef.current.scrollWidth - window.innerWidth),
        ease: 'none',
        scrollTrigger: {
          trigger: sectionRef.current,
          start: 'top top',
          end: () => `+=${trackRef.current.scrollWidth - window.innerWidth}`,
          pin: true,
          pinSpacing: true,
          scrub: 1,
          anticipatePin: 1,
          invalidateOnRefresh: true,
          snap: {
            snapTo: 1 / (PASOS.length - 1),
            duration: { min: 0.2, max: 0.5 },
            delay: 0.08,
            ease: 'power2.inOut',
          },
        },
      })

      /* Header se desvanece al iniciar el scroll horizontal */
      gsap.to('.cf-header', {
        opacity: 0,
        y: -20,
        ease: 'none',
        scrollTrigger: {
          trigger: sectionRef.current,
          start: 'top top',
          end: () => `+=${window.innerHeight * 0.3}`,
          scrub: true,
        },
      })

      /* Barra de progreso */
      gsap.to('.cf-progress-bar', {
        scaleX: 1,
        ease: 'none',
        scrollTrigger: {
          trigger: sectionRef.current,
          start: 'top top',
          end: () => `+=${trackRef.current.scrollWidth - window.innerWidth}`,
          scrub: true,
        },
      })

      return () => horizontal.scrollTrigger?.kill()
    }, sectionRef)

    return () => ctx.revert()
  }, [])

  return (
    <section ref={sectionRef} id="como-funciona" className="relative">
      <style>{`
        @keyframes cf-img-float {
          0%, 100% { transform: translateY(0) rotate(0deg); }
          50% { transform: translateY(-14px) rotate(0.6deg); }
        }
        @keyframes cf-img-glow {
          0%, 100% { opacity: 0.35; transform: scale(0.95); }
          50% { opacity: 0.65; transform: scale(1.08); }
        }
        @keyframes cf-img-shine {
          0% { transform: translateX(-120%) skewX(-12deg); }
          100% { transform: translateX(220%) skewX(-12deg); }
        }
        .cf-img-wrap {
          position: relative;
          width: 100%;
          max-width: min(100%, 20rem);
          margin-inline: auto;
        }
        @media (min-width: 768px) {
          .cf-img-wrap { max-width: 18rem; margin-inline: 0; }
        }
        @media (min-width: 1024px) {
          .cf-img-wrap { max-width: 22rem; }
        }
        .cf-img-glow {
          position: absolute;
          inset: 8%;
          border-radius: 1.25rem;
          background: radial-gradient(ellipse at 50% 50%, rgba(33,150,243,0.45), transparent 68%);
          animation: cf-img-glow 4.5s ease-in-out infinite;
          pointer-events: none;
        }
        .cf-img-frame {
          position: relative;
          z-index: 1;
          overflow: hidden;
          border-radius: 1rem;
          border: 1px solid rgba(33,150,243,0.25);
          box-shadow: 0 24px 48px -16px rgba(33,150,243,0.35);
          animation: cf-img-float 4s ease-in-out infinite;
          will-change: transform;
        }
        .cf-img-frame::after {
          content: '';
          position: absolute;
          inset: 0;
          background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,0.12) 50%, transparent 60%);
          animation: cf-img-shine 5s ease-in-out infinite;
          pointer-events: none;
        }
        .cf-img-frame img {
          display: block;
          width: 100%;
          height: auto;
          object-fit: contain;
        }
        .cf-flip { perspective: 1400px; }
        .cf-flip-inner {
          position: relative;
          display: block;
          width: 100%;
          min-height: 20rem;
          aspect-ratio: 5 / 6;
          transform-style: preserve-3d;
          transition: transform 0.85s cubic-bezier(0.4, 0.15, 0.2, 1);
          cursor: pointer;
          border: none;
          padding: 0;
          background: transparent;
          text-align: left;
        }
        @media (min-width: 768px) {
          .cf-flip-inner { min-height: 22rem; aspect-ratio: auto; height: 100%; }
        }
        .cf-flip-inner.is-flipped { transform: rotateY(180deg); }
        .cf-flip-inner:focus-visible {
          outline: 2px solid #2196f3;
          outline-offset: 4px;
          border-radius: 1.25rem;
        }
        .cf-flip-face {
          position: absolute;
          inset: 0;
          border-radius: 1.25rem;
          backface-visibility: hidden;
          -webkit-backface-visibility: hidden;
          overflow: hidden;
          border: 1px solid rgba(33,150,243,0.28);
          box-shadow: 0 28px 56px -20px rgba(33,150,243,0.35);
        }
        .cf-flip-front {
          background: linear-gradient(160deg, rgba(10,23,51,0.95) 0%, rgba(5,13,31,0.98) 100%);
        }
        .cf-flip-back {
          transform: rotateY(180deg);
          background: linear-gradient(160deg, rgba(5,13,31,0.98) 0%, rgba(10,30,60,0.95) 100%);
        }
        .cf-flip-content {
          position: relative;
          height: 100%;
          padding: 1.5rem;
          display: flex;
          flex-direction: column;
          justify-content: center;
        }
        @media (min-width: 768px) {
          .cf-flip-content { padding: 2rem 2rem 2rem 2.25rem; }
        }
        .cf-flip-hint { animation: cf-flip-hint-pulse 2.5s ease-in-out infinite; }
        @keyframes cf-flip-hint-pulse {
          0%, 100% { opacity: 0.55; }
          50% { opacity: 1; }
        }
        @media (prefers-reduced-motion: reduce) {
          .cf-flip-inner { transition-duration: 0.01ms; }
          .cf-img-glow, .cf-img-frame, .cf-img-frame::after, .cf-flip-hint { animation: none !important; }
        }
      `}</style>
      <div className="h-screen overflow-hidden bg-gradient-to-b from-[#050d1f] to-[#0a1733] relative">

        {/* Barra de progreso superior */}
        <div className="absolute top-0 left-0 right-0 h-0.5 bg-white/5 z-20">
          <div
            className="cf-progress-bar h-full bg-gradient-to-r from-[#2196f3] to-[#10b981] origin-left"
            style={{ transform: 'scaleX(0)' }}
          />
        </div>

        {/* Header */}
        <div className="cf-header absolute top-0 left-0 right-0 z-10 text-center pt-10">
          <span className="text-[10px] text-[#2196f3] tracking-[0.4em] uppercase">Flujo de trabajo</span>
          <h2 className="text-2xl md:text-4xl font-light text-white mt-2">Cómo funciona SIMED</h2>
        </div>

        {/* Track horizontal */}
        <div
          ref={trackRef}
          className="flex h-full items-center will-change-transform"
          style={{ width: `${PASOS.length * 100}vw` }}
        >
          {PASOS.map((paso, i) => (
            <div
              key={paso.n}
              className="flex-shrink-0 w-screen h-full flex items-center justify-center px-8 md:px-24"
            >
              <div
                className={`w-full relative ${
                  paso.image
                    ? 'max-w-5xl flex flex-col md:flex-row md:items-center gap-8 md:gap-12'
                    : 'max-w-xl'
                }`}
              >
                {paso.image && (
                  <>
                    <div className="w-full md:w-[42%] shrink-0 flex justify-center md:justify-start order-first">
                      <ConexionImagen
                        src={paso.image}
                        alt="Conexión del equipo endoscópico con SIMED"
                      />
                    </div>
                    <div className="flex-1 min-w-0 w-full relative">
                      <PasoInfoFlipCard paso={paso} />
                      <div className="absolute right-0 top-1/2 -translate-y-1/2 text-slate-600 animate-pulse hidden md:block pointer-events-none">
                        <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 5l7 7-7 7" />
                        </svg>
                      </div>
                    </div>
                  </>
                )}
                {!paso.image && (
                <>
                {/* Número grande decorativo */}
                <div
                  className="text-[8rem] md:text-[11rem] font-bold leading-none mb-4 select-none"
                  style={{ color: paso.color, opacity: 0.08 }}
                >
                  {paso.n}
                </div>

                {/* Icono + contenido */}
                <div className="border-l-2 pl-8 -mt-6" style={{ borderColor: paso.color }}>
                  <div className="mb-5" style={{ color: paso.color }}>
                    {paso.icon}
                  </div>

                  <h3 className="text-3xl md:text-5xl font-light text-white mb-5 leading-tight">
                    {paso.title}
                  </h3>

                  <p className="text-slate-400 text-base md:text-lg leading-relaxed mb-6 max-w-md">
                    {paso.text}
                  </p>

                  <ul className="space-y-2.5">
                    {paso.features.map(f => (
                      <li key={f} className="flex items-center gap-3 text-sm text-slate-400">
                        <div className="w-1.5 h-1.5 rounded-full flex-shrink-0" style={{ background: paso.color }} />
                        {f}
                      </li>
                    ))}
                  </ul>
                </div>

                {/* Flecha siguiente */}
                {i < PASOS.length - 1 && (
                  <div className="absolute right-0 top-1/2 -translate-y-1/2 text-slate-600 animate-pulse hidden md:block">
                    <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 5l7 7-7 7" />
                    </svg>
                  </div>
                )}
                </>
                )}
              </div>
            </div>
          ))}
        </div>

        {/* Indicador de pasos */}
        <div className="absolute bottom-8 left-0 right-0 flex justify-center gap-2 z-10">
          {PASOS.map((p, i) => (
            <div
              key={i}
              className="h-1 rounded-full transition-all duration-300"
              style={{ width: 32, background: p.color, opacity: 0.4 }}
            />
          ))}
        </div>
      </div>
    </section>
  )
}