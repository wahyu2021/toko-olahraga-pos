<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Branch;
use App\Models\StockMovement;
use App\Models\User; // Diperlukan jika StockMovement mencatat user_id

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ProductSeeder dan BranchSeeder sudah dijalankan
        $produkZoomX = Product::where('sku', 'SPTLR-ZMXX-001')->first();
        $produkAirMax = Product::where('sku', 'SPTBK-ARMX-002')->first();
        $produkJersey = Product::where('sku', 'PKNBL-DRYF-001')->first();
        $produkDumbbell = Product::where('sku', 'ALFIT-DMB-010')->first();

        $cabangPusat = Branch::where('name', 'Toko Olahraga Pusat')->first();
        $cabangJaksel = Branch::where('name', 'Cabang Jakarta Selatan')->first();
        $cabangBandung = Branch::where('name', 'Cabang Bandung Kota')->first();

        // Ambil user admin pusat untuk mencatat stok awal (opsional, sesuaikan dengan model StockMovement)
        $adminPusat = User::where('role', User::ROLE_ADMIN_PUSAT)->first();

        $initialStocks = [];

        if ($produkZoomX) {
            if ($cabangPusat) $initialStocks[] = ['product_id' => $produkZoomX->id, 'branch_id' => $cabangPusat->id, 'quantity' => 50, 'last_restock_date' => now()];
            if ($cabangJaksel) $initialStocks[] = ['product_id' => $produkZoomX->id, 'branch_id' => $cabangJaksel->id, 'quantity' => 30, 'last_restock_date' => now()];
        }
        if ($produkAirMax) {
            if ($cabangPusat) $initialStocks[] = ['product_id' => $produkAirMax->id, 'branch_id' => $cabangPusat->id, 'quantity' => 40, 'last_restock_date' => now()];
            if ($cabangBandung) $initialStocks[] = ['product_id' => $produkAirMax->id, 'branch_id' => $cabangBandung->id, 'quantity' => 25, 'last_restock_date' => now()];
        }
        if ($produkJersey) {
            if ($cabangJaksel) $initialStocks[] = ['product_id' => $produkJersey->id, 'branch_id' => $cabangJaksel->id, 'quantity' => 100, 'last_restock_date' => now()];
            if ($cabangBandung) $initialStocks[] = ['product_id' => $produkJersey->id, 'branch_id' => $cabangBandung->id, 'quantity' => 80, 'last_restock_date' => now()];
        }
        if ($produkDumbbell) {
            if ($cabangPusat) $initialStocks[] = ['product_id' => $produkDumbbell->id, 'branch_id' => $cabangPusat->id, 'quantity' => 30, 'last_restock_date' => now()];
        }


        foreach ($initialStocks as $stockData) {
            $stock = Stock::create($stockData);

            // Catat juga di StockMovement sebagai 'initial'
            StockMovement::create([
                'product_id' => $stock->product_id,
                'branch_id' => $stock->branch_id,
                'user_id' => $adminPusat ? $adminPusat->id : null, // User yang melakukan input awal
                'type' => 'initial',
                'quantity_change' => $stock->quantity,
                'quantity_before' => 0,
                'quantity_after' => $stock->quantity,
                'notes' => 'Stok awal sistem.',
                'movement_date' => now(),
            ]);
        }
    }
}
