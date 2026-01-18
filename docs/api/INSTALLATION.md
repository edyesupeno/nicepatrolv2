# API Documentation Installation Guide

## Overview

Dokumentasi API Nice Patrol menggunakan OpenAPI 3.0 specification dengan Swagger UI untuk interface interaktif.

## Files Structure

```
docs/api/
├── swagger.yaml                           # OpenAPI specification
├── README.md                             # API documentation
├── INSTALLATION.md                       # This file
├── Nice-Patrol-API.postman_collection.json    # Postman collection
└── Nice-Patrol-Environments.postman_environment.json  # Postman environments

public/
└── api-docs.html                         # Swagger UI interface

routes/
└── web.php                              # Routes for API docs
```

## Access Documentation

### 1. Swagger UI (Interactive)

Akses dokumentasi interaktif melalui browser:

- **Local**: http://localhost:8000/api-docs
- **Development**: https://devdash.nicepatrol.id/api-docs  
- **Production**: https://dash.nicepatrol.id/api-docs

### 2. OpenAPI Specification (YAML)

Download atau akses file YAML:

- **Local**: http://localhost:8000/docs/api/swagger.yaml
- **Development**: https://devdash.nicepatrol.id/docs/api/swagger.yaml
- **Production**: https://dash.nicepatrol.id/docs/api/swagger.yaml

### 3. Postman Collection

Import collection dan environment ke Postman:

1. Download files:
   - `docs/api/Nice-Patrol-API.postman_collection.json`
   - `docs/api/Nice-Patrol-Environments.postman_environment.json`

2. Import ke Postman:
   - File → Import → Select files
   - Pilih environment yang sesuai (Local/Development/Production)

## Testing API

### Using Swagger UI

1. Buka http://localhost:8000/api-docs
2. Pilih environment (Local/Development/Production)
3. Klik endpoint **POST /login**
4. Klik "Try it out"
5. Masukkan credentials:
   ```json
   {
     "email": "security@nicepatrol.id",
     "password": "password123"
   }
   ```
6. Klik "Execute"
7. Copy `token` dari response
8. Klik tombol "Authorize" di atas
9. Masukkan: `Bearer {your_token}`
10. Klik "Authorize" dan "Close"
11. Sekarang bisa test endpoint lain

### Using Postman

1. Import collection dan environment
2. Pilih environment yang sesuai
3. Run request "Login" di folder "Authentication"
4. Token akan otomatis tersimpan untuk request lain
5. Test endpoint lain

### Using cURL

```bash
# Login
TOKEN=$(curl -s -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"security@nicepatrol.id","password":"password123"}' \
  | jq -r '.data.token')

# Test authenticated endpoint
curl -X GET http://localhost:8000/api/v1/me \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

## Customization

### Update API Specification

1. Edit `docs/api/swagger.yaml`
2. Refresh browser di `/api-docs` untuk melihat perubahan
3. Atau restart server jika ada cache issue

### Add New Endpoints

1. Tambahkan endpoint baru di `swagger.yaml`:

```yaml
paths:
  /new-endpoint:
    get:
      tags:
        - New Feature
      summary: Description
      responses:
        '200':
          description: Success
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/NewSchema'
```

2. Tambahkan schema jika perlu:

```yaml
components:
  schemas:
    NewSchema:
      type: object
      properties:
        id:
          type: integer
        name:
          type: string
```

### Update Postman Collection

1. Edit `docs/api/Nice-Patrol-API.postman_collection.json`
2. Tambahkan request baru:

```json
{
  "name": "New Endpoint",
  "request": {
    "method": "GET",
    "header": [
      {
        "key": "Accept",
        "value": "application/json"
      }
    ],
    "url": {
      "raw": "{{base_url}}/new-endpoint",
      "host": ["{{base_url}}"],
      "path": ["new-endpoint"]
    }
  }
}
```

## Advanced Setup

### Install L5-Swagger (Optional)

Jika ingin menggunakan package Laravel untuk generate dokumentasi otomatis:

```bash
composer require darkaonline/l5-swagger
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

### Auto-generate from Code

Tambahkan annotations di controller:

```php
/**
 * @OA\Post(
 *     path="/api/v1/login",
 *     summary="User login",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Login successful")
 * )
 */
public function login(Request $request)
{
    // Implementation
}
```

Generate documentation:

```bash
php artisan l5-swagger:generate
```

## Troubleshooting

### CORS Issues

Jika ada masalah CORS saat akses dari domain berbeda, tambahkan di `config/cors.php`:

```php
'paths' => ['api/*', 'docs/api/*', 'api-docs'],
'allowed_origins' => ['*'],
'allowed_headers' => ['*'],
```

### File Not Found

Pastikan routes sudah ditambahkan di `routes/web.php`:

```php
Route::get('/api-docs', function() {
    return response()->file(public_path('api-docs.html'));
})->name('api.docs');

Route::get('/docs/api/swagger.yaml', function() {
    $yamlPath = base_path('docs/api/swagger.yaml');
    if (file_exists($yamlPath)) {
        return response()->file($yamlPath, [
            'Content-Type' => 'application/x-yaml'
        ]);
    }
    return abort(404);
})->name('api.swagger.yaml');
```

### Cache Issues

Clear cache jika ada masalah:

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
```

## Security Notes

### Production Considerations

1. **Disable di Production** (optional):
   ```php
   // routes/web.php
   if (!app()->environment('production')) {
       Route::get('/api-docs', ...);
   }
   ```

2. **Add Authentication**:
   ```php
   Route::middleware('auth')->group(function () {
       Route::get('/api-docs', ...);
   });
   ```

3. **IP Whitelist**:
   ```php
   Route::middleware('ip.whitelist')->group(function () {
       Route::get('/api-docs', ...);
   });
   ```

### Sensitive Information

- Jangan expose production credentials di documentation
- Gunakan example data yang aman
- Hide internal endpoints jika perlu

## Maintenance

### Regular Updates

1. Update specification saat ada perubahan API
2. Test semua endpoint di Postman collection
3. Update README jika ada perubahan flow
4. Sync dengan actual API implementation

### Version Control

1. Tag version di specification:
   ```yaml
   info:
     version: 1.1.0
   ```

2. Maintain changelog di README
3. Archive old versions jika perlu

## Support

Untuk pertanyaan atau masalah:
- Check existing endpoints di `/api-docs`
- Test dengan Postman collection
- Review API logs untuk debugging
- Contact: support@nicepatrol.id