import React, { useState, useEffect } from 'react';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon } from '@heroicons/react/24/outline';
import { salesAPI } from '../../../services/api';

const MasterSales = () => {
  const [sales, setSales] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [formData, setFormData] = useState({
    namasales: '',
    kodesales: '',
    kodedivisi: '',
    alamat: '',
    nohp: '',
    target: '',
    status: 'Aktif'
  });
  const [editingId, setEditingId] = useState(null);

  const itemsPerPage = 10;

  useEffect(() => {
    fetchSales();
  }, []);

  const fetchSales = async () => {
    try {
      setLoading(true);
      console.log('🔄 Fetching sales from:', 'http://localhost:8000/api/sales');
      
      const response = await salesAPI.getAll();
      console.log('📊 Sales API Full Response:', response);
      console.log('📊 Sales API Response Data:', response.data);
      console.log('📊 Sales Array:', response.data?.data);
      
      // Laravel returns data in response.data.data format
      const salesData = response.data?.data || [];
      console.log('📊 Final Sales Data:', salesData);
      console.log('📊 Sales Data Type:', typeof salesData);
      console.log('📊 Is Array:', Array.isArray(salesData));
      
      setSales(salesData);
      console.log('✅ Sales state set successfully');
    } catch (error) {
      console.error('❌ Error fetching sales:', error);
      console.error('❌ Error message:', error.message);
      console.error('❌ Error response:', error.response);
      console.error('❌ Error status:', error.response?.status);
      console.error('❌ Error data:', error.response?.data);
      
      // Set empty array on error
      setSales([]);
    } finally {
      setLoading(false);
      console.log('🔄 Loading set to false');
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editingId) {
        await salesAPI.update(editingId, formData);
      } else {
        await salesAPI.create(formData);
      }
      
      setFormData({
        namasales: '',
        kodesales: '',
        kodedivisi: '',
        alamat: '',
        nohp: '',
        target: '',
        status: 'Aktif'
      });
      setEditingId(null);
      fetchSales();
    } catch (error) {
      console.error('Error saving sales:', error);
    }
  };

  const handleEdit = (sales) => {
    setFormData({
      namasales: sales.namasales || '',
      kodesales: sales.kodesales || '',
      kodedivisi: sales.kodedivisi || '',
      alamat: sales.alamat || '',
      nohp: sales.nohp || '',
      target: sales.target || '',
      status: sales.status === true || sales.status === 'Aktif' || sales.status === 1 ? 'Aktif' : 'Nonaktif'
    });
    setEditingId(sales.kodedivisi + '|' + sales.kodesales); // Composite key
  };

  const handleDelete = async (kodeDivisi, kodeSales) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus sales ini?')) {
      try {
        await salesAPI.delete(`${kodeDivisi}/${kodeSales}`);
        fetchSales();
      } catch (error) {
        console.error('Error deleting sales:', error);
      }
    }
  };

  const handleReset = () => {
    setFormData({
      namasales: '',
      kodesales: '',
      kodedivisi: '',
      alamat: '',
      nohp: '',
      target: '',
      status: 'Aktif'
    });
    setEditingId(null);
  };

  const filteredSales = sales.filter(sales =>
    (sales.namasales || '').toLowerCase().includes(searchTerm.toLowerCase()) ||
    (sales.kodesales || '').toLowerCase().includes(searchTerm.toLowerCase()) ||
    (sales.kodedivisi || '').toLowerCase().includes(searchTerm.toLowerCase())
  );

  const totalPages = Math.ceil(filteredSales.length / itemsPerPage);
  const startIndex = (currentPage - 1) * itemsPerPage;
  const currentData = filteredSales.slice(startIndex, startIndex + itemsPerPage);

  if (loading) {
    return (
      <div className="page-content">
        <div className="loading">
          <div className="loading-spinner"></div>
          <span className="ml-3">Memuat data...</span>
        </div>
      </div>
    );
  }

  return (
    <div className="page-content">
      <div className="content-wrapper">
        <PageHeader 
          title="Master Sales"
          subtitle="Kelola data sales/marketing"
          breadcrumb={['Master Data', 'Sales']}
          showAddButton={false}
        />

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Form Panel */}
          <div className="lg:col-span-1">
            <div className="card p-6">
              <h2 className="text-lg font-semibold text-gray-900 mb-6">
                {editingId ? 'Edit Sales' : 'Tambah Sales'}
              </h2>
              
              <form onSubmit={handleSubmit} className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Nama Sales
                  </label>
                  <input
                    type="text"
                    className="input"
                    placeholder="Masukkan nama sales"
                    value={formData.namasales}
                    onChange={(e) => setFormData({...formData, namasales: e.target.value})}
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Kode Sales
                  </label>
                  <input
                    type="text"
                    className={`input ${!editingId ? 'bg-gray-100' : ''}`}
                    placeholder="Masukkan kode sales"
                    value={formData.kodesales}
                    onChange={(e) => setFormData({...formData, kodesales: e.target.value})}
                    disabled={!editingId}
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Kode Divisi
                  </label>
                  <input
                    type="text"
                    className="input"
                    placeholder="Masukkan kode divisi"
                    value={formData.kodedivisi}
                    onChange={(e) => setFormData({...formData, kodedivisi: e.target.value})}
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    No HP
                  </label>
                  <input
                    type="tel"
                    className="input"
                    placeholder="Masukkan nomor HP"
                    value={formData.nohp}
                    onChange={(e) => setFormData({...formData, nohp: e.target.value})}
                    required
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Alamat
                  </label>
                  <textarea
                    className="input h-20 resize-none"
                    placeholder="Masukkan alamat"
                    value={formData.alamat}
                    onChange={(e) => setFormData({...formData, alamat: e.target.value})}
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Target
                  </label>
                  <input
                    type="number"
                    className="input"
                    placeholder="Masukkan target"
                    value={formData.target}
                    onChange={(e) => setFormData({...formData, target: e.target.value})}
                  />
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Status
                  </label>
                  <select
                    className="input"
                    value={formData.status}
                    onChange={(e) => setFormData({...formData, status: e.target.value})}
                  >
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                  </select>
                </div>

                <div className="flex gap-3 pt-6">
                  <button type="submit" className="btn btn-primary flex-1">
                    {editingId ? 'Update' : 'Simpan'}
                  </button>
                  <button type="button" onClick={handleReset} className="btn btn-secondary">
                    Reset
                  </button>
                </div>
              </form>
            </div>
          </div>

          {/* Table Panel */}
          <div className="lg:col-span-2">
            <div className="table-container">
              <div className="table-header">
                <h2 className="table-title">Daftar Sales</h2>
                <div className="flex items-center gap-4">
                  <div className="relative">
                    <MagnifyingGlassIcon className="w-5 h-5 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                    <input
                      type="text"
                      className="input pl-10 w-64"
                      placeholder="Cari sales..."
                      value={searchTerm}
                      onChange={(e) => setSearchTerm(e.target.value)}
                    />
                  </div>
                </div>
              </div>
              
              <div className="table-wrapper">
                <table className="table">
                  <thead>
                    <tr>
                      <th>Kode Divisi</th>
                      <th>Kode Sales</th>
                      <th>Nama Sales</th>
                      <th>No HP</th>
                      <th>Target</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {currentData.length > 0 ? (
                      currentData.map((sales, index) => (
                        <tr key={`${sales.kodedivisi}-${sales.kodesales}` || index}>
                          <td className="font-medium">
                            {sales.kodedivisi || '-'}
                          </td>
                          <td className="font-medium">
                            {sales.kodesales || '-'}
                          </td>
                          <td className="font-medium">
                            {sales.namasales || '-'}
                          </td>
                          <td className="text-gray-600">
                            {sales.nohp || '-'}
                          </td>
                          <td className="text-gray-600">
                            {sales.target ? new Intl.NumberFormat('id-ID').format(sales.target) : '-'}
                          </td>
                          <td>
                            <span className={`badge ${
                              sales.status === 'Aktif' || 
                              sales.status === 1 || 
                              sales.status === true
                                ? 'badge-success' 
                                : 'badge-neutral'
                            }`}>
                              {sales.status === 'Aktif' || 
                               sales.status === 1 || 
                               sales.status === true
                                ? 'Aktif' 
                                : 'Tidak Aktif'}
                            </span>
                          </td>
                          <td>
                            <div className="action-buttons">
                              <button
                                onClick={() => handleEdit(sales)}
                                className="btn btn-sm btn-ghost text-primary"
                              >
                                <PencilIcon className="w-4 h-4" />
                              </button>
                              <button
                                onClick={() => handleDelete(sales.kodedivisi, sales.kodesales)}
                                className="btn btn-sm btn-ghost text-error"
                              >
                                <TrashIcon className="w-4 h-4" />
                              </button>
                            </div>
                          </td>
                        </tr>
                      ))
                    ) : (
                      <tr>
                        <td colSpan="7" className="text-center py-8">
                          <div className="empty-state">
                            <p>Tidak ada data sales</p>
                          </div>
                        </td>
                      </tr>
                    )}
                  </tbody>
                </table>
              </div>

              {/* Pagination */}
              {totalPages > 1 && (
                <div className="flex justify-between items-center p-6 border-t border-gray-200">
                  <span className="text-sm text-gray-500">
                    Menampilkan {startIndex + 1}-{Math.min(startIndex + itemsPerPage, filteredSales.length)} dari {filteredSales.length} data
                  </span>
                  <div className="flex gap-2">
                    <button
                      onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
                      disabled={currentPage === 1}
                      className="btn btn-sm btn-secondary"
                    >
                      Sebelumnya
                    </button>
                    <button
                      onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
                      disabled={currentPage === totalPages}
                      className="btn btn-sm btn-secondary"
                    >
                      Selanjutnya
                    </button>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Statistics Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
          <div className="card p-6">
            <h3 className="text-sm font-medium text-gray-600 mb-2">Total Sales</h3>
            <p className="text-2xl font-bold text-gray-900">{sales.length}</p>
          </div>
          
          <div className="card p-6">
            <h3 className="text-sm font-medium text-gray-600 mb-2">Sales Aktif</h3>
            <p className="text-2xl font-bold text-success">
              {sales.filter(s => s.status === true || s.status === 'Aktif').length}
            </p>
          </div>
          
          <div className="card p-6">
            <h3 className="text-sm font-medium text-gray-600 mb-2">Sales Nonaktif</h3>
            <p className="text-2xl font-bold text-gray-500">
              {sales.filter(s => s.status !== true && s.status !== 'Aktif').length}
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default MasterSales;
