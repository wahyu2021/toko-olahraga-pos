<?php

namespace App\Livewire\ManajerPusat;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ManajerPusatDashboard extends Component
{
    // Properti untuk Kartu Ringkasan
    public $totalRevenueThisMonth;
    public $activeProductsCount;
    public $totalBranchesCount;
    public $totalActiveUsersCount;

    // Properti untuk Widget
    public $bestSellingProducts;
    public $lowStockItems;

    public function mount()
    {
        $this->loadSummaryData();
        $this->loadWidgetData();
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
        $this->totalActiveUsersCount = User::count();
    }

    public function loadWidgetData()
    {
        $this->bestSellingProducts = SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereMonth('sales.sale_date', now()->month)
            ->whereYear('sales.sale_date', now()->year)
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        $this->lowStockItems = Product::join('stocks', 'products.id', '=', 'stocks.product_id')
            ->join('branches', 'stocks.branch_id', '=', 'branches.id')
            ->where('products.is_active', true)
            ->whereColumn('stocks.quantity', '<=', 'products.low_stock_threshold')
            ->select('products.name as product_name', 'branches.name as branch_name', 'stocks.quantity')
            ->orderBy('stocks.quantity', 'asc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.manajer-pusat.manajer-pusat-dashboard')
            ->layout('layouts.manajer-pusat');
    }
}
