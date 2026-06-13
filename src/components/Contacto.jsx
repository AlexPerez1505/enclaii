import { useEffect, useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import lottie from 'lottie-web'
import DoctorIllustrationData from '../assets/DoctorIllustration.json'
import ThinkingDoctorData from '../assets/ThinkingDoctor.json'

const MAILTO = 'mailto:licencias@cizcalli.gob.mx?subject=Solicitud%20de%20demo%20SIMED'

const Icon = {
  mail: (p) => (<svg {...p} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.6} d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>),
  phone: (p) => (<svg {...p} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.6} d="M3 5a2 2 0 012-2h2.2a1 1 0 01.95.68l1 3a1 1 0 01-.25 1L7.6 9.4a13 13 0 007 7l1.7-1.3a1 1 0 011-.25l3 1a1 1 0 01.68.95V19a2 2 0 01-2 2A16 16 0 013 5z" /></svg>),
  pin: (p) => (<svg {...p} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.6} d="M12 21s-7-5.2-7-11a7 7 0 1114 0c0 5.8-7 11-7 11z" /><circle cx="12" cy="10" r="2.5" strokeWidth={1.6} /></svg>),
  clock: (p) => (<svg {...p} fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" strokeWidth={1.6} /><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.6} d="M12 7v5l3 2" /></svg>),
}

const TELEFONO = '55 1234 5678'
const TELEFONO_LINK = 'tel:+525512345678'
const ORGANIGRAMA_IMG = ''

const FULL_TEXT = 'Hablemos de tu\nservicio de endoscopia';

