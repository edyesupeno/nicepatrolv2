<?php

namespace App\Imports;

use App\Models\Karyawan;
use App\Models\User;
use App\Models\Jabatan;
use App\Models\Project;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class KaryawanImport implements ToCollection, WithHeadingRow
{
    protected $perusahaanId;
    protected $projectId;
    protected $role;
    protected $errors = [];
    protected $successCount = 0;
    protected $skippedCount = 0;

    public function __construct($perusahaanId, $projectId, $role = 'security_officer')
    {
        $this->perusahaanId = $perusahaanId;
        $this->projectId = $projectId;
        $this->role = $role;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 karena header di row 1, data mulai row 2
            
            try {
                // Skip empty rows
                if (empty($row['nik_karyawan']) && empty($row['nama_lengkap'])) {
                    continue;
                }
                
                // Validate required fields
                $validator = Validator::make($row->toArray(), [
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
                    'nik_karyawan.required' => 'NIK Karyawan wajib diisi',
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
                    $this->errors[] = "Baris {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    $this->skippedCount++;
                    continue;
                }
                
                // Check if NIK already exists
                $existingKaryawan = Karyawan::where('nik_karyawan', $row['nik_karyawan'])
                    ->where('perusahaan_id', $this->perusahaanId)
                    ->first();
                
                if ($existingKaryawan) {
                    $this->errors[] = "Baris {$rowNumber}: NIK {$row['nik_karyawan']} sudah ada";
                    $this->skippedCount++;
                    continue;
                }
                
                // Check if email already exists
                $existingUser = User::where('email', $row['email'])->first();
                if ($existingUser) {
                    $this->errors[] = "Baris {$rowNumber}: Email {$row['email']} sudah digunakan";
                    $this->skippedCount++;
                    continue;
                }
                
                // Find project
                $project = Project::where('nama', $row['project'])
                    ->where('perusahaan_id', $this->perusahaanId)
                    ->first();
                
                if (!$project) {
                    $this->errors[] = "Baris {$rowNumber}: Project '{$row['project']}' tidak ditemukan";
                    $this->skippedCount++;
                    continue;
                }
                
                // Find jabatan
                $jabatan = Jabatan::where('nama', $row['jabatan'])
                    ->where('perusahaan_id', $this->perusahaanId)
                    ->first();
                
                if (!$jabatan) {
                    $this->errors[] = "Baris {$rowNumber}: Jabatan '{$row['jabatan']}' tidak ditemukan";
                    $this->skippedCount++;
                    continue;
                }
                
                // Parse tanggal lahir
                $tanggalLahir = null;
                if (!empty($row['tanggal_lahir'])) {
                    try {
                        $tanggalLahir = Carbon::parse($row['tanggal_lahir'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $this->errors[] = "Baris {$rowNumber}: Format tanggal lahir tidak valid";
                        $this->skippedCount++;
                        continue;
                    }
                }
                
                // Parse tanggal masuk
                try {
                    $tanggalMasuk = Carbon::parse($row['tanggal_masuk'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $this->errors[] = "Baris {$rowNumber}: Format tanggal masuk tidak valid";
                    $this->skippedCount++;
                    continue;
                }
                
                // Parse habis kontrak (optional)
                $habisKontrak = null;
                if (!empty($row['habis_kontrak'])) {
                    try {
                        $habisKontrak = Carbon::parse($row['habis_kontrak'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $this->errors[] = "Baris {$rowNumber}: Format habis kontrak tidak valid";
                        $this->skippedCount++;
                        continue;
                    }
                }
                
                // Create User first
                $user = User::create([
                    'name' => $row['nama_lengkap'],
                    'email' => $row['email'],
                    'password' => Hash::make('nicepatrol'), // Default password
                    'perusahaan_id' => $this->perusahaanId,
                    'role' => $this->role, // Use role from import form
                ]);
                
                // Create Karyawan
                Karyawan::create([
                    'user_id' => $user->id,
                    'perusahaan_id' => $this->perusahaanId,
                    'project_id' => $project->id,
                    'jabatan_id' => $jabatan->id,
                    'nik_karyawan' => $row['nik_karyawan'],
                    'nama_lengkap' => $row['nama_lengkap'],
                    'email' => $row['email'],
                    'no_telepon' => $row['no_telepon'] ?? null,
                    'jenis_kelamin' => $row['jenis_kelamin'],
                    'status_perkawinan' => $row['status_perkawinan'],
                    'jumlah_tanggungan' => $row['jumlah_tanggungan'],
                    'tanggal_lahir' => $tanggalLahir,
                    'tempat_lahir' => $row['tempat_lahir'] ?? 'N/A',
                    'status_karyawan' => $row['status_karyawan'],
                    'tanggal_masuk' => $tanggalMasuk,
                    'habis_kontrak' => $habisKontrak,
                    'is_active' => $row['status'] === 'Aktif',
                    // Required fields with default values
                    'nik_ktp' => $row['nik_karyawan'], // Use NIK Karyawan as NIK KTP if not provided
                    'telepon' => $row['no_telepon'] ?? '0000000000',
                    'alamat' => 'Belum diisi',
                    'kota' => 'Belum diisi',
                    'provinsi' => 'Belum diisi',
                    'gaji_pokok' => $row['gaji_pokok'] ?? 0,
                ]);
                
                $this->successCount++;
                
            } catch (\Exception $e) {
                $this->errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                $this->skippedCount++;
            }
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getSkippedCount()
    {
        return $this->skippedCount;
    }
}
