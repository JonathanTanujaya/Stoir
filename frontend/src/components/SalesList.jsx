import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function SalesList({ onEdit, onRefresh }) {
  const [sales, setSales] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchSales = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/sales`);
      let salesData = [];
      if (response.data && Array.isArray(response.data.data)) {
        salesData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        salesData = response.data;
      }
      setSales(salesData); // Ensure it's always an array
      toast.success('Data sales berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching sales:', error);
      toast.error('Gagal memuat data sales.');
      setSales([]); // Ensure sales is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchSales();
  }, [onRefresh]); // Re-fetch when onRefresh prop changes (triggered by parent)

  const handleDelete = async (kodeDivisi, kodeSales) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus sales ini?')) {
      try {
        await axios.delete(`${API_URL}/sales/${kodeDivisi}/${kodeSales}`);
        fetchSales(); // Refresh list after deletion
        toast.success('Sales berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting sales:', error);
        toast.error('Gagal menghapus sales.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data sales...</div>; // Display loading message
  }

  // Ensure sales is an array before mapping
  if (!Array.isArray(sales)) {
    console.error('Sales state is not an array:', sales);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Sales</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>Kode Sales</th>
            <th>Nama Sales</th>
            <th>Alamat</th>
            <th>No HP</th>
            <th>Target</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {sales.map(sale => (
            <tr key={`${sale.KodeDivisi}-${sale.KodeSales}`}>
              <td>{sale.KodeDivisi}</td>
              <td>{sale.KodeSales}</td>
              <td>{sale.NamaSales}</td>
              <td>{sale.Alamat}</td>
              <td>{sale.NoHP}</td>
              <td>{sale.Target}</td>
              <td>{sale.Status ? 'Aktif' : 'Tidak Aktif'}</td>
              <td>
                <button onClick={() => onEdit(sale)}>Edit</button>
                <button onClick={() => handleDelete(sale.KodeDivisi, sale.KodeSales)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default SalesList;
