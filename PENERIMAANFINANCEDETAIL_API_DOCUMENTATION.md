# PENERIMAAN FINANCE DETAIL API DOCUMENTATION

## Overview
The PenerimaanFinanceDetail API provides comprehensive CRUD operations for managing penerimaan finance detail records. This API follows RESTful principles and implements a nested resource structure under the PenerimaanFinance parent resource.

## Model Structure
- **Table**: `penerimaan_finance_detail`
- **Primary Key**: `id` (auto-incrementing)
- **Parent Resource**: PenerimaanFinance (via `kode_divisi` + `no_penerimaan`)

### Database Schema
```sql
CREATE TABLE penerimaan_finance_detail (
    id INTEGER PRIMARY KEY,
    kode_divisi VARCHAR NOT NULL,
    no_penerimaan VARCHAR NOT NULL,
    no_invoice VARCHAR NOT NULL,
    jumlah_invoice NUMERIC(15,2) NOT NULL,
    sisa_invoice NUMERIC(15,2) NOT NULL,
    jumlah_bayar NUMERIC(15,2) NOT NULL,
    jumlah_dispensasi NUMERIC(15,2) NOT NULL,
    status VARCHAR CHECK (status IN ('Open', 'Finish', 'Cancel'))
);
```

### Model Relationships
- `penerimaanFinance()` - BelongsTo PenerimaanFinance
- `invoice()` - BelongsTo Invoice

## Authentication
All endpoints require Sanctum authentication with valid Bearer token.

```http
Authorization: Bearer {your-token}
```

## Base URL Structure
```
/api/divisi/{kodeDivisi}/penerimaan-finances/{noPenerimaan}/details
```

## API Endpoints

### 1. List All Penerimaan Finance Details

**Endpoint**: `GET /api/divisi/{kodeDivisi}/penerimaan-finances/{noPenerimaan}/details`

**Description**: Retrieve paginated list of all penerimaan finance details for a specific penerimaan finance.

**Parameters**:
- `kodeDivisi` (path) - Division code
- `noPenerimaan` (path) - Penerimaan finance number

**Query Parameters**:
- `per_page` (optional, default: 15) - Items per page
- `page` (optional, default: 1) - Page number
- `sort_by` (optional, default: id) - Sort field
- `sort_direction` (optional, default: asc) - Sort direction (asc/desc)
- `search` (optional) - Search term for no_invoice or status
- `status` (optional) - Filter by status
- `no_invoice` (optional) - Filter by invoice number
- `min_jumlah_invoice` (optional) - Minimum invoice amount filter
- `max_jumlah_invoice` (optional) - Maximum invoice amount filter
- `payment_status` (optional) - Filter by payment status (fully_paid/partially_paid/unpaid)

**Success Response** (200):
```json
{
    "data": [
        {
            "id": 1,
            "kode_divisi": "DIV01",
            "no_penerimaan": "PNF/2024/001",
            "no_invoice": "INV/2024/001",
            "jumlah_invoice": "1000000.00",
            "sisa_invoice": "500000.00",
            "jumlah_bayar": "400000.00",
            "jumlah_dispensasi": "100000.00",
            "status": "Open",
            "total_pembayaran": "500000.00",
            "sisa_tagihan": "500000.00",
            "persentase_pembayaran": 50.00,
            "penerimaan_finance": {
                "no_penerimaan": "PNF/2024/001",
                "tanggal": "2024-01-15"
            },
            "invoice": {
                "no_invoice": "INV/2024/001",
                "tgl_faktur": "2024-01-10",
                "grand_total": "1100000.00",
                "kode_cust": "CUST001"
            }
        }
    ],
    "summary": {
        "total_items": 25,
        "total_jumlah_invoice": "25000000.00",
        "total_jumlah_bayar": "15000000.00",
        "total_jumlah_dispensasi": "2500000.00",
        "total_pembayaran": "17500000.00",
        "total_sisa_tagihan": "7500000.00",
        "average_jumlah_invoice": "1000000.00",
        "average_jumlah_bayar": "600000.00",
        "status_breakdown": {
            "Open": 15,
            "Finish": 8,
            "Cancel": 2
        },
        "invoice_stats": {
            "unique_invoices": 25,
            "fully_paid": 8,
            "partially_paid": 15,
            "unpaid": 2
        }
    },
    "current_page": 1,
    "per_page": 15,
    "total": 25,
    "last_page": 2
}
```

**Error Responses**:
- `404` - Parent penerimaan finance not found
- `401` - Unauthorized

### 2. Create New Penerimaan Finance Detail

