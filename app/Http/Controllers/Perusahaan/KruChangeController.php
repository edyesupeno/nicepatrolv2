<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\KruChange;
use App\Models\KruChangeQuestionnaireAnswer;
use App\Models\KruChangeTrackingAnswer;
use App\Models\TimPatroli;
use App\Models\Project;
use App\Models\AreaPatrol;
use App\Models\Shift;
use App\Models\User;
use App\Models\KuesionerPatroli;
use App\Models\PertanyaanKuesioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KruChangeController extends Controller
{
    public function index(Request $request)
    {
        $query = KruChange::with([
                'project:id,nama',
                'areaPatrol:id,nama',
                'timKeluar:id,nama_tim,jenis_regu',
                'timMasuk:id,nama_tim,jenis_regu',
                'petugasKeluar:id,name',
                'petugasMasuk:id,name'
            ]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('areaPatrol', function($sq) use ($search) {
                    $sq->where('nama', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('timKeluar', function($sq) use ($search) {
                    $sq->where('nama_tim', 'ILIKE', "%{$search}%");
                })
                ->orWhereHas('timMasuk', function($sq) use ($search) {
                    $sq->where('nama_tim', 'ILIKE', "%{$search}%");
                });
            });
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by area
        if ($request->filled('area_id')) {
            $query->where('area_patrol_id', $request->area_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('tanggal')) {
            $query->whereDate('waktu_mulai_handover', $request->tanggal);
        }

        $kruChanges = $query->latest('waktu_mulai_handover')->paginate(15)->withQueryString();
        
        $projects = Project::select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        $areas = AreaPatrol::select('id', 'nama', 'project_id')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('perusahaan.kru-change.index', compact('kruChanges', 'projects', 'areas'));
    }

    public function create()
    {
        $projects = Project::select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('perusahaan.kru-change.create', compact('projects'));
    }

    public function getDataByProject(Request $request, $projectId)
    {
        try {
            $perusahaanId = auth()->user()->perusahaan_id ?? null;
            
            if (!$perusahaanId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki perusahaan yang valid',
                ], 401);
            }

            // Validate project
            $project = Project::where('id', $projectId)
                ->where('perusahaan_id', $perusahaanId)
                ->first();
                
            if (!$project) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project tidak ditemukan',
                ], 404);
            }

            // Get areas
            $areas = AreaPatrol::select('id', 'nama')
                ->where('project_id', $projectId)
                ->where('is_active', true)
                ->orderBy('nama')
                ->get();

            // Get active patrol teams for this project with their members
            $timPatrolis = TimPatroli::select('id', 'nama_tim', 'jenis_regu', 'shift_id')
                ->with([
                    'shift:id,nama_shift,jam_mulai,jam_selesai',
                    'anggotaAktif' => function($query) {
                        $query->select('id', 'tim_patroli_id', 'user_id', 'role')
                              ->orderBy('role') // leader first, then wakil_leader, then anggota
                              ->with('user:id,name,email');
                    }
                ])
                ->where('project_id', $projectId)
                ->where('is_active', true)
                ->orderBy('nama_tim')
                ->get();

            // Get shifts
            $shifts = Shift::select('id', 'nama_shift', 'jam_mulai', 'jam_selesai')
                ->where('project_id', $projectId)
                ->orderBy('jam_mulai')
                ->get();

            // Get all security officers for supervisor selection
            $securityOfficers = User::select('id', 'name', 'email')
                ->where('perusahaan_id', $perusahaanId)
                ->where('role', 'security_officer')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'areas' => $areas,
                'tim_patrolis' => $timPatrolis,
                'shifts' => $shifts,
                'security_officers' => $securityOfficers,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getDataByProject: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getKuesionerByTim(Request $request, $timId)
    {
        try {
            $tim = TimPatroli::with(['kuesioners.pertanyaans' => function($query) {
                $query->orderBy('urutan');
            }])->find($timId);

            if (!$tim) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tim patroli tidak ditemukan',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'kuesioners' => $tim->kuesioners,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getKuesionerByTim: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil kuesioner: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'area_patrol_id' => 'required|exists:area_patrols,id',
            'tim_keluar_id' => 'required|exists:tim_patrolis,id',
            'tim_masuk_id' => 'required|exists:tim_patrolis,id|different:tim_keluar_id',
            'waktu_mulai_handover' => 'required|date',
            'petugas_keluar_ids' => 'required|array|min:1',
            'petugas_keluar_ids.*' => 'exists:users,id',
            'petugas_masuk_ids' => 'nullable|array',
            'petugas_masuk_ids.*' => 'exists:users,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'catatan_keluar' => 'nullable|string|max:1000',
            'foto_tim_keluar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_tim_masuk' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'area_patrol_id.required' => 'Area patroli wajib dipilih',
            'tim_keluar_id.required' => 'Tim keluar wajib dipilih',
            'tim_masuk_id.required' => 'Tim masuk wajib dipilih',
            'tim_masuk_id.different' => 'Tim masuk harus berbeda dengan tim keluar',
            'waktu_mulai_handover.required' => 'Waktu handover wajib diisi',
            'petugas_keluar_ids.required' => 'Minimal satu petugas keluar wajib dipilih',
            'petugas_keluar_ids.min' => 'Minimal satu petugas keluar wajib dipilih',
            'foto_tim_keluar.image' => 'Foto tim keluar harus berupa gambar',
            'foto_tim_keluar.mimes' => 'Foto tim keluar harus berformat jpeg, png, atau jpg',
            'foto_tim_keluar.max' => 'Foto tim keluar maksimal 2MB',
            'foto_tim_masuk.image' => 'Foto tim masuk harus berupa gambar',
            'foto_tim_masuk.mimes' => 'Foto tim masuk harus berformat jpeg, png, atau jpg',
            'foto_tim_masuk.max' => 'Foto tim masuk maksimal 2MB',
        ]);

        // Get shift info from teams
        $timKeluar = TimPatroli::find($validated['tim_keluar_id']);
        $timMasuk = TimPatroli::find($validated['tim_masuk_id']);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['shift_keluar_id'] = $timKeluar->shift_id;
        $validated['shift_masuk_id'] = $timMasuk->shift_id;
        $validated['status'] = 'pending';
        
        // Set first petugas for backward compatibility
        $validated['petugas_keluar_id'] = $validated['petugas_keluar_ids'][0] ?? null;
        $validated['petugas_masuk_id'] = $validated['petugas_masuk_ids'][0] ?? null;

        // Handle photo uploads
        if ($request->hasFile('foto_tim_keluar')) {
            $fotoKeluarPath = $request->file('foto_tim_keluar')->store('kru-change/tim-keluar', 'public');
            $validated['foto_tim_keluar'] = $fotoKeluarPath;
        }

        if ($request->hasFile('foto_tim_masuk')) {
            $fotoMasukPath = $request->file('foto_tim_masuk')->store('kru-change/tim-masuk', 'public');
            $validated['foto_tim_masuk'] = $fotoMasukPath;
        }

        $kruChange = KruChange::create($validated);

        return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
            ->with('success', 'Kru change berhasil dijadwalkan');
    }

    public function show(KruChange $kruChange)
    {
        $kruChange->load([
            'project:id,nama',
            'areaPatrol:id,nama',
            'timKeluar:id,nama_tim,jenis_regu',
            'timMasuk:id,nama_tim,jenis_regu',
            'shiftKeluar:id,nama_shift,jam_mulai,jam_selesai',
            'shiftMasuk:id,nama_shift,jam_mulai,jam_selesai',
            'petugasKeluar:id,name,email',
            'petugasMasuk:id,name,email',
            'supervisor:id,name,email',
            'questionnaireAnswers.kuesionerPatroli:id,judul',
            'questionnaireAnswers.pertanyaanKuesioner:id,pertanyaan',
            'questionnaireAnswers.user:id,name'
        ]);

        // Get questionnaires for both teams
        $kuesionerKeluar = $kruChange->timKeluar->kuesioners()->with(['pertanyaans' => function($query) {
            $query->orderBy('urutan');
        }])->get();

        $kuesionerMasuk = $kruChange->timMasuk->kuesioners()->with(['pertanyaans' => function($query) {
            $query->orderBy('urutan');
        }])->get();

        return view('perusahaan.kru-change.show', compact('kruChange', 'kuesionerKeluar', 'kuesionerMasuk'));
    }

    public function edit(KruChange $kruChange)
    {
        if ($kruChange->status !== 'pending') {
            return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
                ->with('error', 'Hanya kru change dengan status pending yang dapat diedit');
        }

        $kruChange->load([
            'project',
            'areaPatrol',
            'timKeluar',
            'timMasuk',
            'petugasKeluar',
            'petugasMasuk',
            'supervisor'
        ]);

        $projects = Project::select('id', 'nama')
            ->where('is_active', true)
            ->orderBy('nama')
            ->get();

        return view('perusahaan.kru-change.edit', compact('kruChange', 'projects'));
    }

    public function update(Request $request, KruChange $kruChange)
    {
        if ($kruChange->status !== 'pending') {
            return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
                ->with('error', 'Hanya kru change dengan status pending yang dapat diedit');
        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'area_patrol_id' => 'required|exists:area_patrols,id',
            'tim_keluar_id' => 'required|exists:tim_patrolis,id',
            'tim_masuk_id' => 'required|exists:tim_patrolis,id|different:tim_keluar_id',
            'waktu_mulai_handover' => 'required|date',
            'petugas_keluar_ids' => 'required|array|min:1',
            'petugas_keluar_ids.*' => 'exists:users,id',
            'petugas_masuk_ids' => 'nullable|array',
            'petugas_masuk_ids.*' => 'exists:users,id',
            'supervisor_id' => 'nullable|exists:users,id',
            'catatan_keluar' => 'nullable|string|max:1000',
        ]);

        // Get shift info from teams
        $timKeluar = TimPatroli::find($validated['tim_keluar_id']);
        $timMasuk = TimPatroli::find($validated['tim_masuk_id']);

        $validated['shift_keluar_id'] = $timKeluar->shift_id;
        $validated['shift_masuk_id'] = $timMasuk->shift_id;
        
        // Set first petugas for backward compatibility
        $validated['petugas_keluar_id'] = $validated['petugas_keluar_ids'][0] ?? null;
        $validated['petugas_masuk_id'] = $validated['petugas_masuk_ids'][0] ?? null;

        $kruChange->update($validated);

        return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
            ->with('success', 'Kru change berhasil diupdate');
    }

    public function destroy(KruChange $kruChange)
    {
        if ($kruChange->status === 'completed') {
            return redirect()->route('perusahaan.kru-change.index')
                ->with('error', 'Kru change yang sudah selesai tidak dapat dihapus');
        }

        DB::transaction(function () use ($kruChange) {
            // Delete questionnaire answers
            $kruChange->questionnaireAnswers()->delete();
            
            // Delete kru change
            $kruChange->delete();
        });

        return redirect()->route('perusahaan.kru-change.index')
            ->with('success', 'Kru change berhasil dihapus');
    }

    public function startHandover(KruChange $kruChange)
    {
        if (!$kruChange->canBeStarted()) {
            return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
                ->with('error', 'Handover tidak dapat dimulai. Pastikan petugas masuk sudah ditentukan dan waktu handover sudah tiba.');
        }

        $kruChange->startHandover();

        return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
            ->with('success', 'Handover berhasil dimulai');
    }

    public function completeHandover(KruChange $kruChange)
    {
        if (!$kruChange->canBeCompleted()) {
            return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
                ->with('error', 'Handover tidak dapat diselesaikan. Pastikan semua approval sudah diberikan.');
        }

        $kruChange->completeHandover();

        return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
            ->with('success', 'Handover berhasil diselesaikan');
    }

    public function cancelHandover(Request $request, KruChange $kruChange)
    {
        $validated = $request->validate([
            'alasan' => 'required|string|max:500'
        ], [
            'alasan.required' => 'Alasan pembatalan wajib diisi'
        ]);

        $kruChange->cancelHandover($validated['alasan']);

        return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
            ->with('success', 'Handover berhasil dibatalkan');
    }

    public function approve(Request $request, KruChange $kruChange)
    {
        $validated = $request->validate([
            'tipe_approval' => 'required|in:keluar,masuk,supervisor',
            'catatan' => 'nullable|string|max:500'
        ]);

        $field = 'approved_' . $validated['tipe_approval'];
        $catatanField = 'catatan_' . $validated['tipe_approval'];

        $updateData = [$field => true];
        if ($validated['catatan']) {
            $updateData[$catatanField] = $validated['catatan'];
        }

        $kruChange->update($updateData);

        $message = match($validated['tipe_approval']) {
            'keluar' => 'Approval tim keluar berhasil diberikan',
            'masuk' => 'Approval tim masuk berhasil diberikan',
            'supervisor' => 'Approval supervisor berhasil diberikan',
        };

        return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
            ->with('success', $message);
    }

    public function updateInventaris(Request $request, KruChange $kruChange)
    {
        $validated = $request->validate([
            'inventaris_id' => 'required|integer',
            'status' => 'required|in:checked,missing,damaged',
            'catatan' => 'nullable|string|max:500'
        ]);

        $kruChange->updateInventarisStatus(
            $validated['inventaris_id'],
            $validated['status'],
            $validated['catatan'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Status inventaris berhasil diupdate',
            'completion_percentage' => $kruChange->getInventarisCompletionPercentage()
        ]);
    }

    public function updateKuesioner(Request $request, KruChange $kruChange)
    {
        $validated = $request->validate([
            'kuesioner_id' => 'required|integer',
            'status' => 'required|in:completed'
        ]);

        $kruChange->updateKuesionerStatus(
            $validated['kuesioner_id'],
            $validated['status']
        );

        return response()->json([
            'success' => true,
            'message' => 'Status kuesioner berhasil diupdate',
            'completion_percentage' => $kruChange->getKuesionerCompletionPercentage()
        ]);
    }

    public function updatePemeriksaan(Request $request, KruChange $kruChange)
    {
        $validated = $request->validate([
            'pemeriksaan_id' => 'required|integer',
            'status' => 'required|in:checked,failed',
            'catatan' => 'nullable|string|max:500'
        ]);

        $kruChange->updatePemeriksaanStatus(
            $validated['pemeriksaan_id'],
            $validated['status'],
            $validated['catatan'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Status pemeriksaan berhasil diupdate',
            'completion_percentage' => $kruChange->getPemeriksaanCompletionPercentage()
        ]);
    }

    public function showKuesionerTracking(KruChange $kruChange)
    {
        if ($kruChange->status !== 'in_progress') {
            return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
                ->with('error', 'Tracking hanya tersedia untuk handover yang sedang berlangsung');
        }

        $kruChange->load([
            'project:id,nama',
            'areaPatrol:id,nama',
            'timKeluar:id,nama_tim,jenis_regu',
            'timMasuk:id,nama_tim,jenis_regu'
        ]);

        // Get kuesioner with pertanyaan
        $kuesioners = $kruChange->timKeluar->kuesioners()->with(['pertanyaans' => function($query) {
            $query->orderBy('urutan');
        }])->get();

        return view('perusahaan.kru-change.kuesioner-tracking', compact('kruChange', 'kuesioners'));
    }

    public function showPemeriksaanTracking(KruChange $kruChange)
    {
        if ($kruChange->status !== 'in_progress') {
            return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
                ->with('error', 'Tracking hanya tersedia untuk handover yang sedang berlangsung');
        }

        $kruChange->load([
            'project:id,nama',
            'areaPatrol:id,nama',
            'timKeluar:id,nama_tim,jenis_regu',
            'timMasuk:id,nama_tim,jenis_regu'
        ]);

        // Get pemeriksaan with pertanyaan
        $pemeriksaans = $kruChange->timKeluar->pemeriksaans()->with(['pertanyaans' => function($query) {
            $query->orderBy('urutan');
        }])->get();

        return view('perusahaan.kru-change.pemeriksaan-tracking', compact('kruChange', 'pemeriksaans'));
    }

    public function submitKuesionerTracking(Request $request, KruChange $kruChange)
    {
        $validated = $request->validate([
            'kuesioner_id' => 'required|exists:kuesioner_patrolis,id',
            'jawaban' => 'required|array',
            'jawaban.*' => 'required|string'
        ]);

        DB::transaction(function () use ($kruChange, $validated) {
            // Save answers
            foreach ($validated['jawaban'] as $pertanyaanId => $jawaban) {
                // Handle array answers (checkbox)
                $jawabanText = is_array($jawaban) ? implode(', ', $jawaban) : $jawaban;
                
                KruChangeTrackingAnswer::create([
                    'kru_change_id' => $kruChange->id,
                    'tipe_tracking' => 'kuesioner',
                    'tracking_id' => $validated['kuesioner_id'],
                    'pertanyaan_id' => $pertanyaanId,
                    'jawaban' => $jawabanText,
                    'user_id' => auth()->id()
                ]);
            }

            // Update kuesioner status
            $kruChange->updateKuesionerStatus($validated['kuesioner_id'], 'completed');
        });

        return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
            ->with('success', 'Kuesioner berhasil diisi dan disimpan');
    }

    public function submitPemeriksaanTracking(Request $request, KruChange $kruChange)
    {
        $validated = $request->validate([
            'pemeriksaan_id' => 'required|exists:pemeriksaan_patrolis,id',
            'jawaban' => 'required|array',
            'jawaban.*' => 'required|string',
            'status_pemeriksaan' => 'required|in:checked,failed',
            'catatan_pemeriksaan' => 'nullable|string|max:1000'
        ]);

        // Validate catatan is required when status is failed
        if ($validated['status_pemeriksaan'] === 'failed' && empty($validated['catatan_pemeriksaan'])) {
            return back()->withErrors(['catatan_pemeriksaan' => 'Catatan wajib diisi untuk status gagal'])
                        ->withInput();
        }

        DB::transaction(function () use ($kruChange, $validated) {
            // Save answers
            foreach ($validated['jawaban'] as $pertanyaanId => $jawaban) {
                // Handle array answers (checkbox)
                $jawabanText = is_array($jawaban) ? implode(', ', $jawaban) : $jawaban;
                
                KruChangeTrackingAnswer::create([
                    'kru_change_id' => $kruChange->id,
                    'tipe_tracking' => 'pemeriksaan',
                    'tracking_id' => $validated['pemeriksaan_id'],
                    'pertanyaan_id' => $pertanyaanId,
                    'jawaban' => $jawabanText,
                    'user_id' => auth()->id()
                ]);
            }

            // Update pemeriksaan status
            $kruChange->updatePemeriksaanStatus(
                $validated['pemeriksaan_id'], 
                $validated['status_pemeriksaan'],
                $validated['catatan_pemeriksaan']
            );
        });

        return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
            ->with('success', 'Pemeriksaan berhasil dilakukan dan disimpan');
    }

    public function uploadFotoTimKeluar(Request $request, KruChange $kruChange)
    {
        $request->validate([
            'foto_tim_keluar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'foto_tim_keluar.required' => 'Foto tim keluar wajib diupload',
            'foto_tim_keluar.image' => 'File harus berupa gambar',
            'foto_tim_keluar.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'foto_tim_keluar.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        // Delete old photo if exists
        if ($kruChange->foto_tim_keluar && \Storage::disk('public')->exists($kruChange->foto_tim_keluar)) {
            \Storage::disk('public')->delete($kruChange->foto_tim_keluar);
        }

        // Upload new photo
        $fotoPath = $request->file('foto_tim_keluar')->store('kru-change/tim-keluar', 'public');
        
        $kruChange->update(['foto_tim_keluar' => $fotoPath]);

        return response()->json([
            'success' => true,
            'message' => 'Foto tim keluar berhasil diupload',
            'foto_url' => \Storage::url($fotoPath)
        ]);
    }

    public function uploadFotoTimMasuk(Request $request, KruChange $kruChange)
    {
        $request->validate([
            'foto_tim_masuk' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'foto_tim_masuk.required' => 'Foto tim masuk wajib diupload',
            'foto_tim_masuk.image' => 'File harus berupa gambar',
            'foto_tim_masuk.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'foto_tim_masuk.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        // Delete old photo if exists
        if ($kruChange->foto_tim_masuk && \Storage::disk('public')->exists($kruChange->foto_tim_masuk)) {
            \Storage::disk('public')->delete($kruChange->foto_tim_masuk);
        }

        // Upload new photo
        $fotoPath = $request->file('foto_tim_masuk')->store('kru-change/tim-masuk', 'public');
        
        $kruChange->update(['foto_tim_masuk' => $fotoPath]);

        return response()->json([
            'success' => true,
            'message' => 'Foto tim masuk berhasil diupload',
            'foto_url' => \Storage::url($fotoPath)
        ]);
    }

    public function deleteFotoTimKeluar(KruChange $kruChange)
    {
        if ($kruChange->foto_tim_keluar && \Storage::disk('public')->exists($kruChange->foto_tim_keluar)) {
            \Storage::disk('public')->delete($kruChange->foto_tim_keluar);
        }

        $kruChange->update(['foto_tim_keluar' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Foto tim keluar berhasil dihapus'
        ]);
    }

    public function deleteFotoTimMasuk(KruChange $kruChange)
    {
        if ($kruChange->foto_tim_masuk && \Storage::disk('public')->exists($kruChange->foto_tim_masuk)) {
            \Storage::disk('public')->delete($kruChange->foto_tim_masuk);
        }

        $kruChange->update(['foto_tim_masuk' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Foto tim masuk berhasil dihapus'
        ]);
    }
}