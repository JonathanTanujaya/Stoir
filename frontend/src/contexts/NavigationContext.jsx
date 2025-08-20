import React, { createContext, useContext, useState, useEffect } from 'react';
import { useLocation } from 'react-router-dom';

// Navigation Context
const NavigationContext = createContext();

// Navigation categories and their items
export const navigationConfig = {
  dashboard: {
    id: 'dashboard',
    label: 'Dashboard',
    icon: 'HomeIcon',
    path: '/dashboard',
    color: 'blue',
    items: []
  },
  master: {
    id: 'master',
    label: 'Master Data',
    icon: 'CubeIcon',
    color: 'indigo',
    items: [
      { label: 'Kategori', path: '/master/kategori', icon: 'TagIcon' },
      { label: 'Sparepart', path: '/master/sparepart', icon: 'CogIcon' },
      { label: 'Stock Min', path: '/master/stock-min', icon: 'ExclamationTriangleIcon' },
      { label: 'Checklist', path: '/master/checklist', icon: 'CheckCircleIcon' },
      { label: 'Area', path: '/master/area', icon: 'MapPinIcon' },
      { label: 'Sales', path: '/master/sales', icon: 'UserGroupIcon' },
      { label: 'Supplier', path: '/master/supplier', icon: 'TruckIcon' },
      { label: 'Customer', path: '/master/customer', icon: 'UsersIcon' },
      { label: 'Bank', path: '/master/bank', icon: 'BuildingLibraryIcon' },
      { label: 'Rekening', path: '/master/rekening', icon: 'CreditCardIcon' }
    ]
  },
  transactions: {
    id: 'transactions',
    label: 'Transaksi',
    icon: 'DocumentTextIcon',
    color: 'green',
    items: [
      { label: 'Pembelian', path: '/transactions/pembelian', icon: 'ShoppingCartIcon' },
      { label: 'Retur Pembelian', path: '/transactions/retur-pembelian', icon: 'ArrowUturnLeftIcon' },
      { label: 'Penjualan', path: '/transactions/penjualan', icon: 'CurrencyDollarIcon' },
      { label: 'Merge Barang', path: '/transactions/merge-barang', icon: 'ArrowsPointingInIcon' },
      { label: 'Retur Penjualan', path: '/transactions/retur-penjualan', icon: 'ArrowUturnRightIcon' },
      { label: 'Invoice Cancel', path: '/transactions/invoice-cancel', icon: 'XCircleIcon' },
      { label: 'Stok Opname', path: '/transactions/stok-opname', icon: 'ClipboardDocumentCheckIcon' },
      { label: 'Pembelian Bonus', path: '/transactions/pembelian-bonus', icon: 'GiftIcon' },
      { label: 'Penjualan Bonus', path: '/transactions/penjualan-bonus', icon: 'SparklesIcon' },
      { label: 'Customer Claim', path: '/transactions/customer-claim', icon: 'ExclamationCircleIcon' },
      { label: 'Pengembalian Claim', path: '/transactions/pengembalian-claim', icon: 'ArrowPathIcon' }
    ]
  },
  finance: {
    id: 'finance',
    label: 'Finance',
    icon: 'CurrencyDollarIcon',
    color: 'yellow',
    items: [
      { label: 'Penerimaan Giro', path: '/finance/penerimaan-giro', icon: 'DocumentCheckIcon' },
      { label: 'Pencarian Giro', path: '/finance/pencarian-giro', icon: 'MagnifyingGlassIcon' },
      { label: 'Penerimaan Resi', path: '/finance/penerimaan-resi', icon: 'InboxIcon' },
      { label: 'Piutang Resi', path: '/finance/piutang-resi', icon: 'ClockIcon' },
      { label: 'Piutang Retur', path: '/finance/piutang-retur', icon: 'ArrowPathIcon' },
      { label: 'Penambahan Saldo', path: '/finance/penambahan-saldo', icon: 'PlusCircleIcon' },
      { label: 'Pengurangan Saldo', path: '/finance/pengurangan-saldo', icon: 'MinusCircleIcon' }
    ]
  },
  reports: {
    id: 'reports',
    label: 'Laporan',
    icon: 'ChartBarIcon',
    color: 'purple',
    items: [
      { label: 'Stok Barang', path: '/reports/stok-barang', icon: 'CubeIcon' },
      { label: 'Kartu Stok', path: '/reports/kartu-stok', icon: 'IdentificationIcon' },
      { label: 'Pembelian', path: '/reports/pembelian', icon: 'ShoppingBagIcon' },
      { label: 'Pembelian Item', path: '/reports/pembelian-item', icon: 'ListBulletIcon' },
      { label: 'Penjualan', path: '/reports/penjualan', icon: 'BanknotesIcon' },
      { label: 'COGS', path: '/reports/cogs', icon: 'CalculatorIcon' },
      { label: 'Return Sales', path: '/reports/return-sales', icon: 'ArrowUturnDownIcon' },
      { label: 'Tampil Invoice', path: '/reports/tampil-invoice', icon: 'DocumentDuplicateIcon' },
      { label: 'Saldo Rekening', path: '/reports/saldo-rekening', icon: 'ScaleIcon' },
      { label: 'Pembayaran Customer', path: '/reports/pembayaran-customer', icon: 'CreditCardIcon' },
      { label: 'Tagihan', path: '/reports/tagihan', icon: 'DocumentTextIcon' },
      { label: 'Pemotongan Return Customer', path: '/reports/pemotongan-return-customer', icon: 'ScissorsIcon' },
      { label: 'Komisi Sales', path: '/reports/komisi-sales', icon: 'TrophyIcon' }
    ]
  }
};

