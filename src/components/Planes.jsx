import { useEffect, useRef, useState } from 'react'
import { gsap } from 'gsap'

const planes = [
  {
    nombre: 'Clínica',
    descripcion: 'Ideal para clínicas que buscan digitalizar su operación endoscópica con una plataforma segura y centralizada.',
    beneficios: ['Gestión de estudios', 'Almacenamiento en la nube', 'Reportes digitales'],
    detalles: ['Implementación rápida', 'Acceso seguro en la nube', 'Flujo ideal para clínicas independientes'],
    imagen: '/plan-clinica.png',
    destacado: false,
  },
  {
    nombre: 'Hospital',
    descripcion: 'Diseñado para instituciones con múltiples usuarios, alto volumen de estudios y necesidades avanzadas de trazabilidad.',
    beneficios: ['Usuarios ilimitados', 'Roles y permisos', 'Auditoría clínica'],
    detalles: ['Control por perfiles', 'Trazabilidad institucional', 'Escalable para múltiples áreas clínicas'],
    imagen: '/plan-hospital.png',
    destacado: true,
  },
  {
    nombre: 'Red médica',
    descripcion: 'Para grupos hospitalarios que requieren estandarizar procesos, compartir información y escalar con seguridad.',
    beneficios: ['Multi-sede', 'Panel administrativo', 'Analítica operativa'],
    detalles: ['Administración centralizada', 'Indicadores por sede', 'Estandarización de procesos médicos'],
    imagen: '/plan-red-medica.png',
    destacado: false,
  },
]

