<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Setup &mdash; {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0a0a0f;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Animated background */
        .bg-orbs {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.2;
            animation: float 8s ease-in-out infinite;
        }
        .orb-1 { width: 600px; height: 600px; background: #7c3aed; top: -200px; left: -200px; animation-delay: 0s; }
        .orb-2 { width: 450px; height: 450px; background: #4f46e5; bottom: -150px; right: -150px; animation-delay: 3s; }
        .orb-3 { width: 350px; height: 350px; background: #06b6d4; top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: 6s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }
        .orb-3 {
            animation: float3 8s ease-in-out infinite;
            animation-delay: 6s;
        }
        @keyframes float3 {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-50%, -55%) scale(1.05); }
        }

        /* Card */
        .card {
            position: relative;
            z-index: 10;
            background: rgba(15, 15, 25, 0.88);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(124, 58, 237, 0.2);
            border-radius: 28px;
            padding: 40px 36px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(124,58,237,0.1);
        }

        /* Setup badge */
        .setup-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(124, 58, 237, 0.15);
            border: 1px solid rgba(124, 58, 237, 0.35);
            color: #a78bfa;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 20px;
            margin: 0 auto 16px;
            width: fit-content;
        }
        .setup-badge svg { width: 12px; height: 12px; }

        /* Header */
        .logo-ring {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 0 30px rgba(124,58,237,0.5);
        }
        .logo-ring svg { width: 32px; height: 32px; color: white; }
        h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #fff;
            text-align: center;
            letter-spacing: -0.02em;
        }
        .subtitle {
            text-align: center;
            color: rgba(255,255,255,0.4);
            font-size: 0.82rem;
            margin-top: 6px;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        /* Steps */
        .steps {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            margin-bottom: 28px;
        }
        .step {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.72rem;
            font-weight: 600;
            color: rgba(255,255,255,0.25);
            letter-spacing: 0.03em;
        }
        .step.active { color: #a78bfa; }
        .step.done { color: #34d399; }
        .step-dot {
            width: 24px; height: 24px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .step.active .step-dot {
            background: rgba(124,58,237,0.25);
            border-color: rgba(124,58,237,0.6);
            color: #a78bfa;
        }
        .step.done .step-dot {
            background: rgba(52,211,153,0.15);
            border-color: rgba(52,211,153,0.4);
            color: #34d399;
        }
        .step-line {
            width: 28px;
            height: 1px;
            background: rgba(255,255,255,0.1);
            margin: 0 4px;
        }

        /* Step panels */
        .step-panel { display: none; }
        .step-panel.active { display: block; }

        /* Name input */
        .name-field {
            margin-bottom: 18px;
        }
        .name-field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: rgba(255,255,255,0.5);
            margin-bottom: 8px;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }
        .name-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            padding: 13px 14px;
            border-radius: 10px;
            outline: none;
            font-size: 1rem;
            font-family: inherit;
            transition: border-color 0.2s, background 0.2s;
        }
        .name-input:focus {
            border-color: rgba(124,58,237,0.6);
            background: rgba(124,58,237,0.05);
        }
        .name-input::placeholder { color: rgba(255,255,255,0.2); }

        /* RFID scan area */
        .rfid-scan-area {
            background: rgba(124,58,237,0.05);
            border: 1px dashed rgba(124,58,237,0.3);
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            margin-bottom: 18px;
            transition: all 0.3s;
        }
        .rfid-scan-area.scanned {
            border-style: solid;
            border-color: rgba(52,211,153,0.4);
            background: rgba(52,211,153,0.05);
        }
        .rfid-scan-area svg { width: 32px; height: 32px; margin: 0 auto 10px; display: block; opacity: 0.5; }
        .rfid-scan-area.scanned svg { opacity: 1; color: #34d399; }
        .rfid-scan-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: rgba(255,255,255,0.6);
        }
        .rfid-scan-area.scanned .rfid-scan-title { color: #34d399; }
        .rfid-uid-display {
            margin-top: 6px;
            font-size: 0.75rem;
            font-family: monospace;
            color: rgba(255,255,255,0.35);
            letter-spacing: 0.05em;
        }
        .rfid-scan-area.scanned .rfid-uid-display { color: rgba(52,211,153,0.7); }

        /* Camera box */
        .camera-box {
            position: relative;
            width: 100%;
            height: 200px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 16px;
        }
        .camera-box video, .camera-box canvas {
            position: absolute; inset: 0;
            width: 100%; height: 100%;
            object-fit: cover;
        }
        .camera-box canvas { pointer-events: none; }
        .camera-placeholder {
            position: absolute; inset: 0;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 10px;
            color: rgba(255,255,255,0.3);
        }
        .camera-placeholder svg { width: 36px; height: 36px; }
        .camera-placeholder span { font-size: 0.78rem; }

        .scanning-overlay {
            position: absolute; inset: 0;
            background: rgba(0,0,0,0.75);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 12px;
            color: #fff;
            font-size: 0.9rem;
            z-index: 10;
        }
        .spinner {
            width: 36px; height: 36px;
            border: 3px solid rgba(124,58,237,0.3);
            border-top-color: #7c3aed;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .face-captured-overlay {
            position: absolute; inset: 0;
            background: rgba(6, 20, 18, 0.8);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 8px;
            color: #34d399;
        }
        .face-captured-overlay svg { width: 40px; height: 40px; }
        .face-captured-overlay span { font-size: 0.88rem; font-weight: 600; }

        /* Corner scan lines */
        .scan-corner {
            position: absolute;
            width: 22px; height: 22px;
            border-color: #7c3aed;
            border-style: solid;
            opacity: 0.6;
        }
        .corner-tl { top: 8px; left: 8px; border-width: 2px 0 0 2px; border-radius: 3px 0 0 0; }
        .corner-tr { top: 8px; right: 8px; border-width: 2px 2px 0 0; border-radius: 0 3px 0 0; }
        .corner-bl { bottom: 8px; left: 8px; border-width: 0 0 2px 2px; border-radius: 0 0 0 3px; }
        .corner-br { bottom: 8px; right: 8px; border-width: 0 2px 2px 0; border-radius: 0 0 3px 0; }

        /* Toast */
        .toast-container {
            position: fixed;
            top: 24px; right: 24px;
            z-index: 9999;
            display: flex; flex-direction: column; gap: 10px;
        }
        .toast {
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #fff;
            min-width: 260px;
            max-width: 360px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            animation: slideIn 0.3s ease;
            display: flex; align-items: flex-start; gap: 10px;
        }
        .toast-success { background: linear-gradient(135deg, #065f46, #047857); border: 1px solid #10b981; }
        .toast-error   { background: linear-gradient(135deg, #7f1d1d, #991b1b); border: 1px solid #ef4444; }
        .toast-info    { background: linear-gradient(135deg, #1e3a5f, #1d4ed8); border: 1px solid #3b82f6; }
        @keyframes slideIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }

        /* Buttons */
        .btn-primary {
            width: 100%;
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.92rem;
            cursor: pointer;
            letter-spacing: 0.02em;
            transition: all 0.2s;
            font-family: inherit;
            margin-top: 4px;
        }
        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, #6d28d9, #4338ca);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(124,58,237,0.4);
        }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

        .btn-secondary {
            width: 100%;
            background: rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.6);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.88rem;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s;
            margin-top: 10px;
        }
        .btn-secondary:hover {
            background: rgba(255,255,255,0.09);
            color: rgba(255,255,255,0.85);
        }

        /* Error message */
        .error-msg {
            color: #f87171;
            font-size: 0.78rem;
            font-weight: 500;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            padding: 10px 14px;
            border-radius: 8px;
            margin-top: 12px;
            display: none;
        }
        .error-msg.show { display: block; }

        /* Hidden RFID input */
        #rfid-scanner { position: absolute; opacity: 0; pointer-events: none; width: 1px; height: 1px; }

        /* Back link */
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.75rem;
            color: rgba(255,255,255,0.2);
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover { color: rgba(255,255,255,0.45); }
    </style>
</head>
<body>
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="card" id="setup-app">
        <!-- Badge -->
        <div class="setup-badge">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/></svg>
            First-Run Setup
        </div>

        <!-- Logo -->
        <div class="logo-ring">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
            </svg>
        </div>

        <h1>Administrator Setup</h1>
        <p class="subtitle">No administrator account exists yet.<br>Register the first admin to secure your system.</p>

        <!-- Step indicator -->
        <div class="steps" id="step-indicator">
            <div class="step active" id="step-label-1">
                <div class="step-dot">1</div>
                Name
            </div>
            <div class="step-line"></div>
            <div class="step" id="step-label-2">
                <div class="step-dot">2</div>
                RFID Card
            </div>
            <div class="step-line"></div>
            <div class="step" id="step-label-3">
                <div class="step-dot">3</div>
                Face Scan
            </div>
        </div>

        <!-- Step 1: Name -->
        <div class="step-panel active" id="panel-1">
            <div class="name-field">
                <label for="admin-name">Full Name</label>
                <input type="text" id="admin-name" class="name-input" placeholder="e.g. John Doe" autocomplete="off" />
            </div>
            <div class="name-field">
                <label for="admin-whatsapp">Administrator WhatsApp</label>
                <input type="tel" id="admin-whatsapp" class="name-input" placeholder="+6281234567890" autocomplete="off" />
                <p class="subtitle" style="margin-top:8px;color:rgba(255,255,255,0.45);font-size:0.75rem;line-height:1.4;">
                    Required for emergency OTP delivery via the WhatsApp bot.
                </p>
            </div>
            <button class="btn-primary" id="btn-next-1" onclick="goToStep2()">
                Continue to RFID Scan &rarr;
            </button>
            <div class="error-msg" id="error-1">Please enter your full name and WhatsApp number.</div>
        </div>

        <!-- Step 2: RFID Scan -->
        <div class="step-panel" id="panel-2">
            <div class="rfid-scan-area" id="rfid-area">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" id="rfid-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5-3.9 19.5m-2.1-19.5-3.9 19.5"/>
                </svg>
                <div class="rfid-scan-title" id="rfid-title">Tap your RFID card on the reader</div>
                <div class="rfid-uid-display" id="rfid-uid-display"></div>
            </div>
            <input type="password" id="rfid-scanner" autocomplete="off" />
            <div class="error-msg" id="error-2">Please scan your RFID card first.</div>
            <button class="btn-secondary" onclick="goToStep1()">&larr; Back</button>
        </div>

        <!-- Step 3: Face Capture -->
        <div class="step-panel" id="panel-3">
            <div class="camera-box">
                <div class="scan-corner corner-tl"></div>
                <div class="scan-corner corner-tr"></div>
                <div class="scan-corner corner-bl"></div>
                <div class="scan-corner corner-br"></div>

                <video id="setup-video" autoplay muted playsinline style="display:none;"></video>
                <canvas id="setup-canvas" style="display:none;"></canvas>

                <div class="camera-placeholder" id="setup-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/>
                    </svg>
                    <span id="setup-camera-status">Loading AI Models...</span>
                </div>

                <div class="scanning-overlay" id="setup-scanning" style="display:none;">
                    <div class="spinner"></div>
                    <span>Capturing face...</span>
                </div>

                <div class="face-captured-overlay" id="setup-face-done" style="display:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <span>Face Registered!</span>
                </div>
            </div>

            <div class="error-msg" id="error-3"></div>

            <button class="btn-primary" id="btn-capture" onclick="captureFace()" disabled>
                Capture Face
            </button>
            <button class="btn-primary" id="btn-register" onclick="registerAdmin()" style="display:none;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;display:inline-block;vertical-align:middle;margin-right:6px;margin-top:-2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                Complete Registration &amp; Enter Admin
            </button>
            <button class="btn-secondary" onclick="retakeFace()" id="btn-retake" style="display:none;">Retake Photo</button>
            <button class="btn-secondary" onclick="goToStep2()" id="btn-back-3">&larr; Back</button>
        </div>
    </div>

    <div class="toast-container" id="toast-container"></div>

    <script>
    (async function() {
        const REGISTER_URL = '{{ route('admin.register') }}';
        const CSRF         = document.querySelector('meta[name="csrf-token"]').content;

        let adminName       = '';
        let adminWhatsapp   = '';
        let rfidUid         = '';
        let rfidBuffer      = '';
        let faceDescriptor  = null;
        let modelsLoaded    = false;
        let cameraStream    = null;
        let detectionLoop   = null;
        let isCameraActive  = false;

        const video       = document.getElementById('setup-video');
        const canvas      = document.getElementById('setup-canvas');
        const placeholder = document.getElementById('setup-placeholder');
        const statusText  = document.getElementById('setup-camera-status');
        const scanning    = document.getElementById('setup-scanning');
        const faceDone    = document.getElementById('setup-face-done');
        const rfidInput   = document.getElementById('rfid-scanner');

        // ─── Toast ───────────────────────────────────────────────
        function showToast(message, type = 'info') {
            const icons = {
                success: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>`,
                error:   `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>`,
                info:    `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>`,
            };
            const el = document.createElement('div');
            el.className = `toast toast-${type}`;
            el.innerHTML = (icons[type] || '') + `<span>${message}</span>`;
            document.getElementById('toast-container').appendChild(el);
            setTimeout(() => el.remove(), 4500);
        }

        function showError(id, msg) {
            const el = document.getElementById(id);
            el.textContent = msg;
            el.classList.add('show');
            setTimeout(() => el.classList.remove('show'), 3500);
        }

        // ─── Step Navigation ─────────────────────────────────────
        function setStep(n) {
            [1, 2, 3].forEach(i => {
                document.getElementById(`panel-${i}`).classList.toggle('active', i === n);
                const lbl = document.getElementById(`step-label-${i}`);
                lbl.classList.remove('active', 'done');
                if (i < n) lbl.classList.add('done');
                else if (i === n) lbl.classList.add('active');
            });
        }

        window.goToStep1 = function() {
            stopCamera();
            setStep(1);
        };

        window.goToStep2 = function() {
            const name = document.getElementById('admin-name').value.trim();
            const whatsapp = document.getElementById('admin-whatsapp').value.trim();
            const whatsappPattern = /^\+?[0-9]{8,20}$/;

            if (!name) {
                showError('error-1', 'Please enter your full name.');
                return;
            }

            if (!whatsapp) {
                showError('error-1', 'Please enter your WhatsApp number.');
                return;
            }

            if (!whatsappPattern.test(whatsapp)) {
                showError('error-1', 'Please enter a valid WhatsApp number including country code.');
                return;
            }

            adminName = name;
            adminWhatsapp = whatsapp;
            setStep(2);
            // Give focus to the hidden RFID scanner input
            setTimeout(() => rfidInput.focus(), 150);
            // Keep focus on RFID input while on step 2
            if (!window._rfidFocusInterval) {
                window._rfidFocusInterval = setInterval(() => {
                    if (document.getElementById('panel-2').classList.contains('active')) {
                        rfidInput.focus();
                    }
                }, 500);
            }
        };

        function goToStep3() {
            setStep(3);
            loadModelsAndCamera();
        }

        // ─── RFID Scanner ────────────────────────────────────────
        rfidInput.addEventListener('keydown', (e) => {
            if (!document.getElementById('panel-2').classList.contains('active')) return;
            if (e.key === 'Enter') {
                e.preventDefault();
                const uid = rfidBuffer.trim();
                rfidBuffer = '';
                rfidInput.value = '';
                if (uid) onRfidScanned(uid);
            } else {
                rfidBuffer += e.key.length === 1 ? e.key : '';
            }
        });

        function onRfidScanned(uid) {
            rfidUid = uid;
            const area  = document.getElementById('rfid-area');
            const icon  = document.getElementById('rfid-icon');
            const title = document.getElementById('rfid-title');
            const disp  = document.getElementById('rfid-uid-display');

            area.classList.add('scanned');
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>`;
            title.textContent = 'RFID Card Registered!';
            disp.textContent  = 'UID: ' + uid;

            showToast('RFID card captured successfully.', 'success');

            // Auto-advance to step 3 after brief pause
            setTimeout(() => goToStep3(), 900);
        }

        // ─── Face-api.js & Camera ────────────────────────────────
        async function loadModelsAndCamera() {
            if (modelsLoaded) { startCamera(); return; }

            statusText.textContent = 'Loading AI Models...';
            try {
                if (typeof faceapi === 'undefined') {
                    await new Promise((resolve, reject) => {
                        const s = document.createElement('script');
                        s.src = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.min.js';
                        s.onload = resolve; s.onerror = reject;
                        document.head.appendChild(s);
                    });
                }
                statusText.textContent = 'Loading recognition models...';
                const modelUrl = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights';
                await faceapi.nets.ssdMobilenetv1.loadFromUri(modelUrl);
                await faceapi.nets.faceLandmark68Net.loadFromUri(modelUrl);
                await faceapi.nets.faceRecognitionNet.loadFromUri(modelUrl);
                modelsLoaded = true;
                await startCamera();
            } catch (err) {
                statusText.textContent = 'Failed to load face recognition models.';
                console.error(err);
            }
        }

        async function startCamera() {
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                video.srcObject = cameraStream;
                video.style.display = '';
                canvas.style.display = '';
                placeholder.style.display = 'none';
                isCameraActive = true;

                document.getElementById('btn-capture').disabled = false;

                video.onloadedmetadata = () => {
                    faceapi.matchDimensions(canvas, { width: video.videoWidth, height: video.videoHeight });
                    detectionLoop = setInterval(async () => {
                        if (!isCameraActive || faceDescriptor) return;
                        const dets = await faceapi.detectAllFaces(video).withFaceLandmarks();
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        const resized = faceapi.resizeResults(dets, { width: video.videoWidth, height: video.videoHeight });
                        faceapi.draw.drawDetections(canvas, resized);
                        faceapi.draw.drawFaceLandmarks(canvas, resized);
                    }, 120);
                };
            } catch (e) {
                statusText.textContent = 'Camera access denied. Please allow camera.';
                console.error(e);
            }
        }

        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(t => t.stop());
                cameraStream = null;
            }
            if (detectionLoop) { clearInterval(detectionLoop); detectionLoop = null; }
            isCameraActive = false;
            video.style.display = 'none';
            canvas.style.display = 'none';
            placeholder.style.display = '';
        }

        // ─── Face Capture ─────────────────────────────────────────
        window.captureFace = async function() {
            const errEl = document.getElementById('error-3');
            errEl.classList.remove('show');
            scanning.style.display = 'flex';
            document.getElementById('btn-capture').disabled = true;

            await new Promise(r => setTimeout(r, 400));
            try {
                const detection = await faceapi.detectSingleFace(video)
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                if (detection) {
                    faceDescriptor = Array.from(detection.descriptor);
                    scanning.style.display = 'none';
                    faceDone.style.display = 'flex';
                    stopCamera();

                    document.getElementById('btn-capture').style.display = 'none';
                    document.getElementById('btn-register').style.display = 'block';
                    document.getElementById('btn-retake').style.display = 'block';
                    document.getElementById('btn-back-3').style.display = 'none';
                    showToast('Face captured successfully!', 'success');
                } else {
                    scanning.style.display = 'none';
                    document.getElementById('btn-capture').disabled = false;
                    errEl.textContent = 'No face detected. Please ensure your face is clearly visible and well-lit.';
                    errEl.classList.add('show');
                }
            } catch (err) {
                scanning.style.display = 'none';
                document.getElementById('btn-capture').disabled = false;
                errEl.textContent = 'Error capturing face: ' + err.message;
                errEl.classList.add('show');
                console.error(err);
            }
        };

        window.retakeFace = async function() {
            faceDescriptor = null;
            faceDone.style.display = 'none';
            document.getElementById('btn-capture').style.display = 'block';
            document.getElementById('btn-register').style.display = 'none';
            document.getElementById('btn-retake').style.display = 'none';
            document.getElementById('btn-back-3').style.display = 'block';
            document.getElementById('error-3').classList.remove('show');
            await startCamera();
        };

        // ─── Final Registration ───────────────────────────────────
        window.registerAdmin = async function() {
            if (!adminName || !rfidUid || !faceDescriptor) {
                showToast('Please complete all steps first.', 'error');
                return;
            }

            const btn = document.getElementById('btn-register');
            btn.disabled = true;
            btn.textContent = 'Registering...';

            try {
                const res = await fetch(REGISTER_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({
                        name:             adminName,
                        whatsapp_number:  adminWhatsapp,
                        rfid_uid:         rfidUid,
                        face_descriptor:  JSON.stringify(faceDescriptor),
                    }),
                });
                const data = await res.json();

                if (data.success) {
                    showToast('Administrator registered! Entering panel...', 'success');
                    btn.textContent = 'Entering Admin Panel...';
                    setTimeout(() => { window.location.href = data.redirect || '/admin'; }, 1000);
                } else {
                    showToast(data.message || 'Registration failed.', 'error');
                    btn.disabled = false;
                    btn.textContent = 'Complete Registration & Enter Admin';
                }
            } catch (err) {
                showToast('Network error. Please try again.', 'error');
                btn.disabled = false;
                btn.textContent = 'Complete Registration & Enter Admin';
            }
        };

        // ─── Initial focus on admin-name ─────────────────────────
        document.getElementById('admin-name').focus();
        document.getElementById('admin-name').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); window.goToStep2(); }
        });
    })();
    </script>
</body>
</html>
