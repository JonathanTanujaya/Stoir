import React, { useState, useEffect } from 'react';
import { sparepartsAPI, barangAPI, categoriesAPI } from '../../../services/api';
import {
  ensureArray,
  generateUniqueKey,
  safeGet,
  standardizeApiResponse,
  handleApiError,
  createLoadingState,
  safeFilter
} from '../../../utils/apiResponseHandler';
import { standardizeSparepart } from '../../../utils/fieldMapping';
import { withErrorBoundary } from '../../../components/ErrorBoundary/MasterDataErrorBoundary';
import '../../../design-system.css';

const MasterSparepart = () => {
  const [appState, setAppState] = useState(createLoadingState());
  const [showForm, setShowForm] = useState(false);
  const [editingItem, setEditingItem] = useState(null);
  const [barangData, setBarangData] = useState([]); // Data barang dari API
  const [filteredBarang, setFilteredBarang] = useState([]); // Data barang yang difilter
  const [searchTerm, setSearchTerm] = useState('');
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
    lokasi: ''
  });
  const [kategoriOptions, setKategoriOptions] = useState([]);

  useEffect(() => {
    fetchData();
    fetchKategori();
  }, []);

  // Filter barang berdasarkan search term
  useEffect(() => {
    if (searchTerm.trim() === '') {
      setFilteredBarang(barangData);
    } else {
      const filtered = barangData.filter(item => 
        item.kode_barang?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        item.nama_barang?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        item.merk?.toLowerCase().includes(searchTerm.toLowerCase()) ||
        item.lokasi?.toLowerCase().includes(searchTerm.toLowerCase())
      );
      setFilteredBarang(filtered);
    }
  }, [searchTerm, barangData]);

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
      setAppState(prev => ({ ...prev, loading: true, error: null }));
      
      // Fetch data barang instead of spareparts
      const response = await barangAPI.getAll();
      
      if (response.data && response.data.data) {
        setBarangData(response.data.data);
        setFilteredBarang(response.data.data);
      } else if (response.data && Array.isArray(response.data)) {
        setBarangData(response.data);
        setFilteredBarang(response.data);
      } else {
        setBarangData([]);
        setFilteredBarang([]);
      }
      
      setAppState(prev => ({ ...prev, loading: false }));
    } catch (error) {
      console.error('Error fetching barang data:', error);
      setAppState(prev => ({ 
        ...prev, 
        loading: false, 
        error: `Error loading data: ${error.message}` 
      }));
      setBarangData([]);
      setFilteredBarang([]);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    try {
      setAppState(prev => ({ ...prev, loading: true }));
      
      const dataToSubmit = {
        ...formData,
        harga_list: parseFloat(formData.harga_list) || 0,
        harga_jual: parseFloat(formData.harga_jual) || 0,
        diskon: parseFloat(formData.diskon) || 0
      };
      
      let response;
      if (editingItem) {
        // Update existing barang
        response = await barangAPI.update(formData.kode_divisi, formData.kode_barang, dataToSubmit);
      } else {
        // Create new barang
        response = await barangAPI.create(dataToSubmit);
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
      setAppState(prev => ({ ...prev, loading: false }));
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
      lokasi: item.lokasi || ''
    });
    setShowForm(true);
  };

  const handleDelete = async (item) => {
    if (window.confirm(`Apakah Anda yakin ingin menghapus ${item.nama_barang}?`)) {
      try {
        setAppState(prev => ({ ...prev, loading: true }));
        
        await barangAPI.delete(item.kode_divisi, item.kode_barang);
        await fetchData(); // Refresh data
        alert('Data berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting data:', error);
        alert(`Error: ${error.response?.data?.message || error.message}`);
      } finally {
        setAppState(prev => ({ ...prev, loading: false }));
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
      lokasi: ''
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

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editingItem) {
        // Update existing item
        await sparepartsAPI.update(formData.kode_divisi, formData.kode_barang, formData);
        setData(prev => prev.map(item => 
          item.id === editingItem.id ? { ...formData, id: editingItem.id } : item
        ));
      } else {
        // Add new item
        const response = await sparepartsAPI.create(formData);
        const newItem = response.data || { ...formData, id: Date.now() };
        setData(prev => [...prev, newItem]);
      }
      
      resetForm();
      alert('Data sparepart berhasil disimpan!');
    } catch (error) {
      console.error('Error saving sparepart:', error);
      alert('Error saving sparepart');
    }
  };

  const handleEdit = (item) => {
    setEditingItem(item);
    setFormData(item);
    setShowForm(true);
  };

  const handleDelete = async (id) => {
    if (confirm('Yakin ingin menghapus data ini?')) {
      try {
        const item = data.find(d => d.id === id);
        if (item && item.kode_divisi && item.kode_barang) {
          await sparepartsAPI.delete(item.kode_divisi, item.kode_barang);
        }
        setData(prev => prev.filter(item => item.id !== id));
        alert('Data berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting sparepart:', error);
        alert('Error deleting sparepart');
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
      satuan: '',
      merk: '',
      diskon: 0,
      lokasi: ''
    });
    setEditingItem(null);
    setShowForm(false);
  };

  return (
    <div className="page-container">
      <div className="page-actions">
        <button 
          className="btn btn-primary"
          onClick={() => setShowForm(!showForm)}
        >
          {showForm ? 'Tutup Form' : 'Tambah Sparepart'}
        </button>
      </div>

      {showForm && (
        <div className="card mb-4">
          <div className="card-header">
            <h3 className="card-title">
              {editingItem ? 'Edit Sparepart' : 'Tambah Sparepart'}
            </h3>
          </div>
          <div className="card-body">
            <form onSubmit={handleSubmit}>
              <div className="form-grid grid-cols-2">
                <div className="form-group">
                  <label className="form-label">Kode Barang *</label>
                  <input
                    type="text"
                    name="kode_barang"
                    value={formData.kode_barang}
                    onChange={handleInputChange}
                    className="form-control"
                    required
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
                  />
                </div>
                <div className="form-group">
                  <label className="form-label">Kode Kategori *</label>
                  <select
                    name="kode_kategori"
                    value={formData.kode_kategori}
                    onChange={handleInputChange}
                    className="form-control"
                    required
                  >
                    <option value="">Pilih Kategori</option>
                    {kategoriOptions.map(kategori => (
                      <option key={kategori.kode} value={kategori.kode}>
                        {kategori.kode} - {kategori.nama}
                      </option>
                    ))}
                  </select>
                </div>
                <div className="form-group">
                  <label className="form-label">Harga List *</label>
                  <input
                    type="number"
                    name="harga_list"
                    value={formData.harga_list}
                    onChange={handleInputChange}
                    className="form-control"
                    min="0"
                    step="1000"
                    required
                  />
                </div>
                <div className="form-group">
                  <label className="form-label">Satuan *</label>
                  <select
                    name="satuan"
                    value={formData.satuan}
                    onChange={handleInputChange}
                    className="form-control"
                    required
                  >
                    <option value="">Pilih Satuan</option>
                    <option value="Pcs">Pcs</option>
                    <option value="Set">Set</option>
                    <option value="Botol">Botol</option>
                    <option value="Liter">Liter</option>
                    <option value="Meter">Meter</option>
                    <option value="Unit">Unit</option>
                  </select>
                </div>
                <div className="form-group">
                  <label className="form-label">Merk</label>
                  <input
                    type="text"
                    name="merk"
                    value={formData.merk}
                    onChange={handleInputChange}
                    className="form-control"
                    placeholder="Masukkan merk produk"
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
                    step="0.1"
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
                    placeholder="Lokasi penyimpanan"
                  />
                </div>
              </div>
              
              <div className="form-actions">
                <button type="submit" className="btn btn-primary">
                  {editingItem ? 'Update' : 'Simpan'}
                </button>
                <button type="button" className="btn btn-secondary" onClick={resetForm}>
                  Batal
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      <div className="card">
        <div className="card-header">
          <h3 className="card-title">Data Sparepart</h3>
        </div>
        <div className="card-body">
          <div className="table-responsive">
            <table className="table">
              <thead>
                <tr>
                  <th>Kode Barang</th>
                  <th>Nama Barang</th>
                  <th>Kategori</th>
                  <th>Merk</th>
                  <th>Satuan</th>
                  <th>Harga List</th>
                  <th>Diskon (%)</th>
                  <th>Lokasi</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                {data.map((item) => (
                  <tr key={item.id}>
                    <td>{item.kode_barang || item.kodebarang}</td>
                    <td>{item.nama_barang || item.kodebarang}</td>
                    <td>{item.kode_kategori || item.kategori || 'N/A'}</td>
                    <td>{item.merk || 'N/A'}</td>
                    <td>{item.satuan || 'N/A'}</td>
                    <td className="text-right">Rp {(item.harga_list || item.modal || 0).toLocaleString()}</td>
                    <td className="text-right">{item.diskon || 0}%</td>
                    <td>{item.lokasi || 'N/A'}</td>
                    <td>
                      <div className="action-buttons">
                        <button 
                          className="btn btn-sm btn-secondary"
                          onClick={() => handleEdit(item)}
                        >
                          Edit
                        </button>
                        <button 
                          className="btn btn-sm btn-danger"
                          onClick={() => handleDelete(item.id)}
                        >
                          Hapus
                        </button>
                      </div>
                    </td>
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

export default withErrorBoundary(MasterSparepart);
