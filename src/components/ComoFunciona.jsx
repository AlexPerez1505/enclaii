import { useEffect, useRef } from 'react'
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
              <div className="max-w-xl w-full relative">
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