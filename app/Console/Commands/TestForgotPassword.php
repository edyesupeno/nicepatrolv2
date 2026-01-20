<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\PasswordResetToken;
use App\Services\WhatsAppService;

class TestForgotPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:forgot-password {email=edy@gmail.com}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test forgot password functionality with WhatsApp OTP';

    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        parent::__construct();
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing Forgot Password functionality for: {$email}");
        $this->newLine();

        // 1. Check if user exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $this->info("✓ User found: {$user->name}");
        
        if (!$user->no_whatsapp) {
            $this->error("✗ User doesn't have WhatsApp number!");
            return 1;
        }
        
        $this->info("✓ WhatsApp number: {$user->no_whatsapp}");
        $this->newLine();

        // 2. Test WhatsApp service
        $this->info("Testing WhatsApp service...");
        $testResult = $this->whatsAppService->testConnection();
        
        if ($testResult['success']) {
            $this->info("✓ WhatsApp API connection successful");
        } else {
            $this->error("✗ WhatsApp API connection failed: " . ($testResult['error'] ?? 'Unknown error'));
            return 1;
        }
        $this->newLine();

        // 3. Create password reset token
        $this->info("Creating password reset token...");
        
        // Clean expired tokens
        PasswordResetToken::cleanExpired();
        
        // Create new token
        $resetToken = PasswordResetToken::createToken($user->email, $user->no_whatsapp);
        
        $this->info("✓ Token created:");
        $this->line("  - Token: {$resetToken->token}");
        $this->line("  - OTP Code: {$resetToken->otp_code}");
        $this->line("  - Expires at: {$resetToken->expires_at}");
        $this->newLine();

        // 4. Send OTP via WhatsApp
        $this->info("Sending OTP via WhatsApp...");
        
        $whatsappResult = $this->whatsAppService->sendOTP(
            $user->no_whatsapp, 
            $resetToken->otp_code, 
            $user->name
        );

        if ($whatsappResult['success']) {
            $this->info("✓ OTP sent successfully to WhatsApp!");
            $this->line("  Check your WhatsApp for the OTP code: {$resetToken->otp_code}");
        } else {
            $this->error("✗ Failed to send OTP: " . ($whatsappResult['error'] ?? 'Unknown error'));
            if (isset($whatsappResult['data'])) {
                $this->line("Response: " . json_encode($whatsappResult['data'], JSON_PRETTY_PRINT));
            }
            return 1;
        }
        $this->newLine();

        // 5. Test token validation
        $this->info("Testing token validation...");
        
        $validToken = PasswordResetToken::findValidToken($user->email, $resetToken->otp_code);
        
        if ($validToken) {
            $this->info("✓ Token validation successful");
            $this->line("  - Token is valid and not expired");
            $this->line("  - Token ID: {$validToken->id}");
        } else {
            $this->error("✗ Token validation failed");
            return 1;
        }
        $this->newLine();

        // 6. Show URLs for testing
        $this->info("URLs for manual testing:");
        $baseUrl = config('app.url');
        $dashboardDomain = config('app.url');
        
        if (str_contains($dashboardDomain, 'devapp')) {
            $dashboardDomain = str_replace('devapp', 'devdash', $dashboardDomain);
        }
        
        $this->line("1. Forgot Password: {$dashboardDomain}/forgot-password");
        $this->line("2. Verify OTP: {$dashboardDomain}/verify-otp?email=" . urlencode($email));
        $this->line("3. Reset Password: {$dashboardDomain}/reset-password?token={$resetToken->token}&email=" . urlencode($email));
        $this->newLine();

        // 7. Summary
        $this->info("🎉 All tests passed! Forgot Password feature is working correctly.");
        $this->newLine();
        
        $this->comment("Next steps:");
        $this->line("1. Open the forgot password URL in your browser");
        $this->line("2. Enter email: {$email}");
        $this->line("3. Check WhatsApp for OTP: {$resetToken->otp_code}");
        $this->line("4. Complete the password reset process");

        return 0;
    }
}
