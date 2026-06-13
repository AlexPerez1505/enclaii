import { useEffect, useRef, useState } from 'react'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import { AnimatePresence, motion } from 'framer-motion'
import { Play, Plus, Volume2, VolumeX, SkipBack, SkipForward } from 'lucide-react'

gsap.registerPlugin(ScrollTrigger)

// Configuración Flip Cards
const FLIP_CONFIG = {
  rotateY: 180,
  duration: 0.6,
  zIndex: 60,
  ease: 'back.out(1.7)'
}

const modulos = [
  // ... (Tus módulos se mantienen exactamente igual)
  { tag: 'Dashboard', title: 'Panel de Control General', desc: 'Visión general instantánea de la actividad clínica y administrativa.', features: ['Indicadores de actividad', 'Accesos rápidos', 'Sgerencias del asistente IA', 'Previzualizacion del dia de hoy',], videoUrl: '/dashboard-demo.mp4' },
  { tag: 'Agenda', title: 'Gestión de Citas y Calendario', desc: 'Organiza y visualiza todas las citas médicas y procedimientos.', features: ['Vistas día/semana/mes', 'Filtrado por médico/sala', 'Confirmación automática', 'Indicadores dinamicos en la agenda'], videoUrl: '/agenda-demo.mp4' },
  { tag: 'Pacientes', title: 'Registro y Expediente Clínico', desc: 'Centraliza la información demográfica y clínica de cada paciente.', features: ['Búsqueda inteligente', 'Información de contacto', 'Linea de tiempo de estudios', 'Modal '], videoUrl: '/pacientes-demo.mp4' },
  { tag: 'Informes', title: 'Visor y Gestión de Reportes', desc: 'Accede, revisa y firma digitalmente los informes generados.', features: ['Firma digital integrada', 'Descarga en PDF', 'Envío seguro por correo'], videoUrl: '/informes-demo.mp4' },
  { tag: 'IA Reportes', title: 'Asistente de IA para Reportes', desc: 'Utiliza IA para analizar imágenes y sugerir descripciones clínicas.', features: ['Detección de hallazgos', 'Generación de texto', 'Ahorro de tiempo'], videoUrl: '/ia-reportes-demo.mp4' },
  { tag: 'Mensajes', title: 'Comunicación Médica Interna', desc: 'Plataforma de mensajería segura para el personal médico.', features: ['Chats individuales', 'Envío de Informes', 'Notificaciones en tiempo real'], videoUrl: '/mensajes-demo.mp4' },
  { tag: 'Nuevo estudio', title: 'Inicio de Procedimiento', desc: 'Workflow optimizado para dar de alta y comenzar estudios.', features: ['Formulario rápido', 'Vinculación de equipos', 'Ajustes de la captura del video',], videoUrl: '/nuevo-estudio-demo.mp4' },
  { tag: 'Galeria', title: 'Banco de Imágenes Médicas', desc: 'Almacenamiento y organización de imágenes y vídeos.', features: ['Filtrado por estudio', 'Edicion de Fotografias y Videos', 'Exportación a diferentes formatos'], videoUrl: '/galeria-demo.mp4' },
  { tag: 'Configuracion', title: 'Ajustes del Sistema', desc: 'Personaliza tu perfil y preferencias de la plataforma.', features: ['Perfil de usuario', 'Integracion de mas personal', 'Claves de seguridad'], videoUrl: '/configuracion-demo.mp4' },
  {
    tag: 'Asistente IA',
    title: 'Soporte de tareas',
    desc: 'Procesamiento de peticiones genradas por.',
    features: ['Busqueda de Pacientes en segundos', 'Dudas resueltas en segundos', 'Comando de voz integrado '],
    videoUrl: '/asistente-ia-demo.mp4'
  },
];

