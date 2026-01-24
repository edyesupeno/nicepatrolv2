<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\KruChange;
use App\Models\KruChangeTrackingAnswer;
use App\Models\Project;
use App\Models\AreaPatrol;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanKruChangeController extends Controller
{
    public function index(Request $request)
    {
        $query = KruChange::with([
            'project:id,nama',
            'areaPatrol:id,nama',
            'timKeluar:id,nama_tim,jenis_regu',
            'timMasuk:id,nama_tim,jenis_regu',
            'shiftKeluar:id,nama_shift',
            'shiftMasuk:id,nama_shift',
            'petugasKeluar:id,name',
            'petugasMasuk:id,name',
            'supervisor:id,name'
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

        // Filter by date range
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('waktu_mulai_handover', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('waktu_mulai_handover', '<=', $request->tanggal_selesai);
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

        return view('perusahaan.laporan-patroli.kru-change.index', compact('kruChanges', 'projects', 'areas'));
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
            'inventarisCheckedBy:id,name',
            'kuesionerCheckedBy:id,name',
            'pemeriksaanCheckedBy:id,name'
        ]);

        // Get tracking answers with questionnaire and inspection names
        $kuesionerAnswers = KruChangeTrackingAnswer::where('kru_change_id', $kruChange->id)
            ->where('tipe_tracking', 'kuesioner')
            ->with(['pertanyaanKuesioner:id,pertanyaan'])
            ->get()
            ->groupBy('tracking_id')
            ->map(function ($answers, $trackingId) {
                $kuesioner = \App\Models\KuesionerPatroli::find($trackingId);
                return [
                    'nama' => $kuesioner ? $kuesioner->judul : "Kuesioner ID: {$trackingId}",
                    'answers' => $answers
                ];
            });

        $pemeriksaanAnswers = KruChangeTrackingAnswer::where('kru_change_id', $kruChange->id)
            ->where('tipe_tracking', 'pemeriksaan')
            ->with(['pertanyaanPemeriksaan:id,pertanyaan'])
            ->get()
            ->groupBy('tracking_id')
            ->map(function ($answers, $trackingId) {
                $pemeriksaan = \App\Models\PemeriksaanPatroli::find($trackingId);
                return [
                    'nama' => $pemeriksaan ? $pemeriksaan->nama : "Pemeriksaan ID: {$trackingId}",
                    'answers' => $answers
                ];
            });

        return view('perusahaan.laporan-patroli.kru-change.show', compact(
            'kruChange', 
            'kuesionerAnswers', 
            'pemeriksaanAnswers'
        ));
    }

    public function exportPdf(KruChange $kruChange)
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
            'inventarisCheckedBy:id,name',
            'kuesionerCheckedBy:id,name',
            'pemeriksaanCheckedBy:id,name'
        ]);

        // Get tracking answers with questionnaire and inspection names
        $kuesionerAnswers = KruChangeTrackingAnswer::where('kru_change_id', $kruChange->id)
            ->where('tipe_tracking', 'kuesioner')
            ->with(['pertanyaanKuesioner:id,pertanyaan'])
            ->get()
            ->groupBy('tracking_id')
            ->map(function ($answers, $trackingId) {
                $kuesioner = \App\Models\KuesionerPatroli::find($trackingId);
                return [
                    'nama' => $kuesioner ? $kuesioner->judul : "Kuesioner ID: {$trackingId}",
                    'answers' => $answers
                ];
            });

        $pemeriksaanAnswers = KruChangeTrackingAnswer::where('kru_change_id', $kruChange->id)
            ->where('tipe_tracking', 'pemeriksaan')
            ->with(['pertanyaanPemeriksaan:id,pertanyaan'])
            ->get()
            ->groupBy('tracking_id')
            ->map(function ($answers, $trackingId) {
                $pemeriksaan = \App\Models\PemeriksaanPatroli::find($trackingId);
                return [
                    'nama' => $pemeriksaan ? $pemeriksaan->nama : "Pemeriksaan ID: {$trackingId}",
                    'answers' => $answers
                ];
            });

        $pdf = Pdf::loadView('perusahaan.laporan-patroli.kru-change.pdf', compact(
            'kruChange', 
            'kuesionerAnswers', 
            'pemeriksaanAnswers'
        ));

        $filename = 'Laporan_Kru_Change_' . $kruChange->hash_id . '_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }

    public function exportMultiplePdf(Request $request)
    {
        $validated = $request->validate([
            'kru_change_ids' => 'required|array',
            'kru_change_ids.*' => 'exists:kru_changes,id'
        ]);

        $kruChanges = KruChange::whereIn('id', $validated['kru_change_ids'])
            ->with([
                'project:id,nama',
                'areaPatrol:id,nama',
                'timKeluar:id,nama_tim,jenis_regu',
                'timMasuk:id,nama_tim,jenis_regu',
                'shiftKeluar:id,nama_shift,jam_mulai,jam_selesai',
                'shiftMasuk:id,nama_shift,jam_mulai,jam_selesai',
                'petugasKeluar:id,name,email',
                'petugasMasuk:id,name,email',
                'supervisor:id,name,email'
            ])
            ->get();

        $pdf = Pdf::loadView('perusahaan.laporan-patroli.kru-change.multiple-pdf', compact('kruChanges'));
        
        $filename = 'Laporan_Kru_Change_Multiple_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }
}
