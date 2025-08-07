# Testing Suite Documentation

## Overview
This testing suite provides comprehensive coverage for the Laravel backend system including:
- Unit Tests for Models and Services
- Feature Tests for API endpoints  
- Integration Tests for workflows
- Authentication and Security Tests

## Test Structure

```
tests/
├── TestCase.php                    # Base test class
├── Unit/
│   ├── Models/
│   │   ├── OpnameTest.php         # Opname model tests
│   │   └── InvoiceTest.php        # Invoice model tests
│   └── Services/
│       └── BusinessRuleServiceTest.php
├── Feature/
│   ├── Api/
│   │   ├── InvoiceApiTest.php     # Invoice API tests
│   │   └── AuthenticationApiTest.php
│   └── Integration/
│       └── InvoiceWorkflowTest.php
└── Traits/
    └── CreatesTestData.php         # Test data factory
```

## Test Configuration

### PHPUnit Configuration (phpunit.xml)
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="CACHE_STORE" value="array"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
    </php>
</phpunit>
```

## Running Tests

### All Tests
```bash
php artisan test
```

### Specific Test Suites
```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only  
php artisan test --testsuite=Feature

# Specific test file
php artisan test tests/Unit/Models/OpnameTest.php

# Specific test method
php artisan test --filter=test_it_can_create_opname
```

### With Coverage
```bash
php artisan test --coverage
php artisan test --coverage-html coverage/
```

## Test Types

### 1. Unit Tests
Test individual components in isolation:

```php
public function test_it_validates_journal_balance()
{
    $balancedDetails = [
        ['debit' => 100000, 'kredit' => 0],
        ['debit' => 0, 'kredit' => 100000]
    ];

    $error = $this->businessRuleService->validateJournalBalance($balancedDetails);
    $this->assertNull($error);
}
```

### 2. Feature Tests
Test API endpoints and user interactions:

```php
public function test_it_can_create_invoice()
{
    $invoiceData = [
        'kodedivisi' => 'TEST',
        'noinvoice' => 'INV001',
        'tanggal' => '2024-01-01',
        'kodecust' => 'CUST001',
        'total' => 150000,
        'details' => [...]
    ];

    $response = $this->authenticateAs($this->user)
                    ->postJson('/api/invoices', $invoiceData);

    $response->assertStatus(201);
}
```

### 3. Integration Tests  
Test complete workflows:

```php
public function test_complete_invoice_workflow()
{
    // 1. Create invoice
    // 2. Approve invoice  
    // 3. Verify stock reduction
    // 4. Verify journal entries
    // 5. Test cancellation
}
```

## Database Testing

### SQLite In-Memory Database
For fast unit tests without PostgreSQL dependency:

```php
// phpunit.xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### PostgreSQL Test Database
For integration tests requiring full database features:

```php
// phpunit.xml  
<env name="DB_CONNECTION" value="pgsql_testing"/>
<env name="DB_DATABASE" value="stoir_test"/>
```

### Test Data Setup
```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestData();
    }
}
```

## Authentication Testing

### Creating Test Users
```php
protected function createAuthenticatedUser($level = 'User'): MasterUser
{
    return MasterUser::create([
        'username' => 'test_' . uniqid(),
        'password' => 'password123',
        'level' => $level,
        'status' => 'Active'
    ]);
}
```

### API Authentication
```php
protected function authenticateAs($user = null)
{
    if (!$user) {
        $user = $this->createAuthenticatedUser();
    }

    $token = $user->createToken('test-token')->plainTextToken;
    
    return $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json'
    ]);
}
```

## Validation Testing

### Form Request Testing
```php
public function test_validates_required_fields()
{
    $invalidData = [
        'kodedivisi' => '',
        'noinvoice' => '',
        'total' => ''
    ];

    $response = $this->postJson('/api/invoices', $invalidData);

    $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'kodedivisi',
                'noinvoice', 
                'total'
            ]);
}
```

