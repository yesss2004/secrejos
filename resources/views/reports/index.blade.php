@extends('layouts.app')

@section('title', 'Laporan & Rekapitulasi')

@section('content')
    @php
        $activeType = $filters['type'] ?? null;
    @endphp

    <x-page-header
        eyebrow="05. Laporan &amp; Rekapitulasi"
        title="Laporan &amp; Rekapitulasi"
        subtitle="Rekap bulanan, realisasi, administrasi, dan laporan siap cetak."
    >
        <a href="{{ route('reports.export', request()->query()) }}" class="btn-secondary">
            <x-icon name="download" class="h-4 w-4" /> Ekspor CSV
        </a>
        @can('create', \App\Models\Report::class)
            <a href="{{ route('reports.create') }}" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4" /> Buat Laporan
            </a>
        @endcan
    </x-page-header>

    <div class="stagger grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-sky h-10 w-10 rounded-lg"><x-icon name="calendar" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rekap Bulanan</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['monthly'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-emerald h-10 w-10 rounded-lg"><x-icon name="trend-up" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Realisasi / Progres</p>
                <p class="text-xl font-extrabold leading-tight text-emerald-700">{{ $stats['progress'] }}%</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-amber h-10 w-10 rounded-lg"><x-icon name="paperclip" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Administrasi / Lampiran</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['attachments'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-violet h-10 w-10 rounded-lg"><x-icon name="printer" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Laporan Siap Cetak</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['ready_to_print'] }}</p>
            </div>
        </div>
    </div>

    <section class="card mt-5 p-5 sm:p-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-[14px] font-extrabold text-ink-900">Rekap Anggaran Subkegiatan</h2>
                <p class="mt-1 text-[11px] text-slate-500">Ringkasan dari dokumen perencanaan tersimpan; mengikuti filter tahun dan pencarian di bawah.</p>
            </div>
            <a href="{{ route('plans.index') }}" class="btn-secondary btn-sm self-start">Lihat Perencanaan</a>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-3 xl:grid-cols-4">
            <div class="rounded-xl bg-slate-50 p-3.5">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Dokumen</p>
                <p class="mt-1 text-lg font-extrabold text-ink-900">{{ $planRecap->document_count }}</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-3.5">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Anggaran Sebelum</p>
                <p class="mt-1 text-sm font-extrabold tabular-nums text-slate-700">Rp{{ number_format($planRecap->recap_before_total, 2, ',', '.') }}</p>
            </div>
            <div class="rounded-xl bg-emerald-50 p-3.5">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Anggaran Sesudah</p>
                <p class="mt-1 text-sm font-extrabold tabular-nums text-emerald-800">Rp{{ number_format($planRecap->recap_after_total, 2, ',', '.') }}</p>
            </div>
            <div class="rounded-xl bg-slate-50 p-3.5">
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Selisih</p>
                <p class="mt-1 text-sm font-extrabold tabular-nums text-slate-700">Rp{{ number_format($planRecap->recap_after_total - $planRecap->recap_before_total, 2, ',', '.') }}</p>
            </div>
        </div>
    </section>

    <div class="card mt-5 overflow-hidden">
        <form method="GET" action="{{ route('reports.index') }}" class="filter-bar">
            <div class="relative min-w-0 flex-1 sm:max-w-xs">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari judul atau isi laporan..."
                       class="input !py-2.5 !pl-10 !text-[13px]">
            </div>

            <select name="type" class="select !w-auto !min-w-[160px] !py-2.5 !text-[13px]">
                <option value="">Semua jenis laporan</option>
                <option value="bulanan" @selected($activeType === 'bulanan')>Laporan Bulanan</option>
                <option value="tahunan" @selected($activeType === 'tahunan')>Laporan Tahunan</option>
            </select>

            <select name="year" class="select !w-auto !min-w-[120px] !py-2.5 !text-[13px]">
                <option value="">Semua tahun</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" @selected((string) ($filters['year'] ?? '') === (string) $year)>{{ $year }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn-primary btn-sm !px-4">
                <x-icon name="filter" class="h-3.5 w-3.5" /> Terapkan
            </button>

            @if(array_filter($filters))
                <a href="{{ route('reports.index') }}" class="btn-ghost btn-sm text-slate-500">Reset</a>
            @endif

            <p class="ml-auto hidden text-[11px] text-slate-400 lg:block">Menampilkan {{ $reports->firstItem() ?? 0 }}&ndash;{{ $reports->lastItem() ?? 0 }} dari {{ $reports->total() }} laporan</p>
        </form>

        @if($reports->count())
            <div class="overflow-x-auto">
                <table class="data-table min-w-[900px]">
                    <thead>
                        <tr>
                            <th class="w-[42%]">Laporan</th>
                            <th>Periode</th>
                            <th>Penyusun</th>
                            <th>Administrasi</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td>
                                    <div class="flex items-start gap-3">
                                        <span class="icon-tile h-10 w-10 rounded-xl {{ $report->type === 'tahunan' ? 'tone-violet' : 'tone-emerald' }}">
                                            <x-icon :name="$report->type === 'tahunan' ? 'chart' : 'document'" class="h-[18px] w-[18px]" />
                                        </span>
                                        <div class="min-w-0">
                                            <a href="{{ route('reports.show', $report) }}" class="line-clamp-1 text-[13px] font-bold text-slate-800 transition hover:text-emerald-700">
                                                {{ $report->title }}
                                            </a>
                                            <p class="mt-1 line-clamp-1 text-[11px] text-slate-400">{{ $report->excerpt }}</p>
                                            <p class="mt-1 text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ $report->type_label }}</p>
                                            @if($report->plan)
                                                <a href="{{ route('plans.show', $report->plan) }}" class="mt-1 inline-flex text-[10px] font-bold text-emerald-700 hover:text-emerald-800">Perencanaan terkait: {{ $report->plan->sub_activity_code }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-50 px-2.5 py-1.5 text-[11px] font-semibold text-slate-600 ring-1 ring-inset ring-slate-200/70">
                                        <x-icon name="calendar" class="h-3.5 w-3.5 text-slate-400" />
                                        {{ $report->short_period }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <x-avatar :user="$report->user" size="xs" />
                                        <div class="min-w-0">
                                            <p class="max-w-[120px] truncate text-[12px] font-semibold text-slate-700">{{ $report->user?->name }}</p>
                                            <p class="text-[10px] text-slate-400">{{ $report->created_at->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </td>
                                    <td>
                                    @if($report->attachments->count())
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500">
                                            <x-icon name="paperclip" class="h-3.5 w-3.5 text-slate-400" /> {{ $report->attachments->count() }} file
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-300">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        @if($report->anyflip_url)
                                            <a href="{{ $report->anyflip_url }}" target="_blank" rel="noopener noreferrer" class="rounded-lg p-2 text-violet-500 transition hover:bg-violet-50 hover:text-violet-700" title="Buka buku digital AnyFlip">
                                                <x-icon name="book" class="h-4 w-4" />
                                            </a>
                                        @endif
                                        <a href="{{ route('reports.print', $report) }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-sky-50 hover:text-sky-700" title="Cetak laporan">
                                            <x-icon name="printer" class="h-4 w-4" />
                                        </a>
                                        <a href="{{ route('reports.show', $report) }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-emerald-600" title="Lihat detail">
                                            <x-icon name="eye" class="h-4 w-4" />
                                        </a>
                                        @can('update', $report)
                                            <a href="{{ route('reports.edit', $report) }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-amber-600" title="Edit laporan">
                                                <x-icon name="pencil" class="h-4 w-4" />
                                            </a>
                                        @endcan
                                        @can('delete', $report)
                                            <form method="POST" action="{{ route('reports.destroy', $report) }}" data-confirm="Hapus laporan &quot;{{ $report->title }}&quot;? Tindakan ini tidak dapat dibatalkan.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus laporan">
                                                    <x-icon name="trash" class="h-4 w-4" />
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $reports->links() }}
            </div>
        @else
            <x-empty-state icon="document" title="Laporan tidak ditemukan" description="Belum ada laporan yang sesuai dengan filter Anda. Coba ubah filter atau buat laporan baru.">
                @can('create', \App\Models\Report::class)
                    <a href="{{ route('reports.create') }}" class="btn-primary btn-sm"><x-icon name="plus" class="h-3.5 w-3.5" /> Buat Laporan</a>
                @endcan
            </x-empty-state>
        @endif
    </div>
@endsection
