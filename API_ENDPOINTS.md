# API Endpoints Summary

## Base URL
`/api`

## Authentication
User authentication required: `GET /api/user` (middleware: auth:sanctum)

## Core Resources

### 1. Divisi Management
```
GET    /api/divisi              # List all divisi
POST   /api/divisi              # Create new divisi  
GET    /api/divisi/{kodeDivisi} # Show specific divisi
PUT    /api/divisi/{kodeDivisi} # Update divisi
DELETE /api/divisi/{kodeDivisi} # Delete divisi
```

### 2. Bank Management (Scoped by Divisi)
```
GET    /api/divisi/{kodeDivisi}/banks              # List banks
POST   /api/divisi/{kodeDivisi}/banks              # Create bank
GET    /api/divisi/{kodeDivisi}/banks/{kodeBank}   # Show bank
PUT    /api/divisi/{kodeDivisi}/banks/{kodeBank}   # Update bank
DELETE /api/divisi/{kodeDivisi}/banks/{kodeBank}   # Delete bank
```

### 3. Area Management (Scoped by Divisi)
```
GET    /api/divisi/{kodeDivisi}/areas              # List areas
POST   /api/divisi/{kodeDivisi}/areas              # Create area
GET    /api/divisi/{kodeDivisi}/areas/{kodeArea}   # Show area
PUT    /api/divisi/{kodeDivisi}/areas/{kodeArea}   # Update area
DELETE /api/divisi/{kodeDivisi}/areas/{kodeArea}   # Delete area
```

### 4. Sales Management (Scoped by Divisi)
```
GET    /api/divisi/{kodeDivisi}/sales              # List sales
POST   /api/divisi/{kodeDivisi}/sales              # Create sales
GET    /api/divisi/{kodeDivisi}/sales/{kodeSales}  # Show sales
PUT    /api/divisi/{kodeDivisi}/sales/{kodeSales}  # Update sales
DELETE /api/divisi/{kodeDivisi}/sales/{kodeSales}  # Delete sales
```

### 5. Customer Management (Scoped by Divisi)
```
GET    /api/divisi/{kodeDivisi}/customers              # List customers
POST   /api/divisi/{kodeDivisi}/customers              # Create customer
GET    /api/divisi/{kodeDivisi}/customers/{kodeCust}   # Show customer
PUT    /api/divisi/{kodeDivisi}/customers/{kodeCust}   # Update customer
DELETE /api/divisi/{kodeDivisi}/customers/{kodeCust}   # Delete customer
```

### 6. Supplier Management (Scoped by Divisi)
```
GET    /api/divisi/{kodeDivisi}/suppliers                  # List suppliers
POST   /api/divisi/{kodeDivisi}/suppliers                  # Create supplier
GET    /api/divisi/{kodeDivisi}/suppliers/{kodeSupplier}   # Show supplier
PUT    /api/divisi/{kodeDivisi}/suppliers/{kodeSupplier}   # Update supplier
DELETE /api/divisi/{kodeDivisi}/suppliers/{kodeSupplier}   # Delete supplier
```

### 7. Barang Management (Scoped by Divisi)
```
GET    /api/divisi/{kodeDivisi}/barangs               # List barangs
POST   /api/divisi/{kodeDivisi}/barangs               # Create barang
GET    /api/divisi/{kodeDivisi}/barangs/{kodeBarang}  # Show barang
PUT    /api/divisi/{kodeDivisi}/barangs/{kodeBarang}  # Update barang
DELETE /api/divisi/{kodeDivisi}/barangs/{kodeBarang} # Delete barang
```

### 8. Invoice Management (Scoped by Divisi)
```
GET    /api/divisi/{kodeDivisi}/invoices              # List invoices
POST   /api/divisi/{kodeDivisi}/invoices              # Create invoice
GET    /api/divisi/{kodeDivisi}/invoices/{noInvoice}  # Show invoice
PUT    /api/divisi/{kodeDivisi}/invoices/{noInvoice}  # Update invoice
DELETE /api/divisi/{kodeDivisi}/invoices/{noInvoice} # Delete invoice
```

## Additional Resources

### 9. D Paket (Detail Paket)
```
GET    /api/divisi/{kodeDivisi}/dpakets     # List dpakets
POST   /api/divisi/{kodeDivisi}/dpakets     # Create dpaket
GET    /api/divisi/{kodeDivisi}/dpakets/{id} # Show dpaket
PUT    /api/divisi/{kodeDivisi}/dpakets/{id} # Update dpaket
DELETE /api/divisi/{kodeDivisi}/dpakets/{id} # Delete dpaket
```

