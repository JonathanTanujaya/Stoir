import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function MCOAList({ onEdit, onRefresh }) {
  const [coas, setCoas] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchCoas = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/mcoa`);
      let coasData = [];
      if (response.data && Array.isArray(response.data.data)) {
        coasData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        coasData = response.data;
      }
      setCoas(coasData); // Ensure it's always an array
      toast.success('Data COA berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching COAs:', error);
      toast.error('Gagal memuat data COA.');
      setCoas([]); // Ensure coas is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchCoas();
  }, [onRefresh]);

  const handleDelete = async (kodeCOA) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus COA ini?')) {
      try {
        await axios.delete(`${API_URL}/mcoa/${kodeCOA}`);
        fetchCoas();
        toast.success('COA berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting COA:', error);
        toast.error('Gagal menghapus COA.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data COA...</div>; // Display loading message
  }

  // Ensure coas is an array before mapping
  if (!Array.isArray(coas)) {
    console.error('COAs state is not an array:', coas);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar COA</h2>
      <table>
        <thead>
          <tr>
            <th>Kode COA</th>
            <th>Nama COA</th>
            <th>Saldo Normal</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {coas.map(coa => (
            <tr key={coa.KodeCOA}>
              <td>{coa.KodeCOA}</td>
              <td>{coa.NamaCOA}</td>
              <td>{coa.SaldoNormal}</td>
              <td>
                <button onClick={() => onEdit(coa)}>Edit</button>
                <button onClick={() => handleDelete(coa.KodeCOA)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default MCOAList;