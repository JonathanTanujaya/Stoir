import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function MDivisiList({ onEdit, onRefresh }) {
  const [divisi, setDivisi] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchDivisi = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/mdivisi`);
      let divisiData = [];
      if (response.data && Array.isArray(response.data.data)) {
        divisiData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        divisiData = response.data;
      }
      setDivisi(divisiData); // Ensure it's always an array
      toast.success('Data divisi berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching divisi:', error);
      toast.error('Gagal memuat data divisi.');
      setDivisi([]); // Ensure divisi is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchDivisi();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus divisi ini?')) {
      try {
        await axios.delete(`${API_URL}/mdivisi/${kodeDivisi}`);
        fetchDivisi();
        toast.success('Divisi berhasil dihapus!');
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