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
export const barangAPI = createAPIService('/barang');

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

// Dashboard API
export const dashboardAPI = {
  getStats: () => api.get('/dashboard/stats'),
  getRecentTransactions: () => api.get('/dashboard/recent-transactions'),
  getChartData: () => api.get('/dashboard/chart-data')
};

export default api;
