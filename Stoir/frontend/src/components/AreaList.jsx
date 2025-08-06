import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function AreaList({ onEdit, onRefresh }) {
  const [areas, setAreas] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchAreas = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/areas`); // Changed from /area to /areas
      let areaData = [];
      if (response.data && Array.isArray(response.data.data)) {
        areaData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        areaData = response.data;
      }
      setAreas(areaData); // Ensure it's always an array
      toast.success('Data area berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching areas:', error);
      toast.error('Gagal memuat data area.');
      setAreas([]); // Ensure areas is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchAreas();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, kodeArea) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus area ini?')) {
      try {
        await axios.delete(`${API_URL}/areas/${kodeDivisi}/${kodeArea}`); // Changed from /area to /areas
        fetchAreas();
        toast.success('Area berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting area:', error);
        toast.error('Gagal menghapus area.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data area...</div>; // Display loading message
  }

  // Ensure areas is an array before mapping
  if (!Array.isArray(areas)) {
    console.error('Areas state is not an array:', areas);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Area</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>Kode Area</th>
            <th>Area</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {areas.map(area => (
            <tr key={`${area.KodeDivisi}-${area.KodeArea}`}>
              <td>{area.KodeDivisi}</td>
              <td>{area.KodeArea}</td>
              <td>{area.Area}</td>
              <td>{area.status ? 'Aktif' : 'Tidak Aktif'}</td>
              <td>
                <button onClick={() => onEdit(area)}>Edit</button>
                <button onClick={() => handleDelete(area.KodeDivisi, area.KodeArea)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default AreaList;
