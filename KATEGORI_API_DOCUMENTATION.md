# Kategori API Documentation

## Overview
API CRUD lengkap untuk manajemen Kategori produk dalam sistem ERP Laravel. Kategori merupakan master data yang digunakan untuk mengklasifikasikan barang dan paket dalam setiap divisi.

## Endpoints

### 1. GET `/api/divisi/{kodeDivisi}/kategoris`
**Deskripsi**: Mendapatkan daftar semua kategori dalam divisi tertentu dengan fitur search, filter, dan sorting.

**Parameters**:
- `kodeDivisi` (path): Kode divisi (required)
- `search` (query): Pencarian berdasarkan kode_kategori atau nama kategori
- `status` (query): Filter berdasarkan status (true/false)
- `sort_by` (query): Field untuk sorting (kode_kategori, kategori, status)
- `sort_order` (query): Arah sorting (asc/desc)

**Response**:
```json
{
  "data": [
    {
      "kode_divisi": "D001",
      "kode_kategori": "K001",
      "kategori": "Elektronik",
      "status": true,
      "status_label": "Aktif",
      "status_badge_class": "success",
      "barangs_count": 25,
      "active_barangs_count": 23,
      "dpakets_count": 5,
      "display_name": "[K001] Elektronik",
      "full_name": "[D001] - [K001] Elektronik"
    }
  ],
  "meta": {
    "total": 8,
    "active_count": 7,
    "inactive_count": 1,
    "divisi_breakdown": [
      {
        "kode_divisi": "D001",
        "total": 8,
        "active": 7,
        "inactive": 1
      }
    ]
  },
  "summary": {
    "total_kategoris": 8,
    "active_kategoris": 7,
    "coverage_by_divisi": 1,
    "average_kategoris_per_divisi": 8.0
  }
}
```

### 2. POST `/api/divisi/{kodeDivisi}/kategoris`
**Deskripsi**: Membuat kategori baru dalam divisi tertentu.

**Request Body**:
```json
{
  "kode_kategori": "K002",
  "kategori": "Fashion",
  "status": true
}
```

**Validation Rules**:
- `kode_kategori`: required, string, max:10, unique dalam divisi
- `kategori`: required, string, max:50
- `status`: boolean (optional, default: true)

**Response**:
```json
{
  "message": "Kategori berhasil dibuat.",
  "data": {
    "kode_divisi": "D001",
    "kode_kategori": "K002",
    "kategori": "Fashion",
    "status": true,
    "status_label": "Aktif",
    "status_badge_class": "success",
    "divisi": {
      "kode_divisi": "D001",
      "nama_divisi": "Divisi Jakarta"
    },
    "display_name": "[K002] Fashion",
    "full_name": "[D001] - [K002] Fashion"
  }
}
```

### 3. GET `/api/divisi/{kodeDivisi}/kategoris/{kodeKategori}`
**Deskripsi**: Mendapatkan detail kategori tertentu dengan informasi relationship.

**Parameters**:
- `kodeDivisi` (path): Kode divisi (required)
- `kodeKategori` (path): Kode kategori (required)

**Response**:
```json
{
  "data": {
    "kode_divisi": "D001",
    "kode_kategori": "K001",
    "kategori": "Elektronik",
    "status": true,
    "status_label": "Aktif",
    "status_badge_class": "success",
    "divisi": {
      "kode_divisi": "D001",
      "nama_divisi": "Divisi Jakarta"
    },
    "barangs_count": 25,
    "active_barangs_count": 23,
    "dpakets_count": 5,
    "display_name": "[K001] Elektronik",
    "full_name": "[D001] - [K001] Elektronik"
  }
}
```

### 4. PUT/PATCH `/api/divisi/{kodeDivisi}/kategoris/{kodeKategori}`
**Deskripsi**: Memperbarui kategori tertentu. Mendukung update kode_kategori dengan penanganan composite key.

**Request Body**:
```json
{
  "kategori": "Elektronik & Gadget",
  "status": false
}
```

**Validation Rules**:
- `kode_kategori`: sometimes, required, string, max:10, unique dalam divisi (ignore current)
- `kategori`: sometimes, required, string, max:50
- `status`: sometimes, boolean

**Response**:
```json
{
  "message": "Kategori berhasil diperbarui.",
  "data": {
    "kode_divisi": "D001",
    "kode_kategori": "K001",
    "kategori": "Elektronik & Gadget",
    "status": false,
    "status_label": "Tidak Aktif",
    "status_badge_class": "danger",
    "display_name": "[K001] Elektronik & Gadget",
    "full_name": "[D001] - [K001] Elektronik & Gadget"
  }
}
```

