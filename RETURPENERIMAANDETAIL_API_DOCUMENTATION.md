# RETUR PENERIMAAN DETAIL API DOCUMENTATION

## Overview
The ReturPenerimaanDetail API provides comprehensive CRUD operations for managing retur penerimaan detail records. This API follows RESTful principles and implements a nested resource structure under the ReturPenerimaan parent resource.

## Model Structure
- **Table**: `retur_penerimaan_detail`
- **Primary Key**: `id` (auto-incrementing)
- **Parent Resource**: ReturPenerimaan (via `kode_divisi` + `no_retur`)

### Database Schema
```sql
CREATE TABLE retur_penerimaan_detail (
    id INTEGER PRIMARY KEY,
    kode_divisi VARCHAR NOT NULL,
    no_retur VARCHAR NOT NULL,
    no_penerimaan VARCHAR NOT NULL,
    kode_barang VARCHAR NOT NULL,
    qty_retur INTEGER NOT NULL,
    harga_nett NUMERIC(15,2) NOT NULL,
    status VARCHAR CHECK (status IN ('Open', 'Closed', 'Cancelled'))
);
```

### Model Relationships
- `returPenerimaan()` - BelongsTo ReturPenerimaan
- `barang()` - BelongsTo Barang  
- `partPenerimaan()` - BelongsTo PartPenerimaan

## Authentication
All endpoints require Sanctum authentication with valid Bearer token.

```http
Authorization: Bearer {your-token}
```

## Base URL Structure
```
/api/divisi/{kodeDivisi}/retur-penerimaan/{noRetur}/details
```

## API Endpoints

### 1. List All Retur Penerimaan Details

**Endpoint**: `GET /api/divisi/{kodeDivisi}/retur-penerimaan/{noRetur}/details`

**Description**: Retrieve paginated list of all retur penerimaan details for a specific retur penerimaan.

**Parameters**:
- `kodeDivisi` (path) - Division code
- `noRetur` (path) - Retur penerimaan number

**Query Parameters**:
- `per_page` (optional, default: 15) - Items per page
- `page` (optional, default: 1) - Page number
- `sort_by` (optional, default: id) - Sort field
- `sort_direction` (optional, default: asc) - Sort direction (asc/desc)
- `search` (optional) - Search term for kode_barang or no_penerimaan
- `status` (optional) - Filter by status
- `kode_barang` (optional) - Filter by barang code
- `no_penerimaan` (optional) - Filter by penerimaan number

**Success Response** (200):
```json
{
    "data": [
        {
            "id": 1,
            "kode_divisi": "DIV01",
            "no_retur": "RTP/2024/001",
            "no_penerimaan": "PEN/2024/001",
            "kode_barang": "BRG001",
            "qty_retur": 5,
            "harga_nett": "10000.00",
            "status": "Open",
            "total_amount": "50000.00",
            "retur_penerimaan": {
                "no_retur": "RTP/2024/001",
                "tanggal": "2024-01-15"
            },
            "barang": {
                "kode_barang": "BRG001",
                "nama_barang": "Product A"
            },
            "part_penerimaan": {
                "no_penerimaan": "PEN/2024/001",
                "tanggal": "2024-01-10"
            }
        }
    ],
    "summary": {
        "total_items": 25,
        "total_qty": 150,
        "total_amount": "750000.00",
        "average_price": "5000.00",
        "status_breakdown": {
            "Open": 15,
            "Closed": 8,
            "Cancelled": 2
        }
    },
    "current_page": 1,
    "per_page": 15,
    "total": 25,
    "last_page": 2
}
```

**Error Responses**:
- `404` - Parent retur penerimaan not found
- `401` - Unauthorized

### 2. Create New Retur Penerimaan Detail

**Endpoint**: `POST /api/divisi/{kodeDivisi}/retur-penerimaan/{noRetur}/details`

**Description**: Create a new retur penerimaan detail record.

**Request Body**:
```json
{
    "no_penerimaan": "PEN/2024/002",
    "kode_barang": "BRG002",
    "qty_retur": 10,
    "harga_nett": 15000,
    "status": "Open"
}
```

**Validation Rules**:
- `no_penerimaan`: required, string, max 50 chars, must exist in part_penerimaan table
- `kode_barang`: required, string, max 20 chars, must exist in barang table
- `qty_retur`: required, integer, min 1
- `harga_nett`: required, numeric, min 0, max 99999999999999.99
- `status`: required, in ['Open', 'Closed', 'Cancelled']

**Success Response** (201):
```json
{
    "message": "Detail retur penerimaan berhasil ditambahkan",
    "data": {
        "id": 2,
        "kode_divisi": "DIV01",
        "no_retur": "RTP/2024/001",
        "no_penerimaan": "PEN/2024/002",
        "kode_barang": "BRG002",
        "qty_retur": 10,
        "harga_nett": "15000.00",
        "status": "Open",
        "total_amount": "150000.00"
    }
}
```

