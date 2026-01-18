# Nice Patrol API Documentation

## Overview

Nice Patrol API adalah RESTful API untuk sistem manajemen keamanan yang menyediakan endpoint untuk autentikasi, manajemen patroli, absensi, dan fitur keamanan lainnya.

## Base URLs

- **Production**: `https://api.nicepatrol.id/v1`
- **Development**: `https://devapi.nicepatrol.id/v1`
- **Local**: `http://localhost:8000/api/v1`

## Authentication

API menggunakan Bearer Token authentication dengan Laravel Sanctum.

### Login Process

1. **POST** `/login` dengan email dan password
2. Simpan `token` dari response
3. Gunakan token di header: `Authorization: Bearer {token}`

### Example Login Request

```bash
curl -X POST https://api.nicepatrol.id/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "security@nicepatrol.id",
    "password": "password123"
  }'
```

### Example Response

```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "security@nicepatrol.id",
      "role": "security_officer"
    },
    "token": "1|abc123def456..."
  }
}
```

### Using Token

```bash
curl -X GET https://api.nicepatrol.id/v1/me \
  -H "Authorization: Bearer 1|abc123def456..." \
  -H "Accept: application/json"
```

## Multi-Tenancy

Semua endpoint secara otomatis memfilter data berdasarkan `perusahaan_id` dari user yang terautentikasi. Hanya superadmin yang dapat mengakses data lintas perusahaan.

## Response Format

### Success Response

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data here
  }
}
```

### Error Response

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field": ["Validation error details"]
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
    "total": 100,
    "last_page": 5
  }
}
```

## Main Endpoints

### Authentication
- `POST /login` - User login
- `POST /logout` - User logout
- `GET /me` - Get current user info
- `POST /user/upload-photo` - Upload user photo

### Shift Schedule
- `GET /shift/my-schedule` - Get user's shift schedule
- `GET /shift/today` - Get today's shift

### Absensi (Attendance)
- `GET /absensi/summary` - Get attendance summary
- `GET /absensi/today-status` - Check today's attendance status
- `POST /absensi/check-in` - Record check-in
- `POST /absensi/check-out` - Record check-out
- `POST /absensi/take-break` - Start break
- `POST /absensi/return-from-break` - End break

### Lokasi (Locations)
- `GET /lokasis` - Get all locations
- `POST /lokasis` - Create new location (admin only)
- `GET /lokasis/{id}` - Get location details
- `PUT /lokasis/{id}` - Update location (admin only)
- `DELETE /lokasis/{id}` - Delete location (admin only)

### Checkpoint
- `GET /checkpoints` - Get all checkpoints
- `POST /checkpoints` - Create new checkpoint (admin only)
- `GET /checkpoints/{id}` - Get checkpoint details
- `PUT /checkpoints/{id}` - Update checkpoint (admin only)
- `DELETE /checkpoints/{id}` - Delete checkpoint (admin only)
- `GET /checkpoints/{id}/asets` - Get checkpoint assets
- `POST /checkpoints/{id}/aset-status` - Update asset status

### Patroli (Patrol)
- `GET /patrolis` - Get all patrols
- `POST /patrolis` - Start new patrol
- `GET /patrolis/{id}` - Get patrol details
- `PUT /patrolis/{id}` - Update/end patrol
- `DELETE /patrolis/{id}` - Cancel patrol
- `POST /patrolis/{id}/scan` - Scan checkpoint during patrol
- `GET /patrolis/{id}/gps-locations` - Get patrol GPS tracking
- `POST /scan-qr` - Scan QR code

### Project Contacts
- `GET /projects/{id}/contacts` - Get project contacts
- `POST /projects/{id}/contacts` - Create project contact (admin only)
- `GET /projects/{id}/contacts/emergency` - Get emergency contacts

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized (invalid/missing token)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

## Rate Limiting

API memiliki rate limiting untuk mencegah abuse:
- **Authenticated requests**: 60 requests per minute
- **Login attempts**: 5 attempts per minute

## Testing Credentials

### Development Environment

