import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import Button from '../../../components/ui/Button';
import Input from '../../../components/ui/Input';
import api from '../../../services/api';

function SparepartListPage() {
  const navigate = useNavigate();
  const [data, setData] = useState([]);
  const [search, setSearch] = useState('');
  
  useEffect(() => {
    api.get('/spareparts').then(res => setData(res.data || []));
  }, []);

  const filteredData = data.filter(item => 
    item.nama_barang?.toLowerCase().includes(search.toLowerCase()) ||
    item.kode_barang?.toLowerCase().includes(search.toLowerCase())
  );

  const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(value || 0);
  };

  const getStockBadge = (stok, minStok) => {
    if (stok <= 0) return { text: 'Habis', color: '#ef4444', bg: '#fef2f2' };
    if (stok <= minStok) return { text: 'Sedikit', color: '#f59e0b', bg: '#fffbeb' };
    return { text: 'Normal', color: '#10b981', bg: '#f0fdf4' };
  };

  return (
    <div style={{ padding: '24px', maxWidth: '1200px', margin: '0 auto' }}>
      {/* Header */}
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '24px' }}>
        <h1 style={{ fontSize: '28px', fontWeight: '600', margin: 0, color: '#1f2937' }}>Sparepart</h1>
        <Button onClick={() => navigate('/master/sparepart/create')}>Tambah Baru</Button>
      </div>

      {/* Search */}
      <div style={{ marginBottom: '20px', maxWidth: '400px' }}>
        <Input 
          placeholder="Cari sparepart..."
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
      </div>

      {/* Table */}
      <div style={{ 
        backgroundColor: 'white', 
        borderRadius: '8px', 
        border: '1px solid #e5e7eb',
        overflow: 'hidden'
      }}>
        <div style={{ overflowX: 'auto' }}>
          <table style={{ width: '100%', borderCollapse: 'collapse' }}>
            <thead>
              <tr style={{ backgroundColor: '#f9fafb', borderBottom: '1px solid #e5e7eb' }}>
                <th style={{ padding: '12px 16px', textAlign: 'left', fontSize: '12px', fontWeight: '600', color: '#6b7280', textTransform: 'uppercase' }}>Kode</th>
                <th style={{ padding: '12px 16px', textAlign: 'left', fontSize: '12px', fontWeight: '600', color: '#6b7280', textTransform: 'uppercase' }}>Nama Sparepart</th>
                <th style={{ padding: '12px 16px', textAlign: 'left', fontSize: '12px', fontWeight: '600', color: '#6b7280', textTransform: 'uppercase' }}>Stok</th>
                <th style={{ padding: '12px 16px', textAlign: 'left', fontSize: '12px', fontWeight: '600', color: '#6b7280', textTransform: 'uppercase' }}>Harga Beli</th>
                <th style={{ padding: '12px 16px', textAlign: 'left', fontSize: '12px', fontWeight: '600', color: '#6b7280', textTransform: 'uppercase' }}>Harga Jual</th>
                <th style={{ padding: '12px 16px', textAlign: 'center', fontSize: '12px', fontWeight: '600', color: '#6b7280', textTransform: 'uppercase' }}>Aksi</th>
              </tr>
            </thead>
            <tbody>
              {filteredData.map((item, idx) => {
                const stockBadge = getStockBadge(item.stok, item.min_stok);
                return (
                  <tr key={item.kode_barang} style={{ borderBottom: idx < filteredData.length - 1 ? '1px solid #f3f4f6' : 'none' }}>
                    <td style={{ padding: '16px', fontSize: '14px', fontWeight: '500', color: '#1f2937' }}>{item.kode_barang}</td>
                    <td style={{ padding: '16px', fontSize: '14px', color: '#1f2937' }}>{item.nama_barang}</td>
                    <td style={{ padding: '16px', fontSize: '14px' }}>
                      <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                        <span style={{ fontWeight: '500', color: '#1f2937' }}>{item.stok}</span>
                        <span style={{
                          padding: '2px 8px',
                          borderRadius: '12px',
                          fontSize: '11px',
                          fontWeight: '500',
                          color: stockBadge.color,
                          backgroundColor: stockBadge.bg
                        }}>
                          {stockBadge.text}
                        </span>
                      </div>
                    </td>
                    <td style={{ padding: '16px', fontSize: '14px', color: '#6b7280' }}>{formatCurrency(item.harga_beli)}</td>
                    <td style={{ padding: '16px', fontSize: '14px', color: '#6b7280' }}>{formatCurrency(item.harga_jual)}</td>
                    <td style={{ padding: '16px', textAlign: 'center' }}>
                      <button
                        onClick={() => navigate(`/master/sparepart/${item.kode_barang}/edit`)}
                        style={{
                          padding: '6px 12px',
                          fontSize: '13px',
                          fontWeight: '500',
                          color: '#3b82f6',
                          backgroundColor: 'transparent',
                          border: '1px solid #3b82f6',
                          borderRadius: '6px',
                          cursor: 'pointer',
                          transition: 'all 0.2s'
                        }}
                        onMouseEnter={(e) => {
                          e.target.style.backgroundColor = '#3b82f6';
                          e.target.style.color = 'white';
                        }}
                        onMouseLeave={(e) => {
                          e.target.style.backgroundColor = 'transparent';
                          e.target.style.color = '#3b82f6';
                        }}
                      >
                        Edit
                      </button>
                    </td>
                  </tr>
                );
              })}
            </tbody>
          </table>
        </div>
        
        {/* Footer */}
        <div style={{ padding: '12px 16px', backgroundColor: '#f9fafb', borderTop: '1px solid #e5e7eb', fontSize: '14px', color: '#6b7280' }}>
          Menampilkan {filteredData.length} dari {data.length} sparepart
        </div>
      </div>
    </div>
  );
}

export default SparepartListPage;
