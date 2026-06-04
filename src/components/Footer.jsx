export default function Footer() {
  return (
    <footer className="ft pt-20 pb-10 relative overflow-hidden">
      <style>{`
        .ft {
          --ft-bg:#050d1f; --ft-border: rgba(255,255,255,0.10);
          --ft-title:#ffffff; --ft-text:#94a3b8; --ft-muted:#64748b;
          --ft-hover:#2196f3; --ft-line: rgba(255,255,255,0.15); --ft-line2: rgba(255,255,255,0.05);
          --ft-glow: rgba(33,150,243,0.4);
          background: var(--ft-bg);
          border-top: 1px solid var(--ft-border);
          transition: background .6s ease, border-color .6s ease;
        }
        :root[data-theme="light"] .ft {
          --ft-bg:#eef6ff; --ft-border: rgba(15,23,42,0.10);
          --ft-title:#0b1220; --ft-text:#475569; --ft-muted:#64748b;
          --ft-hover:#0284c7; --ft-line: rgba(15,23,42,0.12); --ft-line2: rgba(15,23,42,0.06);
          --ft-glow: rgba(33,150,243,0.25);
        }

        .ft-title { color: var(--ft-title); }
        .ft-text { color: var(--ft-text); }
        .ft-muted { color: var(--ft-muted); }
        .ft-link { color: var(--ft-text); transition: color .2s ease; }
        .ft-link:hover { color: var(--ft-hover); }
        .ft-line { height:1px; background: linear-gradient(to right, transparent, var(--ft-line), transparent); }
        .ft-line2 { border-top: 1px solid var(--ft-line2); }

        /* Cambio de logo según tema */
        .logo-light { display: none; }
        :root[data-theme="light"] .logo-dark { display: none; }
        :root[data-theme="light"] .logo-light { display: block; }
      `}</style>

      {/* Glow de fondo */}
      <div
        className="absolute inset-0 opacity-20 pointer-events-none"
        style={{ background: 'radial-gradient(ellipse 60% 50% at 50% 0%, var(--ft-glow), transparent 70%)' }}
      />

      <div className="relative max-w-6xl mx-auto px-6">

        {/* ── Logo gigante centrado (dos versiones) ── */}
        <div className="flex flex-col items-center text-center mb-12">
          <a href="#inicio" className="inline-block group mb-6">
            <img
              src="/logo.png"
              alt="ENCLAII"
              className="logo-dark h-32 md:h-40 w-auto transition-all duration-500
                group-hover:drop-shadow-[0_0_40px_rgba(33,150,243,0.7)] group-hover:scale-105"
            />
            <img
              src="/logoblanco.png"
              alt="ENCLAII"
              className="logo-light h-32 md:h-40 w-auto transition-all duration-500
                group-hover:drop-shadow-[0_0_40px_rgba(33,150,243,0.5)] group-hover:scale-105"
            />
          </a>

          <p className="ft-text text-base md:text-lg max-w-xl leading-relaxed">
            Sistema de Endoscopia Digital con Inteligencia Artificial.
            Centralizando estudios en la nube para hospitales y clínicas.
          </p>

          {/* CTA destacado */}
          <a
            href="mailto:licencias@cizcalli.gob.mx?subject=Solicitud%20de%20demo%20ENCLAII"
            className="inline-flex items-center gap-2 mt-8 bg-[#2196f3] text-white px-6 py-3 rounded-lg
              text-sm font-medium hover:bg-[#1e88e5] transition-all duration-200
              shadow-lg shadow-[#2196f3]/30 hover:shadow-[#2196f3]/50 hover:-translate-y-0.5"
          >
            Solicitar demostración
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
          </a>
        </div>

        {/* Línea separadora */}
        <div className="ft-line mb-10" />

        {/* ── Grid de info ── */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-10 mb-12">

          {/* Columna 1: Navegación */}
          <div>
            <h3 className="ft-title font-semibold text-sm tracking-widest uppercase mb-4">Navegación</h3>
            <ul className="space-y-3 text-sm">
              <li><a href="#pilares"        className="ft-link">Pilares</a></li>
              <li><a href="#como-funciona"  className="ft-link">Cómo funciona</a></li>
              <li><a href="#modulos"        className="ft-link">Módulos</a></li>
              <li><a href="#contacto"       className="ft-link">Contacto</a></li>
            </ul>
          </div>

          {/* Columna 2: Legal */}
          <div>
            <h3 className="ft-title font-semibold text-sm tracking-widest uppercase mb-4">Legal</h3>
            <ul className="space-y-3 text-sm">
              <li><a href="#" className="ft-link">Privacidad</a></li>
              <li><a href="#" className="ft-link">Términos de uso</a></li>
              <li><a href="#" className="ft-link">Aviso de cookies</a></li>
              <li><a href="#" className="ft-link">Cumplimiento NOM</a></li>
            </ul>
          </div>

          {/* Columna 3: Contacto */}
          <div>
            <h3 className="ft-title font-semibold text-sm tracking-widest uppercase mb-4">Contacto</h3>
            <ul className="space-y-3 text-sm">
              <li>
                <a href="mailto:licencias@cizcalli.gob.mx" className="ft-link break-all">
                  soporte@enclaii.com.mx
                </a>
              </li>
              <li className="ft-muted">Soporte en español</li>
              <li className="ft-muted">Disponibilidad 24/7</li>
            </ul>
          </div>
        </div>

        {/* Línea inferior */}
        <div className="ft-line2 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-xs ft-muted">
          <span>© {new Date().getFullYear()} ENCLAII. Todos los derechos reservados.</span>
          <span>Hecho con ❤️ para la salud digital</span>
        </div>
      </div>
    </footer>
  )
}