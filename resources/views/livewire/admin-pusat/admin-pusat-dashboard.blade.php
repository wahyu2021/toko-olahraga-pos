<div>
    {{-- Slot untuk Header --}}
    <x-slot name="header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold leading-tight text-gray-800">
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

            {{-- Kartu Ringkasan (Menggunakan Komponen) --}}
            <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-4">
                <x-dashboard.stat-card title="Pendapatan Bulan Ini"
                    value="Rp {{ number_format($totalRevenueThisMonth, 0, ',', '.') }}" icon="banknotes" color="orange" />

                <x-dashboard.stat-card title="Produk Aktif" :value="$activeProductsCount" icon="cube" color="green" />

                <x-dashboard.stat-card title="Total Cabang" :value="$totalBranchesCount" icon="building-storefront"
                    color="yellow" />

                <x-dashboard.stat-card title="Total Pengguna" :value="$totalActiveUsersCount" icon="users" color="indigo" />
            </div>


            <div class="grid grid-cols-1 gap-6 mb-6 lg:grid-cols-2">
                {{-- Widget Produk Terlaris (Menggunakan Komponen) --}}
                <x-dashboard.widget-card title="Produk Terlaris (Bulan Ini)" icon="arrow-trending-up" color="orange">
                    @forelse ($bestSellingProducts as $product)
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-800">{{ $product->name }}</span>
                            <span class="font-semibold text-gray-600 bg-gray-100 py-1 px-2 rounded-md text-sm">
                                {{ $product->total_sold }} terjual
                            </span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">Belum ada data penjualan bulan ini.</li>
                    @endforelse
                </x-dashboard.widget-card>

                {{-- Widget Peringatan Stok Rendah (Menggunakan Komponen) --}}
                <x-dashboard.widget-card title="Peringatan Stok Rendah" icon="exclamation-triangle" color="red">
                    @forelse ($lowStockItems as $stock)
                        <li class="py-3 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $stock->product_name }}</p>
                                <p class="text-xs text-gray-500">di {{ $stock->branch_name }}</p>
                            </div>
                            <span class="font-bold text-red-600 bg-red-100 py-1 px-2 rounded-md text-sm">
                                Sisa {{ $stock->quantity }}
                            </span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">Tidak ada produk dengan stok rendah saat ini. Bagus!</li>
                    @endforelse
                </x-dashboard.widget-card>
            </div>

            {{-- Area Grafik --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2" wire:ignore x-data="{
                salesTrendData: @js($salesTrendData),
                salesByBranchData: @js($salesByBranchData),
            
                init() {
                    if (window.salesTrendChartInstance) window.salesTrendChartInstance.destroy();
                    if (window.salesByBranchChartInstance) window.salesByBranchChartInstance.destroy();
            
                    // Ganti warna grafik Line
                    this.salesTrendData.datasets[0].borderColor = 'rgb(249, 115, 22)'; // Orange-500
                    this.salesTrendData.datasets[0].backgroundColor = 'rgba(249, 115, 22, 0.1)';
            
                    const trendCtx = this.$refs.salesTrendChart.getContext('2d');
                    window.salesTrendChartInstance = new Chart(trendCtx, {
                        type: 'line',
                        data: this.salesTrendData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: { y: { beginAtZero: true, ticks: { callback: (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value) } } },
                            plugins: { tooltip: { callbacks: { label: (context) => (context.dataset.label || '') + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y) } } }
                        }
                    });
            
                    // Ganti warna grafik Pie
                    this.salesByBranchData.datasets[0].backgroundColor = [
                        'rgba(249, 115, 22, 0.8)', // orange-500
                        'rgba(234, 88, 12, 0.8)', // orange-600
                        'rgba(194, 65, 12, 0.8)', // orange-700
                        'rgba(251, 146, 60, 0.8)', // orange-400
                        'rgba(253, 186, 116, 0.8)', // orange-300
                        'rgba(124, 45, 18, 0.8)', // orange-900
                    ];
            
                    const branchCtx = this.$refs.salesByBranchChart.getContext('2d');
                    window.salesByBranchChartInstance = new Chart(branchCtx, {
                        type: 'pie',
                        data: this.salesByBranchData,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'top' }, tooltip: { callbacks: { label: (context) => context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed) } } }
                        }
                    });
                }
            }">
                {{-- Grafik Tren Penjualan --}}
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tren Pendapatan Harian (30 Hari Terakhir)
                        </h3>
                        <div class="h-80">
                            <canvas x-ref="salesTrendChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Grafik Distribusi Penjualan per Cabang --}}
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Distribusi Penjualan per Cabang (Bulan Ini)
                        </h3>
                        <div class="h-80 flex justify-center items-center">
                            <canvas x-ref="salesByBranchChart" style="max-width: 320px; max-height: 320px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
