#!/usr/bin/env node

/**
 * StockFlow API Testing Script
 * Phase 4: Comprehensive API Endpoint Testing
 */

const axios = require('axios');

const API_BASE_URL = 'http://127.0.0.1:8000/api';

// Test configuration
const testConfig = {
  timeout: 10000,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  }
};

// Expected response format
const expectedResponseFormat = {
  success: 'boolean',
  data: 'any',
  message: 'string',
  timestamp: 'string',
  status_code: 'number'
};

// Test results collector
const testResults = {
  passed: 0,
  failed: 0,
  errors: []
};

// Utility functions
const log = (message, type = 'info') => {
  const timestamp = new Date().toISOString();
  const colors = {
    info: '\x1b[36m',    // Cyan
    success: '\x1b[32m', // Green
    error: '\x1b[31m',   // Red
    warning: '\x1b[33m', // Yellow
    reset: '\x1b[0m'     // Reset
  };
  
  console.log(`${colors[type]}[${timestamp}] ${message}${colors.reset}`);
};

const validateResponseFormat = (response, endpoint) => {
  try {
    const data = response.data;
    
    // Check if response has success property
    if (typeof data.success !== 'boolean') {
      throw new Error(`Missing or invalid 'success' property. Expected boolean, got ${typeof data.success}`);
    }
    
    // Check if response has message property
    if (typeof data.message !== 'string') {
      throw new Error(`Missing or invalid 'message' property. Expected string, got ${typeof data.message}`);
    }
    
    // Check if response has timestamp property
    if (typeof data.timestamp !== 'string') {
      throw new Error(`Missing or invalid 'timestamp' property. Expected string, got ${typeof data.timestamp}`);
    }
    
    // Check if response has status_code property
    if (typeof data.status_code !== 'number') {
      throw new Error(`Missing or invalid 'status_code' property. Expected number, got ${typeof data.status_code}`);
    }
    
    // For successful responses, check data property
    if (data.success && data.data === undefined) {
      throw new Error(`Missing 'data' property for successful response`);
    }
    
    return true;
  } catch (error) {
    log(`❌ Response format validation failed for ${endpoint}: ${error.message}`, 'error');
    return false;
  }
};

// Test individual endpoint
const testEndpoint = async (method, endpoint, data = null, expectedStatus = 200) => {
  try {
    log(`🧪 Testing ${method.toUpperCase()} ${endpoint}`, 'info');
    
    const config = {
      method: method.toLowerCase(),
      url: `${API_BASE_URL}${endpoint}`,
      ...testConfig
    };
    
    if (data && (method.toLowerCase() === 'post' || method.toLowerCase() === 'put')) {
      config.data = data;
    }
    
    const response = await axios(config);
    
    // Validate status code
    if (response.status !== expectedStatus) {
      throw new Error(`Expected status ${expectedStatus}, got ${response.status}`);
    }
    
    // Validate response format
    if (!validateResponseFormat(response, endpoint)) {
      throw new Error('Response format validation failed');
    }
    
    log(`✅ PASS: ${method.toUpperCase()} ${endpoint} (${response.status})`, 'success');
    testResults.passed++;
    
    return {
      success: true,
      status: response.status,
      data: response.data
    };
    
  } catch (error) {
    const errorMsg = `FAIL: ${method.toUpperCase()} ${endpoint} - ${error.message}`;
    log(`❌ ${errorMsg}`, 'error');
    
    testResults.failed++;
    testResults.errors.push({
      endpoint: `${method.toUpperCase()} ${endpoint}`,
      error: error.message,
      status: error.response?.status || 'Network Error'
    });
    
    return {
      success: false,
      error: error.message,
      status: error.response?.status || 0
    };
  }
};

// Main test suite
const runAPITests = async () => {
  log('🚀 Starting StockFlow API Testing Suite', 'info');
  log('=' * 60, 'info');
  
  // Test 1: Authentication endpoints
  log('📋 Testing Authentication Endpoints', 'warning');
  await testEndpoint('GET', '/auth/me', null, 200);
  
  // Test 2: Frontend-friendly endpoints
  log('📋 Testing Frontend-Friendly Endpoints', 'warning');
  await testEndpoint('GET', '/categories');
  await testEndpoint('GET', '/customers');
  await testEndpoint('GET', '/suppliers');
  await testEndpoint('GET', '/barang');
  await testEndpoint('GET', '/invoices');
  
  // Test 3: Master data endpoints (composite keys)
  log('📋 Testing Master Data Endpoints', 'warning');
  await testEndpoint('GET', '/master-suppliers');
  await testEndpoint('GET', '/kategoris');
  await testEndpoint('GET', '/areas');
  await testEndpoint('GET', '/coas');
  await testEndpoint('GET', '/dokumens');
  
  // Test 4: Transaction endpoints
  log('📋 Testing Transaction Endpoints', 'warning');
  await testEndpoint('GET', '/purchases');
  await testEndpoint('GET', '/part-penerimaan');
  await testEndpoint('GET', '/penerimaan-finance');
  await testEndpoint('GET', '/journals');
  await testEndpoint('GET', '/kartu-stok');
  
  // Test 5: Specialized endpoints
  log('📋 Testing Specialized Endpoints', 'warning');
  await testEndpoint('GET', '/companies');
  await testEndpoint('GET', '/banks');
  await testEndpoint('GET', '/opnames');
  await testEndpoint('GET', '/stok-claims');
  await testEndpoint('GET', '/tmp-print-invoices');
  
  // Test 6: View endpoints
  log('📋 Testing View Endpoints', 'warning');
  await testEndpoint('GET', '/v-cust-retur');
  await testEndpoint('GET', '/v-return-sales-detail');
  
  // Test results summary
  log('=' * 60, 'info');
  log('📊 TEST RESULTS SUMMARY', 'warning');
  log(`✅ Passed: ${testResults.passed}`, 'success');
  log(`❌ Failed: ${testResults.failed}`, 'error');
  log(`📈 Success Rate: ${((testResults.passed / (testResults.passed + testResults.failed)) * 100).toFixed(2)}%`, 'info');
  
  if (testResults.errors.length > 0) {
    log('🔍 Error Details:', 'warning');
    testResults.errors.forEach((error, index) => {
      log(`${index + 1}. ${error.endpoint}: ${error.error} (Status: ${error.status})`, 'error');
    });
  }
  
  log('✨ API Testing Complete!', 'success');
};

// Run the tests
runAPITests().catch(error => {
  log(`💥 Test suite failed: ${error.message}`, 'error');
  process.exit(1);
});
