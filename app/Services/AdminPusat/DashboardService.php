<?php

namespace App\Services\AdminPusat;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getSummaryMetrics(): array
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonthNoOverflow()->startOfMonth();

        $revenueData = Sale::where('status', 'completed')
            ->where('sale_date', '>=', $lastMonthStart) // Filter awal untuk performa
            ->selectRaw(
                'SUM(CASE WHEN sale_date >= ? THEN total_amount ELSE 0 END) as current_month_revenue,
                 SUM(CASE WHEN sale_date >= ? AND sale_date < ? THEN total_amount ELSE 0 END) as last_month_revenue',
                [
                    $currentMonthStart, // Binding untuk current_month_revenue
                    $lastMonthStart,    // Binding #1 untuk last_month_revenue
                    $currentMonthStart  // Binding #2 untuk last_month_revenue
                ]
            )->first();

        $totalRevenueThisMonth = $revenueData->current_month_revenue ?? 0;
        $totalRevenueLastMonth = $revenueData->last_month_revenue ?? 0;

        $revenueChangePercentage = 0;
        if ($totalRevenueLastMonth > 0) {
            $revenueChangePercentage = (($totalRevenueThisMonth - $totalRevenueLastMonth) / $totalRevenueLastMonth) * 100;
        } elseif ($totalRevenueThisMonth > 0) {
            $revenueChangePercentage = 100;
        }

        return [
            'totalRevenueThisMonth' => $totalRevenueThisMonth,
            'revenueChangePercentage' => $revenueChangePercentage,
            'activeProductsCount' => Product::where('is_active', true)->count(),
            'totalBranchesCount' => Branch::count(),
            'totalActiveUsersCount' => User::count(),
        ];
    }

    public function getBestSellingProducts(int $limit = 5): \Illuminate\Support\Collection
    {
        return SaleItem::join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereMonth('sales.sale_date', now()->month)
            ->whereYear('sales.sale_date', now()->year)
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderBy('total_sold', 'desc')
            ->take($limit)
            ->get();
    }

    public function getLowStockItems(int $limit = 5): \Illuminate\Support\Collection
    {
        return Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->join('branches', 'stocks.branch_id', '=', 'branches.id')
            ->where('products.is_active', true)
            ->whereColumn('stocks.quantity', '<=', 'products.low_stock_threshold')
            ->select('products.name as product_name', 'branches.name as branch_name', 'stocks.quantity')
            ->orderBy('stocks.quantity', 'asc')
            ->take($limit)
            ->get();
    }

    public function getSalesTrendChartData(int $days = 30): array
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays($days - 1)->startOfDay();

        $sales = Sale::select(
            DB::raw('DATE(sale_date) as sale_day'),
            DB::raw('SUM(total_amount) as daily_revenue')
        )
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('sale_day')
            ->orderBy('sale_day', 'asc')
            ->get()->keyBy('sale_day');

        $labels = [];
        $data = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->toDateString();
            $labels[] = $date->isoFormat('D MMM');
            $data[] = $sales->get($dateString)->daily_revenue ?? 0;
        }

        return [
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

    public function getSalesByBranchChartData(): array
    {
        $sales = Sale::join('branches', 'sales.branch_id', '=', 'branches.id')
            ->select('branches.name as branch_name', DB::raw('SUM(sales.total_amount) as total_revenue'))
            ->where('sales.status', 'completed')
            ->whereMonth('sales.sale_date', now()->month)
            ->whereYear('sales.sale_date', now()->year)
            ->groupBy('branches.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return [
            'labels' => $sales->pluck('branch_name'),
            'datasets' => [[
                'data' => $sales->pluck('total_revenue'),
                'backgroundColor' => [
                    'rgba(249, 115, 22, 0.8)',
                    'rgba(234, 88, 12, 0.8)',
                    'rgba(194, 65, 12, 0.8)',
                    'rgba(251, 146, 60, 0.8)',
                    'rgba(253, 186, 116, 0.8)',
                    'rgba(124, 45, 18, 0.8)',
                ],
                'hoverOffset' => 4,
            ]]
        ];
    }
}
