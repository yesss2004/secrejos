<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    private const PRIORITIES = [
        'tinggi' => 'Tinggi',
        'sedang' => 'Sedang',
        'rendah' => 'Rendah',
    ];

    private const STATUSES = [
        'terbuka' => 'Belum ditangani',
        'ditangani' => 'Sedang ditangani',
        'selesai' => 'Selesai',
    ];

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'priority', 'status']);
        $userIssues = $request->user()->canReview()
            ? Issue::query()
            : $request->user()->issues();
        $issues = (clone $userIssues)
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('cause', 'like', "%{$search}%")
                        ->orWhere('follow_up', 'like', "%{$search}%");
                });
            })
            ->when($filters['priority'] ?? null, fn ($query, string $priority) => $query->where('priority', $priority))
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->orderByRaw("CASE status WHEN 'terbuka' THEN 0 WHEN 'ditangani' THEN 1 ELSE 2 END")
            ->orderByRaw("CASE priority WHEN 'tinggi' THEN 0 WHEN 'sedang' THEN 1 ELSE 2 END")
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => (clone $userIssues)->count(),
            'open' => (clone $userIssues)->where('status', 'terbuka')->count(),
            'in_progress' => (clone $userIssues)->where('status', 'ditangani')->count(),
            'resolved' => (clone $userIssues)->where('status', 'selesai')->count(),
        ];

        return view('issues.index', compact('issues', 'stats', 'filters'));
    }

    public function create(): View
    {
        return view('issues.create', ['priorities' => self::PRIORITIES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'cause' => ['required', 'string', 'max:2000'],
            'follow_up' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', 'in:tinggi,sedang,rendah'],
        ]);

        $issue = $request->user()->issues()->create($validated);
        ActivityLogger::log('created', 'Mencatat kendala "'.$issue->title.'"', $issue);

        return redirect()->route('issues.index')->with('success', 'Kendala berhasil dicatat.');
    }

    public function edit(Request $request, Issue $issue): View
    {
        $this->ensureOwner($request, $issue);

        return view('issues.edit', ['issue' => $issue, 'priorities' => self::PRIORITIES, 'statuses' => self::STATUSES]);
    }

    public function update(Request $request, Issue $issue): RedirectResponse
    {
        $this->ensureOwner($request, $issue);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'cause' => ['required', 'string', 'max:2000'],
            'follow_up' => ['nullable', 'string', 'max:2000'],
            'priority' => ['required', 'in:tinggi,sedang,rendah'],
            'status' => ['required', 'in:terbuka,ditangani,selesai'],
        ]);

        $issue->update($validated);
        ActivityLogger::log('updated', 'Memperbarui kendala "'.$issue->title.'"', $issue);

        return redirect()->route('issues.index')->with('success', 'Perubahan kendala berhasil disimpan.');
    }

    public function destroy(Request $request, Issue $issue): RedirectResponse
    {
        $this->ensureOwner($request, $issue);

        $title = $issue->title;
        $issue->delete();
        ActivityLogger::log('deleted', 'Menghapus kendala "'.$title.'"');

        return redirect()->route('issues.index')->with('success', 'Kendala berhasil dihapus.');
    }

    public function verify(Request $request, Issue $issue): RedirectResponse
    {
        abort_unless($request->user()->canReview(), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:terbuka,ditangani,selesai'],
        ]);

        $issue->update(['status' => $validated['status']]);
        ActivityLogger::log('updated', 'Memverifikasi kendala "'.$issue->title.'" menjadi '.self::STATUSES[$validated['status']], $issue);

        return back()->with('success', 'Status kendala berhasil diverifikasi.');
    }

    private function ensureOwner(Request $request, Issue $issue): void
    {
        abort_unless($request->user()->isAdmin() || $issue->user_id === $request->user()->id, 404);
    }
}
