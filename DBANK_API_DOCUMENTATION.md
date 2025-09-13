# API Documentation: DBank (DetailBank) Management

## Overview
The DBank API provides comprehensive CRUD operations for managing bank account details within divisions. It handles bank account information including account numbers, bank codes, account holders, balances, and status tracking.

## Authentication
All endpoints require Sanctum authentication. Include the bearer token in the Authorization header.

```
Authorization: Bearer {your-sanctum-token}
```

## Base URL
```
/api/divisi/{kodeDivisi}/bank-accounts
```

## Endpoints

### 1. List Bank Accounts

**GET** `/api/divisi/{kodeDivisi}/bank-accounts`

Returns paginated list of bank accounts for a specific division.

#### Parameters
- `kodeDivisi` (string, required): Division code in URL path
- `kode_bank` (string, optional): Filter by bank code
- `status` (string, optional): Filter by account status
- `saldo_min` (decimal, optional): Minimum balance filter
- `saldo_max` (decimal, optional): Maximum balance filter
- `search` (string, optional): Search in account number or account holder name
- `sort_by` (string, optional): Sort field (no_rekening, atas_nama, saldo, status, created_at)
- `sort_order` (string, optional): Sort order (asc, desc)

#### Response
```json
{
  "success": true,
  "message": "Data rekening bank berhasil diambil.",
  "data": {
    "data": [
      {
        "kode_divisi": "DIV01",
        "no_rekening": "123456789",
        "kode_bank": "BNI",
        "atas_nama": "PT Test Company",
        "saldo": "1000000.50",
        "status": "AKTIF",
        "created_at": "2024-01-15 10:30:00",
        "updated_at": "2024-01-15 10:30:00",
        "divisi": {
          "kode_divisi": "DIV01",
          "nama_divisi": "Division 1"
        },
        "bank": {
          "kode_bank": "BNI",
          "nama_bank": "Bank Negara Indonesia"
        },
        "saldo_info": {
          "formatted": "Rp 1.000.000,50",
          "is_positive": true,
          "is_zero": false,
          "status_text": "Aktif"
        }
      }
    ],
    "meta": {
      "total_accounts": 1,
      "saldo_summary": {
        "total_saldo": "1000000.50",
        "total_saldo_formatted": "Rp 1.000.000,50",
        "active_saldo": "1000000.50",
        "active_saldo_formatted": "Rp 1.000.000,50",
        "zero_accounts": 0,
        "negative_saldo": "0.00",
        "negative_saldo_formatted": "Rp 0,00",
        "average_saldo": "1000000.50"
      },
      "status_breakdown": {
        "by_status_field": {
          "AKTIF": 1
        },
        "by_saldo_value": {
          "active": 1,
          "zero": 0,
          "negative": 0
        },
        "total_accounts": 1
      },
      "bank_distribution": {
        "account_count_by_bank": {
          "BNI": 1
        },
        "saldo_by_bank": {
          "BNI": "1000000.50"
        },
        "total_banks": 1
      }
    }
  },
  "division_info": {
    "kode_divisi": "DIV01",
    "nama_divisi": "Division 1"
  }
}
```

### 2. Create Bank Account

**POST** `/api/divisi/{kodeDivisi}/bank-accounts`

Creates a new bank account for the specified division.

#### Request Body
```json
{
  "no_rekening": "123456789",
  "kode_bank": "BNI",
  "atas_nama": "PT Test Company",
  "saldo": 1000000.50,
  "status": "AKTIF"
}
```

#### Validation Rules
- `no_rekening`: required, string, max:50, unique within division
- `kode_bank`: nullable, string, max:5, must exist in m_bank table
- `atas_nama`: required, string, max:50
- `saldo`: nullable, numeric, min:0, max:99999999999999.99
- `status`: nullable, string, max:50

