import { useEffect, useRef, useState } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'

/* ── Capítulos de la historia de ENCLAII ── */
const CHAPTERS = [
  {
    num: '01',
    tag: 'Todo en linea',
    title: 'Los estudios quedan\ndisponibles\nen la pagina web',
    text: 'Los equipos de endoscopia generan imágenes y video que terminan en servidores de la nube, Donde se puede acceder a los registros de pacientes, estudios, informes y desde ahi puedas editar, borrar o incluso mandar sus estudios a pacientes sin necesidad de estar en la computadora local',
    accent: '#ef4444',
    bg: 'radial-gradient(ellipse 70% 60% at 20% 50%, rgba(239,68,68,0.12) 0%, transparent 70%)',
    visual: <Visual1 />,
  },
  {
    num: '02',
    tag: 'La captura',
    title: 'ENCLAII se conecta\na tu equipo y captura\nen tiempo real',
    text: 'Sin pasos adicionales para el médico. ENCLAII se integra con tu torre endoscópica y digitaliza cada estudio de forma automática, asociándolo al expediente del paciente desde el primer segundo.',
    accent: '#2196f3',
    bg: 'radial-gradient(ellipse 70% 60% at 20% 50%, rgba(33,150,243,0.14) 0%, transparent 70%)',
    visual: <Visual2 />,
  },
  {
    num: '03',
    tag: 'Inteligencia Artificial',
    title: 'La IA detecta\nlo que el ojo\npuede perder',
    text: 'Los modelos de inteligencia artificial de ENCLAII analizan cada frame en tiempo real. Marcan automáticamente hallazgos relevantes como pólipos, lesiones o irregularidades, apoyando el criterio del especialista con evidencia objetiva.',
    accent: '#a855f7',
    bg: 'radial-gradient(ellipse 70% 60% at 20% 50%, rgba(168,85,247,0.14) 0%, transparent 70%)',
    visual: <Visual3 />,
  },
  {
    num: '04',
    tag: 'El resultado: Reporte Médico CON AI',
    title: 'Reportes completos\nal instante, desde\ncualquier lugar',
    text: 'Con ENCLAII, el especialista genera reportes clínicos estructurados en segundos. Accesibles desde cualquier dispositivo, compartibles con el paciente o referido de forma segura. Mejor atención, más rápida.',
    accent: '#10b981',
    bg: 'radial-gradient(ellipse 70% 60% at 20% 50%, rgba(16,185,129,0.12) 0%, transparent 70%)',
    visual: <Visual4 />,
  },
]

