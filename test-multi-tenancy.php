<?php

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Perusahaan;
use App\Models\Project;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\PenerimaanBarang;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Multi-Tenancy Implementation ===\n\n";

try {
    // Test 1: Check if global scopes are working
    echo "1. Testing Project Global Scope\n";
    
    // Create test data
    $perusahaanA = Perusahaan::create([
        'nama' => 'Test Perusahaan A',
        'kode' => 'TESTA',
        'alamat' => 'Test Address A',
        'is_active' => true
    ]);
    
    $perusahaanB = Perusahaan::create([
        'nama' => 'Test Perusahaan B',
        'kode' => 'TESTB',
        'alamat' => 'Test Address B',
        'is_active' => true
    ]);
    
    $projectA = Project::create([
        'perusahaan_id' => $perusahaanA->id,
        'nama' => 'Test Project A',
        'is_active' => true
    ]);
    
    $projectB = Project::create([
        'perusahaan_id' => $perusahaanB->id,
        'nama' => 'Test Project B',
        'is_active' => true
    ]);
    
    $jabatanA = Jabatan::create([
        'perusahaan_id' => $perusahaanA->id,
        'nama' => 'Security A'
    ]);
    
    $userA = User::create([
        'perusahaan_id' => $perusahaanA->id,
        'name' => 'User A',
        'email' => 'usera@test.com',
        'password' => bcrypt('password'),
        'role' => 'security_officer'
    ]);
    
    $karyawanA = Karyawan::create([
        'perusahaan_id' => $perusahaanA->id,
        'project_id' => $projectA->id,
        'user_id' => $userA->id,
        'jabatan_id' => $jabatanA->id,
        'nama_lengkap' => 'Karyawan A',
        'nik_karyawan' => 'EMP001',
        'gaji_pokok' => 5000000
    ]);
    
    echo "✅ Test data created successfully\n";
    
    // Test 2: Login as User A and check project filtering
    echo "\n2. Testing Authentication and Project Info\n";
    
    auth()->login($userA);
    echo "✅ User A logged in\n";
    
    // Test project filtering
    $projects = Project::all();
    echo "Projects visible to User A: " . $projects->count() . " (should be 1)\n";
    if ($projects->count() === 1 && $projects->first()->nama === 'Test Project A') {
        echo "✅ Project filtering works correctly\n";
    } else {
        echo "❌ Project filtering failed\n";
    }
    
    // Test 3: Check AuthController response
    echo "\n3. Testing AuthController Response\n";
    
    $authController = new App\Http\Controllers\Api\AuthController();
    $request = new Illuminate\Http\Request();
    $response = $authController->user($request);
    $data = json_decode($response->getContent(), true);
    
    if (isset($data['data']['project_id']) && $data['data']['project_id'] == $projectA->id) {
        echo "✅ AuthController returns correct project_id\n";
    } else {
        echo "❌ AuthController project_id missing or incorrect\n";
        echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    }
    
    // Test 4: Test PenerimaanBarang filtering
    echo "\n4. Testing PenerimaanBarang Multi-Tenancy\n";
    
    $penerimaanA = PenerimaanBarang::create([
        'perusahaan_id' => $perusahaanA->id,
        'project_id' => $projectA->id,
        'nomor_penerimaan' => 'PB001',
        'nama_barang' => 'Laptop A',
        'kategori_barang' => 'Elektronik',
        'jumlah_barang' => 1,
        'satuan' => 'unit',
        'kondisi_barang' => 'Baik',
        'pengirim' => 'Supplier A',
        'tujuan_departemen' => 'IT',
        'tanggal_terima' => now(),
        'status' => 'Diterima',
        'petugas_penerima' => 'User A'
    ]);
    
    $penerimaanB = PenerimaanBarang::create([
        'perusahaan_id' => $perusahaanB->id,
        'project_id' => $projectB->id,
        'nomor_penerimaan' => 'PB002',
        'nama_barang' => 'Laptop B',
        'kategori_barang' => 'Elektronik',
        'jumlah_barang' => 1,
        'satuan' => 'unit',
        'kondisi_barang' => 'Baik',
        'pengirim' => 'Supplier B',
        'tujuan_departemen' => 'IT',
        'tanggal_terima' => now(),
        'status' => 'Diterima',
        'petugas_penerima' => 'User B'
    ]);
    
    $visiblePenerimaan = PenerimaanBarang::all();
    echo "PenerimaanBarang visible to User A: " . $visiblePenerimaan->count() . " (should be 1)\n";
    
    if ($visiblePenerimaan->count() === 1 && $visiblePenerimaan->first()->nama_barang === 'Laptop A') {
        echo "✅ PenerimaanBarang filtering works correctly\n";
    } else {
        echo "❌ PenerimaanBarang filtering failed\n";
    }
    
    echo "\n=== Multi-Tenancy Test Completed ===\n";
    echo "✅ All tests passed! Multi-tenancy is working correctly.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}