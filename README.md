# Sistem Point of Sale (POS) & Inventaris Toko Olahraga

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel)
![Livewire](https://img.shields.io/badge/Livewire-3.x-4E57E8?style=for-the-badge&logo=livewire)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?style=for-the-badge&logo=tailwindcss)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php)

Sistem ini adalah aplikasi web berbasis Laravel yang dirancang untuk mengelola operasional toko olahraga dengan multi-cabang. Aplikasi ini mencakup sistem kasir (Point of Sale), manajemen inventaris, manajemen pengguna, pelaporan keuangan, hingga peramalan permintaan barang.

## 📖 Tentang Proyek

Proyek ini dibangun untuk mengatasi kebutuhan manajemen toko olahraga yang memiliki pusat dan beberapa cabang. Dengan aplikasi ini, admin pusat dapat memonitor seluruh aktivitas bisnis dari semua cabang, sementara setiap cabang dapat mengelola operasionalnya secara mandiri namun tetap terintegrasi dengan pusat.

### Dibangun Dengan

Berikut adalah daftar teknologi utama yang digunakan dalam pengembangan proyek ini:

-   [Laravel](https://laravel.com/) - Framework PHP
-   [Livewire](https://livewire.laravel.com/) - Framework full-stack untuk antarmuka dinamis
-   [Jetstream](https://jetstream.laravel.com/) - Scaffolding Autentikasi
-   [Tailwind CSS](https://tailwindcss.com/) - Framework CSS
-   [Alpine.js](https://alpinejs.dev/) - Framework JavaScript minimalis
-   [MySQL](https://www.mysql.com/) - Database

## ✨ Fitur Utama

Aplikasi ini memiliki beragam fitur yang terbagi berdasarkan peran pengguna:

-   **👨‍💼 Admin Pusat:**

    -   Dashboard monitoring pusat
    -   Manajemen Produk (CRUD)
    -   Manajemen Stok Pusat
    -   Manajemen Pengguna (Semua Role)
    -   Manajemen Pembelian ke Supplier
    -   Laporan Keuangan Keseluruhan
    -   Peramalan Permintaan (Demand Forecasting)

-   **🧑‍✈️ Manajer Pusat:**

    -   Dashboard monitoring pusat
    -   Melihat Stok Pusat & Cabang
    -   Melihat Hasil Peramalan

-   **🏢 Admin Cabang:**

    -   Dashboard monitoring cabang
    -   Manajemen Stok Cabang (Penerimaan & Pengeluaran)
    -   Manajemen Pengguna di Cabangnya (Kasir)

-   **📈 Manajer Cabang:**

    -   Dashboard monitoring cabang
    -   Melihat Stok Cabang
    -   Melihat Laporan Keuangan Cabang
    -   Melihat Hasil Peramalan untuk Cabangnya

-   **🛒 Kasir:**
    -   Antarmuka Point of Sale (POS) untuk transaksi penjualan

## 🚀 Panduan Instalasi

Untuk menjalankan proyek ini di lingkungan lokal, ikuti langkah-langkah berikut.

### Prasyarat

Pastikan perangkat Anda telah terinstal:

-   PHP (versi ^8.2)
-   Composer
-   Node.js & NPM
-   Database (misalnya MySQL, MariaDB)

### Langkah-langkah Instalasi

1.  **Clone Repositori**

    ```sh
    git clone [https://github.com/wahyu2021/toko-olahraga-pos.git](https://github.com/wahyu2021/toko-olahraga-pos.git)
    cd toko-olahraga-pos
    ```

2.  **Install Dependensi PHP**

    ```sh
    composer install
    ```

3.  **Install Dependensi JavaScript**

    ```sh
    npm install
    ```

4.  **Konfigurasi Lingkungan**
    Buat file `.env` dengan menyalin dari `.env.example`.

    ```sh
    cp .env.example .env
    ```

    Kemudian, generate kunci aplikasi.

    ```sh
    php artisan key:generate
    ```

5.  **Konfigurasi Database**
    Buka file `.env` dan sesuaikan konfigurasi database Anda.

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=nama_database_anda
    DB_USERNAME=user_database_anda
    DB_PASSWORD=password_database_anda
    ```

6.  **Jalankan Migrasi & Seeder**
    Migrasi akan membuat struktur tabel, dan seeder akan mengisi data awal (termasuk data user, produk, cabang, dll).

    ```sh
    php artisan migrate --seed
    ```

7.  **Build Aset Frontend**

    ```sh
    npm run dev
    ```

8.  **Jalankan Server Pengembangan**
    ```sh
    php artisan serve
    ```
    Aplikasi sekarang dapat diakses di `http://127.0.0.1:8000`.

## 👤 Akun Pengguna

Setelah menjalankan `seeder`, Anda dapat login menggunakan akun default berikut. Password untuk semua akun adalah `password`.

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

---

Dibuat dengan ❤️ oleh Wahyu
