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

class TemplateKaryawanController extends Controller
{
    public function index(Request $request)
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Get filters
        $projectId = $request->get('project_id');
        $jabatanId = $request->get('jabatan_id');
        $search = $request->get('search');
        
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
        
        // Query templates karyawan only
        $query = TemplateKomponenGaji::with([
            'project:id,nama',
            'jabatan:id,nama',
            'karyawan:id,nama_lengkap,nik_karyawan',
            'komponenPayroll:id,nama_komponen,kode,jenis,tipe_perhitungan'
        ])
        ->whereNotNull('karyawan_id'); // Only karyawan templates
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        if ($jabatanId) {
            $query->where('jabatan_id', $jabatanId);
        }
        
        if ($search) {
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama_lengkap', 'ilike', "%{$search}%")
                  ->orWhere('nik_karyawan', 'ilike', "%{$search}%");
            });
        }
        
        $templates = $query->orderBy('project_id')
            ->orderBy('karyawan_id')
            ->paginate(50);
        
        // Group by project first, then by karyawan
        $groupedByProject = $templates->groupBy('project_id');
        
        return view('perusahaan.payroll.template-karyawan', compact(
            'groupedByProject',
            'templates',
            'projects',
            'jabatans',
            'komponens',
            'projectId',
            'jabatanId',
            'search'
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
                'karyawan_id' => 'required|exists:karyawans,id',
                'komponens' => 'required|array|min:1',
                'komponens.*.komponen_payroll_id' => 'required|exists:komponen_payrolls,id',
                'komponens.*.nilai' => 'required|numeric|min:0',
                'komponens.*.catatan' => 'nullable|string',
            ], [
                'nama_template.required' => 'Nama template wajib diisi',
                'project_id.required' => 'Project wajib dipilih',
                'jabatan_id.required' => 'Jabatan wajib dipilih',
                'karyawan_id.required' => 'Karyawan wajib dipilih',
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
                        'karyawan_id' => $validated['karyawan_id'],
                        'komponen_payroll_id' => $komponen['komponen_payroll_id'],
                        'nilai' => $komponen['nilai'],
                        'level' => 'karyawan',
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
    
    public function destroyByName(Request $request)
    {
        try {
            $karyawanId = $request->input('karyawan_id');
            
            if (!$karyawanId) {
                return redirect()->back()->with('error', 'Karyawan ID tidak valid');
            }
            
            $count = TemplateKomponenGaji::where('karyawan_id', $karyawanId)->delete();
            
            if ($count > 0) {
                return redirect()->back()->with('success', "Template karyawan dengan {$count} komponen berhasil dihapus");
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
    
    // API untuk get template by karyawan_id untuk edit
    public function getTemplateByKaryawan(Request $request)
    {
        $karyawanId = $request->get('karyawan_id');
        
        if (!$karyawanId) {
            return response()->json(['error' => 'Karyawan ID required'], 400);
        }
        
        // Get all components for this karyawan
        $templates = TemplateKomponenGaji::with([
            'project:id,nama',
            'jabatan:id,nama',
            'karyawan:id,nama_lengkap,nik_karyawan',
            'komponenPayroll:id,nama_komponen,kode,jenis,tipe_perhitungan,nilai'
        ])
        ->where('karyawan_id', $karyawanId)
        ->get();
        
        if ($templates->isEmpty()) {
            return response()->json(['error' => 'Template not found'], 404);
        }
        
        $firstTemplate = $templates->first();
        
        return response()->json([
            'nama_template' => $firstTemplate->nama_template,
            'deskripsi' => $firstTemplate->deskripsi,
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
    
    // API untuk get template jabatan by karyawan_id (untuk auto-load saat pilih karyawan)
    public function getJabatanTemplateByKaryawan(Request $request)
    {
        $karyawanId = $request->get('karyawan_id');
        
        if (!$karyawanId) {
            return response()->json(['error' => 'Karyawan ID required'], 400);
        }
        
        // Get karyawan info
        $karyawan = Karyawan::select('id', 'nama_lengkap', 'nik_karyawan', 'project_id', 'jabatan_id')
            ->find($karyawanId);
        
        if (!$karyawan) {
            return response()->json(['error' => 'Karyawan not found'], 404);
        }
        
        // Get template jabatan for this karyawan's jabatan
        $jabatanTemplates = TemplateKomponenGaji::with([
            'komponenPayroll:id,nama_komponen,kode,jenis,tipe_perhitungan,nilai'
        ])
        ->where('project_id', $karyawan->project_id)
        ->where('jabatan_id', $karyawan->jabatan_id)
        ->whereNull('karyawan_id') // Only jabatan template
        ->get();
        
        if ($jabatanTemplates->isEmpty()) {
            return response()->json([
                'has_template' => false,
                'karyawan' => [
                    'id' => $karyawan->id,
                    'nama' => $karyawan->nama_lengkap,
                    'nik' => $karyawan->nik_karyawan,
                    'project_id' => $karyawan->project_id,
                    'jabatan_id' => $karyawan->jabatan_id,
                ],
                'komponens' => []
            ]);
        }
        
        $firstTemplate = $jabatanTemplates->first();
        
        return response()->json([
            'has_template' => true,
            'karyawan' => [
                'id' => $karyawan->id,
                'nama' => $karyawan->nama_lengkap,
                'nik' => $karyawan->nik_karyawan,
                'project_id' => $karyawan->project_id,
                'jabatan_id' => $karyawan->jabatan_id,
            ],
            'template_info' => [
                'nama_template' => $firstTemplate->nama_template,
                'deskripsi' => $firstTemplate->deskripsi,
            ],
            'komponens' => $jabatanTemplates->map(function($t) {
                return [
                    'komponen_payroll_id' => $t->komponen_payroll_id,
                    'nilai' => $t->nilai,
                    'catatan' => $t->catatan,
                    'nama_komponen' => $t->komponenPayroll->nama_komponen,
                    'jenis' => $t->komponenPayroll->jenis,
                    'tipe_perhitungan' => $t->komponenPayroll->tipe_perhitungan,
                ];
            })
        ]);
    }
}
