<?php

namespace App\Livewire\ManajerCabang;

use App\Models\DemandForecast;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ViewBranchForecast extends Component
{
    use WithPagination;

    public $filterMonth = '';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        // Set default filter ke bulan saat ini
        $this->filterMonth = now()->format('Y-m');
    }

    public function updatingFilterMonth()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Ambil ID cabang dari user yang sedang login
        $branchId = Auth::user()->branch_id;

        $query = DemandForecast::with(['product', 'user'])
            // Filter utama: hanya untuk cabang ini
            ->where('branch_id', $branchId)
            // Filter berdasarkan bulan yang dipilih
            ->when($this->filterMonth, function ($q) {
                $q->whereYear('forecast_period_start_date', substr($this->filterMonth, 0, 4))
                    ->whereMonth('forecast_period_start_date', substr($this->filterMonth, 5, 2));
            })
            ->latest('created_at');

        return view('livewire.manajer-cabang.view-branch-forecast', [
            'forecasts' => $query->paginate(15),
        ])->layout('layouts.manajer-cabang');
    }
}
