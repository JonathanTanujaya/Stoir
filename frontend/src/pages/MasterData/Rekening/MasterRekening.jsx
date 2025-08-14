import React, { useState, useEffect } from 'react';
import { rekeningAPI } from '../../../services/api';
import '../../../design-system.css';

const MasterRekening = () => {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [editingItem, setEditingItem] = useState(null);
  const [formData, setFormData] = useState({
    no_rekening: '',
    nama_rekening: '',
    bank: '',
    jenis_rekening: '',
    saldo_awal: 0,
    status: 'Aktif'
  });

  useEffect(() => {
    fetchData();
  }, []);

  const fetchData = async () => {
    try {
      setLoading(true);
      const response = await rekeningAPI.getAll();
      setData(response.data || []);
    } catch (error) {
      console.error('Error fetching rekening data:', error);
      // Fallback to sample data if API fails
      const sampleData = [
        { id: 1, no_rekening: '1234567890', nama_rekening: 'Kas Utama', bank: 'BCA', jenis_rekening: 'Tabungan', saldo_awal: 50000000, status: 'Aktif' },
        { id: 2, no_rekening: '0987654321', nama_rekening: 'Operasional', bank: 'BNI', jenis_rekening: 'Giro', saldo_awal: 25000000, status: 'Aktif' },
        { id: 3, no_rekening: '5555666677', nama_rekening: 'Investasi', bank: 'BRI', jenis_rekening: 'Deposito', saldo_awal: 100000000, status: 'Aktif' }
      ];
      setData(sampleData);
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
        await rekeningAPI.update(editingItem.id, formData);
        setData(prev => prev.map(item => 
          item.id === editingItem.id ? { ...formData, id: editingItem.id } : item
        ));
        alert('Data rekening berhasil diupdate!');
      } else {
        const response = await rekeningAPI.create(formData);
        const newItem = response.data || { ...formData, id: Date.now() };
        setData(prev => [...prev, newItem]);
        alert('Data rekening berhasil ditambahkan!');
      }
      resetForm();
    } catch (error) {
      console.error('Error saving rekening:', error);
      alert('Error saving rekening');
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
        await rekeningAPI.delete(id);
        setData(prev => prev.filter(item => item.id !== id));
        alert('Data berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting rekening:', error);
        alert('Error deleting rekening');
      }
    }
  };

  const resetForm = () => {
    setFormData({ no_rekening: '', nama_rekening: '', bank: '', jenis_rekening: '', saldo_awal: 0, status: 'Aktif' });
    setEditingItem(null);
    setShowForm(false);
  };

  if (loading) return <div className="page-container"><div className="loading-spinner">Loading...</div></div>;

  return (
    <div className="page-container">
      <div className="page-header">
        <h1 className="page-title">Master Rekening</h1>
        <div className="page-actions">
          <button className="btn btn-primary" onClick={() => setShowForm(!showForm)}>
            {showForm ? 'Tutup Form' : 'Tambah Rekening'}
          </button>
        </div>
      </div>

      {showForm && (
        <div className="card mb-4">
          <div className="card-header">
            <h3 className="card-title">{editingItem ? 'Edit Rekening' : 'Tambah Rekening'}</h3>
          </div>
          <div className="card-body">
            <form onSubmit={handleSubmit}>
              <div className="form-grid grid-cols-2">
                <div className="form-group">
                  <label className="form-label">No. Rekening</label>
                  <input type="text" name="no_rekening" value={formData.no_rekening} onChange={handleInputChange} className="form-control" required />
                </div>
                <div className="form-group">
                  <label className="form-label">Nama Rekening</label>
                  <input type="text" name="nama_rekening" value={formData.nama_rekening} onChange={handleInputChange} className="form-control" required />
                </div>
                <div className="form-group">
                  <label className="form-label">Bank</label>
                  <select name="bank" value={formData.bank} onChange={handleInputChange} className="form-control" required>
                    <option value="">Pilih Bank</option>
                    <option value="BCA">BCA</option>
                    <option value="BNI">BNI</option>
                    <option value="BRI">BRI</option>
                    <option value="Mandiri">Mandiri</option>
                  </select>
                </div>
                <div className="form-group">
                  <label className="form-label">Jenis Rekening</label>
                  <select name="jenis_rekening" value={formData.jenis_rekening} onChange={handleInputChange} className="form-control" required>
                    <option value="">Pilih Jenis</option>
                    <option value="Tabungan">Tabungan</option>
                    <option value="Giro">Giro</option>
                    <option value="Deposito">Deposito</option>
                  </select>
                </div>
                <div className="form-group">
                  <label className="form-label">Saldo Awal</label>
                  <input type="number" name="saldo_awal" value={formData.saldo_awal} onChange={handleInputChange} className="form-control" min="0" />
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
        <div className="card-header"><h3 className="card-title">Data Rekening</h3></div>
        <div className="card-body">
          <div className="table-responsive">
            <table className="table">
              <thead>
                <tr><th>No. Rekening</th><th>Nama Rekening</th><th>Bank</th><th>Jenis</th><th>Saldo Awal</th><th>Status</th><th>Aksi</th></tr>
              </thead>
              <tbody>
                {data.map((item) => (
                  <tr key={item.id}>
                    <td>{item.no_rekening}</td>
                    <td>{item.nama_rekening}</td>
                    <td>{item.bank}</td>
                    <td>{item.jenis_rekening}</td>
                    <td className="text-right">Rp {item.saldo_awal.toLocaleString()}</td>
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

export default MasterRekening;
