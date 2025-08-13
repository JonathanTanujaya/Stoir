import React, { useState, useEffect } from 'react';
import '../../design-system.css';

const StokBarangReport = () => {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState({
    category: '',
    status: '',
    dateFrom: '',
    dateTo: ''
  });

  useEffect(() => {
    fetchStokBarang();
  }, []);

  const fetchStokBarang = async () => {
    try {
      setLoading(true);
      // TODO: Replace with actual API call
      // const response = await fetch('/api/reports/stok-barang');
      // const result = await response.json();
      
      // Sample data
      const sampleData = [
        {
          id: 1,
          kode_barang: 'BRG001',
          nama_barang: 'Motor Oil 10W-40',
          kategori: 'Oli & Pelumas',
          stok_awal: 100,
          stok_masuk: 50,
          stok_keluar: 30,
          stok_akhir: 120,
          harga_beli: 45000,
          harga_jual: 55000,
          nilai_stok: 5400000
        },
        {
          id: 2,
          kode_barang: 'BRG002',
          nama_barang: 'Spark Plug NGK',
          kategori: 'Spare Parts',
          stok_awal: 200,
          stok_masuk: 100,
          stok_keluar: 80,
          stok_akhir: 220,
          harga_beli: 25000,
          harga_jual: 35000,
          nilai_stok: 5500000
        }
      ];
      
      setData(sampleData);
    } catch (error) {
      console.error('Error fetching stok barang:', error);
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
        <h1 className="page-title">Laporan Stok Barang</h1>
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
          <div className="form-grid grid-cols-4">
            <div className="form-group">
              <label className="form-label">Kategori</label>
              <select 
                name="category"
                value={filters.category}
                onChange={handleFilterChange}
                className="form-control"
              >
                <option value="">Semua Kategori</option>
                <option value="oli">Oli & Pelumas</option>
                <option value="spare-parts">Spare Parts</option>
                <option value="aksesoris">Aksesoris</option>
              </select>
            </div>
            <div className="form-group">
              <label className="form-label">Status Stok</label>
              <select 
                name="status"
                value={filters.status}
                onChange={handleFilterChange}
                className="form-control"
              >
                <option value="">Semua Status</option>
                <option value="tersedia">Tersedia</option>
                <option value="minimum">Stok Minimum</option>
                <option value="habis">Habis</option>
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
              onClick={fetchStokBarang}
            >
              Apply Filter
            </button>
            <button 
              className="btn btn-secondary"
              onClick={() => setFilters({ category: '', status: '', dateFrom: '', dateTo: '' })}
            >
              Reset
            </button>
          </div>
        </div>
      </div>

      {/* Data Table */}
      <div className="card">
        <div className="card-header">
          <h3 className="card-title">Data Stok Barang</h3>
        </div>
        <div className="card-body">
          <div className="table-responsive">
            <table className="table">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Kategori</th>
                  <th>Stok Awal</th>
                  <th>Stok Masuk</th>
                  <th>Stok Keluar</th>
                  <th>Stok Akhir</th>
                  <th>Harga Beli</th>
                  <th>Harga Jual</th>
                  <th>Nilai Stok</th>
                </tr>
              </thead>
              <tbody>
                {data.map((item) => (
                  <tr key={item.id}>
                    <td>{item.kode_barang}</td>
                    <td>{item.nama_barang}</td>
                    <td>{item.kategori}</td>
                    <td className="text-right">{item.stok_awal.toLocaleString()}</td>
                    <td className="text-right">{item.stok_masuk.toLocaleString()}</td>
                    <td className="text-right">{item.stok_keluar.toLocaleString()}</td>
                    <td className="text-right">{item.stok_akhir.toLocaleString()}</td>
                    <td className="text-right">Rp {item.harga_beli.toLocaleString()}</td>
                    <td className="text-right">Rp {item.harga_jual.toLocaleString()}</td>
                    <td className="text-right">Rp {item.nilai_stok.toLocaleString()}</td>
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

export default StokBarangReport;
