<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat admin default
        User::create([
            'name' => 'Admin Edukasi',
            'email' => 'admin@edukasi.local',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status' => 'verified',
        ]);

        // Buat guru sample untuk testing
        User::create([
            'name' => 'Guru Test',
            'email' => 'guru@edukasi.local',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'status' => 'pending',
        ]);
    }
}

