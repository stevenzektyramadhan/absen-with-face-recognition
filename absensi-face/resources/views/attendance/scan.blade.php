@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto py-8 px-4" x-data="attendanceScan()">

    <h1 class="text-2xl font-bold text-gray-800 mb-2 text-center">📷 Scan Wajah Absensi</h1>
    <p class="text-sm text-gray-500 text-center mb-6">Arahkan wajah ke kamera. Sistem akan mendeteksi otomatis.</p>

    {{-- Webcam Feed --}}
    <div class="bg-gray-900 rounded-xl overflow-hidden shadow-lg relative">
        <video x-ref="video" autoplay playsinline class="w-full rounded-xl" style="max-height: 420px; object-fit: cover;"></video>

        {{-- Scanning overlay --}}
        <div x-show="scanning" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 rounded-xl">
            <div class="text-white text-center">
                <div class="animate-spin inline-block w-8 h-8 border-4 border-white border-t-transparent rounded-full mb-2"></div>
                <p class="text-sm font-semibold">Mendeteksi wajah...</p>
            </div>
        </div>
    </div>

    {{-- Status Messages --}}
    <div class="mt-4">
        {{-- Success --}}
        <div x-show="resultStatus === 'success'" x-transition
             class="bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded-lg text-center">
            <p class="font-bold text-lg" x-text="resultMessage"></p>
        </div>

        {{-- Info --}}
        <div x-show="resultStatus === 'info'" x-transition
             class="bg-blue-50 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg text-center">
            <p class="font-semibold" x-text="resultMessage"></p>
        </div>

        {{-- Error --}}
        <div x-show="resultStatus === 'error'" x-transition
             class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-center">
            <p class="font-semibold" x-text="resultMessage"></p>
        </div>
    </div>

    {{-- Auto-scan indicator --}}
    <div x-show="!finished" class="mt-4 text-center">
        <p class="text-xs text-gray-400">
            Scan otomatis setiap <span class="font-bold">3 detik</span> •
            Percobaan: <span x-text="attempts" class="font-bold"></span>
        </p>
    </div>

    {{-- Buttons --}}
    <div class="mt-6 flex justify-center gap-4">
        <a href="{{ route('dashboard') }}"
           class="px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg shadow hover:bg-gray-700 transition">
            ← Kembali ke Dashboard
        </a>

        <button x-show="finished" @click="reset()"
                class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow hover:bg-indigo-700 transition">
            🔁 Scan Ulang
        </button>
    </div>

    {{-- Hidden Canvas --}}
    <canvas x-ref="canvas" style="display:none;"></canvas>
</div>

<script>
function attendanceScan() {
    return {
        scanning: false,
        finished: false,
        resultStatus: '',
        resultMessage: '',
        attempts: 0,
        intervalId: null,
        stream: null,

        init() {
            this.startCamera();
        },

        startCamera() {
            navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: 640, height: 480 }
            })
            .then(stream => {
                this.stream = stream;
                this.$refs.video.srcObject = stream;
                // Start auto-scan after camera is ready
                this.$refs.video.onloadedmetadata = () => {
                    this.startAutoScan();
                };
            })
            .catch(err => {
                this.resultStatus = 'error';
                this.resultMessage = 'Gagal mengakses kamera: ' + err.message;
                this.finished = true;
            });
        },

        startAutoScan() {
            // First scan after 2 seconds (give camera time to focus)
            setTimeout(() => {
                this.captureAndSend();
            }, 2000);

            // Then every 3 seconds
            this.intervalId = setInterval(() => {
                if (!this.finished) {
                    this.captureAndSend();
                }
            }, 3000);
        },

        async captureAndSend() {
            if (this.scanning || this.finished) return;

            this.scanning = true;
            this.attempts++;

            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);

            const base64Image = canvas.toDataURL('image/jpeg', 0.8);

            try {
                const response = await fetch('{{ route("attendance.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ image: base64Image })
                });

                const data = await response.json();

                this.resultStatus = data.status;
                this.resultMessage = data.message;

                if (data.status === 'success' || data.status === 'info') {
                    this.finished = true;
                    this.stopAutoScan();

                    // Redirect to dashboard after 3 seconds
                    setTimeout(() => {
                        window.location.href = '{{ route("dashboard") }}';
                    }, 3000);
                }
            } catch (err) {
                this.resultStatus = 'error';
                this.resultMessage = 'Gagal mengirim data: ' + err.message;
            }

            this.scanning = false;
        },

        stopAutoScan() {
            if (this.intervalId) {
                clearInterval(this.intervalId);
                this.intervalId = null;
            }
        },

        reset() {
            this.finished = false;
            this.resultStatus = '';
            this.resultMessage = '';
            this.attempts = 0;
            this.startAutoScan();
        },

        destroy() {
            this.stopAutoScan();
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
            }
        }
    }
}
</script>
@endsection
