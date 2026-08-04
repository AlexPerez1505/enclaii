@extends('layouts.app')

@section('title', 'QR de salida externa')
@section('titulo', 'QR de salida externa')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    :root {
        --bg-body: #f6f8fc;
        --surface: #ffffff;
        --surface-2: #fbfcfe;
        --border-color: #e2e8f0;
        --border-strong: #d7e0ea;
        --text-primary: #0f172a;
        --text-secondary: #64748b;
        --text-soft: #94a3b8;
        --primary-color: #2563eb;
        --primary-hover: #1d4ed8;
        --primary-light: #eff6ff;
        --success-color: #10b981;
        --success-deep: #047857;
        --success-bg: #ecfdf5;
        --success-border: #a7f3d0;
        --warning-bg: #fffbeb;
        --warning-text: #b45309;
        --warning-border: #fef3c7;
        --danger-bg: #fef2f2;
        --danger-text: #b91c1c;
        --danger-border: #fecaca;
        --radius-xl: 24px;
        --radius-lg: 20px;
        --radius-md: 14px;
        --radius-sm: 12px;
        --shadow-xs: 0 1px 2px rgba(15, 23, 42, 0.04);
        --shadow-sm: 0 8px 24px rgba(15, 23, 42, 0.06);
        --shadow-md: 0 12px 32px rgba(15, 23, 42, 0.08);
        --shadow-lg: 0 24px 60px rgba(15, 23, 42, 0.10);
        --ease: cubic-bezier(.22,1,.36,1);
    }

    body {
        background:
            radial-gradient(circle at top left, rgba(37,99,235,.05), transparent 22%),
            radial-gradient(circle at top right, rgba(16,185,129,.04), transparent 18%),
            linear-gradient(180deg, #f8fafc 0%, #f3f6fb 100%);
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: var(--text-primary);
    }

    .premium-container {
        max-width: 1180px;
        margin: 0 auto;
        padding: 2rem 1.25rem 3rem;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        padding: 1rem;
       
        border-radius: var(--radius-xl);
      
        box-shadow: var(--shadow-sm);
        position: relative;
        overflow: hidden;
    }

    .page-header::after {
        content: "";
        position: absolute;
        inset: auto 0 0 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(148,163,184,.22), transparent);
    }

    .header-title h1 {
        font-size: clamp(1.8rem, 2.8vw, 2.5rem);
        font-weight: 9;
        letter-spacing: -0.04em;
        margin-bottom: 0.5rem;
        line-height: 1;
    }

    .header-title p {
        color: var(--text-secondary);
        font-size: 1rem;
        margin: 0;
        max-width: 700px;
        line-height: 1.6;
    }

    .badge-dynamic,
    .badge-live {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.5rem 0.9rem;
        border-radius: 999px;
        font-size: 0.88rem;
        font-weight: 800;
        letter-spacing: .01em;
        border: 1px solid transparent;
    }

    .badge-dynamic {
        background-color: var(--primary-light);
        color: var(--primary-color);
        border-color: #dbeafe;
        margin-bottom: .9rem;
    }

    .badge-live {
        background: #f8fafc;
        color: #334155;
        border-color: var(--border-color);
    }

    .badge-live .live-dot {
        width: 10px;
        height: 10px;
        border-radius: 999px;
        background: #22c55e;
        box-shadow: 0 0 0 0 rgba(34,197,94,.5);
        animation: pulseDot 1.7s infinite;
    }

    .badge-live.is-completed {
        background: var(--success-bg);
        color: var(--success-deep);
        border-color: var(--success-border);
    }

    .badge-live.is-completed .live-dot {
        background: var(--success-color);
        box-shadow: none;
        animation: none;
    }

    .badge-live.is-warning {
        background: var(--warning-bg);
        color: var(--warning-text);
        border-color: var(--warning-border);
    }

    .badge-live.is-warning .live-dot {
        background: #f59e0b;
        animation: pulseDotWarning 1.5s infinite;
    }

    @keyframes pulseDot {
        0% { box-shadow: 0 0 0 0 rgba(34,197,94,.45); }
        70% { box-shadow: 0 0 0 10px rgba(34,197,94,0); }
        100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
    }

    @keyframes pulseDotWarning {
        0% { box-shadow: 0 0 0 0 rgba(245,158,11,.45); }
        70% { box-shadow: 0 0 0 10px rgba(245,158,11,0); }
        100% { box-shadow: 0 0 0 0 rgba(245,158,11,0); }
    }

    .btn-premium {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.7rem;
        padding: 0.9rem 1.2rem;
        min-height: 52px;
        border-radius: 16px;
        font-weight: 800;
        font-size: 0.96rem;
        transition: all .25s var(--ease);
        text-decoration: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        white-space: nowrap;
    }

    .btn-premium:hover {
        transform: translateY(-2px);
    }

    .btn-primary-custom {
        background: linear-gradient(135deg, #16a8ef 0%, #2563eb 100%);
        color: white;
        border: 1px solid transparent;
        box-shadow: 0 12px 26px rgba(37, 99, 235, 0.22);
    }

    .btn-primary-custom:hover {
        background: linear-gradient(135deg, #0ea5e9 0%, #1d4ed8 100%);
        color: white;
        box-shadow: 0 16px 32px rgba(37, 99, 235, 0.28);
    }

    .btn-outline-custom {
        background-color: var(--surface);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-xs);
    }

    .btn-outline-custom:hover {
        background-color: #f8fafc;
        color: var(--text-primary);
        border-color: var(--border-strong);
    }

    .btn-success-custom {
        background-color: var(--success-bg);
        color: var(--success-deep);
        border: 1px solid var(--success-border);
        box-shadow: var(--shadow-xs);
    }

    .btn-success-custom:hover {
        background: #dff9ed;
        color: var(--success-deep);
    }

    .premium-card {
        background: linear-gradient(180deg, #ffffff 0%, #fcfdff 100%);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-md);
        padding: 1.6rem;
        height: 100%;
        position: relative;
        overflow: hidden;
        transition: transform .25s var(--ease), box-shadow .25s var(--ease), opacity .25s ease;
    }

    .premium-card.is-done {
        opacity: .4;
        pointer-events: none;
        filter: grayscale(.08);
    }

    .card-title {
        font-size: 1.15rem;
        font-weight: 800;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        letter-spacing: -.02em;
    }

    .qr-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }

    .qr-frame {
        background: var(--surface);
        padding: 1.2rem;
        border-radius: 22px;
        border: 2px dashed var(--border-color);
        position: relative;
        margin-bottom: 1.25rem;
        transition: all 0.3s ease;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.6);
    }

    .qr-frame:hover {
        border-color: var(--primary-color);
    }

    .timer-badge {
        font-size: 1.1rem;
        font-weight: 900;
        color: var(--text-primary);
        background: var(--surface-2);
        padding: 0.8rem 1.1rem;
        border-radius: 14px;
        border: 1px solid var(--border-color);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        margin-top: 0.55rem;
        min-width: 180px;
        box-shadow: var(--shadow-xs);
    }

    .timer-badge.is-warning {
        color: var(--warning-text);
        border-color: var(--warning-border);
        background: var(--warning-bg);
    }

    .timer-badge.is-expired {
        color: var(--danger-text);
        border-color: var(--danger-border);
        background: var(--danger-bg);
    }

    .status-panel {
        width: 100%;
        margin-top: 1rem;
        padding: 1rem;
        border-radius: 18px;
        border: 1px solid var(--border-color);
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .status-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .status-text-main {
        font-size: .98rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    .status-text-sub {
        color: var(--text-secondary);
        font-size: .88rem;
        margin-top: .15rem;
    }

    .url-block {
        width: 100%;
        margin-top: 1.2rem;
    }

    .url-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-align: left;
        width: 100%;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .url-input-wrap {
        position: relative;
        width: 100%;
    }

    .url-input {
        width: 100%;
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 0.95rem 1rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 0.86rem;
        color: var(--text-primary);
        outline: none;
        transition: all 0.2s ease;
    }

    .url-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.08);
        background: #fff;
    }

    .copy-feedback {
        opacity: 0;
        transform: translateY(10px);
        color: var(--success-color);
        font-weight: 700;
        font-size: 0.92rem;
        transition: all 0.3s ease;
        margin-top: 0.75rem;
        text-align: center;
    }

    .copy-feedback.show {
        opacity: 1;
        transform: translateY(0);
    }

    .data-list {
        display: flex;
        flex-direction: column;
        gap: 1.1rem;
    }

    .data-row {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding-bottom: 1.1rem;
        border-bottom: 1px solid var(--border-color);
    }

    .data-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .data-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        background: #f8fafc;
        color: #475569;
        border-radius: 14px;
        font-size: 1.05rem;
        border: 1px solid var(--border-color);
        flex: 0 0 42px;
    }

    .data-content {
        flex: 1;
    }

    .data-label {
        font-size: 0.8rem;
        color: var(--text-secondary);
        font-weight: 700;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .data-value {
        font-size: 1rem;
        color: var(--text-primary);
        font-weight: 700;
    }

    .alert-premium {
        background-color: var(--warning-bg);
        border: 1px solid var(--warning-border);
        color: var(--warning-text);
        border-radius: 16px;
        padding: 1rem;
        font-size: 0.92rem;
        font-weight: 600;
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        line-height: 1.55;
    }

    .completion-overlay {
        position: fixed;
        inset: 0;
        background: rgba(248,250,252,.76);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: opacity .35s ease;
        padding: 1rem;
    }

    .completion-overlay.show {
        opacity: 1;
        pointer-events: all;
    }

    .completion-modal {
        width: min(520px, 100%);
        border-radius: 28px;
        background:
            radial-gradient(circle at top right, rgba(16,185,129,.09), transparent 24%),
            linear-gradient(180deg, #ffffff 0%, #fcfefd 100%);
        border: 1px solid var(--success-border);
        box-shadow: var(--shadow-lg);
        padding: 2rem 1.6rem;
        text-align: center;
        transform: translateY(18px) scale(.98);
        transition: transform .38s var(--ease);
    }

    .completion-overlay.show .completion-modal {
        transform: translateY(0) scale(1);
    }

    .completion-icon {
        width: 92px;
        height: 92px;
        margin: 0 auto 1rem;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(180deg, #ecfdf5 0%, #d1fae5 100%);
        border: 1px solid var(--success-border);
        color: var(--success-deep);
        font-size: 2.2rem;
        box-shadow: 0 0 0 10px rgba(16,185,129,.06);
        animation: successPop .9s var(--ease);
    }

    @keyframes successPop {
        0% { transform: scale(.78); opacity: 0; }
        60% { transform: scale(1.08); opacity: 1; }
        100% { transform: scale(1); }
    }

    .completion-title {
        font-size: 1.65rem;
        font-weight: 900;
        letter-spacing: -.04em;
        color: var(--text-primary);
        margin-bottom: .45rem;
    }

    .completion-sub {
        color: var(--text-secondary);
        line-height: 1.65;
        margin-bottom: 1rem;
        font-weight: 500;
    }

    .completion-progress {
        width: 100%;
        height: 10px;
        border-radius: 999px;
        background: #eef2f7;
        overflow: hidden;
        border: 1px solid var(--border-color);
        margin-top: 1rem;
    }

    .completion-progress-bar {
        height: 100%;
        width: 0%;
        border-radius: inherit;
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        transition: width 2.2s linear;
    }

    .completion-meta {
        margin-top: 1rem;
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        padding: .62rem .9rem;
        border-radius: 999px;
        background: var(--success-bg);
        color: var(--success-deep);
        border: 1px solid var(--success-border);
        font-weight: 800;
        font-size: .92rem;
    }

    .fade-ready {
        transition: opacity .25s ease, transform .25s ease, filter .25s ease;
    }

    @media (max-width: 768px) {
        .premium-container {
            padding: 1.15rem .9rem 2.2rem;
        }

        .premium-card {
            padding: 1.2rem;
        }

        .qr-frame {
            padding: 1rem;
        }

        .timer-badge {
            font-size: 1rem;
            width: 100%;
        }

        .page-header {
            padding: 1.2rem;
        }

        .btn-premium {
            width: 100%;
        }

        .completion-title {
            font-size: 1.4rem;
        }
    }
</style>

<div class="premium-container">
    <div class="page-header fade-ready" id="pageHeader">
        <div class="header-title">
            <span class="badge-dynamic">
                <i class="bi bi-arrow-repeat"></i> Token de Seguridad Activo
            </span>

            <h1>QR Dinámico de Salida</h1>

        </div>

        <div class="d-flex gap-3 flex-wrap">
            <a href="{{ route('servicio.proceso', $servicio->id) }}" class="btn-premium btn-outline-custom">
                <i class="bi bi-arrow-left"></i> Regresar
            </a>

            <a href="{{ $accessUrl }}" target="_blank" class="btn-premium btn-primary-custom" id="openFormBtn">
                <i class="bi bi-box-arrow-up-right"></i> Abrir formulario
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="premium-card fade-ready" id="qrCard">
                <div class="qr-container">
                    <div class="qr-frame" id="qrFrame">
                        <canvas id="qrCanvas"></canvas>
                    </div>

                    <div class="status-panel">
                        <div class="status-line">
                            <div>
                                <div class="status-text-main" id="liveTitle">Monitoreo en tiempo real</div>
                                <div class="status-text-sub" id="liveSubtitle">Esperando que el formulario sea enviado desde el enlace o QR.</div>
                            </div>

                            <span class="badge-live" id="liveBadge">
                                <span class="live-dot"></span>
                                <span id="liveBadgeText">En espera</span>
                            </span>
                        </div>
                    </div>

                    <p class="text-muted mb-2 fs-6 mt-3">
                        Vence: <strong>{{ \Carbon\Carbon::parse($expiraEn)->format('d/m/Y H:i:s') }}</strong>
                    </p>

                    <div class="timer-badge" id="timerBadge">
                        <i class="bi bi-stopwatch text-primary"></i>
                        <span id="countdownText">00:00:00</span>
                    </div>

                    <div class="url-block">
                        <div class="url-label">URL de acceso</div>
                        <div class="url-input-wrap">
                            <input
                                type="text"
                                id="accessUrlInput"
                                class="url-input"
                                value="{{ $accessUrl }}"
                                readonly
                                onclick="this.select(); this.setSelectionRange(0, this.value.length);"
                            >
                        </div>
                    </div>

                    <button type="button" class="btn-premium btn-outline-custom w-100 mt-3 justify-content-center" id="copyBtn">
                        <i class="bi bi-copy"></i>
                        <span id="copyBtnText">Copiar URL de acceso</span>
                    </button>

                    <div class="copy-feedback" id="copyOk">
                        <i class="bi bi-check-circle-fill"></i> ¡Enlace copiado al portapapeles!
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="premium-card fade-ready" id="infoCard">
                <h5 class="card-title">
                    <i class="bi bi-list-columns-reverse text-primary"></i> Especificaciones del Servicio
                </h5>

                <div class="data-list">
                    <div class="data-row">
                        <div class="data-icon"><i class="bi bi-pc-display"></i></div>
                        <div class="data-content">
                            <div class="data-label">Equipo</div>
                            <div class="data-value">
                                {{ $servicio->tipo_equipo ?? '—' }}
                                @if($servicio->subtipo_equipo)
                                    <span class="text-muted fw-normal px-1">•</span> {{ $servicio->subtipo_equipo }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="data-row">
                        <div class="data-icon"><i class="bi bi-upc-scan"></i></div>
                        <div class="data-content">
                            <div class="data-label">Número de Serie</div>
                            <div class="data-value">{{ $servicio->numero_serie ?: '—' }}</div>
                        </div>
                    </div>

                    <div class="data-row">
                        <div class="data-icon"><i class="bi bi-tags"></i></div>
                        <div class="data-content">
                            <div class="data-label">Marca / Modelo</div>
                            <div class="data-value">{{ trim(($servicio->marca ?? '').' '.($servicio->modelo ?? '')) ?: '—' }}</div>
                        </div>
                    </div>

                    <div class="data-row">
                        <div class="data-icon"><i class="bi bi-person-badge"></i></div>
                        <div class="data-content">
                            <div class="data-label">Registrado por</div>
                            <div class="data-value">{{ $servicio->user_name ?: '—' }}</div>
                        </div>
                    </div>

                    <div class="data-row">
                        <div class="data-icon"><i class="bi bi-shield-lock"></i></div>
                        <div class="data-content">
                            <div class="data-label">Servicio ID & Token</div>
                            <div class="data-value">
                                #{{ $servicio->id }}
                                <span class="text-muted fw-normal px-1">|</span>
                                <span class="font-monospace text-muted">{{ \Illuminate\Support\Str::limit($tokenActual, 14, '...') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert-premium mt-4">
                    <i class="bi bi-broadcast-pin fs-5"></i>
                    <div>
                        <strong>Actualización automática</strong><br>
                        Esta pantalla consulta en segundo plano si el formulario ya fue enviado. Al completarse, mostrará la confirmación y redirigirá sola al proceso del servicio.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="completion-overlay" id="completionOverlay" aria-hidden="true">
    <div class="completion-modal">
        <div class="completion-icon">
            <i class="bi bi-check2-circle"></i>
        </div>

        <div class="completion-title">Formulario completado</div>
        <div class="completion-sub" id="completionMessage">
            El registro de salida foránea se guardó correctamente. Esta pantalla se cerrará automáticamente.
        </div>

        <div class="completion-meta">
            <i class="bi bi-arrow-right-circle"></i>
            Redirigiendo al proceso del servicio...
        </div>

        <div class="completion-progress">
            <div class="completion-progress-bar" id="completionProgressBar"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async () => {
    const url = @json($accessUrl);
    const statusUrl = @json(route('servicio.externo.salida.qr.status', $servicio->id));
    const redirectUrl = @json(route('servicio.proceso', $servicio->id));

    const canvas = document.getElementById('qrCanvas');
    const accessUrlInput = document.getElementById('accessUrlInput');
    const copyBtn = document.getElementById('copyBtn');
    const copyOk = document.getElementById('copyOk');
    const countdownText = document.getElementById('countdownText');
    const timerBadge = document.getElementById('timerBadge');
    const liveBadge = document.getElementById('liveBadge');
    const liveBadgeText = document.getElementById('liveBadgeText');
    const liveTitle = document.getElementById('liveTitle');
    const liveSubtitle = document.getElementById('liveSubtitle');
    const qrCard = document.getElementById('qrCard');
    const infoCard = document.getElementById('infoCard');
    const pageHeader = document.getElementById('pageHeader');
    const completionOverlay = document.getElementById('completionOverlay');
    const completionMessage = document.getElementById('completionMessage');
    const completionProgressBar = document.getElementById('completionProgressBar');

    let remaining = Math.max(0, Math.floor(Number(@json($ttlSeconds)) || 0));
    let completed = false;
    let countdownInterval = null;
    let pollingInterval = null;
    let redirectTimeout = null;

    try {
        await QRCode.toCanvas(canvas, url, {
            width: 280,
            margin: 1,
            color: {
                dark: '#0f172a',
                light: '#ffffff'
            }
        });
    } catch (e) {
        console.error('Error generando QR:', e);
    }

    async function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return true;
        }

        accessUrlInput.removeAttribute('readonly');
        accessUrlInput.select();
        accessUrlInput.setSelectionRange(0, accessUrlInput.value.length);

        const copied = document.execCommand('copy');

        accessUrlInput.setAttribute('readonly', true);
        window.getSelection()?.removeAllRanges();

        if (!copied) {
            throw new Error('No se pudo copiar el texto');
        }

        return true;
    }

    function setCopiedState() {
        copyOk.classList.add('show');
        copyBtn.classList.remove('btn-outline-custom', 'btn-primary-custom');
        copyBtn.classList.add('btn-success-custom');
        copyBtn.innerHTML = '<i class="bi bi-check2-all"></i><span>Copiado</span>';

        setTimeout(() => {
            copyOk.classList.remove('show');
            copyBtn.classList.remove('btn-success-custom', 'btn-primary-custom');
            copyBtn.classList.add('btn-outline-custom');
            copyBtn.innerHTML = '<i class="bi bi-copy"></i><span>Copiar URL de acceso</span>';
        }, 2200);
    }

    copyBtn?.addEventListener('click', async () => {
        try {
            await copyToClipboard(url);
            setCopiedState();
        } catch (e) {
            alert('No se pudo copiar automáticamente. Selecciona la URL y cópiala manualmente.');
            accessUrlInput.focus();
            accessUrlInput.select();
            accessUrlInput.setSelectionRange(0, accessUrlInput.value.length);
        }
    });

    accessUrlInput?.addEventListener('focus', function () {
        this.select();
        this.setSelectionRange(0, this.value.length);
    });

    accessUrlInput?.addEventListener('click', function () {
        this.select();
        this.setSelectionRange(0, this.value.length);
    });

    function pad(value) {
        return String(value).padStart(2, '0');
    }

    function setLiveState(state, text, subtext = '') {
        liveBadge.classList.remove('is-completed', 'is-warning');

        if (state === 'completed') {
            liveBadge.classList.add('is-completed');
            liveBadgeText.textContent = text || 'Completado';
        } else if (state === 'warning') {
            liveBadge.classList.add('is-warning');
            liveBadgeText.textContent = text || 'Por vencer';
        } else {
            liveBadgeText.textContent = text || 'En espera';
        }

        if (subtext) {
            liveSubtitle.textContent = subtext;
        }
    }

    function paintCountdown() {
        const safeRemaining = Math.max(0, Math.floor(remaining));
        const hours = Math.floor(safeRemaining / 3600);
        const minutes = Math.floor((safeRemaining % 3600) / 60);
        const seconds = Math.floor(safeRemaining % 60);

        countdownText.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;

        if (completed) return;

        if (safeRemaining <= 0) {
            timerBadge.classList.remove('is-warning');
            timerBadge.classList.add('is-expired');
            setLiveState('warning', 'Expirado', 'El token venció. Se generará uno nuevo automáticamente.');
            return;
        }

        timerBadge.classList.remove('is-expired');

        if (safeRemaining < 30) {
            timerBadge.classList.add('is-warning');
            setLiveState('warning', 'Por vencer', 'El token está por vencer. La pantalla se renovará sola si aún no se envía el formulario.');
        } else {
            timerBadge.classList.remove('is-warning');
            setLiveState('waiting', 'En espera', 'Esperando que el formulario sea enviado desde el enlace o QR.');
        }
    }

    function freezeVisualState() {
        qrCard.classList.add('is-done');
        infoCard.classList.add('is-done');
        pageHeader.style.opacity = '.92';
        pageHeader.style.transform = 'translateY(-2px)';
    }

    function showCompletedScreen(message, nextUrl) {
        if (completed) return;

        completed = true;

        if (countdownInterval) clearInterval(countdownInterval);
        if (pollingInterval) clearInterval(pollingInterval);
        if (redirectTimeout) clearTimeout(redirectTimeout);

        timerBadge.classList.remove('is-warning', 'is-expired');
        setLiveState('completed', 'Completado', 'El formulario ya fue enviado y validado correctamente.');
        liveTitle.textContent = 'Registro finalizado';
        completionMessage.textContent = message || 'El registro de salida foránea se guardó correctamente. Esta pantalla se cerrará automáticamente.';

        freezeVisualState();

        completionOverlay.classList.add('show');
        completionOverlay.setAttribute('aria-hidden', 'false');

        requestAnimationFrame(() => {
            completionProgressBar.style.width = '100%';
        });

        redirectTimeout = setTimeout(() => {
            window.location.href = nextUrl || redirectUrl;
        }, 2200);
    }

    async function checkFormCompletion() {
        if (completed) return;

        try {
            const response = await fetch(statusUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Cache-Control': 'no-cache'
                },
                cache: 'no-store'
            });

            if (!response.ok) return;

            const data = await response.json();

            if (data?.completed) {
                showCompletedScreen(
                    data.message || 'Formulario completado correctamente',
                    data.redirect_url || redirectUrl
                );
            }
        } catch (error) {
            console.warn('No se pudo consultar el estado del formulario:', error);
        }
    }

    paintCountdown();
    await checkFormCompletion();

    countdownInterval = setInterval(async () => {
        if (completed) return;

        remaining = Math.max(0, remaining - 1);
        paintCountdown();

        if (remaining <= 0) {
            clearInterval(countdownInterval);

            await checkFormCompletion();

            if (completed) return;

            document.body.style.opacity = '0.68';
            document.body.style.transition = 'opacity 0.25s ease';

            setTimeout(() => {
                window.location.reload();
            }, 260);
        }
    }, 1000);

    pollingInterval = setInterval(checkFormCompletion, 2500);

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            checkFormCompletion();
        }
    });

    window.addEventListener('focus', () => {
        checkFormCompletion();
    });
});
</script>
@endsection