import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { ToastContainer } from 'react-toastify';
import { ThemeProvider, CssBaseline } from '@mui/material';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import theme from './theme';
import 'react-toastify/dist/ReactToastify.css';

// Create a client
const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      refetchOnWindowFocus: false,
      retry: 1,
      staleTime: 5 * 60 * 1000, // 5 minutes
    },
  },
});

// Layout Components
import AppTailwind from './AppTailwind';
import ErrorBoundary, { NotFound } from './components/ErrorBoundary';

// Pages
import Dashboard from './pages/Dashboard/SimpleDashboard';
import MasterCategories from './pages/MasterData/Categories/MasterCategoriesOptimized';
import MasterCustomers from './pages/MasterData/Customers/MasterCustomersOptimized';
import MasterSuppliers from './pages/MasterData/Suppliers/MasterSuppliers';
import MasterBarang from './pages/MasterData/Barang/MasterBarang';
import MasterSales from './pages/MasterData/Sales/MasterSales';
import PurchaseForm from './pages/Purchasing/PurchaseForm';

// New Master Data Components
import MasterSparepart from './pages/MasterData/Sparepart/MasterSparepart';
import MasterStockMin from './pages/MasterData/StockMin/MasterStockMin';
import MasterArea from './pages/MasterData/Area/MasterArea';
import MasterChecklist from './pages/MasterData/Checklist/MasterChecklist';
import MasterBank from './pages/MasterData/Bank/MasterBank';
import MasterRekening from './pages/MasterData/Rekening/MasterRekening';
import PurchasingIndex from './pages/Purchasing/PurchasingIndex';
import PurchaseList from './pages/Purchasing/PurchaseList';
import SalesIndex from './pages/Sales/SalesIndex';
import SalesForm from './pages/Sales/SalesForm';
import SalesTransactionForm from './pages/Sales/SalesTransactionForm';

// Return Forms
import ReturPembelianForm from './pages/Purchasing/ReturPembelianForm';
import ReturPenjualanForm from './pages/Sales/ReturPenjualanForm';

// New Transaction Components
import {
  MergeBarangForm,
  InvoiceCancelForm,
  StokOpnameForm,
  CustomerClaimForm,
  PembelianBonusForm,
  PenjualanBonusForm,
  PengembalianClaimForm
} from './pages/Transactions';

// Finance Components
import PenerimaanGiro from './pages/Finance/PenerimaanGiro';
import PencarianGiro from './pages/Finance/PencarianGiro';
import PenerimaanResi from './pages/Finance/PenerimaanResi';
import PiutangResi from './pages/Finance/PiutangResi';
import PiutangRetur from './pages/Finance/PiutangRetur';
import PenambahanSaldo from './pages/Finance/PenambahanSaldo';
import PenguranganSaldo from './pages/Finance/PenguranganSaldo';

// Reports
import {
  StokBarangReport,
  KartuStokReport,
  PembelianReport,
  PembelianItemReport,
  PenjualanReport,
  COGSReport,
  ReturnSalesReport,
  TampilInvoiceReport,
  SaldoRekeningReport,
  PembayaranCustomerReport,
  TagihanReport,
  PemotonganReturnCustomerReport,
  KomisiSalesReport
} from './pages/Reports';

import './App.css';

