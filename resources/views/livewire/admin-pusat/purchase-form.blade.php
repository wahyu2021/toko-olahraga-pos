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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-200">
                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier <span
                                    class="text-red-500">*</span></label>
                            <select id="supplier_id" wire:model.lazy="supplier_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
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
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                            @error('purchase_date')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="invoice_number" class="block text-sm font-medium text-gray-700">Nomor Invoice
                                (Opsional)</label>
                            <input type="text" id="invoice_number" wire:model.lazy="invoice_number"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
                        <div>
                            <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700">Estimasi
                                Tiba (Opsional)</label>
                            <input type="date" id="expected_delivery_date" wire:model.lazy="expected_delivery_date"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500">
                        </div>
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
                                            class="h-4 w-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                                        <label for="is_new_product_{{ $index }}"
                                            class="font-medium text-gray-700">Buat Produk Baru?</label>
                                    </div>

                                    {{-- Sisa dari form ini tidak memiliki elemen warna, jadi tidak perlu diubah. Cukup fokus pada input & checkbox. --}}

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
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-orange-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
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
