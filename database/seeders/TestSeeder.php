<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the main test division
        DB::table('m_divisi')->insertOrIgnore([
            'kode_divisi' => 'TEST',
            'nama_divisi' => 'Test Divisi API'
        ]);

        // Create the main test category
        DB::table('m_kategori')->insertOrIgnore([
            'kode_divisi' => 'TEST',
            'kode_kategori' => 'KAT001',
            'kategori' => 'Test Kategori API'
        ]);

        // Create the main test area
        DB::table('m_area')->insertOrIgnore([
            'kode_divisi' => 'TEST',
            'kode_area' => 'AR001',
            'area' => 'Test Area API'
        ]);

        // Create the main test sales
        DB::table('m_sales')->insertOrIgnore([
            'kode_divisi' => 'TEST',
            'kode_sales' => 'SLS001',
            'nama_sales' => 'Test Sales API'
        ]);
    }
}
