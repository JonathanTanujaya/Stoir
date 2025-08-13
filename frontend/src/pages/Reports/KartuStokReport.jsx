import React, { useState, useEffect } from 'react';
import '../../design-system.css';

const KartuStokReport = () => {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState({
    barang: '',
    dateFrom: '',
    dateTo: ''
  });

  useEffect(() => {
    fetchKartuStok();
  }, []);

  const fetchKartuStok = async () => {
    try {
      setLoading(true);
      // TODO: Replace with actual API call
      // const response = await fetch('/api/reports/kartu-stok');
      // const result = await response.json();
      
      // Sample data
      const sampleData = [
        {
          id: 1,
          tanggal: '2025-01-01',
          keterangan: 'Stok Awal',
          referensi: '-',
          masuk: 0,
          keluar: 0,
          saldo: 100,
          harga: 45000,
          nilai: 4500000
        },
        {
          id: 2,
          tanggal: '2025-01-05',
          keterangan: 'Pembelian',
          referensi: 'PO-001',
          masuk: 50,
          keluar: 0,
          saldo: 150,
          harga: 45000,
          nilai: 6750000
        },
        {
          id: 3,
          tanggal: '2025-01-10',
          keterangan: 'Penjualan',
          referensi: 'SO-001',
          masuk: 0,
          keluar: 30,
          saldo: 120,
          harga: 45000,
          nilai: 5400000
        }
      ];
      
      setData(sampleData);
    } catch (error) {
      console.error('Error fetching kartu stok:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleFilterChange = (e) => {
    const { name, value } = e.target;
    setFilters(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleExport = () => {
    // TODO: Implement export functionality
    alert('Export functionality will be implemented');
  };

  const handlePrint = () => {
    window.print();
  };

  if (loading) {
    return (
      <div className="page-container">
        <div className="loading-spinner">Loading...</div>
      </div>
    );
  }

  return (
    <div className="page-container">
      <div className="page-header">
        <h1 className="page-title">Laporan Kartu Stok</h1>
        <div className="page-actions">
          <button 
            className="btn btn-secondary"
            onClick={handlePrint}
          >
            Print
          </button>
          <button 
            className="btn btn-primary"
            onClick={handleExport}
          >
            Export Excel
          </button>
        </div>
      </div>

      {/* Filters */}
      <div className="card mb-4">
        <div className="card-header">
          <h3 className="card-title">Filter</h3>
        </div>
        <div className="card-body">
          <div className="form-grid grid-cols-3">
            <div className="form-group">
              <label className="form-label">Barang</label>
              <select 
                name="barang"
                value={filters.barang}
                onChange={handleFilterChange}
                className="form-control"
              >
                <option value="">Pilih Barang</option>
                <option value="BRG001">BRG001 - Motor Oil 10W-40</option>
                <option value="BRG002">BRG002 - Spark Plug NGK</option>
                <option value="BRG003">BRG003 - Air Filter</option>
              </select>
            </div>
            <div className="form-group">
              <label className="form-label">Tanggal Dari</label>
              <input 
                type="date"
                name="dateFrom"
                value={filters.dateFrom}
                onChange={handleFilterChange}
                className="form-control"
              />
            </div>
            <div className="form-group">
              <label className="form-label">Tanggal Sampai</label>
              <input 
                type="date"
                name="dateTo"
                value={filters.dateTo}
                onChange={handleFilterChange}
                className="form-control"
              />
            </div>
          </div>
          <div className="form-actions">
            <button 
              className="btn btn-primary"
              onClick={fetchKartuStok}
            >
              Apply Filter
            </button>
            <button 
              className="btn btn-secondary"
              onClick={() => setFilters({ barang: '', dateFrom: '', dateTo: '' })}
            >
              Reset
            </button>
          </div>
        </div>
      </div>

      {/* Current Stock Info */}
      <div className="card mb-4">
        <div className="card-header">
          <h3 className="card-title">Informasi Barang</h3>
        </div>
        <div className="card-body">
          <div className="form-grid grid-cols-4">
            <div className="form-group">
              <label className="form-label">Kode Barang</label>
              <p className="form-text">BRG001</p>
            </div>
            <div className="form-group">
              <label className="form-label">Nama Barang</label>
              <p className="form-text">Motor Oil 10W-40</p>
            </div>
            <div className="form-group">
              <label className="form-label">Satuan</label>
              <p className="form-text">Botol</p>
            </div>
            <div className="form-group">
              <label className="form-label">Stok Saat Ini</label>
              <p className="form-text font-bold">120 Botol</p>
            </div>
          </div>
        </div>
      </div>

      {/* Data Table */}
      <div className="card">
        <div className="card-header">
          <h3 className="card-title">Mutasi Stok</h3>
        </div>
        <div className="card-body">
          <div className="table-responsive">
            <table className="table">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Keterangan</th>
                  <th>Referensi</th>
                  <th>Masuk</th>
                  <th>Keluar</th>
                  <th>Saldo</th>
                  <th>Harga</th>
                  <th>Nilai</th>
                </tr>
              </thead>
              <tbody>
                {data.map((item) => (
                  <tr key={item.id}>
                    <td>{new Date(item.tanggal).toLocaleDateString('id-ID')}</td>
                    <td>{item.keterangan}</td>
                    <td>{item.referensi}</td>
                    <td className="text-right">{item.masuk > 0 ? item.masuk.toLocaleString() : '-'}</td>
                    <td className="text-right">{item.keluar > 0 ? item.keluar.toLocaleString() : '-'}</td>
                    <td className="text-right font-bold">{item.saldo.toLocaleString()}</td>
                    <td className="text-right">Rp {item.harga.toLocaleString()}</td>
                    <td className="text-right">Rp {item.nilai.toLocaleString()}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};

export default KartuStokReport;