function VideoPopOver({ videoUrl, setShowVideoPopOver }) {
  const videoRef = useRef(null)
  const [isPlaying, setIsPlaying] = useState(false)
  const [isMuted, setIsMuted] = useState(true)

  const togglePlay = () => {
    if (videoRef.current) {
      if (isPlaying) {
        videoRef.current.pause()
      } else {
        videoRef.current.play()
      }
      setIsPlaying(!isPlaying)
    }
  }

  const toggleMute = () => {
    if (videoRef.current) {
      videoRef.current.muted = !videoRef.current.muted
      setIsMuted(!isMuted)
    }
  }

  const skipBackward = () => {
    if (videoRef.current) {
      videoRef.current.currentTime = Math.max(0, videoRef.current.currentTime - 10)
    }
  }

  const skipForward = () => {
    if (videoRef.current) {
      videoRef.current.currentTime = Math.min(videoRef.current.duration, videoRef.current.currentTime + 10)
    }
  }

  return (
    <div className="fixed left-0 top-0 z-[101] flex h-screen w-screen items-center justify-center">

      {/* CAMBIO 1: Fondo con Imagen */}
      {/* Se eliminó el fondo negro y se agregó la propiedad backgroundImage. 
          NOTA: Asegúrate de poner la ruta correcta de tu imagen en `url('/ruta-de-tu-imagen.png')` */}
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        transition={{ duration: 0.2 }}
        className="absolute left-0 top-0 h-full w-full bg-cover bg-center bg-no-repeat cursor-pointer"
        style={{ backgroundImage: "url('/Fonfo WEb.png')" }} // Reemplaza con el path real de tu imagen
        onClick={() => setShowVideoPopOver(false)}
      >
        {/* CAMBIO 2: Capa oscura superpuesta (Overlay) */}
        {/* Esto oscurece ligeramente la imagen de fondo para que el video y los controles destaquen más y no se pierdan con el brillo de la imagen */}
        <div className="absolute inset-0 bg-black/50 backdrop-blur-[2px]" />
      </motion.div>

      {/* CAMBIO 3: Contenedor del Video Ajustado */}
      {/* Se agregó `w-full px-4 md:px-12` para asegurar que en pantallas pequeñas el video no toque los bordes y mantenga dimensiones responsivas */}
      <motion.div
        initial={{ clipPath: "inset(43.5% 43.5% 33.5% 43.5%)", opacity: 0 }}
        animate={{ clipPath: "inset(0 0 0 0)", opacity: 1 }}
        exit={{
          clipPath: "inset(43.5% 43.5% 33.5% 43.5%)",
          opacity: 0,
          transition: {
            duration: 1,
            type: "spring",
            stiffness: 100,
            damping: 20,
            opacity: { duration: 0.2, delay: 0.8 },
          },
        }}
        transition={{
          duration: 1,
          type: "spring",
          stiffness: 100,
          damping: 20,
        }}
        className="relative aspect-video max-w-6xl w-full px-4 md:px-12" // Reduje a max-w-6xl para que encaje mejor visualmente con el resplandor del fondo
      >
        {/* Contenedor interno del video */}
        {/* Se añadió un ligero efecto de brillo exterior (shadow-[#2196f3]/40) para que haga "match" con el neón de la imagen */}
        <div className="relative w-full h-full bg-[#0a192f] rounded-2xl overflow-hidden border border-[#2196f3]/50 shadow-[0_0_50px_rgba(33,150,243,0.3)]">
          <video
            ref={videoRef}
            src={videoUrl}
            className="w-full h-full object-contain"
            onClick={togglePlay}
            autoPlay
            muted
            playsInline
          />

          <button
            onClick={() => setShowVideoPopOver(false)}
            className="absolute right-4 top-4 z-10 cursor-pointer rounded-full bg-black/50 p-2 transition-colors hover:bg-[#2196f3]"
          >
            <Plus className="h-5 w-5 rotate-45 text-white" />
          </button>

          <div className="absolute bottom-0 left-1/2 flex w-full -translate-x-1/2 items-center justify-center px-5 md:px-10 pb-6 pt-16 bg-gradient-to-t from-black/80 to-transparent">
            <div className="flex items-center gap-4 bg-black/50 backdrop-blur-md rounded-full px-6 py-3 border border-white/10">
              <button
                onClick={skipBackward}
                className="text-white hover:text-[#2196f3] transition-colors"
              >
                <SkipBack className="h-5 w-5" />
              </button>
              <button
                onClick={togglePlay}
                className="text-white hover:text-[#2196f3] transition-colors"
              >
                {isPlaying ? (
                  <svg className="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z" />
                  </svg>
                ) : (
                  <Play className="h-6 w-6 fill-white" />
                )}
              </button>
              <button
                onClick={skipForward}
                className="text-white hover:text-[#2196f3] transition-colors"
              >
                <SkipForward className="h-5 w-5" />
              </button>
              <div className="w-px h-6 bg-white/30" />
              <button
                onClick={toggleMute}
                className="text-white hover:text-[#2196f3] transition-colors"
              >
                {isMuted ? (
                  <VolumeX className="h-5 w-5" />
                ) : (
                  <Volume2 className="h-5 w-5" />
                )}
              </button>
            </div>
          </div>
        </div>
      </motion.div>
    </div>
  )
}

