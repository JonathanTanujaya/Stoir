import axios from 'axios';

// Base URL - fallback to localhost if env var not available
const BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

export const sparepartAPI = {
  /**
   * Get all spareparts
   */
  getAll: async () => {
    try {
      const response = await axios.get(`${BASE_URL}/spareparts`);
      return response.data;
    } catch (error) {
      console.error('Error fetching spareparts:', error);
      throw error;
    }
  },

  /**
   * Get sparepart by kode divisi and kode barang
   */
  getById: async (kodeDivisi, kodeBarang) => {
    try {
      const response = await axios.get(`${BASE_URL}/spareparts/${kodeDivisi}/${kodeBarang}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching sparepart:', error);
      throw error;
    }
  },

  /**
   * Create new sparepart
   */
  create: async (sparepartData) => {
    try {
      const response = await axios.post(`${BASE_URL}/spareparts`, sparepartData);
      return response.data;
    } catch (error) {
      console.error('Error creating sparepart:', error);
      throw error;
    }
  },

  /**
   * Update sparepart
   */
  update: async (kodeDivisi, kodeBarang, sparepartData) => {
    try {
      const response = await axios.put(`${BASE_URL}/spareparts/${kodeDivisi}/${kodeBarang}`, sparepartData);
      return response.data;
    } catch (error) {
      console.error('Error updating sparepart:', error);
      throw error;
    }
  },

  /**
   * Delete sparepart (soft delete)
   */
  delete: async (kodeDivisi, kodeBarang) => {
    try {
      const response = await axios.delete(`${BASE_URL}/spareparts/${kodeDivisi}/${kodeBarang}`);
      return response.data;
    } catch (error) {
      console.error('Error deleting sparepart:', error);
      throw error;
    }
  },

  /**
   * Search spareparts
   */
  search: async (searchParams) => {
    try {
      const response = await axios.get(`${BASE_URL}/spareparts/search`, {
        params: searchParams
      });
      return response.data;
    } catch (error) {
      console.error('Error searching spareparts:', error);
      throw error;
    }
  }
};

export default sparepartAPI;
