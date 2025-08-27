# React Query Frontend Integration

Implementasi lengkap React Query untuk semua endpoint database dengan konfigurasi optimal dan komponen siap pakai.

## 📁 Struktur File yang Dibuat

```
frontend/src/
├── config/
│   ├── endpoints.js           # Konfigurasi semua endpoint API
│   └── queryClient.js         # Konfigurasi React Query client
├── services/
│   └── api.js                 # Enhanced API service layer
├── hooks/
│   └── useApi.js             # Custom hooks untuk semua endpoint
├── providers/
│   └── QueryProvider.jsx     # Provider React Query
├── components/
│   ├── DataTable/
│   │   ├── DataTable.jsx     # Komponen tabel universal
│   │   └── DataTable.css     # Styling tabel
│   └── Tables/
│       ├── CustomerTable.jsx  # Tabel customer
│       ├── BarangTable.jsx   # Tabel barang/produk
│       ├── InvoiceTable.jsx  # Tabel invoice
│       └── Tables.css        # Styling khusus tabel
└── examples/
    ├── DataExamplePage.jsx   # Halaman contoh implementasi
    └── DataExamplePage.css   # Styling halaman contoh
```

## 🚀 Fitur Implementasi

### 1. **Endpoint Configuration** (`config/endpoints.js`)
- ✅ Mapping lengkap 31 tabel database ke endpoint API
- ✅ Metadata konfigurasi untuk setiap endpoint
- ✅ Kategorisasi high/low frequency endpoints
- ✅ Helper functions untuk konfigurasi dinamis

### 2. **Query Client Setup** (`config/queryClient.js`)
- ✅ Konfigurasi optimal untuk caching dan retry
- ✅ Query key factory untuk konsistensi
- ✅ Cache invalidation helpers
- ✅ Prefetch utilities untuk UX yang lebih baik

### 3. **Enhanced API Service** (`services/api.js`)
- ✅ Axios interceptors untuk logging dan error handling
- ✅ Auth token management
- ✅ Response transformation untuk Laravel format
- ✅ Special methods untuk composite keys (m_resi)
- ✅ Bulk operations support

### 4. **Custom Hooks** (`hooks/useApi.js`)
- ✅ Hook untuk setiap tabel database
- ✅ Master data hooks dengan caching optimal
- ✅ Transaction data hooks dengan refresh lebih sering
- ✅ Mutation hooks dengan cache invalidation
- ✅ Composite key support untuk m_resi

### 5. **Universal DataTable** (`components/DataTable/`)
- ✅ Sorting, filtering, pagination built-in
- ✅ Loading dan error states
- ✅ Responsive design
- ✅ Customizable styling
- ✅ Row selection dan click handlers

### 6. **Specialized Tables** (`components/Tables/`)
- ✅ **CustomerTable**: Kolom customer dengan formatting khusus
- ✅ **BarangTable**: Product table dengan stock indicators dan pricing
- ✅ **InvoiceTable**: Invoice table dengan status dan summary cards
- ✅ Format currency, date, dan status badges

### 7. **Provider Setup** (`providers/QueryProvider.jsx`)
- ✅ QueryClient provider dengan DevTools
- ✅ Development-only DevTools
- ✅ Siap untuk production

## 📊 Coverage Database Tables

**100% Coverage - Semua 31 tabel telah dimapping:**

### Master Data (15 tables)
- ✅ customers → `/customers`
- ✅ suppliers → `/suppliers` + `/master-suppliers`
- ✅ m_sales → `/sales`
- ✅ m_area → `/areas`
- ✅ m_kategori → `/kategoris` + `/categories`
- ✅ m_barang → `/barang` + `/barangs`
- ✅ m_sparepart → `/spareparts`
- ✅ m_user → `/users` + `/master-users`
- ✅ m_divisi → `/divisis` + `/divisions`
- ✅ m_bank → `/banks`
- ✅ m_dokumen → `/dokumens` + `/documents`
- ✅ m_company → `/companies`
- ✅ m_coa → `/coas`
- ✅ m_user_modul → `/user-modules` + `/modules`

### Transaction Data (12 tables)
- ✅ invoice → `/invoices`
- ✅ invoice_detail → `/invoice-details`
- ✅ journal → `/journals` + `/journals/all`
- ✅ kartustok → `/kartu-stok` + `/kartu-stok/all`
- ✅ partpenerimaan → `/part-penerimaan` + `/part-penerimaan/all`
- ✅ penerimaanfinance → `/penerimaan-finance` + `/penerimaan-finance/all`
- ✅ saldobank → `/saldo-bank`
- ✅ m_resi → `/resi` + `/m-resi` (composite key support)
- ✅ retur_pembelian → `/return-purchases`
- ✅ retur_penjualan → `/return-sales`
- ✅ tmp_print_invoice → `/tmp-print-invoices`
- ✅ tmp_print_tt → `/tmp-print-tt`

