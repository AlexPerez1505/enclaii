import { useEffect, useRef } from 'react'
import Lottie from 'lottie-react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import animationData from '../assets/animation.json' // descarga uno de lottiefiles.com

export default function LottieScrub() {
  const sectionRef = useRef(null)
  const lottieRef = useRef(null)

  useEffect(() => {
    if (!lottieRef.current) return
    const totalFrames = lottieRef.current.getDuration(true)
    const obj = { frame: 0 }

    const st = gsap.to(obj, {
      frame: totalFrames - 1,
      ease: 'none',
      scrollTrigger: {
        trigger: sectionRef.current,
        start: 'top top',
        end: 'bottom bottom',
        scrub: 1,
        pin: '.lottie-pin',
      },
      onUpdate: () => lottieRef.current?.goToAndStop(obj.frame, true),
    })

    return () => st.scrollTrigger?.kill()
  }, [])

  return (
    <section ref={sectionRef} className="h-[300vh] bg-[#050d1f]">
      <div className="lottie-pin h-screen flex items-center justify-center">
        <div className="w-[600px] h-[600px]">
          <Lottie
            lottieRef={lottieRef}
            animationData={animationData}
            autoplay={false}
            loop={false}
          />
        </div>
      </div>
    </section>
  )
}