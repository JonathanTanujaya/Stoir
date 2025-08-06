import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function CustomerList({ onEdit, onRefresh }) {
  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  console.log('CustomerList: Initial render, customers:', customers); // Log 1

  const fetchCustomers = async () => {
    setLoading(true); // Set loading to true before fetching
    console.log('CustomerList: fetchCustomers called'); // Log 2
    try {
      const response = await axios.get(`${API_URL}/customers`);
      let customerData = [];
      if (response.data && Array.isArray(response.data.data)) {
        customerData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        customerData = response.data;
      }
      setCustomers(customerData); // Ensure it's always an array
      console.log('CustomerList: Data fetched, setting customers to:', customerData); // Log 3
      toast.success('Data pelanggan berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching customers:', error);
      toast.error('Gagal memuat data pelanggan.');
      setCustomers([]); // Ensure customers is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    console.log('CustomerList: useEffect triggered'); // Log 4
    fetchCustomers();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, kodeCust) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')) {
      try {
        await axios.delete(`${API_URL}/customers/${kodeDivisi}/${kodeCust}`);
        fetchCustomers();
        toast.success('Pelanggan berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting customer:', error);
        toast.error('Gagal menghapus pelanggan.');
      }
    }
  };

  console.log('CustomerList: Before return, loading:', loading, 'customers:', customers); // Log 5

  if (loading) {
    return <div>Memuat data pelanggan...</div>; // Display loading message
  }

  // Ensure customers is an array before mapping
  if (!Array.isArray(customers)) {
    console.error('Customers state is not an array:', customers);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Pelanggan</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>Kode Pelanggan</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>Telp</th>
            <th>Credit Limit</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {customers.map(customer => (
            <tr key={`${customer.KodeDivisi}-${customer.KodeCust}`}>
              <td>{customer.KodeDivisi}</td>
              <td>{customer.KodeCust}</td>
              <td>{customer.NamaCust}</td>
              <td>{customer.Alamat}</td>
              <td>{customer.Telp}</td>
              <td>{customer.CreditLimit}</td>
              <td>{customer.Status ? 'Aktif' : 'Tidak Aktif'}</td>
              <td>
                <button onClick={() => onEdit(customer)}>Edit</button>
                <button onClick={() => handleDelete(customer.KodeDivisi, customer.KodeCust)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default CustomerList;