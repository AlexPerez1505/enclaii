import { useEffect, useRef, useState } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

export default function Hero() {
  const sectionRef = useRef(null)
  const orb1Ref = useRef(null)
  const orb2Ref = useRef(null)
  const gridRef = useRef(null)
  const [isLight, setIsLight] = useState(() => {
    if (typeof document === 'undefined') return false
    return document.documentElement.getAttribute('data-theme') === 'light'
  })

  useEffect(() => {
    const syncTheme = () => {
      setIsLight(document.documentElement.getAttribute('data-theme') === 'light')
    }

    syncTheme()

    const themeObserver = new MutationObserver(syncTheme)
    themeObserver.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['data-theme'],
    })

    const handleMouse = e => {
      const x = e.clientX / window.innerWidth - 0.5
      const y = e.clientY / window.innerHeight - 0.5
      gsap.to(orb1Ref.current, { x: x * -36, y: y * -26, duration: 2, ease: 'power1.out' })
      gsap.to(orb2Ref.current, { x: x * 28, y: y * 18, duration: 2.5, ease: 'power1.out' })
      gsap.to(gridRef.current, { x: x * -14, y: y * -8, duration: 3, ease: 'power1.out' })
      gsap.to('.hero-visual', { x: x * 14, y: y * 8, duration: 2.4, ease: 'power1.out' })
    }
    window.addEventListener('mousemove', handleMouse, { passive: true })

    const ctx = gsap.context(() => {
      gsap.set('.hero-line', { opacity: 0, y: 60 })
      gsap.set('.hero-sub', { opacity: 0, y: 22 })
      gsap.set('.hero-btn', { opacity: 0, y: 20 })
      gsap.set('.hero-stat', { opacity: 0, y: 16 })
      gsap.set('.hero-visual', { opacity: 0, scale: 1.04 })
      gsap.set('.scroll-hint', { opacity: 0 })

      const tl = gsap.timeline({ delay: 0.2 })
      tl.to('.hero-visual', { opacity: 1, scale: 1, duration: 1, ease: 'power3.out' })
        .to('.hero-line', { opacity: 1, y: 0, duration: 0.85, stagger: 0.14, ease: 'power3.out' }, '-=0.65')
        .to('.hero-sub', { opacity: 1, y: 0, duration: 0.65, ease: 'power3.out' }, '-=0.4')
        .to('.hero-btn', { opacity: 1, y: 0, duration: 0.55, stagger: 0.1, ease: 'power3.out' }, '-=0.3')
        .to('.hero-stat', { opacity: 1, y: 0, duration: 0.5, stagger: 0.08, ease: 'power3.out' }, '-=0.25')
        .to('.scroll-hint', { opacity: 1, duration: 0.6 }, '-=0.2')

      gsap.to(orb1Ref.current, {
        y: 140,
        ease: 'none',
        scrollTrigger: { trigger: sectionRef.current, start: 'top top', end: 'bottom top', scrub: true },
      })
      gsap.to(orb2Ref.current, {
        y: -70,
        ease: 'none',
        scrollTrigger: { trigger: sectionRef.current, start: 'top top', end: 'bottom top', scrub: true },
      })
      gsap.to('.hero-content-inner', {
        y: 60,
        opacity: 0.18,
        ease: 'none',
        scrollTrigger: { trigger: sectionRef.current, start: '15% top', end: 'bottom top', scrub: true },
      })
    }, sectionRef)

    return () => {
      themeObserver.disconnect()
      ctx.revert()
      window.removeEventListener('mousemove', handleMouse)
    }
  }, [])

  return (
    <section ref={sectionRef} id="inicio" className="relative h-screen min-h-[720px] overflow-hidden flex items-center">
      <div className={isLight ? "absolute inset-0 bg-[#f8fbff]" : "absolute inset-0 bg-gradient-to-b from-[#071428] via-[#050d1f] to-[#050d1f]"} />

      <div className="hero-visual absolute inset-0 z-0 pointer-events-none">
        <video
          key={isLight ? 'hero-light-video' : 'hero-dark-video'}
          src={isLight ? '/videos/cuerpo2-blanco.mp4' : '/videos/cuerpo2-azul.mp4'}
          autoPlay
          muted
          loop
          playsInline
          className="absolute inset-0 h-full w-full object-cover object-[36%_center] opacity-95"
        />
        {isLight && (
          <img
            src="/tapa.png"
            alt=""
            aria-hidden="true"
            className="absolute bottom-[11vh] right-[4vw] w-[62px] md:w-[76px] lg:w-[88px] select-none opacity-95"
          />
        )}
        {!isLight && (
          <img
            src="/tapa-azul.png"
            alt=""
            aria-hidden="true"
            className="absolute bottom-[12vh] right-[3.5vw] w-[62px] md:w-[76px] lg:w-[88px] select-none opacity-100 [mask-image:radial-gradient(ellipse_at_center,black_76%,transparent_96%)]"
          />
        )}
      </div>

      <div ref={orb1Ref} className="absolute inset-0 will-change-transform pointer-events-none">
        <div
          className="absolute top-1/4 left-1/2 -translate-x-1/2 w-[900px] h-[600px] rounded-full"
          style={{ background: 'radial-gradient(ellipse, rgba(33,150,243,0.2) 0%, transparent 65%)', filter: 'blur(50px)' }}
        />
      </div>

      <div ref={orb2Ref} className="absolute inset-0 will-change-transform pointer-events-none">
        <div
          className="absolute top-1/2 right-1/4 w-[500px] h-[500px] rounded-full"
          style={{ background: 'radial-gradient(ellipse, rgba(99,102,241,0.1) 0%, transparent 70%)', filter: 'blur(70px)' }}
        />
      </div>

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

      <div className={isLight ? "absolute inset-y-0 left-0 z-[1] w-3/5 bg-gradient-to-r from-white/96 via-white/72 to-transparent pointer-events-none" : "absolute inset-y-0 left-0 z-[1] w-3/5 bg-gradient-to-r from-[#050d1f]/94 via-[#050d1f]/66 to-transparent pointer-events-none"} />
      <div className={isLight ? "absolute inset-y-0 right-0 z-[1] w-2/5 bg-gradient-to-l from-white/36 to-transparent pointer-events-none" : "absolute inset-y-0 right-0 z-[1] w-2/5 bg-gradient-to-l from-[#050d1f]/36 to-transparent pointer-events-none"} />

      <div className="hero-content-inner relative z-10 w-full max-w-7xl mx-auto px-6 pt-24 pb-10">
        <div className="relative h-[calc(100vh-8rem)] min-h-[560px]">
          <div className="absolute left-0 top-[45%] w-full max-w-[620px] -translate-y-1/2 text-center lg:text-left">
            <span className="hero-line mb-4 inline-block text-xs font-semibold uppercase tracking-[0.32em] text-[#60a5fa]">
              Plataforma clinica inteligente
            </span>

            <h1 className="mb-6 leading-[0.98]">
              <span className={isLight ? "hero-line block text-4xl md:text-5xl lg:text-6xl font-light tracking-tight text-slate-950 mb-2" : "hero-line block text-4xl md:text-5xl lg:text-6xl font-light tracking-tight text-white mb-2"}>
                Endoscopia digital
              </span>
              <span className="hero-line block text-4xl md:text-5xl lg:text-6xl font-light tracking-tight bg-gradient-to-r from-[#2196f3] via-[#60a5fa] to-[#93c5fd] bg-clip-text text-transparent">
                potenciada con IA.
              </span>
            </h1>

            <p className={isLight ? "hero-sub text-base md:text-lg text-slate-700 max-w-lg mx-auto lg:mx-0 mb-8 leading-relaxed" : "hero-sub text-base md:text-lg text-slate-300 max-w-lg mx-auto lg:mx-0 mb-8 leading-relaxed"}>
              <strong className={isLight ? "text-slate-950 font-medium" : "text-white font-medium"}>ENCLAII</strong> centraliza captura,
              almacenamiento y analisis de estudios endoscopicos en la nube, con asistencia
              diagnostica para hospitales y clinicas.
            </p>

            <div className="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
              <a
                href="#contacto"
                className="hero-btn inline-flex items-center justify-center gap-2 bg-[#2196f3] text-white
                  px-7 py-3.5 rounded-xl font-semibold hover:bg-[#1e88e5] transition-all
                  shadow-lg shadow-[#2196f3]/30 hover:shadow-[#2196f3]/50 hover:-translate-y-0.5 text-base"
              >
                Solicitar demo gratuita
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </a>
              <a
                href="#story"
                className={isLight ? "hero-btn inline-flex items-center justify-center bg-white/70 text-slate-800 px-7 py-3.5 rounded-xl font-medium border border-slate-300/70 hover:bg-white hover:border-slate-400/80 transition-all text-base" : "hero-btn inline-flex items-center justify-center bg-white/5 text-slate-200 px-7 py-3.5 rounded-xl font-medium border border-white/10 hover:bg-white/10 hover:border-white/25 transition-all text-base"}
              >
                Ver como funciona
              </a>
            </div>
          </div>

          <div className={isLight ? "absolute bottom-6 right-0 grid w-full max-w-lg grid-cols-3 gap-4 border-t border-slate-300/70 pt-6" : "absolute bottom-6 right-0 grid w-full max-w-lg grid-cols-3 gap-4 border-t border-white/10 pt-6"}>
            {[
              { value: 'Nube', label: 'Estudios disponibles' },
              { value: 'IA', label: 'Apoyo diagnostico' },
              { value: 'DICOM', label: 'Flujo compatible' },
            ].map(s => (
              <div key={s.label} className="hero-stat">
                <div className="text-xl md:text-2xl font-bold text-[#60a5fa] mb-1">{s.value}</div>
                <div className={isLight ? "text-[10px] md:text-xs text-slate-600 uppercase tracking-widest leading-relaxed" : "text-[10px] md:text-xs text-slate-400 uppercase tracking-widest leading-relaxed"}>{s.label}</div>
              </div>
            ))}
          </div>
        </div>
      </div>      <div className="absolute bottom-0 left-0 right-0 z-[1] h-36 bg-gradient-to-t from-[#050d1f] to-transparent pointer-events-none" />

      <div className="scroll-hint absolute bottom-8 left-1/2 z-20 -translate-x-1/2 flex flex-col items-center gap-2 text-slate-500">
        <span className="text-[10px] tracking-[0.4em] uppercase">Scroll</span>
        <div className="w-px h-14 bg-gradient-to-b from-[#2196f3]/60 to-transparent animate-pulse" />
      </div>
    </section>
  )
}



