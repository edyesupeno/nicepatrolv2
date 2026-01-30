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
        Schema::table('penyerahan_perlengkapan_items', function (Blueprint $table) {
            $table->foreignId('karyawan_id')->nullable()->constrained()->onDelete('cascade')->after('item_perlengkapan_id');
            $table->boolean('is_diserahkan')->default(false)->after('kondisi_saat_dikembalikan');
            $table->timestamp('tanggal_diserahkan')->nullable()->after('is_diserahkan');
            
            // Add index
            $table->index(['penyerahan_perlengkapan_id', 'karyawan_id', 'is_diserahkan'], 'penyerahan_karyawan_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyerahan_perlengkapan_items', function (Blueprint $table) {
            $table->dropIndex('penyerahan_karyawan_status_index');
            $table->dropForeign(['karyawan_id']);
            $table->dropColumn(['karyawan_id', 'is_diserahkan', 'tanggal_diserahkan']);
        });
    }
};