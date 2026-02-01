<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use App\Models\Project;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReimbursementLaporanController extends Controller
{
    /**
     * Display reimbursement report
     */
    public function index(Request $request)
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();

        // Check if no project exists
        if ($projects->isEmpty()) {
            return redirect()->route('perusahaan.projects.create')
                ->with('info', 'Anda perlu membuat project terlebih dahulu sebelum dapat melihat laporan reimbursement.');
        }

        // Build query
        $query = Reimbursement::with(['project:id,nama', 'karyawan:id,nama_lengkap', 'user:id,name'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_pengajuan', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_reimbursement', 'like', "%{$search}%")
                  ->orWhere('judul_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', function($kq) use ($search) {
                      $kq->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $reimbursements = $query->paginate(50);

        // Calculate statistics
        $statsQuery = Reimbursement::query();
        
        // Apply same filters for stats
        if ($request->filled('project_id')) {
            $statsQuery->where('project_id', $request->project_id);
        }
        if ($request->filled('status')) {
            $statsQuery->where('status', $request->status);
        }
        if ($request->filled('kategori')) {
            $statsQuery->where('kategori', $request->kategori);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $statsQuery->whereBetween('tanggal_pengajuan', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function($q) use ($search) {
                $q->where('nomor_reimbursement', 'like', "%{$search}%")
                  ->orWhere('judul_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('karyawan', function($kq) use ($search) {
                      $kq->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        $stats = [
            'total_pengajuan' => $statsQuery->count(),
            'total_amount_pengajuan' => $statsQuery->sum('jumlah_pengajuan'),
            'total_amount_disetujui' => (clone $statsQuery)->where('status', 'approved')->sum('jumlah_disetujui'),
            'draft' => (clone $statsQuery)->where('status', 'draft')->count(),
            'submitted' => (clone $statsQuery)->where('status', 'submitted')->count(),
            'approved' => (clone $statsQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $statsQuery)->where('status', 'rejected')->count(),
            'paid' => (clone $statsQuery)->where('status', 'paid')->count(),
        ];

        // Group by project for summary
        $projectSummary = [];
        if (!$request->filled('project_id')) {
            $projectSummaryQuery = Reimbursement::with('project:id,nama')
                ->selectRaw('project_id, 
                    COUNT(*) as total_count,
                    SUM(jumlah_pengajuan) as total_pengajuan,
                    SUM(CASE WHEN status = ? THEN jumlah_disetujui ELSE 0 END) as total_disetujui,
                    COUNT(CASE WHEN status = ? THEN 1 END) as approved_count,
                    COUNT(CASE WHEN status = ? THEN 1 END) as pending_count', ['approved', 'approved', 'submitted']);
            
            // Apply same filters for project summary
            if ($request->filled('status')) {
                $projectSummaryQuery->where('status', $request->status);
            }
            if ($request->filled('kategori')) {
                $projectSummaryQuery->where('kategori', $request->kategori);
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $projectSummaryQuery->whereBetween('tanggal_pengajuan', [$request->start_date, $request->end_date]);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $projectSummaryQuery->where(function($q) use ($search) {
                    $q->where('nomor_reimbursement', 'like', "%{$search}%")
                      ->orWhere('judul_pengajuan', 'like', "%{$search}%")
                      ->orWhereHas('karyawan', function($kq) use ($search) {
                          $kq->where('nama_lengkap', 'like', "%{$search}%");
                      });
                });
            }
            
            $projectSummary = $projectSummaryQuery->groupBy('project_id')->get();
        }

        return view('perusahaan.reimbursement.laporan', compact(
            'reimbursements',
            'projects',
            'stats',
            'projectSummary'
        ));
    }

    /**
     * Export reimbursement report to PDF
     */
    public function exportPdf(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'status' => 'nullable|in:' . implode(',', array_keys(Reimbursement::getAvailableStatus())),
            'kategori' => 'nullable|in:' . implode(',', array_keys(Reimbursement::getAvailableKategori())),
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        // Build query
        $query = Reimbursement::with(['project:id,nama', 'karyawan:id,nama_lengkap,nik_karyawan', 'user:id,name'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_pengajuan', [$request->start_date, $request->end_date]);
        }

        $reimbursements = $query->get();

        // Calculate statistics
        $stats = [
            'total_pengajuan' => $reimbursements->count(),
            'total_amount_pengajuan' => $reimbursements->sum('jumlah_pengajuan'),
            'total_amount_disetujui' => $reimbursements->where('status', 'approved')->sum('jumlah_disetujui'),
            'draft' => $reimbursements->where('status', 'draft')->count(),
            'submitted' => $reimbursements->where('status', 'submitted')->count(),
            'approved' => $reimbursements->where('status', 'approved')->count(),
            'rejected' => $reimbursements->where('status', 'rejected')->count(),
            'paid' => $reimbursements->where('status', 'paid')->count(),
        ];

        // Get project info if filtered
        $project = null;
        if ($request->filled('project_id')) {
            $project = Project::find($request->project_id);
        }

        // Generate filename
        $filename = 'laporan-reimbursement';
        if ($project) {
            $filename .= '-' . \Illuminate\Support\Str::slug($project->nama);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $filename .= '-' . $request->start_date . '-to-' . $request->end_date;
        }
        $filename .= '-' . date('Y-m-d-H-i-s') . '.pdf';

        // Generate PDF
        $pdf = Pdf::loadView('perusahaan.reimbursement.laporan-pdf', compact(
            'reimbursements',
            'stats',
            'project',
            'validated'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }
}