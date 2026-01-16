<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('perusahaan_id', auth()->user()->perusahaan_id)
            ->where('role', '!=', 'superadmin')
            ->paginate(10);
            
        return view('perusahaan.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:admin,petugas',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'role.required' => 'Role wajib dipilih',
        ]);

        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('perusahaan.users.index')
            ->with('success', 'Petugas berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        // Pastikan user adalah milik perusahaan yang sama
        if ($user->perusahaan_id !== auth()->user()->perusahaan_id) {
            abort(404);
        }
        
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        // Pastikan user adalah milik perusahaan yang sama
        if ($user->perusahaan_id !== auth()->user()->perusahaan_id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'role' => 'required|in:admin,petugas',
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'role.required' => 'Role wajib dipilih',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('perusahaan.users.index')
            ->with('success', 'Petugas berhasil diupdate');
    }

    public function destroy(User $user)
    {
        // Pastikan user adalah milik perusahaan yang sama
        if ($user->perusahaan_id !== auth()->user()->perusahaan_id) {
            abort(404);
        }

        // Tidak bisa hapus diri sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('perusahaan.users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $user->delete();

        return redirect()->route('perusahaan.users.index')
            ->with('success', 'Petugas berhasil dihapus');
    }
}