### 10. D TT (Detail Transaction Type)
```
GET    /api/divisi/{kodeDivisi}/dtts        # List dtts
POST   /api/divisi/{kodeDivisi}/dtts        # Create dtt
GET    /api/divisi/{kodeDivisi}/dtts/{id}   # Show dtt
PUT    /api/divisi/{kodeDivisi}/dtts/{id}   # Update dtt
DELETE /api/divisi/{kodeDivisi}/dtts/{id}  # Delete dtt
```

### 11. D Voucher (Detail Voucher)
```
GET    /api/divisi/{kodeDivisi}/dvouchers     # List dvouchers
POST   /api/divisi/{kodeDivisi}/dvouchers     # Create dvoucher
GET    /api/divisi/{kodeDivisi}/dvouchers/{id} # Show dvoucher
PUT    /api/divisi/{kodeDivisi}/dvouchers/{id} # Update dvoucher
DELETE /api/divisi/{kodeDivisi}/dvouchers/{id} # Delete dvoucher
```

### 12. M Dokumen (Master Dokumen)
```
GET    /api/divisi/{kodeDivisi}/mdokumens     # List mdokumens
POST   /api/divisi/{kodeDivisi}/mdokumens     # Create mdokumen
GET    /api/divisi/{kodeDivisi}/mdokumens/{id} # Show mdokumen
PUT    /api/divisi/{kodeDivisi}/mdokumens/{id} # Update mdokumen
DELETE /api/divisi/{kodeDivisi}/mdokumens/{id} # Delete mdokumen
```

### 13. M Resi (Master Resi)
```
GET    /api/divisi/{kodeDivisi}/mresis       # List mresis
POST   /api/divisi/{kodeDivisi}/mresis       # Create mresi
GET    /api/divisi/{kodeDivisi}/mresis/{id}  # Show mresi
PUT    /api/divisi/{kodeDivisi}/mresis/{id}  # Update mresi
DELETE /api/divisi/{kodeDivisi}/mresis/{id} # Delete mresi
```

### 14. M TT (Master Transaction Type)
```
GET    /api/divisi/{kodeDivisi}/mtts         # List mtts
POST   /api/divisi/{kodeDivisi}/mtts         # Create mtt
GET    /api/divisi/{kodeDivisi}/mtts/{id}    # Show mtt
PUT    /api/divisi/{kodeDivisi}/mtts/{id}    # Update mtt
DELETE /api/divisi/{kodeDivisi}/mtts/{id}   # Delete mtt
```

### 15. M Voucher (Master Voucher)
```
GET    /api/divisi/{kodeDivisi}/mvouchers     # List mvouchers
POST   /api/divisi/{kodeDivisi}/mvouchers     # Create mvoucher
GET    /api/divisi/{kodeDivisi}/mvouchers/{id} # Show mvoucher
PUT    /api/divisi/{kodeDivisi}/mvouchers/{id} # Update mvoucher
DELETE /api/divisi/{kodeDivisi}/mvouchers/{id} # Delete mvoucher
```

### 16. Penerimaan Finance
```
GET    /api/divisi/{kodeDivisi}/penerimaan-finances     # List penerimaan finances
POST   /api/divisi/{kodeDivisi}/penerimaan-finances     # Create penerimaan finance
GET    /api/divisi/{kodeDivisi}/penerimaan-finances/{id} # Show penerimaan finance
PUT    /api/divisi/{kodeDivisi}/penerimaan-finances/{id} # Update penerimaan finance
DELETE /api/divisi/{kodeDivisi}/penerimaan-finances/{id} # Delete penerimaan finance
```

### 17. Penerimaan Finance Detail
```
GET    /api/divisi/{kodeDivisi}/penerimaan-finance-details     # List details
POST   /api/divisi/{kodeDivisi}/penerimaan-finance-details     # Create detail
GET    /api/divisi/{kodeDivisi}/penerimaan-finance-details/{id} # Show detail
PUT    /api/divisi/{kodeDivisi}/penerimaan-finance-details/{id} # Update detail
DELETE /api/divisi/{kodeDivisi}/penerimaan-finance-details/{id} # Delete detail
```

### 18. Retur Penerimaan
```
GET    /api/divisi/{kodeDivisi}/retur-penerimaans     # List retur penerimaans
POST   /api/divisi/{kodeDivisi}/retur-penerimaans     # Create retur penerimaan
GET    /api/divisi/{kodeDivisi}/retur-penerimaans/{id} # Show retur penerimaan
PUT    /api/divisi/{kodeDivisi}/retur-penerimaans/{id} # Update retur penerimaan
DELETE /api/divisi/{kodeDivisi}/retur-penerimaans/{id} # Delete retur penerimaan
```

