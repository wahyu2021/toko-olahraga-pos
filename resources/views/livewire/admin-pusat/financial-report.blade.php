@php
    use Carbon\Carbon;
@endphp

<div>
    {{-- Slot untuk Header --}}
    <x-slot name="header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Laporan Keuangan (Penjualan)') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Ringkasan pendapatan, HPP, dan laba kotor dari transaksi penjualan.') }}
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Konten Utama Halaman --}}
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            {{-- Area Filter --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Filter Laporan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                        <div>
                            <label for="startDate" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                            <input wire:model.lazy="startDate" type="date" id="startDate"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('startDate')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="endDate" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input wire:model.lazy="endDate" type="date" id="endDate"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('endDate')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="filterBranchId" class="block text-sm font-medium text-gray-700">Cabang</label>
                            <select wire:model.lazy="filterBranchId" id="filterBranchId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Semua Cabang</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:pt-6">
                            <button wire:click="triggerReportCalculation" type="button"
                                class="w-full inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <svg wire:loading wire:target="triggerReportCalculation"
                                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ __('Tampilkan Laporan') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Area Ringkasan --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Total Pendapatan</h4>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">Rp
                        {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Total HPP (COGS)</h4>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">Rp {{ number_format($totalCOGS, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500">Laba Kotor</h4>
                    <p class="mt-1 text-3xl font-semibold {{ $grossProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($grossProfit, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Area Detail Transaksi --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Transaksi Penjualan</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        No. Invoice</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tanggal</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cabang</th>
                                    {{-- <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kasir</th> --}} {{-- DIHAPUS --}}
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Pendapatan
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">HPP</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Laba Kotor
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @if ($salesDetails && $salesDetails->count() > 0)
                                    @foreach ($salesDetails as $sale)
                                        @php
                                            $saleCOGS = 0;
                                            foreach ($sale->items as $item) {
                                                $saleCOGS += $item->quantity * $item->purchase_price_at_sale;
                                            }
                                            $saleGrossProfit = $sale->total_amount - $saleCOGS;
                                        @endphp
                                        <tr>
                                            <td
                                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                {{ $sale->invoice_number }}</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ Carbon::parse($sale->sale_date)->isoFormat('D MMM YYYY, HH:mm') }}
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $sale->branch->name ?? '-' }}</td>
                                            {{-- <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $sale->user->name ?? '-' }}</td> --}} {{-- DIHAPUS --}}
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-left">Rp
                                                {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-left">Rp
                                                {{ number_format($saleCOGS, 0, ',', '.') }}</td>
                                            <td
                                                class="whitespace-nowrap px-3 py-4 text-sm text-left {{ $saleGrossProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                Rp {{ number_format($saleGrossProfit, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6"
                                            class="whitespace-nowrap px-3 py-4 text-sm text-center text-gray-500">
                                            {{-- Colspan dikurangi dari 7 menjadi 6 --}}
                                            Tidak ada data transaksi penjualan untuk periode atau filter yang dipilih.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        @if ($salesDetails)
                            <div class="mt-4">
                                {{ $salesDetails->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
