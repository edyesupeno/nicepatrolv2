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
        Schema::table('area_patrols', function (Blueprint $table) {
            // Add area_id column after project_id
            $table->foreignId('area_id')->after('project_id')->nullable()->constrained('areas')->onDelete('cascade');
            
            // Add index for better performance
            $table->index(['area_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('area_patrols', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropIndex(['area_id', 'is_active']);
            $table->dropColumn('area_id');
        });
    }
};