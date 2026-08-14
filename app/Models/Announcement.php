<?php

namespace App\Models;

use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'announcement_title',
        'announcement_content',
        'publication_date',
        'status',
        'category',
        'priority',
        'is_pinned',
        'read_count',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'publication_date' => 'date',
            'is_pinned' => 'boolean',
            'read_count' => 'integer',
        ];
    }

    /**
     * Warga yang sudah membaca pengumuman ini.
     *
     * @return BelongsToMany<User, $this>
     */
    public function readBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'announcement_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }
}
