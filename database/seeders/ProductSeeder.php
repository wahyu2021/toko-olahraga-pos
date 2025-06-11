<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID kategori dan supplier
        // Pastikan CategorySeeder dan SupplierSeeder dijalankan sebelumnya
        $katSepatu = Category::where('name', 'Sepatu Olahraga')->first();
        $katPakaian = Category::where('name', 'Pakaian Olahraga')->first();
        $katAlat = Category::where('name', 'Alat Fitness')->first();

        $supSJM = Supplier::where('name', 'PT. Sport Jaya Makmur')->first();
        $supAP = Supplier::where('name', 'CV. Atlet Prima')->first();

        if ($katSepatu && $supSJM) {
            Product::create([
                'name' => 'Sepatu Lari ZoomX',
                'sku' => 'SPTLR-ZMXX-001',
                'description' => 'Sepatu lari dengan teknologi ZoomX untuk kenyamanan maksimal.',
                'category_id' => $katSepatu->id,
                'supplier_id' => $supSJM->id,
                'purchase_price' => 750000,
                'selling_price' => 1200000,
                'low_stock_threshold' => 10,
                'is_active' => true,
            ]);
        }

        if ($katSepatu && $supAP) {
            Product::create([
                'name' => 'Sepatu Basket AirMax',
                'sku' => 'SPTBK-ARMX-002',
                'description' => 'Sepatu basket dengan bantalan AirMax untuk performa tinggi.',
                'category_id' => $katSepatu->id,
                'supplier_id' => $supAP->id,
                'purchase_price' => 900000,
                'selling_price' => 1500000,
                'low_stock_threshold' => 5,
                'is_active' => true,
            ]);
        }

        if ($katPakaian && $supSJM) {
            Product::create([
                'name' => 'Jersey Bola KeringCepat',
                'sku' => 'PKNBL-DRYF-001',
                'description' => 'Jersey bola bahan KeringCepat, ringan dan menyerap keringat.',
                'category_id' => $katPakaian->id,
                'supplier_id' => $supSJM->id,
                'purchase_price' => 150000,
                'selling_price' => 250000,
                'low_stock_threshold' => 20,
                'is_active' => true,
            ]);
        }

        if ($katAlat && $supAP) {
             Product::create([
                'name' => 'Dumbbell Set 10kg',
                'sku' => 'ALFIT-DMB-010',
                'description' => 'Set dumbbell neoprene 2x5kg untuk latihan beban.',
                'category_id' => $katAlat->id,
                'supplier_id' => $supAP->id,
                'purchase_price' => 200000,
                'selling_price' => 350000,
                'low_stock_threshold' => 15,
                'is_active' => true,
            ]);
        }
    }
}