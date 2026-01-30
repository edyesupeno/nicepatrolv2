<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman_asets', function (Blueprint $table) {
            // Add asset type enum
            $table->enum('aset_type', ['data_aset', 'aset_kendaraan'])->default('data_aset')->after('project_id');
            
            // Add aset_kendaraan_id foreign key
            $table->foreignId('aset_kendaraan_id')->nullable()->constrained('aset_kendaraans')->onDelete('cascade')->after('data_aset_id');
            
            // Make data_aset_id nullable since we now have two types
            $table->foreignId('data_aset_id')->nullable()->change();
            
            // Add indexes for new fields
            $table->index(['aset_type', 'status_peminjaman']);
            $table->index(['aset_kendaraan_id', 'status_peminjaman']);
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman_asets', function (Blueprint $table) {
            $table->dropIndex(['aset_type', 'status_peminjaman']);
            $table->dropIndex(['aset_kendaraan_id', 'status_peminjaman']);
            $table->dropForeign(['aset_kendaraan_id']);
            $table->dropColumn(['aset_type', 'aset_kendaraan_id']);
            $table->foreignId('data_aset_id')->nullable(false)->change();
        });
    }
};
