<?php

namespace App\Services\AdminPusat;

use App\Models\Branch;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

/**
 * Service class untuk mengelola semua logika bisnis terkait Cabang.
 */
class BranchService
{
    /**
     * Mengambil data cabang dengan paginasi dan pencarian.
     *
     * @param string $search
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedBranches(string $search = '', int $perPage = 10)
    {
        return Branch::where('name', 'like', '%' . $search . '%')
            ->orderBy('name', 'asc')
            ->paginate($perPage);
    }

    /**
     * Membuat cabang baru.
     *
     * @param array $data
     * @return Branch
     */
    public function createBranch(array $data): Branch
    {
        return Branch::create($data);
    }

    /**
     * Memperbarui data cabang yang ada.
     *
     * @param int $branchId
     * @param array $data
     * @return Branch
     */
    public function updateBranch(int $branchId, array $data): Branch
    {
        $branch = Branch::findOrFail($branchId);
        $branch->update($data);
        return $branch;
    }

    /**
     * Menghapus cabang.
     *
     * @param int $branchId
     * @return void
     * @throws Exception Jika cabang memiliki data terkait.
     * @throws ModelNotFoundException Jika cabang tidak ditemukan.
     */
    public function deleteBranch(int $branchId): void
    {
        $branch = Branch::findOrFail($branchId);

        // Validasi untuk mencegah penghapusan jika ada relasi
        if ($branch->users()->exists() || $branch->stocks()->exists()) {
            throw new Exception('Gagal! Cabang ini memiliki pengguna atau data stok terkait.');
        }

        $branch->delete();
    }
}
