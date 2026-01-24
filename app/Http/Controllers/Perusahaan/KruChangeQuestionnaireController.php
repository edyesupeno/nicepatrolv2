<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\KruChange;
use App\Models\KruChangeQuestionnaireAnswer;
use App\Models\KuesionerPatroli;
use App\Models\PertanyaanKuesioner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class KruChangeQuestionnaireController extends Controller
{
    public function showQuestionnaire(KruChange $kruChange, $tipeJawaban)
    {
        if (!in_array($tipeJawaban, ['keluar', 'masuk'])) {
            abort(404);
        }

        if ($kruChange->status !== 'in_progress') {
            return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
                ->with('error', 'Kuesioner hanya dapat diisi saat handover sedang berlangsung');
        }

        // Get the appropriate team's questionnaires
        $tim = $tipeJawaban === 'keluar' ? $kruChange->timKeluar : $kruChange->timMasuk;
        
        $kuesioners = $tim->kuesioners()->with(['pertanyaans' => function($query) {
            $query->orderBy('urutan');
        }])->get();

        // Get existing answers
        $existingAnswers = KruChangeQuestionnaireAnswer::where('kru_change_id', $kruChange->id)
            ->where('tipe_jawaban', $tipeJawaban)
            ->get()
            ->keyBy('pertanyaan_kuesioner_id');

        return view('perusahaan.kru-change.questionnaire', compact(
            'kruChange', 
            'tipeJawaban', 
            'kuesioners', 
            'existingAnswers'
        ));
    }

    public function submitQuestionnaire(Request $request, KruChange $kruChange, $tipeJawaban)
    {
        if (!in_array($tipeJawaban, ['keluar', 'masuk'])) {
            abort(404);
        }

        if ($kruChange->status !== 'in_progress') {
            return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
                ->with('error', 'Kuesioner hanya dapat diisi saat handover sedang berlangsung');
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|image|max:5120', // 5MB max
        ], [
            'answers.required' => 'Semua pertanyaan wajib dijawab',
            'answers.*.required' => 'Jawaban tidak boleh kosong',
            'photos.*.image' => 'File harus berupa gambar',
            'photos.*.max' => 'Ukuran foto maksimal 5MB',
        ]);

        try {
            DB::transaction(function () use ($validated, $kruChange, $tipeJawaban, $request) {
                foreach ($validated['answers'] as $pertanyaanId => $jawaban) {
                    // Get pertanyaan info
                    $pertanyaan = PertanyaanKuesioner::find($pertanyaanId);
                    if (!$pertanyaan) continue;

                    // Handle photo upload
                    $fotoPath = null;
                    if ($request->hasFile("photos.{$pertanyaanId}")) {
                        $foto = $request->file("photos.{$pertanyaanId}");
                        $filename = time() . '_' . $pertanyaanId . '_' . $foto->getClientOriginalName();
                        $fotoPath = $foto->storeAs('kru-change-questionnaire', $filename, 'public');
                    }

                    // Update or create answer
                    KruChangeQuestionnaireAnswer::updateOrCreate(
                        [
                            'kru_change_id' => $kruChange->id,
                            'pertanyaan_kuesioner_id' => $pertanyaanId,
                            'tipe_jawaban' => $tipeJawaban,
                        ],
                        [
                            'kuesioner_patroli_id' => $pertanyaan->kuesioner_patroli_id,
                            'jawaban' => $jawaban,
                            'foto' => $fotoPath,
                            'user_id' => auth()->id(),
                        ]
                    );
                }
            });

            return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
                ->with('success', 'Kuesioner berhasil disimpan');

        } catch (\Exception $e) {
            \Log::error('Error submitting questionnaire: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal menyimpan kuesioner: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function editQuestionnaire(KruChange $kruChange, $tipeJawaban)
    {
        if (!in_array($tipeJawaban, ['keluar', 'masuk'])) {
            abort(404);
        }

        if ($kruChange->status === 'completed') {
            return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
                ->with('error', 'Kuesioner tidak dapat diedit setelah handover selesai');
        }

        // Get the appropriate team's questionnaires
        $tim = $tipeJawaban === 'keluar' ? $kruChange->timKeluar : $kruChange->timMasuk;
        
        $kuesioners = $tim->kuesioners()->with(['pertanyaans' => function($query) {
            $query->orderBy('urutan');
        }])->get();

        // Get existing answers
        $existingAnswers = KruChangeQuestionnaireAnswer::where('kru_change_id', $kruChange->id)
            ->where('tipe_jawaban', $tipeJawaban)
            ->get()
            ->keyBy('pertanyaan_kuesioner_id');

        return view('perusahaan.kru-change.questionnaire-edit', compact(
            'kruChange', 
            'tipeJawaban', 
            'kuesioners', 
            'existingAnswers'
        ));
    }

    public function updateQuestionnaire(Request $request, KruChange $kruChange, $tipeJawaban)
    {
        if (!in_array($tipeJawaban, ['keluar', 'masuk'])) {
            abort(404);
        }

        if ($kruChange->status === 'completed') {
            return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
                ->with('error', 'Kuesioner tidak dapat diedit setelah handover selesai');
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'nullable|image|max:5120', // 5MB max
            'remove_photos' => 'nullable|array',
            'remove_photos.*' => 'boolean',
        ], [
            'answers.required' => 'Semua pertanyaan wajib dijawab',
            'answers.*.required' => 'Jawaban tidak boleh kosong',
            'photos.*.image' => 'File harus berupa gambar',
            'photos.*.max' => 'Ukuran foto maksimal 5MB',
        ]);

        try {
            DB::transaction(function () use ($validated, $kruChange, $tipeJawaban, $request) {
                foreach ($validated['answers'] as $pertanyaanId => $jawaban) {
                    // Get existing answer
                    $existingAnswer = KruChangeQuestionnaireAnswer::where('kru_change_id', $kruChange->id)
                        ->where('pertanyaan_kuesioner_id', $pertanyaanId)
                        ->where('tipe_jawaban', $tipeJawaban)
                        ->first();

                    if (!$existingAnswer) continue;

                    // Handle photo removal
                    if (isset($validated['remove_photos'][$pertanyaanId]) && $validated['remove_photos'][$pertanyaanId]) {
                        if ($existingAnswer->foto) {
                            Storage::disk('public')->delete($existingAnswer->foto);
                            $existingAnswer->foto = null;
                        }
                    }

                    // Handle new photo upload
                    if ($request->hasFile("photos.{$pertanyaanId}")) {
                        // Delete old photo
                        if ($existingAnswer->foto) {
                            Storage::disk('public')->delete($existingAnswer->foto);
                        }

                        // Upload new photo
                        $foto = $request->file("photos.{$pertanyaanId}");
                        $filename = time() . '_' . $pertanyaanId . '_' . $foto->getClientOriginalName();
                        $existingAnswer->foto = $foto->storeAs('kru-change-questionnaire', $filename, 'public');
                    }

                    // Update answer
                    $existingAnswer->update([
                        'jawaban' => $jawaban,
                        'foto' => $existingAnswer->foto,
                        'user_id' => auth()->id(),
                    ]);
                }
            });

            return redirect()->route('perusahaan.kru-change.show', $kruChange->hash_id)
                ->with('success', 'Kuesioner berhasil diupdate');

        } catch (\Exception $e) {
            \Log::error('Error updating questionnaire: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Gagal mengupdate kuesioner: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function deleteAnswer(KruChange $kruChange, $tipeJawaban, $pertanyaanId)
    {
        if (!in_array($tipeJawaban, ['keluar', 'masuk'])) {
            abort(404);
        }

        if ($kruChange->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Jawaban tidak dapat dihapus setelah handover selesai'
            ], 403);
        }

        try {
            $answer = KruChangeQuestionnaireAnswer::where('kru_change_id', $kruChange->id)
                ->where('pertanyaan_kuesioner_id', $pertanyaanId)
                ->where('tipe_jawaban', $tipeJawaban)
                ->first();

            if (!$answer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jawaban tidak ditemukan'
                ], 404);
            }

            // Delete photo if exists
            if ($answer->foto) {
                Storage::disk('public')->delete($answer->foto);
            }

            $answer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting answer: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jawaban: ' . $e->getMessage()
            ], 500);
        }
    }
}