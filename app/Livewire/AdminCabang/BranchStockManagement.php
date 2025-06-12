<?php

namespace App\Livewire\AdminCabang;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class BranchStockManagement extends Component
{
    use WithPagination;

    // Properti Umum & Tabel
    public $searchProduct = '';
    public $products;
    protected $paginationTheme = 'tailwind';

    // Properti untuk Fitur "Tambah Stok" (Transfer)
    public bool $showAddStockModal = false;
    public $productId;
    public $quantity;
    public $addStockNotes = '';
    public ?int $maxQuantity = null;

    // Properti untuk Fitur "Penyesuaian Stok"
    public bool $showAdjustmentModal = false;
    public ?Stock $selectedStock = null;
    public $adjustmentValue;
    public ?int $currentQuantity = null;
    public ?int $newQuantity = null;
    public string $adjustmentNotes = '';

    protected $rules = [
        'productId' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'addStockNotes' => 'nullable|string|max:255',
        'adjustmentValue' => 'required|integer|not_in:0',
        'adjustmentNotes' => 'required|string|max:255|min:5',
    ];

    protected $messages = [
        'quantity.max' => 'Jumlah permintaan melebihi stok di pusat.',
        'adjustmentValue.not_in' => 'Nilai penyesuaian tidak boleh nol.',
        'adjustmentNotes.required' => 'Catatan atau alasan penyesuaian wajib diisi.',
        'adjustmentNotes.min' => 'Catatan harus berisi minimal 5 karakter.',
    ];

    public function mount()
    {
        $this->products = Product::where('is_active', true)->orderBy('name')->get();
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

    // METODE UNTUK FITUR "TAMBAH STOK" (TRANSFER)
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
            $centralWarehouseBranchId = 1;
            $stock = Stock::where('product_id', $productId)->where('branch_id', $centralWarehouseBranchId)->first();
            $this->maxQuantity = $stock ? $stock->quantity : 0;
            $this->rules['quantity'] = "required|integer|min:1|max:{$this->maxQuantity}";
        } else {
            $this->maxQuantity = null;
            $this->rules['quantity'] = 'required|integer|min:1';
        }
    }

    public function addStock()
    {
        $this->validate(['productId', 'quantity', 'addStockNotes']);
        try {
            DB::transaction(function () {
                $requestingBranchId = Auth::user()->branch_id;
                $centralWarehouseBranchId = 1;
                if (!$requestingBranchId || $requestingBranchId == $centralWarehouseBranchId) {
                    throw new \Exception("Operasi tidak valid untuk cabang ini.");
                }
                $centralStock = Stock::where('product_id', $this->productId)->where('branch_id', $centralWarehouseBranchId)->lockForUpdate()->first();
                if (!$centralStock || $centralStock->quantity < $this->quantity) {
                    throw new \Exception("Stok di Gudang Pusat tidak mencukupi.");
                }
                $branchStock = Stock::firstOrCreate(['product_id' => $this->productId, 'branch_id' => $requestingBranchId], ['quantity' => 0]);
                $centralStock->decrement('quantity', $this->quantity);
                $this->createStockMovement($this->productId, $centralWarehouseBranchId, 'transfer_out', $this->quantity, "Transfer ke Cabang ID: {$requestingBranchId}. {$this->addStockNotes}");
                $branchStock->increment('quantity', $this->quantity);
                $this->createStockMovement($this->productId, $requestingBranchId, 'transfer_in', $this->quantity, "Transfer dari Gudang Pusat. {$this->addStockNotes}");
            });
            session()->flash('message', 'Stok berhasil ditransfer dari Gudang Pusat.');
            $this->closeModal();
            $this->dispatch('stockUpdated');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses transfer: ' . $e->getMessage());
        }
    }

    // METODE UNTUK FITUR "PENYESUAIAN STOK"
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
        $this->newQuantity = $this->currentQuantity + (int)$value;
    }

    public function updateStock()
    {
        $this->validate(['adjustmentValue', 'adjustmentNotes']);
        try {
            DB::transaction(function () {
                $oldQuantity = $this->selectedStock->quantity;
                $finalNewQuantity = $oldQuantity + (int) $this->adjustmentValue;
                if ($finalNewQuantity < 0) {
                    throw new \Exception("Kuantitas baru tidak boleh kurang dari 0.");
                }
                $this->selectedStock->update(['quantity' => $finalNewQuantity]);

                // --- PERBAIKAN LOGIKA TIPE ---
                $adjustmentType = (int) $this->adjustmentValue > 0 ? 'adjustment_increase' : 'adjustment_decrease';

                $this->createStockMovement(
                    $this->selectedStock->product_id,
                    Auth::user()->branch_id,
                    $adjustmentType, // Menggunakan tipe yang valid
                    (int) $this->adjustmentValue,
                    $this->adjustmentNotes,
                    $oldQuantity,
                    $finalNewQuantity
                );
                // --- BATAS PERBAIKAN ---
            });
            session()->flash('success', 'Stok berhasil disesuaikan.');
            $this->closeModal();
            $this->dispatch('stockUpdated');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal menyesuaikan stok: ' . $e->getMessage());
        }
    }

    // METODE BANTU (HELPERS) & UMUM
    private function createStockMovement(int $productId, int $branchId, string $type, int $quantityChange, string $notes, ?int $qtyBefore = null, ?int $qtyAfter = null)
    {
        StockMovement::create([
            'product_id' => $productId,
            'branch_id' => $branchId,
            'user_id' => Auth::id(),
            'type' => $type,
            'quantity' => in_array($type, ['transfer_in', 'transfer_out']) ? $quantityChange : null,
            'quantity_change' => in_array($type, ['adjustment_increase', 'adjustment_decrease']) ? $quantityChange : null,
            'quantity_before' => $qtyBefore,
            'quantity_after' => $qtyAfter,
            'notes' => $notes,
            'movement_date' => now(),
        ]);
    }

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
