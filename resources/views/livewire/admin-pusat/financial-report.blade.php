<div>
    <x-slot name="header">
        <h1 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Laporan Keuangan') }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                {{-- Form Filter --}}
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Laporan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                        <div class="lg:col-span-1">
                            <label for="startDate" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input wire:model.lazy="startDate" type="date" id="startDate"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                        </div>
                        <div class="lg:col-span-1">
                            <label for="endDate" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input wire:model.lazy="endDate" type="date" id="endDate"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                        </div>
                        <div class="lg:col-span-1">
                            <label for="filterBranchId" class="block text-sm font-medium text-gray-700">Cabang</label>
                            <select wire:model.lazy="filterBranchId" id="filterBranchId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                                <option value="">Semua Cabang</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lg:col-span-1">
                            <button wire:click="triggerReportCalculation" type="button"
                                class="w-full inline-flex items-center justify-center rounded-md border border-transparent bg-orange-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                                <div wire:loading wire:target="triggerReportCalculation"
                                    class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-3"></div>
                                Tampilkan
                            </button>
                        </div>
                        <div class="lg:col-span-1">
                            <button wire:click="exportReport" type="button"
                                class="w-full inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <div wire:loading wire:target="exportReport"
                                    class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-3"></div>
                                Export CSV
                            </button>
                        </div>
                    </div>
                </div>

                <div wire:loading.flex wire:target="triggerReportCalculation"
                    class="w-full items-center justify-center p-6 text-gray-600">
                    <svg class="animate-spin h-8 w-8 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="ml-3 text-lg">Memuat data laporan...</span>
                </div>

                <div wire:loading.remove wire:target="triggerReportCalculation">
                    @if ($reportGenerated)
                        <div class="border-t pt-6">
                            {{-- Kartu Metrik --}}
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                                <div
                                    class="relative bg-white pt-5 px-4 pb-12 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
                                    <dt>
                                        <p class="text-sm font-medium text-gray-500 truncate">Total Pendapatan</p>
                                    </dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">Rp
                                        {{ number_format($totalRevenue, 0, ',', '.') }}</dd>
                                    <div class="absolute bottom-0 inset-x-0 bg-gray-50 px-4 py-2 sm:px-6">
                                        <div
                                            class="text-sm @if ($revenueChange >= 0) text-green-600 @else text-red-600 @endif">
                                            <x-heroicon-o-arrow-trending-up class="inline h-5 w-5"
                                                style="{{ $revenueChange >= 0 ? '' : 'transform: rotate(180deg);' }}" />
                                            {{ number_format($revenueChange, 2) }}% dari periode sebelumnya
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="relative bg-white pt-5 px-4 pb-12 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
                                    <dt>
                                        <p class="text-sm font-medium text-gray-500 truncate">Laba Kotor</p>
                                    </dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">Rp
                                        {{ number_format($grossProfit, 0, ',', '.') }}</dd>
                                    <div class="absolute bottom-0 inset-x-0 bg-gray-50 px-4 py-2 sm:px-6">
                                        <div
                                            class="text-sm @if ($profitChange >= 0) text-green-600 @else text-red-600 @endif">
                                            <x-heroicon-o-arrow-trending-up class="inline h-5 w-5"
                                                style="{{ $profitChange >= 0 ? '' : 'transform: rotate(180deg);' }}" />
                                            {{ number_format($profitChange, 2) }}% dari periode sebelumnya
                                        </div>
                                    </div>
                                </div>
                                <div
                                    class="relative bg-white pt-5 px-4 pb-12 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
                                    <dt>
                                        <p class="text-sm font-medium text-gray-500 truncate">Total Transaksi</p>
                                    </dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                        {{ number_format($totalTransactions) }}</dd>
                                    <div class="absolute bottom-0 inset-x-0 bg-gray-50 px-4 py-2 sm:px-6">
                                        <div
                                            class="text-sm @if ($transactionsChange >= 0) text-green-600 @else text-red-600 @endif">
                                            <x-heroicon-o-arrow-trending-up class="inline h-5 w-5"
                                                style="{{ $transactionsChange >= 0 ? '' : 'transform: rotate(180deg);' }}" />
                                            {{ number_format($transactionsChange, 2) }}% dari periode sebelumnya
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Area Grafik --}}
                            <div class="mt-8" wire:ignore x-data="{
                                chart: null,
                                chartData: {{ json_encode($salesDataForChart) }},
                                initChart() {
                                    if (this.chart) { this.chart.destroy(); }
                                    const ctx = this.$refs.canvas.getContext('2d');
                                    const chartConfig = {
                                        type: 'line',
                                        data: this.chartData,
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            interaction: { mode: 'index', intersect: false },
                                            scales: { y: { beginAtZero: true, ticks: { callback: (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value) } } },
                                            plugins: { tooltip: { callbacks: { label: (context) => (context.dataset.label || '') + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y) } } }
                                        }
                                    };
                                    chartConfig.data.datasets.forEach(dataset => {
                                        dataset.tension = 0.2;
                                        dataset.fill = true;
                                    });
                                    this.chart = new Chart(ctx, chartConfig);
                                }
                            }" x-init="initChart()"
                                x-on:report-updated.window="$wire.get('salesDataForChart').then(data => { chartData = data; initChart(); })">
                                <div class="bg-white rounded-lg p-4 sm:p-6 shadow-sm">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Grafik Keuangan Harian</h3>
                                    <div class="h-96">
                                        <canvas x-ref="canvas"></canvas>
                                    </div>
                                </div>
                            </div>

                            {{-- Tabel Detail Transaksi --}}
                            <div class="mt-8">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Transaksi Penjualan</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col"
                                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                                    No. Invoice</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                    Tanggal</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                                    Cabang</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">
                                                    Pendapatan</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">
                                                    HPP</th>
                                                <th scope="col"
                                                    class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">
                                                    Laba Kotor</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @forelse ($salesDetails as $sale)
                                                @php
                                                    $saleCOGS = $sale->items->sum(
                                                        fn($item) => $item->quantity * $item->purchase_price_at_sale,
                                                    );
                                                    $saleGrossProfit = $sale->total_amount - $saleCOGS;
                                                @endphp
                                                <tr>
                                                    <td
                                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                        {{ $sale->invoice_number }}</td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ $sale->sale_date->isoFormat('D MMM YY, HH:mm') }}</td>
                                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                        {{ $sale->branch->name ?? '-' }}</td>
                                                    <td
                                                        class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 text-right">
                                                        Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                                    <td
                                                        class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 text-right">
                                                        Rp {{ number_format($saleCOGS, 0, ',', '.') }}</td>
                                                    <td
                                                        class="whitespace-nowrap px-3 py-4 text-sm text-right font-medium {{ $saleGrossProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                        Rp {{ number_format($saleGrossProfit, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6"
                                                        class="whitespace-nowrap px-3 py-4 text-sm text-center text-gray-500">
                                                        Tidak ada data transaksi untuk ditampilkan pada periode ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    {{ $salesDetails->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <p>Silakan klik "Tampilkan Laporan" untuk memuat data.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
