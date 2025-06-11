<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['branch_id']);
            // Hapus kolom branch_id
            $table->dropColumn('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            // Untuk mengembalikan kolom branch_id jika migrasi di-rollback
            // Anda perlu tahu tipe data asli dan apakah bisa null
            // Berdasarkan migrasi awal Anda, itu adalah foreignId
            $table->foreignId('branch_id')->nullable()->comment('Cabang penerima barang')->constrained()->onDelete('cascade');
            // Catatan: Jika ada data yang dihapus saat dropColumn, data tersebut tidak akan kembali.
            // Ini hanya mengembalikan struktur kolom.
        });
    }
};
