<?php

namespace App\Http\Controllers;

use App\Models\TargetSchedule;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TargetScheduleController extends Controller
{
    private const PRIORITIES = [
        'tinggi' => 'Tinggi',
        'sedang' => 'Sedang',
        'rendah' => 'Rendah',
    ];

    private const STATUSES = [
        'belum_selesai' => 'Belum selesai',
        'selesai' => 'Selesai',
    ];

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'priority', 'status']);
        $userTargets = $request->user()->isAdmin()
            ? TargetSchedule::query()
            : $request->user()->targetSchedules();
        $targets = (clone $userTargets)
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($filters['priority'] ?? null, fn ($query, string $priority) => $query->where('priority', $priority))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->orderByRaw("CASE WHEN status = 'selesai' THEN 1 ELSE 0 END")
            ->orderBy('due_date')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => (clone $userTargets)->count(),
            'overdue' => (clone $userTargets)->where('status', 'belum_selesai')->whereDate('due_date', '<', today())->count(),
            'upcoming' => (clone $userTargets)->where('status', 'belum_selesai')->whereBetween('due_date', [today(), today()->addDays(7)])->count(),
            'completed' => (clone $userTargets)->where('status', 'selesai')->count(),
        ];

        return view('targets.index', compact('targets', 'stats', 'filters'));
    }

    public function create(): View
    {
        return view('targets.create', ['priorities' => self::PRIORITIES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'realization' => ['nullable', 'string', 'max:2000'],
            'due_date' => ['required', 'date'],
            'priority' => ['required', 'in:tinggi,sedang,rendah'],
            'progress' => ['required', 'integer', 'between:0,100'],
        ]);

        $target = $request->user()->targetSchedules()->create($validated);
        ActivityLogger::log('created', 'Menambahkan target "'.$target->title.'"', $target);

        return redirect()->route('targets.index')->with('success', 'Target dan tenggat berhasil ditambahkan.');
    }

    public function edit(Request $request, TargetSchedule $target): View
    {
        $this->ensureOwner($request, $target);

        return view('targets.edit', ['target' => $target, 'priorities' => self::PRIORITIES]);
    }

    public function update(Request $request, TargetSchedule $target): RedirectResponse
    {
        $this->ensureOwner($request, $target);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'realization' => ['nullable', 'string', 'max:2000'],
            'due_date' => ['required', 'date'],
            'priority' => ['required', 'in:tinggi,sedang,rendah'],
            'progress' => ['required', 'integer', 'between:0,100'],
        ]);

        $target->update($validated);
        ActivityLogger::log('updated', 'Memperbarui target "'.$target->title.'"', $target);

        return redirect()->route('targets.index')->with('success', 'Perubahan target berhasil disimpan.');
    }

    public function updateStatus(Request $request, TargetSchedule $target): RedirectResponse
    {
        $this->ensureOwner($request, $target);

        $validated = $request->validate([
            'status' => ['required', 'in:belum_selesai,selesai'],
        ]);

        $target->update(['status' => $validated['status']]);
        ActivityLogger::log('updated', 'Mengubah status target "'.$target->title.'" menjadi '.self::STATUSES[$validated['status']], $target);

        return back()->with('success', 'Status target berhasil diperbarui.');
    }

    public function destroy(Request $request, TargetSchedule $target): RedirectResponse
    {
        $this->ensureOwner($request, $target);

        $title = $target->title;
        $target->delete();
        ActivityLogger::log('deleted', 'Menghapus target "'.$title.'"');

        return redirect()->route('targets.index')->with('success', 'Target berhasil dihapus.');
    }

    private function ensureOwner(Request $request, TargetSchedule $target): void
    {
        abort_unless($request->user()->isAdmin() || $target->user_id === $request->user()->id, 404);
    }
}
