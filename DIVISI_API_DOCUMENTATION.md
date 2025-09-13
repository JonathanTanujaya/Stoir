# Divisi API Documentation

## Overview
API CRUD lengkap untuk manajemen Divisi dalam sistem ERP Laravel. Divisi adalah entitas master yang menjadi parent untuk semua entitas lain dalam sistem.

## Endpoints

### 1. GET `/api/divisi`
**Deskripsi**: Mendapatkan daftar semua divisi dengan fitur search dan sorting.

**Parameters**:
- `search` (query): Pencarian berdasarkan kode_divisi atau nama_divisi
- `sort_by` (query): Field untuk sorting (kode_divisi, nama_divisi)
- `sort_order` (query): Arah sorting (asc/desc)

**Response**:
```json
{
  "data": [
    {
      "kode_divisi": "D001",
      "nama_divisi": "Divisi Jakarta",
      "banks_count": 3,
      "areas_count": 5,
      "customers_count": 25,
      "sales_count": 8,
      "barangs_count": 150,
      "invoices_count": 45,
      "suppliers_count": 12,
      "display_name": "[D001] Divisi Jakarta",
      "has_data": true
    }
  ],
  "meta": {
    "total": 3
  },
  "summary": {
    "total_divisi": 3,
    "total_banks": 8,
    "total_areas": 15,
    "total_customers": 75,
    "total_sales": 24,
    "total_barangs": 450,
    "total_invoices": 135,
    "total_suppliers": 36
  }
}
```

### 2. POST `/api/divisi`
**Deskripsi**: Membuat divisi baru.

**Request Body**:
```json
{
  "kode_divisi": "D002",
  "nama_divisi": "Divisi Bandung"
}
```

**Validation Rules**:
- `kode_divisi`: required, string, max:5, unique
- `nama_divisi`: required, string, max:50

**Response**:
```json
{
  "message": "Divisi berhasil dibuat.",
  "data": {
    "kode_divisi": "D002",
    "nama_divisi": "Divisi Bandung",
    "banks_count": 0,
    "areas_count": 0,
    "customers_count": 0,
    "sales_count": 0,
    "barangs_count": 0,
    "invoices_count": 0,
    "suppliers_count": 0,
    "display_name": "[D002] Divisi Bandung",
    "has_data": false
  }
}
```

### 3. GET `/api/divisi/{kodeDivisi}`
**Deskripsi**: Mendapatkan detail divisi tertentu dengan semua relationship data.

**Parameters**:
- `kodeDivisi` (path): Kode divisi (required)

**Response**:
```json
{
  "data": {
    "kode_divisi": "D001",
    "nama_divisi": "Divisi Jakarta",
    "banks_count": 3,
    "areas_count": 5,
    "customers_count": 25,
    "sales_count": 8,
    "barangs_count": 150,
    "invoices_count": 45,
    "suppliers_count": 12,
    "banks": [
      {
        "kode_bank": "BK001",
        "bank": "Bank BCA",
        "status": true
      }
    ],
    "areas": [
      {
        "kode_area": "A001",
        "area": "Area Jakarta Pusat",
        "status": true
      }
    ],
    "display_name": "[D001] Divisi Jakarta",
    "has_data": true
  }
}
```

### 4. PUT/PATCH `/api/divisi/{kodeDivisi}`
**Deskripsi**: Memperbarui divisi tertentu.

**Request Body**:
```json
{
  "nama_divisi": "Divisi Jakarta Pusat"
}
```

**Validation Rules**:
- `kode_divisi`: sometimes, required, string, max:5, unique (ignore current)
- `nama_divisi`: sometimes, required, string, max:50

**Response**:
```json
{
  "message": "Divisi berhasil diperbarui.",
  "data": {
    "kode_divisi": "D001",
    "nama_divisi": "Divisi Jakarta Pusat",
    "banks_count": 3,
    "areas_count": 5,
    "customers_count": 25,
    "sales_count": 8,
    "barangs_count": 150,
    "invoices_count": 45,
    "suppliers_count": 12,
    "display_name": "[D001] Divisi Jakarta Pusat",
    "has_data": true
  }
}
```

### 5. DELETE `/api/divisi/{kodeDivisi}`
**Deskripsi**: Menghapus divisi tertentu. Divisi dengan data terkait tidak dapat dihapus.

**Parameters**:
- `kodeDivisi` (path): Kode divisi (required)

**Response Success**:
```json
{
  "message": "Divisi berhasil dihapus."
}
```

**Response Error (422)**:
```json
{
  "message": "Divisi tidak dapat dihapus karena masih memiliki data terkait.",
  "details": {
    "banks_count": 3,
    "areas_count": 5,
    "customers_count": 25,
    "sales_count": 8,
    "barangs_count": 150,
    "invoices_count": 45,
    "suppliers_count": 12
  }
}
```

### 6. GET `/api/divisi/stats`
**Deskripsi**: Mendapatkan statistik keseluruhan sistem per divisi.

**Response**:
```json
{
  "data": {
    "total_divisi": 3,
    "total_banks": 8,
    "total_areas": 15,
    "total_customers": 75,
    "total_sales": 24,
    "total_barangs": 450,
    "total_invoices": 135,
    "total_suppliers": 36
  }
}
```

## Features

### Single Primary Key
- Model menggunakan single primary key `kode_divisi`
- Standard Laravel Eloquent operations
- String primary key dengan auto-incrementing disabled

### Search & Filter
- Search berdasarkan `kode_divisi` dan `nama_divisi`
- Sorting berdasarkan field yang diizinkan

### Validation
- Unique validation untuk `kode_divisi`
- Field validation sesuai dengan database constraints
- Custom error messages dalam bahasa Indonesia

### Resource Transformation
- Structured JSON response dengan relationship counts
- Meta information dan summary statistics
- Display names untuk kemudahan UI
- Relationship loading dengan safety checks
- Has_data flag untuk mengetahui apakah divisi memiliki data terkait

### Business Logic
- Divisi dengan data terkait tidak dapat dihapus
- Comprehensive check untuk semua relationship counts
- Protection dari cascading deletion yang tidak diinginkan

### Related Entities
Divisi memiliki relationship dengan:
- **Banks**: Bank yang terdaftar per divisi
- **Areas**: Area coverage per divisi
- **Customers**: Customer yang terdaftar per divisi
- **Sales**: Sales person per divisi
- **Barangs**: Product catalog per divisi
- **Invoices**: Transaksi invoice per divisi
- **Suppliers**: Supplier yang terdaftar per divisi

## Error Handling
- 404: Divisi tidak ditemukan
- 422: Validation error atau business logic violation
- 500: Server error dengan detail pesan

## Authentication
Semua endpoints memerlukan authentication menggunakan Sanctum middleware (`auth:sanctum`).

## Usage Examples

### Create New Divisi
```bash
POST /api/divisi
Content-Type: application/json
Authorization: Bearer {token}

{
  "kode_divisi": "D003",
  "nama_divisi": "Divisi Surabaya"
}
```

### Get All Divisi with Search
```bash
GET /api/divisi?search=Jakarta&sort_by=nama_divisi&sort_order=asc
Authorization: Bearer {token}
```

### Update Divisi
```bash
PUT /api/divisi/D001
Content-Type: application/json
Authorization: Bearer {token}

{
  "nama_divisi": "Divisi Jakarta Raya"
}
```
