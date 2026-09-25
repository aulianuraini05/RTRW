<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;

class ActivityLog
{
    /**
     * Catat satu aktivitas. Dipanggil di tiap aksi penting
     * (pengajuan, ubah status, bayar, hapus, dll) di semua modul.
     */
    public static function record(?User $actor, string $module, string $action, ?string $subject = null, ?string $description = null): void
    {
        Activity::create([
            'user_id' => $actor?->id,
            'rt_id' => $actor?->rt_id,
            'module' => $module,
            'action' => $action,
            'subject' => $subject ? mb_substr($subject, 0, 255) : null,
            'description' => $description,
        ]);
    }
}
