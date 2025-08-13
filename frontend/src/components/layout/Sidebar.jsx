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
        { label: 'Kategori', path: '/master/kategori' },
        { label: 'Sparepart', path: '/master/sparepart' },
        { label: 'Stock Min', path: '/master/stock-min' },
        { label: 'Checklist', path: '/master/checklist' },
        { label: 'Area', path: '/master/area' },
        { label: 'Sales', path: '/master/sales' },
        { label: 'Supplier', path: '/master/supplier' },
        { label: 'Customer', path: '/master/customer' },
        { label: 'Bank', path: '/master/bank' },
        { label: 'Rekening', path: '/master/rekening' },
      ]
    },
    {
      id: 'transactions',
      label: 'Transaksi',
      icon: DocumentTextIcon,
      hasSubmenu: true,
      submenu: [
        { label: 'Pembelian', path: '/transactions/pembelian' },
        { label: 'Retur Pembelian', path: '/transactions/retur-pembelian' },
        { label: 'Penjualan', path: '/transactions/penjualan' },
        { label: 'Merge Barang', path: '/transactions/merge-barang' },
        { label: 'Retur Penjualan', path: '/transactions/retur-penjualan' },
        { label: 'Invoice Cancel', path: '/transactions/invoice-cancel' },
        { label: 'Stok Opname', path: '/transactions/stok-opname' },
        { label: 'Pembelian Bonus', path: '/transactions/pembelian-bonus' },
        { label: 'Penjualan Bonus', path: '/transactions/penjualan-bonus' },
        { label: 'Customer Claim', path: '/transactions/customer-claim' },
        { label: 'Pengembalian Claim', path: '/transactions/pengembalian-claim' },
      ]
    },
    {
      id: 'finance',
      label: 'Finance',
      icon: CurrencyDollarIcon,
      hasSubmenu: true,
      submenu: [
        { label: 'Penerimaan Giro', path: '/finance/penerimaan-giro' },
        { label: 'Pencarian Giro', path: '/finance/pencarian-giro' },
        { label: 'Penerimaan Resi', path: '/finance/penerimaan-resi' },
        { label: 'Piutang Resi', path: '/finance/piutang-resi' },
        { label: 'Piutang Retur', path: '/finance/piutang-retur' },
        { label: 'Penambahan Saldo', path: '/finance/penambahan-saldo' },
        { label: 'Pengurangan Saldo', path: '/finance/pengurangan-saldo' },
      ]
    },
    {
      id: 'reports',
      label: 'Laporan',
      icon: ChartBarIcon,
      hasSubmenu: true,
      submenu: [
        { label: 'Stok Barang', path: '/reports/stok-barang' },
        { label: 'Kartu Stok', path: '/reports/kartu-stok' },
        { label: 'Pembelian', path: '/reports/pembelian' },
        { label: 'Pembelian Item', path: '/reports/pembelian-item' },
        { label: 'Penjualan', path: '/reports/penjualan' },
        { label: 'COGS', path: '/reports/cogs' },
        { label: 'Return Sales', path: '/reports/return-sales' },
        { label: 'Tampil Invoice', path: '/reports/tampil-invoice' },
        { label: 'Saldo Rekening', path: '/reports/saldo-rekening' },
        { label: 'Pembayaran Customer', path: '/reports/pembayaran-customer' },
        { label: 'Tagihan', path: '/reports/tagihan' },
        { label: 'Pemotongan Return Customer', path: '/reports/pemotongan-return-customer' },
        { label: 'Komisi Sales', path: '/reports/komisi-sales' },
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
