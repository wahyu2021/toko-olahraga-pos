<div>
    <x-slot name="header">
        <h1 class="text-xl font-semibold leading-tight text-gray-800">
            Manajemen Stok - Cabang {{ Auth::user()->branch->name ?? 'N/A' }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">

            {{-- Komponen untuk menampilkan pesan session (sukses/error) --}}
            <x-session-message />

            <div class="p-6 bg-white shadow-sm sm:rounded-lg">

                {{-- Tombol untuk membuka modal Tambah Stok --}}
                <div class="flex justify-start mb-4">
                    <x-button wire:click="openAddStockModal">
                        <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                        Tambah Stok (Transfer Dari Pusat)
                    </x-button>
                </div>

                {{-- Kolom Pencarian --}}
                <div class="mb-6">
                    <label for="searchProductStock" class="block text-sm font-medium text-gray-700">Cari Produk
                        (Nama/SKU)</label>
                    <div class="mt-1">
                        <input wire:model.live.debounce.300ms="searchProduct" id="searchProductStock" type="text"
                            placeholder="Masukkan nama atau SKU produk untuk mencari..."
                            class="block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                    </div>
                </div>
                
                {{-- Tabel Daftar Stok --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produk
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kuantitas Saat Ini
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Update Terakhir
                                </th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($stocks as $stock)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-12 h-12">
                                                @if ($stock->product && $stock->product->image_path)
                                                    <img class="object-cover w-12 h-12 rounded-md"
                                                        src="{{ Storage::url($stock->product->image_path) }}"
                                                        alt="{{ $stock->product->name }}">
                                                @else
                                                    <div
                                                        class="flex items-center justify-center w-12 h-12 bg-gray-200 rounded-md">
                                                        <x-heroicon-o-photo class="w-8 h-8 text-gray-400" />
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $stock->product->name ?? 'Produk Dihapus' }}</div>
                                                <div class="text-sm text-gray-500">{{ $stock->product->sku ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                        <span
                                            class="text-lg font-bold {{ $stock->quantity <= ($stock->product->low_stock_threshold ?? 5) ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $stock->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $stock->updated_at->isoFormat('D MMM YY, HH:mm') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
                                        <button wire:click="selectStockForAdjustment({{ $stock->id }})"
                                            class="font-semibold text-orange-600 hover:text-orange-900">
                                            Sesuaikan Stok
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        Tidak ada data stok yang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $stocks->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- =================================== --}}
    {{-- MODAL UNTUK TAMBAH STOK (TRANSFER)  --}}
    {{-- =================================== --}}
    @if ($showAddStockModal)
        <x-dialog-modal wire:model.live="showAddStockModal">
            <x-slot name="title">
                Tambah Stok (Transfer Dari Gudang Pusat)
            </x-slot>

            <x-slot name="content">
                <div class="space-y-4">
                    {{-- Dropdown Pilihan Produk --}}
                    <div>
                        <label for="productId" class="block text-sm font-medium text-gray-700">Produk</label>
                        <select id="productId" wire:model.live="productId"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                            <option value="">-- Pilih Produk --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error for="productId" class="mt-2" />
                    </div>

                    {{-- Input Kuantitas --}}
                    @if ($productId)
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah
                                Transfer</label>
                            <input type="number" id="quantity" wire:model.defer="quantity" min="1"
                                max="{{ $maxQuantity }}"
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                                placeholder="Masukkan jumlah stok">

                            <p class="mt-2 text-sm text-gray-500">
                                Stok tersedia di Gudang Pusat: <strong
                                    class="text-blue-600">{{ $maxQuantity ?? 0 }}</strong>
                            </p>
                            <x-input-error for="quantity" class="mt-2" />
                        </div>
                    @endif

                    {{-- Textarea Catatan --}}
                    <div>
                        <label for="addStockNotes" class="block text-sm font-medium text-gray-700">Catatan
                            (Opsional)</label>
                        <textarea wire:model.defer="addStockNotes" id="addStockNotes" rows="3"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            placeholder="Contoh: Permintaan stok bulanan"></textarea>
                        <x-input-error for="addStockNotes" class="mt-2" />
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">Batal</x-secondary-button>
                <x-button class="ml-3" wire:click="addStock" wire:loading.attr="disabled">
                    Simpan Transfer
                </x-button>
            </x-slot>
        </x-dialog-modal>
    @endif

    {{-- =================================== --}}
    {{-- MODAL UNTUK PENYESUAIAN STOK        --}}
    {{-- =================================== --}}
    @if ($showAdjustmentModal)
        <x-dialog-modal wire:model.live="showAdjustmentModal">
            <x-slot name="title">
                Sesuaikan Stok: {{ $selectedStock->product->name ?? '' }}
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
                        <p class="mt-1 text-xs text-gray-500">Gunakan nilai negatif (-) untuk mengurangi stok, nilai
                            positif (+) untuk menambah.</p>
                        <x-input-error for="adjustmentValue" class="mt-2" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Kuantitas Baru: <span
                                class="text-xl font-bold text-orange-600">{{ $newQuantity }}</span></p>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Catatan/Alasan
                            Penyesuaian</label>
                        <textarea wire:model.defer="adjustmentNotes" id="notes" rows="3"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            placeholder="Contoh: Stok opname, barang rusak, dll."></textarea>
                        <x-input-error for="adjustmentNotes" class="mt-2" />
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">Batal</x-secondary-button>
                <x-button class="ml-3" wire:click="updateStock" wire:loading.attr="disabled">
                    Simpan Penyesuaian
                </x-button>
            </x-slot>
        </x-dialog-modal>
    @endif
</div>
