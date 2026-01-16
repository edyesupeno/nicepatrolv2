---
inclusion: always
---

# Mobile API Standards - Nice Patrol

## CRITICAL RULE: Mobile App HARUS Pakai API

### ❌ DILARANG KERAS
- **JANGAN** buat controller di `app/Http/Controllers/Mobile/` yang handle business logic
- **JANGAN** pakai Laravel session untuk mobile authentication
- **JANGAN** pakai form POST langsung ke Laravel routes
- **JANGAN** buat logic di Mobile controller yang seharusnya di API

### ✅ WAJIB
- **SEMUA** business logic mobile HARUS di `app/Http/Controllers/Api/`
- **SEMUA** authentication mobile HARUS pakai Sanctum Token
- **SEMUA** data fetch HARUS dari API endpoint
- Mobile views (`resources/views/mobile/`) HANYA untuk tampilan HTML
- Mobile JavaScript HARUS pakai `fetch()` atau `axios` ke API

## Struktur yang Benar

### Mobile Views (app.nicepatrol.id)
```
resources/views/mobile/
├── security/
│   ├── home.blade.php          # HANYA HTML + JavaScript
│   ├── patroli.blade.php       # HANYA HTML + JavaScript
│   └── scan.blade.php          # HANYA HTML + JavaScript
└── layouts/
    └── app.blade.php           # Layout dengan API helper
```

**Isi view:**
- HTML structure
- JavaScript untuk fetch data dari API
- No PHP business logic
- No direct database query

### API Controllers (api.nicepatrol.id)
```
app/Http/Controllers/Api/
├── AuthController.php          # Login, logout, user info
├── PatroliController.php       # CRUD patroli
├── CheckpointController.php    # CRUD checkpoint
├── LokasiController.php        # Get lokasi
└── ScanController.php          # QR scan logic
```

**Isi controller:**
- Business logic
- Database queries
- Validation
- Return JSON response

## Contoh SALAH vs BENAR

### ❌ SALAH - Logic di Mobile Controller
```php
// app/Http/Controllers/Mobile/PatroliController.php
public function store(Request $request)
{
    $validated = $request->validate([...]);
    $patroli = Patroli::create($validated);
    return view('mobile.security.patroli', compact('patroli'));
}
```

### ✅ BENAR - Logic di API, View Fetch dari API
```php
// app/Http/Controllers/Api/PatroliController.php
public function store(Request $request)
{
    $validated = $request->validate([...]);
    $patroli = Patroli::create($validated);
    return response()->json([
        'success' => true,
        'data' => $patroli
    ]);
}
```

```javascript
// resources/views/mobile/security/patroli.blade.php
async function createPatroli(data) {
    const response = await API.post('/patrolis', data);
    if (response.success) {
        // Update UI
    }
}
```

## Mobile Routes Structure

### routes/web.php (Mobile Views Only)
```php
// Mobile PWA Routes - HANYA untuk serve views
Route::domain(env('MOBILE_DOMAIN', 'app.nicepatrol.id'))->group(function () {
    // Login view
    Route::get('/login', function() {
        return view('mobile.auth.login');
    })->name('mobile.login');
    
    // Security views (no auth middleware, no business logic)
    Route::prefix('security')->group(function () {
        Route::get('/home', function() {
            return view('mobile.security.home');
        });
        Route::get('/patroli', function() {
            return view('mobile.security.patroli');
        });
    });
});
```

### routes/api.php (Business Logic)
```php
// API Routes - SEMUA business logic disini
Route::domain(env('API_DOMAIN', 'api.nicepatrol.id'))->prefix('v1')->group(function () {
    Route::post('/login', [Api\AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('patrolis', Api\PatroliController::class);
        Route::post('patrolis/{patroli}/scan', [Api\PatroliController::class, 'scanCheckpoint']);
        Route::apiResource('checkpoints', Api\CheckpointController::class);
    });
});
```

## Authentication Flow

### ✅ BENAR - Token-based
```javascript
// 1. Login via API
const response = await fetch('https://api.nicepatrol.id/v1/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
});

const data = await response.json();

// 2. Save token
localStorage.setItem('auth_token', data.data.token);

// 3. Use token for subsequent requests
const patrolis = await fetch('https://api.nicepatrol.id/v1/patrolis', {
    headers: { 
        'Authorization': `Bearer ${localStorage.getItem('auth_token')}` 
    }
});
```

### ❌ SALAH - Session-based
```php
// JANGAN pakai ini untuk mobile!
Route::post('/mobile/login', function(Request $request) {
    Auth::attempt($request->only('email', 'password'));
    return redirect('/mobile/home');
});
```

