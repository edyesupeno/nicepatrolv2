# Database Optimization Standards

## 1. Query Optimization

### WAJIB: Hindari SELECT *
- ❌ **SALAH**: `Model::all()` atau `Model::get()` tanpa select
- ✅ **BENAR**: Selalu specify kolom yang dibutuhkan

**Contoh Salah:**
```php
$users = User::all(); // Mengambil semua kolom
$users = User::with('profile')->get(); // Mengambil semua kolom dari users dan profiles
```

**Contoh Benar:**
```php
$users = User::select('id', 'name', 'email')->get();
$users = User::select('id', 'name', 'email')
    ->with('profile:id,user_id,avatar,bio')
    ->get();
```

### WAJIB: Gunakan Eager Loading
- ❌ **SALAH**: N+1 Query Problem
- ✅ **BENAR**: Eager loading dengan select spesifik

**Contoh Salah:**
```php
$karyawans = Karyawan::all();
foreach ($karyawans as $karyawan) {
    echo $karyawan->project->nama; // N+1 query!
}
```

**Contoh Benar:**
```php
$karyawans = Karyawan::select('id', 'nama', 'project_id')
    ->with('project:id,nama')
    ->get();
```

### WAJIB: Index untuk Kolom yang Sering Di-query
- Tambahkan index untuk kolom yang digunakan di WHERE, JOIN, ORDER BY
- Foreign keys harus selalu punya index

**Contoh Migration:**
```php
Schema::create('karyawans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('perusahaan_id')->constrained()->onDelete('cascade');
    $table->foreignId('project_id')->constrained()->onDelete('cascade');
    $table->string('nik_karyawan')->index(); // Index untuk pencarian
    $table->string('nama_lengkap')->index(); // Index untuk pencarian
    $table->string('email')->unique(); // Unique sudah otomatis index
    $table->boolean('is_active')->index(); // Index untuk filter
    $table->timestamps();
    
    // Composite index untuk query yang sering digunakan
    $table->index(['perusahaan_id', 'is_active']);
});
```

## 2. Pagination

### WAJIB: Gunakan Pagination untuk List Besar
- ❌ **SALAH**: `->get()` untuk data yang banyak
- ✅ **BENAR**: `->paginate()` atau `->simplePaginate()`

**Contoh:**
```php
// Untuk list dengan banyak data
$karyawans = Karyawan::select('id', 'nama', 'email')
    ->paginate(50);

// Untuk infinite scroll
$karyawans = Karyawan::select('id', 'nama', 'email')
    ->simplePaginate(50);
```

## 3. Caching

### Gunakan Cache untuk Data yang Jarang Berubah
```php
// Cache untuk 1 jam
$projects = Cache::remember('projects_' . auth()->user()->perusahaan_id, 3600, function () {
    return Project::select('id', 'nama')
        ->where('perusahaan_id', auth()->user()->perusahaan_id)
        ->orderBy('nama')
        ->get();
});
```

## 4. Chunk untuk Data Besar

### Gunakan Chunk untuk Processing Data Besar
```php
// Proses data dalam batch untuk menghindari memory overflow
Karyawan::select('id', 'nama', 'gaji_pokok')
    ->where('is_active', true)
    ->chunk(100, function ($karyawans) {
        foreach ($karyawans as $karyawan) {
            // Process each karyawan
        }
    });
```

## 5. Query Optimization Checklist

Sebelum deploy, pastikan:

- [ ] Semua query menggunakan select() dengan kolom spesifik
- [ ] Eager loading digunakan untuk relasi
- [ ] Index ditambahkan untuk kolom yang sering di-query
- [ ] Pagination digunakan untuk list data
- [ ] Tidak ada N+1 query problem
- [ ] Cache digunakan untuk data yang jarang berubah
- [ ] Chunk digunakan untuk processing data besar

## 6. Monitoring Query Performance

### Gunakan Laravel Debugbar atau Telescope
```bash
composer require barryvdh/laravel-debugbar --dev
```

### Log Slow Queries
```php
// config/database.php
'connections' => [
    'mysql' => [
        // ...
        'options' => [
            PDO::ATTR_EMULATE_PREPARES => true,
        ],
        'slow_query_log' => true,
        'slow_query_time' => 1, // Log queries > 1 second
    ],
],
```

## 7. Contoh Query Optimization

