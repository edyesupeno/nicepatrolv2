<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the problematic unique constraint
        Schema::table('rekenings', function (Blueprint $table) {
            $table->dropUnique('unique_primary_per_project');
        });

        // Create a partial unique index that only applies when is_primary = true
        DB::statement('CREATE UNIQUE INDEX unique_primary_per_project ON rekenings (project_id) WHERE is_primary = true');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the partial unique index
        DB::statement('DROP INDEX IF EXISTS unique_primary_per_project');

        // Recreate the original constraint (though it was problematic)
        Schema::table('rekenings', function (Blueprint $table) {
            $table->unique(['project_id', 'is_primary'], 'unique_primary_per_project');
        });
    }
};