<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterUser;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if not exists
        MasterUser::firstOrCreate(
            [
                'kodedivisi' => '01',
                'username' => 'admin'
            ],
            [
                'password' => 'admin123', // Simple password for legacy system
                'nama' => 'Administrator'
            ]
        );

        // Create test user for division 02
        MasterUser::firstOrCreate(
            [
                'kodedivisi' => '02', 
                'username' => 'user01'
            ],
            [
                'password' => 'user123', // Simple password for legacy system
                'nama' => 'Test User'
            ]
        );

        $this->command->info('Admin users created successfully!');
    }
}
