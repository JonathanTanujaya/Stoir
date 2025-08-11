import React, { useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { 
  HomeIcon, 
  CubeIcon, 
  DocumentTextIcon, 
  CurrencyDollarIcon,
  ChartBarIcon,
  Bars3Icon,
  XMarkIcon,
  ChevronDownIcon
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
      path: '/dashboard'
    },
    {
      id: 'master',
      label: 'Master Data',
      icon: CubeIcon,
      hasSubmenu: true,
      submenu: [
        { label: 'Kategori Barang', path: '/master/categories' },
        { label: 'Data Barang', path: '/master/barang' },
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
    <div className={`sidebar transition-all duration-300 ${collapsed ? 'w-16' : 'w-64'}`}>
      <div className="sidebar-header">
        <div className="flex items-center justify-between">
          {!collapsed && (
            <div className="sidebar-brand">
              StockFlow
            </div>
          )}
          <button
            onClick={onToggle}
            className="btn btn-ghost p-2"
          >
            {collapsed ? <Bars3Icon className="w-5 h-5" /> : <XMarkIcon className="w-5 h-5" />}
          </button>
        </div>
      </div>

      <nav className="sidebar-nav">
        {menuItems.map((item) => (
          <div key={item.id} className="nav-item">
            {/* Main Menu Item */}
            <div
              className={`nav-link cursor-pointer ${
                item.hasSubmenu && isActiveParent(item.submenu)
                  ? 'active'
                  : isActiveMenu(item.path)
                  ? 'active'
                  : ''
              }`}
              onClick={() => {
                if (item.hasSubmenu) {
                  toggleSubmenu(item.id);
                } else {
                  navigate(item.path);
                }
              }}
            >
              <div className="flex items-center gap-3">
                <item.icon className="nav-icon" />
                {!collapsed && (
                  <span className="font-medium">{item.label}</span>
                )}
              </div>
              {!collapsed && item.hasSubmenu && (
                <ChevronDownIcon 
                  className={`w-4 h-4 transition-transform ${
                    expandedMenus[item.id] ? 'rotate-180' : ''
                  }`}
                />
              )}
            </div>

            {/* Submenu */}
            {!collapsed && item.hasSubmenu && expandedMenus[item.id] && (
              <div className="ml-8 mt-1 space-y-1">
                {item.submenu.map((subItem, index) => (
                  <div
                    key={index}
                    className={`nav-link cursor-pointer text-sm ${
                      isActiveMenu(subItem.path) ? 'active' : ''
                    }`}
                    onClick={() => navigate(subItem.path)}
                  >
                    {subItem.label}
                  </div>
                ))}
              </div>
            )}
          </div>
        ))}
      </nav>
    </div>
  );
};

export default Sidebar;
