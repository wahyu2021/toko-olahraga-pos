<div>
    {{-- HEADER HALAMAN --}}
    <x-slot name="header">
        <h1 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Manajemen Stok Keseluruhan') }}
        </h1>
    </x-slot>

    {{-- KONTEN UTAMA --}}
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            <div class="p-4 bg-white shadow-sm sm:rounded-lg sm:p-6">
                {{-- ... (Filter dan Tabel tetap sama seperti sebelumnya) ... --}}
                <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="searchProductStock" class="block text-sm font-medium text-gray-700">Cari Produk
                            (Nama/SKU)</label>
                        <input wire:model.live.debounce.300ms="searchProduct" id="searchProductStock" type="text"
                            placeholder="Masukkan nama atau SKU produk..."
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="filterBranchStock" class="block text-sm font-medium text-gray-700">Filter
                            Cabang</label>
                        <select wire:model.live="filterBranch" id="filterBranchStock"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                            <option value="">Semua Cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Produk</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">SKU</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cabang</th>
                                <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Kuantitas</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Update Terakhir
                                </th>
                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($stocksView as $stock)
                                <tr>
                                    <td
                                        class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 whitespace-nowrap sm:pl-6">
                                        {{ $stock->product->name ?? 'N/A' }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $stock->product->sku ?? '-' }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $stock->branch->name ?? 'N/A' }}</td>
                                    <td
                                        class="px-3 py-4 text-sm font-semibold text-center text-gray-500 whitespace-nowrap">
                                        {{ $stock->quantity }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $stock->updated_at->isoFormat('D MMM YY, HH:mm') }}</td>
                                    <td
                                        class="relative py-4 pl-3 pr-4 text-sm font-medium text-right whitespace-nowrap sm:pr-6">
                                        <button wire:click="selectStockForAdjustment({{ $stock->id }})"
                                            class="font-semibold text-orange-600 hover:text-orange-900">Sesuaikan</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="px-3 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                        Tidak ada data stok ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $stocksView->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Penyesuaian Stok (Tidak ada perubahan di sini) --}}
    @if ($showAdjustmentModal)
        <x-dialog-modal wire:model.live="showAdjustmentModal">
            {{-- ... Konten modal tetap sama ... --}}
            <x-slot name="title">
                Sesuaikan Stok: {{ $selectedStock->product->name ?? '...' }}
                <p class="text-sm font-normal text-gray-500">Cabang: {{ $selectedStock->branch->name ?? '...' }}</p>
            </x-slot>
            <x-slot name="content">
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600">Kuantitas Awal: <span
                                class="font-bold">{{ $currentQuantity }}</span></p>
                    </div>
                    <div>
                        <label for="adjustment" class="block text-sm font-medium text-gray-700">Nilai
                            Penyesuaian</label>
                        <input type="number" wire:model.live="adjustmentValue" id="adjustment"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            placeholder="Contoh: -5 atau 10">
                        <p class="mt-1 text-xs text-gray-500">Gunakan nilai negatif (-) untuk mengurangi, dan positif
                            (+) untuk menambah stok.</p>
                        <x-input-error for="adjustmentValue" class="mt-2" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Kuantitas Baru: <span
                                class="text-xl font-bold text-orange-600">{{ $newQuantity }}</span></p>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Catatan/Alasan
                            Penyesuaian</label>
                        <textarea wire:model.defer="notes" id="notes" rows="3"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            placeholder="Contoh: Hasil stok opname, barang rusak, dll."></textarea>
                        <x-input-error for="notes" class="mt-2" />
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">Batal</x-secondary-button>
                <x-button class="ml-3" wire:click="adjustStock" wire:loading.attr="disabled">
                    Simpan Penyesuaian
                </x-button>
            </x-slot>
        </x-dialog-modal>
    @endif

    <div x-data="{ show: false, message: '', type: '' }"
        x-on:show-notification.window="
            message = $event.detail.message;
            type = $event.detail.type;
            show = true;
            setTimeout(() => show = false, 4000)
        "
        x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2" style="display: none;"
        class="fixed z-50 w-full max-w-sm top-5 right-5">
        <div class="p-4 rounded-lg shadow-lg"
            :class="{
                'bg-green-100 text-green-800 border border-green-200': type === 'success',
                'bg-red-100 text-red-800 border border-red-200': type === 'error',
            }">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-check-circle x-show="type === 'success'" class="w-6 h-6 text-green-600" />
                    <x-heroicon-o-x-circle x-show="type === 'error'" class="w-6 h-6 text-red-600" />
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium" x-text="message"></p>
                </div>
            </div>
        </div>
    </div>
</div>
