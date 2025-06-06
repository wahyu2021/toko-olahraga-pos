<?php

namespace App\Livewire\AdminPusat;

use Livewire\Component;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\DemandForecast as ForecastModel; // Alias untuk menghindari konflik nama kelas
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DemandForecasting extends Component
{
    // Input dari User
    public $selectedProductId;
    public $selectedBranchId;
    public $historicalMonths = 3; // Jumlah bulan historis untuk SMA
    public $forecastForMonth; // Bulan target (format YYYY-MM)

    public $products = [];
    public $branches = [];

    // Output
    public $forecastedQuantity = null;
    public $calculationDetails = null; // Untuk menampilkan detail perhitungan/data historis
    public $statusMessage = '';
    public $forecastPeriodStartDate;
    public $forecastPeriodEndDate;

    protected function rules()
    {
        return [
            'selectedProductId' => 'required|exists:products,id',
            'selectedBranchId' => 'required|exists:branches,id',
            'historicalMonths' => 'required|integer|min:1|max:12',
            'forecastForMonth' => 'required|date_format:Y-m',
        ];
    }

    protected $messages = [
        'selectedProductId.required' => 'Produk wajib dipilih.',
        'selectedBranchId.required' => 'Cabang wajib dipilih.',
        'historicalMonths.required' => 'Periode bulan historis wajib diisi.',
        'historicalMonths.integer' => 'Periode bulan historis harus angka.',
        'historicalMonths.min' => 'Minimal periode bulan historis adalah 1.',
        'historicalMonths.max' => 'Maksimal periode bulan historis adalah 12.',
        'forecastForMonth.required' => 'Bulan target peramalan wajib diisi.',
        'forecastForMonth.date_format' => 'Format bulan target harus YYYY-MM (misal: 2025-07).',
    ];

    public function mount()
    {
        $this->products = Product::where('is_active', true)->orderBy('name')->get();
        $this->branches = Branch::orderBy('name')->get();
        $this->forecastForMonth = Carbon::now()->addMonth()->format('Y-m'); // Default ke bulan depan
    }

    public function generateForecast()
    {
        $this->validate();
        $this->resetOutput();

        try {
            $targetMonth = Carbon::createFromFormat('Y-m', $this->forecastForMonth)->startOfMonth();
            $this->forecastPeriodStartDate = $targetMonth->toDateString();
            $this->forecastPeriodEndDate = $targetMonth->copy()->endOfMonth()->toDateString();

            $historicalData = [];
            $totalSales = 0;
            $validPeriods = 0;

            for ($i = 1; $i <= $this->historicalMonths; $i++) {
                $currentHistoricalMonthStart = $targetMonth->copy()->subMonths($i)->startOfMonth();
                $currentHistoricalMonthEnd = $targetMonth->copy()->subMonths($i)->endOfMonth();

                $monthlySales = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sale_items.product_id', $this->selectedProductId)
                    ->where('sales.branch_id', $this->selectedBranchId)
                    ->where('sales.status', 'completed')
                    ->whereBetween('sales.sale_date', [$currentHistoricalMonthStart, $currentHistoricalMonthEnd])
                    ->sum('sale_items.quantity');

                $historicalData[] = [
                    'period' => $currentHistoricalMonthStart->isoFormat('MMMM YYYY'),
                    'sales' => (int)$monthlySales,
                ];
                $totalSales += (int)$monthlySales;
                if ((int)$monthlySales > 0) { // Hanya hitung periode dengan penjualan untuk SMA yang lebih representatif
                    $validPeriods++;
                }
            }

            $this->calculationDetails = [
                'product' => Product::find($this->selectedProductId)->name,
                'branch' => Branch::find($this->selectedBranchId)->name,
                'historical_data' => array_reverse($historicalData) // Tampilkan dari yang terlama
            ];

            if ($validPeriods > 0) {
                $this->forecastedQuantity = round($totalSales / $validPeriods);
                $this->statusMessage = "Peramalan berhasil dibuat.";
            } else {
                $this->forecastedQuantity = 0; // Jika tidak ada data historis, ramalan 0
                $this->statusMessage = "Tidak ada data penjualan historis yang cukup untuk produk dan cabang ini dalam " . $this->historicalMonths . " bulan terakhir. Peramalan diatur ke 0.";
            }
        } catch (\Exception $e) {
            $this->statusMessage = "Terjadi kesalahan saat membuat peramalan: " . $e->getMessage();
            $this->forecastedQuantity = null;
            $this->calculationDetails = null;
        }
    }

    public function saveForecast()
    {
        $this->validate([ // Validasi ulang sebelum simpan
            'selectedProductId' => 'required|exists:products,id',
            'selectedBranchId' => 'required|exists:branches,id',
            'forecastForMonth' => 'required|date_format:Y-m',
        ]);

        if ($this->forecastedQuantity === null) {
            session()->flash('error', 'Tidak ada data peramalan untuk disimpan. Harap generate peramalan terlebih dahulu.');
            return;
        }

        try {
            ForecastModel::updateOrCreate(
                [
                    'product_id' => $this->selectedProductId,
                    'branch_id' => $this->selectedBranchId,
                    'forecast_period_start_date' => $this->forecastPeriodStartDate,
                ],
                [
                    'forecast_period_end_date' => $this->forecastPeriodEndDate,
                    'forecasted_quantity' => $this->forecastedQuantity,
                    'forecasting_method_used' => 'SMA', // Simple Moving Average
                    'parameters_used' => json_encode(['historical_months' => $this->historicalMonths, 'data_points' => $this->calculationDetails['historical_data'] ?? []]),
                    'user_id' => Auth::id(),
                ]
            );
            session()->flash('message', 'Data peramalan berhasil disimpan.');
            // Reset form atau output setelah simpan jika perlu
            // $this->resetOutput();
            // $this->resetInput();

        } catch (\Exception $e) {
            session()->flash('error', "Gagal menyimpan peramalan: " . $e->getMessage());
        }
    }

    private function resetOutput()
    {
        $this->forecastedQuantity = null;
        $this->calculationDetails = null;
        $this->statusMessage = '';
        $this->forecastPeriodStartDate = null;
        $this->forecastPeriodEndDate = null;
        session()->forget(['message', 'error']);
    }

    // private function resetInput() // Jika ingin mereset input setelah save
    // {
    //     $this->selectedProductId = null;
    //     $this->selectedBranchId = null;
    //     $this->historicalMonths = 3;
    //     $this->forecastForMonth = Carbon::now()->addMonth()->format('Y-m');
    // }

    public function render()
    {
        return view('livewire.admin-pusat.demand-forecasting')->layout('layouts.admin-pusat');
    }
}
