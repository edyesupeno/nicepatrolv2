<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patroli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatroliController extends Controller
{
    public function index(Request $request)
    {
        $patrolis = Patroli::select([
                'id',
                'perusahaan_id',
                'user_id',
                'lokasi_id',
                'waktu_mulai',
                'waktu_selesai',
                'status',
                'catatan'
            ])
            ->with([
                'user:id,name',
                'lokasi:id,nama',
                'details' => function($query) {
                    $query->with([
                        'checkpoint:id,nama,rute_patrol_id',
                        'checkpoint.rutePatrol:id,nama'
                    ])->orderBy('waktu_scan', 'asc');
                }
            ])
            ->orderBy('waktu_mulai', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $patrolis,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lokasi_id' => 'required|exists:kantors,id',
            'waktu_mulai' => 'required',
            'catatan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $validated['user_id'] = auth()->id();
            $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
            $validated['status'] = 'berlangsung';

            $patroli = Patroli::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Patroli berhasil dimulai',
                'data' => $patroli->load(['user:id,name', 'kantor:id,nama_kantor']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai patroli: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Patroli $patroli)
    {
        $patroli->load([
            'user:id,name',
            'lokasi:id,nama',
            'details' => function($query) {
                $query->with([
                    'checkpoint:id,nama,rute_patrol_id',
                    'checkpoint.rutePatrol:id,nama'
                ])->orderBy('waktu_scan', 'asc');
            }
        ]);

        return response()->json([
            'success' => true,
            'data' => $patroli,
        ]);
    }

    // Get GPS locations for all patrol details
    public function getGpsLocations(Patroli $patroli)
    {
        $locations = $patroli->details()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with([
                'checkpoint:id,nama,rute_patrol_id',
                'checkpoint.rutePatrol:id,nama'
            ])
            ->select('id', 'checkpoint_id', 'waktu_scan', 'latitude', 'longitude', 'catatan', 'status')
            ->orderBy('waktu_scan', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'patroli' => [
                    'id' => $patroli->id,
                    'lokasi' => $patroli->lokasi->nama ?? 'Unknown',
                    'user' => $patroli->user->name ?? 'Unknown',
                    'waktu_mulai' => $patroli->waktu_mulai,
                    'status' => $patroli->status
                ],
                'locations' => $locations->map(function($detail) {
                    return [
                        'id' => $detail->id,
                        'checkpoint' => $detail->checkpoint->nama ?? 'Unknown',
                        'rute' => $detail->checkpoint->rutePatrol->nama ?? 'Unknown',
                        'waktu_scan' => $detail->waktu_scan,
                        'latitude' => (float) $detail->latitude,
                        'longitude' => (float) $detail->longitude,
                        'catatan' => $detail->catatan,
                        'status' => $detail->status,
                        'google_maps_url' => "https://maps.google.com/?q={$detail->latitude},{$detail->longitude}"
                    ];
                })
            ]
        ]);
    }

    public function scanCheckpoint(Request $request, Patroli $patroli)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            // Find checkpoint by QR code
            $checkpoint = \App\Models\Checkpoint::where('qr_code', $validated['qr_code'])
                ->where('is_active', true)
                ->first();

            if (!$checkpoint) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code checkpoint tidak valid atau tidak aktif',
                ], 404);
            }

            // Check if checkpoint already scanned in this patrol
            $existingDetail = \App\Models\PatroliDetail::where('patroli_id', $patroli->id)
                ->where('checkpoint_id', $checkpoint->id)
                ->first();

            if ($existingDetail) {
                // If already scanned, still allow to view checkpoint with assets
                $checkpoint->load(['asets' => function($query) {
                    $query->select('aset_kawasans.id', 'kode_aset', 'nama', 'kategori', 'merk', 'model', 'foto')
                          ->where('is_active', true)
                          ->withPivot('catatan');
                }]);

                return response()->json([
                    'success' => true,
                    'message' => 'Checkpoint sudah di-scan sebelumnya. Menampilkan detail checkpoint.',
                    'data' => [
                        'checkpoint' => $checkpoint,
                        'patrol_detail' => $existingDetail,
                        'scan_time' => $existingDetail->waktu_scan,
                        'already_scanned' => true
                    ]
                ], 200);
            }

            // Create patrol detail
            $patroliDetail = \App\Models\PatroliDetail::create([
                'patroli_id' => $patroli->id,
                'checkpoint_id' => $checkpoint->id,
                'waktu_scan' => now(),
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'status' => 'normal',
            ]);

            // Load checkpoint with assets
            $checkpoint->load(['asets' => function($query) {
                $query->select('aset_kawasans.id', 'kode_aset', 'nama', 'kategori', 'merk', 'model', 'foto')
                      ->where('is_active', true)
                      ->withPivot('catatan');
            }]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkpoint berhasil di-scan',
                'data' => [
                    'checkpoint' => $checkpoint,
                    'patrol_detail' => $patroliDetail,
                    'scan_time' => $patroliDetail->waktu_scan,
                    'already_scanned' => false
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal scan checkpoint: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Add new method for scanning without existing patrol
    public function scanQRCode(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            // Parse QR code - handle both simple string and JSON format
            $qrCodeValue = $validated['qr_code'];
            
            // Try to decode as JSON first
            $qrData = json_decode($qrCodeValue, true);
            if (json_last_error() === JSON_ERROR_NONE && isset($qrData['qr_code'])) {
                // QR code is JSON format, extract the actual qr_code
                $actualQrCode = $qrData['qr_code'];
            } else {
                // QR code is simple string format
                $actualQrCode = $qrCodeValue;
            }

            // Debug logging
            \Log::info('Scan QR Code attempt', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'Unknown',
                'perusahaan_id' => auth()->user()->perusahaan_id ?? 'Unknown',
                'raw_qr_code' => $qrCodeValue,
                'parsed_qr_code' => $actualQrCode,
                'is_json_format' => isset($qrData)
            ]);

            // Find checkpoint by QR code
            $checkpoint = \App\Models\Checkpoint::where('qr_code', $actualQrCode)
                ->where('is_active', true)
                ->first();

            if (!$checkpoint) {
                // Debug: Check if checkpoint exists without global scope
                $checkpointWithoutScope = \App\Models\Checkpoint::withoutGlobalScope('perusahaan')
                    ->where('qr_code', $actualQrCode)
                    ->first();
                
                \Log::warning('Checkpoint not found', [
                    'raw_qr_code' => $qrCodeValue,
                    'parsed_qr_code' => $actualQrCode,
                    'user_perusahaan_id' => auth()->user()->perusahaan_id,
                    'checkpoint_exists_without_scope' => $checkpointWithoutScope ? 'Yes' : 'No',
                    'checkpoint_perusahaan_id' => $checkpointWithoutScope ? $checkpointWithoutScope->perusahaan_id : 'N/A'
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'QR Code checkpoint tidak valid atau tidak aktif',
                    'debug' => [
                        'raw_qr_code' => $qrCodeValue,
                        'parsed_qr_code' => $actualQrCode,
                        'user_perusahaan_id' => auth()->user()->perusahaan_id,
                        'checkpoint_found_without_scope' => $checkpointWithoutScope ? true : false
                    ]
                ], 404);
            }

            // Check if user has active patrol
            $activePatroli = Patroli::where('user_id', auth()->id())
                ->where('status', 'berlangsung')
                ->first();

            if (!$activePatroli) {
                // Create new patrol automatically
                $kantor = \App\Models\Kantor::where('perusahaan_id', auth()->user()->perusahaan_id)
                    ->first();

                if (!$kantor) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tidak ada kantor yang tersedia untuk memulai patroli',
                    ], 400);
                }

                $activePatroli = Patroli::create([
                    'user_id' => auth()->id(),
                    'perusahaan_id' => auth()->user()->perusahaan_id,
                    'lokasi_id' => $kantor->id,
                    'waktu_mulai' => now(),
                    'status' => 'berlangsung',
                    'catatan' => 'Patroli dimulai otomatis dari scan checkpoint'
                ]);
            }

            // Check if checkpoint already scanned in this patrol
            $existingDetail = \App\Models\PatroliDetail::where('patroli_id', $activePatroli->id)
                ->where('checkpoint_id', $checkpoint->id)
                ->first();

            if (!$existingDetail) {
                // Create patrol detail
                $patroliDetail = \App\Models\PatroliDetail::create([
                    'patroli_id' => $activePatroli->id,
                    'checkpoint_id' => $checkpoint->id,
                    'waktu_scan' => now(),
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'status' => 'normal',
                ]);
            } else {
                $patroliDetail = $existingDetail;
            }

            // Load checkpoint with assets (all assets for status checking)
            $checkpoint->load(['asets' => function($query) {
                $query->select('aset_kawasans.id', 'kode_aset', 'nama', 'kategori', 'merk', 'model', 'foto')
                      ->where('is_active', true)
                      ->withPivot('catatan');
            }]);

            // Select 1 random asset for photo verification
            $photoVerificationAset = $checkpoint->asets->random(1)->first();

            DB::commit();

            \Log::info('Scan QR Code success', [
                'checkpoint_id' => $checkpoint->id,
                'patrol_detail_id' => $patroliDetail->id,
                'asets_count' => $checkpoint->asets->count(),
                'photo_verification_aset' => $photoVerificationAset->nama ?? 'None'
            ]);

            return response()->json([
                'success' => true,
                'message' => $existingDetail ? 'Checkpoint sudah di-scan sebelumnya' : 'Checkpoint berhasil di-scan',
                'data' => [
                    'checkpoint' => $checkpoint,
                    'patrol_detail' => $patroliDetail,
                    'patroli' => $activePatroli,
                    'scan_time' => $patroliDetail->waktu_scan,
                    'already_scanned' => (bool) $existingDetail,
                    'asets' => $checkpoint->asets, // All assets for status checking
                    'photo_verification_aset' => $photoVerificationAset ?? null, // 1 random asset for photo
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Scan QR Code error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal scan checkpoint: ' . $e->getMessage(),
            ], 500);
        }
    }
}
