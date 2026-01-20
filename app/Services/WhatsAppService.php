<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.base_url');
        $this->token = config('services.whatsapp.token');
    }

    /**
     * Send WhatsApp message
     */
    public function sendMessage(string $phone, string $message): array
    {
        try {
            // Format phone number (remove + and ensure it starts with country code)
            $phone = $this->formatPhoneNumber($phone);

            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'messageType' => 'text',
                'to' => $phone,
                'body' => $message,
            ]);

            $responseData = $response->json();

            Log::info('WhatsApp API Response', [
                'phone' => $phone,
                'status' => $response->status(),
                'response' => $responseData
            ]);

            return [
                'success' => $response->successful(),
                'data' => $responseData,
                'status' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp API Error', [
                'phone' => $phone,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 500
            ];
        }
    }

    /**
     * Send OTP via WhatsApp
     */
    public function sendOTP(string $phone, string $otpCode, string $userName = 'User'): array
    {
        $message = $this->buildOTPMessage($otpCode, $userName);
        return $this->sendMessage($phone, $message);
    }

    /**
     * Send password reset notification
     */
    public function sendPasswordResetNotification(string $phone, string $userName = 'User'): array
    {
        $message = $this->buildPasswordResetMessage($userName);
        return $this->sendMessage($phone, $message);
    }

    /**
     * Format phone number for WhatsApp API
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 62 (Indonesia country code)
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // If doesn't start with country code, add 62
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        
        return $phone;
    }

    /**
     * Build OTP message template
     */
    private function buildOTPMessage(string $otpCode, string $userName): string
    {
        return "🔐 *Nice Patrol - Kode OTP*\n\n" .
               "Halo {$userName},\n\n" .
               "Kode OTP untuk reset password Anda adalah:\n\n" .
               "*{$otpCode}*\n\n" .
               "⏰ Kode ini berlaku selama 15 menit.\n" .
               "🚫 Jangan bagikan kode ini kepada siapapun.\n\n" .
               "Jika Anda tidak meminta reset password, abaikan pesan ini.\n\n" .
               "Terima kasih,\n" .
               "Tim Nice Patrol";
    }

    /**
     * Build password reset success message
     */
    private function buildPasswordResetMessage(string $userName): string
    {
        return "✅ *Nice Patrol - Password Berhasil Direset*\n\n" .
               "Halo {$userName},\n\n" .
               "Password Anda telah berhasil direset.\n" .
               "Silakan login dengan password baru Anda.\n\n" .
               "🔒 Untuk keamanan, pastikan:\n" .
               "• Gunakan password yang kuat\n" .
               "• Jangan bagikan password kepada siapapun\n" .
               "• Logout dari perangkat yang tidak dikenal\n\n" .
               "Terima kasih,\n" .
               "Tim Nice Patrol";
    }

    /**
     * Test WhatsApp connection
     */
    public function testConnection(): array
    {
        try {
            // Test with a simple message to check if API is accessible
            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($this->baseUrl, [
                'messageType' => 'text',
                'to' => '6281234567890', // dummy number for testing
                'body' => 'API Test'
            ]);

            return [
                'success' => $response->successful() || $response->status() === 400, // 400 might be expected for dummy number
                'status' => $response->status(),
                'data' => $response->json()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}