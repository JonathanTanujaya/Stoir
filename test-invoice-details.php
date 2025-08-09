<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Testing invoice-related tables...\n";
    
    // Check if tables exist
    $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'dbo' AND table_name LIKE '%invoice%'");
    
    echo "Found invoice tables:\n";
    foreach ($tables as $table) {
        echo "- " . $table->table_name . "\n";
    }
    
    // Test invoice table first
    echo "\nTesting dbo.invoice table:\n";
    $invoices = DB::table('dbo.invoice')->limit(3)->get();
    echo "Invoice records: " . count($invoices) . "\n";
    
    // Test invoicedetail table
    echo "\nTesting dbo.invoicedetail table:\n";
    $details = DB::table('dbo.invoicedetail')->limit(3)->get();
    echo "Invoice detail records: " . count($details) . "\n";
    
    if (count($details) > 0) {
        echo "Sample invoice detail:\n";
        print_r($details[0]);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