```
Security Officer:
- Email: security@nicepatrol.id
- Password: password123

Admin Perusahaan:
- Email: admin@nicepatrol.id  
- Password: password123

Superadmin:
- Email: superadmin@nicepatrol.id
- Password: password123
```

## Interactive Documentation

Akses dokumentasi interaktif Swagger UI di:
- **Local**: http://localhost:8000/api-docs
- **Development**: https://devdash.nicepatrol.id/api-docs
- **Production**: https://dash.nicepatrol.id/api-docs

## Example Usage

### Start Patrol

```bash
# 1. Login first
TOKEN=$(curl -s -X POST https://api.nicepatrol.id/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"security@nicepatrol.id","password":"password123"}' \
  | jq -r '.data.token')

# 2. Start patrol
curl -X POST https://api.nicepatrol.id/v1/patrolis \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lokasi_id": 1,
    "tanggal_patroli": "2024-01-17",
    "catatan_mulai": "Memulai patroli rutin"
  }'
```

### Scan Checkpoint

```bash
# Scan checkpoint during patrol
curl -X POST https://api.nicepatrol.id/v1/patrolis/abc123def456/scan \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "checkpoint_id": 1,
    "latitude": -6.2088,
    "longitude": 106.8456,
    "catatan": "Kondisi normal",
    "insiden": false
  }'
```

### Check Attendance

```bash
# Check in
curl -X POST https://api.nicepatrol.id/v1/absensi/check-in \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "lokasi_id": 1,
    "latitude": -6.2088,
    "longitude": 106.8456,
    "catatan": "Masuk tepat waktu"
  }'
```

## SDK & Libraries

### JavaScript/Node.js

```javascript
class NicePatrolAPI {
  constructor(baseURL, token = null) {
    this.baseURL = baseURL;
    this.token = token;
  }

  async login(email, password) {
    const response = await fetch(`${this.baseURL}/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ email, password })
    });
    const data = await response.json();
    if (data.success) {
      this.token = data.data.token;
    }
    return data;
  }

  async get(endpoint) {
    return this.request('GET', endpoint);
  }

  async post(endpoint, data) {
    return this.request('POST', endpoint, data);
  }

  async request(method, endpoint, data = null) {
    const options = {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...(this.token && { 'Authorization': `Bearer ${this.token}` })
      }
    };

    if (data) {
      options.body = JSON.stringify(data);
    }

    const response = await fetch(`${this.baseURL}${endpoint}`, options);
    return response.json();
  }
}

// Usage
const api = new NicePatrolAPI('https://api.nicepatrol.id/v1');
await api.login('security@nicepatrol.id', 'password123');
const user = await api.get('/me');
```

### PHP

```php
class NicePatrolAPI {
    private $baseURL;
    private $token;

    public function __construct($baseURL, $token = null) {
        $this->baseURL = $baseURL;
        $this->token = $token;
    }

    public function login($email, $password) {
        $response = $this->request('POST', '/login', [
            'email' => $email,
            'password' => $password
        ]);
        
        if ($response['success']) {
            $this->token = $response['data']['token'];
        }
        
        return $response;
    }

    public function get($endpoint) {
        return $this->request('GET', $endpoint);
    }

    public function post($endpoint, $data) {
        return $this->request('POST', $endpoint, $data);
    }

    private function request($method, $endpoint, $data = null) {
        $curl = curl_init();
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        if ($this->token) {
            $headers[] = 'Authorization: Bearer ' . $this->token;
        }
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseURL . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $data ? json_encode($data) : null
        ]);
        
        $response = curl_exec($curl);
        curl_close($curl);
        
        return json_decode($response, true);
    }
}

// Usage
$api = new NicePatrolAPI('https://api.nicepatrol.id/v1');
$api->login('security@nicepatrol.id', 'password123');
$user = $api->get('/me');
```

## Support

Untuk pertanyaan atau dukungan teknis:
- Email: support@nicepatrol.id
- Documentation: https://docs.nicepatrol.id
- Status Page: https://status.nicepatrol.id

## Changelog

### v1.0.0 (2024-01-17)
- Initial API release
- Authentication endpoints
- Patrol management
- Attendance system
- Location and checkpoint management
- Project contacts
- Multi-tenancy support