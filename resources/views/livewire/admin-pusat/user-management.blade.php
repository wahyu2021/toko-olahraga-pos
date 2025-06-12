<div>
    {{-- Header Halaman --}}
    <x-slot name="header">
        <h1 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Manajemen Pengguna') }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">

            {{-- Menampilkan notifikasi sukses/error dengan komponen --}}
            <x-session-message />

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    {{-- Kontrol: Pencarian, Filter, dan Tombol Tambah --}}
                    <div class="mb-6 grid grid-cols-1 items-end gap-4 md:grid-cols-3">
                        <div>
                            <x-label for="search" value="{{ __('Cari Pengguna (Nama/Email)') }}" />
                            <x-input wire:model.live.debounce.300ms="search" id="search" type="text"
                                class="mt-1 block w-full" placeholder="Cari..." />
                        </div>
                        <div>
                            <x-label for="filterRole" value="{{ __('Filter Berdasarkan Peran') }}" />
                            <select wire:model.live="filterRole" id="filterRole"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                                <option value="">Semua Peran</option>
                                @foreach ($allRoles as $roleKey => $roleName)
                                    <option value="{{ $roleKey }}">{{ $roleName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:text-right">
                            {{-- Memanggil metode yang benar dari komponen yang sudah direfaktor --}}
                            <x-button wire:click="openUserModal()">
                                {{ __('Tambah Pengguna') }}
                            </x-button>
                        </div>
                    </div>

                    {{-- Tabel Pengguna --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                        <button wire:click="sortBy('name')" class="flex items-center space-x-1">
                                            <span>Nama</span>
                                            @if ($sortField === 'name')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="{{ $sortAsc ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        <button wire:click="sortBy('email')" class="flex items-center space-x-1">
                                            <span>Email</span>
                                            @if ($sortField === 'email')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="{{ $sortAsc ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                                </svg>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Peran</th>
                                    <th scope="col"
                                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Cabang</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span
                                            class="sr-only">Aksi</span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($users as $user)
                                    <tr wire:key="user-{{ $user->id }}">
                                        <td
                                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            {{ $user->name }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $user->email }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $allRoles[$user->role] ?? $user->role }}</td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ $user->branch->name ?? '-' }}</td>
                                        <td
                                            class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                            <button wire:click="editUser({{ $user->id }})"
                                                class="text-orange-600 hover:text-orange-900">Edit</button>
                                            <button wire:click="confirmDelete({{ $user->id }})"
                                                class="ml-4 text-red-600 hover:text-red-900">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="whitespace-nowrap p-4 text-sm text-center text-gray-500">
                                            Tidak ada pengguna ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Link Paginasi --}}
                    @if ($users->hasPages())
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah/Edit Pengguna menggunakan Komponen Jetstream --}}
    <x-dialog-modal wire:model.live="showUserModal">
        <x-slot name="title">
            {{ $isEditMode ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                {{-- Field Nama --}}
                <div>
                    <x-label for="name" value="{{ __('Nama') }}" />
                    <x-input wire:model="name" id="name" type="text" class="mt-1 block w-full" />
                    <x-input-error for="name" class="mt-2" />
                </div>
                {{-- Field Email --}}
                <div>
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input wire:model="email" id="email" type="email" class="mt-1 block w-full" />
                    <x-input-error for="email" class="mt-2" />
                </div>
                {{-- Field Password --}}
                <div>
                    <x-label for="password" value="{{ __('Password') }}" />
                    @if ($isEditMode)
                        <p class="text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password.</p>
                    @endif
                    <x-input wire:model="password" id="password" type="password" class="mt-1 block w-full" />
                    <x-input-error for="password" class="mt-2" />
                </div>
                {{-- Field Konfirmasi Password --}}
                <div>
                    <x-label for="password_confirmation" value="{{ __('Konfirmasi Password') }}" />
                    <x-input wire:model="password_confirmation" id="password_confirmation" type="password"
                        class="mt-1 block w-full" />
                </div>
                {{-- Field Peran --}}
                <div>
                    <x-label for="role" value="{{ __('Peran') }}" />
                    <select wire:model.live="role" id="role"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                        <option value="">Pilih Peran</option>
                        @foreach ($allRoles as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="role" class="mt-2" />
                </div>

                {{-- PERBAIKAN: Field Cabang dipanggil sebagai metode --}}
                @if ($this->shouldShowBranchField())
                    <div wire:key="branch-field">
                        <x-label for="branch_id" value="{{ __('Cabang') }}" />
                        <select wire:model="branch_id" id="branch_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                            <option value="">Pilih Cabang</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="branch_id" class="mt-2" />
                    </div>
                @endif
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">
                {{ __('Batal') }}
            </x-secondary-button>

            <x-button class="ml-3" wire:click="saveUser()" wire:loading.attr="disabled">
                {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Pengguna' }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    {{-- Modal Konfirmasi Hapus --}}
    <x-confirmation-modal wire:model.live="showDeleteModal">
        <x-slot name="title">
            Hapus Pengguna
        </x-slot>

        <x-slot name="content">
            Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">
                Batal
            </x-secondary-button>

            <x-danger-button class="ml-3" wire:click="deleteUser()" wire:loading.attr="disabled">
                Ya, Hapus
            </x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
