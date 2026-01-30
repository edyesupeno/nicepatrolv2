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
        Schema::table('penyerahan_perlengkapans', function (Blueprint $table) {
            // Add new date fields
            $table->date('tanggal_mulai')->nullable()->after('tanggal_penyerahan');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            
            // Copy existing tanggal_penyerahan to tanggal_mulai for existing records
            // and set tanggal_selesai to 7 days after tanggal_mulai
        });
        
        // Update existing records
        DB::statement("UPDATE penyerahan_perlengkapans SET tanggal_mulai = tanggal_penyerahan WHERE tanggal_mulai IS NULL");
        DB::statement("UPDATE penyerahan_perlengkapans SET tanggal_selesai = tanggal_mulai + INTERVAL '7 days' WHERE tanggal_selesai IS NULL");
        
        // Make fields required after updating existing records
        Schema::table('penyerahan_perlengkapans', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable(false)->change();
            $table->date('tanggal_selesai')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyerahan_perlengkapans', function (Blueprint $table) {
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }
};