import React from 'react';
import { PlusIcon } from '@heroicons/react/24/outline';

const PageHeader = ({ 
  title, 
  subtitle, 
  breadcrumb, 
  onAdd, 
  addButtonText = "Tambah",
  showAddButton = true 
}) => {
  return (
    <div className="bg-white/90 backdrop-blur-sm rounded-2xl shadow-card p-6 mb-6">
      <div className="flex items-center justify-between">
        <div>
          {breadcrumb && (
            <nav className="text-sm text-gray-500 mb-2">
              {breadcrumb.map((item, index) => (
                <span key={index}>
                  {index > 0 && ' > '}
                  <span className={index === breadcrumb.length - 1 ? 'text-gray-700' : ''}>
                    {item}
                  </span>
                </span>
              ))}
            </nav>
          )}
          <h1 className="text-2xl font-bold text-gray-900">{title}</h1>
          {subtitle && (
            <p className="text-gray-600 mt-1">{subtitle}</p>
          )}
        </div>
        
        {showAddButton && (
          <button
            onClick={onAdd}
            className="btn-primary flex items-center gap-2"
          >
            <PlusIcon className="w-4 h-4" />
            {addButtonText}
          </button>
        )}
      </div>
    </div>
  );
};

export default PageHeader;
