import { useState, useEffect } from 'react';
import axios from 'axios';
import { toast } from 'react-toastify';

const API_URL = 'http://localhost:8000/api';

function MasterUserList({ onEdit, onRefresh }) {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true); // Add loading state

  const fetchUsers = async () => {
    setLoading(true); // Set loading to true before fetching
    try {
      const response = await axios.get(`${API_URL}/master-user`);
      let userData = [];
      if (response.data && Array.isArray(response.data.data)) {
        userData = response.data.data;
      } else if (response.data && Array.isArray(response.data)) {
        userData = response.data;
      }
      setUsers(userData); // Ensure it's always an array
      toast.success('Data pengguna berhasil dimuat!');
    } catch (error) {
      console.error('Error fetching users:', error);
      toast.error('Gagal memuat data pengguna.');
      setUsers([]); // Ensure users is an empty array on error
    } finally {
      setLoading(false); // Set loading to false after fetching (success or error)
    }
  };

  useEffect(() => {
    fetchUsers();
  }, [onRefresh]);

  const handleDelete = async (kodeDivisi, username) => {
    if (window.confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
      try {
        await axios.delete(`${API_URL}/master-user/${kodeDivisi}/${username}`);
        fetchUsers();
        toast.success('Pengguna berhasil dihapus!');
      } catch (error) {
        console.error('Error deleting user:', error);
        toast.error('Gagal menghapus pengguna.');
      }
    }
  };

  if (loading) {
    return <div>Memuat data pengguna...</div>; // Display loading message
  }

  // Ensure users is an array before mapping
  if (!Array.isArray(users)) {
    console.error('Users state is not an array:', users);
    return <div>Terjadi kesalahan dalam memuat data.</div>; // Or handle gracefully
  }

  return (
    <div>
      <h2>Daftar Pengguna</h2>
      <table>
        <thead>
          <tr>
            <th>Kode Divisi</th>
            <th>Username</th>
            <th>Nama</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          {users.map(user => (
            <tr key={`${user.KodeDivisi}-${user.Username}`}>
              <td>{user.KodeDivisi}</td>
              <td>{user.Username}</td>
              <td>{user.Nama}</td>
              <td>
                <button onClick={() => onEdit(user)}>Edit</button>
                <button onClick={() => handleDelete(user.KodeDivisi, user.Username)}>Hapus</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}

export default MasterUserList;