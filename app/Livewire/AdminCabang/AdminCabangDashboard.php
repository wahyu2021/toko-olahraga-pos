<?php

namespace App\Livewire\AdminCabang;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminCabangDashboard extends Component
{
    public $branch;
    public $totalRevenueThisMonth = 0;
    public $totalTransactionsThisMonth = 0;
    public $bestSellingProducts;
    public $lowStockItems;
    public $salesTrendData; // Properti untuk data grafik

    public function mount()
    {
        $this->branch = Auth::user()->branch;

        if ($this->branch) {
            $this->loadSummaryData();
            $this->loadWidgetData();
            $this->prepareSalesTrendChart(); // Panggil fungsi untuk grafik
        }
    }

    public function loadSummaryData()
    {
        $salesQuery = Sale::where('branch_id', $this->branch->id)
            ->where('status', 'completed')
            ->whereYear('sale_date', now()->year)
            ->whereMonth('sale_date', now()->month);

        $this->totalRevenueThisMonth = $salesQuery->sum('total_amount');
        $this->totalTransactionsThisMonth = $salesQuery->count();
    }

    public function loadWidgetData()
    {
        $this->bestSellingProducts = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.branch_id', $this->branch->id)
            ->where('sales.status', 'completed')
            ->whereMonth('sales.sale_date', now()->month)
            ->whereYear('sales.sale_date', now()->year)
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        $this->lowStockItems = Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->where('stocks.branch_id', $this->branch->id)
            ->where('products.is_active', true)
            ->whereColumn('stocks.quantity', '<=', 'products.low_stock_threshold')
            ->select('products.name as product_name', 'stocks.quantity')
            ->orderBy('stocks.quantity', 'asc')
            ->take(5)
            ->get();
    }

    // Fungsi baru untuk mengambil data grafik
    public function prepareSalesTrendChart()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(29);

        $sales = Sale::where('branch_id', $this->branch->id)
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(sale_date) as sale_day'),
                DB::raw('SUM(total_amount) as daily_revenue')
            )
            ->groupBy('sale_day')->orderBy('sale_day', 'asc')
            ->get()->keyBy('sale_day');

        $labels = [];
        $data = [];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->toDateString();
            $labels[] = $currentDate->isoFormat('D MMM');
            $data[] = $sales->get($dateString)->daily_revenue ?? 0;
            $currentDate->addDay();
        }

        $this->salesTrendData = [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Pendapatan Harian',
                'data' => $data,
                'borderColor' => 'rgb(249, 115, 22)',
                'backgroundColor' => 'rgba(249, 115, 22, 0.1)',
                'tension' => 0.2,
                'fill' => true,
            ]]
        ];
    }

    public function render()
    {
        return view('livewire.admin-cabang.admin-cabang-dashboard')
            ->layout('layouts.admin-cabang');
    }
}
