# API Documentation: DBarang (Barang Detail) Management

## Overview
The DBarang API provides comprehensive CRUD operations for managing product detail records within divisions. It handles inventory details including entry dates, cost prices (modal), stock quantities, and stock movement tracking for specific products.

## Authentication
All endpoints require Sanctum authentication. Include the bearer token in the Authorization header.

```
Authorization: Bearer {your-sanctum-token}
```

## Base URL
```
/api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details
```

## Endpoints

### 1. List Barang Details

**GET** `/api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details`

Returns list of product detail records for a specific division and product.

#### Parameters
- `kodeDivisi` (string, required): Division code in URL path
- `kodeBarang` (string, required): Product code in URL path
- `available_only` (boolean, optional): Filter only items with stock > 0
- `modal_min` (decimal, optional): Minimum cost price filter
- `modal_max` (decimal, optional): Maximum cost price filter
- `stok_min` (integer, optional): Minimum stock filter
- `stok_max` (integer, optional): Maximum stock filter
- `tgl_masuk_from` (date, optional): Entry date from filter
- `tgl_masuk_to` (date, optional): Entry date to filter
- `sort_by` (string, optional): Sort field (id, tgl_masuk, modal, stok, created_at)
- `sort_order` (string, optional): Sort order (asc, desc)

#### Response
```json
{
  "success": true,
  "message": "Data detail barang berhasil diambil.",
  "data": {
    "data": [
      {
        "id": 1,
        "kode_divisi": "DIV01",
        "kode_barang": "BRG001",
        "tgl_masuk": "2024-01-15 10:30:00",
        "modal": "125000.75",
        "stok": 50,
        "created_at": "2024-01-15 10:30:00",
        "updated_at": "2024-01-15 10:30:00",
        "divisi": {
          "kode_divisi": "DIV01",
          "nama_divisi": "Division 1"
        },
        "barang": {
          "kode_divisi": "DIV01",
          "kode_barang": "BRG001",
          "nama_barang": "Product 1",
          "satuan": "PCS",
          "kategori": "Electronics"
        },
        "stock_info": {
          "is_available": true,
          "stock_status": "Tersedia",
          "total_value": "6250037.50",
          "total_value_formatted": "Rp 6.250.037,50",
          "modal_formatted": "Rp 125.000,75"
        }
      }
    ],
    "meta": {
      "total_items": 1,
      "stock_summary": {
        "total_stock": 50,
        "available_items": 1,
        "empty_items": 0,
        "low_stock_items": 0,
        "average_stock": 50,
        "max_stock": 50,
        "min_stock": 50
      },
      "value_summary": {
        "total_inventory_value": "6250037.50",
        "total_inventory_value_formatted": "Rp 6.250.037,50",
        "average_modal": "125000.75",
        "average_modal_formatted": "Rp 125.000,75",
        "highest_modal": "125000.75",
        "highest_modal_formatted": "Rp 125.000,75",
        "lowest_modal": "125000.75",
        "lowest_modal_formatted": "Rp 125.000,75"
      },
      "modal_distribution": {
        "low_price_items": 0,
        "mid_price_items": 1,
        "high_price_items": 0,
        "no_price_items": 0,
        "total_items": 1
      },
      "product_distribution": {
        "unique_products": 1,
        "items_per_product": {
          "BRG001": 1
        },
        "stock_per_product": {
          "BRG001": 50
        },
        "average_items_per_product": 1
      }
    }
  },
  "division_info": {
    "kode_divisi": "DIV01",
    "nama_divisi": "Division 1"
  },
  "product_info": {
    "kode_barang": "BRG001",
    "nama_barang": "Product 1",
    "satuan": "PCS",
    "kategori": "Electronics"
  }
}
```

### 2. Create Barang Detail

**POST** `/api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details`

Creates a new product detail record for the specified division and product.

#### Request Body
```json
{
  "tgl_masuk": "2024-01-15",
  "modal": 125000.75,
  "stok": 50
}
```

#### Validation Rules
- `kode_divisi`: required, string, max:5 (auto-filled from route)
- `kode_barang`: required, string, max:30 (auto-filled from route)
- `tgl_masuk`: nullable, date (defaults to current datetime if not provided)
- `modal`: nullable, numeric, min:0, max:99999999999999.99
- `stok`: nullable, integer, min:0

