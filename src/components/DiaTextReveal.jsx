export function DiaTextReveal({
  text,
  colors = ['#A97CF8', '#F38CB8', '#FDCC92'],
  className = '',
  duration = 4,
  staggerDelay = 0.04,
}) {
  /* Gradiente extendido (repite el primer color al final → loop sin saltos) */
  const gradient = `linear-gradient(90deg, ${[...colors, colors[0]].join(', ')})`
  const chars = Array.from(text)

  return (
    <span
      className={`dia-reveal inline-block ${className}`}
      style={{ '--dia-duration': `${duration}s` }}
    >
      {chars.map((ch, i) => (
        <span
          key={i}
          className="dia-char inline-block"
          style={{
            '--char-delay': `${i * staggerDelay}s`,
            backgroundImage: gradient,
          }}
        >
          {ch === ' ' ? '\u00A0' : ch}
        </span>
      ))}
    </span>
  )
}

export default DiaTextReveal