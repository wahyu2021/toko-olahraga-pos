<div>
    {{-- **TAMBAHKAN SLOT HEADER DI SINI** --}}
    <x-slot name="header">
        <h1 class="text-xl font-semibold leading-tight text-gray-800">
            Laporan Stok Produk
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div>
                        <label for="searchProductStock" class="block text-sm font-medium text-gray-700">Cari Produk
                            (Nama/SKU)</label>
                        <input wire:model.live.debounce.300ms="searchProduct" id="searchProductStock" type="text"
                            placeholder="Masukkan nama atau SKU..."
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
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Gambar</th>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                    Produk</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">SKU</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cabang</th>
                                <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Kuantitas</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Stok Ulang
                                    Terakhir</th>
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
                                            <div
                                                class="h-10 w-10 bg-gray-200 rounded-md flex items-center justify-center">
                                                <x-heroicon-o-photo class="h-6 w-6 text-gray-400" />
                                            </div>
                                        @endif
                                    </td>
                                    <td
                                        class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        {{ $stock->product->name ?? 'Produk Dihapus' }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $stock->product->sku ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $stock->branch->name ?? 'Cabang Dihapus' }}</td>
                                    <td
                                        class="whitespace-nowrap px-3 py-4 text-sm text-gray-700 text-center font-semibold">
                                        {{ $stock->quantity }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $stock->last_restock_date ? \Carbon\Carbon::parse($stock->last_restock_date)->isoFormat('D MMM YY') : '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-gray-500">Tidak ada data stok
                                        ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $stocksView->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