#### Response (201 Created)
```json
{
  "success": true,
  "message": "Detail barang berhasil dibuat.",
  "data": {
    "id": 1,
    "kode_divisi": "DIV01",
    "kode_barang": "BRG001",
    "tgl_masuk": "2024-01-15 10:30:00",
    "modal": "125000.75",
    "stok": 50,
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00",
    "divisi": {
      "kode_divisi": "DIV01",
      "nama_divisi": "Division 1"
    },
    "barang": {
      "kode_divisi": "DIV01",
      "kode_barang": "BRG001",
      "nama_barang": "Product 1",
      "satuan": "PCS",
      "kategori": "Electronics"
    },
    "stock_info": {
      "is_available": true,
      "stock_status": "Tersedia",
      "total_value": "6250037.50",
      "total_value_formatted": "Rp 6.250.037,50",
      "modal_formatted": "Rp 125.000,75"
    }
  }
}
```

### 3. Show Barang Detail

**GET** `/api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details/{id}`

Retrieve specific product detail record.

#### Parameters
- `kodeDivisi` (string, required): Division code
- `kodeBarang` (string, required): Product code
- `id` (integer, required): Detail record ID

#### Response (200 OK)
```json
{
  "success": true,
  "message": "Detail barang berhasil diambil.",
  "data": {
    "id": 1,
    "kode_divisi": "DIV01",
    "kode_barang": "BRG001",
    "tgl_masuk": "2024-01-15 10:30:00",
    "modal": "125000.75",
    "stok": 50,
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00",
    "divisi": {
      "kode_divisi": "DIV01",
      "nama_divisi": "Division 1"
    },
    "barang": {
      "kode_divisi": "DIV01",
      "kode_barang": "BRG001",
      "nama_barang": "Product 1",
      "satuan": "PCS",
      "kategori": "Electronics"
    },
    "stock_info": {
      "is_available": true,
      "stock_status": "Tersedia",
      "total_value": "6250037.50",
      "total_value_formatted": "Rp 6.250.037,50",
      "modal_formatted": "Rp 125.000,75"
    },
    "stock_movements_count": 5
  }
}
```

### 4. Update Barang Detail

**PUT** `/api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details/{id}`

Update product detail information.

#### Parameters
- `kodeDivisi` (string, required): Division code
- `kodeBarang` (string, required): Product code
- `id` (integer, required): Detail record ID

#### Request Body
```json
{
  "modal": 135000.00,
  "stok": 45,
  "tgl_masuk": "2024-01-15"
}
```

#### Validation Rules
- `kode_divisi`: sometimes, required, string, max:5
- `kode_barang`: sometimes, required, string, max:30
- `tgl_masuk`: nullable, date
- `modal`: nullable, numeric, min:0, max:99999999999999.99
- `stok`: nullable, integer, min:0

#### Response (200 OK)
```json
{
  "success": true,
  "message": "Detail barang berhasil diperbarui.",
  "data": {
    "id": 1,
    "kode_divisi": "DIV01",
    "kode_barang": "BRG001",
    "tgl_masuk": "2024-01-15 10:30:00",
    "modal": "135000.00",
    "stok": 45,
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 11:00:00",
    "divisi": {
      "kode_divisi": "DIV01",
      "nama_divisi": "Division 1"
    },
    "barang": {
      "kode_divisi": "DIV01",
      "kode_barang": "BRG001",
      "nama_barang": "Product 1",
      "satuan": "PCS",
      "kategori": "Electronics"
    },
    "stock_info": {
      "is_available": true,
      "stock_status": "Tersedia",
      "total_value": "6075000.00",
      "total_value_formatted": "Rp 6.075.000,00",
      "modal_formatted": "Rp 135.000,00"
    }
  }
}
```

### 5. Delete Barang Detail

**DELETE** `/api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details/{id}`

Delete a product detail record. Only allowed if no stock movements exist.

#### Parameters
- `kodeDivisi` (string, required): Division code
- `kodeBarang` (string, required): Product code
- `id` (integer, required): Detail record ID

#### Response (200 OK)
```json
{
  "success": true,
  "message": "Detail barang berhasil dihapus."
}
```

#### Response (422 Unprocessable Entity) - If has stock movements
```json
{
  "success": false,
  "message": "Detail barang tidak dapat dihapus karena memiliki riwayat pergerakan stok."
}
```

### 6. Barang Detail Statistics

**GET** `/api/divisi/{kodeDivisi}/barangs/{kodeBarang}/details-statistics`

