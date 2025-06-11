<div>
    <x-slot name="header">
        <h1 class="text-xl font-semibold leading-tight text-gray-800">
            Manajemen Pengguna - Cabang {{ Auth::user()->branch->name }}
        </h1>
    </x-slot>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <x-session-message />

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4">
                    {{-- Area Filter --}}
                    <div class="flex-grow grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">Cari Pengguna</label>
                            <input wire:model.live.debounce.300ms="search" id="search" type="text"
                                placeholder="Nama atau email..."
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="filterRole" class="block text-sm font-medium text-gray-700">Filter Peran</label>
                            <select wire:model.live="filterRole" id="filterRole"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm">
                                <option value="">Semua Peran</option>
                                @foreach ($rolesForBranch as $roleKey => $roleName)
                                    <option value="{{ $roleKey }}">{{ $roleName }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- Tombol Tambah --}}
                    <div class="flex-shrink-0">
                        <button wire:click="addUser()" type="button"
                            class="w-full inline-flex items-center justify-center rounded-md border border-transparent bg-orange-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                            Tambah Pengguna
                        </button>
                    </div>
                </div>

                {{-- Tabel Pengguna --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Peran</th>
                                <th scope="col" class="relative px-6 py-3"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $rolesForBranch[$user->role] ?? $user->role }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="editUser({{ $user->id }})"
                                            class="text-orange-600 hover:text-orange-900">Edit</button>
                                        <button wire:click="confirmDelete({{ $user->id }})"
                                            class="ml-4 text-red-600 hover:text-red-900">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-12 text-gray-500">Tidak ada pengguna
                                        ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah/Edit Pengguna --}}
    @if ($showUserModal)
        <x-dialog-modal wire:model.live="showUserModal">
            <x-slot name="title">
                {{ $isEditMode ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}
            </x-slot>
            <x-slot name="content">
                <div class="space-y-4">
                    <div>
                        <x-label for="name" value="Nama" />
                        <x-input id="name" type="text" class="mt-1 block w-full" wire:model.defer="name" />
                        <x-input-error for="name" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="email" value="Email" />
                        <x-input id="email" type="email" class="mt-1 block w-full" wire:model.defer="email" />
                        <x-input-error for="email" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="role" value="Peran" />
                        <select wire:model.defer="role" id="role"
                            class="mt-1 block w-full border-gray-300 focus:border-orange-500 focus:ring-orange-500 rounded-md shadow-sm">
                            <option value="">Pilih Peran</option>
                            @foreach ($rolesForBranch as $roleKey => $roleName)
                                <option value="{{ $roleKey }}">{{ $roleName }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="role" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="password" value="Password {{ $isEditMode ? '(Opsional)' : '' }}" />
                        <x-input id="password" type="password" class="mt-1 block w-full" wire:model.defer="password" />
                        <x-input-error for="password" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="password_confirmation" value="Konfirmasi Password" />
                        <x-input id="password_confirmation" type="password" class="mt-1 block w-full"
                            wire:model.defer="password_confirmation" />
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">Batal</x-secondary-button>
                <x-button class="ml-3" wire:click="{{ $isEditMode ? 'updateUser' : 'createUser' }}"
                    wire:loading.attr="disabled">
                    {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Pengguna' }}
                </x-button>
            </x-slot>
        </x-dialog-modal>
    @endif

    {{-- Modal Konfirmasi Hapus --}}
    <x-confirmation-modal wire:model.live="showDeleteModal">
        <x-slot name="title">Hapus Pengguna</x-slot>
        <x-slot name="content">Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat
            dibatalkan.</x-slot>
        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">Batal</x-secondary-button>
            <x-danger-button class="ml-3" wire:click="deleteUser()" wire:loading.attr="disabled">Ya,
                Hapus</x-danger-button>
        </x-slot>
    </x-confirmation-modal>
</div>
