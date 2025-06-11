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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->nullable()->unique(); // Dari supplier
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->comment('Cabang penerima barang')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->comment('User yang mencatat')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->date('purchase_date');
            $table->date('expected_delivery_date')->nullable();
            $table->enum('status', ['pending', 'ordered', 'partially_received', 'received', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};