**Error Responses**:
- `422` - Validation errors
- `409` - Duplicate entry (same no_penerimaan + kode_barang combination exists)
- `404` - Parent retur penerimaan not found

### 3. Show Specific Retur Penerimaan Detail

**Endpoint**: `GET /api/divisi/{kodeDivisi}/retur-penerimaan/{noRetur}/details/{id}`

**Description**: Retrieve a specific retur penerimaan detail by ID.

**Success Response** (200):
```json
{
    "data": {
        "id": 1,
        "kode_divisi": "DIV01",
        "no_retur": "RTP/2024/001",
        "no_penerimaan": "PEN/2024/001",
        "kode_barang": "BRG001",
        "qty_retur": 5,
        "harga_nett": "10000.00",
        "status": "Open",
        "total_amount": "50000.00",
        "retur_penerimaan": {
            "no_retur": "RTP/2024/001",
            "tanggal": "2024-01-15"
        },
        "barang": {
            "kode_barang": "BRG001",
            "nama_barang": "Product A"
        },
        "part_penerimaan": {
            "no_penerimaan": "PEN/2024/001",
            "tanggal": "2024-01-10"
        }
    }
}
```

**Error Responses**:
- `404` - Detail not found or doesn't belong to specified retur penerimaan

### 4. Update Retur Penerimaan Detail

**Endpoint**: `PUT /api/divisi/{kodeDivisi}/retur-penerimaan/{noRetur}/details/{id}`

**Description**: Update an existing retur penerimaan detail.

**Request Body**:
```json
{
    "no_penerimaan": "PEN/2024/002",
    "kode_barang": "BRG002",
    "qty_retur": 8,
    "harga_nett": 12000,
    "status": "Closed"
}
```

**Validation Rules**: Same as create, but all fields are optional.

**Success Response** (200):
```json
{
    "message": "Detail retur penerimaan berhasil diperbarui",
    "data": {
        "id": 1,
        "kode_divisi": "DIV01",
        "no_retur": "RTP/2024/001",
        "no_penerimaan": "PEN/2024/002",
        "kode_barang": "BRG002",
        "qty_retur": 8,
        "harga_nett": "12000.00",
        "status": "Closed",
        "total_amount": "96000.00"
    }
}
```

**Error Responses**:
- `422` - Validation errors
- `409` - Duplicate entry conflict
- `404` - Detail not found

### 5. Delete Retur Penerimaan Detail

**Endpoint**: `DELETE /api/divisi/{kodeDivisi}/retur-penerimaan/{noRetur}/details/{id}`

**Description**: Delete a specific retur penerimaan detail.

**Success Response** (200):
```json
{
    "message": "Detail retur penerimaan berhasil dihapus"
}
```

**Error Responses**:
- `404` - Detail not found

### 6. Get Statistics

**Endpoint**: `GET /api/divisi/{kodeDivisi}/retur-penerimaan/{noRetur}/details-stats`

**Description**: Get comprehensive statistics for retur penerimaan details.

**Query Parameters**:
- `status` (optional) - Filter by status
- `kode_barang` (optional) - Filter by barang code
- `no_penerimaan` (optional) - Filter by penerimaan number

**Success Response** (200):
```json
{
    "stats": {
        "total_items": 25,
        "total_qty": 150,
        "total_amount": "750000.00",
        "average_price": "5000.00",
        "average_qty": 6,
        "status_breakdown": {
            "Open": 15,
            "Closed": 8,
            "Cancelled": 2
        },
        "top_barang": [
            {
                "kode_barang": "BRG001",
                "total_qty": 50,
                "total_amount": "250000.00"
            }
        ],
        "top_penerimaan": [
            {
                "no_penerimaan": "PEN/2024/001",
                "total_qty": 30,
                "total_amount": "150000.00"
            }
        ]
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

1. **Uniqueness**: Each combination of `no_penerimaan` + `kode_barang` must be unique within the same retur penerimaan
2. **Parent Verification**: The retur penerimaan must exist and belong to the specified division
3. **Quantity Validation**: qty_retur must be positive integer
4. **Price Validation**: harga_nett must be non-negative decimal
5. **Status Management**: Status transitions should follow business logic
6. **Reference Integrity**: All foreign keys (kode_barang, no_penerimaan) must reference existing records

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
- **Filtering**: Multiple filter options available
- **Sorting**: Flexible sorting by any field
- **Search**: Text search across relevant fields
- **Eager Loading**: Relationships loaded efficiently
- **Caching**: Database schema and relationships cached

## Security Features

- **Authentication**: Sanctum bearer token required
- **Scoped Access**: All operations scoped to division and retur penerimaan
- **Validation**: Comprehensive input validation
- **SQL Injection Protection**: Eloquent ORM prevents SQL injection
- **Mass Assignment Protection**: Only fillable fields can be mass assigned

## Testing

The API has been tested with comprehensive manual tests covering:
- Model loading and relationships
- Controller functionality
- Request validation
- Resource transformation
- Database schema compatibility
- Route parameter handling

All tests pass successfully, confirming the API is ready for production use.
