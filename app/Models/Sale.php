<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'branch_id',
        'user_id', // Cashier
        'customer_name',
        'subtotal_amount',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'amount_received',
        'change_amount',
        'payment_method',
        'status',
        'notes',
        'sale_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'subtotal_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'sale_date' => 'datetime',
    ];

    /**
     * Get the branch where the sale occurred.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user (cashier) who processed the sale.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items included in this sale.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id'); // Eksplisit foreign key jika berbeda dari konvensi
    }

    /**
     * Get all of the sale's stock movements.
     */
    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'referenceable');
    }
}