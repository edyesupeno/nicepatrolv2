<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_komponen_gajis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('jabatan_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('karyawan_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('komponen_payroll_id')->constrained()->onDelete('cascade');
            $table->decimal('nilai', 15, 2);
            $table->enum('level', ['project', 'jabatan', 'karyawan'])->default('project');
            $table->boolean('aktif')->default(true);
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['perusahaan_id', 'project_id']);
            $table->index(['project_id', 'jabatan_id']);
            $table->index(['karyawan_id']);
            $table->index(['komponen_payroll_id']);
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['project_id', 'jabatan_id', 'karyawan_id', 'komponen_payroll_id'], 'unique_template_komponen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_komponen_gajis');
    }
};
