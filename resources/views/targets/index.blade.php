@extends('layouts.app')

@section('title', 'Target & Jadwal')

@section('content')
    <x-page-header
        eyebrow="Modul Target &amp; Jadwal"
        title="Target &amp; Jadwal"
        subtitle="Kelola tenggat waktu dan prioritas penyelesaian, termasuk pengingat Pajak Kendaraan Dinas."
    >
        <a href="{{ route('targets.create') }}" class="btn-primary">
            <x-icon name="plus" class="h-4 w-4" /> Tambah Target
        </a>
    </x-page-header>

    <div class="stagger grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-slate h-10 w-10 rounded-lg"><x-icon name="list" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Target</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-rose h-10 w-10 rounded-lg"><x-icon name="alert" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tenggat Terlewat</p>
                <p class="text-xl font-extrabold leading-tight text-rose-700">{{ $stats['overdue'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-amber h-10 w-10 rounded-lg"><x-icon name="calendar" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tenggat 7 Hari</p>
                <p class="text-xl font-extrabold leading-tight text-amber-700">{{ $stats['upcoming'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-emerald h-10 w-10 rounded-lg"><x-icon name="check-circle" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Selesai</p>
                <p class="text-xl font-extrabold leading-tight text-emerald-700">{{ $stats['completed'] }}</p>
            </div>
        </div>
    </div>

    <div class="card mt-5 overflow-hidden">
        <form method="GET" action="{{ route('targets.index') }}" class="filter-bar">
            <div class="relative min-w-0 flex-1 sm:max-w-xs">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari target atau kegiatan..."
                       class="input !py-2.5 !pl-10 !text-[13px]">
            </div>

            <select name="priority" class="select !w-auto !min-w-[140px] !py-2.5 !text-[13px]">
                <option value="">Semua prioritas</option>
                @foreach(['tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['priority'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="status" class="select !w-auto !min-w-[150px] !py-2.5 !text-[13px]">
                <option value="">Semua status</option>
                <option value="belum_selesai" @selected(($filters['status'] ?? '') === 'belum_selesai')>Belum selesai</option>
                <option value="selesai" @selected(($filters['status'] ?? '') === 'selesai')>Selesai</option>
            </select>

            <button type="submit" class="btn-primary btn-sm !px-4"><x-icon name="filter" class="h-3.5 w-3.5" /> Terapkan</button>

            @if(array_filter($filters))
                <a href="{{ route('targets.index') }}" class="btn-ghost btn-sm text-slate-500">Reset</a>
            @endif

            <p class="ml-auto hidden text-[11px] text-slate-400 lg:block">{{ $targets->total() }} target</p>
        </form>

        @if($targets->count())
            <div class="divide-y divide-slate-100">
                @foreach($targets as $target)
                    @php
                        $isOverdue = $target->status !== 'selesai' && $target->due_date->isBefore(today());
                        $isDueSoon = $target->status !== 'selesai' && !$isOverdue && $target->due_date->lte(today()->addDays(7));
                        $priorityTone = match($target->priority) {
                            'tinggi' => 'rose',
                            'sedang' => 'amber',
                            default => 'slate',
                        };
                    @endphp
                    <article class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between sm:p-5">
                        <div class="flex min-w-0 gap-3">
                            <span class="mt-0.5 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isOverdue ? 'bg-rose-50 text-rose-600' : ($isDueSoon ? 'bg-amber-50 text-amber-600' : 'bg-sky-50 text-sky-700') }}">
                                <x-icon name="calendar" class="h-[18px] w-[18px]" />
                            </span>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-sm font-extrabold text-slate-900">{{ $target->title }}</h2>
                                    <span class="badge badge-{{ $priorityTone }}">Prioritas {{ ucfirst($target->priority) }}</span>
                                    @if($target->status === 'selesai')
                                        <span class="badge badge-emerald">Selesai</span>
                                    @elseif($isOverdue)
                                        <span class="badge badge-rose">Terlambat</span>
                                    @elseif($isDueSoon)
                                        <span class="badge badge-amber">{{ $target->due_date->isToday() ? 'Tenggat hari ini' : 'Segera jatuh tempo' }}</span>
                                    @else
                                        <span class="badge badge-sky">Belum selesai</span>
                                    @endif
                                </div>
                                @if($target->description)
                                    <p class="mt-1.5 whitespace-pre-line text-[12px] leading-relaxed text-slate-600">{{ $target->description }}</p>
                                @endif
                                <p class="mt-2 flex items-center gap-1.5 text-[11px] font-semibold {{ $isOverdue ? 'text-rose-700' : 'text-slate-500' }}">
                                    <x-icon name="clock" class="h-3.5 w-3.5" />
                                    Tenggat {{ $target->due_date->locale('id')->translatedFormat('l, d F Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-1 pl-[52px] sm:pl-0">
                            <form method="POST" action="{{ route('targets.status', $target) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $target->status === 'selesai' ? 'belum_selesai' : 'selesai' }}">
                                <button type="submit" class="btn-secondary btn-sm">
                                    <x-icon name="{{ $target->status === 'selesai' ? 'refresh' : 'check' }}" class="h-3.5 w-3.5" />
                                    {{ $target->status === 'selesai' ? 'Buka kembali' : 'Tandai selesai' }}
                                </button>
                            </form>
                            <a href="{{ route('targets.edit', $target) }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-amber-50 hover:text-amber-700" title="Edit target">
                                <x-icon name="pencil" class="h-4 w-4" />
                            </a>
                            <form method="POST" action="{{ route('targets.destroy', $target) }}" data-confirm="Hapus target &quot;{{ $target->title }}&quot;?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus target">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $targets->links() }}
            </div>
        @else
            <x-empty-state icon="calendar" title="Belum ada target dan jadwal" description="Tambahkan kegiatan dengan tenggat waktu dan prioritas, seperti Pajak Kendaraan Dinas.">
                <a href="{{ route('targets.create') }}" class="btn-primary btn-sm"><x-icon name="plus" class="h-3.5 w-3.5" /> Tambah Target</a>
            </x-empty-state>
        @endif
    </div>
@endsection
