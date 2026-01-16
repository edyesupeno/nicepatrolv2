# Development Standards

Dokumen ini menjelaskan standards yang harus diikuti dalam development Nice Patrol System.

## URL Routing dengan Hash ID

### Mengapa Hash ID?

1. **Security**: Menyembunyikan jumlah data sebenarnya
2. **Privacy**: Tidak expose internal ID
3. **Professional**: URL lebih clean dan aman

### Implementasi

Semua model yang digunakan di URL harus menggunakan trait `HasHashId`:

```php
use App\Traits\HasHashId;

class Perusahaan extends Model
{
    use HasHashId;
    
    protected $appends = ['hash_id'];
}
```

### Penggunaan di Route

```php
// ❌ SALAH
Route::get('/perusahaans/{id}', ...);

// ✅ BENAR
Route::get('/perusahaans/{perusahaan}', ...);
// URL akan menjadi: /perusahaans/abc123def456
```

### Penggunaan di View

```blade
{{-- ❌ SALAH --}}
<a href="{{ route('admin.perusahaans.edit', $perusahaan->id) }}">

{{-- ✅ BENAR --}}
<a href="{{ route('admin.perusahaans.edit', $perusahaan->hash_id) }}">
```

## SweetAlert2 untuk Notifikasi

### Setup

SweetAlert2 sudah di-include di `resources/views/layouts/app.blade.php`:

```html
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

### Success Message

```javascript
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: 'Data berhasil disimpan',
    timer: 3000,
    showConfirmButton: false,
    toast: true,
    position: 'top-end'
});
```

### Error Message

```javascript
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: 'Terjadi kesalahan',
    confirmButtonColor: '#7c3aed'
});
```

### Confirmation Dialog

```javascript
Swal.fire({
    title: 'Yakin ingin menghapus?',
    text: "Data tidak dapat dikembalikan!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal'
}).then((result) => {
    if (result.isConfirmed) {
        // Submit form atau action
        document.getElementById('delete-form').submit();
    }
});
```

### Auto-show dari Session

Layout sudah handle auto-show untuk session flash:

```php
// Controller
return redirect()->route('admin.perusahaans.index')
    ->with('success', 'Perusahaan berhasil ditambahkan');
```

## Validation Error Messages

Semua validation error harus dalam Bahasa Indonesia:

```php
$validated = $request->validate([
    'nama' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
], [
    'nama.required' => 'Nama wajib diisi',
    'email.required' => 'Email wajib diisi',
    'email.email' => 'Format email tidak valid',
    'email.unique' => 'Email sudah terdaftar',
]);
```

## CRUD Pattern

### Create

```php
public function store(Request $request)
{
    $validated = $request->validate([...]);
    
    Model::create($validated);
    
    return redirect()->route('admin.models.index')
        ->with('success', 'Data berhasil ditambahkan');
}
```

### Update

```php
public function update(Request $request, Model $model)
{
    $validated = $request->validate([
        'field' => 'required|unique:table,field,' . $model->id,
    ]);
    
    $model->update($validated);
    
    return redirect()->route('admin.models.index')
        ->with('success', 'Data berhasil diupdate');
}
```

### Delete

```php
public function destroy(Model $model)
{
    $model->delete();
    
    return redirect()->route('admin.models.index')
        ->with('success', 'Data berhasil dihapus');
}
```

## Code Formatting

Gunakan Laravel Pint untuk formatting:

```bash
./vendor/bin/pint
```

## Testing

### Feature Test Example

```php
public function test_can_create_perusahaan()
{
    $response = $this->post('/admin/perusahaans', [
        'nama' => 'PT Test',
        'kode' => 'TEST',
    ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('perusahaans', [
        'nama' => 'PT Test',
    ]);
}
```

## Git Commit Messages

Format:

```
type(scope): subject

body (optional)
```

Contoh:

```
feat(perusahaan): add hash id for URL routing

- Install hashids package
- Add HasHashId trait
- Update routes to use hash_id
- Update views to use hash_id
```

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: Formatting
- `refactor`: Code restructuring
- `test`: Adding tests
- `chore`: Maintenance
