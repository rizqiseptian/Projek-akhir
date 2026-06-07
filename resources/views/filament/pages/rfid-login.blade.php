<x-filament-panels::page>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        .rfid-login-container {
            font-family: 'Inter', sans-serif;
            background: #0a0a0f;
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
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
        .rfid-card {
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

        /* Hidden input */
        #scanner-field { position: absolute; opacity: 0; pointer-events: none; width: 1px; height: 1px; }
    </style>

    <div x-data="twoFactorLogin" x-on:destroyed="stopCamera" class="rfid-login-container">
        <div class="bg-orbs">
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>
        </div>

        <div class="rfid-card">
            <!-- Logo -->
            <div class="logo-ring">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
            </div>

            <h1>Cashier Login</h1>
            <p class="subtitle">RFID & Face Verification System</p>

            <!-- Camera Feed -->
            <div class="camera-box">
                <div class="scan-corner corner-tl"></div>
                <div class="scan-corner corner-tr"></div>
                <div class="scan-corner corner-bl"></div>
                <div class="scan-corner corner-br"></div>

                <video x-ref="video" autoplay muted playsinline style="display:none;"></video>
                <canvas x-ref="canvas" style="display:none;"></canvas>

                <div class="camera-placeholder" x-show="!isCameraActive">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/>
                    </svg>
                    <span x-text="cameraStatusText">Loading AI Models...</span>
                </div>

                <div class="scanning-overlay" x-show="isVerifying">
                    <div class="spinner"></div>
                    <span>Verifying Face...</span>
                </div>
            </div>

            <!-- Status -->
            <div class="status-area" x-show="!isVerifying">
                <div class="status-icon" x-show="!scanRequested">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#7c3aed" class="animate-pulse">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <div class="status-icon" x-show="scanRequested">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#7c3aed" class="animate-bounce">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                    </svg>
                </div>
                <div class="status-title" x-text="scanRequested ? 'FACE VERIFICATION' : 'SYSTEM LOCKED'"></div>
                <div class="status-desc" x-text="scanRequested ? 'Please look at the camera to verify your identity.' : 'Please tap your ID card on the reader to unlock the system.'"></div>
            </div>

            <input 
                type="password" 
                wire:model.live="rfid_input" 
                id="scanner-field"
                class="absolute opacity-0 pointer-events-none" 
                autofocus 
                autocomplete="off"
                x-bind:disabled="scanRequested || isVerifying"
            />
        </div>
    </div>

    @pushonce('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('twoFactorLogin', () => ({
                isCameraActive: false,
                isVerifying: false,
                scanRequested: false,
                cameraStatusText: 'Loading AI Models...',
                modelsLoaded: false,
                stream: null,
                intervalId: null,

                async init() {
                    // Start forcing focus on RFID input
                    this.setupScannerFocus();
                    
                    // Listen for Livewire events
                    window.addEventListener('request-face-scan', () => this.handleScanRequest());
                    window.addEventListener('resume-rfid-scan', () => this.resetScanState());

                    // Load face-api.js
                    if (typeof faceapi === 'undefined') {
                        try {
                            await this.loadScript('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.min.js');
                        } catch (e) {
                            this.cameraStatusText = 'Failed to load face-api.js library.';
                            return;
                        }
                    }
                    
                    try {
                        const modelUrl = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights';
                        await faceapi.nets.ssdMobilenetv1.loadFromUri(modelUrl);
                        await faceapi.nets.faceLandmark68Net.loadFromUri(modelUrl);
                        await faceapi.nets.faceRecognitionNet.loadFromUri(modelUrl);
                        this.modelsLoaded = true;
                        this.cameraStatusText = 'Camera is ready. Please allow camera access.';
                        
                        // Automatically start camera
                        await this.startCamera();
                    } catch (error) {
                        this.cameraStatusText = 'Failed to load face recognition models.';
                        console.error(error);
                    }
                },

                setupScannerFocus() {
                    const input = document.getElementById('scanner-field');
                    if (!input) return;
                    
                    document.addEventListener('click', () => {
                        if (!this.scanRequested && !this.isVerifying) input.focus();
                    });

                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') e.preventDefault();
                    });

                    setInterval(() => {
                        if (document.activeElement !== input && !this.scanRequested && !this.isVerifying) {
                            input.focus();
                        }
                    }, 500);
                },

                async loadScript(src) {
                    return new Promise((resolve, reject) => {
                        const script = document.createElement('script');
                        script.src = src;
                        script.onload = resolve;
                        script.onerror = reject;
                        document.head.appendChild(script);
                    });
                },

                async startCamera() {
                    if (!this.modelsLoaded) return;

                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({ 
                            video: { facingMode: "user" } 
                        });
                        this.$refs.video.srcObject = this.stream;
                        this.$refs.video.style.display = '';
                        this.$refs.canvas.style.display = '';
                        this.isCameraActive = true;
                        
                        this.$refs.video.onloadedmetadata = () => {
                            const canvas = this.$refs.canvas;
                            const displaySize = { width: this.$refs.video.videoWidth, height: this.$refs.video.videoHeight };
                            faceapi.matchDimensions(canvas, displaySize);
                            
                            this.intervalId = setInterval(async () => {
                                if (!this.isCameraActive || this.isVerifying) return;
                                
                                const detections = await faceapi.detectAllFaces(this.$refs.video)
                                    .withFaceLandmarks();
                                    
                                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                                faceapi.draw.drawDetections(canvas, resizedDetections);
                            }, 100);
                        };
                    } catch (error) {
                        this.cameraStatusText = 'Camera access denied.';
                        console.error(error);
                    }
                },

                async handleScanRequest() {
                    this.scanRequested = true;
                    this.isVerifying = true;
                    
                    if (!this.isCameraActive) {
                        @this.verifyFaceMatch('[]');
                        return;
                    }

                    // Give a slight delay to capture a good frame
                    setTimeout(async () => {
                        try {
                            const detection = await faceapi.detectSingleFace(this.$refs.video)
                                .withFaceLandmarks()
                                .withFaceDescriptor();

                            if (detection) {
                                const descriptorArray = Array.from(detection.descriptor);
                                @this.verifyFaceMatch(JSON.stringify(descriptorArray));
                            } else {
                                // No face detected
                                @this.verifyFaceMatch('[]');
                            }
                        } catch (error) {
                            console.error(error);
                            @this.verifyFaceMatch('[]');
                        }
                    }, 500);
                },

                resetScanState() {
                    this.scanRequested = false;
                    this.isVerifying = false;
                },

                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                    this.isCameraActive = false;
                    this.$refs.video.style.display = 'none';
                    this.$refs.canvas.style.display = 'none';
                    if (this.intervalId) {
                        clearInterval(this.intervalId);
                        this.intervalId = null;
                    }
                }
            }));
        });
    </script>
    @endpushonce
</x-filament-panels::page>