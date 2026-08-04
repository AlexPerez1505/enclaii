<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Restringido</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.18);
            --glass-bg-strong: rgba(255, 255, 255, 0.26);
            --glass-border: rgba(255, 255, 255, 0.38);
            --glass-border-soft: rgba(255, 255, 255, 0.22);

            --text-main: rgba(20, 18, 18, 0.96);
            --text-muted: rgba(20, 18, 18, 0.72);
            --text-soft: rgba(14, 11, 11, 0.58);

            --danger-bg: rgba(239, 68, 68, 0.18);
            --danger-border: rgba(248, 113, 113, 0.38);
            --danger-text: #ff0303;

            --primary-bg: rgba(255, 255, 255, 0.88);
            --primary-text: #111827;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background:
                radial-gradient(circle at 20% 15%, rgba(124, 58, 237, 0.48), transparent 32%),
                radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.34), transparent 34%),
                radial-gradient(circle at 50% 85%, rgba(236, 72, 153, 0.26), transparent 30%),
                linear-gradient(135deg, #020617, #0f172a 45%, #111827);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 24px;
            color: var(--text-main);
            overflow: hidden;
        }

        #bg-canvas {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            pointer-events: none;
        }

        .page-glow {
            position: fixed;
            width: 520px;
            height: 520px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            filter: blur(80px);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            pointer-events: none;
        }

        .container {
            width: 100%;
            max-width: 540px;
            position: relative;
            z-index: 10;
            padding: 42px;
            text-align: center;

            background:
                linear-gradient(145deg, rgba(255, 255, 255, 0.32), rgba(255, 255, 255, 0.10)),
                rgba(255, 255, 255, 0.14);

            border: 1px solid var(--glass-border);
            border-radius: 34px;

            box-shadow:
                0 35px 90px rgba(0, 0, 0, 0.48),
                inset 0 1px 0 rgba(255, 255, 255, 0.45),
                inset 0 -1px 0 rgba(255, 255, 255, 0.12);

            backdrop-filter: blur(28px) saturate(180%);
            -webkit-backdrop-filter: blur(28px) saturate(180%);

            animation: fadeIn 0.75s ease-out;
            overflow: hidden;
        }

        .container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 12%;
            right: 12%;
            height: 1px;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.85),
                transparent
            );
        }

        .container::after {
            content: "";
            position: absolute;
            inset: 1px;
            border-radius: 33px;
            pointer-events: none;
            background:
                radial-gradient(circle at 22% 0%, rgba(255, 255, 255, 0.34), transparent 34%),
                radial-gradient(circle at 100% 100%, rgba(255, 255, 255, 0.10), transparent 35%);
            opacity: 0.8;
        }

        .content-layer {
            position: relative;
            z-index: 2;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(22px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .icon-container {
            width: 76px;
            height: 76px;
            background:
                linear-gradient(145deg, rgba(255, 255, 255, 0.34), rgba(255, 255, 255, 0.10)),
                rgba(239, 68, 68, 0.20);
            border: 1px solid rgba(255, 255, 255, 0.36);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: #ffffff;
            font-size: 30px;
            box-shadow:
                0 18px 42px rgba(0, 0, 0, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        h1 {
            font-size: 30px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.045em;
            color: var(--text-main);
            text-shadow: 0 8px 28px rgba(0, 0, 0, 0.35);
        }

        .message {
            font-size: 15px;
            line-height: 1.7;
            color: var(--text-muted);
            margin-bottom: 28px;
        }

        .schedule-card {
            background:
                linear-gradient(145deg, rgba(255, 255, 255, 0.24), rgba(255, 255, 255, 0.08)),
                rgba(255, 255, 255, 0.12);
            border: 1px solid var(--glass-border-soft);
            border-radius: 24px;
            padding: 20px 22px;
            margin-bottom: 28px;
            text-align: left;
            box-shadow:
                0 18px 45px rgba(0, 0, 0, 0.18),
                inset 0 1px 0 rgba(255, 255, 255, 0.32);
            backdrop-filter: blur(20px) saturate(160%);
            -webkit-backdrop-filter: blur(20px) saturate(160%);
        }

        .schedule-title {
            display: flex;
            align-items: center;
            gap: 9px;
            color: var(--text-main);
            font-size: 12px;
            font-weight: 800;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .schedule-title i {
            color: rgba(12, 10, 10, 0.78);
        }

        .schedule-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            padding: 13px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
            font-size: 14px;
        }

        .schedule-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .schedule-day {
            font-weight: 700;
            color: var(--text-main);
        }

        .schedule-hour {
            color: var(--text-muted);
            text-align: right;
            font-weight: 500;
        }

        .badge-closed {
            background: var(--danger-bg);
            color: var(--danger-text);
            border: 1px solid var(--danger-border);
            font-weight: 800;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            text-decoration: none;
            cursor: pointer;
            padding: 13px 18px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 800;
            transition: all 0.22s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            flex: 1;
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .btn-primary {
            background: var(--primary-bg);
            color: var(--primary-text);
            border: 1px solid rgba(255, 255, 255, 0.70);
            box-shadow:
                0 14px 30px rgba(0, 0, 0, 0.24),
                inset 0 1px 0 rgba(255, 255, 255, 0.95);
        }

        .btn-primary:hover {
            background: #ffffff;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.12);
            color: var(--text-main);
            border: 1px solid rgba(255, 255, 255, 0.24);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.24);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.20);
            transform: translateY(-2px);
        }

        .footer-note {
            margin-top: 24px;
            font-size: 12px;
            line-height: 1.6;
            color: var(--text-soft);
        }

        @media (max-width: 560px) {
            body {
                padding: 16px;
                overflow-y: auto;
            }

            .container {
                padding: 32px 22px;
                border-radius: 28px;
            }

            .container::after {
                border-radius: 27px;
            }

            h1 {
                font-size: 25px;
            }

            .message {
                font-size: 14px;
            }

            .schedule-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }

            .schedule-hour {
                text-align: left;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <canvas id="bg-canvas"></canvas>
    <div class="page-glow"></div>

    <div class="container">
        <div class="content-layer">
            <div class="icon-container">
                <i class="fa-solid fa-shield-halved"></i>
            </div>

            <h1>Acceso Restringido</h1>

            <p class="message">
                Tu usuario no tiene permisos para ingresar al sistema en este momento.
            </p>

            <div class="schedule-card">
                <div class="schedule-title">
                    <i class="fa-regular fa-clock"></i>
                    Horario de Operación
                </div>

                <div class="schedule-row">
                    <span class="schedule-day">Lunes a Viernes</span>
                    <span class="schedule-hour">09:00 AM — 08:30 PM</span>
                </div>

                <div class="schedule-row">
                    <span class="schedule-day">Sábado</span>
                    <span class="schedule-hour">09:00 AM — 01:30 PM</span>
                </div>

                <div class="schedule-row">
                    <span class="schedule-day">Domingo</span>
                    <span class="badge-closed">Cerrado</span>
                </div>
            </div>

            <div class="actions">
                <a href="{{ url('/') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    Volver
                </a>

                <a href="{{ url('/login') }}" class="btn btn-primary">
                    Intentar de nuevo
                </a>
            </div>

            <p class="footer-note">
                Ref: Política de control de acceso por horario. Contacte a soporte si cree que es un error.
            </p>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('bg-canvas');
        const renderer = new THREE.WebGLRenderer({
            canvas,
            antialias: true,
            alpha: true
        });

        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(
            45,
            window.innerWidth / window.innerHeight,
            0.1,
            100
        );

        camera.position.z = 30;

        const ambientLight = new THREE.AmbientLight(0xffffff, 0.45);
        scene.add(ambientLight);

        const pointLight1 = new THREE.PointLight(0xffffff, 0.9);
        pointLight1.position.set(20, 20, 20);
        scene.add(pointLight1);

        const pointLight2 = new THREE.PointLight(0x7c3aed, 1.15);
        pointLight2.position.set(-20, -20, 20);
        scene.add(pointLight2);

        const pointLight3 = new THREE.PointLight(0x38bdf8, 0.65);
        pointLight3.position.set(0, 12, 18);
        scene.add(pointLight3);

        const colors = [
            0x7c3aed,
            0xffffff,
            0x111827,
            0x334155,
            0x38bdf8,
            0xec4899
        ];

        const spheres = [];
        const numSpheres = 60;
        const geometry = new THREE.SphereGeometry(1, 32, 32);

        for (let i = 0; i < numSpheres; i++) {
            const material = new THREE.MeshStandardMaterial({
                color: colors[Math.floor(Math.random() * colors.length)],
                roughness: 0.12,
                metalness: 0.22
            });

            const sphere = new THREE.Mesh(geometry, material);

            const scale = Math.random() * 1.5 + 0.45;
            sphere.scale.set(scale, scale, scale);

            sphere.position.x = (Math.random() - 0.5) * 42;
            sphere.position.y = (Math.random() - 0.5) * 32;
            sphere.position.z = (Math.random() - 0.5) * 20;

            sphere.userData = {
                velocity: new THREE.Vector3(
                    (Math.random() - 0.5) * 0.045,
                    (Math.random() - 0.5) * 0.045,
                    (Math.random() - 0.5) * 0.045
                ),
                radius: scale
            };

            scene.add(sphere);
            spheres.push(sphere);
        }

        const bounds = {
            x: 25,
            y: 15,
            z: 10
        };

        function animate() {
            requestAnimationFrame(animate);

            spheres.forEach((sphere) => {
                sphere.position.add(sphere.userData.velocity);

                if (Math.abs(sphere.position.x) + sphere.userData.radius > bounds.x) {
                    sphere.userData.velocity.x *= -1;
                }

                if (Math.abs(sphere.position.y) + sphere.userData.radius > bounds.y) {
                    sphere.userData.velocity.y *= -1;
                }

                if (Math.abs(sphere.position.z) + sphere.userData.radius > bounds.z) {
                    sphere.userData.velocity.z *= -1;
                }

                sphere.rotation.x += 0.005;
                sphere.rotation.y += 0.005;
            });

            renderer.render(scene, camera);
        }

        animate();

        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>

</body>
</html>