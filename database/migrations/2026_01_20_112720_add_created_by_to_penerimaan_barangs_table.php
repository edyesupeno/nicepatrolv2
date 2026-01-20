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
            // Add created_by field as nullable first
            $table->foreignId('created_by')->nullable()->after('perusahaan_id')->constrained('users')->onDelete('cascade');
        });
        
        // Update existing records to set created_by to first admin user
        $firstAdminUser = \App\Models\User::where('role', 'admin')->first();
        if ($firstAdminUser) {
            \DB::table('penerimaan_barangs')
                ->whereNull('created_by')
                ->update(['created_by' => $firstAdminUser->id]);
        }
        
        // Now make the field required
        Schema::table('penerimaan_barangs', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable(false)->change();
            
            // Add indexes for performance optimization
            $table->index(['perusahaan_id', 'created_by']);
            $table->index(['created_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penerimaan_barangs', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropIndex(['perusahaan_id', 'created_by']);
            $table->dropIndex(['created_by', 'created_at']);
            $table->dropColumn('created_by');
        });
    }
};