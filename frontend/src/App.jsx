import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { ToastContainer } from 'react-toastify';
import { ThemeProvider, CssBaseline } from '@mui/material';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { Suspense, lazy } from 'react';
import theme from './theme';
import 'react-toastify/dist/ReactToastify.css';

// Import helpers
import { PageSuspense } from './components/SuspenseHelpers';

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

// Layout Components (keep these as regular imports for core functionality)
import ModernLayout from './components/Layout/ModernLayout';
import UniversalModernLayout from './components/Layout/UniversalModernLayout';
import DashboardOnlyLayout from './components/Layout/DashboardOnlyLayout';
import ErrorBoundary, { NotFound } from './components/ErrorBoundary';
import { PageLoading } from './components/LoadingComponents';

// Lazy load pages for better performance
const Dashboard = lazy(() => import('./pages/Dashboard/BasicDashboard'));
const MasterCategories = lazy(
  () => import('./pages/MasterData/Categories/MasterCategoriesOptimized')
);
const MasterCustomers = lazy(() => import('./pages/MasterData/Customers/MasterCustomersOptimized'));
const MasterSuppliers = lazy(() => import('./pages/MasterData/Suppliers/MasterSuppliers'));
const MasterSales = lazy(() => import('./pages/MasterData/Sales/MasterSales'));
const PurchaseForm = lazy(() => import('./pages/Purchasing/PurchaseForm'));

// Lazy load Master Data Components
const MasterSparepart = lazy(() => import('./pages/MasterData/Sparepart/MasterSparepartOptimized'));
const MasterArea = lazy(() => import('./pages/MasterData/Area/MasterAreaOptimized'));
const MasterChecklist = lazy(() => import('./pages/MasterData/Checklist/MasterChecklistOptimized'));
const MasterBank = lazy(() => import('./pages/MasterData/Bank/MasterBank'));
const PurchasingIndex = lazy(() => import('./pages/Purchasing/PurchasingIndex'));
const PurchaseList = lazy(() => import('./pages/Purchasing/PurchaseList'));
const SalesIndex = lazy(() => import('./pages/Sales/SalesIndex'));
const SalesForm = lazy(() => import('./pages/Sales/SalesForm'));
const SalesTransactionForm = lazy(() => import('./pages/Sales/SalesTransactionForm'));

// Lazy load Return Forms
const ReturPembelianForm = lazy(() => import('./pages/Purchasing/ReturPembelianForm'));
const ReturPenjualanForm = lazy(() => import('./pages/Sales/ReturPenjualanForm'));

// Lazy load Transaction Components
const StokOpnameForm = lazy(() =>
  import('./pages/Transactions').then(module => ({ default: module.StokOpnameForm }))
);
const CustomerClaimForm = lazy(() =>
  import('./pages/Transactions').then(module => ({ default: module.CustomerClaimForm }))
);
const PembelianBonusForm = lazy(() =>
  import('./pages/Transactions').then(module => ({ default: module.PembelianBonusForm }))
);

// Lazy load individual transaction component
const PenjualanBonus = lazy(() => import('./pages/Transactions/PenjualanBonus'));

// Lazy load Search Components
const SearchResultsPage = lazy(() => import('./pages/SearchResultsPage'));

// Lazy load Finance Components
const PenerimaanResi = lazy(() => import('./pages/Finance/PenerimaanResi'));
const PiutangResi = lazy(() => import('./pages/Finance/PiutangResi'));
const PiutangRetur = lazy(() => import('./pages/Finance/PiutangRetur'));
const PenambahanSaldo = lazy(() => import('./pages/Finance/PenambahanSaldo'));
const PenguranganSaldo = lazy(() => import('./pages/Finance/PenguranganSaldo'));

