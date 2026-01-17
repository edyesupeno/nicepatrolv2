<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kehadirans', function (Blueprint $table) {
            // Add radius tracking for check-in and check-out
            $table->boolean('on_radius_masuk')->default(true)->after('on_radius');
            $table->boolean('on_radius_keluar')->default(true)->after('on_radius_masuk');
            $table->integer('jarak_masuk')->nullable()->after('on_radius_keluar'); // distance in meters
            $table->integer('jarak_keluar')->nullable()->after('jarak_masuk'); // distance in meters
            
            // Add coordinates for check-in and check-out
            $table->decimal('latitude_masuk', 10, 8)->nullable()->after('jarak_keluar');
            $table->decimal('longitude_masuk', 11, 8)->nullable()->after('latitude_masuk');
            $table->decimal('latitude_keluar', 10, 8)->nullable()->after('longitude_masuk');
            $table->decimal('longitude_keluar', 11, 8)->nullable()->after('latitude_keluar');
        });
    }

    public function down(): void
    {
        Schema::table('kehadirans', function (Blueprint $table) {
            $table->dropColumn([
                'on_radius_masuk',
                'on_radius_keluar', 
                'jarak_masuk',
                'jarak_keluar',
                'latitude_masuk',
                'longitude_masuk',
                'latitude_keluar',
                'longitude_keluar'
            ]);
        });
    }
};