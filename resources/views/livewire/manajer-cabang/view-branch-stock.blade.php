<div>
    <x-slot name="header">
        <h1 class="text-xl font-semibold leading-tight text-gray-800">
            Laporan Stok - Cabang {{ Auth::user()->branch->name ?? 'N/A' }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="mb-6">
                    <label for="searchProductStock" class="block text-sm font-medium text-gray-700">Cari Produk
                        (Nama/SKU)</label>
                    <div class="mt-1">
                        <input wire:model.live.debounce.300ms="searchProduct" id="searchProductStock" type="text"
                            placeholder="Masukkan nama atau SKU produk untuk mencari..."
                            class="block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Gambar
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produk
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    SKU
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Kuantitas
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Stok Ulang Terakhir
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($stocks as $stock)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($stock->product && $stock->product->image_path)
                                            <img src="{{ Storage::url($stock->product->image_path) }}"
                                                alt="{{ $stock->product->name }}"
                                                class="h-12 w-12 object-cover rounded-md">
                                        @else
                                            <div
                                                class="h-12 w-12 bg-gray-200 rounded-md flex items-center justify-center">
                                                <x-heroicon-o-photo class="h-8 w-8 text-gray-400" />
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $stock->product->name ?? 'Produk Dihapus' }}</div>
                                        <div class="text-sm text-gray-500">{{ $stock->product->category->name ?? '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $stock->product->sku ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="text-lg font-bold {{ $stock->quantity <= ($stock->product->low_stock_threshold ?? 5) ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $stock->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $stock->last_restock_date ? \Carbon\Carbon::parse($stock->last_restock_date)->isoFormat('D MMMM YYYY') : 'Belum Pernah' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        Tidak ada data stok yang cocok dengan pencarian Anda.
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
</div>
