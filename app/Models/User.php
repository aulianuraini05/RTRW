<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Check if the user has admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user has warga role.
     */
    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }

    /**
     * @return HasMany<Aspiration, $this>
     */
    public function aspirations(): HasMany
    {
        return $this->hasMany(Aspiration::class);
    }

    /**
     * @return HasMany<Letter, $this>
     */
    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class);
    }

    /**
     * @return HasMany<AssetLoan, $this>
     */
    public function assetLoans(): HasMany
    {
        return $this->hasMany(AssetLoan::class);
    }

    /**
     * @return HasMany<CashTransaction, $this>
     */
    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    /**
     * @return HasMany<Contribution, $this>
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    /**
     * @return HasMany<Marketplace, $this>
     */
    public function marketplaces(): HasMany
    {
        return $this->hasMany(Marketplace::class);
    }

    /**
     * @return HasMany<MarketplacePurchase, $this>
     */
    public function marketplacePurchases(): HasMany
    {
        return $this->hasMany(MarketplacePurchase::class, 'buyer_id');
    }

    /**
     * @return HasMany<MarketplacePurchase, $this>
     */
    public function marketplaceSales(): HasMany
    {
        return $this->hasMany(MarketplacePurchase::class, 'seller_id');
    }

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
}
