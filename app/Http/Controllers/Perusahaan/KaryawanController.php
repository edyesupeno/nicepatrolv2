<?php

namespace App\Http\Controllers\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Project;
use App\Models\Jabatan;
use App\Models\StatusKaryawan;
use App\Models\Area;
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
        
        $karyawan = Karyawan::with(['project', 'jabatan.projects', 'user', 'perusahaan', 'pengalamanKerjas', 'pendidikans', 'sertifikasis', 'medicalCheckups', 'areas.project'])
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
            'role' => 'required|in:security_officer,office_employee,manager_project,admin_project,admin_branch,finance_branch,admin_hsse',
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
                'role' => $validated['role'], // Use role from form
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
                'status_perkawinan' => $validated['status_perkawinan'],
                'jumlah_tanggungan' => $validated['jumlah_tanggungan'],
                'telepon' => $validated['telepon'],
                'alamat' => $validated['alamat'],
                'kota' => $validated['kota'],
                'provinsi' => $validated['provinsi'],
            ]);

            // CRITICAL: Auto-assign semua area di project ke karyawan baru
            $projectAreas = Area::where('project_id', $validated['project_id'])->get();
            
            if ($projectAreas->count() > 0) {
                $areaData = [];
                foreach ($projectAreas as $index => $area) {
                    $areaData[$area->id] = [
                        'is_primary' => $index === 0, // Area pertama jadi primary
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                $karyawan->areas()->attach($areaData);
            }

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
            DB::beginTransaction();
            
            // Cek apakah project_id berubah
            $projectChanged = $karyawan->project_id != $validated['project_id'];
            
            // Update data karyawan
            $karyawan->update([
                'project_id' => $validated['project_id'],
                'jabatan_id' => $validated['jabatan_id'],
                'status_karyawan' => $validated['status_karyawan'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'tanggal_keluar' => $validated['tanggal_keluar'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            // CRITICAL: Jika project berubah, update karyawan areas
            if ($projectChanged) {
                // Hapus semua area assignments lama
                $karyawan->areas()->detach();
                
                // Ambil semua area di project baru
                $newAreas = Area::where('project_id', $validated['project_id'])->get();
                
                if ($newAreas->count() > 0) {
                    // Assign semua area di project baru
                    $areaData = [];
                    foreach ($newAreas as $index => $area) {
                        $areaData[$area->id] = [
                            'is_primary' => $index === 0, // Area pertama jadi primary
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    
                    $karyawan->areas()->attach($areaData);
                    
                    $message = 'Informasi pekerjaan berhasil diperbarui. Area kerja otomatis disesuaikan dengan project baru (' . $newAreas->count() . ' area).';
                } else {
                    $message = 'Informasi pekerjaan berhasil diperbarui. Project baru belum memiliki area kerja.';
                }
            } else {
                $message = 'Informasi pekerjaan berhasil diperbarui.';
            }

            DB::commit();

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => $message, 'active_tab' => 'informasi']);
        } catch (\Exception $e) {
            DB::rollBack();
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

    public function updateRole(Request $request, $hashId)
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
            'role' => 'required|in:security_officer,office_employee,manager_project,admin_project,admin_branch,finance_branch,admin_hsse',
        ], [
            'role.required' => 'Role wajib dipilih',
            'role.in' => 'Role tidak valid',
        ]);

        try {
            $karyawan->user->update(['role' => $validated['role']]);

            return redirect()->route('perusahaan.karyawans.show', $karyawan->hash_id)
                            ->with(['success' => 'Role berhasil diperbarui', 'active_tab' => 'akun-pengguna']);
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Gagal memperbarui role: ' . $e->getMessage());
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
            'role' => 'required|in:security_officer,office_employee,manager_project,admin_project,admin_branch,finance_branch,admin_hsse',
            'file' => 'required|mimes:xlsx,xls|max:10240', // Increased to 10MB for larger files
        ], [
            'project_id.required' => 'Project wajib dipilih',
            'role.required' => 'Role wajib dipilih',
            'role.in' => 'Role tidak valid',
            'file.required' => 'File Excel wajib diupload',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);
        
        $perusahaanId = auth()->user()->perusahaan_id;
        $projectId = $request->project_id;
        $role = $request->role;
        $userId = auth()->id();
        
        try {
            // Store uploaded file temporarily
            $file = $request->file('file');
            $fileName = 'import_karyawan_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = storage_path('app/temp/' . $fileName);
            
            // Create temp directory if not exists
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            $file->move(storage_path('app/temp'), $fileName);
            
            // Generate job ID
            $jobId = uniqid('import_karyawan_');
            
            // Dispatch background job
            $job = new \App\Jobs\ImportKaryawanJob($filePath, $perusahaanId, $projectId, $role, $userId, $jobId);
            dispatch($job);
            
            return response()->json([
                'success' => true,
                'message' => 'Import dimulai di background. Silakan pantau progress.',
                'job_id' => $jobId,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai import: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function importProgress(Request $request)
    {
        $userId = auth()->id();
        $jobId = $request->get('job_id');
        
        if (!$jobId) {
            return response()->json([
                'success' => false,
                'message' => 'Job ID tidak ditemukan',
            ], 400);
        }
        
        $progress = \Illuminate\Support\Facades\Cache::get("import_progress_{$userId}_{$jobId}");
        
        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => 'Progress tidak ditemukan',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }

    public function exportPage(Request $request)
    {
        $projects = Project::where('perusahaan_id', auth()->user()->perusahaan_id)->get();
        $jabatans = Jabatan::where('perusahaan_id', auth()->user()->perusahaan_id)->get();
        $statusKaryawans = StatusKaryawan::all();

        return view('perusahaan.karyawans.export', compact('projects', 'jabatans', 'statusKaryawans'));
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'format' => 'required|in:excel,pdf',
        ]);

        $projectId = $request->project_id;
        $perusahaanId = auth()->user()->perusahaan_id;
        
        // Build filters
        $filters = [];
        if ($request->filled('search')) {
            $filters['search'] = $request->search;
        }
        if ($request->filled('status_karyawan')) {
            $filters['status_karyawan'] = $request->status_karyawan;
        }
        if ($request->filled('jabatan_id')) {
            $filters['jabatan_id'] = $request->jabatan_id;
        }
        if ($request->filled('is_active')) {
            $filters['is_active'] = $request->is_active;
        }

        // Get project name for filename
        $projectName = 'Semua_Project';
        if ($projectId) {
            $project = Project::find($projectId);
            $projectName = $project ? str_replace(' ', '_', $project->nama) : 'Project';
        }

        $fileName = 'Data_Karyawan_' . $projectName . '_' . date('Y-m-d_H-i-s');

        if ($request->format === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\KaryawanExport($projectId, $perusahaanId, $filters),
                $fileName . '.xlsx'
            );
        } else {
            // For PDF export
            return $this->exportPdf($request, $projectId, $perusahaanId, $filters, $fileName);
        }
    }

    private function exportPdf($request, $projectId, $perusahaanId, $filters, $fileName)
    {
        $query = Karyawan::with(['project', 'jabatan', 'user'])
            ->where('perusahaan_id', $perusahaanId);

        // Filter by project if specified
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        // Apply additional filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nik_karyawan', 'like', "%{$search}%")
                  ->orWhere('nik_ktp', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['status_karyawan'])) {
            $query->where('status_karyawan', $filters['status_karyawan']);
        }

        if (!empty($filters['jabatan_id'])) {
            $query->where('jabatan_id', $filters['jabatan_id']);
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        $karyawans = $query->orderBy('nama_lengkap')->get();

        // Get project info
        $project = null;
        if ($projectId) {
            $project = Project::find($projectId);
        }

        $perusahaan = auth()->user()->perusahaan;

        $data = [
            'karyawans' => $karyawans,
            'project' => $project,
            'perusahaan' => $perusahaan,
            'filters' => $filters,
            'total' => $karyawans->count(),
            'generated_at' => now()->format('d/m/Y H:i:s'),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('perusahaan.karyawans.export-pdf', $data);
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download(str_replace(['/', '\\'], '_', $fileName) . '.pdf');
    }

    // Area Management Methods
    
    public function getAvailableAreas($hashId)
    {
        try {
            // Decode hash_id to get real id
            $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
            
            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid ID'
                ], 400);
            }
            
            $karyawan = Karyawan::findOrFail($id);
            
            // Get areas dari project karyawan yang belum di-assign ke karyawan ini
            $assignedAreaIds = $karyawan->areas()->pluck('areas.id')->toArray();
            
            $availableAreas = Area::select(['id', 'nama', 'alamat', 'project_id'])
                ->with('project:id,nama')
                ->where('project_id', $karyawan->project_id) // Hanya area dari project karyawan
                ->whereNotIn('id', $assignedAreaIds)
                ->get()
                ->map(function ($area) {
                    return [
                        'hash_id' => $area->hash_id,
                        'nama' => $area->nama,
                        'alamat' => $area->alamat,
                        'project_nama' => $area->project->nama ?? 'N/A'
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $availableAreas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar area: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function addArea(Request $request, $hashId)
    {
        try {
            // Decode hash_id to get real id
            $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
            
            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid ID'
                ], 400);
            }
            
            $validated = $request->validate([
                'area_hash_id' => 'required|string',
                'is_primary' => 'boolean'
            ]);
            
            $karyawan = Karyawan::findOrFail($id);
            
            // Decode area hash_id
            $areaId = \Vinkla\Hashids\Facades\Hashids::decode($validated['area_hash_id'])[0] ?? null;
            
            if (!$areaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Area tidak valid'
                ], 400);
            }
            
            $area = Area::findOrFail($areaId);
            
            // Cek apakah area sudah di-assign
            if ($karyawan->areas()->where('area_id', $areaId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Area sudah ditugaskan ke karyawan ini'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Jika ini akan jadi primary area, set semua area lain jadi non-primary
            if ($validated['is_primary'] ?? false) {
                $karyawan->areas()->updateExistingPivot(
                    $karyawan->areas()->pluck('areas.id')->toArray(),
                    ['is_primary' => false]
                );
            }
            
            // Attach area baru
            $karyawan->areas()->attach($areaId, [
                'is_primary' => $validated['is_primary'] ?? false
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Area kerja berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan area: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function addMultipleAreas(Request $request, $hashId)
    {
        try {
            // Decode hash_id to get real id
            $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
            
            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid ID'
                ], 400);
            }
            
            $validated = $request->validate([
                'area_hash_ids' => 'required|array|min:1',
                'area_hash_ids.*' => 'required|string',
                'set_first_as_primary' => 'boolean'
            ]);
            
            $karyawan = Karyawan::findOrFail($id);
            
            // Decode area hash_ids
            $areaIds = [];
            foreach ($validated['area_hash_ids'] as $areaHashId) {
                $areaId = \Vinkla\Hashids\Facades\Hashids::decode($areaHashId)[0] ?? null;
                if ($areaId) {
                    $areaIds[] = $areaId;
                }
            }
            
            if (empty($areaIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada area yang valid'
                ], 400);
            }
            
            // Verify all areas exist and belong to the same project as karyawan
            $areas = Area::whereIn('id', $areaIds)
                         ->where('project_id', $karyawan->project_id)
                         ->get();
            
            if ($areas->count() !== count($areaIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa area tidak valid atau tidak sesuai dengan project karyawan'
                ], 400);
            }
            
            // Check for existing assignments
            $existingAreaIds = $karyawan->areas()->whereIn('area_id', $areaIds)->pluck('area_id')->toArray();
            if (!empty($existingAreaIds)) {
                $existingAreas = Area::whereIn('id', $existingAreaIds)->pluck('nama')->toArray();
                return response()->json([
                    'success' => false,
                    'message' => 'Area berikut sudah ditugaskan: ' . implode(', ', $existingAreas)
                ], 400);
            }
            
            DB::beginTransaction();
            
            // If setting first as primary, unset all current primary areas
            if ($validated['set_first_as_primary'] ?? false) {
                $karyawan->areas()->updateExistingPivot(
                    $karyawan->areas()->pluck('areas.id')->toArray(),
                    ['is_primary' => false]
                );
            }
            
            // Attach all areas
            $attachData = [];
            foreach ($areaIds as $index => $areaId) {
                $attachData[$areaId] = [
                    'is_primary' => ($validated['set_first_as_primary'] ?? false) && $index === 0,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            $karyawan->areas()->attach($attachData);
            
            DB::commit();
            
            $count = count($areaIds);
            return response()->json([
                'success' => true,
                'message' => "Berhasil menambahkan {$count} area kerja"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan area: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function setPrimaryArea($hashId, $areaHashId)
    {
        try {
            // Decode hash_id to get real id
            $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
            $areaId = \Vinkla\Hashids\Facades\Hashids::decode($areaHashId)[0] ?? null;
            
            if (!$id || !$areaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid ID'
                ], 400);
            }
            
            $karyawan = Karyawan::findOrFail($id);
            
            // Cek apakah area sudah di-assign ke karyawan
            if (!$karyawan->areas()->where('area_id', $areaId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Area tidak ditemukan pada karyawan ini'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Set semua area jadi non-primary
            $karyawan->areas()->updateExistingPivot(
                $karyawan->areas()->pluck('areas.id')->toArray(),
                ['is_primary' => false]
            );
            
            // Set area yang dipilih jadi primary
            $karyawan->areas()->updateExistingPivot($areaId, ['is_primary' => true]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Area utama berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui area utama: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function removeArea($hashId, $areaHashId)
    {
        try {
            // Decode hash_id to get real id
            $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
            $areaId = \Vinkla\Hashids\Facades\Hashids::decode($areaHashId)[0] ?? null;
            
            if (!$id || !$areaId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid ID'
                ], 400);
            }
            
            $karyawan = Karyawan::findOrFail($id);
            
            // Cek apakah area sudah di-assign ke karyawan
            $pivotData = $karyawan->areas()->where('area_id', $areaId)->first();
            if (!$pivotData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Area tidak ditemukan pada karyawan ini'
                ], 400);
            }
            
            DB::beginTransaction();
            
            $wasPrimary = $pivotData->pivot->is_primary;
            
            // Detach area
            $karyawan->areas()->detach($areaId);
            
            // Jika area yang dihapus adalah primary, set area pertama yang tersisa jadi primary
            if ($wasPrimary) {
                $firstArea = $karyawan->areas()->first();
                if ($firstArea) {
                    $karyawan->areas()->updateExistingPivot($firstArea->id, ['is_primary' => true]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Area kerja berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus area: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function removeAllAreas($hashId)
    {
        try {
            // Decode hash_id to get real id
            $id = \Vinkla\Hashids\Facades\Hashids::decode($hashId)[0] ?? null;
            
            if (!$id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid ID'
                ], 400);
            }
            
            $karyawan = Karyawan::findOrFail($id);
            
            $areaCount = $karyawan->areas()->count();
            
            if ($areaCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada area yang ditugaskan ke karyawan ini'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Detach all areas
            $karyawan->areas()->detach();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$areaCount} area kerja"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus area: ' . $e->getMessage()
            ], 500);
        }
    }
}

