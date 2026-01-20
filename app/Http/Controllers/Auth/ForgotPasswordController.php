<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordResetToken;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordController extends Controller
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Show forgot password form
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP to user's WhatsApp
     */
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar dalam sistem',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find user
            $user = User::where('email', $request->email)->first();
            
            if (!$user->no_whatsapp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp tidak terdaftar untuk akun ini'
                ], 400);
            }

            // Clean expired tokens
            PasswordResetToken::cleanExpired();

            // Create new token
            $resetToken = PasswordResetToken::createToken($user->email, $user->no_whatsapp);

            // Send OTP via WhatsApp
            $whatsappResult = $this->whatsAppService->sendOTP(
                $user->no_whatsapp, 
                $resetToken->otp_code, 
                $user->name
            );

            if (!$whatsappResult['success']) {
                // Delete token if WhatsApp failed
                $resetToken->delete();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim OTP ke WhatsApp. Silakan coba lagi.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP telah dikirim ke WhatsApp Anda',
                'data' => [
                    'phone_masked' => $this->maskPhoneNumber($user->no_whatsapp),
                    'expires_in' => 15 // minutes
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyForm(Request $request)
    {
        $email = $request->get('email');
        
        if (!$email) {
            return redirect()->route('password.request')
                ->with('error', 'Session expired. Silakan ulangi proses reset password.');
        }

        return view('auth.verify-otp', compact('email'));
    }

    /**
     * Verify OTP and show reset password form
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
        ], [
            'email.required' => 'Email wajib diisi',
            'otp_code.required' => 'Kode OTP wajib diisi',
            'otp_code.size' => 'Kode OTP harus 6 digit',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find valid token
            $resetToken = PasswordResetToken::findValidToken($request->email, $request->otp_code);

            if (!$resetToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode OTP tidak valid atau sudah expired'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kode OTP valid',
                'data' => [
                    'token' => $resetToken->token
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Show reset password form
     */
    public function showResetForm(Request $request)
    {
        $token = $request->get('token');
        $email = $request->get('email');
        
        if (!$token || !$email) {
            return redirect()->route('password.request')
                ->with('error', 'Token tidak valid. Silakan ulangi proses reset password.');
        }

        // Verify token exists and is valid
        $resetToken = PasswordResetToken::where('token', $token)
            ->where('email', $email)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetToken) {
            return redirect()->route('password.request')
                ->with('error', 'Token expired atau tidak valid. Silakan ulangi proses reset password.');
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'token.required' => 'Token wajib diisi',
            'email.required' => 'Email wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find valid token
            $resetToken = PasswordResetToken::where('token', $request->token)
                ->where('email', $request->email)
                ->where('is_used', false)
                ->where('expires_at', '>', now())
                ->first();

            if (!$resetToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token expired atau tidak valid'
                ], 400);
            }

            // Find user
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->password)
            ]);

            // Mark token as used
            $resetToken->markAsUsed();

            // Send WhatsApp notification
            if ($user->no_whatsapp) {
                $this->whatsAppService->sendPasswordResetNotification(
                    $user->no_whatsapp, 
                    $user->name
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil direset. Silakan login dengan password baru Anda.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak valid'
            ], 422);
        }

        // Check if user can request new OTP (rate limiting)
        $lastToken = PasswordResetToken::where('email', $request->email)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastToken && $lastToken->created_at->diffInMinutes(now()) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan tunggu 1 menit sebelum meminta OTP baru'
            ], 429);
        }

        // Resend OTP (same as sendOTP)
        return $this->sendOTP($request);
    }

    /**
     * Mask phone number for privacy
     */
    private function maskPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) > 6) {
            return substr($phone, 0, 3) . str_repeat('*', strlen($phone) - 6) . substr($phone, -3);
        }
        
        return $phone;
    }
}
