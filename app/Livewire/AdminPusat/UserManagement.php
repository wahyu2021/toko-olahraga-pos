<?php

namespace App\Livewire\AdminPusat;

use Livewire\Component;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortAsc = true;
    public $filterRole = ''; // Properti baru untuk filter peran

    public $userId;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role;
    public $branch_id;

    public $allRoles = []; // Sudah ada, akan digunakan untuk mengisi dropdown filter
    public $branches = [];

    public $showUserModal = false;
    public $showDeleteModal = false;
    public $isEditMode = false;

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        // ... (rules Anda tetap sama)
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'password' => $this->isEditMode && !$this->password ? 'nullable' : ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys($this->allRoles))],
            'branch_id' => [
                Rule::requiredIf(function () {
                    return in_array($this->role, [User::ROLE_ADMIN_CABANG, User::ROLE_MANAJER_CABANG, User::ROLE_KASIR]);
                }),
                'nullable',
                Rule::exists('branches', 'id')->when(function () {
                     return in_array($this->role, [User::ROLE_ADMIN_CABANG, User::ROLE_MANAJER_CABANG, User::ROLE_KASIR]);
                }, function ($query) {
                    return $query;
                }),
            ],
        ];
    }

    public function messages()
    {
        // ... (messages Anda tetap sama)
        return [
            'branch_id.required' => 'Cabang wajib diisi untuk peran ini.',
        ];
    }

    public function mount()
    {
        $this->allRoles = [
            User::ROLE_ADMIN_PUSAT => 'Admin Pusat',
            User::ROLE_ADMIN_CABANG => 'Admin Cabang',
            User::ROLE_MANAJER_PUSAT => 'Manajer Pusat',
            User::ROLE_MANAJER_CABANG => 'Manajer Cabang',
            User::ROLE_KASIR => 'Kasir',
        ];
        $this->branches = Branch::orderBy('name')->get();
    }

    public function sortBy($field)
    {
        // ... (sortBy Anda tetap sama)
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }
        $this->sortField = $field;
    }

    public function updatedRole($value)
    {
        // ... (updatedRole Anda tetap sama)
        if (in_array($value, [User::ROLE_ADMIN_PUSAT, User::ROLE_MANAJER_PUSAT])) {
            $this->branch_id = null;
        }
    }

    // Metode untuk mereset paginasi saat filter berubah
    // Livewire 3 biasanya menangani ini secara otomatis untuk `wire:model.live`
    // Namun, menambahkannya secara eksplisit tidak ada salahnya
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
    }


    public function addUser()
    {
        // ... (addUser Anda tetap sama)
        $this->resetForm();
        $this->isEditMode = false;
        $this->showUserModal = true;
    }

    public function createUser()
    {
        // ... (createUser Anda tetap sama)
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'branch_id' => $this->isBranchAssociatedRole($this->role) ? $this->branch_id : null,
        ]);

        $this->showUserModal = false;
        session()->flash('message', 'Pengguna berhasil ditambahkan.');
        $this->resetForm();
    }

    public function editUser(User $user)
    {
        // ... (editUser Anda tetap sama)
        $this->resetForm();
        $this->isEditMode = true;
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->branch_id = $user->branch_id;
        $this->showUserModal = true;
    }

    public function updateUser()
    {
        // ... (updateUser Anda tetap sama)
        $this->validate();

        $user = User::find($this->userId);
        if ($user) {
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'branch_id' => $this->isBranchAssociatedRole($this->role) ? $this->branch_id : null,
            ];

            if (!empty($this->password)) {
                $userData['password'] = Hash::make($this->password);
            }

            $user->update($userData);
            $this->showUserModal = false;
            session()->flash('message', 'Pengguna berhasil diperbarui.');
            $this->resetForm();
        }
    }

    public function confirmDelete($userId)
    {
        // ... (confirmDelete Anda tetap sama)
        $this->userId = $userId;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        // ... (deleteUser Anda tetap sama)
        $user = User::find($this->userId);
        if ($user) {
            $user->delete();
            session()->flash('message', 'Pengguna berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        // ... (resetForm Anda tetap sama)
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = '';
        $this->branch_id = null;
        $this->resetErrorBag();
    }

    private function isBranchAssociatedRole($role)
    {
        // ... (isBranchAssociatedRole Anda tetap sama)
        return in_array($role, [User::ROLE_ADMIN_CABANG, User::ROLE_MANAJER_CABANG, User::ROLE_KASIR]);
    }

    public function closeModal()
    {
        // ... (closeModal Anda tetap sama)
        $this->showUserModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    public function render()
    {
        $usersQuery = User::with('branch')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });

        // Terapkan filter peran jika dipilih
        if (!empty($this->filterRole)) {
            $usersQuery->where('role', $this->filterRole);
        }

        $users = $usersQuery->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate(10);

        return view('livewire.admin-pusat.user-management', [
            'users' => $users,
        ])->layout('layouts.admin-pusat');
    }
}