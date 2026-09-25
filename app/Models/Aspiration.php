<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aspiration extends Model
{
    use HasFactory;

    protected $fillable = [
        'aspiration_title',
        'aspiration_content',
        'category',
        'submission_date',
        'aspiration_status',
        'user_id',
        'rt_id',
        'forwarded_to',
        'forwarded_by',
        'forwarded_at',
        'tanggapan',
        'tanggapan_by',
        'tanggapan_at',
        'photo_path',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RT pemilik aspirasi (dicatat saat pengajuan, tetap ada walau akun warga dihapus).
     */
    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }

    /**
     * Pengurus (RT/RW/Admin) yang menulis tanggapan.
     *
     * @return BelongsTo<User, $this>
     */
    public function tanggapanAuthor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tanggapan_by');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'submission_date' => 'date',
            'forwarded_at' => 'datetime',
            'tanggapan_at' => 'datetime',
        ];
    }
}
