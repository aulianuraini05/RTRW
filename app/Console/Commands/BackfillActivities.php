<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\Announcement;
use App\Models\Aspiration;
use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\CashTransaction;
use App\Models\Contribution;
use App\Models\Letter;
use App\Models\Marketplace;
use Illuminate\Console\Command;

class BackfillActivities extends Command
{
    protected $signature = 'activities:backfill';

    protected $description = 'Isi riwayat aktivitas dari data lama (sekali jalan sebelum/sesudah deploy). Aman dijalankan ulang — baris yang sudah ada dilewati.';

    public function handle(): int
    {
        $count = 0;
        $count += $this->backfillAspiration();
        $count += $this->backfillLetter();
        $count += $this->backfillAsset();
        $count += $this->backfillCash();
        $count += $this->backfillContribution();
        $count += $this->backfillAnnouncement();
        $count += $this->backfillMarketplace();

        $this->info("Selesai. {$count} riwayat lama ditambahkan.");

        return self::SUCCESS;
    }

    private function add(?int $userId, ?int $rtId, string $module, string $action, ?string $subject, ?string $description, $at): bool
    {
        $exists = Activity::where('module', $module)
            ->where('action', $action)
            ->where('subject', $subject)
            ->when($userId, fn ($q) => $q->where('user_id', $userId), fn ($q) => $q->whereNull('user_id'))
            ->exists();

        if ($exists) {
            return false;
        }

        $activity = new Activity([
            'user_id' => $userId,
            'rt_id' => $rtId,
            'module' => $module,
            'action' => $action,
            'subject' => $subject ? mb_substr($subject, 0, 255) : null,
            'description' => $description,
        ]);
        $activity->created_at = $at;
        $activity->updated_at = $at;
        $activity->save();

        return true;
    }

    private function backfillAspiration(): int
    {
        $n = 0;
        foreach (Aspiration::with('user')->orderBy('id')->get() as $a) {
            $n += (int) $this->add(
                $a->user_id, $a->rt_id ?? $a->user?->rt_id,
                'aspirasi', 'mengajukan aspirasi', $a->aspiration_title,
                'Status: '.ucfirst($a->aspiration_status).' (data lama).',
                $a->created_at
            );
        }

        return $n;
    }

    private function backfillLetter(): int
    {
        $n = 0;
        foreach (Letter::with('user')->orderBy('id')->get() as $l) {
            $n += (int) $this->add(
                $l->user_id, $l->user?->rt_id,
                'surat', 'mengajukan surat', $l->letter_type.' ('.$l->letter_number.')',
                'Status: '.ucfirst($l->letter_status).' (data lama).',
                $l->created_at
            );
        }

        return $n;
    }

    private function backfillAsset(): int
    {
        $n = 0;
        foreach (Asset::orderBy('id')->get() as $asset) {
            $n += (int) $this->add(
                null, $asset->rt_id,
                'aset', 'menambah aset', $asset->asset_name.' ('.$asset->quantity.' unit)',
                'Data lama.',
                $asset->created_at
            );
        }
        foreach (AssetLoan::with(['user', 'asset'])->orderBy('id')->get() as $loan) {
            $n += (int) $this->add(
                $loan->user_id, $loan->user?->rt_id,
                'aset', 'mengajukan peminjaman', ($loan->asset?->asset_name ?? 'Aset').' ('.$loan->quantity.' unit)',
                'Status: '.ucfirst($loan->loan_status).' (data lama).',
                $loan->created_at
            );
        }

        return $n;
    }

    private function backfillCash(): int
    {
        $n = 0;
        foreach (CashTransaction::with('user')->orderBy('id')->get() as $t) {
            $n += (int) $this->add(
                $t->user_id, $t->rt_id ?? $t->user?->rt_id,
                'kas', 'pembayaran kas ('.ucfirst($t->payment_status).')',
                $t->payment_code.' (Rp '.number_format((float) $t->amount, 0, ',', '.').')',
                'Dibayar oleh '.($t->payer_name ?? $t->user?->name ?? 'Warga').' (data lama).',
                $t->created_at
            );
        }

        return $n;
    }

    private function backfillContribution(): int
    {
        $n = 0;
        foreach (Contribution::with('user')->orderBy('id')->get() as $c) {
            $n += (int) $this->add(
                $c->user_id, $c->rt_id ?? $c->user?->rt_id,
                'iuran', 'pembayaran iuran ('.ucfirst($c->payment_status).')',
                $c->payment_code.' (Rp '.number_format((float) $c->amount, 0, ',', '.').')',
                'Dibayar oleh '.($c->payer_name ?? $c->user?->name ?? 'Warga').' (data lama).',
                $c->created_at
            );
        }

        return $n;
    }

    private function backfillAnnouncement(): int
    {
        $n = 0;
        foreach (Announcement::orderBy('id')->get() as $a) {
            $user = $a->created_by ? \App\Models\User::find($a->created_by) : null;
            $n += (int) $this->add(
                $a->created_by, $user?->rt_id,
                'pengumuman', 'membuat pengumuman', $a->announcement_title,
                'Data lama.',
                $a->created_at
            );
        }

        return $n;
    }

    private function backfillMarketplace(): int
    {
        $n = 0;
        foreach (Marketplace::with('user')->orderBy('id')->get() as $m) {
            $n += (int) $this->add(
                $m->user_id, $m->user?->rt_id,
                'marketplace', 'mendaftarkan produk', $m->product_name,
                'Data lama.',
                $m->created_at
            );
        }

        return $n;
    }
}
