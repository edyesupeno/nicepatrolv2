@extends('layouts.auth')

@section('title', 'Verifikasi OTP')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-green-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-mobile-alt text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Verifikasi OTP</h2>
            <p class="text-gray-600">Masukkan kode 6 digit yang telah dikirim ke WhatsApp Anda</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form id="verifyOtpForm" class="space-y-6">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                
                <!-- OTP Input -->
                <div>
                    <label for="otp_code" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2 text-green-600"></i>Kode OTP
                    </label>
                    <input 
                        type="text" 
                        id="otp_code" 
                        name="otp_code" 
                        required
                        maxlength="6"
                        pattern="[0-9]{6}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent text-center text-2xl font-mono tracking-widest"
                        placeholder="000000"
                        autocomplete="one-time-code"
                    >
                    <div class="text-red-500 text-sm mt-1 hidden" id="otp-error"></div>
                </div>

                <!-- Timer -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Kode akan expired dalam: <span id="timer" class="font-mono font-bold text-red-600">15:00</span>
                    </p>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                >
                    <span id="submitText">
                        <i class="fas fa-check mr-2"></i>Verifikasi OTP
                    </span>
                    <span id="loadingText" class="hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Memverifikasi...
                    </span>
                </button>
            </form>

            <!-- Resend OTP -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600 mb-2">Tidak menerima kode?</p>
                <button 
                    id="resendBtn" 
                    class="text-blue-600 hover:text-blue-700 text-sm font-medium disabled:text-gray-400 disabled:cursor-not-allowed"
                    disabled
                >
                    <i class="fas fa-redo mr-2"></i>Kirim Ulang OTP
                </button>
                <p id="resendTimer" class="text-xs text-gray-500 mt-1">Kirim ulang tersedia dalam <span id="resendCountdown">60</span> detik</p>
            </div>

            <!-- Back -->
            <div class="mt-4 text-center">
                <a href="{{ route('password.request') }}" class="text-gray-600 hover:text-gray-700 text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>

        <!-- Info -->
        <div class="bg-yellow-50 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-600 mt-1 mr-3"></i>
                <div class="text-sm text-yellow-800">
                    <p class="font-medium mb-1">Perhatian:</p>
                    <ul class="list-disc list-inside space-y-1 text-yellow-700">
                        <li>Periksa pesan WhatsApp Anda</li>
                        <li>Kode OTP terdiri dari 6 digit angka</li>
                        <li>Jangan bagikan kode ini kepada siapapun</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let timerInterval;
let resendInterval;
let timeLeft = 15 * 60; // 15 minutes in seconds
let resendTimeLeft = 60; // 60 seconds

// Start timer
function startTimer() {
    timerInterval = setInterval(() => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        document.getElementById('timer').textContent = 
            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            document.getElementById('timer').textContent = '00:00';
            document.getElementById('timer').classList.remove('text-red-600');
            document.getElementById('timer').classList.add('text-gray-400');
            
            Swal.fire({
                icon: 'warning',
                title: 'Kode Expired',
                text: 'Kode OTP telah expired. Silakan minta kode baru.',
                confirmButtonText: 'Minta Kode Baru'
            }).then(() => {
                window.location.href = '{{ route("password.request") }}';
            });
        }
        
        timeLeft--;
    }, 1000);
}

// Start resend timer
function startResendTimer() {
    const resendBtn = document.getElementById('resendBtn');
    const resendTimer = document.getElementById('resendTimer');
    const resendCountdown = document.getElementById('resendCountdown');
    
    resendBtn.disabled = true;
    resendTimer.classList.remove('hidden');
    
    resendInterval = setInterval(() => {
        resendCountdown.textContent = resendTimeLeft;
        
        if (resendTimeLeft <= 0) {
            clearInterval(resendInterval);
            resendBtn.disabled = false;
            resendTimer.classList.add('hidden');
            resendTimeLeft = 60; // Reset for next time
        }
        
        resendTimeLeft--;
    }, 1000);
}

// Initialize timers
startTimer();
startResendTimer();

// OTP input formatting
document.getElementById('otp_code').addEventListener('input', function(e) {
    // Only allow numbers
    this.value = this.value.replace(/[^0-9]/g, '');
    
    // Auto-submit when 6 digits entered
    if (this.value.length === 6) {
        document.getElementById('verifyOtpForm').dispatchEvent(new Event('submit'));
    }
});

// Form submission
document.getElementById('verifyOtpForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingText = document.getElementById('loadingText');
    const otpInput = document.getElementById('otp_code');
    const otpError = document.getElementById('otp-error');
    
    // Reset errors
    otpError.classList.add('hidden');
    otpInput.classList.remove('border-red-500');
    
    // Show loading
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    loadingText.classList.remove('hidden');
    
    try {
        const formData = new FormData(this);
        
        const response = await fetch('{{ route("password.verify-otp") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Clear timers
            clearInterval(timerInterval);
            clearInterval(resendInterval);
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                confirmButtonText: 'Lanjutkan'
            }).then(() => {
                // Redirect to reset password form
                window.location.href = `{{ route("password.reset") }}?token=${data.data.token}&email={{ $email }}`;
            });
        } else {
            // Show errors
            if (data.errors && data.errors.otp_code) {
                otpError.textContent = data.errors.otp_code[0];
                otpError.classList.remove('hidden');
                otpInput.classList.add('border-red-500');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'Kode OTP tidak valid'
                });
            }
            otpInput.select();
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan jaringan. Silakan coba lagi.'
        });
    } finally {
        // Hide loading
        submitBtn.disabled = false;
        submitText.classList.remove('hidden');
        loadingText.classList.add('hidden');
    }
});

// Resend OTP
document.getElementById('resendBtn').addEventListener('click', async function() {
    const resendBtn = this;
    
    resendBtn.disabled = true;
    resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
    
    try {
        const response = await fetch('{{ route("password.resend-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                email: '{{ $email }}'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            });
            
            // Reset timers
            timeLeft = 15 * 60;
            resendTimeLeft = 60;
            startResendTimer();
            
            // Clear OTP input
            document.getElementById('otp_code').value = '';
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message || 'Gagal mengirim ulang OTP'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan jaringan. Silakan coba lagi.'
        });
    } finally {
        resendBtn.innerHTML = '<i class="fas fa-redo mr-2"></i>Kirim Ulang OTP';
    }
});
</script>
@endpush