### Business Rule Testing
```php
public function test_validates_stock_availability()
{
    $invoiceData = [
        'details' => [
            [
                'kodebarang' => 'BRG001',
                'qty' => 150 // Exceeds available stock
            ]
        ]
    ];

    $response = $this->postJson('/api/invoices', $invoiceData);
    $response->assertStatus(422);
}
```

## Security Testing

### Rate Limiting
```php
public function test_respects_rate_limiting()
{
    // Make 125 requests
    for ($i = 0; $i < 125; $i++) {
        $this->getJson('/api/invoices');
    }

    // 126th request should be rate limited
    $response = $this->getJson('/api/invoices');
    $response->assertStatus(429);
}
```

### Input Sanitization
```php
public function test_sanitizes_malicious_input()
{
    $maliciousData = [
        'noinvoice' => 'INV<script>alert("xss")</script>001',
        'keterangan' => '<script>alert("xss")</script>Test'
    ];

    $response = $this->postJson('/api/invoices', $maliciousData);
    
    // Verify script tags are removed
}
```

## Test Utilities

### Test Data Factory
```php
trait CreatesTestData
{
    protected function createTestCustomer($attributes = []): MCust
    {
        $defaults = [
            'kodecust' => 'CUST' . rand(100, 999),
            'namacust' => 'Test Customer',
            'credit_limit' => 1000000
        ];

        return MCust::create(array_merge($defaults, $attributes));
    }
}
```

### Assertions
```php
// Database assertions
$this->assertDatabaseHas('invoices', ['noinvoice' => 'INV001']);
$this->assertDatabaseMissing('invoices', ['noinvoice' => 'INV001']);

// JSON assertions  
$response->assertJsonStructure(['data' => ['noinvoice', 'total']]);
$response->assertJson(['message' => 'Success']);

// Model assertions
$this->assertInstanceOf(Invoice::class, $invoice);
$this->assertEquals('INV001', $invoice->noinvoice);
```

## Continuous Integration

### GitHub Actions Example
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        
    - name: Install Dependencies
      run: composer install
      
    - name: Run Tests
      run: php artisan test
```

## Best Practices

### 1. Test Naming
- Use descriptive test method names
- Prefix with `test_` for PHPUnit compatibility
- Use `it_` pattern for readability: `test_it_can_create_invoice`

### 2. Test Structure (AAA Pattern)
```php
public function test_it_validates_credit_limit()
{
    // Arrange
    $customer = $this->createTestCustomer(['credit_limit' => 1000000]);
    $invoiceData = ['total' => 2000000]; // Exceeds limit
    
    // Act  
    $response = $this->postJson('/api/invoices', $invoiceData);
    
    // Assert
    $response->assertStatus(422);
}
```

### 3. Test Isolation
- Each test should be independent
- Use `RefreshDatabase` trait
- Clean up test data properly

### 4. Mock External Dependencies
```php
public function test_sends_notification()
{
    Mail::fake();
    
    // Test code
    
    Mail::assertSent(InvoiceCreatedMail::class);
}
```

## Troubleshooting

### Common Issues

1. **Database Connection Errors**
   - Ensure test database exists
   - Check database configuration
   - Use SQLite for unit tests

2. **Class Not Found Errors**
   - Run `composer dump-autoload`
   - Check namespace declarations
   - Verify use statements

3. **Authentication Failures**
   - Ensure Sanctum is configured
   - Check token generation
   - Verify middleware registration

### Performance Tips

1. Use SQLite in-memory for unit tests
2. Group related tests in same class
3. Use test data factories
4. Minimize database interactions
5. Run tests in parallel when possible

## Coverage Goals

- **Models**: 90%+ coverage of methods and relationships
- **Controllers**: 85%+ coverage of endpoints and validation
- **Services**: 95%+ coverage of business logic
- **API Endpoints**: 100% coverage of public endpoints
- **Authentication**: 100% coverage of auth flows

This comprehensive testing suite ensures the reliability, security, and maintainability of the Laravel backend system.
