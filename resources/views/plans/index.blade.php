@extends('layouts.app')

@section('title', 'Perencanaan & Anggaran')

@section('content')
    <x-page-header eyebrow="Perencanaan &amp; Anggaran" title="Perencanaan Subkegiatan" subtitle="Kelola identitas, indikator kinerja, dan rincian anggaran sebelum serta sesudah perubahan.">
        @if(auth()->user()->canManageData())
            <a href="{{ route('plans.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Buat Perencanaan</a>
        @endif
    </x-page-header>

    <div class="stagger grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-emerald h-10 w-10 rounded-lg"><x-icon name="document" class="h-[18px] w-[18px]" /></span>
            <div><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Dokumen</p><p class="text-xl font-extrabold text-ink-900">{{ $plans->total() }}</p></div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-sky h-10 w-10 rounded-lg"><x-icon name="calendar" class="h-[18px] w-[18px]" /></span>
            <div><p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Dokumen Tahun Ini</p><p class="text-xl font-extrabold text-ink-900">{{ $currentYearCount }}</p></div>
        </div>
    </div>

    <div class="card mt-5 overflow-hidden">
        <form method="GET" action="{{ route('plans.index') }}" class="filter-bar">
            <div class="relative min-w-0 flex-1 sm:max-w-md">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari subkegiatan atau kode..."
                       class="input !py-2.5 !pl-10 !text-[13px]">
            </div>
            <select name="year" class="select !w-auto !min-w-[130px] !py-2.5 !text-[13px]">
                <option value="">Semua tahun</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" @selected((string) ($filters['year'] ?? '') === (string) $year)>{{ $year }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary btn-sm !px-4"><x-icon name="filter" class="h-3.5 w-3.5" /> Terapkan</button>
            @if(array_filter($filters))
                <a href="{{ route('plans.index') }}" class="btn-ghost btn-sm text-slate-500">Reset</a>
            @endif
            <p class="ml-auto hidden text-[11px] text-slate-400 lg:block">Menampilkan {{ $plans->firstItem() ?? 0 }}&ndash;{{ $plans->lastItem() ?? 0 }} dari {{ $plans->total() }}</p>
        </form>

        @if($plans->count())
            <div class="overflow-x-auto">
                <table class="data-table min-w-[850px]">
                    <thead><tr><th>Subkegiatan</th><th>Tahun</th><th>Unit Organisasi</th><th class="text-right">Anggaran Sesudah</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody>
                        @foreach($plans as $plan)
                            <tr>
                                <td>
                                    <a href="{{ route('plans.show', $plan) }}" class="text-[13px] font-bold text-slate-800 hover:text-emerald-700">{{ $plan->sub_activity_name }}</a>
                                    <p class="mt-1 text-[11px] text-slate-400">{{ $plan->sub_activity_code }} &middot; {{ $plan->program_name }}</p>
                                </td>
                                <td><span class="badge badge-sky">{{ $plan->year }}</span></td>
                                <td class="text-[12px] text-slate-600">{{ $plan->organization_unit }}</td>
                                <td class="text-right text-[12px] font-bold tabular-nums text-slate-700">Rp{{ number_format($plan->total_after, 2, ',', '.') }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('plans.print', $plan) }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-sky-50 hover:text-sky-700" title="Cetak / Simpan PDF"><x-icon name="printer" class="h-4 w-4" /></a>
                                        <a href="{{ route('plans.show', $plan) }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-emerald-600" title="Lihat detail"><x-icon name="eye" class="h-4 w-4" /></a>
                                        @if($plan->canBeEditedBy(auth()->user()))
                                            <a href="{{ route('plans.edit', $plan) }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-amber-50 hover:text-amber-600" title="Edit"><x-icon name="pencil" class="h-4 w-4" /></a>
                                            <form method="POST" action="{{ route('plans.destroy', $plan) }}" data-confirm="Hapus dokumen perencanaan ini?">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus"><x-icon name="trash" class="h-4 w-4" /></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-5 py-4">{{ $plans->links() }}</div>
        @else
            <x-empty-state icon="document" title="Belum ada perencanaan" description="Buat dokumen perencanaan subkegiatan pertama untuk mulai mencatat indikator dan anggarannya.">
                @if(auth()->user()->canManageData())
                    <a href="{{ route('plans.create') }}" class="btn-primary btn-sm"><x-icon name="plus" class="h-3.5 w-3.5" /> Buat Perencanaan</a>
                @endif
            </x-empty-state>
        @endif
    </div>
@endsection