// Custom hook for navigation
export const useNavigation = () => {
  const context = useContext(NavigationContext);
  if (!context) {
    throw new Error('useNavigation must be used within NavigationProvider');
  }
  return context;
};

// Navigation Provider Component
export const NavigationProvider = ({ children }) => {
  const location = useLocation();
  const [activeCategory, setActiveCategory] = useState('dashboard');
  const [recentItems, setRecentItems] = useState([]);
  const [favoriteItems, setFavoriteItems] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [commandPaletteOpen, setCommandPaletteOpen] = useState(false);

  // Auto-detect active category based on current route
  useEffect(() => {
    const pathSegments = location.pathname.split('/');
    const categoryFromPath = pathSegments[1];
    
    if (navigationConfig[categoryFromPath]) {
      setActiveCategory(categoryFromPath);
    } else if (location.pathname === '/' || location.pathname === '/dashboard') {
      setActiveCategory('dashboard');
    }
  }, [location.pathname]);

  // Add to recent items when navigating
  useEffect(() => {
    const currentPath = location.pathname;
    if (currentPath !== '/' && currentPath !== '/dashboard') {
      // Find the menu item
      const allItems = Object.values(navigationConfig).flatMap(cat => cat.items || []);
      const currentItem = allItems.find(item => item.path === currentPath);
      
      if (currentItem) {
        setRecentItems(prev => {
          const filtered = prev.filter(item => item.path !== currentPath);
          return [{ ...currentItem, timestamp: Date.now() }, ...filtered].slice(0, 5);
        });
      }
    }
  }, [location.pathname]);

  // Toggle favorite item
  const toggleFavorite = (item) => {
    setFavoriteItems(prev => {
      const exists = prev.find(fav => fav.path === item.path);
      if (exists) {
        return prev.filter(fav => fav.path !== item.path);
      } else {
        return [...prev, item].slice(0, 8); // Max 8 favorites
      }
    });
  };

  // Search functionality
  const searchItems = (query) => {
    if (!query.trim()) return [];
    
    const allItems = Object.values(navigationConfig).flatMap(cat => {
      const categoryItems = cat.items || [];
      return [
        { ...cat, type: 'category' },
        ...categoryItems.map(item => ({ ...item, category: cat.label, type: 'item' }))
      ];
    });

    return allItems.filter(item =>
      item.label.toLowerCase().includes(query.toLowerCase()) ||
      (item.category && item.category.toLowerCase().includes(query.toLowerCase()))
    );
  };

  // Keyboard shortcuts
  useEffect(() => {
    const handleKeyDown = (e) => {
      // Ctrl+K or Cmd+K for command palette
      if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        setCommandPaletteOpen(true);
      }
      
      // Escape to close command palette
      if (e.key === 'Escape') {
        setCommandPaletteOpen(false);
        setSearchQuery('');
      }
    };

    document.addEventListener('keydown', handleKeyDown);
    return () => document.removeEventListener('keydown', handleKeyDown);
  }, []);

  const value = {
    // State
    activeCategory,
    recentItems,
    favoriteItems,
    searchQuery,
    commandPaletteOpen,
    
    // Actions
    setActiveCategory,
    setSearchQuery,
    setCommandPaletteOpen,
    toggleFavorite,
    searchItems,
    
    // Config
    navigationConfig,
    
    // Computed
    activeItems: navigationConfig[activeCategory]?.items || [],
    activeCategoryConfig: navigationConfig[activeCategory]
  };

  return (
    <NavigationContext.Provider value={value}>
      {children}
    </NavigationContext.Provider>
  );
};

export default NavigationProvider;
