import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import Card from '../../../components/ui/Card';
import Button from '../../../components/ui/Button';
import Input from '../../../components/ui/Input';
import Select from '../../../components/ui/Select';
import { toast } from 'react-toastify';
import api from '../../../services/api';

function SparepartFormPage() {
  const navigate = useNavigate();
  const { id } = useParams();
  const isEdit = !!id;
  const [formData, setFormData] = useState({
    kode_barang: '',
    nama_barang: '',
    kode_kategori: '',
    satuan: 'pcs',
    stok: '',
    min_stok: '',
    harga_beli: '',
    harga_jual: '',
  });
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    api.get('/kategori').then(res => setCategories(res.data || []));
    if (isEdit) {
      api.get(`/spareparts/${id}`).then(res => {
        setFormData(res.data || {});
      });
    }
  }, [id, isEdit]);

  const handleChange = e => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async e => {
    e.preventDefault();
    setLoading(true);
    try {
      if (isEdit) {
        await api.put(`/spareparts/${id}`, formData);
        toast.success('Sparepart berhasil diupdate!');
      } else {
        await api.post('/spareparts', formData);
        toast.success('Sparepart berhasil ditambahkan!');
      }
      navigate('/master/sparepart');
    } catch (err) {
      toast.error('Gagal menyimpan data!');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ padding: '24px', maxWidth: '800px', margin: '0 auto' }}>
      {/* Header */}
      <div style={{ marginBottom: '32px' }}>
        <button
          onClick={() => navigate('/master/sparepart')}
          style={{
            padding: '8px 0',
            fontSize: '14px',
            color: '#6b7280',
            backgroundColor: 'transparent',
            border: 'none',
            cursor: 'pointer',
            marginBottom: '8px'
          }}
        >
          ← Kembali ke Daftar Sparepart
        </button>
        <h1 style={{ fontSize: '32px', fontWeight: '600', margin: 0, color: '#1f2937' }}>
          {isEdit ? 'Edit Sparepart' : 'Tambah Sparepart Baru'}
        </h1>
      </div>

      {/* Form */}
      <div style={{
        backgroundColor: 'white',
        borderRadius: '12px',
        border: '1px solid #e5e7eb',
        padding: '32px'
      }}>
        <form onSubmit={handleSubmit}>
          <div style={{ display: 'grid', gap: '24px' }}>
            {/* Row 1: Kode & Kategori */}
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px' }}>
              <Input 
                label="Kode Barang" 
                name="kode_barang" 
                value={formData.kode_barang} 
                onChange={handleChange} 
                required 
                disabled={isEdit}
                placeholder="BRG001"
              />
              <Select label="Kategori" name="kode_kategori" value={formData.kode_kategori} onChange={handleChange} required>
                <option value="">Pilih Kategori</option>
                {categories.map(cat => (
                  <option key={cat.kode_kategori} value={cat.kode_kategori}>{cat.nama_kategori}</option>
                ))}
              </Select>
            </div>

            {/* Row 2: Nama (Full Width) */}
            <Input 
              label="Nama Sparepart" 
              name="nama_barang" 
              value={formData.nama_barang} 
              onChange={handleChange} 
              required
              placeholder="Masukkan nama sparepart..."
            />

            {/* Row 3: Satuan & Stok */}
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '20px' }}>
              <Select label="Satuan" name="satuan" value={formData.satuan} onChange={handleChange} required>
                <option value="pcs">Pcs</option>
                <option value="box">Box</option>
                <option value="set">Set</option>
                <option value="unit">Unit</option>
                <option value="pack">Pack</option>
              </Select>
              <Input 
                label="Stok Saat Ini" 
                type="number" 
                name="stok" 
                value={formData.stok} 
                onChange={handleChange} 
                required
                placeholder="0"
              />
              <Input 
                label="Minimum Stok" 
                type="number" 
                name="min_stok" 
                value={formData.min_stok} 
                onChange={handleChange} 
                required
                placeholder="0"
              />
            </div>

            {/* Row 4: Harga */}
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '20px' }}>
              <Input 
                label="Harga Beli" 
                type="number" 
                name="harga_beli" 
                value={formData.harga_beli} 
                onChange={handleChange} 
                required
                placeholder="0"
              />
              <Input 
                label="Harga Jual" 
                type="number" 
                name="harga_jual" 
                value={formData.harga_jual} 
                onChange={handleChange} 
                required
                placeholder="0"
              />
            </div>
          </div>

          {/* Actions */}
          <div style={{ 
            display: 'flex', 
            gap: '12px', 
            marginTop: '40px', 
            paddingTop: '24px',
            borderTop: '1px solid #f3f4f6'
          }}>
            <Button 
              type="button" 
              variant="secondary" 
              onClick={() => navigate('/master/sparepart')}
              style={{ marginRight: 'auto' }}
            >
              Batal
            </Button>
            <Button type="submit" disabled={loading}>
              {loading ? 'Menyimpan...' : (isEdit ? 'Update Sparepart' : 'Simpan Sparepart')}
            </Button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default SparepartFormPage;
