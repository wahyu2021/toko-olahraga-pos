<div>
    {{-- Slot untuk Header --}}
    <x-slot name="header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Dashboard Admin Pusat') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Ringkasan umum operasional dan penjualan.') }}
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Konten Utama Halaman --}}
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            {{-- Kartu Ringkasan --}}
            <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Total Pendapatan Bulan Ini --}}
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <x-heroicon-o-currency-dollar class="h-6 w-6 text-white" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 truncate">Pendapatan Bulan Ini</p>
                            <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($totalRevenueThisMonth, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Jumlah Produk Aktif --}}
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <x-heroicon-o-cube class="h-6 w-6 text-white" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 truncate">Produk Aktif</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $activeProductsCount }}</p>
                        </div>
                    </div>
                </div>

                {{-- Jumlah Cabang --}}
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <x-heroicon-o-building-storefront class="h-6 w-6 text-white" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 truncate">Total Cabang</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $totalBranchesCount }}</p>
                        </div>
                    </div>
                </div>

                {{-- Jumlah Pengguna --}}
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <x-heroicon-o-users class="h-6 w-6 text-white" />
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 truncate">Total Pengguna</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $totalActiveUsersCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Area Grafik --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                {{-- Grafik Tren Penjualan --}}
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tren Pendapatan Harian (30 Hari Terakhir)</h3>
                        {{-- {{ Atur tinggi canvas container }} --}}
                        <div class="h-80"> 
                            <canvas id="salesTrendChart" wire:ignore></canvas>
                        </div>
                    </div>
                </div>

                {{-- Grafik Distribusi Penjualan per Cabang --}}
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Distribusi Penjualan per Cabang (Bulan Ini)</h3>
                        {{-- {{ Atur tinggi dan pusatkan canvas }} --}}
                         <div class="h-80 flex justify-center items-center"> 
                            {{-- {{ Batasi ukuran pie chart agar tidak terlalu besar }} --}}
                            <canvas id="salesByBranchChart" wire:ignore style="max-width: 320px; max-height: 320px;"></canvas> 
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    // Sales Trend Chart
    let salesTrendChartInstance = null;
    const salesTrendCtx = document.getElementById('salesTrendChart');
    if (salesTrendCtx) {
        salesTrendChartInstance = new Chart(salesTrendCtx, {
            type: 'line',
            data: {
                labels: [], // Akan diisi oleh Livewire
                datasets: [{
                    label: 'Pendapatan Harian',
                    data: [], // Akan diisi oleh Livewire
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    document.addEventListener('livewire:init', () => {
        Livewire.on('salesTrendChartUpdated', (event) => {
            if (salesTrendChartInstance && event[0]) { // event[0] berisi data chart
                salesTrendChartInstance.data.labels = event[0].labels;
                salesTrendChartInstance.data.datasets = event[0].datasets;
                salesTrendChartInstance.update();
            }
        });
    });

    // Sales by Branch Chart
    let salesByBranchChartInstance = null;
    const salesByBranchCtx = document.getElementById('salesByBranchChart');
    if (salesByBranchCtx) {
        salesByBranchChartInstance = new Chart(salesByBranchCtx, {
            type: 'pie',
            data: {
                labels: [], // Akan diisi oleh Livewire
                datasets: [{
                    label: 'Distribusi Penjualan',
                    data: [], // Akan diisi oleh Livewire
                    backgroundColor: [],
                    borderColor: [],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Penting untuk kontrol ukuran via CSS container
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                         callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                     label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed);
                                }
                                return label;
                            },
                            // Jika ingin menampilkan persentase
                            // afterLabel: function(context) {
                            //     const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            //     const value = context.parsed;
                            //     const percentage = total > 0 ? ((value / total) * 100).toFixed(2) + '%' : '0%';
                            //     return ' (' + percentage + ')';
                            // }
                        }
                    }
                }
            }
        });
    }
    document.addEventListener('livewire:init', () => {
         Livewire.on('salesByBranchChartUpdated', (event) => {
            if (salesByBranchChartInstance && event[0]) { // event[0] berisi data chart
                salesByBranchChartInstance.data.labels = event[0].labels;
                salesByBranchChartInstance.data.datasets = event[0].datasets;
                salesByBranchChartInstance.update();
            }
        });
    });

    // Jika Anda ingin me-refresh data chart saat komponen Livewire di-refresh (misalnya setelah mount)
    // Anda mungkin perlu memanggil dispatch event dari mount juga.
    // Di atas sudah ditambahkan this->dispatch di mount dan prepareChart methods.

</script>
@endpush