<?php

namespace App\Livewire\ManajerPusat;

use App\Models\Branch;
use App\Models\DemandForecast;
use Livewire\Component;
use Livewire\WithPagination;

class ViewForecast extends Component
{
    use WithPagination;

    public $filterBranchId = '';
    public $filterMonth = '';
    public $branches;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
        $this->filterMonth = now()->format('Y-m');
    }

    public function render()
    {
        $query = DemandForecast::with(['product', 'branch', 'user'])
            ->when($this->filterBranchId, fn($q) => $q->where('branch_id', $this->filterBranchId))
            ->when($this->filterMonth, function ($q) {
                $q->whereYear('forecast_period_start_date', substr($this->filterMonth, 0, 4))
                    ->whereMonth('forecast_period_start_date', substr($this->filterMonth, 5, 2));
            })
            ->latest('created_at');

        return view('livewire.manajer-pusat.view-forecast', [
            'forecasts' => $query->paginate(20),
        ])->layout('layouts.manajer-pusat');
    }
}
