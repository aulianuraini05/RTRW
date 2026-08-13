<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Marketplace extends Model
{
    protected $fillable = [
        'user_id',
        'product_name',
        'description',
        'price',
        'product_status',
        'seller_phone',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('product_status', 'tersedia');
    }

    public function whatsappLink(string $message): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->seller_phone ?? '');

        if (! $phone) {
            return null;
        }

        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    }

    public function whatsappMessage(): string
    {
        $seller = $this->user?->name ?? 'Penjual';

        return "Halo {$seller}, saya ingin memesan *{$this->product_name}* seharga Rp ".number_format((float) $this->price, 0, ',', '.').'. Apakah masih tersedia?';
    }
}
