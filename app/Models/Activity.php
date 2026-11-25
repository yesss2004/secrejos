<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'icon',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getToneAttribute(): string
    {
        return match ($this->action) {
            'approved' => 'emerald',
            'rejected' => 'rose',
            'deleted' => 'rose',
            'updated' => 'amber',
            'created' => 'sky',
            default => 'slate',
        };
    }
}
