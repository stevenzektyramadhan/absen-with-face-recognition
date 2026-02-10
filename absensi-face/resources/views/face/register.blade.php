@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">📸 Registrasi Wajah</h1>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Webcam & Capture UI with Alpine.js --}}
    <div x-data="faceRegister()" class="space-y-6">

        {{-- Webcam Feed --}}
        <div class="bg-gray-900 rounded-xl overflow-hidden shadow-lg">
            <video x-ref="video" autoplay playsinline class="w-full rounded-xl" style="max-height: 400px; object-fit: cover;"></video>
        </div>

        {{-- Hidden Canvas --}}
        <canvas x-ref="canvas" style="display: none;"></canvas>

        {{-- Preview (shown after capture) --}}
        <template x-if="capturedImage">
            <div class="text-center">
                <p class="text-sm text-gray-500 mb-2">Hasil Tangkapan:</p>
                <img :src="capturedImage" class="mx-auto rounded-xl shadow-md border-2 border-indigo-300" style="max-height: 300px;">
            </div>
        </template>

        {{-- Buttons --}}
        <div class="flex justify-center gap-4">
            {{-- Capture Button --}}
            <button @click="capture()"
                    type="button"
                    class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg shadow hover:bg-indigo-700 transition">
                📷 Ambil Foto
            </button>

            {{-- Submit Button --}}
            <button @click="submit()"
                    type="button"
                    :disabled="!capturedImage || loading"
                    :class="capturedImage && !loading ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
                    class="px-6 py-3 text-white font-semibold rounded-lg shadow transition">
                <span x-show="!loading">💾 Simpan Wajah</span>
                <span x-show="loading">⏳ Menyimpan...</span>
            </button>
        </div>

        {{-- Status Message --}}
        <p x-show="statusMsg" x-text="statusMsg" class="text-center text-sm" :class="statusOk ? 'text-green-600' : 'text-red-600'"></p>
    </div>
</div>

<script>
function faceRegister() {
    return {
        capturedImage: null,
        loading: false,
        statusMsg: '',
        statusOk: false,

        init() {
            // Start webcam
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } })
                .then(stream => {
                    this.$refs.video.srcObject = stream;
                })
                .catch(err => {
                    this.statusMsg = 'Gagal mengakses kamera: ' + err.message;
                    this.statusOk = false;
                });
        },

        capture() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);

            this.capturedImage = canvas.toDataURL('image/jpeg', 0.9);
            this.statusMsg = 'Foto berhasil ditangkap! Klik "Simpan Wajah" untuk mendaftarkan.';
            this.statusOk = true;
        },

        async submit() {
            if (!this.capturedImage) return;

            this.loading = true;
            this.statusMsg = '';

            try {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("face.register.store") }}';

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                const imageInput = document.createElement('input');
                imageInput.type = 'hidden';
                imageInput.name = 'image';
                imageInput.value = this.capturedImage;
                form.appendChild(imageInput);

                document.body.appendChild(form);
                form.submit();
            } catch (err) {
                this.statusMsg = 'Gagal mengirim: ' + err.message;
                this.statusOk = false;
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
