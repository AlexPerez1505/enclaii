import { useRef, useEffect, useState } from 'react'
import { motion, useSpring, useMotionValue, useAnimate } from 'framer-motion'
import gsap from 'gsap'
import ScrollTrigger from 'gsap/ScrollTrigger'

gsap.registerPlugin(ScrollTrigger)

const REST_TILT = [
  { rot: -3.5, tx: -10 },
  { rot:  2.8, tx:   8 },
  { rot: -2.0, tx:  -6 },
  { rot:  1.5, tx:   5 },
]

const PASOS = [
  {
    n: '01',
    title: 'Conecta el equipo',
    text: 'ENCLAII se integra con tu torre endoscópica. Detecta automáticamente la conexión de los dispositivos de captura.',
    features: ['Compatible con marcas principales como Olympus', 'Instalación en 1 día', 'Sin cambiar tu flujo actual'],
    color: '#2196f3',
    icon: (
      <svg viewBox="0 0 64 64" className="w-12 h-12" fill="none" stroke="currentColor" strokeWidth="1.5">
        <rect x="8" y="20" width="20" height="28" rx="3" />
        <rect x="36" y="14" width="20" height="36" rx="3" />
        <line x1="28" y1="34" x2="36" y2="34" />
        <circle cx="48" cy="32" r="5" />
        <circle cx="18" cy="34" r="5" />
      </svg>
    ),
  },
  {
    n: '02',
    title: 'Captura el estudio',
    text: 'Imágenes y video se digitalizan en tiempo real y se asocian automáticamente al expediente del paciente desde la primera captura en la base de datos que se almacena en la nube, la cual te permite acceder a ellos desde cualquier dispositivo.',
    features: ['Alta resolución 4K', 'Video en tiempo real', 'Metadatos automáticos'],
    color: '#06b6d4',
    icon: (
      <svg viewBox="0 0 64 64" className="w-12 h-12" fill="none" stroke="currentColor" strokeWidth="1.5">
        <circle cx="32" cy="32" r="16" />
        <circle cx="32" cy="32" r="6" />
        <line x1="32" y1="10" x2="32" y2="16" />
        <line x1="32" y1="48" x2="32" y2="54" />
        <line x1="10" y1="32" x2="16" y2="32" />
        <line x1="48" y1="32" x2="54" y2="32" />
      </svg>
    ),
  },
  {
    n: '03',
    title: 'IA analiza hallazgos',
    text: 'Los modelos de inteligencia artificial analizan cada frame. Destacan automáticamente pólipos, lesiones o irregularidades para revisión del especialista herramienta de apoyo para los medicos para un diagnostico más preciso.',
    features: ['Análisis en tiempo real', 'Alta sensibilidad diagnóstica', 'Aprendizaje continuo'],
    color: '#a855f7',
    icon: (
      <svg viewBox="0 0 64 64" className="w-12 h-12" fill="none" stroke="currentColor" strokeWidth="1.5">
        <path d="M20 44 L32 16 L44 44" />
        <line x1="24" y1="36" x2="40" y2="36" />
        <circle cx="32" cy="54" r="4" />
        <path d="M14 20 Q10 32 14 44" />
        <path d="M50 20 Q54 32 50 44" />
      </svg>
    ),
  },
  {
    n: '04',
    title: 'Genera el reporte',
    text: 'Crea reportes clínicos estructurados en segundos. Compártelos con el paciente o médico referido vía correo, PDF o enlace seguro con QR.',
    features: ['Plantillas clínicas', 'Firma digital incluida', 'Acceso 24/7 en la nube'],
    color: '#10b981',
    icon: (
      <svg viewBox="0 0 64 64" className="w-12 h-12" fill="none" stroke="currentColor" strokeWidth="1.5">
        <rect x="14" y="8" width="36" height="48" rx="4" />
        <line x1="22" y1="24" x2="42" y2="24" />
        <line x1="22" y1="32" x2="38" y2="32" />
        <line x1="22" y1="40" x2="36" y2="40" />
        <polyline points="24,16 28,20 36,12" />
      </svg>
    ),
  },
]

const SPRING   = { stiffness: 55, damping: 22, mass: 1 }
const N        = PASOS.length
const SEG      = 1 / N
const STACK_OFFSET = 22

