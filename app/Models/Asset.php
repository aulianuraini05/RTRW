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
     * Siapa yang boleh mengonfirmasi peminjaman aset ini.
     *
     * - Aset milik RT: hanya Ketua RT dari RT tersebut (tergantung yang buat).
     * - Aset Umum RW: ditangani admin/RW, bukan Ketua RT.
     * - Superadmin selalu boleh (pemilik sistem).
     */
    public function userCanConfirmLoan(?User $user): bool
    {
        if (! $user || ! $user->isAdmin()) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($this->rt_id !== null) {
            return $user->isRt()
                && ! empty($user->rt_id)
                && (int) $user->rt_id === (int) $this->rt_id;
        }

        return ! $user->isRt();
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
