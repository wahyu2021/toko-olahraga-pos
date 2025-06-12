<?php

namespace App\Livewire\AdminPusat;

use App\Models\Branch;
use Livewire\Component;
use Livewire\WithPagination;

class BranchManagement extends Component
{
    use WithPagination;

    // Properti untuk tabel dan modal
    public $search = '';
    public bool $showBranchModal = false;
    public ?int $branchId = null;
    public string $name = '';
    public string $address = '';
    public string $phone = '';

    protected $paginationTheme = 'tailwind';

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

    /**
     * Membuka modal dalam mode 'tambah baru'.
     */
    public function create()
    {
        $this->resetErrorBag();
        $this->reset(['branchId', 'name', 'address', 'phone']);
        $this->showBranchModal = true;
    }

    /**
     * Membuka modal dalam mode 'edit' dan mengisi data.
     */
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
     * Menyimpan data (baik baru maupun update).
     */
    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
        ];

        if ($this->branchId) {
            // Mode Update
            $branch = Branch::findOrFail($this->branchId);
            $branch->update($data);
            $message = 'Cabang berhasil diperbarui.';
        } else {
            // Mode Tambah Baru
            Branch::create($data);
            $message = 'Cabang baru berhasil ditambahkan.';
        }

        $this->closeModal();
        $this->dispatch('show-notification', message: $message, type: 'success');
    }

    public function closeModal()
    {
        $this->showBranchModal = false;
    }

    public function render()
    {
        $branches = Branch::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.admin-pusat.branch-management', [
            'branches' => $branches,
        ])->layout('layouts.admin-pusat');
    }
}
