<?php

namespace App\Livewire\AdminPusat;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class StockManagement extends Component
{
    use WithPagination;

    // Properti untuk Tabel dan Filter
    public $searchProduct = '';
    public $filterBranch = '';
    public $branches = [];

    // Properti untuk Modal Penyesuaian
    public bool $showAdjustmentModal = false;
    public ?Stock $selectedStock = null;
    public $adjustmentValue;
    public ?int $currentQuantity = null;
    public ?int $newQuantity = null;
    public string $notes = '';

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        return [
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

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
    }

    public function updatingSearchProduct()
    {
        $this->resetPage();
    }

    public function updatingFilterBranch()
    {
        $this->resetPage();
    }

    public function selectStockForAdjustment(int $stockId)
    {
        $stock = Stock::with(['product', 'branch'])->findOrFail($stockId);
        $this->selectedStock = $stock;
        $this->currentQuantity = $stock->quantity;
        $this->newQuantity = $stock->quantity;
        $this->reset(['adjustmentValue', 'notes']);
        $this->resetErrorBag();
        $this->showAdjustmentModal = true;
    }

    public function updatedAdjustmentValue($value)
    {
        $this->newQuantity = $this->currentQuantity + (int)$value;
    }

    public function adjustStock()
    {
        $this->validate();
        if (!$this->selectedStock) {
            $this->dispatch('show-notification', message: 'Gagal, stok tidak ditemukan.', type: 'error');
            return;
        }

        try {
            $productName = $this->selectedStock->product->name;
            DB::transaction(function () {
                $oldQuantity = $this->selectedStock->quantity;
                $finalNewQuantity = $oldQuantity + (int) $this->adjustmentValue;

                if ($finalNewQuantity < 0) {
                    throw new \Exception("Kuantitas baru tidak boleh kurang dari 0.");
                }
                $this->selectedStock->update(['quantity' => $finalNewQuantity]);
                $adjustmentType = (int) $this->adjustmentValue > 0 ? 'adjustment_increase' : 'adjustment_decrease';
                StockMovement::create([
                    'product_id' => $this->selectedStock->product_id,
                    'branch_id' => $this->selectedStock->branch_id,
                    'user_id' => Auth::id(),
                    'type' => $adjustmentType,
                    'quantity_change' => (int) $this->adjustmentValue,
                    'quantity_before' => $oldQuantity,
                    'quantity_after' => $finalNewQuantity,
                    'notes' => $this->notes,
                    'movement_date' => now(),
                ]);
            });

            // --- PERUBAHAN UTAMA: MENGGANTI session() DENGAN dispatch() ---
            $this->dispatch('show-notification', message: "Stok untuk produk {$productName} berhasil disesuaikan.", type: 'success');
            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('show-notification', message: 'Gagal menyesuaikan stok: ' . $e->getMessage(), type: 'error');
        }
    }

    public function closeModal()
    {
        $this->showAdjustmentModal = false;
    }

    public function render()
    {
        // ... (Fungsi render tetap sama)
        $stocksQuery = Stock::with(['product.category', 'branch'])
            ->when($this->searchProduct, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchProduct . '%')
                      ->orWhere('sku', 'like', '%' . $this->searchProduct . '%');
                });
            })
            ->when($this->filterBranch, function ($query) {
                $query->where('branch_id', $this->filterBranch);
            });
        $stocksView = $stocksQuery->join('products', 'stocks.product_id', '=', 'products.id')
            ->join('branches', 'stocks.branch_id', '=', 'branches.id')
            ->select('stocks.*')
            ->orderBy('branches.name', 'asc')
            ->orderBy('products.name', 'asc')
            ->paginate(15);
        return view('livewire.admin-pusat.stock-management', ['stocksView' => $stocksView])->layout('layouts.admin-pusat');
    }
}