import { useState, useEffect } from 'react';
import { toast } from 'react-toastify';
import { kategoriService } from '../config/apiService.js';

function KategoriList({ onEdit, onRefresh }) {
  const [kategori, setKategori] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchKategori = async () => {
    setLoading(true);
    try {
      const result = await kategoriService.getAll();
      if (result.success) {
        setKategori(Array.isArray(result.data) ? result.data : []);
        toast.success(result.message);
      } else {
        toast.error(result.message);
        setKategori([]);
      }
    } catch (error) {
      console.error('Error fetching kategori:', error);
      toast.error('Gagal memuat data kategori');
      setKategori([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchKategori();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, kodeKategori) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
      try {
        const result = await kategoriService.delete(kodeDivisi, kodeKategori);
        if (result.success) {
          toast.success(result.message);
          fetchKategori();
        } else {
          toast.error(result.message);
        }
      } catch (error) {
        console.error('Error deleting kategori:', error);
        toast.error('Gagal menghapus kategori');
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
          {kategori.map((cat, index) => (
            <tr key={cat.kodedivisi && cat.kodekategori 
              ? `${cat.kodedivisi}-${cat.kodekategori}` 
              : `kategori-${index}`
            }>
              <td>{cat.kodedivisi || '-'}</td>
              <td>{cat.kodekategori || '-'}</td>
              <td>{cat.kategori || '-'}</td>
              <td>{cat.status ? 'Aktif' : 'Tidak Aktif'}</td>
              <td>
                <button 
                  onClick={() => onEdit(cat)}
                  disabled={!cat.kodedivisi || !cat.kodekategori}
                >
                  Edit
                </button>
                <button 
                  onClick={() => handleDelete(cat.kodedivisi, cat.kodekategori)}
                  disabled={!cat.kodedivisi || !cat.kodekategori}
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

export default KategoriList;