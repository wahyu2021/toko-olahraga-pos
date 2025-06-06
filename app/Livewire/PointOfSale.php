<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Stock;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\User; // Pastikan diimport dan namespace benar
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PointOfSale extends Component
{
    public $searchInput = '';
    public $cart = []; // Format: ['product_id' => ['id', 'name', 'sku', 'price', 'quantity', 'stock_at_branch', 'subtotal', 'image_path', 'purchase_price_at_sale']]
    public $customerName = '';

    public $subTotal = 0;
    public $grandTotal = 0;
    public $discount = 0; // Placeholder, bisa dikembangkan
    public $tax = 0;      // Placeholder, bisa dikembangkan

    public $currentBranchId;
    public $lastFoundProductInfo = null; // Untuk menampilkan info produk yang baru discan/dicari

    public $showReceiptModal = false;
    public $receiptData = null; // Untuk menyimpan data sale yang akan dicetak

    public $paymentMethods = [];
    public $selectedPaymentMethod = 'cash'; // Metode pembayaran default

    protected $listeners = ['productScanned' => 'handleProductScanned'];

    protected function rules()
    {
        $validPaymentMethods = array_keys($this->paymentMethods);
        // Fallback jika $paymentMethods belum terisi saat validasi awal (seharusnya sudah di mount)
        if (empty($validPaymentMethods)) {
            $validPaymentMethods = ['cash', 'debit_card', 'credit_card', 'qris', 'ewallet_gopay', 'ewallet_ovo', 'ewallet_dana', 'ewallet_shopeepay'];
        }

        return [
            'customerName' => 'nullable|string|max:255',
            'selectedPaymentMethod' => ['required', Rule::in($validPaymentMethods)],
        ];
    }

    protected function messages()
    {
        return [
            'customerName.max' => 'Nama pelanggan terlalu panjang.',
            'selectedPaymentMethod.required' => 'Metode pembayaran wajib dipilih.',
            'selectedPaymentMethod.in' => 'Metode pembayaran yang dipilih tidak valid.',
        ];
    }

    public function mount()
    {
        $user = Auth::user();
        if ($user && $user->branch_id) {
            $this->currentBranchId = $user->branch_id;
        } else {
            session()->flash('error', 'Kasir tidak terasosiasi dengan cabang manapun. Hubungi Admin.');
            Log::warning('User POS tanpa branch_id: ' . ($user ? $user->id : 'Guest/Unknown'), [
                'user_name' => $user ? $user->name : 'Guest/Unknown'
            ]);
            // Pertimbangkan untuk me-redirect atau menonaktifkan fungsionalitas jika branch_id tidak ada
        }

        $this->paymentMethods = [
            'cash' => 'Tunai (Cash)',
            'debit_card' => 'Kartu Debit',
            'credit_card' => 'Kartu Kredit',
            'qris' => 'QRIS',
            'ewallet_gopay' => 'GoPay',
            'ewallet_ovo' => 'OVO',
            'ewallet_dana' => 'DANA',
            'ewallet_shopeepay' => 'ShopeePay',
            // Tambahkan metode lain sesuai kebutuhan
        ];

        // Pastikan default selectedPaymentMethod valid dan ada dalam daftar
        if (!array_key_exists($this->selectedPaymentMethod, $this->paymentMethods)) {
            $this->selectedPaymentMethod = 'cash'; // Fallback ke 'cash'
        }

        $this->calculateTotals(); // Hitung total awal (kemungkinan 0)
    }

    // Coba ubah parameter method ini:
    public function handleProductScanned(string $sku) // Terima langsung string SKU
    {
        Log::info('handleProductScanned dipanggil dengan SKU (parameter langsung):', ['sku_param' => $sku]);

        if ($sku !== null && !empty(trim($sku))) {
            
            $this->searchInput = trim($sku);
            $this->searchAndAddProduct();
        } else {
            Log::warning('handleProductScanned menerima SKU kosong atau null sebagai parameter langsung.', ['received_sku' => $sku]);
            session()->flash('error', 'Gagal memproses hasil scan QR. Data SKU tidak valid/kosong (direct).');
        }
    }

    public function searchAndAddProduct()
    {
        $trimmedSearchInput = trim($this->searchInput);
        if (empty($trimmedSearchInput) || !$this->currentBranchId) {
            $this->lastFoundProductInfo = ['error' => 'Input SKU atau ID Produk tidak boleh kosong.'];
            $this->searchInput = ''; // Bersihkan input setelah pesan error
            return;
        }

        $product = Product::where(function ($query) use ($trimmedSearchInput) {
            $query->where('sku', $trimmedSearchInput)
                ->orWhere('id', $trimmedSearchInput); // Memungkinkan input ID produk juga
        })
            ->where('is_active', true)
            ->first();

        if ($product) {
            $stock = Stock::where('product_id', $product->id)
                ->where('branch_id', $this->currentBranchId)
                ->first();

            $stockAtBranch = $stock ? $stock->quantity : 0;
            $quantityInCart = isset($this->cart[$product->id]) ? $this->cart[$product->id]['quantity'] : 0;

            if ($stockAtBranch > $quantityInCart) {
                $this->addToCart($product, $stockAtBranch);
                $this->lastFoundProductInfo = [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->selling_price,
                    'stock' => $stockAtBranch,
                    'image_path' => $product->image_path,
                    'success' => true
                ];
            } else {
                $this->lastFoundProductInfo = [
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'error' => 'Stok produk habis atau tidak mencukupi di cabang ini (' . $stockAtBranch . ' tersedia, ' . $quantityInCart . ' di keranjang).'
                ];
                session()->flash('cartError', 'Stok produk ' . $product->name . ' habis atau tidak mencukupi.');
            }
        } else {
            $this->lastFoundProductInfo = ['error' => 'Produk dengan SKU/ID "' . $trimmedSearchInput . '" tidak ditemukan atau tidak aktif.'];
            session()->flash('cartError', 'Produk tidak ditemukan.');
        }
        $this->searchInput = ''; // Reset input setelah pencarian
    }

    public function addToCart(Product $product, $stockAtBranch)
    {
        if (isset($this->cart[$product->id])) {
            if ($this->cart[$product->id]['quantity'] < $stockAtBranch) {
                $this->cart[$product->id]['quantity']++;
            } else {
                session()->flash('cartError', 'Kuantitas ' . $product->name . ' melebihi stok yang tersedia (' . $stockAtBranch . ').');
                return; // Hentikan penambahan jika stok tidak cukup
            }
        } else {
            $this->cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => (float)$product->selling_price,
                'purchase_price_at_sale' => (float)$product->purchase_price, // Untuk HPP
                'quantity' => 1,
                'stock_at_branch' => $stockAtBranch, // Simpan info stok saat item ditambah
                'image_path' => $product->image_path,
                // 'subtotal' akan dihitung di calculateTotals
            ];
        }
        $this->calculateTotals();
    }

    public function incrementItem($productId)
    {
        if (isset($this->cart[$productId])) {
            $item = $this->cart[$productId];
            if ($item['quantity'] < $item['stock_at_branch']) {
                $this->cart[$productId]['quantity']++;
                $this->calculateTotals();
            } else {
                session()->flash('cartError', 'Kuantitas ' . $item['name'] . ' melebihi stok (' . $item['stock_at_branch'] . ').');
            }
        }
    }

    public function decrementItem($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] > 1) {
                $this->cart[$productId]['quantity']--;
            } else {
                unset($this->cart[$productId]); // Hapus item jika kuantitas menjadi 0 atau kurang
            }
            $this->calculateTotals();
        }
    }

    public function removeItem($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subTotal = 0;
        foreach ($this->cart as $key => $item) {
            $price = is_numeric($item['price']) ? (float)$item['price'] : 0;
            $quantity = is_numeric($item['quantity']) ? (int)$item['quantity'] : 0;
            $this->cart[$key]['subtotal'] = $price * $quantity;
            $this->subTotal += $this->cart[$key]['subtotal'];
        }
        // Implementasi diskon dan pajak bisa ditambahkan di sini
        // $this->grandTotal = ($this->subTotal - $this->discount) + $this->tax;
        $this->grandTotal = $this->subTotal; // Untuk saat ini, grandTotal = subTotal
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->customerName = '';
        $this->lastFoundProductInfo = null;
        $this->selectedPaymentMethod = 'cash'; // Reset metode pembayaran ke default
        session()->forget('cartError'); // Hapus juga pesan error keranjang
        $this->calculateTotals(); // Hitung ulang total menjadi 0
    }

    public function processSale()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang belanja kosong.');
            return;
        }
        if (!$this->currentBranchId) {
            session()->flash('error', 'Cabang kasir tidak valid. Hubungi Admin.');
            return;
        }

        $this->validate(); // Validasi customerName dan selectedPaymentMethod

        DB::beginTransaction();
        try {
            $sale = Sale::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-' . Str::upper(Str::random(6)),
                'branch_id' => $this->currentBranchId,
                'user_id' => Auth::id(),
                'customer_name' => trim($this->customerName) ?: null,
                'subtotal_amount' => $this->subTotal,
                'discount_amount' => $this->discount,
                'tax_amount' => $this->tax,
                'total_amount' => $this->grandTotal,
                'payment_method' => $this->selectedPaymentMethod,
                'status' => 'completed',
                'sale_date' => now(),
            ]);

            foreach ($this->cart as $item) {
                // Pengecekan stok sekali lagi sebelum menyimpan item (best practice)
                $stock = Stock::where('product_id', $item['id'])
                    ->where('branch_id', $this->currentBranchId)
                    ->lockForUpdate() // Lock row untuk mencegah race condition
                    ->first();

                if (!$stock || $stock->quantity < $item['quantity']) {
                    // Buat pesan error lebih spesifik
                    $productName = $item['name'] ?? 'Produk dengan ID ' . $item['id'];
                    throw new \Exception('Stok tidak mencukupi untuk produk "' . $productName . '". Sisa stok: ' . ($stock->quantity ?? 0) . ', diminta: ' . $item['quantity'] . '. Transaksi dibatalkan.');
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'purchase_price_at_sale' => $item['purchase_price_at_sale'],
                    'subtotal' => $item['subtotal'],
                ]);

                $quantityBefore = $stock->quantity;
                $stock->quantity -= $item['quantity'];
                $stock->save();

                StockMovement::create([
                    'product_id' => $item['id'],
                    'branch_id' => $this->currentBranchId,
                    'user_id' => Auth::id(),
                    'type' => 'sale',
                    'quantity_change' => -$item['quantity'],
                    'quantity_before' => $quantityBefore,
                    'quantity_after' => $stock->quantity,
                    'referenceable_id' => $sale->id,
                    'referenceable_type' => Sale::class,
                    'movement_date' => now(),
                ]);
            }

            DB::commit();

            $loadedSaleData = Sale::with(['branch', 'user', 'items.product'])->find($sale->id);

            if ($loadedSaleData) {
                $this->receiptData = $loadedSaleData;
                $this->showReceiptModal = true;
                session()->flash('message', 'Transaksi berhasil! Invoice: ' . $sale->invoice_number);
            } else {
                session()->flash('error', 'Transaksi berhasil disimpan, tetapi gagal memuat data struk.');
                Log::error('Gagal memuat data struk setelah penjualan untuk Sale ID: ' . $sale->id);
            }

            $this->clearCart();
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Validasi gagal saat proses penjualan: ', $e->errors());
            // Pesan error validasi akan otomatis ditampilkan oleh Livewire jika ada error bag di view
            // Jika tidak, Anda bisa flash errornya
            $firstMessage = $e->validator->errors()->first();
            session()->flash('error', 'Validasi gagal: ' . $firstMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Transaksi gagal: ' . $e->getMessage());
            Log::error('Transaksi POS Gagal: ' . $e->getMessage(), [
                'exception' => $e,
                'cart' => $this->cart,
                'user_id' => Auth::id(),
                'branch_id' => $this->currentBranchId
            ]);
            $this->receiptData = null; // Pastikan data struk kosong jika gagal
            $this->showReceiptModal = false; // Pastikan modal tidak tampil
        }
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->receiptData = null;
    }

    public function render()
    {
        return view('livewire.point-of-sale')
            ->layout('layouts.pos');
    }
}
