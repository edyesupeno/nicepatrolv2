<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManifestController extends Controller
{
    public function manifest()
    {
        // Get app icon from settings
        $appIcon = setting('app_favicon');
        $iconUrl = $appIcon 
            ? asset('storage/' . $appIcon) 
            : asset('favicon.png');
        
        // Get app name from settings
        $appName = setting('app_name', 'Nice Patrol');
        
        // Get theme color from settings or use default
        $themeColor = setting('app_primary_color', '#0071CE'); // Nice Patrol blue
        
        $manifest = [
            'name' => $appName,
            'short_name' => $appName,
            'description' => 'Aplikasi Patroli Security',
            'start_url' => '/login',
            'scope' => '/',
            'display' => 'fullscreen',
            'display_override' => ['fullscreen', 'standalone'],
            'background_color' => '#ffffff',
            'theme_color' => $themeColor,
            'orientation' => 'portrait-primary',
            'icons' => [
                [
                    'src' => $iconUrl,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ],
                [
                    'src' => $iconUrl,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ]
            ]
        ];
        
        return response()->json($manifest)
            ->header('Content-Type', 'application/manifest+json');
    }
}
