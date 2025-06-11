{{-- resources/views/livewire/product-management.blade.php --}}

<div>
    {{-- Slot untuk Header --}}
    <x-slot name="header">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ __('Manajemen Produk') }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Kelola semua data produk dalam sistem.') }}
                </p>
            </div>
        </div>
    </x-slot>

    {{-- Konten Utama Halaman --}}
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">

            {{-- ... (Pesan Flash Message Anda tetap di sini) ... --}}
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
            @if (session()->has('error'))
                <div class="mb-4 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    {{-- ... (Area Kontrol: Pencarian, Filter Kategori, dan Tombol Tambah Anda tetap di sini) ... --}}
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                        <div>
                            <label for="searchProduct" class="block text-sm font-medium text-gray-700">Cari
                                Produk</label>
                            <input wire:model.live.debounce.300ms="search" id="searchProduct" type="text"
                                placeholder="Nama, SKU..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="filterCategory" class="block text-sm font-medium text-gray-700">Filter
                                Kategori</label>
                            <select wire:model.live="filterCategory" id="filterCategory"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lg:col-span-2 md:text-right">
                            <button wire:click="addProduct()" type="button"
                                class="inline-flex items-center justify-center rounded-md border border-transparent bg-orange-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 sm:w-auto">
                                {{ __('Tambah Produk') }}
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
                                        <button wire:click="sortBy('name')" class="flex items-center">Nama Produk
                                            @if ($sortField === 'name')<span
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
                                        <button wire:click="sortBy('sku')" class="flex items-center">SKU @if ($sortField === 'sku')<span
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
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kategori</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Supplier</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Harga Beli
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Harga Jual
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span
                                            class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($products as $product)
                                    <tr>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            @if ($product->image_path)
                                                <img src="{{ Storage::url($product->image_path) }}"
                                                    alt="{{ $product->name }}"
                                                    class="h-10 w-10 object-cover rounded-md">
                                            @else
                                                <span class="text-xs text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                        <td
                                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            {{ $product->name }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $product->sku }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $product->category->name ?? '-' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $product->supplier->name ?? '-' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-left">Rp
                                            {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-left">Rp
                                            {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            @if ($product->is_active)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td
                                            class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <button wire:click="editProduct({{ $product->id }})"
                                                class="text-orange-600 hover:text-orange-900">Edit</button>
                                            <button wire:click="confirmDelete({{ $product->id }})"
                                                class="ml-4 text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9"
                                            class="whitespace-nowrap px-3 py-4 text-sm text-center text-gray-500">
                                            Tidak ada produk ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>

            @if ($showProductModal)
                <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
                    aria-modal="true">
                    <div
                        class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true">
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                            aria-hidden="true">&#8203;</span>
                        <div
                            class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                            <form wire:submit.prevent="{{ $isEditMode ? 'updateProduct' : 'createProduct' }}">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    {{ $isEditMode ? 'Edit Produk' : 'Tambah Produk Baru' }}
                                </h3>
                                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                    <div class="sm:col-span-6">
                                        <label for="product-name" class="block text-sm font-medium text-gray-700">Nama
                                            Produk</label>
                                        <input wire:model.defer="name" type="text" id="product-name"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('name') border-red-500 @enderror">
                                        @error('name')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="product-sku" class="block text-sm font-medium text-gray-700">SKU
                                            (Kode Produk)</label>
                                        <input wire:model.defer="sku" type="text" id="product-sku"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('sku') border-red-500 @enderror">
                                        @error('sku')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="product-category"
                                            class="block text-sm font-medium text-gray-700">Kategori</label>
                                        <select wire:model.defer="category_id" id="product-category"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('category_id') border-red-500 @enderror">
                                            <option value="">Pilih Kategori</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-6">
                                        <label for="product-description"
                                            class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                        <textarea wire:model.defer="description" id="product-description" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('description') border-red-500 @enderror"></textarea>
                                        @error('description')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="product-supplier"
                                            class="block text-sm font-medium text-gray-700">Supplier (Opsional)</label>
                                        <select wire:model.defer="supplier_id" id="product-supplier"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('supplier_id') border-red-500 @enderror">
                                            <option value="">Pilih Supplier</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="product-newProductImage"
                                            class="block text-sm font-medium text-gray-700">Gambar Produk</label>
                                        <input wire:model="newProductImage" type="file"
                                            id="product-newProductImage"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 @error('newProductImage') border-red-500 @enderror">
                                        @error('newProductImage')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror

                                        <div wire:loading wire:target="newProductImage"
                                            class="mt-1 text-sm text-gray-500">Uploading...</div>

                                        @if ($newProductImage)
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500">Pratinjau Gambar Baru:</p>
                                                <img src="{{ $newProductImage->temporaryUrl() }}"
                                                    alt="Pratinjau Gambar Baru"
                                                    class="mt-1 h-20 w-20 object-cover rounded-md">
                                            </div>
                                        @elseif ($existingImagePath)
                                            <div class="mt-2">
                                                <p class="text-xs text-gray-500">Gambar Saat Ini:</p>
                                                <img src="{{ Storage::url($existingImagePath) }}" alt="Gambar Produk"
                                                    class="mt-1 h-20 w-20 object-cover rounded-md">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="product-purchase_price"
                                            class="block text-sm font-medium text-gray-700">Harga Beli</label>
                                        <div class="relative mt-1 rounded-md shadow-sm">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input wire:model.defer="purchase_price" type="number" step="any"
                                                id="product-purchase_price"
                                                class="block w-full rounded-md border-gray-300 pl-10 pr-3 focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('purchase_price') border-red-500 @enderror"
                                                placeholder="0">
                                        </div>
                                        @error('purchase_price')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="product-selling_price"
                                            class="block text-sm font-medium text-gray-700">Harga Jual</label>
                                        <div class="relative mt-1 rounded-md shadow-sm">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm">Rp</span>
                                            </div>
                                            <input wire:model.defer="selling_price" type="number" step="any"
                                                id="product-selling_price"
                                                class="block w-full rounded-md border-gray-300 pl-10 pr-3 focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('selling_price') border-red-500 @enderror"
                                                placeholder="0">
                                        </div>
                                        @error('selling_price')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label for="product-low_stock_threshold"
                                            class="block text-sm font-medium text-gray-700">Batas Stok Rendah</label>
                                        <input wire:model.defer="low_stock_threshold" type="number"
                                            id="product-low_stock_threshold"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('low_stock_threshold') border-red-500 @enderror"
                                            placeholder="0">
                                        @error('low_stock_threshold')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="sm:col-span-3 flex items-end">
                                        <div class="flex items-center">
                                            <input wire:model.defer="is_active" id="product-is_active"
                                                type="checkbox"
                                                class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                            <label for="product-is_active"
                                                class="ml-2 block text-sm text-gray-900">Produk Aktif</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-8 sm:flex sm:flex-row-reverse">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Produk' }}
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

            @if ($showDeleteModal)
                <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title-delete" role="dialog"
                    aria-modal="true">
                    <div
                        class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true">
                        </div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                            aria-hidden="true">&#8203;</span>
                        <div
                            class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                            <div>
                                <div
                                    class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                                    <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-5">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-delete">
                                        Hapus Produk</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus produk ini?
                                            Stok terkait mungkin juga akan terpengaruh. Tindakan ini tidak dapat
                                            dibatalkan.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse">
                                <button wire:click="deleteProduct()" type="button"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">Ya,
                                    Hapus</button>
                                <button wire:click="closeModal()" type="button"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:w-auto sm:text-sm">Batal</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
