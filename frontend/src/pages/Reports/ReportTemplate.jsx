import React from 'react';
import '../../design-system.css';

const ReportTemplate = ({ title, children, onPrint, onExport }) => {
  return (
    <div className="page-container">
      <div className="page-header">
        <h1 className="page-title">{title}</h1>
        <div className="page-actions">
          <button 
            className="btn btn-secondary"
            onClick={onPrint || (() => window.print())}
          >
            Print
          </button>
          <button 
            className="btn btn-primary"
            onClick={onExport || (() => alert('Export functionality will be implemented'))}
          >
            Export Excel
          </button>
        </div>
      </div>
      {children}
      <div className="report-info">
        <p className="text-muted">
          Laporan ini dibuat secara otomatis oleh sistem pada {new Date().toLocaleDateString('id-ID')} {new Date().toLocaleTimeString('id-ID')}
        </p>
      </div>
    </div>
  );
};

export default ReportTemplate;
