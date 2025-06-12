<?php

namespace App\Livewire\AdminPusat;

use App\Services\AdminPusat\DashboardService; // Pastikan ini di-import
use Livewire\Component;

class AdminPusatDashboard extends Component
{
    // Properti untuk data Dashboard
    public $totalRevenueThisMonth;
    public $revenueChangePercentage;
    public $activeProductsCount;
    public $totalBranchesCount;
    public $totalActiveUsersCount;
    public $bestSellingProducts;
    public $lowStockItems;
    public $salesTrendData;
    public $salesByBranchData;

    /**
     * Metode mount akan memanggil service untuk memuat semua data.
     */
    public function mount(DashboardService $dashboardService)
    {
        $summaryMetrics = $dashboardService->getSummaryMetrics();
        $this->totalRevenueThisMonth = $summaryMetrics['totalRevenueThisMonth'];
        $this->revenueChangePercentage = $summaryMetrics['revenueChangePercentage'];
        $this->activeProductsCount = $summaryMetrics['activeProductsCount'];
        $this->totalBranchesCount = $summaryMetrics['totalBranchesCount'];
        $this->totalActiveUsersCount = $summaryMetrics['totalActiveUsersCount'];

        $this->bestSellingProducts = $dashboardService->getBestSellingProducts();
        $this->lowStockItems = $dashboardService->getLowStockItems();

        $this->salesTrendData = $dashboardService->getSalesTrendChartData();
        $this->salesByBranchData = $dashboardService->getSalesByBranchChartData();
    }

    /**
     * Merender view komponen.
     */
    public function render()
    {
        return view('livewire.admin-pusat.admin-pusat-dashboard')
            ->layout('layouts.admin-pusat');
    }
}
