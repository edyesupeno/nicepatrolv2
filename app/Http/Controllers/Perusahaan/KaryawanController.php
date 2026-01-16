<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Project;
use App\Models\Jabatan;
use App\Models\StatusKaryawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with(['project', 'jabatan.projects', 'user']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_karyawan', 'like', "%{$search}%")
                  ->orWhere('nik_ktp', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status
        if ($request->filled('status_karyawan')) {
            $query->where('status_karyawan', $request->status_karyawan);
        }

        // Filter by jabatan
        if ($request->filled('jabatan_id')) {
            $query->where('jabatan_id', $request->jabatan_id);
        }

        // Filter by status aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $karyawans = $query->orderBy('created_at', 'desc')->paginate(10);

        $projects = Project::where('perusahaan_id', auth()->user()->perusahaan_id)->get();
        $jabatans = Jabatan::where('perusahaan_id', auth()->user()->perusahaan_id)->get();
        $statusKaryawans = StatusKaryawan::all();

        return view('perusahaan.karyawans.index', compact('karyawans', 'projects', 'jabatans', 'statusKaryawans'));
    }

    public function show($hashId)
    {
        // Decode hash_id to get real id
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        
        if (!$id) {
            abort(404);
        }
        
        $karyawan = Karyawan::with(['project', 'jabatan.projects', 'user', 'perusahaan', 'pengalamanKerjas', 'pendidikans', 'sertifikasis', 'medicalCheckups'])
                           ->findOrFail($id);

        $projects = Project::where('perusahaan_id', auth()->user()->perusahaan_id)->get();
        $jabatans = Jabatan::with('projects')
                          ->where('perusahaan_id', auth()->user()->perusahaan_id)
                          ->get();
        $statusKaryawans = StatusKaryawan::all();

        return view('perusahaan.karyawans.show', compact('karyawan', 'projects', 'jabatans', 'statusKaryawans'));
    }

    public function store(Request $request)
    {
        // Validasi dasar
        $rules = [
            // Data Dasar
            'nik_karyawan' => 'required|string|max:255|unique:karyawans,nik_karyawan',
            'project_id' => 'required|exists:projects,id',
            'status_karyawan' => 'required|string',
            'jabatan_id' => 'required|exists:jabatans,id',
            'tanggal_masuk' => 'required|date',
            'is_active' => 'required|boolean',
            
            // Data Pribadi
            'nama_lengkap' => 'required|string|max:255',
            'nik_ktp' => 'required|string|size:16|unique:karyawans,nik_ktp',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'status_perkawinan' => 'required|in:TK,K',
            'jumlah_tanggungan' => 'required|integer|min:0|max:3',
            'telepon' => 'required|string|max:20',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            
            // Akun Login
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];

        // Validasi conditional untuk tanggal_keluar
        if (stripos($request->status_karyawan, 'kontrak') !== false) {
            $rules['tanggal_keluar'] = 'required|date|after:tanggal_masuk';
        } else {
            $rules['tanggal_keluar'] = 'nullable|date|after:tanggal_masuk';
        }

        $validated = $request->validate($rules, [
            'nik_karyawan.required' => 'NIK Karyawan wajib diisi',
            'nik_karyawan.unique' => 'NIK Karyawan sudah terdaftar',
            'project_id.required' => 'Project wajib dipilih',
            'project_id.exists' => 'Project tidak valid',
            'status_karyawan.required' => 'Status Karyawan wajib dipilih',
            'jabatan_id.required' => 'Jabatan wajib dipilih',
            'tanggal_masuk.required' => 'Tanggal Masuk wajib diisi',
            'tanggal_keluar.required' => 'Tanggal berakhir kontrak wajib diisi untuk karyawan kontrak',
            'tanggal_keluar.after' => 'Tanggal berakhir kontrak harus setelah tanggal masuk',
            'is_active.required' => 'Status Aktif wajib dipilih',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi',
            'nik_ktp.required' => 'NIK KTP wajib diisi',
            'nik_ktp.size' => 'NIK KTP harus 16 digit',
            'nik_ktp.unique' => 'NIK KTP sudah terdaftar',
            'tempat_lahir.required' => 'Tempat Lahir wajib diisi',
            'tanggal_lahir.required' => 'Tanggal Lahir wajib diisi',
            'jenis_kelamin.required' => 'Jenis Kelamin wajib dipilih',
            'telepon.required' => 'Telepon wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'kota.required' => 'Kota wajib diisi',
            'provinsi.required' => 'Provinsi wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        DB::beginTransaction();
        try {
            // Create user account
            $user = User::create([
                'perusahaan_id' => auth()->user()->perusahaan_id,
                'name' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'petugas',
                'is_active' => $validated['is_active'],
            ]);

            // Create karyawan
            $karyawan = Karyawan::create([
                'perusahaan_id' => auth()->user()->perusahaan_id,
                'user_id' => $user->id,
                'project_id' => $validated['project_id'],
                'nik_karyawan' => $validated['nik_karyawan'],
                'status_karyawan' => $validated['status_karyawan'],
                'jabatan_id' => $validated['jabatan_id'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'tanggal_keluar' => $validated['tanggal_keluar'] ?? null,
                'is_active' => $validated['is_active'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'nik_ktp' => $validated['nik_ktp'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'telepon' => $validated['telepon'],
                'alamat' => $validated['alamat'],
                'kota' => $validated['kota'],
                'provinsi' => $validated['provinsi'],
            ]);

            DB::commit();

            return redirect()->route('perusahaan.karyawans.index')
                            ->with('success', 'Karyawan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal menambahkan karyawan: ' . $e->getMessage());
        }
    }

    public function edit($hashId)
    {
        // Decode hash_id to get real id
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        
        if (!$id) {
            return response()->json(['error' => 'Invalid ID'], 404);
        }
        
        $karyawan = Karyawan::with('user')->findOrFail($id);

        return response()->json([
            'hash_id' => $karyawan->hash_id,
            'nik_karyawan' => $karyawan->nik_karyawan,
            'status_karyawan' => $karyawan->status_karyawan,
            'jabatan_id' => $karyawan->jabatan_id,
            'tanggal_masuk' => $karyawan->tanggal_masuk->format('Y-m-d'),
            'is_active' => $karyawan->is_active,
            'nama_lengkap' => $karyawan->nama_lengkap,
            'nik_ktp' => $karyawan->nik_ktp,
            'tempat_lahir' => $karyawan->tempat_lahir,
            'tanggal_lahir' => $karyawan->tanggal_lahir->format('Y-m-d'),
            'jenis_kelamin' => $karyawan->jenis_kelamin,
            'telepon' => $karyawan->telepon,
            'alamat' => $karyawan->alamat,
            'kota' => $karyawan->kota,
            'provinsi' => $karyawan->provinsi,
            'email' => $karyawan->user->email ?? '',
        ]);
    }

    public function update(Request $request, $hashId)
    {
        // Decode hash_id to get real id
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::with('user')->findOrFail($id);

        $validated = $request->validate([
            // Data Dasar
            'nik_karyawan' => 'required|string|max:255|unique:karyawans,nik_karyawan,' . $karyawan->id,
            'status_karyawan' => 'required|string',
            'jabatan_id' => 'required|exists:jabatans,id',
            'tanggal_masuk' => 'required|date',
            'is_active' => 'required|boolean',
            
            // Data Pribadi
            'nama_lengkap' => 'required|string|max:255',
            'nik_ktp' => 'required|string|size:16|unique:karyawans,nik_ktp,' . $karyawan->id,
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'status_perkawinan' => 'required|in:TK,K',
            'jumlah_tanggungan' => 'required|integer|min:0|max:3',
            'telepon' => 'required|string|max:20',
            'alamat' => 'required|string',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            
            // Akun Login
            'email' => 'required|email|unique:users,email,' . ($karyawan->user_id ?? 'NULL'),
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'nik_karyawan.required' => 'NIK Karyawan wajib diisi',
            'nik_karyawan.unique' => 'NIK Karyawan sudah terdaftar',
            'status_karyawan.required' => 'Status Karyawan wajib dipilih',
            'jabatan_id.required' => 'Jabatan wajib dipilih',
            'tanggal_masuk.required' => 'Tanggal Masuk wajib diisi',
            'is_active.required' => 'Status Aktif wajib dipilih',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi',
            'nik_ktp.required' => 'NIK KTP wajib diisi',
            'nik_ktp.size' => 'NIK KTP harus 16 digit',
            'nik_ktp.unique' => 'NIK KTP sudah terdaftar',
            'tempat_lahir.required' => 'Tempat Lahir wajib diisi',
            'tanggal_lahir.required' => 'Tanggal Lahir wajib diisi',
            'jenis_kelamin.required' => 'Jenis Kelamin wajib dipilih',
            'telepon.required' => 'Telepon wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'kota.required' => 'Kota wajib diisi',
            'provinsi.required' => 'Provinsi wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        DB::beginTransaction();
        try {
            // Update karyawan
            $karyawan->update([
                'nik_karyawan' => $validated['nik_karyawan'],
                'status_karyawan' => $validated['status_karyawan'],
                'jabatan_id' => $validated['jabatan_id'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'is_active' => $validated['is_active'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'nik_ktp' => $validated['nik_ktp'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'telepon' => $validated['telepon'],
                'alamat' => $validated['alamat'],
                'kota' => $validated['kota'],
                'provinsi' => $validated['provinsi'],
            ]);

            // Update user if exists
            if ($karyawan->user) {
                $userData = [
                    'name' => $validated['nama_lengkap'],
                    'email' => $validated['email'],
                    'is_active' => $validated['is_active'],
                ];

                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($validated['password']);
                }

                $karyawan->user->update($userData);
            }

            DB::commit();

            return redirect()->route('perusahaan.karyawans.index')
                            ->with('success', 'Data karyawan berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Gagal memperbarui karyawan: ' . $e->getMessage());
        }
    }

    public function destroy($hashId)
    {
        // Decode hash_id to get real id
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::with('user')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete user if exists
            if ($karyawan->user) {
                $karyawan->user->delete();
            }

            // Delete karyawan
            $karyawan->delete();

            DB::commit();

            return redirect()->route('perusahaan.karyawans.index')
                            ->with('success', 'Karyawan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', 'Gagal menghapus karyawan: ' . $e->getMessage());
        }
    }

    public function uploadFoto(Request $request, $hashId)
    {
        // Decode hash_id to get real id
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($id);

        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'foto.required' => 'Foto wajib dipilih',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto harus JPG, JPEG, atau PNG',
            'foto.max' => 'Ukuran foto maksimal 2MB',
        ]);

        try {
            // Delete old photo if exists
            if ($karyawan->foto && \Storage::disk('public')->exists($karyawan->foto)) {
                \Storage::disk('public')->delete($karyawan->foto);
            }

            // Store new photo
            $path = $request->file('foto')->store('karyawan-photos', 'public');
            
            $karyawan->update(['foto' => $path]);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Foto karyawan berhasil diperbarui', 'active_tab' => 'informasi']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal mengupload foto: ' . $e->getMessage());
        }
    }

    public function updateNama(Request $request, $hashId)
    {
        // Decode hash_id to get real id
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::with('user')->findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nama_lengkap.max' => 'Nama lengkap maksimal 255 karakter',
        ]);

        try {
            DB::beginTransaction();

            // Update karyawan
            $karyawan->update([
                'nama_lengkap' => $request->nama_lengkap,
            ]);

            // Update user name if exists
            if ($karyawan->user) {
                $karyawan->user->update([
                    'name' => $request->nama_lengkap,
                ]);
            }

            DB::commit();

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Nama karyawan berhasil diperbarui', 'active_tab' => 'informasi']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', 'Gagal memperbarui nama: ' . $e->getMessage());
        }
    }

    public function updatePekerjaan(Request $request, $hashId)
    {
        // Decode hash_id to get real id
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($id);

        // Validasi dasar
        $rules = [
            'project_id' => 'required|exists:projects,id',
            'jabatan_id' => 'required|exists:jabatans,id',
            'status_karyawan' => 'required|string',
            'tanggal_masuk' => 'required|date',
            'is_active' => 'required|boolean',
        ];

        // Validasi conditional untuk tanggal_keluar
        if (stripos($request->status_karyawan, 'kontrak') !== false) {
            $rules['tanggal_keluar'] = 'required|date|after:tanggal_masuk';
        } else {
            $rules['tanggal_keluar'] = 'nullable|date|after:tanggal_masuk';
        }

        $validated = $request->validate($rules, [
            'project_id.required' => 'Project wajib dipilih',
            'project_id.exists' => 'Project tidak valid',
            'jabatan_id.required' => 'Jabatan wajib dipilih',
            'jabatan_id.exists' => 'Jabatan tidak valid',
            'status_karyawan.required' => 'Status karyawan wajib dipilih',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi',
            'tanggal_masuk.date' => 'Format tanggal tidak valid',
            'tanggal_keluar.required' => 'Tanggal berakhir kontrak wajib diisi untuk karyawan kontrak',
            'tanggal_keluar.date' => 'Format tanggal tidak valid',
            'tanggal_keluar.after' => 'Tanggal berakhir kontrak harus setelah tanggal masuk',
            'is_active.required' => 'Status aktif wajib dipilih',
        ]);

        try {
            $karyawan->update([
                'project_id' => $validated['project_id'],
                'jabatan_id' => $validated['jabatan_id'],
                'status_karyawan' => $validated['status_karyawan'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'tanggal_keluar' => $validated['tanggal_keluar'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Informasi pekerjaan berhasil diperbarui', 'active_tab' => 'informasi']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal memperbarui informasi pekerjaan: ' . $e->getMessage());
        }
    }

    public function updatePribadi(Request $request, $hashId)
    {
        // Decode hash_id to get real id
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::with('user')->findOrFail($id);

        $validated = $request->validate([
            'nik_ktp' => 'required|string|size:16|unique:karyawans,nik_ktp,' . $karyawan->id,
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . ($karyawan->user_id ?? 'NULL'),
            'alamat' => 'required|string',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
        ], [
            'nik_ktp.required' => 'NIK KTP wajib diisi',
            'nik_ktp.size' => 'NIK KTP harus 16 digit',
            'nik_ktp.unique' => 'NIK KTP sudah terdaftar',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date' => 'Format tanggal tidak valid',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid',
            'telepon.required' => 'Telepon wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'alamat.required' => 'Alamat wajib diisi',
            'kota.required' => 'Kota wajib diisi',
            'provinsi.required' => 'Provinsi wajib diisi',
        ]);

        try {
            DB::beginTransaction();

            // Update karyawan
            $karyawan->update([
                'nik_ktp' => $validated['nik_ktp'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'telepon' => $validated['telepon'],
                'alamat' => $validated['alamat'],
                'kota' => $validated['kota'],
                'provinsi' => $validated['provinsi'],
            ]);

            // Update user email if exists
            if ($karyawan->user) {
                $karyawan->user->update([
                    'email' => $validated['email'],
                ]);
            }

            DB::commit();

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Data pribadi berhasil diperbarui', 'active_tab' => 'informasi']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                            ->with('error', 'Gagal memperbarui data pribadi: ' . $e->getMessage());
        }
    }

    public function updateRekeningBank(Request $request, $hashId)
    {
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($id);

        $validated = $request->validate([
            'nama_bank' => 'required|string|max:255',
            'nomor_rekening' => 'required|string|max:50',
            'nama_pemilik_rekening' => 'required|string|max:255',
            'cabang_bank' => 'nullable|string|max:255',
        ], [
            'nama_bank.required' => 'Nama bank wajib diisi',
            'nomor_rekening.required' => 'Nomor rekening wajib diisi',
            'nama_pemilik_rekening.required' => 'Nama pemilik rekening wajib diisi',
        ]);

        try {
            $karyawan->update($validated);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Data rekening bank berhasil diperbarui', 'active_tab' => 'rekening-bank']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal memperbarui data rekening bank: ' . $e->getMessage());
        }
    }

    public function updateBpjs(Request $request, $hashId)
    {
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::findOrFail($id);

        $validated = $request->validate([
            // JKM
            'bpjs_jkm_nomor' => 'nullable|string|max:255',
            'bpjs_jkm_npp' => 'nullable|string|max:255',
            'bpjs_jkm_tanggal_terdaftar' => 'nullable|date',
            'bpjs_jkm_status' => 'nullable|string|max:255',
            'bpjs_jkm_catatan' => 'nullable|string',
            // JKK
            'bpjs_jkk_nomor' => 'nullable|string|max:255',
            'bpjs_jkk_npp' => 'nullable|string|max:255',
            'bpjs_jkk_tanggal_terdaftar' => 'nullable|date',
            'bpjs_jkk_status' => 'nullable|string|max:255',
            'bpjs_jkk_catatan' => 'nullable|string',
            // JP
            'bpjs_jp_nomor' => 'nullable|string|max:255',
            'bpjs_jp_npp' => 'nullable|string|max:255',
            'bpjs_jp_tanggal_terdaftar' => 'nullable|date',
            'bpjs_jp_status' => 'nullable|string|max:255',
            'bpjs_jp_catatan' => 'nullable|string',
            // JHT
            'bpjs_jht_nomor' => 'nullable|string|max:255',
            'bpjs_jht_npp' => 'nullable|string|max:255',
            'bpjs_jht_tanggal_terdaftar' => 'nullable|date',
            'bpjs_jht_status' => 'nullable|string|max:255',
            'bpjs_jht_catatan' => 'nullable|string',
            // Kesehatan
            'bpjs_kesehatan_nomor' => 'nullable|string|max:255',
            'bpjs_kesehatan_tanggal_terdaftar' => 'nullable|date',
            'bpjs_kesehatan_status' => 'nullable|string|max:255',
            'bpjs_kesehatan_catatan' => 'nullable|string',
        ]);

        try {
            $karyawan->update($validated);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Data BPJS berhasil diperbarui', 'active_tab' => 'bpjs']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal memperbarui data BPJS: ' . $e->getMessage());
        }
    }

    public function updateEmail(Request $request, $hashId)
    {
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::with('user')->findOrFail($id);

        if (!$karyawan->user) {
            return redirect()->back()->with('error', 'Karyawan tidak memiliki akun pengguna');
        }

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,' . $karyawan->user_id,
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
        ]);

        try {
            $karyawan->user->update(['email' => $validated['email']]);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Email berhasil diperbarui', 'active_tab' => 'akun-pengguna']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal memperbarui email: ' . $e->getMessage());
        }
    }

    public function resetPassword(Request $request, $hashId)
    {
        $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
        
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid ID');
        }
        
        $karyawan = Karyawan::with('user')->findOrFail($id);

        if (!$karyawan->user) {
            return redirect()->back()->with('error', 'Karyawan tidak memiliki akun pengguna');
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        try {
            $karyawan->user->update(['password' => Hash::make($validated['password'])]);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Password berhasil direset', 'active_tab' => 'akun-pengguna']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal mereset password: ' . $e->getMessage());
        }
    }
    
    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);
        
        $projectId = $request->project_id;
        $perusahaanId = auth()->user()->perusahaan_id;
        
        $project = Project::find($projectId);
        $fileName = 'Template_Karyawan_' . str_replace(' ', '_', $project->nama) . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\KaryawanTemplateExport($projectId, $perusahaanId),
            $fileName
        );
    }
    
    public function importExcel(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'file.required' => 'File Excel wajib diupload',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
            'file.max' => 'Ukuran file maksimal 2MB',
        ]);
        
        $perusahaanId = auth()->user()->perusahaan_id;
        $projectId = $request->project_id;
        
        try {
            $import = new \App\Imports\KaryawanImport($perusahaanId, $projectId);
            
            \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file'));
            
            $errors = $import->getErrors();
            $successCount = $import->getSuccessCount();
            $skippedCount = $import->getSkippedCount();
            
            // Jika tidak ada yang berhasil dan ada error, tampilkan error
            if ($successCount === 0 && count($errors) > 0) {
                $errorMessage = "Import gagal. Error: " . implode('; ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $errorMessage .= " ... dan " . (count($errors) - 3) . " error lainnya";
                }
                return back()->with('error', $errorMessage);
            }
            
            // Jika ada yang berhasil, tampilkan pesan sukses ringkas
            $message = "Import berhasil: {$successCount} karyawan berhasil ditambahkan";
            
            if ($skippedCount > 0) {
                $message .= ", {$skippedCount} data di-skip";
            }
            
            // Jika ada error tapi juga ada yang berhasil, tampilkan warning ringkas
            if (count($errors) > 0) {
                // Hanya tampilkan jumlah error, tidak detail
                return back()->with('warning', $message . ". {$skippedCount} data gagal diimport (duplikat atau data tidak valid)");
            }
            
            return back()->with('success', $message);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}

