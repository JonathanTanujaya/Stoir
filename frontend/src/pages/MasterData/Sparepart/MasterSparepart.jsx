import React, { useState, useEffect } from 'react';
import { sparepartsAPI } from '../../../services/api';
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
  const [formData, setFormData] = useState({
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
  const [kategoriOptions, setKategoriOptions] = useState([
    { kode: 'OLI', nama: 'Oli & Pelumas' },
    { kode: 'ELK', nama: 'Kelistrikan' },
    { kode: 'MSN', nama: 'Mesin' },
    { kode: 'BDY', nama: 'Body' },
    { kode: 'AKS', nama: 'Aksesoris' }
  ]);

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      const response = await sparepartsAPI.getAll();
      
      if (response.data && response.data.data) {
        setData(response.data.data);
      } else if (response.data && Array.isArray(response.data)) {
        setData(response.data);
      } else {
        // Fallback sample data
        const sampleData = [
          {
            id: 1,
            kode_divisi: 'DIV01',
            kode_barang: 'SP001',
            nama_barang: 'Motor Oil 10W-40',
            kategori: 'Oli & Pelumas',
            merk: 'Yamalube',
            satuan: 'Botol',
            harga_beli: 45000,
            harga_jual: 55000,
            stok_min: 10,
            keterangan: 'Oli motor sintetik'
          },
          {
            id: 2,
            kode_divisi: 'DIV01',
            kode_barang: 'SP002',
            nama_barang: 'Spark Plug NGK',
            kategori: 'Kelistrikan',
            merk: 'NGK',
            satuan: 'Pcs',
            harga_beli: 25000,
            harga_jual: 35000,
            stok_min: 20,
            keterangan: 'Busi motor'
          }
        ];
        setData(sampleData);
      }
    } catch (error) {
      console.error('Error fetching sparepart data:', error);
      // Fallback to sample data on error
      const sampleData = [
        {
          id: 1,
          kode_divisi: 'DIV01',
          kode_barang: 'SP001',
          nama_barang: 'Motor Oil 10W-40',
          kategori: 'Oli & Pelumas',
          merk: 'Yamalube',
          satuan: 'Botol',
          harga_beli: 45000,
          harga_jual: 55000,
          stok_min: 10,
          keterangan: 'Oli motor sintetik'
        }
      ];
      setData(sampleData);
    }
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
      <div className="page-header">
        <h1 className="page-title">Master Sparepart</h1>
        <div className="page-actions">
          <button 
            className="btn btn-primary"
            onClick={() => setShowForm(!showForm)}
          >
            {showForm ? 'Tutup Form' : 'Tambah Sparepart'}
          </button>
        </div>
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
