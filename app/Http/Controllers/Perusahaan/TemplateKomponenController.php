<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\TemplateKomponenGaji;
use App\Models\Project;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\KomponenPayroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TemplateKomponenController extends Controller
{
    public function index(Request $request)
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Get filters
        $projectId = $request->get('project_id');
        $jabatanId = $request->get('jabatan_id');
        $level = $request->get('level', 'all'); // all, jabatan, karyawan
        
        // Get projects and jabatans for filters (dengan cache)
        $projects = Cache::remember('projects_' . $perusahaanId, 3600, function () {
            return Project::select('id', 'nama')->orderBy('nama')->get();
        });
        
        $jabatans = Cache::remember('jabatans_' . $perusahaanId, 3600, function () {
            return Jabatan::select('id', 'nama')->orderBy('nama')->get();
        });
        
        // Get komponen payroll aktif
        $komponens = KomponenPayroll::select('id', 'nama_komponen', 'kode', 'jenis', 'tipe_perhitungan', 'nilai')
            ->where('aktif', true)
            ->orderBy('jenis')
            ->orderBy('nama_komponen')
            ->get();
        
        // Query templates jabatan only (exclude karyawan templates)
        $query = TemplateKomponenGaji::with([
            'project:id,nama',
            'jabatan:id,nama',
            'komponenPayroll:id,nama_komponen,kode,jenis,tipe_perhitungan'
        ])
        ->whereNotNull('jabatan_id')
        ->whereNull('karyawan_id'); // Only jabatan templates
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        if ($jabatanId) {
            $query->where('jabatan_id', $jabatanId);
        }
        
        $templates = $query->orderBy('project_id')
            ->orderBy('nama_template')
            ->orderBy('jabatan_id')
            ->orderBy('karyawan_id')
            ->paginate(50);
        
        // Group by project first, then by nama_template
        $groupedByProject = $templates->groupBy('project_id');
        $groupedTemplates = $templates->groupBy('nama_template');
        
        return view('perusahaan.template-komponen.index', compact(
            'groupedTemplates',
            'groupedByProject',
            'templates',
            'projects',
            'jabatans',
            'komponens',
            'projectId',
            'jabatanId'
        ));
    }
    
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_template' => 'required|string|max:255',
                'deskripsi' => 'nullable|string',
                'project_id' => 'required|exists:projects,id',
                'jabatan_id' => 'required|exists:jabatans,id',
                'komponens' => 'required|array|min:1',
                'komponens.*.komponen_payroll_id' => 'required|exists:komponen_payrolls,id',
                'komponens.*.nilai' => 'required|numeric|min:0',
                'komponens.*.catatan' => 'nullable|string',
            ], [
                'nama_template.required' => 'Nama template wajib diisi',
                'project_id.required' => 'Project wajib dipilih',
                'jabatan_id.required' => 'Jabatan wajib dipilih',
                'komponens.required' => 'Minimal 1 komponen harus ditambahkan',
                'komponens.*.komponen_payroll_id.required' => 'Komponen payroll wajib dipilih',
                'komponens.*.nilai.required' => 'Nilai wajib diisi',
            ]);
            
            $perusahaanId = auth()->user()->perusahaan_id;
            $count = 0;
            
            DB::transaction(function () use ($validated, $perusahaanId, &$count) {
                foreach ($validated['komponens'] as $komponen) {
                    $data = [
                        'perusahaan_id' => $perusahaanId,
                        'nama_template' => $validated['nama_template'],
                        'deskripsi' => $validated['deskripsi'] ?? null,
                        'project_id' => $validated['project_id'],
                        'jabatan_id' => $validated['jabatan_id'],
                        'karyawan_id' => null, // Always null for jabatan templates
                        'komponen_payroll_id' => $komponen['komponen_payroll_id'],
                        'nilai' => $komponen['nilai'],
                        'level' => 'jabatan',
                        'aktif' => true,
                        'is_default' => false,
                        'catatan' => $komponen['catatan'] ?? null,
                    ];
                    
                    // Check if already exists, update or create
                    TemplateKomponenGaji::updateOrCreate(
                        [
                            'project_id' => $data['project_id'],
                            'jabatan_id' => $data['jabatan_id'],
                            'karyawan_id' => $data['karyawan_id'],
                            'komponen_payroll_id' => $data['komponen_payroll_id'],
                        ],
                        $data
                    );
                    
                    $count++;
                }
            });
            
            return redirect()->back()->with('success', "Berhasil menyimpan template \"{$validated['nama_template']}\" dengan {$count} komponen");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->with('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()))->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan template: ' . $e->getMessage())->withInput();
        }
    }
    
    public function update(Request $request, TemplateKomponenGaji $templateKomponen)
    {
        try {
            $validated = $request->validate([
                'nilai' => 'required|numeric|min:0',
                'aktif' => 'boolean',
                'catatan' => 'nullable|string',
            ], [
                'nilai.required' => 'Nilai wajib diisi',
                'nilai.numeric' => 'Nilai harus berupa angka',
                'nilai.min' => 'Nilai tidak boleh negatif',
            ]);
            
            $validated['aktif'] = $request->has('aktif');
            
            $templateKomponen->update($validated);
            
            return redirect()->back()->with('success', 'Template komponen berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal update template: ' . $e->getMessage());
        }
    }
    
    public function destroy(TemplateKomponenGaji $templateKomponen)
    {
        try {
            $templateKomponen->delete();
            return redirect()->back()->with('success', 'Template komponen berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus template: ' . $e->getMessage());
        }
    }
    
    // Delete all templates by nama_template
    public function destroyByName(Request $request)
    {
        try {
            $namaTemplate = $request->input('nama_template');
            
            // Handle empty or null nama_template
            // Empty string, null, or 'Template Tanpa Nama' all mean we should delete null templates
            if (empty($namaTemplate) || $namaTemplate === 'Template Tanpa Nama') {
                // Delete templates with null or empty nama_template
                $count = TemplateKomponenGaji::where(function($query) {
                    $query->whereNull('nama_template')
                          ->orWhere('nama_template', '');
                })->delete();
                
                $displayName = 'Template Tanpa Nama';
            } else {
                $count = TemplateKomponenGaji::where('nama_template', $namaTemplate)->delete();
                $displayName = $namaTemplate;
            }
            
            if ($count > 0) {
                return redirect()->back()->with('success', "Template \"{$displayName}\" dengan {$count} komponen berhasil dihapus");
            } else {
                return redirect()->back()->with('error', 'Template tidak ditemukan');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus template: ' . $e->getMessage());
        }
    }
    
    // API untuk get karyawan by project & jabatan
    public function getKaryawans(Request $request)
    {
        $projectId = $request->get('project_id');
        $jabatanId = $request->get('jabatan_id');
        
        $query = Karyawan::select('id', 'nik_karyawan', 'nama_lengkap')
            ->where('is_active', true);
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        if ($jabatanId) {
            $query->where('jabatan_id', $jabatanId);
        }
        
        $karyawans = $query->orderBy('nama_lengkap')->get();
        
        return response()->json($karyawans);
    }
    
    // API untuk get jabatan yang sudah punya template
    public function getUsedJabatans(Request $request)
    {
        $projectId = $request->get('project_id');
        $excludeJabatanId = $request->get('exclude_jabatan_id'); // Untuk edit mode
        
        if (!$projectId) {
            return response()->json([]);
        }
        
        // Get jabatan IDs yang sudah punya template
        $query = TemplateKomponenGaji::where('project_id', $projectId)
            ->whereNotNull('jabatan_id')
            ->whereNull('karyawan_id')
            ->distinct();
        
        // Exclude jabatan yang sedang diedit
        if ($excludeJabatanId) {
            $query->where('jabatan_id', '!=', $excludeJabatanId);
        }
        
        $usedJabatanIds = $query->pluck('jabatan_id')->toArray();
        
        return response()->json($usedJabatanIds);
    }
    
    // API untuk get template by nama_template untuk edit
    public function getTemplateByName(Request $request)
    {
        $namaTemplate = $request->get('nama_template');
        $projectId = $request->get('project_id');
        
        if (!$namaTemplate) {
            return response()->json(['error' => 'Nama template required'], 400);
        }
        
        // Get all components for this template
        $templates = TemplateKomponenGaji::with([
            'project:id,nama',
            'jabatan:id,nama',
            'karyawan:id,nama_lengkap,nik_karyawan',
            'komponenPayroll:id,nama_komponen,kode,jenis,tipe_perhitungan,nilai'
        ])
        ->where('nama_template', $namaTemplate);
        
        if ($projectId) {
            $templates->where('project_id', $projectId);
        }
        
        $templates = $templates->get();
        
        if ($templates->isEmpty()) {
            return response()->json(['error' => 'Template not found'], 404);
        }
        
        $firstTemplate = $templates->first();
        
        return response()->json([
            'nama_template' => $firstTemplate->nama_template,
            'deskripsi' => $firstTemplate->deskripsi,
            'scope' => $firstTemplate->level,
            'project_id' => $firstTemplate->project_id,
            'jabatan_id' => $firstTemplate->jabatan_id,
            'karyawan_id' => $firstTemplate->karyawan_id,
            'komponens' => $templates->map(function($t) {
                return [
                    'komponen_payroll_id' => $t->komponen_payroll_id,
                    'nilai' => $t->nilai,
                    'catatan' => $t->catatan,
                ];
            })
        ]);
    }
}
