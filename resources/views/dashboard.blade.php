@extends('layouts.app')

@section('title', 'Dashboard Monitoring')

@section('content')
    <div class="space-y-5">
        <section class="overflow-hidden rounded-[18px] bg-[#142d4f] text-white shadow-[0_14px_32px_-20px_rgba(15,23,42,0.65)]">
            <div class="px-3.5 pb-4 pt-3 sm:px-6 sm:pb-6 sm:pt-5">
                <div class="flex items-center justify-between border-b border-white/15 pb-4">
                    <div class="flex min-w-0 items-center gap-2.5">
                        <button type="button" data-sidebar-toggle class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white transition hover:bg-white/15 lg:hidden" aria-label="Buka menu">
                            <x-icon name="building" class="h-5 w-5" />
                        </button>
                        <span class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white lg:flex">
                            <x-icon name="building" class="h-5 w-5" />
                        </span>
                        <div class="min-w-0">
                            <p class="truncate text-[15px] font-extrabold leading-tight tracking-[-0.02em] sm:text-lg">SEKRETARIAT</p>
                            <p class="truncate text-[9px] font-medium uppercase tracking-wide text-slate-300 sm:text-[11px]">Monitoring &amp; Control Center</p>
                        </div>
                    </div>
                    <span class="ml-3 shrink-0 rounded-full bg-emerald-500/15 px-2.5 py-1 text-[10px] font-bold text-emerald-300 ring-1 ring-inset ring-emerald-400/20">
                        TA {{ now()->year }}
                    </span>
                </div>

                <div class="pt-4 sm:pt-5">
                    <h1 class="text-[25px] font-extrabold leading-tight tracking-[-0.04em] sm:text-3xl">Dashboard Monitoring</h1>
                    <p class="mt-1.5 text-[13px] text-slate-300 sm:text-sm">Pengendalian pelaksanaan {{ $totalTargets }} target pekerjaan</p>

                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                        <span class="flex items-center gap-2 text-[11px] font-medium text-slate-200 sm:text-xs">
                            <x-icon name="calendar" class="h-4 w-4 text-slate-300" />
                            Periode laporan
                        </span>
                        <span class="inline-flex items-center gap-3 rounded-full border border-white/25 px-3 py-1.5 text-[12px] font-semibold text-white sm:px-3.5">
                            {{ $period }}
                            <x-icon name="chevron-down" class="h-3.5 w-3.5 text-slate-300" />
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <section>
            <div class="mb-3 flex items-center justify-between gap-3">
                <h2 class="text-[16px] font-extrabold tracking-tight text-slate-900 sm:text-lg">Ringkasan eksekutif</h2>
                <span class="rounded-full bg-sky-100 px-2.5 py-1 text-[9px] font-extrabold uppercase tracking-wide text-sky-800 sm:text-[10px]">Data tercatat</span>
            </div>

            <div class="grid grid-cols-2 gap-2 sm:gap-3 xl:grid-cols-4">
                <article class="min-h-[122px] rounded-2xl bg-[#f1f1f1] p-3 sm:min-h-[145px] sm:p-4">
                    <div class="flex items-start justify-between gap-1">
                        <p class="text-[12px] leading-snug text-slate-600 sm:text-sm">Total target</p>
                        <x-icon name="layers" class="mt-0.5 h-4 w-4 shrink-0 text-sky-600" />
                    </div>
                    <p class="mt-2 text-[27px] font-medium leading-none tracking-tight text-slate-950 sm:text-3xl">{{ $totalTargets }}</p>
                    <p class="mt-2 text-[11px] text-slate-600 sm:text-xs">Target yang dicatat</p>
                </article>

                <article class="min-h-[122px] rounded-2xl bg-[#f1f1f1] p-3 sm:min-h-[145px] sm:p-4">
                    <div class="flex items-start justify-between gap-1">
                        <p class="text-[12px] leading-snug text-slate-600 sm:text-sm">Tenggat 7 hari</p>
                        <x-icon name="calendar" class="mt-0.5 h-4 w-4 shrink-0 text-blue-600" />
                    </div>
                    <p class="mt-2 text-[27px] font-semibold leading-none tracking-tight text-slate-950 sm:text-3xl">{{ $upcomingTargets }}</p>
                    <p class="mt-2 text-[11px] text-slate-600 sm:text-xs">Target segera jatuh tempo</p>
                </article>

                <article class="min-h-[122px] rounded-2xl bg-[#f1f1f1] p-3 sm:min-h-[145px] sm:p-4">
                    <div class="flex items-start justify-between gap-1">
                        <p class="text-[12px] leading-snug text-slate-600 sm:text-sm">Realisasi / progres</p>
                        <x-icon name="trend-up" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" />
                    </div>
                    <p class="mt-2 text-[27px] font-semibold leading-none tracking-tight text-emerald-800 sm:text-3xl">{{ $averageProgress }}%</p>
                    <p class="mt-2 text-[11px] text-slate-600 sm:text-xs">Rata-rata progres target</p>
                </article>

                <article class="min-h-[122px] rounded-2xl bg-[#f1f1f1] p-3 sm:min-h-[145px] sm:p-4">
                    <div class="flex items-start justify-between gap-1">
                        <p class="text-[12px] leading-snug text-slate-600 sm:text-sm">Dokumen laporan</p>
                        <x-icon name="folder" class="mt-0.5 h-4 w-4 shrink-0 text-amber-600" />
                    </div>
                    <p class="mt-2 text-[27px] font-semibold leading-none tracking-tight text-slate-950 sm:text-3xl">{{ $reportCount }}</p>
                    <p class="mt-2 text-[11px] text-slate-600 sm:text-xs">{{ $attachmentCount }} lampiran administrasi</p>
                </article>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 sm:text-lg">Detail target dan progres</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500 sm:text-xs">Realisasi dan progres dari target yang Anda input.</p>
                </div>
                @if(auth()->user()->canManageData())
                    <a href="{{ route('targets.index') }}" class="btn-secondary btn-sm">Lihat Target &amp; Jadwal</a>
                @endif
            </div>

            @if($targets->isNotEmpty())
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($targets as $target)
                        @php
                            $priorityTone = match($target->priority) {
                                'tinggi' => 'rose',
                                'sedang' => 'amber',
                                default => 'slate',
                            };
                        @endphp
                        <article class="rounded-xl border border-slate-200 bg-slate-50/60 p-3.5">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="text-[12px] font-bold leading-snug text-slate-800 sm:text-[13px]">{{ $target->title }}</h3>
                                <span class="badge badge-{{ $priorityTone }}">Prioritas {{ ucfirst($target->priority) }}</span>
                            </div>
                            <p class="mt-2 line-clamp-2 text-[11px] leading-relaxed text-slate-500">{{ $target->description ?: 'Detail target belum diisi.' }}</p>
                            <p class="mt-2 line-clamp-2 text-[11px] leading-relaxed text-slate-700">
                                <span class="font-bold">Realisasi:</span> {{ $target->realization ?: 'Belum dicatat.' }}
                            </p>
                            <div class="mt-3 flex items-center justify-between gap-2 text-[10px] text-slate-500">
                                <span>Progres</span>
                                <span class="font-bold text-slate-700">{{ $target->progress }}%</span>
                            </div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-slate-200">
                                <div class="h-full rounded-full bg-emerald-500" style="width: {{ $target->progress }}%"></div>
                            </div>
                            <p class="mt-2 text-[10px] text-slate-500">Tenggat {{ $target->due_date->locale('id')->translatedFormat('d M Y') }}</p>
                        </article>
                    @endforeach
                </div>
            @else
                <x-empty-state icon="layers" title="Belum ada target" description="Tambahkan target dan isi realisasi serta progresnya untuk melihat ringkasan monitoring.">
                    @if(auth()->user()->canManageData())
                        <a href="{{ route('targets.create') }}" class="btn-primary btn-sm"><x-icon name="plus" class="h-3.5 w-3.5" /> Tambah Target</a>
                    @endif
                </x-empty-state>
            @endif
        </section>
    </div>
@endsection
