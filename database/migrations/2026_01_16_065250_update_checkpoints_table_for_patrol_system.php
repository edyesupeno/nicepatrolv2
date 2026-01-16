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
        Schema::table('checkpoints', function (Blueprint $table) {
            // Drop old columns
            $table->dropForeign(['lokasi_id']);
            $table->dropColumn(['lokasi_id', 'kode']);
            
            // Add new columns
            $table->foreignId('rute_patrol_id')->after('perusahaan_id')->constrained()->onDelete('cascade');
            $table->string('qr_code')->nullable()->after('nama');
            $table->string('alamat')->nullable()->after('deskripsi');
            $table->decimal('latitude', 10, 8)->nullable()->after('alamat');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            
            // Add index
            $table->index(['perusahaan_id', 'rute_patrol_id', 'is_active']);
            $table->index('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            // Drop new columns
            $table->dropForeign(['rute_patrol_id']);
            $table->dropColumn(['rute_patrol_id', 'qr_code', 'alamat', 'latitude', 'longitude']);
            
            // Restore old columns
            $table->foreignId('lokasi_id')->after('perusahaan_id')->constrained('lokasis')->onDelete('cascade');
            $table->string('kode')->unique()->after('nama');
        });
    }
};