#### Response (201 Created)
```json
{
  "success": true,
  "message": "Rekening bank berhasil dibuat.",
  "data": {
    "kode_divisi": "DIV01",
    "no_rekening": "123456789",
    "kode_bank": "BNI",
    "atas_nama": "PT Test Company",
    "saldo": "1000000.50",
    "status": "AKTIF",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00",
    "divisi": {
      "kode_divisi": "DIV01",
      "nama_divisi": "Division 1"
    },
    "bank": {
      "kode_bank": "BNI",
      "nama_bank": "Bank Negara Indonesia"
    },
    "saldo_info": {
      "formatted": "Rp 1.000.000,50",
      "is_positive": true,
      "is_zero": false,
      "status_text": "Aktif"
    }
  }
}
```

### 3. Show Bank Account

**GET** `/api/divisi/{kodeDivisi}/bank-accounts/{noRekening}`

Retrieve specific bank account details.

#### Parameters
- `kodeDivisi` (string, required): Division code
- `noRekening` (string, required): Account number

#### Response (200 OK)
```json
{
  "success": true,
  "message": "Detail rekening bank berhasil diambil.",
  "data": {
    "kode_divisi": "DIV01",
    "no_rekening": "123456789",
    "kode_bank": "BNI",
    "atas_nama": "PT Test Company",
    "saldo": "1000000.50",
    "status": "AKTIF",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00",
    "divisi": {
      "kode_divisi": "DIV01",
      "nama_divisi": "Division 1"
    },
    "bank": {
      "kode_bank": "BNI",
      "nama_bank": "Bank Negara Indonesia"
    },
    "saldo_info": {
      "formatted": "Rp 1.000.000,50",
      "is_positive": true,
      "is_zero": false,
      "status_text": "Aktif"
    }
  }
}
```

### 4. Update Bank Account

**PUT** `/api/divisi/{kodeDivisi}/bank-accounts/{noRekening}`

Update bank account information.

#### Parameters
- `kodeDivisi` (string, required): Division code
- `noRekening` (string, required): Account number

#### Request Body
```json
{
  "no_rekening": "123456789",
  "kode_bank": "BNI",
  "atas_nama": "PT Test Company Updated",
  "saldo": 1500000.75,
  "status": "AKTIF"
}
```

#### Validation Rules
- `no_rekening`: sometimes, required, string, max:50, unique within division (excluding current)
- `kode_bank`: nullable, string, max:5
- `atas_nama`: sometimes, required, string, max:50
- `saldo`: nullable, numeric, min:0, max:99999999999999.99
- `status`: nullable, string, max:50

#### Response (200 OK)
```json
{
  "success": true,
  "message": "Rekening bank berhasil diperbarui.",
  "data": {
    "kode_divisi": "DIV01",
    "no_rekening": "123456789",
    "kode_bank": "BNI",
    "atas_nama": "PT Test Company Updated",
    "saldo": "1500000.75",
    "status": "AKTIF",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 11:00:00",
    "divisi": {
      "kode_divisi": "DIV01",
      "nama_divisi": "Division 1"
    },
    "bank": {
      "kode_bank": "BNI",
      "nama_bank": "Bank Negara Indonesia"
    },
    "saldo_info": {
      "formatted": "Rp 1.500.000,75",
      "is_positive": true,
      "is_zero": false,
      "status_text": "Aktif"
    }
  }
}
```

### 5. Delete Bank Account

**DELETE** `/api/divisi/{kodeDivisi}/bank-accounts/{noRekening}`

Delete a bank account. Only allowed if no related transactions exist.

#### Parameters
- `kodeDivisi` (string, required): Division code
- `noRekening` (string, required): Account number

#### Response (200 OK)
```json
{
  "success": true,
  "message": "Rekening bank berhasil dihapus."
}
```

#### Response (422 Unprocessable Entity) - If has transactions
```json
{
  "success": false,
  "message": "Rekening bank tidak dapat dihapus karena memiliki riwayat transaksi."
}
```