## API Response Format

### Success Response
```json
{
    "success": true,
    "message": "Operation successful",
    "data": {
        // Your data here
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["Error detail"]
    }
}
```

### Pagination Response
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [...],
        "per_page": 20,
        "total": 100
    }
}
```

## JavaScript API Helper

Gunakan helper yang sudah dibuat di `public/mobile/js/app.js`:

```javascript
// GET request
const patrolis = await API.get('/patrolis');

// POST request
const newPatroli = await API.post('/patrolis', {
    lokasi_id: 1,
    tanggal_patroli: '2024-01-17'
});

// PUT request
const updated = await API.put('/patrolis/1', { status: 'selesai' });

// DELETE request
await API.delete('/patrolis/1');
```

## Checklist Sebelum Commit

Saat develop mobile feature, pastikan:

- [ ] Business logic ada di `app/Http/Controllers/Api/`
- [ ] Mobile views HANYA HTML + JavaScript
- [ ] Authentication pakai Sanctum token (bukan session)
- [ ] Semua data fetch dari API endpoint
- [ ] API return JSON response (bukan view)
- [ ] Token disimpan di localStorage
- [ ] API endpoint ter-dokumentasi
- [ ] Error handling dengan proper HTTP status code
- [ ] Multi-tenancy rules tetap berlaku di API

## Exception: Kapan Boleh Pakai Controller Mobile?

**HANYA** untuk:
1. Serve static view (return view saja, no logic)
2. Redirect sederhana

**Contoh yang diperbolehkan:**
```php
// app/Http/Controllers/Mobile/AuthController.php
public function showLogin()
{
    return view('mobile.auth.login'); // OK - hanya serve view
}
```

## Benefits Approach Ini

1. ✅ **Reusable** - API bisa dipakai untuk native app iOS/Android
2. ✅ **Scalable** - API bisa di-deploy terpisah (microservices)
3. ✅ **Testable** - API mudah di-test dengan Postman/PHPUnit
4. ✅ **Offline-first** - PWA bisa cache API response
5. ✅ **Clean separation** - Frontend (Mobile) terpisah dari Backend (API)
6. ✅ **Future-proof** - Siap untuk native app development

## Contoh Lengkap: Feature Patroli

### 1. API Controller
```php
// app/Http/Controllers/Api/PatroliController.php
class PatroliController extends Controller
{
    public function index(Request $request)
    {
        $patrolis = Patroli::with('lokasi')
            ->where('user_id', $request->user()->id)
            ->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $patrolis
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lokasi_id' => 'required|exists:lokasis,id',
            'tanggal_patroli' => 'required|date',
        ]);
        
        $validated['user_id'] = $request->user()->id;
        $validated['perusahaan_id'] = $request->user()->perusahaan_id;
        
        $patroli = Patroli::create($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Patroli berhasil dibuat',
            'data' => $patroli
        ], 201);
    }
}
```

### 2. API Route
```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/patrolis', [Api\PatroliController::class, 'index']);
    Route::post('/patrolis', [Api\PatroliController::class, 'store']);
});
```

### 3. Mobile View
```html
<!-- resources/views/mobile/security/patroli.blade.php -->
<div id="patroliList"></div>
<button onclick="createPatroli()">Mulai Patroli</button>

<script>
// Load patroli list
async function loadPatrolis() {
    const response = await API.get('/patrolis');
    if (response.success) {
        renderPatrolis(response.data.data);
    }
}

// Create new patroli
async function createPatroli() {
    const data = {
        lokasi_id: document.getElementById('lokasi').value,
        tanggal_patroli: new Date().toISOString().split('T')[0]
    };
    
    const response = await API.post('/patrolis', data);
    if (response.success) {
        Swal.fire('Berhasil', response.message, 'success');
        loadPatrolis();
    }
}

// Load on page ready
loadPatrolis();
</script>
```

### 4. Web Route (View Only)
```php
// routes/web.php
Route::get('/security/patroli', function() {
    return view('mobile.security.patroli');
});
```

## Summary

**Golden Rule:**
> Mobile app adalah **CLIENT** yang consume **API**. 
> Semua logic ada di API, mobile hanya tampilan + JavaScript.

**Remember:**
- Mobile views = HTML + JavaScript
- API controllers = Business logic + Database
- Authentication = Token (bukan session)
- Data flow = View → API → Database

Ikuti aturan ini untuk memastikan mobile app scalable dan maintainable!
