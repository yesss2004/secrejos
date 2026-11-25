@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
    @php
        $actionLabels = [
            'created' => 'Dibuat',
            'updated' => 'Diperbarui',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'deleted' => 'Dihapus',
            'login' => 'Masuk',
            'logout' => 'Keluar',
            'exported' => 'Ekspor',
            'downloaded' => 'Unduhan',
        ];
        $grouped = $activities->getCollection()->groupBy(fn ($activity) => $activity->created_at->locale('id')->translatedFormat('l, d F Y'));
    @endphp

    <x-page-header
        eyebrow="Modul Target &amp; Jadwal"
        title="Target &amp; Jadwal"
        subtitle="Pantau target, progres, dan tenggat waktu pekerjaan serta pengingat jadwal penting seperti Pajak Kendaraan Dinas."
    />

    <div class="stagger grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-emerald h-10 w-10 rounded-lg"><x-icon name="activity" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Hari Ini</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['today'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-sky h-10 w-10 rounded-lg"><x-icon name="calendar" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">7 Hari Terakhir</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['week'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-violet h-10 w-10 rounded-lg"><x-icon name="database" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Tercatat</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['total'] }}</p>
            </div>
        </div>
    </div>

    <div class="card mt-5 overflow-hidden">
        <form method="GET" action="{{ route('activities.index') }}" class="filter-bar">
            <div class="relative min-w-0 flex-1 sm:max-w-xs">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari aktivitas..."
                       class="input !py-2.5 !pl-10 !text-[13px]">
            </div>

            <select name="action" class="select !w-auto !min-w-[140px] !py-2.5 !text-[13px]">
                <option value="">Semua aksi</option>
                @foreach($actionLabels as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['action'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="user" class="select !w-auto !min-w-[150px] !py-2.5 !text-[13px]">
                <option value="">Semua pengguna</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected((string) ($filters['user'] ?? '') === (string) $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn-primary btn-sm !px-4"><x-icon name="filter" class="h-3.5 w-3.5" /> Terapkan</button>

            @if(array_filter($filters))
                <a href="{{ route('activities.index') }}" class="btn-ghost btn-sm text-slate-500">Reset</a>
            @endif

            <p class="ml-auto hidden text-[11px] text-slate-400 lg:block">{{ $activities->total() }} aktivitas</p>
        </form>

        @if($activities->count())
            <div class="p-5 sm:p-6">
                @foreach($grouped as $date => $items)
                    <div class="mb-6 last:mb-0">
                        <div class="mb-3 flex items-center gap-3">
                            <span class="rounded-lg bg-slate-100 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ $date }}</span>
                            <span class="h-px flex-1 bg-slate-100"></span>
                            <span class="text-[10px] font-semibold text-slate-400">{{ $items->count() }} aktivitas</span>
                        </div>

                        <div class="space-y-2">
                            @foreach($items as $activity)
                                <div class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/40 p-3.5 transition hover:border-slate-200 hover:bg-white">
                                    @if($activity->user)
                                        <x-avatar :user="$activity->user" size="sm" />
                                    @else
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-400"><x-icon name="shield" class="h-3.5 w-3.5" /></span>
                                    @endif

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                            <p class="text-[12px] font-bold text-slate-800">{{ $activity->user?->name ?? 'Sistem' }}</p>
                                            <span class="badge badge-{{ $activity->tone }}">{{ $actionLabels[$activity->action] ?? ucfirst($activity->action) }}</span>
                                        </div>
                                        <p class="mt-1 text-[12px] leading-relaxed text-slate-600">{{ $activity->description }}</p>
                                    </div>

                                    <div class="shrink-0 text-right">
                                        <p class="text-[10px] font-semibold text-slate-500">{{ $activity->created_at->format('H:i') }}</p>
                                        <p class="mt-0.5 text-[9px] text-slate-400">{{ $activity->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $activities->links() }}
            </div>
        @else
            <x-empty-state icon="activity" title="Belum ada aktivitas" description="Aktivitas pengguna akan tercatat dan tampil di sini." />
        @endif
    </div>
@endsection