export default function ScrollStory() {
  const sectionRef = useRef(null)
  const [theme, setTheme] = useState('dark')

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

  useEffect(() => {
    const ctx = gsap.context(() => {
      const panels  = gsap.utils.toArray('.ss-panel',  sectionRef.current)
      const bgs     = gsap.utils.toArray('.ss-bg',     sectionRef.current)
      const dots    = gsap.utils.toArray('.ss-dot',    sectionRef.current)
      const visuals = gsap.utils.toArray('.ss-visual', sectionRef.current)

      /* Estado inicial: todo oculto salvo capítulo 0 */
      gsap.set(panels.slice(1),  { opacity: 0, y: 50 })
      gsap.set(visuals.slice(1), { opacity: 0, scale: 0.85 })
      gsap.set(bgs.slice(1),     { opacity: 0 })
      gsap.set(dots,             { scale: 1, opacity: 0.25 })
      gsap.set(dots[0],          { scale: 1.8, opacity: 1 })

      /* Timeline maestro pineado + scrub + snap */
      const tl = gsap.timeline({
        scrollTrigger: {
          trigger: sectionRef.current,
          start: 'top top',
          end: () => `+=${(CHAPTERS.length - 1) * window.innerHeight}`,
          pin: true,
          pinSpacing: true,
          scrub: 1,
          anticipatePin: 1,
          invalidateOnRefresh: true,
          snap: {
            snapTo: 1 / (CHAPTERS.length - 1),
            duration: { min: 0.2, max: 0.5 },
            delay: 0.08,
            ease: 'power2.inOut',
          },
        },
      })

      /* Construye crossfades capítulo a capítulo */
      for (let i = 0; i < CHAPTERS.length - 1; i++) {
        const out = i
        const inn = i + 1

        tl.to(panels[out],  { opacity: 0, y: -50,     duration: 0.5, ease: 'power2.in' }, i)
          .to(visuals[out], { opacity: 0, scale: 0.85, duration: 0.5, ease: 'power2.in' }, i)
          .to(bgs[out],     { opacity: 0,              duration: 0.5 }, i)
          .to(dots[out],    { scale: 1, opacity: 0.25, duration: 0.4 }, i)

        tl.fromTo(panels[inn],
          { opacity: 0, y: 50 },
          { opacity: 1, y: 0, duration: 0.5, ease: 'power2.out' }, i + 0.4)
         .fromTo(visuals[inn],
          { opacity: 0, scale: 0.85 },
          { opacity: 1, scale: 1, duration: 0.5, ease: 'power2.out' }, i + 0.4)
         .to(bgs[inn],  { opacity: 1, duration: 0.5 }, i + 0.4)
         .to(dots[inn], { scale: 1.8, opacity: 1, duration: 0.4 }, i + 0.4)
      }
    }, sectionRef)

    return () => ctx.revert()
  }, [])

  return (
    <section ref={sectionRef} id="story" className="relative">
      <div className="h-screen overflow-hidden relative">

        {/* Fondos por capítulo */}
        {CHAPTERS.map((ch, i) => (
          <div
            key={i}
            className="ss-bg absolute inset-0"
            style={{ 
              background: theme === 'light'
                ? `${ch.bg}, linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%)`
                : `${ch.bg}, linear-gradient(135deg, #050d1f 0%, #0a1733 100%)`
            }}
          />
        ))}

        {/* Grid decorativo fijo */}
        <div
          className="absolute inset-0 pointer-events-none"
          style={{
            backgroundImage: theme === 'light'
              ? 'linear-gradient(rgba(0,0,0,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.05) 1px, transparent 1px)'
              : 'linear-gradient(rgba(255,255,255,1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,1) 1px, transparent 1px)',
            backgroundSize: '80px 80px',
            opacity: theme === 'light' ? 0.03 : 0.02,
          }}
        />

        {/* Layout principal */}
        <div className="relative z-10 h-full grid grid-cols-1 md:grid-cols-2 max-w-6xl mx-auto px-6">

          {/* Columna izquierda: texto */}
          <div className="relative flex items-center py-24">
            {CHAPTERS.map((ch, i) => (
              <div key={i} className="ss-panel absolute max-w-lg pr-8">
                <div className="flex items-center gap-3 mb-6">
                  <span
                    className="text-xs font-bold tracking-[0.35em] uppercase"
                    style={{ color: ch.accent }}
                  >
                    {ch.num}
                  </span>
                  <div className="h-px w-8" style={{ background: ch.accent }} />
                  <span className="text-xs tracking-widest uppercase" style={{ color: theme === 'light' ? '#64748b' : '#64748b' }}>{ch.tag}</span>
                </div>

                <h2 className="text-3xl md:text-5xl font-light leading-tight mb-6 whitespace-pre-line" style={{ color: theme === 'light' ? '#1e293b' : 'white' }}>
                  {ch.title}
                </h2>

                <p className="text-base md:text-lg leading-relaxed" style={{ color: theme === 'light' ? '#475569' : '#94a3b8' }}>
                  {ch.text}
                </p>

                <div className="flex items-center gap-2 mt-8 text-xs uppercase tracking-widest" style={{ color: theme === 'light' ? '#94a3b8' : '#475569' }}>
                  <span>{i + 1}</span>
                  <span>/</span>
                  <span>{CHAPTERS.length}</span>
                </div>
              </div>
            ))}
          </div>

          {/* Columna derecha: visual */}
          <div className="hidden md:flex items-center justify-center py-24 relative">
            {CHAPTERS.map((ch, i) => (
              <div key={i} className="ss-visual absolute inset-0 flex items-center justify-center">
                {ch.visual}
              </div>
            ))}
          </div>
        </div>

        {/* Puntos de progreso lateral */}
        <div className="absolute right-6 md:right-10 top-1/2 -translate-y-1/2 flex flex-col gap-4 z-20">
          {CHAPTERS.map((ch, i) => (
            <button
              key={i}
              className="ss-dot w-2 h-2 rounded-full transition-colors"
              style={{ background: ch.accent }}
              aria-label={`Ir al capítulo ${i + 1}`}
            />
          ))}
        </div>

        {/* Nombre de sección fijo arriba */}
        <div className="absolute top-12 left-0 right-0 h-20 flex items-end justify-center pb-4 z-10 pointer-events-none">
          <span className="text-[10px] tracking-[0.5em] uppercase" style={{ color: theme === 'light' ? '#94a3b8' : '#475569' }}>Beneficios de ENCLAII</span>
        </div>
      </div>
    </section>
  )
}

