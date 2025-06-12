{{-- PERUBAHAN DI SINI: Menggunakan variabel $showBranchModal yang benar --}}
@if ($showBranchModal)
    <x-dialog-modal wire:model.live="showBranchModal">
        <x-slot name="title">
            {{ $branchId ? 'Edit Cabang' : 'Tambah Cabang Baru' }}
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Cabang</label>
                    <input type="text" wire:model.defer="name" id="name"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                        placeholder="Contoh: Cabang Jakarta Selatan">
                    <x-input-error for="name" class="mt-2" />
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea wire:model.defer="address" id="address" rows="3"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                        placeholder="Contoh: Jl. Jenderal Sudirman No. 123"></textarea>
                    <x-input-error for="address" class="mt-2" />
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                    <input type="text" wire:model.defer="phone" id="phone"
                        class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                        placeholder="Contoh: 021-555-1234">
                    <x-input-error for="phone" class="mt-2" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal()" wire:loading.attr="disabled">Batal</x-secondary-button>
            <x-button class="ml-3" wire:click="save" wire:loading.attr="disabled">
                Simpan
            </x-button>
        </x-slot>
    </x-dialog-modal>
@endif
