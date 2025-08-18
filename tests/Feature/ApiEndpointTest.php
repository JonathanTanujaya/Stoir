<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiEndpointTest extends TestCase
{
    /**
     * Test API response format standardization
     * Phase 4: Comprehensive API Testing
     */
    
    /**
     * Test that categories endpoint returns standardized format
     */
    public function test_categories_endpoint_format()
    {
        $response = $this->get('/api/categories');
        
        $response->assertStatus(200);
        
        // Test standardized response format
        $response->assertJsonStructure([
            'success',
            'data',
            'message',
            'timestamp',
            'status_code'
        ]);
        
        // Validate specific format requirements
        $data = $response->json();
        $this->assertIsBool($data['success']);
        $this->assertIsString($data['message']);
        $this->assertIsString($data['timestamp']);
        $this->assertIsInt($data['status_code']);
        $this->assertEquals(200, $data['status_code']);
    }
    
    /**
     * Test that customers endpoint returns standardized format
     */
    public function test_customers_endpoint_format()
    {
        $response = $this->get('/api/customers');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'message',
            'timestamp',
            'status_code'
        ]);
    }
    
    /**
     * Test that suppliers endpoint returns standardized format
     */
    public function test_suppliers_endpoint_format()
    {
        $response = $this->get('/api/suppliers');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'message',
            'timestamp',
            'status_code'
        ]);
    }
    
    /**
     * Test that barang endpoint returns standardized format
     */
    public function test_barang_endpoint_format()
    {
        $response = $this->get('/api/barang');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'message',
            'timestamp',
            'status_code'
        ]);
    }
    
    /**
     * Test that master data endpoints work with composite keys
     */
    public function test_master_suppliers_endpoint_format()
    {
        $response = $this->get('/api/master-suppliers');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'message',
            'timestamp',
            'status_code'
        ]);
    }
    
    /**
     * Test that authentication endpoints work
     */
    public function test_auth_me_endpoint_format()
    {
        // Without authentication, should still return standardized error format
        $response = $this->get('/api/auth/me');
        
        // May return 401 or 200 depending on setup
        $this->assertTrue(in_array($response->status(), [200, 401]));
        
        $response->assertJsonStructure([
            'success',
            'message',
            'timestamp',
            'status_code'
        ]);
    }
    
    /**
     * Test that error responses are standardized
     */
    public function test_error_response_format()
    {
        // Test with invalid endpoint
        $response = $this->get('/api/invalid-endpoint');
        
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'success',
            'error',
            'message',
            'timestamp',
            'status_code'
        ]);
        
        $data = $response->json();
        $this->assertFalse($data['success']);
        $this->assertEquals(404, $data['status_code']);
    }
    
    /**
     * Test multiple endpoints for consistency
     */
    public function test_endpoint_consistency()
    {
        $endpoints = [
            '/api/categories',
            '/api/customers',
            '/api/suppliers',
            '/api/barang',
            '/api/companies',
            '/api/areas',
            '/api/banks'
        ];
        
        foreach ($endpoints as $endpoint) {
            $response = $this->get($endpoint);
            
            // Should return 200 and have standardized format
            $response->assertStatus(200);
            $response->assertJsonStructure([
                'success',
                'data',
                'message',
                'timestamp',
                'status_code'
            ]);
            
            $data = $response->json();
            $this->assertTrue($data['success'], "Endpoint {$endpoint} should return success=true");
            $this->assertEquals(200, $data['status_code'], "Endpoint {$endpoint} should have status_code=200");
        }
    }
    
    /**
     * Test POST endpoint format (using categories as example)
     */
    public function test_post_endpoint_format()
    {
        $testData = [
            'nama_kategori' => 'Test Category',
            'keterangan' => 'Test Description'
        ];
        
        $response = $this->post('/api/categories', $testData);
        
        // May return 201 or validation error, both should be standardized
        $this->assertTrue(in_array($response->status(), [200, 201, 422]));
        
        $response->assertJsonStructure([
            'success',
            'message',
            'timestamp',
            'status_code'
        ]);
    }
    
    /**
     * Performance test - measure response times
     */
    public function test_api_performance()
    {
        $startTime = microtime(true);
        
        $response = $this->get('/api/categories');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds
        
        $response->assertStatus(200);
        
        // Assert response time is under 1 second (1000ms)
        $this->assertLessThan(1000, $responseTime, 'API response should be under 1 second');
        
        echo "\n📊 Categories endpoint response time: " . round($responseTime, 2) . "ms\n";
    }
}
