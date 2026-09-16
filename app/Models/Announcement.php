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
        'image',
        'publication_date',
        'status',
        'category',
        'priority',
        'target_rt_ids',
        'created_by',
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
            'target_rt_ids' => 'array',
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

    /**
     * Cek apakah pengumuman ini ditujukan untuk RT tertentu atau semua RT.
     */
    public function isForAllRt(): bool
    {
        return empty($this->target_rt_ids);
    }

    /**
     * Cek apakah pengumuman ini ditujukan untuk RT tertentu.
     */
    public function targetsRt(int $rtId): bool
    {
        if ($this->isForAllRt()) {
            return true;
        }

        return in_array($rtId, $this->target_rt_ids) || in_array((string) $rtId, $this->target_rt_ids);
    }
}
