import React, { useState } from 'react';
import {
  Box,
  Container,
  Paper,
  Typography,
  TextField,
  Button,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Grid,
  Card,
  CardContent,
  Chip,
  IconButton,
  InputAdornment,
  Divider,
} from '@mui/material';
import {
  Search as SearchIcon,
  Edit as EditIcon,
  Delete as DeleteIcon,
  Print as PrintIcon,
  Save as SaveIcon,
  Cancel as CancelIcon,
  Inventory as InventoryIcon,
  ShoppingCart as ShoppingCartIcon,
  Assignment as AssignmentIcon,
} from '@mui/icons-material';

const PenjualanBonus = () => {
  const [customerCode, setCustomerCode] = useState('');
  const [itemCode, setItemCode] = useState('BRG001');
  const [quantity, setQuantity] = useState('24');

  // Sample data untuk table
  const tableData = [
    { no: 1, kodeBarang: 'BRG001', namaBarang: 'Indomie Goreng Special', qty: 24, satuan: 'Dus' },
    { no: 2, kodeBarang: 'BRG002', namaBarang: 'Mie Sedaap Soto Lamongan', qty: 12, satuan: 'Dus' },
    {
      no: 3,
      kodeBarang: 'BRG003',
      namaBarang: 'Sarimi Isi 2 Rasa Ayam Bawang',
      qty: 36,
      satuan: 'Dus',
    },
    { no: 4, kodeBarang: 'BRG004', namaBarang: 'Supermie Kuah Rasa Soto', qty: 18, satuan: 'Dus' },
    {
      no: 5,
      kodeBarang: 'BRG005',
      namaBarang: 'Mie Gelas Jumbo Ayam Spesial',
      qty: 48,
      satuan: 'Dus',
    },
    { no: 6, kodeBarang: 'BRG006', namaBarang: 'Pop Mie Rasa Ayam Bawang', qty: 30, satuan: 'Dus' },
  ];

  const totalQuantity = tableData.reduce((sum, item) => sum + item.qty, 0);

  return (
    <Container maxWidth="xl" sx={{ py: 2 }}>
      {/* Main Form Container - Tanpa Page Header */}
      <Paper
        elevation={3}
        sx={{
          borderRadius: 3,
          overflow: 'hidden',
          background: 'linear-gradient(135deg, #FFFFFF 0%, #FAFBFC 100%)',
        }}
      >
        {/* Form Header - Compact */}
        <Box
          sx={{
            p: 2,
            borderBottom: '1px solid #E2E8F0',
            background: 'linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%)',
          }}
        >
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
            <Box
              sx={{
                width: 32,
                height: 32,
                borderRadius: 2,
                background: 'rgba(255,255,255,0.2)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
              }}
            >
              <AssignmentIcon sx={{ color: 'white', fontSize: 18 }} />
            </Box>
            <Typography variant="h6" sx={{ fontWeight: 600, color: 'white' }}>
              Form Penjualan Bonus
            </Typography>
          </Box>
        </Box>

        {/* Form Content - Compact */}
        <Box sx={{ p: 2 }}>
          {/* Customer Section */}
          <Grid container spacing={2} sx={{ mb: 3 }}>
            <Grid item xs={12} md={4}>
              <Typography variant="subtitle2" sx={{ mb: 1, fontWeight: 500, color: '#374151' }}>
                Kode Customer *
              </Typography>
              <TextField
                fullWidth
                size="small"
                placeholder="Ketik untuk mencari..."
                value={customerCode}
                onChange={e => setCustomerCode(e.target.value)}
                InputProps={{
                  endAdornment: (
                    <InputAdornment position="end">
                      <IconButton size="small">
                        <SearchIcon fontSize="small" />
                      </IconButton>
                    </InputAdornment>
                  ),
                }}
                sx={{
                  '& .MuiOutlinedInput-root': {
                    borderRadius: 2,
                    backgroundColor: '#FFFFFF',
                  },
                }}
              />
            </Grid>
            <Grid item xs={12} md={8}>
              <Typography variant="subtitle2" sx={{ mb: 1, fontWeight: 500, color: '#374151' }}>
                Nama Customer
              </Typography>
              <TextField
                fullWidth
                value="PT. Maju Bersama Sejahtera"
                disabled
                sx={{
                  '& .MuiOutlinedInput-root': {
                    borderRadius: 2,
                    backgroundColor: '#F8FAFC',
                  },
                }}
              />
            </Grid>
          </Grid>

          {/* Item Section Header - Compact */}
          <Box sx={{ display: 'flex', alignItems: 'center', gap: 2, mb: 2 }}>
            <Box
              sx={{
                width: 28,
                height: 28,
                borderRadius: 1.5,
                background: 'linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
              }}
            >
              <InventoryIcon sx={{ color: '#6366F1', fontSize: 16 }} />
            </Box>
            <Typography variant="subtitle1" sx={{ fontWeight: 600, color: '#1E293B' }}>
              Detail Barang
            </Typography>
          </Box>

          {/* Item Input Row - Compact */}
          <Grid container spacing={2} sx={{ mb: 3 }}>
            <Grid item xs={12} md={2.5}>
              <Typography variant="body2" sx={{ mb: 1, fontWeight: 500, color: '#374151' }}>
                Kode Barang *
              </Typography>
              <TextField
                fullWidth
                size="small"
                value={itemCode}
                onChange={e => setItemCode(e.target.value)}
                InputProps={{
                  endAdornment: (
                    <InputAdornment position="end">
                      <IconButton size="small">
                        <SearchIcon fontSize="small" />
                      </IconButton>
                    </InputAdornment>
                  ),
                }}
                sx={{
                  '& .MuiOutlinedInput-root': {
                    borderRadius: 2,
                    backgroundColor: '#FFFFFF',
                  },
                }}
              />
            </Grid>
            <Grid item xs={12} md={4}>
              <Typography variant="body2" sx={{ mb: 1, fontWeight: 500, color: '#374151' }}>
                Nama Barang
              </Typography>
              <TextField
                fullWidth
                size="small"
                value="Indomie Goreng Special"
                disabled
                sx={{
                  '& .MuiOutlinedInput-root': {
                    borderRadius: 2,
                    backgroundColor: '#F8FAFC',
                  },
                }}
              />
            </Grid>
            <Grid item xs={12} md={2}>
              <Typography variant="body2" sx={{ mb: 1, fontWeight: 500, color: '#374151' }}>
                Qty *
              </Typography>
              <TextField
                fullWidth
                size="small"
                value={quantity}
                onChange={e => setQuantity(e.target.value)}
                sx={{
                  '& .MuiOutlinedInput-root': {
                    borderRadius: 2,
                    backgroundColor: '#FFFFFF',
                  },
                }}
              />
            </Grid>
            <Grid item xs={12} md={2}>
              <Typography variant="body2" sx={{ mb: 1, fontWeight: 500, color: 'transparent' }}>
                Action
              </Typography>
              <Button
                variant="contained"
                size="small"
                fullWidth
                sx={{
                  borderRadius: 2,
                  background: 'linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%)',
                  fontWeight: 600,
                }}
              >
                Tambah
              </Button>
            </Grid>
          </Grid>

          {/* Data Table - Compact */}
          <TableContainer
            component={Paper}
            sx={{
              borderRadius: 2,
              border: '1px solid #E2E8F0',
              mb: 2,
            }}
          >
            <Table size="small">
              <TableHead>
                <TableRow sx={{ backgroundColor: '#FAFBFC' }}>
                  <TableCell sx={{ fontWeight: 600, color: '#475569', fontSize: '0.75rem' }}>
                    NO
                  </TableCell>
                  <TableCell sx={{ fontWeight: 600, color: '#475569', fontSize: '0.75rem' }}>
                    KODE BARANG
                  </TableCell>
                  <TableCell sx={{ fontWeight: 600, color: '#475569', fontSize: '0.75rem' }}>
                    NAMA BARANG
                  </TableCell>
                  <TableCell sx={{ fontWeight: 600, color: '#475569', fontSize: '0.75rem' }}>
                    QTY SUPPLY
                  </TableCell>
                  <TableCell sx={{ fontWeight: 600, color: '#475569', fontSize: '0.75rem' }}>
                    SATUAN
                  </TableCell>
                  <TableCell sx={{ fontWeight: 600, color: '#475569', fontSize: '0.75rem' }}>
                    AKSI
                  </TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {tableData.map((row, index) => (
                  <TableRow
                    key={row.no}
                    sx={{
                      backgroundColor: index % 2 === 0 ? '#FFFFFF' : '#FAFBFC',
                      '&:hover': { backgroundColor: '#F1F5F9' },
                    }}
                  >
                    <TableCell>{row.no}</TableCell>
                    <TableCell sx={{ fontWeight: 500 }}>{row.kodeBarang}</TableCell>
                    <TableCell>{row.namaBarang}</TableCell>
                    <TableCell sx={{ fontWeight: 500 }}>{row.qty}</TableCell>
                    <TableCell>{row.satuan}</TableCell>
                    <TableCell>
                      <Box sx={{ display: 'flex', gap: 1 }}>
                        <Button
                          size="small"
                          variant="outlined"
                          sx={{
                            minWidth: 'auto',
                            px: 2,
                            borderRadius: 1.5,
                            borderColor: '#E2E8F0',
                            color: '#64748B',
                          }}
                        >
                          <EditIcon fontSize="small" />
                        </Button>
                        <Button
                          size="small"
                          variant="outlined"
                          color="error"
                          sx={{
                            minWidth: 'auto',
                            px: 2,
                            borderRadius: 1.5,
                          }}
                        >
                          <DeleteIcon fontSize="small" />
                        </Button>
                      </Box>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>

          {/* Summary Section - Compact */}
          <Card
            sx={{
              background: 'linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%)',
              border: '1px solid #C7D2FE',
              borderRadius: 3,
              mb: 2,
            }}
          >
            <CardContent sx={{ py: 2 }}>
              <Grid container spacing={2}>
                <Grid item xs={12} md={4}>
                  <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
                    <Box
                      sx={{
                        width: 40,
                        height: 40,
                        borderRadius: 2,
                        background: '#6366F1',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                      }}
                    >
                      <InventoryIcon sx={{ color: '#FFFFFF', fontSize: 20 }} />
                    </Box>
                    <Box>
                      <Typography variant="caption" sx={{ color: '#475569', fontWeight: 500 }}>
                        Total Item:
                      </Typography>
                      <Typography variant="h6" sx={{ color: '#6366F1', fontWeight: 700 }}>
                        {tableData.length} Produk
                      </Typography>
                    </Box>
                  </Box>
                </Grid>

                <Grid item xs={12} md={4}>
                  <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
                    <Box
                      sx={{
                        width: 40,
                        height: 40,
                        borderRadius: 2,
                        background: '#06B6D4',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                      }}
                    >
                      <ShoppingCartIcon sx={{ color: '#FFFFFF', fontSize: 20 }} />
                    </Box>
                    <Box>
                      <Typography variant="caption" sx={{ color: '#475569', fontWeight: 500 }}>
                        Total Quantity:
                      </Typography>
                      <Typography variant="h6" sx={{ color: '#06B6D4', fontWeight: 700 }}>
                        {totalQuantity} Dus
                      </Typography>
                    </Box>
                  </Box>
                </Grid>

                <Grid item xs={12} md={4}>
                  <Box sx={{ display: 'flex', alignItems: 'center', gap: 2 }}>
                    <Box
                      sx={{
                        width: 40,
                        height: 40,
                        borderRadius: 2,
                        background: '#10B981',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                      }}
                    >
                      ✓
                    </Box>
                    <Box>
                      <Typography variant="caption" sx={{ color: '#475569', fontWeight: 500 }}>
                        Status:
                      </Typography>
                      <Typography variant="h6" sx={{ color: '#10B981', fontWeight: 600 }}>
                        Siap Disimpan
                      </Typography>
                    </Box>
                  </Box>
                </Grid>
              </Grid>
            </CardContent>
          </Card>

          {/* Timestamp - Compact */}
          <Typography variant="caption" sx={{ color: '#64748B', mb: 2 }}>
            Transaksi dibuat pada: 28 Juli 2025, 11:42 WIB
          </Typography>

          {/* Action Buttons - Compact */}
          <Box sx={{ display: 'flex', gap: 2, justifyContent: 'space-between' }}>
            <Box sx={{ display: 'flex', gap: 2 }}>
              <Button
                variant="outlined"
                size="small"
                startIcon={<CancelIcon />}
                sx={{
                  px: 3,
                  borderRadius: 2,
                  borderColor: '#E2E8F0',
                  color: '#64748B',
                }}
              >
                Batal
              </Button>
              <Button
                variant="contained"
                size="small"
                startIcon={<SaveIcon />}
                sx={{
                  px: 3,
                  borderRadius: 2,
                  background: 'linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%)',
                  fontWeight: 600,
                }}
              >
                Simpan
              </Button>
            </Box>

            <Button
              variant="outlined"
              size="small"
              startIcon={<PrintIcon />}
              sx={{
                px: 3,
                borderRadius: 2,
                borderColor: '#E2E8F0',
                color: '#64748B',
              }}
            >
              Cetak
            </Button>
          </Box>
        </Box>
      </Paper>
    </Container>
  );
};

export default PenjualanBonus;
