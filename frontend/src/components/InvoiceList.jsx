import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function InvoiceList({ onEdit, onRefresh }) {
  const [invoices, setInvoices] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchInvoices = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/invoices`);
      let invoiceData = [];
      if (response.data && Array.isArray(response.data.data)) {
        invoiceData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        invoiceData = response.data;
      }
      setInvoices(invoiceData); // Ensure it's always an array
      toast.success('Data invoice berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching invoices:', error);
      toast.error('Gagal memuat data invoice.');
      setInvoices([]); // Ensure invoices is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchInvoices();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, noInvoice) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus invoice ini?')) {
      try {
        await axios.delete(`${API_URL}/invoices/${kodeDivisi}/${noInvoice}`);
        fetchInvoices();
        toast.success('Invoice berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting invoice:', error);
        toast.error('Gagal menghapus invoice.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data invoice...</div>; // Display loading message
  }

  // Ensure invoices is an array before mapping
  if (!Array.isArray(invoices)) {
    console.error('Invoices state is not an array:', invoices);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Invoice</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>No Invoice</th>
            <th>Tgl Faktur</th>
            <th>Kode Customer</th>
            <th>Total</th>
            <th>Grand Total</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {invoices.map(invoice => (
            <tr key={`${invoice.KodeDivisi}-${invoice.NoInvoice}`}>
              <td>{invoice.KodeDivisi}</td>
              <td>{invoice.NoInvoice}</td>
              <td>{invoice.TglFaktur}</td>
              <td>{invoice.KodeCust}</td>
              <td>{invoice.Total}</td>
              <td>{invoice.GrandTotal}</td>
              <td>{invoice.Status}</td>
              <td>
                <button onClick={() => onEdit(invoice)}>Edit</button>
                <button onClick={() => handleDelete(invoice.KodeDivisi, invoice.NoInvoice)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default InvoiceList;