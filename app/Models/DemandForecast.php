<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'branch_id',
        'forecast_period_start_date',
        'forecast_period_end_date',
        'forecasted_quantity',
        'forecasting_method_used',
        'parameters_used',
        'actual_sales_for_period',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'forecast_period_start_date' => 'date',
        'forecast_period_end_date' => 'date',
        'forecasted_quantity' => 'integer',
        'actual_sales_for_period' => 'integer',
        'parameters_used' => 'json', // Untuk menyimpan parameter forecasting
    ];

    /**
     * Get the product for which this forecast is made.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the branch for which this forecast is made.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created this forecast.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}