# INVOICE DETAIL API DOCUMENTATION

## Overview
API ini menyediakan operasi CRUD (Create, Read, Update, Delete) lengkap untuk entitas InvoiceDetail dalam sistem ERP. InvoiceDetail adalah nested resource di bawah Invoice yang merepresentasikan item-item dalam sebuah invoice.

## Table Structure
```sql
-- Tabel invoice_detail
CREATE TABLE invoice_detail (
    id SERIAL PRIMARY KEY,
    kode_divisi VARCHAR(5) NOT NULL,
    no_invoice VARCHAR(15) NOT NULL,
    kode_barang VARCHAR(50) NOT NULL,
    qty_supply INT NOT NULL,
    harga_jual NUMERIC(15,2) NOT NULL,
    jenis VARCHAR(50),
    diskon1 NUMERIC(5,2),
    diskon2 NUMERIC(5,2),
    harga_nett NUMERIC(15,2) NOT NULL,
    status VARCHAR(50)
);
```

## Authentication
Semua endpoint InvoiceDetail API memerlukan autentikasi menggunakan Sanctum Bearer Token:
```
Authorization: Bearer {your-api-token}
```

## Base URL
```
/api/divisi/{kodeDivisi}/invoices/{noInvoice}/details
```

## Endpoints

### 1. List Invoice Details
**GET** `/api/divisi/{kodeDivisi}/invoices/{noInvoice}/details`

Mengambil daftar detail item dalam invoice tertentu dengan fitur pencarian, filter, dan pagination.

#### Parameters
- `kodeDivisi` (path) - Kode divisi (required)
- `noInvoice` (path) - Nomor invoice (required)
- `search` (query) - Pencarian dalam kode_barang, jenis, atau status
- `kode_barang` (query) - Filter berdasarkan kode barang
- `jenis` (query) - Filter berdasarkan jenis
- `status` (query) - Filter berdasarkan status
- `min_harga` (query) - Filter harga jual minimum
- `max_harga` (query) - Filter harga jual maksimum
- `min_qty` (query) - Filter quantity minimum
- `max_qty` (query) - Filter quantity maksimum
- `sort_by` (query) - Field untuk sorting (id, kode_barang, qty_supply, harga_jual, harga_nett, jenis, status). Default: id
- `sort_order` (query) - Arah sorting (asc, desc). Default: asc
- `per_page` (query) - Jumlah data per halaman (max: 100). Default: 15
- `page` (query) - Nomor halaman. Default: 1

#### Response Example
```json
{
  "data": [
    {
      "id": 1,
      "kode_divisi": "DIV01",
      "no_invoice": "INV001",
      "kode_barang": "BRG001",
      "qty_supply": 10,
      "harga_jual": 100000.00,
      "jenis": "Regular",
      "diskon1": 5.00,
      "diskon2": 2.00,
      "harga_nett": 930000.00,
      "status": "Active",
      "gross_amount": 1000000.00,
      "total_discount_percent": 7.00,
      "discount_amount": 70000.00,
      "unit_nett_price": 93000.00,
      "display_name": "BRG001 (Qty: 10)",
      "formatted_gross_amount": "Rp 1.000.000,00",
      "formatted_nett_amount": "Rp 930.000,00",
      "status_badge_class": "badge-success",
      "invoice": {
        "kode_divisi": "DIV01",
        "no_invoice": "INV001",
        "tanggal": "2024-01-15",
        "customer_name": "PT ABC"
      },
      "barang": {
        "kode_divisi": "DIV01",
        "kode_barang": "BRG001",
        "barang": "Produk ABC",
        "satuan": "PCS",
        "status": true
      },
      "divisi": {
        "kode_divisi": "DIV01",
        "divisi": "Divisi Utama"
      },
      "created_at": "2024-01-15T10:30:00.000Z",
      "updated_at": "2024-01-15T10:30:00.000Z"
    }
  ],
  "meta": {
    "total": 5,
    "count": 5,
    "per_page": 15,
    "current_page": 1,
    "total_pages": 1,
    "has_more_pages": false
  },
  "links": {
    "first": "http://localhost/api/divisi/DIV01/invoices/INV001/details?page=1",
    "last": "http://localhost/api/divisi/DIV01/invoices/INV001/details?page=1",
    "prev": null,
    "next": null
  },
  "summary": {
    "total_items": 5,
    "total_quantity": 50,
    "total_gross_amount": 5000000.00,
    "total_net_amount": 4650000.00,
    "average_unit_price": 100000.00
  },
  "success": true,
  "message": "Invoice details retrieved successfully"
}
```

### 2. Create Invoice Detail
**POST** `/api/divisi/{kodeDivisi}/invoices/{noInvoice}/details`

Membuat item detail baru dalam invoice tertentu.

#### Parameters
- `kodeDivisi` (path) - Kode divisi (required)
- `noInvoice` (path) - Nomor invoice (required)