**Endpoint**: `POST /api/divisi/{kodeDivisi}/penerimaan-finances/{noPenerimaan}/details`

**Description**: Create a new penerimaan finance detail record.

**Request Body**:
```json
{
    "no_invoice": "INV/2024/002",
    "jumlah_invoice": 1500000,
    "sisa_invoice": 1000000,
    "jumlah_bayar": 400000,
    "jumlah_dispensasi": 100000,
    "status": "Open"
}
```

**Validation Rules**:
- `no_invoice`: required, string, max 15 chars, must exist in invoice table for the division
- `jumlah_invoice`: required, numeric, min 0, max 99999999999999.99
- `sisa_invoice`: required, numeric, min 0, max 99999999999999.99
- `jumlah_bayar`: required, numeric, min 0, max 99999999999999.99
- `jumlah_dispensasi`: required, numeric, min 0, max 99999999999999.99
- `status`: nullable, string, max 20 chars, in ['Open', 'Finish', 'Cancel']

**Success Response** (201):
```json
{
    "message": "Detail penerimaan finance berhasil ditambahkan",
    "data": {
        "id": 2,
        "kode_divisi": "DIV01",
        "no_penerimaan": "PNF/2024/001",
        "no_invoice": "INV/2024/002",
        "jumlah_invoice": "1500000.00",
        "sisa_invoice": "1000000.00",
        "jumlah_bayar": "400000.00",
        "jumlah_dispensasi": "100000.00",
        "status": "Open",
        "total_pembayaran": "500000.00",
        "sisa_tagihan": "1000000.00",
        "persentase_pembayaran": 33.33
    }
}
```

**Error Responses**:
- `422` - Validation errors
- `409` - Duplicate entry (same invoice already exists in this penerimaan finance)
- `404` - Parent penerimaan finance not found

### 3. Show Specific Penerimaan Finance Detail

**Endpoint**: `GET /api/divisi/{kodeDivisi}/penerimaan-finances/{noPenerimaan}/details/{id}`

**Description**: Retrieve a specific penerimaan finance detail by ID.

**Success Response** (200):
```json
{
    "data": {
        "id": 1,
        "kode_divisi": "DIV01",
        "no_penerimaan": "PNF/2024/001",
        "no_invoice": "INV/2024/001",
        "jumlah_invoice": "1000000.00",
        "sisa_invoice": "500000.00",
        "jumlah_bayar": "400000.00",
        "jumlah_dispensasi": "100000.00",
        "status": "Open",
        "total_pembayaran": "500000.00",
        "sisa_tagihan": "500000.00",
        "persentase_pembayaran": 50.00,
        "penerimaan_finance": {
            "no_penerimaan": "PNF/2024/001",
            "tanggal": "2024-01-15"
        },
        "invoice": {
            "no_invoice": "INV/2024/001",
            "tgl_faktur": "2024-01-10",
            "grand_total": "1100000.00",
            "kode_cust": "CUST001"
        }
    }
}
```

**Error Responses**:
- `404` - Detail not found or doesn't belong to specified penerimaan finance

### 4. Update Penerimaan Finance Detail

**Endpoint**: `PUT /api/divisi/{kodeDivisi}/penerimaan-finances/{noPenerimaan}/details/{id}`

**Description**: Update an existing penerimaan finance detail.

**Request Body**:
```json
{
    "jumlah_bayar": 500000,
    "jumlah_dispensasi": 200000,
    "status": "Finish"
}
```

**Validation Rules**: Same as create, but all fields are optional.

**Success Response** (200):
```json
{
    "message": "Detail penerimaan finance berhasil diperbarui",
    "data": {
        "id": 1,
        "kode_divisi": "DIV01",
        "no_penerimaan": "PNF/2024/001",
        "no_invoice": "INV/2024/001",
        "jumlah_invoice": "1000000.00",
        "sisa_invoice": "500000.00",
        "jumlah_bayar": "500000.00",
        "jumlah_dispensasi": "200000.00",
        "status": "Finish",
        "total_pembayaran": "700000.00",
        "sisa_tagihan": "300000.00",
        "persentase_pembayaran": 70.00
    }
}
```

**Error Responses**:
- `422` - Validation errors
- `409` - Duplicate entry conflict
- `404` - Detail not found

### 5. Delete Penerimaan Finance Detail

**Endpoint**: `DELETE /api/divisi/{kodeDivisi}/penerimaan-finances/{noPenerimaan}/details/{id}`

**Description**: Delete a specific penerimaan finance detail.

**Success Response** (200):
```json
{
    "message": "Detail penerimaan finance berhasil dihapus"
}
```

