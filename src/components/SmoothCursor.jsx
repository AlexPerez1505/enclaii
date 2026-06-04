import { useEffect, useRef, useState } from 'react'
import { motion, useSpring } from 'motion/react'

const DefaultCursorSVG = () => (
  <svg
    width="50"
    height="54"
    viewBox="0 0 50 54"
    fill="none"
    style={{ scale: 0.5 }}
    xmlns="http://www.w3.org/2000/svg"
  >
    <g filter="url(#filter0_d_91_7928)">
      <path
        d="M42.6817 41.1495L27.5103 6.79925C26.7269 5.02557 24.2082 5.02558 23.3927 6.79925L7.59814 41.1495C6.75833 42.9759 8.52712 44.8902 10.4125 44.1954L24.3757 39.0496C24.7669 38.9046 25.1958 38.9046 25.587 39.0496L39.5502 44.1954C41.4355 44.8902 43.2043 42.9759 42.6817 41.1495Z"
        fill="black"
      />
      <path
        d="M43.7146 40.6933L28.5432 6.34306C27.3568 3.65428 23.5773 3.65427 22.3909 6.34306L6.59631 40.6933C5.31123 43.5743 8.00958 46.5557 10.9325 45.4646L24.8956 40.3188C25.0156 40.2741 25.1472 40.2741 25.2672 40.3188L39.2303 45.4646C42.1532 46.5557 44.9999 43.5743 43.7146 40.6933Z"
        stroke="white"
        strokeWidth="2.25825"
      />
    </g>
    <defs>
      <filter
        id="filter0_d_91_7928"
        x="0.602397"
        y="0.952444"
        width="49.0584"
        height="52.428"
        filterUnits="userSpaceOnUse"
        colorInterpolationFilters="sRGB"
      >
        <feFlood floodOpacity="0" result="BackgroundImageFix" />
        <feColorMatrix
          in="SourceAlpha"
          type="matrix"
          values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
          result="hardAlpha"
        />
        <feOffset dy="2.25825" />
        <feGaussianBlur stdDeviation="2.25825" />
        <feComposite in2="hardAlpha" operator="out" />
        <feColorMatrix
          type="matrix"
          values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.2 0"
        />
        <feBlend
          mode="normal"
          in2="BackgroundImageFix"
          result="effect1_dropShadow_91_7928"
        />
        <feBlend
          mode="normal"
          in="SourceGraphic"
          in2="effect1_dropShadow_91_7928"
          result="shape"
        />
      </filter>
    </defs>
  </svg>
)

export function SmoothCursor({
  cursor = <DefaultCursorSVG />,
  springConfig = {
    damping: 45,
    stiffness: 400,
    mass: 1,
    restDelta: 0.001,
  },
}) {
  const [isMoving, setIsMoving] = useState(false)
  const lastMousePos = useRef({ x: 0, y: 0 })
  const velocity = useRef({ x: 0, y: 0 })
  const lastUpdateTime = useRef(Date.now())
  const previousAngle = useRef(0)
  const accumulatedRotation = useRef(0)

  const cursorX = useSpring(0, springConfig)
  const cursorY = useSpring(0, springConfig)
  const rotation = useSpring(0, { ...springConfig, damping: 60, stiffness: 300 })
  const scale = useSpring(1, { ...springConfig, stiffness: 500, damping: 35 })

  useEffect(() => {
    // Solo en dispositivos con puntero fino (mouse)
    if (!window.matchMedia('(pointer: fine)').matches) return

    const updateVelocity = (currentPos) => {
      const currentTime = Date.now()
      const deltaTime = currentTime - lastUpdateTime.current
      if (deltaTime > 0) {
        velocity.current = {
          x: (currentPos.x - lastMousePos.current.x) / deltaTime,
          y: (currentPos.y - lastMousePos.current.y) / deltaTime,
        }
      }
      lastUpdateTime.current = currentTime
      lastMousePos.current = currentPos
    }

    const smoothMouseMove = (e) => {
      const currentPos = { x: e.clientX, y: e.clientY }
      updateVelocity(currentPos)
      const speed = Math.hypot(velocity.current.x, velocity.current.y)

      cursorX.set(currentPos.x)
      cursorY.set(currentPos.y)

      if (speed > 0.1) {
        const currentAngle =
          Math.atan2(velocity.current.y, velocity.current.x) * (180 / Math.PI) + 90
        let angleDiff = currentAngle - previousAngle.current
        if (angleDiff > 180) angleDiff -= 360
        if (angleDiff < -180) angleDiff += 360
        accumulatedRotation.current += angleDiff
        rotation.set(accumulatedRotation.current)
        previousAngle.current = currentAngle

        scale.set(0.95)
        setIsMoving(true)

        const timeout = setTimeout(() => {
          scale.set(1)
          setIsMoving(false)
        }, 150)
        return () => clearTimeout(timeout)
      }
    }

    let rafId
    const throttledMouseMove = (e) => {
      if (rafId) return
      rafId = requestAnimationFrame(() => {
        smoothMouseMove(e)
        rafId = 0
      })
    }

    document.body.style.cursor = 'none'
    window.addEventListener('mousemove', throttledMouseMove)

    return () => {
      window.removeEventListener('mousemove', throttledMouseMove)
      document.body.style.cursor = 'auto'
      if (rafId) cancelAnimationFrame(rafId)
    }
  }, [cursorX, cursorY, rotation, scale])

  return (
    <motion.div
      style={{
        position: 'fixed',
        left: 0,
        top: 0,
        translateX: '-50%',
        translateY: '-50%',
        rotate: rotation,
        scale: scale,
        zIndex: 9999,
        pointerEvents: 'none',
        willChange: 'transform',
        x: cursorX,
        y: cursorY,
      }}
      initial={{ scale: 0 }}
      animate={{ scale: 1 }}
      transition={{ type: 'spring', stiffness: 400, damping: 30 }}
    >
      {cursor}
    </motion.div>
  )
}