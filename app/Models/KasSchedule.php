<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KasSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rt_id',
        'month',
        'year',
        'amount',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<Rt, $this>
     */
    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Tagihan kas warga yang diterbitkan dari jadwal ini.
     *
     * @return HasMany<CashTransaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class, 'kas_schedule_id');
    }

    /**
     * Label periode, contoh: "September 2026".
     */
    public function getPeriodLabelAttribute(): string
    {
        return Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y');
    }
}
