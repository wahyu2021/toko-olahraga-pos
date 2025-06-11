<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID cabang untuk referensi
        // Pastikan BranchSeeder dijalankan sebelum UserSeeder
        $cabangJaksel = Branch::where('name', 'Cabang Jakarta Selatan')->first();
        $cabangBandung = Branch::where('name', 'Cabang Bandung Kota')->first();

        // Admin Pusat
        User::create([
            'name' => 'Admin Pusat',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN_PUSAT,
            'branch_id' => null, // Admin Pusat tidak terikat cabang
            'email_verified_at' => now(),
        ]);

        // Manajer Pusat
        User::create([
            'name' => 'Manajer Pusat',
            'email' => 'manajerpusat@example.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_MANAJER_PUSAT,
            'branch_id' => null, // Manajer Pusat tidak terikat cabang
            'email_verified_at' => now(),
        ]);

        if ($cabangJaksel) {
            // Admin Cabang Jakarta Selatan
            User::create([
                'name' => 'Admin Cabang Jaksel',
                'email' => 'adminjaksel@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN_CABANG,
                'branch_id' => $cabangJaksel->id,
                'email_verified_at' => now(),
            ]);

            // Manajer Cabang Jakarta Selatan
            User::create([
                'name' => 'Manajer Cabang Jaksel',
                'email' => 'manajerjaksel@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_MANAJER_CABANG,
                'branch_id' => $cabangJaksel->id,
                'email_verified_at' => now(),
            ]);

            // Kasir Cabang Jakarta Selatan
            User::create([
                'name' => 'Kasir Jaksel 1',
                'email' => 'kasirjaksel1@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_KASIR,
                'branch_id' => $cabangJaksel->id,
                'email_verified_at' => now(),
            ]);
        }

        if ($cabangBandung) {
            // Manajer Cabang Bandung
            User::create([
                'name' => 'Manajer Cabang Bandung',
                'email' => 'manajerbandung@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_MANAJER_CABANG,
                'branch_id' => $cabangBandung->id,
                'email_verified_at' => now(),
            ]);

            // Kasir Cabang Bandung
            User::create([
                'name' => 'Kasir Bandung 1',
                'email' => 'kasirbandung1@example.com',
                'password' => Hash::make('password'),
                'role' => User::ROLE_KASIR,
                'branch_id' => $cabangBandung->id,
                'email_verified_at' => now(),
            ]);
        }
    }
}