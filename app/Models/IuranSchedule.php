<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IuranSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'rt_id',
        'jenis',
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
     * Tagihan iuran warga yang diterbitkan dari jadwal ini.
     *
     * @return HasMany<Contribution, $this>
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class, 'iuran_schedule_id');
    }

    /**
     * Label periode, contoh: "September 2026".
     */
    public function getPeriodLabelAttribute(): string
    {
        return Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y');
    }

    /**
     * Label lengkap: jenis + periode, contoh: "Iuran Sampah September 2026".
     */
    public function getFullLabelAttribute(): string
    {
        return 'Iuran '.$this->jenis.' '.$this->period_label;
    }

    /**
     * Daftar jenis iuran yang tersedia.
     *
     * @return list<string>
     */
    public static function jenisOptions(): array
    {
        return ['Sampah', 'Keamanan', 'Kebersihan', 'Pembangunan', 'Acara', 'Lainnya'];
    }
}
