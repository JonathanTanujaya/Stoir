# Barang CRUD Blueprint - Production Ready Template

## Overview
Blueprint ini adalah template production-ready untuk implementasi CRUD yang dapat direplikasi ke semua model lainnya dalam sistem ERP. Implementasi ini mengikuti best practices Laravel dan standar enterprise development.

## Architecture Pillars

### 1. Form Request Validation Classes ✅
**Files:**
- `app/Http/Requests/StoreBarangRequest.php`
- `app/Http/Requests/UpdateBarangRequest.php`

**Features:**
- Validation rules separated from controller
- Custom error messages
- Business rule enforcement
- Unique constraints with divisi context
- Data preparation methods

**Benefits:**
- Cleaner controllers
- Reusable validation logic
- Centralized business rules
- Better error handling

### 2. API Resource Standardization ✅
**Files:**
- `app/Http/Resources/BarangResource.php`
- `app/Http/Resources/BarangCollection.php`

**Features:**
- Standardized JSON response format
- Currency formatting
- Relationship handling
- Conditional field inclusion
- Metadata enrichment
- Performance metrics

**Benefits:**
- Consistent API responses
- Frontend-friendly data format
- Controlled data exposure
- Performance insights

### 3. Comprehensive Testing Suite ✅
**Files:**
- `tests/Feature/BarangApiTest.php`

**Features:**
- Full CRUD operation testing
- Validation error testing
- Edge case handling
- API response structure validation
- Error scenario testing
- Performance assertions

**Benefits:**
- Regression prevention
- Documentation as code
- Confident refactoring
- Quality assurance

## Enhanced Controller Features

### Error Handling & Logging
```php
// Comprehensive error handling with logging
try {
    // Business logic
} catch (\Exception $e) {
    Log::error('Context-specific error message', [
        'context' => $data,
        'error' => $e->getMessage()
    ]);
    
    return standardized_error_response();
}
```

### Resource Usage
```php
// Standardized resource responses
return (new BarangResource($barang))
    ->additional([
        'success' => true,
        'message' => 'Operation successful'
    ])
    ->response()
    ->setStatusCode(201);
```

### Smart Deletion
```php
// Business-aware deletion logic
if ($hasTransactions) {
    // Soft delete - preserve data integrity
    $barang->update(['status' => false]);
} else {
    // Hard delete - clean removal
    $barang->delete();
}
```

## Response Structure Standard

### Single Resource Response
```json
{
    "data": {
        "kode_divisi": "TEST",
        "kode_barang": "BRG001",
        "nama_barang": "Product Name",
        "pricing": {
            "harga_list": "Rp 100.000",
            "harga_jual": "Rp 120.000",
            "harga_list_raw": 100000.0,
            "harga_jual_raw": 120000.0
        },
        "inventory": {
            "satuan": "PCS",
            "stok_min": 5,
            "status": true,
            "status_text": "Active"
        },
        "discounts": {
            "disc1": 5.0,
            "disc2": 2.0,
            "disc1_formatted": "5%",
            "disc2_formatted": "2%"
        },
        "product_info": {
            "merk": "Brand Name",
            "barcode": "1234567890"
        },
        "meta": {
            "created_at": "2025-09-09T03:32:53.000000Z",
            "updated_at": "2025-09-09T03:32:53.000000Z",
            "created_at_human": "2 hours ago",
            "updated_at_human": "2 hours ago"
        },
        "relationships": {
            "divisi": {...},
            "kategori": {...}
        }
    },
    "success": true,
    "message": "Operation successful",
    "api_version": "1.0",
    "timestamp": "2025-09-09T03:32:53.000000Z"
}
```

### Collection Response
```json
{
    "data": [...],
    "summary": {
        "total_items": 5,
        "has_active_items": true,
        "categories_count": 2,
        "total_value": 500000,
        "average_price": 100000
    },
    "filters_applied": {
        "search": "laptop",
        "kategori": "KAT001",
        "status": "1"
    },
    "pagination": {
        "current_page": 1,
        "per_page": 15,
        "total": 5,
        "last_page": 1,
        "has_more_pages": false
    }
}
```

## Validation Rules Template

