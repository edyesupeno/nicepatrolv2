<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Data Dasar
            $table->string('nik_karyawan')->unique();
            $table->string('status_karyawan');
            $table->foreignId('jabatan_id')->constrained('jabatans')->onDelete('restrict');
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->boolean('is_active')->default(true);
            
            // Data Pribadi
            $table->string('nama_lengkap');
            $table->string('nik_ktp', 16)->unique();
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('telepon', 20);
            $table->text('alamat');
            $table->string('kota');
            $table->string('provinsi');
            
            // Foto
            $table->string('foto')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('perusahaan_id');
            $table->index('user_id');
            $table->index('jabatan_id');
            $table->index('nik_karyawan');
            $table->index('nik_ktp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karyawans');
    }
};
