import axios from 'axios';

// Create axios instance with base configuration
const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  timeout: 30000, // 30 seconds
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

// Response interceptor for error handling and response transformation
apiClient.interceptors.response.use(
  response => {
    // Log responses in development
    if (import.meta.env.DEV) {
      console.log(`API Response: ${response.config.method?.toUpperCase()} ${response.config.url}`, {
        status: response.status,
        data: response.data,
      });
    }

    // Transform Laravel paginated responses
    if (response.data.data && Array.isArray(response.data.data)) {
      return {
        ...response,
        data: {
          data: response.data.data,
          totalCount: response.data.totalCount || response.data.total || response.data.data.length,
          success: response.data.success !== false,
          message: response.data.message || 'Success',
          // Include pagination info if available
          ...(response.data.current_page && {
            pagination: {
              currentPage: response.data.current_page,
              lastPage: response.data.last_page,
              perPage: response.data.per_page,
              total: response.data.total,
            },
          }),
        },
      };
    }

    return response;
  },
  error => {
    // Handle different error types
    if (error.response) {
      // Server responded with error status
      const { status, data } = error.response;

      console.error(`API Error ${status}:`, {
        url: error.config?.url,
        method: error.config?.method,
        message: data?.message || error.message,
        errors: data?.errors,
      });

      // Handle specific status codes
      switch (status) {
        case 401:
          // Unauthorized - clear token and redirect to login
          localStorage.removeItem('auth_token');
          window.location.href = '/login';
          break;
        case 403:
          // Forbidden
          console.warn('Access forbidden to resource');
          break;
        case 422:
          // Validation errors
          console.warn('Validation errors:', data?.errors);
          break;
        case 429:
          // Rate limiting
          console.warn('Rate limit exceeded');
          break;
        case 500:
          // Server error
          console.error('Server error occurred');
          break;
      }

      // Transform error response for consistent handling
      const transformedError = new Error(data?.message || `HTTP ${status} Error`);
      transformedError.status = status;
      transformedError.errors = data?.errors;
      transformedError.response = error.response;

      return Promise.reject(transformedError);
    } else if (error.request) {
      // Network error
      console.error('Network error:', error.message);
      const networkError = new Error('Network error - please check your connection');
      networkError.isNetworkError = true;
      return Promise.reject(networkError);
    } else {
      // Other error
      console.error('API setup error:', error.message);
      return Promise.reject(error);
    }
  }
);

// Generic API methods
export const api = {
  // GET requests
  get: async (url, params = {}) => {
    const response = await apiClient.get(url, { params });
    return response.data;
  },

  // GET single item by ID
  getById: async (url, id) => {
    const response = await apiClient.get(`${url}/${id}`);
    return response.data;
  },

  // POST requests
  post: async (url, data = {}) => {
    const response = await apiClient.post(url, data);
    return response.data;
  },

  // PUT requests (full update)
  put: async (url, id, data = {}) => {
    const response = await apiClient.put(`${url}/${id}`, data);
    return response.data;
  },

  // PATCH requests (partial update)
  patch: async (url, id, data = {}) => {
    const response = await apiClient.patch(`${url}/${id}`, data);
    return response.data;
  },

  // DELETE requests
  delete: async (url, id) => {
    const response = await apiClient.delete(`${url}/${id}`);
    return response.data;
  },

  // Special method for composite keys (like m_resi)
  getByCompositeKey: async (url, key1, key2) => {
    const response = await apiClient.get(`${url}/${key1}/${key2}`);
    return response.data;
  },

  // Special method for composite key updates
  updateByCompositeKey: async (url, key1, key2, data = {}) => {
    const response = await apiClient.put(`${url}/${key1}/${key2}`, data);
    return response.data;
  },

  // Special method for composite key deletes
  deleteByCompositeKey: async (url, key1, key2) => {
    const response = await apiClient.delete(`${url}/${key1}/${key2}`);
    return response.data;
  },

  // Bulk operations
  bulkCreate: async (url, dataArray) => {
    const response = await apiClient.post(`${url}/bulk`, { data: dataArray });
    return response.data;
  },

  bulkUpdate: async (url, dataArray) => {
    const response = await apiClient.patch(`${url}/bulk`, { data: dataArray });
    return response.data;
  },

  bulkDelete: async (url, idsArray) => {
    const response = await apiClient.delete(`${url}/bulk`, { data: { ids: idsArray } });
    return response.data;
  },
};

