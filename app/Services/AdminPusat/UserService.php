<?php

namespace App\Services\AdminPusat;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service class untuk mengelola semua logika bisnis terkait Pengguna.
 */
class UserService
{
    /**
     * Mengambil data pengguna dengan paginasi, filter, dan pengurutan.
     */
    public function getPaginatedUsers(
        string $search = '',
        string $filterRole = '',
        string $sortField = 'name',
        bool $sortAsc = true,
        int $perPage = 10
    ): LengthAwarePaginator {
        $query = User::with('branch')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });

        if (!empty($filterRole)) {
            $query->where('role', $filterRole);
        }

        $query->orderBy($sortField, $sortAsc ? 'asc' : 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Mendapatkan semua peran yang tersedia untuk manajemen pengguna.
     */
    public function getAvailableRoles(): array
    {
        return [
            User::ROLE_ADMIN_PUSAT => 'Admin Pusat',
            User::ROLE_ADMIN_CABANG => 'Admin Cabang',
            User::ROLE_MANAJER_PUSAT => 'Manajer Pusat',
            User::ROLE_MANAJER_CABANG => 'Manajer Cabang',
            User::ROLE_KASIR => 'Kasir',
        ];
    }

    /**
     * Mendapatkan semua data cabang.
     */
    public function getAllBranches(): Collection
    {
        return Branch::orderBy('name')->get();
    }

    /**
     * Membuat pengguna baru.
     * @param array $data Data pengguna yang tervalidasi.
     */
    public function createUser(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'branch_id' => $this->isBranchAssociatedRole($data['role']) ? $data['branch_id'] : null,
        ]);
    }

    /**
     * Memperbarui data pengguna yang ada.
     * @param int $userId ID pengguna yang akan diupdate.
     * @param array $data Data pengguna yang tervalidasi.
     */
    public function updateUser(int $userId, array $data): User
    {
        $user = User::findOrFail($userId);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'branch_id' => $this->isBranchAssociatedRole($data['role']) ? $data['branch_id'] : null,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);
        return $user;
    }

    /**
     * Menghapus pengguna.
     * @param int $userId ID pengguna yang akan dihapus.
     */
    public function deleteUser(int $userId): void
    {
        $user = User::findOrFail($userId);
        // Anda bisa menambahkan logika tambahan di sini, misalnya:
        // if ($user->id === auth()->id()) {
        //     throw new \Exception("Anda tidak dapat menghapus akun Anda sendiri.");
        // }
        $user->delete();
    }

    /**
     * Memeriksa apakah sebuah peran memerlukan asosiasi dengan cabang.
     */
    public function isBranchAssociatedRole(?string $role): bool
    {
        if (!$role) {
            return false;
        }
        return in_array($role, [User::ROLE_ADMIN_CABANG, User::ROLE_MANAJER_CABANG, User::ROLE_KASIR]);
    }
}
