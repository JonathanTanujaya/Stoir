import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function MBankList({ onEdit, onRefresh }) {
  const [banks, setBanks] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchBanks = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/banks`); // Changed from /mbank to /banks
      let bankData = [];
      if (response.data && Array.isArray(response.data.data)) {
        bankData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        bankData = response.data;
      }
      setBanks(bankData); // Ensure it's always an array
      toast.success('Data bank berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching banks:', error);
      toast.error('Gagal memuat data bank.');
      setBanks([]); // Ensure banks is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchBanks();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, kodeBank) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus bank ini?')) {
      try {
        await axios.delete(`${API_URL}/banks/${kodeDivisi}/${kodeBank}`); // Changed from /mbank to /banks
        fetchBanks();
        toast.success('Bank berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting bank:', error);
        toast.error('Gagal menghapus bank.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data bank...</div>; // Display loading message
  }

  // Ensure banks is an array before mapping
  if (!Array.isArray(banks)) {
    console.error('Banks state is not an array:', banks);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Bank</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>Kode Bank</th>
            <th>Nama Bank</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {banks.map(bank => (
            <tr key={`${bank.KodeDivisi}-${bank.KodeBank}`}>
              <td>{bank.KodeDivisi}</td>
              <td>{bank.KodeBank}</td>
              <td>{bank.NamaBank}</td>
              <td>{bank.Status ? 'Aktif' : 'Tidak Aktif'}</td>
              <td>
                <button onClick={() => onEdit(bank)}>Edit</button>
                <button onClick={() => handleDelete(bank.KodeDivisi, bank.KodeBank)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default MBankList;