// Generic API functions for backward compatibility
const createAPIService = endpoint => ({
  getAll: () => api.get(endpoint),
  getById: id => api.getById(endpoint, id),
  create: data => api.post(endpoint, data),
  update: (id, data) => api.put(endpoint, id, data),
  delete: id => api.delete(endpoint, id),
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
export const stockMinAPI = {
  getAll: () => api.get('/barang'),
  update: (kodeDivisi, kodeBarang, data) => api.put(`/barang/${kodeDivisi}/${kodeBarang}`, data),
};

export const checklistAPI = {
  getAll: () => api.get('/dokumens'),
  create: data => api.post('/dokumens', data),
  update: (kodeDivisi, kodeDokumen, data) =>
    api.put(`/dokumens/${kodeDivisi}/${kodeDokumen}`, data),
  delete: (kodeDivisi, kodeDokumen) => api.delete(`/dokumens/${kodeDivisi}/${kodeDokumen}`),
};

// Transaction APIs
export const mergeBarangAPI = {
  getAll: () => api.get('/barang'),
  merge: data => api.post('/barang', data),
};

export const invoiceCancelAPI = {
  getAll: () => api.get('/invoices'),
  cancel: id => api.delete(`/invoices/${id}`),
};

export const stockOpnameAPI = {
  getAll: () => api.get('/opnames'),
  create: data => api.post('/opnames', data),
  update: (id, data) => api.put(`/opnames/${id}`, data),
  delete: id => api.delete(`/opnames/${id}`),
};

export const pembelianBonusAPI = {
  getAll: () => api.get('/part-penerimaan/all'),
  create: data => api.post('/part-penerimaan', data),
  update: (id, data) => api.put(`/part-penerimaan/${id}`, data),
  delete: id => api.delete(`/part-penerimaan/${id}`),
};

export const penjualanBonusAPI = {
  getAll: () => api.get('/invoices'),
  create: data => api.post('/invoices', data),
  update: (id, data) => api.put(`/invoices/${id}`, data),
  delete: id => api.delete(`/invoices/${id}`),
};

export const customerClaimAPI = {
  getAll: () => api.get('/return-sales'),
  create: data => api.post('/return-sales', data),
  getCustomers: () => api.get('/return-sales/customers'),
  getInvoices: () => api.get('/return-sales/invoices'),
};

export const pengembalianClaimAPI = {
  getAll: () => api.get('/return-purchases'),
  create: data => api.post('/return-purchases', data),
  getPurchases: () => api.get('/return-purchases/purchases'),
};

// Sales Form APIs
export const salesFormAPI = {
  getCustomers: () => api.get('/customers'),
  getSalesPersons: () => api.get('/sales'),
  getBarang: () => api.get('/barang'),
  createInvoice: data => api.post('/invoices', data),
};

// Finance APIs
export const penerimaanGiroAPI = {
  getAll: () => api.get('/penerimaan-finance/all'),
  create: data => api.post('/penerimaan-finance', data),
  update: (id, data) => api.put(`/penerimaan-finance/${id}`, data),
  delete: id => api.delete(`/penerimaan-finance/${id}`),
};

export const pencarianGiroAPI = {
  getAll: () => api.get('/penerimaan-finance/all'),
  search: params => api.get('/penerimaan-finance/all', { params }),
};

export const penerimaanResiAPI = {
  getAll: () => api.get('/penerimaan-finance/all'),
  create: data => api.post('/penerimaan-finance', data),
  update: (id, data) => api.put(`/penerimaan-finance/${id}`, data),
  delete: id => api.delete(`/penerimaan-finance/${id}`),
};

export const piutangResiAPI = {
  getAll: () => api.get('/penerimaan-finance/all'),
  create: data => api.post('/penerimaan-finance', data),
  update: (id, data) => api.put(`/penerimaan-finance/${id}`, data),
  delete: id => api.delete(`/penerimaan-finance/${id}`),
};

export const piutangReturAPI = {
  getAll: () => api.get('/return-sales'),
  create: data => api.post('/return-sales', data),
  update: (id, data) => api.put(`/return-sales/${id}`, data),
  delete: id => api.delete(`/return-sales/${id}`),
};

export const penambahanSaldoAPI = {
  getAll: () => api.get('/journals/all'),
  create: data => api.post('/journals', data),
  update: (id, data) => api.put(`/journals/${id}`, data),
  delete: id => api.delete(`/journals/${id}`),
};

export const penguranganSaldoAPI = {
  getAll: () => api.get('/journals/all'),
  create: data => api.post('/journals', data),
  update: (id, data) => api.put(`/journals/${id}`, data),
  delete: id => api.delete(`/journals/${id}`),
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
  },
};

// Reports APIs
export const reportsAPI = {
  stokBarang: () => api.get('/kartu-stok/all'),
  kartuStok: (kodeDivisi, kodeBarang) =>
    api.get(`/kartu-stok/by-barang/${kodeDivisi}/${kodeBarang}`),
  pembelian: () => api.get('/part-penerimaan/all'),
  pembelianItem: () => api.get('/part-penerimaan/all'),
  penjualan: () => api.get('/invoices'),
  cogs: () => api.get('/kartu-stok/all'),
  returnSales: () => api.get('/return-sales'),
  tampilInvoice: () => api.get('/invoices'),
  pembayaranCustomer: () => api.get('/penerimaan-finance/all'),
  tagihan: () => api.get('/invoices'),
  pemotonganReturnCustomer: () => api.get('/return-sales'),
  komisiSales: () => api.get('/invoices'),
};

// Dashboard API
export const dashboardAPI = {
  getStats: () => api.get('/dashboard/stats'),
  getRecentTransactions: () => api.get('/dashboard/recent-transactions'),
  getChartData: () => api.get('/dashboard/chart-data'),
};

// Export axios instance for custom requests
export { apiClient };

// Export default
export default api;
