<?php

namespace App\Livewire\AdminPusat;

use App\Models\Branch;
use App\Services\AdminPusat\BranchService;
use Livewire\Component;
use Livewire\WithPagination;
use Exception;

class BranchManagement extends Component
{
    use WithPagination;

    // Properti untuk state komponen
    public $search = '';
    public bool $showBranchModal = false;
    public ?int $branchId = null;
    public string $name = '';
    public string $address = '';
    public string $phone = '';

    public bool $confirmingBranchDeletion = false;
    public ?int $branchIdToDelete = null;

    protected $paginationTheme = 'tailwind';

    // Aturan validasi tetap di sini karena terkait langsung dengan form
    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:branches,name,' . $this->branchId],
            'address' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
        ];
    }

    protected $messages = [
        'name.required' => 'Nama cabang wajib diisi.',
        'name.unique' => 'Nama cabang sudah ada.',
        'address.required' => 'Alamat wajib diisi.',
        'phone.required' => 'Nomor telepon wajib diisi.',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetErrorBag();
        $this->reset(['branchId', 'name', 'address', 'phone']);
        $this->showBranchModal = true;
    }

    public function edit(Branch $branch)
    {
        $this->resetErrorBag();
        $this->branchId = $branch->id;
        $this->name = $branch->name;
        $this->address = $branch->address;
        $this->phone = $branch->phone;
        $this->showBranchModal = true;
    }

    /**
     * 2. Metode save() sekarang memanggil BranchService
     */
    public function save(BranchService $branchService)
    {
        $validatedData = $this->validate();
        $message = '';

        try {
            if ($this->branchId) {
                // Mode Update
                $branchService->updateBranch($this->branchId, $validatedData);
                $message = 'Cabang berhasil diperbarui.';
            } else {
                // Mode Tambah Baru
                $branchService->createBranch($validatedData);
                $message = 'Cabang baru berhasil ditambahkan.';
            }
            $this->closeModal();
            $this->dispatch('show-notification', message: $message, type: 'success');
        } catch (Exception $e) {
            $this->dispatch('show-notification', message: 'Terjadi kesalahan: ' . $e->getMessage(), type: 'error');
        }
    }

    public function closeModal()
    {
        $this->showBranchModal = false;
    }

    public function confirmDelete(int $id)
    {
        $this->branchIdToDelete = $id;
        $this->confirmingBranchDeletion = true;
    }

    /**
     * 3. Metode delete() sekarang memanggil BranchService
     */
    public function delete(BranchService $branchService)
    {
        try {
            $branchService->deleteBranch($this->branchIdToDelete);
            $this->dispatch('show-notification', message: 'Cabang berhasil dihapus.', type: 'success');
        } catch (Exception $e) {
            // Menangkap error dari service (misal: cabang tidak bisa dihapus)
            $this->dispatch('show-notification', message: $e->getMessage(), type: 'error');
        } finally {
            $this->confirmingBranchDeletion = false;
        }
    }

    /**
     * 4. Render() sekarang memanggil BranchService
     */
    public function render(BranchService $branchService)
    {
        $branches = $branchService->getPaginatedBranches($this->search);

        return view('livewire.admin-pusat.branch-management', [
            'branches' => $branches,
        ])->layout('layouts.admin-pusat');
    }
}
