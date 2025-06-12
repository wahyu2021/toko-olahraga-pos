<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class BranchStockService
{
    /**
     * Memproses permintaan transfer stok dari gudang pusat ke cabang.
     *
     * @throws \Exception
     */
    public function requestStockTransfer(int $productId, int $quantity, int $requestingBranchId, ?string $notes): void
    {
        DB::transaction(function () use ($productId, $quantity, $requestingBranchId, $notes) {
            $centralWarehouseBranchId = 1; // ID Gudang Pusat

            if ($requestingBranchId === $centralWarehouseBranchId) {
                throw new Exception("Gudang Pusat tidak dapat melakukan transfer ke dirinya sendiri.");
            }

            // Kunci baris stok pusat untuk mencegah race condition
            $centralStock = Stock::where('product_id', $productId)
                ->where('branch_id', $centralWarehouseBranchId)
                ->lockForUpdate()
                ->first();

            if (!$centralStock || $centralStock->quantity < $quantity) {
                throw new Exception("Stok di Gudang Pusat tidak mencukupi.");
            }

            $centralStockBefore = $centralStock->quantity;
            $centralStockAfter = $centralStockBefore - $quantity;

            // Kurangi stok pusat
            $centralStock->decrement('quantity', $quantity);

            // Buat catatan pergerakan dengan data lengkap
            $this->createStockMovement(
                $productId,
                $centralWarehouseBranchId,
                'transfer_out',
                -$quantity,
                "Transfer ke Cabang ID: {$requestingBranchId}. {$notes}",
                $centralStockBefore, // <-- Kirim nilai sebelum
                $centralStockAfter   // <-- Kirim nilai sesudah
            );

            // 2. Catat kuantitas SEBELUM & SESUDAH untuk Cabang
            $branchStock = Stock::firstOrCreate(
                ['product_id' => $productId, 'branch_id' => $requestingBranchId],
                ['quantity' => 0]
            );
            $branchStockBefore = $branchStock->quantity;
            $branchStockAfter = $branchStockBefore + $quantity;

            // Tambah stok cabang
            $branchStock->increment('quantity', $quantity);

            // Buat catatan pergerakan dengan data lengkap
            $this->createStockMovement(
                $productId,
                $requestingBranchId,
                'transfer_in',
                $quantity,
                "Transfer dari Gudang Pusat. {$notes}",
                $branchStockBefore, // <-- Kirim nilai sebelum
                $branchStockAfter   // <-- Kirim nilai sesudah
            );
        });
    }

    /**
     * Memproses penyesuaian stok di sebuah cabang.
     *
     * @throws \Exception
     */
    public function adjustStock(Stock $stock, int $adjustmentValue, string $notes): void
    {
        DB::transaction(function () use ($stock, $adjustmentValue, $notes) {
            $oldQuantity = $stock->quantity;
            $newQuantity = $oldQuantity + $adjustmentValue;

            if ($newQuantity < 0) {
                throw new Exception("Kuantitas akhir tidak boleh kurang dari nol.");
            }

            $stock->update(['quantity' => $newQuantity]);

            $adjustmentType = $adjustmentValue > 0 ? 'adjustment_increase' : 'adjustment_decrease';

            $this->createStockMovement(
                $stock->product_id,
                $stock->branch_id,
                $adjustmentType,
                $adjustmentValue,
                $notes,
                $oldQuantity,
                $newQuantity
            );
        });
    }

    /**
     * Helper privat untuk mencatat semua pergerakan stok.
     */
    private function createStockMovement(int $productId, int $branchId, string $type, int $quantity_change, string $notes, ?int $qtyBefore = null, ?int $qtyAfter = null): void
    {
        StockMovement::create([
            'product_id' => $productId,
            'branch_id' => $branchId,
            'user_id' => Auth::id(),
            'type' => $type,
            'quantity_change' => $quantity_change,
            'quantity_before' => $qtyBefore,
            'quantity_after' => $qtyAfter,
            'notes' => $notes,
            'movement_date' => now(),
        ]);
    }
}
