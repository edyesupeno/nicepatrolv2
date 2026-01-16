<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $query = User::with('perusahaan');

        if (!auth()->user()->isSuperAdmin()) {
            $query->where('perusahaan_id', auth()->user()->perusahaan_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:superadmin,admin,petugas',
            'perusahaan_id' => 'nullable|exists:perusahaans,id',
            'is_active' => 'boolean',
        ]);

        if (!auth()->user()->isSuperAdmin() && $validated['role'] === 'superadmin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if (!auth()->user()->isSuperAdmin()) {
            $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        return response()->json($user->load('perusahaan'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'in:superadmin,admin,petugas',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'User berhasil dihapus']);
    }
}
