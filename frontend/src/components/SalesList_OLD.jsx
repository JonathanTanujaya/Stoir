import { useState, useEffect } from 'react';
import { toast } from 'react-toastify';
import { salesService } from '../config/apiService.js';

function SalesList({ onEdit, onRefresh }) {
  const [sales, setSales] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchSales = async () => {
    setLoading(true);
    try {
      const result = await salesService.getAll();
      if (result.success) {
        setSales(Array.isArray(result.data) ? result.data : []);
        // Success toast dihapus, hanya tampilkan saat aksi
      } else {
        toast.error(result.message);
        setSales([]);
      }
    } catch (error) {
      console.error('Error fetching sales:', error);
      toast.error('Gagal memuat data sales.');
      setSales([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchSales();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, kodeSales) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus sales ini?')) {
      try {
        const result = await salesService.delete(kodeDivisi, kodeSales);
        if (result.success) {
          toast.success(result.message);
          fetchSales();
        } else {
          toast.error(result.message);
        }
      } catch (error) {
        console.error('Error deleting sales:', error);
        toast.error('Gagal menghapus sales.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data sales...</div>;
  }

  if (!Array.isArray(sales)) {
    console.error('Sales state is not an array:', sales);
    return <div>Terjadi kesalahan dalam memuat data.</div>;
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
          {sales.map((sale, index) => (
            <tr key={sale.kodedivisi && sale.kodesales 
              ? `${sale.kodedivisi}-${sale.kodesales}` 
              : `sale-${index}`
            }>
              <td>{sale.kodedivisi || '-'}</td>
              <td>{sale.kodesales || '-'}</td>
              <td>{sale.namasales || '-'}</td>
              <td>{sale.alamat || '-'}</td>
              <td>{sale.nohp || '-'}</td>
              <td>{sale.target || 0}</td>
              <td>{sale.status ? 'Aktif' : 'Tidak Aktif'}</td>
              <td>
                <button 
                  onClick={() => onEdit(sale)}
                  disabled={!sale.kodedivisi || !sale.kodesales}
                >
                  Edit
                </button>
                <button 
                  onClick={() => handleDelete(sale.kodedivisi, sale.kodesales)}
                  disabled={!sale.kodedivisi || !sale.kodesales}
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

export default SalesList;
