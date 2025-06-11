<?php

namespace App\Livewire\AdminPusat;

use App\Models\Branch;
use App\Models\DemandForecast as ForecastModel;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DemandForecasting extends Component
{
    // Input
    public $selectedBranchId;
    public $historicalMonths = 3;
    public $forecastForMonth;
    public $branches = [];

    // Output
    public $forecastResults = [];
    public $branchName = '';
    public $statusMessage = '';
    public $forecastPeriodStartDate;

    // Properti Baru untuk Ringkasan
    public $totalProductsNeedRestock = 0;
    public $totalUnitsToShip = 0;

    protected function rules()
    {
        return [
            'selectedBranchId' => 'required|exists:branches,id',
            'historicalMonths' => 'required|integer|min:1|max:12',
            'forecastForMonth' => 'required|date_format:Y-m',
        ];
    }

    protected $messages = [
        'selectedBranchId.required' => 'Cabang wajib dipilih.',
        'historicalMonths.required' => 'Periode bulan historis wajib diisi.',
        'historicalMonths.min' => 'Minimal periode bulan historis adalah 1.',
        'forecastForMonth.required' => 'Bulan target peramalan wajib diisi.',
    ];

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
        $this->forecastForMonth = Carbon::now()->addMonth()->format('Y-m');
    }

    public function generateForecast()
    {
        $this->validate();
        $this->resetOutput();

        try {
            $targetMonth = Carbon::createFromFormat('Y-m', $this->forecastForMonth)->startOfMonth();
            $this->forecastPeriodStartDate = $targetMonth->toDateString();

            $selectedBranch = Branch::find($this->selectedBranchId);
            $this->branchName = $selectedBranch->name;

            $productsInBranch = Product::where('is_active', true)
                ->whereHas('stocks', function ($query) {
                    $query->where('branch_id', $this->selectedBranchId);
                })
                ->with(['stocks' => function ($query) {
                    $query->where('branch_id', $this->selectedBranchId);
                }])
                ->get();

            if ($productsInBranch->isEmpty()) {
                $this->statusMessage = "Tidak ada produk yang terdaftar di cabang {$this->branchName}.";
                return;
            }

            $results = [];
            foreach ($productsInBranch as $product) {
                $totalSales = 0;
                $validPeriods = 0;

                for ($i = 1; $i <= $this->historicalMonths; $i++) {
                    $historicalMonthStart = $targetMonth->copy()->subMonths($i)->startOfMonth();
                    $historicalMonthEnd = $targetMonth->copy()->subMonths($i)->endOfMonth();
                    $monthlySales = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
                        ->where('sale_items.product_id', $product->id)
                        ->where('sales.branch_id', $this->selectedBranchId)
                        ->where('sales.status', 'completed')
                        ->whereBetween('sales.sale_date', [$historicalMonthStart, $historicalMonthEnd])
                        ->sum('sale_items.quantity');
                    $totalSales += (int)$monthlySales;
                    if ((int)$monthlySales > 0) $validPeriods++;
                }

                $forecastedQty = ($validPeriods > 0) ? round($totalSales / $validPeriods) : 0;
                $currentStock = $product->stocks->first()->quantity ?? 0;
                $recommendation = max(0, $forecastedQty - $currentStock);

                $results[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'current_stock' => $currentStock,
                    'forecast_quantity' => $forecastedQty,
                    'recommendation' => $recommendation,
                ];

                if ($recommendation > 0) {
                    $this->totalProductsNeedRestock++;
                    $this->totalUnitsToShip += $recommendation;
                }
            }

            $this->forecastResults = $results;
            $this->statusMessage = "Peramalan untuk cabang {$this->branchName} berhasil dibuat.";
        } catch (\Exception $e) {
            $this->statusMessage = "Terjadi kesalahan: " . $e->getMessage();
        }
    }

    public function saveForecast()
    {
        if (empty($this->forecastResults)) {
            session()->flash('error', 'Tidak ada data peramalan untuk disimpan.');
            return;
        }

        try {
            $targetMonth = Carbon::createFromFormat('Y-m', $this->forecastForMonth)->startOfMonth();
            $forecastEndDate = $targetMonth->copy()->endOfMonth()->toDateString();

            foreach ($this->forecastResults as $result) {
                ForecastModel::updateOrCreate(
                    [
                        'product_id' => $result['product_id'],
                        'branch_id' => $this->selectedBranchId,
                        'forecast_period_start_date' => $this->forecastPeriodStartDate,
                    ],
                    [
                        'forecast_period_end_date' => $forecastEndDate,
                        'forecasted_quantity' => $result['forecast_quantity'],
                        'forecasting_method_used' => 'SMA',
                        'parameters_used' => json_encode(['historical_months' => $this->historicalMonths]),
                        'user_id' => Auth::id(),
                    ]
                );
            }
            session()->flash('message', 'Semua data peramalan untuk cabang ' . $this->branchName . ' berhasil disimpan.');
        } catch (\Exception $e) {
            session()->flash('error', "Gagal menyimpan peramalan: " . $e->getMessage());
        }
    }

    private function resetOutput()
    {
        $this->forecastResults = [];
        $this->branchName = '';
        $this->statusMessage = '';
        $this->forecastPeriodStartDate = null;
        $this->totalProductsNeedRestock = 0;
        $this->totalUnitsToShip = 0;
        session()->forget(['message', 'error']);
    }

    public function render()
    {
        return view('livewire.admin-pusat.demand-forecasting')->layout('layouts.admin-pusat');
    }
}
