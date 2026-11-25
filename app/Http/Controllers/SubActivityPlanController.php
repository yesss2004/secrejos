<?php

namespace App\Http\Controllers;

use App\Models\SubActivityPlan;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SubActivityPlanController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'year']);
        $plans = SubActivityPlan::with(['user', 'budgetLines'])
            ->filter($filters)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $years = SubActivityPlan::query()->select('year')->distinct()->orderByDesc('year')->pluck('year');
        $currentYearCount = SubActivityPlan::where('year', now()->year)->count();

        return view('plans.index', compact('plans', 'years', 'filters', 'currentYearCount'));
    }

    public function create(): View
    {
        return view('plans.create', ['plan' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePlan($request);

        $plan = DB::transaction(function () use ($validated, $request) {
            $plan = SubActivityPlan::create([
                ...$validated['plan'],
                'user_id' => $request->user()->id,
            ]);
            $this->syncDetails($plan, $validated);
            $this->syncReport($plan, $validated);

            return $plan;
        });
        $report = $plan->report()->firstOrFail();

        ActivityLogger::log('created', 'Membuat perencanaan subkegiatan dan laporan "'.$plan->sub_activity_name.'"', $plan);

        return redirect()
            ->route('reports.show', $report)
            ->with('success', 'Perencanaan berhasil disimpan dan masuk ke Laporan & Rekapitulasi.');
    }

    public function show(SubActivityPlan $plan): View
    {
        $plan->load(['user', 'indicators', 'budgetLines']);

        return view('plans.show', compact('plan'));
    }

    public function edit(SubActivityPlan $plan): View
    {
        $this->authorizePlanEdit($plan);
        $plan->load(['indicators', 'budgetLines']);

        return view('plans.edit', compact('plan'));
    }

    public function update(Request $request, SubActivityPlan $plan): RedirectResponse
    {
        $this->authorizePlanEdit($plan);
        $validated = $this->validatePlan($request);

        DB::transaction(function () use ($plan, $validated) {
            $plan->update($validated['plan']);
            $plan->indicators()->delete();
            $plan->budgetLines()->delete();
            $this->syncDetails($plan, $validated);
            $this->syncReport($plan, $validated);
        });
        $report = $plan->report()->firstOrFail();

        ActivityLogger::log('updated', 'Memperbarui perencanaan subkegiatan dan laporan "'.$plan->sub_activity_name.'"', $plan);

        return redirect()
            ->route('reports.show', $report)
            ->with('success', 'Perencanaan dan laporan rekap berhasil diperbarui.');
    }

    public function destroy(SubActivityPlan $plan): RedirectResponse
    {
        $this->authorizePlanEdit($plan);
        $name = $plan->sub_activity_name;
        $plan->delete();

        ActivityLogger::log('deleted', 'Menghapus perencanaan subkegiatan "'.$name.'"', null);

        return redirect()->route('plans.index')->with('success', 'Perencanaan berhasil dihapus.');
    }

    public function print(SubActivityPlan $plan): View
    {
        $plan->load(['user', 'indicators', 'budgetLines']);

        return view('plans.print', compact('plan'));
    }

    private function validatePlan(Request $request): array
    {
        return $request->validate([
            'plan.year' => ['required', 'integer', 'digits:4', 'min:2000', 'max:'.(now()->year + 5)],
            'plan.organization_unit' => ['required', 'string', 'max:160'],
            'plan.sub_organization_unit' => ['nullable', 'string', 'max:160'],
            'plan.program_code' => ['nullable', 'string', 'max:40'],
            'plan.program_name' => ['required', 'string', 'max:200'],
            'plan.activity_code' => ['nullable', 'string', 'max:40'],
            'plan.activity_name' => ['required', 'string', 'max:200'],
            'plan.sub_activity_code' => ['required', 'string', 'max:40'],
            'plan.sub_activity_name' => ['required', 'string', 'max:200'],
            'plan.spm' => ['nullable', 'string', 'max:160'],
            'plan.service_type' => ['nullable', 'string', 'max:160'],
            'plan.funding_source' => ['nullable', 'string', 'max:160'],
            'plan.location' => ['nullable', 'string', 'max:200'],
            'plan.implementation_time' => ['nullable', 'string', 'max:100'],
            'plan.target_group' => ['nullable', 'string', 'max:200'],
            'plan.output' => ['nullable', 'string', 'max:200'],
            'indicators' => ['required', 'array', 'min:1', 'max:30'],
            'indicators.*.category' => ['required', 'string', 'max:80'],
            'indicators.*.indicator_before' => ['required', 'string', 'max:200'],
            'indicators.*.benchmark_before' => ['required', 'string', 'max:1000'],
            'indicators.*.target_before' => ['required', 'string', 'max:160'],
            'indicators.*.indicator_after' => ['required', 'string', 'max:200'],
            'indicators.*.benchmark_after' => ['required', 'string', 'max:1000'],
            'indicators.*.target_after' => ['required', 'string', 'max:160'],
            'budget_lines' => ['required', 'array', 'min:1', 'max:100'],
            'budget_lines.*.account_code' => ['nullable', 'string', 'max:60'],
            'budget_lines.*.description' => ['required', 'string', 'max:2000'],
            'budget_lines.*.coefficient_before' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'budget_lines.*.unit_before' => ['nullable', 'string', 'max:80'],
            'budget_lines.*.unit_price_before' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'budget_lines.*.tax_before' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'budget_lines.*.coefficient_after' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'budget_lines.*.unit_after' => ['nullable', 'string', 'max:80'],
            'budget_lines.*.unit_price_after' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'budget_lines.*.tax_after' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
        ], [], [
            'plan.year' => 'tahun anggaran',
            'plan.organization_unit' => 'unit organisasi',
            'plan.program_name' => 'nama program',
            'plan.activity_name' => 'nama kegiatan',
            'plan.sub_activity_code' => 'kode subkegiatan',
            'plan.sub_activity_name' => 'nama subkegiatan',
            'indicators.*.category' => 'kelompok indikator',
            'indicators.*.benchmark_before' => 'tolok ukur sebelum',
            'indicators.*.benchmark_after' => 'tolok ukur sesudah',
            'budget_lines.*.description' => 'uraian anggaran',
        ]);
    }

    private function syncDetails(SubActivityPlan $plan, array $validated): void
    {
        foreach ($validated['indicators'] as $index => $indicator) {
            $plan->indicators()->create([...$indicator, 'sort_order' => $index]);
        }

        foreach ($validated['budget_lines'] as $index => $line) {
            $plan->budgetLines()->create([...$line, 'sort_order' => $index]);
        }
    }

    private function syncReport(SubActivityPlan $plan, array $validated): void
    {
        $budgetLines = collect($validated['budget_lines']);
        $totalBefore = $budgetLines->sum(fn (array $line) => ($line['coefficient_before'] * $line['unit_price_before']) + $line['tax_before']);
        $totalAfter = $budgetLines->sum(fn (array $line) => ($line['coefficient_after'] * $line['unit_price_after']) + $line['tax_after']);
        $title = Str::limit($plan->sub_activity_code.' - '.$plan->sub_activity_name, 160, '');
        $description = implode("\n", [
            'Laporan ini dibuat otomatis dari dokumen perencanaan subkegiatan.',
            'Kode subkegiatan: '.$plan->sub_activity_code,
            'Nama subkegiatan: '.$plan->sub_activity_name,
            'Unit organisasi: '.$plan->organization_unit,
            'Program: '.$plan->program_name,
            'Kegiatan: '.$plan->activity_name,
            'Sumber pendanaan: '.($plan->funding_source ?: 'Belum diisi'),
            'Total anggaran sebelum: Rp'.number_format($totalBefore, 2, ',', '.'),
            'Total anggaran sesudah: Rp'.number_format($totalAfter, 2, ',', '.'),
            'Selisih anggaran: Rp'.number_format($totalAfter - $totalBefore, 2, ',', '.'),
            'Rincian indikator dan anggaran tersedia pada dokumen perencanaan terkait.',
        ]);

        $report = $plan->report;
        if (! $report) {
            $plan->report()->create([
                'title' => $title,
                'type' => 'tahunan',
                'year' => $plan->year,
                'description' => $description,
                'status' => 'pending',
                'user_id' => $plan->user_id,
            ]);

            return;
        }

        $report->update([
            'title' => $title,
            'type' => 'tahunan',
            'year' => $plan->year,
            'month' => null,
            'description' => $description,
            'status' => 'pending',
            'reviewer_id' => null,
            'review_note' => null,
            'reviewed_at' => null,
        ]);
    }

    private function authorizePlanEdit(SubActivityPlan $plan): void
    {
        abort_unless($plan->canBeEditedBy(request()->user()), 403);
    }
}
