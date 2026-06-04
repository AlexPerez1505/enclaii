import { useEffect } from 'react'
import { Routes, Route } from 'react-router-dom'
import Lenis from 'lenis'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

import Navbar from './components/Navbar'
import { SmoothCursor } from './components/SmoothCursor'
import Hero from './components/Hero'
import Stats from './components/Stats'
import ScrollStory from './components/ScrollStory'
import ThreeHero from './components/ThreeHero'
// import LottieScrub from './components/LottieScrub'
import Pilares from './components/Pilares'
import ComoFunciona from './components/ComoFunciona'
import ScrubVideo from './components/ScrubVideo'
import Modulos from './components/Modulos'
import CTA from './components/CTA'
import Footer from './components/Footer'
import Contacto from './components/Contacto'
import PorQue from './components/PorQue'

gsap.registerPlugin(ScrollTrigger)

function Home() {
  return (
    <main>
      <Hero />
      <Stats />
      <ScrollStory />
      <ThreeHero />
      <Pilares />
      {/* <LottieScrub /> */}
      <ComoFunciona />
      <ScrubVideo />
      <Modulos />
      <CTA />
    </main>
  )
}

export default function App() {
  useEffect(() => {
    const lenis = new Lenis({
      duration: 1.4,
      easing: t => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
      smoothWheel: true,
    })

    lenis.on('scroll', ScrollTrigger.update)
    gsap.ticker.add(time => lenis.raf(time * 1000))
    gsap.ticker.lagSmoothing(0)

    return () => { lenis.destroy() }
  }, [])

  return (
    <div className="min-h-screen bg-[#050d1f] text-slate-200 overflow-x-hidden">
      <SmoothCursor />
      <Navbar />
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/contacto" element={<Contacto />} />
        <Route path="/por-que" element={<PorQue />} />
      </Routes>
      <Footer />
    </div>
  )
}