**Error Responses**:
- `404` - Detail not found

### 6. Get Statistics

**Endpoint**: `GET /api/divisi/{kodeDivisi}/penerimaan-finances/{noPenerimaan}/details-stats`

**Description**: Get comprehensive statistics for penerimaan finance details.

**Query Parameters**:
- `status` (optional) - Filter by status
- `no_invoice` (optional) - Filter by invoice number

**Success Response** (200):
```json
{
    "stats": {
        "total_items": 25,
        "total_jumlah_invoice": "25000000.00",
        "total_jumlah_bayar": "15000000.00",
        "total_jumlah_dispensasi": "2500000.00",
        "total_pembayaran": "17500000.00",
        "total_sisa_tagihan": "7500000.00",
        "average_jumlah_invoice": "1000000.00",
        "average_jumlah_bayar": "600000.00",
        "average_payment_percentage": 70.00,
        "status_breakdown": {
            "Open": 15,
            "Finish": 8,
            "Cancel": 2
        },
        "payment_status_breakdown": {
            "fully_paid": 8,
            "partially_paid": 15,
            "unpaid": 2
        },
        "top_invoices_by_amount": [
            {
                "no_invoice": "INV/2024/010",
                "jumlah_invoice": "5000000.00",
                "total_pembayaran": "4500000.00",
                "sisa_tagihan": "500000.00"
            }
        ],
        "invoice_summary": {
            "unique_invoices": 25,
            "max_invoice_amount": "5000000.00",
            "min_invoice_amount": "100000.00",
            "max_payment_amount": "4500000.00",
            "min_payment_amount": "0.00"
        }
    }
}
```

## Response Format

### Success Response Structure
```json
{
    "message": "Success message (for CUD operations)",
    "data": {}, // Single resource
    "data": [], // Multiple resources
    "summary": {}, // Collection summary (for index)
    "stats": {}, // Statistics (for stats endpoint)
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7
}
```

### Error Response Structure
```json
{
    "message": "Error description",
    "errors": {
        "field_name": ["Validation error message"]
    }
}
```

## Business Rules

1. **Invoice Uniqueness**: Each invoice can only appear once within the same penerimaan finance
2. **Parent Verification**: The penerimaan finance must exist and belong to the specified division
3. **Financial Validation**: All monetary amounts must be non-negative decimals
4. **Status Management**: Status transitions should follow business logic
5. **Reference Integrity**: All foreign keys (no_invoice) must reference existing records
6. **Payment Calculations**: Total pembayaran = jumlah_bayar + jumlah_dispensasi

## Calculated Fields

- **total_pembayaran**: jumlah_bayar + jumlah_dispensasi
- **sisa_tagihan**: jumlah_invoice - total_pembayaran
- **persentase_pembayaran**: (total_pembayaran / jumlah_invoice) × 100

## Payment Status Classification

- **Fully Paid**: total_pembayaran >= jumlah_invoice
- **Partially Paid**: 0 < total_pembayaran < jumlah_invoice
- **Unpaid**: total_pembayaran = 0

## Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `404` - Not Found
- `409` - Conflict (duplicate entry)
- `422` - Unprocessable Entity (validation errors)
- `500` - Internal Server Error

## Performance Features

- **Pagination**: All list endpoints support pagination
- **Filtering**: Multiple filter options available including payment status
- **Sorting**: Flexible sorting by any allowed field
- **Search**: Text search across invoice numbers and status
- **Eager Loading**: Relationships loaded efficiently
- **Caching**: Database schema and relationships cached

## Security Features

- **Authentication**: Sanctum bearer token required
- **Scoped Access**: All operations scoped to division and penerimaan finance
- **Validation**: Comprehensive input validation with business rules
- **SQL Injection Protection**: Eloquent ORM prevents SQL injection
- **Mass Assignment Protection**: Only fillable fields can be mass assigned

## Financial Analysis Features

- **Payment Tracking**: Comprehensive payment status classification
- **Invoice Analytics**: Top invoices, payment distribution, completion rates
- **Statistical Reporting**: Averages, totals, breakdowns by various criteria
- **Performance Metrics**: Payment percentages, completion rates, outstanding amounts

## Testing

The API has been tested with comprehensive manual tests covering:
- Model loading and configuration
- Controller functionality with all CRUD methods
- Request validation classes with proper rules
- Resource transformation with calculated fields
- Database schema compatibility
- Model relationships with proper foreign key handling
- Route parameter handling for nested resources
- Validation rules with business logic enforcement

All tests pass successfully, confirming the API is ready for production use.
