import React from 'react';
import { Link, useLocation } from 'react-router-dom';

function Breadcrumb() {
  const location = useLocation();
  const pathnames = location.pathname.split('/').filter(x => x);

  const breadcrumbNameMap = {
    'transactions': 'Transaksi',
    'purchasing': 'Pembelian',
    'pembelian': 'Pembelian',
    'form': 'Form Baru',
    'list': 'Daftar',
    'master': 'Master Data',
    'categories': 'Kategori',
    'customer': 'Customer',
    'supplier': 'Supplier',
    'barang': 'Barang',
    'sales': 'Sales',
    'dashboard': 'Dashboard'
  };

  const buildPath = (index) => {
    return '/' + pathnames.slice(0, index + 1).join('/');
  };

  if (pathnames.length === 0) return null;

  return (
    <nav className="breadcrumb" aria-label="Breadcrumb">
      <ol className="breadcrumb-list">
        <li className="breadcrumb-item">
          <Link to="/" className="breadcrumb-link">🏠 Home</Link>
        </li>
        {pathnames.map((name, index) => {
          const routeTo = buildPath(index);
          const isLast = index === pathnames.length - 1;
          const displayName = breadcrumbNameMap[name] || name;

          return (
            <li key={name} className="breadcrumb-item">
              <span className="breadcrumb-separator">›</span>
              {isLast ? (
                <span className="breadcrumb-current">{displayName}</span>
              ) : (
                <Link to={routeTo} className="breadcrumb-link">
                  {displayName}
                </Link>
              )}
            </li>
          );
        })}
      </ol>
    </nav>
  );
}

export default Breadcrumb;
