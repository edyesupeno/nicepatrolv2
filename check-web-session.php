<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Check Web Dashboard Session ===\n\n";

try {
    // Cek session files
    $sessionPath = storage_path('framework/sessions');
    echo "Session path: {$sessionPath}\n";
    
    if (is_dir($sessionPath)) {
        $sessionFiles = glob($sessionPath . '/*');
        echo "Session files found: " . count($sessionFiles) . "\n";
        
        foreach ($sessionFiles as $file) {
            if (is_file($file)) {
                $content = file_get_contents($file);
                
                // Decode Laravel session
                if (strpos($content, 'laravel_session') !== false) {
                    echo "\nSession file: " . basename($file) . "\n";
                    
                    // Try to extract user info
                    if (preg_match('/login_web_[a-f0-9]+.*?i:(\d+);/', $content, $matches)) {
                        $userId = $matches[1];
                        $user = App\Models\User::find($userId);
                        
                        if ($user) {
                            echo "  Logged in user: {$user->name} (ID: {$user->id})\n";
                            echo "  Role: {$user->role}\n";
                            echo "  Perusahaan ID: {$user->perusahaan_id}\n";
                            
                            // Test query dengan user ini
                            auth()->login($user);
                            $count = App\Models\PenerimaanBarang::count();
                            echo "  Can see {$count} penerimaan barang records\n";
                        }
                    }
                }
            }
        }
    }
    
    echo "\n=== Recommendation ===\n";
    echo "1. Login ke web dashboard dengan user: edy@gmail.com\n";
    echo "2. Password: 12345678\n";
    echo "3. Setelah login, data API yang dibuat akan muncul di dashboard\n";
    
    echo "\n=== Data yang Dibuat via API ===\n";
    $apiUser = App\Models\User::where('email', 'edy@gmail.com')->first();
    auth()->login($apiUser);
    
    $apiData = App\Models\PenerimaanBarang::latest()->take(5)->get();
    foreach ($apiData as $item) {
        echo "  - {$item->nama_barang} (Nomor: {$item->nomor_penerimaan})\n";
        echo "    Created: {$item->created_at->format('d/m/Y H:i')}\n";
        echo "    Created by: {$item->createdBy->name ?? 'Unknown'}\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}