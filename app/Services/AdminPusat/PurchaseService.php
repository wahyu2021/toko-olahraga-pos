<?php

namespace App\Services\AdminPusat;

use App\Models\Purchase;
use App\Models\Stock;
use App\Models\Supplier; // Tambahkan ini
use Illuminate\Database\Eloquent\Collection; // Tambahkan ini
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    /**
     * Mengambil daftar supplier untuk filter.
     */
    public function getSuppliersForFilter(): Collection
    {
        return Supplier::orderBy('name')->get();
    }

    /**
     * Mengambil daftar pembelian dengan paginasi, filter, dan sorting.
     */
    public function getPaginatedPurchases(
        string $search = '',
        ?string $startDate = null,
        ?string $endDate = null,
        string $sortField = 'purchase_date',
        bool $sortAsc = false
    ): LengthAwarePaginator {
        // ... (Fungsi ini sudah benar, tidak perlu diubah)
        return Purchase::with(['supplier', 'user'])
            ->when($search, function ($query, $search) {
                $query->where('invoice_number', 'like', '%' . $search . '%')
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->when($startDate, function ($query, $startDate) {
                $query->whereDate('purchase_date', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->whereDate('purchase_date', '<=', $endDate);
            })
            ->orderBy($sortField, $sortAsc ? 'asc' : 'desc')
            ->paginate(15);
    }

    /**
     * Menghapus pembelian dan mengembalikan stok produk ke kondisi semula.
     */
    public function deletePurchaseAndAdjustStock(int $purchaseId): void
    {
        // ... (Fungsi ini sudah benar, tidak perlu diubah)
        DB::transaction(function () use ($purchaseId) {
            $purchase = Purchase::with('items.product')->findOrFail($purchaseId);

            foreach ($purchase->items as $item) {
                $stock = Stock::where('product_id', $item->product_id)
                    ->whereNull('branch_id')
                    ->first();

                if ($stock) {
                    $stock->decrement('quantity', $item->quantity);
                }
            }

            $purchase->delete();
        });
    }

    /**
     * Menyimpan data dari formulir pembelian (membuat atau memperbarui).
     */
    public function savePurchaseTransaction(array $purchaseData, array $formItems, ?int $purchaseId): Purchase
    {
        // ... (Fungsi ini sudah benar dari sebelumnya, tidak perlu diubah)
        return DB::transaction(function () use ($purchaseData, $formItems, $purchaseId) {
            $purchase = Purchase::updateOrCreate(
                ['id' => $purchaseId],
                $purchaseData
            );

            $currentItemIds = [];

            foreach ($formItems as $itemData) {
                $productStock = Stock::firstOrCreate(
                    ['product_id' => $itemData['product_id'], 'branch_id' => null],
                    ['quantity' => 0]
                );

                $item = $purchase->items()->updateOrCreate(
                    ['product_id' => $itemData['product_id']],
                    [
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                        'total' => $itemData['quantity'] * $itemData['price'],
                    ]
                );

                // Asumsi ada kolom 'central_stock_after_purchase' dari form
                if (isset($itemData['central_stock_after_purchase'])) {
                    $productStock->update(['quantity' => $itemData['central_stock_after_purchase']]);
                }

                $currentItemIds[] = $item->id;
            }

            $purchase->items()->whereNotIn('id', $currentItemIds)->delete();

            $purchase->update(['total_amount' => $purchase->items->sum('total')]);

            return $purchase;
        });
    }
}
