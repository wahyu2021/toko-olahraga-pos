<?php

namespace App\Livewire\AdminCabang;

use App\Models\Product;
use App\Models\Stock;
use App\Services\BranchStockService; // <-- Gunakan Service
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Exception;

class BranchStockManagement extends Component
{
    use WithPagination;

    // Properti Umum & Tabel
    public string $searchProduct = '';
    public $products;
    protected $paginationTheme = 'tailwind';

    // Properti untuk Fitur "Tambah Stok" (Transfer)
    public bool $showAddStockModal = false;
    public ?int $productId = null;
    public ?int $quantity = null;
    public string $addStockNotes = '';
    public ?int $maxQuantity = null;

    // Properti untuk Fitur "Penyesuaian Stok"
    public bool $showAdjustmentModal = false;
    public ?Stock $selectedStock = null;
    public ?int $adjustmentValue = null;
    public ?int $currentQuantity = null;
    public ?int $newQuantity = null;
    public string $adjustmentNotes = '';

    public function mount()
    {
        $this->products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku']);
    }

    public function render()
    {
        $branchId = Auth::user()->branch_id;
        $stocks = collect();

        if ($branchId) {
            $stocks = Stock::with(['product.category'])
                ->where('branch_id', $branchId)
                ->whereHas('product', function ($query) {
                    $query->where('name', 'like', '%' . $this->searchProduct . '%')
                        ->orWhere('sku', 'like', '%' . $this->searchProduct . '%');
                })
                ->orderByDesc('updated_at')
                ->paginate(15);
        }

        return view('livewire.admin-cabang.branch-stock-management', [
            'stocks' => $stocks,
        ])->layout('layouts.admin-cabang');
    }

    // --- METODE UNTUK FITUR "TAMBAH STOK" (TRANSFER) ---

    public function openAddStockModal()
    {
        $this->resetErrorBag();
        $this->reset('productId', 'quantity', 'addStockNotes', 'maxQuantity');
        $this->showAddStockModal = true;
    }

    public function updatedProductId($productId)
    {
        $this->quantity = null;
        if ($productId) {
            $stock = Stock::where('product_id', $productId)->where('branch_id', 1)->first(); // Asumsi ID 1 adalah pusat
            $this->maxQuantity = $stock ? $stock->quantity : 0;
        } else {
            $this->maxQuantity = null;
        }
    }

    public function addStock(BranchStockService $stockService)
    {
        $validatedData = $this->validate([
            'productId' => 'required|exists:products,id',
            'quantity' => "required|integer|min:1|max:{$this->maxQuantity}",
            'addStockNotes' => 'nullable|string|max:255',
        ], [
            'quantity.max' => 'Jumlah permintaan melebihi stok di pusat.',
        ]);

        try {
            $stockService->requestStockTransfer(
                $validatedData['productId'],
                $validatedData['quantity'],
                Auth::user()->branch_id,
                $validatedData['addStockNotes']
            );

            session()->flash('message', 'Stok berhasil ditransfer dari Gudang Pusat.');
            $this->closeModal();
            $this->dispatch('stockUpdated'); // Tetap berguna jika ada komponen lain yang mendengarkan

        } catch (Exception $e) {
            session()->flash('error', 'Gagal memproses transfer: ' . $e->getMessage());
        }
    }

    // --- METODE UNTUK FITUR "PENYESUAIAN STOK" ---

    public function selectStockForAdjustment(int $stockId)
    {
        $stock = Stock::where('id', $stockId)->where('branch_id', Auth::user()->branch_id)->firstOrFail();
        $this->selectedStock = $stock;
        $this->currentQuantity = $stock->quantity;
        $this->newQuantity = $stock->quantity;
        $this->resetErrorBag();
        $this->reset('adjustmentValue', 'adjustmentNotes');
        $this->showAdjustmentModal = true;
    }

    public function updatedAdjustmentValue($value)
    {
        // Pastikan nilai penyesuaian adalah integer
        $this->newQuantity = $this->currentQuantity + (int)$value;
    }

    public function updateStock(BranchStockService $stockService)
    {
        $validatedData = $this->validate([
            'adjustmentValue' => 'required|integer|not_in:0',
            'adjustmentNotes' => 'required|string|max:255|min:5',
        ], [
            'adjustmentValue.not_in' => 'Nilai penyesuaian tidak boleh nol.',
            'adjustmentNotes.required' => 'Catatan atau alasan penyesuaian wajib diisi.',
            'adjustmentNotes.min' => 'Catatan harus berisi minimal 5 karakter.',
        ]);

        try {
            $stockService->adjustStock(
                $this->selectedStock,
                $validatedData['adjustmentValue'],
                $validatedData['adjustmentNotes']
            );

            session()->flash('message', 'Stok berhasil disesuaikan.');
            $this->closeModal();
            $this->dispatch('stockUpdated');
        } catch (Exception $e) {
            session()->flash('error', 'Gagal menyesuaikan stok: ' . $e->getMessage());
        }
    }

    // --- METODE UMUM ---
    public function closeModal()
    {
        $this->showAddStockModal = false;
        $this->showAdjustmentModal = false;
    }

    public function updatingSearchProduct()
    {
        $this->resetPage();
    }
}
