<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'rt_id',
        'asset_name',
        'asset_type',
        'quantity',
        'condition',
        'description',
        'image',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Rt, $this>
     */
    public function rt(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }

    /**
     * Cek apakah aset ini milik bersama (Aset Umum RW).
     */
    public function isPublicRw(): bool
    {
        return $this->rt_id === null;
    }

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
