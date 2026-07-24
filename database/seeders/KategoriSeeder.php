<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        Kategori::create(['nama_kategori' => 'mudah']);
        Kategori::create(['nama_kategori' => 'normal']);
        Kategori::create(['nama_kategori' => 'sulit']);
    }
}