<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'action', 'user']);

        $activities = Activity::with('user')
            ->when($filters['search'] ?? null, fn ($query, $search) => $query->where('description', 'like', "%{$search}%"))
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($filters['user'] ?? null, fn ($query, $user) => $query->where('user_id', $user))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'today' => Activity::whereDate('created_at', today())->count(),
            'week' => Activity::where('created_at', '>=', now()->subDays(7))->count(),
            'total' => Activity::count(),
        ];

        $users = User::orderBy('name')->get(['id', 'name']);

        return view('activities.index', compact('activities', 'stats', 'filters', 'users'));
    }
}
