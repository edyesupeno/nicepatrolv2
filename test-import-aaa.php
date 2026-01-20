<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Import dengan file aaa.xlsx\n";
echo "=====================================\n\n";

// Test parameters
$filePath = storage_path('app/temp/aaa.xlsx');
$perusahaanId = 1;
$projectId = 2; // Project Security ABB
$role = 'security_officer';
$userId = 1;
$jobId = 'test_aaa_' . time();

echo "📋 Test Parameters:\n";
echo "- File: $filePath\n";
echo "- Perusahaan ID: $perusahaanId\n";
echo "- Project ID: $projectId\n";
echo "- Role: $role\n";
echo "- User ID: $userId\n";
echo "- Job ID: $jobId\n\n";

// Check if file exists
if (!file_exists($filePath)) {
    echo "❌ File not found: $filePath\n";
    echo "📁 Files in temp directory:\n";
    $files = scandir(storage_path('app/temp/'));
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  - $file\n";
        }
    }
    exit(1);
}

echo "✅ File exists (" . filesize($filePath) . " bytes)\n";

// Test reading Excel file first
try {
    echo "\n📖 Reading Excel file...\n";
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    
    echo "✅ Excel file loaded successfully\n";
    echo "📊 Total rows: " . count($rows) . "\n";
    
    if (count($rows) > 0) {
        echo "📋 Headers: " . implode(', ', $rows[0]) . "\n";
        
        if (count($rows) > 1) {
            echo "📄 Sample data (row 2): " . implode(', ', array_slice($rows[1], 0, 5)) . "...\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Failed to read Excel file: " . $e->getMessage() . "\n";
    exit(1);
}

// Create and execute job
try {
    echo "\n🚀 Creating and executing import job...\n";
    
    $job = new \App\Jobs\ImportKaryawanJob($filePath, $perusahaanId, $projectId, $role, $userId, $jobId);
    
    echo "✅ Job created\n";
    
    // Execute job directly (synchronous for testing)
    $job->handle();
    
    echo "✅ Job executed\n";
    
    // Check progress
    $progress = \Illuminate\Support\Facades\Cache::get("import_progress_{$userId}_{$jobId}");
    if ($progress) {
        echo "\n📊 Final Results:\n";
        echo "- Progress: " . $progress['percentage'] . "%\n";
        echo "- Status: " . $progress['message'] . "\n";
        echo "- Berhasil: " . $progress['success_count'] . " karyawan\n";
        echo "- Di-skip: " . $progress['skipped_count'] . " data\n";
        echo "- Errors: " . count($progress['errors']) . " error\n";
        
        if (!empty($progress['errors'])) {
            echo "\n❌ Detail Errors:\n";
            foreach (array_slice($progress['errors'], 0, 10) as $error) {
                echo "  - $error\n";
            }
            if (count($progress['errors']) > 10) {
                echo "  ... dan " . (count($progress['errors']) - 10) . " error lainnya\n";
            }
        }
        
        if ($progress['success_count'] > 0) {
            echo "\n✅ Import berhasil! Cek tabel karyawan untuk melihat data yang diimport.\n";
        }
    } else {
        echo "❌ No progress data found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Job failed: " . $e->getMessage() . "\n";
    echo "\n🔍 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎯 Test completed!\n";