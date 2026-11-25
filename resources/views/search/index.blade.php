@extends('layouts.app')

@section('title', 'Hasil Pencarian')

@section('content')
    @php
        $total = $reports->count() + $letters->count();
        $hasQuery = strlen($query) >= 2;
    @endphp

    <x-page-header
        eyebrow="Pusat Pencarian"
        title="Hasil Pencarian"
        subtitle="Telusuri laporan, surat, nomor arsip, dan keterangan terkait dalam satu pintu."
    >
        <a href="{{ route('dashboard') }}" class="btn-secondary">
            <x-icon name="arrow-left" class="h-4 w-4" /> Kembali ke Dashboard
        </a>
    </x-page-header>

    <div class="card mb-5 p-4 sm:p-5">
        <form method="GET" action="{{ route('search') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative min-w-0 flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ $query }}" autofocus
                       placeholder="Cari kata kunci laporan atau surat..."
                       class="input !py-3 !pl-10 !text-[13px]">
            </div>
            <button type="submit" class="btn-primary sm:w-auto">
                <x-icon name="search" class="h-4 w-4" /> Cari Sekarang
            </button>
            @if($hasQuery)
                <a href="{{ route('search') }}" class="btn-ghost text-slate-500">
                    <x-icon name="refresh" class="h-4 w-4" /> Bersihkan
                </a>
            @endif
        </form>

        @if($hasQuery)
            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4 text-[11px]">
                <span class="font-semibold text-slate-500">Kata kunci:</span>
                <span class="badge badge-emerald">{{ $query }}</span>
                <span class="text-slate-400">&middot;</span>
                <span class="text-slate-500"><strong class="text-slate-700">{{ $total }}</strong> hasil ditemukan</span>
                <span class="rounded-full bg-slate-100 px-2 py-0.5 font-semibold text-slate-500">{{ $reports->count() }} laporan</span>
                <span class="rounded-full bg-slate-100 px-2 py-0.5 font-semibold text-slate-500">{{ $letters->count() }} surat</span>
            </div>
        @endif
    </div>

    @if(! $hasQuery)
        <x-empty-state icon="search" title="Mulai pencarian Anda" description="Masukkan minimal 2 karakter untuk menelusuri laporan sekretariat dan arsip persuratan. Pencarian mencakup judul, isi, nomor surat, dan pengirim.">
            <div class="flex flex-wrap items-center justify-center gap-2">
                <a href="{{ route('reports.index') }}" class="btn-secondary btn-sm"><x-icon name="document" class="h-3.5 w-3.5" /> Lihat Laporan</a>
                <a href="{{ route('letters.index') }}" class="btn-secondary btn-sm"><x-icon name="mail" class="h-3.5 w-3.5" /> Lihat Persuratan</a>
            </div>
        </x-empty-state>
    @elseif($total === 0)
        <x-empty-state icon="search" title="Tidak ada hasil untuk &ldquo;{{ $query }}&rdquo;" description="Coba gunakan kata kunci yang lebih singkat, periksa ejaan, atau telusuri langsung melalui daftar laporan dan persuratan.">
            <div class="flex flex-wrap items-center justify-center gap-2">
                <a href="{{ route('reports.index', ['search' => $query]) }}" class="btn-secondary btn-sm"><x-icon name="document" class="h-3.5 w-3.5" /> Cari di Laporan</a>
                <a href="{{ route('letters.index', ['search' => $query]) }}" class="btn-secondary btn-sm"><x-icon name="mail" class="h-3.5 w-3.5" /> Cari di Surat</a>
            </div>
        </x-empty-state>
    @else
        <div class="grid gap-5 xl:grid-cols-2">
            <section class="card overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div class="flex items-center gap-2.5">
                        <span class="icon-tile tone-emerald h-9 w-9 rounded-lg"><x-icon name="document" class="h-4 w-4" /></span>
                        <div>
                            <h2 class="text-sm font-extrabold text-ink-900">Laporan</h2>
                            <p class="text-[11px] text-slate-400">{{ $reports->count() }} hasil relevan</p>
                        </div>
                    </div>
                    <a href="{{ route('reports.index', ['search' => $query]) }}" class="text-[11px] font-bold text-emerald-700 transition hover:text-emerald-800">Lihat semua</a>
                </div>

                @forelse($reports as $report)
                    <a href="{{ route('reports.show', $report) }}" class="group flex gap-3 border-b border-slate-50 px-5 py-4 transition last:border-0 hover:bg-slate-50/70">
                        <span class="icon-tile h-10 w-10 shrink-0 rounded-xl {{ $report->type === 'tahunan' ? 'tone-violet' : 'tone-emerald' }}">
                            <x-icon :name="$report->type === 'tahunan' ? 'chart' : 'document'" class="h-[18px] w-[18px]" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <p class="line-clamp-1 text-[13px] font-bold text-slate-800 transition group-hover:text-emerald-700">{{ $report->title }}</p>
                                <x-status-badge :tone="$report->status_tone" :label="$report->status_label" class="shrink-0" />
                            </div>
                            <p class="mt-1 line-clamp-2 text-[11px] leading-relaxed text-slate-500">{{ $report->excerpt }}</p>
                            <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-[10px] text-slate-400">
                                <span class="font-semibold text-slate-500">{{ $report->type_label }}</span>
                                <span class="h-0.5 w-0.5 rounded-full bg-slate-300"></span>
                                <span>{{ $report->short_period }}</span>
                                <span class="h-0.5 w-0.5 rounded-full bg-slate-300"></span>
                                <span>{{ $report->user?->name }}</span>
                            </p>
                        </div>
                        <x-icon name="chevron-right" class="mt-2 h-4 w-4 shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-emerald-500" />
                    </a>
                @empty
                    <div class="px-5 py-10 text-center">
                        <x-icon name="document" class="mx-auto mb-2 h-6 w-6 text-slate-300" />
                        <p class="text-xs font-semibold text-slate-500">Tidak ada laporan yang cocok</p>
                    </div>
                @endforelse
            </section>

            <section class="card overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div class="flex items-center gap-2.5">
                        <span class="icon-tile tone-sky h-9 w-9 rounded-lg"><x-icon name="mail" class="h-4 w-4" /></span>
                        <div>
                            <h2 class="text-sm font-extrabold text-ink-900">Persuratan</h2>
                            <p class="text-[11px] text-slate-400">{{ $letters->count() }} hasil relevan</p>
                        </div>
                    </div>
                    <a href="{{ route('letters.index', ['search' => $query]) }}" class="text-[11px] font-bold text-emerald-700 transition hover:text-emerald-800">Lihat semua</a>
                </div>

                @forelse($letters as $letter)
                    <a href="{{ route('letters.show', $letter) }}" class="group flex gap-3 border-b border-slate-50 px-5 py-4 transition last:border-0 hover:bg-slate-50/70">
                        <span class="icon-tile h-10 w-10 shrink-0 rounded-xl {{ $letter->type === 'masuk' ? 'tone-sky' : 'tone-violet' }}">
                            <x-icon :name="$letter->type === 'masuk' ? 'inbox' : 'send'" class="h-[18px] w-[18px]" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <p class="line-clamp-1 text-[13px] font-bold text-slate-800 transition group-hover:text-emerald-700">{{ $letter->title }}</p>
                                <x-status-badge :tone="$letter->status_tone" :label="$letter->status_label" class="shrink-0" />
                            </div>
                            <p class="mt-1 line-clamp-1 font-mono text-[11px] font-semibold text-slate-500">{{ $letter->reference_number }}</p>
                            <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-[10px] text-slate-400">
                                <span class="font-semibold text-slate-500">{{ $letter->type_label }}</span>
                                <span class="h-0.5 w-0.5 rounded-full bg-slate-300"></span>
                                <span class="truncate">{{ $letter->correspondent }}</span>
                                <span class="h-0.5 w-0.5 rounded-full bg-slate-300"></span>
                                <span>{{ $letter->letter_date->format('d M Y') }}</span>
                            </p>
                        </div>
                        <x-icon name="chevron-right" class="mt-2 h-4 w-4 shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-emerald-500" />
                    </a>
                @empty
                    <div class="px-5 py-10 text-center">
                        <x-icon name="mail" class="mx-auto mb-2 h-6 w-6 text-slate-300" />
                        <p class="text-xs font-semibold text-slate-500">Tidak ada surat yang cocok</p>
                    </div>
                @endforelse
            </section>
        </div>
    @endif
@endsection
