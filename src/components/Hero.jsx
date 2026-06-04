import { useEffect, useRef } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

export default function Hero() {
  const sectionRef = useRef(null)
  const orb1Ref    = useRef(null)
  const orb2Ref    = useRef(null)
  const gridRef    = useRef(null)

  useEffect(() => {
    /* ── Parallax con el mouse ── */
    const handleMouse = e => {
      const x = e.clientX / window.innerWidth  - 0.5
      const y = e.clientY / window.innerHeight - 0.5
      gsap.to(orb1Ref.current, { x: x * -60, y: y * -40, duration: 2,   ease: 'power1.out' })
      gsap.to(orb2Ref.current, { x: x *  40, y: y *  25, duration: 2.5, ease: 'power1.out' })
      gsap.to(gridRef.current, { x: x * -20, y: y * -12, duration: 3,   ease: 'power1.out' })
    }
    window.addEventListener('mousemove', handleMouse, { passive: true })

    const ctx = gsap.context(() => {
      /* ── Estado inicial explícito ── */
      gsap.set('.hero-line',   { opacity: 0, y: 80 })
      gsap.set('.hero-sub',    { opacity: 0, y: 20 })
      gsap.set('.hero-btn',    { opacity: 0, y: 20 })
      gsap.set('.hero-stat',   { opacity: 0, y: 16 })
      gsap.set('.scroll-hint', { opacity: 0 })

      /* ── Timeline de entrada ── */
      const tl = gsap.timeline({ delay: 0.2 })
      tl.to('.hero-line',  { opacity: 1, y: 0, duration: 0.9, stagger: 0.18, ease: 'power3.out' })
        .to('.hero-sub',   { opacity: 1, y: 0, duration: 0.7, ease: 'power3.out' }, '-=0.5')
        .to('.hero-btn',   { opacity: 1, y: 0, duration: 0.6, stagger: 0.1, ease: 'power3.out' }, '-=0.4')
        .to('.hero-stat',  { opacity: 1, y: 0, duration: 0.5, stagger: 0.08, ease: 'power3.out' }, '-=0.3')
        .to('.scroll-hint',{ opacity: 1,        duration: 0.6 }, '-=0.2')

      /* ── Parallax de fondo al hacer scroll ── */
      gsap.to(orb1Ref.current, {
        y: 180, ease: 'none',
        scrollTrigger: { trigger: sectionRef.current, start: 'top top', end: 'bottom top', scrub: true },
      })
      gsap.to(orb2Ref.current, {
        y: -90, ease: 'none',
        scrollTrigger: { trigger: sectionRef.current, start: 'top top', end: 'bottom top', scrub: true },
      })

      /* ── Contenido se aleja al scrollear (efecto cinematográfico) ── */
      gsap.to('.hero-content-inner', {
        y: 80, opacity: 0.2, ease: 'none',
        scrollTrigger: { trigger: sectionRef.current, start: '15% top', end: 'bottom top', scrub: true },
      })
    }, sectionRef)

    return () => {
      ctx.revert()
      window.removeEventListener('mousemove', handleMouse)
    }
  }, [])

  return (
    <section ref={sectionRef} id="inicio" className="relative overflow-hidden min-h-screen flex items-center">
      {/* ── Fondo base ── */}
      <div className="absolute inset-0 bg-gradient-to-b from-[#071428] via-[#050d1f] to-[#050d1f]" />

      {/* ── Capa 1: orbe principal ── */}
      <div ref={orb1Ref} className="absolute inset-0 will-change-transform pointer-events-none">
        <div
          className="absolute top-1/4 left-1/2 -translate-x-1/2 w-[900px] h-[600px] rounded-full"
          style={{ background: 'radial-gradient(ellipse, rgba(33,150,243,0.28) 0%, transparent 65%)', filter: 'blur(50px)' }}
        />
      </div>

      {/* ── Capa 2: orbe secundario ── */}
      <div ref={orb2Ref} className="absolute inset-0 will-change-transform pointer-events-none">
        <div
          className="absolute top-1/2 right-1/4 w-[500px] h-[500px] rounded-full"
          style={{ background: 'radial-gradient(ellipse, rgba(99,102,241,0.12) 0%, transparent 70%)', filter: 'blur(70px)' }}
        />
      </div>

      {/* ── Capa 3: grid con parallax ── */}
      <div
        ref={gridRef}
        className="absolute inset-[-5%] will-change-transform pointer-events-none"
        style={{
          backgroundImage:
            'linear-gradient(rgba(33,150,243,1) 1px, transparent 1px), linear-gradient(90deg, rgba(33,150,243,1) 1px, transparent 1px)',
          backgroundSize: '70px 70px',
          opacity: 0.035,
        }}
      />

      {/* ── Contenido ── */}
      <div className="hero-content-inner relative z-10 w-full max-w-6xl mx-auto px-6 pt-28 pb-24">
        <div className="text-center">

          {/* Título — dos líneas que entran con stagger */}
          <h1 className="mb-10 leading-[0.95]">
            <span className="hero-line block text-5xl md:text-7xl lg:text-8xl font-light tracking-tight text-white mb-2">
              Endoscopia digital
            </span>
            <span className="hero-line block text-5xl md:text-7xl lg:text-8xl font-light tracking-tight bg-gradient-to-r from-[#2196f3] via-[#60a5fa] to-[#93c5fd] bg-clip-text text-transparent">
              potenciada con IA.
            </span>
          </h1>

          {/* Subtítulo */}
          <p className="hero-sub text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-12 leading-relaxed">
            <strong className="text-white font-medium">ENCLAII</strong> centraliza la captura,
            almacenamiento y análisis de estudios endoscópicos en la nube, con asistencia
            diagnóstica inteligente para hospitales y clínicas.
          </p>

          {/* Botones */}
          <div className="flex flex-col sm:flex-row gap-4 justify-center mb-16">
            <a
              href="#contacto"
              className="hero-btn inline-flex items-center justify-center gap-2 bg-[#2196f3] text-white
                px-9 py-4 rounded-xl font-semibold hover:bg-[#1e88e5] transition-all
                shadow-lg shadow-[#2196f3]/30 hover:shadow-[#2196f3]/50 hover:-translate-y-0.5 text-base"
            >
              Solicitar demo gratuita
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </a>
            <a
              href="#story"
              className="hero-btn inline-flex items-center justify-center bg-white/5 text-slate-200
                px-9 py-4 rounded-xl font-medium border border-white/10 hover:bg-white/10
                hover:border-white/25 transition-all text-base"
            >
              Ver cómo funciona
            </a>
          </div>

          {/* Stats rápidas */}
          <div className="grid grid-cols-3 gap-6 max-w-md mx-auto border-t border-white/5 pt-10">
            {[
              { value: '100%',  label: 'En la nube' },
              { value: 'IA',    label: 'Diagnóstico asistido' },
              { value: 'DICOM', label: 'Compatible' },
            ].map(s => (
              <div key={s.label} className="hero-stat text-center">
                <div className="text-2xl font-bold text-[#2196f3] mb-1">{s.value}</div>
                <div className="text-xs text-slate-500 uppercase tracking-widest">{s.label}</div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* Fade bottom */}
      <div className="absolute bottom-0 left-0 right-0 h-36 bg-gradient-to-t from-[#050d1f] to-transparent pointer-events-none" />

      {/* Scroll hint */}
      <div className="scroll-hint absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-slate-500">
        <span className="text-[10px] tracking-[0.4em] uppercase">Scroll</span>
        <div className="w-px h-14 bg-gradient-to-b from-[#2196f3]/60 to-transparent animate-pulse" />
      </div>
    </section>
  )
}