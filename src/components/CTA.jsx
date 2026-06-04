import { useEffect, useRef } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

export default function CTA() {
  const sectionRef = useRef(null)

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo('.cta-content',
        { opacity: 0, y: 60, scale: 0.97 },
        {
          opacity: 1, y: 0, scale: 1,
          duration: 1,
          ease: 'power3.out',
          scrollTrigger: { trigger: sectionRef.current, start: 'top 75%' }
        }
      )
    }, sectionRef)

    return () => ctx.revert()
  }, [])

  return (
    <section ref={sectionRef} id="contacto" className="py-28 relative overflow-hidden">
      {/* Fondo */}
      <div className="absolute inset-0 bg-gradient-to-b from-[#0a1733] to-[#050d1f]" />
      <div
        className="absolute inset-0 opacity-30"
        style={{ background: 'radial-gradient(ellipse 60% 60% at 50% 50%, rgba(33,150,243,0.2), transparent 70%)' }}
      />

      <div className="relative z-10 max-w-4xl mx-auto px-6 text-center">
        <div className="cta-content inline-block w-full p-12 rounded-3xl border border-white/10
          bg-white/[0.03] backdrop-blur-sm shadow-2xl shadow-black/40">
          {/* Badge */}
          <span className="inline-block text-xs font-medium tracking-[0.3em] text-[#2196f3] uppercase mb-6 bg-[#2196f3]/10 px-4 py-1.5 rounded-full">
            Demo personalizada · Sin costo
          </span>

          <h2 className="text-3xl md:text-5xl font-light text-white mb-5 leading-tight">
            ¿Listo para modernizar tu
            <br />
            <span className="bg-gradient-to-r from-[#2196f3] to-[#60a5fa] bg-clip-text text-transparent">
              servicio de endoscopia?
            </span>
          </h2>

          <p className="text-lg text-slate-400 mb-10 max-w-xl mx-auto leading-relaxed">
            Agenda una demostración de SIMED y conoce cómo se integra con tu institución,
            tu equipo y tu flujo de trabajo actual.
          </p>

          <div className="flex flex-col sm:flex-row gap-4 justify-center mb-8">
            <a
              href="mailto:licencias@cizcalli.gob.mx?subject=Solicitud%20de%20demo%20SIMED"
              className="inline-flex items-center justify-center gap-2 bg-[#2196f3] text-white
                px-8 py-4 rounded-xl font-semibold hover:bg-[#1e88e5] transition-all duration-200
                shadow-lg shadow-[#2196f3]/30 hover:shadow-[#2196f3]/50 hover:-translate-y-0.5 text-base"
            >
              Solicitar demo gratuita
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </a>
            <a
              href="mailto:licencias@cizcalli.gob.mx"
              className="inline-flex items-center justify-center bg-white/5 text-slate-200
                px-8 py-4 rounded-xl font-medium border border-white/15 hover:bg-white/10
                hover:border-white/25 transition-all duration-200 text-base"
            >
              Enviar un mensaje
            </a>
          </div>

          {/* Garantías */}
          <div className="flex flex-wrap justify-center gap-6 text-sm text-slate-500">
            {[
              '✓ Sin compromisos',
              '✓ Demo en tu institución',
              '✓ Soporte en español',
            ].map(g => (
              <span key={g} className="text-slate-400">{g}</span>
            ))}
          </div>
        </div>
      </div>
    </section>
  )
}
