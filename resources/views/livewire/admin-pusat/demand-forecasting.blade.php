<div>
    {{-- Slot untuk Header --}}
    <x-slot name="header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Peramalan Permintaan (Demand Forecasting)') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Generate perkiraan permintaan stok produk untuk periode mendatang.') }}
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Konten Utama Halaman --}}
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            @if (session()->has('message'))
                <div class="mb-4 rounded-md bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0"><svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg></div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="mb-4 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0"><svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg></div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form Input Peramalan --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6">
                <form wire:submit.prevent="generateForecast" class="p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Parameter Peramalan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label for="selectedProductId"
                                class="block text-sm font-medium text-gray-700">Produk</label>
                            <select wire:model.lazy="selectedProductId" id="selectedProductId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('selectedProductId') border-red-500 @enderror">
                                <option value="">Pilih Produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedProductId')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="selectedBranchId" class="block text-sm font-medium text-gray-700">Cabang</label>
                            <select wire:model.lazy="selectedBranchId" id="selectedBranchId"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('selectedBranchId') border-red-500 @enderror">
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
                            <label for="historicalMonths" class="block text-sm font-medium text-gray-700">Bulan Data
                                Historis (SMA)</label>
                            <input wire:model.lazy="historicalMonths" type="number" id="historicalMonths"
                                min="1" max="12"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('historicalMonths') border-red-500 @enderror">
                            @error('historicalMonths')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="forecastForMonth" class="block text-sm font-medium text-gray-700">Ramalan untuk
                                Bulan</label>
                            <input wire:model.lazy="forecastForMonth" type="month" id="forecastForMonth"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('forecastForMonth') border-red-500 @enderror">
                            @error('forecastForMonth')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <svg wire:loading wire:target="generateForecast"
                                class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Generate Peramalan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Hasil Peramalan --}}
            @if ($statusMessage)
                <div
                    class="mb-4 rounded-md {{ $forecastedQuantity !== null && $forecastedQuantity >= 0 ? 'bg-blue-50 text-blue-700' : 'bg-yellow-50 text-yellow-700' }} p-4">
                    <p class="text-sm">{{ $statusMessage }}</p>
                </div>
            @endif

            @if ($forecastedQuantity !== null || $calculationDetails)
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Hasil Peramalan</h3>
                        @if ($calculationDetails)
                            <p class="text-sm text-gray-600">Produk: <span
                                    class="font-semibold">{{ $calculationDetails['product'] }}</span></p>
                            <p class="text-sm text-gray-600">Cabang: <span
                                    class="font-semibold">{{ $calculationDetails['branch'] }}</span></p>
                            <p class="text-sm text-gray-600">Periode Ramalan: <span
                                    class="font-semibold">{{ $forecastPeriodStartDate ? Carbon\Carbon::parse($forecastPeriodStartDate)->isoFormat('MMMM YYYY') : '-' }}</span>
                            </p>
                        @endif

                        @if ($forecastedQuantity !== null)
                            <div class="mt-4">
                                <p class="text-sm text-gray-500">Perkiraan Kuantitas Permintaan:</p>
                                <p class="text-3xl font-semibold text-blue-600">{{ $forecastedQuantity }} unit</p>
                            </div>
                            <div class="mt-6">
                                <button wire:click="saveForecast" type="button"
                                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    <svg wire:loading wire:target="saveForecast"
                                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Simpan Hasil Peramalan
                                </button>
                            </div>
                        @endif

                        @if ($calculationDetails && !empty($calculationDetails['historical_data']))
                            <div class="mt-6">
                                <h4 class="text-md font-medium text-gray-800 mb-2">Data Historis Penjualan
                                    ({{ $historicalMonths }} bulan terakhir):</h4>
                                <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                    @foreach ($calculationDetails['historical_data'] as $data)
                                        <li>{{ $data['period'] }}: {{ $data['sales'] }} unit</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
