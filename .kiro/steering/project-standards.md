---
inclusion: always
---

# Project Standards - Nice Patrol System

## 1. URL Routing Standards

### WAJIB: Gunakan Hash ID, BUKAN Integer ID
- ❌ **SALAH**: `/admin/perusahaans/1/edit`
- ✅ **BENAR**: `/admin/perusahaans/abc123def456/edit`

**Implementasi:**
1. Install package hashids: `composer require vinkla/hashids`
2. Tambahkan trait `HasHashId` di semua model
3. Gunakan route model binding dengan hash_id
4. Semua URL harus menggunakan hash_id, bukan id

**Contoh Model:**
```php
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $appends = ['hash_id'];
    
    public function getHashIdAttribute()
    {
        return \Vinkla\Hashids\Facades\Hashids::encode($this->id);
    }
    
    public function getRouteKeyName()
    {
        return 'hash_id';
    }
    
    public function resolveRouteBinding($value, $field = null)
    {
        $id = \Vinkla\Hashids\Facades\Hashids::decode($value)[0] ?? null;
        return $this->where('id', $id)->firstOrFail();
    }
}
```

## 2. Multi-Tenancy Standards (CRITICAL!)

### WAJIB: Isolasi Data Per Perusahaan

**Aturan Ketat:**
1. ❌ **DILARANG KERAS**: Perusahaan A melihat data Perusahaan B
2. ✅ **WAJIB**: Semua query harus filter berdasarkan `perusahaan_id`
3. ✅ **WAJIB**: Global scope untuk auto-filter di model
4. ✅ **WAJIB**: Validasi perusahaan_id di controller
5. ✅ **EXCEPTION**: Hanya Superadmin yang bisa akses semua data

### Implementasi Global Scope

**Semua model yang punya `perusahaan_id` WAJIB punya global scope:**

```php
use Illuminate\Database\Eloquent\Builder;

class Lokasi extends Model
{
    protected static function booted(): void
    {
        static::addGlobalScope('perusahaan', function (Builder $builder) {
            if (auth()->check() && auth()->user()->perusahaan_id) {
                $builder->where('perusahaan_id', auth()->user()->perusahaan_id);
            }
        });
    }
}
```

### Auto-assign perusahaan_id saat Create

```php
public function store(Request $request)
{
    $validated = $request->validate([...]);
    
    // WAJIB: Auto-assign perusahaan_id
    if (!auth()->user()->isSuperAdmin()) {
        $validated['perusahaan_id'] = auth()->user()->perusahaan_id;
    }
    
    Model::create($validated);
}
```

### Validasi Ownership

```php
public function update(Request $request, Model $model)
{
    // WAJIB: Cek ownership (sudah otomatis dengan global scope)
    // Jika user bukan superadmin dan model bukan miliknya, 
    // global scope akan return 404
    
    $model->update($validated);
}
```

### Testing Multi-Tenancy

**WAJIB test untuk memastikan isolasi data:**

```php
public function test_perusahaan_cannot_see_other_perusahaan_data()
{
    $perusahaanA = Perusahaan::factory()->create();
    $perusahaanB = Perusahaan::factory()->create();
    
    $userA = User::factory()->create(['perusahaan_id' => $perusahaanA->id]);
    $lokasiB = Lokasi::factory()->create(['perusahaan_id' => $perusahaanB->id]);
    
    $this->actingAs($userA);
    
    // User A tidak boleh bisa akses lokasi B
    $response = $this->get(route('admin.lokasis.show', $lokasiB->hash_id));
    $response->assertStatus(404);
}
```

### Checklist Multi-Tenancy

Sebelum deploy, pastikan:

- [ ] Semua model dengan `perusahaan_id` punya global scope
- [ ] Semua create auto-assign `perusahaan_id`
- [ ] Tidak ada query tanpa filter `perusahaan_id` (kecuali superadmin)
- [ ] Test isolasi data sudah dibuat dan pass
- [ ] API endpoint juga ter-filter dengan benar
- [ ] Relasi antar model sudah benar (belongsTo perusahaan)

## 3. Alert & Notification Standards

### WAJIB: Gunakan SweetAlert2, BUKAN Browser Alert/Confirm
- ❌ **SALAH**: `alert()`, `confirm()`, `prompt()`
- ✅ **BENAR**: SweetAlert2 untuk semua notifikasi

**Implementasi:**
1. Include SweetAlert2 di layout: `<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>`
2. Gunakan untuk:
   - Success message
   - Error message
   - Confirmation dialog (delete, update, etc)
   - Loading state

**Contoh Success:**
```javascript
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Data berhasil disimpan',
    timer: 2000,
    showConfirmButton: false
});
```

**Contoh Confirmation:**
```javascript
Swal.fire({
    title: 'Yakin ingin menghapus?',
    text: "Data tidak dapat dikembalikan!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal'
}).then((result) => {
    if (result.isConfirmed) {
        // Submit form
    }
});
```

## 4. Documentation Standards

### ❌ DILARANG: Membuat Dokumentasi Tanpa Diminta
- **JANGAN** membuat file dokumentasi (.md) kecuali user secara eksplisit meminta
- **JANGAN** membuat file README, CHANGELOG, atau dokumentasi apapun secara otomatis
- **FOKUS** pada implementasi code, bukan dokumentasi

### Jika Diminta Dokumentasi:
- Semua file MD di folder `docs/`
- Struktur: `docs/api/`, `docs/deployment/`, `docs/development/`

