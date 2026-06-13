import { useEffect, useRef } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

const pilares = [
  {
    title: 'Endoscopia',
    description: 'Captura de imágenes y video en alta resolución integrada con tu equipo endoscópico.',
  },
  {
    title: 'Nube Segura',
    description: 'Acceso inmediato a estudios desde cualquier dispositivo, con respaldo automático y cifrado.',
  },
  {
    title: 'Inteligencia Artificial',
    description: 'Asistencia diagnóstica con modelos de IA para detectar hallazgos relevantes.',
  },
  {
    title: 'Seguridad Médica',
    description: 'Protección de datos clínicos mediante cifrado, control de accesos y auditorías.',
  },
  {
    title: 'Reportes Clínicos',
    description: 'Generación de informes médicos organizados, claros y listos para compartir.',
  },
  {
    title: 'Flujo Centralizado',
    description: 'Todo el proceso del estudio en una sola plataforma: captura, análisis y almacenamiento.',
  },
]

export default function Pilares() {
  const sectionRef = useRef(null)

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo(
        '.pilares-heading',
        { opacity: 0, y: 40 },
        {
          opacity: 1,
          y: 0,
          duration: 0.9,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: '.pilares-heading',
            start: 'top 85%',
          },
        }
      )

      gsap.fromTo(
        '.pilares-sub',
        { opacity: 0, y: 30 },
        {
          opacity: 1,
          y: 0,
          duration: 0.8,
          ease: 'power3.out',
          scrollTrigger: {
            trigger: '.pilares-sub',
            start: 'top 85%',
          },
        }
      )

      const cards = gsap.utils.toArray('.pilar-card')
      const endoscopes = gsap.utils.toArray('.endo-orbit')
      const pc = sectionRef.current.querySelector('.pc-center')
      const N = cards.length
      const section = sectionRef.current.querySelector('.pilares-stage')

      const PX_PER_CARD = 320
      const CIRCLE_PX = 1900
      const INTRO_TOTAL = N * PX_PER_CARD
      const OUTRO_TOTAL = N * PX_PER_CARD
      const TOTAL = INTRO_TOTAL + CIRCLE_PX + OUTRO_TOTAL

      const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v))
      const lerp = (a, b, t) => a + (b - a) * t
      const easeOut = (t) => 1 - Math.pow(1 - t, 3)
      const easeIn = (t) => t * t * t

      gsap.set(pc, {
        xPercent: -50,
        yPercent: -50,
        scale: 0.9,
        opacity: 0,
        zIndex: 35,
      })

      gsap.set(endoscopes, {
        xPercent: -50,
        yPercent: -50,
        opacity: 0,
        transformOrigin: '50% 50%',
      })

      function render(scrollY) {
        const rect = section.getBoundingClientRect()
        const VW = rect.width
        const VH = rect.height

        const CX = VW * 0.5
        const CY = VH * 0.52
        const RX = VW * 0.34
        const RY = VH * 0.34

        const OX = -220
        const OY = VH + 180
        const DX = VW + 220
        const DY = VH + 180

        let phase = 'INTRO'
        let spin = 0

        if (scrollY < INTRO_TOTAL) {
          phase = 'INTRO'
        } else if (scrollY < INTRO_TOTAL + CIRCLE_PX) {
          phase = 'CIRCLE'
          spin = ((scrollY - INTRO_TOTAL) / CIRCLE_PX) * N
        } else {
          phase = 'OUTRO'
          spin = N
        }

        const globalProgress = clamp(scrollY / TOTAL, 0, 1)
        const pcProgress = clamp(scrollY / 520, 0, 1)

        gsap.set(pc, {
          left: CX,
          top: CY,
          scale: 0.78 + easeOut(pcProgress) * 0.22,
          opacity: easeOut(pcProgress),
        })

        const orbitSettings = [
          {
            direction: 1,
            speed: 4.2,
            radiusX: 0.3,
            radiusY: 0.24,
            baseScale: 0.72,
            scaleDepth: 0.22,
            offset: 0,
          },
          {
            direction: -1,
            speed: 3.1,
            radiusX: 0.24,
            radiusY: 0.18,
            baseScale: 0.55,
            scaleDepth: 0.18,
            offset: Math.PI * 0.65,
          },
        ]

        endoscopes.forEach((endo, i) => {
          const config = orbitSettings[i]
          const angle =
            globalProgress * Math.PI * config.speed * config.direction + config.offset

          const d = (Math.sin(angle) + 1) / 2
          const x = CX + Math.cos(angle) * (VW * config.radiusX)
          const y = CY + Math.sin(angle) * (VH * config.radiusY)
          const scale = config.baseScale + d * config.scaleDepth

          gsap.set(endo, {
            left: x,
            top: y,
            scaleX: (i === 0 ? 1 : -1) * scale,
            scaleY: scale,
            opacity: 0.25 + d * 0.75,
            rotation: Math.cos(angle) * 12 + (i === 0 ? -8 : 10),
            zIndex: d > 0.5 ? 58 : 18,
          })
        })

        function circleAngle(i, spinValue) {
          return -Math.PI / 2 + i * ((Math.PI * 2) / N) + spinValue * ((Math.PI * 2) / N)
        }

        function circleXY(angle) {
          return {
            x: CX + Math.cos(angle) * RX,
            y: CY + Math.sin(angle) * RY,
          }
        }

        function depthProps(angle) {
          const d = (Math.sin(angle) + 1) / 2

          return {
            scale: 0.58 + d * 0.42,
            opacity: 0.38 + d * 0.62,
            zIndex: 70 + Math.round(d * 60),
          }
        }

        cards.forEach((card, i) => {
          const destAngle = circleAngle(i, 0)
          const destXY = circleXY(destAngle)

          const outroAngle = circleAngle(i, N)
          const outroXY = circleXY(outroAngle)

          let x
          let y
          let scale
          let opacity
          let zIndex

          if (phase === 'INTRO') {
            const raw = (scrollY - i * PX_PER_CARD) / PX_PER_CARD
            const t = clamp(raw, 0, 1)
            const e = easeOut(t)

            x = lerp(CX, destXY.x, e)
            y = lerp(CY, destXY.y, e)

            const startAngle = Math.atan2(OY - CY, OX - CX)
            const angle = lerp(startAngle, destAngle, e)
            const depth = depthProps(angle)

            scale = 0.35 + depth.scale * e
            opacity = t < 0.01 ? 0 : depth.opacity
            zIndex = depth.zIndex
          }

          if (phase === 'CIRCLE') {
            const angle = circleAngle(i, spin)
            const pos = circleXY(angle)
            const depth = depthProps(angle)

            x = pos.x
            y = pos.y
            scale = depth.scale
            opacity = depth.opacity
            zIndex = depth.zIndex
          }

          if (phase === 'OUTRO') {
            const outroScroll = scrollY - (INTRO_TOTAL + CIRCLE_PX)
            const raw = (outroScroll - i * PX_PER_CARD) / PX_PER_CARD
            const t = clamp(raw, 0, 1)
            const e = easeIn(t)

            x = lerp(outroXY.x, DX, e)
            y = lerp(outroXY.y, DY, e)

            const exitAngle = Math.atan2(DY - CY, DX - CX)
            const angle = lerp(outroAngle, exitAngle, e)
            const depth = depthProps(angle)

            scale = depth.scale * (1 - e * 0.35)
            opacity = t > 0.99 ? 0 : depth.opacity
            zIndex = depth.zIndex
          }

          gsap.set(card, {
            x,
            y,
            xPercent: -50,
            yPercent: -50,
            scale,
            opacity,
            zIndex,
            rotate: (i % 2 === 0 ? -1 : 1) * 4,
          })
        })
      }

      ScrollTrigger.create({
        trigger: section,
        start: 'top top',
        end: `+=${TOTAL}`,
        scrub: true,
        pin: true,
        onUpdate: (self) => {
          render(self.progress * TOTAL)
        },
      })

      render(0)
    }, sectionRef)

    return () => ctx.revert()
  }, [])

  return (
    <section ref={sectionRef} id="pilares" className="bg-[#050d1f]">
      {/* Separador */}
      <div className="max-w-6xl mx-auto px-6 mb-4">
        <div className="h-px bg-gradient-to-r from-transparent via-[#2196f3]/30 to-transparent" />
      </div>

      <div className="max-w-6xl mx-auto px-6 pt-28">
        {/* Encabezado */}
        <div className="text-center mb-16">
          <span className="pilares-heading inline-block text-xs font-medium tracking-[0.3em] text-[#2196f3] uppercase mb-4">
            Plataforma
          </span>
          <h2 className="pilares-heading text-3xl md:text-5xl font-light text-white mb-5 leading-tight">
            Seis pilares, una plataforma
          </h2>
          <p className="pilares-sub text-lg text-slate-400 max-w-xl mx-auto leading-relaxed">
            ENCLAII unifica el flujo completo del estudio endoscópico, desde la captura
            hasta el diagnóstico asistido por inteligencia artificial.
          </p>
        </div>

        {/* Tarjetas animadas */}
        <div className="pilares-stage relative h-screen overflow-hidden">
          <div className="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(33,150,243,0.16),transparent_52%)]" />

          <img
            src="/endoscopio-1.png"
            alt=""
            aria-hidden="true"
            className="endo-orbit pointer-events-none absolute left-1/2 top-1/2 w-[520px] md:w-[660px] lg:w-[760px] select-none drop-shadow-[0_0_35px_rgba(33,150,243,0.28)]"
          />

          <img
            src="/endoscopio-1.png"
            alt=""
            aria-hidden="true"
            className="endo-orbit pointer-events-none absolute left-1/2 top-1/2 w-[500px] md:w-[620px] lg:w-[720px] scale-x-[-1] select-none drop-shadow-[0_0_35px_rgba(33,150,243,0.25)]"
          />

          <img
            src="/pc.png"
            alt="Plataforma médica ENCLAII"
            className="pc-center pointer-events-none absolute left-1/2 top-1/2 w-[440px] md:w-[560px] lg:w-[680px] select-none drop-shadow-[0_0_55px_rgba(33,150,243,0.38)]"
          />

          {pilares.map((p) => (
            <div
              key={p.title}
              className="pilar-card absolute left-0 top-0 w-[260px] md:w-[320px] p-6 rounded-2xl
              bg-gradient-to-b from-[#2196f3]/25 to-[#2196f3]/10
              border border-white/10 text-white shadow-2xl shadow-black/40 backdrop-blur-md"
            >
              <h3 className="text-xl md:text-2xl font-semibold mb-4">
                {p.title}
              </h3>

              <p className="text-sm md:text-base text-slate-300 leading-relaxed">
                {p.description}
              </p>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
