import { useState, useEffect } from 'react';
import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

function BarangForm({ item, onSave, onCancel }) {
  const [formData, setFormData] = useState({
    KodeDivisi: '',
    KodeBarang: '',
    NamaBarang: '',
    KodeKategori: '',
    HargaList: '',
    HargaJual: '',
    HargaList2: '',
    HargaJual2: '',
    Satuan: '',
    Disc1: '',
    Disc2: '',
    merk: '',
    Barcode: '',
    status: true,
    Lokasi: '',
    StokMin: '',
    Checklist: false,
  });

  useEffect(() => {
    if (item) {
      setFormData(item);
    }
  }, [item]);

  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData(prevData => ({
      ...prevData,
      [name]: type === 'checkbox' ? checked : value
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (item) {
        // Update existing item
        await axios.put(`${API_URL}/barang/${item.KodeDivisi}/${item.KodeBarang}`, formData);
      } else {
        // Create new item
        await axios.post(`${API_URL}/barang`, formData);
      }
      onSave();
    } catch (error) {
      console.error('Error saving barang:', error);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <div>
        <label>Kode Divisi:</label>
        <input type="text" name="KodeDivisi" value={formData.KodeDivisi} onChange={handleChange} required />
      </div>
      <div>
        <label>Kode Barang:</label>
        <input type="text" name="KodeBarang" value={formData.KodeBarang} onChange={handleChange} required />
      </div>
      <div>
        <label>Nama Barang:</label>
        <input type="text" name="NamaBarang" value={formData.NamaBarang} onChange={handleChange} />
      </div>
      <div>
        <label>Kode Kategori:</label>
        <input type="text" name="KodeKategori" value={formData.KodeKategori} onChange={handleChange} />
      </div>
      <div>
        <label>Harga List:</label>
        <input type="number" name="HargaList" value={formData.HargaList} onChange={handleChange} />
      </div>
      <div>
        <label>Harga Jual:</label>
        <input type="number" name="HargaJual" value={formData.HargaJual} onChange={handleChange} />
      </div>
      <div>
        <label>Harga List 2:</label>
        <input type="number" name="HargaList2" value={formData.HargaList2} onChange={handleChange} />
      </div>
      <div>
        <label>Harga Jual 2:</label>
        <input type="number" name="HargaJual2" value={formData.HargaJual2} onChange={handleChange} />
      </div>
      <div>
        <label>Satuan:</label>
        <input type="text" name="Satuan" value={formData.Satuan} onChange={handleChange} />
      </div>
      <div>
        <label>Disc 1:</label>
        <input type="number" name="Disc1" value={formData.Disc1} onChange={handleChange} />
      </div>
      <div>
        <label>Disc 2:</label>
        <input type="number" name="Disc2" value={formData.Disc2} onChange={handleChange} />
      </div>
      <div>
        <label>Merk:</label>
        <input type="text" name="merk" value={formData.merk} onChange={handleChange} />
      </div>
      <div>
        <label>Barcode:</label>
        <input type="text" name="Barcode" value={formData.Barcode} onChange={handleChange} />
      </div>
      <div>
        <label>Status:</label>
        <input type="checkbox" name="status" checked={formData.status} onChange={handleChange} />
      </div>
      <div>
        <label>Lokasi:</label>
        <input type="text" name="Lokasi" value={formData.Lokasi} onChange={handleChange} />
      </div>
      <div>
        <label>Stok Min:</label>
        <input type="number" name="StokMin" value={formData.StokMin} onChange={handleChange} />
      </div>
      <div>
        <label>Checklist:</label>
        <input type="checkbox" name="Checklist" checked={formData.Checklist} onChange={handleChange} />
      </div>
      <button type="submit">Simpan</button>
      <button type="button" onClick={onCancel}>Batal</button>
    </form>
  );
}

export default BarangForm;
