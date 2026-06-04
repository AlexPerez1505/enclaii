import { useState, useRef, useEffect } from 'react'
import { flushSync } from 'react-dom'

export default function AnimatedThemeToggler({ className = '' }) {
  const [isLight, setIsLight] = useState(false)
  const buttonRef = useRef(null)

  /* Inicializa desde localStorage */
  useEffect(() => {
    const stored = localStorage.getItem('simed-theme')
    const initial = stored === 'light'
    setIsLight(initial)
    if (initial) {
      document.documentElement.setAttribute('data-theme', 'light')
    } else {
      document.documentElement.removeAttribute('data-theme')
    }
  }, [])

  const changeTheme = async () => {
    if (!buttonRef.current) return

    const applyTheme = () => {
      flushSync(() => {
        const next = !isLight
        setIsLight(next)
        if (next) {
          document.documentElement.setAttribute('data-theme', 'light')
        } else {
          document.documentElement.removeAttribute('data-theme')
        }
        localStorage.setItem('simed-theme', next ? 'light' : 'dark')
      })
    }

    /* Fallback para navegadores sin View Transitions API (Firefox actual) */
    if (!document.startViewTransition) {
      applyTheme()
      return
    }

    await document.startViewTransition(applyTheme).ready

    /* Calcula el centro del botón */
    const { top, left, width, height } = buttonRef.current.getBoundingClientRect()
    const y = top + height / 2
    const x = left + width / 2

    /* Radio máximo = distancia desde el botón a la esquina más lejana */
    const right = window.innerWidth - left
    const bottom = window.innerHeight - top
    const maxRad = Math.hypot(Math.max(left, right), Math.max(top, bottom))

    /* Animación de clip-path circular */
    document.documentElement.animate(
      {
        clipPath: [
          `circle(0px at ${x}px ${y}px)`,
          `circle(${maxRad}px at ${x}px ${y}px)`,
        ],
      },
      {
        duration: 700,
        easing: 'ease-in-out',
        pseudoElement: '::view-transition-new(root)',
      }
    )
  }

  return (
    <button
      ref={buttonRef}
      onClick={changeTheme}
      aria-label={`Cambiar a tema ${isLight ? 'oscuro' : 'claro'}`}
      className={`relative p-2 rounded-lg border border-white/10 hover:border-[#2196f3]/50
        hover:bg-white/5 transition-colors text-slate-300 hover:text-[#2196f3] ${className}`}
    >
      {isLight ? (
        /* Luna — modo claro activo, clic para pasar a oscuro */
        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth={1.8}>
          <path
            strokeLinecap="round"
            strokeLinejoin="round"
            d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z"
          />
        </svg>
      ) : (
        /* Sol — modo oscuro activo, clic para pasar a claro */
        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth={1.8}>
          <path
            strokeLinecap="round"
            strokeLinejoin="round"
            d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
          />
        </svg>
      )}
    </button>
  )
}