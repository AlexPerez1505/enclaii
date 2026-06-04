import { useEffect, useRef } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

const TOTAL_FRAMES = 50
const FRAME_PATH = (i) => `/frames/frame_${String(i).padStart(4, '0')}.jpg`

export default function ScrubVideo() {
  const sectionRef = useRef(null)
  const canvasRef = useRef(null)

  useEffect(() => {
    const canvas = canvasRef.current
    const ctx = canvas.getContext('2d')
    const images = []
    const obj = { frame: 0 }

    /* Carga todas las imágenes */
    for (let i = 1; i <= TOTAL_FRAMES; i++) {
      const img = new Image()
      img.src = FRAME_PATH(i)
      images.push(img)
    }

    const resize = () => {
      canvas.width  = window.innerWidth
      canvas.height = window.innerHeight
    }
    resize()
    window.addEventListener('resize', resize)

    const render = () => {
      const img = images[Math.floor(obj.frame)]
      if (!img || !img.complete) return
      ctx.clearRect(0, 0, canvas.width, canvas.height)
      // Cover: rellena toda la pantalla manteniendo aspect
      const scale = Math.max(canvas.width / img.width, canvas.height / img.height)
      const w = img.width * scale
      const h = img.height * scale
      const x = (canvas.width - w) / 2
      const y = (canvas.height - h) / 2
      ctx.drawImage(img, x, y, w, h)
    }

    images[0].onload = render

    const st = gsap.to(obj, {
      frame: TOTAL_FRAMES - 1,
      snap: 'frame',
      ease: 'none',
      scrollTrigger: {
        trigger: sectionRef.current,
        start: 'top top',
        end: 'bottom bottom',
        scrub: 0.5,
        pin: '.scrub-pin',
      },
      onUpdate: render,
    })

    return () => {
      st.scrollTrigger?.kill()
      st.kill()
      window.removeEventListener('resize', resize)
    }
  }, [])

  return (
    <section ref={sectionRef} className="relative h-[400vh] bg-black">
      <div className="scrub-pin h-screen w-full overflow-hidden">
        <canvas ref={canvasRef} className="w-full h-full" />
      </div>
    </section>
  )
}