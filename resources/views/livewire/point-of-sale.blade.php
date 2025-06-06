{{-- resources/views/livewire/point-of-sale.blade.php --}}
<div class="flex flex-col lg:flex-row h-[calc(100vh-4rem)]" x-data> {{-- 4rem adalah tinggi header asumsi dari layouts/pos.blade.php --}}

    {{-- Panel Kiri (Lebih Besar): Input Produk dan Daftar Keranjang --}}
    <div class="w-full lg:w-7/12 flex flex-col bg-gray-50 p-3 sm:p-4 overflow-y-auto">
        {{-- Input Produk Manual --}}
        <div class="mb-4 sticky top-0 bg-gray-50 py-2 z-10">
            <form wire:submit.prevent="searchAndAddProduct" class="flex gap-2">
                <input wire:model="searchInput" type="text" id="searchInput"
                    class="flex-grow block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                    placeholder="Masukkan SKU atau ID Produk" autofocus x-ref="searchInputPOS">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 sm:mr-2" />
                    <span class="hidden sm:inline">Cari</span>
                </button>
            </form>
        </div>

        {{-- Info Produk Terakhir Ditemukan --}}
        <div class="mb-4 p-3 border border-gray-200 rounded-lg bg-white min-h-[120px]">
            <h3 class="text-xs font-medium text-gray-500 mb-1">Info Produk Terakhir:</h3>
            @if ($lastFoundProductInfo)
                @if (isset($lastFoundProductInfo['success']))
                    <div class="flex items-start gap-3">
                        @if ($lastFoundProductInfo['image_path'])
                            <img src="{{ Storage::url($lastFoundProductInfo['image_path']) }}"
                                alt="{{ $lastFoundProductInfo['name'] }}"
                                class="h-16 w-16 object-cover rounded flex-shrink-0">
                        @else
                            <div
                                class="h-16 w-16 bg-gray-100 rounded flex items-center justify-center text-gray-400 text-xs flex-shrink-0">
                                N/A</div>
                        @endif
                        <div class="flex-grow">
                            <p class="text-sm font-semibold text-gray-700 leading-tight">
                                {{ $lastFoundProductInfo['name'] }}</p>
                            <p class="text-xs text-gray-500">SKU: {{ $lastFoundProductInfo['sku'] }}</p>
                            <p class="text-xs text-gray-500">Harga: Rp
                                {{ number_format($lastFoundProductInfo['price'], 0, ',', '.') }}</p>
                            <p
                                class="text-xs font-medium {{ $lastFoundProductInfo['stock'] > 5 ? 'text-green-600' : ($lastFoundProductInfo['stock'] > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                Stok: {{ $lastFoundProductInfo['stock'] }}
                            </p>
                        </div>
                    </div>
                    <p class="mt-1 text-green-600 text-xs font-medium">Produk ditambahkan/diupdate di keranjang.</p>
                @elseif(isset($lastFoundProductInfo['error']))
                    <div class="flex items-start gap-3">
                        @if (isset($lastFoundProductInfo['name']))
                            <div
                                class="h-16 w-16 bg-gray-100 rounded flex items-center justify-center text-gray-400 text-xs flex-shrink-0">
                                Info</div>
                            <div class="flex-grow">
                                <p class="text-sm font-semibold text-gray-700 leading-tight">
                                    {{ $lastFoundProductInfo['name'] }}</p>
                                <p class="text-xs text-gray-500">SKU: {{ $lastFoundProductInfo['sku'] }}</p>
                            </div>
                        @endif
                    </div>
                    <p class="mt-1 text-red-600 text-sm font-medium">{{ $lastFoundProductInfo['error'] }}</p>
                @endif
            @else
                <p class="text-gray-400 text-center text-xs py-4">Scan atau masukkan SKU untuk melihat info produk.</p>
            @endif
        </div>

        @if (session()->has('cartError'))
            <div class="mb-3 p-2.5 rounded-md bg-red-100 text-red-700 text-xs" role="alert">
                {{ session('cartError') }}
            </div>
        @endif

        {{-- Daftar Item Keranjang --}}
        <h2 class="text-lg font-semibold text-gray-700 mb-2 mt-2">Keranjang Belanja</h2>
        <div class="flex-grow overflow-y-auto border rounded-md border-gray-200 bg-white min-h-[200px]">
            @if (empty($cart))
                <div class="flex items-center justify-center h-full">
                    <p class="text-gray-500 text-center py-10">Keranjang masih kosong.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach ($cart as $item)
                        <div wire:key="cart-item-{{ $item['id'] }}"
                            class="flex items-center justify-between p-3 hover:bg-orange-50 transition-colors duration-150">
                            <div class="flex items-center flex-grow min-w-0 mr-2">
                                @if ($item['image_path'])
                                    <img src="{{ Storage::url($item['image_path']) }}" alt="{{ $item['name'] }}"
                                        class="h-10 w-10 object-cover rounded mr-3 flex-shrink-0">
                                @else
                                    <div
                                        class="h-10 w-10 bg-gray-200 rounded mr-3 flex items-center justify-center text-gray-400 text-xs flex-shrink-0">
                                        N/A</div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800 text-sm truncate" title="{{ $item['name'] }}">
                                        {{ $item['name'] }}</p>
                                    <p class="text-xs text-gray-500">Rp
                                        {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center flex-shrink-0 mx-2">
                                <button wire:click="decrementItem('{{ $item['id'] }}')"
                                    class="p-1 text-gray-500 hover:text-red-600 rounded-full focus:outline-none focus:ring-2 focus:ring-red-300">
                                    <x-heroicon-o-minus class="w-5 h-5" />
                                </button>
                                <input type="text" value="{{ $item['quantity'] }}" readonly
                                    class="mx-1 w-10 text-center border-0 bg-transparent text-sm font-medium focus:ring-0 p-0">
                                <button wire:click="incrementItem('{{ $item['id'] }}')"
                                    class="p-1 text-gray-500 hover:text-green-600 rounded-full focus:outline-none focus:ring-2 focus:ring-green-300">
                                    <x-heroicon-o-plus class="w-5 h-5" />
                                </button>
                            </div>
                            <div class="text-right w-24 flex-shrink-0 font-semibold text-gray-700 text-sm">
                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                            </div>
                            <button wire:click="removeItem('{{ $item['id'] }}')"
                                class="ml-2 p-1 text-gray-400 hover:text-red-600 rounded-full focus:outline-none focus:ring-2 focus:ring-red-300">
                                <x-heroicon-o-trash class="w-5 h-5" />
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Panel Kanan (Lebih Kecil): Total, Pembayaran, dan QR SCANNER --}}
    <div
        class="w-full lg:w-5/12 bg-white p-3 sm:p-4 flex flex-col border-t lg:border-t-0 lg:border-l border-gray-200 overflow-y-auto">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Ringkasan & Pembayaran</h2>

        <div class="mb-4">
            <label for="customerName" class="block text-sm font-medium text-gray-700">Nama Pelanggan (Opsional)</label>
            <input wire:model.lazy="customerName" type="text" id="customerName"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm"
                placeholder="Masukkan nama pelanggan">
            @error('customerName')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="selectedPaymentMethod" class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
            <select wire:model.lazy="selectedPaymentMethod" id="selectedPaymentMethod"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm @error('selectedPaymentMethod') border-red-500 @enderror">
                @foreach ($paymentMethods as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            @error('selectedPaymentMethod')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        {{-- === AREA SCANNER QR CODE === --}}
        <div class="mb-4 border-t pt-4">
            <p class="block text-sm font-medium text-gray-700 mb-2">Opsi Scan QR Code Produk</p>

            {{-- Opsi 1: Live Camera Scan --}}
            <div class="mb-3 p-3 border border-gray-200 rounded-md">
                <p class="text-xs text-gray-600 mb-2 font-medium">1. Gunakan Kamera Perangkat:</p>
                <div id="qr-reader" style="width: 100%; max-width:300px; margin:auto; display:none;"
                    class="border border-gray-300 rounded-md bg-gray-100 aspect-[4/3] mb-2"></div>
                <div id="qr-camera-status" class="mt-1 mb-2 text-sm text-center h-6"></div>

                <div class="grid grid-cols-1 gap-2 mb-2">
                    <button type="button" id="startCameraButton"
                        class="w-full inline-flex items-center justify-center rounded-md border border-transparent bg-teal-500 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                        <x-heroicon-o-camera class="w-5 h-5 mr-2" />
                        Buka Kamera & Scan
                    </button>
                </div>
                <button type="button" id="stopCameraButton" style="display:none;"
                    class="w-full inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2">
                    Tutup Kamera
                </button>
            </div>

            {{-- Pemisah --}}
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center"><span
                        class="bg-white px-3 text-sm font-medium text-gray-500">ATAU</span></div>
            </div>

            {{-- Opsi 2: Upload Gambar QR --}}
            <div class="p-3 border border-gray-200 rounded-md">
                <p class="text-xs text-gray-600 mb-2 font-medium">2. Upload Gambar QR Code:</p>
                <input type="file" id="qrImageFile" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 mb-2" />
                <div id="qr-file-upload-preview" class="my-2 flex justify-center"></div>
                <button type="button" id="scanUploadedImageButton"
                    class="w-full inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-500 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                    disabled>
                    <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2" />
                    Scan Gambar Pilihan
                </button>
                <div id="qr-file-scan-results" class="mt-2 text-sm text-center h-6"></div>
            </div>
        </div>
        {{-- === AKHIR AREA SCANNER QR CODE === --}}

        <div class="space-y-2 text-sm mb-4 flex-grow">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Subtotal ({{ count($cart) }} item):</span>
                <span class="font-medium text-gray-800">Rp {{ number_format($subTotal, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="border-t-2 border-dashed pt-4 mt-auto">
            <div class="flex justify-between items-center text-2xl font-bold text-orange-600 mb-6">
                <span>Total Bayar:</span>
                <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <button wire:click="clearCart"
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-4 rounded-lg shadow-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-opacity-75">
                    Batal Transaksi
                </button>
                <button wire:click="processSale" wire:loading.attr="disabled" {{ empty($cart) ? 'disabled' : '' }}
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg shadow-md transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-opacity-75 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading wire:target="processSale" class="mr-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </span>
                    <span wire:loading wire:target="processSale">Memproses...</span>
                    <span wire:loading.remove wire:target="processSale">Proses Bayar</span>
                </button>
            </div>
        </div>
        @if (session()->has('message'))
            <div class="mt-4 p-3 rounded-md bg-green-100 text-green-700 text-sm" role="alert">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error') && !session()->has('cartError'))
            <div class="mt-4 p-3 rounded-md bg-red-100 text-red-700 text-sm" role="alert">
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- Modal Struk Pembelian --}}
    @if ($showReceiptModal && $receiptData)
        <div class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title-receipt" role="dialog"
            aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeReceiptModal"
                    aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div id="receipt-content-wrapper"
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle w-full sm:max-w-xs">
                    <div class="p-4 text-sm font-mono" id="receipt-printable-area">
                        <div class="text-center mb-3">
                            <h2 class="text-lg font-semibold">
                                {{ $receiptData->branch->name ?? config('app.name', 'Toko Anda') }}</h2>
                            <p class="text-xs">{{ $receiptData->branch->address ?? 'Alamat Toko' }}</p>
                            <p class="text-xs">Telp: {{ $receiptData->branch->phone ?? '-' }}</p>
                        </div>
                        <hr class="my-2 border-dashed">
                        <div class="flex justify-between text-xs">
                            <span>No: {{ $receiptData->invoice_number }}</span>
                            <span>{{ \Carbon\Carbon::parse($receiptData->sale_date)->isoFormat('DD/MM/YY HH:mm') }}</span>
                        </div>
                        <div class="text-xs">Kasir: {{ $receiptData->user->name ?? '-' }}</div>
                        @if ($receiptData->customer_name)
                            <div class="text-xs">Pelanggan: {{ $receiptData->customer_name }}</div>
                        @endif
                        <div class="text-xs">Pembayaran:
                            {{ $paymentMethods[$receiptData->payment_method] ?? $receiptData->payment_method }}</div>
                        <hr class="my-2 border-dashed">
                        @foreach ($receiptData->items as $item)
                            <div class="mb-1">
                                <div class="text-xs">{{ $item->product->name ?? 'Produk Dihapus' }}</div>
                                <div class="flex justify-between text-xs">
                                    <span>{{ $item->quantity }}x @
                                        Rp{{ number_format($item->unit_price, 0, ',', '.') }}</span>
                                    <span>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endforeach
                        <hr class="my-2 border-dashed">
                        <div class="flex justify-between text-xs">
                            <span>SUBTOTAL</span>
                            <span
                                class="font-semibold">Rp{{ number_format($receiptData->subtotal_amount, 0, ',', '.') }}</span>
                        </div>
                        @if ($receiptData->discount_amount > 0)
                            <div class="flex justify-between text-xs">
                                <span>DISKON</span>
                                <span
                                    class="font-semibold">-Rp{{ number_format($receiptData->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <hr class="my-1 border-dashed">
                        <div class="flex justify-between text-xs font-bold">
                            <span>TOTAL</span>
                            <span>Rp{{ number_format($receiptData->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <hr class="my-2 border-dashed">
                        <div class="text-center text-xs mt-3">
                            Terima kasih atas kunjungan Anda!
                            <p>{{ config('app.name', 'Toko Anda') }}</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="printReceipt()"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-500 text-base font-medium text-white hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Cetak Struk
                        </button>
                        <button wire:click="closeReceiptModal" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function printReceipt() {
                const printContents = document.getElementById('receipt-printable-area').innerHTML;
                const originalContents = document.body.innerHTML;
                const receiptStyles =
                    `<style> @media print { @page { size: 58mm auto; margin: 0mm; } body { margin:0; font-family:monospace; font-size:9pt;} img {display:none;} .text-center {text-align:center;} .mb-3{margin-bottom:0.75rem;} .text-lg{font-size:1.125rem;} .font-semibold{font-weight:600;} .text-xs{font-size:0.75rem;line-height:1rem;} hr.my-2{margin-top:0.5rem;margin-bottom:0.5rem;border-style:dashed;border-top-width:1px;} .flex{display:flex;} .justify-between{justify-content:space-between;} .mb-1{margin-bottom:0.25rem;} .mt-3{margin-top:0.75rem;} body * {visibility: hidden;} #receipt-printable-area, #receipt-printable-area * {visibility: visible;} #receipt-printable-area {position:absolute;left:0;top:0;width:100%;} } </style>`;
                document.body.innerHTML = receiptStyles + printContents;
                window.print();
                document.body.innerHTML = originalContents;
                Livewire.dispatch('receiptClosed');
            }
        </script>
    @endif
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            const searchInputPOS = document.querySelector('[x-ref="searchInputPOS"]');
            if (searchInputPOS) {
                if (!document.querySelector('.fixed.inset-0.z-\\[100\\][aria-labelledby="modal-title-receipt"]')) {
                    searchInputPOS.focus();
                }
            }

            if (typeof window.Html5Qrcode === 'undefined') {
                console.error(
                    "Html5Qrcode library tidak ditemukan. Pastikan sudah diimpor di app.js/bootstrap.js dan dikompilasi."
                );
                const qrCameraStatus = document.getElementById('qr-camera-status');
                const qrFileScanResults = document.getElementById('qr-file-scan-results');
                if (qrCameraStatus) qrCameraStatus.innerHTML =
                    '<span class="text-red-600 text-xs">Error: Gagal memuat QR Scanner.</span>';
                if (qrFileScanResults) qrFileScanResults.innerHTML =
                    '<span class="text-red-600 text-xs">Error: Gagal memuat QR Scanner.</span>';
                return;
            }

            const html5QrCode = new window.Html5Qrcode("qr-reader", {
                verbose: false
            });
            let isCameraScanning = false;

            const qrReaderElement = document.getElementById('qr-reader');
            const startCameraButton = document.getElementById('startCameraButton');
            const stopCameraButton = document.getElementById('stopCameraButton');
            const qrCameraStatus = document.getElementById('qr-camera-status');

            const qrImageFile = document.getElementById('qrImageFile');
            const scanUploadedImageButton = document.getElementById('scanUploadedImageButton');
            const qrFileScanResults = document.getElementById('qr-file-scan-results');
            const qrFileUploadPreview = document.getElementById('qr-file-upload-preview');

            const stopCameraAndResetUI = (statusMessage = "Kamera ditutup.") => {
                if (isCameraScanning && html5QrCode && typeof html5QrCode.stop === 'function') {
                    return html5QrCode.stop().then(() => {
                        console.log("[Kamera] Scanner dihentikan.");
                    }).catch(err => {
                        console.error("[Kamera] Gagal menghentikan QR Scanner:", err);
                    }).finally(() => {
                        isCameraScanning = false;
                        if (qrReaderElement) qrReaderElement.style.display = 'none';
                        if (startCameraButton) startCameraButton.style.display = 'inline-flex';
                        if (stopCameraButton) stopCameraButton.style.display = 'none';
                        // Hanya set status message umum jika tidak ada pesan spesifik (sukses/error scan) yang akan ditampilkan setelahnya
                        // oleh qrCodeSuccessCallbackCamera
                        const currentStatusText = qrCameraStatus ? qrCameraStatus.innerText : "";
                        if (qrCameraStatus && !currentStatusText.includes("QR Terbaca:") && !
                            currentStatusText.includes("Error: Hasil scan") && !currentStatusText
                            .includes("Memproses...")) {
                            qrCameraStatus.innerHTML = `<span class="text-xs">${statusMessage}</span>`;
                        } else if (qrCameraStatus && currentStatusText.includes("Memproses...")) {
                            // Biarkan pesan "Memproses..." jika ada, akan diupdate oleh callback sukses/error
                        } else if (qrCameraStatus && statusMessage ===
                            "Kamera ditutup.") { // Jika hanya menutup kamera manual
                            qrCameraStatus.innerHTML =
                                `<span class="text-xs">${statusMessage} Klik "Buka Kamera" untuk scan lagi.</span>`;
                        }
                    });
                } else {
                    isCameraScanning = false;
                    if (qrReaderElement) qrReaderElement.style.display = 'none';
                    if (startCameraButton) startCameraButton.style.display = 'inline-flex';
                    if (stopCameraButton) stopCameraButton.style.display = 'none';
                    if (qrCameraStatus) {
                        const currentStatusText = qrCameraStatus.innerText;
                        if (!currentStatusText.includes("QR Terbaca:") && !currentStatusText.includes(
                                "Error: Hasil scan")) {
                            qrCameraStatus.innerHTML = `<span class="text-xs">${statusMessage}</span>`;
                        }
                    }
                    return Promise.resolve();
                }
            };

            const qrCodeSuccessCallbackCamera = (decodedText, decodedResult) => {
                if (!isCameraScanning) {
                    console.log("[Kamera] Scan terdeteksi tapi kamera sudah tidak aktif, diabaikan.");
                    return;
                }

                const skuToProcess = decodedText.trim();

                // Tampilkan pesan "Memproses" terlebih dahulu
                if (qrCameraStatus) {
                    qrCameraStatus.innerHTML =
                        `<span class="text-blue-600 text-xs">QR Terdeteksi: <strong>${skuToProcess}</strong>. Memproses...</span>`;
                }

                // Hentikan kamera
                stopCameraAndResetUI("Memproses hasil scan...").then(
                    () => { // Pesan ini mungkin tidak akan terlihat karena dioverride cepat
                        console.log(`[Kamera] QR Terdeteksi dan kamera dihentikan: ${skuToProcess}`);

                        if (skuToProcess && skuToProcess !== '') {
                            // Update status menjadi sukses setelah kamera pasti berhenti dan sebelum kirim ke Livewire
                            if (qrCameraStatus) {
                                qrCameraStatus.innerHTML =
                                    `<span class="text-green-600 text-xs">QR Terbaca: <strong>${skuToProcess}</strong>. Produk sedang dicari...</span>`;
                            }
                            Livewire.dispatchTo('point-of-sale', 'productScanned', {
                                sku: skuToProcess
                            });
                            if (searchInputPOS) searchInputPOS.value = '';
                            
                        } else {
                            console.error("[Kamera] DecodedText tidak valid untuk dikirim:", skuToProcess);
                            if (qrCameraStatus) qrCameraStatus.innerHTML =
                                `<span class="text-red-600 text-xs">Error: Hasil scan QR tidak valid.</span>`;
                        }

                        setTimeout(() => {
                            if (qrCameraStatus) {
                                const currentText = qrCameraStatus.innerText;
                                if (currentText.includes("QR Terbaca:") || currentText.includes(
                                        "Hasil scan QR tidak valid")) {
                                    qrCameraStatus.innerHTML =
                                        '<span class="text-xs">Kamera nonaktif. Klik "Buka Kamera" untuk scan.</span>';
                                }
                            }
                        }, 4000);

                        if (searchInputPOS && !document.querySelector(
                                '.fixed.inset-0.z-\\[100\\][aria-labelledby="modal-title-receipt"]')) {
                            setTimeout(() => searchInputPOS.focus(), 50);
                        }

                        


                    }).catch(err => {
                    console.error("[Kamera] Gagal menghentikan atau memproses scan:", err);
                    if (qrCameraStatus) qrCameraStatus.innerHTML =
                        `<span class="text-red-600 text-xs">Error internal saat memproses scan kamera.</span>`;
                    // Pastikan UI kamera direset jika ada error saat stop
                    isCameraScanning = false;
                    if (qrReaderElement) qrReaderElement.style.display = 'none';
                    if (startCameraButton) startCameraButton.style.display = 'inline-flex';
                    if (stopCameraButton) stopCameraButton.style.display = 'none';
                });
            };

            const qrCodeErrorCallbackCamera = (errorMessage) => {
                if (isCameraScanning) { // Hanya proses error jika kamera aktif
                    // Hindari log "No QR code found" yang berlebihan jika kamera masih mencari
                    if (errorMessage && !errorMessage.toLowerCase().includes("qr code parse error") && !
                        errorMessage.toLowerCase().includes("not found")) {
                        console.warn(`[Kamera] Pesan error scanner: ${errorMessage}`);
                        // Anda bisa menampilkan error minor di qrCameraStatus jika diperlukan, tapi hati-hati agar tidak terlalu berisik
                        // if (qrCameraStatus) qrCameraStatus.innerHTML = `<span class="text-yellow-600 text-xs">Mencoba membaca QR...</span>`;
                    } else if (errorMessage.toLowerCase().includes("not found") || errorMessage.toLowerCase()
                        .includes("qr code parse error")) {
                        // Status normal ketika kamera aktif dan belum menemukan QR
                        if (qrCameraStatus && isCameraScanning) qrCameraStatus.innerHTML =
                            '<span class="text-xs">Arahkan QR code ke kamera.</span>';
                    }
                }
            };

            const cameraConfig = {
                fps: 5,
                qrbox: {
                    width: 200,
                    height: 150
                },
                rememberLastUsedCamera: true,
            };

            if (startCameraButton && stopCameraButton && qrCameraStatus && qrReaderElement) {
                startCameraButton.addEventListener('click', () => {
                    if (isCameraScanning) return;
                    qrReaderElement.style.display = 'block';
                    qrCameraStatus.innerHTML =
                        '<span class="text-xs text-blue-600">Memulai kamera...</span>';

                    html5QrCode.start({
                            facingMode: "environment"
                        }, cameraConfig, qrCodeSuccessCallbackCamera, qrCodeErrorCallbackCamera)
                        .then(() => {
                            console.log("[Kamera] Scanner berhasil dimulai.");
                            isCameraScanning = true;
                            startCameraButton.style.display = 'none';
                            stopCameraButton.style.display = 'inline-flex';
                            qrCameraStatus.innerHTML =
                                '<span class="text-xs">Arahkan QR code ke kamera.</span>';
                        }).catch(err => {
                            console.error(`[Kamera] Tidak dapat memulai QR Scanner: `, err);
                            qrCameraStatus.innerHTML =
                                `<span class="text-red-600 text-xs">Error Kamera: ${err}. Cek izin & kamera.</span>`;
                            qrReaderElement.style.display = 'none';
                            isCameraScanning = false;
                        });
                });

                stopCameraButton.addEventListener('click', () => {
                    // Pesan "Kamera ditutup." akan di-handle oleh stopCameraAndResetUI
                    stopCameraAndResetUI();
                });
            }

            if (qrImageFile && scanUploadedImageButton && qrFileScanResults && qrFileUploadPreview) {
                qrImageFile.addEventListener('change', (event) => {
                    if (event.target.files && event.target.files.length > 0) {
                        scanUploadedImageButton.disabled = false;
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            qrFileUploadPreview.innerHTML =
                                `<img src="${e.target.result}" alt="Preview" class="max-h-28 rounded-md border"/>`;
                        }
                        reader.readAsDataURL(event.target.files[0]);
                        qrFileScanResults.innerHTML =
                            '<span class="text-xs">Gambar dipilih. Klik "Scan Gambar Pilihan".</span>';
                    } else {
                        scanUploadedImageButton.disabled = true;
                        qrFileUploadPreview.innerHTML = '';
                        qrFileScanResults.innerHTML = '';
                    }
                });

                scanUploadedImageButton.addEventListener('click', () => {
                    if (qrImageFile.files.length === 0) {
                        qrFileScanResults.innerHTML =
                            '<span class="text-yellow-600 text-xs">Pilih file gambar terlebih dahulu.</span>';
                        return;
                    }
                    const imageFile = qrImageFile.files[0];
                    qrFileScanResults.innerHTML =
                        '<span class="text-xs text-blue-600">Memindai gambar...</span>';

                    html5QrCode.scanFile(imageFile, false)
                        .then(decodedText => {
                            const skuFromFile = decodedText.trim();
                            console.log("[Upload File] SUKSES SCAN! Decoded Text:", skuFromFile);

                            if (skuFromFile && skuFromFile !== '') {
                                qrFileScanResults.innerHTML =
                                    `<span class="text-green-600 text-xs">QR File Terbaca: <strong>${skuFromFile}</strong>. Produk sedang dicari...</span>`;
                                Livewire.dispatchTo('point-of-sale', 'productScanned', {
                                    sku: skuFromFile
                                });
                            } else {
                                console.error("[Upload File] DecodedText tidak valid untuk dikirim:",
                                    skuFromFile);
                                qrFileScanResults.innerHTML =
                                    `<span class="text-red-600 text-xs">Error: Hasil scan dari file tidak valid.</span>`;
                            }

                            if (searchInputPOS) searchInputPOS.value = '';
                            qrImageFile.value = "";
                            qrFileUploadPreview.innerHTML = '';
                            scanUploadedImageButton.disabled = true;

                            setTimeout(() => {
                                if (qrFileScanResults) {
                                    const currentText = qrFileScanResults.innerText;
                                    if (currentText.includes("QR File Terbaca:") || currentText
                                        .includes("Hasil scan dari file tidak valid")) {
                                        qrFileScanResults.innerHTML =
                                            '<span class="text-xs">Pilih file QR atau gunakan kamera.</span>';
                                    }
                                }
                            }, 4000);
                        })
                        .catch(err => {
                            console.error(`[Upload File] Error memindai file: ${err}`);
                            qrFileScanResults.innerHTML =
                                `<span class="text-red-600 text-xs">Gagal scan file: ${err}. Pastikan gambar jelas & berisi QR code.</span>`;
                            setTimeout(() => {
                                if (qrFileScanResults && qrFileScanResults.innerHTML.includes(
                                        'Gagal scan file:')) {
                                    qrFileScanResults.innerHTML =
                                        '<span class="text-xs">Pilih file QR atau gunakan kamera.</span>';
                                }
                            }, 4000);
                        });
                });
            }
        });

        Livewire.on('receiptClosed', () => {
            const searchInputPOS = document.querySelector('[x-ref="searchInputPOS"]');
            if (searchInputPOS && !document.querySelector(
                    '.fixed.inset-0.z-\\[100\\][aria-labelledby="modal-title-receipt"]')) {
                setTimeout(() => searchInputPOS.focus(), 50);
            }
        });

        // Fungsi printReceipt sudah ada di dalam script modal, bisa dihapus dari sini jika tidak digunakan secara global
        // Jika tetap ingin ada di sini sebagai fallback atau penggunaan lain:
        function printReceipt() {
            const printContents = document.getElementById('receipt-printable-area');
            if (!printContents) {
                console.error("Elemen struk untuk dicetak tidak ditemukan.");
                return;
            }
            const printableArea = printContents.innerHTML;
            const originalContents = document.body.innerHTML;
            const receiptStyles =
                `<style> @media print { @page { size: 58mm auto; margin: 0mm; } body { margin:0; font-family:monospace; font-size:9pt;} img {display:none;} .text-center {text-align:center;} .mb-3{margin-bottom:0.75rem;} .text-lg{font-size:1.125rem;} .font-semibold{font-weight:600;} .text-xs{font-size:0.75rem;line-height:1rem;} hr.my-2{margin-top:0.5rem;margin-bottom:0.5rem;border-style:dashed;border-top-width:1px;} .flex{display:flex;} .justify-between{justify-content:space-between;} .mb-1{margin-bottom:0.25rem;} .mt-3{margin-top:0.75rem;} body * {visibility: hidden;} #receipt-printable-area, #receipt-printable-area * {visibility: visible;} #receipt-printable-area {position:absolute;left:0;top:0;width:100%;} } </style>`;

            // Buat iframe sementara untuk mencetak
            const iframe = document.createElement('iframe');
            iframe.style.height = '0';
            iframe.style.width = '0';
            iframe.style.position = 'absolute';
            iframe.style.visibility = 'hidden';
            document.body.appendChild(iframe);

            const iframeDoc = iframe.contentWindow.document;
            iframeDoc.open();
            iframeDoc.write('<html><head><title>Struk</title>' + receiptStyles + '</head><body>' + printableArea +
                '</body></html>');
            iframeDoc.close();

            iframe.contentWindow.focus();
            iframe.contentWindow.print();

            // Hapus iframe setelah beberapa saat
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 1000);

            // Kembalikan fokus ke input search jika modal tidak ada
            Livewire.dispatch('receiptClosed');
        }
    </script>
@endpush
