import React, { useState, useEffect, useMemo } from 'react';
import {
  Box,
  Card,
  CardContent,
  Typography,
  Button,
  TextField,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  IconButton,
  Chip,
  Dialog,
  DialogTitle,
  DialogContent,
  DialogActions,
  Grid,
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  TablePagination,
  Tooltip,
  Alert,
  CircularProgress,
  InputAdornment,
} from '@mui/material';
import {
  Add as AddIcon,
  Edit as EditIcon,
  Delete as DeleteIcon,
  Search as SearchIcon,
  KeyboardArrowUp as ArrowUpIcon,
  KeyboardArrowDown as ArrowDownIcon,
} from '@mui/icons-material';
import { categoriesAPI } from '../../../services/categoriesAPI';

const MasterCategories = () => {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  const [currentPage, setCurrentPage] = useState(0);
  const [pageSize, setPageSize] = useState(10);
  const [sortKey, setSortKey] = useState('namakategori');
  const [sortDir, setSortDir] = useState('asc');
  const [showForm, setShowForm] = useState(false);
  const [formData, setFormData] = useState({
    namakategori: '',
    kodekategori: '',
    keterangan: '',
    status: 'Aktif',
  });
  const [editingId, setEditingId] = useState(null);
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(null);

  // Handle sorting
  const handleSort = (column) => {
    if (sortKey === column) {
      setSortDir(sortDir === 'asc' ? 'desc' : 'asc');
    } else {
      setSortKey(column);
      setSortDir('asc');
    }
  };

  // Data dummy untuk testing
  const dummyCategories = [
    {
      id: 1,
      kodeKategori: 'KAT001',
      namaKategori: 'Elektronik',
      deskripsi: 'Kategori untuk produk elektronik',
      status: 'Aktif'
    },
    {
      id: 2,
      kodeKategori: 'KAT002',
      namaKategori: 'Fashion',
      deskripsi: 'Kategori untuk produk fashion',
      status: 'Aktif'
    },
    {
      id: 3,
      kodeKategori: 'KAT003',
      namaKategori: 'Makanan',
      deskripsi: 'Kategori untuk produk makanan dan minuman',
      status: 'Tidak Aktif'
    }
  ];

  useEffect(() => {
    console.log('Component mounted, fetching categories...');
    fetchCategories();
  }, []);

  const fetchCategories = async () => {
    try {
      setLoading(true);
      setError(null);
      const response = await categoriesAPI.getAll();
      console.log('Full API Response:', response);
      console.log('Response data:', response.data);
      console.log('Response data type:', typeof response.data);
      console.log('Response data keys:', Object.keys(response.data || {}));
      
      // Handle different response structures
      let categoriesData = [];
      if (response.data) {
        // Try different possible structures
        if (Array.isArray(response.data.data)) {
          categoriesData = response.data.data;
          console.log('Using response.data.data (Laravel format)');
        } else if (Array.isArray(response.data)) {
          categoriesData = response.data;
          console.log('Using response.data (direct array)');
        } else if (response.data.categories && Array.isArray(response.data.categories)) {
          categoriesData = response.data.categories;
          console.log('Using response.data.categories');
        } else {
          console.log('Response structure:', response.data);
          // Try to extract any array from the response
          const values = Object.values(response.data);
          const arrayValue = values.find(val => Array.isArray(val));
          if (arrayValue) {
            categoriesData = arrayValue;
            console.log('Found array in response values');
          }
        }
      }
      
      console.log('=== DEBUGGING API RESPONSE ===');
      console.log('Full response:', response);
      console.log('Response.data:', response.data);
      console.log('Response.data type:', typeof response.data);
      
      // Handle different response structures
      let rawData = [];
      if (response.data) {
        console.log('Response.data exists');
        
        // Check all possible structures
        if (Array.isArray(response.data)) {
          console.log('response.data is direct array');
          rawData = response.data;
        } else if (response.data.data && Array.isArray(response.data.data)) {
          console.log('response.data.data is array');
          rawData = response.data.data;
        } else if (response.data.categories && Array.isArray(response.data.categories)) {
          console.log('response.data.categories is array');
          rawData = response.data.categories;
        } else {
          console.log('Searching for arrays in response.data...');
          console.log('Response.data keys:', Object.keys(response.data));
          const values = Object.values(response.data);
          const arrayValue = values.find(val => Array.isArray(val));
          if (arrayValue) {
            rawData = arrayValue;
            console.log('Found array in values');
          }
        }
      }
      
      console.log('=== EXTRACTED DATA ===');
      console.log('rawData:', rawData);
      console.log('rawData length:', rawData.length);
      console.log('First 3 items:', rawData.slice(0, 3));
      
      // If we have data, examine structure
      if (rawData.length > 0) {
        const firstItem = rawData[0];
        console.log('=== FIRST ITEM ANALYSIS ===');
        console.log('First item:', firstItem);
        console.log('First item type:', typeof firstItem);
        console.log('Is array:', Array.isArray(firstItem));
        console.log('Is object:', typeof firstItem === 'object' && !Array.isArray(firstItem));
        
        if (Array.isArray(firstItem)) {
          console.log('=== ARRAY STRUCTURE ===');
          firstItem.forEach((val, idx) => {
            console.log(`[${idx}]: "${val}" (${typeof val})`);
          });
        } else if (typeof firstItem === 'object') {
          console.log('=== OBJECT STRUCTURE ===');
          console.log('Keys:', Object.keys(firstItem));
          Object.entries(firstItem).forEach(([key, val]) => {
            console.log(`${key}: "${val}" (${typeof val})`);
          });
        }
      }
      
      // Always set the data as-is first
      console.log('Setting raw data to state...');
      setCategories(rawData);
      
      if (categoriesData.length > 0) {
        setSuccess(`Berhasil memuat ${categoriesData.length} kategori dari API`);
      } else {
        setSuccess('Data kategori kosong dari API');
        // Don't use dummy data, show actual empty state
      }
      setTimeout(() => setSuccess(null), 3000);
    } catch (error) {
      console.error('Error fetching categories:', error);
      setError('Gagal memuat dari API: ' + (error.response?.data?.message || error.message));
      setCategories([]);
      setTimeout(() => setError(null), 3000);
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editingId) {
        await categoriesAPI.update(editingId, formData);
        setSuccess('Kategori berhasil diupdate');
      } else {
        await categoriesAPI.create(formData);
        setSuccess('Kategori berhasil ditambahkan');
      }
      fetchCategories();
      resetForm();
      setTimeout(() => setSuccess(null), 3000);
    } catch (error) {
      console.error('Error saving category:', error);
      setError('Gagal menyimpan kategori');
      setTimeout(() => setError(null), 3000);
    }
  };

  const handleEdit = (category) => {
    setFormData({
      namakategori: category.namakategori || '',
      kodekategori: category.kodekategori || '',
      keterangan: category.keterangan || '',
      status: category.status || 'Aktif',
    });
    setEditingId(category.id);
    setShowForm(true);
  };

  const handleDelete = async (id) => {
    if (window.confirm('Yakin ingin menghapus kategori ini?')) {
      try {
        await categoriesAPI.delete(id);
        setSuccess('Kategori berhasil dihapus');
        fetchCategories();
        setTimeout(() => setSuccess(null), 3000);
      } catch (error) {
        console.error('Error deleting category:', error);
        setError('Gagal menghapus kategori');
        setTimeout(() => setError(null), 3000);
      }
    }
  };

  const resetForm = () => {
    setFormData({
      namakategori: '',
      kodekategori: '',
      keterangan: '',
      status: 'Aktif',
    });
    setEditingId(null);
    setShowForm(false);
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  // Filter dan sort data
  const filteredAndSortedCategories = useMemo(() => {
    console.log('Processing categories in useMemo:', categories);
    console.log('Categories length:', categories.length);
    console.log('Search term:', searchTerm);
    
    if (!Array.isArray(categories) || categories.length === 0) {
      console.log('Categories is not array or empty');
      return [];
    }

    // Apply search filter
    let filtered = categories;
    if (searchTerm && searchTerm.trim() !== '') {
      const search = searchTerm.toLowerCase();
      filtered = categories.filter(category => {
        const nama = (category.namakategori || '').toLowerCase();
        const kode = (category.kodekategori || '').toLowerCase();
        const desc = (category.keterangan || '').toLowerCase();
        
        return nama.includes(search) || kode.includes(search) || desc.includes(search);
      });
    }

    console.log('Filtered categories:', filtered);
    console.log('Filtered length:', filtered.length);

    // Sort data
    if (sortKey && filtered.length > 0) {
      filtered.sort((a, b) => {
        let aVal = a[sortKey] || a.nama_kategori || a.name || '';
        let bVal = b[sortKey] || b.nama_kategori || b.name || '';
        
        if (typeof aVal === 'string') aVal = aVal.toLowerCase();
        if (typeof bVal === 'string') bVal = bVal.toLowerCase();
        
        const compareResult = aVal.toString().localeCompare(bVal.toString());
        return sortDir === 'asc' ? compareResult : -compareResult;
      });
    }

    console.log('Final sorted categories:', filtered);
    return filtered;
  }, [categories, searchTerm, sortKey, sortDir]);

  // Pagination
  const paginatedCategories = useMemo(() => {
    const startIndex = currentPage * pageSize;
    const result = filteredAndSortedCategories.slice(startIndex, startIndex + pageSize);
    console.log('Paginated categories:', result);
    console.log('Paginated length:', result.length);
    return result;
  }, [filteredAndSortedCategories, currentPage, pageSize]);

  const handleChangePage = (event, newPage) => {
    setCurrentPage(newPage);
  };

  const handleChangeRowsPerPage = (event) => {
    setPageSize(parseInt(event.target.value, 10));
    setCurrentPage(0);
  };

  // Debug log current state
  console.log('=== MasterCategories Render ===');
  console.log('categories state:', categories);
  console.log('loading state:', loading);
  console.log('categories length:', categories.length);
  console.log('filteredAndSortedCategories length:', filteredAndSortedCategories.length);
  console.log('paginatedCategories length:', paginatedCategories.length);

  return (
    <Box p={3}>
      {/* Alert Messages */}
      {error && (
        <Alert severity="error" sx={{ mb: 2 }} onClose={() => setError(null)}>
          {error}
        </Alert>
      )}
      {success && (
        <Alert severity="success" sx={{ mb: 2 }} onClose={() => setSuccess(null)}>
          {success}
        </Alert>
      )}

      {/* Search */}
      <Card sx={{ mb: 3 }}>
        <CardContent>
          <Grid container spacing={2} alignItems="center">
            <Grid item xs={12}>
              <TextField
                fullWidth
                placeholder="Cari kategori..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                InputProps={{
                  startAdornment: (
                    <InputAdornment position="start">
                      <SearchIcon />
                    </InputAdornment>
                  ),
                }}
              />
            </Grid>
          </Grid>
        </CardContent>
      </Card>

      {/* Data Table */}
      <Card>
        <CardContent>
          <Box display="flex" justifyContent="space-between" alignItems="center" mb={2}>
            <Typography variant="h6">
              Data Kategori ({filteredAndSortedCategories.length} item)
            </Typography>
            <Button
              variant="contained"
              startIcon={<AddIcon />}
              onClick={() => {
                resetForm();
                setShowForm(true);
              }}
            >
              Tambah Kategori
            </Button>
          </Box>
          
          {loading ? (
            <Box display="flex" justifyContent="center" p={4}>
              <CircularProgress />
            </Box>
          ) : (
            <>
              <TableContainer component={Paper} variant="outlined">
                <Table>
                  <TableHead>
                    <TableRow sx={{ bgcolor: 'grey.50' }}>
                      <TableCell><strong>No</strong></TableCell>
                      
                      <TableCell 
                        sx={{ cursor: 'pointer', userSelect: 'none' }}
                        onClick={() => handleSort('kodekategori')}
                      >
                        <Box display="flex" alignItems="center">
                          <strong>Kode Kategori</strong>
                          {sortKey === 'kodekategori' && (
                            sortDir === 'asc' ? <ArrowUpIcon fontSize="small" /> : <ArrowDownIcon fontSize="small" />
                          )}
                        </Box>
                      </TableCell>
                      
                      <TableCell 
                        sx={{ cursor: 'pointer', userSelect: 'none' }}
                        onClick={() => handleSort('namakategori')}
                      >
                        <Box display="flex" alignItems="center">
                          <strong>Nama Kategori</strong>
                          {sortKey === 'namakategori' && (
                            sortDir === 'asc' ? <ArrowUpIcon fontSize="small" /> : <ArrowDownIcon fontSize="small" />
                          )}
                        </Box>
                      </TableCell>
                      
                      <TableCell><strong>Deskripsi</strong></TableCell>
                      
                      <TableCell 
                        sx={{ cursor: 'pointer', userSelect: 'none' }}
                        onClick={() => handleSort('status')}
                      >
                        <Box display="flex" alignItems="center">
                          <strong>Status</strong>
                          {sortKey === 'status' && (
                            sortDir === 'asc' ? <ArrowUpIcon fontSize="small" /> : <ArrowDownIcon fontSize="small" />
                          )}
                        </Box>
                      </TableCell>
                      
                      <TableCell align="center"><strong>Aksi</strong></TableCell>
                    </TableRow>
                  </TableHead>
                  <TableBody>
                    {(() => {
                      console.log('=== TABLE RENDER ===');
                      console.log('paginatedCategories in render:', paginatedCategories);
                      console.log('paginatedCategories.length in render:', paginatedCategories.length);
                      console.log('categories.length:', categories.length);
                      console.log('filteredAndSortedCategories.length:', filteredAndSortedCategories.length);
                      
                      if (paginatedCategories.length === 0) {
                        return (
                          <TableRow>
                            <TableCell colSpan={6} align="center" sx={{ py: 4 }}>
                              <Typography color="textSecondary">
                                {categories.length > 0 
                                  ? `Debug: Total ${categories.length} kategori, Filtered ${filteredAndSortedCategories.length}, Paginated ${paginatedCategories.length}`
                                  : 'Tidak ada data kategori'
                                }
                              </Typography>
                            </TableCell>
                          </TableRow>
                        );
                      }
                      
                      return paginatedCategories.map((category, index) => {
                        console.log(`Rendering category ${index}:`, category);
                        
                        // Use the correct field names from API response
                        const kodeKategori = category.kodekategori || '-';
                        const namaKategori = category.namakategori || '-';
                        const deskripsi = category.keterangan || '-';
                        const status = category.status || 'Aktif';
                        
                        return (
                          <TableRow key={category.id || index} hover>
                            <TableCell>{currentPage * pageSize + index + 1}</TableCell>
                            <TableCell>
                              <Typography variant="body2" fontWeight="medium">
                                {kodeKategori}
                              </Typography>
                            </TableCell>
                            <TableCell>
                              <Typography variant="body1">
                                {namaKategori}
                              </Typography>
                            </TableCell>
                            <TableCell>
                              <Typography variant="body2" color="textSecondary">
                                {deskripsi}
                              </Typography>
                            </TableCell>
                            <TableCell>
                              <Chip
                                label={status}
                                color={status === 'Aktif' ? 'success' : 'default'}
                                size="small"
                              />
                            </TableCell>
                            <TableCell align="center">
                              <Tooltip title="Edit">
                                <IconButton
                                  size="small"
                                  color="primary"
                                  onClick={() => handleEdit(category)}
                                >
                                  <EditIcon fontSize="small" />
                                </IconButton>
                              </Tooltip>
                              <Tooltip title="Hapus">
                                <IconButton
                                  size="small"
                                  color="error"
                                  onClick={() => handleDelete(category.id)}
                                >
                                  <DeleteIcon fontSize="small" />
                                </IconButton>
                              </Tooltip>
                            </TableCell>
                          </TableRow>
                        );
                      });
                    })()}
                  </TableBody>
                </Table>
              </TableContainer>

              {/* Pagination */}
              <TablePagination
                component="div"
                count={filteredAndSortedCategories.length}
                page={currentPage}
                onPageChange={handleChangePage}
                rowsPerPage={pageSize}
                onRowsPerPageChange={handleChangeRowsPerPage}
                rowsPerPageOptions={[5, 10, 25, 50]}
                labelRowsPerPage="Baris per halaman:"
                labelDisplayedRows={({ from, to, count }) =>
                  `${from}-${to} dari ${count !== -1 ? count : `lebih dari ${to}`}`
                }
              />
            </>
          )}
        </CardContent>
      </Card>

      {/* Form Dialog */}
      <Dialog
        open={showForm}
        onClose={resetForm}
        maxWidth="sm"
        fullWidth
      >
        <DialogTitle>
          {editingId ? 'Edit Kategori' : 'Tambah Kategori Baru'}
        </DialogTitle>
        <form onSubmit={handleSubmit}>
          <DialogContent>
            <Grid container spacing={2} sx={{ mt: 1 }}>
              <Grid item xs={12} sm={6}>
                <TextField
                  fullWidth
                  label="Kode Kategori"
                  name="kodekategori"
                  value={formData.kodekategori}
                  onChange={handleInputChange}
                  required
                  placeholder="contoh: KAT001"
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <TextField
                  fullWidth
                  label="Nama Kategori"
                  name="namakategori"
                  value={formData.namakategori}
                  onChange={handleInputChange}
                  required
                  placeholder="contoh: Elektronik"
                />
              </Grid>
              <Grid item xs={12}>
                <TextField
                  fullWidth
                  label="Keterangan"
                  name="keterangan"
                  value={formData.keterangan}
                  onChange={handleInputChange}
                  multiline
                  rows={3}
                  placeholder="Keterangan kategori (opsional)"
                />
              </Grid>
              <Grid item xs={12} sm={6}>
                <FormControl fullWidth>
                  <InputLabel>Status</InputLabel>
                  <Select
                    name="status"
                    value={formData.status}
                    onChange={handleInputChange}
                    label="Status"
                  >
                    <MenuItem value="Aktif">Aktif</MenuItem>
                    <MenuItem value="Tidak Aktif">Tidak Aktif</MenuItem>
                  </Select>
                </FormControl>
              </Grid>
            </Grid>
          </DialogContent>
          <DialogActions>
            <Button onClick={resetForm}>
              Batal
            </Button>
            <Button type="submit" variant="contained">
              {editingId ? 'Update' : 'Simpan'}
            </Button>
          </DialogActions>
        </form>
      </Dialog>
    </Box>
  );
};

export default MasterCategories;