// Clamp + remap lineal
function remap(value, inMin, inMax, outMin, outMax) {
  const t = Math.min(Math.max((value - inMin) / (inMax - inMin), 0), 1)
  return outMin + (outMax - outMin) * t
}

export default function ComoFunciona() {
  const sectionRef = useRef(null)
  const panelRef   = useRef(null)

  // Shared progress MotionValue driven by GSAP ScrollTrigger
  const progress = useMotionValue(0)

  useEffect(() => {
    const trigger = ScrollTrigger.create({
      trigger: sectionRef.current,
      pin:     panelRef.current,
      start:   'top top',
      end:     `+=${N * window.innerHeight}`,
      scrub:   0.5,
      onUpdate: (self) => progress.set(self.progress),
    })

    return () => trigger.kill()
  }, [])

  return (
    <section id="como-funciona" ref={sectionRef} className="bg-gradient-to-b from-[#050d1f] to-[#0a1733]">
      <div
        ref={panelRef}
        className="h-screen flex flex-col items-center justify-center relative overflow-hidden"
      >
        {/* Título */}
        <div className="absolute top-20 inset-x-0 text-center pointer-events-none select-none z-10">
          <p className="text-[10px] text-[#2196f3] tracking-[0.4em] uppercase mb-1">Flujo de trabajo</p>
          <h2 className="text-2xl md:text-4xl font-light text-white">Cómo funciona ENCLAII</h2>
        </div>

        {/* Stack */}
        <div className="relative w-[min(480px,90vw)]" style={{ height: 'min(380px,55vh)' }}>
          {PASOS.map((paso, i) => (
            <StickyCard
              key={paso.n}
              i={i}
              total={N}
              {...paso}
              progress={progress}
            />
          ))}
        </div>

        {/* Progress dots */}
        <div className="absolute bottom-8 flex gap-2 z-20">
          {PASOS.map((p, i) => (
            <ProgressDot key={i} index={i} color={p.color} progress={progress} />
          ))}
        </div>

        {/* Luces azules que dejan caminos de luz */}
        <LightTrails />
      </div>
    </section>
  )
}

