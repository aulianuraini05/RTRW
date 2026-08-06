<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_name',
        'asset_type',
        'quantity',
        'condition',
        'description',
    ];

    /**
     * @return HasMany<AssetLoan, $this>
     */
    public function loans(): HasMany
    {
        return $this->hasMany(AssetLoan::class);
    }

    public function availableQuantity(): int
    {
        $borrowed = $this->loans()
            ->whereIn('loan_status', ['disetujui', 'dipinjam'])
            ->sum('quantity');

        return max(0, $this->quantity - $borrowed);
    }
}
