import { useState, useEffect } from 'react';
import { customerService } from '../config/apiService.js';
import { toast } from 'react-toastify';



function CustomerList({ onEdit, onRefresh }) {
  const [customers, setCustomers] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchCustomers = async () => {
    setLoading(true);
    try {
      const result = await customerService.getAll();
      if (result.success) {
        setCustomers(Array.isArray(result.data) ? result.data : []);
        // Success toast dihapus, hanya tampilkan saat aksi
      } else {
        toast.error(result.message);
        setCustomers([]);
      }
    } catch (error) {
      console.error('Error fetching customers:', error);
      toast.error('Gagal memuat data pelanggan.');
      setCustomers([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchCustomers();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, kodeCust) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus pelanggan ini?')) {
      try {
        const result = await customerService.delete(kodeDivisi, kodeCust);
        if (result.success) {
          toast.success(result.message);
          fetchCustomers();
        } else {
          toast.error(result.message);
        }
      } catch (error) {
        console.error('Error deleting customer:', error);
        toast.error('Gagal menghapus pelanggan.');
      }
    }
  };

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
          {customers.map((customer, index) => (
            <tr key={customer.KodeDivisi && customer.KodeCust 
              ? `${customer.KodeDivisi}-${customer.KodeCust}` 
              : `customer-${index}`
            }>
              <td>{customer.KodeDivisi || '-'}</td>
              <td>{customer.KodeCust || '-'}</td>
              <td>{customer.NamaCust || '-'}</td>
              <td>{customer.Alamat || '-'}</td>
              <td>{customer.Telp || '-'}</td>
              <td>{customer.CreditLimit || 0}</td>
              <td>{customer.Status ? 'Aktif' : 'Tidak Aktif'}</td>
              <td>
                <button 
                  onClick={() => onEdit(customer)}
                  disabled={!customer.KodeDivisi || !customer.KodeCust}
                >
                  Edit
                </button>
                <button 
                  onClick={() => handleDelete(customer.KodeDivisi, customer.KodeCust)}
                  disabled={!customer.KodeDivisi || !customer.KodeCust}
                >
                  Hapus
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default CustomerList;