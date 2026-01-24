<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kru_changes', function (Blueprint $table) {
            // Tracking status untuk inventaris, kuesioner, dan pemeriksaan
            $table->json('inventaris_status')->nullable()->after('catatan_supervisor');
            $table->json('kuesioner_status')->nullable()->after('inventaris_status');
            $table->json('pemeriksaan_status')->nullable()->after('kuesioner_status');
            
            // Timestamp untuk tracking
            $table->timestamp('inventaris_checked_at')->nullable()->after('pemeriksaan_status');
            $table->timestamp('kuesioner_checked_at')->nullable()->after('inventaris_checked_at');
            $table->timestamp('pemeriksaan_checked_at')->nullable()->after('kuesioner_checked_at');
            
            // User yang melakukan checking
            $table->foreignId('inventaris_checked_by')->nullable()->constrained('users')->after('pemeriksaan_checked_at');
            $table->foreignId('kuesioner_checked_by')->nullable()->constrained('users')->after('inventaris_checked_by');
            $table->foreignId('pemeriksaan_checked_by')->nullable()->constrained('users')->after('kuesioner_checked_by');
            
            // Catatan untuk masing-masing tracking
            $table->text('inventaris_catatan')->nullable()->after('pemeriksaan_checked_by');
            $table->text('kuesioner_catatan')->nullable()->after('inventaris_catatan');
            $table->text('pemeriksaan_catatan')->nullable()->after('kuesioner_catatan');
        });
    }

    public function down(): void
    {
        Schema::table('kru_changes', function (Blueprint $table) {
            $table->dropForeign(['inventaris_checked_by']);
            $table->dropForeign(['kuesioner_checked_by']);
            $table->dropForeign(['pemeriksaan_checked_by']);
            
            $table->dropColumn([
                'inventaris_status',
                'kuesioner_status',
                'pemeriksaan_status',
                'inventaris_checked_at',
                'kuesioner_checked_at',
                'pemeriksaan_checked_at',
                'inventaris_checked_by',
                'kuesioner_checked_by',
                'pemeriksaan_checked_by',
                'inventaris_catatan',
                'kuesioner_catatan',
                'pemeriksaan_catatan'
            ]);
        });
    }
};