<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function index(Request $request): View
    {
        $userTargets = $request->user()->targetSchedules();
        $targets = (clone $userTargets)
            ->orderBy('due_date')
            ->orderByDesc('priority')
            ->get();

        $stats = [
            'total' => $targets->count(),
            'average_progress' => (int) round((float) $targets->avg('progress')),
            'completed' => $targets->where('status', 'selesai')->count(),
        ];

        return view('monitoring.index', compact('targets', 'stats'));
    }
}
