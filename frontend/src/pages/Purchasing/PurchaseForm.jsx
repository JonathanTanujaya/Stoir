import React, { useState, useMemo, useCallback } from 'react';
import { purchasesAPI, suppliersAPI, barangAPI } from '../../services/api';
import { toast } from 'react-toastify';
import PageHeader from '../../components/Layout/PageHeader';

// Helpers
const todayStr = () => new Date().toISOString().slice(0,10);
const addDays = (base, days) => {
  const d = new Date(base); d.setDate(d.getDate()+days); return d.toISOString().slice(0,10);
};
const calcNet = (price, d1, d2) => {
  const p = Number(price)||0, a = Number(d1)||0, b = Number(d2)||0;
  return p * (1 - a/100) * (1 - b/100);
};

const emptyHeader = {
  receiptDate: todayStr(),
  dueDate: addDays(todayStr(), 14),
  supplier: null,
  invoiceNumber: '',
  taxPercent: 11,
  globalDiscount: 0,
  note: ''
};

const emptyEntry = { code:'', name:'', qty:1, unit:'PCS', price:'', disc1:'0', disc2:'0' };

function PurchaseForm() {
  const [header, setHeader] = useState(emptyHeader);
  const [items, setItems] = useState([]);
  const [entry, setEntry] = useState(emptyEntry);
  const [mergeSame, setMergeSame] = useState(true);
  const [printMode, setPrintMode] = useState(false);
  const [saving, setSaving] = useState(false);
  const [editingRow, setEditingRow] = useState(null);
  const [supplierModalOpen, setSupplierModalOpen] = useState(false);
  const [productModalOpen, setProductModalOpen] = useState(false);
  const [supplierLookup, setSupplierLookup] = useState('');
  const [supplierResults, setSupplierResults] = useState([]);
  const [productLookup, setProductLookup] = useState('');
  const [productResults, setProductResults] = useState([]);
  const [checkingInvoice, setCheckingInvoice] = useState(false);
  const [invoiceExists, setInvoiceExists] = useState(false);
  const [autoSaveDraft, setAutoSaveDraft] = useState(true);
  const [lastSaved, setLastSaved] = useState(null);
  const [showShortcuts, setShowShortcuts] = useState(false);

  // Auto-save draft functionality
  React.useEffect(() => {
    if (!autoSaveDraft) return;
    const timer = setTimeout(() => {
      const draft = { header, items, timestamp: Date.now() };
      localStorage.setItem('purchase-draft', JSON.stringify(draft));
      setLastSaved(new Date().toLocaleTimeString());
    }, 2000);
    return () => clearTimeout(timer);
  }, [header, items, autoSaveDraft]);

  // Load draft on mount
  React.useEffect(() => {
    const saved = localStorage.getItem('purchase-draft');
    if (saved) {
      try {
        const draft = JSON.parse(saved);
        if (draft.header && draft.items) {
          setHeader(draft.header);
          setItems(draft.items);
          toast.info('Draft dimuat dari penyimpanan');
        }
      } catch (e) {
        console.warn('Invalid draft data');
      }
    }
  }, []);

  const clearDraft = () => {
    localStorage.removeItem('purchase-draft');
    setHeader(emptyHeader);
    setItems([]);
    setLastSaved(null);
    toast.success('Draft dihapus');
  };

  // Invoice uniqueness check with debounce
  React.useEffect(() => {
    const timer = setTimeout(() => {
      if (header.invoiceNumber && header.invoiceNumber.length > 2) {
        setCheckingInvoice(true);
        purchasesAPI.getAll()
          .then(res => {
            const data = res.data?.data || [];
            const exists = data.some(p => 
              (p.invoice_number || p.invoiceNumber || '').toLowerCase() === 
              header.invoiceNumber.toLowerCase()
            );
            setInvoiceExists(exists);
          })
          .catch(() => setInvoiceExists(false))
          .finally(() => setCheckingInvoice(false));
      } else {
        setInvoiceExists(false);
        setCheckingInvoice(false);
      }
    }, 500);
    return () => clearTimeout(timer);
  }, [header.invoiceNumber]);

  // Supplier search debounce
  React.useEffect(()=>{
    const t = setTimeout(()=>{
      if(supplierModalOpen && supplierLookup){
        suppliersAPI.getAll().then(res=>{
          const data = res.data?.data || [];
          setSupplierResults(data.filter(s=> (s.nama || '').toLowerCase().includes(supplierLookup.toLowerCase())));
        }).catch(()=> setSupplierResults([]));
      }
    }, 300);
    return ()=> clearTimeout(t);
  }, [supplierLookup, supplierModalOpen]);

  // Product search debounce
  React.useEffect(()=>{
    const t = setTimeout(()=>{
      if(productModalOpen && productLookup){
        barangAPI.getAll().then(res=>{
          const data = res.data?.data || [];
          setProductResults(data.filter(p=> (p.nama_barang || p.namaBarang || p.kode_barang || '').toLowerCase().includes(productLookup.toLowerCase())));
        }).catch(()=> setProductResults([]));
      }
    }, 300);
    return ()=> clearTimeout(t);
  }, [productLookup, productModalOpen]);

  const totals = useMemo(()=>{
    const total = items.reduce((acc, r) => acc + r.subTotal, 0);
    const globalDisc = Number(header.globalDiscount)||0;
    const afterDisc = total - globalDisc;
    const taxPercent = Number(header.taxPercent)||0;
    const tax = afterDisc * (taxPercent / 100);
    const grand = afterDisc + tax;
    return { total, afterDisc, tax, grand };
  }, [items, header.globalDiscount, header.taxPercent]);

  const addItem = useCallback(()=>{
    if(!entry.code || !entry.name) return;
    const qty = Number(entry.qty)||0; const price = Number(entry.price)||0;
    if(qty<=0 || price<=0) return;
    const disc1 = Number(entry.disc1)||0; const disc2 = Number(entry.disc2)||0;
    const netPrice = calcNet(price, disc1, disc2);
    const subTotal = netPrice * qty;
    const newItem = { idTemp: Date.now(), code:entry.code, name:entry.name, qty, unit:entry.unit, price, disc1, disc2, netPrice, subTotal };

    setItems(prev => {
      if(mergeSame){
        const existing = prev.find(r => r.code === entry.code);
        if(existing){
          return prev.map(r => r.code === entry.code ? { ...r, qty: r.qty + qty, subTotal: r.netPrice * (r.qty + qty) } : r);
        }
      }
      return [...prev, newItem];
    });
    setEntry(emptyEntry);
  }, [entry, mergeSame]);

  const removeItem = (idTemp) => setItems(prev=> prev.filter(r=> r.idTemp!==idTemp));

  const startEditRow = (row) => {
    setEditingRow(row.idTemp);
    setEntry({ code:row.code, name:row.name, qty:row.qty, unit:row.unit, price:row.price, disc1:row.disc1, disc2:row.disc2 });
  };

  const applyEdit = () => {
    if(!editingRow) return;
    const qty = Number(entry.qty)||0; const price = Number(entry.price)||0;
    const disc1 = Number(entry.disc1)||0; const disc2 = Number(entry.disc2)||0;
    if(qty<=0 || price<=0){ toast.warn('Qty & Harga harus > 0'); return; }
    const netPrice = calcNet(price, disc1, disc2);
    const subTotal = netPrice * qty;
    setItems(prev => prev.map(r => r.idTemp === editingRow ? { ...r, code:entry.code, name:entry.name, qty, price, disc1, disc2, netPrice, subTotal } : r));
    setEditingRow(null);
    setEntry(emptyEntry);
  };

  const cancelEdit = () => { setEditingRow(null); setEntry(emptyEntry); };

  const handleKey = (e)=>{ if(e.key==='Enter'){ e.preventDefault(); editingRow ? applyEdit() : addItem(); }};

  const validate = () => {
    if(!header.invoiceNumber) return 'No Faktur wajib';
    if(invoiceExists) return 'No Faktur sudah ada';
    if(!header.supplier) return 'Supplier belum dipilih';
    if(items.length === 0) return 'Minimal 1 item';
    if(checkingInvoice) return 'Sedang validasi invoice...';
    return null;
  };

  const submit = async () => {
    const error = validate();
    if(error){ toast.error(error); return; }
    setSaving(true);
    try {
      const payload = {
        header: {
          receiptDate: header.receiptDate,
          dueDate: header.dueDate,
          supplier_id: header.supplier.id,
          invoiceNumber: header.invoiceNumber,
          taxPercent: Number(header.taxPercent)||0,
          globalDiscount: Number(header.globalDiscount)||0,
          note: header.note
        },
        items: items.map(r=> ({
          code:r.code,
          name:r.name,
          qty:r.qty,
          unit:r.unit,
          price:r.price,
          disc1:r.disc1,
          disc2:r.disc2
        }))
      };
      await purchasesAPI.create(payload);
      toast.success('Pembelian tersimpan');
      localStorage.removeItem('purchase-draft'); // Clear draft on successful save
      setHeader(emptyHeader);
      setItems([]);
      setLastSaved(null);
    } catch (e){
      console.error(e);
      toast.error('Gagal menyimpan');
    } finally { setSaving(false); }
  };

  const pickSupplier = (s) => { 
    setHeader(h=> ({...h, supplier:{ id:s.id, code:s.kodecust || s.kode || s.code, name:s.nama || s.namacust || s.name }})); 
    setSupplierModalOpen(false); 
  };

  const pickProduct = (p) => { 
    setEntry(en=> ({ 
      ...en, 
      code: p.kode_barang || p.kode || p.code, 
      name: p.nama_barang || p.namaBarang || p.name, 
      price: p.modal || p.harga || p.price || '',
      unit: p.satuan || p.unit || 'PCS'
    })); 
    setProductModalOpen(false); 
  };

  const format = (val) => new Intl.NumberFormat('id-ID').format(Number(val)||0);

  // Currency input formatting
  const formatCurrency = (value) => {
    const num = value.toString().replace(/[^\d]/g, '');
    return new Intl.NumberFormat('id-ID').format(num);
  };

  const handleCurrencyInput = (field, value) => {
    const cleaned = value.replace(/[^\d]/g, '');
    setEntry(v => ({ ...v, [field]: cleaned }));
  };

  // Keyboard shortcuts
  React.useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.ctrlKey || e.metaKey) {
        switch (e.key) {
          case 's':
            e.preventDefault();
            submit();
            break;
          case 'd':
            e.preventDefault();
            clearDraft();
            break;
          case 'p':
            e.preventDefault();
            setPrintMode(!printMode);
            break;
          case 'f':
            e.preventDefault();
            setSupplierModalOpen(true);
            break;
          case 'g':
            e.preventDefault();
            setProductModalOpen(true);
            break;
        }
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [printMode]);

  return (
    <div className={`purchase-form ${printMode ? 'print-mode' : ''}`}>
      <PageHeader />
      
      <div className="pf-header">
        <div className="pf-controls no-print">
          <label className="checkbox-label">
            <input type="checkbox" checked={mergeSame} onChange={e=>setMergeSame(e.target.checked)} />
            Gabung Item Sama
          </label>
          <label className="checkbox-label">
            <input type="checkbox" checked={autoSaveDraft} onChange={e=>setAutoSaveDraft(e.target.checked)} />
            Auto Save Draft
          </label>
          {lastSaved && <span className="text-xs text-gray-500">Tersimpan: {lastSaved}</span>}
          <button onClick={clearDraft} className="btn-secondary mini">Hapus Draft</button>
          <button onClick={()=>setPrintMode(!printMode)} className="btn-secondary">
            {printMode ? 'Mode Normal' : 'Mode Print'}
          </button>
          <button onClick={()=>setShowShortcuts(!showShortcuts)} className="btn-secondary mini" title="Keyboard Shortcuts">
            ⌨️
          </button>
        </div>
      </div>

      <div className="pf-header-form">
        <div className="pf-field">
          <label>Tgl Terima</label>
          <input type="date" value={header.receiptDate} onChange={e=>setHeader(h=>({...h, receiptDate:e.target.value}))} />
        </div>
        <div className="pf-field">
          <label>Tgl Jatuh Tempo</label>
          <input type="date" value={header.dueDate} onChange={e=>setHeader(h=>({...h, dueDate:e.target.value}))} />
        </div>
        <div className="pf-field">
          <label>
            No Faktur 
            {checkingInvoice && <span className="text-xs text-blue-500 ml-1">(checking...)</span>}
          </label>
          <div className="pf-invoice-field">
            <input 
              value={header.invoiceNumber} 
              onChange={e=>setHeader(h=>({...h, invoiceNumber:e.target.value}))} 
              className={invoiceExists ? 'error' : ''}
              placeholder="PO2024001" 
            />
            {invoiceExists && <span className="error-indicator">⚠ Sudah ada</span>}
          </div>
        </div>
        <div className="pf-field span-2">
          <label>Supplier</label>
          <div className="pf-supplier-select">
            <input value={header.supplier?.name || ''} placeholder="(pilih)" readOnly />
            <button type="button" className="btn-secondary mini" onClick={()=> setSupplierModalOpen(true)}>Cari</button>
          </div>
        </div>
        <div className="pf-field">
          <label>PPN %</label>
          <input type="number" value={header.taxPercent} onChange={e=>setHeader(h=>({...h, taxPercent:e.target.value}))} />
        </div>
        <div className="pf-field">
          <label>Diskon Global</label>
          <input type="text" value={formatCurrency(header.globalDiscount)} onChange={e=>setHeader(h=>({...h, globalDiscount:e.target.value.replace(/[^\d]/g, '')}))} />
        </div>
        <div className="pf-field span-2">
          <label>Catatan</label>
          <input value={header.note} onChange={e=>setHeader(h=>({...h, note:e.target.value}))} placeholder="Optional" />
        </div>
      </div>

      <div className="pf-entry-bar no-print" onKeyDown={handleKey}>
        <input className="pf-code" placeholder="Kode" value={entry.code} onChange={e=>setEntry(v=>({...v, code:e.target.value}))} />
        <input className="pf-name" placeholder="Nama Barang" value={entry.name} onChange={e=>setEntry(v=>({...v, name:e.target.value}))} />
        <input className="pf-qty" type="number" placeholder="Qty" value={entry.qty} onChange={e=>setEntry(v=>({...v, qty:e.target.value}))} />
        <input className="pf-unit" placeholder="Satuan" value={entry.unit} onChange={e=>setEntry(v=>({...v, unit:e.target.value}))} />
        <input className="pf-price" type="text" placeholder="Harga" value={formatCurrency(entry.price)} onChange={e=>handleCurrencyInput('price', e.target.value)} />
        <input className="pf-disc" type="number" placeholder="D1%" value={entry.disc1} onChange={e=>setEntry(v=>({...v, disc1:e.target.value}))} />
        <input className="pf-disc" type="number" placeholder="D2%" value={entry.disc2} onChange={e=>setEntry(v=>({...v, disc2:e.target.value}))} />
        {editingRow ? (
          <div className="pf-edit-actions">
            <button type="button" className="btn-primary pf-add" onClick={applyEdit}>Simpan</button>
            <button type="button" className="btn-secondary pf-add" onClick={cancelEdit}>Batal</button>
          </div>
        ) : (
          <>
            <button type="button" className="btn-primary pf-add" onClick={addItem}>Tambah</button>
            <button type="button" className="btn-secondary pf-add" onClick={()=> setProductModalOpen(true)}>Cari</button>
          </>
        )}
      </div>

      <div className="pf-items">
        <table>
          <thead>
            <tr>
              <th className="w-12">No</th>
              <th className="w-20">Kode</th>
              <th className="w-40">Nama Barang</th>
              <th className="w-12">Qty</th>
              <th className="w-12">Satuan</th>
              <th className="w-20">Harga</th>
              <th className="w-12">D1%</th>
              <th className="w-12">D2%</th>
              <th className="w-20">Harga Net</th>
              <th className="w-20">Subtotal</th>
              <th className="w-16 no-print">Aksi</th>
            </tr>
          </thead>
          <tbody>
            {items.map((r, i) => (
              <tr key={r.idTemp}>
                <td className="num">{i+1}</td>
                <td className="mono">{r.code}</td>
                <td>{r.name}</td>
                <td className="num">{r.qty}</td>
                <td>{r.unit}</td>
                <td className="num">{format(r.price)}</td>
                <td className="num">{r.disc1}%</td>
                <td className="num">{r.disc2}%</td>
                <td className="num">{format(r.netPrice)}</td>
                <td className="num">{format(r.subTotal)}</td>
                <td className="no-print">
                  <div className="pf-row-actions">
                    <button className="btn-icon-sm" onClick={()=> startEditRow(r)} title="Edit">✎</button>
                    <button className="btn-icon-sm" onClick={()=>removeItem(r.idTemp)} title="Hapus">✕</button>
                  </div>
                </td>
              </tr>
            ))}
            {items.length === 0 && (
              <tr><td colSpan="11" className="text-center text-gray-400 py-4">Belum ada item</td></tr>
            )}
          </tbody>
        </table>
      </div>

      <div className="pf-totals">
        <div className="pf-tot-line"><span>Total</span><span>{format(totals.total)}</span></div>
        <div className="pf-tot-line"><span>Diskon Global</span><span>{format(header.globalDiscount)}</span></div>
        <div className="pf-tot-line"><span>DPP</span><span>{format(totals.afterDisc)}</span></div>
        <div className="pf-tot-line"><span>PPN ({header.taxPercent}%)</span><span>{format(totals.tax)}</span></div>
        <div className="pf-tot-grand pf-tot-line"><span>Grand Total</span><span>{format(totals.grand)}</span></div>
        <div className="pf-actions no-print">
          <button type="button" className="btn-primary" disabled={saving || checkingInvoice || invoiceExists} onClick={submit}>
            {saving ? 'Menyimpan...' : 'Simpan'}
          </button>
        </div>
      </div>

      <div className="pf-print-footer">
        <div className="sign-col">Dibuat Oleh<br/><br/><br/>________________</div>
        <div className="sign-col">Diperiksa<br/><br/><br/>________________</div>
        <div className="sign-col">Disetujui<br/><br/><br/>________________</div>
      </div>

      {/* Supplier Modal */}
      {supplierModalOpen && (
        <div className="modal-overlay no-print">
          <div className="modal-content pf-modal">
            <div className="modal-header">
              <h3>Pilih Supplier</h3>
              <button onClick={()=> setSupplierModalOpen(false)} className="modal-close">×</button>
            </div>
            <div className="modal-body pf-modal-body">
              <input placeholder="Cari supplier..." value={supplierLookup} onChange={e=>setSupplierLookup(e.target.value)} className="form-input w-full mb-2" />
              <div className="pf-modal-list">
                {supplierResults.map(s=> (
                  <button key={s.id} className="pf-modal-row" onClick={()=> pickSupplier(s)}>
                    <span className="mono w-24 text-left">{s.kodecust || s.kode || s.code}</span>
                    <span className="flex-1 text-left">{s.nama || s.namacust || s.name}</span>
                  </button>
                ))}
                {supplierLookup && supplierResults.length===0 && (<div className="pf-modal-empty">Tidak ada hasil</div>)}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Product Modal */}
      {productModalOpen && (
        <div className="modal-overlay no-print">
          <div className="modal-content pf-modal">
            <div className="modal-header">
              <h3>Pilih Barang</h3>
              <button onClick={()=> setProductModalOpen(false)} className="modal-close">×</button>
            </div>
            <div className="modal-body pf-modal-body">
              <input placeholder="Cari barang..." value={productLookup} onChange={e=>setProductLookup(e.target.value)} className="form-input w-full mb-2" />
              <div className="pf-modal-list">
                {productResults.map(p=> (
                  <button key={p.id} className="pf-modal-row" onClick={()=> pickProduct(p)}>
                    <span className="mono w-24 text-left">{p.kode_barang || p.kode || p.code}</span>
                    <span className="flex-1 text-left">{p.nama_barang || p.namaBarang || p.name}</span>
                    <span className="num w-24">{p.modal || p.harga || p.price}</span>
                  </button>
                ))}
                {productLookup && productResults.length===0 && (<div className="pf-modal-empty">Tidak ada hasil</div>)}
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Shortcuts Help */}
      {showShortcuts && (
        <div className="modal-overlay no-print">
          <div className="modal-content" style={{maxWidth: '400px'}}>
            <div className="modal-header">
              <h3>Keyboard Shortcuts</h3>
              <button onClick={()=> setShowShortcuts(false)} className="modal-close">×</button>
            </div>
            <div className="modal-body">
              <div className="shortcuts-grid">
                <div><kbd>Ctrl+S</kbd> Simpan</div>
                <div><kbd>Ctrl+D</kbd> Hapus Draft</div>
                <div><kbd>Ctrl+P</kbd> Toggle Print Mode</div>
                <div><kbd>Ctrl+F</kbd> Cari Supplier</div>
                <div><kbd>Ctrl+G</kbd> Cari Barang</div>
                <div><kbd>Enter</kbd> Tambah/Simpan Item</div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

export default PurchaseForm;