/* ════════════════════════════════════════════════
   Visuales SVG animados por capítulo
════════════════════════════════════════════════ */

/* Capítulo 1 — Interfaz Médica con ondas biométricas */
function Visual1() {
  return (
    <svg viewBox="0 0 380 380" className="w-72 h-72 md:w-96 md:h-96">
      <defs>
        <filter id="glow1">
          <feGaussianBlur stdDeviation="4" result="blur" />
          <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
        </filter>
        <radialGradient id="coreGradient1" cx="50%" cy="50%" r="50%">
          <stop offset="0%" stopColor="#F87171" />
          <stop offset="100%" stopColor="#DC2626" />
        </radialGradient>
        <linearGradient id="waveGradient1" x1="0%" y1="0%" x2="100%" y2="0%">
          <stop offset="0%" stopColor="#991B1B" />
          <stop offset="50%" stopColor="#EF4444" />
          <stop offset="100%" stopColor="#991B1B" />
        </linearGradient>
        <linearGradient id="ringGradient1" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stopColor="rgba(239, 68, 68, 0.6)" />
          <stop offset="100%" stopColor="rgba(239, 68, 68, 0.1)" />
        </linearGradient>
      </defs>

      {/* Núcleo central brillante en forma de nube pequeña flotante */}
      <g>
        <animateTransform
          attributeName="transform"
          type="translate"
          values="0 0; 0 -8; 0 0"
          dur="3s"
          repeatCount="indefinite"
          origin="center"
        />
        <path
          d="M160 150 Q160 135 175 135 Q178 120 190 120 Q205 120 210 135 Q225 135 225 150 Q225 165 210 165 L175 165 Q160 165 160 150"
          fill="url(#coreGradient1)"
          filter="url(#glow1)"
        />
      </g>

      {/* Ondas biométricas estilo montañas */}
      <path
        d="M0 228 L40 200 L80 240 L120 180 L160 220 L200 160 L240 210 L280 170 L320 230 L360 190 L380 200"
        fill="none"
        stroke="url(#waveGradient1)"
        strokeWidth="2"
        opacity="0.8"
        filter="url(#glow1)"
      >
        <animate attributeName="d"
          values="M0 228 L40 200 L80 240 L120 180 L160 220 L200 160 L240 210 L280 170 L320 230 L360 190 L380 200;
                  M0 228 L40 210 L80 230 L120 190 L160 210 L200 170 L240 200 L280 180 L320 220 L360 200 L380 210;
                  M0 228 L40 200 L80 240 L120 180 L160 220 L200 160 L240 210 L280 170 L320 230 L360 190 L380 200"
          dur="4s"
          repeatCount="indefinite"
        />
      </path>
      <path
        d="M0 240 L40 215 L80 255 L120 195 L160 235 L200 175 L240 225 L280 185 L320 245 L360 205 L380 215"
        fill="none"
        stroke="url(#waveGradient1)"
        strokeWidth="2"
        opacity="0.6"
        filter="url(#glow1)"
      >
        <animate attributeName="d"
          values="M0 240 L40 215 L80 255 L120 195 L160 235 L200 175 L240 225 L280 185 L320 245 L360 205 L380 215;
                  M0 240 L40 225 L80 245 L120 205 L160 225 L200 185 L240 215 L280 195 L320 235 L360 215 L380 225;
                  M0 240 L40 215 L80 255 L120 195 L160 235 L200 175 L240 225 L280 185 L320 245 L360 205 L380 215"
          dur="4.5s"
          repeatCount="indefinite"
        />
      </path>
      <path
        d="M0 250 L40 225 L80 265 L120 205 L160 245 L200 185 L240 235 L280 195 L320 255 L360 215 L380 225"
        fill="none"
        stroke="url(#waveGradient1)"
        strokeWidth="2"
        opacity="0.4"
        filter="url(#glow1)"
      >
        <animate attributeName="d"
          values="M0 250 L40 225 L80 265 L120 205 L160 245 L200 185 L240 235 L280 195 L320 255 L360 215 L380 225;
                  M0 250 L40 235 L80 255 L120 215 L160 235 L200 195 L240 225 L280 205 L320 245 L360 225 L380 235;
                  M0 250 L40 225 L80 265 L120 205 L160 245 L200 185 L240 235 L280 195 L320 255 L360 215 L380 225"
          dur="5s"
          repeatCount="indefinite"
        />
      </path>
      <path
        d="M0 260 L40 235 L80 275 L120 215 L160 255 L200 195 L240 245 L280 205 L320 265 L360 225 L380 235"
        fill="none"
        stroke="url(#waveGradient1)"
        strokeWidth="2"
        opacity="0.2"
        filter="url(#glow1)"
      >
        <animate attributeName="d"
          values="M0 260 L40 235 L80 275 L120 215 L160 255 L200 195 L240 245 L280 205 L320 265 L360 225 L380 235;
                  M0 260 L40 245 L80 265 L120 225 L160 245 L200 205 L240 235 L280 215 L320 255 L360 235 L380 245;
                  M0 260 L40 235 L80 275 L120 215 L160 255 L200 195 L240 245 L280 205 L320 265 L360 225 L380 235"
          dur="5.5s"
          repeatCount="indefinite"
        />
      </path>

      {/* Destellos rojos */}
      {[{x: 50, y: 100}, {x: 320, y: 80}, {x: 80, y: 280}, {x: 300, y: 260}, {x: 150, y: 320}, {x: 200, y: 50}, {x: 350, y: 180}, {x: 30, y: 200}, {x: 250, y: 340}, {x: 100, y: 150}].map((pos, i) => (
        <circle key={i} cx={pos.x} cy={pos.y} r="3" fill="#EF4444" filter="url(#glow1)">
          <animate attributeName="opacity" values="0;1;0" dur={`${1.2 + i * 0.2}s`} repeatCount="indefinite" />
          <animate attributeName="r" values="2;4;2" dur={`${1.2 + i * 0.2}s`} repeatCount="indefinite" />
        </circle>
      ))}
    </svg>
  )
}

