# ERP System - Laravel & React

This is a complete ERP (Enterprise Resource Planning) system built with Laravel backend and React frontend, based on the provided PostgreSQL database schema.

## Project Structure

### Backend (Laravel)

#### Models
All models are located in `app/Models/` and represent the database tables:

- **Company.php** - Company information
- **Divisi.php** - Division master data
- **Bank.php** - Bank master data
- **DetailBank.php** - Bank account details
- **Area.php** - Area master data
- **Sales.php** - Sales person master data
- **Customer.php** - Customer master data
- **Kategori.php** - Category master data
- **Barang.php** - Product/item master data
- **DetailBarang.php** - Product detail with stock information
- **Supplier.php** - Supplier master data
- **TransactionType.php** - Transaction type definitions
- **DetailTransaction.php** - Transaction type details
- **Invoice.php** - Sales invoice header
- **InvoiceDetail.php** - Sales invoice line items
- **Journal.php** - Accounting journal entries
- **KartuStok.php** - Stock card (inventory movements)
- **PartPenerimaan.php** - Purchase receipt header
- **PartPenerimaanDetail.php** - Purchase receipt line items
- **Coa.php** - Chart of accounts

#### Controllers
All controllers are located in `app/Http/Controllers/` and provide CRUD API endpoints:

- **DivisiController.php** - Division management
- **BankController.php** - Bank management
- **CustomerController.php** - Customer management
- **SupplierController.php** - Supplier management
- **BarangController.php** - Product management
- **InvoiceController.php** - Invoice management
- **AreaController.php** - Area management
- **SalesController.php** - Sales person management
- **ReportController.php** - Reporting endpoints (views)
- **ProcedureController.php** - Stored procedure endpoints

#### API Routes
Located in `routes/api.php`, providing RESTful endpoints:

```
/api/divisi - Division CRUD
/api/divisi/{kodeDivisi}/banks - Banks per division
/api/divisi/{kodeDivisi}/areas - Areas per division
/api/divisi/{kodeDivisi}/sales - Sales persons per division
/api/divisi/{kodeDivisi}/customers - Customers per division
/api/divisi/{kodeDivisi}/suppliers - Suppliers per division
/api/divisi/{kodeDivisi}/barangs - Products per division
/api/divisi/{kodeDivisi}/invoices - Invoices per division
/api/reports/* - Various reports from views
/api/procedures/* - Stored procedure endpoints
```

#### Database Migrations
Sample migrations in `database/migrations/`:
- **2024_01_01_000001_create_company_table.php**
- **2024_01_01_000002_create_m_divisi_table.php**
- **2024_01_01_000003_create_m_coa_table.php**

### Frontend (React)

#### Components
All components are located in `frontend/src/components/`:

- **ERPApp.jsx** - Main application component with tab navigation
- **DivisiManager.jsx** - Division management interface
- **CustomerManager.jsx** - Customer management interface
- **BarangManager.jsx** - Product management interface
- **InvoiceManager.jsx** - Invoice management interface
- **ReportsManager.jsx** - Reports viewing interface
- **ProcedureManager.jsx** - Stored procedure execution interface

#### Features
1. **Master Data Management**: Full CRUD operations for all master data
2. **Multi-tenant Architecture**: All data is scoped by division (kode_divisi)
3. **Responsive Design**: Built with Tailwind CSS
4. **Real-time Reports**: Direct integration with PostgreSQL views
5. **Stored Procedures**: Interface for executing business logic procedures
6. **Export Functionality**: CSV export for reports
7. **Form Validation**: Client and server-side validation

## Database Integration

### Views Support
The application integrates with 24 PostgreSQL views:
- Stock summary with critical/low/normal status
- Financial reports with adjusted balances
- Aging reports for receivables
- Sales summaries per sales person and period
- Return/retur monitoring
- Dashboard KPI overview

### Stored Procedures Support
Integration with 14 stored procedures:
- Invoice creation with automatic stock reduction (FIFO)
- Purchase receipt processing with automatic journaling
- Sales return processing
- Invoice cancellation with stock restoration
- Stock opname/adjustment
- Document number generation
- And more...

## Getting Started

### Prerequisites
- PHP 8.1+
- Composer
- Node.js 16+
- PostgreSQL 12+

### Backend Setup
```bash
# Install Laravel dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Start Laravel server
php artisan serve
```

### Frontend Setup
```bash
# Navigate to frontend directory
cd frontend

# Install dependencies
npm install

# Start development server
npm run dev
```

### Database Setup
1. Create PostgreSQL database
2. Run the SQL scripts in order:
   - `create_tables_pk.sql` - Create all tables
   - `uptView.txt` - Create all views
   - `uptProcedure.txt` - Create all stored procedures

## API Documentation

### Authentication
Currently using Laravel Sanctum (commented out in routes). You can enable authentication by uncommenting middleware in `routes/api.php`.

### Error Handling
All API endpoints return consistent JSON responses:
```json
{
  "message": "Success message",
  "data": {...}
}
```

For errors:
```json
{
  "error": "Error message"
}
```

### Pagination
List endpoints support Laravel's built-in pagination. Add query parameters:
- `?page=1` - Page number
- `?per_page=15` - Items per page

## Deployment

### Production Checklist
1. Set `APP_ENV=production` in `.env`
2. Run `composer install --optimize-autoloader --no-dev`
3. Run `php artisan config:cache`
4. Run `php artisan route:cache`
5. Run `php artisan view:cache`
6. Build frontend: `npm run build`
7. Configure web server (Nginx/Apache)

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
