<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marketplace extends Model
{
    protected $fillable = [
        'user_id',
        'product_name',
        'description',
        'price',
        'stock',
        'product_status',
        'seller_phone',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<MarketplacePurchase, $this>
     */
    public function purchases(): HasMany
    {
        return $this->hasMany(MarketplacePurchase::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('product_status', 'tersedia');
    }
}