function App() {
  return (
    <ErrorBoundary>
      <QueryClientProvider client={queryClient}>
        <ThemeProvider theme={theme}>
          <CssBaseline />
          <Router>
          <div className="App">
          <Routes>
            {/* Main Application Routes with Layout */}
            <Route path="/" element={<AppTailwind />}>
              <Route index element={<Navigate to="/dashboard" replace />} />
              <Route path="dashboard" element={<Dashboard />} />
            
            {/* Master Data Routes */}
            <Route path="master/categories" element={<MasterCategories />} />
            <Route path="master/kategori" element={<MasterCategories />} />
            <Route path="master/customer" element={<MasterCustomers />} />
            <Route path="master/supplier" element={<MasterSuppliers />} />
            <Route path="master/barang" element={<MasterBarang />} />
            <Route path="master/sales" element={<MasterSales />} />
            <Route path="master/sparepart" element={<MasterSparepart />} />
            <Route path="master/stock-min" element={<MasterStockMin />} />
            <Route path="master/area" element={<MasterArea />} />
            <Route path="master/checklist" element={<MasterChecklist />} />
            <Route path="master/bank" element={<MasterBank />} />
            <Route path="master/rekening" element={<MasterRekening />} />
            
            {/* Purchasing Module - Simplified Routes */}
            <Route path="transactions/purchasing" element={<PurchasingIndex />} />
            <Route path="transactions/purchasing/form" element={<PurchaseForm />} />
            <Route path="transactions/purchasing/list" element={<PurchaseList />} />
            <Route path="transactions/purchasing/return" element={<ReturPembelianForm />} />
            <Route path="transactions/purchasing/retur-pembelian" element={<ReturPembelianForm />} />
            
            {/* Sales Module */}
            <Route path="transactions/sales" element={<SalesIndex />} />
            <Route path="transactions/sales/form" element={<SalesForm />} />
            <Route path="transactions/sales/new" element={<SalesTransactionForm />} />
            <Route path="transactions/sales/penjualan" element={<SalesForm />} />
            <Route path="transactions/sales/return" element={<ReturPenjualanForm />} />
            <Route path="transactions/sales/retur-penjualan" element={<ReturPenjualanForm />} />
            
            {/* New Transaction Routes */}
            <Route path="transactions/pembelian" element={<PurchaseForm />} />
            <Route path="transactions/retur-pembelian" element={<ReturPembelianForm />} />
            <Route path="transactions/penjualan" element={<SalesTransactionForm />} />
            <Route path="transactions/penjualan-new" element={<SalesTransactionForm />} />
            <Route path="transactions/merge-barang" element={<MergeBarangForm />} />
            <Route path="transactions/retur-penjualan" element={<ReturPenjualanForm />} />
            <Route path="transactions/invoice-cancel" element={<InvoiceCancelForm />} />
            <Route path="transactions/stok-opname" element={<StokOpnameForm />} />
            <Route path="transactions/pembelian-bonus" element={<PembelianBonusForm />} />
            <Route path="transactions/penjualan-bonus" element={<PenjualanBonusForm />} />
            <Route path="transactions/customer-claim" element={<CustomerClaimForm />} />
            <Route path="transactions/pengembalian-claim" element={<PengembalianClaimForm />} />
            
            {/* Legacy routes for backward compatibility */}
            <Route path="pembelian/form" element={<PurchaseForm />} />
            <Route path="pembelian/list" element={<PurchaseList />} />
            <Route path="retur/pembelian" element={<ReturPembelianForm />} />
            <Route path="retur/penjualan" element={<ReturPenjualanForm />} />
            
            {/* Reports Module */}
            <Route path="reports/stok-barang" element={<StokBarangReport />} />
            <Route path="reports/kartu-stok" element={<KartuStokReport />} />
            <Route path="reports/pembelian" element={<PembelianReport />} />
            <Route path="reports/pembelian-item" element={<PembelianItemReport />} />
            <Route path="reports/penjualan" element={<PenjualanReport />} />
            <Route path="reports/cogs" element={<COGSReport />} />
            <Route path="reports/return-sales" element={<ReturnSalesReport />} />
            <Route path="reports/tampil-invoice" element={<TampilInvoiceReport />} />
            <Route path="reports/saldo-rekening" element={<SaldoRekeningReport />} />
            <Route path="reports/pembayaran-customer" element={<PembayaranCustomerReport />} />
            <Route path="reports/tagihan" element={<TagihanReport />} />
            <Route path="reports/pemotongan-return-customer" element={<PemotonganReturnCustomerReport />} />
            <Route path="reports/komisi-sales" element={<KomisiSalesReport />} />
            
            {/* Finance Module */}
            <Route path="finance/penerimaan-giro" element={<PenerimaanGiro />} />
            <Route path="finance/pencarian-giro" element={<PencarianGiro />} />
            <Route path="finance/penerimaan-resi" element={<PenerimaanResi />} />
            <Route path="finance/piutang-resi" element={<PiutangResi />} />
            <Route path="finance/piutang-retur" element={<PiutangRetur />} />
            <Route path="finance/penambahan-saldo" element={<PenambahanSaldo />} />
            <Route path="finance/pengurangan-saldo" element={<PenguranganSaldo />} />
            
            {/* Add more routes here as needed */}
          </Route>
          
          {/* Catch-all route for 404 */}
          <Route path="*" element={<NotFound />} />
        </Routes>
        
        {/* Toast Notifications */}
        <ToastContainer
          position="top-right"
          autoClose={5000}
          hideProgressBar={false}
          newestOnTop={false}
          closeOnClick
          rtl={false}
          pauseOnFocusLoss
          draggable
          pauseOnHover
          theme="light"
        />
        </div>
      </Router>
    </ThemeProvider>
    </QueryClientProvider>
    </ErrorBoundary>
  );
}

export default App;
