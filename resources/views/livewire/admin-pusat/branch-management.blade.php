<div>
    {{-- HEADER HALAMAN --}}
    <x-slot name="header">
        <h1 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Manajemen Cabang') }}
        </h1>
    </x-slot>

    {{-- KONTEN UTAMA --}}
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="p-4 bg-white shadow-sm sm:rounded-lg sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama cabang..."
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                    </div>
                    <div>
                        <x-button wire:click="create">
                            <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                            Tambah Cabang Baru
                        </x-button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama Cabang</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Alamat</th>
                                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Telepon</th>
                                <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($branches as $branch)
                                <tr>
                                    <td class="px-3 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">
                                        {{ $branch->name }}</td>
                                    <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $branch->address }}
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $branch->phone }}
                                    </td>
                                    <td
                                        class="relative py-4 pl-3 pr-4 text-sm font-medium text-right whitespace-nowrap sm:pr-6">
                                        <button wire:click="edit({{ $branch->id }})"
                                            class="font-semibold text-orange-600 hover:text-orange-900">Edit</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="px-3 py-4 text-sm text-center text-gray-500 whitespace-nowrap">
                                        Tidak ada data cabang ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        {{ $branches->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Memanggil file partial untuk modal --}}
    @include('livewire.admin-pusat.partials._branch-modal')

    {{-- Komponen Notifikasi (jika belum ada di layout utama) --}}
    <div x-data="{ show: false, message: '', type: '' }"
        x-on:show-notification.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 4000)"
        x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2" style="display: none;"
        class="fixed z-50 w-full max-w-sm top-5 right-5">
        <div class="p-4 rounded-lg shadow-lg"
            :class="{ 'bg-green-100 text-green-800 border border-green-200': type === 'success', 'bg-red-100 text-red-800 border border-red-200': type === 'error' }">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <x-heroicon-o-check-circle x-show="type === 'success'" class="w-6 h-6 text-green-600" />
                    <x-heroicon-o-x-circle x-show="type === 'error'" class="w-6 h-6 text-red-600" />
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium" x-text="message"></p>
                </div>
            </div>
        </div>
    </div>
</div>
