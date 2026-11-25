@extends('layouts.app')

@section('title', 'Detail Perencanaan Subkegiatan')

@section('content')
    <x-page-header eyebrow="Perencanaan &amp; Anggaran" title="{{ $plan->sub_activity_name }}" subtitle="{{ $plan->sub_activity_code }} &middot; Tahun Anggaran {{ $plan->year }}">
        <a href="{{ route('plans.print', $plan) }}" class="btn-secondary"><x-icon name="printer" class="h-4 w-4" /> Cetak / Simpan PDF</a>
        @if($plan->canBeEditedBy(auth()->user()))
            <a href="{{ route('plans.edit', $plan) }}" class="btn-primary"><x-icon name="pencil" class="h-4 w-4" /> Edit</a>
        @endif
    </x-page-header>

    <div class="grid gap-5 xl:grid-cols-3">
        <section class="card p-5 sm:p-6 xl:col-span-2">
            <h2 class="mb-4 text-[15px] font-extrabold text-ink-900">Informasi Subkegiatan</h2>
            <dl class="grid gap-4 sm:grid-cols-2">
                @foreach([
                    ['Unit Organisasi', $plan->organization_unit],
                    ['Sub Unit Organisasi', $plan->sub_organization_unit],
                    ['Program', trim(($plan->program_code ? $plan->program_code.' ' : '').$plan->program_name)],
                    ['Kegiatan', trim(($plan->activity_code ? $plan->activity_code.' ' : '').$plan->activity_name)],
                    ['Subkegiatan', $plan->sub_activity_code.' '.$plan->sub_activity_name],
                    ['Sumber Pendanaan', $plan->funding_source],
                    ['Lokasi', $plan->location],
                    ['Waktu Pelaksanaan', $plan->implementation_time],
                    ['Keluaran', $plan->output],
                    ['Kelompok Sasaran', $plan->target_group],
                    ['SPM', $plan->spm],
                    ['Jenis Layanan', $plan->service_type],
                ] as [$label, $value])
                    @if($value)
                        <div><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $label }}</dt><dd class="mt-1 text-[12px] font-semibold text-slate-700">{{ $value }}</dd></div>
                    @endif
                @endforeach
            </dl>
        </section>

        <aside class="card p-5 sm:p-6">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ringkasan Anggaran</p>
            <div class="mt-4 space-y-4">
                <div><p class="text-[11px] text-slate-500">Sebelum</p><p class="mt-1 text-xl font-extrabold tabular-nums text-slate-800">Rp{{ number_format($plan->total_before, 2, ',', '.') }}</p></div>
                <div><p class="text-[11px] text-slate-500">Sesudah</p><p class="mt-1 text-xl font-extrabold tabular-nums text-emerald-700">Rp{{ number_format($plan->total_after, 2, ',', '.') }}</p></div>
                <div class="border-t border-slate-100 pt-3"><p class="text-[11px] text-slate-500">Bertambah / (Berkurang)</p><p class="mt-1 text-lg font-extrabold tabular-nums {{ $plan->total_after - $plan->total_before < 0 ? 'text-rose-600' : 'text-slate-800' }}">Rp{{ number_format($plan->total_after - $plan->total_before, 2, ',', '.') }}</p></div>
            </div>
            <p class="mt-5 border-t border-slate-100 pt-3 text-[10px] text-slate-400">Disusun oleh {{ $plan->user?->name }} pada {{ $plan->created_at->format('d/m/Y') }}</p>
        </aside>
    </div>

    <section class="card mt-5 overflow-hidden">
        <div class="border-b border-slate-100 p-5 sm:px-6"><h2 class="text-[15px] font-extrabold text-ink-900">Indikator dan Tolok Ukur Kinerja</h2></div>
        <div class="overflow-x-auto">
            <table class="data-table min-w-[950px]">
                <thead><tr><th rowspan="2">Kelompok</th><th colspan="3" class="text-center">Sebelum</th><th colspan="3" class="text-center">Sesudah</th></tr><tr><th>Indikator</th><th>Tolok Ukur</th><th>Target</th><th>Indikator</th><th>Tolok Ukur</th><th>Target</th></tr></thead>
                <tbody>
                    @foreach($plan->indicators as $indicator)
                        <tr>
                            <td class="font-semibold">{{ $indicator->category }}</td><td>{{ $indicator->indicator_before }}</td><td>{{ $indicator->benchmark_before }}</td><td>{{ $indicator->target_before }}</td>
                            <td>{{ $indicator->indicator_after }}</td><td>{{ $indicator->benchmark_after }}</td><td>{{ $indicator->target_after }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="card mt-5 overflow-hidden">
        <div class="border-b border-slate-100 p-5 sm:px-6"><h2 class="text-[15px] font-extrabold text-ink-900">Rincian Anggaran Belanja</h2></div>
        <div class="overflow-x-auto">
            <table class="data-table min-w-[1150px]">
                <thead><tr><th rowspan="2">Kode Rekening</th><th rowspan="2">Uraian</th><th colspan="5" class="text-center">Sebelum</th><th colspan="5" class="text-center">Sesudah</th><th rowspan="2" class="text-right">Selisih</th></tr><tr><th>Koefisien</th><th>Satuan</th><th>Harga (Rp)</th><th>PPN (Rp)</th><th>Jumlah (Rp)</th><th>Koefisien</th><th>Satuan</th><th>Harga (Rp)</th><th>PPN (Rp)</th><th>Jumlah (Rp)</th></tr></thead>
                <tbody>
                    @foreach($plan->budgetLines as $line)
                        <tr>
                            <td>{{ $line->account_code ?: '—' }}</td><td>{{ $line->description }}</td>
                            <td>{{ number_format($line->coefficient_before, 2, ',', '.') }}</td><td>{{ $line->unit_before ?: '—' }}</td>
                            <td>{{ number_format($line->unit_price_before, 2, ',', '.') }}</td><td>{{ number_format($line->tax_before, 2, ',', '.') }}</td><td class="font-semibold">{{ number_format($line->before_total, 2, ',', '.') }}</td>
                            <td>{{ number_format($line->coefficient_after, 2, ',', '.') }}</td><td>{{ $line->unit_after ?: '—' }}</td>
                            <td>{{ number_format($line->unit_price_after, 2, ',', '.') }}</td><td>{{ number_format($line->tax_after, 2, ',', '.') }}</td><td class="font-semibold">{{ number_format($line->after_total, 2, ',', '.') }}</td>
                            <td class="text-right font-semibold">{{ number_format($line->difference, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot><tr><th colspan="6" class="text-right">Jumlah Anggaran</th><th>{{ number_format($plan->total_before, 2, ',', '.') }}</th><th colspan="4" class="text-right">Jumlah Anggaran Sesudah</th><th>{{ number_format($plan->total_after, 2, ',', '.') }}</th><th class="text-right">{{ number_format($plan->total_after - $plan->total_before, 2, ',', '.') }}</th></tr></tfoot>
            </table>
        </div>
    </section>
@endsection