export default function Modulos() {
  const sectionRef = useRef(null)
  const [hoveredCard, setHoveredCard] = useState(null)
  const [showVideoPopOver, setShowVideoPopOver] = useState(false)
  const [selectedVideoUrl, setSelectedVideoUrl] = useState(null)

  const handleCardEnter = (index) => {
    setHoveredCard(index)
    const cardInner = document.querySelectorAll('.mod-card > div')[index]
    gsap.to(cardInner, {
      rotateY: FLIP_CONFIG.rotateY,
      duration: FLIP_CONFIG.duration,
      ease: FLIP_CONFIG.ease
    })
  }

  const handleCardLeave = (index) => {
    setHoveredCard(null)
    const cardInner = document.querySelectorAll('.mod-card > div')[index]
    gsap.to(cardInner, {
      rotateY: 0,
      duration: FLIP_CONFIG.duration,
      ease: FLIP_CONFIG.ease
    })
  }

  useEffect(() => {
    const ctx = gsap.context(() => {
      gsap.fromTo('.mod-heading', { opacity: 0, y: 40 }, {
        opacity: 1, y: 0, duration: 0.9, ease: 'power3.out',
        scrollTrigger: { trigger: '.mod-heading', start: 'top 85%' }
      });

      const cards = document.querySelectorAll('.mod-card');
      cards.forEach((card, i) => {
        const isEven = i % 2 === 0;
        gsap.fromTo(card, { opacity: 0, x: isEven ? -100 : 100, y: 50 }, {
          opacity: 1, x: 0, y: 0, duration: 1, ease: 'power3.out',
          clearProps: "none",
          scrollTrigger: { trigger: card, start: 'top 85%', toggleActions: 'play reverse play reverse' }
        });
      });
    }, sectionRef);
    return () => ctx.revert();
  }, []);

  return (
    <section ref={sectionRef} id="modulos" className="py-28 bg-[#050d1f] overflow-hidden relative">
      <div className="max-w-6xl mx-auto px-6 relative z-0">
        <div className="text-center mb-16">
          <span className="mod-heading inline-block text-xs font-medium tracking-[0.3em] text-[#2196f3] uppercase mb-4">Módulos SIMED</span>
          <h2 className="mod-heading text-3xl md:text-5xl font-light text-white mb-5">Todo lo que necesitas</h2>
        </div>
        <div className="mod-grid grid md:grid-cols-2 gap-6 [perspective:1000px]">
          {modulos.map((m, i) => (
            <div
              key={m.title}
              className="mod-card relative w-full h-full"
              style={{ minHeight: '400px' }}
              onMouseEnter={() => handleCardEnter(i)}
              onMouseLeave={() => handleCardLeave(i)}
            >
              <div className={`relative w-full h-full [transform-style:preserve-3d] ${i % 2 === 0 ? 'bg-gradient-to-br from-[#2196f3]/5 to-transparent' : 'bg-gradient-to-br from-white/[0.03] to-transparent'}`} style={{ minHeight: '260px' }}>
                <div className="absolute inset-0 p-8 rounded-2xl border border-white/10 [backface-visibility:hidden]">
                  <span className="inline-block text-xs font-medium tracking-widest text-[#2196f3] uppercase mb-3 bg-[#2196f3]/10 px-3 py-1 rounded-full">{m.tag}</span>
                  <h3 className="text-xl font-semibold text-white mb-3">{m.title}</h3>
                  <p className="text-slate-400 text-sm leading-relaxed mb-5">{m.desc}</p>
                  <ul className="space-y-2">
                    {m.features.map(f => (
                      <li key={f} className="flex items-center gap-2 text-sm text-slate-400">
                        <svg className="w-4 h-4 text-[#2196f3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" /></svg>
                        {f}
                      </li>
                    ))}
                  </ul>
                </div>
                <div className="absolute inset-0 p-8 rounded-2xl border border-[#2196f3]/50 bg-[#2196f3]/10 [backface-visibility:hidden] [transform:rotateY(180deg)] flex items-center justify-center">
                  <div className="text-center w-full">
                    <span className="inline-block text-xs font-medium tracking-widest text-[#2196f3] uppercase mb-3 bg-[#2196f3]/20 px-3 py-1 rounded-full">{m.tag}</span>
                    <h3 className="text-xl font-semibold text-white mb-3">{m.title}</h3>

                    {/* Mini previsualización del video */}
                    <div
                      onClick={() => {
                        setSelectedVideoUrl(m.videoUrl)
                        setShowVideoPopOver(true)
                      }}
                      className="relative mt-4 mx-auto w-full aspect-video bg-black/50 rounded-xl overflow-hidden cursor-pointer group hover:scale-105 transition-transform duration-300"
                    >
                      <video
                        src={m.videoUrl}
                        className="w-full h-full object-cover opacity-60 group-hover:opacity-80 transition-opacity"
                        muted
                        autoPlay
                        loop
                        playsInline
                      />
                      <div className="absolute inset-0 flex items-center justify-center">
                        <div className="w-8 h-8 bg-[#2196f3] rounded-full flex items-center justify-center shadow-lg shadow-[#2196f3]/50 group-hover:scale-110 transition-transform">
                          <Play className="w-6 h-6 fill-white ml-1" />
                        </div>
                      </div>
                      <div className="absolute bottom-2 left-2 right-2">
                        <span className="text-xs text-white/70 bg-black/50 px-2 py-1 rounded">Click para ver demo</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Video PopOver */}
      <AnimatePresence>
        {showVideoPopOver && selectedVideoUrl && (
          <VideoPopOver
            videoUrl={selectedVideoUrl}
            setShowVideoPopOver={setShowVideoPopOver}
          />
        )}
      </AnimatePresence>
    </section>
  )
}