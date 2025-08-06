import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function BarangList({ onEdit, onRefresh }) {
  const [barang, setBarang] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchBarang = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/barang`);
      let barangData = [];
      if (response.data && Array.isArray(response.data.data)) {
        barangData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        barangData = response.data;
      }
      setBarang(barangData); // Ensure it's always an array
      toast.success('Data barang berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching barang:', error);
      toast.error('Gagal memuat data barang.');
      setBarang([]); // Ensure barang is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchBarang();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, kodeBarang) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
      try {
        await axios.delete(`${API_URL}/barang/${kodeDivisi}/${kodeBarang}`);
        fetchBarang();
        toast.success('Barang berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting barang:', error);
        toast.error('Gagal menghapus barang.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data barang...</div>; // Display loading message
  }

  // Ensure barang is an array before mapping
  if (!Array.isArray(barang)) {
    console.error('Barang state is not an array:', barang);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Barang</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Kategori</th>
            <th>Harga Jual</th>
            <th>Satuan</th>
            <th>Merk</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {barang.map(item => (
            <tr key={`${item.KodeDivisi}-${item.KodeBarang}`}>
              <td>{item.KodeDivisi}</td>
              <td>{item.KodeBarang}</td>
              <td>{item.NamaBarang}</td>
              <td>{item.KodeKategori}</td>
              <td>{item.HargaJual}</td>
              <td>{item.Satuan}</td>
              <td>{item.merk}</td>
              <td>{item.status ? 'Aktif' : 'Tidak Aktif'}</td>
              <td>
                <button onClick={() => onEdit(item)}>Edit</button>
                <button onClick={() => handleDelete(item.KodeDivisi, item.KodeBarang)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default BarangList;