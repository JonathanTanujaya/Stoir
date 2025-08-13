import React, { useState, useEffect } from 'react';
import '../../../design-system.css';

const MasterBank = () => {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editingItem, setEditingItem] = useState(null);
  const [formData, setFormData] = useState({
    kode_bank: '',
    nama_bank: '',
    alamat: '',
    telepon: '',
    status: 'Aktif'
  });

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      // Sample data
      const sampleData = [
        { id: 1, kode_bank: 'BCA', nama_bank: 'Bank Central Asia', alamat: 'Jakarta', telepon: '021-1500888', status: 'Aktif' },
        { id: 2, kode_bank: 'BNI', nama_bank: 'Bank Negara Indonesia', alamat: 'Jakarta', telepon: '021-500046', status: 'Aktif' },
        { id: 3, kode_bank: 'BRI', nama_bank: 'Bank Rakyat Indonesia', alamat: 'Jakarta', telepon: '021-57987400', status: 'Aktif' }
      ];
      setData(sampleData);
    } catch (error) {
      console.error('Error fetching bank data:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editingItem) {
        setData(prev => prev.map(item => 
          item.id === editingItem.id ? { ...formData, id: editingItem.id } : item
        ));
      } else {
        const newItem = { ...formData, id: Date.now() };
        setData(prev => [...prev, newItem]);
      }
      resetForm();
      alert('Data bank berhasil disimpan!');
    } catch (error) {
      console.error('Error saving bank:', error);
      alert('Error saving bank');
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
    setFormData({ kode_bank: '', nama_bank: '', alamat: '', telepon: '', status: 'Aktif' });
    setEditingItem(null);
    setShowForm(false);
  };

  if (loading) return <div className="page-container"><div className="loading-spinner">Loading...</div></div>;

  return (
    <div className="page-container">
      <div className="page-header">
        <h1 className="page-title">Master Bank</h1>
        <div className="page-actions">
          <button className="btn btn-primary" onClick={() => setShowForm(!showForm)}>
            {showForm ? 'Tutup Form' : 'Tambah Bank'}
          </button>
        </div>
      </div>

      {showForm && (
        <div className="card mb-4">
          <div className="card-header">
            <h3 className="card-title">{editingItem ? 'Edit Bank' : 'Tambah Bank'}</h3>
          </div>
          <div className="card-body">
            <form onSubmit={handleSubmit}>
              <div className="form-grid grid-cols-2">
                <div className="form-group">
                  <label className="form-label">Kode Bank</label>
                  <input type="text" name="kode_bank" value={formData.kode_bank} onChange={handleInputChange} className="form-control" required />
                </div>
                <div className="form-group">
                  <label className="form-label">Nama Bank</label>
                  <input type="text" name="nama_bank" value={formData.nama_bank} onChange={handleInputChange} className="form-control" required />
                </div>
                <div className="form-group">
                  <label className="form-label">Alamat</label>
                  <input type="text" name="alamat" value={formData.alamat} onChange={handleInputChange} className="form-control" />
                </div>
                <div className="form-group">
                  <label className="form-label">Telepon</label>
                  <input type="text" name="telepon" value={formData.telepon} onChange={handleInputChange} className="form-control" />
                </div>
              </div>
              <div className="form-actions">
                <button type="submit" className="btn btn-primary">{editingItem ? 'Update' : 'Simpan'}</button>
                <button type="button" className="btn btn-secondary" onClick={resetForm}>Batal</button>
              </div>
            </form>
          </div>
        </div>
      )}

      <div className="card">
        <div className="card-header"><h3 className="card-title">Data Bank</h3></div>
        <div className="card-body">
          <div className="table-responsive">
            <table className="table">
              <thead>
                <tr><th>Kode</th><th>Nama Bank</th><th>Alamat</th><th>Telepon</th><th>Status</th><th>Aksi</th></tr>
              </thead>
              <tbody>
                {data.map((item) => (
                  <tr key={item.id}>
                    <td>{item.kode_bank}</td>
                    <td>{item.nama_bank}</td>
                    <td>{item.alamat}</td>
                    <td>{item.telepon}</td>
                    <td><span className="badge badge-success">{item.status}</span></td>
                    <td>
                      <div className="action-buttons">
                        <button className="btn btn-sm btn-secondary" onClick={() => handleEdit(item)}>Edit</button>
                        <button className="btn btn-sm btn-danger" onClick={() => handleDelete(item.id)}>Hapus</button>
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

export default MasterBank;
