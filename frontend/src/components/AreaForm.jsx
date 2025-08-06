import { useState, useEffect } from 'react';
import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

function AreaForm({ area, onSave, onCancel }) {
  const [formData, setFormData] = useState({
    KodeDivisi: '',
    KodeArea: '',
    Area: '',
    status: true,
  });

  useEffect(() => {
    if (area) {
      setFormData(area);
    }
  }, [area]);

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
      if (area) {
        // Update existing area
        await axios.put(`${API_URL}/area/${area.KodeDivisi}/${area.KodeArea}`, formData);
      } else {
        // Create new area
        await axios.post(`${API_URL}/area`, formData);
      }
      onSave();
    } catch (error) {
      console.error('Error saving area:', error);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <div>
        <label>Kode Divisi:</label>
        <input type="text" name="KodeDivisi" value={formData.KodeDivisi} onChange={handleChange} required />
      </div>
      <div>
        <label>Kode Area:</label>
        <input type="text" name="KodeArea" value={formData.KodeArea} onChange={handleChange} required />
      </div>
      <div>
        <label>Area:</label>
        <input type="text" name="Area" value={formData.Area} onChange={handleChange} />
      </div>
      <div>
        <label>Status:</label>
        <input type="checkbox" name="status" checked={formData.status} onChange={handleChange} />
      </div>
      <button type="submit">Simpan</button>
      <button type="button" onClick={onCancel}>Batal</button>
    </form>
  );
}

export default AreaForm;
