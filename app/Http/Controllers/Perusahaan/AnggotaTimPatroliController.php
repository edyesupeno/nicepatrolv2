<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\AnggotaTimPatroli;
use App\Models\TimPatroli;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnggotaTimPatroliController extends Controller
{
    public function index(TimPatroli $timPatroli)
    {
        $anggota = AnggotaTimPatroli::with(['user:id,name,email'])
            ->where('tim_patroli_id', $timPatroli->id)
            ->latest()
            ->paginate(15);

        return view('perusahaan.tim-patroli.anggota.index', compact('timPatroli', 'anggota'));
    }

    public function create(TimPatroli $timPatroli)
    {
        // Get users yang:
        // 1. Role security officer
        // 2. Belum jadi anggota tim patroli aktif lainnya
        // 3. Belum jadi leader tim patroli lainnya
        $availableUsers = User::select('id', 'name', 'email')
            ->where('perusahaan_id', auth()->user()->perusahaan_id)
            ->where('is_active', true)
            ->where('role', 'security_officer')
            ->whereNotIn('id', function($query) {
                // Exclude users yang sudah jadi anggota tim aktif
                $query->select('user_id')
                    ->from('anggota_tim_patroli')
                    ->where('is_active', true);
            })
            ->whereNotIn('id', function($query) {
                // Exclude users yang sudah jadi leader tim aktif
                $query->select('leader_id')
                    ->from('tim_patrolis')
                    ->where('is_active', true)
                    ->whereNotNull('leader_id');
            })
            ->orderBy('name')
            ->get();

        return view('perusahaan.tim-patroli.anggota.create', compact('timPatroli', 'availableUsers'));
    }

    public function store(Request $request, TimPatroli $timPatroli)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) use ($timPatroli) {
                    // Check if user is security officer
                    $user = User::find($value);
                    if (!$user || $user->role !== 'security_officer') {
                        $fail('User harus memiliki role security officer.');
                        return;
                    }
                    
                    // Check if user belongs to same company
                    if ($user->perusahaan_id !== auth()->user()->perusahaan_id) {
                        $fail('User harus dari perusahaan yang sama.');
                        return;
                    }
                    
                    // Check if user is already a leader in another active team
                    $isLeaderElsewhere = \App\Models\TimPatroli::where('leader_id', $value)
                        ->where('is_active', true)
                        ->where('id', '!=', $timPatroli->id)
                        ->exists();
                    
                    if ($isLeaderElsewhere) {
                        $fail('User sudah menjadi leader di tim patroli lain.');
                        return;
                    }
                }
            ],
            'role' => 'required|in:anggota,wakil_leader',
            'tanggal_bergabung' => 'required|date',
            'catatan' => 'nullable|string|max:500',
        ], [
            'user_id.required' => 'User wajib dipilih',
            'role.required' => 'Role wajib dipilih',
            'tanggal_bergabung.required' => 'Tanggal bergabung wajib diisi',
        ]);

        // Check if user already active in another team
        $existingMember = AnggotaTimPatroli::where('user_id', $validated['user_id'])
            ->where('is_active', true)
            ->first();

        if ($existingMember) {
            return back()->withErrors(['user_id' => 'User sudah menjadi anggota tim patroli aktif lainnya']);
        }

        // Check if role wakil_leader already exists in this team
        if ($validated['role'] === 'wakil_leader') {
            $existingWakilLeader = AnggotaTimPatroli::where('tim_patroli_id', $timPatroli->id)
                ->where('role', 'wakil_leader')
                ->where('is_active', true)
                ->first();

            if ($existingWakilLeader) {
                return back()->withErrors(['role' => 'Tim ini sudah memiliki wakil leader aktif']);
            }
        }

        $validated['tim_patroli_id'] = $timPatroli->id;
        $validated['is_active'] = true;

        AnggotaTimPatroli::create($validated);

        return redirect()->route('perusahaan.tim-patroli.anggota.index', $timPatroli->hash_id)
            ->with('success', 'Anggota tim berhasil ditambahkan');
    }

    public function show(TimPatroli $timPatroli, AnggotaTimPatroli $anggotaTimPatroli)
    {
        $anggotaTimPatroli->load(['user', 'timPatroli']);
        
        return view('perusahaan.tim-patroli.anggota.show', compact('timPatroli', 'anggotaTimPatroli'));
    }

    public function edit(TimPatroli $timPatroli, AnggotaTimPatroli $anggotaTimPatroli)
    {
        $anggotaTimPatroli->load(['user']);
        
        return view('perusahaan.tim-patroli.anggota.edit', compact('timPatroli', 'anggotaTimPatroli'));
    }

    public function update(Request $request, TimPatroli $timPatroli, AnggotaTimPatroli $anggotaTimPatroli)
    {
        $validated = $request->validate([
            'role' => 'required|in:anggota,wakil_leader',
            'tanggal_bergabung' => 'required|date',
            'tanggal_keluar' => 'nullable|date|after_or_equal:tanggal_bergabung',
            'is_active' => 'required|boolean',
            'catatan' => 'nullable|string|max:500',
        ], [
            'role.required' => 'Role wajib dipilih',
            'tanggal_bergabung.required' => 'Tanggal bergabung wajib diisi',
            'tanggal_keluar.after_or_equal' => 'Tanggal keluar harus setelah atau sama dengan tanggal bergabung',
            'is_active.required' => 'Status wajib dipilih',
        ]);

        // Check if role wakil_leader already exists in this team (exclude current member)
        if ($validated['role'] === 'wakil_leader' && $validated['is_active']) {
            $existingWakilLeader = AnggotaTimPatroli::where('tim_patroli_id', $timPatroli->id)
                ->where('role', 'wakil_leader')
                ->where('is_active', true)
                ->where('id', '!=', $anggotaTimPatroli->id)
                ->first();

            if ($existingWakilLeader) {
                return back()->withErrors(['role' => 'Tim ini sudah memiliki wakil leader aktif lainnya']);
            }
        }

        // Auto set tanggal_keluar if status changed to inactive
        if (!$validated['is_active'] && $anggotaTimPatroli->is_active && !$validated['tanggal_keluar']) {
            $validated['tanggal_keluar'] = now()->format('Y-m-d');
        }

        $anggotaTimPatroli->update($validated);

        return redirect()->route('perusahaan.tim-patroli.anggota.index', $timPatroli->hash_id)
            ->with('success', 'Data anggota tim berhasil diupdate');
    }

    public function destroy(TimPatroli $timPatroli, AnggotaTimPatroli $anggotaTimPatroli)
    {
        $anggotaTimPatroli->delete();

        return redirect()->route('perusahaan.tim-patroli.anggota.index', $timPatroli->hash_id)
            ->with('success', 'Anggota tim berhasil dihapus');
    }

    public function nonaktifkan(TimPatroli $timPatroli, AnggotaTimPatroli $anggotaTimPatroli)
    {
        $anggotaTimPatroli->update([
            'is_active' => false,
            'tanggal_keluar' => now()->format('Y-m-d')
        ]);

        return redirect()->route('perusahaan.tim-patroli.anggota.index', $timPatroli->hash_id)
            ->with('success', 'Anggota tim berhasil dinonaktifkan');
    }

    public function aktifkan(TimPatroli $timPatroli, AnggotaTimPatroli $anggotaTimPatroli)
    {
        // Check if user already active in another team
        $existingMember = AnggotaTimPatroli::where('user_id', $anggotaTimPatroli->user_id)
            ->where('is_active', true)
            ->where('id', '!=', $anggotaTimPatroli->id)
            ->first();

        if ($existingMember) {
            return back()->withErrors(['error' => 'User sudah menjadi anggota tim patroli aktif lainnya']);
        }

        // Check if user is still security officer and from same company
        $user = User::find($anggotaTimPatroli->user_id);
        if (!$user || $user->role !== 'security_officer') {
            return back()->withErrors(['error' => 'User harus memiliki role security officer']);
        }

        if ($user->perusahaan_id !== auth()->user()->perusahaan_id) {
            return back()->withErrors(['error' => 'User harus dari perusahaan yang sama']);
        }

        // Check if user is already a leader in another active team
        $isLeaderElsewhere = \App\Models\TimPatroli::where('leader_id', $anggotaTimPatroli->user_id)
            ->where('is_active', true)
            ->where('id', '!=', $timPatroli->id)
            ->exists();

        if ($isLeaderElsewhere) {
            return back()->withErrors(['error' => 'User sudah menjadi leader di tim patroli lain']);
        }

        $anggotaTimPatroli->update([
            'is_active' => true,
            'tanggal_keluar' => null
        ]);

        return redirect()->route('perusahaan.tim-patroli.anggota.index', $timPatroli->hash_id)
            ->with('success', 'Anggota tim berhasil diaktifkan kembali');
    }
}