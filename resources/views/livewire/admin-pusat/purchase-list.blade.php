<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen & Riwayat Pembelian') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 lg:p-8">

                <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                    <h3 class="text-2xl font-semibold text-gray-700 mb-2 sm:mb-0">
                        Riwayat Pembelian Barang
                    </h3>
                    <a href="{{ route('admin-pusat.purchases.create') }}"
                        class="px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-50 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 3a1 1 0 011 1v4h4a1 1 0 110 2h-4v4a1 1 0 11-2 0v-4H5a1 1 0 110-2h4V4a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Buat Pembelian Baru
                    </a>
                </div>

                {{-- Filter --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari No. Invoice / Supplier..."
                        class="form-input rounded-md shadow-sm py-2 px-3 border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                    <select wire:model.live="filterSupplier"
                        class="form-select rounded-md shadow-sm py-2 px-3 border-gray-300 focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        <option value="">Semua Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    <div class="flex items-center space-x-2">
                        <input type="date" wire:model.live="startDate"
                            class="form-input rounded-md shadow-sm py-2 px-3 border-gray-300 w-full focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                        <span class="text-gray-500">-</span>
                        <input type="date" wire:model.live="endDate"
                            class="form-input rounded-md shadow-sm py-2 px-3 border-gray-300 w-full focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                    </div>
                </div>

                {{-- Tabel Riwayat Pembelian --}}
                <div class="overflow-x-auto bg-white rounded-lg shadow">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tgl Pembelian</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Invoice</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Supplier</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah Barang</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($purchases as $purchase)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($purchase->purchase_date)->isoFormat('DD MMM YYYY') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $purchase->invoice_number ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $purchase->supplier->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 font-semibold">Rp
                                        {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 text-center font-medium">
                                        {{ $purchase->total_quantity_received ?? '0' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $purchase->status == 'completed' || $purchase->status == 'received' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ Str::title($purchase->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button wire:click="showDetails({{ $purchase->id }})"
                                            class="text-orange-600 hover:text-orange-900">Lihat Detail</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada
                                        data pembelian yang ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($purchases->hasPages())
                    <div class="mt-6">
                        {{ $purchases->links() }}
                    </div>
                @endif

                {{-- Modal Detail Pembelian --}}
                @if ($isDetailModalOpen && $selectedPurchase)
                    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="detail-purchase-modal"
                        role="dialog" aria-modal="true">
                        <div
                            class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                                wire:click="closeDetailModal()" aria-hidden="true"></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full"
                                role="document">
                                <div class="bg-orange-700 px-4 py-3 sm:px-6 flex justify-between items-center">
                                    <h3 class="text-lg leading-6 font-medium text-white" id="detail-purchase-modal">
                                        Detail Pembelian
                                        #{{ $selectedPurchase->invoice_number ?? $selectedPurchase->id }}
                                    </h3>
                                    <button wire:click="closeDetailModal()" class="text-white hover:text-gray-200">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-4">
                                        <div><strong class="text-gray-600">Supplier:</strong>
                                            {{ $selectedPurchase->supplier->name ?? 'N/A' }}</div>
                                        <div><strong class="text-gray-600">Diinput oleh:</strong>
                                            {{ $selectedPurchase->user->name ?? 'N/A' }}</div>
                                        <div><strong class="text-gray-600">Tanggal Beli:</strong>
                                            {{ \Carbon\Carbon::parse($selectedPurchase->purchase_date)->isoFormat('dddd, DD MMMM YYYY') }}
                                        </div>
                                        <div><strong class="text-gray-600">Estimasi Tiba:</strong>
                                            {{ $selectedPurchase->expected_delivery_date ? \Carbon\Carbon::parse($selectedPurchase->expected_delivery_date)->isoFormat('dddd, DD MMMM YYYY') : '-' }}
                                        </div>
                                        <div><strong class="text-gray-600">Status:</strong> <span
                                                class="font-semibold">{{ Str::title($selectedPurchase->status) }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 border-t pt-4">
                                        <h4 class="font-medium text-gray-800 mb-2">Item yang Dibeli:</h4>
                                        <table class="min-w-full">
                                            {{-- Table content remains the same --}}
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
                                <div class="bg-gray-100 px-4 py-3 sm:px-6 text-right">
                                    <button type="button" wire:click="closeDetailModal()"
                                        class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:w-auto sm:text-sm">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