#### Request Body
```json
{
  "kode_barang": "BRG001",
  "qty_supply": 10,
  "harga_jual": 100000,
  "jenis": "Regular",
  "diskon1": 5.0,
  "diskon2": 2.0,
  "harga_nett": 930000,
  "status": "Active"
}
```

#### Validation Rules
- `kode_barang`: required, string, max:50, exists dalam master_barang untuk divisi
- `qty_supply`: required, integer, min:1
- `harga_jual`: required, numeric, min:0
- `jenis`: nullable, string, max:50
- `diskon1`: nullable, numeric, min:0, max:100
- `diskon2`: nullable, numeric, min:0, max:100
- `harga_nett`: required, numeric, min:0
- `status`: nullable, string, max:50

#### Response Example (201 Created)
```json
{
  "id": 1,
  "kode_divisi": "DIV01",
  "no_invoice": "INV001",
  "kode_barang": "BRG001",
  "qty_supply": 10,
  "harga_jual": 100000.00,
  "jenis": "Regular",
  "diskon1": 5.00,
  "diskon2": 2.00,
  "harga_nett": 930000.00,
  "status": "Active",
  "gross_amount": 1000000.00,
  "total_discount_percent": 7.00,
  "discount_amount": 70000.00,
  "unit_nett_price": 93000.00,
  "display_name": "BRG001 (Qty: 10)",
  "formatted_gross_amount": "Rp 1.000.000,00",
  "formatted_nett_amount": "Rp 930.000,00",
  "status_badge_class": "badge-success",
  "created_at": "2024-01-15T10:30:00.000Z",
  "updated_at": "2024-01-15T10:30:00.000Z"
}
```

### 3. Get Invoice Detail
**GET** `/api/divisi/{kodeDivisi}/invoices/{noInvoice}/details/{id}`

Mengambil detail item invoice berdasarkan ID.

#### Parameters
- `kodeDivisi` (path) - Kode divisi (required)
- `noInvoice` (path) - Nomor invoice (required)
- `id` (path) - ID invoice detail (required)

#### Response Example
```json
{
  "id": 1,
  "kode_divisi": "DIV01",
  "no_invoice": "INV001",
  "kode_barang": "BRG001",
  "qty_supply": 10,
  "harga_jual": 100000.00,
  "jenis": "Regular",
  "diskon1": 5.00,
  "diskon2": 2.00,
  "harga_nett": 930000.00,
  "status": "Active",
  "gross_amount": 1000000.00,
  "total_discount_percent": 7.00,
  "discount_amount": 70000.00,
  "unit_nett_price": 93000.00,
  "display_name": "BRG001 (Qty: 10)",
  "formatted_gross_amount": "Rp 1.000.000,00",
  "formatted_nett_amount": "Rp 930.000,00",
  "status_badge_class": "badge-success",
  "invoice": {
    "kode_divisi": "DIV01",
    "no_invoice": "INV001",
    "tanggal": "2024-01-15",
    "customer_name": "PT ABC"
  },
  "barang": {
    "kode_divisi": "DIV01",
    "kode_barang": "BRG001",
    "barang": "Produk ABC",
    "satuan": "PCS",
    "status": true
  },
  "divisi": {
    "kode_divisi": "DIV01",
    "divisi": "Divisi Utama"
  },
  "created_at": "2024-01-15T10:30:00.000Z",
  "updated_at": "2024-01-15T10:30:00.000Z"
}
```

### 4. Update Invoice Detail
**PUT** `/api/divisi/{kodeDivisi}/invoices/{noInvoice}/details/{id}`

Memperbarui data item detail invoice.

#### Parameters
- `kodeDivisi` (path) - Kode divisi (required)
- `noInvoice` (path) - Nomor invoice (required)
- `id` (path) - ID invoice detail (required)

#### Request Body
```json
{
  "qty_supply": 15,
  "harga_jual": 95000,
  "harga_nett": 1425000,
  "status": "Updated"
}
```

#### Validation Rules
- `kode_barang`: sometimes, string, max:50, exists dalam master_barang untuk divisi
- `qty_supply`: sometimes, integer, min:1
- `harga_jual`: sometimes, numeric, min:0
- `jenis`: sometimes, nullable, string, max:50
- `diskon1`: sometimes, nullable, numeric, min:0, max:100
- `diskon2`: sometimes, nullable, numeric, min:0, max:100
- `harga_nett`: sometimes, numeric, min:0
- `status`: sometimes, nullable, string, max:50

#### Response Example
```json
{
  "id": 1,
  "kode_divisi": "DIV01",
  "no_invoice": "INV001",
  "kode_barang": "BRG001",
  "qty_supply": 15,
  "harga_jual": 95000.00,
  "jenis": "Regular",
  "diskon1": 5.00,
  "diskon2": 2.00,
  "harga_nett": 1425000.00,
  "status": "Updated",
  "gross_amount": 1425000.00,
  "total_discount_percent": 7.00,
  "discount_amount": 0.00,
  "unit_nett_price": 95000.00,
  "display_name": "BRG001 (Qty: 15)",
  "formatted_gross_amount": "Rp 1.425.000,00",
  "formatted_nett_amount": "Rp 1.425.000,00",
  "status_badge_class": "badge-primary",
  "created_at": "2024-01-15T10:30:00.000Z",
  "updated_at": "2024-01-15T15:45:00.000Z"
}
```

