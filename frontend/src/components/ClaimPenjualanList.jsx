import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function ClaimPenjualanList({ onEdit, onRefresh }) {
  const [claims, setClaims] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchClaims = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/claims`);
      let claimsData = [];
      if (response.data && Array.isArray(response.data.data)) {
        claimsData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        claimsData = response.data;
      }
      setClaims(claimsData); // Ensure it's always an array
      toast.success('Data klaim penjualan berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching claims:', error);
      toast.error('Gagal memuat data klaim penjualan.');
      setClaims([]); // Ensure claims is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchClaims();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, noClaim) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus klaim ini?')) {
      try {
        await axios.delete(`${API_URL}/claims/${kodeDivisi}/${noClaim}`);
        fetchClaims();
        toast.success('Klaim penjualan berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting claim:', error);
        toast.error('Gagal menghapus klaim penjualan.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data klaim penjualan...</div>; // Display loading message
  }

  // Ensure claims is an array before mapping
  if (!Array.isArray(claims)) {
    console.error('Claims state is not an array:', claims);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Klaim Penjualan</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>No Klaim</th>
            <th>Tgl Klaim</th>
            <th>Kode Customer</th>
            <th>Keterangan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {claims.map(claim => (
            <tr key={`${claim.KodeDivisi}-${claim.NoClaim}`}>
              <td>{claim.KodeDivisi}</td>
              <td>{claim.NoClaim}</td>
              <td>{claim.TglClaim}</td>
              <td>{claim.KodeCust}</td>
              <td>{claim.Keterangan}</td>
              <td>{claim.Status}</td>
              <td>
                <button onClick={() => onEdit(claim)}>Edit</button>
                <button onClick={() => handleDelete(claim.KodeDivisi, claim.NoClaim)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default ClaimPenjualanList;