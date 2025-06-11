<div>
    <x-slot name="header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Peramalan Permintaan per Cabang') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Generate perkiraan permintaan stok untuk semua produk di satu cabang.') }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            @if (session()->has('message'))
                <div class="mb-4 rounded-md bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p class="font-bold">Sukses</p>
                    <p>{{ session('message') }}</p>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="mb-4 rounded-md bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">Gagal</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- Form Input Peramalan --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <form wire:submit.prevent="generateForecast" class="p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Parameter Peramalan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="selectedBranchId" class="block text-sm font-medium text-gray-700">Pilih
                                Cabang</label>
                            <select wire:model.lazy="selectedBranchId" id="selectedBranchId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('selectedBranchId') border-red-500 @enderror">
                                <option value="">Pilih Cabang</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedBranchId')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="historicalMonths" class="block text-sm font-medium text-gray-700">Data Historis
                                (Bulan)</label>
                            <input wire:model.lazy="historicalMonths" type="number" id="historicalMonths"
                                min="1" max="12"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('historicalMonths') border-red-500 @enderror">
                            @error('historicalMonths')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="forecastForMonth" class="block text-sm font-medium text-gray-700">Ramalan untuk
                                Bulan</label>
                            <input wire:model.lazy="forecastForMonth" type="month" id="forecastForMonth"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('forecastForMonth') border-red-500 @enderror">
                            @error('forecastForMonth')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-orange-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                            <div wire:loading wire:target="generateForecast"
                                class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-3"></div>
                            Generate Peramalan
                        </button>
                    </div>
                </form>
            </div>

            <div wire:loading.flex wire:target="generateForecast"
                class="w-full items-center justify-center p-6 text-gray-600">
                <svg class="animate-spin h-8 w-8 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="ml-3 text-lg">Menghitung peramalan, mohon tunggu...</span>
            </div>

            @if (!empty($forecastResults))
                <div class="bg-white shadow-sm sm:rounded-lg" wire:loading.remove wire:target="generateForecast">
                    <div class="p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Hasil Peramalan untuk Cabang:
                                    {{ $branchName }}</h3>
                                <p class="text-sm text-gray-500">Periode:
                                    {{ \Carbon\Carbon::parse($forecastPeriodStartDate)->isoFormat('MMMM YYYY') }}</p>
                            </div>
                            <button wire:click="saveForecast" type="button"
                                class="mt-2 sm:mt-0 inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <div wire:loading wire:target="saveForecast"
                                    class="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-3"></div>
                                Simpan Semua Hasil
                            </button>
                        </div>

                        {{-- Kartu Ringkasan Hasil --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-md">
                                <h4 class="text-sm font-medium text-yellow-800">Produk Butuh Restock</h4>
                                <p class="text-2xl font-bold text-yellow-900">{{ $totalProductsNeedRestock }} Jenis
                                    Produk</p>
                            </div>
                            <div class="bg-orange-50 border-l-4 border-orange-400 p-4 rounded-md">
                                <h4 class="text-sm font-medium text-orange-800">Total Unit Direkomendasikan</h4>
                                <p class="text-2xl font-bold text-orange-900">
                                    {{ number_format($totalUnitsToShip, 0, ',', '.') }} Unit</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Produk</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            SKU</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Stok Saat Ini</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Permintaan Diramalkan</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Rekomendasi Kirim</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($forecastResults as $result)
                                        <tr class="{{ $result['recommendation'] > 0 ? 'bg-yellow-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $result['product_name'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $result['sku'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                                {{ $result['current_stock'] }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-semibold text-center">
                                                {{ $result['forecast_quantity'] }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm font-bold text-center {{ $result['recommendation'] > 0 ? 'text-red-600' : 'text-gray-500' }}">
                                                {{ $result['recommendation'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @elseif($statusMessage)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500" wire:loading.remove
                    wire:target="generateForecast">
                    <p>{{ $statusMessage }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
