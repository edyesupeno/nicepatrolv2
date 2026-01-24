 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kru_change_questionnaire_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kru_change_id')->constrained('kru_changes')->onDelete('cascade');
            $table->foreignId('kuesioner_patroli_id')->constrained('kuesioner_patrolis')->onDelete('cascade');
            $table->foreignId('pertanyaan_kuesioner_id')->constrained('pertanyaan_kuesioners')->onDelete('cascade');
            
            // Jawaban
            $table->text('jawaban');
            $table->json('jawaban_detail')->nullable(); // Untuk jawaban kompleks (multiple choice, etc)
            
            // Foto pendukung
            $table->string('foto')->nullable();
            
            // Tipe jawaban (keluar/masuk)
            $table->enum('tipe_jawaban', ['keluar', 'masuk']); // Tim keluar atau masuk yang jawab
            
            // User yang menjawab
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['kru_change_id', 'tipe_jawaban']);
            $table->index(['kuesioner_patroli_id', 'pertanyaan_kuesioner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kru_change_questionnaire_answers');
    }
};