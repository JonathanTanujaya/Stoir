import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

// Layout Components
import AppTailwind from './AppTailwind';

// Pages
import Dashboard from './pages/Dashboard/SimpleDashboard';
import MasterCategories from './pages/MasterData/Categories/MasterCategoriesOptimized';
import MasterCustomers from './pages/MasterData/Customers/MasterCustomersOptimized';
import MasterSuppliers from './pages/MasterData/Suppliers/MasterSuppliers';
import MasterBarang from './pages/MasterData/Barang/MasterBarang';
import MasterSales from './pages/MasterData/Sales/MasterSales';
import PurchaseForm from './pages/Purchasing/PurchaseForm';
import PurchasingIndex from './pages/Purchasing/PurchasingIndex';
import PurchaseList from './pages/Purchasing/PurchaseList';
import SalesIndex from './pages/Sales/SalesIndex';
import SalesForm from './pages/Sales/SalesForm';

// Return Forms
import ReturPembelianForm from './pages/Purchasing/ReturPembelianForm';
import ReturPenjualanForm from './pages/Sales/ReturPenjualanForm';

import './App.css';

function App() {
  return (
    <Router>
      <div className="App">
        <Routes>
          {/* Main Application Routes with Layout */}
          <Route path="/" element={<AppTailwind />}>
            <Route index element={<Navigate to="/dashboard" replace />} />
            <Route path="dashboard" element={<Dashboard />} />
            <Route path="master/categories" element={<MasterCategories />} />
            <Route path="master/customer" element={<MasterCustomers />} />
            <Route path="master/supplier" element={<MasterSuppliers />} />
            <Route path="master/barang" element={<MasterBarang />} />
            <Route path="master/sales" element={<MasterSales />} />
            
            {/* Purchasing Module - Simplified Routes */}
            <Route path="transactions/purchasing" element={<PurchasingIndex />} />
            <Route path="transactions/purchasing/form" element={<PurchaseForm />} />
            <Route path="transactions/purchasing/list" element={<PurchaseList />} />
            <Route path="transactions/purchasing/return" element={<ReturPembelianForm />} />
            <Route path="transactions/purchasing/retur-pembelian" element={<ReturPembelianForm />} />
            
            {/* Sales Module */}
            <Route path="transactions/sales" element={<SalesIndex />} />
            <Route path="transactions/sales/form" element={<SalesForm />} />
            <Route path="transactions/sales/return" element={<ReturPenjualanForm />} />
            <Route path="transactions/sales/retur-penjualan" element={<ReturPenjualanForm />} />
            
            {/* Legacy routes for backward compatibility */}
            <Route path="pembelian/form" element={<PurchaseForm />} />
            <Route path="pembelian/list" element={<PurchaseList />} />
            <Route path="retur/pembelian" element={<ReturPembelianForm />} />
            <Route path="retur/penjualan" element={<ReturPenjualanForm />} />
            {/* Add more routes here as needed */}
          </Route>
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
  );
}

export default App;
