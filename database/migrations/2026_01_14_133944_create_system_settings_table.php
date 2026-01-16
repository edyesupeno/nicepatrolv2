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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('text'); // text, textarea, image, file
            $table->string('group')->default('general'); // general, appearance, seo
            $table->timestamps();
        });

        // Insert default settings
        DB::table('system_settings')->insert([
            [
                'key' => 'app_name',
                'value' => 'Nice Patrol',
                'type' => 'text',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_title',
                'value' => 'Nice Patrol - Sistem Manajemen Patroli Keamanan',
                'type' => 'text',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_description',
                'value' => 'Sistem manajemen patroli keamanan berbasis SaaS untuk perusahaan security',
                'type' => 'textarea',
                'group' => 'seo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_keywords',
                'value' => 'patroli, keamanan, security, manajemen, saas',
                'type' => 'text',
                'group' => 'seo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_favicon',
                'value' => null,
                'type' => 'image',
                'group' => 'appearance',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'footer_text',
                'value' => 'Nice Patrol - Sistem Manajemen Patroli Keamanan',
                'type' => 'text',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'copyright_text',
                'value' => '© 2026 Nice Patrol. All rights reserved.',
                'type' => 'text',
                'group' => 'general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
