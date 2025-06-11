<?php

namespace App\Livewire\AdminPusat;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Stock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminPusatDashboard extends Component
{
    // Properti untuk Kartu Ringkasan
    public $totalRevenueThisMonth;
    public $revenueChangePercentage; // BARU: Untuk perbandingan pendapatan
    public $activeProductsCount;
    public $totalBranchesCount;
    public $totalActiveUsersCount;

    // Properti untuk Widget Baru
    public $bestSellingProducts;
    public $lowStockItems;

    // Properti untuk Data Grafik
    public $salesTrendData;
    public $salesByBranchData;

    /**
     * Metode ini berjalan saat komponen pertama kali dimuat.
     */
    public function mount()
    {
        $this->loadSummaryData();
        $this->loadWidgetData();
        $this->prepareSalesTrendChart();
        $this->prepareSalesByBranchChart();
    }

    /**
     * Mengambil data untuk kartu ringkasan di bagian atas.
     */
    public function loadSummaryData()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $lastMonth = Carbon::now()->subMonthNoOverflow();

        // Pendapatan Bulan Ini
        $this->totalRevenueThisMonth = Sale::where('status', 'completed')
            ->whereYear('sale_date', $currentYear)
            ->whereMonth('sale_date', $currentMonth)
            ->sum('total_amount');

        // BARU: Logika Perbandingan Pendapatan
        $totalRevenueLastMonth = Sale::where('status', 'completed')
            ->whereYear('sale_date', $lastMonth->year)
            ->whereMonth('sale_date', $lastMonth->month)
            ->sum('total_amount');

        if ($totalRevenueLastMonth > 0) {
            $this->revenueChangePercentage = (($this->totalRevenueThisMonth - $totalRevenueLastMonth) / $totalRevenueLastMonth) * 100;
        } else {
            $this->revenueChangePercentage = $this->totalRevenueThisMonth > 0 ? 100 : 0; // Anggap 100% jika bulan lalu 0
        }

        // Data Ringkasan Lainnya
        $this->activeProductsCount = Product::where('is_active', true)->count();
        $this->totalBranchesCount = Branch::count();
        $this->totalActiveUsersCount = User::count();
    }


    /**
     * Load Data Widget
     */
    public function loadWidgetData()
    {
        // Widget Produk Terlaris (Bulan Ini)
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

        // Widget Peringatan Stok Rendah (Stok <= 10)
        // Sesuaikan '10' dengan ambang batas yang Anda inginkan
        // subquery untuk menjumlahkan stok dari tabel 'stocks'
        $aggregatedStocks = Stock::select(
            'product_id',
            DB::raw('SUM(quantity) as total_stock')
        )->groupBy('product_id');

        // Query utama untuk produk, di-join dengan hasil subquery di atas
        $this->lowStockItems = Stock::join('products', 'stocks.product_id', '=', 'products.id')
            ->join('branches', 'stocks.branch_id', '=', 'branches.id')
            ->where('products.is_active', true)
            // Bandingkan kolom 'quantity' dari tabel stocks
            // dengan kolom 'low_stock_threshold' dari tabel products
            ->whereColumn('stocks.quantity', '<=', 'products.low_stock_threshold')
            // Pilih kolom yang kita butuhkan untuk ditampilkan
            ->select(
                'products.name as product_name',
                'branches.name as branch_name',
                'stocks.quantity'
            )
            ->orderBy('stocks.quantity', 'asc') // Urutkan dari stok paling sedikit
            ->take(5) // Ambil 5 record stok terendah
            ->get();
    }

    /**
     * Menyiapkan data untuk grafik tren penjualan (line chart).
     */
    public function prepareSalesTrendChart()
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(29);

        // Ambil data penjualan aktual
        $sales = Sale::select(
            DB::raw('DATE(sale_date) as sale_day'),
            DB::raw('SUM(total_amount) as daily_revenue')
        )
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('sale_day')
            ->orderBy('sale_day', 'asc')
            ->get()
            ->keyBy('sale_day'); // Gunakan keyBy untuk memudahkan pencarian tanggal

        $labels = [];
        $data = [];
        // Buat rentang tanggal 30 hari agar semua hari ada di grafik
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateString = $currentDate->toDateString();
            $labels[] = $currentDate->isoFormat('D MMM'); // Format label (e.g., "6 Jun")
            $data[] = isset($sales[$dateString]) ? (float) $sales[$dateString]->daily_revenue : 0; // Isi dengan 0 jika tidak ada penjualan
            $currentDate->addDay();
        }

        // Siapkan data dalam format yang dibutuhkan Chart.js
        $this->salesTrendData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pendapatan Harian',
                    'data' => $data,
                    'borderColor' => 'rgb(37, 99, 235)', // Warna biru sesuai tema
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)',
                    'tension' => 0.2,
                    'fill' => true,
                ]
            ]
        ];
    }

    /**
     * Menyiapkan data untuk grafik penjualan per cabang (pie chart).
     */
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
        $data = $sales->pluck('total_revenue')->map(fn($value) => (float) $value)->toArray();

        // Siapkan beberapa warna untuk pie chart
        $backgroundColors = [
            'rgba(37, 99, 235, 0.8)',   // blue-700
            'rgba(22, 163, 74, 0.8)',   // green-600
            'rgba(234, 179, 8, 0.8)',   // amber-500
            'rgba(220, 38, 38, 0.8)',   // red-600
            'rgba(147, 51, 234, 0.8)',  // purple-600
            'rgba(236, 72, 153, 0.8)',  // pink-500
        ];

        $this->salesByBranchData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($labels)),
                    'hoverOffset' => 4
                ]
            ]
        ];
    }

    /**
     * Merender view komponen.
     */
    public function render()
    {
        // Ganti 'layouts.admin-pusat' dengan nama file layout Anda yang sebenarnya jika berbeda
        return view('livewire.admin-pusat.admin-pusat-dashboard')
            ->layout('layouts.admin-pusat');
    }
}
