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
        Schema::table('atensi_recipients', function (Blueprint $table) {
            // Composite index for atensi_id queries (most common)
            $table->index(['atensi_id', 'read_at'], 'idx_atensi_read');
            $table->index(['atensi_id', 'acknowledged_at'], 'idx_atensi_acknowledged');
            
            // Index for user-based queries
            $table->index(['user_id', 'read_at'], 'idx_user_read');
            $table->index(['user_id', 'acknowledged_at'], 'idx_user_acknowledged');
            
            // Index for status filtering
            $table->index('read_at', 'idx_read_at');
            $table->index('acknowledged_at', 'idx_acknowledged_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atensi_recipients', function (Blueprint $table) {
            $table->dropIndex('idx_atensi_read');
            $table->dropIndex('idx_atensi_acknowledged');
            $table->dropIndex('idx_user_read');
            $table->dropIndex('idx_user_acknowledged');
            $table->dropIndex('idx_read_at');
            $table->dropIndex('idx_acknowledged_at');
        });
    }
};