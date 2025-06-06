<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => 'Sepatu Olahraga', 'description' => 'Berbagai jenis sepatu untuk aktivitas olahraga.']);
        Category::create(['name' => 'Pakaian Olahraga', 'description' => 'Pakaian nyaman untuk berolahraga.']);
        Category::create(['name' => 'Alat Fitness', 'description' => 'Peralatan untuk latihan kebugaran.']);
        Category::create(['name' => 'Aksesoris Olahraga', 'description' => 'Perlengkapan pendukung aktivitas olahraga.']);
        Category::create(['name' => 'Raket & Bola', 'description' => 'Peralatan untuk olahraga raket dan bola.']);
    }
}