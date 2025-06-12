<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Manajemen & Riwayat Pembelian</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 lg:p-8 shadow-xl rounded-lg">

                <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                    <h3 class="text-2xl font-semibold text-gray-700 mb-2 sm:mb-0">Riwayat Pembelian Barang</h3>
                    <a href="{{ route('admin-pusat.purchases.create') }}"
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h4V4a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Buat Pembelian Baru
                    </a>
                </div>

                {{-- Filter --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <input type="text" wire:model.debounce.300ms="search"
                        placeholder="Cari No. Invoice / Supplier..."
                        class="form-input rounded shadow-sm py-2 px-3 border focus:ring-orange-500">
                    <select wire:model="filterSupplier"
                        class="form-select rounded shadow-sm py-2 px-3 border focus:ring-orange-500">
                        <option value="">Semua Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier['id'] }}">{{ $supplier['name'] }}</option>
                        @endforeach
                    </select>
                    <div class="flex space-x-2">
                        <input type="date" wire:model="startDate"
                            class="form-input rounded shadow-sm py-2 px-3 border focus:ring-orange-500">
                        <span class="self-center text-gray-500">-</span>
                        <input type="date" wire:model="endDate"
                            class="form-input rounded shadow-sm py-2 px-3 border focus:ring-orange-500">
                    </div>
                </div>

                {{-- Tabel --}}
                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th wire:click="sortBy('purchase_date')"
                                    class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Tgl Pembelian</th>
                                <th wire:click="sortBy('invoice_number')"
                                    class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    No. Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier
                                </th>
                                <th wire:click="sortBy('total_amount')"
                                    class="cursor-pointer px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Total</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Jumlah
                                    Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($purchases as $purchase)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->isoFormat('DD MMM YYYY') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium">{{ $purchase->invoice_number ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $purchase->supplier->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-800">Rp
                                        {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        {{ $purchase->total_quantity_received ?? 0 }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        <span
                                            class="px-2 inline-flex text-xs font-semibold rounded-full {{ in_array($purchase->status, ['completed', 'received']) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ Str::title($purchase->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <button wire:click="showDetails({{ $purchase->id }})"
                                            class="text-orange-600 hover:text-orange-900">Lihat Detail</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data
                                        pembelian.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($purchases->hasPages())
                    <div class="mt-6">{{ $purchases->links() }}</div>
                @endif

                {{-- Modal Detail --}}
                @if ($isDetailModalOpen && $selectedPurchase)
                    <div
                        class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-gray-500 bg-opacity-75">
                        <div class="bg-white rounded-lg overflow-hidden shadow-xl w-full max-w-3xl">
                            <div class="bg-orange-700 px-6 py-3 flex justify-between items-center">
                                <h3 class="text-lg font-medium text-white">Detail Pembelian
                                    #{{ $selectedPurchase->invoice_number ?? $selectedPurchase->id }}</h3>
                                <button wire:click="closeDetailModal()" class="text-white hover:text-gray-200">
                                    ✕
                                </button>
                            </div>
                            <div class="px-6 py-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                                    <div><strong>Supplier:</strong> {{ $selectedPurchase->supplier->name ?? 'N/A' }}
                                    </div>
                                    <div><strong>Diinput oleh:</strong> {{ $selectedPurchase->user->name ?? 'N/A' }}
                                    </div>
                                    <div><strong>Tanggal Beli:</strong>
                                        {{ \Carbon\Carbon::parse($selectedPurchase->purchase_date)->isoFormat('dddd, DD MMMM YYYY') }}
                                    </div>
                                    <div><strong>Estimasi Tiba:</strong>
                                        {{ $selectedPurchase->expected_delivery_date ? \Carbon\Carbon::parse($selectedPurchase->expected_delivery_date)->isoFormat('dddd, DD MMMM YYYY') : '-' }}
                                    </div>
                                    <div><strong>Status:</strong> <span
                                            class="font-semibold">{{ Str::title($selectedPurchase->status) }}</span>
                                    </div>
                                </div>
                                <div class="mt-4 border-t pt-4">
                                    <h4 class="font-medium text-gray-800 mb-2">Item yang Dibeli:</h4>
                                    <table class="min-w-full text-sm">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-2 text-left">Produk</th>
                                                <th class="px-4 py-2 text-right">Qty</th>
                                                <th class="px-4 py-2 text-right">Harga</th>
                                                <th class="px-4 py-2 text-right">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($selectedPurchase->items as $item)
                                                <tr>
                                                    <td class="px-4 py-2">{{ $item->product->name ?? '#' }}</td>
                                                    <td class="px-4 py-2 text-right">{{ $item->quantity_received }}</td>
                                                    <td class="px-4 py-2 text-right">Rp
                                                        {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                                    <td class="px-4 py-2 text-right">Rp
                                                        {{ number_format($item->quantity_received * $item->unit_price, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if ($selectedPurchase->notes)
                                    <div class="mt-4 border-t pt-4">
                                        <h4 class="font-medium text-gray-800 mb-1">Catatan:</h4>
                                        <p class="text-sm text-gray-600 whitespace-pre-wrap">
                                            {{ $selectedPurchase->notes }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="bg-gray-100 px-6 py-3 text-right">
                                <button wire:click="closeDetailModal()"
                                    class="px-4 py-2 bg-white border rounded shadow hover:bg-gray-50">Tutup</button>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
