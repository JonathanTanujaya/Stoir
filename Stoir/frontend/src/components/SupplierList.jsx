import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function SupplierList({ onEdit, onRefresh }) {
  const [suppliers, setSuppliers] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchSuppliers = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/suppliers`); // Changed from /supplier to /suppliers
      let suppliersData = [];
      if (response.data && Array.isArray(response.data.data)) {
        suppliersData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        suppliersData = response.data;
      }
      setSuppliers(suppliersData); // Ensure it's always an array
      toast.success('Data supplier berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching suppliers:', error);
      toast.error('Gagal memuat data supplier.');
      setSuppliers([]); // Ensure suppliers is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchSuppliers();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, kodeSupplier) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus supplier ini?')) {
      try {
        await axios.delete(`${API_URL}/suppliers/${kodeDivisi}/${kodeSupplier}`); // Changed from /supplier to /suppliers
        fetchSuppliers();
        toast.success('Supplier berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting supplier:', error);
        toast.error('Gagal menghapus supplier.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data supplier...</div>; // Display loading message
  }

  // Ensure suppliers is an array before mapping
  if (!Array.isArray(suppliers)) {
    console.error('Suppliers state is not an array:', suppliers);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Supplier</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>Kode Supplier</th>
            <th>Nama Supplier</th>
            <th>Alamat</th>
            <th>Telp</th>
            <th>Contact</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {suppliers.map(supplier => (
            <tr key={`${supplier.KodeDivisi}-${supplier.KodeSupplier}`}>
              <td>{supplier.KodeDivisi}</td>
              <td>{supplier.KodeSupplier}</td>
              <td>{supplier.NamaSupplier}</td>
              <td>{supplier.Alamat}</td>
              <td>{supplier.Telp}</td>
              <td>{supplier.contact}</td>
              <td>{supplier.status ? 'Aktif' : 'Tidak Aktif'}</td>
              <td>
                <button onClick={() => onEdit(supplier)}>Edit</button>
                <button onClick={() => handleDelete(supplier.KodeDivisi, supplier.KodeSupplier)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default SupplierList;
