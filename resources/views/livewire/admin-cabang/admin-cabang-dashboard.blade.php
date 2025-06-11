<div>
    <x-slot name="header">
        <h1 class="text-xl font-semibold leading-tight text-gray-800">
            Dashboard Admin Cabang: {{ $branch->name ?? 'Tidak Terhubung ke Cabang' }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            @if (!$branch)
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-red-600">Akun Anda tidak terhubung ke cabang manapun. Silakan hubungi Admin Pusat.</p>
                </div>
            @else
                {{-- Kartu Ringkasan Cabang --}}
                <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2">
                    <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-orange-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                                <x-heroicon-o-banknotes class="h-6 w-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 truncate">Pendapatan Cabang (Bulan Ini)</p>
                                <p class="text-2xl font-semibold text-gray-900">Rp
                                    {{ number_format($totalRevenueThisMonth, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-indigo-500">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <x-heroicon-o-shopping-cart class="h-6 w-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500 truncate">Total Transaksi (Bulan Ini)</p>
                                <p class="text-2xl font-semibold text-gray-900">
                                    {{ number_format($totalTransactionsThisMonth, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Area Grafik --}}
                <div class="mb-6" wire:ignore x-data="{
                    salesData: {{ json_encode($salesTrendData) }},
                    initChart() {
                        const chartCanvas = this.$refs.canvas;
                        if (!chartCanvas) return;
                        const ctx = chartCanvas.getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: this.salesData,
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: { y: { beginAtZero: true, ticks: { callback: (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value) } } },
                                plugins: { tooltip: { callbacks: { label: (context) => (context.dataset.label || '') + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y) } } }
                            }
                        });
                    }
                }" x-init="initChart()">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                Tren Pendapatan Harian (30 Hari Terakhir)
                            </h3>
                            <div class="h-80">
                                <canvas x-ref="canvas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Widget --}}
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <x-heroicon-o-arrow-trending-up class="h-6 w-6 mr-2 text-orange-700" />
                                Produk Terlaris di Cabang Ini (Bulan Ini)
                            </h3>
                            @if ($bestSellingProducts && $bestSellingProducts->count() > 0)
                                <ul class="divide-y divide-gray-200">
                                    @foreach ($bestSellingProducts as $product)
                                        <li class="py-3 flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-800">{{ $product->name }}</span>
                                            <span
                                                class="font-semibold text-gray-600 bg-gray-100 py-1 px-2 rounded-md text-sm">{{ $product->total_sold }}
                                                terjual</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500">Belum ada data penjualan bulan ini.</p>
                            @endif
                        </div>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                                <x-heroicon-o-exclamation-triangle class="h-6 w-6 mr-2 text-red-500" />
                                Peringatan Stok Rendah di Cabang Ini
                            </h3>
                            @if ($lowStockItems && $lowStockItems->count() > 0)
                                <ul class="divide-y divide-gray-200">
                                    @foreach ($lowStockItems as $stock)
                                        <li class="py-3 flex justify-between items-center">
                                            <p class="text-sm font-medium text-gray-800">{{ $stock->product_name }}</p>
                                            <span
                                                class="font-bold text-red-600 bg-red-100 py-1 px-2 rounded-md text-sm">Sisa
                                                {{ $stock->quantity }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500">Tidak ada produk dengan stok rendah saat ini.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
