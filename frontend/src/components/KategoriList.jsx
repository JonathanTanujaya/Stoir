import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function KategoriList({ onEdit, onRefresh }) {
  const [kategori, setKategori] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchKategori = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/kategori`);
      let kategoriData = [];
      if (response.data && Array.isArray(response.data.data)) {
        kategoriData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        kategoriData = response.data;
      }
      setKategori(kategoriData); // Ensure it's always an array
      toast.success('Data kategori berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching kategori:', error);
      toast.error('Gagal memuat data kategori.');
      setKategori([]); // Ensure kategori is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchKategori();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, kodeKategori) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
      try {
        await axios.delete(`${API_URL}/kategori/${kodeDivisi}/${kodeKategori}`);
        fetchKategori();
        toast.success('Kategori berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting kategori:', error);
        toast.error('Gagal menghapus kategori.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data kategori...</div>; // Display loading message
  }

  // Ensure kategori is an array before mapping
  if (!Array.isArray(kategori)) {
    console.error('Kategori state is not an array:', kategori);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Kategori</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>Kode Kategori</th>
            <th>Kategori</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {kategori.map(cat => (
            <tr key={`${cat.KodeDivisi}-${cat.KodeKategori}`}>
              <td>{cat.KodeDivisi}</td>
              <td>{cat.KodeKategori}</td>
              <td>{cat.Kategori}</td>
              <td>{cat.Status ? 'Aktif' : 'Tidak Aktif'}</td>
              <td>
                <button onClick={() => onEdit(cat)}>Edit</button>
                <button onClick={() => handleDelete(cat.KodeDivisi, cat.KodeKategori)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default KategoriList;