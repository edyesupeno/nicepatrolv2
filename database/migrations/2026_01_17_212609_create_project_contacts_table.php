<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->string('nama_kontak');
            $table->string('jabatan_kontak');
            $table->string('nomor_telepon');
            $table->string('email')->nullable();
            $table->enum('jenis_kontak', [
                'polisi',
                'pemadam_kebakaran', 
                'ambulans',
                'security',
                'manager_project',
                'supervisor',
                'teknisi',
                'lainnya'
            ]);
            $table->text('keterangan')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index untuk performance
            $table->index(['project_id', 'is_active']);
            $table->index(['perusahaan_id', 'jenis_kontak']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_contacts');
    }
};