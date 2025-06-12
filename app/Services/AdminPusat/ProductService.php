<?php

namespace App\Services\AdminPusat;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

/**
 * Service class untuk mengelola semua logika bisnis terkait Produk.
 */
class ProductService
{
    /**
     * Mengambil data produk dengan paginasi, filter, pencarian, dan sorting.
     */
    public function getPaginatedProducts(
        string $search = '',
        ?int $categoryId = null,
        string $sortField = 'name',
        bool $sortAsc = true,
        int $perPage = 15 // Anda bisa sesuaikan jumlah per halaman
    ): LengthAwarePaginator {
        return Product::with(['category', 'supplier'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%');
            })
            ->when($categoryId, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->orderBy($sortField, $sortAsc ? 'asc' : 'desc')
            ->paginate($perPage);
    }

    /**
     * Mengambil semua kategori untuk dropdown.
     */
    public function getAllCategories(): Collection
    {
        return Category::orderBy('name')->get();
    }

    /**
     * Mengambil semua supplier untuk dropdown.
     */
    public function getAllSuppliers(): Collection
    {
        return Supplier::orderBy('name')->get();
    }

    /**
     * Membuat produk baru beserta gambarnya.
     *
     * @param array $data Data produk yang tervalidasi.
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        if (!empty($data['newProductImage'])) {
            $data['image_path'] = $data['newProductImage']->store('products', 'public');
        }
        unset($data['newProductImage']); // Hapus dari array data sebelum create

        return Product::create($data);
    }

    /**
     * Memperbarui data produk yang ada.
     *
     * @param int $productId ID produk.
     * @param array $data Data produk yang tervalidasi.
     * @return Product
     */
    public function updateProduct(int $productId, array $data): Product
    {
        $product = Product::findOrFail($productId);

        if (!empty($data['newProductImage'])) {
            // Hapus gambar lama jika ada
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            // Simpan gambar baru dan perbarui path di data
            $data['image_path'] = $data['newProductImage']->store('products', 'public');
        }
        unset($data['newProductImage']); // Hapus dari array data sebelum update

        $product->update($data);
        return $product;
    }

    /**
     * Menghapus produk beserta file gambarnya.
     *
     * @param int $productId ID produk.
     */
    public function deleteProduct(int $productId): void
    {
        $product = Product::findOrFail($productId);

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();
    }
}