/* Capítulo 2 — Sistema Endoscópico */
function Visual2() {
  return (
    <svg viewBox="0 0 380 380" className="w-72 h-72 md:w-96 md:h-96">
      <defs>
        <filter id="glow2">
          <feGaussianBlur stdDeviation="4" result="blur" />
          <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
        </filter>
        <radialGradient id="lightGradient" cx="50%" cy="50%" r="50%">
          <stop offset="0%" stopColor="rgba(96, 165, 250, 0.7)" />
          <stop offset="100%" stopColor="rgba(37, 99, 235, 0)" />
        </radialGradient>
      </defs>

      {/* Torre de endoscopio */}
      <g transform="translate(80, 100)">
        <rect x="-40" y="0" width="110" height="70" rx="6" fill="#0F172A" stroke="#3B82F6" strokeWidth="1.5" />
        <rect x="-30" y="15" width="45" height="20" fill="#1E293B" />
        <circle cx="40" cy="25" r="6" fill="#2563EB" />
        
        {/* Botones del sistema */}
        {[{x: -30, y: 45}, {x: -15, y: 45}, {x: 0, y: 45}, {x: 15, y: 45}, {x: 30, y: 45}].map((btn, i) => (
          <rect key={i} x={btn.x} y={btn.y} width="10" height="8" rx="2" fill="#1E40AF" stroke="#3B82F6" strokeWidth="0.5">
            <animate attributeName="fill" values="#1E40AF;#3B82F6;#1E40AF" dur={`${1 + i * 0.2}s`} repeatCount="indefinite" />
          </rect>
        ))}
      </g>

      {/* Mango de control */}
      <g transform="translate(280, 80)">
        <rect x="-10" y="-20" width="20" height="80" rx="8" fill="#0F172A" stroke="#60A5FA" strokeWidth="1" />
        <circle cx="0" cy="0" r="10" stroke="#2563EB" strokeWidth="2" fill="none" />
        <circle cx="0" cy="0" r="4" fill="#60A5FA" />
        <rect x="-6" y="-30" width="6" height="8" fill="#1E40AF" />
        <rect x="0" y="-30" width="6" height="8" fill="#1E3A8A" />
      </g>

      {/* Cable umbilical */}
      <path d="M120 120 C150 180 200 180 280 120" fill="none" stroke="#1E3A8A" strokeWidth="3.5" />
      <path d="M120 120 C150 180 200 180 280 120" fill="none" stroke="#3B82F6" strokeWidth="1.5" filter="url(#glow2)" />

      {/* Tubo de inserción flexible */}
      <path d="M280 120 C290 200 200 280 80 320" fill="none" stroke="#0F172A" strokeWidth="4" />
      <path d="M280 120 C290 200 200 280 80 320" fill="none" stroke="#60A5FA" strokeWidth="1.2" filter="url(#glow2)" />

      {/* Punta distal */}
      <g transform="translate(80, 320)">
        <circle cx="0" cy="0" r="7" fill="#DBEAFE" />
        <circle cx="0" cy="0" r="14" fill="url(#lightGradient)" filter="url(#glow2)" />
      </g>

      {/* Monitor de escritorio */}
      <g transform="translate(80, 340)">
        {/* Cuerpo del monitor */}
        <rect x="-50" y="-40" width="100" height="60" rx="6" fill="#0F172A" stroke="#3B82F6" strokeWidth="1.5" />
        
        {/* Pantalla */}
        <rect x="-45" y="-35" width="90" height="45" rx="3" fill="#1E293B" />
        <rect x="-40" y="-30" width="80" height="35" rx="2" fill="#0F172A" opacity="0.5">
          <animate attributeName="fill" values="#0F172A;#1E293B;#0F172A" dur="2s" repeatCount="indefinite" />
        </rect>
        
        {/* Base del monitor */}
        <rect x="-20" y="20" width="40" height="10" rx="3" fill="#0F172A" stroke="#3B82F6" strokeWidth="1" />
        <rect x="-10" y="30" width="20" height="5" rx="2" fill="#3B82F6" opacity="0.5" />
      </g>

      {/* Cometa animado por cable umbilical */}
      <g>
        <animateTransform
          attributeName="transform"
          type="translate"
          from="120 120"
          to="280 120"
          dur="1.75s"
          repeatCount="indefinite"
        />
        <rect x="-5" y="-5" width="10" height="10" rx="5" fill="#FFFFFF" stroke="#60A5FA" strokeWidth="1.5" filter="url(#glow2)">
          <animate attributeName="opacity" values="0;1;1;0" dur="1.75s" repeatCount="indefinite" />
        </rect>
      </g>

      {/* Cometa animado por tubo flexible */}
      <g>
        <animateTransform
          attributeName="transform"
          type="translate"
          from="280 120"
          to="80 320"
          dur="1.75s"
          begin="1.75s"
          repeatCount="indefinite"
        />
        <rect x="-5" y="-5" width="10" height="10" rx="5" fill="#FFFFFF" stroke="#60A5FA" strokeWidth="1.5" filter="url(#glow2)">
          <animate attributeName="opacity" values="0;1;1;0" dur="1.75s" repeatCount="indefinite" />
        </rect>
      </g>
    </svg>
  )
}

