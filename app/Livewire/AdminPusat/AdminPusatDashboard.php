<?php

namespace App\Livewire\AdminPusat;

use Livewire\Component;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Branch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminPusatDashboard extends Component
{
    // Properti untuk Ringkasan
    public $totalRevenueThisMonth;
    public $activeProductsCount;
    public $totalBranchesCount;
    public $totalActiveUsersCount;

    // Properti untuk Data Grafik
    public $salesTrendData; // Untuk grafik tren penjualan
    public $salesByBranchData; // Untuk grafik penjualan per cabang

    public function mount()
    {
        $this->loadSummaryData();
        $this->prepareSalesTrendChart();
        $this->prepareSalesByBranchChart();
    }

    public function loadSummaryData()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $this->totalRevenueThisMonth = Sale::where('status', 'completed')
            ->whereYear('sale_date', $currentYear)
            ->whereMonth('sale_date', $currentMonth)
            ->sum('total_amount');

        $this->activeProductsCount = Product::where('is_active', true)->count();
        $this->totalBranchesCount = Branch::count();
        $this->totalActiveUsersCount = User::count(); // Atau filter berdasarkan status aktif jika ada
    }

    public function prepareSalesTrendChart()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(29); // 30 hari termasuk hari ini

        $sales = Sale::select(
            DB::raw('DATE(sale_date) as sale_day'),
            DB::raw('SUM(total_amount) as daily_revenue')
        )
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('sale_day')
            ->orderBy('sale_day', 'asc')
            ->get();

        $labels = [];
        $data = [];
        // Inisialisasi semua tanggal dalam rentang dengan revenue 0
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $labels[] = $currentDate->isoFormat('D MMM');
            $data[$currentDate->toDateString()] = 0;
            $currentDate->addDay();
        }

        // Isi data revenue aktual
        foreach ($sales as $sale) {
            $data[$sale->sale_day] = (float) $sale->daily_revenue;
        }

        $this->salesTrendData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan Harian (30 Hari Terakhir)',
                    'data' => array_values($data), // Pastikan urutan sesuai dengan labels
                    'borderColor' => 'rgb(59, 130, 246)', // blue-500
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.1,
                    'fill' => true,
                ]
            ]
        ];
        // Dispatch event untuk menginisialisasi/update chart di frontend
        $this->dispatch('salesTrendChartUpdated', $this->salesTrendData);
    }

    public function prepareSalesByBranchChart()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $sales = Sale::join('branches', 'sales.branch_id', '=', 'branches.id')
            ->select('branches.name as branch_name', DB::raw('SUM(sales.total_amount) as total_revenue'))
            ->where('sales.status', 'completed')
            ->whereYear('sales.sale_date', $currentYear)
            ->whereMonth('sales.sale_date', $currentMonth)
            ->groupBy('branches.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        $labels = $sales->pluck('branch_name')->toArray();
        $data = $sales->pluck('total_revenue')->map(function ($value) {
            return (float) $value;
        })->toArray();

        // Warna untuk Pie Chart (bisa ditambahkan lebih banyak jika cabang banyak)
        $backgroundColors = [
            'rgba(59, 130, 246, 0.7)', // blue-500
            'rgba(239, 68, 68, 0.7)',  // red-500
            'rgba(245, 158, 11, 0.7)', // amber-500
            'rgba(16, 185, 129, 0.7)', // emerald-500
            'rgba(139, 92, 246, 0.7)', // violet-500
            'rgba(236, 72, 153, 0.7)', // pink-500
        ];
        $borderColors = [
            'rgb(59, 130, 246)',
            'rgb(239, 68, 68)',
            'rgb(245, 158, 11)',
            'rgb(16, 185, 129)',
            'rgb(139, 92, 246)',
            'rgb(236, 72, 153)',
        ];


        $this->salesByBranchData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Distribusi Penjualan per Cabang (Bulan Ini)',
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($labels)),
                    'borderColor' => array_slice($borderColors, 0, count($labels)),
                    'hoverOffset' => 4
                ]
            ]
        ];
        $this->dispatch('salesByBranchChartUpdated', $this->salesByBranchData);
    }


    public function render()
    {
        return view('livewire.admin-pusat.admin-pusat-dashboard')
            ->layout('layouts.admin-pusat');
    }
}
