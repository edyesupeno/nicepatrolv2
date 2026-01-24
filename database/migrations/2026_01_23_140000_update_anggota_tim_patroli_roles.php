<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update enum to include 'leader' role
        DB::statement("ALTER TABLE anggota_tim_patroli DROP CONSTRAINT IF EXISTS anggota_tim_patroli_role_check");
        DB::statement("ALTER TABLE anggota_tim_patroli ADD CONSTRAINT anggota_tim_patroli_role_check CHECK (role IN ('leader', 'wakil_leader', 'anggota'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE anggota_tim_patroli DROP CONSTRAINT IF EXISTS anggota_tim_patroli_role_check");
        DB::statement("ALTER TABLE anggota_tim_patroli ADD CONSTRAINT anggota_tim_patroli_role_check CHECK (role IN ('anggota', 'wakil_leader'))");
    }
};