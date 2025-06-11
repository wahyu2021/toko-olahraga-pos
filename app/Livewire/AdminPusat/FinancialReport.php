<?php

namespace App\Livewire\AdminPusat;

use App\Models\Branch;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialReport extends Component
{
    use WithPagination;

    // Properti untuk filter
    public $startDate;
    public $endDate;
    public $filterBranchId = '';
    public $branches = [];

    // Metrik Periode Saat Ini
    public $totalRevenue = 0, $totalCOGS = 0, $grossProfit = 0;
    public $totalTransactions = 0, $grossProfitMargin = 0, $averageTransactionValue = 0;

    // Metrik Perbandingan Periode Sebelumnya
    public $revenueChange = 0, $profitChange = 0, $transactionsChange = 0;

    // Data Grafik & Status
    public $salesDataForChart;
    public $reportGenerated = false;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->branches = Branch::orderBy('name')->get();
        $this->endDate = Carbon::now()->format('Y-m-d');
        $this->startDate = Carbon::now()->subDays(29)->format('Y-m-d'); // Default 30 hari
        $this->triggerReportCalculation();
    }

    public function triggerReportCalculation()
    {
        $this->validate([
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);
        $this->resetPage();
        $this->calculateAllMetrics();
        // Dispatch event untuk memberitahu frontend (JS) agar merender ulang chart
        $this->dispatch('reportUpdated');
    }

    private function calculateMetricsForPeriod($start, $end)
    {
        $query = Sale::query()->where('status', 'completed')->whereBetween('sale_date', [$start, $end]);
        if ($this->filterBranchId) {
            $query->where('branch_id', $this->filterBranchId);
        }
        $sales = $query->get();
        $saleIds = $sales->pluck('id');

        $revenue = $sales->sum('total_amount');
        $cogs = SaleItem::whereIn('sale_id', $saleIds)
            ->select(DB::raw('SUM(quantity * purchase_price_at_sale) as total'))
            ->value('total') ?? 0;

        return [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'profit' => $revenue - $cogs,
            'transactions' => $sales->count(),
        ];
    }

    public function calculateAllMetrics()
    {
        $currentStart = Carbon::parse($this->startDate)->startOfDay();
        $currentEnd = Carbon::parse($this->endDate)->endOfDay();

        $currentData = $this->calculateMetricsForPeriod($currentStart, $currentEnd);
        $this->totalRevenue = $currentData['revenue'];
        $this->totalCOGS = $currentData['cogs'];
        $this->grossProfit = $currentData['profit'];
        $this->totalTransactions = $currentData['transactions'];
        $this->grossProfitMargin = $this->totalRevenue > 0 ? ($this->grossProfit / $this->totalRevenue) * 100 : 0;
        $this->averageTransactionValue = $this->totalTransactions > 0 ? $this->totalRevenue / $this->totalTransactions : 0;

        $periodDuration = $currentStart->diffInDays($currentEnd);
        $previousStart = $currentStart->copy()->subDays($periodDuration + 1);
        $previousEnd = $currentStart->copy()->subDay()->endOfDay();
        $previousData = $this->calculateMetricsForPeriod($previousStart, $previousEnd);

        $this->revenueChange = $this->calculateChange($this->totalRevenue, $previousData['revenue']);
        $this->profitChange = $this->calculateChange($this->grossProfit, $previousData['profit']);
        $this->transactionsChange = $this->calculateChange($this->totalTransactions, $previousData['transactions']);

        $this->prepareChartData($currentStart, $currentEnd);
        $this->reportGenerated = true;
    }

    private function calculateChange($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return (($current - $previous) / $previous) * 100;
    }

    public function prepareChartData(Carbon $start, Carbon $end)
    {
        $query = Sale::query()->where('status', 'completed')->whereBetween('sale_date', [$start, $end]);
        if ($this->filterBranchId) {
            $query->where('branch_id', $this->filterBranchId);
        }
        $saleIds = $query->pluck('id');

        $dailyData = Sale::whereIn('id', $saleIds)
            ->select(DB::raw('DATE(sale_date) as date'), DB::raw('SUM(total_amount) as daily_revenue'))
            ->groupBy('date')->orderBy('date')->get()->keyBy('date');

        $dailyCogs = SaleItem::join('sales', 'sale_items.sale_id', '=', 'sales.id')->whereIn('sales.id', $saleIds)
            ->select(DB::raw('DATE(sales.sale_date) as date'), DB::raw('SUM(sale_items.quantity * sale_items.purchase_price_at_sale) as daily_cogs'))
            ->groupBy('date')->get()->keyBy('date');

        $labels = [];
        $revenueData = [];
        $cogsData = [];
        $profitData = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $revenue = $dailyData->get($formattedDate)->daily_revenue ?? 0;
            $cogs = $dailyCogs->get($formattedDate)->daily_cogs ?? 0;

            $revenueData[] = $revenue;
            $cogsData[] = $cogs;
            $profitData[] = $revenue - $cogs;
        }

        $this->salesDataForChart = [
            'labels' => $labels,
            'datasets' => [
                ['label' => 'Pendapatan', 'data' => $revenueData, 'borderColor' => 'rgb(249, 115, 22)', 'backgroundColor' => 'rgba(249, 115, 22, 0.1)'],
                ['label' => 'HPP (COGS)', 'data' => $cogsData, 'borderColor' => 'rgb(239, 68, 68)', 'backgroundColor' => 'rgba(239, 68, 68, 0.1)'],
                ['label' => 'Laba Kotor', 'data' => $profitData, 'borderColor' => 'rgb(34, 197, 94)', 'backgroundColor' => 'rgba(34, 197, 94, 0.1)'],
            ],
        ];
    }

    public function exportReport(): StreamedResponse
    {
        $this->triggerReportCalculation();
        $fileName = 'Laporan_Keuangan_' . $this->startDate . '_hingga_' . $this->endDate . '.csv';

        $query = Sale::with(['branch', 'user', 'items'])
            ->whereBetween('sale_date', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->where('status', 'completed');
        if ($this->filterBranchId) {
            $query->where('branch_id', $this->filterBranchId);
        }
        $sales = $query->orderBy('sale_date', 'desc')->get();

        return response()->streamDownload(function () use ($sales) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No. Invoice', 'Tanggal', 'Cabang', 'Kasir', 'Pendapatan', 'HPP (COGS)', 'Laba Kotor']);

            foreach ($sales as $sale) {
                $saleCOGS = $sale->items->sum(fn($item) => $item->quantity * $item->purchase_price_at_sale);
                $saleGrossProfit = $sale->total_amount - $saleCOGS;
                fputcsv($file, [
                    $sale->invoice_number,
                    $sale->sale_date->format('Y-m-d H:i:s'),
                    $sale->branch->name ?? '-',
                    $sale->user->name ?? '-',
                    $sale->total_amount,
                    $saleCOGS,
                    $saleGrossProfit,
                ]);
            }
            fclose($file);
        }, $fileName);
    }

    public function render()
    {
        $query = Sale::with(['branch', 'user', 'items'])
            ->whereBetween('sale_date', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->where('status', 'completed')
            ->orderBy('sale_date', 'desc');

        if ($this->filterBranchId) {
            $query->where('branch_id', $this->filterBranchId);
        }

        return view('livewire.admin-pusat.financial-report', [
            'salesDetails' => $query->paginate(10),
        ])->layout('layouts.admin-pusat');
    }
}
