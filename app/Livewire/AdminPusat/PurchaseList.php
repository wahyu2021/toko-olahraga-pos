<?php

namespace App\Livewire\AdminPusat;

use Livewire\Component;
use App\Models\Purchase;
use App\Models\Supplier;
// use App\Models\Branch; // Dihapus karena sudah tidak digunakan di sini
use Livewire\WithPagination;

class PurchaseList extends Component
{
    use WithPagination;

    // Properti untuk filter dan pencarian
    public $search = '';
    public $filterSupplier = '';
    public $startDate, $endDate;

    // Properti untuk modal detail
    public $selectedPurchase;
    public $isDetailModalOpen = false;

    protected $paginationTheme = 'tailwind';

    // Hapus listener untuk filter yang sudah tidak ada
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterSupplier()
    {
        $this->resetPage();
    }
    public function updatingStartDate()
    {
        $this->resetPage();
    }
    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        // === PERBAIKAN PADA QUERY UTAMA ===
        $query = Purchase::with(['supplier', 'user'])
            ->withSum('items as total_quantity_received', 'quantity_received')
            ->latest('purchase_date'); // Urutkan berdasarkan tanggal pembelian terbaru

        // Terapkan filter pencarian
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('supplier', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Terapkan filter lainnya
        if (!empty($this->filterSupplier)) {
            $query->where('supplier_id', $this->filterSupplier);
        }
        if (!empty($this->startDate) && !empty($this->endDate)) {
            $query->whereBetween('purchase_date', [$this->startDate, $this->endDate]);
        }

        $purchases = $query->paginate(15);

        // Tidak perlu lagi memuat data cabang
        $suppliers = Supplier::orderBy('name')->get();

        return view('livewire.admin-pusat.purchase-list', [
            'purchases' => $purchases,
            'suppliers' => $suppliers,
            // $branches tidak lagi di-pass ke view
        ])->layout('layouts.admin-pusat');
    }

    // Metode untuk menampilkan detail pembelian di modal
    public function showDetails($purchaseId)
    {
        // Eager load relasi 'items' dan 'items.product' untuk ditampilkan di modal
        // Relasi 'branch' dihapus dari sini karena sudah tidak ada di tabel purchases
        $this->selectedPurchase = Purchase::with(['supplier', 'user', 'items', 'items.product'])->find($purchaseId);
        $this->isDetailModalOpen = true;
    }

    public function closeDetailModal()
    {
        $this->isDetailModalOpen = false;
        $this->selectedPurchase = null;
    }
}