Get comprehensive statistics for product details within a division and product.

#### Parameters
- `kodeDivisi` (string, required): Division code
- `kodeBarang` (string, required): Product code

#### Response (200 OK)
```json
{
  "success": true,
  "message": "Statistik detail barang berhasil diambil.",
  "data": {
    "total_entries": 3,
    "total_stock": 150,
    "total_inventory_value": 18750000.00,
    "average_modal": 125000.00,
    "highest_modal": 150000.00,
    "lowest_modal": 100000.00,
    "available_entries": 2,
    "empty_entries": 1,
    "stock_distribution": {
      "high_stock": 1,
      "medium_stock": 1,
      "low_stock": 0,
      "empty_stock": 1
    },
    "value_distribution": {
      "high_value": 0,
      "medium_value": 3,
      "low_value": 0,
      "no_value": 0
    },
    "formatted_totals": {
      "total_inventory_value": "Rp 18.750.000,00",
      "average_modal": "Rp 125.000,00"
    }
  },
  "division_info": {
    "kode_divisi": "DIV01",
    "nama_divisi": "Division 1"
  },
  "product_info": {
    "kode_barang": "BRG001",
    "nama_barang": "Product 1",
    "satuan": "PCS",
    "kategori": "Electronics"
  }
}
```

## Error Responses

### 400 Bad Request
```json
{
  "success": false,
  "message": "Invalid request parameters."
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Divisi tidak ditemukan."
}
```

or

```json
{
  "success": false,
  "message": "Barang tidak ditemukan."
}
```

or

```json
{
  "success": false,
  "message": "Detail barang tidak ditemukan."
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "modal": [
      "Modal tidak boleh negatif."
    ],
    "stok": [
      "Stok tidak boleh negatif."
    ],
    "tgl_masuk": [
      "Tanggal masuk harus berupa tanggal yang valid."
    ]
  }
}
```

## Database Schema

### Table: d_barang (DBarang Model)
```sql
CREATE TABLE d_barang (
    id SERIAL PRIMARY KEY,
    kode_divisi VARCHAR(5) NOT NULL,
    kode_barang VARCHAR(30) NOT NULL,
    tgl_masuk TIMESTAMPTZ,
    modal DECIMAL(15,2),
    stok INTEGER,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    FOREIGN KEY (kode_divisi, kode_barang) REFERENCES m_barang(kode_divisi, kode_barang)
);
```

## Business Rules

1. **Auto-incrementing Primary Key**: Each detail record has a unique auto-incrementing ID
2. **Division Validation**: Division must exist before creating product details
3. **Product Validation**: Product must exist in the specified division
4. **Stock Management**: Stock quantities cannot be negative
5. **Cost Price Management**: Modal prices cannot be negative
6. **Entry Date Default**: If not provided, entry date defaults to current timestamp
7. **Deletion Protection**: Details with stock movement history cannot be deleted
8. **Stock Status Calculation**: Automatic categorization based on stock levels
9. **Audit Trail**: All changes are tracked with timestamps

## Stock Status Categories

- **Habis**: Stock = 0
- **Rendah**: Stock 1-4
- **Sedang**: Stock 5-19
- **Tersedia**: Stock ≥ 20

## Value Categories

- **Low Value**: Modal ≤ Rp 99,999
- **Medium Value**: Modal Rp 100,000 - Rp 999,999
- **High Value**: Modal > Rp 1,000,000

## Usage Examples

### Creating a new product detail
```bash
curl -X POST "http://localhost:8000/api/divisi/DIV01/barangs/BRG001/details" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "tgl_masuk": "2024-01-15",
    "modal": 125000.75,
    "stok": 50
  }'
```

### Getting product details with filters
```bash
curl -X GET "http://localhost:8000/api/divisi/DIV01/barangs/BRG001/details?available_only=true&modal_min=100000&sort_by=modal&sort_order=desc" \
  -H "Authorization: Bearer your-token"
```

### Updating a product detail
```bash
curl -X PUT "http://localhost:8000/api/divisi/DIV01/barangs/BRG001/details/1" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "modal": 135000.00,
    "stok": 45
  }'
```

## Related APIs
- **Divisi API**: For division management
- **Barang API**: For product master data management
- **KartuStok API**: For stock movement tracking
- **Invoice API**: For sales transactions affecting stock

---

*This documentation covers the DBarang API for comprehensive product detail inventory management within the ERP system.*
