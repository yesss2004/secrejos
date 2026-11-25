<?php

namespace App\Support;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, string $description, ?Model $subject = null, ?string $icon = null): Activity
    {
        return Activity::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
            'icon' => $icon ?? match ($action) {
                'approved' => 'check-circle',
                'rejected' => 'x-circle',
                'deleted' => 'trash',
                'updated' => 'pencil',
                'login' => 'login',
                default => 'plus-circle',
            },
        ]);
    }
}