/* Capítulo 3 — Escaneo IA */
function Visual3() {
  return (
    <svg viewBox="0 0 380 380" className="w-72 h-72 md:w-96 md:h-96">
      <defs>
        <filter id="glow3">
          <feGaussianBlur stdDeviation="4" result="blur" />
          <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
        </filter>
        <linearGradient id="laserGradient" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stopColor="rgba(139, 92, 246, 0.25)" />
          <stop offset="100%" stopColor="rgba(139, 92, 246, 0)" />
        </linearGradient>
      </defs>

      {/* Recuadro de enfoque */}
      <g transform="translate(190, 160)">
        {/* Fondo sutil */}
        <rect x="-133" y="-63" width="266" height="126" fill="rgba(139, 92, 246, 0.03)" />
        {/* Borde del recuadro */}
        <rect x="-133" y="-63" width="266" height="126" fill="none" stroke="#8B5CF6" strokeWidth="1.5" />
      </g>

      {/* Esquinas tácticas HUD */}
      <g transform="translate(190, 160)" fill="#A78BFA">
        {/* Esquina superior izquierda */}
        <rect x="-135" y="-65" width="15" height="4" />
        <rect x="-135" y="-65" width="4" height="15" />
        {/* Esquina superior derecha */}
        <rect x="120" y="-65" width="15" height="4" />
        <rect x="131" y="-65" width="4" height="15" />
        {/* Esquina inferior izquierda */}
        <rect x="-135" y="61" width="15" height="4" />
        <rect x="-135" y="50" width="4" height="15" />
        {/* Esquina inferior derecha */}
        <rect x="120" y="61" width="15" height="4" />
        <rect x="131" y="50" width="4" height="15" />
      </g>

      {/* Línea láser de escaneo */}
      <g>
        <animateTransform
          attributeName="transform"
          type="translate"
          values="57 97; 57 223; 57 97"
          dur="3s"
          repeatCount="indefinite"
        />
        <rect x="0" y="0" width="266" height="3" fill="#FFFFFF" filter="url(#glow3)">
          <animate attributeName="opacity" values="1;0.8;1" dur="3s" repeatCount="indefinite" />
        </rect>
        <rect x="0" y="-10" width="266" height="10" fill="url(#laserGradient)" />
      </g>

      {/* Etiqueta de detección */}
      <g transform="translate(57, 65)">
        <rect x="0" y="0" width="250" height="24" rx="4" fill="rgba(139, 92, 246, 0.15)" stroke="#6511c5" strokeWidth="1">
          <animate attributeName="opacity" values="0.3;1;0.3" dur="1.6s" repeatCount="indefinite" />
        </rect>
        <circle cx="12" cy="12" r="3" fill="#EF4444">
          <animate attributeName="opacity" values="0.3;1;0.3" dur="1.6s" repeatCount="indefinite" />
        </circle>
        <text x="22" y="16" fontSize="10" fill="#A78BFA" fontWeight="bold" letterSpacing="0.5">
          <animate attributeName="opacity" values="0.3;1;0.3" dur="1.6s" repeatCount="indefinite" />
          ANÁLISIS DE IA: DETECTANDO IMAGEN...
        </text>
      </g>
    </svg>
  )
}

