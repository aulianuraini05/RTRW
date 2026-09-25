<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Letter extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'letter_number',
        'letter_type',
        'submission_date',
        'letter_date',
        'purpose',
        'letter_status',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Lampiran syarat per jenis surat (jumlah & jenis beda-beda).
     *
     * @return HasMany<LetterAttachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(LetterAttachment::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'submission_date' => 'date',
            'letter_date' => 'date',
        ];
    }
}
