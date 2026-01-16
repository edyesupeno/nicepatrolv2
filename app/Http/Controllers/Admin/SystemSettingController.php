<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemSettingController extends Controller
{
    public function index()
    {
        // Check if user is superadmin
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $settings = SystemSetting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Check if user is superadmin
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_title' => 'nullable|string|max:255',
            'app_description' => 'nullable|string',
            'app_keywords' => 'nullable|string',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'app_favicon' => 'nullable|image|mimes:ico,png|max:1024',
            'footer_text' => 'nullable|string|max:255',
            'copyright_text' => 'nullable|string|max:255',
        ], [
            'app_name.max' => 'Nama aplikasi maksimal 255 karakter',
            'app_title.max' => 'Title maksimal 255 karakter',
            'app_logo.image' => 'Logo harus berupa gambar',
            'app_logo.mimes' => 'Logo harus berformat jpeg, png, jpg, atau svg',
            'app_logo.max' => 'Ukuran logo maksimal 2MB',
            'app_favicon.image' => 'Favicon harus berupa gambar',
            'app_favicon.mimes' => 'Favicon harus berformat ico atau png',
            'app_favicon.max' => 'Ukuran favicon maksimal 1MB',
        ]);

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            // Delete old logo
            $oldLogo = SystemSetting::get('app_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $logoPath = $request->file('app_logo')->store('settings', 'public');
            SystemSetting::set('app_logo', $logoPath);
        }

        // Handle favicon upload
        if ($request->hasFile('app_favicon')) {
            // Delete old favicon
            $oldFavicon = SystemSetting::get('app_favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }

            $faviconPath = $request->file('app_favicon')->store('settings', 'public');
            SystemSetting::set('app_favicon', $faviconPath);
        }

        // Update text settings
        foreach (['app_name', 'app_title', 'app_description', 'app_keywords', 'footer_text', 'copyright_text'] as $key) {
            if ($request->has($key)) {
                SystemSetting::set($key, $request->input($key));
            }
        }

        // Clear cache
        SystemSetting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan sistem berhasil diupdate');
    }
}
