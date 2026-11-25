<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'position',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function reviewedReports(): HasMany
    {
        return $this->hasMany(Report::class, 'reviewer_id');
    }

    public function letters(): HasMany
    {
        return $this->hasMany(Letter::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function targetSchedules(): HasMany
    {
        return $this->hasMany(TargetSchedule::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function canManageData(): bool
    {
        return in_array($this->role, ['admin', 'operator', 'staff'], true);
    }

    public function canReview(): bool
    {
        return in_array($this->role, ['admin', 'verifikator'], true);
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles, true)
            || ($this->role === 'staff' && in_array('operator', $roles, true));
    }

    public function getInitialsAttribute(): string
    {
        return Str::of($this->name)
            ->squish()
            ->explode(' ')
            ->take(2)
            ->map(fn (string $word) => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Admin',
            'operator', 'staff' => 'Operator Subbagian',
            'verifikator' => 'Verifikator / PPK',
            'pimpinan' => 'Pimpinan / Sekretaris',
            default => 'Pengguna',
        };
    }

    public function getAvatarColorAttribute(): string
    {
        $palette = ['navy', 'emerald', 'amber', 'rose', 'violet', 'sky'];
        $index = crc32($this->email ?? $this->name ?? 'user') % count($palette);

        return $palette[$index];
    }
}
