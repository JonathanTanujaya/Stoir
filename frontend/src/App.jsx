import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import CssBaseline from '@mui/material/CssBaseline';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

// Context
import { AuthProvider } from './contexts/AuthContext';

// Layout & Auth Components
import LoginPage from './components/auth/LoginPage';
import ProtectedRoute from './components/auth/ProtectedRoute';
import MainLayout from './components/layout/MainLayout';
import DashboardPage from './components/dashboard/DashboardPage';

// Existing Pages (keep existing imports)
import CustomerPage from './components/CustomerPage.jsx';
import SalesPage from './components/SalesPage.jsx';
import BarangPage from './components/BarangPage.jsx';
import KategoriPage from './components/KategoriPage.jsx';
import AreaPage from './components/AreaPage.jsx';
import SupplierPage from './components/SupplierPage.jsx';
import MBankPage from './components/MBankPage.jsx';
import MCOAPage from './components/MCOAPage.jsx';
import MDivisiPage from './components/MDivisiPage.jsx';
import MDokumenPage from './components/MDokumenPage.jsx';
import MModulePage from './components/MModulePage.jsx';
import MasterUserPage from './components/MasterUserPage.jsx';
import MResiPage from './components/MResiPage.jsx';
import MTTPage from './components/MTTPage.jsx';
import MTransPage from './components/MTransPage.jsx';
import InvoicePage from './components/InvoicePage.jsx';
import PartPenerimaanPage from './components/PartPenerimaanPage.jsx';
import ReturnSalesPage from './components/ReturnSalesPage.jsx';
import PenerimaanFinancePage from './components/PenerimaanFinancePage.jsx';
import ClaimPenjualanPage from './components/ClaimPenjualanPage.jsx';
import PartPenerimaanBonusPage from './pages/PartPenerimaanBonusPage.jsx';
import ReturPenerimaanPage from './pages/ReturPenerimaanPage.jsx';
import SaldoBankPage from './pages/SaldoBankPage.jsx';
import SPVPage from './pages/SPVPage.jsx';
import StokClaimPage from './pages/StokClaimPage.jsx';
import StokMinimumPage from './pages/StokMinimumPage.jsx';
import TmpPrintTTPage from './pages/TmpPrintTTPage.jsx';
import UserModulePage from './pages/UserModulePage.jsx';
import DPaketPage from './pages/DPaketPage.jsx';
import MVoucherPage from './pages/MVoucherPage.jsx';
import MergeBarangPage from './pages/MergeBarangPage.jsx';
import OpnamePage from './pages/OpnamePage.jsx';
import './App.css';

// Material-UI Theme
const theme = createTheme({
  palette: {
    primary: {
      main: '#1976d2',
    },
    secondary: {
      main: '#dc004e',
    },
  },
});

function App() {
  return (
    <ThemeProvider theme={theme}>
      <CssBaseline />
      <AuthProvider>
        <Router>
          <div className="App">
            <Routes>
              {/* Public Routes */}
              <Route path="/login" element={<LoginPage />} />
              
              {/* Protected Routes */}
              <Route path="/" element={
                <ProtectedRoute>
                  <MainLayout />
                </ProtectedRoute>
              }>
                {/* Default redirect to dashboard */}
                <Route index element={<Navigate to="/dashboard" replace />} />
                
                {/* Dashboard */}
                <Route path="dashboard" element={<DashboardPage />} />
                
                {/* Master Data Routes */}
                <Route path="customers" element={<CustomerPage />} />
                <Route path="sales" element={<SalesPage />} />
                <Route path="barang" element={<BarangPage />} />
                <Route path="kategori" element={<KategoriPage />} />
                <Route path="areas" element={<AreaPage />} />
                <Route path="suppliers" element={<SupplierPage />} />
                <Route path="divisi" element={<MDivisiPage />} />
                <Route path="master-user" element={<MasterUserPage />} />
                
                {/* Transaction Routes */}
                <Route path="invoices" element={<InvoicePage />} />
                <Route path="part-penerimaan" element={<PartPenerimaanPage />} />
                <Route path="return-sales" element={<ReturnSalesPage />} />
                <Route path="claim-penjualan" element={<ClaimPenjualanPage />} />
                <Route path="opname" element={<OpnamePage />} />
                <Route path="part-penerimaan-bonus" element={<PartPenerimaanBonusPage />} />
                <Route path="retur-penerimaan" element={<ReturPenerimaanPage />} />
                
                {/* Finance Routes */}
                <Route path="mcoa" element={<MCOAPage />} />
                <Route path="mbank" element={<MBankPage />} />
                <Route path="saldo-bank" element={<SaldoBankPage />} />
                <Route path="penerimaan-finance" element={<PenerimaanFinancePage />} />
                
                {/* Configuration Routes */}
                <Route path="mdokumen" element={<MDokumenPage />} />
                <Route path="mmodule" element={<MModulePage />} />
                <Route path="mresi" element={<MResiPage />} />
                <Route path="mtt" element={<MTTPage />} />
                <Route path="mtrans" element={<MTransPage />} />
                <Route path="mvoucher" element={<MVoucherPage />} />
                <Route path="merge-barang" element={<MergeBarangPage />} />
                <Route path="spv" element={<SPVPage />} />
                <Route path="stok-claim" element={<StokClaimPage />} />
                <Route path="stok-minimum" element={<StokMinimumPage />} />
                <Route path="tmp-print-tt" element={<TmpPrintTTPage />} />
                <Route path="user-module" element={<UserModulePage />} />
                <Route path="dpaket" element={<DPaketPage />} />
              </Route>
              
              {/* Catch all route - redirect to login */}
              <Route path="*" element={<Navigate to="/login" replace />} />
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
            />
          </div>
        </Router>
      </AuthProvider>
    </ThemeProvider>
  );
}

export default App;
