<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="faceCaptureComponent({
            state: $wire.$entangle('{{ $getStatePath() }}')
        })"
        x-on:destroyed="destroy"
        class="flex flex-col items-center space-y-4 py-4"
    >
        <!-- Video element for webcam -->
        <div class="relative w-full max-w-md bg-gray-100 rounded-lg overflow-hidden border border-gray-300 dark:border-gray-700 dark:bg-gray-800 flex justify-center min-h-[250px]">
            <video x-ref="video" autoplay muted playsinline class="w-full h-auto" x-show="isCameraActive"></video>
            <canvas x-ref="canvas" class="absolute top-0 left-0 w-full h-full pointer-events-none" x-show="isCameraActive"></canvas>
            
            <div x-show="!isCameraActive && !state" class="absolute inset-0 flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                <x-heroicon-o-camera class="w-12 h-12 mb-2 opacity-50" />
                <span>Camera is off</span>
            </div>

            <div x-show="state" class="absolute inset-0 flex flex-col items-center justify-center bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400">
                <x-heroicon-o-check-badge class="w-16 h-16 mb-2" />
                <span class="font-bold">Face Descriptor Captured</span>
            </div>
            
            <div x-show="isLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 bg-opacity-75 text-white z-10">
                <x-heroicon-o-arrow-path class="w-8 h-8 animate-spin mb-2" />
                <span x-text="loadingText"></span>
            </div>
        </div>

        <div class="flex space-x-4">
            <x-filament::button type="button" color="primary" x-on:click="startCamera" x-show="!isCameraActive && !state" x-bind:disabled="!modelsLoaded">
                <span x-text="modelsLoaded ? 'Start Camera' : 'Loading Models...'"></span>
            </x-filament::button>
            <x-filament::button type="button" color="success" x-on:click="captureFace" x-show="isCameraActive">
                Capture Face
            </x-filament::button>
            <x-filament::button type="button" color="danger" x-on:click="retakeFace" x-show="state">
                Retake
            </x-filament::button>
        </div>
        
        <!-- Error message -->
        <div x-show="errorMessage" class="text-red-600 dark:text-red-400 font-medium flex items-center mt-2 text-sm bg-red-50 dark:bg-red-900/30 p-2 rounded-md" x-text="errorMessage">
        </div>
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
