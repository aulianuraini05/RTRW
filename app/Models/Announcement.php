<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
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
}
