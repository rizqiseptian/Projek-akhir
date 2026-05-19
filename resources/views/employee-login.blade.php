<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Employee Login &mdash; {{ config('app.name') }}</title>
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
            opacity: 0.25;
            animation: float 8s ease-in-out infinite;
        }
        .orb-1 { width: 500px; height: 500px; background: #f59e0b; top: -150px; left: -150px; animation-delay: 0s; }
        .orb-2 { width: 400px; height: 400px; background: #8b5cf6; bottom: -120px; right: -120px; animation-delay: 3s; }
        .orb-3 { width: 300px; height: 300px; background: #06b6d4; top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: 6s; }

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
            background: rgba(15, 15, 25, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 28px;
            padding: 40px 36px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 32px 80px rgba(0,0,0,0.6), 0 0 0 1px rgba(245,158,11,0.08);
        }

        /* Header */
        .logo-ring {
            width: 64px; height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 0 30px rgba(245,158,11,0.4);
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
            font-size: 0.85rem;
            margin-top: 6px;
            margin-bottom: 28px;
        }

        /* Camera box */
        .camera-box {
            position: relative;
            width: 100%;
            height: 220px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
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
        .camera-placeholder svg { width: 40px; height: 40px; }
        .camera-placeholder span { font-size: 0.8rem; }

        /* Scanning overlay */
        .scanning-overlay {
            position: absolute; inset: 0;
            background: rgba(0,0,0,0.7);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 12px;
            color: #fff;
            font-size: 0.9rem;
            z-index: 10;
        }
        .spinner {
            width: 36px; height: 36px;
            border: 3px solid rgba(245,158,11,0.3);
            border-top-color: #f59e0b;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Corner scan lines */
        .scan-corner {
            position: absolute;
            width: 28px; height: 28px;
            border-color: #f59e0b;
            border-style: solid;
            opacity: 0.7;
        }
        .corner-tl { top: 10px; left: 10px; border-width: 2px 0 0 2px; border-radius: 4px 0 0 0; }
        .corner-tr { top: 10px; right: 10px; border-width: 2px 2px 0 0; border-radius: 0 4px 0 0; }
        .corner-bl { bottom: 10px; left: 10px; border-width: 0 0 2px 2px; border-radius: 0 0 0 4px; }
        .corner-br { bottom: 10px; right: 10px; border-width: 0 2px 2px 0; border-radius: 0 0 4px 0; }

        /* Status area */
        .status-area {
            text-align: center;
            margin-bottom: 20px;
        }
        .status-icon {
            width: 44px; height: 44px;
            margin: 0 auto 12px;
        }
        .status-icon svg { width: 44px; height: 44px; }
        .status-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.01em;
        }
        .status-desc {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.4);
            margin-top: 4px;
            line-height: 1.5;
        }

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

        /* Hidden input */
        #scanner-field { position: absolute; opacity: 0; pointer-events: none; width: 1px; height: 1px; }

        /* Link */
        .admin-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.78rem;
            color: rgba(255,255,255,0.25);
            text-decoration: none;
            transition: color 0.2s;
        }
        .admin-link:hover { color: rgba(255,255,255,0.5); }

        /* Pulse ring around lock icon */
        @keyframes pulseRing {
            0% { box-shadow: 0 0 0 0 rgba(245,158,11, 0.4); }
            70% { box-shadow: 0 0 0 14px rgba(245,158,11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245,158,11, 0); }
        }
        .pulse-icon svg {
            animation: iconPulse 2s ease-in-out infinite;
        }
        @keyframes iconPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.7; }
        }
        .bounce-icon svg {
            animation: iconBounce 1s ease-in-out infinite;
        }
        @keyframes iconBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        /* Emergency Bypass */
        .emergency-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #ef4444;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.25s ease;
            z-index: 50;
            display: flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 0 0 0 rgba(239,68,68,0.3);
            animation: emergencyPulse 3s ease-in-out infinite;
        }
        .emergency-btn:hover {
            background: rgba(239, 68, 68, 0.18);
            border-color: rgba(239, 68, 68, 0.7);
            box-shadow: 0 4px 20px rgba(239,68,68,0.25);
            transform: translateY(-1px);
        }
        @keyframes emergencyPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0.3); }
            50% { box-shadow: 0 0 0 6px rgba(239,68,68,0); }
        }
        .emergency-modal {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(12px);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s;
        }
        .emergency-modal.active {
            opacity: 1;
            pointer-events: auto;
        }
        .emergency-card {
            background: #0f0f18;
            border: 1px solid rgba(239,68,68,0.25);
            border-top: 3px solid #ef4444;
            padding: 32px 28px 28px;
            border-radius: 18px;
            width: 100%;
            max-width: 380px;
            text-align: center;
            box-shadow: 0 24px 60px rgba(0,0,0,0.6), 0 0 40px rgba(239,68,68,0.1);
        }
        .emergency-header-icon {
            width: 52px; height: 52px;
            background: rgba(239,68,68,0.12);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .emergency-header-icon svg { width: 26px; height: 26px; color: #ef4444; }
        .emergency-card h3 {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            margin-bottom: 4px;
        }
        .emergency-card p {
            color: rgba(255,255,255,0.4);
            font-size: 0.78rem;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .emergency-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            padding: 13px 14px;
            border-radius: 10px;
            margin-bottom: 14px;
            outline: none;
            font-size: 1rem;
            font-family: inherit;
            letter-spacing: 0.1em;
            text-align: center;
            transition: border-color 0.2s;
        }
        .emergency-input:focus {
            border-color: rgba(239,68,68,0.6);
            background: rgba(239,68,68,0.05);
        }
        .emergency-submit {
            width: 100%;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            margin-bottom: 14px;
            letter-spacing: 0.02em;
            transition: all 0.2s;
            font-family: inherit;
        }
        .emergency-submit:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(239,68,68,0.35);
        }
        .emergency-cancel {
            color: rgba(255,255,255,0.35);
            font-size: 0.8rem;
            cursor: pointer;
            transition: color 0.2s;
            font-family: inherit;
            background: none;
            border: none;
            display: block;
            width: 100%;
            text-align: center;
            padding: 4px;
        }
        .emergency-cancel:hover {
            color: rgba(255,255,255,0.7);
        }
    </style>
</head>
<body>
    <div class="bg-orbs">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="card" id="login-app">
        <!-- Logo -->
        <div class="logo-ring">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z" />
            </svg>
        </div>
        <h1>{{ config('app.name') }}</h1>
        <p class="subtitle">Employee Access System &bull; RFID &amp; Face Verification</p>

        <!-- Camera Feed -->
        <div class="camera-box">
            <div class="scan-corner corner-tl"></div>
            <div class="scan-corner corner-tr"></div>
            <div class="scan-corner corner-bl"></div>
            <div class="scan-corner corner-br"></div>

            <video id="camera-video" autoplay muted playsinline style="display:none;"></video>
            <canvas id="camera-canvas" style="display:none;"></canvas>

            <div class="camera-placeholder" id="camera-placeholder">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                </svg>
                <span id="camera-status-text">Loading AI Models...</span>
            </div>

            <!-- Verifying overlay -->
            <div class="scanning-overlay" id="scanning-overlay" style="display:none;">
                <div class="spinner"></div>
                <span>Verifying Face...</span>
            </div>
        </div>

        <!-- Status -->
        <div class="status-area">
            <div class="status-icon pulse-icon" id="icon-locked">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#f59e0b">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>
            <div class="status-icon bounce-icon" id="icon-face" style="display:none;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#f59e0b">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                </svg>
            </div>
            <div class="status-title" id="status-title">SYSTEM LOCKED</div>
            <div class="status-desc" id="status-desc">Tap your employee ID card on the reader to unlock.</div>
        </div>

        <!-- Hidden RFID input (focused always) -->
        <input type="password" id="scanner-field" autocomplete="off" />

        <a href="/admin/login" class="admin-link">Admin Panel Login &rarr;</a>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Emergency Button -->
    <button class="emergency-btn" id="emergency-btn" onclick="openEmergencyModal()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 16px; height: 16px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        Emergency Access
    </button>

    <!-- Emergency Modal -->
    <div class="emergency-modal" id="emergency-modal">
        <div class="emergency-card">
            <div class="emergency-header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3>Emergency Access</h3>
            <p>Enter the administrator PIN to bypass<br>RFID &amp; face verification.</p>
            <input type="password" id="emergency-pin" class="emergency-input" placeholder="Enter Emergency PIN" autocomplete="off" />
            <button class="emergency-submit" id="emergency-submit-btn" onclick="submitEmergency()">Unlock &amp; Enter Admin</button>
            <button class="emergency-cancel" onclick="closeEmergencyModal()">Cancel</button>
        </div>
    </div>

    <script>
    (async function() {
        const VERIFY_URL  = '{{ route('employee.verify') }}';
        const BYPASS_URL  = '{{ route('employee.emergencyBypass') }}';
        const CSRF        = document.querySelector('meta[name="csrf-token"]').content;

        // Elements
        const video       = document.getElementById('camera-video');
        const canvas      = document.getElementById('camera-canvas');
        const placeholder = document.getElementById('camera-placeholder');
        const statusText  = document.getElementById('camera-status-text');
        const overlay     = document.getElementById('scanning-overlay');
        const iconLocked  = document.getElementById('icon-locked');
        const iconFace    = document.getElementById('icon-face');
        const statusTitle = document.getElementById('status-title');
        const statusDesc  = document.getElementById('status-desc');
        const rfidInput   = document.getElementById('scanner-field');

        let modelsLoaded  = false;
        let isCameraActive = false;
        let isVerifying   = false;
        let scanRequested = false;
        let cameraStream  = null;
        let detectionLoop = null;
        let rfidBuffer    = '';

        // ───── Toast helper ─────
        function showToast(message, type = 'info') {
            const icons = {
                success: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>`,
                error:   `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>`,
                info:    `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" /></svg>`,
            };
            const el = document.createElement('div');
            el.className = `toast toast-${type}`;
            el.innerHTML = (icons[type] || '') + `<span>${message}</span>`;
            document.getElementById('toast-container').appendChild(el);
            setTimeout(() => el.remove(), 4000);
        }

        // ───── UI state helpers ─────
        window.openEmergencyModal = function() {
            document.getElementById('emergency-modal').classList.add('active');
            document.getElementById('emergency-pin').value = '';
            setTimeout(() => document.getElementById('emergency-pin').focus(), 150);
        };

        window.closeEmergencyModal = function() {
            document.getElementById('emergency-modal').classList.remove('active');
            rfidInput.focus();
        };

        // Allow pressing Enter inside the PIN field to submit
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('emergency-pin').addEventListener('keydown', (e) => {
                if (e.key === 'Enter') { e.preventDefault(); submitEmergency(); }
            });
        });

        window.submitEmergency = async function() {
            const pin = document.getElementById('emergency-pin').value.trim();
            const btn = document.getElementById('emergency-submit-btn');

            if (!pin) {
                showToast('Please enter the emergency PIN.', 'error');
                return;
            }

            btn.disabled = true;
            btn.textContent = 'Verifying...';

            try {
                const res = await fetch(BYPASS_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({ pin }),
                });
                const data = await res.json();

                if (data.success) {
                    showToast('Emergency Access Granted!', 'success');
                    btn.textContent = 'Redirecting...';
                    setTimeout(() => { window.location.href = data.redirect || '/admin'; }, 800);
                } else {
                    showToast(data.message || 'Access denied.', 'error');
                    btn.disabled = false;
                    btn.textContent = 'Unlock & Enter Admin';
                    document.getElementById('emergency-pin').value = '';
                    document.getElementById('emergency-pin').focus();
                }
            } catch (err) {
                showToast('Network error. Please try again.', 'error');
                btn.disabled = false;
                btn.textContent = 'Unlock & Enter Admin';
            }
        };

        function setLockedState() {
            scanRequested = false;
            isVerifying   = false;
            iconLocked.style.display = '';
            iconFace.style.display   = 'none';
            statusTitle.textContent  = 'SYSTEM LOCKED';
            statusDesc.textContent   = 'Tap your employee ID card on the reader to unlock.';
            overlay.style.display    = 'none';
            rfidBuffer = '';
            rfidInput.value = '';
        }

        function setFaceState() {
            scanRequested = true;
            iconLocked.style.display = 'none';
            iconFace.style.display   = '';
            statusTitle.textContent  = 'FACE VERIFICATION';
            statusDesc.textContent   = 'RFID accepted. Please look at the camera to verify your identity.';
        }

        function setVerifyingState() {
            isVerifying = true;
            overlay.style.display = 'flex';
        }

        // ───── Camera ─────
        async function startCamera() {
            if (!modelsLoaded) return;
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                video.srcObject = cameraStream;
                video.style.display = '';
                canvas.style.display = '';
                placeholder.style.display = 'none';
                isCameraActive = true;

                video.onloadedmetadata = () => {
                    faceapi.matchDimensions(canvas, { width: video.videoWidth, height: video.videoHeight });
                    detectionLoop = setInterval(async () => {
                        if (!isCameraActive || isVerifying) return;
                        const dets = await faceapi.detectAllFaces(video).withFaceLandmarks();
                        const ctx = canvas.getContext('2d');
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        const resized = faceapi.resizeResults(dets, { width: video.videoWidth, height: video.videoHeight });
                        faceapi.draw.drawDetections(canvas, resized);
                    }, 120);
                };
            } catch (e) {
                statusText.textContent = 'Camera access denied. Please allow camera.';
                console.error(e);
            }
        }

        // ───── Face capture & verify ─────
        async function captureFaceAndVerify(rfidUid) {
            setFaceState();
            setVerifyingState();

            let descriptorJson = '[]';

            if (isCameraActive) {
                await new Promise(r => setTimeout(r, 600));
                try {
                    const detection = await faceapi
                        .detectSingleFace(video)
                        .withFaceLandmarks()
                        .withFaceDescriptor();
                    if (detection) {
                        descriptorJson = JSON.stringify(Array.from(detection.descriptor));
                    }
                } catch (err) {
                    console.error(err);
                }
            }

            try {
                const res = await fetch(VERIFY_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                    },
                    body: JSON.stringify({ rfid_uid: rfidUid, face_descriptor: descriptorJson }),
                });
                const data = await res.json();

                if (data.success) {
                    showToast('Access Granted! Welcome.', 'success');
                    setTimeout(() => { window.location.href = data.redirect || '/admin'; }, 800);
                } else {
                    showToast(data.message || 'Access denied.', 'error');
                    setLockedState();
                }
            } catch (err) {
                showToast('Network error. Please try again.', 'error');
                setLockedState();
            }
        }

        // ───── RFID input focus loop ─────
        function maintainFocus() {
            setInterval(() => {
                const modalActive = document.getElementById('emergency-modal').classList.contains('active');
                if (!scanRequested && !isVerifying && !modalActive && document.activeElement !== rfidInput) {
                    rfidInput.focus();
                }
            }, 300);
        }

        document.addEventListener('click', () => {
            const modalActive = document.getElementById('emergency-modal').classList.contains('active');
            if (!scanRequested && !isVerifying && !modalActive) rfidInput.focus();
        });

        // Most RFID scanners act as keyboards and end with Enter
        rfidInput.addEventListener('keydown', async (e) => {
            const modalActive = document.getElementById('emergency-modal').classList.contains('active');
            if (modalActive) return; // ignore scanner input on main page if modal is open

            if (e.key === 'Enter') {
                e.preventDefault();
                const uid = rfidBuffer.trim();
                rfidBuffer = '';
                rfidInput.value = '';
                if (uid && !scanRequested && !isVerifying) {
                    await captureFaceAndVerify(uid);
                }
            } else {
                rfidBuffer += e.key === 'Backspace' ? '' : (e.key.length === 1 ? e.key : '');
            }
        });

        // ───── Boot: load face-api then camera ─────
        (async () => {
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
                statusText.textContent = 'Camera ready.';
                await startCamera();
            } catch (err) {
                statusText.textContent = 'Failed to load face recognition models.';
                console.error(err);
            }
            maintainFocus();
            rfidInput.focus();
        })();
    })();
    </script>
</body>
</html>
