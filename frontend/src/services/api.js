import axios from 'axios';

// Base API configuration
const API_BASE_URL = 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
    // No authorization header for development
  }
});

// API response interceptor
api.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error('API Error:', error);
    return Promise.reject(error);
  }
);

// Generic API functions
const createAPIService = (endpoint) => ({
  getAll: () => api.get(endpoint),
  getById: (id) => api.get(`${endpoint}/${id}`),
  create: (data) => api.post(endpoint, data),
  update: (id, data) => api.put(`${endpoint}/${id}`, data),
  delete: (id) => api.delete(`${endpoint}/${id}`)
});

// Export API services for each endpoint
export const categoriesAPI = createAPIService('/categories');
export const customersAPI = createAPIService('/customers');
export const suppliersAPI = createAPIService('/suppliers');
export const salesAPI = createAPIService('/sales');
export const barangAPI = createAPIService('/barangs');
export const purchasesAPI = createAPIService('/purchases');

export const companiesAPI = createAPIService('/companies');
export const invoicesAPI = createAPIService('/invoices');
export const invoiceDetailsAPI = createAPIService('/invoice-details');
export const partPenerimaanAPI = createAPIService('/part-penerimaan');
export const penerimaanFinanceAPI = createAPIService('/penerimaan-finance');
export const returnSalesAPI = createAPIService('/return-sales');
export const kartuStokAPI = createAPIService('/kartu-stok');
export const tmpPrintInvoicesAPI = createAPIService('/tmp-print-invoices');
export const journalsAPI = createAPIService('/journals');
export const banksAPI = createAPIService('/banks');
export const areasAPI = createAPIService('/areas');
export const divisionsAPI = createAPIService('/divisions');
export const documentsAPI = createAPIService('/documents');
export const modulesAPI = createAPIService('/modules');
export const usersAPI = createAPIService('/users');

// Extended API services for all modules
export const sparepartsAPI = {
  getAll: () => api.get('/barang'),
  create: (data) => api.post('/barang', data),
  update: (kodeDivisi, kodeBarang, data) => api.put(`/barang/${kodeDivisi}/${kodeBarang}`, data),
  delete: (kodeDivisi, kodeBarang) => api.delete(`/barang/${kodeDivisi}/${kodeBarang}`),
  getByDivisi: (kodeDivisi) => api.get(`/barang/${kodeDivisi}`),
};

export const stockMinAPI = {
  getAll: () => api.get('/barang'),
  update: (kodeDivisi, kodeBarang, data) => api.put(`/barang/${kodeDivisi}/${kodeBarang}`, data),
};

export const checklistAPI = {
  getAll: () => api.get('/dokumens'),
  create: (data) => api.post('/dokumens', data),
  update: (kodeDivisi, kodeDokumen, data) => api.put(`/dokumens/${kodeDivisi}/${kodeDokumen}`, data),
  delete: (kodeDivisi, kodeDokumen) => api.delete(`/dokumens/${kodeDivisi}/${kodeDokumen}`),
};

export const rekeningAPI = {
  getAll: () => api.get('/coas'),
  create: (data) => api.post('/coas', data),
  update: (kodeDivisi, kodeCOA, data) => api.put(`/coas/${kodeDivisi}/${kodeCOA}`, data),
  delete: (kodeDivisi, kodeCOA) => api.delete(`/coas/${kodeDivisi}/${kodeCOA}`),
};

// Transaction APIs
export const mergeBarangAPI = {
  getAll: () => api.get('/barang'),
  merge: (data) => api.post('/barang', data),
};

export const invoiceCancelAPI = {
  getAll: () => api.get('/invoices'),
  cancel: (id) => api.delete(`/invoices/${id}`),
};

export const stockOpnameAPI = {
  getAll: () => api.get('/opnames'),
  create: (data) => api.post('/opnames', data),
  update: (id, data) => api.put(`/opnames/${id}`, data),
  delete: (id) => api.delete(`/opnames/${id}`),
};

export const pembelianBonusAPI = {
  getAll: () => api.get('/part-penerimaan/all'),
  create: (data) => api.post('/part-penerimaan', data),
  update: (id, data) => api.put(`/part-penerimaan/${id}`, data),
  delete: (id) => api.delete(`/part-penerimaan/${id}`),
};

export const penjualanBonusAPI = {
  getAll: () => api.get('/invoices'),
  create: (data) => api.post('/invoices', data),
  update: (id, data) => api.put(`/invoices/${id}`, data),
  delete: (id) => api.delete(`/invoices/${id}`),
};

