<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'purchase_price_at_sale',
        'discount_per_item',
        'subtotal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'purchase_price_at_sale' => 'decimal:2',
        'discount_per_item' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Get the sale that this item belongs to.
     */
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * Get the product associated with this sale item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}