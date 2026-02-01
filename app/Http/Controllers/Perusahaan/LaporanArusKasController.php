<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Rekening;
use App\Models\TransaksiRekening;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class LaporanArusKasController extends Controller
{
    /**
     * Display laporan arus kas
     */
    public function index(Request $request)
    {
        $projects = Project::select('id', 'nama')->orderBy('nama')->get();
        $rekenings = Rekening::with('project:id,nama')
            ->select('id', 'project_id', 'nama_rekening', 'warna_card')
            ->active()
            ->orderBy('project_id')
            ->orderBy('nama_rekening')
            ->get();

        // Check if no project exists
        if ($projects->isEmpty()) {
            return redirect()->route('perusahaan.projects.create')
                ->with('info', 'Anda perlu membuat project terlebih dahulu sebelum dapat mengelola keuangan.');
        }

        // Check if no rekening exists
        if ($rekenings->isEmpty()) {
            // Create empty paginated result
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), // items
                0, // total
                50, // per page
                1, // current page
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );
            
            return view('perusahaan.laporan-arus-kas.index', [
                'projects' => $projects,
                'rekenings' => collect(),
                'transaksis' => $emptyPaginator,
                'stats' => [
                    'total_debit' => 0,
                    'total_kredit' => 0,
                    'total_transaksi' => 0,
                    'net_cash_flow' => 0,
                    'transaksi_per_kategori' => collect()
                ],
                'saldoRekenings' => collect(),
                'startDate' => $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d')),
                'endDate' => $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d')),
                'rekeningId' => null,
                'projectId' => null
            ]);
        }

        // Default periode (bulan ini)
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $rekeningId = $request->get('rekening_id');
        $projectId = $request->get('project_id');

        // Query transaksi
        $query = TransaksiRekening::with(['rekening:id,nama_rekening,warna_card,project_id', 'rekening.project:id,nama', 'user:id,name'])
            ->select('id', 'rekening_id', 'nomor_transaksi', 'tanggal_transaksi', 'jenis_transaksi', 'jumlah', 'saldo_sebelum', 'saldo_sesudah', 'kategori_transaksi', 'keterangan', 'referensi', 'user_id', 'is_verified', 'created_at', 'updated_at')
            ->byPeriode($startDate, $endDate)
            ->whereHas('rekening') // Pastikan rekening masih ada
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan rekening
        if ($rekeningId) {
            $query->where('rekening_id', $rekeningId);
        }

        // Filter berdasarkan project
        if ($projectId) {
            $query->whereHas('rekening', function($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });
        }

        $transaksis = $query->paginate(50);

        // Statistics
        $stats = $this->getStatistics($startDate, $endDate, $rekeningId, $projectId);

        // Saldo per rekening
        $saldoRekenings = $this->getSaldoRekenings($rekeningId, $projectId);

        return view('perusahaan.laporan-arus-kas.index', compact(
            'transaksis', 
            'projects', 
            'rekenings', 
            'stats', 
            'saldoRekenings',
            'startDate',
            'endDate',
            'rekeningId',
            'projectId'
        ));
    }

    /**
     * Get statistics
     */
    private function getStatistics($startDate, $endDate, $rekeningId = null, $projectId = null)
    {
        $query = TransaksiRekening::byPeriode($startDate, $endDate);

        if ($rekeningId) {
            $query->where('rekening_id', $rekeningId);
        }

        if ($projectId) {
            $query->whereHas('rekening', function($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });
        }

        $totalDebit = $query->clone()->debit()->sum('jumlah');
        $totalKredit = $query->clone()->kredit()->sum('jumlah');
        $totalTransaksi = $query->count();
        $netCashFlow = $totalDebit - $totalKredit;

        // Transaksi per kategori
        $transaksiPerKategori = $query->clone()
            ->selectRaw('kategori_transaksi, jenis_transaksi, SUM(jumlah) as total')
            ->groupBy('kategori_transaksi', 'jenis_transaksi')
            ->get()
            ->groupBy('kategori_transaksi');

        return [
            'total_debit' => $totalDebit,
            'total_kredit' => $totalKredit,
            'total_transaksi' => $totalTransaksi,
            'net_cash_flow' => $netCashFlow,
            'transaksi_per_kategori' => $transaksiPerKategori
        ];
    }

    /**
     * Get saldo per rekening
     */
    private function getSaldoRekenings($rekeningId = null, $projectId = null)
    {
        $query = Rekening::with('project:id,nama')
            ->select('id', 'project_id', 'nama_rekening', 'saldo_saat_ini', 'mata_uang', 'warna_card')
            ->active();

        if ($rekeningId) {
            $query->where('id', $rekeningId);
        }

        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        return $query->orderBy('project_id')->orderBy('nama_rekening')->get();
    }

    /**
     * Show detail transaksi rekening
     */
    public function show(Rekening $rekening, Request $request)
    {
        // Default periode (bulan ini)
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Transaksi rekening
        $transaksis = TransaksiRekening::with(['user:id,name'])
            ->where('rekening_id', $rekening->id)
            ->byPeriode($startDate, $endDate)
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Statistics untuk rekening ini
        $stats = [
            'total_debit' => TransaksiRekening::where('rekening_id', $rekening->id)
                ->byPeriode($startDate, $endDate)
                ->debit()
                ->sum('jumlah'),
            'total_kredit' => TransaksiRekening::where('rekening_id', $rekening->id)
                ->byPeriode($startDate, $endDate)
                ->kredit()
                ->sum('jumlah'),
            'total_transaksi' => TransaksiRekening::where('rekening_id', $rekening->id)
                ->byPeriode($startDate, $endDate)
                ->count(),
        ];

        $stats['net_cash_flow'] = $stats['total_debit'] - $stats['total_kredit'];

        // Saldo awal periode
        $saldoAwalPeriode = $this->getSaldoAwalPeriode($rekening->id, $startDate);

        return view('perusahaan.laporan-arus-kas.show', compact(
            'rekening',
            'transaksis',
            'stats',
            'saldoAwalPeriode',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get saldo awal periode
     */
    private function getSaldoAwalPeriode($rekeningId, $startDate)
    {
        $lastTransaction = TransaksiRekening::where('rekening_id', $rekeningId)
            ->where('tanggal_transaksi', '<', $startDate)
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastTransaction) {
            return $lastTransaction->saldo_sesudah;
        }

        // Jika tidak ada transaksi sebelumnya, ambil saldo awal rekening
        $rekening = Rekening::find($rekeningId);
        return $rekening ? $rekening->saldo_awal : 0;
    }

    /**
     * Export laporan ke PDF
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $rekeningId = $request->get('rekening_id');
        $projectId = $request->get('project_id');

        // Query transaksi
        $query = TransaksiRekening::with(['rekening.project', 'user'])
            ->byPeriode($startDate, $endDate)
            ->orderBy('tanggal_transaksi', 'desc')
            ->orderBy('created_at', 'desc');

        if ($rekeningId) {
            $query->where('rekening_id', $rekeningId);
        }

        if ($projectId) {
            $query->whereHas('rekening', function($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });
        }

        $transaksis = $query->get();
        $stats = $this->getStatistics($startDate, $endDate, $rekeningId, $projectId);
        $saldoRekenings = $this->getSaldoRekenings($rekeningId, $projectId);
        
        // Get projects for PDF
        $projects = Project::select('id', 'nama')->get();
        
        // Get transaksi per kategori for summary
        $kategoriQuery = TransaksiRekening::byPeriode($startDate, $endDate);
        
        if ($rekeningId) {
            $kategoriQuery->where('rekening_id', $rekeningId);
        }

        if ($projectId) {
            $kategoriQuery->whereHas('rekening', function($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });
        }
        
        $transaksiPerKategori = $kategoriQuery
            ->selectRaw('kategori_transaksi, jenis_transaksi, COUNT(*) as jumlah_transaksi, SUM(jumlah) as total')
            ->groupBy('kategori_transaksi', 'jenis_transaksi')
            ->get()
            ->map(function($item) {
                $item->kategori_transaksi_label = TransaksiRekening::getAvailableKategori()[$item->kategori_transaksi] ?? ucwords(str_replace('_', ' ', $item->kategori_transaksi));
                return $item;
            });

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('perusahaan.laporan-arus-kas.pdf', compact(
            'transaksis',
            'stats',
            'saldoRekenings',
            'projects',
            'transaksiPerKategori',
            'startDate',
            'endDate',
            'rekeningId',
            'projectId'
        ));

        $filename = 'laporan-arus-kas-' . $startDate . '-to-' . $endDate . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export laporan ke Excel
     */
    public function exportExcel(Request $request)
    {
        // Implementation for Excel export
        // You can use Laravel Excel package for this
        return response()->json(['message' => 'Excel export will be implemented']);
    }
}