function StickyCard({ i, total, n, title, text, features, color, icon, progress }) {
  const [theme, setTheme] = useState('dark')
  const topOffset   = (total - 1 - i) * STACK_OFFSET
  const isStackable = i < total - 1

  useEffect(() => {
    const checkTheme = () => {
      const currentTheme = document.documentElement.getAttribute('data-theme')
      setTheme(currentTheme === 'light' ? 'light' : 'dark')
    }
    
    checkTheme()
    const observer = new MutationObserver(checkTheme)
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] })
    
    return () => observer.disconnect()
  }, [])

  // Ranges for this card
  const enterStart = i * SEG
  const enterEnd   = enterStart + SEG * 0.5

  const stackStart = (i + 1) * SEG
  const stackEnd   = stackStart + SEG * 0.55

  const tiltStart  = stackStart + SEG * 0.1
  const tiltEnd    = tiltStart  + SEG * 0.55

  const targetScale = Math.max(0.82, 1 - (total - i - 1) * 0.055)

  // Raw MotionValues — start off-screen except card 0
  const rawY       = useMotionValue(i === 0 ? topOffset : 600)
  const rawScale   = useMotionValue(1)
  const rawOpacity = useMotionValue(i === 0 ? 1 : 0)
  const rawRot     = useMotionValue(0)
  const rawTX      = useMotionValue(0)

  useEffect(() => {
    const unsubscribe = progress.on('change', (p) => {
      // Y: slide up from below into resting position
      rawY.set(remap(p, enterStart, enterEnd, 600, topOffset))

      // Once entered, show it
      if (p >= enterStart) rawOpacity.set(1)

      // Stack effects only for cards that get buried
      if (isStackable) {
        rawScale.set(remap(p, stackStart, stackEnd, 1, targetScale))
        rawRot.set(remap(p, tiltStart, tiltEnd, 0, REST_TILT[i].rot))
        rawTX.set(remap(p, tiltStart, tiltEnd, 0, REST_TILT[i].tx))
      }
    })

    return () => unsubscribe()
  }, [])

  const y       = useSpring(rawY,       SPRING)
  const scale   = useSpring(rawScale,   SPRING)
  const opacity = useSpring(rawOpacity, SPRING)
  const rotate  = useSpring(rawRot,     SPRING)
  const x       = useSpring(rawTX,      SPRING)

  return (
    <motion.div
      style={{
        position: 'absolute',
        inset: 0,
        top: -topOffset,
        y, scale, opacity, rotate, x,
        zIndex: i + 1,
        transformOrigin: 'top center',
      }}
    >
      <div
        className="w-full h-full rounded-2xl p-7 md:p-10 flex flex-col gap-5 relative overflow-hidden"
        style={{
          background: theme === 'light' 
            ? 'linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%)' 
            : 'linear-gradient(135deg, #0d1f3c 0%, #0a1628 100%)',
          border: `1px solid ${theme === 'light' ? color : color}55`,
          backdropFilter: 'blur(14px)',
          boxShadow: theme === 'light'
            ? `0 30px 80px rgba(0,0,0,.15), 0 0 0 1px rgba(0,0,0,.06)`
            : `0 30px 80px rgba(0,0,0,.55), 0 0 0 1px rgba(255,255,255,.06)`,
        }}
      >
        <div className="flex items-center gap-3">
          <span className="text-xs font-bold tracking-[.3em] uppercase" style={{ color }}>{n}</span>
          <div className="h-px flex-1" style={{ background: `${color}44` }} />
        </div>

        <div className="flex items-start gap-4">
          <div style={{ color }} className="shrink-0">{icon}</div>
          <h3 className="text-2xl md:text-3xl font-light leading-snug" style={{ color: theme === 'light' ? '#1e293b' : 'white' }}>{title}</h3>
        </div>

        <p className="text-sm md:text-base leading-relaxed" style={{ color: theme === 'light' ? '#475569' : 'rgba(255,255,255,0.55)' }}>{text}</p>

        <ul className="mt-auto space-y-2">
          {features.map((f, fi) => (
            <li key={fi} className="flex items-center gap-2 text-xs" style={{ color: theme === 'light' ? '#64748b' : 'rgba(255,255,255,0.45)' }}>
              <span className="w-1.5 h-1.5 rounded-full shrink-0" style={{ background: color }} />
              {f}
            </li>
          ))}
        </ul>

        <div
          className="absolute -top-10 -right-10 w-44 h-44 rounded-full pointer-events-none"
          style={{ background: `radial-gradient(circle,${color}22 0%,transparent 70%)` }}
        />
      </div>
    </motion.div>
  )
}

function ProgressDot({ index, color, progress }) {
  const raw   = useMotionValue(0)
  const scale = useSpring(raw, { stiffness: 120, damping: 20 })

  useEffect(() => {
    const unsubscribe = progress.on('change', (p) => {
      raw.set(remap(p, index * SEG, (index + 1) * SEG, 0, 1))
    })
    return () => unsubscribe()
  }, [])

  return (
    <motion.div
      style={{ scale, backgroundColor: color }}
      className="w-1.5 h-1.5 rounded-full opacity-70"
    />
  )
}

function LightTrails() {
  const [lights, setLights] = useState([])

  useEffect(() => {
    const newLights = Array.from({ length: 30 }, (_, i) => ({
      id: i,
      x: Math.random() * 100,
      y: Math.random() * 100,
      size: Math.random() * 8 + 4,
      duration: Math.random() * 3 + 2,
      delay: Math.random() * 2,
    }))
    setLights(newLights)
  }, [])

  return (
    <div className="absolute inset-0 pointer-events-none overflow-hidden">
      {lights.map((light) => (
        <motion.div
          key={light.id}
          className="absolute rounded-full"
          style={{
            width: light.size,
            height: light.size,
            background: 'rgba(33, 150, 243, 0.6)',
            boxShadow: '0 0 20px rgba(33, 150, 243, 0.8), 0 0 40px rgba(33, 150, 243, 0.4)',
            left: `${light.x}%`,
            top: `${light.y}%`,
          }}
          animate={{
            x: [0, Math.random() * 200 - 100],
            y: [0, Math.random() * 300 - 150],
            opacity: [0.3, 0.8, 0.3],
          }}
          transition={{
            duration: light.duration,
            repeat: Infinity,
            repeatType: 'reverse',
            delay: light.delay,
            ease: 'easeInOut',
          }}
        />
      ))}
    </div>
  )
}