// Lazy load Reports
const StokBarangReport = lazy(() =>
  import('./pages/Reports').then(module => ({ default: module.StokBarangReport }))
);
const PembelianReport = lazy(() =>
  import('./pages/Reports').then(module => ({ default: module.PembelianReport }))
);
const PenjualanReport = lazy(() =>
  import('./pages/Reports').then(module => ({ default: module.PenjualanReport }))
);

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
                {/* Dashboard Route with Modern Layout - Top Bar Only */}
                <Route path="/dashboard" element={<DashboardOnlyLayout />}>
                  <Route
                    index
                    element={
                      <Suspense fallback={<PageLoading />}>
                        <Dashboard />
                      </Suspense>
                    }
                  />
                </Route>

                {/* Universal Modern Layout - All routes except dashboard */}
                <Route path="/" element={<UniversalModernLayout />}>
                  <Route index element={<Navigate to="/dashboard" replace />} />

                  {/* Search Route */}
                  <Route
                    path="search"
                    element={
                      <Suspense fallback={<PageLoading />}>
                        <SearchResultsPage />
                      </Suspense>
                    }
                  />

                  {/* Master Data Routes */}
                  <Route
                    path="master/categories"
                    element={
                      <Suspense fallback={<PageLoading />}>
                        <MasterCategories />
                      </Suspense>
                    }
                  />
                  <Route
                    path="master/kategori"
                    element={
                      <Suspense fallback={<PageLoading />}>
                        <MasterCategories />
                      </Suspense>
                    }
                  />
                  <Route
                    path="master/customer"
                    element={
                      <Suspense fallback={<PageLoading />}>
                        <MasterCustomers />
                      </Suspense>
                    }
                  />
                  <Route path="master/supplier" element={<MasterSuppliers />} />
                  <Route path="master/sparepart" element={<MasterSparepart />} />
                  <Route path="master/sales" element={<MasterSales />} />
                  <Route path="master/area" element={<MasterArea />} />
                  <Route path="master/checklist" element={<MasterChecklist />} />
                  <Route path="master/bank" element={<MasterBank />} />

                  {/* Transaction Routes */}
                  <Route path="transactions/pembelian" element={<PurchaseForm />} />
                  <Route path="transactions/retur-pembelian" element={<ReturPembelianForm />} />
                  <Route path="transactions/penjualan" element={<SalesForm />} />
                  <Route path="transactions/retur-penjualan" element={<ReturPenjualanForm />} />
                  <Route path="transactions/stok-opname" element={<StokOpnameForm />} />
                  <Route path="transactions/pembelian-bonus" element={<PembelianBonusForm />} />
                  <Route path="transactions/penjualan-bonus" element={<PenjualanBonus />} />
                  <Route path="transactions/customer-claim" element={<CustomerClaimForm />} />

                  {/* Finance Routes */}
                  <Route path="finance/penerimaan-resi" element={<PenerimaanResi />} />
                  <Route path="finance/piutang-resi" element={<PiutangResi />} />
                  <Route path="finance/piutang-retur" element={<PiutangRetur />} />
                  <Route path="finance/penambahan-saldo" element={<PenambahanSaldo />} />
                  <Route path="finance/pengurangan-saldo" element={<PenguranganSaldo />} />

                  {/* Reports Routes */}
                  <Route path="reports/stok-barang" element={<StokBarangReport />} />
                  <Route path="reports/pembelian" element={<PembelianReport />} />
                  <Route path="reports/penjualan" element={<PenjualanReport />} />

                  {/* Legacy routes for backward compatibility */}
                  <Route path="pembelian/form" element={<PurchaseForm />} />
                  <Route path="pembelian/list" element={<PurchaseList />} />
                  <Route path="retur/pembelian" element={<ReturPembelianForm />} />
                  <Route path="retur/penjualan" element={<ReturPenjualanForm />} />
                </Route>

                {/* Main Application Routes with Modern Layout */}
                <Route path="/" element={<ModernLayout />}>
                  <Route index element={<Navigate to="/dashboard" replace />} />

                  {/* Search Route */}
                  <Route
                    path="search"
                    element={
                      <Suspense fallback={<PageLoading />}>
                        <SearchResultsPage />
                      </Suspense>
                    }
                  />

                  {/* Master Data Routes */}
                  <Route
                    path="master/categories"
                    element={
                      <Suspense fallback={<PageLoading />}>
                        <MasterCategories />
                      </Suspense>
                    }
                  />
                  <Route
                    path="master/kategori"
                    element={
                      <Suspense fallback={<PageLoading />}>
                        <MasterCategories />
                      </Suspense>
                    }
                  />
                  <Route
                    path="master/customer"
                    element={
                      <Suspense fallback={<PageLoading />}>
                        <MasterCustomers />
                      </Suspense>
                    }
                  />
                  <Route path="master/supplier" element={<MasterSuppliers />} />
                  <Route path="master/sparepart" element={<MasterSparepart />} />

                  <Route path="master/sales" element={<MasterSales />} />
                  <Route path="master/area" element={<MasterArea />} />
                  <Route path="master/checklist" element={<MasterChecklist />} />
                  <Route path="master/bank" element={<MasterBank />} />

                  {/* Purchasing Module - Simplified Routes */}
                  <Route path="transactions/purchasing" element={<PurchasingIndex />} />
                  <Route path="transactions/purchasing/form" element={<PurchaseForm />} />
                  <Route path="transactions/purchasing/list" element={<PurchaseList />} />
                  <Route path="transactions/purchasing/return" element={<ReturPembelianForm />} />
                  <Route
                    path="transactions/purchasing/retur-pembelian"
                    element={<ReturPembelianForm />}
                  />

                  {/* Sales Module */}
                  <Route path="transactions/sales" element={<SalesIndex />} />
                  <Route path="transactions/sales/form" element={<SalesForm />} />
                  <Route path="transactions/sales/new" element={<SalesTransactionForm />} />
                  <Route path="transactions/sales/penjualan" element={<SalesForm />} />
                  <Route path="transactions/sales/return" element={<ReturPenjualanForm />} />
                  <Route
                    path="transactions/sales/retur-penjualan"
                    element={<ReturPenjualanForm />}
                  />

                  {/* Legacy routes for backward compatibility */}
                  <Route path="pembelian/form" element={<PurchaseForm />} />
                  <Route path="pembelian/list" element={<PurchaseList />} />
                  <Route path="retur/pembelian" element={<ReturPembelianForm />} />
                  <Route path="retur/penjualan" element={<ReturPenjualanForm />} />

                  {/* Reports Module */}
                  <Route path="reports/stok-barang" element={<StokBarangReport />} />
                  <Route path="reports/pembelian" element={<PembelianReport />} />
                  <Route path="reports/penjualan" element={<PenjualanReport />} />

                  {/* Finance Module */}
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
