import React from 'react';
import { PlusIcon, ChevronRightIcon } from '@heroicons/react/24/outline';

const PageHeader = ({ 
  title, 
  subtitle, 
  breadcrumb, 
  onAdd, 
  addButtonText = "Tambah",
  showAddButton = true 
}) => {
  return (
    <div className="page-header">
      <div className="header-content">
        <div>
          {breadcrumb && (
            <nav className="breadcrumb">
              {breadcrumb.map((item, index) => (
                <span key={index} className="flex items-center">
                  {index > 0 && <ChevronRightIcon className="w-4 h-4 breadcrumb-separator" />}
                  <span className={`${index === breadcrumb.length - 1 ? 'text-gray-900 font-medium' : 'text-gray-500'}`}>
                    {item}
                  </span>
                </span>
              ))}
            </nav>
          )}
          <h1 className="header-title">{title}</h1>
          {subtitle && (
            <p className="text-gray-600 mt-1">{subtitle}</p>
          )}
        </div>
        
        {showAddButton && onAdd && (
          <button
            onClick={onAdd}
            className="btn btn-primary"
          >
            <PlusIcon className="w-5 h-5" />
            {addButtonText}
          </button>
        )}
      </div>
    </div>
  );
};

export default PageHeader;
