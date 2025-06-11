<h1 align="center">🚀 Sistem POS & Inventaris Toko Olahraga 🚀</h1>

<p align="center">
  <strong>Kelola Bisnis Toko Olahraga Anda dengan Cerdas, Efisien, dan Modern!</strong><br>
  Dibangun dengan ❤️ oleh Wahyu.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel 11">
  <img src="https://img.shields.io/badge/Livewire-✓-FB70A9?style=for-the-badge&logo=livewire" alt="Livewire">
  <img src="https://img.shields.io/badge/Jetstream-✓-14B8A6?style=for-the-badge" alt="Jetstream">
  <img src="https://img.shields.io/badge/Tailwind_CSS-✓-38B2AC?style=for-the-badge&logo=tailwind-css" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php" alt="PHP 8.2+">
</p>

## 👋 Selamat Datang di Sistem POS & Inventaris Toko Olahraga!

Pernahkah Anda kesulitan mengelola operasional toko olahraga yang memiliki banyak cabang? Sistem ini hadir sebagai solusi! Aplikasi web inovatif ini dirancang khusus untuk bisnis retail olahraga yang ingin memodernisasi cara mereka mengelola penjualan, inventaris, dan keuangan secara terpusat. Lupakan kerumitan pencatatan manual dan sambut era digital manajemen bisnis yang terintegrasi dan mudah diakses.

Sistem ini memberdayakan **Admin Pusat** dengan alat komprehensif untuk memonitor, dan mengelola seluruh cabang. Di sisi lain, setiap **Cabang** mendapatkan kemudahan untuk mengelola operasionalnya sendiri, mulai dari transaksi kasir hingga stok barang, yang semuanya terhubung secara real-time ke pusat.

✨ **Catatan:** Untuk pengalaman pengguna terbaik dan akses ke semua fitur manajemen yang kaya, kami sangat merekomendasikan penggunaan sistem ini pada perangkat desktop.

## 🌟 Fitur Unggulan yang Membuat Perbedaan

Sistem ini dikemas dengan fitur-fitur canggih untuk memaksimalkan efisiensi bisnis Anda, dibagi berdasarkan peran:

### 👨‍💼 Untuk Admin & Manajer Pusat:

-   📊 **Dashboard Monitoring Terpusat:** Pantau denyut nadi seluruh bisnis Anda! Statistik kunci dari semua cabang dalam satu tampilan dinamis.
-   📦 **Manajemen Produk & Stok Pusat:** Kendalikan setiap produk! CRUD lengkap untuk produk dan kelola stok di gudang pusat.
-   🚚 **Manajemen Pembelian ke Supplier:** Proses pembelian barang dari supplier tercatat dengan rapi.
-   💰 **Laporan Keuangan Konsolidasi:** Dapatkan gambaran keuangan keseluruhan dari semua cabang.
-   📈 **Peramalan Permintaan (Demand Forecasting):** Buat keputusan bisnis yang lebih baik dengan fitur peramalan permintaan produk.
-   👤 **Kontrol Pengguna Terpusat:** Kelola semua akun pengguna di seluruh cabang dan peran.

### 🏢 Untuk Admin & Manajer Cabang:

-   🏠 **Dashboard Cabang Personal:** Lihat ringkasan penjualan dan stok untuk cabang Anda.
-   🗂️ **Manajemen Stok Cabang:** Kelola penerimaan dan pengeluaran barang di cabang Anda dengan mudah.
-   💸 **Laporan Keuangan Cabang:** Pantau performa keuangan cabang Anda secara spesifik.
-   👤 **Manajemen Pengguna Cabang:** Kelola akun untuk kasir di cabang Anda.

### 🛒 Untuk Kasir:

-   🖥️ **Antarmuka Point of Sale (POS) Modern:** Lakukan transaksi penjualan dengan cepat, mudah, dan intuitif.
-   🧾 **Pencatatan Transaksi Real-time:** Setiap transaksi yang Anda buat akan langsung tercatat di sistem pusat dan cabang.

