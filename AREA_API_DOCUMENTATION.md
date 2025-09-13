# Area API Documentation

## Overview
API CRUD lengkap untuk manajemen Area dalam sistem ERP Laravel.

## Endpoints

### 1. GET `/api/divisi/{kodeDivisi}/areas`
**Deskripsi**: Mendapatkan daftar semua area dalam divisi tertentu dengan fitur search, filter, dan sorting.

**Parameters**:
- `kodeDivisi` (path): Kode divisi (required)
- `search` (query): Pencarian berdasarkan kode_area atau nama area
- `status` (query): Filter berdasarkan status (true/false)
- `sort_by` (query): Field untuk sorting (kode_area, area, status)
- `sort_order` (query): Arah sorting (asc/desc)

**Response**:
```json
{
  "data": [
    {
      "kode_divisi": "D001",
      "kode_area": "A001",
      "area": "Area Jakarta Pusat",
      "status": true,
      "status_label": "Aktif",
      "status_badge_class": "success",
      "display_name": "[A001] Area Jakarta Pusat",
      "full_name": "[D001] - [A001] Area Jakarta Pusat"
    }
  ],
  "meta": {
    "total": 5,
    "active_count": 4,
    "inactive_count": 1,
    "divisi_breakdown": [...]
  },
  "summary": {
    "total_areas": 5,
    "active_areas": 4,
    "coverage_by_divisi": 1,
    "average_areas_per_divisi": 5.0
  }
}
```

### 2. POST `/api/divisi/{kodeDivisi}/areas`
**Deskripsi**: Membuat area baru dalam divisi tertentu.

**Request Body**:
```json
{
  "kode_area": "A002",
  "area": "Area Jakarta Selatan",
  "status": true
}
```

**Validation Rules**:
- `kode_area`: required, string, max:10, unique dalam divisi
- `area`: required, string, max:50
- `status`: boolean

**Response**:
```json
{
  "message": "Area berhasil dibuat.",
  "data": {
    "kode_divisi": "D001",
    "kode_area": "A002",
    "area": "Area Jakarta Selatan",
    "status": true,
    "status_label": "Aktif",
    "status_badge_class": "success",
    "display_name": "[A002] Area Jakarta Selatan",
    "full_name": "[D001] - [A002] Area Jakarta Selatan"
  }
}
```

### 3. GET `/api/divisi/{kodeDivisi}/areas/{kodeArea}`
**Deskripsi**: Mendapatkan detail area tertentu.

**Parameters**:
- `kodeDivisi` (path): Kode divisi (required)
- `kodeArea` (path): Kode area (required)

**Response**:
```json
{
  "data": {
    "kode_divisi": "D001",
    "kode_area": "A001",
    "area": "Area Jakarta Pusat",
    "status": true,
    "status_label": "Aktif",
    "status_badge_class": "success",
    "divisi": {
      "kode_divisi": "D001",
      "divisi": "Divisi Jakarta",
      "status": true
    },
    "customers_count": 15,
    "active_customers_count": 12,
    "sales_count": 3,
    "active_sales_count": 3,
    "display_name": "[A001] Area Jakarta Pusat",
    "full_name": "[D001] - [A001] Area Jakarta Pusat"
  }
}
```

### 4. PUT/PATCH `/api/divisi/{kodeDivisi}/areas/{kodeArea}`
**Deskripsi**: Memperbarui area tertentu.

**Request Body**:
```json
{
  "area": "Area Jakarta Pusat (Updated)",
  "status": false
}
```

**Validation Rules**:
- `kode_area`: sometimes, required, string, max:10, unique dalam divisi (ignore current)
- `area`: sometimes, required, string, max:50
- `status`: sometimes, boolean

**Response**:
```json
{
  "message": "Area berhasil diperbarui.",
  "data": {
    "kode_divisi": "D001",
    "kode_area": "A001",
    "area": "Area Jakarta Pusat (Updated)",
    "status": false,
    "status_label": "Tidak Aktif",
    "status_badge_class": "danger",
    "display_name": "[A001] Area Jakarta Pusat (Updated)",
    "full_name": "[D001] - [A001] Area Jakarta Pusat (Updated)"
  }
}
```

### 5. DELETE `/api/divisi/{kodeDivisi}/areas/{kodeArea}`
**Deskripsi**: Menghapus area tertentu. Area dengan data terkait (customers/sales) tidak dapat dihapus.

**Parameters**:
- `kodeDivisi` (path): Kode divisi (required)
- `kodeArea` (path): Kode area (required)

**Response Success**:
```json
{
  "message": "Area berhasil dihapus."
}
```

**Response Error (422)**:
```json
{
  "message": "Area tidak dapat dihapus karena masih memiliki data terkait.",
  "details": {
    "customers_count": 5,
    "sales_count": 2
  }
}
```

### 6. GET `/api/divisi/{kodeDivisi}/areas/stats`
**Deskripsi**: Mendapatkan statistik area dalam divisi tertentu.

**Parameters**:
- `kodeDivisi` (path): Kode divisi (required)

**Response**:
```json
{
  "data": {
    "total_areas": 8,
    "active_areas": 6,
    "inactive_areas": 2,
    "total_customers": 45,
    "total_sales": 12
  }
}
```

## Features

### Composite Key Support
- Model menggunakan composite primary key `['kode_divisi', 'kode_area']`
- Controller menangani composite key dengan query manual untuk update/delete
- Route model binding disesuaikan untuk composite key

### Search & Filter
- Search berdasarkan `kode_area` dan `area`
- Filter berdasarkan `status`
- Sorting berdasarkan field yang diizinkan

### Validation
- Unique validation untuk `kode_area` dalam scope `kode_divisi`
- Field validation sesuai dengan database constraints
- Custom error messages dalam bahasa Indonesia

### Resource Transformation
- Structured JSON response dengan meta information
- Status badge classes untuk UI
- Display names dan full names untuk kemudahan UI
- Relationship loading dengan safety checks
- Statistical summaries dalam collection

### Business Logic
- Area dengan customers atau sales tidak dapat dihapus
- Status management dengan label dan badge classes
- Comprehensive analytics dan statistics

## Error Handling
- 404: Area tidak ditemukan
- 422: Validation error atau business logic violation
- 500: Server error

## Authentication
Semua endpoints memerlukan authentication menggunakan Sanctum middleware (`auth:sanctum`).
