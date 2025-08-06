import { useState, useEffect } from 'react';
import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

function SalesForm({ sale, onSave, onCancel }) {
  const [formData, setFormData] = useState({
    KodeDivisi: '',
    KodeSales: '',
    NamaSales: '',
    Alamat: '',
    NoHP: '',
    Target: '',
    Status: true,
  });

  useEffect(() => {
    if (sale) {
      setFormData(sale);
    }
  }, [sale]);

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
      if (sale) {
        // Update existing sale
        await axios.put(`${API_URL}/sales/${sale.KodeDivisi}/${sale.KodeSales}`, formData);
      } else {
        // Create new sale
        await axios.post(`${API_URL}/sales`, formData);
      }
      onSave();
    } catch (error) {
      console.error('Error saving sales:', error);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <div>
        <label>Kode Divisi:</label>
        <input type="text" name="KodeDivisi" value={formData.KodeDivisi} onChange={handleChange} required />
      </div>
      <div>
        <label>Kode Sales:</label>
        <input type="text" name="KodeSales" value={formData.KodeSales} onChange={handleChange} required />
      </div>
      <div>
        <label>Nama Sales:</label>
        <input type="text" name="NamaSales" value={formData.NamaSales} onChange={handleChange} />
      </div>
      <div>
        <label>Alamat:</label>
        <input type="text" name="Alamat" value={formData.Alamat} onChange={handleChange} />
      </div>
      <div>
        <label>No HP:</label>
        <input type="text" name="NoHP" value={formData.NoHP} onChange={handleChange} />
      </div>
      <div>
        <label>Target:</label>
        <input type="number" name="Target" value={formData.Target} onChange={handleChange} />
      </div>
      <div>
        <label>Status:</label>
        <input type="checkbox" name="Status" checked={formData.Status} onChange={handleChange} />
      </div>
      <button type="submit">Simpan</button>
      <button type="button" onClick={onCancel}>Batal</button>
    </form>
  );
}

export default SalesForm;