export const customerClaimAPI = {
  getAll: () => api.get('/return-sales'),
  create: (data) => api.post('/return-sales', data),
  getCustomers: () => api.get('/return-sales/customers'),
  getInvoices: () => api.get('/return-sales/invoices'),
};

export const pengembalianClaimAPI = {
  getAll: () => api.get('/return-purchases'),
  create: (data) => api.post('/return-purchases', data),
  getPurchases: () => api.get('/return-purchases/purchases'),
};

// Sales Form APIs
export const salesFormAPI = {
  getCustomers: () => api.get('/customers'),
  getSalesPersons: () => api.get('/sales'),
  getBarang: () => api.get('/barang'),
  createInvoice: (data) => api.post('/invoices', data),
};

// Finance APIs
export const penerimaanGiroAPI = {
  getAll: () => api.get('/penerimaan-finance/all'),
  create: (data) => api.post('/penerimaan-finance', data),
  update: (id, data) => api.put(`/penerimaan-finance/${id}`, data),
  delete: (id) => api.delete(`/penerimaan-finance/${id}`),
};

export const pencarianGiroAPI = {
  getAll: () => api.get('/penerimaan-finance/all'),
  search: (params) => api.get('/penerimaan-finance/all', { params }),
};

export const penerimaanResiAPI = {
  getAll: () => api.get('/penerimaan-finance/all'),
  create: (data) => api.post('/penerimaan-finance', data),
  update: (id, data) => api.put(`/penerimaan-finance/${id}`, data),
  delete: (id) => api.delete(`/penerimaan-finance/${id}`),
};

export const piutangResiAPI = {
  getAll: () => api.get('/penerimaan-finance/all'),
  create: (data) => api.post('/penerimaan-finance', data),
  update: (id, data) => api.put(`/penerimaan-finance/${id}`, data),
  delete: (id) => api.delete(`/penerimaan-finance/${id}`),
};

export const piutangReturAPI = {
  getAll: () => api.get('/return-sales'),
  create: (data) => api.post('/return-sales', data),
  update: (id, data) => api.put(`/return-sales/${id}`, data),
  delete: (id) => api.delete(`/return-sales/${id}`),
};

export const penambahanSaldoAPI = {
  getAll: () => api.get('/journals/all'),
  create: (data) => api.post('/journals', data),
  update: (id, data) => api.put(`/journals/${id}`, data),
  delete: (id) => api.delete(`/journals/${id}`),
};

export const penguranganSaldoAPI = {
  getAll: () => api.get('/journals/all'),
  create: (data) => api.post('/journals', data),
  update: (id, data) => api.put(`/journals/${id}`, data),
  delete: (id) => api.delete(`/journals/${id}`),
};

// Finance APIs grouped
export const financeAPI = {
  penerimaan: {
    giro: penerimaanGiroAPI,
    resi: penerimaanResiAPI,
  },
  pencarian: {
    giro: pencarianGiroAPI,
  },
  piutang: {
    resi: piutangResiAPI,
    retur: piutangReturAPI,
  },
  saldo: {
    penambahan: penambahanSaldoAPI,
    pengurangan: penguranganSaldoAPI,
  }
};

// Reports APIs
export const reportsAPI = {
  stokBarang: () => api.get('/kartu-stok/all'),
  kartuStok: (kodeDivisi, kodeBarang) => api.get(`/kartu-stok/by-barang/${kodeDivisi}/${kodeBarang}`),
  pembelian: () => api.get('/part-penerimaan/all'),
  pembelianItem: () => api.get('/part-penerimaan/all'),
  penjualan: () => api.get('/invoices'),
  cogs: () => api.get('/kartu-stok/all'),
  returnSales: () => api.get('/return-sales'),
  tampilInvoice: () => api.get('/invoices'),
  saldoRekening: () => api.get('/journals/all'),
  pembayaranCustomer: () => api.get('/penerimaan-finance/all'),
  tagihan: () => api.get('/invoices'),
  pemotonganReturnCustomer: () => api.get('/return-sales'),
  komisiSales: () => api.get('/invoices'),
};

// Dashboard API
export const dashboardAPI = {
  getStats: () => api.get('/dashboard/stats'),
  getRecentTransactions: () => api.get('/dashboard/recent-transactions'),
  getChartData: () => api.get('/dashboard/chart-data')
};

export default api;
