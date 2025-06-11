<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'supplier_id',
        // 'branch_id', // Branch receiving goods
        'user_id',   // User recording purchase
        'total_amount',
        'purchase_date',
        'expected_delivery_date',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'purchase_date' => 'date',
        'expected_delivery_date' => 'date',
    ];

    /**
     * Get the supplier for this purchase.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the branch that received the goods for this purchase.
     */
    // public function branch(): BelongsTo
    // {
    //     return $this->belongsTo(Branch::class);
    // }

    /**
     * Get the user who recorded this purchase.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items included in this purchase.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id'); // Eksplisit foreign key
    }

    /**
     * Get all of the purchase's stock movements.
     */
    public function stockMovements(): MorphMany
    {
        return $this->morphMany(StockMovement::class, 'referenceable');
    }
}