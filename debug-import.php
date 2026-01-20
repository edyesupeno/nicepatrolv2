<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test basic functionality
echo "🔍 Debug Import Karyawan\n";
echo "========================\n\n";

// 1. Test database connection
try {
    $pdo = DB::connection()->getPdo();
    echo "✅ Database connection: OK\n";
} catch (Exception $e) {
    echo "❌ Database connection: FAILED - " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Check queue table
try {
    $jobCount = DB::table('jobs')->count();
    echo "✅ Queue table exists, jobs count: $jobCount\n";
} catch (Exception $e) {
    echo "❌ Queue table: FAILED - " . $e->getMessage() . "\n";
}

// 3. Check temp directory
$tempDir = storage_path('app/temp');
if (!file_exists($tempDir)) {
    mkdir($tempDir, 0755, true);
    echo "✅ Created temp directory: $tempDir\n";
} else {
    echo "✅ Temp directory exists: $tempDir\n";
}

// 4. Test job creation
echo "\n🧪 Testing Job Creation...\n";

try {
    // Create a dummy file for testing
    $testFile = $tempDir . '/test_import.xlsx';
    file_put_contents($testFile, 'dummy content');
    
    $jobId = uniqid('test_import_');
    $job = new \App\Jobs\ImportKaryawanJob($testFile, 1, 1, 'security_officer', 1, $jobId);
    
    echo "✅ Job created successfully with ID: $jobId\n";
    
    // Test progress update
    $reflection = new ReflectionClass($job);
    $method = $reflection->getMethod('updateProgress');
    $method->setAccessible(true);
    $method->invoke($job, 50, 'Testing progress...', 0, 0, []);
    
    echo "✅ Progress update test: OK\n";
    
    // Check if progress was stored
    $progress = Cache::get("import_progress_1_$jobId");
    if ($progress) {
        echo "✅ Progress stored in cache: " . json_encode($progress) . "\n";
    } else {
        echo "❌ Progress not found in cache\n";
    }
    
    // Clean up
    unlink($testFile);
    
} catch (Exception $e) {
    echo "❌ Job creation failed: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n✅ Debug completed!\n";