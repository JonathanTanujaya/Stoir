import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { 
  HomeIcon, 
  CubeIcon, 
  DocumentTextIcon, 
  CurrencyDollarIcon,
  ChartBarIcon,
  Bars3Icon,
  XMarkIcon
} from '@heroicons/react/24/outline';

const Sidebar = ({ collapsed, onToggle }) => {
  const location = useLocation();
  const navigate = useNavigate();
  const [expandedMenus, setExpandedMenus] = useState({
    master: true,
    transactions: false,
    reports: false
  });

  const toggleSubmenu = (menu) => {
    setExpandedMenus(prev => ({
      ...prev,
      [menu]: !prev[menu]
    }));
  };

  const menuItems = [
    {
      id: 'dashboard',
      label: 'Dashboard',
      icon: HomeIcon,
      path: '/'
    },
    {
      id: 'master',
      label: 'Master Data',
      icon: CubeIcon,
      hasSubmenu: true,
      submenu: [
        { label: 'Kategori Barang', path: '/master/categories' },
        { label: 'Data Barang', path: '/master/sparepart' },
        { label: 'Customer', path: '/master/customer' },
        { label: 'Supplier', path: '/master/supplier' },
        { label: 'Sales', path: '/master/sales' },
      ]
    },
    {
      id: 'transactions',
      label: 'Transaksi',
      icon: DocumentTextIcon,
      hasSubmenu: true,
      submenu: [
        { label: 'Form Pembelian', path: '/transactions/purchasing/pembelian' },
        { label: 'Form Retur Pembelian', path: '/transactions/purchasing/retur-pembelian' },
        { label: 'Form Penjualan', path: '/transactions/sales/penjualan' },
        { label: 'Form Retur Penjualan', path: '/transactions/sales/retur-penjualan' },
      ]
    },
    {
      id: 'finance',
      label: 'Finance',
      icon: CurrencyDollarIcon,
      hasSubmenu: true,
      submenu: [
        { label: 'Penerimaan Warkat', path: '/finance/penerimaan-warkat' },
        { label: 'Resi Bank', path: '/finance/resi-bank' },
        { label: 'Saldo Bank', path: '/finance/saldo-bank' },
      ]
    },
    {
      id: 'reports',
      label: 'Laporan',
      icon: ChartBarIcon,
      hasSubmenu: true,
      submenu: [
        { label: 'Laporan Stok Barang', path: '/reports/inventory/stok-barang' },
        { label: 'Kartu Stok', path: '/reports/inventory/kartu-stok' },
        { label: 'View COGS', path: '/reports/inventory/view-cogs' },
        { label: 'View Penjualan', path: '/reports/sales/view-penjualan' },
      ]
    }
  ];

  const isActiveMenu = (path) => {
    return location.pathname === path;
  };

  const isActiveParent = (submenu) => {
    return submenu.some(item => location.pathname.startsWith(item.path.split('/').slice(0, 2).join('/')));
  };

  return (
    <div className={`fixed left-0 top-0 h-full bg-gradient-sidebar backdrop-blur-md shadow-custom transition-all duration-300 z-50 ${
      collapsed ? 'w-16' : 'w-72'
    }`}>
      <div className="p-5">
        {/* Logo Area */}
        <div className="flex items-center justify-between mb-8">
          {!collapsed && (
            <div className="text-center">
              <h1 className="text-xl font-bold text-brand-600">StockFlow</h1>
              <p className="text-xs text-gray-500 mt-1">MANAGEMENT SYSTEM</p>
            </div>
          )}
          <button
            onClick={onToggle}
            className="p-2 rounded-lg hover:bg-white/20 transition-colors"
          >
            {collapsed ? <Bars3Icon className="w-5 h-5" /> : <XMarkIcon className="w-5 h-5" />}
          </button>
        </div>

        {/* Navigation Menu */}
        <nav className="space-y-2">
          {menuItems.map((item) => (
            <div key={item.id}>
              {/* Main Menu Item */}
              <div
                className={`sidebar-item ${
                  item.hasSubmenu 
                    ? (isActiveParent(item.submenu) ? 'active' : '')
                    : (isActiveMenu(item.path) ? 'active' : '')
                }`}
                onClick={() => {
                  if (item.hasSubmenu) {
                    toggleSubmenu(item.id);
                  } else {
                    navigate(item.path);
                  }
                }}
              >
                <item.icon className="w-5 h-5 mr-3" />
                {!collapsed && (
                  <>
                    <span className="flex-1">{item.label}</span>
                    {item.hasSubmenu && (
                      <svg
                        className={`w-4 h-4 transition-transform ${
                          expandedMenus[item.id] ? 'rotate-90' : ''
                        }`}
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                      >
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                      </svg>
                    )}
                  </>
                )}
              </div>

              {/* Submenu */}
              {item.hasSubmenu && !collapsed && expandedMenus[item.id] && (
                <div className="ml-4 mt-3 space-y-2">
                  {item.submenu.map((subItem, index) => (
                    <div
                      key={index}
                      className={`flex items-center px-4 py-3 rounded-xl text-sm cursor-pointer transition-all duration-300 font-medium ${
                        isActiveMenu(subItem.path)
                          ? 'bg-gradient-to-r from-white/30 to-white/20 text-white font-bold shadow-lg border border-white/20'
                          : 'text-gray-300 hover:bg-white/15 hover:text-white hover:shadow-md border border-transparent hover:border-white/10'
                      }`}
                      onClick={() => navigate(subItem.path)}
                    >
                      <div className="w-3 h-3 rounded-full bg-current opacity-80 mr-3 shadow-sm"></div>
                      {subItem.label}
                    </div>
                  ))}
                </div>
              )}
            </div>
          ))}
        </nav>
      </div>
    </div>
  );
};

export default Sidebar;
