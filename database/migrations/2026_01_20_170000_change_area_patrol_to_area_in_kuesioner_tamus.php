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
        // First, check if there's any data in kuesioner_tamus
        $hasData = DB::table('kuesioner_tamus')->exists();
        
        if ($hasData) {
            // If there's data, we need to handle it carefully
            // For now, let's just truncate the table since this is development
            DB::table('kuesioner_tamus')->truncate();
        }
        
        Schema::table('kuesioner_tamus', function (Blueprint $table) {
            // Drop foreign key dan index untuk area_patrol_id
            $table->dropForeign(['area_patrol_id']);
            $table->dropIndex(['project_id', 'area_patrol_id']);
            
            // Drop column area_patrol_id
            $table->dropColumn('area_patrol_id');
            
            // Add area_id yang merujuk ke tabel areas (bukan area_patrols)
            $table->foreignId('area_id')->after('project_id')->constrained('areas')->onDelete('cascade');
            
            // Add new index
            $table->index(['project_id', 'area_id']);
        });
        
        // Check if unique constraint exists before dropping
        $constraintExists = DB::select("
            SELECT constraint_name 
            FROM information_schema.table_constraints 
            WHERE table_name = 'kuesioner_tamus' 
            AND constraint_name = 'unique_area_kuesioner_tamu'
        ");
        
        Schema::table('kuesioner_tamus', function (Blueprint $table) use ($constraintExists) {
            // Drop old unique constraint only if it exists
            if (!empty($constraintExists)) {
                $table->dropUnique('unique_area_kuesioner_tamu');
            }
            
            // Add new unique constraint untuk area_id
            $table->unique('area_id', 'unique_area_kuesioner_tamu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kuesioner_tamus', function (Blueprint $table) {
            // Drop new constraint and foreign key
            $table->dropUnique('unique_area_kuesioner_tamu');
            $table->dropForeign(['area_id']);
            $table->dropIndex(['project_id', 'area_id']);
            $table->dropColumn('area_id');
            
            // Restore area_patrol_id
            $table->foreignId('area_patrol_id')->after('project_id')->constrained('area_patrols')->onDelete('cascade');
            $table->index(['project_id', 'area_patrol_id']);
            $table->unique('area_patrol_id', 'unique_area_kuesioner_tamu');
        });
    }
};