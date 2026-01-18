<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aset_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patroli_detail_id')->constrained()->onDelete('cascade');
            $table->foreignId('aset_kawasan_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['aman', 'rusak', 'hilang'])->default('aman');
            $table->text('catatan')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();

            // Index untuk query yang sering digunakan
            $table->index(['patroli_detail_id', 'aset_kawasan_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aset_checks');
    }
};
