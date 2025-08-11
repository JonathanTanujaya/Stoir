// Test API manually
import { categoriesAPI } from './services/api.js';

console.log('Testing API...');

const testAPI = async () => {
  try {
    console.log('Making API call...');
    const response = await categoriesAPI.getAll();
    console.log('Response:', response);
    console.log('Response data:', response.data);
  } catch (error) {
    console.error('Error:', error);
  }
};

testAPI();
