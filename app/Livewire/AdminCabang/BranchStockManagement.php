<?php

namespace App\Livewire\AdminCabang;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class BranchStockManagement extends Component
{
    use WithPagination;

    // Properti untuk Tabel dan Filter
    public $searchProduct = '';
    protected $paginationTheme = 'tailwind';

    // Properti untuk Modal Penyesuaian
    public $showAdjustmentModal = false;
    public $selectedStock;
    public $adjustmentValue; // Nilai penambah/pengurang
    public $currentQuantity;
    public $newQuantity;
    public $notes = '';

    protected function rules()
    {
        return [
            // Ganti nama properti dari 'adjustment' ke 'adjustmentValue' agar tidak bentrok
            'adjustmentValue' => 'required|integer|not_in:0',
            'notes' => 'required|string|max:255|min:5',
        ];
    }

    protected $messages = [
        'adjustmentValue.required' => 'Nilai penyesuaian harus diisi.',
        'adjustmentValue.integer' => 'Nilai harus berupa angka bulat.',
        'adjustmentValue.not_in' => 'Nilai penyesuaian tidak boleh nol.',
        'notes.required' => 'Catatan atau alasan penyesuaian wajib diisi.',
        'notes.min' => 'Catatan harus berisi minimal 5 karakter.',
    ];

    public function updatingSearchProduct()
    {
        $this->resetPage();
    }

    public function selectStockForAdjustment(int $stockId)
    {
        $branchId = Auth::user()->branch_id;
        // Ambil stok dan pastikan milik cabang yang benar
        $stock = Stock::where('id', $stockId)->where('branch_id', $branchId)->firstOrFail();

        $this->selectedStock = $stock;
        $this->currentQuantity = $stock->quantity;
        $this->newQuantity = $stock->quantity;
        $this->adjustmentValue = null;
        $this->notes = '';
        $this->resetErrorBag();
        $this->showAdjustmentModal = true;
    }

    // Fungsi ini akan berjalan setiap kali nilai 'adjustmentValue' diperbarui
    public function updatedAdjustmentValue($value)
    {
        $adjustment = (int) $value;
        $this->newQuantity = $this->currentQuantity + $adjustment;
    }

    public function updateStock()
    {
        $this->validate();

        if (!$this->selectedStock) {
            session()->flash('error', 'Gagal memproses, stok tidak ditemukan.');
            return;
        }

        DB::transaction(function () {
            $oldQuantity = $this->selectedStock->quantity;
            $finalNewQuantity = $oldQuantity + (int) $this->adjustmentValue;

            if ($finalNewQuantity < 0) {
                // Mencegah stok menjadi negatif dari penyesuaian
                session()->flash('error', 'Penyesuaian gagal. Kuantitas baru tidak boleh kurang dari 0.');
                return;
            }

            $this->selectedStock->update(['quantity' => $finalNewQuantity]);

            StockMovement::create([
                'product_id' => $this->selectedStock->product_id,
                'branch_id' => Auth::user()->branch_id,
                'user_id' => Auth::id(),
                'type' => 'adjustment', // Tipe bisa dibuat lebih spesifik jika perlu
                'quantity_change' => (int) $this->adjustmentValue,
                'quantity_before' => $oldQuantity,
                'quantity_after' => $finalNewQuantity,
                'notes' => $this->notes,
                'movement_date' => now(),
            ]);

            session()->flash('success', 'Stok untuk produk ' . $this->selectedStock->product->name . ' berhasil disesuaikan.');
            $this->showAdjustmentModal = false;
        });
    }

    public function closeModal()
    {
        $this->showAdjustmentModal = false;
    }

    public function render()
    {
        $branchId = Auth::user()->branch_id;
        $stocks = collect(); // Default collection kosong

        if ($branchId) {
            $stocks = Stock::with(['product.category'])
                ->where('branch_id', $branchId) // **KUNCI UTAMA: FILTER OTOMATIS**
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
}
