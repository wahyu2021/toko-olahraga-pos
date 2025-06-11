<?php

namespace App\Livewire\ManajerCabang;

use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ViewBranchStock extends Component
{
    use WithPagination;

    public $searchProduct = '';

    protected $paginationTheme = 'tailwind';

    public function render()
    {
        // Mengambil ID cabang dari user yang sedang login
        $branchId = Auth::user()->branch_id;

        // Query stok hanya untuk cabang tersebut
        $stocks = Stock::where('branch_id', $branchId)
            ->with(['product']) // Eager load relasi produk untuk efisiensi
            ->whereHas('product', function ($query) {
                // Filter berdasarkan nama atau SKU produk
                $query->where('name', 'like', '%' . $this->searchProduct . '%')
                    ->orWhere('sku', 'like', '%' . $this->searchProduct . '%');
            })
            ->latest('updated_at')
            ->paginate(15);

        return view('livewire.manajer-cabang.view-branch-stock', [
            'stocks' => $stocks,
        ])->layout('layouts.manajer-cabang');
    }
}
