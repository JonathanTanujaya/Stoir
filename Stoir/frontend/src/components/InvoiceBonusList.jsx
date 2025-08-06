import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function InvoiceBonusList({ onEdit, onRefresh }) {
  const [invoices, setInvoices] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchInvoices = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/invoice-bonus`);
      let invoiceData = [];
      if (response.data && Array.isArray(response.data.data)) {
        invoiceData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        invoiceData = response.data;
      }
      setInvoices(invoiceData); // Ensure it's always an array
      toast.success('Data invoice bonus berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching invoice bonus:', error);
      toast.error('Gagal memuat data invoice bonus.');
      setInvoices([]); // Ensure invoices is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchInvoices();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, noInvoiceBonus) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus invoice bonus ini?')) {
      try {
        await axios.delete(`${API_URL}/invoice-bonus/${kodeDivisi}/${noInvoiceBonus}`);
        fetchInvoices();
        toast.success('Invoice bonus berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting invoice bonus:', error);
        toast.error('Gagal menghapus invoice bonus.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data invoice bonus...</div>; // Display loading message
  }

  // Ensure invoices is an array before mapping
  if (!Array.isArray(invoices)) {
    console.error('Invoices state is not an array:', invoices);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Invoice Bonus</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>No Invoice Bonus</th>
            <th>Tgl Faktur</th>
            <th>Kode Customer</th>
            <th>Keterangan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {invoices.map(invoice => (
            <tr key={`${invoice.KodeDivisi}-${invoice.NoInvoiceBonus}`}>
              <td>{invoice.KodeDivisi}</td>
              <td>{invoice.NoInvoiceBonus}</td>
              <td>{invoice.TglFaktur}</td>
              <td>{invoice.KodeCust}</td>
              <td>{invoice.Ket}</td>
              <td>{invoice.Status}</td>
              <td>
                <button onClick={() => onEdit(invoice)}>Edit</button>
                <button onClick={() => handleDelete(invoice.KodeDivisi, invoice.NoInvoiceBonus)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default InvoiceBonusList;