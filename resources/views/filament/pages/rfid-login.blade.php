<x-filament-panels::page>
    <div x-data="twoFactorLogin" x-on:destroyed="stopCamera" class="flex flex-col items-center justify-center min-h-[60vh] text-center">
        <div class="p-8 bg-white dark:bg-gray-900 shadow-2xl rounded-3xl border-4 border-primary-500 max-w-lg w-full">
            
            <!-- Webcam Container -->
            <div class="relative w-full h-64 bg-gray-100 dark:bg-gray-800 rounded-2xl overflow-hidden mb-6 flex justify-center items-center border border-gray-300 dark:border-gray-700">
                <video x-ref="video" autoplay muted playsinline class="w-full h-auto" x-show="isCameraActive"></video>
                <canvas x-ref="canvas" class="absolute top-0 left-0 w-full h-full pointer-events-none" x-show="isCameraActive"></canvas>
                
                <div x-show="!isCameraActive" class="absolute inset-0 flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-camera class="w-12 h-12 mb-2 opacity-50" />
                    <span x-text="cameraStatusText">Starting Camera...</span>
                </div>
                
                <div x-show="isVerifying" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 bg-opacity-75 text-white z-10">
                    <x-heroicon-o-arrow-path class="w-8 h-8 animate-spin mb-2" />
                    <span>Verifying Face...</span>
                </div>
            </div>

            <!-- Status Indicator -->
            <div x-show="!isVerifying">
                <x-heroicon-o-lock-closed class="w-16 h-16 text-primary-500 mx-auto mb-4 animate-pulse" x-show="!scanRequested" />
                <x-heroicon-o-face-smile class="w-16 h-16 text-primary-500 mx-auto mb-4 animate-bounce" x-show="scanRequested" style="display: none;" />
                
                <h1 class="text-3xl font-black tracking-tight text-gray-900 dark:text-white">
                    <span x-text="scanRequested ? 'FACE VERIFICATION' : 'CASHIER LOCKED'"></span>
                </h1>
                
                <p class="text-gray-500 dark:text-gray-400 mt-2 text-md">
                    <span x-text="scanRequested ? 'Please look at the camera to verify your identity.' : 'Please tap your ID card on the reader to unlock the system.'"></span>
                </p>
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