<?php

namespace App\Livewire\AdminPusat;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Validation\Rule;

class ProductManagement extends Component
{
    use WithPagination, WithFileUploads; 

    public $search = '';
    public $sortField = 'name';
    public $sortAsc = true;
    public $filterCategory = '';

    public $productId;
    public $name, $sku, $description, $category_id, $supplier_id;
    public $purchase_price, $selling_price, $low_stock_threshold;
    public $is_active = true;

    // Properti untuk gambar
    public $newProductImage; // Untuk file upload baru
    public $existingImagePath; // Untuk path gambar yang sudah ada saat edit

    public $categories = [];
    public $suppliers = [];

    public $showProductModal = false;
    public $showDeleteModal = false;
    public $isEditMode = false;

    protected $paginationTheme = 'tailwind';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => ['required', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($this->productId)],
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:purchase_price',
            'newProductImage' => 'nullable|image|max:2048', // Validasi untuk gambar baru (maks 2MB)
            'low_stock_threshold' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'selling_price.gte' => 'Harga jual harus lebih besar atau sama dengan harga beli.',
            'newProductImage.image' => 'File yang diupload harus berupa gambar.',
            'newProductImage.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }

    public function mount()
    {
        $this->categories = Category::orderBy('name')->get();
        $this->suppliers = Supplier::orderBy('name')->get();
    }

    public function sortBy($field) // ... (tetap sama)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }
        $this->sortField = $field;
    }


    public function updatingSearch() // ... (tetap sama)
    {
        $this->resetPage();
    }

    public function updatingFilterCategory() // ... (tetap sama)
    {
        $this->resetPage();
    }

    public function addProduct()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showProductModal = true;
    }

    public function createProduct()
    {
        $validatedData = $this->validate();
        $imagePath = null;

        if ($this->newProductImage) {
            $imagePath = $this->newProductImage->store('products', 'public'); // Simpan di storage/app/public/products
        }

        Product::create([
            'name' => $validatedData['name'],
            'sku' => $validatedData['sku'],
            'description' => $validatedData['description'],
            'category_id' => $validatedData['category_id'],
            'supplier_id' => $validatedData['supplier_id'],
            'purchase_price' => $validatedData['purchase_price'],
            'selling_price' => $validatedData['selling_price'],
            'image_path' => $imagePath, // Simpan path gambar
            'low_stock_threshold' => $validatedData['low_stock_threshold'],
            'is_active' => $validatedData['is_active'],
        ]);

        $this->showProductModal = false;
        session()->flash('message', 'Produk berhasil ditambahkan.');
        $this->resetForm();
    }

    public function editProduct(Product $product)
    {
        $this->resetForm();
        $this->isEditMode = true;
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->description = $product->description;
        $this->category_id = $product->category_id;
        $this->supplier_id = $product->supplier_id;
        $this->purchase_price = $product->purchase_price;
        $this->selling_price = $product->selling_price;
        $this->existingImagePath = $product->image_path; // Muat path gambar yang sudah ada
        $this->low_stock_threshold = $product->low_stock_threshold;
        $this->is_active = $product->is_active;
        $this->newProductImage = null; // Pastikan newProductImage kosong saat edit
        $this->showProductModal = true;
    }

    public function updateProduct()
    {
        $validatedData = $this->validate();
        $product = Product::find($this->productId);
        $imagePath = $product->image_path; // Default ke gambar yang sudah ada

        if ($product) {
            if ($this->newProductImage) {
                // Hapus gambar lama jika ada dan gambar baru diupload
                if ($product->image_path) {
                    Storage::disk('public')->delete($product->image_path);
                }
                $imagePath = $this->newProductImage->store('products', 'public');
            }

            $product->update([
                'name' => $validatedData['name'],
                'sku' => $validatedData['sku'],
                'description' => $validatedData['description'],
                'category_id' => $validatedData['category_id'],
                'supplier_id' => $validatedData['supplier_id'],
                'purchase_price' => $validatedData['purchase_price'],
                'selling_price' => $validatedData['selling_price'],
                'image_path' => $imagePath,
                'low_stock_threshold' => $validatedData['low_stock_threshold'],
                'is_active' => $validatedData['is_active'],
            ]);

            $this->showProductModal = false;
            session()->flash('message', 'Produk berhasil diperbarui.');
            $this->resetForm();
        }
    }

    public function confirmDelete($productId) // ... (tetap sama)
    {
        $this->productId = $productId;
        $this->showDeleteModal = true;
    }

    public function deleteProduct()
    {
        $product = Product::find($this->productId);
        if ($product) {
            // Hapus gambar dari storage jika ada
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $product->delete();
            session()->flash('message', 'Produk berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->productId = null;
        $this->name = '';
        $this->sku = '';
        $this->description = '';
        $this->category_id = null;
        $this->supplier_id = null;
        $this->purchase_price = '';
        $this->selling_price = '';
        $this->low_stock_threshold = 0;
        $this->is_active = true;

        $this->newProductImage = null; // Reset properti gambar baru
        $this->existingImagePath = null; // Reset path gambar yang ada
        $this->resetErrorBag();
    }

    // Dipanggil saat ada file baru yang dipilih untuk newProductImage
    public function updatedNewProductImage()
    {
        $this->validateOnly('newProductImage');
    }


    public function closeModal() // ... (tetap sama)
    {
        $this->showProductModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }


    public function render() // ... (render Anda tetap sama, hanya pastikan $products di-pass)
    {
        $productsQuery = Product::with(['category', 'supplier'])
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            });

        if (!empty($this->filterCategory)) {
            $productsQuery->where('category_id', $this->filterCategory);
        }

        $products = $productsQuery->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate(10);

        return view('livewire.admin-pusat.product-management', [
            'products' => $products,
        ])->layout('layouts.admin-pusat');
    }
}
