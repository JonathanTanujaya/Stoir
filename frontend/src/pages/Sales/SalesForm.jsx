import React, { useState, useEffect } from 'react';
import { toast } from 'react-toastify';
import PageHeader from '../../components/Layout/PageHeader';

const SalesForm = () => {
    // State for form header
    const [formData, setFormData] = useState({
        kode_customer: '',
        kode_sales: '',
        tipe: 'Cash',
        tanggal: new Date().toISOString().split('T')[0],
        grand_total: 0,
        discount_percent: 0,
        discount_amount: 0,
        pajak_percent: 0,
        pajak_amount: 0,
        final_total: 0
    });

    // State for item input
    const [itemInput, setItemInput] = useState({
        kode_barang: '',
        nama_barang: '',
        harga: 0,
        qty: 1,
        disc1: 0,
        disc2: 0
    });

    // State for items list
    const [items, setItems] = useState([]);

    // State for dropdowns data
    const [customers, setCustomers] = useState([]);
    const [salesPersons, setSalesPersons] = useState([]);
    const [barangs, setBarangs] = useState([]);

    // State for autocomplete
    const [customerSearch, setCustomerSearch] = useState('');
    const [salesSearch, setSalesSearch] = useState('');
    const [barangSearch, setBarangSearch] = useState('');
    const [showCustomerDropdown, setShowCustomerDropdown] = useState(false);
    const [showSalesDropdown, setShowSalesDropdown] = useState(false);
    const [showBarangDropdown, setShowBarangDropdown] = useState(false);

    const [loading, setLoading] = useState(false);

    // Initialize data
    useEffect(() => {
        fetchCustomers();
        fetchSalesPersons();
        fetchBarangs();
    }, []);

    // API calls
    const fetchCustomers = async () => {
        try {
            const response = await fetch('http://localhost:8000/api/return-sales/customers');
            const data = await response.json();
            if (data.status === 'success') {
                setCustomers(data.data);
            }
        } catch (error) {
            console.error('Error fetching customers:', error);
            toast.error('Failed to load customers');
        }
    };

    const fetchSalesPersons = async () => {
        try {
            const response = await fetch('http://localhost:8000/api/sales-persons');
            const data = await response.json();
            if (data.success) {
                setSalesPersons(data.data);
            }
        } catch (error) {
            console.error('Error fetching sales persons:', error);
            toast.error('Failed to load sales persons');
        }
    };

    const fetchBarangs = async () => {
        try {
            const response = await fetch('http://localhost:8000/api/barang');
            const data = await response.json();
            if (data.success) {
                setBarangs(data.data);
            }
        } catch (error) {
            console.error('Error fetching barang:', error);
            toast.error('Failed to load barang');
        }
    };

    // Filter functions for autocomplete
    const filteredCustomers = customers.filter(customer =>
        customer.code.toLowerCase().includes(customerSearch.toLowerCase()) ||
        customer.name.toLowerCase().includes(customerSearch.toLowerCase())
    );

    const filteredSalesPersons = salesPersons.filter(sales =>
        sales.code.toLowerCase().includes(salesSearch.toLowerCase()) ||
        sales.name.toLowerCase().includes(salesSearch.toLowerCase())
    );

    const filteredBarangs = barangs.filter(barang =>
        barang.code.toLowerCase().includes(barangSearch.toLowerCase()) ||
        barang.name.toLowerCase().includes(barangSearch.toLowerCase())
    );

    // Handle customer selection
    const handleCustomerSelect = (customer) => {
        setFormData(prev => ({ ...prev, kode_customer: customer.code }));
        setCustomerSearch(`${customer.code} - ${customer.name}`);
        setShowCustomerDropdown(false);
    };

    // Handle sales selection
    const handleSalesSelect = (sales) => {
        setFormData(prev => ({ ...prev, kode_sales: sales.code }));
        setSalesSearch(`${sales.code} - ${sales.name}`);
        setShowSalesDropdown(false);
    };

    // Handle barang selection
    const handleBarangSelect = (barang) => {
        setItemInput(prev => ({
            ...prev,
            kode_barang: barang.code,
            nama_barang: barang.name,
            harga: barang.harga_jual || 0
        }));
        setBarangSearch(`${barang.code} - ${barang.name}`);
        setShowBarangDropdown(false);
    };

    // Add item to list
    const addItem = () => {
        if (!itemInput.kode_barang || !itemInput.nama_barang) {
            toast.error('Please select a product');
            return;
        }

        if (itemInput.qty <= 0) {
            toast.error('Quantity must be greater than 0');
            return;
        }

        const hargaSetelahDisc1 = itemInput.harga - (itemInput.harga * itemInput.disc1 / 100);
        const hargaNett = hargaSetelahDisc1 - (hargaSetelahDisc1 * itemInput.disc2 / 100);
        const subTotal = hargaNett * itemInput.qty;

        const newItem = {
            id: Date.now(),
            kode_barang: itemInput.kode_barang,
            nama_barang: itemInput.nama_barang,
            qty: itemInput.qty,
            satuan: 'PCS', // Default satuan
            harga_jual: itemInput.harga,
            disc1: itemInput.disc1,
            disc2: itemInput.disc2,
            harga_nett: hargaNett,
            sub_total: subTotal
        };

        setItems(prev => [...prev, newItem]);
        calculateTotal([...items, newItem]);
        
        // Reset item input
        setItemInput({
            kode_barang: '',
            nama_barang: '',
            harga: 0,
            qty: 1,
            disc1: 0,
            disc2: 0
        });
        setBarangSearch('');
    };

    // Remove item from list
    const removeItem = (itemId) => {
        const updatedItems = items.filter(item => item.id !== itemId);
        setItems(updatedItems);
        calculateTotal(updatedItems);
    };

    // Calculate totals
    const calculateTotal = (itemsList) => {
        const grandTotal = itemsList.reduce((sum, item) => sum + item.sub_total, 0);
        const discountAmount = grandTotal * formData.discount_percent / 100;
        const afterDiscount = grandTotal - discountAmount;
        const pajakAmount = afterDiscount * formData.pajak_percent / 100;
        const finalTotal = afterDiscount + pajakAmount;

        setFormData(prev => ({
            ...prev,
            grand_total: grandTotal,
            discount_amount: discountAmount,
            pajak_amount: pajakAmount,
            final_total: finalTotal
        }));
    };

    // Handle discount change
    const handleDiscountChange = (percent) => {
        setFormData(prev => ({ ...prev, discount_percent: percent }));
        const discountAmount = formData.grand_total * percent / 100;
        const afterDiscount = formData.grand_total - discountAmount;
        const pajakAmount = afterDiscount * formData.pajak_percent / 100;
        const finalTotal = afterDiscount + pajakAmount;

        setFormData(prev => ({
            ...prev,
            discount_amount: discountAmount,
            pajak_amount: pajakAmount,
            final_total: finalTotal
        }));
    };

    // Handle pajak change
    const handlePajakChange = (percent) => {
        setFormData(prev => ({ ...prev, pajak_percent: percent }));
        const afterDiscount = formData.grand_total - formData.discount_amount;
        const pajakAmount = afterDiscount * percent / 100;
        const finalTotal = afterDiscount + pajakAmount;

        setFormData(prev => ({
            ...prev,
            pajak_amount: pajakAmount,
            final_total: finalTotal
        }));
    };

    // Submit form
    const handleSubmit = async (e) => {
        e.preventDefault();
        
        if (items.length === 0) {
            toast.error('Please add at least one item');
            return;
        }

        if (!formData.kode_customer) {
            toast.error('Please select a customer');
            return;
        }

        try {
            setLoading(true);
            const response = await fetch('http://localhost:8000/api/sales', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    header: formData,
                    items: items
                })
            });

            const result = await response.json();
            if (result.success) {
                toast.success('Sales transaction created successfully');
                // Reset form
                setFormData({
                    kode_customer: '',
                    kode_sales: '',
                    tipe: 'Cash',
                    tanggal: new Date().toISOString().split('T')[0],
                    grand_total: 0,
                    discount_percent: 0,
                    discount_amount: 0,
                    pajak_percent: 0,
                    pajak_amount: 0,
                    final_total: 0
                });
                setItems([]);
                setCustomerSearch('');
                setSalesSearch('');
            } else {
                toast.error(result.message || 'Failed to create sales transaction');
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            toast.error('An error occurred while saving');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="sales-form">
            <PageHeader 
                title="Form Penjualan"
                subtitle="Buat transaksi penjualan baru"
            />
            
            <div className="content-container">
                <form onSubmit={handleSubmit} className="sales-form-container">
                    {/* Header Section */}
                    <div className="form-header">
                        <div className="header-row">
                            <div className="form-group">
                                <label htmlFor="kode_customer">Kode Customer</label>
                                <div className="autocomplete-container">
                                    <input
                                        type="text"
                                        id="kode_customer"
                                        value={customerSearch}
                                        onChange={(e) => {
                                            setCustomerSearch(e.target.value);
                                            setShowCustomerDropdown(true);
                                        }}
                                        onFocus={() => setShowCustomerDropdown(true)}
                                        placeholder="Pilih atau ketik kode customer..."
                                        required
                                    />
                                    {showCustomerDropdown && (
                                        <div className="autocomplete-dropdown">
                                            {filteredCustomers.slice(0, 10).map((customer) => (
                                                <div
                                                    key={customer.id}
                                                    className="autocomplete-item"
                                                    onClick={() => handleCustomerSelect(customer)}
                                                >
                                                    <strong>{customer.code}</strong> - {customer.name}
                                                    <br />
                                                    <small className="text-gray-500">{customer.address}</small>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>

                            <div className="form-group">
                                <label htmlFor="kode_sales">Kode Sales</label>
                                <div className="autocomplete-container">
                                    <input
                                        type="text"
                                        id="kode_sales"
                                        value={salesSearch}
                                        onChange={(e) => {
                                            setSalesSearch(e.target.value);
                                            setShowSalesDropdown(true);
                                        }}
                                        onFocus={() => setShowSalesDropdown(true)}
                                        placeholder="Pilih atau ketik kode sales..."
                                    />
                                    {showSalesDropdown && (
                                        <div className="autocomplete-dropdown">
                                            {filteredSalesPersons.slice(0, 10).map((sales) => (
                                                <div
                                                    key={sales.id}
                                                    className="autocomplete-item"
                                                    onClick={() => handleSalesSelect(sales)}
                                                >
                                                    <strong>{sales.code}</strong> - {sales.name}
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>

                            <div className="form-group">
                                <label htmlFor="tipe">Tipe</label>
                                <select
                                    id="tipe"
                                    value={formData.tipe}
                                    onChange={(e) => setFormData(prev => ({ ...prev, tipe: e.target.value }))}
                                >
                                    <option value="Cash">Cash</option>
                                    <option value="Credit">Credit</option>
                                    <option value="Transfer">Transfer</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Item Input Section */}
                    <div className="item-input-section">
                        <div className="item-input-row">
                            <div className="form-group flex-2">
                                <label htmlFor="kode_barang">Kode Barang</label>
                                <div className="autocomplete-container">
                                    <input
                                        type="text"
                                        id="kode_barang"
                                        value={barangSearch}
                                        onChange={(e) => {
                                            setBarangSearch(e.target.value);
                                            setShowBarangDropdown(true);
                                        }}
                                        onFocus={() => setShowBarangDropdown(true)}
                                        placeholder="Pilih atau ketik kode barang..."
                                    />
                                    {showBarangDropdown && (
                                        <div className="autocomplete-dropdown">
                                            {filteredBarangs.slice(0, 10).map((barang) => (
                                                <div
                                                    key={barang.id}
                                                    className="autocomplete-item"
                                                    onClick={() => handleBarangSelect(barang)}
                                                >
                                                    <strong>{barang.code}</strong> - {barang.name}
                                                    <br />
                                                    <small className="text-gray-500">Stok: {barang.stok} | Harga: {new Intl.NumberFormat('id-ID').format(barang.harga_jual)}</small>
                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            </div>

                            <div className="form-group flex-2">
                                <label htmlFor="nama_barang">Nama Barang</label>
                                <input
                                    type="text"
                                    id="nama_barang"
                                    value={itemInput.nama_barang}
                                    readOnly
                                    placeholder="Nama barang akan terisi otomatis"
                                />
                            </div>

                            <div className="form-group">
                                <label htmlFor="harga">Harga</label>
                                <input
                                    type="number"
                                    id="harga"
                                    value={itemInput.harga}
                                    onChange={(e) => setItemInput(prev => ({ ...prev, harga: parseFloat(e.target.value) || 0 }))}
                                    min="0"
                                    step="0.01"
                                />
                            </div>

                            <div className="form-group">
                                <label htmlFor="qty">Qty</label>
                                <input
                                    type="number"
                                    id="qty"
                                    value={itemInput.qty}
                                    onChange={(e) => setItemInput(prev => ({ ...prev, qty: parseInt(e.target.value) || 1 }))}
                                    min="1"
                                />
                            </div>

                            <div className="form-group">
                                <label htmlFor="disc1">Disc1 (%)</label>
                                <input
                                    type="number"
                                    id="disc1"
                                    value={itemInput.disc1}
                                    onChange={(e) => setItemInput(prev => ({ ...prev, disc1: parseFloat(e.target.value) || 0 }))}
                                    min="0"
                                    max="100"
                                    step="0.01"
                                />
                            </div>

                            <div className="form-group">
                                <label htmlFor="disc2">Disc2 (%)</label>
                                <input
                                    type="number"
                                    id="disc2"
                                    value={itemInput.disc2}
                                    onChange={(e) => setItemInput(prev => ({ ...prev, disc2: parseFloat(e.target.value) || 0 }))}
                                    min="0"
                                    max="100"
                                    step="0.01"
                                />
                            </div>

                            <div className="form-group">
                                <button
                                    type="button"
                                    onClick={addItem}
                                    className="btn btn-add-item"
                                >
                                    Tambah Item
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Items Table */}
                    <div className="items-table-section">
                        <div className="table-responsive">
                            <table className="items-table">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Qty Supply</th>
                                        <th>Satuan</th>
                                        <th>Harga Jual</th>
                                        <th>Diskon1</th>
                                        <th>Diskon2</th>
                                        <th>Harga Nett</th>
                                        <th>Sub Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {items.map((item, index) => (
                                        <tr key={item.id}>
                                            <td>{index + 1}</td>
                                            <td>{item.kode_barang}</td>
                                            <td>{item.nama_barang}</td>
                                            <td>{item.qty}</td>
                                            <td>{item.satuan}</td>
                                            <td>{new Intl.NumberFormat('id-ID').format(item.harga_jual)}</td>
                                            <td>{item.disc1}%</td>
                                            <td>{item.disc2}%</td>
                                            <td>{new Intl.NumberFormat('id-ID').format(item.harga_nett)}</td>
                                            <td>{new Intl.NumberFormat('id-ID').format(item.sub_total)}</td>
                                            <td>
                                                <button
                                                    type="button"
                                                    onClick={() => removeItem(item.id)}
                                                    className="btn btn-remove"
                                                >
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                    {items.length === 0 && (
                                        <tr>
                                            <td colSpan="11" className="text-center text-gray-500 py-4">
                                                Belum ada item yang ditambahkan
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* Footer Totals */}
                    <div className="form-footer">
                        <div className="totals-section">
                            <div className="total-row">
                                <label>Grand Total:</label>
                                <input
                                    type="text"
                                    value={new Intl.NumberFormat('id-ID').format(formData.grand_total)}
                                    readOnly
                                    className="total-input"
                                />
                            </div>

                            <div className="total-row">
                                <label>Discount:</label>
                                <div className="discount-inputs">
                                    <input
                                        type="number"
                                        value={formData.discount_percent}
                                        onChange={(e) => handleDiscountChange(parseFloat(e.target.value) || 0)}
                                        placeholder="%"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        className="percent-input"
                                    />
                                    <span>%</span>
                                    <label>Pajak:</label>
                                    <input
                                        type="number"
                                        value={formData.pajak_percent}
                                        onChange={(e) => handlePajakChange(parseFloat(e.target.value) || 0)}
                                        placeholder="%"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        className="percent-input"
                                    />
                                    <span>%</span>
                                </div>
                            </div>

                            <div className="total-row final-total">
                                <label>Grand Total:</label>
                                <input
                                    type="text"
                                    value={new Intl.NumberFormat('id-ID').format(formData.final_total)}
                                    readOnly
                                    className="final-total-input"
                                />
                            </div>
                        </div>

                        <div className="form-actions">
                            <button
                                type="submit"
                                disabled={loading || items.length === 0}
                                className="btn btn-submit"
                            >
                                {loading ? 'Menyimpan...' : 'Simpan Transaksi'}
                            </button>
                            <button
                                type="button"
                                onClick={() => window.history.back()}
                                className="btn btn-cancel"
                            >
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default SalesForm;
