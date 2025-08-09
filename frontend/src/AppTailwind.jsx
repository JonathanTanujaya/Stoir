import React, { useState } from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Sidebar from './components/Layout/Sidebar';
import MainContent from './components/Layout/MainContent';

// Import New Pages
import Dashboard from './pages/Dashboard/Dashboard';
import MasterCategories from './pages/MasterData/Categories/MasterCategories';

// Import existing pages for backward compatibility
import CustomerPage from './components/CustomerPage.jsx';
import SalesPage from './components/SalesPage.jsx';
import BarangPage from './components/BarangPage.jsx';
import SupplierPage from './components/SupplierPage.jsx';

function App() {
  const [sidebarCollapsed, setSidebarCollapsed] = useState(false);

  return (
    <Router>
      <div className="min-h-screen bg-gradient-brand">
        <div className="flex h-screen">
          {/* Sidebar */}
          <Sidebar 
            collapsed={sidebarCollapsed} 
            onToggle={() => setSidebarCollapsed(!sidebarCollapsed)} 
          />
          
          {/* Main Content */}
          <div className={`flex-1 transition-all duration-300 ${sidebarCollapsed ? 'ml-16' : 'ml-72'}`}>
            <MainContent>
              <Routes>
                {/* Dashboard */}
                <Route path="/" element={<Dashboard />} />
                
                {/* Master Data Routes */}
                <Route path="/master/categories" element={<MasterCategories />} />
                <Route path="/master/customer" element={<CustomerPage />} />
                <Route path="/master/sparepart" element={<BarangPage />} />
                <Route path="/master/supplier" element={<SupplierPage />} />
                <Route path="/master/sales" element={<SalesPage />} />
                
                {/* Temporary routes for existing pages */}
                <Route path="/customers" element={<CustomerPage />} />
                <Route path="/barang" element={<BarangPage />} />
                <Route path="/suppliers" element={<SupplierPage />} />
                <Route path="/sales" element={<SalesPage />} />
              </Routes>
            </MainContent>
          </div>
        </div>
      </div>
    </Router>
  );
}

export default App;
