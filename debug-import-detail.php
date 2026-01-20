<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Debug Import Detail - aaa.xlsx\n";
echo "=================================\n\n";

$filePath = storage_path('app/temp/aaa.xlsx');

// Read and analyze Excel file
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
$worksheet = $spreadsheet->getActiveSheet();
$rows = $worksheet->toArray();

echo "📊 File Analysis:\n";
echo "- Total rows: " . count($rows) . "\n";

// Show headers
$header = array_shift($rows);
echo "- Headers (" . count($header) . "):\n";
foreach ($header as $i => $h) {
    echo "  [$i] '$h'\n";
}

echo "\n📄 Sample Data Rows:\n";
foreach (array_slice($rows, 0, 3) as $i => $row) {
    echo "Row " . ($i + 2) . ":\n";
    foreach ($row as $j => $cell) {
        $headerName = $header[$j] ?? "Col$j";
        echo "  [$headerName] = '$cell'\n";
    }
    echo "\n";
}

// Test header mapping
echo "🔧 Testing Header Mapping:\n";
$headerLower = array_map('strtolower', $header);
echo "- Lowercase headers: " . json_encode($headerLower) . "\n";

// Test data conversion for first row
if (count($rows) > 0) {
    echo "\n🧪 Testing Data Conversion (Row 2):\n";
    $testRow = $rows[0];
    $data = array_combine($headerLower, $testRow);
    
    echo "- Combined data: " . json_encode($data) . "\n";
    
    // Test backward compatibility mapping
    if (isset($data['no_badge'])) {
        $data['nik_karyawan'] = $data['no_badge'];
        echo "- Mapped 'no_badge' to 'nik_karyawan': " . $data['nik_karyawan'] . "\n";
    } elseif (isset($data['no badge'])) {
        $data['nik_karyawan'] = $data['no badge'];
        echo "- Mapped 'no badge' to 'nik_karyawan': " . $data['nik_karyawan'] . "\n";
    } elseif (isset($data['nik karyawan'])) {
        $data['nik_karyawan'] = $data['nik karyawan'];
        echo "- Mapped 'nik karyawan' to 'nik_karyawan': " . $data['nik_karyawan'] . "\n";
    }
    
    // Check required fields
    $requiredFields = ['nik_karyawan', 'nama_lengkap', 'email', 'project', 'jabatan'];
    echo "\n✅ Required Fields Check:\n";
    foreach ($requiredFields as $field) {
        $value = $data[$field] ?? 'MISSING';
        $status = !empty($value) && $value !== 'MISSING' ? '✅' : '❌';
        echo "  $status $field: '$value'\n";
    }
    
    // Check if project exists
    echo "\n🏢 Project Check:\n";
    $projectName = $data['project'] ?? '';
    echo "- Looking for project: '$projectName'\n";
    
    $project = \App\Models\Project::where('nama', $projectName)
        ->where('perusahaan_id', 1)
        ->first();
    
    if ($project) {
        echo "  ✅ Project found: ID {$project->id} - {$project->nama}\n";
    } else {
        echo "  ❌ Project NOT found\n";
        echo "  📋 Available projects:\n";
        $projects = \App\Models\Project::where('perusahaan_id', 1)->get();
        foreach ($projects as $p) {
            echo "    - ID {$p->id}: '{$p->nama}'\n";
        }
    }
    
    // Check if jabatan exists
    echo "\n👔 Jabatan Check:\n";
    $jabatanName = $data['jabatan'] ?? '';
    echo "- Looking for jabatan: '$jabatanName'\n";
    
    $jabatan = \App\Models\Jabatan::where('nama', $jabatanName)
        ->where('perusahaan_id', 1)
        ->first();
    
    if ($jabatan) {
        echo "  ✅ Jabatan found: ID {$jabatan->id} - {$jabatan->nama}\n";
    } else {
        echo "  ❌ Jabatan NOT found\n";
        echo "  📋 Available jabatans:\n";
        $jabatans = \App\Models\Jabatan::where('perusahaan_id', 1)->get();
        foreach ($jabatans as $j) {
            echo "    - ID {$j->id}: '{$j->nama}'\n";
        }
    }
}

echo "\n🎯 Debug completed!\n";