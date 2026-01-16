<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if columns exist before dropping
            if (Schema::hasColumn('users', 'jabatan_id')) {
                // Try to drop foreign key if it exists
                try {
                    $table->dropForeign(['jabatan_id']);
                } catch (\Exception $e) {
                    // Foreign key doesn't exist, continue
                }
                
                // Drop karyawan-related columns
                $table->dropColumn([
                    'nik_karyawan',
                    'status_karyawan',
                    'jabatan_id',
                    'tanggal_masuk',
                    'nik_ktp',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'jenis_kelamin',
                    'telepon',
                    'alamat',
                    'kota',
                    'provinsi',
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Data Dasar
            $table->string('nik_karyawan')->nullable()->after('email');
            $table->string('status_karyawan')->nullable()->after('nik_karyawan');
            $table->foreignId('jabatan_id')->nullable()->after('status_karyawan')->constrained('jabatans')->onDelete('set null');
            $table->date('tanggal_masuk')->nullable()->after('jabatan_id');
            
            // Data Pribadi
            $table->string('nik_ktp', 16)->nullable()->after('is_active');
            $table->string('tempat_lahir')->nullable()->after('nik_ktp');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable()->after('tanggal_lahir');
            $table->string('telepon', 20)->nullable()->after('jenis_kelamin');
            $table->text('alamat')->nullable()->after('telepon');
            $table->string('kota')->nullable()->after('alamat');
            $table->string('provinsi')->nullable()->after('kota');
        });
    }
};
