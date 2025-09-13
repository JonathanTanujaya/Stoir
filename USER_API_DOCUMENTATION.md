# USER API DOCUMENTATION

## Overview
API ini menyediakan operasi CRUD (Create, Read, Update, Delete) lengkap untuk entitas User dalam sistem ERP. User dalam sistem ini menggunakan composite key yang terdiri dari `kode_divisi` dan `username`.

## Table Structure
```sql
-- Tabel master_user
CREATE TABLE master_user (
    kode_divisi VARCHAR(5) NOT NULL,
    username VARCHAR(50) NOT NULL,
    nama VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL,
    PRIMARY KEY (kode_divisi, username)
);
```

## Authentication
Semua endpoint User API memerlukan autentikasi menggunakan Sanctum Bearer Token:
```
Authorization: Bearer {your-api-token}
```

## Base URL
```
/api/divisi/{kodeDivisi}/users
```

## Endpoints

### 1. List Users
**GET** `/api/divisi/{kodeDivisi}/users`

Mengambil daftar user dalam divisi tertentu dengan fitur pencarian, filter, dan pagination.

#### Parameters
- `kodeDivisi` (path) - Kode divisi (required)
- `search` (query) - Pencarian dalam username atau nama
- `username` (query) - Filter berdasarkan username
- `nama` (query) - Filter berdasarkan nama
- `sort_by` (query) - Field untuk sorting (username, nama). Default: username
- `sort_order` (query) - Arah sorting (asc, desc). Default: asc
- `per_page` (query) - Jumlah data per halaman (max: 100). Default: 15
- `page` (query) - Nomor halaman. Default: 1

#### Response Example
```json
{
  "data": [
    {
      "kode_divisi": "DIV01",
      "username": "johndoe",
      "nama": "John Doe",
      "display_name": "John Doe (johndoe)",
      "divisi": {
        "kode_divisi": "DIV01",
        "divisi": "Divisi Utama"
      },
      "composite_key": "DIV01-johndoe",
      "created_at": "2024-01-15T10:30:00.000Z",
      "updated_at": "2024-01-15T10:30:00.000Z"
    }
  ],
  "meta": {
    "total": 25,
    "count": 15,
    "per_page": 15,
    "current_page": 1,
    "total_pages": 2,
    "has_more_pages": true
  },
  "links": {
    "first": "http://localhost/api/divisi/DIV01/users?page=1",
    "last": "http://localhost/api/divisi/DIV01/users?page=2",
    "prev": null,
    "next": "http://localhost/api/divisi/DIV01/users?page=2"
  },
  "success": true,
  "message": "Users retrieved successfully"
}
```

### 2. Create User
**POST** `/api/divisi/{kodeDivisi}/users`

Membuat user baru dalam divisi tertentu.

#### Parameters
- `kodeDivisi` (path) - Kode divisi (required)

#### Request Body
```json
{
  "username": "johndoe",
  "nama": "John Doe",
  "password": "secretpassword123"
}
```

#### Validation Rules
- `username`: required, string, max:50, unique dalam divisi
- `nama`: required, string, max:50
- `password`: required, string, min:8

#### Response Example (201 Created)
```json
{
  "kode_divisi": "DIV01",
  "username": "johndoe",
  "nama": "John Doe",
  "display_name": "John Doe (johndoe)",
  "divisi": {
    "kode_divisi": "DIV01",
    "divisi": "Divisi Utama"
  },
  "composite_key": "DIV01-johndoe",
  "created_at": "2024-01-15T10:30:00.000Z",
  "updated_at": "2024-01-15T10:30:00.000Z"
}
```

### 3. Get User Detail
**GET** `/api/divisi/{kodeDivisi}/users/{username}`

Mengambil detail user berdasarkan composite key.

#### Parameters
- `kodeDivisi` (path) - Kode divisi (required)
- `username` (path) - Username (required)

#### Response Example
```json
{
  "kode_divisi": "DIV01",
  "username": "johndoe",
  "nama": "John Doe",
  "display_name": "John Doe (johndoe)",
  "divisi": {
    "kode_divisi": "DIV01",
    "divisi": "Divisi Utama"
  },
  "composite_key": "DIV01-johndoe",
  "created_at": "2024-01-15T10:30:00.000Z",
  "updated_at": "2024-01-15T10:30:00.000Z"
}
```

