import { useEffect, useRef } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

const modulos = [
  {
    tag: 'Diagnóstico',
    title: 'Visor DICOM integrado',
    desc: 'Visualiza estudios en formato DICOM directamente en el navegador. Herramientas de medición, anotación y comparación de imágenes.',
    features: ['Zoom y medición', 'Anotaciones clínicas', 'Comparación side-by-side'],
  },
  {
    tag: 'Expediente',
    title: 'Historial del paciente',
    desc: 'Acceso rápido al historial endoscópico completo de cada paciente. Búsqueda avanzada por fecha, diagnóstico o médico tratante.',
    features: ['Línea de tiempo clínica', 'Búsqueda avanzada', 'Acceso por QR'],
  },
  {
    tag: 'Reportes',
    title: 'Informes automáticos',
    desc: 'Genera reportes en segundos con plantillas predefinidas. Comparte con el paciente o referido vía correo o enlace seguro.',
    features: ['Plantillas personalizables', 'Firma digital', 'Envío automático'],
  },
  {
    tag: 'Administración',
    title: 'Panel de control',
    desc: 'Gestiona usuarios, permisos y equipos desde un solo lugar. Estadísticas de uso y trazabilidad completa de cada acción.',
    features: ['Roles y permisos', 'Logs de auditoría', 'Estadísticas de uso'],
  },
]

export default function Modulos() {
  const sectionRef = useRef(null)

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo('.mod-heading',
        { opacity: 0, y: 40 },
        {
          opacity: 1, y: 0, duration: 0.9, ease: 'power3.out',
          scrollTrigger: { trigger: '.mod-heading', start: 'top 85%' }
        }
      )
      gsap.fromTo('.mod-card',
        { opacity: 0, y: 60, scale: 0.97 },
        {
          opacity: 1, y: 0, scale: 1,
          duration: 0.8,
          stagger: 0.14,
          ease: 'power3.out',
          scrollTrigger: { trigger: '.mod-grid', start: 'top 80%' }
        }
      )
    }, sectionRef)

    return () => ctx.revert()
  }, [])

  return (
    <section ref={sectionRef} id="modulos" className="py-28 bg-[#050d1f]">
      <div className="max-w-6xl mx-auto px-6">
        {/* Encabezado */}
        <div className="text-center mb-16">
          <span className="mod-heading inline-block text-xs font-medium tracking-[0.3em] text-[#2196f3] uppercase mb-4">
            Módulos SIMED
          </span>
          <h2 className="mod-heading text-3xl md:text-5xl font-light text-white mb-5">
            Todo lo que necesitas
          </h2>
          <p className="mod-heading text-lg text-slate-400 max-w-xl mx-auto">
            Una suite completa de herramientas para la gestión integral del servicio de endoscopia.
          </p>
        </div>

        {/* Grid de módulos */}
        <div className="mod-grid grid md:grid-cols-2 gap-6">
          {modulos.map((m, i) => (
            <div
              key={m.title}
              className={`mod-card group relative p-8 rounded-2xl border border-white/10
                hover:border-[#2196f3]/30 transition-all duration-300 hover:-translate-y-1
                hover:shadow-xl hover:shadow-[#2196f3]/10 overflow-hidden
                ${i % 2 === 0 ? 'bg-gradient-to-br from-[#2196f3]/5 to-transparent' : 'bg-gradient-to-br from-white/[0.03] to-transparent'}`}
            >
              {/* Tag */}
              <span className="inline-block text-xs font-medium tracking-widest text-[#2196f3] uppercase mb-3 bg-[#2196f3]/10 px-3 py-1 rounded-full">
                {m.tag}
              </span>

              <h3 className="text-xl font-semibold text-white mb-3">{m.title}</h3>
              <p className="text-slate-400 text-sm leading-relaxed mb-5">{m.desc}</p>

              {/* Features */}
              <ul className="space-y-2">
                {m.features.map(f => (
                  <li key={f} className="flex items-center gap-2 text-sm text-slate-400">
                    <svg className="w-4 h-4 text-[#2196f3] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                    </svg>
                    {f}
                  </li>
                ))}
              </ul>

              {/* Glow en hover */}
              <div className="absolute -bottom-10 -right-10 w-40 h-40 bg-[#2196f3]/5 rounded-full
                opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none blur-2xl" />
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
