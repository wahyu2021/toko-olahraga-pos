<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Pastikan sudah ada
        'branch_id', // Pastikan sudah ada
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Definisi peran untuk referensi.
     * Anda bisa membuat konstanta untuk ini agar lebih mudah dikelola.
     */
    public const ROLE_ADMIN_PUSAT = 'admin_pusat';
    public const ROLE_ADMIN_CABANG = 'admin_cabang';
    public const ROLE_MANAJER_PUSAT = 'manajer_pusat';
    public const ROLE_MANAJER_CABANG = 'manajer_cabang';
    public const ROLE_KASIR = 'kasir';

    /**
     * Get the branch that the user belongs to (for admin_cabang, manajer_cabang, kasir).
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the sales made by the user (cashier).
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get the purchases recorded by the user.
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get the stock movements initiated by the user.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get the demand forecasts created by the user.
     */
    public function demandForecasts(): HasMany
    {
        return $this->hasMany(DemandForecast::class);
    }

    // Helper functions for roles
    public function hasRole(string $roleName): bool
    {
        return $this->role === $roleName;
    }

    public function isAdminPusat(): bool
    {
        return $this->role === self::ROLE_ADMIN_PUSAT;
    }

    public function isAdminCabang(): bool
    {
        return $this->role === self::ROLE_ADMIN_CABANG;
    }

    public function isManajerPusat(): bool
    {
        return $this->role === self::ROLE_MANAJER_PUSAT;
    }

    public function isManajerCabang(): bool
    {
        return $this->role === self::ROLE_MANAJER_CABANG;
    }

    public function isKasir(): bool
    {
        return $this->role === self::ROLE_KASIR;
    }

    /**
     * Checks if the user is associated with any central role.
     */
    public function isPusatUser(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN_PUSAT, self::ROLE_MANAJER_PUSAT]);
    }

    /**
     * Checks if the user is associated with any branch-specific role.
     */
    public function isCabangUser(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN_CABANG, self::ROLE_MANAJER_CABANG, self::ROLE_KASIR]);
    }
}