### Store Request Pattern
```php
public function rules(): array
{
    $kodeDivisi = $this->route('kodeDivisi');
    
    return [
        'kode_barang' => [
            'required',
            'string',
            'max:20',
            'regex:/^[A-Z0-9]+$/',
            Rule::unique('m_barang')
                ->where('kode_divisi', $kodeDivisi)
        ],
        'nama_barang' => 'required|string|max:100',
        // ... other rules
    ];
}
```

### Update Request Pattern
```php
public function rules(): array
{
    $kodeDivisi = $this->route('kodeDivisi');
    $kodeBarang = $this->route('kodeBarang');
    
    return [
        'kode_barang' => [
            'sometimes',
            'required',
            'string',
            'max:20',
            Rule::unique('m_barang')
                ->where('kode_divisi', $kodeDivisi)
                ->ignore($kodeBarang, 'kode_barang')
        ],
        // ... other rules
    ];
}
```

## Testing Pattern Template

### Basic CRUD Tests
```php
public function test_can_create_resource(): void
{
    $data = $this->getValidData();
    
    $response = $this->postJson($this->getEndpoint(), $data);
    
    $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonStructure($this->getExpectedStructure());
            
    $this->assertDatabaseHas($this->getTable(), $data);
}
```

### Validation Tests
```php
public function test_validation_errors_for_invalid_data(): void
{
    $invalidData = $this->getInvalidData();
    
    $response = $this->postJson($this->getEndpoint(), $invalidData);
    
    $response->assertStatus(422)
            ->assertJsonValidationErrors($this->getExpectedErrors());
}
```

## Replication Checklist

### For Each New Model (Customer, Sales, Invoice):

#### 1. Form Requests
- [ ] Create StoreXxxRequest
- [ ] Create UpdateXxxRequest
- [ ] Define validation rules
- [ ] Add custom messages
- [ ] Implement data preparation

#### 2. API Resources
- [ ] Create XxxResource
- [ ] Create XxxCollection
- [ ] Define response structure
- [ ] Add relationship handling
- [ ] Implement formatters

#### 3. Controller Enhancements
- [ ] Add error handling
- [ ] Implement logging
- [ ] Use resources for responses
- [ ] Add business logic
- [ ] Implement smart operations

#### 4. Testing Suite
- [ ] Create XxxApiTest
- [ ] Test all CRUD operations
- [ ] Test validation scenarios
- [ ] Test error handling
- [ ] Test edge cases

#### 5. Documentation
- [ ] Document API endpoints
- [ ] Document response structures
- [ ] Document business rules
- [ ] Create usage examples

## Performance Considerations

### Database Optimization
- Proper indexing on search fields
- Efficient relationship loading
- Query optimization
- Pagination for large datasets

### API Optimization
- Resource caching
- Response compression
- Rate limiting
- Query parameter validation

### Code Quality
- SOLID principles
- DRY implementation
- Clear separation of concerns
- Comprehensive documentation

## Security Features

### Input Validation
- Strict validation rules
- SQL injection prevention
- XSS protection
- Business rule enforcement

### Access Control
- Authentication middleware
- Authorization policies
- Role-based permissions
- Audit logging

### Data Protection
- Sensitive data masking
- Secure data transmission
- Data integrity constraints
- Backup strategies

## Maintenance Guidelines

### Code Standards
- PSR-12 coding standards
- Laravel conventions
- Comprehensive comments
- Type declarations

### Version Control
- Feature branch workflow
- Descriptive commit messages
- Code review process
- Automated testing

### Monitoring
- Error logging
- Performance monitoring
- Usage analytics
- Health checks

## Next Implementation Targets

1. **Customer CRUD** - Apply blueprint to Customer model
2. **Sales CRUD** - Implement with Invoice relationships
3. **Invoice CRUD** - Complex model with multiple relationships
4. **Supplier CRUD** - Purchasing module foundation

## Conclusion

Blueprint Barang telah berhasil diimplementasikan dengan standar production-ready. Template ini mencakup:

- ✅ Form Request validation yang robust
- ✅ API Resource yang standardized
- ✅ Testing suite yang comprehensive
- ✅ Error handling yang proper
- ✅ Logging yang detailed
- ✅ Documentation yang lengkap

Blueprint ini siap untuk direplikasi ke models lainnya dengan penyesuaian business logic sesuai kebutuhan masing-masing model.
