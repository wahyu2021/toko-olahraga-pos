<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create([
            'name' => 'PT. Sport Jaya Makmur',
            'contact_person' => 'Bapak Anto',
            'phone' => '081234567890',
            'address' => 'Jl. Industri Raya No. 10, Jakarta Timur',
            'email' => 'anto.sjm@example.com'
        ]);

        Supplier::create([
            'name' => 'CV. Atlet Prima',
            'contact_person' => 'Ibu Siska',
            'phone' => '087654321098',
            'address' => 'Jl. Kopo No. 25, Bandung',
            'email' => 'siska.ap@example.com'
        ]);

        Supplier::create([
            'name' => 'Global Sports Apparel',
            'contact_person' => 'Mr. John Doe',
            'phone' => '08111222333',
            'address' => 'International Trade Center, Singapore',
            'email' => 'john.doe@globalsports.com'
        ]);
    }
}