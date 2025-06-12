<?php

namespace App\Livewire\AdminPusat;

use App\Models\Product;
use App\Services\AdminPusat\ProductService;
use Exception;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ProductManagement extends Component
{
    use WithPagination, WithFileUploads;

    // Properti UI State
    public $search = '';
    public $filterCategory = null;
    public bool $showProductModal = false;
    public bool $showDeleteModal = false;
    public bool $isEditMode = false;

    // Properti untuk Sorting
    public string $sortField = 'name';
    public bool $sortAsc = true;

    // Properti untuk Form (Sesuai dengan view Anda)
    public ?int $productId = null;
    public string $name = '';
    public string $sku = '';
    public ?int $category_id = null;
    public ?int $supplier_id = null;
    public string $description = '';
    public $purchase_price = '';
    public $selling_price = '';
    public $low_stock_threshold = '';
    public bool $is_active = true;
    public $newProductImage = null;
    public ?string $existingImagePath = null;

    // PERBAIKAN: Properti yang hilang dideklarasikan kembali
    public $categories = [];
    public $suppliers = [];

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $this->productId,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            'newProductImage' => 'nullable|image|max:2048',
            'low_stock_threshold' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function mount(ProductService $productService)
    {
        // Metode ini sekarang akan mengisi properti yang sudah dideklarasikan
        $this->categories = $productService->getAllCategories();
        $this->suppliers = $productService->getAllSuppliers();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }
        $this->sortField = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    private function resetForm()
    {
        $this->resetExcept('categories', 'suppliers', 'sortField', 'sortAsc', 'search', 'filterCategory');
        $this->resetErrorBag();
    }

    // Metode ini dipanggil dari tombol 'Tambah Produk'
    public function addProduct()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->is_active = true;
        $this->showProductModal = true;
    }

    public function editProduct(Product $product)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->description = $product->description;
        $this->purchase_price = $product->purchase_price;
        $this->selling_price = $product->selling_price;
        $this->low_stock_threshold = $product->low_stock_threshold;
        $this->category_id = $product->category_id;
        $this->supplier_id = $product->supplier_id;
        $this->is_active = $product->is_active;
        $this->existingImagePath = $product->image_path;
        $this->showProductModal = true;
    }

    // Menggunakan metode yang didelegasikan ke Service
    public function createProduct(ProductService $productService)
    {
        $validatedData = $this->validate();

        try {
            $productService->createProduct($validatedData);
            session()->flash('message', 'Produk baru berhasil ditambahkan.');
            $this->closeModal();
        } catch (Exception $e) {
            session()->flash('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }

    public function updateProduct(ProductService $productService)
    {
        $validatedData = $this->validate();

        try {
            $productService->updateProduct($this->productId, $validatedData);
            session()->flash('message', 'Produk berhasil diperbarui.');
            $this->closeModal();
        } catch (Exception $e) {
            session()->flash('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    public function confirmDelete(int $productId)
    {
        $this->productId = $productId;
        $this->showDeleteModal = true;
    }

    public function deleteProduct(ProductService $productService)
    {
        try {
            $productService->deleteProduct($this->productId);
            session()->flash('message', 'Produk berhasil dihapus.');
        } catch (Exception $e) {
            session()->flash('error', 'Gagal menghapus produk: ' . $e->getMessage());
        } finally {
            $this->closeModal();
        }
    }

    public function closeModal()
    {
        $this->showProductModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    public function render(ProductService $productService)
    {
        $categoryId = $this->filterCategory ? (int)$this->filterCategory : null;

        $products = $productService->getPaginatedProducts(
            $this->search,
            $categoryId,
            $this->sortField,
            $this->sortAsc
        );

        return view('livewire.admin-pusat.product-management', [
            'products' => $products,
        ])->layout('layouts.admin-pusat');
    }
}
