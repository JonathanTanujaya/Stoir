import React, { useState, useEffect } from 'react';
import PageHeader from '../../../components/Layout/PageHeader';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon } from '@heroicons/react/24/outline';
import { categoriesAPI } from '../../../services/api';

const MasterCategories = () => {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [formData, setFormData] = useState({
    namaKategori: '',
    kodeKategori: '',
    deskripsi: '',
    status: 'Aktif'
  });
  const [editingId, setEditingId] = useState(null);

  const itemsPerPage = 10;

  useEffect(() => {
    fetchCategories();
  }, []);

  const fetchCategories = async () => {
    try {
      setLoading(true);
      const response = await categoriesAPI.getAll();
      setCategories(response.data || []);
    } catch (error) {
      console.error('Error fetching categories:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editingId) {
        await categoriesAPI.update(editingId, formData);
      } else {
        await categoriesAPI.create(formData);
      }
      
      // Reset form
      setFormData({
        namaKategori: '',
        kodeKategori: '',
        deskripsi: '',
        status: 'Aktif'
      });
      setEditingId(null);
      
      // Refresh data
      fetchCategories();
    } catch (error) {
      console.error('Error saving category:', error);
    }
  };

  const handleEdit = (category) => {
    setFormData({
      namaKategori: category.namaKategori || '',
      kodeKategori: category.kodeKategori || '',
      deskripsi: category.deskripsi || '',
      status: category.status || 'Aktif'
    });
    setEditingId(category.id);
  };

  const handleDelete = async (id) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
      try {
        await categoriesAPI.delete(id);
        fetchCategories();
      } catch (error) {
        console.error('Error deleting category:', error);
      }
    }
  };

  const handleReset = () => {
    setFormData({
      namaKategori: '',
      kodeKategori: '',
      deskripsi: '',
      status: 'Aktif'
    });
    setEditingId(null);
  };

  // Filter and pagination
  const filteredCategories = categories.filter(category =>
    category.namaKategori?.toLowerCase().includes(searchTerm.toLowerCase()) ||
    category.kodeKategori?.toLowerCase().includes(searchTerm.toLowerCase())
  );

  const totalPages = Math.ceil(filteredCategories.length / itemsPerPage);
  const startIndex = (currentPage - 1) * itemsPerPage;
  const paginatedCategories = filteredCategories.slice(startIndex, startIndex + itemsPerPage);

  const totalCategories = categories.length;
  const activeCategories = categories.filter(cat => cat.status === 'Aktif').length;

  return (
    <div className="p-6">
      <PageHeader
        title="Kategori Barang"
        breadcrumb={['Dashboard', 'Master', 'Kategori Barang']}
        showAddButton={false}
      />

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Left Panel - Form */}
        <div className="lg:col-span-1">
          <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-white/20 p-8">
            <h2 className="text-xl font-bold text-gray-900 mb-8 flex items-center">
              {editingId ? '✏️ Edit Kategori' : '➕ Form Kategori Baru'}
            </h2>
            
            <form onSubmit={handleSubmit} className="space-y-6">
              <div>
                <label className="block text-sm font-bold text-gray-800 mb-3">
                  📝 Nama Kategori
                </label>
                <input
                  type="text"
                  className="input-field text-base font-medium"
                  placeholder="Masukkan nama kategori"
                  value={formData.namaKategori}
                  onChange={(e) => setFormData({...formData, namaKategori: e.target.value})}
                  required
                />
              </div>

              <div>
                <label className="block text-sm font-bold text-gray-800 mb-3">
                  🏷️ Kode Kategori
                </label>
                <input
                  type="text"
                  className={`input-field text-base font-medium ${!editingId ? 'bg-gray-100' : ''}`}
                  placeholder="Auto generated"
                  value={formData.kodeKategori}
                  onChange={(e) => setFormData({...formData, kodeKategori: e.target.value})}
                  disabled={!editingId}
                />
              </div>

              <div>
                <label className="block text-sm font-bold text-gray-800 mb-3">
                  📄 Deskripsi
                </label>
                <textarea
                  className="input-field h-20 resize-none text-base"
                  placeholder="Deskripsi kategori..."
                  value={formData.deskripsi}
                  onChange={(e) => setFormData({...formData, deskripsi: e.target.value})}
                />
              </div>

              <div>
                <label className="block text-sm font-bold text-gray-800 mb-3">
                  ⚡ Status
                </label>
                <select
                  className="input-field text-base font-medium"
                  value={formData.status}
                  onChange={(e) => setFormData({...formData, status: e.target.value})}
                >
                  <option value="Aktif">✅ Aktif</option>
                  <option value="Nonaktif">❌ Nonaktif</option>
                </select>
              </div>

              <div className="flex gap-3 pt-6">
                <button type="submit" className="btn-primary flex-1 py-3 text-base font-bold">
                  {editingId ? '✓ Update Kategori' : '+ Simpan Kategori'}
                </button>
                <button type="button" onClick={handleReset} className="btn-secondary px-6 py-3 text-base font-bold">
                  ↻ Reset
                </button>
              </div>
            </form>
          </div>
        </div>

        {/* Right Panel - Table */}
        <div className="lg:col-span-2">
          <div className="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-white/20 p-8">
            <div className="flex items-center justify-between mb-8">
              <h2 className="text-xl font-bold text-gray-900 flex items-center">
                📋 Daftar Kategori
              </h2>
              
              <div className="flex items-center gap-4">
                {/* Search */}
                <div className="relative">
                  <MagnifyingGlassIcon className="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-500" />
                  <input
                    type="text"
                    className="pl-10 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 text-sm font-medium bg-white shadow-lg hover:shadow-xl transition-all duration-300"
                    placeholder="🔍 Cari kategori..."
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                  />
                </div>
                
                {/* Export Button */}
                <button className="px-6 py-3 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-xl hover:from-emerald-700 hover:to-emerald-800 transition-all duration-300 text-sm font-bold shadow-lg hover:shadow-xl transform hover:scale-105">
                  📊 Export Excel
                </button>
              </div>
            </div>

            {/* Table */}
            <div className="overflow-hidden border border-gray-200 rounded-lg">
              <table className="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr className="bg-gray-50">
                    <th className="table-header">Kode</th>
                    <th className="table-header">Nama Kategori</th>
                    <th className="table-header">Total Item</th>
                    <th className="table-header">Status</th>
                    <th className="table-header">Aksi</th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {loading ? (
                    <tr>
                      <td colSpan="5" className="table-cell text-center py-8">
                        Loading...
                      </td>
                    </tr>
                  ) : paginatedCategories.length === 0 ? (
                    <tr>
                      <td colSpan="5" className="table-cell text-center py-8">
                        Tidak ada data kategori
                      </td>
                    </tr>
                  ) : (
                    paginatedCategories.map((category, index) => (
                      <tr key={category.id || index} className={index % 2 === 0 ? 'bg-white' : 'bg-gray-50'}>
                        <td className="table-cell font-medium">
                          {category.kodeKategori || category.kodekategori}
                        </td>
                        <td className="table-cell">
                          {category.namaKategori || category.namakategori}
                        </td>
                        <td className="table-cell">
                          {category.totalItem || 0} Item
                        </td>
                        <td className="table-cell">
                          <span className={`inline-flex px-3 py-2 text-xs font-bold rounded-xl shadow-lg ${
                            (category.status || category.statusaktif) === 'Aktif' || category.statusaktif === true
                              ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white'
                              : 'bg-gradient-to-r from-yellow-500 to-yellow-600 text-white'
                          }`}>
                            {(category.status || category.statusaktif) === 'Aktif' || category.statusaktif === true ? '✅ Aktif' : '⏸️ Nonaktif'}
                          </span>
                        </td>
                        <td className="table-cell">
                          <div className="flex items-center gap-3">
                            <button
                              onClick={() => handleEdit(category)}
                              className="p-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
                              title="Edit Kategori"
                            >
                              <PencilIcon className="w-5 h-5 group-hover:scale-110 transition-transform" />
                            </button>
                            <button
                              onClick={() => handleDelete(category.id)}
                              className="p-3 bg-gradient-to-r from-red-600 to-red-700 text-white hover:from-red-700 hover:to-red-800 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 group"
                              title="Hapus Kategori"
                            >
                              <TrashIcon className="w-5 h-5 group-hover:scale-110 transition-transform" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>

            {/* Pagination */}
            <div className="flex items-center justify-between mt-6">
              <div className="text-sm text-gray-600">
                Menampilkan {startIndex + 1}-{Math.min(startIndex + itemsPerPage, filteredCategories.length)} dari {filteredCategories.length} data
              </div>
              
              <div className="flex items-center gap-3">
                {Array.from({ length: totalPages }, (_, i) => i + 1).map((page) => (
                  <button
                    key={page}
                    onClick={() => setCurrentPage(page)}
                    className={`w-10 h-10 rounded-xl text-sm font-bold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 ${
                      currentPage === page
                        ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white'
                        : 'bg-white border-2 border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-indigo-300'
                    }`}
                  >
                    {page}
                  </button>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <div className="bg-gradient-to-r from-emerald-50 to-emerald-100 border-2 border-emerald-300 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
          <h3 className="text-lg font-bold text-emerald-800 mb-3 flex items-center">📊 Total Kategori</h3>
          <p className="text-4xl font-bold text-emerald-700">{totalCategories}</p>
          <p className="text-sm text-emerald-600 mt-2 font-medium">📈 +2 dari bulan lalu</p>
        </div>
        
        <div className="bg-gradient-to-r from-indigo-50 to-purple-100 border-2 border-indigo-300 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
          <h3 className="text-lg font-bold text-indigo-800 mb-3 flex items-center">⚡ Kategori Aktif</h3>
          <p className="text-4xl font-bold text-indigo-700">{activeCategories}</p>
          <p className="text-sm text-indigo-600 mt-2 font-medium">🎯 {Math.round((activeCategories / totalCategories) * 100) || 0}% dari total</p>
        </div>
      </div>
    </div>
  );
};

export default MasterCategories;
