@extends('layouts.app')

@section('title', 'Kendala')

@section('content')
    <x-page-header
        eyebrow="03. Kendala"
        title="Kendala"
        subtitle="Catat hambatan, penyebab, prioritas, dan tindak lanjut penyelesaian."
    >
        @if(auth()->user()->canManageData())
            <a href="{{ route('issues.create') }}" class="btn-primary">
                <x-icon name="plus" class="h-4 w-4" /> Catat Kendala
            </a>
        @endif
    </x-page-header>

    <div class="stagger grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-slate h-10 w-10 rounded-lg"><x-icon name="list" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Kendala</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-rose h-10 w-10 rounded-lg"><x-icon name="alert" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Belum Ditangani</p>
                <p class="text-xl font-extrabold leading-tight text-rose-700">{{ $stats['open'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-amber h-10 w-10 rounded-lg"><x-icon name="activity" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Sedang Ditangani</p>
                <p class="text-xl font-extrabold leading-tight text-amber-700">{{ $stats['in_progress'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-emerald h-10 w-10 rounded-lg"><x-icon name="check-circle" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Selesai</p>
                <p class="text-xl font-extrabold leading-tight text-emerald-700">{{ $stats['resolved'] }}</p>
            </div>
        </div>
    </div>

    <div class="card mt-5 overflow-hidden">
        <form method="GET" action="{{ route('issues.index') }}" class="filter-bar">
            <div class="relative min-w-0 flex-1 sm:max-w-xs">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari kendala..."
                       class="input !py-2.5 !pl-10 !text-[13px]">
            </div>

            <select name="priority" class="select !w-auto !min-w-[140px] !py-2.5 !text-[13px]">
                <option value="">Semua prioritas</option>
                @foreach(['tinggi' => 'Tinggi', 'sedang' => 'Sedang', 'rendah' => 'Rendah'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['priority'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="status" class="select !w-auto !min-w-[160px] !py-2.5 !text-[13px]">
                <option value="">Semua status</option>
                @foreach(['terbuka' => 'Belum ditangani', 'ditangani' => 'Sedang ditangani', 'selesai' => 'Selesai'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn-primary btn-sm !px-4"><x-icon name="filter" class="h-3.5 w-3.5" /> Terapkan</button>
            @if(array_filter($filters))
                <a href="{{ route('issues.index') }}" class="btn-ghost btn-sm text-slate-500">Reset</a>
            @endif
            <p class="ml-auto hidden text-[11px] text-slate-400 lg:block">{{ $issues->total() }} kendala</p>
        </form>

        @if($issues->count())
            <div class="divide-y divide-slate-100">
                @foreach($issues as $issue)
                    @php
                        $priorityTone = match($issue->priority) {
                            'tinggi' => 'rose',
                            'sedang' => 'amber',
                            default => 'slate',
                        };
                        $statusTone = match($issue->status) {
                            'selesai' => 'emerald',
                            'ditangani' => 'sky',
                            default => 'rose',
                        };
                        $statusLabel = match($issue->status) {
                            'selesai' => 'Selesai',
                            'ditangani' => 'Sedang ditangani',
                            default => 'Belum ditangani',
                        };
                    @endphp
                    <article class="p-4 sm:p-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-sm font-extrabold text-slate-900">{{ $issue->title }}</h2>
                                    <span class="badge badge-{{ $priorityTone }}">Prioritas {{ ucfirst($issue->priority) }}</span>
                                    <span class="badge badge-{{ $statusTone }}">{{ $statusLabel }}</span>
                                </div>
                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Penyebab</p>
                                        <p class="mt-1 whitespace-pre-line text-[12px] leading-relaxed text-slate-600">{{ $issue->cause }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tindak lanjut</p>
                                        <p class="mt-1 whitespace-pre-line text-[12px] leading-relaxed text-slate-600">{{ $issue->follow_up ?: 'Belum dicatat.' }}</p>
                                    </div>
                                </div>
                                <p class="mt-3 text-[10px] text-slate-400">Diperbarui {{ $issue->updated_at->locale('id')->diffForHumans() }}</p>
                            </div>
                            <div class="flex shrink-0 flex-wrap items-center gap-1">
                                @if(auth()->user()->canManageData())
                                    <a href="{{ route('issues.edit', $issue) }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-amber-50 hover:text-amber-700" title="Edit kendala">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form method="POST" action="{{ route('issues.destroy', $issue) }}" data-confirm="Hapus kendala &quot;{{ $issue->title }}&quot;?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus kendala">
                                            <x-icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </form>
                                @endif
                                @if(auth()->user()->canReview())
                                    <form method="POST" action="{{ route('issues.verify', $issue) }}" class="flex items-center gap-1">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" aria-label="Verifikasi status kendala" class="select !w-auto !py-1.5 !text-[11px]">
                                            @foreach(['terbuka' => 'Belum ditangani', 'ditangani' => 'Sedang ditangani', 'selesai' => 'Selesai'] as $value => $label)
                                                <option value="{{ $value }}" @selected($issue->status === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn-secondary btn-sm">Verifikasi</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="border-t border-slate-100 px-5 py-4">
                {{ $issues->links() }}
            </div>
        @else
            <x-empty-state icon="alert" title="Belum ada kendala" description="Catat hambatan beserta penyebab dan tindak lanjutnya agar mudah dipantau.">
                @if(auth()->user()->canManageData())
                    <a href="{{ route('issues.create') }}" class="btn-primary btn-sm"><x-icon name="plus" class="h-3.5 w-3.5" /> Catat Kendala</a>
                @endif
            </x-empty-state>
        @endif
    </div>
@endsection
