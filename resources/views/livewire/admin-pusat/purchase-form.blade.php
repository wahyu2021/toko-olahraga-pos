<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Form Pembelian & Penambahan Produk Baru') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">

                {{-- Notifikasi --}}
                @if (session()->has('message'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded relative mb-4 shadow"
                        role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded relative mb-4 shadow"
                        role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <form wire:submit.prevent="savePurchase">
                    {{-- Informasi Utama Pembelian (Layout Disesuaikan) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200">
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier <span
                                    class="text-red-500">*</span></label>
                            <select id="supplier_id" wire:model.lazy="supplier_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="purchase_date" class="block text-sm font-medium text-gray-700">Tanggal Pembelian
                                <span class="text-red-500">*</span></label>
                            <input type="date" id="purchase_date" wire:model.lazy="purchase_date"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            @error('purchase_date')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="invoice_number" class="block text-sm font-medium text-gray-700">Nomor Invoice
                                (Opsional)</label>
                            <input type="text" id="invoice_number" wire:model.lazy="invoice_number"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700">Estimasi
                                Tiba (Opsional)</label>
                            <input type="date" id="expected_delivery_date" wire:model.lazy="expected_delivery_date"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        {{-- Dropdown Cabang Tujuan SUDAH DIHAPUS DARI SINI --}}
                    </div>

                    {{-- Detail Item Pembelian --}}
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Produk Dibeli</h3>
                        <div class="space-y-6">
                            @foreach ($orderProducts as $index => $item)
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200"
                                    wire:key="product-row-{{ $index }}">
                                    <div class="flex justify-end mb-2">
                                        @if (count($orderProducts) > 1)
                                            <button wire:click.prevent="removeProduct({{ $index }})"
                                                class="text-red-500 hover:text-red-700">
                                                <x-heroicon-o-trash class="w-5 h-5" />
                                            </button>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-3 mb-4">
                                        <input id="is_new_product_{{ $index }}"
                                            wire:model.live="orderProducts.{{ $index }}.is_new_product"
                                            type="checkbox"
                                            class="h-4 w-4 text-blue-700 border-gray-300 rounded focus:ring-blue-700">
                                        <label for="is_new_product_{{ $index }}"
                                            class="font-medium text-gray-700">
                                            Buat Produk Baru?
                                        </label>
                                    </div>

                                    @if ($item['is_new_product'])
                                        {{-- FORM UNTUK PRODUK BARU --}}
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 border-t border-gray-200">
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Nama Produk Baru
                                                    <span class="text-red-500">*</span></label>
                                                <input type="text"
                                                    wire:model.defer="orderProducts.{{ $index }}.product_name"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                                @error('orderProducts.' . $index . '.product_name')
                                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Kategori <span
                                                        class="text-red-500">*</span></label>
                                                <select
                                                    wire:model.defer="orderProducts.{{ $index }}.category_id"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                                    <option value="">Pilih Kategori</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('orderProducts.' . $index . '.category_id')
                                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Harga Jual <span
                                                        class="text-red-500">*</span></label>
                                                <input type="number"
                                                    wire:model.defer="orderProducts.{{ $index }}.selling_price"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                                    min="0" step="any">
                                                @error('orderProducts.' . $index . '.selling_price')
                                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">SKU
                                                    (Opsional)</label>
                                                <input type="text"
                                                    wire:model.defer="orderProducts.{{ $index }}.sku"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Deskripsi
                                                    (Opsional)</label>
                                                <textarea wire:model.defer="orderProducts.{{ $index }}.description" rows="2"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                                            </div>
                                        </div>
                                    @else
                                        {{-- DROPDOWN UNTUK PRODUK YANG SUDAH ADA --}}
                                        <div class="p-4 border-t border-gray-200">
                                            <label class="block text-sm font-medium text-gray-700">Pilih Produk <span
                                                    class="text-red-500">*</span></label>
                                            <select wire:model.live="orderProducts.{{ $index }}.product_id"
                                                class="block w-full border-gray-300 rounded-md shadow-sm">
                                                <option value="">Pilih Produk yang Sudah Ada</option>
                                                @foreach ($allProducts as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('orderProducts.' . $index . '.product_id')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif

                                    {{-- Input Jumlah dan Harga Beli (Selalu ada) --}}
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 border-t border-dashed border-gray-300 mt-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Jumlah Pesan <span
                                                    class="text-red-500">*</span></label>
                                            <input type="number"
                                                wire:model.defer="orderProducts.{{ $index }}.quantity_ordered"
                                                class="block w-full border-gray-300 rounded-md shadow-sm"
                                                min="1">
                                            @error('orderProducts.' . $index . '.quantity_ordered')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Harga Beli Satuan
                                                <span class="text-red-500">*</span></label>
                                            <input type="number"
                                                wire:model.defer="orderProducts.{{ $index }}.unit_price"
                                                class="block w-full border-gray-300 rounded-md shadow-sm"
                                                min="0" step="any">
                                            @error('orderProducts.' . $index . '.unit_price')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                                            <p class="text-lg font-semibold text-gray-800 mt-2">
                                                Rp
                                                {{ number_format(($item['quantity_ordered'] ?? 0) * ($item['unit_price'] ?? 0), 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            <button wire:click.prevent="addProduct"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-800 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                                + Tambah Produk Lain
                            </button>
                        </div>
                    </div>

                    {{-- Catatan dan Tombol Aksi Final --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan Tambahan
                                (Opsional)</label>
                            <textarea id="notes" wire:model.lazy="notes" rows="3"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-blue-700 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <div wire:loading wire:target="savePurchase"
                                    class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-3"></div>
                                Simpan Pembelian
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
