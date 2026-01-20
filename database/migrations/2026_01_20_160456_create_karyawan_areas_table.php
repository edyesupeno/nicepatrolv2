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
        // Buat tabel pivot untuk area kerja karyawan (many-to-many)
        if (!Schema::hasTable('karyawan_areas')) {
            Schema::create('karyawan_areas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
                $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
                $table->boolean('is_primary')->default(false); // Area utama karyawan
                $table->timestamps();
                
                // Unique constraint untuk mencegah duplikasi
                $table->unique(['karyawan_id', 'area_id']);
                
                // Index untuk performance
                $table->index(['karyawan_id', 'is_primary']);
                $table->index('area_id');
            });

            echo "✅ Created karyawan_areas pivot table\n";

            // Populate area kerja default (semua area di project karyawan)
            $this->populateDefaultAreas();
        } else {
            echo "ℹ️  karyawan_areas table already exists\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tabel pivot area kerja
        Schema::dropIfExists('karyawan_areas');
        echo "✅ Dropped karyawan_areas pivot table\n";
    }

    /**
     * Populate area kerja default untuk karyawan
     */
    private function populateDefaultAreas(): void
    {
        try {
            // Get karyawan yang sudah punya project_id dan aktif
            $karyawans = DB::table('karyawans')
                ->whereNotNull('project_id')
                ->where('is_active', true)
                ->get();

            $totalKaryawan = $karyawans->count();
            $processedKaryawan = 0;

            echo "📊 Processing {$totalKaryawan} active karyawans...\n";

            foreach ($karyawans as $karyawan) {
                // Get semua area di project karyawan
                $areas = DB::table('areas')
                    ->where('project_id', $karyawan->project_id)
                    ->get();

                if ($areas->count() > 0) {
                    foreach ($areas as $index => $area) {
                        // Insert ke karyawan_areas (gunakan insertOrIgnore untuk menghindari error duplikasi)
                        DB::table('karyawan_areas')->insertOrIgnore([
                            'karyawan_id' => $karyawan->id,
                            'area_id' => $area->id,
                            'is_primary' => $index === 0, // Area pertama jadi primary
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    $processedKaryawan++;
                    echo "   ✅ Karyawan ID {$karyawan->id}: {$areas->count()} areas assigned\n";
                } else {
                    echo "   ⚠️  Karyawan ID {$karyawan->id}: No areas found in project {$karyawan->project_id}\n";
                }
            }

            echo "✅ Successfully populated areas for {$processedKaryawan}/{$totalKaryawan} karyawans\n";
        } catch (\Exception $e) {
            echo "❌ Error populating areas: " . $e->getMessage() . "\n";
            echo "   This is not critical - you can manually assign areas later\n";
        }
    }
};