### 4. Update User
**PUT** `/api/divisi/{kodeDivisi}/users/{username}`

Memperbarui data user. Username tidak dapat diubah karena merupakan bagian dari primary key.

#### Parameters
- `kodeDivisi` (path) - Kode divisi (required)
- `username` (path) - Username (required)

#### Request Body
```json
{
  "nama": "John Doe Updated",
  "password": "newsecretpassword123"
}
```

#### Validation Rules
- `nama`: sometimes, string, max:50
- `password`: sometimes, string, min:8

#### Response Example
```json
{
  "kode_divisi": "DIV01",
  "username": "johndoe",
  "nama": "John Doe Updated",
  "display_name": "John Doe Updated (johndoe)",
  "divisi": {
    "kode_divisi": "DIV01",
    "divisi": "Divisi Utama"
  },
  "composite_key": "DIV01-johndoe",
  "created_at": "2024-01-15T10:30:00.000Z",
  "updated_at": "2024-01-15T15:45:00.000Z"
}
```

### 5. Delete User
**DELETE** `/api/divisi/{kodeDivisi}/users/{username}`

Menghapus user berdasarkan composite key.

#### Parameters
- `kodeDivisi` (path) - Kode divisi (required)
- `username` (path) - Username (required)

#### Response
- **Status**: 204 No Content
- **Body**: Empty

### 6. User Statistics
**GET** `/api/divisi/{kodeDivisi}/users-stats`

Mengambil statistik user dalam divisi tertentu.

#### Parameters
- `kodeDivisi` (path) - Kode divisi (required)

#### Response Example
```json
{
  "total_users": 25,
  "users_by_month": {
    "1": 5,
    "2": 8,
    "3": 12
  }
}
```

## Error Responses

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "username": [
      "Username sudah digunakan dalam divisi ini."
    ],
    "password": [
      "Password minimal 8 karakter."
    ]
  }
}
```

### Not Found (404)
```json
{
  "message": "No query results for model [App\\Models\\User]."
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

## Key Features

### 1. Composite Key Support
- Model menggunakan HasCompositeKey trait
- Primary key: [`kode_divisi`, `username`]
- Route model binding menggunakan username sebagai parameter

### 2. Password Security
- Password otomatis di-hash menggunakan Hash::make()
- Password tidak ditampilkan dalam response (hidden field)
- Password casting menggunakan 'hashed' cast

### 3. Scope-Aware Validation
- Username harus unique dalam scope divisi
- Validasi dilakukan pada level divisi, bukan global

### 4. Search & Filter
- Pencarian dalam field username dan nama
- Filter berdasarkan field tertentu
- Sorting fleksibel dengan validasi field

### 5. Relationship Loading
- Eager loading relationship divisi
- Optimasi query untuk performa

## Implementation Notes

### Model Enhancement
- Menggunakan tabel `master_user`
- Composite primary key dengan HasCompositeKey trait
- Relationship dengan model Divisi
- Password security dengan hashed casting

### Controller Features
- Manual query handling untuk composite key
- Password hashing dalam store dan update
- Search dan filter functionality
- Pagination dengan metadata lengkap

### Validation Strategy
- Scope-aware unique validation
- Custom error messages dalam bahasa Indonesia
- Partial update support dengan 'sometimes' rules

### Security Considerations
- Password selalu di-hash sebelum disimpan
- Password tidak pernah ditampilkan dalam response
- Autentikasi required untuk semua endpoint

## Usage Examples

### Create User with cURL
```bash
curl -X POST "http://localhost/api/divisi/DIV01/users" \
  -H "Authorization: Bearer your-api-token" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "johndoe",
    "nama": "John Doe",
    "password": "secretpassword123"
  }'
```

### Update User Password
```bash
curl -X PUT "http://localhost/api/divisi/DIV01/users/johndoe" \
  -H "Authorization: Bearer your-api-token" \
  -H "Content-Type: application/json" \
  -d '{
    "password": "newsecretpassword123"
  }'
```

### Search Users
```bash
curl -X GET "http://localhost/api/divisi/DIV01/users?search=john&per_page=10" \
  -H "Authorization: Bearer your-api-token"
```

### Get User Statistics
```bash
curl -X GET "http://localhost/api/divisi/DIV01/users-stats" \
  -H "Authorization: Bearer your-api-token"
```
