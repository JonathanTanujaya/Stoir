import React, { useState, useEffect } from 'react';
import { sparepartAPI } from '../../../services/sparepartAPI';
import { categoriesAPI } from '../../../services/api';
import '../../../design-system.css';

const MasterSparepart = () => {
  const [loading, setLoading] = useState(false);
  const [showForm, setShowForm] = useState(false);
  const [editingItem, setEditingItem] = useState(null);
  const [barangData, setBarangData] = useState([]);
  const [filteredBarang, setFilteredBarang] = useState([]);
  const [searchTerm, setSearchTerm] = useState('');
  const [sortBy, setSortBy] = useState('nama_barang');
  const [sortDirection, setSortDirection] = useState('asc');
  const [formData, setFormData] = useState({
    kode_divisi: '01',
    kode_barang: '',
    nama_barang: '',
    kode_kategori: '',
    harga_list: 0,
    harga_jual: 0,
    satuan: '',
    merk: '',
    diskon: 0,
    lokasi: '',
    quantity: 0
  });
  const [kategoriOptions, setKategoriOptions] = useState([]);

  useEffect(() => {
    fetchData();
    fetchKategori();
  }, []);

  // Filter dan sort barang berdasarkan search term dan sorting
  useEffect(() => {
    let filtered = barangData;
    
    // Filter berdasarkan search term
    if (searchTerm.trim() !== '') {
      filtered = barangData.filter(item => 
        item.kode_barang?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        item.nama_barang?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        item.merk?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        item.kode_kategori?.toLowerCase().includes(searchTerm.toLowerCase())
      );
    }
    
    // Sort berdasarkan sortBy dan sortDirection
    filtered = [...filtered].sort((a, b) => {
      let aValue = a[sortBy] || '';
      let bValue = b[sortBy] || '';
      
      // Handle numeric values
      if (sortBy === 'harga_list' || sortBy === 'harga_jual' || sortBy === 'quantity') {
        aValue = parseFloat(aValue) || 0;
        bValue = parseFloat(bValue) || 0;
      } else {
        aValue = aValue.toString().toLowerCase();
        bValue = bValue.toString().toLowerCase();
      }
      
      if (sortDirection === 'asc') {
        return aValue > bValue ? 1 : -1;
      } else {
        return aValue < bValue ? 1 : -1;
      }
    });
    
    setFilteredBarang(filtered);
  }, [searchTerm, barangData, sortBy, sortDirection]);

  const fetchKategori = async () => {
    try {
      const response = await categoriesAPI.getAll();
      if (response.data && Array.isArray(response.data)) {
        setKategoriOptions(response.data.map(cat => ({
          kode: cat.kode_kategori || cat.kode,
          nama: cat.nama_kategori || cat.nama
        })));
      }
    } catch (error) {
      console.error('Error fetching kategori:', error);
      // Fallback categories
      setKategoriOptions([
        { kode: 'OLI', nama: 'Oli & Pelumas' },
        { kode: 'ELK', nama: 'Kelistrikan' },
        { kode: 'MSN', nama: 'Mesin' },
        { kode: 'BDY', nama: 'Body' },
        { kode: 'AKS', nama: 'Aksesoris' }
      ]);
    }
  };

  const fetchData = async () => {
    try {
      setLoading(true);
      
      const response = await sparepartAPI.getAll();
      
      if (response.data && response.data.data) {
        // Map data dengan quantity random untuk demo
        const mappedData = response.data.data.map(item => ({
          ...item,
          quantity: Math.floor(Math.random() * 100) + 1 // Random quantity untuk demo
        }));
        setBarangData(mappedData);
      } else if (response.data && Array.isArray(response.data)) {
        const mappedData = response.data.map(item => ({
          ...item,
          quantity: Math.floor(Math.random() * 100) + 1
        }));
        setBarangData(mappedData);
      } else {
        setBarangData([]);
      }
    } catch (error) {
      console.error('Error fetching barang data:', error);
      setBarangData([]);
    } finally {
      setLoading(false);
    }
  };

  const handleSort = (field) => {
    if (sortBy === field) {
      setSortDirection(sortDirection === 'asc' ? 'desc' : 'asc');
    } else {
      setSortBy(field);
      setSortDirection('asc');
    }
  };

  const getSortIcon = (field) => {
    if (sortBy !== field) return '↕️';
    return sortDirection === 'asc' ? '↑' : '↓';
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    try {
      setLoading(true);
      
      const dataToSubmit = {
        ...formData,
        harga_list: parseFloat(formData.harga_list) || 0,
        harga_jual: parseFloat(formData.harga_jual) || 0,
        diskon: parseFloat(formData.diskon) || 0
      };
      
      let response;
      if (editingItem) {
        // Update existing barang
        response = await sparepartAPI.update(formData.kode_divisi, formData.kode_barang, dataToSubmit);
      } else {
        // Create new barang
        response = await sparepartAPI.create(dataToSubmit);
      }
      
      if (response.status === 200 || response.status === 201) {
        await fetchData(); // Refresh data
        resetForm();
        alert(editingItem ? 'Data berhasil diupdate!' : 'Data berhasil ditambahkan!');
      }
    } catch (error) {
      console.error('Error saving data:', error);
      alert(`Error: ${error.response?.data?.message || error.message}`);
    } finally {
      setLoading(false);
    }
  };

  const handleEdit = (item) => {
    setEditingItem(item);
    setFormData({
      kode_divisi: item.kode_divisi || '01',
      kode_barang: item.kode_barang || '',
      nama_barang: item.nama_barang || '',
      kode_kategori: item.kode_kategori || '',
      harga_list: item.harga_list || 0,
      harga_jual: item.harga_jual || 0,
      satuan: item.satuan || '',
      merk: item.merk || '',
      diskon: item.diskon || 0,
      lokasi: item.lokasi || '',
      quantity: item.quantity || 0
    });
    setShowForm(true);
  };

  const handleDelete = async (item) => {
    if (window.confirm(`Apakah Anda yakin ingin menghapus ${item.nama_barang}?`)) {
      try {
        setLoading(true);
        
        await sparepartAPI.delete(item.kode_divisi, item.kode_barang);
        await fetchData(); // Refresh data
        alert('Data berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting data:', error);
        alert(`Error: ${error.response?.data?.message || error.message}`);
      } finally {
        setLoading(false);
      }
    }
  };

  const resetForm = () => {
    setFormData({
      kode_divisi: '01',
      kode_barang: '',
      nama_barang: '',
      kode_kategori: '',
      harga_list: 0,
      harga_jual: 0,
      satuan: '',
      merk: '',
      diskon: 0,
      lokasi: '',
      quantity: 0
    });
    setEditingItem(null);
    setShowForm(false);
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR'
    }).format(amount);
  };

  if (loading) {
    return (
      <div className="page-container">
        <div className="loading-state">
          <div className="spinner"></div>
          <p>Loading data barang...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="page-container">
      {/* Header Actions */}
      <div className="page-actions">
        <button 
          className="btn btn-primary"
          onClick={() => setShowForm(!showForm)}
        >
          {showForm ? 'Tutup Form' : 'Tambah Barang Baru'}
        </button>
      </div>

      {/* Form Section */}
      {showForm && (
        <div className="card mb-4">
          <div className="card-header">
            <h3 className="card-title">
              {editingItem ? 'Edit Data Barang' : 'Tambah Barang Baru'}
            </h3>
          </div>
          <div className="card-body">
            <form onSubmit={handleSubmit}>
              <div className="form-grid grid-cols-3">
                <div className="form-group">
                  <label className="form-label">Kode Barang *</label>
                  <input
                    type="text"
                    name="kode_barang"
                    value={formData.kode_barang}
                    onChange={handleInputChange}
                    className="form-control"
                    required
                    placeholder="Contoh: SP001"
                  />
                </div>
                
                <div className="form-group">
                  <label className="form-label">Nama Barang *</label>
                  <input
                    type="text"
                    name="nama_barang"
                    value={formData.nama_barang}
                    onChange={handleInputChange}
                    className="form-control"
                    required
                    placeholder="Contoh: Motor Oil 10W-40"
                  />
                </div>

                <div className="form-group">
                  <label className="form-label">Kategori</label>
                  <select
                    name="kode_kategori"
                    value={formData.kode_kategori}
                    onChange={handleInputChange}
                    className="form-control"
                  >
                    <option value="">Pilih Kategori</option>
                    {kategoriOptions.map(cat => (
                      <option key={cat.kode} value={cat.kode}>
                        {cat.nama}
                      </option>
                    ))}
                  </select>
                </div>

                <div className="form-group">
                  <label className="form-label">Harga List</label>
                  <input
                    type="number"
                    name="harga_list"
                    value={formData.harga_list}
                    onChange={handleInputChange}
                    className="form-control"
                    min="0"
                    step="0.01"
                    placeholder="0"
                  />
                </div>

                <div className="form-group">
                  <label className="form-label">Harga Jual</label>
                  <input
                    type="number"
                    name="harga_jual"
                    value={formData.harga_jual}
                    onChange={handleInputChange}
                    className="form-control"
                    min="0"
                    step="0.01"
                    placeholder="0"
                  />
                </div>

                <div className="form-group">
                  <label className="form-label">Satuan</label>
                  <input
                    type="text"
                    name="satuan"
                    value={formData.satuan}
                    onChange={handleInputChange}
                    className="form-control"
                    placeholder="Contoh: Pcs, Botol, Liter"
                  />
                </div>

                <div className="form-group">
                  <label className="form-label">Merk</label>
                  <input
                    type="text"
                    name="merk"
                    value={formData.merk}
                    onChange={handleInputChange}
                    className="form-control"
                    placeholder="Contoh: Yamalube, NGK"
                  />
                </div>

                <div className="form-group">
                  <label className="form-label">Diskon (%)</label>
                  <input
                    type="number"
                    name="diskon"
                    value={formData.diskon}
                    onChange={handleInputChange}
                    className="form-control"
                    min="0"
                    max="100"
                    step="0.01"
                    placeholder="0"
                  />
                </div>

                <div className="form-group">
                  <label className="form-label">Lokasi</label>
                  <input
                    type="text"
                    name="lokasi"
                    value={formData.lokasi}
                    onChange={handleInputChange}
                    className="form-control"
                    placeholder="Contoh: Rak A1, Gudang B"
                  />
                </div>

                <div className="form-group">
                  <label className="form-label">Quantity</label>
                  <input
                    type="number"
                    name="quantity"
                    value={formData.quantity}
                    onChange={handleInputChange}
                    className="form-control"
                    min="0"
                    step="1"
                    placeholder="0"
                  />
                </div>
              </div>

              <div className="form-actions">
                <button type="submit" className="btn btn-primary">
                  {editingItem ? 'Update' : 'Simpan'}
                </button>
                <button 
                  type="button" 
                  className="btn btn-secondary"
                  onClick={resetForm}
                >
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Search Section */}
      <div className="card mb-4">
        <div className="card-header">
          <h3 className="card-title">Cari Data Barang</h3>
        </div>
        <div className="card-body">
          <div className="search-container">
            <input
              type="text"
              placeholder="Cari berdasarkan kode, nama, merk, atau lokasi..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="form-control"
            />
          </div>
        </div>
      </div>

      {/* Data Table */}
      <div className="card">
        <div className="card-header">
          <h3 className="card-title">Data Barang Sparepart</h3>
          <div className="text-sm text-gray-600">
            Total: {filteredBarang.length} items
          </div>
        </div>
        <div className="card-body">
          <div className="table-responsive">
            <table className="table">
              <thead>
                <tr>
                  <th onClick={() => handleSort('nama_barang')} style={{cursor: 'pointer'}}>
                    Nama Barang {getSortIcon('nama_barang')}
                  </th>
                  <th onClick={() => handleSort('kode_kategori')} style={{cursor: 'pointer'}}>
                    Kategori {getSortIcon('kode_kategori')}
                  </th>
                  <th onClick={() => handleSort('merk')} style={{cursor: 'pointer'}}>
                    Merk {getSortIcon('merk')}
                  </th>
                  <th onClick={() => handleSort('satuan')} style={{cursor: 'pointer'}}>
                    Satuan {getSortIcon('satuan')}
                  </th>
                  <th onClick={() => handleSort('harga_jual')} style={{cursor: 'pointer'}}>
                    Harga Jual {getSortIcon('harga_jual')}
                  </th>
                  <th onClick={() => handleSort('quantity')} style={{cursor: 'pointer'}}>
                    Quantity {getSortIcon('quantity')}
                  </th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                {filteredBarang.length > 0 ? (
                  filteredBarang.map((item, index) => (
                    <tr key={item.id || index}>
                      <td>{item.nama_barang || '-'}</td>
                      <td>{item.kode_kategori || '-'}</td>
                      <td>{item.merk || '-'}</td>
                      <td>{item.satuan || '-'}</td>
                      <td>{formatCurrency(item.harga_jual || 0)}</td>
                      <td>{item.quantity || 0}</td>
                      <td>
                        <div className="action-buttons">
                          <button
                            className="btn btn-sm btn-outline-primary"
                            onClick={() => handleEdit(item)}
                            title="Edit"
                          >
                            ✏️
                          </button>
                          <button
                            className="btn btn-sm btn-outline-danger"
                            onClick={() => handleDelete(item)}
                            title="Hapus"
                          >
                            🗑️
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))
                ) : (
                  <tr>
                    <td colSpan="7" className="text-center text-gray-500">
                      {searchTerm ? 'Tidak ada data yang cocok dengan pencarian' : 'Belum ada data barang'}
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};

export default MasterSparepart;
