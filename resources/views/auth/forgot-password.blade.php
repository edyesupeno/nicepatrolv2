@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-key text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Lupa Password?</h2>
            <p class="text-gray-600">Masukkan email Anda dan kami akan mengirimkan kode OTP ke WhatsApp</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form id="forgotPasswordForm" class="space-y-6">
                @csrf
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-blue-600"></i>Email
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Masukkan email Anda"
                    >
                    <div class="text-red-500 text-sm mt-1 hidden" id="email-error"></div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                >
                    <span id="submitText">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Kode OTP
                    </span>
                    <span id="loadingText" class="hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...
                    </span>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Login
                </a>
            </div>
        </div>

        <!-- Info -->
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mt-1 mr-3"></i>
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">Informasi:</p>
                    <ul class="list-disc list-inside space-y-1 text-blue-700">
                        <li>Kode OTP akan dikirim ke nomor WhatsApp yang terdaftar</li>
                        <li>Kode berlaku selama 15 menit</li>
                        <li>Pastikan nomor WhatsApp Anda aktif</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingText = document.getElementById('loadingText');
    const emailInput = document.getElementById('email');
    const emailError = document.getElementById('email-error');
    
    // Reset errors
    emailError.classList.add('hidden');
    emailInput.classList.remove('border-red-500');
    
    // Show loading
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    loadingText.classList.remove('hidden');
    
    try {
        const formData = new FormData(this);
        
        const response = await fetch('{{ route("password.send-otp") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                confirmButtonText: 'Lanjutkan'
            }).then(() => {
                // Redirect to OTP verification
                window.location.href = `{{ route("password.verify") }}?email=${encodeURIComponent(emailInput.value)}`;
            });
        } else {
            // Show errors
            if (data.errors && data.errors.email) {
                emailError.textContent = data.errors.email[0];
                emailError.classList.remove('hidden');
                emailInput.classList.add('border-red-500');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: data.message || 'Terjadi kesalahan. Silakan coba lagi.'
                });
            }
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
</script>
@endpush