export default function Planes() {
  const sectionRef = useRef(null)
  const [flippedPlans, setFlippedPlans] = useState({})

  const toggleFlip = planName => {
    setFlippedPlans(current => ({
      ...current,
      [planName]: !current[planName],
    }))
  }

  const handleMouseMove = event => {
    const card = event.currentTarget
    const rect = card.getBoundingClientRect()
    const x = event.clientX - rect.left
    const y = event.clientY - rect.top
    const tiltY = ((x / rect.width) - 0.5) * 10
    const tiltX = ((0.5 - (y / rect.height)) * 10)

    card.style.setProperty('--spotlight-x', `${x}px`)
    card.style.setProperty('--spotlight-y', `${y}px`)
    card.style.setProperty('--tilt-x', `${tiltX}deg`)
    card.style.setProperty('--tilt-y', `${tiltY}deg`)
  }

  const handleMouseLeave = event => {
    const card = event.currentTarget

    card.style.setProperty('--tilt-x', '0deg')
    card.style.setProperty('--tilt-y', '0deg')
  }

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo('.planes-heading',
        { opacity: 0, y: 40 },
        {
          opacity: 1,
          y: 0,
          duration: 0.9,
          ease: 'power3.out',
          scrollTrigger: { trigger: '.planes-heading', start: 'top 85%' },
        }
      )

      gsap.fromTo('.plan-card',
        { opacity: 0, y: 60, scale: 0.96 },
        {
          opacity: 1,
          y: 0,
          scale: 1,
          duration: 0.85,
          stagger: 0.18,
          ease: 'power3.out',
          scrollTrigger: { trigger: '.planes-grid', start: 'top 80%' },
        }
      )

      gsap.fromTo('.plan-check',
        { opacity: 0, x: -14, scale: 0.96 },
        {
          opacity: 1,
          x: 0,
          scale: 1,
          duration: 0.45,
          stagger: 0.08,
          ease: 'power2.out',
          scrollTrigger: { trigger: '.planes-grid', start: 'top 76%' },
        }
      )
    }, sectionRef)

    return () => ctx.revert()
  }, [])

  return (
    <section ref={sectionRef} id="planes" className="planes-section relative overflow-hidden py-18 md:py-20">
      <style>{`
        @property --border-angle {
          syntax: '<angle>';
          inherits: false;
          initial-value: 0deg;
        }

        .planes-section {
          background:
            radial-gradient(circle at 16% 30%, rgba(20, 184, 166, 0.16), transparent 26%),
            radial-gradient(circle at 82% 22%, rgba(33, 150, 243, 0.18), transparent 28%),
            linear-gradient(180deg, #081323 0%, #0a1728 48%, #050d1f 100%);
        }

        [data-theme="light"] .planes-section {
          background:
            radial-gradient(circle at 18% 30%, rgba(20, 184, 166, 0.12), transparent 28%),
            radial-gradient(circle at 82% 18%, rgba(33, 150, 243, 0.14), transparent 28%),
            linear-gradient(180deg, #f7fbff 0%, #eef7ff 46%, #ffffff 100%);
        }

        .plan-card {
          --spotlight-x: 50%;
          --spotlight-y: 50%;
          --tilt-x: 0deg;
          --tilt-y: 0deg;
          isolation: isolate;
          perspective: 1400px;
          transform: translateY(0) scale(1);
          background: transparent;
          box-shadow: none;
          cursor: pointer;
        }

        .plan-card-inner {
          position: relative;
          min-height: 440px;
          width: 100%;
          transform-style: preserve-3d;
          transform: rotateX(var(--tilt-x)) rotateY(var(--tilt-y)) scale(1);
          transition: transform .35s cubic-bezier(.2,.8,.2,1), filter .35s ease;
          will-change: transform;
        }

        .plan-card:hover .plan-card-inner {
          transform: rotateX(var(--tilt-x)) rotateY(var(--tilt-y)) scale(1.045);
        }

        .plan-card.is-flipped .plan-card-inner {
          transform: rotateX(var(--tilt-x)) rotateY(calc(180deg + var(--tilt-y))) scale(1);
        }

        .plan-card.is-flipped:hover .plan-card-inner {
          transform: rotateX(var(--tilt-x)) rotateY(calc(180deg + var(--tilt-y))) scale(1.045);
        }

        .plan-face {
          position: absolute;
          inset: 0;
          display: flex;
          min-height: 440px;
          flex-direction: column;
          overflow: hidden;
          border-radius: 1.35rem;
          border: 1px solid rgba(255, 255, 255, 0.10);
          padding: 1.25rem;
          backface-visibility: hidden;
          -webkit-backface-visibility: hidden;
          background:
            linear-gradient(180deg, rgba(255,255,255,0.10), rgba(255,255,255,0.035)),
            rgba(5, 13, 31, 0.86);
          box-shadow:
            0 28px 84px rgba(0, 0, 0, 0.46),
            inset 0 1px 0 rgba(255, 255, 255, 0.10);
        }

        .plan-face-back {
          transform: rotateY(180deg);
        }

        [data-theme="light"] .plan-face {
          background:
            linear-gradient(180deg, rgba(255,255,255,0.92), rgba(238,247,255,0.82)),
            rgba(255, 255, 255, 0.88);
          box-shadow:
            0 24px 70px rgba(15, 23, 42, 0.14),
            inset 0 1px 0 rgba(255, 255, 255, 0.85);
        }

        .plan-face::before {
          content: '';
          position: absolute;
          inset: 0;
          border-radius: 1.35rem;
          padding: 1px;
          background: linear-gradient(140deg, rgba(255,255,255,0.20), rgba(33,150,243,0.16), rgba(255,255,255,0.07));
          pointer-events: none;
          z-index: 2;
          -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
          -webkit-mask-composite: xor;
          mask-composite: exclude;
        }

        .plan-face::after {
          content: '';
          position: absolute;
          inset: 0;
          border-radius: 1.35rem;
          padding: 2px;
          background:
            conic-gradient(
              from var(--border-angle),
              transparent 0deg,
              transparent 238deg,
              rgba(97, 255, 220, 0.10) 260deg,
              rgba(97, 255, 220, 0.95) 282deg,
              rgba(99, 165, 255, 1) 306deg,
              rgba(99, 165, 255, 0.18) 330deg,
              transparent 350deg,
              transparent 360deg
            );
          opacity: 0;
          pointer-events: none;
          z-index: 4;
          transition: opacity 0.25s ease;
          animation: plan-border-run 2.2s linear infinite paused;
          filter: drop-shadow(0 0 10px rgba(99, 165, 255, 0.72));
          -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
          -webkit-mask-composite: xor;
          mask-composite: exclude;
        }

        .plan-card:hover .plan-face::after,
        .plan-card.is-flipped .plan-face::after {
          opacity: 1;
          animation-play-state: running;
        }

        .plan-card:hover .plan-face {
          box-shadow:
            0 34px 100px rgba(0, 0, 0, 0.58),
            0 0 62px rgba(33, 150, 243, 0.28),
            0 0 22px rgba(110, 168, 255, 0.18),
            inset 0 1px 0 rgba(255, 255, 255, 0.14);
        }

        [data-theme="light"] .plan-card:hover .plan-face {
          box-shadow:
            0 30px 86px rgba(15, 23, 42, 0.18),
            0 0 42px rgba(33, 150, 243, 0.16),
            inset 0 1px 0 rgba(255, 255, 255, 0.95);
        }

        .plan-spotlight {
          position: absolute;
          inset: 0;
          border-radius: 1.35rem;
          z-index: 1;
          opacity: 0;
          pointer-events: none;
          transition: opacity 0.25s ease;
          background:
            radial-gradient(
              360px circle at var(--spotlight-x) var(--spotlight-y),
              rgba(97,255,220,0.14),
              rgba(33,150,243,0.12) 34%,
              transparent 68%
            );
        }

        .plan-card:hover .plan-spotlight {
          opacity: 1;
        }

        .plan-card.is-flipped .plan-spotlight {
          opacity: 1;
        }

        .plan-badge {
          color: #ffffff;
          background: rgba(255, 255, 255, 0.15);
          border-color: rgba(255, 255, 255, 0.10);
        }

        [data-theme="light"] .plan-badge {
          color: #075985;
          background: rgba(224, 242, 254, 0.92);
          border-color: rgba(14, 165, 233, 0.28);
          box-shadow: 0 10px 28px rgba(14, 165, 233, 0.16);
        }

        .plan-media {
          position: relative;
          height: 148px;
          margin: -0.55rem -0.55rem 0.9rem;
          overflow: hidden;
          border-radius: 1rem;
          border: 1px solid rgba(255, 255, 255, 0.10);
          background: rgba(255, 255, 255, 0.04);
        }

        .plan-media img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          object-position: center;
          transform: scale(1.03);
          filter: saturate(1.08) contrast(1.06);
          transition: transform 0.7s ease, filter 0.7s ease;
        }

        .plan-media::before {
          content: '';
          position: absolute;
          inset: 0;
          z-index: 1;
          background:
            linear-gradient(to bottom, rgba(5,13,31,0.02) 0%, rgba(5,13,31,0.18) 42%, rgba(5,13,31,0.82) 100%),
            repeating-linear-gradient(to bottom, transparent 0 8px, rgba(33,150,243,0.08) 9px);
          pointer-events: none;
        }

        [data-theme="light"] .plan-media::before {
          background:
            linear-gradient(to bottom, rgba(255,255,255,0.00) 0%, rgba(255,255,255,0.12) 44%, rgba(248,251,255,0.54) 100%),
            repeating-linear-gradient(to bottom, transparent 0 8px, rgba(33,150,243,0.05) 9px);
        }

        .planes-title {
          color: #f8fafc;
        }

        .planes-copy,
        .plan-copy,
        .plan-check {
          color: #cbd5e1;
        }

        .plan-title {
          color: #ffffff;
        }

        [data-theme="light"] .planes-title,
        [data-theme="light"] .plan-title {
          color: #0f172a;
        }

        [data-theme="light"] .planes-copy,
        [data-theme="light"] .plan-copy,
        [data-theme="light"] .plan-check {
          color: #475569;
        }

        [data-theme="light"] .plan-face-back li {
          color: #475569;
        }

        [data-theme="light"] .plan-face-back p {
          color: #0f172a;
        }

        .plan-media::after {
          content: '';
          position: absolute;
          inset: 0;
          z-index: 2;
          background: linear-gradient(110deg, transparent 0%, rgba(97,255,220,0.20) 45%, rgba(255,255,255,0.38) 50%, rgba(99,165,255,0.18) 55%, transparent 100%);
          transform: translateX(-130%);
          animation: plan-media-sheen 4s ease-in-out infinite;
          pointer-events: none;
        }

        .plan-card:hover .plan-media img {
          transform: scale(1.08) translateY(-3px);
          filter: saturate(1.2) contrast(1.12) brightness(1.08);
        }

        .plan-content {
          min-height: 400px;
        }

        .plan-description {
          min-height: 66px;
          border-bottom: 1px solid rgba(255, 255, 255, 0.08);
          padding-bottom: 0.9rem;
        }

        [data-theme="light"] .plan-description {
          border-bottom-color: rgba(14, 165, 233, 0.12);
        }

        .medical-particles {
          position: absolute;
          inset: 0;
          overflow: hidden;
          pointer-events: none;
        }

        .medical-particle {
          position: absolute;
          width: 5px;
          height: 5px;
          border-radius: 999px;
          background: rgba(147, 197, 253, 0.75);
          box-shadow: 0 0 18px rgba(33, 150, 243, 0.7);
          animation: medical-particle-float 14s ease-in-out infinite;
        }

        .medical-particle:nth-child(2n) {
          width: 3px;
          height: 3px;
          background: rgba(255, 255, 255, 0.78);
          box-shadow: 0 0 14px rgba(255, 255, 255, 0.55);
          animation-duration: 18s;
        }

        .medical-particle:nth-child(3n)::after {
          content: '';
          position: absolute;
          left: 50%;
          top: 50%;
          width: 13px;
          height: 1px;
          transform: translate(-50%, -50%);
          background: rgba(147, 197, 253, 0.45);
          box-shadow: 0 0 10px rgba(33, 150, 243, 0.45);
        }

        @keyframes medical-particle-float {
          0%, 100% { transform: translate3d(0, 0, 0) scale(1); opacity: .28; }
          50% { transform: translate3d(22px, -34px, 0) scale(1.25); opacity: .82; }
        }

        @keyframes plan-border-run {
          from { --border-angle: 0deg; }
          to { --border-angle: 360deg; }
        }

        @keyframes plan-media-sheen {
          0%, 38% { transform: translateX(-130%); }
          68%, 100% { transform: translateX(130%); }
        }

        @media (max-width: 767px) {
          .plan-card,
          .plan-card-inner,
          .plan-face {
            min-height: 470px;
          }
        }
      `}</style>

      <div className="absolute inset-0 pointer-events-none">
        <div className="absolute top-24 left-1/2 h-[560px] w-[560px] -translate-x-1/2 rounded-full bg-[#2196f3]/10 blur-3xl" />
        <div className="absolute bottom-0 right-0 h-[360px] w-[360px] rounded-full bg-emerald-400/10 blur-3xl" />
      </div>

      <div className="medical-particles">
        {Array.from({ length: 22 }).map((_, i) => (
          <span
            key={i}
            className="medical-particle"
            style={{
              left: `${(i * 17) % 100}%`,
              top: `${12 + ((i * 23) % 76)}%`,
              animationDelay: `${i * 0.55}s`,
            }}
          />
        ))}
      </div>

      <div className="relative mx-auto max-w-6xl px-6">
        <div className="planes-heading mb-10 text-center">
          <span className="mb-4 inline-block text-xs font-medium uppercase tracking-[0.3em] text-[#2196f3]">
            Planes ENCLAII
          </span>
          <h2 className="planes-title mb-4 text-3xl font-light md:text-5xl">
            Soluciones para cada institución
          </h2>
          <p className="planes-copy mx-auto max-w-2xl text-base md:text-lg">
            Elige el modelo que mejor se adapta a tu operación clínica, con implementación guiada y escalabilidad desde el primer día.
          </p>
        </div>

        <div className="planes-grid grid gap-6 md:grid-cols-3">
          {planes.map(plan => {
            const isFlipped = Boolean(flippedPlans[plan.nombre])

            return (
            <article
              key={plan.nombre}
              onMouseMove={handleMouseMove}
              onMouseLeave={handleMouseLeave}
              onClick={() => toggleFlip(plan.nombre)}
              className={`plan-card relative min-h-[440px] transition-all duration-300 ${
                plan.destacado ? 'md:-translate-y-4' : ''
              } ${
                isFlipped ? 'is-flipped' : ''
              }`}
              role="button"
              tabIndex={0}
              onKeyDown={event => {
                if (event.key === 'Enter' || event.key === ' ') {
                  event.preventDefault()
                  toggleFlip(plan.nombre)
                }
              }}
            >
              <div className="plan-card-inner">
                <div className="plan-face plan-face-front">
                  <div className="plan-spotlight" />
                  <div className="plan-content relative z-10 flex flex-col">
                    {plan.destacado && (
                      <span className="plan-badge absolute left-0 top-0 z-20 rounded-md border px-3 py-1 text-[10px] font-semibold tracking-wide backdrop-blur">
                        Recomendado
                      </span>
                    )}

                    <div className="plan-media">
                      <img src={plan.imagen} alt={plan.nombre} />
                    </div>

                    <h3 className="plan-title mb-2 text-xl font-semibold md:text-2xl">{plan.nombre}</h3>
                    <p className="plan-copy plan-description mb-3 text-sm leading-relaxed">{plan.descripcion}</p>

                    <ul className="mb-4 space-y-2">
                      {plan.beneficios.map(beneficio => (
                        <li key={beneficio} className="plan-check flex items-center gap-3 text-sm text-slate-300">
                          <span className="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-emerald-300/20 text-emerald-200">
                            <svg className="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                            </svg>
                          </span>
                          {beneficio}
                        </li>
                      ))}
                    </ul>

                    <span className="mb-3 mt-auto text-center text-xs font-medium tracking-wide text-[#6ea8ff]">
                      Click para ver detalles
                    </span>

                    <a
                      href="/contacto"
                      onClick={event => event.stopPropagation()}
                      className="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-emerald-300 to-[#6ea8ff] px-5 py-3 text-sm font-semibold text-[#071428] shadow-lg shadow-[#2196f3]/20 transition-all duration-200 hover:brightness-110"
                    >
                      Solicitar información
                    </a>
                  </div>
                </div>

                <div className="plan-face plan-face-back">
                  <div className="plan-spotlight" />
                  <div className="relative z-10 flex min-h-[400px] flex-col">
                    <span className="mb-4 inline-block text-xs font-medium uppercase tracking-[0.3em] text-[#2196f3]">
                      Detalles del plan
                    </span>

                    <h3 className="plan-title mb-3 text-2xl font-semibold">{plan.nombre}</h3>
                    <p className="plan-copy mb-6 text-sm leading-relaxed">{plan.descripcion}</p>

                    <div className="mb-6 rounded-2xl border border-white/10 bg-white/[0.04] p-4">
                      <p className="mb-3 text-sm font-semibold text-white">Incluye:</p>
                      <ul className="space-y-3">
                        {[...plan.beneficios, ...plan.detalles].map(detalle => (
                          <li key={detalle} className="flex items-start gap-3 text-sm text-slate-300">
                            <span className="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#6ea8ff] shadow-[0_0_12px_rgba(110,168,255,0.8)]" />
                            {detalle}
                          </li>
                        ))}
                      </ul>
                    </div>

                    <span className="mt-auto text-center text-xs font-medium tracking-wide text-[#6ea8ff]">
                      Click para regresar
                    </span>
                  </div>
                </div>
              </div>
            </article>
            )
          })}
        </div>
      </div>
    </section>
  )
}
