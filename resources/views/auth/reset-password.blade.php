@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 bg-purple-600 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-lock text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Reset Password</h2>
            <p class="text-gray-600">Masukkan password baru untuk akun Anda</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form id="resetPasswordForm" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                
                <!-- Email Display -->
                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-envelope mr-2 text-gray-500"></i>
                        Reset password untuk: <span class="font-medium text-gray-900">{{ $email }}</span>
                    </p>
                </div>
                
                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2 text-purple-600"></i>Password Baru
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            minlength="8"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Masukkan password baru"
                        >
                        <button 
                            type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePassword('password')"
                        >
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="password-icon"></i>
                        </button>
                    </div>
                    <div class="text-red-500 text-sm mt-1 hidden" id="password-error"></div>
                    
                    <!-- Password Strength Indicator -->
                    <div class="mt-2">
                        <div class="flex space-x-1">
                            <div class="h-1 flex-1 bg-gray-200 rounded" id="strength-1"></div>
                            <div class="h-1 flex-1 bg-gray-200 rounded" id="strength-2"></div>
                            <div class="h-1 flex-1 bg-gray-200 rounded" id="strength-3"></div>
                            <div class="h-1 flex-1 bg-gray-200 rounded" id="strength-4"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1" id="strength-text">Minimal 8 karakter</p>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-check-double mr-2 text-purple-600"></i>Konfirmasi Password
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required
                            minlength="8"
                            class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Ulangi password baru"
                        >
                        <button 
                            type="button" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                            onclick="togglePassword('password_confirmation')"
                        >
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="password_confirmation-icon"></i>
                        </button>
                    </div>
                    <div class="text-red-500 text-sm mt-1 hidden" id="password_confirmation-error"></div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center"
                >
                    <span id="submitText">
                        <i class="fas fa-save mr-2"></i>Reset Password
                    </span>
                    <span id="loadingText" class="hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...
                    </span>
                </button>
            </form>

            <!-- Back to Login -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-700 text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali ke Login
                </a>
            </div>
        </div>

        <!-- Security Tips -->
        <div class="bg-green-50 rounded-lg p-4">
            <div class="flex items-start">
                <i class="fas fa-shield-alt text-green-600 mt-1 mr-3"></i>
                <div class="text-sm text-green-800">
                    <p class="font-medium mb-1">Tips Keamanan:</p>
                    <ul class="list-disc list-inside space-y-1 text-green-700">
                        <li>Gunakan kombinasi huruf besar, kecil, angka, dan simbol</li>
                        <li>Minimal 8 karakter</li>
                        <li>Jangan gunakan informasi pribadi</li>
                        <li>Jangan bagikan password kepada siapapun</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password strength checker
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBars = [
        document.getElementById('strength-1'),
        document.getElementById('strength-2'),
        document.getElementById('strength-3'),
        document.getElementById('strength-4')
    ];
    const strengthText = document.getElementById('strength-text');
    
    // Reset bars
    strengthBars.forEach(bar => {
        bar.className = 'h-1 flex-1 bg-gray-200 rounded';
    });
    
    let strength = 0;
    let text = 'Sangat Lemah';
    
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    // Update bars and text
    if (strength >= 1) {
        strengthBars[0].classList.add('bg-red-500');
        text = 'Lemah';
    }
    if (strength >= 2) {
        strengthBars[1].classList.add('bg-yellow-500');
        text = 'Sedang';
    }
    if (strength >= 3) {
        strengthBars[2].classList.add('bg-blue-500');
        text = 'Kuat';
    }
    if (strength >= 4) {
        strengthBars[3].classList.add('bg-green-500');
        text = 'Sangat Kuat';
    }
    
    strengthText.textContent = text;
});

// Password confirmation checker
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmation = this.value;
    const errorDiv = document.getElementById('password_confirmation-error');
    
    if (confirmation && password !== confirmation) {
        this.classList.add('border-red-500');
        errorDiv.textContent = 'Password tidak cocok';
        errorDiv.classList.remove('hidden');
    } else {
        this.classList.remove('border-red-500');
        errorDiv.classList.add('hidden');
    }
});

// Form submission
document.getElementById('resetPasswordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingText = document.getElementById('loadingText');
    const passwordInput = document.getElementById('password');
    const confirmationInput = document.getElementById('password_confirmation');
    const passwordError = document.getElementById('password-error');
    const confirmationError = document.getElementById('password_confirmation-error');
    
    // Reset errors
    passwordError.classList.add('hidden');
    confirmationError.classList.add('hidden');
    passwordInput.classList.remove('border-red-500');
    confirmationInput.classList.remove('border-red-500');
    
    // Validate passwords match
    if (passwordInput.value !== confirmationInput.value) {
        confirmationInput.classList.add('border-red-500');
        confirmationError.textContent = 'Password tidak cocok';
        confirmationError.classList.remove('hidden');
        return;
    }
    
    // Show loading
    submitBtn.disabled = true;
    submitText.classList.add('hidden');
    loadingText.classList.remove('hidden');
    
    try {
        const formData = new FormData(this);
        
        const response = await fetch('{{ route("password.update") }}', {
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
                confirmButtonText: 'Login Sekarang'
            }).then(() => {
                // Redirect to login
                window.location.href = '{{ route("login") }}';
            });
        } else {
            // Show errors
            if (data.errors) {
                if (data.errors.password) {
                    passwordError.textContent = data.errors.password[0];
                    passwordError.classList.remove('hidden');
                    passwordInput.classList.add('border-red-500');
                }
                if (data.errors.password_confirmation) {
                    confirmationError.textContent = data.errors.password_confirmation[0];
                    confirmationError.classList.remove('hidden');
                    confirmationInput.classList.add('border-red-500');
                }
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