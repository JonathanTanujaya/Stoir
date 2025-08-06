import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function MDokumenList({ onEdit, onRefresh }) {
  const [dokumen, setDokumen] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchDokumen = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/mdokumen`);
      let dokumenData = [];
      if (response.data && Array.isArray(response.data.data)) {
        dokumenData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        dokumenData = response.data;
      }
      setDokumen(dokumenData); // Ensure it's always an array
      toast.success('Data dokumen berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching dokumen:', error);
      toast.error('Gagal memuat data dokumen.');
      setDokumen([]); // Ensure dokumen is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchDokumen();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, kodeDok) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
      try {
        await axios.delete(`${API_URL}/mdokumen/${kodeDivisi}/${kodeDok}`);
        fetchDokumen();
        toast.success('Dokumen berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting dokumen:', error);
        toast.error('Gagal menghapus dokumen.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data dokumen...</div>; // Display loading message
  }

  // Ensure dokumen is an array before mapping
  if (!Array.isArray(dokumen)) {
    console.error('Dokumen state is not an array:', dokumen);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Dokumen</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>Kode Dokumen</th>
            <th>Nomor</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {dokumen.map(doc => (
            <tr key={`${doc.KodeDivisi}-${doc.KodeDok}`}>
              <td>{doc.KodeDivisi}</td>
              <td>{doc.KodeDok}</td>
              <td>{doc.Nomor}</td>
              <td>
                <button onClick={() => onEdit(doc)}>Edit</button>
                <button onClick={() => handleDelete(doc.KodeDivisi, doc.KodeDok)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default MDokumenList;