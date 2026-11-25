@extends('layouts.app')

@section('title', 'Modul Monitoring')

@section('content')
    <x-page-header
        eyebrow="Modul Monitoring"
        title="Monitoring Target"
        subtitle="Lihat detail target, realisasi pekerjaan, dan progres penyelesaian dalam satu halaman."
    >
        <a href="{{ route('targets.index') }}" class="btn-secondary">
            <x-icon name="calendar" class="h-4 w-4" /> Kelola Target &amp; Jadwal
        </a>
    </x-page-header>

    <div class="stagger grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-sky h-10 w-10 rounded-lg"><x-icon name="layers" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Target</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-emerald h-10 w-10 rounded-lg"><x-icon name="trend-up" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rata-rata Progres</p>
                <p class="text-xl font-extrabold leading-tight text-emerald-700">{{ $stats['average_progress'] }}%</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-violet h-10 w-10 rounded-lg"><x-icon name="check-circle" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Target Selesai</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['completed'] }}</p>
            </div>
        </div>
    </div>

    <section class="card mt-5 overflow-hidden">
        <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
            <h2 class="text-base font-extrabold text-slate-900">Detail target, realisasi, dan progres</h2>
            <p class="mt-1 text-[11px] text-slate-500">Data ini berasal dari target yang Anda catat sendiri.</p>
        </div>

        @if($targets->isNotEmpty())
            <div class="divide-y divide-slate-100">
                @foreach($targets as $target)
                    @php
                        $priorityTone = match($target->priority) {
                            'tinggi' => 'rose',
                            'sedang' => 'amber',
                            default => 'slate',
                        };
                    @endphp
                    <article class="grid gap-4 p-4 sm:p-5 lg:grid-cols-[1.2fr_1fr_0.8fr]">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-sm font-extrabold text-slate-900">{{ $target->title }}</h3>
                                <span class="badge badge-{{ $priorityTone }}">Prioritas {{ ucfirst($target->priority) }}</span>
                                @if($target->status === 'selesai')
                                    <span class="badge badge-emerald">Selesai</span>
                                @endif
                            </div>
                            <p class="mt-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">Detail target</p>
                            <p class="mt-1 whitespace-pre-line text-[12px] leading-relaxed text-slate-600">{{ $target->description ?: 'Belum ada detail target.' }}</p>
                            <p class="mt-2 text-[10px] font-semibold text-slate-500">Tenggat {{ $target->due_date->locale('id')->translatedFormat('d F Y') }}</p>
                        </div>

                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Realisasi</p>
                            <p class="mt-1 whitespace-pre-line text-[12px] leading-relaxed text-slate-700">{{ $target->realization ?: 'Belum ada realisasi dicatat.' }}</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Progres</p>
                                <span class="text-sm font-extrabold text-slate-800">{{ $target->progress }}%</span>
                            </div>
                            <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width: {{ $target->progress }}%"></div>
                            </div>
                            <a href="{{ route('targets.edit', $target) }}" class="mt-3 inline-flex items-center gap-1 text-[11px] font-bold text-sky-700 hover:text-sky-900">
                                Perbarui data <x-icon name="arrow-right" class="h-3 w-3" />
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <x-empty-state icon="chart" title="Belum ada data monitoring" description="Tambahkan target dan isi detail, realisasi, serta progres untuk mulai memantau pekerjaan.">
                <a href="{{ route('targets.create') }}" class="btn-primary btn-sm"><x-icon name="plus" class="h-3.5 w-3.5" /> Tambah Target</a>
            </x-empty-state>
        @endif
    </section>
@endsection
