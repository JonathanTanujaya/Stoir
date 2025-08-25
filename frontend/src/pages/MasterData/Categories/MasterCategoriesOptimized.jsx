import React, { useState, useEffect, useMemo } from 'react';
import { MagnifyingGlassIcon, PencilIcon, TrashIcon, PlusIcon } from '@heroicons/react/24/outline';
import { categoriesAPI } from '../../../services/api';

const MasterCategories = () => {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [pageSize, setPageSize] = useState(15);
  const [sortKey, setSortKey] = useState('namaKategori');
  const [sortDir, setSortDir] = useState('asc');
  const [showForm, setShowForm] = useState(false);
  const [formData, setFormData] = useState({
    namaKategori: '',
    kodeKategori: '',
    deskripsi: '',
    status: 'Aktif'
  });
  const [editingId, setEditingId] = useState(null);
  const [updatingId, setUpdatingId] = useState(null);

  // Debounce search term
  useEffect(() => {
    const t = setTimeout(() => setDebouncedSearch(searchTerm.trim()), 350);
    return () => clearTimeout(t);
  }, [searchTerm]);

  useEffect(() => {
    fetchCategories();
  }, []);

  const fetchCategories = async () => {
    try {
      setLoading(true);
      const response = await categoriesAPI.getAll();
      const categoriesData = response.data?.data || [];
      setCategories(categoriesData);
    } catch (error) {
      console.error('Error fetching categories:', error);
      setCategories([]);
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
      fetchCategories();
      resetForm();
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
    setShowForm(true);
  };

  const handleDelete = async (id) => {
    if (window.confirm('Yakin ingin menghapus kategori ini?')) {
      try {
        await categoriesAPI.delete(id);
        fetchCategories();
      } catch (error) {
        console.error('Error deleting category:', error);
      }
    }
  };

  const toggleStatusInline = async (category) => {
    const newStatus = category.status === 'Aktif' ? 'Tidak Aktif' : 'Aktif';
    // Optimistic update
    setCategories(prev => prev.map(c => c.id === category.id ? { ...c, status: newStatus } : c));
    setUpdatingId(category.id);
    try {
      await categoriesAPI.update(category.id, { ...category, status: newStatus });
    } catch (e) {
      // revert on error
      setCategories(prev => prev.map(c => c.id === category.id ? { ...c, status: category.status } : c));
      console.error('Toggle status gagal', e);
    } finally {
      setUpdatingId(null);
    }
  };

  const handleInputChange = (e) => {
    setFormData(prev => ({
      ...prev,
      [e.target.name]: e.target.value
    }));
  };

  const resetForm = () => {
    setFormData({
      namaKategori: '',
      kodeKategori: '',
      deskripsi: '',
      status: 'Aktif'
    });
    setEditingId(null);
    setShowForm(false);
  };

  // Memoized filtered list
  const filteredCategories = useMemo(() => {
    if (!debouncedSearch) return categories;
    const q = debouncedSearch.toLowerCase();
    return categories.filter(c => (
      c.namaKategori?.toLowerCase().includes(q) ||
      c.kodeKategori?.toLowerCase().includes(q) ||
      c.deskripsi?.toLowerCase().includes(q)
    ));
  }, [categories, debouncedSearch]);

  // Sorting
  const sortedCategories = useMemo(() => {
    const data = [...filteredCategories];
    data.sort((a, b) => {
      const va = (a[sortKey] ?? '').toString().toLowerCase();
      const vb = (b[sortKey] ?? '').toString().toLowerCase();
      if (va < vb) return sortDir === 'asc' ? -1 : 1;
      if (va > vb) return sortDir === 'asc' ? 1 : -1;
      return 0;
    });
    return data;
  }, [filteredCategories, sortKey, sortDir]);

  const totalPages = Math.ceil(sortedCategories.length / pageSize) || 1;
  const paginatedCategories = useMemo(() => {
    const start = (currentPage - 1) * pageSize;
    return sortedCategories.slice(start, start + pageSize);
  }, [sortedCategories, currentPage, pageSize]);

  // Reset page when filters or pageSize change
  useEffect(() => { setCurrentPage(1); }, [debouncedSearch, pageSize, sortKey, sortDir]);

  const toggleSort = (key) => {
    if (sortKey === key) {
      setSortDir(d => d === 'asc' ? 'desc' : 'asc');
    } else {
      setSortKey(key);
      setSortDir('asc');
    }
  };

  const highlight = (text) => {
    if (!debouncedSearch) return text || '-';
    const safe = (text || '').toString();
    const idx = safe.toLowerCase().indexOf(debouncedSearch.toLowerCase());
    if (idx === -1) return safe || '-';
    const before = safe.slice(0, idx);
    const match = safe.slice(idx, idx + debouncedSearch.length);
    const after = safe.slice(idx + debouncedSearch.length);
    return (<span>{before}<mark className="hl-match">{match}</mark>{after}</span>);
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="container mx-auto px-6 pb-8">
        {/* Enhanced Content Header */}
        <div className="content-header">
          <div className="content-title-section">
            <h2>Daftar Kategori</h2>
            <p>Total {filteredCategories.length} kategori tersedia</p>
          </div>
          
          <div className="flex items-center gap-4">
            {/* Enhanced Search */}
            <div className="search-container">
              <input
                type="text"
                placeholder="Cari kategori..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="search-input"
              />
              <MagnifyingGlassIcon className="search-icon" />
            </div>
            <select
              value={pageSize}
              onChange={e => setPageSize(Number(e.target.value))}
              className="page-size-select"
            >
              {[10,15,25,50].map(s => <option key={s} value={s}>{s}/hal</option>)}
            </select>
            
            {/* Enhanced Add Button */}
            <button
              onClick={() => {
                resetForm();
                setShowForm(true);
              }}
              className="btn-primary"
            >
              <PlusIcon className="btn-icon" />
              Tambah Kategori
            </button>
          </div>
        </div>

        {/* Form Modal - Compact */}
        {showForm && (
          <div className="modal-overlay">
            <div className="modal-content">
              <div className="modal-header">
                <h3 className="modal-title">
                  {editingId ? 'Edit Kategori' : 'Tambah Kategori'}
                </h3>
                <button 
                  onClick={resetForm}
                  className="modal-close"
                >
                  ×
                </button>
              </div>

              <form onSubmit={handleSubmit} className="modal-body">
                <div className="form-grid">
                  <div className="form-group">
                    <label className="form-label">Kode Kategori *</label>
                    <input
                      type="text"
                      name="kodeKategori"
                      value={formData.kodeKategori}
                      onChange={handleInputChange}
                      className="form-input"
                      placeholder="KAT001"
                      required
                    />
                  </div>

                  <div className="form-group">
                    <label className="form-label">Nama Kategori *</label>
                    <input
                      type="text"
                      name="namaKategori"
                      value={formData.namaKategori}
                      onChange={handleInputChange}
                      className="form-input"
                      placeholder="Nama kategori"
                      required
                    />
                  </div>

                  <div className="form-group col-span-2">
                    <label className="form-label">Deskripsi</label>
                    <textarea
                      name="deskripsi"
                      value={formData.deskripsi}
                      onChange={handleInputChange}
                      className="form-textarea"
                      rows="2"
                      placeholder="Deskripsi kategori"
                    />
                  </div>

                  <div className="form-group">
                    <label className="form-label">Status</label>
                    <select
                      name="status"
                      value={formData.status}
                      onChange={handleInputChange}
                      className="form-select"
                    >
                      <option value="Aktif">Aktif</option>
                      <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                  </div>
                </div>

                <div className="modal-footer">
                  <button 
                    type="button" 
                    onClick={resetForm}
                    className="btn btn-secondary"
                  >
                    Batal
                  </button>
                  <button 
                    type="submit"
                    className="btn btn-primary"
                  >
                    {editingId ? 'Update' : 'Simpan'}
                  </button>
                </div>
              </form>
            </div>
          </div>
        )}

        {/* Compact Data Table */}
        <div className="data-table-container">
          {loading ? (
            <div className="table-wrapper">
              <table className="data-table categories-table">
                <colgroup>
                  <col className="col-code" />
                  <col className="col-name" />
                  <col className="col-desc" />
                  <col className="col-status" />
                  <col className="col-actions" />
                </colgroup>
                <tbody>
                  {Array.from({ length: 6 }).map((_, i) => (
                    <tr key={i} className="table-row skeleton-row">
                      <td><div className="sk-box w-16" /></td>
                      <td><div className="sk-box w-40" /></td>
                      <td><div className="sk-box w-64" /></td>
                      <td><div className="sk-badge" /></td>
                      <td><div className="sk-actions" /></td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <>
              <div className="table-wrapper">
                <table className="data-table categories-table">
                  {/* Structural column sizing for consistent header/body alignment */}
                  <colgroup>
                    <col className="col-code" />
                    <col className="col-name" />
                    <col className="col-desc" />
                    <col className="col-status" />
                    <col className="col-actions" />
                  </colgroup>
                  {/* Header dihapus sesuai permintaan */}
                  <tbody>
                    {paginatedCategories.length > 0 && (
                      <tr className="pseudo-header-row">
                        <td onClick={() => toggleSort('kodeKategori')} className="ph-cell clickable">Kode {sortKey==='kodeKategori' && (<span className="sort-indicator">{sortDir==='asc'?'▲':'▼'}</span>)}</td>
                        <td onClick={() => toggleSort('namaKategori')} className="ph-cell clickable">Nama {sortKey==='namaKategori' && (<span className="sort-indicator">{sortDir==='asc'?'▲':'▼'}</span>)}</td>
                        <td onClick={() => toggleSort('deskripsi')} className="ph-cell clickable">Deskripsi {sortKey==='deskripsi' && (<span className="sort-indicator">{sortDir==='asc'?'▲':'▼'}</span>)}</td>
                        <td onClick={() => toggleSort('status')} className="ph-cell clickable center">Status {sortKey==='status' && (<span className="sort-indicator">{sortDir==='asc'?'▲':'▼'}</span>)}</td>
                        <td className="ph-cell center">Aksi</td>
                      </tr>
                    )}
                    {paginatedCategories.length === 0 ? (
                      <tr>
                        <td colSpan="5" className="text-center py-16">
                          <div className="flex flex-col items-center">
                            <div className="text-6xl mb-4 opacity-50">📂</div>
                            <h3 className="text-lg font-semibold text-gray-700 mb-2">
                              {searchTerm ? 'Tidak ada kategori yang ditemukan' : 'Belum ada data kategori'}
                            </h3>
                            <p className="text-sm text-gray-500 mb-4">
                              {searchTerm 
                                ? `Tidak ada kategori yang cocok dengan "${searchTerm}"`
                                : 'Mulai dengan menambahkan kategori baru untuk produk Anda'
                              }
                            </p>
                            {!searchTerm && (
                              <button
                                onClick={() => setShowForm(true)}
                                className="btn-primary"
                              >
                                <PlusIcon className="btn-icon" />
                                Tambah Kategori Pertama
                              </button>
                            )}
                          </div>
                        </td>
                      </tr>
                    ) : (
          paginatedCategories.map((category, index) => (
                        <tr key={category.id} className="table-row">
                          <td className="col-code font-mono font-medium text-primary-600">
            {highlight(category.kodeKategori)}
                          </td>
                          <td className="col-name text-gray-900">
            {highlight(category.namaKategori)}
                          </td>
                          <td className="col-desc text-gray-600 truncate" title={category.deskripsi || category.namaKategori}>
                            {(!category.deskripsi || category.deskripsi === category.namaKategori) ? '-' : highlight(category.deskripsi)}
                          </td>
                          <td className="col-status">
                            <button
                              type="button"
                              onClick={() => toggleStatusInline(category)}
                              disabled={updatingId === category.id}
                              className={`status-badge btn-status-toggle ${category.status === 'Aktif' ? 'status-active' : 'status-inactive'} ${updatingId === category.id ? 'is-loading' : ''}`}
                              title="Klik untuk toggle status"
                            >
                              {updatingId === category.id ? '...' : (category.status || 'Aktif')}
                            </button>
                          </td>
                          <td className="col-actions">
                            <div className="action-buttons">
                              <button
                                onClick={() => handleEdit(category)}
                                className="btn-icon-sm btn-edit"
                                title="Edit Kategori"
                              >
                                <PencilIcon className="icon-sm" />
                              </button>
                              <button
                                onClick={() => handleDelete(category.id)}
                                className="btn-icon-sm btn-delete"
                                title="Hapus Kategori"
                              >
                                <TrashIcon className="icon-sm" />
                              </button>
                            </div>
                          </td>
                        </tr>
                      ))
                    )}
                  </tbody>
                </table>
              </div>

              {/* Compact Pagination */}
              {totalPages > 1 && (
                <div className="pagination">
                  <span className="pagination-info">
                    {(() => {
                      const start = (currentPage - 1) * pageSize + 1;
                      const end = Math.min(currentPage * pageSize, filteredCategories.length);
                      return `Menampilkan ${start}-${end} dari ${filteredCategories.length} data | Halaman ${currentPage} / ${totalPages}`;
                    })()}
                  </span>
                  <div className="pagination-controls">
                    <button
                      onClick={() => setCurrentPage(prev => Math.max(prev - 1, 1))}
                      disabled={currentPage === 1}
                      className="pagination-btn"
                    >
                      ‹ Prev
                    </button>
                    <button
                      onClick={() => setCurrentPage(prev => Math.min(prev + 1, totalPages))}
                      disabled={currentPage === totalPages}
                      className="pagination-btn"
                    >
                      Next ›
                    </button>
                  </div>
                </div>
              )}
            </>
          )}
        </div>
      </div>
    </div>
  );
};

export default MasterCategories;