/* Capítulo 4 — Reporte Médico IA */
function Visual4() {
  return (
    <svg viewBox="0 0 380 380" className="w-72 h-72 md:w-96 md:h-96">
      <defs>
        <filter id="glow4">
          <feGaussianBlur stdDeviation="3" result="blur" />
          <feMerge><feMergeNode in="blur" /><feMergeNode in="SourceGraphic" /></feMerge>
        </filter>
      </defs>

      {/* Encabezado del reporte */}
      <g transform="translate(57, 50)">
        <text x="0" y="0" fontSize="10" fill="#10b981" fontWeight="bold" letterSpacing="1.5">INFORME GENERADO POR IA</text>
        <text x="0" y="16" fontSize="12" fill="#94A3B8">ID Paciente: #9482-AMB</text>
        <line x1="0" y1="28" x2="266" y2="28" stroke="rgba(16, 185, 129, 0.2)" strokeWidth="1" />
      </g>

      {/* Gráfica lineal de salud */}
      <g transform="translate(57, 120)">
        {/* Línea de la gráfica */}
        <path d="M0 0 L40 8 L80 -8 L120 16 L160 -4 L200 4 L266 0" fill="none" stroke="rgba(16, 185, 129, 0.4)" strokeWidth="1.5" />
        <path d="M0 0 L40 8 L80 -8 L120 16 L160 -4 L200 4 L266 0" fill="none" stroke="#10b981" strokeWidth="0.8" filter="url(#glow4)" />
        
        {/* Puntos de control */}
        <circle cx="80" cy="-8" r="3" fill="#059669" />
        <circle cx="120" cy="16" r="3" fill="#10b981" />
        
        {/* Cometa animado recorriendo la gráfica */}
        <g>
          <animateTransform
            attributeName="transform"
            type="translate"
            values="0 0; 40 8; 80 -8; 120 16; 160 -4; 200 4; 266 0; 0 0"
            dur="3s"
            repeatCount="indefinite"
          />
          <circle cx="0" cy="0" r="4" fill="#FFFFFF" stroke="#10b981" strokeWidth="1" filter="url(#glow4)" />
        </g>
      </g>

      {/* Líneas de texto simuladas */}
      <g transform="translate(57, 170)" fill="rgba(16, 185, 129, 0.15)">
        {/* Bloque de texto 1 */}
        <rect x="0" y="0" width="266" height="8" rx="2" />
        <rect x="0" y="14" width="226" height="8" rx="2" />
        
        {/* Bloque de texto 2 */}
        <rect x="0" y="42" width="266" height="8" rx="2" />
        <rect x="0" y="56" width="160" height="8" rx="2" />
      </g>

      {/* Rastros verticales de luz */}
      <g>
        <animateTransform
          attributeName="transform"
          type="translate"
          values="57 170; 323 170; 57 170"
          dur="3s"
          repeatCount="indefinite"
        />
        <rect x="0" y="0" width="2" height="22" fill="#059669" filter="url(#glow4)" />
      </g>
      <g>
        <animateTransform
          attributeName="transform"
          type="translate"
          values="57 212; 323 212; 57 212"
          dur="3s"
          repeatCount="indefinite"
        />
        <rect x="0" y="0" width="2" height="22" fill="#059669" filter="url(#glow4)" />
      </g>
    </svg>
  )
}