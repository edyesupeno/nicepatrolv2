<?php

namespace App\Jobs;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\Project;
use App\Models\Area;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportKaryawanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $perusahaanId;
    protected $projectId;
    protected $role;
    protected $userId;
    public $jobId; // Make it public so it can be accessed

    public function __construct($filePath, $perusahaanId, $projectId, $role, $userId, $jobId = null)
    {
        $this->filePath = $filePath;
        $this->perusahaanId = $perusahaanId;
        $this->projectId = $projectId;
        $this->role = $role;
        $this->userId = $userId;
        $this->jobId = $jobId ?: uniqid('import_karyawan_');
    }

    public function handle()
    {
        try {
            \Log::info("ImportKaryawanJob started", [
                'job_id' => $this->jobId,
                'file_path' => $this->filePath,
                'user_id' => $this->userId
            ]);
            
            // Initialize progress
            $this->updateProgress(0, 'Memulai import...');

            // Check if file exists
            if (!file_exists($this->filePath)) {
                \Log::error("File not found: " . $this->filePath);
                $this->updateProgress(100, 'Error: File tidak ditemukan', 0, 0, ['File tidak ditemukan: ' . $this->filePath]);
                return;
            }

            // Load Excel file
            \Log::info("Loading Excel file: " . $this->filePath);
            $spreadsheet = IOFactory::load($this->filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            \Log::info("Excel loaded, total rows: " . count($rows));

            // Remove header row
            $header = array_shift($rows);
            $totalRows = count($rows);

            \Log::info("Header: " . json_encode($header));
            \Log::info("Data rows: " . $totalRows);

            if ($totalRows === 0) {
                $this->updateProgress(100, 'Selesai', 0, 0, ['File kosong atau tidak ada data']);
                return;
            }

            $this->updateProgress(10, "Memproses {$totalRows} baris data...");

            // Process data
            $errors = [];
            $successCount = 0;
            $skippedCount = 0;

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 karena header di row 1, data mulai row 2
                $progress = 10 + (($index + 1) / $totalRows) * 80; // 10-90%

                try {
                    // Convert array to associative array using header
                    // Clean headers: remove *, trim spaces, lowercase
                    $cleanHeaders = array_map(function($h) {
                        return strtolower(trim(str_replace('*', '', $h)));
                    }, $header);
                    
                    $data = array_combine($cleanHeaders, $row);
                    
                    // Map header fields to expected field names
                    $mappedData = [];
                    
                    // Map "No Badge" to "nik_karyawan"
                    if (isset($data['no badge'])) {
                        $mappedData['nik_karyawan'] = $data['no badge'];
                    } elseif (isset($data['no_badge'])) {
                        $mappedData['nik_karyawan'] = $data['no_badge'];
                    } elseif (isset($data['nik karyawan'])) {
                        $mappedData['nik_karyawan'] = $data['nik karyawan'];
                    }
                    
                    // Map "Nama Lengkap" to "nama_lengkap"
                    if (isset($data['nama lengkap'])) {
                        $mappedData['nama_lengkap'] = $data['nama lengkap'];
                    }
                    
                    // Map "Email" (already correct)
                    if (isset($data['email'])) {
                        $mappedData['email'] = $data['email'];
                    }
                    
                    // Map "No. Telepon" to "no_telepon"
                    if (isset($data['no. telepon'])) {
                        $mappedData['no_telepon'] = $data['no. telepon'];
                    } elseif (isset($data['no telepon'])) {
                        $mappedData['no_telepon'] = $data['no telepon'];
                    }
                    
                    // Map "Project" (already correct)
                    if (isset($data['project'])) {
                        $mappedData['project'] = $data['project'];
                    }
                    
                    // Map "Jabatan" (already correct)
                    if (isset($data['jabatan'])) {
                        $mappedData['jabatan'] = $data['jabatan'];
                    }
                    
                    // Map "Status Karyawan" to "status_karyawan"
                    if (isset($data['status karyawan'])) {
                        $mappedData['status_karyawan'] = $data['status karyawan'];
                    }
                    
                    // Map "Jenis Kelamin" to "jenis_kelamin"
                    if (isset($data['jenis kelamin'])) {
                        $mappedData['jenis_kelamin'] = $data['jenis kelamin'];
                    }
                    
                    // Map "Status Perkawinan" to "status_perkawinan"
                    if (isset($data['status perkawinan'])) {
                        $mappedData['status_perkawinan'] = $data['status perkawinan'];
                    }
                    
                    // Map "Jumlah Tanggungan" to "jumlah_tanggungan"
                    if (isset($data['jumlah tanggungan'])) {
                        $mappedData['jumlah_tanggungan'] = $data['jumlah tanggungan'];
                    }
                    
                    // Map "Tanggal Lahir" to "tanggal_lahir"
                    if (isset($data['tanggal lahir'])) {
                        $mappedData['tanggal_lahir'] = $data['tanggal lahir'];
                    }
                    
                    // Map "Tempat Lahir" to "tempat_lahir"
                    if (isset($data['tempat lahir'])) {
                        $mappedData['tempat_lahir'] = $data['tempat lahir'];
                    }
                    
                    // Map "Tanggal Masuk" to "tanggal_masuk"
                    if (isset($data['tanggal masuk'])) {
                        $mappedData['tanggal_masuk'] = $data['tanggal masuk'];
                    }
                    
                    // Map "Habis Kontrak" to "habis_kontrak"
                    if (isset($data['habis kontrak'])) {
                        $mappedData['habis_kontrak'] = $data['habis kontrak'];
                    }
                    
                    // Map "Status" (already correct)
                    if (isset($data['status'])) {
                        $mappedData['status'] = $data['status'];
                    }
                    
                    // Use mapped data
                    $data = $mappedData;

                    // Skip empty rows - SIMPLE: Cek kolom A, B, C (No Badge, Nama, Email)
                    $nikKaryawan = trim($data['nik_karyawan'] ?? '');
                    $namaLengkap = trim($data['nama_lengkap'] ?? '');
                    $email = trim($data['email'] ?? '');
                    
                    // Jika ketiga kolom utama kosong, skip baris ini
                    if (empty($nikKaryawan) && empty($namaLengkap) && empty($email)) {
                        continue;
                    }
                    
                    // Jika salah satu dari 3 kolom utama kosong, juga skip (kemungkinan instruksi)
                    if (empty($nikKaryawan) || empty($namaLengkap) || empty($email)) {
                        continue;
                    }

                    $result = $this->processRow($data, $rowNumber);
                    
                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $errors[] = $result['error'];
                        $skippedCount++;
                    }

                    // Update progress every 10 rows
                    if ($index % 10 === 0) {
                        $this->updateProgress(
                            $progress, 
                            "Memproses baris {$rowNumber}...",
                            $successCount,
                            $skippedCount,
                            array_slice($errors, -3) // Last 3 errors
                        );
                    }

                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                    $skippedCount++;
                }
            }

            // Final progress
            $this->updateProgress(
                100, 
                'Import selesai!',
                $successCount,
                $skippedCount,
                $errors
            );

            // Clean up file
            if (file_exists($this->filePath)) {
                unlink($this->filePath);
            }

        } catch (\Exception $e) {
            \Log::error("ImportKaryawanJob failed", [
                'job_id' => $this->jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->updateProgress(100, 'Import gagal: ' . $e->getMessage(), 0, 0, [$e->getMessage()]);
        }
    }

    private function processRow($data, $rowNumber)
    {
        // Validate required fields
        $validator = Validator::make($data, [
            'nik_karyawan' => 'required|string|max:50',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'project' => 'required|string',
            'jabatan' => 'required|string',
            'status_karyawan' => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'status_perkawinan' => 'required|in:TK,K',
            'jumlah_tanggungan' => 'required|integer|min:0|max:3',
            'tanggal_masuk' => 'required|date',
            'status' => 'required|in:Aktif,Tidak Aktif',
            // Optional fields
            'no_telepon' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:255',
            'habis_kontrak' => 'nullable|date',
            'gaji_pokok' => 'nullable|numeric|min:0',
        ], [
            'nik_karyawan.required' => 'No Badge wajib diisi',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'project.required' => 'Project wajib diisi',
            'jabatan.required' => 'Jabatan wajib diisi',
            'status_karyawan.required' => 'Status Karyawan wajib diisi',
            'jenis_kelamin.required' => 'Jenis Kelamin wajib diisi',
            'jenis_kelamin.in' => 'Jenis Kelamin harus Laki-laki atau Perempuan',
            'status_perkawinan.required' => 'Status Perkawinan wajib diisi',
            'status_perkawinan.in' => 'Status Perkawinan harus TK atau K',
            'jumlah_tanggungan.required' => 'Jumlah Tanggungan wajib diisi',
            'jumlah_tanggungan.integer' => 'Jumlah Tanggungan harus berupa angka',
            'jumlah_tanggungan.min' => 'Jumlah Tanggungan minimal 0',
            'jumlah_tanggungan.max' => 'Jumlah Tanggungan maksimal 3',
            'tanggal_masuk.required' => 'Tanggal Masuk wajib diisi',
            'tanggal_masuk.date' => 'Format Tanggal Masuk tidak valid',
            'status.required' => 'Status wajib diisi',
            'status.in' => 'Status harus Aktif atau Tidak Aktif',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'error' => "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all())
            ];
        }

        // Check if No Badge already exists
        $existingKaryawan = Karyawan::where('nik_karyawan', $data['nik_karyawan'])
            ->where('perusahaan_id', $this->perusahaanId)
            ->first();

        if ($existingKaryawan) {
            return [
                'success' => false,
                'error' => "Baris {$rowNumber}: No Badge {$data['nik_karyawan']} sudah ada"
            ];
        }

        // Check if email already exists
        $existingUser = User::where('email', $data['email'])->first();
        if ($existingUser) {
            return [
                'success' => false,
                'error' => "Baris {$rowNumber}: Email {$data['email']} sudah digunakan"
            ];
        }

        // Find project
        $project = Project::where('nama', $data['project'])
            ->where('perusahaan_id', $this->perusahaanId)
            ->first();

        if (!$project) {
            return [
                'success' => false,
                'error' => "Baris {$rowNumber}: Project '{$data['project']}' tidak ditemukan"
            ];
        }

        // Find jabatan
        $jabatan = Jabatan::where('nama', $data['jabatan'])
            ->where('perusahaan_id', $this->perusahaanId)
            ->first();

        if (!$jabatan) {
            return [
                'success' => false,
                'error' => "Baris {$rowNumber}: Jabatan '{$data['jabatan']}' tidak ditemukan"
            ];
        }

        // Parse dates
        $tanggalLahir = null;
        if (!empty($data['tanggal_lahir'])) {
            try {
                $tanggalLahir = Carbon::parse($data['tanggal_lahir'])->format('Y-m-d');
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'error' => "Baris {$rowNumber}: Format tanggal lahir tidak valid"
                ];
            }
        }

        try {
            $tanggalMasuk = Carbon::parse($data['tanggal_masuk'])->format('Y-m-d');
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "Baris {$rowNumber}: Format tanggal masuk tidak valid"
            ];
        }

        $habisKontrak = null;
        if (!empty($data['habis_kontrak'])) {
            try {
                $habisKontrak = Carbon::parse($data['habis_kontrak'])->format('Y-m-d');
            } catch (\Exception $e) {
                return [
                    'success' => false,
                    'error' => "Baris {$rowNumber}: Format habis kontrak tidak valid"
                ];
            }
        }

        try {
            DB::transaction(function () use ($data, $project, $jabatan, $tanggalLahir, $tanggalMasuk, $habisKontrak) {
                // Create User first
                $user = User::create([
                    'name' => $data['nama_lengkap'],
                    'email' => $data['email'],
                    'password' => Hash::make('nicepatrol'), // Default password
                    'perusahaan_id' => $this->perusahaanId,
                    'role' => $this->role,
                ]);

                // Create Karyawan
                $karyawan = Karyawan::create([
                    'user_id' => $user->id,
                    'perusahaan_id' => $this->perusahaanId,
                    'project_id' => $project->id,
                    'jabatan_id' => $jabatan->id,
                    'nik_karyawan' => $data['nik_karyawan'], // This is now "No Badge"
                    'nama_lengkap' => $data['nama_lengkap'],
                    'email' => $data['email'],
                    'no_telepon' => $data['no_telepon'] ?? null,
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'status_perkawinan' => $data['status_perkawinan'],
                    'jumlah_tanggungan' => $data['jumlah_tanggungan'],
                    'tanggal_lahir' => $tanggalLahir,
                    'tempat_lahir' => $data['tempat_lahir'] ?? 'N/A',
                    'status_karyawan' => $data['status_karyawan'],
                    'tanggal_masuk' => $tanggalMasuk,
                    'habis_kontrak' => $habisKontrak,
                    'is_active' => $data['status'] === 'Aktif',
                    // Required fields with default values
                    'nik_ktp' => $data['nik_karyawan'], // Use No Badge as NIK KTP if not provided
                    'telepon' => $data['no_telepon'] ?? '0000000000',
                    'alamat' => 'Belum diisi',
                    'kota' => 'Belum diisi',
                    'provinsi' => 'Belum diisi',
                    'gaji_pokok' => $data['gaji_pokok'] ?? 0,
                ]);

                // AUTO-ADD AREA: Assign semua area di project ke karyawan
                $this->autoAssignAreas($karyawan, $project);
            });

            return ['success' => true];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => "Baris {$rowNumber}: " . $e->getMessage()
            ];
        }
    }

    private function autoAssignAreas($karyawan, $project)
    {
        // Get all areas in the project
        $areas = Area::where('project_id', $project->id)
            ->where('perusahaan_id', $this->perusahaanId)
            ->get();

        if ($areas->count() > 0) {
            foreach ($areas as $index => $area) {
                // Insert ke karyawan_areas
                DB::table('karyawan_areas')->insertOrIgnore([
                    'karyawan_id' => $karyawan->id,
                    'area_id' => $area->id,
                    'is_primary' => $index === 0, // First area is primary
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function updateProgress($percentage, $message, $successCount = 0, $skippedCount = 0, $errors = [])
    {
        $progress = [
            'percentage' => $percentage,
            'message' => $message,
            'success_count' => $successCount,
            'skipped_count' => $skippedCount,
            'errors' => array_slice($errors, -5), // Keep last 5 errors
            'completed' => $percentage >= 100,
            'timestamp' => now()->toISOString(),
        ];

        // Store in cache for 1 hour
        Cache::put("import_progress_{$this->userId}_{$this->jobId}", $progress, 3600);
    }

    public function getJobId()
    {
        return $this->jobId;
    }
}