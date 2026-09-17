<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'iuran_schedule_id',
        'rt_id',
        'payer_name',
        'amount',
        'payment_method',
        'payment_code',
        'paid_at',
        'payment_status',
        'proof_of_payment',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Jadwal iuran bulanan yang menerbitkan tagihan ini (jika ada).
     *
     * @return BelongsTo<IuranSchedule, $this>
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(IuranSchedule::class, 'iuran_schedule_id');
    }

    /**
     * RT pemilik catatan iuran ini.
     *
     * @return BelongsTo<Rt, $this>
     */
    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }

    /**
     * Nama pembayar untuk ditampilkan.
     * Prioritas: payer_name (input manual) -> nama akun warga -> fallback.
     */
    public function getDisplayNameAttribute(): string
    {
        if (! empty($this->payer_name)) {
            return $this->payer_name;
        }

        if ($this->relationLoaded('user') || $this->user) {
            return $this->user?->name ?? 'Warga';
        }

        return 'Warga';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }
}
