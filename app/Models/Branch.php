<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
    ];

    /**
     * Get the users associated with the branch.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the stock records for the branch.
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Get the sales made at this branch.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the purchases received at this branch.
     */
    // public function purchases(): HasMany
    // {
    //     return $this->hasMany(Purchase::class);
    // }

    /**
     * Get the stock movements at this branch.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get the demand forecasts for this branch.
     */
    public function demandForecasts(): HasMany
    {
        return $this->hasMany(DemandForecast::class);
    }
}