### ❌ SALAH - Query Tidak Optimal
```php
// Mengambil semua kolom
$karyawans = Karyawan::with('project', 'jabatan')->get();

// N+1 Query
$karyawans = Karyawan::all();
foreach ($karyawans as $karyawan) {
    echo $karyawan->project->nama;
    echo $karyawan->jabatan->nama;
}

// Tidak ada pagination
$karyawans = Karyawan::where('is_active', true)->get();
```

### ✅ BENAR - Query Optimal
```php
// Select kolom spesifik dengan eager loading
$karyawans = Karyawan::select([
        'id',
        'project_id',
        'jabatan_id',
        'nik_karyawan',
        'nama_lengkap',
        'email',
        'gaji_pokok'
    ])
    ->with([
        'project:id,nama',
        'jabatan:id,nama'
    ])
    ->where('is_active', true)
    ->paginate(50);

// Dengan caching untuk dropdown
$projects = Cache::remember('projects_list', 3600, function () {
    return Project::select('id', 'nama')->orderBy('nama')->get();
});
```

## 8. Database Design Best Practices

### Normalisasi yang Tepat
- Hindari data redundan
- Gunakan foreign keys dengan constraints
- Pisahkan data yang jarang diakses ke tabel terpisah

### Tipe Data yang Tepat
- Gunakan tipe data yang sesuai (INT untuk angka, VARCHAR untuk string pendek, TEXT untuk string panjang)
- Gunakan ENUM untuk pilihan yang terbatas
- Gunakan DECIMAL untuk uang, bukan FLOAT

### Soft Deletes
- Gunakan soft deletes untuk data penting
- Tambahkan index pada deleted_at

```php
Schema::create('karyawans', function (Blueprint $table) {
    $table->id();
    // ... columns
    $table->softDeletes();
    $table->timestamps();
    
    // Index untuk soft deletes query
    $table->index('deleted_at');
});
```

## 9. Race Condition & Concurrency Control

### WAJIB: Hindari Race Condition untuk Data Kritis

Race condition terjadi ketika multiple users/processes mengakses dan memodifikasi data yang sama secara bersamaan, menyebabkan inkonsistensi data.

### Teknik 1: Database Transactions
Gunakan transaction untuk operasi yang melibatkan multiple queries.

**Contoh Salah:**
```php
// Tanpa transaction - BERBAHAYA!
$user = User::find($id);
$user->balance -= 100;
$user->save();

$transaction = Transaction::create([
    'user_id' => $id,
    'amount' => -100
]);
```

**Contoh Benar:**
```php
use Illuminate\Support\Facades\DB;

DB::transaction(function () use ($id) {
    $user = User::find($id);
    $user->balance -= 100;
    $user->save();
    
    Transaction::create([
        'user_id' => $id,
        'amount' => -100
    ]);
});
```

### Teknik 2: Pessimistic Locking (Row Locking)
Gunakan `lockForUpdate()` untuk lock row saat update.

**Contoh:**
```php
DB::transaction(function () use ($id, $amount) {
    // Lock row untuk mencegah concurrent update
    $user = User::where('id', $id)->lockForUpdate()->first();
    
    if ($user->balance >= $amount) {
        $user->balance -= $amount;
        $user->save();
        
        Transaction::create([
            'user_id' => $id,
            'amount' => -$amount
        ]);
    }
});
```

### Teknik 3: Optimistic Locking (Version Control)
Gunakan version column untuk detect concurrent updates.

**Migration:**
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->integer('stock');
    $table->integer('version')->default(0); // Version column
    $table->timestamps();
});
```

**Model:**
```php
class Product extends Model
{
    protected $fillable = ['name', 'stock', 'version'];
    
    public function decrementStock($quantity)
    {
        $currentVersion = $this->version;
        
        $affected = DB::table('products')
            ->where('id', $this->id)
            ->where('version', $currentVersion)
            ->where('stock', '>=', $quantity)
            ->update([
                'stock' => DB::raw('stock - ' . $quantity),
                'version' => DB::raw('version + 1'),
                'updated_at' => now()
            ]);
        
        if ($affected === 0) {
            throw new \Exception('Stock update failed due to concurrent modification');
        }
        
        $this->refresh();
    }
}
```

### Teknik 4: Atomic Operations
Gunakan DB::raw() untuk operasi atomic.

**Contoh Salah:**
```php
// Race condition - BERBAHAYA!
$product = Product::find($id);
$product->stock = $product->stock - 1;
$product->save();
```

**Contoh Benar:**
```php
// Atomic operation - AMAN
Product::where('id', $id)
    ->where('stock', '>', 0)
    ->update(['stock' => DB::raw('stock - 1')]);

