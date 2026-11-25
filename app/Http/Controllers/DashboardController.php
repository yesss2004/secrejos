<?php

namespace App\Http\Controllers;

use App\Models\ReportAttachment;
use App\Models\Report;
use App\Models\TargetSchedule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();
        $userTargets = TargetSchedule::query();
        $totalTargets = (clone $userTargets)->count();
        $averageProgress = (int) round((float) (clone $userTargets)->avg('progress'));
        $upcomingTargets = (clone $userTargets)
            ->where('status', '!=', 'selesai')
            ->whereBetween('due_date', [today(), today()->addDays(7)])
            ->count();
        $reportCount = Report::count();
        $attachmentCount = ReportAttachment::count();
        $targets = (clone $userTargets)
            ->orderByRaw("CASE WHEN status = 'selesai' THEN 1 ELSE 0 END")
            ->orderBy('due_date')
            ->take(12)
            ->get();
        $period = now()->locale('id')->translatedFormat('F Y');

        return view('dashboard', compact(
            'user',
            'totalTargets',
            'averageProgress',
            'upcomingTargets',
            'reportCount',
            'attachmentCount',
            'targets',
            'period',
        ));
    }
}
