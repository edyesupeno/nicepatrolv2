# Peminjaman Aset - Login Redirect Issue Fix

## Issue Reported
- **Problem**: When saving asset return form, user gets redirected to login page
- **Expected**: Form should process successfully and redirect to asset detail page
- **Impact**: Users cannot complete asset return process

## Possible Causes Analysis

### 1. **Session Expiration**
- User session may have expired while filling the form
- Long form completion time can cause session timeout
- CSRF token becomes invalid with expired session

### 2. **Authentication Middleware**
- Route has `perusahaan` middleware which checks authentication
- If session expires, middleware redirects to login
- Form submission fails authentication check

### 3. **CSRF Token Issues**
- Token mismatch due to session expiration
- Token not properly included in form
- Token validation failure

### 4. **Route/Method Issues**
- Incorrect form action URL
- Wrong HTTP method (GET vs POST)
- Route parameter binding issues

## Investigation Results

### ✅ **Route Configuration - CORRECT**
```php
// routes/web.php
Route::prefix('perusahaan')->name('perusahaan.')->middleware('perusahaan')->group(function () {
    Route::post('peminjaman-aset/{peminjamanAset}/return', [PeminjamanAsetController::class, 'returnAsset'])
        ->name('peminjaman-aset.return');
});
```

### ✅ **Middleware Configuration - CORRECT**
```php
// app/Http/Middleware/PerusahaanMiddleware.php
public function handle(Request $request, Closure $next): Response
{
    if (!auth()->check()) {
        return redirect()->route('login'); // This causes the redirect
    }
    // ... other checks
}
```

### ✅ **Form Configuration - CORRECT**
```html
<form action="{{ route('perusahaan.peminjaman-aset.return', $peminjamanAset->hash_id) }}" 
      method="POST" enctype="multipart/form-data">
    @csrf
    <!-- form fields -->
</form>
```

### ✅ **Controller Method - CORRECT**
```php
public function returnAsset(Request $request, PeminjamanAset $peminjamanAset)
{
    // Validation and processing logic
}
```

## Solutions Implemented

### 1. **Enhanced Error Handling**
Added comprehensive error handling in controller:

```php
public function returnAsset(Request $request, PeminjamanAset $peminjamanAset)
{
    try {
        // ... existing logic ...
        
        return redirect()
            ->route('perusahaan.peminjaman-aset.show', $peminjamanAset->hash_id)
            ->with('success', 'Aset berhasil dikembalikan');
            
    } catch (\Exception $e) {
        \Log::error('Error in returnAsset: ' . $e->getMessage(), [
            'peminjaman_id' => $peminjamanAset->id,
            'user_id' => auth()->id(),
            'request_data' => $request->all()
        ]);
        
        return redirect()
            ->route('perusahaan.peminjaman-aset.return-form', $peminjamanAset->hash_id)
            ->with('error', 'Terjadi kesalahan saat memproses pengembalian aset. Silakan coba lagi.')
            ->withInput();
    }
}
```

### 2. **Enhanced Form Error Display**
Added error message display in form:

```html
@if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
            <div class="text-red-800">{{ session('error') }}</div>
        </div>
    </div>
@endif
```

### 3. **Enhanced JavaScript Debugging**
Added debug logging in JavaScript:

```javascript
// Add debug info to form
console.log('Submitting form to:', returnForm.action);
console.log('CSRF Token:', returnForm.querySelector('input[name="_token"]').value);
console.log('Method:', returnForm.method);

// Submit the form
returnForm.submit();
```

### 4. **Form ID Addition**
Added form ID for better JavaScript handling:

```html
<form id="returnForm" action="..." method="POST" enctype="multipart/form-data">
```

## Debugging Steps for Users

### 1. **Check Browser Console**
- Open Developer Tools (F12)
- Check Console tab for JavaScript errors
- Look for network requests and responses

### 2. **Check Session Status**
- Verify user is still logged in
- Check if session has expired
- Try refreshing the page before submitting

### 3. **Check Form Data**
- Ensure all required fields are filled
- Verify file upload size limits
- Check CSRF token presence

### 4. **Check Laravel Logs**
- Look at `storage/logs/laravel.log`
- Check for authentication errors
- Look for validation errors

## Prevention Measures

### 1. **Session Configuration**
Ensure proper session configuration in `config/session.php`:

```php
'lifetime' => 120, // 2 hours
'expire_on_close' => false,
'encrypt' => false,
'files' => storage_path('framework/sessions'),
'connection' => null,
'table' => 'sessions',
'store' => null,
'lottery' => [2, 100],
'cookie' => env('SESSION_COOKIE', 'laravel_session'),
'path' => '/',
'domain' => env('SESSION_DOMAIN', null),
'secure' => env('SESSION_SECURE_COOKIE', false),
'http_only' => true,
'same_site' => 'lax',
```

### 2. **CSRF Protection**
Ensure CSRF token is properly included:

```html
@csrf
<!-- or -->
<input type="hidden" name="_token" value="{{ csrf_token() }}">
```

### 3. **Form Validation**
Client-side validation before submission:

```javascript
// Validate required fields before submission
const kondisi = kondisiSelect.value;
if (!kondisi) {
    // Show error and prevent submission
    return;
}
```

## Files Modified

1. `app/Http/Controllers/Perusahaan/PeminjamanAsetController.php`
   - Added try-catch error handling in `returnAsset()` method
   - Added logging for debugging
   - Added proper error redirect with input

2. `resources/views/perusahaan/peminjaman-aset/return.blade.php`
   - Added error message display
   - Added form ID for JavaScript
   - Added debug logging in JavaScript

## Next Steps for Troubleshooting

If the issue persists:

1. **Check Laravel Logs**: Look for specific error messages
2. **Verify Session**: Ensure session is not expiring too quickly
3. **Test Authentication**: Verify user remains authenticated
4. **Check CSRF**: Ensure CSRF token is valid
5. **Network Tab**: Check browser network tab for failed requests
6. **Server Logs**: Check web server logs for additional errors

The enhanced error handling and logging should now provide better insight into what's causing the login redirect issue.