## 5. CRUD Standards

### Create (Store)
- Validasi input
- Hash password jika ada
- **WAJIB**: Auto-assign `perusahaan_id` (kecuali superadmin)
- Return dengan SweetAlert success
- Redirect ke index

### Read (Index/Show)
- Gunakan hash_id di URL
- **WAJIB**: Filter berdasarkan `perusahaan_id` (via global scope)
- Pagination untuk list
- Eager loading untuk relasi

### Update
- Gunakan hash_id di URL
- **WAJIB**: Validasi ownership (otomatis via global scope)
- Validasi dengan unique:table,column,{id}
- SweetAlert confirmation sebelum update
- SweetAlert success setelah update

### Delete
- Gunakan hash_id di URL
- **WAJIB**: Validasi ownership (otomatis via global scope)
- **WAJIB** SweetAlert confirmation
- Soft delete jika memungkinkan
- SweetAlert success setelah delete

## 6. Form Validation

### Client-side
- HTML5 validation (required, email, min, max)
- Real-time validation dengan JavaScript

### Server-side
- Laravel validation rules
- Custom error messages dalam Bahasa Indonesia
- Return validation errors dengan format yang jelas

## 7. Security Standards

- CSRF protection untuk semua form
- XSS protection (escape output)
- SQL injection protection (use Eloquent/Query Builder)
- Rate limiting untuk API
- Authentication middleware untuk protected routes
- **CRITICAL**: Multi-tenancy isolation (perusahaan_id filter)
- Hash ID untuk URL obfuscation

## 8. Code Style

### PHP
- PSR-12 coding standard
- Use type hints
- Use strict types
- Meaningful variable names

### JavaScript
- ES6+ syntax
- Async/await untuk asynchronous operations
- Meaningful function names
- Comment untuk logic yang kompleks

### Blade
- Indent dengan 4 spaces
- Use @auth, @guest directives
- Extract reusable components
- Use @section, @yield properly

## 9. Database Standards

- Migration untuk semua perubahan schema
- Seeder untuk data awal
- Foreign key constraints
- Index untuk kolom yang sering di-query
- Soft deletes untuk data penting
- **WAJIB**: Kolom `perusahaan_id` di semua tabel yang perlu isolasi

## 10. Git Standards

### Commit Message Format
```
type(scope): subject

body (optional)
```

**Types:**
- feat: New feature
- fix: Bug fix
- docs: Documentation
- style: Formatting
- refactor: Code restructuring
- test: Adding tests
- chore: Maintenance

**Example:**
```
feat(perusahaan): add hash id for URL routing

- Install hashids package
- Add HasHashId trait
- Update routes to use hash_id
- Update views to use hash_id
```

## 11. Testing Standards

- Unit tests untuk business logic
- Feature tests untuk endpoints
- Browser tests untuk critical flows
- **WAJIB**: Test multi-tenancy isolation
- Minimum 70% code coverage

## Checklist Sebelum Commit

- [ ] Semua URL menggunakan hash_id
- [ ] Tidak ada alert/confirm browser
- [ ] Semua notifikasi menggunakan SweetAlert2
- [ ] File MD ada di folder docs/
- [ ] Code sudah di-format (Pint)
- [ ] Tidak ada console.log() yang tertinggal
- [ ] Validation error dalam Bahasa Indonesia
- [ ] CSRF token ada di semua form
- [ ] Migration sudah di-test
- [ ] Dokumentasi sudah di-update
- [ ] **CRITICAL**: Global scope perusahaan_id sudah ditambahkan
- [ ] **CRITICAL**: Auto-assign perusahaan_id di create
- [ ] **CRITICAL**: Test multi-tenancy isolation sudah dibuat

## Security Checklist Multi-Tenancy

### CRITICAL - Wajib Dicek Sebelum Deploy!

1. **Model dengan perusahaan_id:**
   - [ ] Lokasi - Global scope ✓
   - [ ] Checkpoint - Global scope ✓
   - [ ] Patroli - Global scope ✓
   - [ ] PatroliDetail - Cek via relasi
   - [ ] User - Filter manual di controller

2. **Controller Create:**
   - [ ] Auto-assign perusahaan_id
   - [ ] Validasi tidak bisa set perusahaan_id manual (kecuali superadmin)

3. **Controller Update/Delete:**
   - [ ] Global scope otomatis validasi ownership
   - [ ] Return 404 jika bukan milik perusahaan

4. **API Endpoints:**
   - [ ] Semua endpoint ter-filter perusahaan_id
   - [ ] Token authentication check perusahaan_id

5. **Testing:**
   - [ ] Test user A tidak bisa akses data user B
   - [ ] Test superadmin bisa akses semua data
   - [ ] Test API isolation

## Contoh Kasus yang DILARANG

### ❌ SALAH - Query tanpa filter
```php
// BAHAYA! Bisa lihat semua data
$lokasis = Lokasi::all();
```

### ✅ BENAR - Dengan global scope
```php
// Aman! Otomatis ter-filter
$lokasis = Lokasi::all(); // Global scope auto-filter
```

### ❌ SALAH - Manual query tanpa filter
```php
// BAHAYA! Bypass global scope
$lokasi = Lokasi::withoutGlobalScope('perusahaan')->find($id);
```

### ✅ BENAR - Respect global scope
```php
// Aman! Pakai global scope
$lokasi = Lokasi::find($id); // Auto-filter perusahaan_id
```
