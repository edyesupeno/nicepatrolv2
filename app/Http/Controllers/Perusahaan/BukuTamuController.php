<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\BukuTamu;
use App\Models\Project;
use App\Models\User;
use App\Models\KuesionerTamu;
use App\Models\AreaPatrol;
use App\Models\JawabanKuesionerTamu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BukuTamuController extends Controller
{
    public function index(Request $request)
    {
        $query = BukuTamu::with(['project', 'area', 'inputBy']);

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
                  ->orWhere('bertemu', 'ILIKE', "%{$search}%")
                  ->orWhere('lokasi_dituju', 'ILIKE', "%{$search}%")
                  ->orWhere('qr_code', 'ILIKE', "%{$search}%")
                  ->orWhere('no_kartu_pinjam', 'ILIKE', "%{$search}%");
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
                    $query->today();
                    break;
                case 'week':
                    $query->thisWeek();
                    break;
                case 'visiting':
                    $query->visiting();
                    break;
            }
        }

        $bukuTamus = $query->orderBy('check_in', 'desc')
                          ->paginate(15);

        $projects = Project::where('is_active', true)->get();
        $areas = \App\Models\Area::get();

        // Statistics
        $stats = [
            'total_today' => BukuTamu::today()->count(),
            'visiting_now' => BukuTamu::visiting()->count(),
            'total_week' => BukuTamu::thisWeek()->count(),
            'total_all' => BukuTamu::count(),
        ];

        return view('perusahaan.buku-tamu.index', compact('bukuTamus', 'projects', 'areas', 'stats'));
    }

    public function create()
    {
        $projects = Project::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        $areas = \App\Models\Area::with('project')->orderBy('nama')->get();
        $areaPatrols = AreaPatrol::with(['project', 'kuesionerTamus.pertanyaans'])
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('perusahaan.buku-tamu.create', compact(
            'projects', 
            'users', 
            'areas', 
            'areaPatrols'
        ));
    }

    public function store(Request $request)
    {
        // Debug: Log request data
        \Log::info('Buku Tamu Store Request:', [
            'all_data' => $request->all(),
            'project_id' => $request->project_id,
            'guest_book_mode' => $request->guest_book_mode,
            'enable_questionnaire' => $request->enable_questionnaire
        ]);

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
        
        // Debug: Log determined mode
        \Log::info('Determined Mode:', [
            'project_found' => $project ? true : false,
            'guest_book_mode' => $guestBookMode,
            'enable_questionnaire' => $enableQuestionnaire,
            'form_mode' => $request->guest_book_mode,
            'form_questionnaire' => $request->enable_questionnaire
        ]);
        
        // Base validation rules (always required for both modes)
        $rules = [
            // Project selection (now required)
            'project_id' => 'required|exists:projects,id',
            
            // Step 1: Data Diri (always required)
            'nama_tamu' => 'required|string|max:255',
            'perusahaan_tamu' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            
            // Step 3: Data Kunjungan (always required)
            'keperluan' => 'required|string|max:255',
            'mulai_kunjungan' => 'required|date',
            'selesai_kunjungan' => 'required|date|after:mulai_kunjungan',
            'lama_kunjungan' => 'required|string|max:100',
            
            // Optional fields
            'area_id' => 'required|exists:areas,id',
            'area_patrol_id' => 'nullable|exists:area_patrols,id',
            'bertemu' => 'nullable|string|max:255',
            'no_kartu_pinjam' => 'nullable|string|max:50',
            'keterangan_tambahan' => 'nullable|string',
            'catatan' => 'nullable|string',
            
            // Dynamic questionnaire answers
            'kuesioner_answers' => 'nullable|array',
            'kuesioner_answers.*' => 'nullable|string',
        ];
        
        // Simplified validation for debugging
        $rules = [
            'project_id' => 'required|exists:projects,id',
            'nama_tamu' => 'required|string|max:255',
            'perusahaan_tamu' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'keperluan' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'mulai_kunjungan' => 'required|date',
            'selesai_kunjungan' => 'required|date|after:mulai_kunjungan',
            'lama_kunjungan' => 'required|string|max:100',
            
            // Make everything else optional for now
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
            'area_patrol_id' => 'nullable|exists:area_patrols,id',
            'bertemu' => 'nullable|string',
            'no_kartu_pinjam' => 'nullable|string',
            'keterangan_tambahan' => 'nullable|string',
            'catatan' => 'nullable|string',
            'kuesioner_answers' => 'nullable|array',
            'kuesioner_answers.*' => 'nullable|string',
        ];
        
        // Debug: Log validation rules
        \Log::info('Validation Rules:', $rules);
        
        // Simplified validation messages
        $messages = [
            'project_id.required' => 'Project wajib dipilih',
            'nama_tamu.required' => 'Nama tamu wajib diisi',
            'perusahaan_tamu.required' => 'Instansi wajib diisi',
            'jabatan.required' => 'Jabatan wajib diisi',
            'foto.required' => 'Foto selfie wajib diupload',
            'keperluan.required' => 'Maksud & tujuan wajib diisi',
            'area_id.required' => 'Area/lokasi wajib dipilih',
            'mulai_kunjungan.required' => 'Waktu mulai kunjungan wajib diisi',
            'selesai_kunjungan.required' => 'Waktu selesai kunjungan wajib diisi',
            'lama_kunjungan.required' => 'Lama kunjungan wajib diisi',
        ];

        try {
            $validated = $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['input_by'] = auth()->id();
        $validated['status'] = 'sedang_berkunjung';
        $validated['check_in'] = $validated['mulai_kunjungan'];

        // Set lokasi_dituju from area if not provided (for Simple mode)
        if (empty($validated['lokasi_dituju']) && !empty($validated['area_id'])) {
            $area = \App\Models\Area::find($validated['area_id']);
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
            foreach ($validated['kuesioner_answers'] as $pertanyaanId => $jawaban) {
                if (!empty($jawaban)) {
                    JawabanKuesionerTamu::create([
                        'buku_tamu_id' => $bukuTamu->id,
                        'pertanyaan_tamu_id' => $pertanyaanId,
                        'jawaban' => is_array($jawaban) ? implode(', ', $jawaban) : $jawaban,
                    ]);
                }
            }
        }

        return redirect()->route('perusahaan.buku-tamu.index')
            ->with('success', 'Data tamu berhasil dicatat. QR Code: ' . $bukuTamu->qr_code);
    }

    public function show(BukuTamu $bukuTamu)
    {
        $bukuTamu->load([
            'project', 
            'area', 
            'areaPatrol',
            'inputBy',
            'jawabanKuesioner.pertanyaanTamu'
        ]);
        
        return view('perusahaan.buku-tamu.show', compact('bukuTamu'));
    }

    public function edit(BukuTamu $bukuTamu)
    {
        $projects = Project::where('is_active', true)->get();
        $users = User::where('is_active', true)->get();
        $areas = \App\Models\Area::with('project')->orderBy('nama')->get();

        return view('perusahaan.buku-tamu.edit', compact('bukuTamu', 'projects', 'users', 'areas'));
    }

    public function update(Request $request, BukuTamu $bukuTamu)
    {
        $validated = $request->validate([
            // Step 1: Data Diri
            'nama_tamu' => 'required|string|max:255',
            'nik' => 'required|string|size:16|regex:/^[0-9]{16}$/',
            'tanggal_lahir' => 'required|date|before:today',
            'domisili' => 'required|string',
            'perusahaan_tamu' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_identitas' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Step 2: Kontak Tamu
            'email' => 'required|email|max:255',
            'no_whatsapp' => 'required|string|max:20',
            'kontak_darurat_telepon' => 'required|string|max:20',
            'kontak_darurat_nama' => 'required|string|max:255',
            'kontak_darurat_hubungan' => 'required|string|max:100',
            
            // Step 3: Data Kunjungan
            'keperluan' => 'required|string|max:255',
            'lokasi_dituju' => 'required|string|max:255',
            'mulai_kunjungan' => 'required|date',
            'selesai_kunjungan' => 'required|date|after:mulai_kunjungan',
            'lama_kunjungan' => 'required|string|max:100',
            
            // Step 4: Kuesioner
            'pertanyaan_1' => 'required|in:Ya,Tidak',
            'pertanyaan_2' => 'nullable|array',
            'pertanyaan_2.*' => 'string|max:255',
            'pertanyaan_3' => 'required|in:Ya,Tidak',
            
            // Optional/hidden fields
            'project_id' => 'nullable|exists:projects,id',
            'area_id' => 'required|exists:areas,id',
            'bertemu' => 'nullable|string|max:255',
            'status' => 'required|in:sedang_berkunjung,sudah_keluar',
            'no_kartu_pinjam' => 'nullable|string|max:50',
            'keterangan_tambahan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ], [
            // Step 1 validation messages
            'nama_tamu.required' => 'Nama tamu wajib diisi',
            'nik.required' => 'NIK wajib diisi',
            'nik.size' => 'NIK harus 16 digit',
            'nik.regex' => 'NIK harus berupa angka 16 digit',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini',
            'domisili.required' => 'Domisili wajib diisi',
            'perusahaan_tamu.required' => 'Instansi wajib diisi',
            'jabatan.required' => 'Jabatan wajib diisi',
            'foto.image' => 'File foto harus berupa gambar',
            'foto.mimes' => 'Format foto harus jpeg, png, atau jpg',
            'foto.max' => 'Ukuran foto maksimal 2MB',
            'foto_identitas.image' => 'File foto identitas harus berupa gambar',
            'foto_identitas.mimes' => 'Format foto identitas harus jpeg, png, atau jpg',
            'foto_identitas.max' => 'Ukuran foto identitas maksimal 2MB',
            
            // Step 2 validation messages
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'no_whatsapp.required' => 'Nomor WhatsApp wajib diisi',
            'kontak_darurat_telepon.required' => 'Kontak darurat wajib diisi',
            'kontak_darurat_nama.required' => 'Nama kontak darurat wajib diisi',
            'kontak_darurat_hubungan.required' => 'Hubungan kontak darurat wajib dipilih',
            
            // Step 3 validation messages
            'keperluan.required' => 'Maksud & tujuan wajib diisi',
            'lokasi_dituju.required' => 'Lokasi yang dituju wajib diisi',
            'mulai_kunjungan.required' => 'Waktu mulai kunjungan wajib diisi',
            'selesai_kunjungan.required' => 'Waktu selesai kunjungan wajib diisi',
            'selesai_kunjungan.after' => 'Waktu selesai harus setelah waktu mulai',
            'lama_kunjungan.required' => 'Lama kunjungan wajib diisi',
            
            // Step 4 validation messages
            'pertanyaan_1.required' => 'Pertanyaan 1 wajib dijawab',
            'pertanyaan_3.required' => 'Pertanyaan 3 wajib dijawab',
            
            // Other validation messages
            'status.required' => 'Status wajib dipilih',
            'area_id.required' => 'Area/lokasi wajib dipilih',
            'area_id.exists' => 'Area/lokasi tidak valid',
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

        return redirect()->route('perusahaan.buku-tamu.index')
            ->with('success', 'Data tamu berhasil diupdate');
    }

    public function destroy(BukuTamu $bukuTamu)
    {
        // Delete photos if exist
        if ($bukuTamu->foto) {
            Storage::disk('public')->delete($bukuTamu->foto);
        }
        if ($bukuTamu->foto_identitas) {
            Storage::disk('public')->delete($bukuTamu->foto_identitas);
        }

        $bukuTamu->delete();

        return redirect()->route('perusahaan.buku-tamu.index')
            ->with('success', 'Data tamu berhasil dihapus');
    }

    /**
     * Check out guest
     */
    public function checkOut(Request $request, BukuTamu $bukuTamu)
    {
        $validated = $request->validate([
            'catatan' => 'nullable|string',
        ]);

        $bukuTamu->checkOut($validated['catatan'] ?? null);

        return response()->json([
            'success' => true,
            'message' => 'Tamu berhasil check out',
            'data' => [
                'status' => $bukuTamu->status_label,
                'check_out' => $bukuTamu->check_out->format('d M Y H:i'),
                'duration' => $bukuTamu->duration,
            ]
        ]);
    }

    /**
     * Get guest by QR code
     */
    public function getByQrCode(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $bukuTamu = BukuTamu::where('qr_code', $validated['qr_code'])
                           ->with(['project', 'inputBy'])
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
    }

    /**
     * Generate QR Code image
     */
    public function generateQrCode(BukuTamu $bukuTamu)
    {
        $bukuTamu->load([
            'project', 
            'area',
            'perusahaan:id,nama,logo',
            'inputBy:id,name'
        ]);
        
        return view('perusahaan.buku-tamu.qr-code', compact('bukuTamu'));
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
            \Log::error('Error in getProjectSettings:', [
                'error' => $e->getMessage(),
                'project_id' => $request->get('project_id')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kuesioner by project only (new method for simplified flow)
     */
    public function getKuesionerByProject(Request $request)
    {
        try {
            $projectId = $request->get('project_id');
            
            \Log::info('getKuesionerByProject called', [
                'project_id' => $projectId,
                'user_id' => auth()->id(),
                'perusahaan_id' => auth()->user()->perusahaan_id ?? null
            ]);
            
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID required'
                ]);
            }

            // Find active kuesioner for this project
            $kuesioner = KuesionerTamu::with(['pertanyaans' => function($query) {
                    $query->orderBy('urutan');
                }])
                ->where('project_id', $projectId)
                ->where('is_active', true)
                ->first();

            \Log::info('Kuesioner search result', [
                'found' => $kuesioner ? true : false,
                'kuesioner_id' => $kuesioner ? $kuesioner->id : null,
                'pertanyaans_count' => $kuesioner && $kuesioner->pertanyaans ? $kuesioner->pertanyaans->count() : 0
            ]);

            if (!$kuesioner || !$kuesioner->pertanyaans || $kuesioner->pertanyaans->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuesioner tidak ditemukan untuk project ini'
                ]);
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
            \Log::error('Error in getKuesionerByProject', [
                'error' => $e->getMessage(),
                'project_id' => $request->get('project_id'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get kuesioner by project and area patrol
     */
    public function getKuesionerByProjectAndArea(Request $request)
    {
        $projectId = $request->get('project_id');
        $areaPatrolId = $request->get('area_patrol_id');
        
        if (!$projectId || !$areaPatrolId) {
            return response()->json([
                'success' => false,
                'message' => 'Project ID and Area Patrol ID required'
            ]);
        }

        $kuesioner = KuesionerTamu::with('pertanyaans')
            ->where('project_id', $projectId)
            ->where('area_patrol_id', $areaPatrolId)
            ->where('is_active', true)
            ->first();

        if (!$kuesioner) {
            return response()->json([
                'success' => false,
                'message' => 'Kuesioner tidak ditemukan untuk project dan area ini'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $kuesioner
        ]);
    }

    /**
     * Get kuesioner by area patrol (legacy method - now updated)
     */
    public function getKuesionerByArea(Request $request)
    {
        $areaPatrolId = $request->get('area_patrol_id');
        
        if (!$areaPatrolId) {
            return response()->json([
                'success' => false,
                'message' => 'Area patrol ID required'
            ]);
        }

        $kuesioner = KuesionerTamu::with('pertanyaans')
            ->where('area_patrol_id', $areaPatrolId)
            ->where('is_active', true)
            ->first();

        if (!$kuesioner) {
            return response()->json([
                'success' => false,
                'message' => 'Kuesioner tidak ditemukan untuk area ini'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kuesioner' => $kuesioner,
                'pertanyaans' => $kuesioner->pertanyaans
            ]
        ]);
    }
}