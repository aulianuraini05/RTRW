<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'rt_id', 'no_whatsapp', 'nik', 'no_kk', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'status_perkawinan', 'agama', 'pendidikan_terakhir', 'pekerjaan', 'alamat_rumah', 'no_rumah'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Check if the user has superadmin role.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if the user has RW admin role.
     */
    public function isRw(): bool
    {
        return $this->role === 'rw';
    }

    /**
     * Check if the user has RT admin role.
     */
    public function isRt(): bool
    {
        return $this->role === 'rt';
    }

    /**
     * Check if the user has any admin/management role (admin, superadmin, rw, rt).
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin', 'rw', 'rt']);
    }

    /**
     * Check if the user has warga role.
     */
    public function isWarga(): bool
    {
        return $this->role === 'warga';
    }

    /**
     * RT tempat warga terdaftar.
     *
     * @return BelongsTo<Rt, $this>
     */
    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }

    /**
     * @return HasMany<Aspiration, $this>
     */
    public function aspirations(): HasMany
    {
        return $this->hasMany(Aspiration::class);
    }

    /**
     * @return HasMany<Letter, $this>
     */
    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class);
    }

    /**
     * @return HasMany<AssetLoan, $this>
     */
    public function assetLoans(): HasMany
    {
        return $this->hasMany(AssetLoan::class);
    }

    /**
     * @return HasMany<CashTransaction, $this>
     */
    public function cashTransactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }

    /**
     * @return HasMany<Contribution, $this>
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    /**
     * @return HasMany<Marketplace, $this>
     */
    public function marketplaces(): HasMany
    {
        return $this->hasMany(Marketplace::class);
    }

    /**
     * @return BelongsToMany<Announcement, $this>
     */
    public function readAnnouncements(): BelongsToMany
    {
        return $this->belongsToMany(Announcement::class, 'announcement_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    /**
     * Cek apakah profil demografi sudah lengkap.
     */
    public function isProfileComplete(): bool
    {
        return ! empty($this->nik)
            && ! empty($this->no_kk)
            && ! empty($this->tempat_lahir)
            && ! empty($this->tanggal_lahir)
            && ! empty($this->jenis_kelamin)
            && ! empty($this->alamat_rumah);
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
        ];
    }
}
