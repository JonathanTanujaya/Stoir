import { useState, useEffect } from 'react';
import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

function InvoiceBonusForm({ invoiceBonus, onSave, onCancel }) {
  const [formData, setFormData] = useState({
    KodeDivisi: '',
    NoInvoiceBonus: '',
    TglFaktur: '',
    KodeCust: '',
    Ket: '',
    Status: '',
    username: '',
    // For InvoiceBonusDetail
    KodeBarang: '',
    QtySupply: '',
    HargaNett: '',
  });

  useEffect(() => {
    if (invoiceBonus) {
      setFormData(invoiceBonus);
    }
  }, [invoiceBonus]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prevData => ({
      ...prevData,
      [name]: value
    }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (invoiceBonus) {
        // Update existing invoice bonus
        await axios.put(`${API_URL}/invoice-bonus/${invoiceBonus.KodeDivisi}/${invoiceBonus.NoInvoiceBonus}`, formData);
      } else {
        // Create new invoice bonus
        await axios.post(`${API_URL}/invoice-bonus`, formData);
      }
      onSave();
    } catch (error) {
      console.error('Error saving invoice bonus:', error);
    }
  };

  return (
    <form onSubmit={handleSubmit}>
      <h2>{invoiceBonus ? 'Edit' : 'Tambah'} Invoice Bonus</h2>
      <div>
        <label>Kode Divisi:</label>
        <input type="text" name="KodeDivisi" value={formData.KodeDivisi} onChange={handleChange} required />
      </div>
      <div>
        <label>No Invoice Bonus:</label>
        <input type="text" name="NoInvoiceBonus" value={formData.NoInvoiceBonus} onChange={handleChange} required />
      </div>
      <div>
        <label>Tgl Faktur:</label>
        <input type="date" name="TglFaktur" value={formData.TglFaktur} onChange={handleChange} />
      </div>
      <div>
        <label>Kode Customer:</label>
        <input type="text" name="KodeCust" value={formData.KodeCust} onChange={handleChange} />
      </div>
      <div>
        <label>Keterangan:</label>
        <input type="text" name="Ket" value={formData.Ket} onChange={handleChange} />
      </div>
      <div>
        <label>Status:</label>
        <input type="text" name="Status" value={formData.Status} onChange={handleChange} />
      </div>
      <div>
        <label>Username:</label>
        <input type="text" name="username" value={formData.username} onChange={handleChange} />
      </div>

      <h3>Detail Invoice Bonus</h3>
      <div>
        <label>Kode Barang:</label>
        <input type="text" name="KodeBarang" value={formData.KodeBarang} onChange={handleChange} />
      </div>
      <div>
        <label>Qty Supply:</label>
        <input type="number" name="QtySupply" value={formData.QtySupply} onChange={handleChange} />
      </div>
      <div>
        <label>Harga Nett:</label>
        <input type="number" name="HargaNett" value={formData.HargaNett} onChange={handleChange} />
      </div>

      <button type="submit">Simpan</button>
      <button type="button" onClick={onCancel}>Batal</button>
    </form>
  );
}

export default InvoiceBonusForm;
