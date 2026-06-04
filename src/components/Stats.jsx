import { useEffect, useRef } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

const STATS = [
  { end: 1200, suffix: '+',  label: 'Estudios procesados',    color: '#2196f3' },
  { end: 98,   suffix: '%',  label: 'Satisfacción clínica',   color: '#06b6d4' },
  { end: 24,   suffix: '/7', label: 'Disponibilidad en nube', color: '#a855f7' },
  { end: 3,    suffix: 'x',  label: 'Más rápido que papel',   color: '#10b981' },
]

export default function Stats() {
  const sectionRef = useRef(null)

  useEffect(() => {
    const ctx = gsap.context(() => {
      const root    = sectionRef.current
      const numbers = root.querySelectorAll('.stat-number')
      const cards   = root.querySelectorAll('.stat-card')
      const headers = root.querySelectorAll('.stats-header > *')
      const rules   = root.querySelectorAll('.stats-rule')
      const inners  = root.querySelectorAll('.stat-inner')

      const proxies = STATS.map(() => ({ val: 0 }))
      let tweens = []

      /* Pinta los contadores */
      const paint = () => {
        proxies.forEach((p, i) => {
          if (numbers[i]) numbers[i].textContent = Math.round(p.val) + STATS[i].suffix
        })
      }

      const resetCounters = () => {
        tweens.forEach(t => t.kill())
        tweens = []
        proxies.forEach(p => (p.val = 0))
        paint()
      }

      const animateCounters = () => {
        resetCounters()
        proxies.forEach((proxy, i) => {
          tweens.push(
            gsap.to(proxy, {
              val: STATS[i].end,
              duration: 2.2,
              ease: 'power2.out',
              onUpdate: paint,
            })
          )
        })
      }

      /* ── Estado inicial (todo oculto pero existente en el DOM) ── */
      gsap.set(headers, { opacity: 0, y: 30 })
      gsap.set(rules,   { scaleX: 0, transformOrigin: 'left center' })
      gsap.set(cards,   { opacity: 0, y: 60, scale: 0.95 })
      paint()

      /* ── Animación de entrada (reusable) ── */
      const playEntrance = () => {
        gsap.to(headers, {
          opacity: 1, y: 0,
          stagger: 0.12, duration: 0.8, ease: 'power3.out',
        })
        gsap.to(rules, {
          scaleX: 1, duration: 1.2, ease: 'power3.inOut',
        })
        gsap.to(cards, {
          opacity: 1, y: 0, scale: 1,
          stagger: 0.12, duration: 0.9, ease: 'power3.out',
          onComplete: animateCounters,
        })
      }

      /* ── Reset al salir ── */
      const resetEntrance = () => {
        gsap.set(headers, { opacity: 0, y: 30 })
        gsap.set(rules,   { scaleX: 0 })
        gsap.set(cards,   { opacity: 0, y: 60, scale: 0.95 })
        resetCounters()
      }

      /* ── Trigger principal: dispara la animación cada vez que entras ── */
      ScrollTrigger.create({
        trigger: root,
        start: 'top 80%',
        end: 'bottom 20%',
        onEnter:      playEntrance,
        onEnterBack:  playEntrance,
        onLeave:      resetEntrance,
        onLeaveBack:  resetEntrance,
      })

      /* ── Movimiento continuo sutil en el wrapper interno ── */
      gsap.to(inners, {
        y: -8,
        duration: 2.6,
        ease: 'sine.inOut',
        repeat: -1,
        yoyo: true,
        stagger: { each: 0.35, from: 'start' },
      })

      /* ── Pulso de opacidad en los números (sustituye al filter problemático) ── */
      gsap.to(numbers, {
        opacity: 0.7,
        duration: 1.8,
        ease: 'sine.inOut',
        repeat: -1,
        yoyo: true,
        stagger: { each: 0.4, from: 'start' },
      })
    }, sectionRef)

    return () => ctx.revert()
  }, [])

  return (
    <section ref={sectionRef} className="py-24 bg-[#050d1f] relative overflow-hidden">
      {/* Glow de fondo */}
      <div
        className="absolute inset-0 opacity-20 pointer-events-none"
        style={{
          background:
            'radial-gradient(ellipse 60% 50% at 50% 30%, rgba(33,150,243,0.28), transparent 70%)',
        }}
      />

      {/* Grid decorativo */}
      <div
        className="absolute inset-0 opacity-[0.04] pointer-events-none"
        style={{
          backgroundImage:
            'linear-gradient(rgba(33,150,243,1) 1px, transparent 1px), linear-gradient(90deg, rgba(33,150,243,1) 1px, transparent 1px)',
          backgroundSize: '70px 70px',
        }}
      />

      <div className="relative max-w-6xl mx-auto px-6">
        {/* ── Header ── */}
        <div className="stats-header text-center mb-16">
          <span className="inline-block text-xs font-medium tracking-[0.3em] text-[#2196f3] uppercase mb-4">
            Resultados
          </span>
          <h2 className="text-3xl md:text-5xl font-light text-white mb-5 leading-tight">
            Números que respaldan{' '}
            <span className="bg-gradient-to-r from-[#2196f3] to-[#60a5fa] bg-clip-text text-transparent">
              ENCLAII
            </span>
          </h2>
          <p className="text-lg text-slate-400 max-w-xl mx-auto leading-relaxed">
            Cifras reales de la plataforma operando en instituciones de salud
            que confían en nosotros para la gestión de estudios endoscópicos.
          </p>
        </div>

        {/* Línea superior */}
        <div className="stats-rule h-px bg-gradient-to-r from-transparent via-[#2196f3]/40 to-transparent mb-16" />

        {/* ── Tarjetas ── */}
        <div className="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
          {STATS.map(s => (
            <div
              key={s.label}
              className="stat-card text-center p-8 rounded-2xl bg-white/[0.04] border border-white/10
                hover:border-white/30 hover:bg-white/[0.08] transition-all duration-300
                cursor-default"
            >
              {/* Wrapper interno: recibe la flotación continua */}
              <div className="stat-inner">
                <div
                  className="stat-number text-5xl md:text-6xl font-bold mb-3 tabular-nums"
                  style={{
                    color: s.color,
                    textShadow: `0 0 20px ${s.color}40`,
                  }}
                >
                  0{s.suffix}
                </div>
                <div className="text-xs text-slate-400 uppercase tracking-[0.2em]">
                  {s.label}
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Línea inferior */}
        <div className="stats-rule h-px bg-gradient-to-r from-transparent via-[#2196f3]/40 to-transparent mt-16" />
      </div>
    </section>
  )
}