### 6. Bank Account Statistics

**GET** `/api/divisi/{kodeDivisi}/bank-accounts-statistics`

Get comprehensive statistics for bank accounts within a division.

#### Parameters
- `kodeDivisi` (string, required): Division code

#### Response (200 OK)
```json
{
  "success": true,
  "message": "Statistik rekening bank berhasil diambil.",
  "data": {
    "total_accounts": 5,
    "total_saldo": 25000000.75,
    "average_saldo": 5000000.15,
    "max_saldo": 10000000.00,
    "min_saldo": 500000.00,
    "accounts_by_status": {
      "AKTIF": 4,
      "NONAKTIF": 1
    },
    "accounts_by_bank": {
      "BNI": 2,
      "BCA": 2,
      "MANDIRI": 1
    },
    "saldo_distribution": {
      "positive": 4,
      "zero": 0,
      "negative": 1
    },
    "formatted_totals": {
      "total_saldo": "Rp 25.000.000,75",
      "average_saldo": "Rp 5.000.000,15"
    }
  },
  "division_info": {
    "kode_divisi": "DIV01",
    "nama_divisi": "Division 1"
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
  "message": "Rekening bank tidak ditemukan."
}
```

### 422 Validation Error
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "no_rekening": [
      "Nomor rekening wajib diisi."
    ],
    "atas_nama": [
      "Atas nama wajib diisi."
    ],
    "saldo": [
      "Saldo tidak boleh negatif."
    ]
  }
}
```

## Database Schema

### Table: d_bank (DetailBank Model)
```sql
CREATE TABLE d_bank (
    kode_divisi VARCHAR(5) NOT NULL,
    no_rekening VARCHAR(50) NOT NULL,
    kode_bank VARCHAR(5),
    atas_nama VARCHAR(50) NOT NULL,
    saldo DECIMAL(15,2) DEFAULT 0,
    status VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    PRIMARY KEY (kode_divisi, no_rekening),
    FOREIGN KEY (kode_divisi) REFERENCES m_divisi(kode_divisi),
    FOREIGN KEY (kode_bank) REFERENCES m_bank(kode_bank)
);
```

## Business Rules

1. **Composite Primary Key**: Each bank account is uniquely identified by the combination of division code and account number
2. **Division Validation**: Division must exist before creating bank accounts
3. **Bank Validation**: Bank code must exist in the bank master table (if provided)
4. **Account Number Uniqueness**: Account numbers must be unique within each division
5. **Balance Constraints**: Balance cannot be negative
6. **Deletion Protection**: Accounts with transaction history cannot be deleted
7. **Status Management**: Account status affects availability for transactions
8. **Audit Trail**: All changes are tracked with timestamps

## Usage Examples

### Creating a new bank account
```bash
curl -X POST "http://localhost:8000/api/divisi/DIV01/bank-accounts" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "no_rekening": "123456789",
    "kode_bank": "BNI",
    "atas_nama": "PT Test Company",
    "saldo": 1000000.50,
    "status": "AKTIF"
  }'
```

### Getting bank accounts with filters
```bash
curl -X GET "http://localhost:8000/api/divisi/DIV01/bank-accounts?kode_bank=BNI&saldo_min=500000&sort_by=saldo&sort_order=desc" \
  -H "Authorization: Bearer your-token"
```

### Updating a bank account
```bash
curl -X PUT "http://localhost:8000/api/divisi/DIV01/bank-accounts/123456789" \
  -H "Authorization: Bearer your-token" \
  -H "Content-Type: application/json" \
  -d '{
    "atas_nama": "PT Test Company Updated",
    "saldo": 1500000.75
  }'
```

## Related APIs
- **Divisi API**: For division management
- **Bank API**: For bank master data management  
- **SaldoBank API**: For bank balance transaction history
- **User API**: For user management within divisions

---

*This documentation covers the DBank (DetailBank) API for comprehensive bank account management within the ERP system.*
