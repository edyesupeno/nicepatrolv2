<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Project;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DaftarPayrollController extends Controller
{
    public function index(Request $request)
    {
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Get filters
        $periode = $request->get('periode', now()->format('Y-m'));
        $projectId = $request->get('project_id');
        $jabatanId = $request->get('jabatan_id');
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        
        // Get projects and jabatans for filters (optimized)
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $jabatans = Jabatan::select('id', 'nama')->orderBy('nama')->get();
        
        // Query payrolls (optimized with specific columns)
        $query = Payroll::select([
                'id',
                'perusahaan_id',
                'karyawan_id',
                'project_id',
                'periode',
                'periode_start',
                'periode_end',
                'gaji_pokok',
                'gaji_bruto',
                'gaji_netto',
                // Kehadiran columns
                'hari_kerja',
                'hari_masuk',
                'hari_alpha',
                'hari_sakit',
                'hari_izin',
                'hari_cuti',
                'hari_lembur',
                // Status & approval
                'status',
                'approved_by',
                'approved_at',
                'paid_by',
                'paid_at',
                'created_at'
            ])
            ->with([
                'karyawan:id,nik_karyawan,nama_lengkap,jabatan_id',
                'karyawan.jabatan:id,nama',
                'project:id,nama'
            ])
            ->where('periode', $periode);
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        if ($jabatanId) {
            $query->whereHas('karyawan', function($q) use ($jabatanId) {
                $q->where('jabatan_id', $jabatanId);
            });
        }
        
        if ($status != 'all') {
            $query->where('status', $status);
        }
        
        if ($search) {
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama_lengkap', 'ilike', "%{$search}%")
                  ->orWhere('nik_karyawan', 'ilike', "%{$search}%");
            });
        }
        
        $payrolls = $query->orderBy('created_at', 'desc')
            ->paginate(50)
            ->withQueryString();
        
        // Get statistics (optimized with single query)
        $stats = $this->getStatistics($periode, $projectId, $jabatanId);
        
        return view('perusahaan.payroll.daftar', compact(
            'payrolls',
            'projects',
            'jabatans',
            'periode',
            'projectId',
            'jabatanId',
            'status',
            'search',
            'stats'
        ));
    }
    
    public function show(Payroll $payroll)
    {
        $payroll->load([
            'karyawan:id,nik_karyawan,nama_lengkap,jabatan_id,gaji_pokok',
            'karyawan.jabatan:id,nama',
            'project:id,nama',
            'approvedBy:id,name',
            'paidBy:id,name'
        ]);
        
        // Get kehadiran data based on periode_start and periode_end
        // If periode_start/end not set, fallback to periode month
        if ($payroll->periode_start && $payroll->periode_end) {
            $startDate = $payroll->periode_start;
            $endDate = $payroll->periode_end;
        } else {
            $periodeDate = \Carbon\Carbon::createFromFormat('Y-m', $payroll->periode);
            $startDate = $periodeDate->copy()->startOfMonth();
            $endDate = $periodeDate->copy()->endOfMonth();
        }
        
        $kehadirans = \App\Models\Kehadiran::select([
                'id',
                'karyawan_id',
                'tanggal',
                'status',
                'jam_masuk',
                'jam_keluar',
                'durasi_kerja',
                'keterangan'
            ])
            ->where('karyawan_id', $payroll->karyawan_id)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();
        
        return view('perusahaan.payroll.detail', compact('payroll', 'kehadirans'));
    }
    
    public function approve(Request $request, Payroll $payroll)
    {
        try {
            if ($payroll->status != 'draft') {
                return redirect()->back()->with('error', 'Hanya payroll dengan status draft yang bisa di-approve');
            }
            
            $payroll->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
            
            return redirect()->back()->with('success', 'Payroll berhasil di-approve');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal approve payroll: ' . $e->getMessage());
        }
    }
    
    public function bulkApprove(Request $request)
    {
        try {
            $validated = $request->validate([
                'payroll_ids' => 'required|array|min:1',
                'payroll_ids.*' => 'exists:payrolls,id',
            ]);
            
            $count = Payroll::whereIn('id', $validated['payroll_ids'])
                ->where('status', 'draft')
                ->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            
            return redirect()->back()->with('success', "Berhasil approve {$count} payroll");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal bulk approve: ' . $e->getMessage());
        }
    }
    
    public function destroy(Payroll $payroll)
    {
        try {
            if ($payroll->status != 'draft') {
                return redirect()->back()->with('error', 'Hanya payroll dengan status draft yang bisa dihapus');
            }
            
            $payroll->delete();
            
            return redirect()->back()->with('success', 'Payroll berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus payroll: ' . $e->getMessage());
        }
    }
    
    private function getStatistics($periode, $projectId = null, $jabatanId = null)
    {
        $query = Payroll::select([
                'status',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(gaji_netto) as total_netto'),
                DB::raw('SUM(gaji_bruto) as total_bruto')
            ])
            ->where('periode', $periode);
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }
        
        if ($jabatanId) {
            $query->whereHas('karyawan', function($q) use ($jabatanId) {
                $q->where('jabatan_id', $jabatanId);
            });
        }
        
        // Get grouped statistics in single query
        $grouped = $query->groupBy('status')->get();
        
        // Initialize stats
        $stats = [
            'total' => 0,
            'draft' => 0,
            'approved' => 0,
            'paid' => 0,
            'total_gaji_netto' => 0,
            'total_gaji_bruto' => 0,
        ];
        
        // Process grouped results
        foreach ($grouped as $item) {
            $stats['total'] += $item->count;
            $stats['total_gaji_netto'] += $item->total_netto ?? 0;
            $stats['total_gaji_bruto'] += $item->total_bruto ?? 0;
            
            if ($item->status == 'draft') {
                $stats['draft'] = $item->count;
            } elseif ($item->status == 'approved') {
                $stats['approved'] = $item->count;
            } elseif ($item->status == 'paid') {
                $stats['paid'] = $item->count;
            }
        }
        
        return $stats;
    }
}
