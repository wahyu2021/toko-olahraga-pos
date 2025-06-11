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
        Schema::create('demand_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->date('forecast_period_start_date');
            $table->date('forecast_period_end_date');
            $table->integer('forecasted_quantity');
            $table->string('forecasting_method_used')->nullable(); // e.g., 'SMA', 'WMA', 'ES'
            $table->json('parameters_used')->nullable(); // Simpan parameter yg digunakan untuk forecasting
            $table->integer('actual_sales_for_period')->nullable()->comment('Untuk evaluasi akurasi');
            $table->foreignId('user_id')->comment('User yang membuat forecast')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['product_id', 'branch_id', 'forecast_period_start_date'], 'product_branch_period_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demand_forecasts');
    }
};