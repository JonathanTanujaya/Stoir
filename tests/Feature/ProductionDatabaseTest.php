<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\DBarang;

class ProductionDatabaseTest extends TestCase
{
    public function test_database_connection_works(): void
    {
        // Test koneksi database production
        $userCount = User::count();
        
        $this->assertIsInt($userCount);
        $this->assertGreaterThanOrEqual(0, $userCount);
    }
    
    public function test_d_barang_api_endpoint_structure(): void
    {
        // Test basic API endpoint access structure (without authentication for now)
        // This verifies route registration and basic controller structure
        
        $response = $this->get('/api/divisi/DIV01/barangs/TEST001/details');
        
        $statusCode = $response->status();
        $responseBody = $response->getContent();
        
        // Debug output
        echo "\nStatus Code: " . $statusCode;
        echo "\nResponse Body: " . substr($responseBody, 0, 200) . "...";
        
        // Route should be registered (not 404)
        $this->assertNotEquals(404, $statusCode, 'Route should be registered');
        
        // Should be one of the expected authentication-related codes
        $this->assertContains($statusCode, [302, 401, 403, 422], 
            "Expected authentication redirect or error, got: {$statusCode}");
    }
    
    public function test_d_barang_model_works(): void
    {
        // Test basic DBarang model functionality
        $dBarangCount = DBarang::count();
        $this->assertIsInt($dBarangCount);
        
        // Test dengan sample data jika ada
        if ($dBarangCount > 0) {
            $sampleDBarang = DBarang::first();
            $this->assertNotNull($sampleDBarang);
            $this->assertNotNull($sampleDBarang->kode_divisi);
            $this->assertNotNull($sampleDBarang->kode_barang);
        }
        
        $this->assertTrue(true); // Basic test passes
    }
}