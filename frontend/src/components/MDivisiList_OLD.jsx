import { useState, useEffect } from 'react';
import { toast } from 'react-toastify';
import { mdivisiService } from '../config/apiService.js';

function MDivisiList({ onEdit, onRefresh }) {
  const [divisi, setDivisi] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchDivisi = async () => {
    setLoading(true);
    try {
      const result = await mdivisiService.getAll();
      if (result.success) {
        setDivisi(Array.isArray(result.data) ? result.data : []);
      } else {
        console.error('Error fetching divisi:', result.error);
        toast.error(result.message || 'Gagal memuat data divisi.');
        setDivisi([]);
      }
    } catch (error) {
      console.error('Error fetching divisi:', error);
      toast.error('Gagal memuat data divisi.');
      setDivisi([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchDivisi();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus divisi ini?')) {
      try {
        const result = await mdivisiService.delete(kodeDivisi);
        if (result.success) {
          fetchDivisi();
          toast.success(result.message || 'Divisi berhasil dihapus!');
        } else {
          toast.error(result.message || 'Gagal menghapus divisi.');
        }
      } catch (error) {
        console.error('Error deleting divisi:', error);
        toast.error('Gagal menghapus divisi.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data divisi...</div>; // Display loading message
  }

  // Ensure divisi is an array before mapping
  if (!Array.isArray(divisi)) {
    console.error('Divisi state is not an array:', divisi);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Divisi</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>Divisi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {divisi.map(div => (
            <tr key={div.KodeDivisi}>
              <td>{div.KodeDivisi}</td>
              <td>{div.Divisi}</td>
              <td>
                <button onClick={() => onEdit(div)}>Edit</button>
                <button onClick={() => handleDelete(div.KodeDivisi)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default MDivisiList;