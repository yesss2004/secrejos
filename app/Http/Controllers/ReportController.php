<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportAttachment;
use App\Models\SubActivityPlan;
use App\Models\TargetSchedule;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Report::class);

        $filters = $request->only(['search', 'type', 'year']);

        $reports = Report::with(['user', 'attachments', 'plan'])
            ->filter($filters)
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $stats = [
            'monthly' => Report::where('type', 'bulanan')->count(),
            'progress' => (int) round((float) TargetSchedule::avg('progress')),
            'attachments' => ReportAttachment::count(),
            'ready_to_print' => Report::count(),
        ];

        $planRecap = SubActivityPlan::query()
            ->leftJoin('plan_budget_lines', 'plan_budget_lines.sub_activity_plan_id', '=', 'sub_activity_plans.id')
            ->when($filters['year'] ?? null, fn ($query, string $year) => $query->where('sub_activity_plans.year', $year))
            ->when($filters['search'] ?? null, function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('sub_activity_plans.sub_activity_name', 'like', "%{$search}%")
                        ->orWhere('sub_activity_plans.sub_activity_code', 'like', "%{$search}%")
                        ->orWhere('sub_activity_plans.program_name', 'like', "%{$search}%")
                        ->orWhere('sub_activity_plans.activity_name', 'like', "%{$search}%");
                });
            })
            ->selectRaw('COUNT(DISTINCT sub_activity_plans.id) as document_count')
            ->selectRaw('COALESCE(SUM(plan_budget_lines.coefficient_before * plan_budget_lines.unit_price_before + plan_budget_lines.tax_before), 0) as recap_before_total')
            ->selectRaw('COALESCE(SUM(plan_budget_lines.coefficient_after * plan_budget_lines.unit_price_after + plan_budget_lines.tax_after), 0) as recap_after_total')
            ->first();

        $years = Report::query()
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('reports.index', compact('reports', 'stats', 'years', 'filters', 'planRecap'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Report::class);

        $suggestedType = in_array($request->type, ['bulanan', 'tahunan'], true) ? $request->type : 'bulanan';
        $currentYear = (int) now()->year;
        $years = range($currentYear + 1, $currentYear - 4);

        return view('reports.create', compact('suggestedType', 'years'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Report::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'type' => ['required', 'in:bulanan,tahunan'],
            'year' => ['required', 'integer', 'digits:4', 'min:2000', 'max:'.(now()->year + 1)],
            'month' => ['nullable', 'required_if:type,bulanan', 'integer', 'between:1,12'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'anyflip_url' => ['nullable', 'url', 'regex:/^https?:\/\/(?:www\.)?(?:[a-z0-9-]+\.)*anyflip\.com\//i', 'max:2048'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
        ], $this->messages(), $this->attributes());

        $report = DB::transaction(function () use ($validated, $request) {
            $report = Report::create([
                'title' => $validated['title'],
                'type' => $validated['type'],
                'year' => (int) $validated['year'],
                'month' => $validated['type'] === 'bulanan' ? (int) $validated['month'] : null,
                'description' => $validated['description'],
                'anyflip_url' => $this->normalizeAnyflipUrl($validated['anyflip_url'] ?? null),
                'status' => 'pending',
                'user_id' => $request->user()->id,
            ]);

            $this->storeAttachments($report, $request);

            return $report;
        });

        ActivityLogger::log('created', 'Menambahkan laporan "'.$report->title.'"', $report);

        return redirect()
            ->route('reports.show', $report)
            ->with('success', 'Laporan berhasil disubmit dan menunggu review administrator.');
    }

    public function show(Report $report): View
    {
        $this->authorize('view', $report);

        $report->load(['user', 'reviewer', 'attachments', 'plan']);

        return view('reports.show', compact('report'));
    }

    public function edit(Report $report): View
    {
        $this->authorize('update', $report);

        $report->load('attachments');
        $years = range((int) now()->year + 1, (int) now()->year - 4);

        return view('reports.edit', compact('report', 'years'));
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        $this->authorize('update', $report);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'type' => ['required', 'in:bulanan,tahunan'],
            'year' => ['required', 'integer', 'digits:4', 'min:2000', 'max:'.(now()->year + 1)],
            'month' => ['nullable', 'required_if:type,bulanan', 'integer', 'between:1,12'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'anyflip_url' => ['nullable', 'url', 'regex:/^https?:\/\/(?:www\.)?(?:[a-z0-9-]+\.)*anyflip\.com\//i', 'max:2048'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
        ], $this->messages(), $this->attributes());

        DB::transaction(function () use ($report, $validated, $request) {
            $report->update([
                'title' => $validated['title'],
                'type' => $validated['type'],
                'year' => (int) $validated['year'],
                'month' => $validated['type'] === 'bulanan' ? (int) $validated['month'] : null,
                'description' => $validated['description'],
                'anyflip_url' => $this->normalizeAnyflipUrl($validated['anyflip_url'] ?? null),
            ]);

            $this->storeAttachments($report, $request);
        });

        ActivityLogger::log('updated', 'Memperbarui laporan "'.$report->title.'"', $report);

        return redirect()
            ->route('reports.show', $report)
            ->with('success', 'Perubahan laporan berhasil disimpan.');
    }

    public function destroy(Report $report): RedirectResponse
    {
        $this->authorize('delete', $report);

        $title = $report->title;

        DB::transaction(function () use ($report) {
            foreach ($report->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $report->delete();
        });

        ActivityLogger::log('deleted', 'Menghapus laporan "'.$title.'"', null);

        return redirect()
            ->route('reports.index')
            ->with('success', 'Laporan "'.$title.'" berhasil dihapus.');
    }

    public function review(Request $request, Report $report): RedirectResponse
    {
        $this->authorize('review', $report);

        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'review_note' => ['nullable', 'required_if:decision,rejected', 'string', 'max:1000'],
        ], $this->messages(), [
            'decision' => 'keputusan',
            'review_note' => 'catatan review',
        ]);

        $report->update([
            'status' => $validated['decision'],
            'review_note' => $validated['review_note'] ?? null,
            'reviewer_id' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        ActivityLogger::log(
            $validated['decision'],
            ($validated['decision'] === 'approved' ? 'Menyetujui' : 'Menolak').' laporan "'.$report->title.'"',
            $report
        );

        return redirect()
            ->route('reports.show', $report)
            ->with('success', $validated['decision'] === 'approved'
                ? 'Laporan berhasil disetujui.'
                : 'Laporan ditolak dan catatan review telah dikirim.');
    }

    public function download(ReportAttachment $attachment): StreamedResponse
    {
        $attachment->loadMissing('report');
        $this->authorize('view', $attachment->report);

        abort_unless(Storage::disk('public')->exists($attachment->file_path), 404, 'File tidak ditemukan.');

        $downloadName = str_replace(['/', '\\'], '-', $attachment->file_name ?: basename($attachment->file_path));

        return Storage::disk('public')->download($attachment->file_path, $downloadName);
    }

    public function destroyAttachment(ReportAttachment $attachment): RedirectResponse
    {
        $attachment->loadMissing('report');
        $this->authorize('update', $attachment->report);

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        ActivityLogger::log('updated', 'Menghapus lampiran "'.$attachment->file_name.'"', $attachment->report);

        return redirect()
            ->route('reports.edit', $attachment->report)
            ->with('success', 'Lampiran berhasil dihapus.');
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Report::class);

        $filters = $request->only(['search', 'type', 'status', 'year']);
        $reports = Report::with('user')->filter($filters)->latest()->get();

        ActivityLogger::log('exported', 'Mengekspor daftar laporan ke CSV', null, 'download');

        return response()->streamDownload(function () use ($reports) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['No', 'Judul Laporan', 'Jenis', 'Periode', 'Status', 'Penyusun', 'Tanggal Dibuat']);

            foreach ($reports as $index => $report) {
                fputcsv($handle, [
                    $index + 1,
                    $report->title,
                    $report->type_label,
                    $report->period_label,
                    $report->status_label,
                    $report->user?->name,
                    $report->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($handle);
        }, 'laporan-sekretariat-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function print(Report $report): View
    {
        $this->authorize('view', $report);

        $report->load(['user', 'reviewer', 'attachments', 'plan']);

        return view('reports.print', compact('report'));
    }

    private function normalizeAnyflipUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        return $url === '' ? null : $url;
    }

    private function storeAttachments(Report $report, Request $request): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            $path = $file->store('reports/'.$report->id, 'public');

            $report->attachments()->create([
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }
    }

    private function messages(): array
    {
        return [
            'attachments.max' => 'Maksimal 5 file lampiran dalam satu kali unggah.',
            'attachments.*.max' => 'Ukuran setiap lampiran maksimal 10 MB.',
            'attachments.*.mimes' => 'Lampiran harus berupa PDF, DOC/DOCX, XLS/XLSX, JPG, atau PNG.',
            'description.min' => 'Deskripsi laporan minimal 20 karakter agar informasi cukup jelas.',
            'month.required_if' => 'Bulan wajib dipilih untuk laporan bulanan.',
            'anyflip_url.url' => 'Tautan AnyFlip harus berupa URL yang valid.',
            'anyflip_url.regex' => 'Tautan AnyFlip harus berasal dari domain anyflip.com.',
        ];
    }

    private function attributes(): array
    {
        return [
            'title' => 'judul laporan',
            'type' => 'jenis laporan',
            'year' => 'tahun',
            'month' => 'bulan',
            'description' => 'deskripsi',
            'anyflip_url' => 'tautan AnyFlip',
            'attachments' => 'lampiran',
        ];
    }
}
