<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kru_changes', function (Blueprint $table) {
            // Ubah petugas menjadi JSON array untuk multiple selection
            $table->json('petugas_keluar_ids')->nullable()->after('petugas_keluar_id');
            $table->json('petugas_masuk_ids')->nullable()->after('petugas_masuk_id');
            
            // Keep single petugas for backward compatibility (bisa dihapus nanti)
            $table->foreignId('petugas_keluar_id')->nullable()->change();
            $table->foreignId('petugas_masuk_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kru_changes', function (Blueprint $table) {
            $table->dropColumn(['petugas_keluar_ids', 'petugas_masuk_ids']);
            $table->foreignId('petugas_keluar_id')->nullable(false)->change();
        });
    }
};