<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BukuTamu;
use App\Models\Project;
use App\Models\Area;
use App\Models\KuesionerTamu;
use App\Models\JawabanKuesionerTamu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BukuTamuController extends Controller
{
    /**
     * Get all guest book entries
     */
    public function index(Request $request)
    {
        try {
            $query = BukuTamu::with(['project:id,nama', 'area:id,nama', 'inputBy:id,name']);

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama_tamu', 'ILIKE', "%{$search}%")
                      ->orWhere('nik', 'ILIKE', "%{$search}%")
                      ->orWhere('email', 'ILIKE', "%{$search}%")
                      ->orWhere('no_whatsapp', 'ILIKE', "%{$search}%")
                      ->orWhere('perusahaan_tamu', 'ILIKE', "%{$search}%")
                      ->orWhere('jabatan', 'ILIKE', "%{$search}%")
                      ->orWhere('keperluan', 'ILIKE', "%{$search}%")
                      ->orWhere('qr_code', 'ILIKE', "%{$search}%");
                });
            }

            // Filter by project
            if ($request->filled('project_id')) {
                $query->where('project_id', $request->project_id);
            }

            // Filter by area
            if ($request->filled('area_id')) {
                $query->where('area_id', $request->area_id);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('check_in', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('check_in', '<=', $request->date_to);
            }

            // Filter by time period
            if ($request->filled('period')) {
                switch ($request->period) {
                    case 'today':
                        $query->whereDate('check_in', today());
                        break;
                    case 'week':
                        $query->whereBetween('check_in', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'visiting':
                        $query->where('status', 'sedang_berkunjung');
                        break;
                }
            }

            $perPage = $request->get('per_page', 20);
            $bukuTamus = $query->orderBy('check_in', 'desc')
                              ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $bukuTamus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data buku tamu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new guest book entry
     */
    public function store(Request $request)
    {
        try {
            // Get project settings to determine validation rules
            $project = null;
            if ($request->filled('project_id')) {
                $project = Project::find($request->project_id);
            }
            
            // Use form data first, then project data, then fallback
            $guestBookMode = $request->guest_book_mode ?: ($project ? $project->guest_book_mode : 'simple');
            $enableQuestionnaire = $request->enable_questionnaire !== null ? 
                (bool)$request->enable_questionnaire : 
                ($project ? $project->enable_questionnaire : false);
            
            // Base validation rules that apply to both modes
            $baseRules = [
                'project_id' => 'required|exists:projects,id',
                'nama_tamu' => 'required|string|max:255',
                'perusahaan_tamu' => 'required|string|max:255',
                'keperluan' => 'required|string|max:255',
                'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'area_id' => 'required|exists:areas,id',
                'mulai_kunjungan' => 'required|date',
                'lama_kunjungan' => 'nullable|string|max:100',
            ];

            // Additional rules for MIGAS mode
            $migasRules = [
                'nik' => 'required|string|size:16|regex:/^[0-9]{16}$/',
                'tanggal_lahir' => 'required|date|before:today',
                'domisili' => 'required|string',
                'jabatan' => 'required|string|max:255',
                'foto_identitas' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'email' => 'required|email|max:255',
                'no_whatsapp' => 'required|string|max:20',
                'kontak_darurat_telepon' => 'required|string|max:20',
                'kontak_darurat_nama' => 'required|string|max:255',
                'kontak_darurat_hubungan' => 'required|string|max:100',
                'lokasi_dituju' => 'required|string|max:255',
                'selesai_kunjungan' => 'nullable|date|after:mulai_kunjungan',
            ];

            // Optional fields for both modes
            $optionalRules = [
                'area_patrol_id' => 'nullable|exists:area_patrols,id',
                'bertemu' => 'nullable|string|max:255',
                'no_kartu_pinjam' => 'nullable|string|max:50',
                'keterangan_tambahan' => 'nullable|string',
                'catatan' => 'nullable|string',
                'kuesioner_answers' => 'nullable|string', // Accept as JSON string
            ];

            // Combine rules based on mode
            if ($guestBookMode === 'migas' || $guestBookMode === 'standard_migas') {
                $rules = array_merge($baseRules, $migasRules, $optionalRules);
            } else {
                // Simple mode - make selesai_kunjungan required
                $rules = array_merge($baseRules, $optionalRules, [
                    'selesai_kunjungan' => 'required|date|after:mulai_kunjungan',
                ]);
                
                // Make some MIGAS fields optional for simple mode
                $rules = array_merge($rules, [
                    'jabatan' => 'nullable|string|max:255',
                    'nik' => 'nullable|string',
                    'tanggal_lahir' => 'nullable|date',
                    'domisili' => 'nullable|string',
                    'foto_identitas' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                    'lokasi_dituju' => 'nullable|string',
                    'email' => 'nullable|email',
                    'no_whatsapp' => 'nullable|string',
                    'kontak_darurat_telepon' => 'nullable|string',
                    'kontak_darurat_nama' => 'nullable|string',
                    'kontak_darurat_hubungan' => 'nullable|string',
                ]);
            }
            
            $validated = $request->validate($rules);

            $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
            $validated['input_by'] = auth()->id();
            $validated['status'] = 'sedang_berkunjung';
            $validated['check_in'] = $validated['mulai_kunjungan'];

            // Handle duration calculation
            if (!empty($validated['selesai_kunjungan'])) {
                // Calculate duration if end time is provided
                $mulai = new \DateTime($validated['mulai_kunjungan']);
                $selesai = new \DateTime($validated['selesai_kunjungan']);
                $diff = $mulai->diff($selesai);
                
                $hours = $diff->h + ($diff->days * 24);
                $minutes = $diff->i;
                
                $validated['lama_kunjungan'] = "{$hours} jam {$minutes} menit";
            } else {
                // For MIGAS mode, if no end time provided, leave duration empty
                $validated['lama_kunjungan'] = null;
            }

            // Set lokasi_dituju from area if not provided (for Simple mode)
            if (empty($validated['lokasi_dituju']) && !empty($validated['area_id'])) {
                $area = Area::find($validated['area_id']);
                if ($area) {
                    $validated['lokasi_dituju'] = $area->nama . ($area->alamat ? ' - ' . $area->alamat : '');
                }
            }

            // Handle photo upload
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto');
                $filename = 'foto_' . time() . '_' . Str::random(10) . '.' . $foto->getClientOriginalExtension();
                $path = $foto->storeAs('buku-tamu', $filename, 'public');
                $validated['foto'] = $path;
            }

            // Handle identity photo upload (only for Standard MIGAS mode or if file is provided)
            if ($request->hasFile('foto_identitas')) {
                $fotoIdentitas = $request->file('foto_identitas');
                $filename = 'identitas_' . time() . '_' . Str::random(10) . '.' . $fotoIdentitas->getClientOriginalExtension();
                $path = $fotoIdentitas->storeAs('buku-tamu/identitas', $filename, 'public');
                $validated['foto_identitas'] = $path;
            }

            $bukuTamu = BukuTamu::create($validated);

            // Save dynamic questionnaire answers if questionnaire is enabled and answers provided
            if ($enableQuestionnaire && !empty($validated['kuesioner_answers'])) {
                // Parse JSON if it's a string
                $answers = $validated['kuesioner_answers'];
                if (is_string($answers)) {
                    $answers = json_decode($answers, true);
                }
                
                if (is_array($answers)) {
                    foreach ($answers as $pertanyaanId => $jawaban) {
                        if (!empty($jawaban)) {
                            // Handle array answers (checkboxes)
                            if (is_array($jawaban)) {
                                $jawaban = implode(', ', $jawaban);
                            }
                            
                            JawabanKuesionerTamu::create([
                                'buku_tamu_id' => $bukuTamu->id,
                                'pertanyaan_tamu_id' => $pertanyaanId,
                                'jawaban' => $jawaban,
                            ]);
                        }
                    }
                }
            }

            // Load relationships for response
            $bukuTamu->load(['project:id,nama', 'area:id,nama', 'inputBy:id,name']);

            return response()->json([
                'success' => true,
                'message' => 'Data tamu berhasil dicatat. QR Code: ' . $bukuTamu->qr_code,
                'data' => $bukuTamu
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data tamu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified guest book entry
     */
    public function show(BukuTamu $bukuTamu)
    {
        try {
            $bukuTamu->load([
                'project:id,nama', 
                'area:id,nama,alamat', 
                'areaPatrol:id,nama',
                'inputBy:id,name',
                'jawabanKuesioner.pertanyaanTamu'
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $bukuTamu
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data tamu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified guest book entry
     */
    public function update(Request $request, BukuTamu $bukuTamu)
    {
        try {
            $validated = $request->validate([
                // Step 1: Data Diri
                'nama_tamu' => 'required|string|max:255',
                'nik' => 'nullable|string|size:16|regex:/^[0-9]{16}$/',
                'tanggal_lahir' => 'nullable|date|before:today',
                'domisili' => 'nullable|string',
                'perusahaan_tamu' => 'required|string|max:255',
                'jabatan' => 'nullable|string|max:255',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'foto_identitas' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                
                // Step 2: Kontak Tamu
                'email' => 'nullable|email|max:255',
                'no_whatsapp' => 'nullable|string|max:20',
                'kontak_darurat_telepon' => 'nullable|string|max:20',
                'kontak_darurat_nama' => 'nullable|string|max:255',
                'kontak_darurat_hubungan' => 'nullable|string|max:100',
                
                // Step 3: Data Kunjungan
                'keperluan' => 'required|string|max:255',
                'lokasi_dituju' => 'nullable|string|max:255',
                'mulai_kunjungan' => 'required|date',
                'selesai_kunjungan' => 'nullable|date|after:mulai_kunjungan',
                'lama_kunjungan' => 'nullable|string|max:100',
                
                // Optional/hidden fields
                'project_id' => 'nullable|exists:projects,id',
                'area_id' => 'required|exists:areas,id',
                'bertemu' => 'nullable|string|max:255',
                'status' => 'required|in:sedang_berkunjung,sudah_keluar',
                'no_kartu_pinjam' => 'nullable|string|max:50',
                'keterangan_tambahan' => 'nullable|string',
                'catatan' => 'nullable|string',
            ]);

            // Handle photo upload
            if ($request->hasFile('foto')) {
                // Delete old photo if exists
                if ($bukuTamu->foto) {
                    Storage::disk('public')->delete($bukuTamu->foto);
                }

                $foto = $request->file('foto');
                $filename = 'foto_' . time() . '_' . Str::random(10) . '.' . $foto->getClientOriginalExtension();
                $path = $foto->storeAs('buku-tamu', $filename, 'public');
                $validated['foto'] = $path;
            }

            // Handle identity photo upload
            if ($request->hasFile('foto_identitas')) {
                // Delete old identity photo if exists
                if ($bukuTamu->foto_identitas) {
                    Storage::disk('public')->delete($bukuTamu->foto_identitas);
                }

                $fotoIdentitas = $request->file('foto_identitas');
                $filename = 'identitas_' . time() . '_' . Str::random(10) . '.' . $fotoIdentitas->getClientOriginalExtension();
                $path = $fotoIdentitas->storeAs('buku-tamu/identitas', $filename, 'public');
                $validated['foto_identitas'] = $path;
            }

            // Handle status change
            if ($validated['status'] === 'sudah_keluar' && $bukuTamu->status === 'sedang_berkunjung') {
                $validated['check_out'] = now();
            } elseif ($validated['status'] === 'sedang_berkunjung' && $bukuTamu->status === 'sudah_keluar') {
                $validated['check_out'] = null;
            }

            // Update check_in if mulai_kunjungan changed
            if ($bukuTamu->mulai_kunjungan != $validated['mulai_kunjungan']) {
                $validated['check_in'] = $validated['mulai_kunjungan'];
            }

            $bukuTamu->update($validated);

            // Load relationships for response
            $bukuTamu->load(['project:id,nama', 'area:id,nama', 'inputBy:id,name']);

            return response()->json([
                'success' => true,
                'message' => 'Data tamu berhasil diupdate',
                'data' => $bukuTamu
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data tamu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified guest book entry
     */
    public function destroy(BukuTamu $bukuTamu)
    {
        try {
            // Delete photos if exist
            if ($bukuTamu->foto) {
                Storage::disk('public')->delete($bukuTamu->foto);
            }
            if ($bukuTamu->foto_identitas) {
                Storage::disk('public')->delete($bukuTamu->foto_identitas);
            }

            $bukuTamu->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data tamu berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data tamu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check out guest
     */
    public function checkOut(Request $request, BukuTamu $bukuTamu)
    {
        try {
            $validated = $request->validate([
                'catatan' => 'nullable|string',
            ]);

            if ($bukuTamu->status === 'sudah_keluar') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamu sudah check out sebelumnya'
                ], 400);
            }

            $bukuTamu->update([
                'status' => 'sudah_keluar',
                'check_out' => now(),
                'catatan' => $validated['catatan'] ?? $bukuTamu->catatan
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tamu berhasil check out',
                'data' => [
                    'status' => 'sudah_keluar',
                    'check_out' => $bukuTamu->check_out->format('d M Y H:i'),
                    'duration' => $bukuTamu->duration ?? 'Tidak diketahui',
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal check out tamu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get guest by QR code
     */
    public function getByQrCode(Request $request)
    {
        try {
            $validated = $request->validate([
                'qr_code' => 'required|string',
            ]);

            $bukuTamu = BukuTamu::where('qr_code', $validated['qr_code'])
                               ->with(['project:id,nama', 'area:id,nama', 'inputBy:id,name'])
                               ->first();

            if (!$bukuTamu) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $bukuTamu,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get project settings for guest book
     */
    public function getProjectSettings(Request $request)
    {
        try {
            $projectId = $request->get('project_id');
            
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID required'
                ], 400);
            }

            $project = Project::find($projectId);

            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $project->id,
                    'nama' => $project->nama,
                    'guest_book_mode' => $project->guest_book_mode,
                    'enable_questionnaire' => $project->enable_questionnaire,
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get questionnaire by project and area
     */
    public function getKuesionerByArea(Request $request)
    {
        try {
            $projectId = $request->get('project_id');
            $areaId = $request->get('area_id');
            
            if (!$projectId || !$areaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID and Area ID required'
                ], 400);
            }

            // Find active kuesioner for this project and area
            $kuesioner = KuesionerTamu::with(['pertanyaans' => function($query) {
                    $query->orderBy('urutan');
                }])
                ->where('project_id', $projectId)
                ->where('area_id', $areaId)
                ->where('is_active', true)
                ->first();

            \Log::info('API Questionnaire search', [
                'project_id' => $projectId,
                'area_id' => $areaId,
                'found' => $kuesioner ? true : false,
                'kuesioner_id' => $kuesioner ? $kuesioner->id : null,
                'questions_count' => $kuesioner && $kuesioner->pertanyaans ? $kuesioner->pertanyaans->count() : 0
            ]);

            if (!$kuesioner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ini belum memiliki kuesioner. Harap segera membuat kuesioner untuk area ini.',
                    'error_type' => 'no_questionnaire'
                ], 404);
            }

            if (!$kuesioner->pertanyaans || $kuesioner->pertanyaans->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuesioner ditemukan tetapi belum memiliki pertanyaan. Harap tambahkan pertanyaan ke kuesioner.',
                    'error_type' => 'no_questions'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $kuesioner->id,
                    'judul' => $kuesioner->judul,
                    'deskripsi' => $kuesioner->deskripsi,
                    'pertanyaans' => $kuesioner->pertanyaans->map(function($pertanyaan) {
                        return [
                            'id' => $pertanyaan->id,
                            'pertanyaan' => $pertanyaan->pertanyaan,
                            'tipe_jawaban' => $pertanyaan->tipe_jawaban,
                            'opsi_jawaban' => $pertanyaan->opsi_jawaban,
                            'is_required' => $pertanyaan->is_required,
                            'urutan' => $pertanyaan->urutan,
                        ];
                    })
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save questionnaire answers for existing guest
     */
    public function saveGuestQuestionnaire(Request $request, BukuTamu $bukuTamu)
    {
        try {
            $validated = $request->validate([
                'kuesioner_answers' => 'required|array',
            ]);

            // Parse answers
            $answers = $validated['kuesioner_answers'];
            
            // Validate that all question IDs exist
            $questionIds = array_keys($answers);
            $existingQuestionIds = \App\Models\PertanyaanTamu::whereIn('id', $questionIds)->pluck('id')->toArray();
            $invalidQuestionIds = array_diff($questionIds, $existingQuestionIds);
            
            if (!empty($invalidQuestionIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pertanyaan dengan ID ' . implode(', ', $invalidQuestionIds) . ' tidak ditemukan. Harap pastikan kuesioner sudah dibuat dengan benar.',
                    'error_type' => 'invalid_question_ids',
                    'invalid_ids' => $invalidQuestionIds
                ], 400);
            }
            
            // Delete existing answers for this guest
            JawabanKuesionerTamu::where('buku_tamu_id', $bukuTamu->id)->delete();
            
            // Save new answers
            foreach ($answers as $pertanyaanId => $jawaban) {
                if (!empty($jawaban)) {
                    // Handle array answers (checkboxes)
                    if (is_array($jawaban)) {
                        $jawaban = implode(', ', $jawaban);
                    }
                    
                    JawabanKuesionerTamu::create([
                        'buku_tamu_id' => $bukuTamu->id,
                        'pertanyaan_tamu_id' => $pertanyaanId,
                        'jawaban' => $jawaban,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Jawaban kuesioner berhasil disimpan'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan jawaban: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get statistics
     */
    public function getStatistics(Request $request)
    {
        try {
            $stats = [
                'total_today' => BukuTamu::whereDate('check_in', today())->count(),
                'visiting_now' => BukuTamu::where('status', 'sedang_berkunjung')->count(),
                'total_week' => BukuTamu::whereBetween('check_in', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'total_all' => BukuTamu::count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available guest cards
     */
    public function getAvailableCards(Request $request)
    {
        try {
            $projectId = $request->get('project_id');
            
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID required'
                ], 400);
            }

            // Get available cards for the project
            $availableCards = \App\Models\KartuTamu::where('project_id', $projectId)
                ->available()
                ->select('id', 'no_kartu', 'nfc_kartu', 'keterangan')
                ->orderBy('no_kartu')
                ->get();

            // Check if no cards available
            if ($availableCards->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ini belum memiliki kartu tamu atau semua kartu sedang terpakai. Harap tambahkan kartu tamu atau tunggu hingga ada kartu yang dikembalikan.',
                    'error_type' => 'no_available_cards'
                ], 404);
            }

            // Transform the response to use hash_id instead of id
            $cardsData = $availableCards->map(function($card) {
                return [
                    'id' => $card->hash_id, // Use hash_id instead of numeric id
                    'no_kartu' => $card->no_kartu,
                    'nfc_kartu' => $card->nfc_kartu,
                    'keterangan' => $card->keterangan
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $cardsData,
                'meta' => [
                    'total_available' => $availableCards->count(),
                    'project_id' => $projectId
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting available cards', [
                'project_id' => $request->get('project_id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign guest card to guest
     */
    public function assignCard(Request $request, BukuTamu $bukuTamu)
    {
        try {
            $validated = $request->validate([
                'no_kartu' => 'required|string|exists:kartu_tamus,no_kartu',
            ]);

            // Check if guest already has a card
            if ($bukuTamu->no_kartu_pinjam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamu sudah memiliki kartu: ' . $bukuTamu->no_kartu_pinjam
                ], 400);
            }

            // Find the card and check if it's available
            $kartuTamu = \App\Models\KartuTamu::where('no_kartu', $validated['no_kartu'])->first();
            
            if (!$kartuTamu) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu tidak ditemukan'
                ], 404);
            }

            if ($kartuTamu->status !== 'tersedia') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kartu tidak tersedia. Status: ' . $kartuTamu->status
                ], 400);
            }

            // Assign card to guest
            $kartuTamu->assignToGuest($bukuTamu->nama_tamu, $bukuTamu->perusahaan_tamu);

            // Update guest record
            $bukuTamu->update([
                'no_kartu_pinjam' => $validated['no_kartu']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kartu berhasil diberikan kepada tamu',
                'data' => [
                    'no_kartu' => $validated['no_kartu'],
                    'nama_tamu' => $bukuTamu->nama_tamu,
                    'status_kartu' => 'dipinjam'
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Return guest card
     */
    public function returnCard(Request $request, BukuTamu $bukuTamu)
    {
        try {
            if (!$bukuTamu->no_kartu_pinjam) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tamu tidak memiliki kartu yang dipinjam'
                ], 400);
            }

            // Find the card and return it
            $kartuTamu = \App\Models\KartuTamu::where('no_kartu', $bukuTamu->no_kartu_pinjam)->first();
            if ($kartuTamu) {
                $kartuTamu->returnFromGuest();
            }

            // Clear card from guest record
            $bukuTamu->update([
                'no_kartu_pinjam' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kartu berhasil dikembalikan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}