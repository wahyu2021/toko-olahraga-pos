<?php

namespace App\Livewire\AdminPusat;

use Livewire\Component;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Branch;
use App\Models\StockMovement;
use App\Models\User; // Untuk mencatat user_id di stock_movements
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class StockManagement extends Component
{
    use WithPagination;

    public $searchProduct = '';
    public $filterBranch = '';
    public $sortField = 'products.name'; // Default sort by product name
    public $sortAsc = true;

    // Properti untuk modal
    public $stock_id; // Untuk identifikasi stok yang diedit (opsional, bisa berdasarkan product_id & branch_id)
    public $product_id;
    public $branch_id;
    public $quantity;
    public $notes;
    public $movement_type = 'adjustment_increase'; // 'initial', 'adjustment_increase', 'adjustment_decrease'

    public $products = [];
    public $branches = [];
    public $currentStockQuantity = 0; // Untuk menampilkan stok saat ini di modal

    public $showStockModal = false;
    // public $showDeleteModal = false; // Kita tidak akan implementasi delete stok langsung

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'branch_id' => 'required|exists:branches,id',
            'quantity' => 'required|integer|min:0', // Untuk set langsung kuantitas baru
            'notes' => 'required|string|max:255',
            'movement_type' => ['required', Rule::in(['initial', 'adjustment_increase', 'adjustment_decrease', 'set_quantity'])],
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'Produk wajib dipilih.',
            'branch_id.required' => 'Cabang wajib dipilih.',
            'quantity.required' => 'Kuantitas wajib diisi.',
            'quantity.integer' => 'Kuantitas harus berupa angka.',
            'quantity.min' => 'Kuantitas tidak boleh kurang dari 0.',
            'notes.required' => 'Catatan/alasan penyesuaian wajib diisi.',
        ];
    }

    public function mount()
    {
        $this->products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku']);
        $this->branches = Branch::orderBy('name')->get(['id', 'name']);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }
        $this->sortField = $field;
    }

    public function updatingSearchProduct()
    {
        $this->resetPage();
    }

    public function updatingFilterBranch()
    {
        $this->resetPage();
    }

    // Fungsi untuk mengambil stok saat ini ketika produk dan cabang dipilih di modal
    public function updatedProductId($value)
    {
        $this->fetchCurrentStock();
    }
    public function updatedBranchId($value)
    {
        $this->fetchCurrentStock();
    }

    private function fetchCurrentStock()
    {
        if ($this->product_id && $this->branch_id) {
            $stock = Stock::where('product_id', $this->product_id)
                          ->where('branch_id', $this->branch_id)
                          ->first();
            $this->currentStockQuantity = $stock ? $stock->quantity : 0;
        } else {
            $this->currentStockQuantity = 0;
        }
    }


    public function openStockModal($productId = null, $branchId = null)
    {
        $this->resetForm();
        if ($productId && $branchId) {
            $stock = Stock::where('product_id', $productId)->where('branch_id', $branchId)->first();
            if ($stock) {
                $this->stock_id = $stock->id;
                $this->product_id = $stock->product_id;
                $this->branch_id = $stock->branch_id;
                $this->quantity = $stock->quantity; // Isi dengan kuantitas saat ini untuk diedit
                $this->currentStockQuantity = $stock->quantity;
                $this->movement_type = 'set_quantity'; // Default untuk edit
            } else {
                // Jika stok belum ada, siapkan untuk input awal
                $this->product_id = $productId;
                $this->branch_id = $branchId;
                $this->movement_type = 'initial';
                $this->currentStockQuantity = 0;
            }
        } else {
            $this->movement_type = 'initial'; // Default untuk penambahan baru
        }
        $this->showStockModal = true;
    }

    public function saveStock()
    {
        $this->validate();

        $stock = Stock::firstOrNew([
            'product_id' => $this->product_id,
            'branch_id' => $this->branch_id,
        ]);

        $quantityBefore = $stock->quantity ?? 0;
        $newQuantity = (int)$this->quantity; // Kuantitas baru yang diinput user
        $quantityChange = $newQuantity - $quantityBefore;

        // Tentukan tipe pergerakan berdasarkan perubahan
        $actualMovementType = $this->movement_type; // Default
        if (!$stock->exists) { // Jika stok baru dibuat
            $actualMovementType = 'initial';
            $quantityChange = $newQuantity; // Perubahan adalah kuantitas baru itu sendiri
        } elseif ($this->movement_type === 'set_quantity') { // Jika user set kuantitas absolut
             if ($newQuantity > $quantityBefore) {
                $actualMovementType = 'adjustment_increase';
            } elseif ($newQuantity < $quantityBefore) {
                $actualMovementType = 'adjustment_decrease';
            } else {
                // Tidak ada perubahan, mungkin hanya update notes atau tanggal?
                // Untuk saat ini, jika kuantitas sama, kita skip movement jika tidak ada perubahan lain.
                // Atau bisa tetap catat sebagai 'adjustment' dengan change = 0 jika notes penting.
                // Kita anggap jika set_quantity sama dengan existing, tidak ada movement berarti.
                if ($newQuantity == $quantityBefore) {
                     $this->showStockModal = false;
                     session()->flash('message', 'Kuantitas stok tidak berubah.');
                     $this->resetForm();
                     return;
                }
            }
        }
        // Jika movement_type sudah 'adjustment_increase' atau 'adjustment_decrease', quantityChange sudah benar.

        $stock->quantity = $newQuantity;
        if ($quantityChange > 0 || !$stock->exists) { // Hanya update last_restock_date jika stok bertambah atau baru
            $stock->last_restock_date = now();
        }
        $stock->save();

        StockMovement::create([
            'product_id' => $stock->product_id,
            'branch_id' => $stock->branch_id,
            'user_id' => Auth::id(),
            'type' => $actualMovementType,
            'quantity_change' => $quantityChange,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $stock->quantity,
            'notes' => $this->notes,
            'movement_date' => now(),
            'referenceable_id' => null, // Karena ini penyesuaian manual
            'referenceable_type' => null,
        ]);

        $this->showStockModal = false;
        session()->flash('message', 'Stok berhasil diperbarui/disesuaikan.');
        $this->resetForm();
    }


    private function resetForm()
    {
        $this->stock_id = null;
        $this->product_id = null;
        $this->branch_id = null;
        $this->quantity = '';
        $this->notes = '';
        $this->movement_type = 'adjustment_increase';
        $this->currentStockQuantity = 0;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->showStockModal = false;
        $this->resetForm();
    }

    public function render()
    {
        $stocksQuery = Stock::with(['product', 'branch'])
            ->join('products', 'stocks.product_id', '=', 'products.id') // Join untuk sorting by product name/sku
            ->join('branches', 'stocks.branch_id', '=', 'branches.id')   // Join untuk sorting by branch name
            ->select('stocks.*'); // Pastikan memilih semua kolom dari stocks

        if (!empty($this->searchProduct)) {
            $stocksQuery->whereHas('product', function ($query) {
                $query->where('name', 'like', '%' . $this->searchProduct . '%')
                    ->orWhere('sku', 'like', '%' . $this->searchProduct . '%');
            });
        }

        if (!empty($this->filterBranch)) {
            $stocksQuery->where('stocks.branch_id', $this->filterBranch);
        }
        
        // Apply sorting
        // Pisahkan logic sorting untuk field dari tabel join
        if (strpos($this->sortField, '.') !== false) {
            [$relation, $field] = explode('.', $this->sortField);
            // Untuk relasi, kita sudah join, jadi bisa sort langsung.
            // Misal: 'products.name' atau 'branches.name'
            $stocksQuery->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc');
        } else {
            // Untuk field dari tabel stocks sendiri
            $stocksQuery->orderBy('stocks.'.$this->sortField, $this->sortAsc ? 'asc' : 'desc');
        }


        $stocksView = $stocksQuery->paginate(15); // Lebih banyak item per halaman untuk stok

        return view('livewire.admin-pusat.stock-management', [
            'stocksView' => $stocksView,
        ])->layout('layouts.admin-pusat');
    }
}