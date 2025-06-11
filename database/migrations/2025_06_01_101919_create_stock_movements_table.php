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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->comment('User yang melakukan aksi')->constrained()->onDelete('set null');
            $table->enum('type', ['initial', 'purchase', 'sale', 'transfer_in', 'transfer_out', 'adjustment_increase', 'adjustment_decrease', 'return']);
            $table->integer('quantity_change')->comment('Perubahan kuantitas, bisa positif atau negatif');
            $table->integer('quantity_before')->comment('Kuantitas sebelum perubahan');
            $table->integer('quantity_after')->comment('Kuantitas setelah perubahan');
            $table->nullableMorphs('referenceable'); // Untuk reference ke Sale, Purchase, Transfer, etc. (referenceable_id, referenceable_type)
            $table->text('notes')->nullable();
            $table->timestamp('movement_date')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};