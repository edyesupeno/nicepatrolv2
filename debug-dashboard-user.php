<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Debug Dashboard User Issue ===\n\n";

try {
    // Simulasi login user yang sama dengan API
    $user = App\Models\User::where('email', 'edy@gmail.com')->first();
    
    if (!$user) {
        echo "❌ User edy@gmail.com tidak ditemukan\n";
        exit;
    }
    
    echo "✅ User ditemukan: {$user->name} (ID: {$user->id})\n";
    echo "   Role: {$user->role}\n";
    echo "   Perusahaan ID: {$user->perusahaan_id}\n";
    
    // Login user
    auth()->login($user);
    
    echo "\n=== Simulasi Web Controller Query ===\n";
    
    // Query seperti di web controller
    $webQuery = App\Models\PenerimaanBarang::with(['perusahaan', 'project', 'area'])
        ->orderBy('tanggal_terima', 'desc');
    
    echo "Web Query SQL: " . $webQuery->toSql() . "\n";
    $webData = $webQuery->get();
    
    echo "Web Data Count: {$webData->count()}\n";
    foreach ($webData as $item) {
        echo "  - {$item->nama_barang} (created_by: {$item->created_by}, perusahaan_id: {$item->perusahaan_id}, project_id: {$item->project_id})\n";
    }
    
    echo "\n=== Simulasi API Controller Query ===\n";
    
    // Query seperti di API controller
    $apiQuery = App\Models\PenerimaanBarang::with(['project:id,nama', 'area:id,nama', 'createdBy:id,name'])
        ->select([
            'id',
            'created_by',
            'project_id',
            'area_id',
            'nomor_penerimaan',
            'nama_barang',
            'kategori_barang',
            'jumlah_barang',
            'satuan',
            'kondisi_barang',
            'pengirim',
            'tujuan_departemen',
            'tanggal_terima',
            'status',
            'petugas_penerima'
        ]);
    
    echo "API Query SQL: " . $apiQuery->toSql() . "\n";
    $apiData = $apiQuery->get();
    
    echo "API Data Count: {$apiData->count()}\n";
    foreach ($apiData as $item) {
        echo "  - {$item->nama_barang} (created_by: {$item->created_by}, project_id: {$item->project_id})\n";
    }
    
    echo "\n=== Global Scopes Analysis ===\n";
    
    // Cek global scopes yang aktif
    $model = new App\Models\PenerimaanBarang();
    $scopes = $model->getGlobalScopes();
    
    echo "Active Global Scopes:\n";
    foreach ($scopes as $name => $scope) {
        echo "  - {$name}: " . get_class($scope) . "\n";
    }
    
    echo "\n=== Test dengan User Role Berbeda ===\n";
    
    // Test dengan admin
    $admin = App\Models\User::where('role', 'admin')->where('perusahaan_id', 1)->first();
    if ($admin) {
        auth()->login($admin);
        echo "Login sebagai Admin: {$admin->name}\n";
        
        $adminData = App\Models\PenerimaanBarang::count();
        echo "Admin dapat melihat: {$adminData} records\n";
    }
    
    // Test dengan superadmin
    $superadmin = App\Models\User::where('role', 'superadmin')->first();
    if ($superadmin) {
        auth()->login($superadmin);
        echo "Login sebagai Superadmin: {$superadmin->name}\n";
        
        $superadminData = App\Models\PenerimaanBarang::count();
        echo "Superadmin dapat melihat: {$superadminData} records\n";
    }
    
    // Kembali ke user security
    auth()->login($user);
    echo "Kembali login sebagai Security: {$user->name}\n";
    $securityData = App\Models\PenerimaanBarang::count();
    echo "Security dapat melihat: {$securityData} records\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}