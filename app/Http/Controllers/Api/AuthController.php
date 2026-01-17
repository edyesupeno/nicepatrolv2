<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::with(['karyawan.jabatan'])->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif',
            ], 403);
        }

        // Check if user has mobile access (security_officer or office_employee)
        if (!$user->isSecurityOfficer() && !$user->isOfficeEmployee()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke aplikasi mobile',
            ], 403);
        }

        // Delete old tokens (optional - untuk single device login)
        // $user->tokens()->delete();

        // Create token
        $token = $user->createToken('mobile-app')->plainTextToken;

        // Get foto from karyawan
        $foto = null;
        if ($user->karyawan && $user->karyawan->foto) {
            $foto = asset('storage/' . $user->karyawan->foto);
        }

        // Get jabatan name from karyawan
        $jabatanName = null;
        if ($user->karyawan && $user->karyawan->jabatan) {
            $jabatanName = $user->karyawan->jabatan->nama;
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_display' => $user->getRoleDisplayName(),
                    'perusahaan_id' => $user->perusahaan_id,
                    'foto' => $foto,
                    'jabatan_name' => $jabatanName,
                    'perusahaan' => $user->perusahaan ? [
                        'id' => $user->perusahaan->id,
                        'nama' => $user->perusahaan->nama_perusahaan,
                    ] : null,
                ],
                'token' => $token,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        
        // Load karyawan dengan jabatan, pastikan global scope jabatan bekerja
        $user->load(['karyawan' => function($query) use ($user) {
            $query->with(['jabatan' => function($jabatanQuery) use ($user) {
                // Pastikan jabatan query menggunakan perusahaan_id yang benar
                $jabatanQuery->where('jabatans.perusahaan_id', $user->perusahaan_id);
            }]);
        }]);
        
        // Get foto from karyawan
        $foto = null;
        if ($user->karyawan && $user->karyawan->foto) {
            $foto = asset('storage/' . $user->karyawan->foto);
        }
        
        // Get jabatan name from karyawan
        $jabatanName = null;
        if ($user->karyawan && $user->karyawan->jabatan) {
            $jabatanName = $user->karyawan->jabatan->nama;
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'role_display' => $user->getRoleDisplayName(),
                'perusahaan_id' => $user->perusahaan_id,
                'foto' => $foto,
                'jabatan_name' => $jabatanName,
                'perusahaan' => $user->perusahaan ? [
                    'id' => $user->perusahaan->id,
                    'nama' => $user->perusahaan->nama_perusahaan,
                ] : null,
            ],
        ]);
    }

    public function me(Request $request)
    {
        return $this->user($request);
    }
    
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // max 10MB
        ]);
        
        $user = $request->user()->load('karyawan');
        
        if (!$user->karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan',
            ], 404);
        }
        
        try {
            // Delete old photo if exists
            \App\Helpers\ImageHelper::delete($user->karyawan->foto);
            
            // Compress and save new photo (max 100KB, max width 800px)
            $path = \App\Helpers\ImageHelper::compressAndSave(
                $request->file('foto'),
                'karyawan/foto',
                100, // max 100KB
                800, // max width 800px
                85   // initial quality 85%
            );
            
            // Update karyawan foto
            $user->karyawan->update(['foto' => $path]);
            
            $fotoUrl = \App\Helpers\ImageHelper::url($path);
            
            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diupload dan dikompres',
                'data' => [
                    'foto_url' => $fotoUrl,
                    'foto_path' => $path,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupload foto: ' . $e->getMessage(),
            ], 500);
        }
    }
}
