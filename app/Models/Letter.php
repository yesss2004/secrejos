<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Letter extends Model
{
    protected $fillable = [
        'reference_number',
        'title',
        'type',
        'classification',
        'correspondent',
        'letter_date',
        'received_date',
        'description',
        'status',
        'file_path',
        'file_name',
        'file_size',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'letter_date' => 'date',
            'received_date' => 'date',
            'file_size' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('reference_number', 'like', "%{$search}%")
                        ->orWhere('correspondent', 'like', "%{$search}%");
                });
            })
            ->when($filters['type'] ?? null, fn (Builder $query, string $type) => $query->where('type', $type))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['classification'] ?? null, fn (Builder $query, string $classification) => $query->where('classification', $classification));
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'keluar' ? 'Surat Keluar' : 'Surat Masuk';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'arsip' => 'Diarsipkan',
            default => 'Baru',
        };
    }

    public function getStatusToneAttribute(): string
    {
        return match ($this->status) {
            'diproses' => 'amber',
            'selesai' => 'emerald',
            'arsip' => 'slate',
            default => 'sky',
        };
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->description ?? ''), 110);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        $power = min($power, count($units) - 1);

        return number_format($bytes / (1024 ** $power), $power > 1 ? 1 : 0).' '.$units[$power];
    }
}
