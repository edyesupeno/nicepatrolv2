<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Checkpoint;
use App\Models\AsetKawasan;
use App\Models\PatroliDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsetCheckpointController extends Controller
{
    public function checkpointAsets(Checkpoint $checkpoint)
    {
        // Load all assets for status checking
        $checkpoint->load(['asets' => function($query) {
            $query->select('aset_kawasans.id', 'kode_aset', 'nama', 'kategori', 'merk', 'model', 'foto', 'is_active')
                  ->where('is_active', true)
                  ->withPivot('catatan');
        }]);

        // Select 1 random asset for photo verification
        $randomAsetForPhoto = $checkpoint->asets->random(1)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'checkpoint' => $checkpoint,
                'asets' => $checkpoint->asets, // All assets for status checking
                'photo_verification_aset' => $randomAsetForPhoto, // 1 random asset for photo
            ]
        ]);
    }

    public function updateAsetStatus(Request $request, Checkpoint $checkpoint)
    {
        // Validasi dasar
        $validated = $request->validate([
            'patroli_detail_id' => 'required|exists:patroli_details,id',
            'aset_checks' => 'required|array',
            'aset_checks.*.aset_id' => 'required|exists:aset_kawasans,id',
            'aset_checks.*.status' => 'required|in:aman,bermasalah,hilang',
            'aset_checks.*.catatan' => 'nullable|string|max:500',
            'aset_checks.*.foto' => 'nullable|string', // Base64 image
        ]);

        // Validasi khusus: foto dan catatan wajib untuk status selain "aman"
        foreach ($validated['aset_checks'] as $index => $asetCheck) {
            if (in_array($asetCheck['status'], ['bermasalah', 'hilang'])) {
                // Validasi foto wajib
                if (empty($asetCheck['foto'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Foto wajib diisi untuk aset dengan status ' . $asetCheck['status'],
                        'errors' => [
                            "aset_checks.{$index}.foto" => ['Foto wajib diisi untuk status ' . $asetCheck['status']]
                        ]
                    ], 422);
                }

                // Validasi catatan wajib
                if (empty($asetCheck['catatan']) || trim($asetCheck['catatan']) === '') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Catatan wajib diisi untuk aset dengan status ' . $asetCheck['status'],
                        'errors' => [
                            "aset_checks.{$index}.catatan" => ['Catatan wajib diisi untuk status ' . $asetCheck['status']]
                        ]
                    ], 422);
                }
            }
        }

        try {
            DB::beginTransaction();

            $patroliDetail = PatroliDetail::find($validated['patroli_detail_id']);
            
            // Verify patrol detail belongs to this checkpoint
            if ($patroliDetail->checkpoint_id !== $checkpoint->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patrol detail tidak sesuai dengan checkpoint',
                ], 400);
            }

            $asetChecks = [];
            foreach ($validated['aset_checks'] as $asetCheck) {
                // Verify asset belongs to this checkpoint
                $asetExists = $checkpoint->asets()->where('aset_kawasans.id', $asetCheck['aset_id'])->exists();
                if (!$asetExists) {
                    continue; // Skip if asset doesn't belong to checkpoint
                }

                // Handle foto upload if provided
                $fotoPath = null;
                if (!empty($asetCheck['foto'])) {
                    $fotoPath = $this->saveBase64Image($asetCheck['foto'], 'aset-checks');
                    
                    // Jika gagal upload foto untuk status bermasalah/hilang, return error
                    if ($fotoPath === null && in_array($asetCheck['status'], ['bermasalah', 'hilang'])) {
                        throw new \Exception('Gagal menyimpan foto untuk aset dengan status ' . $asetCheck['status']);
                    }
                }

                $asetChecks[] = [
                    'patroli_detail_id' => $patroliDetail->id,
                    'aset_kawasan_id' => $asetCheck['aset_id'],
                    'status' => $asetCheck['status'],
                    'catatan' => $asetCheck['catatan'] ?? null,
                    'foto' => $fotoPath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert aset checks
            if (!empty($asetChecks)) {
                DB::table('aset_checks')->insert($asetChecks);
            }

            // Update patrol detail status based on aset checks
            $hasIssues = collect($validated['aset_checks'])->contains(function($check) {
                return in_array($check['status'], ['bermasalah', 'hilang']);
            });

            $patroliDetail->update([
                'status' => $hasIssues ? 'bermasalah' : 'normal',
                'catatan' => $hasIssues ? 'Ada aset dengan masalah' : 'Semua aset dalam kondisi baik',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Status aset berhasil diperbarui',
                'data' => [
                    'patrol_detail' => $patroliDetail->fresh(),
                    'aset_checks_count' => count($asetChecks),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal update status aset: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function saveBase64Image($base64String, $folder)
    {
        try {
            // Remove data:image/jpeg;base64, prefix if exists
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            if ($imageData === false) {
                throw new \Exception('Invalid base64 image');
            }

            // Generate unique filename
            $filename = uniqid() . '_' . time() . '.jpg';
            $path = "uploads/{$folder}/" . date('Y/m');
            
            // Create directory if not exists
            $fullPath = storage_path("app/public/{$path}");
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }

            // Save file
            $filePath = "{$path}/{$filename}";
            file_put_contents(storage_path("app/public/{$filePath}"), $imageData);

            return $filePath;
        } catch (\Exception $e) {
            \Log::error('Failed to save base64 image: ' . $e->getMessage());
            return null;
        }
    }
}