import { useEffect, useRef } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

const pilares = [
  {
    title: 'Endoscopia',
    description:
      'Captura de imágenes y video en alta resolución integrada con tu equipo endoscópico. Estudios completos, etiquetados y trazables desde el primer momento.',
    color: 'from-[#2196f3]/20 to-[#2196f3]/5',
    border: 'hover:border-[#2196f3]/50',
    iconBg: 'bg-[#2196f3]/10 border-[#2196f3]/30 text-[#2196f3] group-hover:bg-[#2196f3]/20',
    icon: (
      <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth={1.5}>
        <path strokeLinecap="round" strokeLinejoin="round"
          d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
        <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
      </svg>
    ),
  },
  {
    title: 'Nube Segura',
    description:
      'Acceso inmediato a estudios desde cualquier dispositivo. Respaldo automático con cifrado, cumplimiento NOM y disponibilidad 24/7 para tu equipo médico.',
    color: 'from-[#06b6d4]/20 to-[#06b6d4]/5',
    border: 'hover:border-[#06b6d4]/50',
    iconBg: 'bg-[#06b6d4]/10 border-[#06b6d4]/30 text-[#06b6d4] group-hover:bg-[#06b6d4]/20',
    icon: (
      <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth={1.5}>
        <path strokeLinecap="round" strokeLinejoin="round"
          d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" />
      </svg>
    ),
  },
  {
    title: 'Inteligencia Artificial',
    description:
      'Modelos de IA entrenados para asistencia diagnóstica en tiempo real. Detección automática de hallazgos relevantes y apoyo al criterio del especialista.',
    color: 'from-[#a855f7]/20 to-[#a855f7]/5',
    border: 'hover:border-[#a855f7]/50',
    iconBg: 'bg-[#a855f7]/10 border-[#a855f7]/30 text-[#a855f7] group-hover:bg-[#a855f7]/20',
    icon: (
      <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth={1.5}>
        <path strokeLinecap="round" strokeLinejoin="round"
          d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z" />
      </svg>
    ),
  },
]

export default function Pilares() {
  const sectionRef = useRef(null)

  useEffect(() => {
    const ctx = gsap.context(() => {
      // Título entra
      gsap.fromTo('.pilares-heading',
        { opacity: 0, y: 40 },
        {
          opacity: 1, y: 0, duration: 0.9, ease: 'power3.out',
          scrollTrigger: { trigger: '.pilares-heading', start: 'top 85%' },
        }
      )
      gsap.fromTo('.pilares-sub',
        { opacity: 0, y: 30 },
        {
          opacity: 1, y: 0, duration: 0.8, ease: 'power3.out',
          scrollTrigger: { trigger: '.pilares-sub', start: 'top 85%' },
        }
      )

      // Tarjetas: cada una entra con stagger
      gsap.fromTo('.pilar-card',
        { opacity: 0, y: 70, scale: 0.96 },
        {
          opacity: 1, y: 0, scale: 1,
          duration: 0.85,
          stagger: 0.18,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: '.pilares-grid',
            start: 'top 80%',
          }
        }
      )
    }, sectionRef)

    return () => ctx.revert()
  }, [])

  return (
    <section ref={sectionRef} id="pilares" className="py-28 bg-[#050d1f]">
      {/* Separador */}
      <div className="max-w-6xl mx-auto px-6 mb-4">
        <div className="h-px bg-gradient-to-r from-transparent via-[#2196f3]/30 to-transparent" />
      </div>

      <div className="max-w-6xl mx-auto px-6 pt-16">
        {/* Encabezado */}
        <div className="text-center mb-16">
          <span className="pilares-heading inline-block text-xs font-medium tracking-[0.3em] text-[#2196f3] uppercase mb-4">
            Plataforma
          </span>
          <h2 className="pilares-heading text-3xl md:text-5xl font-light text-white mb-5 leading-tight">
            Tres pilares, una plataforma
          </h2>
          <p className="pilares-sub text-lg text-slate-400 max-w-xl mx-auto leading-relaxed">
            ENCLAII unifica el flujo completo del estudio endoscópico, desde la captura
            hasta el diagnóstico asistido por inteligencia artificial.
          </p>
        </div>

        {/* Tarjetas */}
        <div className="pilares-grid grid md:grid-cols-3 gap-6">
          {pilares.map(p => (
            <div
              key={p.title}
              className={`pilar-card group p-8 rounded-2xl bg-gradient-to-b ${p.color}
                border border-white/10 ${p.border} transition-all duration-300 cursor-default
                hover:-translate-y-1 hover:shadow-2xl hover:shadow-black/40`}
            >
              <div className={`w-14 h-14 ${p.iconBg} border rounded-xl flex items-center justify-center mb-6 transition-all duration-300`}>
                {p.icon}
              </div>
              <h3 className="text-xl font-semibold text-white mb-3">{p.title}</h3>
              <p className="text-slate-400 leading-relaxed text-sm">{p.description}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
