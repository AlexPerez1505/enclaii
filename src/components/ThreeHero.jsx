import { useRef, useEffect } from 'react'
import lottie from 'lottie-web'
import animationData from '../assets/animation.json'

/* Lottie usando lottie-web directo */
function LottieAnimation() {
  const containerRef = useRef(null)

  useEffect(() => {
    if (!containerRef.current) return
    const anim = lottie.loadAnimation({
      container: containerRef.current,
      renderer: 'svg',
      loop: true,
      autoplay: true,
      animationData: animationData,
    })
    return () => anim.destroy()
  }, [])

  return <div ref={containerRef} className="w-full h-full" />
}

export default function ThreeHero() {
  return (
    <section className="relative min-h-screen bg-[#050d1f] overflow-hidden flex items-center justify-center py-20">

      {/* Glow de fondo */}
      <div
        className="absolute inset-0 opacity-50 pointer-events-none"
        style={{
          background:
            'radial-gradient(ellipse 60% 60% at 50% 50%, rgba(33,150,243,0.25), transparent 70%)',
        }}
      />

      {/* Grid decorativo sutil */}
      <div
        className="absolute inset-0 opacity-[0.04] pointer-events-none"
        style={{
          backgroundImage:
            'linear-gradient(rgba(33,150,243,1) 1px, transparent 1px), linear-gradient(90deg, rgba(33,150,243,1) 1px, transparent 1px)',
          backgroundSize: '70px 70px',
        }}
      />

      {/* Contenido: texto + Lottie */}
      <div className="relative z-10 flex flex-col items-center justify-center px-6 gap-4">

        <span className="text-xs tracking-[0.4em] uppercase text-[#2196f3]">
          Tecnología en movimiento
        </span>

        <h2 className="text-4xl md:text-6xl lg:text-7xl font-light text-white text-center leading-[0.95] drop-shadow-[0_0_40px_rgba(33,150,243,0.5)]">
          Endoscopia
          <br />
          <span className="bg-gradient-to-r from-[#2196f3] via-[#60a5fa] to-[#a855f7] bg-clip-text text-transparent">
            en otra dimensión.
          </span>
        </h2>

        {/* Lottie protagonista con glow azul */}
        <div className="w-[320px] h-[320px] md:w-[450px] md:h-[450px] lg:w-[550px] lg:h-[550px]
          drop-shadow-[0_0_80px_rgba(33,150,243,0.7)]">
          <LottieAnimation />
        </div>
      </div>
    </section>
  )
}