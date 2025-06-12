<?php

namespace App\Livewire\AdminPusat;

use App\Models\User;
use App\Services\AdminPusat\UserService;
use Exception;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    // Properti untuk UI state
    public $search = '';
    public $sortField = 'name';
    public $sortAsc = true;
    public $filterRole = '';
    public bool $showUserModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditMode = false;

    // Properti untuk Form
    public ?int $userId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = '';
    public ?int $branch_id = null;

    // Properti untuk data dropdown
    public array $allRoles = [];
    public $branches; // Akan diisi dengan koleksi

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'password' => $this->isEditMode && !$this->password ? 'nullable' : ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys($this->allRoles))],
            'branch_id' => [
                'nullable',
                // PERBAIKAN: Memanggil metode lokal untuk konsistensi.
                Rule::requiredIf(fn() => $this->shouldShowBranchField()),
                Rule::exists('branches', 'id'),
            ],
        ];
    }

    public function messages()
    {
        return [
            'branch_id.required' => 'Cabang wajib diisi untuk peran ini.',
        ];
    }

    public function mount(UserService $userService)
    {
        $this->allRoles = $userService->getAvailableRoles();
        $this->branches = $userService->getAllBranches();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }
        $this->sortField = $field;
    }

    public function updatedRole($value)
    {
        if (!(new UserService())->isBranchAssociatedRole($value)) {
            $this->branch_id = null;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->reset(['userId', 'name', 'email', 'password', 'password_confirmation', 'role', 'branch_id', 'isEditMode']);
        $this->resetErrorBag();
    }

    public function openUserModal()
    {
        $this->resetForm();
        $this->showUserModal = true;
    }

    public function editUser(User $user)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->branch_id = $user->branch_id;
        $this->showUserModal = true;
    }

    public function confirmDelete(int $userId)
    {
        $this->userId = $userId;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showUserModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    public function saveUser(UserService $userService)
    {
        $validatedData = $this->validate();

        try {
            if ($this->isEditMode) {
                $userService->updateUser($this->userId, $validatedData);
                session()->flash('message', 'Data pengguna berhasil diperbarui.');
            } else {
                $userService->createUser($validatedData);
                session()->flash('message', 'Pengguna baru berhasil ditambahkan.');
            }
            $this->closeModal();
        } catch (Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function deleteUser(UserService $userService)
    {
        try {
            $userService->deleteUser($this->userId);
            session()->flash('message', 'Pengguna berhasil dihapus.');
        } catch (Exception $e) {
            session()->flash('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        } finally {
            $this->closeModal();
        }
    }

    /**
     * Metode publik untuk memeriksa apakah dropdown cabang harus ditampilkan.
     * Dapat dipanggil dari file view Blade.
     */
    public function shouldShowBranchField(): bool
    {
        return (new UserService())->isBranchAssociatedRole($this->role);
    }

    public function render(UserService $userService)
    {
        $users = $userService->getPaginatedUsers(
            $this->search,
            $this->filterRole,
            $this->sortField,
            $this->sortAsc
        );

        return view('livewire.admin-pusat.user-management', [
            'users' => $users,
        ])->layout('layouts.admin-pusat');
    }
}
