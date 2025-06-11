<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder bawaan Jetstream jika ada, atau User::factory()
        // \App\Models\User::factory(10)->create();
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            BranchSeeder::class,
            UserSeeder::class, // UserSeeder setelah BranchSeeder karena ada dependensi branch_id
            CategorySeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class, // ProductSeeder setelah CategorySeeder dan SupplierSeeder
            StockSeeder::class,   // StockSeeder setelah ProductSeeder dan BranchSeeder
        ]);
    }
}