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
        Schema::table('penerimaan_barangs', function (Blueprint $table) {
            // Change pengirim from ENUM to STRING to allow free text input
            $table->string('pengirim')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penerimaan_barangs', function (Blueprint $table) {
            // Revert back to ENUM
            $table->enum('pengirim', ['Kurir', 'Client', 'Lainnya'])->change();
        });
    }
};