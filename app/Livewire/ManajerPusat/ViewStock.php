<?php

namespace App\Livewire\ManajerPusat;

use App\Models\Branch;
use App\Models\Stock;
use Livewire\Component;
use Livewire\WithPagination;

class ViewStock extends Component
{
    use WithPagination;

    public $searchProduct = '';
    public $filterBranch = '';
    public $sortField = 'products.name';
    public $sortAsc = true;
    public $branches;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
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

    public function render()
    {
        $stocks = Stock::query()
            ->with(['product', 'branch'])
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->join('branches', 'stocks.branch_id', '=', 'branches.id')
            ->select('stocks.*')
            ->when($this->searchProduct, function ($query) {
                $query->where(function ($q) {
                    $q->where('products.name', 'like', '%' . $this->searchProduct . '%')
                        ->orWhere('products.sku', 'like', '%' . $this->searchProduct . '%');
                });
            })
            ->when($this->filterBranch, function ($query) {
                $query->where('stocks.branch_id', $this->filterBranch);
            })
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate(15);

        return view('livewire.manajer-pusat.view-stock', [
            'stocksView' => $stocks,
        ])->layout('layouts.manajer-pusat');
    }
}