export default function Contacto() {
  const [showOrg, setShowOrg] = useState(false);
  const [mounted, setMounted] = useState(false);
  const [typed, setTyped] = useState('');
  const lottieRef = useRef(null);

  useEffect(() => {
    setMounted(true);
  }, []);

  useEffect(() => {
    if (!mounted) return;
    let i = 0;
    setTyped('');
    const id = setInterval(() => {
      i++;
      setTyped(FULL_TEXT.slice(0, i));
      if (i >= FULL_TEXT.length) clearInterval(id);
    }, 55);
    return () => clearInterval(id);
  }, [mounted]);

  useEffect(() => {
    if (!lottieRef.current) return;
    const anim = lottie.loadAnimation({
      container: lottieRef.current,
      renderer: 'svg',
      loop: true,
      autoplay: true,
      animationData: DoctorIllustrationData,
    });
    return () => anim.destroy();
  }, []);

  return (
    <div style={styles.root}>
      {/* Grid Background */}
      <div className="grid-background"></div>
      
      {/* Floating Nodes */}
      <div className="floating-nodes">
        <div className="node node-1"></div>
        <div className="node node-2"></div>
        <div className="node node-3"></div>
        <div className="node node-4"></div>
        <div className="node node-5"></div>
        <div className="node node-6"></div>
        <div className="node node-7"></div>
        <div className="node node-8"></div>
        <div className="node node-9"></div>
        <div className="node node-10"></div>
      </div>
      
      <div style={{...styles.container, opacity: mounted ? 1 : 0, transition: 'opacity 0.6s ease-out'}}>

        {/* LEFT */}
        <div style={{...styles.leftCol, opacity: mounted ? 1 : 0, transform: mounted ? 'translateY(0)' : 'translateY(20px)', transition: 'opacity 0.6s ease-out 0.1s, transform 0.6s ease-out 0.1s'}}>
          <div className="badge-hover" style={styles.availableBadge}>
            <span style={styles.dot} />
            Disponibles ahora
          </div>
          <div className="robot-hover" style={styles.robotContainer}>
            <div 
              ref={lottieRef}
              style={{ width: 350, height: 350, filter: "drop-shadow(0 0 35px rgba(56,189,248,0.6))" }}
            />
          </div>
          <p className="response-note-hover" style={styles.responseNote}>Respuesta en menos de 24 h</p>
        </div>

        {/* RIGHT */}
        <div style={styles.rightCol}>
          <div style={{...styles.headlineBlock, opacity: mounted ? 1 : 0, transform: mounted ? 'translateY(0)' : 'translateY(20px)', transition: 'opacity 0.6s ease-out 0.2s, transform 0.6s ease-out 0.2s'}}>
            <span className="label-hover" style={styles.label}>Contacto</span>
            <h1 style={styles.headline}>
              {typed.split('\n').map((line, li) => (
                <span key={li}>
                  {li > 0 && <br />}
                  <span style={styles.gradientText}>{line}</span>
                </span>
              ))}
              <span style={styles.cursor}>|</span>
            </h1>
            <p className="subtext-hover" style={styles.subtext}>
              Escríbenos, llámanos o agenda una demostración de SIMED. Respondemos en menos de 24 horas hábiles.
            </p>
            <button onClick={() => setShowOrg(true)} className="btn-secondary-hover" style={styles.btnSecondary}>
              Sobre nosotros
            </button>
          </div>

          <div style={{...styles.actionGrid, opacity: mounted ? 1 : 0, transform: mounted ? 'translateY(0)' : 'translateY(20px)', transition: 'opacity 0.6s ease-out 0.3s, transform 0.6s ease-out 0.3s'}}>
            <ActionCard
              icon={<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#475569" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>}
              title="Chat"
              desc="Ayuda rápida y personalizada."
              link="mailto:licencias@clizalli.gob.mx"
              linkLabel="licencias@clizalli.gob.mx"
            />
            <ActionCard
              icon={<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#475569" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z" /></svg>}
              title="Teléfono"
              desc="Contacta con un especialista."
              extras={
                <>
                  <div className="phone-row-hover" style={styles.phoneRow}><span style={styles.phoneLabel}>Paciente</span><a href="tel:5512345678" className="action-link-hover" style={styles.actionLink}>55 1234 5678</a></div>
                  <div className="phone-row-hover" style={styles.phoneRow}><span style={styles.phoneLabel}>Horario</span><span style={styles.phoneVal}>Lun–Vie 9–18 h</span></div>
                </>
              }
            />
            <ActionCard
              icon={<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#475569" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10" /><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" /><line x1="12" y1="17" x2="12.01" y2="17" /></svg>}
              title="Ayuda"
              desc="Guías y respuestas paso a paso."
              link="mailto:licencias@clizalli.gob.mx"
              linkLabel="Contactar"
            />
          </div>

          <div style={{opacity: mounted ? 1 : 0, transform: mounted ? 'translateY(0)' : 'translateY(20px)', transition: 'opacity 0.6s ease-out 0.4s, transform 0.6s ease-out 0.4s'}}>
            <MapCard />
          </div>
        </div>
      </div>

      {/* Modal */}
      {showOrg && (
        <div style={{...styles.modalOverlay, opacity: 1, animation: 'fadeIn 0.3s ease-out'}} onClick={() => setShowOrg(false)}>
          <div className="modal-box-hover" style={styles.modalBox} onClick={e => e.stopPropagation()}>
            <div style={styles.modalHeader}>
              <div>
                <div style={styles.modalTitle}>¿Qué es Enclaii?</div>
                <div style={styles.modalSub}>Plataforma médica de siguiente generación</div>
              </div>
              <button onClick={() => setShowOrg(false)} className="modal-close-hover" style={styles.modalClose}>✕</button>
            </div>
            <div style={styles.modalBody}>
              <div style={styles.modalLottie}>
                <ThinkingDoctorLottie />
              </div>
              <div style={styles.modalText}>
                <p style={styles.modalDesc}>
                  Enclaii es una plataforma web especializada en la gestión y optimización de tratamientos médicos. Diseñada bajo rigurosos estándares de desarrollo e interfaz de usuario, la plataforma resuelve la desorganización de los expedientes clínicos tradicionales, permitiendo a los especialistas automatizar el seguimiento de pacientes, centralizar historiales clínicos y garantizar decisiones médicas más precisas y oportunas.
                </p>
                <div style={styles.modalFeatures}>
                  <div style={styles.modalFeatureItem}><span style={styles.modalFeatureDot}/>Automatización del seguimiento de pacientes</div>
                  <div style={styles.modalFeatureItem}><span style={styles.modalFeatureDot}/>Centralización de historiales clínicos</div>
                  <div style={styles.modalFeatureItem}><span style={styles.modalFeatureDot}/>Decisiones médicas precisas y oportunas</div>
                  <div style={styles.modalFeatureItem}><span style={styles.modalFeatureDot}/>Estándares rigurosos de UI/UX médico</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}

      <style dangerouslySetInnerHTML={{ __html: `
        @keyframes dotBlink { 0%,100%{opacity:1} 50%{opacity:0.3} }
        @keyframes mapPulse { 0%,100%{transform:scale(1);opacity:.5} 50%{transform:scale(1.5);opacity:.15} }
        @keyframes fadeIn { from{opacity:0} to{opacity:1} }
        @keyframes slideUp { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
        @keyframes glow { 0%,100%{box-shadow:0 0 20px rgba(56,189,248,0.3)} 50%{box-shadow:0 0 40px rgba(56,189,248,0.6)} }
        @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
        @keyframes textGlow { 0%,100%{text-shadow:0 0 20px rgba(56,189,248,0.5),0 0 40px rgba(56,189,248,0.3)} 50%{text-shadow:0 0 30px rgba(56,189,248,0.7),0 0 60px rgba(56,189,248,0.5)} }
        @keyframes iconPulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.1)} }
        @keyframes borderGlow { 0%,100%{border-color:#1e293b} 50%{border-color:#38bdf8} }
        @keyframes iconRotate { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }
        @keyframes textShimmer { 0%{background-position:-200% center} 100%{background-position:200% center} }
        @keyframes circleFloat { 0%{transform:translate(0,0) scale(1); opacity:0.6} 25%{transform:translate(30px,-40px) scale(1.15); opacity:0.8} 50%{transform:translate(0,-60px) scale(1.3); opacity:0.9} 75%{transform:translate(-30px,-40px) scale(1.15); opacity:0.8} 100%{transform:translate(0,0) scale(1); opacity:0.6} }
        @keyframes circleExpand { 0%{transform:scale(0.6); opacity:0.4} 50%{transform:scale(1.5); opacity:0.8} 100%{transform:scale(0.6); opacity:0.4} }
        @keyframes circleRotate { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }
        @keyframes circleBounce { 0%{transform:translate(0,0) scale(1)} 20%{transform:translate(40px,-30px) scale(1.1)} 40%{transform:translate(-20px,-50px) scale(1.2)} 60%{transform:translate(-40px,-20px) scale(1.15)} 80%{transform:translate(20px,-10px) scale(1.05)} 100%{transform:translate(0,0) scale(1)} }
        @keyframes circleDrift { 0%{transform:translate(0,0)} 33%{transform:translate(-50px,30px)} 66%{transform:translate(40px,-40px)} 100%{transform:translate(0,0)} }
        @keyframes waveArm { 0%,100%{transform:rotate(0deg)} 25%{transform:rotate(-15deg)} 75%{transform:rotate(15deg)} }
        @keyframes rotateCircle { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
        .badge-hover:hover { transform: scale(1.05); }
        .robot-hover:hover { transform: scale(1.05); }
        .btn-secondary-hover:hover { border-color: #1d4ed8; color: #38BDF8; box-shadow: 0 0 30px rgba(29,78,216,0.4); }
        .action-card-hover:hover { border-color: #2563eb; transform: translateY(-4px); box-shadow: 0 8px 32px rgba(37,99,235,0.4); }
        .action-icon-hover:hover { transform: scale(1.15) rotate(5deg); transition: all 0.3s ease; }
        .action-link-hover:hover { color: #7dd3fc; text-decoration: underline; }
        .location-card-hover:hover { border-color: #2563eb; box-shadow: 0 8px 32px rgba(37,99,235,0.4); }
        .location-icon-box-hover:hover { border-color: #1d4ed8; background-color: rgba(29,78,216,0.15); animation: iconPulse 0.6s ease-in-out; }
        .ext-link-hover:hover { border-color: #1d4ed8; color: #7dd3fc; }
        .modal-box-hover:hover { border-color: #1d4ed8; box-shadow: 0 0 50px rgba(29,78,216,0.35); }
        .modal-close-hover:hover { border-color: #1d4ed8; color: #38BDF8; background-color: rgba(29,78,216,0.15); }
        .label-hover:hover { color: #38BDF8; letter-spacing: 0.2em; transition: all 0.3s ease; }
        .subtext-hover:hover { color: #38BDF8; transition: color 0.3s ease; }
        .response-note-hover:hover { color: #38BDF8; transform: scale(1.05); transition: all 0.3s ease; }
        .phone-row-hover:hover { transform: translateX(4px); transition: transform 0.3s ease; }
        .animated-bg-circles { position: fixed; inset: 0; pointer-events: none; overflow: hidden; z-index: 0; }
        .circle { position: absolute; border-radius: 50%; background: radial-gradient(circle, rgba(59,130,246,0.35) 0%, transparent 70%); }
        .circle-1 { width: 450px; height: 450px; top: -120px; left: -120px; animation: circleBounce 8s ease-in-out infinite, circleRotate 12s linear infinite; }
        .circle-2 { width: 350px; height: 350px; top: 50%; right: -70px; animation: circleDrift 7s ease-in-out infinite 1s, circleRotate 14s linear infinite reverse; }
        .circle-3 { width: 300px; height: 300px; bottom: -70px; left: 25%; animation: circleBounce 9s ease-in-out infinite 0.5s; }
        .circle-4 { width: 400px; height: 400px; top: 25%; left: -180px; animation: circleDrift 8.5s ease-in-out infinite 2s, circleRotate 16s linear infinite; }
        .circle-5 { width: 250px; height: 250px; bottom: 15%; right: 5%; animation: circleBounce 7.5s ease-in-out infinite 3s; }
        .circle-6 { width: 320px; height: 320px; top: 10%; right: 15%; animation: circleDrift 8s ease-in-out infinite 1.2s, circleRotate 18s linear infinite reverse; }
        .circle-7 { width: 280px; height: 280px; bottom: 10%; left: 10%; animation: circleBounce 9.5s ease-in-out infinite 2.2s; }
        .circle-8 { width: 380px; height: 380px; top: 40%; left: 5%; animation: circleDrift 7.8s ease-in-out infinite 2.8s, circleRotate 20s linear infinite; }
        .grid-background { position: fixed; inset: 0; pointer-events: none; z-index: 0; background-image: linear-gradient(rgba(30,41,59,0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(30,41,59,0.3) 1px, transparent 1px); background-size: 50px 50px; }
        .floating-nodes { position: fixed; inset: 0; pointer-events: none; z-index: 0; }
        .node { position: absolute; width: 4px; height: 4px; background: #38bdf8; border-radius: 50%; box-shadow: 0 0 10px rgba(56,189,248,0.5); animation: nodeFloat 4s ease-in-out infinite; }
        .node-1 { top: 10%; left: 15%; animation-delay: 0s; }
        .node-2 { top: 20%; right: 20%; animation-delay: 0.5s; }
        .node-3 { top: 40%; left: 10%; animation-delay: 1s; }
        .node-4 { top: 60%; right: 15%; animation-delay: 1.5s; }
        .node-5 { top: 80%; left: 20%; animation-delay: 2s; }
        .node-6 { top: 30%; right: 30%; animation-delay: 2.5s; }
        .node-7 { top: 70%; left: 35%; animation-delay: 3s; }
        .node-8 { top: 50%; right: 25%; animation-delay: 3.5s; }
        .node-9 { top: 15%; left: 40%; animation-delay: 0.3s; }
        .node-10 { top: 85%; right: 10%; animation-delay: 0.8s; }
        @keyframes nodeFloat { 0%,100%{transform:translateY(0) scale(1); opacity:0.4} 50%{transform:translateY(-15px) scale(1.3); opacity:0.9} }
        @keyframes gradientShift { 0%{background-position:0% center} 100%{background-position:200% center} }
        @keyframes cursorBlink { 0%,100%{opacity:1} 50%{opacity:0} }
        @keyframes wordIn { 0%{opacity:0;transform:translateY(18px)} 100%{opacity:1;transform:translateY(0)} }
        .word-anim { display:inline-block; opacity:0; animation: wordIn 0.5s cubic-bezier(0.22,1,0.36,1) forwards; }
      ` }} />
    </div>
  );
}

function ThinkingDoctorLottie() {
  const ref = useRef(null);
  useEffect(() => {
    if (!ref.current) return;
    const anim = lottie.loadAnimation({
      container: ref.current,
      renderer: 'svg',
      loop: true,
      autoplay: true,
      animationData: ThinkingDoctorData,
    });
    return () => anim.destroy();
  }, []);
  return <div ref={ref} style={{ width: 260, height: 234, filter: 'drop-shadow(0 0 20px rgba(56,189,248,0.4))' }} />;
}

function ActionCard({ icon, title, desc, link, linkLabel, extras }) {
  return (
    <div className="action-card-hover" style={styles.actionCard}>
      <div className="action-icon-hover" style={styles.actionIcon}>{icon}</div>
      <div style={styles.actionTitle}>{title}</div>
      <div style={styles.actionDesc}>{desc}</div>
      {link && <a href={link} className="action-link-hover" style={styles.actionLink}>{linkLabel}</a>}
      {extras}
    </div>
  );
}

function MapCard() {
  return (
    <div className="location-card-hover" style={styles.locationCard}>
      <div style={styles.locationHeader}>
        <div className="location-icon-box-hover" style={styles.locationIconBox}>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.6" strokeLinecap="round" strokeLinejoin="round">
            <path d="M12 21s-7-5.2-7-11a7 7 0 1114 0c0 5.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>
          </svg>
        </div>
        <div>
          <div style={styles.locationCity}>Xonacatlan, Estado de Mexico  C.P. 52060</div>
          <div style={styles.locationStreet}>C. Benito Juarez S/N, Col. La Jardona</div>
        </div>
        <a href="https://www.google.com/maps?q=Calle+Benito+Juarez+S/N,+La+Jardona,+Xonacatlan,+Estado+de+Mexico,+52060" target="_blank" rel="noopener noreferrer" className="ext-link-hover" style={styles.extLink}>
          Ver mapa
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
        </a>
      </div>
      <div style={styles.mapWrap}>
        <iframe
          title="Ubicacion"
          src="https://maps.google.com/maps?q=Calle+Benito+Juarez+S/N,+La+Jardona,+Xonacatlan,+Estado+de+Mexico,+52060&output=embed"
          width="100%"
          height="180"
          style={{ border: 0, display: 'block', filter: 'invert(90%) hue-rotate(180deg) saturate(0.7) brightness(0.85)' }}
          allowFullScreen
          loading="lazy"
          referrerPolicy="no-referrer-when-downgrade"
        />
      </div>
    </div>
  );
}

const styles = {
  wordAnim: {
    display: 'inline-block',
    opacity: 0,
    animation: 'wordIn 0.5s cubic-bezier(0.22,1,0.36,1) forwards',
    animationFillMode: 'forwards',
  },
  wordAnimGradient: {
    display: 'inline-block',
    opacity: 0,
    animation: 'wordIn 0.5s cubic-bezier(0.22,1,0.36,1) forwards, gradientShift 3s linear 0.7s infinite',
    background: 'linear-gradient(90deg, #38bdf8, #818cf8, #38bdf8)',
    backgroundSize: '200% auto',
    WebkitBackgroundClip: 'text',
    WebkitTextFillColor: 'transparent',
    backgroundClip: 'text',
    animationFillMode: 'forwards',
  },
  gradientText: {
    background: 'linear-gradient(90deg, #38bdf8, #818cf8, #38bdf8)',
    backgroundSize: '200% auto',
    WebkitBackgroundClip: 'text',
    WebkitTextFillColor: 'transparent',
    backgroundClip: 'text',
    animation: 'gradientShift 3s linear infinite',
  },
  cursor: {
    display: 'inline-block',
    color: '#38bdf8',
    fontWeight: 300,
    marginLeft: 2,
    animation: 'cursorBlink 0.8s ease-in-out infinite',
  },
  root: {
    minHeight: "100vh",
    background: "#060d18",
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    fontFamily: "'DM Sans', sans-serif",
    color: "#e2e8f0",
  },
  container: {
    display: "flex",
    gap: 64,
    alignItems: "center",
    maxWidth: 1060,
    width: "100%",
    padding: "60px 32px",
  },
  leftCol: {
    flex: "0 0 320px",
    display: "flex",
    flexDirection: "column",
    alignItems: "center",
    gap: 12,
  },
  availableBadge: {
    display: "flex",
    alignItems: "center",
    gap: 7,
    fontSize: 12,
    color: "#475569",
    letterSpacing: "0.04em",
  },
  badgeHover: {
    transition: "all 0.3s ease",
    cursor: "default",
  },
  robotContainer: {
    transition: "transform 0.4s ease",
  },
  robotHover: {
    ":hover": {
      transform: "scale(1.05)",
    },
  },
  dot: {
    display: "inline-block",
    width: 6,
    height: 6,
    borderRadius: "50%",
    background: "#38bdf8",
    animation: "dotBlink 2s ease-in-out infinite",
  },
  responseNote: {
    fontSize: 12,
    color: "#1e293b",
    margin: 0,
    letterSpacing: "0.03em",
  },
  rightCol: {
    flex: 1,
    display: "flex",
    flexDirection: "column",
    gap: 28,
  },
  headlineBlock: {
    display: "flex",
    flexDirection: "column",
    gap: 14,
  },
  label: {
    fontSize: 11,
    color: "#1e293b",
    letterSpacing: "0.15em",
    textTransform: "uppercase",
  },
  headline: {
    fontWeight: 500,
    fontSize: 32,
    lineHeight: 1.25,
    margin: 0,
    color: "#e2e8f0",
  },
  accent: {
    color: "#e2e8f0",
  },
  accentGlow: {
    textShadow: "0 0 20px rgba(56,189,248,0.5), 0 0 40px rgba(56,189,248,0.3)",
    animation: "textGlow 3s ease-in-out infinite",
  },
  subtext: {
    fontSize: 14,
    color: "#334155",
    lineHeight: 1.75,
    margin: 0,
    maxWidth: 380,
  },
  btnSecondary: {
    display: "inline-flex",
    alignItems: "center",
    gap: 6,
    background: "transparent",
    border: "1px solid #1e293b",
    borderRadius: 8,
    color: "#475569",
    fontSize: 13,
    padding: "8px 16px",
    cursor: "pointer",
    width: "fit-content",
  },
  btnSecondaryHover: {
    transition: "all 0.3s ease",
    ":hover": {
      borderColor: "#38bdf8",
      color: "#94a3b8",
      boxShadow: "0 0 20px rgba(56,189,248,0.2)",
    },
  },
  actionGrid: {
    display: "grid",
    gridTemplateColumns: "repeat(3,1fr)",
    gap: 10,
  },
  actionCard: {
    background: "rgba(5, 17, 38, 0.75)",
    border: "1px solid #112E5A",
    borderRadius: 10,
    padding: "16px 14px",
    display: "flex",
    flexDirection: "column",
    gap: 6,
    boxShadow: "0 0 25px rgba(17, 46, 90, 0.4), inset 0 1px 0 rgba(255,255,255,0.05)",
  },
  actionCardHover: {
    transition: "all 0.3s ease",
    ":hover": {
      borderColor: "#38bdf8",
      transform: "translateY(-4px)",
      boxShadow: "0 8px 24px rgba(56,189,248,0.15)",
    },
  },
  actionIcon: {
    marginBottom: 4,
    color: "#38BDF8",
    filter: "drop-shadow(0 0 8px rgba(56,189,248,0.6))",
  },
  actionTitle: {
    fontSize: 13,
    fontWeight: 500,
    color: "#38BDF8",
  },
  actionDesc: {
    fontSize: 11,
    color: "#38BDF8",
    lineHeight: 1.5,
  },
  actionLink: {
    fontSize: 11,
    color: "#38BDF8",
    textDecoration: "none",
    marginTop: 2,
  },
  actionLinkHover: {
    transition: "all 0.2s ease",
    ":hover": {
      color: "#7dd3fc",
      textDecoration: "underline",
    },
  },
  phoneRow: {
    display: "flex",
    gap: 6,
    alignItems: "baseline",
  },
  phoneLabel: {
    fontSize: 10,
    color: "#38BDF8",
  },
  phoneVal: {
    fontSize: 11,
    color: "#38BDF8",
  },
  locationCard: {
    background: "rgba(5, 17, 38, 0.75)",
    border: "1px solid #112E5A",
    borderRadius: 10,
    overflow: "hidden",
    transition: "all 0.3s ease",
    boxShadow: "0 0 25px rgba(17, 46, 90, 0.4), inset 0 1px 0 rgba(255,255,255,0.05)",
  },
  locationCardHover: {
    ":hover": {
      borderColor: "#38bdf8",
      boxShadow: "0 8px 24px rgba(56,189,248,0.15)",
    },
  },
  locationHeader: {
    display: "flex",
    alignItems: "center",
    gap: 10,
    padding: "12px 16px",
  },
  locationIconBox: {
    width: 30,
    height: 30,
    flexShrink: 0,
    background: "#060d18",
    border: "1px solid #1e293b",
    borderRadius: 6,
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    color: "#38BDF8",
    filter: "drop-shadow(0 0 8px rgba(56,189,248,0.6))",
  },
  locationCity: {
    fontSize: 13,
    fontWeight: 500,
    color: "#38BDF8",
  },
  locationStreet: {
    fontSize: 11,
    color: "#38BDF8",
    marginTop: 1,
  },
  extLink: {
    marginLeft: "auto",
    display: "flex",
    alignItems: "center",
    gap: 4,
    fontSize: 11,
    color: "#38BDF8",
    textDecoration: "none",
    padding: "4px 8px",
    border: "1px solid #1e293b",
    borderRadius: 5,
    flexShrink: 0,
    transition: "all 0.2s ease",
  },
  extLinkHover: {
    ":hover": {
      borderColor: "#38bdf8",
      color: "#7dd3fc",
    },
  },
  mapWrap: {
    position: "relative",
    height: 180,
    overflow: "hidden",
  },
  mapPlaceholder: {
    position: "absolute",
    inset: 0,
    background: "#0a1628",
    display: "flex",
    flexDirection: "column",
    alignItems: "center",
    justifyContent: "center",
  },
  mapPulse: {
    width: 14,
    height: 14,
    borderRadius: "50%",
    background: "#38bdf8",
    animation: "mapPulse 1.8s ease-in-out infinite",
  },
  modalOverlay: {
    position: "fixed",
    inset: 0,
    background: "rgba(0,0,0,0.65)",
    zIndex: 100,
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    padding: 24,
  },
  modalBoxHover: {
    transition: "all 0.3s ease",
    ":hover": {
      borderColor: "#38bdf8",
      boxShadow: "0 0 40px rgba(56,189,248,0.2)",
    },
  },
  modalHeader: {
    display: "flex",
    justifyContent: "space-between",
    alignItems: "flex-start",
    padding: "18px 22px 12px",
    borderBottom: "1px solid #1e293b",
  },
  modalTitle: {
    fontSize: 16,
    fontWeight: 500,
    color: "#38BDF8",
  },
  modalSub: {
    fontSize: 11,
    color: "#38BDF8",
    marginTop: 3,
  },
  modalClose: {
    background: "transparent",
    border: "1px solid #1e293b",
    borderRadius: 6,
    color: "#475569",
    fontSize: 13,
    width: 28,
    height: 28,
    display: "flex",
    alignItems: "center",
    justifyContent: "center",
    cursor: "pointer",
    flexShrink: 0,
  },
  modalCloseHover: {
    transition: "all 0.2s ease",
    ":hover": {
      borderColor: "#38bdf8",
      color: "#94a3b8",
      backgroundColor: "rgba(56,189,248,0.1)",
    },
  },
  modalImg: {
    width: "100%",
    height: "auto",
    display: "block",
    maxHeight: "75vh",
    objectFit: "contain",
    padding: "16px 20px 20px",
    boxSizing: "border-box",
  },
  modalBody: {
    display: "flex",
    alignItems: "center",
    gap: 24,
    padding: "20px 24px 28px",
  },
  modalLottie: {
    flexShrink: 0,
  },
  modalText: {
    flex: 1,
    display: "flex",
    flexDirection: "column",
    gap: 16,
  },
  modalDesc: {
    fontSize: 14,
    color: "#94a3b8",
    lineHeight: 1.8,
    margin: 0,
  },
  modalFeatures: {
    display: "flex",
    flexDirection: "column",
    gap: 8,
  },
  modalFeatureItem: {
    display: "flex",
    alignItems: "center",
    gap: 8,
    fontSize: 13,
    color: "#cbd5e1",
  },
  modalFeatureDot: {
    display: "inline-block",
    width: 6,
    height: 6,
    borderRadius: "50%",
    background: "#38bdf8",
    flexShrink: 0,
    boxShadow: "0 0 6px rgba(56,189,248,0.7)",
  },
  modalBox: {
    background: "#0a1628",
    border: "1px solid #1e293b",
    borderRadius: 12,
    maxWidth: 820,
    width: "100%",
    overflow: "hidden",
  },
};
