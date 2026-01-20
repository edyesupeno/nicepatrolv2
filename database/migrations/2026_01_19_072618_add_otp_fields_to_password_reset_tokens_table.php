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
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('password_reset_tokens', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('password_reset_tokens', 'otp_code')) {
                $table->string('otp_code', 6)->after('token');
            }
            if (!Schema::hasColumn('password_reset_tokens', 'expires_at')) {
                $table->timestamp('expires_at')->after('otp_code');
            }
            if (!Schema::hasColumn('password_reset_tokens', 'is_used')) {
                $table->boolean('is_used')->default(false)->after('expires_at');
            }
            if (!Schema::hasColumn('password_reset_tokens', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
        
        // Add indexes separately
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->index('email');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropIndex(['password_reset_tokens_email_index']);
            $table->dropIndex(['password_reset_tokens_phone_index']);
            $table->dropIndex(['password_reset_tokens_email_token_index']);
            $table->dropIndex(['password_reset_tokens_phone_token_index']);
            
            $table->dropColumn(['id', 'phone', 'otp_code', 'expires_at', 'is_used', 'created_at', 'updated_at']);
        });
    }
};
