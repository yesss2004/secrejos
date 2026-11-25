<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Report $report): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->canManageData();
    }

    public function update(User $user, Report $report): bool
    {
        return ! $report->plan && $report->canBeEditedBy($user);
    }

    public function delete(User $user, Report $report): bool
    {
        return ! $report->plan && $report->canBeEditedBy($user);
    }

    public function review(User $user, Report $report): bool
    {
        return $user->canReview();
    }
}
