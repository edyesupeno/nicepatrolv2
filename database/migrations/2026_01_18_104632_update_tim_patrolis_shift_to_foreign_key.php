<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tim_patrolis', function (Blueprint $table) {
            // Drop the old enum column
            $table->dropColumn('shift');
        });
        
        Schema::table('tim_patrolis', function (Blueprint $table) {
            // Add new foreign key column
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->onDelete('set null')->after('nama_tim');
        });
    }

    public function down(): void
    {
        Schema::table('tim_patrolis', function (Blueprint $table) {
            // Drop the foreign key column
            $table->dropForeign(['shift_id']);
            $table->dropColumn('shift_id');
        });
        
        Schema::table('tim_patrolis', function (Blueprint $table) {
            // Restore the old enum column
            $table->enum('shift', ['pagi', 'siang', 'malam'])->default('pagi')->after('nama_tim');
        });
    }
};
