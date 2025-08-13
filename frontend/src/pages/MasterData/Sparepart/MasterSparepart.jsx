import React, { useState, useEffect } from 'react';
import '../../../design-system.css';

const MasterSparepart = () => {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editingItem, setEditingItem] = useState(null);
  const [formData, setFormData] = useState({
    kode_sparepart: '',
    nama_sparepart: '',
    kategori: '',
    merk: '',
    satuan: '',
    harga_beli: 0,
    harga_jual: 0,
    stok_min: 0,
    keterangan: ''
  });

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      // Sample data - replace with actual API call
      const sampleData = [
        {
          id: 1,
          kode_sparepart: 'SP001',
          nama_sparepart: 'Motor Oil 10W-40',
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
          kode_sparepart: 'SP002',
          nama_sparepart: 'Spark Plug NGK',
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
    } catch (error) {
      console.error('Error fetching sparepart data:', error);
    } finally {
      setLoading(false);
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
        setData(prev => prev.map(item => 
          item.id === editingItem.id ? { ...formData, id: editingItem.id } : item
        ));
      } else {
        // Add new item
        const newItem = { ...formData, id: Date.now() };
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

  const handleDelete = (id) => {
    if (confirm('Yakin ingin menghapus data ini?')) {
      setData(prev => prev.filter(item => item.id !== id));
    }
  };

  const resetForm = () => {
    setFormData({
      kode_sparepart: '',
      nama_sparepart: '',
      kategori: '',
      merk: '',
      satuan: '',
      harga_beli: 0,
      harga_jual: 0,
      stok_min: 0,
      keterangan: ''
    });
    setEditingItem(null);
    setShowForm(false);
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
                  <label className="form-label">Kode Sparepart</label>
                  <input
                    type="text"
                    name="kode_sparepart"
                    value={formData.kode_sparepart}
                    onChange={handleInputChange}
                    className="form-control"
                    required
                  />
                </div>
                <div className="form-group">
                  <label className="form-label">Nama Sparepart</label>
                  <input
                    type="text"
                    name="nama_sparepart"
                    value={formData.nama_sparepart}
                    onChange={handleInputChange}
                    className="form-control"
                    required
                  />
                </div>
                <div className="form-group">
                  <label className="form-label">Kategori</label>
                  <select
                    name="kategori"
                    value={formData.kategori}
                    onChange={handleInputChange}
                    className="form-control"
                    required
                  >
                    <option value="">Pilih Kategori</option>
                    <option value="Oli & Pelumas">Oli & Pelumas</option>
                    <option value="Kelistrikan">Kelistrikan</option>
                    <option value="Mesin">Mesin</option>
                    <option value="Body">Body</option>
                    <option value="Aksesoris">Aksesoris</option>
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
                  />
                </div>
                <div className="form-group">
                  <label className="form-label">Satuan</label>
                  <select
                    name="satuan"
                    value={formData.satuan}
                    onChange={handleInputChange}
                    className="form-control"
                    required
                  >
                    <option value="">Pilih Satuan</option>
                    <option value="Pcs">Pcs</option>
                    <option value="Botol">Botol</option>
                    <option value="Liter">Liter</option>
                    <option value="Set">Set</option>
                    <option value="Pasang">Pasang</option>
                  </select>
                </div>
                <div className="form-group">
                  <label className="form-label">Stok Minimum</label>
                  <input
                    type="number"
                    name="stok_min"
                    value={formData.stok_min}
                    onChange={handleInputChange}
                    className="form-control"
                    min="0"
                  />
                </div>
                <div className="form-group">
                  <label className="form-label">Harga Beli</label>
                  <input
                    type="number"
                    name="harga_beli"
                    value={formData.harga_beli}
                    onChange={handleInputChange}
                    className="form-control"
                    min="0"
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
                  />
                </div>
              </div>
              <div className="form-group">
                <label className="form-label">Keterangan</label>
                <textarea
                  name="keterangan"
                  value={formData.keterangan}
                  onChange={handleInputChange}
                  className="form-control"
                  rows="3"
                ></textarea>
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
                  <th>Kode</th>
                  <th>Nama Sparepart</th>
                  <th>Kategori</th>
                  <th>Merk</th>
                  <th>Satuan</th>
                  <th>Harga Beli</th>
                  <th>Harga Jual</th>
                  <th>Stok Min</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                {data.map((item) => (
                  <tr key={item.id}>
                    <td>{item.kode_sparepart}</td>
                    <td>{item.nama_sparepart}</td>
                    <td>{item.kategori}</td>
                    <td>{item.merk}</td>
                    <td>{item.satuan}</td>
                    <td className="text-right">Rp {item.harga_beli.toLocaleString()}</td>
                    <td className="text-right">Rp {item.harga_jual.toLocaleString()}</td>
                    <td className="text-right">{item.stok_min}</td>
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

export default MasterSparepart;
