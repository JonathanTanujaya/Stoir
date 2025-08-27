import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import CssBaseline from '@mui/material/CssBaseline';
import ModernLayout from './components/Layout/ModernLayout';

// Create MUI theme
const theme = createTheme({
  palette: {
    mode: 'light',
    primary: {
      main: '#3B82F6',
      50: '#EFF6FF',
      100: '#DBEAFE',
      700: '#1D4ED8',
    },
    background: {
      default: '#F8FAFC',
      paper: '#FFFFFF',
    },
    text: {
      primary: '#1F2937',
      secondary: '#6B7280',
    },
  },
  typography: {
    fontFamily: '"Inter", "Roboto", "Helvetica", "Arial", sans-serif',
    h6: {
      fontWeight: 600,
    },
  },
  shape: {
    borderRadius: 8,
  },
  components: {
    MuiButton: {
      styleOverrides: {
        root: {
          textTransform: 'none',
          borderRadius: 6,
        },
      },
    },
    MuiChip: {
      styleOverrides: {
        root: {
          borderRadius: 6,
        },
      },
    },
  },
});

// Demo page components
const DashboardPage = () => (
  <div>
    <h1>Dashboard</h1>
    <p>Welcome to the new navigation system!</p>
  </div>
);

const MasterKategoriPage = () => (
  <div>
    <h1>Kategori</h1>
    <p>Master data kategori page</p>
  </div>
);

const TransactionPembelianPage = () => (
  <div>
    <h1>Pembelian</h1>
    <p>Transaction pembelian page</p>
  </div>
);

const ReportsStokPage = () => (
  <div>
    <h1>Stok Barang</h1>
    <p>Reports stok barang page</p>
  </div>
);

function AppWithModernNavigation() {
  return (
    <ThemeProvider theme={theme}>
      <CssBaseline />
      <Router>
        <ModernLayout>
          <Routes>
            <Route path="/" element={<DashboardPage />} />
            <Route path="/dashboard" element={<DashboardPage />} />
            <Route path="/master/kategori" element={<MasterKategoriPage />} />
            <Route path="/transactions/pembelian" element={<TransactionPembelianPage />} />
            <Route path="/reports/stok-barang" element={<ReportsStokPage />} />
            {/* Add all your existing routes here */}
          </Routes>
        </ModernLayout>
      </Router>
    </ThemeProvider>
  );
}

export default AppWithModernNavigation;
