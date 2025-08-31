import React, { useState } from 'react';
import { Plus, Search, Package, Calendar, FileText, Building, DollarSign, Trash2 } from 'lucide-react';

const ModernPurchaseFormCompact = () => {
  const [formData, setFormData] = useState({
    tglTerima: '',
    tglJatuhTempo: '',
    noFaktur: '',
    supplier: '',
    catatan: '',
    ppn: '11',
    diskonGlobal: '0',
    items: [
      { id: 1, kode: 'BRG001', nama: 'Sparepart Engine A100', qty: 2, satuan: 'PCS', harga: 150000, d1: 5, d2: 2, hargaNet: 139500, subtotal: 279000 }
    ]
  });

  const [showSupplierModal, setShowSupplierModal] = useState(false);

  const handleInputChange = (field, value) => {
    setFormData(prev => ({
      ...prev,
      [field]: value
    }));
  };

  const addItem = () => {
    const newItem = {
      id: Date.now(),
      kode: '',
      nama: '',
      qty: 1,
      satuan: 'PCS',
      harga: 0,
      d1: 0,
      d2: 0,
      hargaNet: 0,
      subtotal: 0
    };
    setFormData(prev => ({
      ...prev,
      items: [...prev.items, newItem]
    }));
  };

  const removeItem = (index) => {
    setFormData(prev => ({
      ...prev,
      items: prev.items.filter((_, i) => i !== index)
    }));
  };

  const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(amount);
  };

  const calculateTotals = () => {
    const subtotal = formData.items.reduce((sum, item) => sum + item.subtotal, 0);
    const diskon = parseFloat(formData.diskonGlobal) || 0;
    const dpp = subtotal - diskon;
    const ppn = dpp * (parseFloat(formData.ppn) / 100);
    const total = dpp + ppn;
    
    return { subtotal, diskon, dpp, ppn, total };
  };

  const totals = calculateTotals();

  return (
    <div className="min-h-screen bg-gray-50 p-4">
      <div className="max-w-7xl mx-auto space-y-4">
        
        {/* Header Form - Compact 3 columns */}
        <div className="bg-white rounded-lg shadow-sm border p-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">TGL TERIMA</label>
              <input
                type="date"
                value={formData.tglTerima}
                onChange={(e) => handleInputChange('tglTerima', e.target.value)}
                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">TGL JATUH TEMPO</label>
              <input
                type="date"
                value={formData.tglJatuhTempo}
                onChange={(e) => handleInputChange('tglJatuhTempo', e.target.value)}
                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">NO FAKTUR</label>
              <input
                type="text"
                value={formData.noFaktur}
                onChange={(e) => handleInputChange('noFaktur', e.target.value)}
                placeholder="Masukkan nomor faktur"
                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>

          {/* Supplier & Catatan - 2 columns */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">SUPPLIER</label>
              <div className="flex space-x-2">
                <select
                  value={formData.supplier}
                  onChange={(e) => handleInputChange('supplier', e.target.value)}
                  className="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="">Pilih Supplier</option>
                  <option value="supplier1">PT. Supplier Utama</option>
                  <option value="supplier2">CV. Mitra Jaya</option>
                </select>
                <button 
                  onClick={() => setShowSupplierModal(true)}
                  className="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                >
                  <Search className="w-4 h-4" />
                </button>
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">CATATAN</label>
              <input
                type="text"
                value={formData.catatan}
                onChange={(e) => handleInputChange('catatan', e.target.value)}
                placeholder="Catatan transaksi..."
                className="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
          </div>
        </div>

        {/* Main Content - Table dan Summary dalam satu row */}
        <div className="grid grid-cols-1 lg:grid-cols-4 gap-4">
          
          {/* Items Table - 3/4 width */}
          <div className="lg:col-span-3 bg-white rounded-lg shadow-sm border">
            <div className="p-4 border-b border-gray-200">
              <div className="flex items-center justify-between">
                <h3 className="text-lg font-semibold text-gray-900">Daftar Barang</h3>
                <button 
                  onClick={addItem}
                  className="px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm flex items-center space-x-1"
                >
                  <Plus className="w-4 h-4" />
                  <span>Tambah</span>
                </button>
              </div>
            </div>
            
            {/* Compact Table */}
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                    <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                    <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                    <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                    <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                    <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">D1%</th>
                    <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">D2%</th>
                    <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    <th className="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {formData.items.map((item, index) => (
                    <tr key={item.id} className="hover:bg-gray-50">
                      <td className="px-3 py-2 text-sm text-gray-900">{index + 1}</td>
                      <td className="px-3 py-2 text-sm font-mono text-gray-900">{item.kode}</td>
                      <td className="px-3 py-2 text-sm text-gray-900 max-w-48 truncate">{item.nama}</td>
                      <td className="px-3 py-2 text-sm text-gray-900 text-right">{item.qty}</td>
                      <td className="px-3 py-2 text-sm text-gray-900">{item.satuan}</td>
                      <td className="px-3 py-2 text-sm text-gray-900 text-right">{formatCurrency(item.harga)}</td>
                      <td className="px-3 py-2 text-sm text-gray-900 text-right">{item.d1}%</td>
                      <td className="px-3 py-2 text-sm text-gray-900 text-right">{item.d2}%</td>
                      <td className="px-3 py-2 text-sm text-gray-900 text-right font-medium">{formatCurrency(item.subtotal)}</td>
                      <td className="px-3 py-2 text-sm text-gray-900">
                        <button 
                          onClick={() => removeItem(index)}
                          className="text-red-600 hover:text-red-900 p-1"
                          title="Hapus item"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </td>
                    </tr>
                  ))}
                  {formData.items.length === 0 && (
                    <tr>
                      <td colSpan="10" className="px-3 py-8 text-center text-gray-500">
                        <Package className="w-8 h-8 mx-auto mb-2 text-gray-300" />
                        <p className="text-sm">Belum ada item</p>
                      </td>
                    </tr>
                  )}
                </tbody>
              </table>
            </div>
          </div>

          {/* Sticky Summary Panel - 1/4 width */}
          <div className="lg:col-span-1">
            <div className="bg-white rounded-lg shadow-sm border p-4 sticky top-4">
              <h3 className="text-lg font-semibold text-gray-900 mb-4">Ringkasan</h3>
              
              {/* PPN & Diskon */}
              <div className="space-y-3 mb-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">PPN %</label>
                  <input
                    type="number"
                    value={formData.ppn}
                    onChange={(e) => handleInputChange('ppn', e.target.value)}
                    className="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">Diskon Global</label>
                  <input
                    type="number"
                    value={formData.diskonGlobal}
                    onChange={(e) => handleInputChange('diskonGlobal', e.target.value)}
                    className="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                  />
                </div>
              </div>

              {/* Summary */}
              <div className="border-t pt-4 space-y-2">
                <div className="flex justify-between text-sm">
                  <span className="text-gray-600">Subtotal:</span>
                  <span className="font-medium">{formatCurrency(totals.subtotal)}</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-600">Diskon:</span>
                  <span className="font-medium text-red-600">-{formatCurrency(totals.diskon)}</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-600">DPP:</span>
                  <span className="font-medium">{formatCurrency(totals.dpp)}</span>
                </div>
                <div className="flex justify-between text-sm">
                  <span className="text-gray-600">PPN ({formData.ppn}%):</span>
                  <span className="font-medium">{formatCurrency(totals.ppn)}</span>
                </div>
                <div className="border-t pt-2">
                  <div className="flex justify-between">
                    <span className="font-semibold text-gray-900">Total:</span>
                    <span className="font-bold text-blue-600">{formatCurrency(totals.total)}</span>
                  </div>
                </div>
              </div>

              {/* Action Buttons */}
              <div className="mt-6 space-y-2">
                <button className="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                  Simpan Transaksi
                </button>
                <button className="w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                  Simpan Draft
                </button>
                <button className="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 text-sm">
                  Reset
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ModernPurchaseFormCompact;
