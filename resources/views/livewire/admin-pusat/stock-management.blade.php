<div>
    {{-- Slot untuk Header --}}
    <x-slot name="header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Manajemen Stok') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Lihat dan sesuaikan kuantitas stok produk per cabang.') }}
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
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                        <div>
                            <label for="searchProductStock" class="block text-sm font-medium text-gray-700">Cari Produk
                                (Nama/SKU)</label>
                            <input wire:model.live.debounce.300ms="searchProduct" id="searchProductStock" type="text"
                                placeholder="Masukkan nama atau SKU produk..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="filterBranchStock" class="block text-sm font-medium text-gray-700">Filter
                                Cabang</label>
                            <select wire:model.live="filterBranch" id="filterBranchStock"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                                <option value="">Semua Cabang</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lg:col-span-2 md:text-right">
                            <button wire:click="openStockModal()" type="button"
                                class="inline-flex items-center justify-center rounded-md border border-transparent bg-orange-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 sm:w-auto">
                                {{ __('Tambah / Sesuaikan Stok') }}
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Gambar</th>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        <button wire:click="sortBy('products.name')" class="flex items-center">Produk
                                            @if ($sortField === 'products.name')<span
                                                    class="ml-1">
                                                    @if ($sortAsc)
                                                        &uarr;
                                                    @else
                                                        &darr;
                                                    @endif
                                                </span>@endif
                                        </button>
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        <button wire:click="sortBy('products.sku')" class="flex items-center">SKU
                                            @if ($sortField === 'products.sku')<span
                                                    class="ml-1">
                                                    @if ($sortAsc)
                                                        &uarr;
                                                    @else
                                                        &darr;
                                                    @endif
                                                </span>@endif
                                        </button>
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        <button wire:click="sortBy('branches.name')" class="flex items-center">Cabang
                                            @if ($sortField === 'branches.name')<span
                                                    class="ml-1">
                                                    @if ($sortAsc)
                                                        &uarr;
                                                    @else
                                                        &darr;
                                                    @endif
                                                </span>@endif
                                        </button>
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        <button wire:click="sortBy('quantity')" class="flex items-center">Kuantitas
                                            @if ($sortField === 'quantity')<span
                                                    class="ml-1">
                                                    @if ($sortAsc)
                                                        &uarr;
                                                    @else
                                                        &darr;
                                                    @endif
                                                </span>@endif
                                        </button>
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        <button wire:click="sortBy('last_restock_date')" class="flex items-center">Stok
                                            Ulang Terakhir @if ($sortField === 'last_restock_date')<span class="ml-1">
                                                    @if ($sortAsc)
                                                        &uarr;
                                                    @else
                                                        &darr;
                                                    @endif
                                                </span>@endif
                                        </button>
                                    </th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span
                                            class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($stocksView as $stock)
                                    <tr>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            @if ($stock->product && $stock->product->image_path)
                                                <img src="{{ Storage::url($stock->product->image_path) }}"
                                                    alt="{{ $stock->product->name }}"
                                                    class="h-10 w-10 object-cover rounded-md">
                                            @else
                                                <span class="text-xs text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td
                                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            {{ $stock->product->name ?? 'Produk Dihapus' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $stock->product->sku ?? '-' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $stock->branch->name ?? 'Cabang Dihapus' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-semibold">
                                            {{ $stock->quantity }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $stock->last_restock_date ? \Carbon\Carbon::parse($stock->last_restock_date)->isoFormat('D MMM YY') : '-' }}
                                        </td>
                                        <td
                                            class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <button
                                                wire:click="openStockModal({{ $stock->product_id }}, {{ $stock->branch_id }})"
                                                class="text-orange-600 hover:text-orange-900">Sesuaikan</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8"
                                            class="whitespace-nowrap px-3 py-4 text-sm text-center text-gray-500">
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

            {{-- Modal Tambah/Sesuaikan Stok --}}
            @if ($showStockModal)
                <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                    aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true">
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                            aria-hidden="true">&#8203;</span>
                        <div
                            class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                            <form wire:submit.prevent="saveStock">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    {{ $stock_id || ($product_id && $branch_id && $currentStockQuantity > 0) ? 'Sesuaikan Kuantitas Stok' : 'Tambah Stok Awal / Pembelian' }}
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="modal-product_id"
                                            class="block text-sm font-medium text-gray-700">Produk</label>
                                        <select wire:model.live="product_id" id="modal-product_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('product_id') border-red-500 @enderror"
                                            {{ $stock_id || ($product_id && $branch_id) ? 'disabled' : '' }}>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}
                                                    ({{ $product->sku }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('product_id')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="modal-branch_id"
                                            class="block text-sm font-medium text-gray-700">Cabang</label>
                                        <select wire:model.live="branch_id" id="modal-branch_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('branch_id') border-red-500 @enderror"
                                            {{ $stock_id || ($product_id && $branch_id) ? 'disabled' : '' }}>
                                            <option value="">Pilih Cabang</option>
                                            @foreach ($branches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('branch_id')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    @if ($product_id && $branch_id)
                                        <div class="text-sm text-gray-600">Stok Saat Ini: <span
                                                class="font-semibold">{{ $currentStockQuantity }}</span></div>
                                    @endif

                                    <div>
                                        <label for="modal-quantity"
                                            class="block text-sm font-medium text-gray-700">Kuantitas Baru</label>
                                        <input wire:model.defer="quantity" type="number" id="modal-quantity"
                                            placeholder="Masukkan kuantitas akhir"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('quantity') border-red-500 @enderror">
                                        @error('quantity')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="modal-movement_type"
                                            class="block text-sm font-medium text-gray-700">Jenis Pergerakan</label>
                                        <select wire:model.defer="movement_type" id="modal-movement_type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('movement_type') border-red-500 @enderror">
                                            <option value="initial"
                                                {{ $stock_id || ($product_id && $branch_id && $currentStockQuantity > 0) ? 'disabled' : '' }}>
                                                Stok Awal (Initial)</option>
                                            <option value="set_quantity">Set Kuantitas Absolut</option>
                                        </select>
                                        @error('movement_type')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                        <p class="mt-1 text-xs text-gray-500">Pilih 'Stok Awal' hanya jika produk ini
                                            baru pertama kali masuk. Untuk lainnya, gunakan 'Set Kuantitas Absolut'.</p>
                                    </div>

                                    <div>
                                        <label for="modal-notes"
                                            class="block text-sm font-medium text-gray-700">Catatan / Alasan</label>
                                        <textarea wire:model.defer="notes" id="modal-notes" rows="3"
                                            placeholder="Contoh: Stok opname mingguan, Penerimaan barang dari supplier X, dll."
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('notes') border-red-500 @enderror"></textarea>
                                        @error('notes')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-8 sm:flex sm:flex-row-reverse">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Simpan Stok
                                    </button>
                                    <button wire:click="closeModal()" type="button"
                                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:w-auto sm:text-sm">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
