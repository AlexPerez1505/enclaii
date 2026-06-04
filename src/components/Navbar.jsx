import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import AnimatedThemeToggler from './AnimatedThemeToggler'

const links = [
  { href: '/#pilares',       label: 'Pilares' },
  { href: '/#como-funciona', label: 'Cómo funciona' },
  { href: '/#modulos',       label: 'Módulos' },
]

export default function Navbar() {
  const [open, setOpen] = useState(false)
  const [scrolled, setScrolled] = useState(false)

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 60)
    window.addEventListener('scroll', onScroll, { passive: true })
    return () => window.removeEventListener('scroll', onScroll)
  }, [])

  return (
    <header
      data-scrolled={scrolled ? 'true' : 'false'}
      className="nav fixed top-0 left-0 right-0 z-[100] transition-all duration-500"
    >
      <style>{`
        .nav {
          --nav-text:#94a3b8; --nav-hover:#2196f3;
          --nav-bar: rgba(5,13,31,0.90); --nav-border: rgba(255,255,255,0.10);
          --nav-shadow: 0 10px 30px -10px rgba(0,0,0,0.5);
          --mobile-bg:#050d1f; --mobile-text:#cbd5e1;
        }
        :root[data-theme="light"] .nav {
          --nav-text:#475569; --nav-hover:#0284c7;
          --nav-bar: rgba(255,255,255,0.85); --nav-border: rgba(15,23,42,0.10);
          --nav-shadow: 0 10px 30px -12px rgba(15,23,42,0.18);
          --mobile-bg:#ffffff; --mobile-text:#334155;
        }
        /* Barra de fondo según scroll */
        .nav[data-scrolled="true"] {
          background: var(--nav-bar);
          backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
          border-bottom: 1px solid var(--nav-border);
          box-shadow: var(--nav-shadow);
        }
        .nav[data-scrolled="false"] { background: transparent; border-bottom: 1px solid transparent; }

        .nav-link { color: var(--nav-text); transition: color .2s ease; }
        .nav-link:hover { color: var(--nav-hover); }
        .nav-link::after { content:''; position:absolute; bottom:0; left:0; width:0; height:1px;
          background: var(--nav-hover); transition: width .3s ease; }
        .nav-link:hover::after { width:100%; }

        .nav-burger { color: var(--nav-text); }
        .nav-burger:hover { color: var(--nav-hover); }

        .nav-mobile { background: var(--mobile-bg); border-bottom:1px solid var(--nav-border); }
        .nav-mobile a { color: var(--mobile-text); transition: color .2s ease; }
        .nav-mobile a:hover { color: var(--nav-hover); }

        /* Cambio de logo según tema */
        .logo-light { display: none; }                       /* oculto en oscuro */
        :root[data-theme="light"] .logo-dark { display: none; }   /* oculta el de oscuro en claro */
        :root[data-theme="light"] .logo-light { display: block; } /* muestra el blanco en claro */
      `}</style>

      <nav
        className={`max-w-6xl mx-auto px-6 flex items-center justify-between transition-all duration-500 ${
          scrolled ? 'h-20' : 'h-28'
        }`}
      >
        {/* Logo (dos versiones: una por tema) */}
        <Link to="/" className="flex items-center group -my-4">
          {/* Logo para fondo oscuro */}
          <img
            src="/logo.png"
            alt="ENCLAII"
            className={`logo-dark w-auto transition-all duration-500 object-contain
              group-hover:drop-shadow-[0_0_24px_rgba(33,150,243,0.9)]
              group-hover:scale-105
              ${scrolled ? 'h-16' : 'h-24'}`}
          />
          {/* Logo para fondo claro/blanco */}
          <img
            src="/logoblanco.png"
            alt="ENCLAII"
            className={`logo-light w-auto transition-all duration-500 object-contain
              group-hover:drop-shadow-[0_0_24px_rgba(33,150,243,0.5)]
              group-hover:scale-105
              ${scrolled ? 'h-16' : 'h-24'}`}
          />
        </Link>

        {/* Links desktop */}
        <ul className="hidden md:flex gap-8 text-sm font-medium items-center">
          {links.map(l => (
            <li key={l.href}>
              <a href={l.href} className="nav-link relative">{l.label}</a>
            </li>
          ))}
          <li>
            <Link to="/por-que" className="nav-link relative">¿Por qué SIMED?</Link>
          </li>
          <li>
            <Link to="/contacto" className="nav-link relative">Contacto</Link>
          </li>
        </ul>

        {/* Toggle + CTA (desktop) */}
        <div className="hidden md:flex items-center gap-3">
          <AnimatedThemeToggler />
          <Link
            to="/contacto"
            className="inline-flex items-center gap-2 bg-[#2196f3] text-white px-5 py-2 rounded-lg
              text-sm font-medium hover:bg-[#1e88e5] transition-all duration-200
              shadow-lg shadow-[#2196f3]/25 hover:shadow-[#2196f3]/40 hover:-translate-y-px"
          >
            Solicitar demo
          </Link>
        </div>

        {/* Toggle + Burger (mobile) */}
        <div className="md:hidden flex items-center gap-2">
          <AnimatedThemeToggler />
          <button
            className="nav-burger p-2"
            onClick={() => setOpen(!open)}
            aria-label="Toggle menu"
          >
            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                d={open ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'}
              />
            </svg>
          </button>
        </div>
      </nav>

      {/* Mobile menu */}
      <div
        className={`nav-mobile md:hidden overflow-hidden transition-all duration-300
          ${open ? 'max-h-80 opacity-100' : 'max-h-0 opacity-0'}`}
      >
        <div className="px-6 py-4 space-y-3">
          {links.map(l => (
            <a key={l.href} href={l.href} onClick={() => setOpen(false)} className="block">{l.label}</a>
          ))}
          <Link to="/por-que" onClick={() => setOpen(false)} className="block">¿Por qué SIMED?</Link>
          <Link to="/contacto" onClick={() => setOpen(false)} className="block">Contacto</Link>
          <Link
            to="/contacto"
            onClick={() => setOpen(false)}
            className="block bg-[#2196f3] text-white text-center px-4 py-2 rounded-lg font-medium"
          >
            Solicitar demo
          </Link>
        </div>
      </div>
    </header>
  )
}