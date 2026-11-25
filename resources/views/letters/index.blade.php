@extends('layouts.app')

@section('title', 'Persuratan & Arsip')

@section('content')
    @php
        $baseQuery = request()->except(['page', 'type']);
        $typeUrl = fn ($type) => route('letters.index', $type ? array_merge($baseQuery, ['type' => $type]) : $baseQuery);
        $activeType = $filters['type'] ?? null;
    @endphp

    <x-page-header
        eyebrow="Persuratan & Arsip"
        title="Manajemen Surat"
        subtitle="Catat surat masuk dan keluar, pantau tindak lanjut, dan jaga arsip digital tetap tertata."
    >
        <a href="{{ route('letters.export', request()->query()) }}" class="btn-secondary">
            <x-icon name="download" class="h-4 w-4" /> Ekspor CSV
        </a>
        <a href="{{ route('letters.create', ['type' => 'keluar']) }}" class="btn-secondary">
            <x-icon name="send" class="h-4 w-4" /> Surat Keluar
        </a>
        <a href="{{ route('letters.create') }}" class="btn-primary">
            <x-icon name="plus" class="h-4 w-4" /> Catat Surat
        </a>
    </x-page-header>

    <div class="stagger grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-slate h-10 w-10 rounded-lg"><x-icon name="archive" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Arsip</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-sky h-10 w-10 rounded-lg"><x-icon name="inbox" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Surat Masuk</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['masuk'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-violet h-10 w-10 rounded-lg"><x-icon name="send" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Surat Keluar</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['keluar'] }}</p>
            </div>
        </div>
        <div class="card flex items-center gap-3 p-4">
            <span class="icon-tile tone-emerald h-10 w-10 rounded-lg"><x-icon name="check-circle" class="h-[18px] w-[18px]" /></span>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Selesai</p>
                <p class="text-xl font-extrabold leading-tight text-ink-900">{{ $stats['selesai'] }}</p>
            </div>
        </div>
    </div>

    <div class="card mt-5 overflow-hidden">
        <div class="flex items-center gap-1 overflow-x-auto border-b border-slate-100 px-3 pt-3 sm:px-5">
            @php
                $tabs = [
                    null => ['label' => 'Semua Surat', 'count' => ($typeCounts['masuk'] ?? 0) + ($typeCounts['keluar'] ?? 0)],
                    'masuk' => ['label' => 'Surat Masuk', 'count' => $typeCounts['masuk'] ?? 0],
                    'keluar' => ['label' => 'Surat Keluar', 'count' => $typeCounts['keluar'] ?? 0],
                ];
            @endphp

            @foreach($tabs as $key => $tab)
                <a href="{{ $typeUrl($key) }}"
                   class="relative shrink-0 px-3 pb-3 pt-1 text-[12px] font-bold transition {{ $activeType === $key ? 'text-emerald-700' : 'text-slate-400 hover:text-slate-700' }}">
                    {{ $tab['label'] }}
                    <span class="ml-1 rounded-full px-1.5 py-0.5 text-[10px] {{ $activeType === $key ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $tab['count'] }}</span>
                    @if($activeType === $key)
                        <span class="absolute inset-x-2 bottom-0 h-0.5 rounded-full bg-emerald-500"></span>
                    @endif
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('letters.index') }}" class="filter-bar">
            @if($activeType)
                <input type="hidden" name="type" value="{{ $activeType }}">
            @endif

            <div class="relative min-w-0 flex-1 sm:max-w-xs">
                <x-icon name="search" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nomor, judul, atau pengirim..."
                       class="input !py-2.5 !pl-10 !text-[13px]">
            </div>

            <select name="status" class="select !w-auto !min-w-[130px] !py-2.5 !text-[13px]">
                <option value="">Semua status</option>
                @foreach(\App\Http\Controllers\LetterController::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="classification" class="select !w-auto !min-w-[140px] !py-2.5 !text-[13px]">
                <option value="">Semua klasifikasi</option>
                @foreach($classifications as $classification)
                    <option value="{{ $classification }}" @selected(($filters['classification'] ?? '') === $classification)>{{ $classification }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn-primary btn-sm !px-4"><x-icon name="filter" class="h-3.5 w-3.5" /> Terapkan</button>

            @if(array_filter($filters))
                <a href="{{ route('letters.index') }}" class="btn-ghost btn-sm text-slate-500">Reset</a>
            @endif

            <p class="ml-auto hidden text-[11px] text-slate-400 lg:block">Menampilkan {{ $letters->firstItem() ?? 0 }}&ndash;{{ $letters->lastItem() ?? 0 }} dari {{ $letters->total() }} surat</p>
        </form>

        @if($letters->count())
            <div class="overflow-x-auto">
                <table class="data-table min-w-[980px]">
                    <thead>
                        <tr>
                            <th class="w-[38%]">Surat</th>
                            <th>Arah</th>
                            <th>Pengirim / Penerima</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($letters as $letter)
                            <tr>
                                <td>
                                    <div class="flex items-start gap-3">
                                        <span class="icon-tile h-10 w-10 rounded-xl {{ $letter->type === 'masuk' ? 'tone-sky' : 'tone-violet' }}">
                                            <x-icon :name="$letter->type === 'masuk' ? 'inbox' : 'send'" class="h-[18px] w-[18px]" />
                                        </span>
                                        <div class="min-w-0">
                                            <a href="{{ route('letters.show', $letter) }}" class="line-clamp-1 text-[13px] font-bold text-slate-800 transition hover:text-emerald-700">{{ $letter->title }}</a>
                                            <p class="mt-0.5 flex items-center gap-1.5 text-[10px] text-slate-400">
                                                <span class="font-mono font-semibold text-slate-500">{{ $letter->reference_number }}</span>
                                                <span class="h-0.5 w-0.5 rounded-full bg-slate-300"></span>
                                                <span>{{ $letter->classification }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge {{ $letter->type === 'masuk' ? 'badge-sky' : 'badge-violet' }}">{{ $letter->type_label }}</span></td>
                                <td>
                                    <p class="max-w-[170px] truncate text-[12px] font-semibold text-slate-700">{{ $letter->correspondent }}</p>
                                    <p class="mt-0.5 text-[10px] text-slate-400">dicatat {{ $letter->user?->name }}</p>
                                </td>
                                <td>
                                    <p class="text-[12px] font-semibold text-slate-700">{{ $letter->letter_date->format('d M Y') }}</p>
                                    @if($letter->received_date)
                                        <p class="mt-0.5 text-[10px] text-slate-400">diterima {{ $letter->received_date->format('d M Y') }}</p>
                                    @endif
                                </td>
                                <td><x-status-badge :tone="$letter->status_tone" :label="$letter->status_label" /></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        @if($letter->file_path)
                                            <a href="{{ route('letters.download', $letter) }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-emerald-600" title="Unduh berkas">
                                                <x-icon name="download" class="h-4 w-4" />
                                            </a>
                                        @endif
                                        <a href="{{ route('letters.show', $letter) }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-emerald-600" title="Lihat detail">
                                            <x-icon name="eye" class="h-4 w-4" />
                                        </a>
                                        <a href="{{ route('letters.edit', $letter) }}" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-amber-600" title="Edit surat">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                        </a>
                                        <form method="POST" action="{{ route('letters.destroy', $letter) }}" data-confirm="Hapus arsip surat &quot;{{ $letter->title }}&quot;? Tindakan ini tidak dapat dibatalkan.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600" title="Hapus surat">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-5 py-4">
                {{ $letters->links() }}
            </div>
        @else
            <x-empty-state icon="mail" title="Surat tidak ditemukan" description="Belum ada surat yang sesuai filter. Catat surat baru atau ubah kata kunci pencarian.">
                <a href="{{ route('letters.create') }}" class="btn-primary btn-sm"><x-icon name="plus" class="h-3.5 w-3.5" /> Catat Surat</a>
            </x-empty-state>
        @endif
    </div>
@endsection