## 🛠️ Dibangun Dengan Teknologi Terkini

Sistem ini memanfaatkan kekuatan teknologi web modern untuk performa dan pengalaman pengguna terbaik:

-   **Framework Backend:** Laravel 11 (Kecepatan, Keamanan, Skalabilitas)
-   **Framework Frontend Dinamis:** Livewire (Interaktivitas Real-time Tanpa Reload Halaman)
-   **Scaffolding Autentikasi & UI:** Laravel Jetstream (Stack Livewire - Fondasi Kuat)
-   **Styling:** Tailwind CSS (Desain Utility-First yang Elegan dan Responsif)
-   **Database:** MySQL (Fleksibel untuk database relasional lain yang didukung Laravel)
-   **Web Server:** Apache/Nginx (atau `php artisan serve` untuk development kilat)
-   **PHP:** Versi 8.2+
-   **Manajemen Dependensi:** Composer (PHP), NPM (JavaScript)

## 🚀 Siap Memulai? Panduan Instalasi Cepat

Ikuti langkah-langkah ini untuk menjalankan sistem di lingkungan lokal Anda:

1.  **Clone Repositori Ini:**
    `bash
    git clone [https://github.com/wahyu2021/toko-olahraga-pos.git](https://github.com/wahyu2021/toko-olahraga-pos.git)
    cd toko-olahraga-pos
    `

2.  **Instal Dependensi PHP:**
    `bash
    composer install
    `

3.  **Persiapkan File Environment Anda:**
    Salin `.env.example` menjadi `.env`:
    `bash
    cp .env.example .env
    `

4.  **Generate Kunci Aplikasi:**
    `bash
    php artisan key:generate
    `

5.  **Atur Koneksi ke Database Anda (di file `.env`):**
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_toko_olahraga # Sesuaikan!
    DB_USERNAME=root      # Sesuaikan!
    DB_PASSWORD=          # Sesuaikan!

APP_URL=http://localhost:8000 # Penting untuk URL yang benar!
    ```

6.  **Bangun Struktur Data Anda (Migrasi & Seeder):**
    `bash
    php artisan migrate --seed
    `
    _(Seeder akan mengisi data awal untuk peran, pengguna, produk, cabang, dll.)_

7.  **Pasang Aset Frontend:**
    `bash
    npm install && npm run dev
    `
    _(Untuk produksi: `npm run build`)_

8.  **Aktifkan Link Penyimpanan (Storage Link):**
    `bash
    php artisan storage:link
    `

9.  **Nyalakan Servernya! (Development Server):**
    `bash
    php artisan serve
    `
    🎉 Aplikasi Anda siap di `http://localhost:8000`! 🎉

## 💡 Cara Menggunakan

Setelah setup berhasil:

-   Buka aplikasi di browser Anda (`http://localhost:8000`).
-   **Login Akun:**
    -   🔒 Ingat! Akun default dibuat oleh Seeder.
    -   Password untuk semua akun adalah: `password`
    -   **Contoh Akun (dari Seeder):**

| Peran                | Email                        |
| -------------------- | ---------------------------- |
| **Admin Pusat**      | `adminpusat@example.com`     |
| **Manajer Pusat**    | `manajerpusat@example.com`   |
| **Admin Cabang 1**   | `admincabang1@example.com`   |
| **Manajer Cabang 1** | `manajercabang1@example.com` |
| **Kasir Cabang 1**   | `kasir1@example.com`         |
| **Admin Cabang 2**   | `admincabang2@example.com`   |
| **Manajer Cabang 2** | `manajercabang2@example.com` |
| **Kasir Cabang 2**   | `kasir2@example.com`         |

## 🙏 Ucapan Terima Kasih

Proyek ini merupakan hasil karya dan dedikasi oleh **Wahyu**.
