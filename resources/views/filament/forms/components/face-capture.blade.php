<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <style>
        .face-capture-container {
            font-family: 'Inter', sans-serif;
            position: relative;
        }

        /* Camera box */
        .camera-box {
            position: relative;
            width: 100%;
            max-width: 320px;
            height: 200px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            overflow: hidden;
            margin: 0 auto 16px;
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

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #7c3aed, #4f46e5);
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            letter-spacing: 0.02em;
            transition: all 0.2s;
            font-family: inherit;
        }
        .btn-primary:hover:not(:disabled) {
            background: linear-gradient(135deg, #6d28d9, #4338ca);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(124,58,237,0.4);
        }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }

        .btn-success {
            background: linear-gradient(135deg, #059669, #047857);
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            letter-spacing: 0.02em;
            transition: all 0.2s;
            font-family: inherit;
        }
        .btn-success:hover {
            background: linear-gradient(135deg, #047857, #065f46);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5,150,105,0.4);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            letter-spacing: 0.02em;
            transition: all 0.2s;
            font-family: inherit;
        }
        .btn-danger:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.5);
        }

        /* Error message */
        .error-msg {
            color: #f87171;
            font-size: 0.78rem;
            font-weight: 500;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.2);
            padding: 8px 12px;
            border-radius: 8px;
            margin-top: 12px;
            text-align: center;
        }
    </style>

    <div x-data="faceCaptureComponent({
            state: $wire.$entangle('{{ $getStatePath() }}')
        })"
        x-on:destroyed="destroy"
        class="face-capture-container flex flex-col items-center py-2"
    >
        <!-- Camera Feed -->
        <div class="camera-box">
            <div class="scan-corner corner-tl"></div>
            <div class="scan-corner corner-tr"></div>
            <div class="scan-corner corner-bl"></div>
            <div class="scan-corner corner-br"></div>

            <video x-ref="video" autoplay muted playsinline style="display:none;"></video>
            <canvas x-ref="canvas" style="display:none;"></canvas>

            <div class="camera-placeholder" x-show="!isCameraActive && !state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z"/>
                </svg>
                <span x-text="isLoading ? loadingText : 'Camera is off'"></span>
            </div>

            <div class="scanning-overlay" x-show="isLoading && isCameraActive">
                <div class="spinner"></div>
                <span x-text="loadingText"></span>
            </div>

            <div class="face-captured-overlay" x-show="state">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <span>Face Registered!</span>
            </div>
        </div>

        <div class="flex space-x-2 mt-3">
            <button type="button" class="btn-primary" x-on:click="startCamera" x-show="!isCameraActive && !state" x-bind:disabled="!modelsLoaded">
                <span x-text="modelsLoaded ? 'Start Camera' : 'Loading...'"></span>
            </button>
            <button type="button" class="btn-success" x-on:click="captureFace" x-show="isCameraActive">
                Capture Face
            </button>
            <button type="button" class="btn-danger" x-on:click="retakeFace" x-show="state">
                Retake Photo
            </button>
        </div>

        <!-- Error message -->
        <div x-show="errorMessage" class="error-msg" x-text="errorMessage"></div>
    </div>

    @pushonce('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('faceCaptureComponent', ({ state }) => ({
                state,
                isCameraActive: false,
                isLoading: true,
                loadingText: 'Loading AI Models...',
                errorMessage: '',
                modelsLoaded: false,
                stream: null,
                intervalId: null,

                async init() {
                    this.isLoading = true;
                    // Load face-api.js if not already loaded
                    if (typeof faceapi === 'undefined') {
                        try {
                            await this.loadScript('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.12/dist/face-api.min.js');
                        } catch (e) {
                            this.errorMessage = 'Failed to load face-api.js library.';
                            this.isLoading = false;
                            return;
                        }
                    }
                    
                    try {
                        // Load models from CDN to ensure they are always available
                        const modelUrl = 'https://cdn.jsdelivr.net/gh/justadudewhohacks/face-api.js@master/weights';
                        await faceapi.nets.ssdMobilenetv1.loadFromUri(modelUrl);
                        await faceapi.nets.faceLandmark68Net.loadFromUri(modelUrl);
                        await faceapi.nets.faceRecognitionNet.loadFromUri(modelUrl);
                        this.modelsLoaded = true;
                        this.isLoading = false;
                    } catch (error) {
                        this.errorMessage = 'Failed to load face recognition models.';
                        this.isLoading = false;
                        console.error(error);
                    }
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
                    this.errorMessage = '';
                    if (!this.modelsLoaded) {
                        this.errorMessage = 'Models not loaded yet.';
                        return;
                    }

                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({ 
                            video: { facingMode: "user" } 
                        });
                        this.$refs.video.srcObject = this.stream;
                        this.$refs.video.style.display = '';
                        this.$refs.canvas.style.display = '';
                        this.isCameraActive = true;
                        
                        // Set up drawing on canvas
                        this.$refs.video.onloadedmetadata = () => {
                            const canvas = this.$refs.canvas;
                            const displaySize = { width: this.$refs.video.videoWidth, height: this.$refs.video.videoHeight };
                            faceapi.matchDimensions(canvas, displaySize);
                            
                            this.intervalId = setInterval(async () => {
                                if (!this.isCameraActive) return;
                                
                                const detections = await faceapi.detectAllFaces(this.$refs.video)
                                    .withFaceLandmarks();
                                    
                                const resizedDetections = faceapi.resizeResults(detections, displaySize);
                                canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                                faceapi.draw.drawDetections(canvas, resizedDetections);
                                faceapi.draw.drawFaceLandmarks(canvas, resizedDetections);
                            }, 100);
                        };
                    } catch (error) {
                        this.errorMessage = 'Could not access the camera. Please ensure permissions are granted.';
                        console.error(error);
                    }
                },

                async captureFace() {
                    this.isLoading = true;
                    this.loadingText = 'Processing Face...';
                    this.errorMessage = '';

                    try {
                        const detection = await faceapi.detectSingleFace(this.$refs.video)
                            .withFaceLandmarks()
                            .withFaceDescriptor();

                        if (detection) {
                            // Extract descriptor (Float32Array) and convert to JSON array string
                            const descriptorArray = Array.from(detection.descriptor);
                            this.state = JSON.stringify(descriptorArray);
                            this.stopCamera();
                        } else {
                            this.errorMessage = 'No face detected. Please ensure your face is clearly visible and try again.';
                        }
                    } catch (error) {
                        this.errorMessage = 'Error capturing face. ' + error.message;
                        console.error(error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                retakeFace() {
                    this.state = null;
                    this.startCamera();
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
                    // Clear canvas
                    if (this.$refs.canvas) {
                        const canvas = this.$refs.canvas;
                        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                    }
                },
                
                destroy() {
                    this.stopCamera();
                }
            }));
        });
    </script>
    @endpushonce
</x-dynamic-component>