// Atau dengan increment/decrement
Product::find($id)->decrement('stock');
```

### Teknik 5: Database Constraints
Gunakan database constraints untuk enforce data integrity.

**Migration:**
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->integer('stock')->unsigned(); // Tidak boleh negatif
    $table->timestamps();
    
    // Check constraint untuk stock
    DB::statement('ALTER TABLE products ADD CONSTRAINT stock_non_negative CHECK (stock >= 0)');
});
```

### Teknik 6: Queue untuk Operasi Sequential
Gunakan queue untuk operasi yang harus sequential.

**Contoh:**
```php
// Dispatch job ke queue
ProcessPayment::dispatch($orderId)->onQueue('payments');

// Job akan diproses sequential
class ProcessPayment implements ShouldQueue
{
    public function handle()
    {
        DB::transaction(function () {
            $order = Order::lockForUpdate()->find($this->orderId);
            
            if ($order->status === 'pending') {
                // Process payment
                $order->status = 'paid';
                $order->save();
            }
        });
    }
}
```

### Contoh Kasus: Update Gaji Massal dengan Race Condition Protection

**Implementasi Aman:**
```php
public function updateMassal(Request $request)
{
    try {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'jabatan_id' => 'nullable|exists:jabatans,id',
            'gaji_pokok' => 'required|numeric|min:0',
        ]);
        
        DB::transaction(function () use ($validated) {
            $query = Karyawan::where('is_active', true);
            
            if (!empty($validated['project_id'])) {
                $query->where('project_id', $validated['project_id']);
            }
            
            if (!empty($validated['jabatan_id'])) {
                $query->where('jabatan_id', $validated['jabatan_id']);
            }
            
            // Lock rows sebelum update
            $karyawans = $query->lockForUpdate()->get();
            $count = $karyawans->count();
            
            // Update dengan atomic operation
            $query->update([
                'gaji_pokok' => $validated['gaji_pokok'],
                'updated_at' => now()
            ]);
            
            // Log perubahan
            foreach ($karyawans as $karyawan) {
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'update_gaji_massal',
                    'model' => 'Karyawan',
                    'model_id' => $karyawan->id,
                    'old_value' => $karyawan->gaji_pokok,
                    'new_value' => $validated['gaji_pokok']
                ]);
            }
        });
        
        return response()->json([
            'success' => true,
            'message' => "Berhasil update gaji pokok untuk {$count} karyawan",
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Gagal update: ' . $e->getMessage(),
        ], 500);
    }
}
```

### Race Condition Checklist

Sebelum deploy, pastikan:

- [ ] Gunakan DB transaction untuk operasi multi-step
- [ ] Gunakan lockForUpdate() untuk data yang sering di-update concurrent
- [ ] Gunakan atomic operations (increment/decrement/DB::raw)
- [ ] Tambahkan version column untuk optimistic locking jika perlu
- [ ] Gunakan database constraints untuk data integrity
- [ ] Gunakan queue untuk operasi yang harus sequential
- [ ] Test concurrent access dengan multiple users
- [ ] Log semua perubahan data kritis untuk audit trail

### Contoh Testing Race Condition

```php
// Test concurrent update
public function test_concurrent_stock_update()
{
    $product = Product::create(['name' => 'Test', 'stock' => 10]);
    
    // Simulate 10 concurrent requests
    $promises = [];
    for ($i = 0; $i < 10; $i++) {
        $promises[] = async(function () use ($product) {
            $product->decrementStock(1);
        });
    }
    
    await($promises);
    
    $product->refresh();
    $this->assertEquals(0, $product->stock);
}
```

### Best Practices

1. **Selalu gunakan transaction** untuk operasi yang melibatkan multiple tables
2. **Lock row** saat update data yang sering di-access concurrent
3. **Atomic operations** untuk counter/balance updates
4. **Version control** untuk detect concurrent modifications
5. **Database constraints** untuk enforce business rules
6. **Queue** untuk operasi sequential yang penting
7. **Audit log** untuk track semua perubahan data kritis
8. **Test** concurrent scenarios sebelum production

