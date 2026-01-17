<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManifestController extends Controller
{
    public function manifest(Request $request)
    {
        // Get app settings (you can customize these based on your settings)
        $appName = setting('app_name', 'Nice Patrol');
        $appDescription = setting('app_description', 'Sistem Manajemen Patroli dan Kehadiran');
        $themeColor = setting('app_primary_color', '#0071CE');
        $backgroundColor = setting('app_background_color', '#ffffff');
        
        // Get app icon
        $appIcon = setting('app_favicon') ? asset('storage/' . setting('app_favicon')) : asset('favicon.png');
        
        $manifest = [
            'name' => $appName,
            'short_name' => $appName,
            'description' => $appDescription,
            'start_url' => '/',
            'display' => 'standalone',
            'orientation' => 'portrait',
            'theme_color' => $themeColor,
            'background_color' => $backgroundColor,
            'scope' => '/',
            'lang' => 'id',
            'categories' => ['business', 'productivity'],
            'icons' => [
                [
                    'src' => $appIcon,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ],
                [
                    'src' => $appIcon,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ]
            ],
            'screenshots' => [
                [
                    'src' => asset('images/screenshot-mobile.png'),
                    'sizes' => '390x844',
                    'type' => 'image/png',
                    'form_factor' => 'narrow'
                ]
            ],
            'shortcuts' => [
                [
                    'name' => 'Absensi',
                    'short_name' => 'Absensi',
                    'description' => 'Lihat kehadiran',
                    'url' => '/employee/kehadiran',
                    'icons' => [
                        [
                            'src' => $appIcon,
                            'sizes' => '96x96'
                        ]
                    ]
                ],
                [
                    'name' => 'Patroli',
                    'short_name' => 'Patroli',
                    'description' => 'Mulai patroli',
                    'url' => '/security/patroli',
                    'icons' => [
                        [
                            'src' => $appIcon,
                            'sizes' => '96x96'
                        ]
                    ]
                ]
            ]
        ];
        
        return response()->json($manifest)
            ->header('Content-Type', 'application/manifest+json');
    }
}