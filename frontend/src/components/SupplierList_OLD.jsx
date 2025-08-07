import { useState, useEffect } from 'react';
import { toast } from 'react-toastify';
import { supplierService } from '../config/apiService.js';

function SupplierList({ onEdit, onRefresh }) {
  const [suppliers, setSuppliers] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchSuppliers = async () => {
    setLoading(true);
    try {
      const result = await supplierService.getAll();
      if (result.success) {
        setSuppliers(Array.isArray(result.data) ? result.data : []);
      } else {
        console.error('Error fetching suppliers:', result.error);
        toast.error(result.message || 'Gagal memuat data supplier.');
        setSuppliers([]);
      }
    } catch (error) {
      console.error('Error fetching suppliers:', error);
      toast.error('Gagal memuat data supplier.');
      setSuppliers([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchSuppliers();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, kodeSupplier) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus supplier ini?')) {
      try {
        // For supplier, we might need to use composite key or check with API structure
        const id = `${kodeDivisi}-${kodeSupplier}`; // Adjust based on actual API requirement
        const result = await supplierService.delete(id);
        if (result.success) {
          fetchSuppliers();
          toast.success(result.message || 'Supplier berhasil dihapus!');
        } else {
          toast.error(result.message || 'Gagal menghapus supplier.');
        }
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