### 5. DELETE `/api/divisi/{kodeDivisi}/kategoris/{kodeKategori}`
**Deskripsi**: Menghapus kategori tertentu. Kategori dengan data terkait (barangs/dpakets) tidak dapat dihapus.

**Parameters**:
- `kodeDivisi` (path): Kode divisi (required)
- `kodeKategori` (path): Kode kategori (required)

**Response Success**:
```json
{
  "message": "Kategori berhasil dihapus."
}
```

**Response Error (422)**:
```json
{
  "message": "Kategori tidak dapat dihapus karena masih memiliki data terkait.",
  "details": {
    "barangs_count": 25,
    "dpakets_count": 5
  }
}
```

### 6. GET `/api/divisi/{kodeDivisi}/kategoris/stats`
**Deskripsi**: Mendapatkan statistik kategori dalam divisi tertentu.

**Parameters**:
- `kodeDivisi` (path): Kode divisi (required)

**Response**:
```json
{
  "data": {
    "total_kategoris": 8,
    "active_kategoris": 7,
    "inactive_kategoris": 1,
    "total_barangs": 150,
    "total_dpakets": 25
  }
}
```

## Features

### Composite Key Support
- Model menggunakan composite primary key `['kode_divisi', 'kode_kategori']`
- HasCompositeKey trait untuk handling composite key operations
- Controller menangani composite key dengan query manual untuk update/delete
- Route model binding disesuaikan untuk composite key
- Special handling untuk update kode_kategori (create new + delete old)

### Search & Filter
- Search berdasarkan `kode_kategori` dan `kategori`
- Filter berdasarkan `status`
- Sorting berdasarkan field yang diizinkan

### Validation
- Unique validation untuk `kode_kategori` dalam scope `kode_divisi`
- Field validation sesuai dengan database constraints
- Custom error messages dalam bahasa Indonesia
- Sometimes validation untuk partial updates

### Resource Transformation
- Structured JSON response dengan meta information
- Status badge classes untuk UI
- Display names dan full names untuk kemudahan UI
- Relationship loading dengan safety checks
- Statistical counts untuk related entities

### Business Logic
- Kategori dengan barangs atau dpakets tidak dapat dihapus
- Status management dengan label dan badge classes
- Automatic kode_divisi assignment dari route parameter
- Default status = true untuk new kategori

### Related Entities
Kategori memiliki relationship dengan:
- **Divisi**: Parent divisi reference
- **Barangs**: Produk yang menggunakan kategori ini
- **DPakets**: Paket produk yang menggunakan kategori ini

## Error Handling
- 404: Kategori tidak ditemukan
- 422: Validation error atau business logic violation (cannot delete with related data)
- 500: Server error dengan detail pesan

## Authentication
Semua endpoints memerlukan authentication menggunakan Sanctum middleware (`auth:sanctum`).

## Usage Examples

### Create New Kategori
```bash
POST /api/divisi/D001/kategoris
Content-Type: application/json
Authorization: Bearer {token}

{
  "kode_kategori": "K003",
  "kategori": "Makanan & Minuman",
  "status": true
}
```

### Get All Kategoris with Search and Filter
```bash
GET /api/divisi/D001/kategoris?search=Elektronik&status=true&sort_by=kategori&sort_order=asc
Authorization: Bearer {token}
```

### Update Kategori
```bash
PUT /api/divisi/D001/kategoris/K001
Content-Type: application/json
Authorization: Bearer {token}

{
  "kategori": "Elektronik & Teknologi",
  "status": true
}
```

### Update Kode Kategori (Special Handling)
```bash
PUT /api/divisi/D001/kategoris/K001
Content-Type: application/json
Authorization: Bearer {token}

{
  "kode_kategori": "ELEC",
  "kategori": "Electronics"
}
```

## Database Schema
```sql
CREATE TABLE m_kategori (
    kode_divisi VARCHAR(5) NOT NULL,
    kode_kategori VARCHAR(10) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    status BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (kode_divisi, kode_kategori),
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi)
);
```

## Business Rules
1. Kode kategori harus unique dalam scope divisi
2. Kategori aktif dapat memiliki barangs dan dpakets
3. Kategori tidak dapat dihapus jika masih digunakan oleh barangs atau dpakets
4. Update kode_kategori akan membuat record baru dan menghapus yang lama
5. Status default adalah true (aktif) untuk kategori baru