### 5. Delete Invoice Detail
**DELETE** `/api/divisi/{kodeDivisi}/invoices/{noInvoice}/details/{id}`

Menghapus item detail invoice berdasarkan ID.

#### Parameters
- `kodeDivisi` (path) - Kode divisi (required)
- `noInvoice` (path) - Nomor invoice (required)
- `id` (path) - ID invoice detail (required)

#### Response
- **Status**: 204 No Content
- **Body**: Empty

### 6. Invoice Detail Statistics
**GET** `/api/divisi/{kodeDivisi}/invoices/{noInvoice}/details-stats`

Mengambil statistik detail item dalam invoice tertentu.

#### Parameters
- `kodeDivisi` (path) - Kode divisi (required)
- `noInvoice` (path) - Nomor invoice (required)

#### Response Example
```json
{
  "total_items": 5,
  "total_quantity": 50,
  "total_gross_amount": 5000000.00,
  "total_net_amount": 4650000.00,
  "average_unit_price": 100000.00,
  "items_by_status": {
    "Active": 3,
    "Updated": 1,
    "Pending": 1
  },
  "items_by_type": {
    "Regular": 4,
    "Special": 1
  }
}
```

## Error Responses

### Validation Error (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "kode_barang": [
      "Kode barang tidak ditemukan dalam divisi ini."
    ],
    "qty_supply": [
      "Quantity supply minimal 1."
    ]
  }
}
```

### Not Found (404)
```json
{
  "message": "No query results for model [App\\Models\\InvoiceDetail]."
}
```

### Parent Not Found (404)
```json
{
  "message": "No query results for model [App\\Models\\Invoice]."
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

## Key Features

### 1. Nested Resource Architecture
- InvoiceDetail sebagai child resource dari Invoice
- Automatic parent verification untuk setiap operasi
- Route parameter inheritance dari parent

### 2. Advanced Calculations
- Gross amount calculation (qty × harga_jual)
- Discount calculations (diskon1 + diskon2)
- Net amount per unit calculations
- Real-time financial summaries

### 3. Comprehensive Filtering
- Multi-field search (kode_barang, jenis, status)
- Price range filtering (min_harga, max_harga)
- Quantity range filtering (min_qty, max_qty)
- Status-based filtering

### 4. Rich Response Data
- Calculated financial fields
- Formatted currency displays
- Status badge styling classes
- Display names for UI components

### 5. Relationship Loading
- Eager loading invoice, barang, dan divisi relationships
- Optimized queries untuk performance
- Safety checks untuk missing relationships

### 6. Statistics & Analytics
- Item count and quantity summaries
- Financial aggregations (gross, net, average)
- Grouping by status and type
- Real-time calculation updates

## Implementation Notes

### Model Enhancements
- Auto-incrementing ID primary key
- Decimal precision casting untuk financial fields
- Integer casting untuk quantities
- Comprehensive relationship definitions

### Controller Features
- Parent invoice verification pada setiap operation
- Advanced filtering dan search capabilities
- Nested resource parameter handling
- Statistics calculation dengan database aggregations

### Validation Strategy
- Cross-reference validation dengan master_barang
- Percentage validation untuk discount fields
- Minimum value enforcement untuk financial fields
- Scope-aware existence checks

### Security Considerations
- Parent resource verification mencegah unauthorized access
- Divisi-scoped barang validation
- Sanctum authentication pada semua endpoints

## Usage Examples

### Create Invoice Detail with cURL
```bash
curl -X POST "http://localhost/api/divisi/DIV01/invoices/INV001/details" \
  -H "Authorization: Bearer your-api-token" \
  -H "Content-Type: application/json" \
  -d '{
    "kode_barang": "BRG001",
    "qty_supply": 10,
    "harga_jual": 100000,
    "jenis": "Regular",
    "diskon1": 5.0,
    "diskon2": 2.0,
    "harga_nett": 930000,
    "status": "Active"
  }'
```

### Update Invoice Detail Quantity
```bash
curl -X PUT "http://localhost/api/divisi/DIV01/invoices/INV001/details/1" \
  -H "Authorization: Bearer your-api-token" \
  -H "Content-Type: application/json" \
  -d '{
    "qty_supply": 15,
    "harga_nett": 1395000
  }'
```

### Filter by Price Range
```bash
curl -X GET "http://localhost/api/divisi/DIV01/invoices/INV001/details?min_harga=50000&max_harga=150000&per_page=10" \
  -H "Authorization: Bearer your-api-token"
```

### Get Invoice Statistics
```bash
curl -X GET "http://localhost/api/divisi/DIV01/invoices/INV001/details-stats" \
  -H "Authorization: Bearer your-api-token"
```
