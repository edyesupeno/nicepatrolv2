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
        // Untuk PostgreSQL, kita perlu drop constraint lama dan buat baru
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
        
        // Tambahkan constraint baru dengan roles yang diperluas
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('superadmin', 'admin', 'security_officer', 'office_employee', 'manager_project', 'admin_project', 'admin_branch', 'finance_branch', 'admin_hsse', 'petugas'))");
        
        // Update default value
        DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'security_officer'");
        
        // Update existing 'petugas' users to 'security_officer' (optional)
        // DB::table('users')->where('role', 'petugas')->update(['role' => 'security_officer']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke constraint lama
        DB::statement("ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_check");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('superadmin', 'admin', 'petugas'))");
        DB::statement("ALTER TABLE users ALTER COLUMN role SET DEFAULT 'petugas'");
    }
};
