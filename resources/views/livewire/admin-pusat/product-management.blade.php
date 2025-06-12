<div>
    {{-- Slot untuk Header --}}
    <x-slot name="header">
        <h1 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Manajemen Produk') }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">

            {{-- Komponen notifikasi session yang lebih ringkas --}}
            <x-session-message />

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    {{-- Kontrol: Pencarian, Filter, dan Tombol Tambah --}}
                    <div class="mb-6 grid grid-cols-1 items-end gap-4 md:grid-cols-3">
                        <div>
                            <x-label for="search" value="{{ __('Cari Produk (Nama/SKU)') }}" />
                            <x-input wire:model.live.debounce.300ms="search" id="search" type="text"
                                class="mt-1 block w-full" placeholder="Cari..." />
                        </div>
                        <div>
                            <x-label for="filterCategory" value="{{ __('Filter Berdasarkan Kategori') }}" />
                            <select wire:model.live="filterCategory" id="filterCategory"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:text-right">
                            <x-button wire:click="addProduct()">
                                {{ __('Tambah Produk') }}
                            </x-button>
                        </div>
                    </div>

                    {{-- Tabel Produk --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Gambar</th>
                                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        <button wire:click="sortBy('name')" class="flex items-center space-x-1">
                                            <span>Nama Produk</span>
                                            @if ($sortField === 'name')
                                                <span>{{ $sortAsc ? '↑' : '↓' }}</span>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        <button wire:click="sortBy('sku')" class="flex items-center space-x-1">
                                            <span>SKU</span>
                                            @if ($sortField === 'sku')
                                                <span>{{ $sortAsc ? '↑' : '↓' }}</span>
                                            @endif
                                        </button>
                                    </th>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kategori</th>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Harga Jual
                                    </th>
                                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                    <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($products as $product)
                                    <tr wire:key="product-{{ $product->id }}">
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            @if ($product->image_path)
                                                <img src="{{ Storage::url($product->image_path) }}"
                                                    alt="{{ $product->name }}"
                                                    class="h-12 w-12 rounded-md object-cover">
                                            @else
                                                <div
                                                    class="flex h-12 w-12 items-center justify-center rounded-md bg-gray-100">
                                                    <span class="text-xs font-medium text-gray-500">N/A</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td
                                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            {{ $product->name }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $product->sku }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $product->category->name ?? '-' }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Rp
                                            {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            @if ($product->is_active)
                                                <span
                                                    class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Non-Aktif</span>
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
                                        <td colspan="7" class="p-4 text-sm text-center text-gray-500">
                                            Tidak ada produk ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($products->hasPages())
                        <div class="mt-4">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah/Edit Produk menggunakan Komponen Bawaan Jetstream --}}
    <x-dialog-modal wire:model.live="showProductModal">
        <x-slot name="title">
            {{ $isEditMode ? 'Edit Produk' : 'Tambah Produk Baru' }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-6 gap-6">
                {{-- Nama Produk --}}
                <div class="col-span-6">
                    <x-label for="name" value="Nama Produk" />
                    <x-input wire:model.defer="name" id="name" type="text" class="mt-1 block w-full" />
                    <x-input-error for="name" class="mt-2" />
                </div>
                {{-- SKU --}}
                <div class="col-span-6 sm:col-span-3">
                    <x-label for="sku" value="SKU (Kode Produk)" />
                    <x-input wire:model.defer="sku" id="sku" type="text" class="mt-1 block w-full" />
                    <x-input-error for="sku" class="mt-2" />
                </div>
                {{-- Kategori --}}
                <div class="col-span-6 sm:col-span-3">
                    <x-label for="category_id" value="Kategori" />
                    <select wire:model.defer="category_id" id="category_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="category_id" class="mt-2" />
                </div>
                {{-- Deskripsi --}}
                <div class="col-span-6">
                    <x-label for="description" value="Deskripsi" />
                    <textarea wire:model.defer="description" id="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"></textarea>
                    <x-input-error for="description" class="mt-2" />
                </div>
                {{-- Harga Beli & Jual --}}
                <div class="col-span-6 sm:col-span-3">
                    <x-label for="purchase_price" value="Harga Beli (Rp)" />
                    <x-input wire:model.defer="purchase_price" id="purchase_price" type="number"
                        class="mt-1 block w-full" />
                    <x-input-error for="purchase_price" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <x-label for="selling_price" value="Harga Jual (Rp)" />
                    <x-input wire:model.defer="selling_price" id="selling_price" type="number"
                        class="mt-1 block w-full" />
                    <x-input-error for="selling_price" class="mt-2" />
                </div>
                {{-- Supplier & Batas Stok --}}
                <div class="col-span-6 sm:col-span-3">
                    <x-label for="supplier_id" value="Supplier (Opsional)" />
                    <select wire:model.defer="supplier_id" id="supplier_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                        <option value="">Pilih Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="supplier_id" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-3">
                    <x-label for="low_stock_threshold" value="Batas Stok Rendah" />
                    <x-input wire:model.defer="low_stock_threshold" id="low_stock_threshold" type="number"
                        class="mt-1 block w-full" />
                    <x-input-error for="low_stock_threshold" class="mt-2" />
                </div>
                {{-- Upload Gambar --}}
                <div class="col-span-6">
                    <x-label for="newProductImage" value="Gambar Produk" />
                    <input wire:model="newProductImage" type="file" id="newProductImage"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    <x-input-error for="newProductImage" class="mt-2" />
                    <div wire:loading wire:target="newProductImage" class="mt-2 text-sm text-gray-500">Mengunggah...
                    </div>
                    {{-- Pratinjau Gambar --}}
                    @if ($newProductImage)
                        <img src="{{ $newProductImage->temporaryUrl() }}"
                            class="mt-4 h-32 w-32 rounded-md object-cover">
                    @elseif ($existingImagePath)
                        <img src="{{ Storage::url($existingImagePath) }}"
                            class="mt-4 h-32 w-32 rounded-md object-cover">
                    @endif
                </div>
                {{-- Status Aktif --}}
                <div class="col-span-6">
                    <div class="flex items-center">
                        <x-checkbox wire:model.defer="is_active" id="is_active" />
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Produk Aktif</label>
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">
                Batal
            </x-secondary-button>
            <x-button class="ml-3" wire:submit.prevent="{{ $isEditMode ? 'updateProduct' : 'createProduct' }}"
                wire:click="{{ $isEditMode ? 'updateProduct' : 'createProduct' }}" wire:loading.attr="disabled">
                {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Produk' }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    {{-- Modal Konfirmasi Hapus --}}
    <x-confirmation-modal wire:model.live="showDeleteModal">
        <x-slot name="title">Hapus Produk</x-slot>
        <x-slot name="content">Apakah Anda yakin ingin menghapus produk ini? Stok terkait mungkin juga akan
            terpengaruh.</x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">Batal</x-secondary-button>
            <x-danger-button class="ml-3" wire:click="deleteProduct()" wire:loading.attr="disabled">Ya,
                Hapus</x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
