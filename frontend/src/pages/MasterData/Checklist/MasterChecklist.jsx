import React from 'react';
import '../../../design-system.css';

const MasterChecklist = () => {
  return (
    <div className="page-container">
      <div className="page-header">
        <h1 className="page-title">Master Checklist</h1>
        <p className="page-subtitle">Daftar checklist untuk berbagai proses bisnis</p>
      </div>

      <div className="card">
        <div className="card-body">
          <div className="under-construction">
            <h3>🚧 Under Construction</h3>
            <p>Halaman ini sedang dalam tahap pengembangan.</p>
            <p>Fitur checklist akan segera tersedia untuk:</p>
            <ul style={{ textAlign: 'left', display: 'inline-block' }}>
              <li>Checklist pemeriksaan barang</li>
              <li>Checklist pengiriman</li>
              <li>Checklist quality control</li>
              <li>Checklist maintenance</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
};

export default MasterChecklist;
