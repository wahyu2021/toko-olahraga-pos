<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::create([
            'name' => 'Toko Olahraga Pusat',
            'address' => 'Jl. Merdeka No. 1, Kota Bahagia',
            'phone' => '021-123456'
        ]);

        Branch::create([
            'name' => 'Cabang Jakarta Selatan',
            'address' => 'Jl. Sudirman Kav. 20, Jakarta Selatan',
            'phone' => '021-654321'
        ]);

        Branch::create([
            'name' => 'Cabang Bandung Kota',
            'address' => 'Jl. Asia Afrika No. 101, Bandung',
            'phone' => '022-789012'
        ]);
    }
}