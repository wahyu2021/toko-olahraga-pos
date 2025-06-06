<?php

namespace App\Livewire\AdminPusat;

use Livewire\Component;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Branch;
use Carbon\Carbon;
use Livewire\WithPagination;

class FinancialReport extends Component
{
    use WithPagination;

    public $startDate;
    public $endDate;
    public $filterBranchId = '';

    public $branches = [];

    public $totalRevenue = 0;
    public $totalCOGS = 0;
    public $grossProfit = 0;
    // HAPUS: public $salesDetails; // Jangan simpan paginator di properti publik

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->toDateString();
        $this->endDate = Carbon::now()->endOfMonth()->toDateString();
        $this->branches = Branch::orderBy('name')->get();
        $this->calculateSummaryData(); // Hitung data ringkasan saat mount
    }

    // Fungsi ini hanya akan dipanggil saat filter berubah
    public function updatedStartDate()
    {
        $this->resetPage(); // Reset paginasi untuk data detail
        $this->calculateSummaryData();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
        $this->calculateSummaryData();
    }

    public function updatedFilterBranchId()
    {
        $this->resetPage();
        $this->calculateSummaryData();
    }

    // Fungsi untuk memicu kalkulasi ulang data ringkasan,
    // bisa juga dipanggil dari tombol jika Anda tidak menggunakan updated hooks
    public function triggerReportCalculation()
    {
        $this->resetPage(); // Reset paginasi untuk data detail
        $this->validate([ // Validasi filter sebelum kalkulasi
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
            'filterBranchId' => 'nullable|exists:branches,id',
        ]);
        $this->calculateSummaryData();
    }

    public function calculateSummaryData()
    {
        // Validasi sudah dipindahkan ke triggerReportCalculation atau bisa tetap di sini
        // jika method ini bisa dipanggil dari tempat lain juga.
        // Jika validasi ada di triggerReportCalculation, pastikan data sudah divalidasi sebelumnya.

        $startDateCarbon = Carbon::parse($this->startDate)->startOfDay();
        $endDateCarbon = Carbon::parse($this->endDate)->endOfDay();

        $salesQuery = Sale::with(['items'])
            ->whereBetween('sale_date', [$startDateCarbon, $endDateCarbon])
            ->where('status', 'completed');

        if (!empty($this->filterBranchId)) {
            $salesQuery->where('branch_id', $this->filterBranchId);
        }

        $allMatchingSales = $salesQuery->get();

        $this->totalRevenue = $allMatchingSales->sum('total_amount');
        $currentTotalCOGS = 0;
        foreach ($allMatchingSales as $sale) {
            foreach ($sale->items as $item) {
                $currentTotalCOGS += ($item->quantity * $item->purchase_price_at_sale);
            }
        }
        $this->totalCOGS = $currentTotalCOGS;
        $this->grossProfit = $this->totalRevenue - $this->totalCOGS;
    }

    public function render()
    {
        // Validasi tanggal untuk memastikan tidak error saat parsing
        // Meskipun sudah ada di calculateSummaryData, render juga bisa dipanggil sebelum filter divalidasi
        try {
            $startDateCarbon = Carbon::parse($this->startDate)->startOfDay();
            $endDateCarbon = Carbon::parse($this->endDate)->endOfDay();
        } catch (\Exception $e) {
            // Handle invalid date format, mungkin set default atau tampilkan error
            // Untuk sekarang, kita biarkan default dari mount jika ada error parsing
            $startDateCarbon = Carbon::now()->startOfMonth()->startOfDay();
            $endDateCarbon = Carbon::now()->endOfMonth()->endOfDay();
            // Anda mungkin ingin menampilkan flash message error di sini
        }


        // Query untuk detail penjualan dengan paginasi dilakukan di sini
        $paginatedSalesQuery = Sale::with(['branch', 'user', 'items.product'])
            ->whereBetween('sale_date', [$startDateCarbon, $endDateCarbon])
            ->where('status', 'completed')
            ->orderBy('sale_date', 'desc');

        if (!empty($this->filterBranchId)) {
            $paginatedSalesQuery->where('branch_id', $this->filterBranchId);
        }

        // Panggil calculateSummaryData jika belum ada data (misalnya saat load awal setelah mount)
        // atau jika Anda ingin memastikan data ringkasan selalu terupdate sebelum render.
        // Namun, karena sudah ada di mount dan updated hooks, ini mungkin tidak selalu perlu.
        // Lebih baik panggil calculateSummaryData secara eksplisit jika ada perubahan filter.
        // Untuk konsistensi, kita pastikan data ringkasan ada.
        if ($this->totalRevenue == 0 && $this->totalCOGS == 0 && $this->grossProfit == 0 && Sale::count() > 0) {
            // Hanya hitung ulang jika semua nol dan ada penjualan, menandakan mungkin belum dihitung
            $this->calculateSummaryData();
        }


        return view('livewire.admin-pusat.financial-report', [
            'salesDetails' => $paginatedSalesQuery->paginate(15),
            // totalRevenue, totalCOGS, grossProfit sudah properti publik
        ])->layout('layouts.admin-pusat');
    }
}
