<?php

namespace App\Livewire\AdminCabang;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class BranchUserManagement extends Component
{
    use WithPagination;

    // Properti untuk Form
    public $userId, $name, $email, $password, $password_confirmation, $role;
    public $isEditMode = false;
    public $showUserModal = false;
    public $showDeleteModal = false;

    // Properti untuk Tabel
    public $search = '';
    public $filterRole = '';
    public $rolesForBranch = [];

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'password' => $this->isEditMode && !$this->password ? 'nullable' : ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(array_keys($this->rolesForBranch))],
        ];
    }

    public function mount()
    {
        // Admin Cabang hanya bisa mengelola peran di bawahnya
        $this->rolesForBranch = [
            User::ROLE_KASIR => 'Kasir',
            User::ROLE_MANAJER_CABANG => 'Manajer Cabang',
        ];
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
        $this->reset(['userId', 'name', 'email', 'password', 'password_confirmation', 'role', 'isEditMode']);
        $this->resetErrorBag();
    }

    public function addUser()
    {
        $this->resetForm();
        $this->showUserModal = true;
    }

    public function createUser()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'branch_id' => Auth::user()->branch_id, // Otomatis set ke cabang admin
        ]);

        session()->flash('success', 'Pengguna baru berhasil ditambahkan.');
        $this->closeModal();
    }

    public function editUser(User $user)
    {
        // Pastikan admin cabang tidak bisa mengedit user di luar cabangnya
        if ($user->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Akses ditolak.');
        }

        $this->resetForm();
        $this->isEditMode = true;
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->showUserModal = true;
    }

    public function updateUser()
    {
        $user = User::findOrFail($this->userId);
        if ($user->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Akses ditolak.');
        }

        $validatedData = $this->validate();

        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
        ];

        if (!empty($validatedData['password'])) {
            $userData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($userData);

        session()->flash('success', 'Data pengguna berhasil diperbarui.');
        $this->closeModal();
    }

    public function confirmDelete($userId)
    {
        $this->userId = $userId;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        $user = User::findOrFail($this->userId);
        if ($user->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Akses ditolak.');
        }

        $user->delete();
        session()->flash('success', 'Pengguna berhasil dihapus.');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showUserModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    public function render()
    {
        $branchId = Auth::user()->branch_id;

        $users = User::query()
            ->where('branch_id', $branchId)
            ->where('id', '!=', Auth::id()) // Jangan tampilkan diri sendiri
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterRole, function ($query) {
                $query->where('role', $this->filterRole);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin-cabang.branch-user-management', [
            'users' => $users
        ])->layout('layouts.admin-cabang');
    }
}
