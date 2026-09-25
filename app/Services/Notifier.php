<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AppNotification;

class Notifier
{
    /**
     * Kirim notifikasi lonceng ke satu akun. Aktor tidak perlu
     * diberi notifikasi atas aksinya sendiri.
     */
    public static function send(?User $recipient, string $title, string $message, string $url = '/dashboard', ?User $actor = null): void
    {
        if (! $recipient) {
            return;
        }

        if ($actor && (int) $actor->id === (int) $recipient->id) {
            return;
        }

        $recipient->notify(new AppNotification($title, $message, $url));
    }

    /**
     * Kirim ke banyak akun sekaligus.
     *
     * @param iterable<User> $recipients
     */
    public static function sendMany(iterable $recipients, string $title, string $message, string $url = '/dashboard', ?User $actor = null): void
    {
        foreach ($recipients as $recipient) {
            self::send($recipient, $title, $message, $url, $actor);
        }
    }

    /**
     * Pengurus yang berhak atas satu RT: Ketua RT-nya + RW/Admin.
     * Dipakai mis. saat warga mengajukan sesuatu.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, User>
     */
    public static function managersForRt(?int $rtId)
    {
        return User::query()
            ->where(function ($q) use ($rtId) {
                $q->whereIn('role', ['admin', 'superadmin', 'rw'])
                    ->orWhere(fn ($qq) => $qq->where('role', 'rt')->where('rt_id', $rtId));
            })
            ->get();
    }
}
