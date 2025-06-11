<?php

namespace App\Livewire\AdminPusat;

use Livewire\Component;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PurchaseForm extends Component
{
    // Hapus $branches dari properti
    public $suppliers, $allProducts, $categories;

    public $supplier_id, $purchase_date;
    public $invoice_number, $expected_delivery_date, $notes;
    public $status = 'received';

    public $orderProducts = [];

    public function mount()
    {
        $this->suppliers = Supplier::orderBy('name')->get();
        $this->allProducts = Product::where('is_active', true)->orderBy('name')->get();
        $this->categories = Category::orderBy('name')->get();
        $this->purchase_date = now()->format('Y-m-d');
        $this->orderProducts = [$this->newProductRow()];
    }

    private function newProductRow(): array
    {
        return [
            'product_id' => '',
            'is_new_product' => false,
            'product_name' => '',
            'category_id' => '',
            'selling_price' => 0,
            'sku' => '',
            'description' => '',
            'quantity_ordered' => 1,
            'unit_price' => 0,
        ];
    }

    public function addProduct()
    { /* ... (tidak berubah) ... */
    }
    public function removeProduct($index)
    { /* ... (tidak berubah) ... */
    }
    public function updatedOrderProducts($value, $key)
    { /* ... (tidak berubah) ... */
    }

    public function savePurchase()
    {
        // Validasi data utama pembelian (tanpa branch_id)
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
        ]);

        try {
            DB::transaction(function () {
                $totalAmount = 0;

                // Loop validasi dan kalkulasi total (tidak berubah)
                foreach ($this->orderProducts as $index => $product) {
                    // ... (validasi per item) ...
                    $totalAmount += $product['quantity_ordered'] * $product['unit_price'];
                }

                // 1. Buat record di tabel purchases (tanpa branch_id)
                $purchase = Purchase::create([
                    'supplier_id' => $this->supplier_id,
                    // 'branch_id' => 1, // <-- DIHAPUS
                    'user_id' => Auth::id(),
                    'invoice_number' => $this->invoice_number,
                    'purchase_date' => $this->purchase_date,
                    'expected_delivery_date' => $this->expected_delivery_date,
                    'total_amount' => $totalAmount,
                    'status' => $this->status,
                    'notes' => $this->notes,
                ]);

                // 2. Loop untuk menyimpan item dan update stok
                foreach ($this->orderProducts as $productData) {
                    $productModel = null;

                    if ($productData['is_new_product']) {
                        // ... (logika create produk baru - tidak berubah) ...
                        $productModel = Product::create([
                            'name' => $productData['product_name'],
                            'category_id' => $productData['category_id'],
                            'supplier_id' => $this->supplier_id,
                            'selling_price' => $productData['selling_price'],
                            'purchase_price' => $productData['unit_price'],
                            'sku' => $productData['sku'] ?? null,
                            'description' => $productData['description'] ?? null,
                            'is_active' => true,
                        ]);
                    } else {
                        $productModel = Product::find($productData['product_id']);
                        $productModel->update(['purchase_price' => $productData['unit_price']]);
                    }

                    // Buat record di purchase_items (tidak berubah)
                    $purchase->items()->create([
                        'product_id' => $productModel->id,
                        'quantity_ordered' => $productData['quantity_ordered'],
                        'quantity_received' => $productData['quantity_ordered'], // Asumsi diterima penuh
                        'unit_price' => $productData['unit_price'],
                        'subtotal' => $productData['quantity_ordered'] * $productData['unit_price'],
                    ]);

                    // 3. Update stok di cabang PUSAT (ID = 1)
                    // Logika ini masih relevan jika tabel 'stocks' Anda masih memiliki 'branch_id'
                    // meskipun tabel 'purchases' sudah tidak punya.
                    // Ini mengasumsikan semua pembelian masuk ke satu gudang pusat.
                    Stock::updateOrCreate(
                        ['product_id' => $productModel->id, 'branch_id' => 1], // Selalu ke branch_id 1
                        ['quantity' => DB::raw('quantity + ' . $productData['quantity_ordered']), 'last_restock_date' => now()]
                    );
                }
            });

            session()->flash('message', 'Data pembelian berhasil disimpan. Produk baru (jika ada) dan stok telah diperbarui.');
            $this->resetForm();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->reset();
        $this->mount();
    }

    public function render()
    {
        return view('livewire.admin-pusat.purchase-form')
            ->layout('layouts.admin-pusat');
    }
}