### Administrative (4 tables)
- ✅ spv → `/spv`
- ✅ opname → `/opnames`
- ✅ stok_claim → `/stok-claims`
- ✅ v_cust_retur → `/v-cust-retur`
- ✅ v_return_penjualan_detail → `/v-return-sales-detail`

## 🎯 Penggunaan

### 1. Setup Provider
```jsx
import QueryProvider from './providers/QueryProvider';

function App() {
  return (
    <QueryProvider>
      <YourApp />
    </QueryProvider>
  );
}
```

### 2. Menggunakan Hooks
```jsx
import { useCustomers, useBarang, useInvoices } from './hooks/useApi';

function MyComponent() {
  const { data: customers, isLoading, error } = useCustomers();
  const { data: products } = useBarang({ kodeDivisi: '01' });
  const { data: invoices } = useInvoices({ status: 'pending' });
  
  // Handle loading dan error states
  if (isLoading) return <div>Loading...</div>;
  if (error) return <div>Error: {error.message}</div>;
  
  return (
    <div>
      {/* Render data */}
    </div>
  );
}
```

### 3. Menggunakan Table Components
```jsx
import CustomerTable from './components/Tables/CustomerTable';
import BarangTable from './components/Tables/BarangTable';
import InvoiceTable from './components/Tables/InvoiceTable';

function MyPage() {
  return (
    <div>
      <CustomerTable 
        onCustomerSelect={(customer) => console.log(customer)}
        pageSize={15}
      />
      
      <BarangTable 
        divisionFilter="01"
        showDivision={false}
      />
      
      <InvoiceTable 
        filters={{ status: 'pending' }}
        onInvoiceSelect={(invoice) => navigate(`/invoice/${invoice.id}`)}
      />
    </div>
  );
}
```

### 4. Mutations
```jsx
import { useCreateCustomer, useUpdateCustomer } from './hooks/useApi';

function CustomerForm() {
  const createCustomer = useCreateCustomer();
  const updateCustomer = useUpdateCustomer();
  
  const handleSubmit = (data) => {
    createCustomer.mutate(data, {
      onSuccess: () => {
        // Customer berhasil dibuat, cache akan diinvalidate otomatis
      }
    });
  };
}
```

## 🔧 Konfigurasi

### Environment Variables
```env
VITE_API_BASE_URL=http://localhost:8000/api
```

### Package Dependencies
```bash
npm install @tanstack/react-query @tanstack/react-table axios
npm install --save-dev @tanstack/react-query-devtools
```

## 🎨 Styling

Semua komponen telah dilengkapi dengan CSS styling yang responsive dan modern:
- Loading states dengan spinner
- Error states dengan retry button
- Responsive design untuk mobile
- Status badges dan indicators
- Currency dan date formatting
- Professional table styling

## 🔄 State Management

- **Caching**: Data master di-cache 5-30 menit, transaksi 30 detik - 2 menit
- **Invalidation**: Cache otomatis diinvalidate setelah mutations
- **Retry**: Automatic retry dengan exponential backoff
- **Background Updates**: Data di-refetch di background
- **Optimistic Updates**: UI update sebelum server response

## 📈 Performance

- **Prefetching**: Master data di-prefetch untuk UX yang lebih baik
- **Pagination**: Built-in pagination untuk large datasets
- **Virtual Scrolling**: Siap untuk implementasi jika diperlukan
- **Debounced Search**: Search input dengan debouncing
- **Lazy Loading**: Component loading sesuai kebutuhan

## 🛠 Next Steps

1. **Install Dependencies**:
   ```bash
   cd frontend
   npm install @tanstack/react-query @tanstack/react-table axios
   npm install --save-dev @tanstack/react-query-devtools
   ```

2. **Integrate Provider** ke main App component

3. **Test API Connection** dengan example page

4. **Customize Tables** sesuai kebutuhan UI/UX

5. **Add Mutations** untuk CRUD operations

6. **Implement Search & Filters** advanced

7. **Add Real-time Updates** dengan WebSocket jika diperlukan

Semua endpoint backend sudah siap, frontend React Query setup sudah lengkap. Tinggal integrate dan customize sesuai kebutuhan aplikasi! 🚀