### 19. Retur Penerimaan Detail
```
GET    /api/divisi/{kodeDivisi}/retur-penerimaan-details     # List details
POST   /api/divisi/{kodeDivisi}/retur-penerimaan-details     # Create detail
GET    /api/divisi/{kodeDivisi}/retur-penerimaan-details/{id} # Show detail
PUT    /api/divisi/{kodeDivisi}/retur-penerimaan-details/{id} # Update detail
DELETE /api/divisi/{kodeDivisi}/retur-penerimaan-details/{id} # Delete detail
```

### 20. Return Sales
```
GET    /api/divisi/{kodeDivisi}/return-sales     # List return sales
POST   /api/divisi/{kodeDivisi}/return-sales     # Create return sales
GET    /api/divisi/{kodeDivisi}/return-sales/{id} # Show return sales
PUT    /api/divisi/{kodeDivisi}/return-sales/{id} # Update return sales
DELETE /api/divisi/{kodeDivisi}/return-sales/{id} # Delete return sales
```

### 21. Return Sales Detail
```
GET    /api/divisi/{kodeDivisi}/return-sales-details     # List details
POST   /api/divisi/{kodeDivisi}/return-sales-details     # Create detail
GET    /api/divisi/{kodeDivisi}/return-sales-details/{id} # Show detail
PUT    /api/divisi/{kodeDivisi}/return-sales-details/{id} # Update detail
DELETE /api/divisi/{kodeDivisi}/return-sales-details/{id} # Delete detail
```

### 22. Saldo Bank
```
GET    /api/divisi/{kodeDivisi}/saldo-banks                    # List saldo banks
POST   /api/divisi/{kodeDivisi}/saldo-banks                    # Create saldo bank
GET    /api/divisi/{kodeDivisi}/saldo-banks/{id}               # Show saldo bank
PUT    /api/divisi/{kodeDivisi}/saldo-banks/{id}               # Update saldo bank
DELETE /api/divisi/{kodeDivisi}/saldo-banks/{id}              # Delete saldo bank
GET    /api/divisi/{kodeDivisi}/saldo-banks/bank/{kodeBank}    # Get by bank
GET    /api/divisi/{kodeDivisi}/saldo-banks/bank/{kodeBank}/latest # Get latest balance
```

### 23. Stok Minimum
```
GET    /api/divisi/{kodeDivisi}/stok-minimums                 # List stok minimums
POST   /api/divisi/{kodeDivisi}/stok-minimums                 # Create stok minimum
GET    /api/divisi/{kodeDivisi}/stok-minimums/{id}            # Show stok minimum
PUT    /api/divisi/{kodeDivisi}/stok-minimums/{id}            # Update stok minimum
DELETE /api/divisi/{kodeDivisi}/stok-minimums/{id}           # Delete stok minimum
GET    /api/divisi/{kodeDivisi}/stok-minimums/check/low-stock # Check low stock items
```

## Reports API
```
GET /api/reports/stok-summary      # Inventory summary report
GET /api/reports/financial-report  # Financial report
GET /api/reports/aging-report      # Aging analysis report
GET /api/reports/sales-summary     # Sales summary report
GET /api/reports/return-summary    # Return summary report
GET /api/reports/dashboard-kpi     # Dashboard KPI metrics
```

## Stored Procedures API
```
POST /api/procedures/invoice           # Create invoice via procedure
POST /api/procedures/part-penerimaan   # Create part penerimaan via procedure
POST /api/procedures/retur-sales       # Create retur sales via procedure
POST /api/procedures/batalkan-invoice  # Cancel invoice via procedure
POST /api/procedures/stok-opname       # Stock opname via procedure
POST /api/procedures/generate-nomor    # Generate document numbers
```

## Response Format
All API endpoints return JSON responses in the format:
```json
{
  "data": {...},
  "message": "Success message",
  "status": "success|error"
}
```

## Error Handling
- 400: Bad Request (validation errors)
- 401: Unauthorized 
- 404: Not Found
- 422: Unprocessable Entity (validation errors)
- 500: Internal Server Error

## Validation Rules
All POST/PUT requests include comprehensive validation:
- Required fields validation
- Data type validation
- Foreign key existence validation
- Business rule validation
- Unique constraint validation
