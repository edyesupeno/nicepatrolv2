<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Import Job Manually\n";
echo "==============================\n\n";

// Test parameters
$filePath = 'storage/app/temp/sample_import.csv';
$perusahaanId = 1;
$projectId = 2; // Project Security ABB
$role = 'security_officer';
$userId = 1;
$jobId = 'test_manual_' . time();

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
    exit(1);
}

echo "✅ File exists\n";

// Create and dispatch job
try {
    $job = new \App\Jobs\ImportKaryawanJob($filePath, $perusahaanId, $projectId, $role, $userId, $jobId);
    
    echo "✅ Job created\n";
    
    // Execute job directly (synchronous for testing)
    $job->handle();
    
    echo "✅ Job executed\n";
    
    // Check progress
    $progress = \Illuminate\Support\Facades\Cache::get("import_progress_{$userId}_{$jobId}");
    if ($progress) {
        echo "📊 Final Progress:\n";
        echo "- Percentage: " . $progress['percentage'] . "%\n";
        echo "- Message: " . $progress['message'] . "\n";
        echo "- Success: " . $progress['success_count'] . "\n";
        echo "- Skipped: " . $progress['skipped_count'] . "\n";
        echo "- Errors: " . count($progress['errors']) . "\n";
        
        if (!empty($progress['errors'])) {
            echo "\n❌ Errors:\n";
            foreach ($progress['errors'] as $error) {
                echo "  - $error\n";
            }
        }
    } else {
        echo "❌ No progress data found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Job failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎯 Test completed!\n";